<?php

namespace App\Services;

use Carbon\Carbon;
use DateTime;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class CloudTalkService
{
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'auth' => [config('cloudtalk.CLOUDTALK_ID'), config('cloudtalk.CLOUDTALK_SECRET')],
        ]);
    }

    public function getCallListHistory(int $limit = 10, int $page = 1, string $startDate = '', string $endDate = '', mixed $tagId = '')
    {
        try {
            $url = 'https://my.cloudtalk.io/api/calls/index.json';
            $param = [
                'limit' => $limit,
                'page' => $page,
            ];
            if ($startDate != '') {
                $param['date_from'] = $startDate;
            }
            if ($endDate != '') {
                $param['date_to'] = $endDate;
            }
            if (!empty($tagId)) {
                $param['tag_id'] = $tagId;
            }
            $url .= '?'.http_build_query($param);
            $response = $this->client->get($url);
            $body = $response->getBody();
            $data = json_decode($body);

            $row = [
                'itemsCount' => $data->responseData->itemsCount,
                'pageCount' => $data->responseData->pageCount,
                'pageNumber' => $data->responseData->pageNumber,
                'limit' => $data->responseData->limit,
                'data' => [],
            ];
            foreach ($data->responseData->data as $item) {
                if ($item->Cdr && $item->Agent) {
                    $startTime = Carbon::parse($item->Cdr->started_at);
                    $endTime = Carbon::parse($item->Cdr->ended_at);
                    $duration = $endTime->diff($startTime)->format('%H:%I:%S');
                    // MAKE SURE OUTBONDNUMBER AND FIRSTNAME IS EXISTS
                    if (isset($item->Agent->default_outbound_number) && isset($item->Agent->firstname)) {
                        $row['data'][] = [
                            'type' => $item->Cdr->type,
                            'contact_number' => $item->Agent->default_outbound_number,
                            'agent_firstname' => $item->Agent->firstname,
                            'agent_lastname' => $item->Agent->lastname ?? '',
                            'started_at' => Carbon::parse($item->Cdr->started_at)->format('d M Y H:i:s'),
                            'duration' => $duration,
                            'recorded' => $item->Cdr->recorded,
                            'recording_link' => $item->Cdr->recording_link ?? '',
                        ];
                    }
                }
            }

            return $row;
        } catch (\Exception $e) {
            if (config('app.env') != 'production') {
                Log::error($e->getMessage());
            }

            return null;
        }
    }

    public function getGroupStisticCard()
    {
        try {
            $response = $this->client->get('https://my.cloudtalk.io/api/statistics/realtime/groups.json');
            $body = $response->getBody();
            $data = json_decode($body);
            $itemTotalCall = [];
            $itemTotalUnanswer = [];
            $itemAvgCallDuration = [];
            $itemAvgWaitingTime = [];
            foreach ($data->responseData->data->groups as $item) {
                $totalCall = $item->answered + $item->unanswered;
                $itemTotalCall[] = $totalCall;
                $itemTotalUnanswer[] = $item->unanswered;
                $itemAvgCallDuration[] = $item->avg_call_duration;
                $itemAvgWaitingTime[] = $item->avg_waiting_time;
            }
            $row = [
                'total_call' => array_sum($itemTotalCall),
                'total_unanswered' => array_sum($itemTotalUnanswer),
                'average_call_duration' => array_sum($itemAvgCallDuration) / count($itemAvgCallDuration),
                'average_waiting_time' => array_sum($itemAvgWaitingTime) / count($itemAvgWaitingTime),
            ];

            return $row;
        } catch (\Exception $e) {
            if (config('app.env') != 'production') {
                Log::error($e->getMessage());
            }
            $row = [
                'total_call' => 0,
                'total_unanswered' => 0,
                'average_call_duration' => 0,
                'average_waiting_time' => 0,
            ];

            return $row;
        }
    }

    public function getStatisticCall(string $startDate, string $endDate, mixed $tagId = '')
    {
        $limit = 50;
        $page = 1;
        $formatStartDate = date('Y-m-d', strtotime($startDate));
        $formatEndDate = date('Y-m-d', strtotime($endDate));
        $dataReturn = [];

        // Initialize an array to store all hours in a day
        $allHours = array_map(function ($hour) {
            return sprintf('%02d:00:00', $hour);
        }, range(0, 23));

        $currentDate = new DateTime($formatStartDate);
        $endDateObj = new DateTime($formatEndDate);

        while ($currentDate <= $endDateObj) {
            $date = $currentDate->format('Y-m-d');
            $dataReturn[$date] = array_fill_keys($allHours, 0);
            $currentDate->modify('+1 day');
        }

        while ($page < 100) {
            $url = 'https://my.cloudtalk.io/api/calls/index.json';
            $param = [
                'limit' => $limit,
                'page' => $page,
                'date_from' => $formatStartDate,
                'date_to' => $formatEndDate,
            ];
            if (!empty($tagId)) {
                $param['tag_id'] = $tagId;
            }
            $url .= '?'.http_build_query($param);
            $response = $this->client->get($url);
            $body = $response->getBody();
            $data = json_decode($body, true);

            // Extracting the 'data' array from the response
            $dataArray = $data['responseData']['data'];

            if (!empty($dataArray)) {
                // Iterate through each data entry
                foreach ($dataArray as $dataItem) {
                    // MAKE SURE DATA AGENT NUMBER AND FIRSTNAME IS EXISTS.
                    if (isset($dataItem['Agent']['default_outbound_number']) && isset($dataItem['Agent']['firstname'])) {
                        // Extracting the date and time from 'started_at'
                        $dateTime = new DateTime($dataItem['Cdr']['started_at']);
                        $date = $dateTime->format('Y-m-d');
                        $hour = $dateTime->format('H:00:00');

                        // Incrementing the count for the specific hour
                        $dataReturn[$date][$hour]++;
                    }
                }
            } else {
                break;
            }

            $page++;
        }

        return $dataReturn;
    }

    public function getTags()
    {
        try {
            $response = $this->client->get('https://my.cloudtalk.io/api/tags/index.json');
            $body = $response->getBody();
            $data = json_decode($body, true);
            if ($data['responseData']['data']) {
                return $data['responseData']['data']['Tag'];
            } else {
                return [];
            }
        } catch (Exception $e) {
            return [];
        }
    }
}
