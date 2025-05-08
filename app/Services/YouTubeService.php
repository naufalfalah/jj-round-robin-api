<?php

namespace App\Services;

use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\MultipartStream;

class YouTubeService
{
    protected $clientId;

    protected $clientSecret;

    protected $redirectUri;

    protected $scopes;

    protected $baseUrl = 'https://www.googleapis.com/youtube/v3';

    protected $userId;

    protected $accessToken;

    protected $refreshToken;

    protected $client;

    public function __construct($authorization = null, $refreshToken = null, $userId = null)
    {
        $this->clientId = config('services.youtube.client_id');
        $this->clientSecret = config('services.youtube.client_secret');
        $this->redirectUri = route('auth.youtube.callback');
        $this->scopes = [
            'https://www.googleapis.com/auth/youtube',
            'https://www.googleapis.com/auth/youtube.readonly',
            'https://www.googleapis.com/auth/youtube.upload',
            'https://www.googleapis.com/auth/youtube.force-ssl',
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
        $user->youtube_access_token = json_encode($authorization);
        $user->save();

        // Set new access token
        $this->accessToken = $authorization['access_token'];

        return $authorization;
    }

    public function getProfile()
    {
        try {
            $params = '?part=snippet,contentDetails,statistics';
            $params .= '&mine=true';

            $response = $this->client->get("$this->baseUrl/channels$params");

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getResponse() && $e->getResponse()->getStatusCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, []);
            }

            return $this->handleError($e);
        }
    }

    public function getVideoCategories()
    {
        try {
            $response = $this->client->get("$this->baseUrl/videoCategories?regionCode");

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getResponse() && $e->getResponse()->getStatusCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, []);
            }

            return $this->handleError($e);
        }
    }

    public function uploadVideo($snippetContents, $videoPath)
    {
        try {
            $boundary = uniqid();
            $multipartBody = new MultipartStream([
                [
                    'name' => 'snippet',
                    'contents' => $snippetContents,
                    'headers' => [
                        'Content-Type' => 'application/json; charset=UTF-8',
                    ],
                ],
                [
                    'name' => 'video',
                    'contents' => fopen($videoPath, 'r'),
                    'headers' => [
                        'Content-Type' => 'video/*',
                    ],
                ],
            ], $boundary);

            $response = $this->client->post('https://www.googleapis.com/upload/youtube/v3/videos?part=snippet,status&uploadType=multipart', [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->accessToken,
                    'Content-Type' => 'multipart/related; boundary='.$boundary,
                ],
                'body' => $multipartBody,
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getResponse() && $e->getResponse()->getStatusCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, [$snippetContents, $videoPath]);
            }

            return ['error' => true, 'message' => $e->getMessage(), 'details' => json_decode($e->getResponse()->getBody()->getContents(), true)];
        }
    }

    /**
     * @param PostSchedule $postSchedule
     */
    public function createPost($postSchedule)
    {
        try {
            $snippetContents = json_encode([
                'snippet' => [
                    'categoryId' => $postSchedule->youtube_category_id,
                    'title' => $postSchedule->youtube_video_title,
                    'description' => $postSchedule->youtube_video_description,
                    'tags' => explode(',', $postSchedule->youtube_tags),
                ],
                'status' => [
                    'privacyStatus' => $postSchedule->youtube_privacy_status,
                ],
            ]);
            $videoPath = public_path($postSchedule->youtube_video);

            $response = $this->uploadVideo($snippetContents, $videoPath);

            return $response;
        } catch (RequestException $e) {
            return $this->handleError($e);
        }
    }

    public function getAllAnalytics()
    {
        $analytics = [
            'total' => [
                'views' => 0,
                'subcribers' => 0,
                'videos' => 0,
            ],
        ];

        try {
            $getProfile = $this->getProfile();
            $statistics = $getProfile['items'][0]['statistics'];

            $analytics['total']['views'] = $statistics['viewCount'];
            $analytics['total']['subscribers'] = $statistics['subscriberCount'];
            $analytics['total']['videos'] = $statistics['videoCount'];

            return $analytics;
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, []);
            }

            return $this->handleError($e);
        }
    }
}
