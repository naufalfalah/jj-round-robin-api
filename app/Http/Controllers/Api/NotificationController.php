<?php

namespace App\Http\Controllers\Api;

use App\Constants\NotificationConstant;
use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

/**
 * @group Notification
 */
class NotificationController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get Notifications
     *
     * @authenticated
     */
    public function index(Request $request)
    {
        $userId = auth('api')->id();

        $notifications = Notification::where('user_id', $userId)
            ->where('user_type', 'user')
            ->orderBy('id', $request->get('order', 'desc'))
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'body' => $notification->body,
                    'is_read' => $notification->is_read,
                    'lead_url' => $notification->lead_url ?? null,
                    'lead_name' => $notification?->lead?->name ?? null,
                    'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $notification->updated_at->format('Y-m-d H:i:s'),
                ];
            });

        ActivityLogHelper::save_activity($userId, 'View All Files', 'ClientFiles', 'app');

        if ($notifications->isEmpty()) {
            return $this->sendErrorResponse('No Notification found.', 404);
        }

        return $this->sendSuccessResponse('Notification Fetch Successfully', ['notifications' => $notifications]);
    }

    /**
     * Get Notification Types
     */
    public function types()
    {
        $notificationTypes = NotificationConstant::TYPES;

        return $this->sendSuccessResponse('Notification Types Fetch Successfully', ['notificationTypes' => $notificationTypes]);
    }
}
