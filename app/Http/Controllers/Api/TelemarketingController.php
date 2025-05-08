<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClientCalendarEvent;
use App\Traits\ApiResponseTrait;
use App\Traits\PackageTrait;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

/**
 * @group Request Service
 *
 * @subgroup Telemarketing
 *
 * @authenticated
 */
class TelemarketingController extends Controller
{
    use ApiResponseTrait, PackageTrait;

    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'auth' => [config('cloudtalk.CLOUDTALK_ID'), config('cloudtalk.CLOUDTALK_SECRET')],
        ]);
    }

    /**
     * Get Telemarketing
     */
    public function index()
    {
        try {
            $response = $this->client->get('https://my.cloudtalk.io/api/statistics/realtime/groups.json');
            $data = json_decode($response->getBody());

            $itemTotalCall = $itemTotalUnanswer = $itemAvgCallDuration = $itemAvgWaitingTime = [];

            foreach ($data->responseData->data->groups as $item) {
                $totalCall = $item->answered + $item->unanswered;
                $itemTotalCall[] = $totalCall;
                $itemTotalUnanswer[] = $item->unanswered;
                $itemAvgCallDuration[] = $item->avg_call_duration;
                $itemAvgWaitingTime[] = $item->avg_waiting_time;
            }

            $totalCallMinutes = array_sum($itemAvgCallDuration) ?? 0;
            $totalCallsMade = array_sum($itemTotalCall) ?? 0;
        } catch (\Exception $e) {
            if (config('app.env') !== 'production') {
                Log::error($e->getMessage());
            }

            $totalCallMinutes = 0;
            $totalCallsMade = 0;
        }

        $userId = auth('api')->user()->id;
        $appointments = ClientCalendarEvent::where('client_id', $userId)
            ->whereDate('event_date', Carbon::today())
            ->get();

        $data = [
            'total_call_minutes' => $totalCallMinutes,
            'total_calls_made' => $totalCallsMade,
            'total_appointment' => $appointments->count(),
            'total_calls' => $totalCallsMade,
            'peak_time' => 0,
            'appointment_conversion' => $totalCallsMade > 0 ? round(($appointments->count() / $totalCallsMade) * 100, 2) : 0,
            'top_project' => [
                'name' => '',
                'description' => '',
                'percentage1' => 0,
                'percentage' => 0,
            ],
            'appointment_set_today' => $appointments,
        ];

        return $this->sendSuccessResponse('Telemarketing fetched successfully', $data);
    }
}
