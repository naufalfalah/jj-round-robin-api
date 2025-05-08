<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class ActivityLogHelper
{
    public static function save_activity_with_check($auth_id, $action, $tableName, $timeFrameMinutes = 5)
    {
        $timeFrame = Carbon::now()->subMinutes($timeFrameMinutes);
        $existingActivity = DB::table('activity_logs')
            ->where('user_id', $auth_id)
            ->where('activity', $action)
            ->where('table', $tableName)
            ->where('created_at', '>=', $timeFrame)
            ->first();

        if (!$existingActivity) {
            self::save_activity($auth_id, $action, $tableName);
        }
    }

    public static function save_activity($user_id, $action, $tableName = null, $platform = null)
    {
        ActivityLog::create([
            'user_id' => $user_id,
            'activity' => $action,
            'table' => $tableName ?? '',
            'user_ip' => Request::ip(),
            'user_agent' => Request::header('User-Agent'),
            'current_url' => Request::url(),
            'platform' => $platform ?? 'website',
        ]);
    }
}
