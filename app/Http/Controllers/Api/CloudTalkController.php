<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Services\CloudTalkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * @group Cloud Talk
 */
class CloudTalkController extends Controller
{
    public function callStatistics(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-d'));
        $endDate = $request->input('end_date');
        $tagId = $request->input('tag_id', '');
        // $cacheKey = 'statistic_' . md5($startDate . $endDate);

        // // Check if data is already in cache
        // if (Cache::has($cacheKey)) {
        //     $statistic = Cache::get($cacheKey);
        // } else {
        //     if (!empty($endDate)) {
        //         $services = new CloudTalkService();
        //         $statistic = $services->getStatisticCall($startDate, date('Y-m-d', strtotime($endDate)));

        //         // Cache the data for 5 minutes (300 seconds)
        //         Cache::put($cacheKey, $statistic, 300);
        //     } else {
        //         return response()->json(null);
        //     }
        // }

        if (!empty($endDate)) {
            $services = new CloudTalkService;
            $statistic = $services->getStatisticCall($startDate, date('Y-m-d', strtotime($endDate)), $tagId);
        } else {
            return response()->json(null);
        }

        ActivityLogHelper::save_activity(auth('api')->id(), 'Call Statistics', 'CloudTalkService', 'app');

        return response()->json($statistic);
    }
}
