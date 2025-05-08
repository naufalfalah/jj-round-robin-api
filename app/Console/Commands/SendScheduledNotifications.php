<?php

namespace App\Console\Commands;

use App\Models\NotificationSchedule;
use App\Services\FirebaseService;
use Illuminate\Console\Command;

class SendScheduledNotifications extends Command
{
    protected $signature = 'notifications:send-scheduled';

    protected $description = 'Send scheduled push notifications';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $firebaseService = app(FirebaseService::class);

        $notifications = NotificationSchedule::where('is_sent', false)
            ->where('scheduled_at', '<=', now())
            ->get();

        foreach ($notifications as $notification) {
            try {
                $firebaseService->sendNotification(
                    $notification->device_token,
                    $notification->title,
                    $notification->body
                );

                $notification->update(['is_sent' => true]);
                $this->info("Notification ID {$notification->id} sent successfully.");
            } catch (\Exception $e) {
                $this->error("Failed to send notification ID {$notification->id}: {$e->getMessage()}");
            }
        }

        return 0;
    }
}
