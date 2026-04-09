<?php

namespace App\Jobs;

use App\Services\FirebaseNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendBulkNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $message,
        public array $data = []
    ) {}

    public function handle(FirebaseNotificationService $firebaseService): void
    {
        $results = $firebaseService->sendToAll(
            $this->title,
            $this->message,
            $this->data
        );

        Log::info('Bulk notification job completed', [
            'total' => count($results),
            'successful' => collect($results)->where('success', true)->count(),
            'failed' => collect($results)->where('success', false)->count(),
        ]);
    }
}
