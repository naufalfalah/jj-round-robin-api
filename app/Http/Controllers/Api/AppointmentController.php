<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Models\ClientCalendarEvent;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

/**
 * @group Appointment
 *
 * @authenticated
 */
class AppointmentController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get Appointments
     */
    public function index(Request $request)
    {
        $clientId = auth('api')->user()->id;

        $appointments = ClientCalendarEvent::query()
            ->where('client_id', $clientId);

        if ($request->date) {
            $appointments = $appointments->whereDate('event_date', $request->date);
        }

        $appointments = $appointments->get();

        if (!isset($appointments) || $appointments->count() == 0) {
            return $this->sendErrorResponse('Appointments Are Not Found', 404);
        }

        $transformedData = [];

        // Loop through each item in the original data
        foreach ($appointments as $item) {
            $date = $item['event_date'];
            $startTime = $item['start_time'];

            // Create a new entry in the transformed data array if it doesn't exist
            if (!isset($transformedData[$date])) {
                $transformedData[$date] = [];
            }

            // Create a new entry for the start time if it doesn't exist
            if (!isset($transformedData[$date][$startTime])) {
                $transformedData[$date][$startTime] = [];
            }

            // Append the relevant data to the start time entry
            $transformedData[$date][$startTime][] = [
                'id' => $item['id'],
                'title' => $item['title'],
                'location' => $item['location'],
                'description' => $item['description'],
                'end_time' => $item['end_time'],
            ];
        }
        ActivityLogHelper::save_activity($clientId, 'View Appointment', 'ClientCalendarEvent', 'app');

        return $this->sendSuccessResponse('Appointment fetched successfully', $transformedData);
    }

    /**
     * Get Appointment
     */
    public function show(int $id)
    {
        $clientId = auth('api')->user()->id;

        $appointment = ClientCalendarEvent::where('id', $id)
            ->where('client_id', $clientId)
            ->first();

        if (!$appointment) {
            return $this->sendErrorResponse('Appointment Is Not Found', 404);
        }

        $data = [
            'appointment' => $appointment,
        ];

        ActivityLogHelper::save_activity($clientId, 'View Appointment', 'ClientCalendarEvent', 'app');

        return $this->sendSuccessResponse('Appointment fetched successfully', $data);
    }
}
