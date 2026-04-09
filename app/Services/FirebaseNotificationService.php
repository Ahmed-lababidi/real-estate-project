<?php

namespace App\Services;

use App\Models\FcmToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseNotificationService
{
    protected string $projectId;
    protected string $clientEmail;
    protected string $privateKey;

    public function __construct()
    {
        $this->projectId = config('services.firebase.project_id');
        $this->clientEmail = config('services.firebase.client_email');
        $this->privateKey = str_replace('\n', "\n", config('services.firebase.private_key'));
    }

    public function isConfigured(): bool
    {
        return !empty($this->projectId)
            && !empty($this->clientEmail)
            && !empty($this->privateKey);
    }

    protected function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public function stringifyData(array $data): array
    {
        $result = [];

        foreach ($data as $key => $value) {
            if ($value === null) {
                $result[$key] = '';
            } elseif (is_array($value) || is_object($value)) {
                $result[$key] = json_encode($value, JSON_UNESCAPED_UNICODE);
            } else {
                $result[$key] = (string) $value;
            }
        }

        return $result;
    }

    public function getAccessToken(): string
    {
        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT',
        ];

        $now = time();

        $payload = [
            'iss' => $this->clientEmail,
            'sub' => $this->clientEmail,
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        ];

        $jwtHeader = $this->base64UrlEncode(json_encode($header));
        $jwtPayload = $this->base64UrlEncode(json_encode($payload));

        $signatureInput = $jwtHeader . '.' . $jwtPayload;

        $privateKey = openssl_pkey_get_private($this->privateKey);

        if (! $privateKey) {
            throw new \Exception('Invalid Firebase private key.');
        }

        openssl_sign($signatureInput, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        $jwtSignature = $this->base64UrlEncode($signature);
        $jwt = $signatureInput . '.' . $jwtSignature;

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        if (! $response->successful() || ! $response->json('access_token')) {
            Log::error('Failed to get Firebase access token', [
                'response' => $response->body(),
            ]);

            throw new \Exception('Failed to get Firebase access token');
        }

        return $response->json('access_token');
    }

    public function sendToToken(string $token, string $title, string $body, array $data = []): array
    {
        try {
            $accessToken = $this->getAccessToken();

            $stringifiedData = $this->stringifyData(array_merge([
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                'sound' => 'default',
                'priority' => 'high',
                'notification_type' => 'general',
            ], $data));

            $payload = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                        'image' => $data['image_url'] ?? null,
                    ],
                    'data' => $stringifiedData,
                    'android' => [
                        'priority' => 'high',
                        'notification' => [
                            'sound' => 'default',
                            'channel_id' => 'general_notifications_channel',
                            'icon' => 'notification_icon',
                            'color' => $data['color'] ?? '#4CAF50',
                        ],
                    ],
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'sound' => 'default',
                                'badge' => 1,
                            ],
                        ],
                    ],
                    'webpush' => [
                        'headers' => [
                            'Urgency' => 'high',
                        ],
                    ],
                ],
            ];

            $response = Http::withToken($accessToken)
                ->post("https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send", $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'token' => $token,
                    'status' => $response->status(),
                    'response' => $response->json(),
                    'should_disable_token' => false,
                ];
            }

            $responseBody = $response->json() ?? $response->body();

            Log::warning('FCM send failed', [
                'token' => substr($token, 0, 20) . '...',
                'status' => $response->status(),
                'response' => $responseBody,
            ]);

            return [
                'success' => false,
                'token' => $token,
                'status' => $response->status(),
                'response' => $responseBody,
                'should_disable_token' => $this->shouldDisableToken($responseBody),
            ];
        } catch (\Throwable $e) {
            Log::error('FCM send exception', [
                'token' => substr($token, 0, 20) . '...',
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'token' => $token,
                'status' => 500,
                'response' => $e->getMessage(),
                'should_disable_token' => false,
            ];
        }
    }

    protected function shouldDisableToken($responseBody): bool
    {
        $body = strtolower(json_encode($responseBody, JSON_UNESCAPED_UNICODE));

        return str_contains($body, 'unregistered')
            || str_contains($body, 'not_found')
            || str_contains($body, 'invalid registration token')
            || str_contains($body, 'requested entity was not found')
            || str_contains($body, 'not registered');
    }

    public function sendToMany(array $tokens, string $title, string $body, array $data = []): array
    {
        $results = [];

        foreach ($tokens as $token) {
            if (! $token || trim($token) === '') {
                continue;
            }

            $results[] = $this->sendToToken($token, $title, $body, $data);

            usleep(50000); // 50ms
        }

        $this->disableInvalidTokens($results);

        return $results;
    }

    public function sendToAll(string $title, string $body, array $data = []): array
    {
        $tokens = FcmToken::query()
            ->active()
            ->pluck('fcm_token')
            ->toArray();

        return $this->sendToMany($tokens, $title, $body, $data);
    }

    public function disableInvalidTokens(array $results): void
    {
        $invalidTokens = collect($results)
            ->filter(fn($item) => ($item['should_disable_token'] ?? false) && !empty($item['token']))
            ->pluck('token')
            ->unique()
            ->values()
            ->toArray();

        if (empty($invalidTokens)) {
            return;
        }

        FcmToken::query()
            ->whereIn('fcm_token', $invalidTokens)
            ->update([
                'is_active' => false,
                'last_used_at' => now(),
                'updated_at' => now(),
            ]);

        Log::info('Invalid FCM tokens disabled', [
            'count' => count($invalidTokens),
        ]);
    }
}
