<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClientCalendarEvent;
use App\Models\User;
use App\Services\GoogleCalendarService;
use App\Traits\ApiResponseTrait;
use App\Traits\GoogleTrait;
use DateTimeImmutable;
use Google\Service\Oauth2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * @group Calendar
 */
class CalendarController extends Controller
{
    use ApiResponseTrait;
    use GoogleTrait;

    public function index()
    {
        $userId = auth('api')->id();
        $user = User::find($userId);

        $this->checkRefreshTokenNewUser($user);
        $googleCalendarService = new GoogleCalendarService($user->google_access_token);

        if (!$user->calendar_id) {
            $jomeCalendar = $googleCalendarService->createCalendar();
            User::where('id', $user->id)->update([
                'calendar_id' => $jomeCalendar['id'],
            ]);
        }

        $timeMin = now()->parse(request()->start)->toIso8601String(); // or specify a start date
        $timeMax = now()->parse(request()->end)->toIso8601String(); // or specify an end date

        $calendarId = $user->calendar_id;
        $calendarEvents = $googleCalendarService->listEvents($calendarId, $timeMin, $timeMax);

        $events = [];
        if (isset($calendarEvents) && count($calendarEvents['items'])) {
            foreach ($calendarEvents['items'] as $event) {
                $start = $event['start']['dateTime'] ?? $event['start']['date'];
                $end = $event['end']['dateTime'] ?? $event['end']['date'];

                $events[] = [
                    'id' => $event['id'],
                    'title' => $event['summary'] ?? 'Untitled Event',
                    'description' => $event['description'] ?? '',
                    'start' => $start,
                    'end' => $end,
                    'allDay' => isset($event['start']['date']) ? true : false,
                ];
            }
        }

        return $this->sendSuccessResponse('Calendar Events Fetch Successfully', ['events' => $events]);
    }

    public function storeEvent(Request $request)
    {
        $rules = [
            'title' => 'required',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $userId = auth('api')->id();
        $user = User::find($userId);

        $summary = $request->title;
        $description = $request->description;
        $date = $request->date;
        $startTime = $request->start_time;
        $endTime = $request->end_time;

        $startDatetimeString = $date.' '.$startTime;
        $endDatetimeString = $date.' '.$endTime;

        $startDatetimeImmutable = new DateTimeImmutable($startDatetimeString);
        $endDatetimeImmutable = new DateTimeImmutable($endDatetimeString);

        $startDateTime = $startDatetimeImmutable->format('Y-m-d\TH:i:s');
        $endDateTime = $endDatetimeImmutable->format('Y-m-d\TH:i:s');

        $this->checkRefreshTokenNewUser($user);
        $googleCalendarService = new GoogleCalendarService($user->google_access_token);

        DB::beginTransaction();
        try {
            $calendarId = $user->calendar_id;
            $calendarEvent = $googleCalendarService->createEvent($calendarId, $summary, $description, $startDateTime, $endDateTime);

            ClientCalendarEvent::create([
                'client_id' => $userId,
                'title' => $summary,
                'event_date' => $date ?? null,
                'start_time' => $startTime ?? null,
                'end_time' => $endTime ?? null,
                'description' => $description ?? null,
                'calender_event_id' => $calendarEvent['id'],
                'added_by_id' => auth('web')->id(),
            ]);

            DB::commit();

            return $this->sendSuccessResponse('Calendar Events Added Successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function showEvent(Request $request)
    {
        $rules = [
            'event_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $userId = auth('api')->id();
        $user = User::find($userId);

        $calendarId = $user->calendar_id;
        $eventId = $request->event_id;

        $this->checkRefreshTokenNewUser($user);
        $googleCalendarService = new GoogleCalendarService($user->google_access_token);
        $event = $googleCalendarService->getEvent($calendarId, $eventId);

        return $this->sendSuccessResponse('Calendar Event Fetched Successfully', ['event' => $event]);
    }

    public function updateEvent(Request $request)
    {
        $rules = [
            'title' => 'required',
            'date' => 'required|date|after:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $userId = auth('api')->id();
        $user = User::find($userId);

        $eventId = $request->event_id;
        $summary = $request->title;
        $description = $request->description;
        $date = $request->date;
        $startTime = $request->start_time;
        $endTime = $request->end_time;

        $startDatetimeString = $date.' '.$startTime;
        $endDatetimeString = $date.' '.$endTime;

        $startDatetimeImmutable = new DateTimeImmutable($startDatetimeString);
        $endDatetimeImmutable = new DateTimeImmutable($endDatetimeString);

        $startDateTime = $startDatetimeImmutable->format('Y-m-d\TH:i:s');
        $endDateTime = $endDatetimeImmutable->format('Y-m-d\TH:i:s');

        $this->checkRefreshTokenNewUser($user);
        $googleCalendarService = new GoogleCalendarService($user->google_access_token);

        DB::beginTransaction();
        try {
            $calendarId = $user->calendar_id;
            $googleCalendarService->updateEvent($calendarId, $eventId, $summary, $description, $startDateTime, $endDateTime);

            DB::commit();

            return $this->sendSuccessResponse('Calendar Event Updated Successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->sendErrorResponse($e->getMessage(), 500);
        }
    }

    public function destroyEvent(Request $request)
    {
        $rules = [
            'event_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $userId = auth('api')->id();
        $user = User::find($userId);

        $calendarId = $user->calendar_id;
        $eventId = $request->event_id;

        $this->checkRefreshTokenNewUser($user);
        $googleCalendarService = new GoogleCalendarService($user->google_access_token);
        $googleCalendarService->deleteEvent($calendarId, $eventId);

        return $this->sendSuccessResponse('Calendar Events Deleted Successfully');
    }

    public function checkGoogleCalendar()
    {
        if (!$this->checkRefreshToken(auth('api')->user())) {
            return $this->sendSuccessResponse('Google Calendar is not connected', [
                'status' => 'not_connected',
                'breadcrumb' => 'Google Integration',
                'title' => 'Google Integration',
            ]);
        }

        return $this->sendSuccessResponse('Google Calendar is connected', [
            'status' => 'connected',
            'breadcrumb_main' => 'Google Calendar',
            'breadcrumb' => 'Calendar',
            'title' => 'Google Calendar',
        ]);
    }

    public function oauth(Request $request)
    {
        $userId = auth('api')->id();
        $authCode = urldecode($request->input('code'));

        $client = $this->getClient();
        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
        $client->setAccessToken(json_encode($accessToken));

        $service = new Oauth2($client);
        $userFromGoogle = $service->userinfo->get();

        if (auth('api')->check()) {
            $user = User::find($userId);
            $user->provider_id = $userFromGoogle->id;
            $user->provider_name = 'google';
            $user->google_access_token = json_encode($accessToken);
            $user->save();
        }

        return $this->sendSuccessResponse('Google Calendar connected successfully', [
            'message' => '',
            'google_user' => [
                'id' => $userFromGoogle->id,
                'email' => $userFromGoogle->email,
                'name' => $userFromGoogle->name,
            ],
            'status' => 'connected',
        ]);
    }

    public function getAuthUrl()
    {
        $client = $this->getClient();
        $authUrl = $client->createAuthUrl();

        return $this->sendSuccessResponse('Use this URL to connect Google Calendar', [
            'auth_url' => $authUrl,
        ]);
    }

    public function disconnectGoogleCalendar(Request $request)
    {
        $userId = auth('api')->id();
        $user = User::find($userId);

        $user->google_access_token = null;
        $user->save();

        return $this->sendSuccessResponse('Calender Disconnected Successfully');
    }
}
