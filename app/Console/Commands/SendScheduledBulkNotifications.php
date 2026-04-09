<?php

namespace App\Console\Commands;

use App\Jobs\SendBulkNotificationJob;
use App\Models\BulkNotification;
use Illuminate\Console\Command;

class SendScheduledBulkNotifications extends Command
{
    protected $signature = 'notifications:send-scheduled';
    protected $description = 'Send scheduled bulk notifications';

    public function handle(): int
    {
        $notifications = BulkNotification::pendingScheduled()->get();

        foreach ($notifications as $notification) {
            $notification->update([
                'status' => BulkNotification::STATUS_QUEUED,
            ]);

            SendBulkNotificationJob::dispatch($notification->id);

            $this->info("Notification #{$notification->id} queued.");
        }

        return self::SUCCESS;
    }
}
