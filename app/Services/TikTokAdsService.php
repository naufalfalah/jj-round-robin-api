<?php

namespace App\Services;

use DateInterval;
use DatePeriod;
use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class TikTokAdsService
{
    protected $appId;

    protected $secret;

    protected $redirectUri;

    protected $scopes;

    protected $oauthUrl = 'https://business-api.tiktok.com/portal/auth';

    protected $baseUrl = 'https://business-api.tiktok.com/open_api';

    protected $version = 'v1.3';

    protected $userId;

    protected $accessToken;

    protected $advertiserIds;

    protected $client;

    public function __construct($authorization = null, $userId = null)
    {
        $this->appId = config('services.tiktok_ads.app_id');
        $this->secret = config('services.tiktok_ads.secret');
        $this->redirectUri = route('auth.tiktok_ads.callback');

        $this->userId = $userId;
        $this->accessToken = $authorization ? $authorization['access_token'] : null;
        $this->advertiserIds = $authorization ? $authorization['advertiser_ids'] : [];

        $this->client = new Client([
            'headers' => [
                'Access-Token' => "$this->accessToken",
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    protected function handleError(RequestException $e)
    {
        if ($e->getResponse() && $e->getResponse()->getStatusCode() == 401) {
            $errorResponse = [
                'error' => [
                    'message' => 'Token not valid. Please login first.',
                ],
            ];
        } else {
            $errorResponse = [
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ];
        }

        return $errorResponse;
    }

    public function getRedirectUrl()
    {
        $url = $this->oauthUrl;
        $url .= "?app_id={$this->appId}";
        $url .= '&state=your_custom_params';
        $url .= '&redirect_uri='.urlencode($this->redirectUri);

        return $url;
    }

    public function getAccessToken($code)
    {
        $response = $this->client->post("$this->baseUrl/$this->version/oauth2/access_token/", [
            'json' => [
                'app_id' => $this->appId,
                'secret' => $this->secret,
                'auth_code' => $code,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    public function getAdvertiser()
    {
        try {
            $params = "?app_id={$this->appId}";
            $params .= "&secret={$this->secret}";

            $response = $this->client->get("$this->baseUrl/$this->version/oauth2/advertiser/get/$params");

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return $this->handleError($e);
        }
    }

    public function getCampaigns($advertiserId)
    {
        try {
            $params = "?advertiser_id=$advertiserId";

            $response = $this->client->get("$this->baseUrl/$this->version/campaign/get/$params");

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return $this->handleError($e);
        }
    }

    public function getAdgroups($advertiserId)
    {
        try {
            $params = "?advertiser_id=$advertiserId";
            $params .= '&page_size=1000';

            $response = $this->client->get("$this->baseUrl/$this->version/adgroup/get/$params");

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return $this->handleError($e);
        }
    }

    public function getAds($advertiserId)
    {
        try {
            $params = "?advertiser_id=$advertiserId";
            $params .= '&page_size=1000';

            $response = $this->client->get("$this->baseUrl/$this->version/ad/get/$params");

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return $this->handleError($e);
        }
    }

    public function getAllCampaigns()
    {
        $result = [
            'campaigns' => [],
            'adsets' => [],
            'ads' => [],
        ];

        foreach ($this->advertiserIds as $advertiserId) {
            $campaigns = $this->getCampaigns($advertiserId);
            foreach ($campaigns['data']['list'] as $campaign) {
                $result['campaigns'][] = $campaign;
            }
            $adgroups = $this->getAdgroups($advertiserId);
            foreach ($adgroups['data']['list'] as $adgroup) {
                $result['adsets'][] = $adgroup;
            }
            $ads = $this->getAds($advertiserId);
            foreach ($ads['data']['list'] as $ad) {
                $result['ads'][] = $ad;
            }
        }

        return $result;
    }

    public function getReport($advertiserId, $date = [])
    {
        $params = "?advertiser_id=$advertiserId";
        $body = [
            'advertiser_id' => $advertiserId,
            'report_type' => 'BASIC',
            'data_level' => 'AUCTION_ADVERTISER',
            'dimensions' => ['advertiser_id'],
            'metrics' => [
                'conversion',
                'spend',
                'impressions',
                'reach',
                'clicks',
            ],
        ];
        if (count($date)) {
            $body['start_date'] = $date['start_date'];
            $body['end_date'] = $date['end_date'];
        }

        try {
            $response = $this->client->get("$this->baseUrl/v1.2/reports/integrated/get/$params", $body);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return $this->handleError($e);
        }
    }

    public function getAllAnalytics()
    {
        $analytics = [
            'total' => [
                'conversion' => 0,
                'spend' => 0,
                'impressions' => 0,
                'reach' => 0,
                'clicks' => 0,
            ],
            'data' => [
                'conversion' => [],
                'spend' => [],
                'impressions' => [],
                'reach' => [],
                'clicks' => [],
            ],
        ];

        try {
            $advertiserId = $this->advertiserIds[0];

            $endDate = new DateTime;
            $interval = new DateInterval('P1D');
            $startDate = (clone $endDate)->sub(new DateInterval('P6D'));
            $dateRange = new DatePeriod($startDate, $interval, $endDate->add($interval));

            foreach ($dateRange as $date) {
                $currentDate = $date->format('Y-m-d');

                $date = [
                    'start_date' => $currentDate,
                    'end_date' => $currentDate,
                ];

                $getReport = $this->getReport($advertiserId, $date);

                if (isset($getReport['data']['list'])) {
                    if (!count($getReport['data']['list'])) {
                        foreach (array_keys($analytics) as $metric) {
                            $analytics[$metric][$currentDate] = 0;
                        }
                    }

                    foreach ($getReport['data']['list'] as $metricData) {
                        foreach (array_keys($analytics) as $metric) {
                            if (!isset($analytics[$metric][$currentDate])) {
                                $analytics[$metric][$currentDate] = 0;
                            }
                            $analytics[$metric][$currentDate] += $metricData[$metric] ?? 0;
                        }
                    }
                }
            }

            return $analytics;
        } catch (RequestException $e) {
            return $this->handleError($e);
        }
    }
}
