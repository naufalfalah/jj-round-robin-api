<?php

namespace App\Services;

use App\Models\Admin;
use App\Traits\GoogleTrait;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class GoogleCalendarService
{
    use GoogleTrait;

    protected $baseUrl = 'https://www.googleapis.com/calendar/v3';
    protected $client;
    protected $timeZone = 'Asia/Singapore';

    public function __construct($access_token = null)
    {
        $this->getAdminClient();
        $this->initializeClient($access_token);
    }

    private function initializeClient($access_token)
    {
        if ($access_token) {
            $authorization = json_decode($access_token, true);
        } else {
            $admin = Admin::find(1);
            $authorization = json_decode($admin->google_access_token, true);
        }
        $accessToken = $authorization['access_token'];

        $this->client = new Client([
            'headers' => [
                'Authorization' => "Bearer $accessToken",
                'Content-Type' => 'application/json',
                // 'developer-token' => config('services.google.developer_token'),
            ]
        ]);
    }

    private function refreshToken()
    {
        $admin = Admin::find(1);
        if (isset($admin->google_refresh_token)) {
            $refreshToken = $admin->google_refresh_token;
            $clientId = config('services.google.client_id');
            $clientSecret = config('services.google.client_secret');
    
            $client = new Client();
            $response = $client->post('https://oauth2.googleapis.com/token', [
                'form_params' => [
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refreshToken,
                ],
            ]);
    
            $newTokenData = json_decode($response->getBody(), true);
            $admin->google_access_token = json_encode($newTokenData);
            $admin->save();
        }

        $this->initializeClient();
    }

    private function handleRequest($method, $url, $options = [])
    {
        try {
            $response = $this->client->{$method}($url, $options);
        } catch (RequestException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
    
            if ($statusCode == 400) {
                $response = $e->getResponse();
            } elseif ($statusCode == 401) {
                $response = $e->getResponse();
            } elseif ($statusCode == 403) {
                $response = $e->getResponse();
            } else {
                throw $e;
            }
        }

        $response = json_decode($response->getBody(), true);
        
        if (isset($response['error'])) {
            if (isset($response['error']['status'])) {
                return [
                    'errors' => $response['error']['status'],
                ];
            }
            $errorCode = $response['error']['details'][0]['errors'][0]['errorCode'];
            return [
                'errors' => $errorCode,
            ];
        }

        return $response;
    }

    public function listCalendars()
    {
        $response = $this->client->get("{$this->baseUrl}/users/me/calendarList");

        return json_decode($response->getBody()->getContents(), true);
    }

    public function createCalendar($summary = 'Jome Journey Calendar')
    {
        $response = $this->client->post("{$this->baseUrl}/calendars", [
            'json' => [
                'summary' => $summary,
                'timeZone' => $this->timeZone,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function updateCalendar($calendarId, $summary)
    {
        $response = $this->client->put("{$this->baseUrl}/calendars/{$calendarId}", [
            'json' => [
                'summary' => $summary,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function deleteCalendar($calendarId)
    {
        $this->client->delete("{$this->baseUrl}/calendars/{$calendarId}");

        return true;
    }

    public function listEvents($calendarId, $timeMin, $timeMax)
    {
        $response = $this->client->get("{$this->baseUrl}/calendars/{$calendarId}/events", [
            'query' => [
                'timeMin' => $timeMin,
                'timeMax' => $timeMax,
                'maxResults' => 30,
                'singleEvents' => 'true',
                'orderBy' => 'startTime'
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function createEvent($calendarId, $summary, $description, $startTime, $endTime, $attendees = [])
    {
        $response = $this->client->post("{$this->baseUrl}/calendars/{$calendarId}/events", [
            'json' => [
                'summary' => $summary,
                'description' => $description,
                'start' => [
                    'dateTime' => $startTime,
                    'timeZone' => $this->timeZone,
                ],
                'end' => [
                    'dateTime' => $endTime,
                    'timeZone' => $this->timeZone,
                ],
                'attendees' => $attendees,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getEvent($calendarId, $eventId)
    {
        $response = $this->client->get("{$this->baseUrl}/calendars/{$calendarId}/events/{$eventId}");

        return json_decode($response->getBody()->getContents(), true);
    }

    public function updateEvent($calendarId, $eventId, $summary, $description, $startTime, $endTime)
    {
        $response = $this->client->put("{$this->baseUrl}/calendars/{$calendarId}/events/{$eventId}", [
            'json' => [
                'summary' => $summary,
                'description' => $description,
                'start' => [
                    'dateTime' => $startTime,
                    'timeZone' => $this->timeZone,
                ],
                'end' => [
                    'dateTime' => $endTime,
                    'timeZone' => $this->timeZone,
                ],
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function deleteEvent($calendarId, $eventId)
    {
        $this->client->delete("{$this->baseUrl}/calendars/{$calendarId}/events/{$eventId}");

        return true;
    }
}
