<?php

namespace App\Services;

use App\Models\FcmToken;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    protected string $apiUrl;
    protected string $apiToken;

    public function __construct()
    {
        $this->apiUrl = config('services.supabase.notification_url');
        $this->apiToken = config('services.supabase.notification_token');
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiUrl) && !empty($this->apiToken);
    }

    public function sendToToken(
        string $fcmToken,
        string $title,
        string $message,
        array $data = []
    ): array {
        try {
            $response = Http::timeout(15)
                ->retry(2, 500)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiToken,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiUrl, [
                    'fcm_token' => $fcmToken,
                    'title' => $title,
                    'message' => $message,
                    'data' => $data,
                ]);

            return $this->handleResponse($response, $fcmToken);
        } catch (\Throwable $e) {
            Log::error('Notification sending exception', [
                'fcm_token' => $fcmToken,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'status' => 500,
                'message' => $e->getMessage(),
                'should_disable_token' => false,
            ];
        }
    }

    protected function handleResponse(Response $response, string $fcmToken): array
    {
        if ($response->successful()) {
            return [
                'success' => true,
                'status' => $response->status(),
                'message' => 'Notification sent successfully.',
                'response' => $response->json() ?? $response->body(),
                'should_disable_token' => false,
            ];
        }

        $body = $response->json() ?? $response->body();

        Log::warning('Notification sending failed', [
            'fcm_token' => $fcmToken,
            'status' => $response->status(),
            'response' => $body,
        ]);

        return [
            'success' => false,
            'status' => $response->status(),
            'message' => 'Failed to send notification.',
            'response' => $body,
            'should_disable_token' => $this->shouldDisableToken($response),
        ];
    }

    protected function shouldDisableToken(Response $response): bool
    {
        $status = $response->status();
        $body = strtolower(json_encode($response->json() ?? $response->body()));

        return
            $status === 404 ||
            str_contains($body, 'registration-token-not-registered') ||
            str_contains($body, 'invalid registration token') ||
            str_contains($body, 'requested entity was not found') ||
            str_contains($body, 'not registered') ||
            str_contains($body, 'invalid argument');
    }

    public function disableToken(FcmToken $token): void
    {
        $token->update([
            'is_active' => false,
        ]);
    }
}
