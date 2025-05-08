<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Models\Automation;
use App\Models\ClientCalendarEvent;
use App\Models\FormRequest;
use App\Models\LeadClient;
use App\Models\User;
use App\Traits\ApiResponseTrait;

/**
 * @group Home
 *
 * @authenticated
 */
class HomeController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get Home
     */
    public function index()
    {
        $data = [
            'leads' => [
                'new_leads' => $this->get_client_new_leads(),
                'follow_ups' => $this->get_follow_up(),
            ],
            'appointments' => $this->get_appointment(),
            'task' => [
                'pending' => $this->get_task_pending(),
            ],
            'automation' => $this->get_automation(),
        ];
        ActivityLogHelper::save_activity(auth('api')->id(), 'Home Data Request', 'FormRequest', 'app');

        return $this->sendSuccessResponse('Home Data Fetch Successfully', $data);
    }

    private function get_client_new_leads()
    {
        $user_id = auth('api')->user()->id;
        $client_new_leads = LeadClient::with('activity', 'lead_data', 'lead_groups')->where('client_id', $user_id)->where('status', 'new_lead')->latest()->count();

        return $client_new_leads;
    }

    private function get_follow_up()
    {
        $user_id = auth('api')->user()->id;
        $client_leads_follow_up = LeadClient::with('activity', 'lead_data', 'lead_groups')->where('client_id', $user_id)->whereNotNull('follow_up_date_time')->whereDate('follow_up_date_time', '>=', now()->format('Y-m-d'))->latest()->count();

        return $client_leads_follow_up;
    }

    private function get_appointment()
    {
        $todayDate = now()->format('Y-m-d');
        $userId = auth('api')->user()->id;
        $appointment = ClientCalendarEvent::where('client_id', $userId)
            ->whereDate('event_date', '>=', $todayDate)->count();

        return $appointment;
    }

    private function get_task_pending()
    {
        $userId = auth('api')->user()->id;
        $formRequest = FormRequest::where('client_id', $userId)
            ->whereHas('formGroup', function ($query) {
                $query->where('name', '!=', 'completed');
            });
        $totalSubTaskPending = 0;
        $totalFormRequestPending = $formRequest->count();
        foreach ($formRequest->get() as $task) {
            $totalSubTaskPending += $task->pendingSubtasksCount();
        }

        return (int) $totalSubTaskPending + $totalFormRequestPending;
    }

    private function get_automation()
    {
        $automations = Automation::where('client_id', auth('api')->user()->id)->count();

        return $automations;
    }

    /**
     * Get Started Tasks
     */
    public function getStartedTasks()
    {
        $userId = auth('api')->id();
        $user = User::find($userId);
        $data = [
            'all_tasks' => 0,
            'completed_tasks' => 0,
            'tasks' => [
                'profile' => true,
                'facebook' => (bool) $user->facebook_access_token,
                'instagram' => (bool) $user->facebook_access_token,
                'whatsapp' => false,
                'google_ads' => (bool) $user->google_ads_access_token,
                'gmail' => (bool) $user->google_access_token,
            ],
        ];

        return $this->sendSuccessResponse('Started Tasks Fetch Successfully', $data);
    }
}
