<?php

namespace App\Services;

use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class GoogleBusinessService
{
    protected $clientId;

    protected $clientSecret;

    protected $redirectUri;

    protected $scopes;

    protected $accountManagerUrl = 'https://mybusinessaccountmanagement.googleapis.com/v1';

    protected $baseUrl = 'https://mybusiness.googleapis.com/v4';

    protected $userId;

    protected $accessToken;

    protected $refreshToken;

    protected $client;

    public function __construct($authorization = null, $refreshToken = null, $userId = null)
    {
        $this->clientId = config('services.google_business.client_id');
        $this->clientSecret = config('services.google_business.client_secret');
        $this->redirectUri = route('auth.google_business.callback');
        $this->scopes = [
            'https://www.googleapis.com/auth/business.manage',
            'https://www.googleapis.com/auth/plus.business.manage',
        ];

        $this->userId = $userId;
        $this->accessToken = $authorization ? $authorization['access_token'] : null;
        $this->refreshToken = $refreshToken;

        $this->client = new Client([
            'headers' => [
                'Authorization' => "Bearer $this->accessToken",
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
                'Authorization' => "Bearer {$authorization['access_token']}",
                'Content-Type' => 'application/json',
            ],
        ]);

        // Retry the original function with the new token
        return call_user_func_array([$this, $originalFunction], $params);
    }

    public function getRedirectUrl()
    {
        $url = 'https://accounts.google.com/o/oauth2/v2/auth';
        $url .= "?client_id={$this->clientId}";
        $url .= "&redirect_uri={$this->redirectUri}";
        $url .= '&response_type=code';
        $url .= '&scope='.implode(' ', $this->scopes);
        $url .= '&access_type=offline';
        $url .= '&prompt=consent';

        return $url;
    }

    public function getAccessToken($code)
    {
        $client = new Client;

        $params = '?client_id='.$this->clientId;
        $params .= '&client_secret='.$this->clientSecret;
        $params .= '&grant_type=authorization_code';
        $params .= '&code='.$code;
        $params .= '&redirect_uri='.$this->redirectUri;

        $response = $client->post("https://oauth2.googleapis.com/token$params", [
            'headers' => [
                'Content-Length' => 0,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    public function refreshAccessToken()
    {
        $client = new Client;

        $params = '?client_id='.$this->clientId;
        $params .= '&client_secret='.$this->clientSecret;
        $params .= '&grant_type=refresh_token';
        $params .= '&refresh_token='.$this->refreshToken;

        $response = $client->post("https://oauth2.googleapis.com/token$params", [
            'headers' => [
                'Content-Length' => 0,
            ],
        ]);

        $authorization = json_decode($response->getBody(), true);

        // Save new authorization
        $user = User::find($this->userId);
        $user->google_business_access_token = json_encode($authorization);
        $user->save();

        // Set new access token
        $this->accessToken = $authorization['access_token'];

        return $authorization;
    }

    public function getProfile()
    {
        try {
            $response = $this->client->get("$this->accountManagerUrl/accounts");

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getResponse() && $e->getResponse()->getStatusCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, []);
            }

            return $this->handleError($e);
        }
    }

    public function getAccountLocation($accountId)
    {
        try {
            $params = '?read_mask=name';

            $response = $this->client->get("$this->accountManagerUrl/accounts/$accountId/locations$params");

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getResponse() && $e->getResponse()->getStatusCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, [$accountId]);
            }

            return $this->handleError($e);
        }
    }

    public function createLocalPosts($accountId, $locationId, $postData)
    {
        $postBody = [
            'summary' => $postData->google_business_summary,
            'topicType' => 'STANDARD',
        ];
        if ($postData->google_business_media) {
            $postBody['media'] = [
                [
                    'mediaFormat' => 'PHOTO',
                    'sourceUrl' => $postData->google_business_media,
                ],
            ];
        }

        try {
            $response = $this->client->post("$this->baseUrl/accounts/$accountId/locations/$locationId/localPosts", [
                'json' => $postBody,
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getResponse() && $e->getResponse()->getStatusCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, [$accountId, $locationId, $postData]);
            }

            return ['error' => true, 'message' => $e->getMessage(), 'details' => json_decode($e->getResponse()->getBody()->getContents(), true)];
        }
    }

    public function getReviews($accountId, $locationId)
    {
        try {
            $response = $this->client->get("$this->baseUrl/accounts/$accountId/locations/$locationId/reviews");

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getResponse() && $e->getResponse()->getStatusCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, [$accountId, $locationId]);
            }

            return $this->handleError($e);
        }
    }

    /**
     * @param PostSchedule $postSchedule
     */
    public function createPost($postSchedule)
    {
        try {
            $getProfile = $this->getProfile();
            $accountName = $getProfile['accounts'][0]['name'];
            $accountId = str_replace('accounts/', '', $accountName);

            $getAccountLocation = $this->getAccountLocation($accountId);
            $locationName = $getAccountLocation['locations'][0]['name'];
            $locationId = str_replace('locations/', '', $locationName);

            $response = $this->createLocalPosts($accountId, $locationId, $postSchedule);

            return $response;

        } catch (RequestException $e) {
            return $this->handleError($e);
        }
    }

    public function getAllAnalytics()
    {
        $analytics = [
            'total' => [
                'average_rating' => 0,
                'total_review' => 0,
            ],
        ];

        try {
            $getProfile = $this->getProfile();
            $accountName = $getProfile['accounts'][0]['name'];
            $accountId = str_replace('accounts/', '', $accountName);

            $getAccountLocation = $this->getAccountLocation($accountId);
            $locationName = $getAccountLocation['locations'][0]['name'];
            $locationId = str_replace('locations/', '', $locationName);

            $getReviews = $this->getReviews($accountId, $locationId);

            $analytics['total']['average_rating'] = $getReviews['averageRating'];
            $analytics['total']['total_review'] = $getReviews['totalReviewCount'];

            return $analytics;
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, []);
            }

            return $this->handleError($e);
        }
    }
}
