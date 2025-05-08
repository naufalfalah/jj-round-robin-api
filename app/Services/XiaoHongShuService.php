<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class XiaoHongShuService
{
    protected $appId;

    protected $appSecret;

    protected $redirectUri;

    protected $userId;

    protected $accessToken;

    protected $refreshToken;

    protected $client;

    public function __construct($authorization = null, $userId = null)
    {
        $this->appId = config('services.xiao_hong_shu.app_id');
        $this->appSecret = config('services.xiao_hong_shu.app_secret');
        $this->redirectUri = route('auth.xiao_hong_shu.callback');

        $this->userId = $userId;
        $this->accessToken = $authorization ? $authorization['access_token'] : null;
        // $this->refreshToken = $authorization ? $authorization['refresh_token'] : null;

        $this->client = new Client([
            'headers' => [
                // 'Authorization' => "Bearer $this->accessToken",
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
                    'message' => 'An error occurred while retrieving user data.',
                ],
            ];
        }

        return $errorResponse;
    }

    protected function refreshTokenAndRetry($originalFunction, $params)
    {
        $authorization = $this->refreshAccessToken();

        // Update the client with the new access token
        $this->client = new Client([
            'headers' => [
                // 'Authorization' => "Bearer {$authorization['access_token']}",
                'Content-Type' => 'application/json',
            ],
        ]);

        // Retry the original function with the new token
        return call_user_func_array([$this, $originalFunction], $params);
    }

    public function getRedirectUrl()
    {
        // TODO: Revisit after xiao hong shu app
        $url = '';

        return $url;
    }

    public function getAccessToken($code)
    {
        // TODO: Revisit after xiao hong shu app
    }

    public function refreshAccessToken()
    {
        // TODO: Revisit after xiao hong shu app
    }

    /**
     * @param PostSchedule $postSchedule
     */
    public function publishContent($postSchedule)
    {
        try {
            // TODO: Revisit after xiao hong shu app

        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, [$postSchedule]);
            }

            return $this->handleError($e);
        }
    }

    public function getAllAnalytics()
    {
        $analytics = [
            'total' => [
                'impressions' => 0,
                'reach' => 0,
                'views' => 0,
            ],
            'data' => [
                'impressions' => [],
                'reach' => [],
                'views' => [],
            ],
        ];

        try {
            // TODO: Revisit after xiao hong shu app

            return $analytics;
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, []);
            }

            return $this->handleError($e);
        }
    }
}
