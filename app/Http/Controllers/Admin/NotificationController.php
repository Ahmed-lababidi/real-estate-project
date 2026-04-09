<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SendBulkNotificationRequest;
use App\Http\Requests\Admin\SendNotificationRequest;
use App\Http\Requests\Public\StoreFcmTokenRequest;
use App\Models\FcmToken;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\JsonResponse;
use App\Traits\ApiResponse;

class NotificationController extends Controller
{
    use ApiResponse;
    public function __construct(
        protected FirebaseNotificationService $firebaseService
    ) {}

    public function storeToken(StoreFcmTokenRequest $request): JsonResponse
    {
        $fcmToken = FcmToken::where('fcm_token', $request->fcm_token)->first();
        if ($fcmToken) {
            return $this->errorResponse('FCM token already exists.');
        }

        $token = FcmToken::updateOrCreate(
            ['fcm_token' => $request->fcm_token],
            [
                'device_id' => $request->device_id,
                'platform' => $request->platform,
                'is_active' => true,
                'last_used_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'FCM token stored successfully.',
            'data' => $token,
        ]);
    }

    public function sendToOne(SendNotificationRequest $request): JsonResponse
    {
        $result = $this->firebaseService->sendToToken(
            $request->fcm_token,
            $request->title,
            $request->message,
            $request->input('data', [])
        );

        if (($result['should_disable_token'] ?? false) === true) {
            FcmToken::where('fcm_token', $request->fcm_token)->update([
                'is_active' => false,
                'last_used_at' => now(),
            ]);
        }

        return response()->json([
            'success' => $result['success'],
            'message' => $result['success']
                ? 'Notification sent successfully.'
                : 'Failed to send notification.',
            'data' => $result,
        ], $result['success'] ? 200 : 500);
    }

    public function sendToAll(SendBulkNotificationRequest $request): JsonResponse
    {
        \App\Jobs\SendBulkNotificationJob::dispatch(
            $request->title,
            $request->message,
            $request->input('data', [])
        );

        return response()->json([
            'success' => true,
            'message' => 'Bulk notification job dispatched successfully.',
        ]);
    }

    public function deactivateToken(StoreFcmTokenRequest $request): JsonResponse
    {
        $fcmToken = FcmToken::where('fcm_token', $request->fcm_token)->first();
        if (!$fcmToken) {
            return response()->json([
                'success' => false,
                'message' => 'FCM token not found.',
            ], 404);
        }
        if (!$fcmToken->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'FCM token is already deactivated.',
            ], 400);
        }
        FcmToken::where('fcm_token', $request->fcm_token)->update([
            'is_active' => false,
            'last_used_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'FCM token deactivated successfully.',
        ]);
    }
}
