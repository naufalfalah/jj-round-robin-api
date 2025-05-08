<?php

namespace App\Services;

use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class TikTokService
{
    protected $clientKey;

    protected $clientSecret;

    protected $redirectUri;

    protected $scopes;

    protected $oauthUrl = 'https://www.tiktok.com/v2/auth/authorize/';

    protected $baseUrl = 'https://open.tiktokapis.com/v2';

    protected $userId;

    protected $accessToken;

    protected $refreshToken;

    protected $client;

    public function __construct($authorization = null, $userId = null)
    {
        $this->clientKey = config('services.tiktok.client_key');
        $this->clientSecret = config('services.tiktok.client_secret');
        $this->redirectUri = route('auth.tiktok.callback');
        $this->scopes = [
            'user.info.basic',
            'user.info.profile',
            'user.info.stats',
            'video.publish',
            'video.upload',
        ];

        $this->userId = $userId;
        $this->accessToken = $authorization ? $authorization['access_token'] : null;
        $this->refreshToken = $authorization ? $authorization['refresh_token'] : null;

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
                    'message' => $e->getMessage(),
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
        $csrfState = substr(str_shuffle(md5(microtime())), 0, 10);
        setcookie('csrfState', $csrfState, time() + 60);

        $url = "{$this->oauthUrl}";
        $url .= "?client_key={$this->clientKey}";
        $url .= "&redirect_uri={$this->redirectUri}";
        $url .= '&scope='.implode(',', $this->scopes);
        $url .= '&response_type=code';
        $url .= "&state={$csrfState}";

        return $url;
    }

    public function getAccessToken($code)
    {
        $client = new Client;

        $response = $client->post("$this->baseUrl/oauth/token/", [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Cache-Control' => 'no-cache',
            ],
            'form_params' => [
                'client_key' => $this->clientKey,
                'client_secret' => $this->clientSecret,
                'code' => urlencode($code),
                'grant_type' => 'authorization_code',
                'redirect_uri' => $this->redirectUri,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    public function refreshAccessToken()
    {
        $response = $this->client->post("$this->baseUrl/oauth/token/", [
            'form_params' => [
                'client_key' => $this->clientKey,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'refresh_token',
                'refresh_token' => $this->refreshToken,
            ],
        ]);

        $authorization = json_decode($response->getBody(), true);

        // Save new authorization
        $user = User::find($this->userId);
        $user->tiktok_access_token = json_encode($authorization);
        $user->save();

        // Set new access token
        $this->accessToken = $authorization['access_token'];

        return $authorization;
    }

    public function getUserInfo()
    {
        $params = '?fields=open_id,union_id,avatar_url,display_name,follower_count,following_count,heart_count,video_count';

        try {
            $response = $this->client->get("$this->baseUrl/user/info/$params");

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getResponse() && $e->getResponse()->getStatusCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, []);
            }

            return $this->handleError($e);
        }
    }

    /**
     * @param PostSchedule $postSchedule
     */
    public function publishVideo($postSchedule)
    {
        try {
            $response = $this->client->post("$this->baseUrl/post/publish/video/init/", [
                'json' => [
                    'post_info' => [
                        'title' => $postSchedule->tiktok_title,
                        'privacy_level' => $postSchedule->tiktok_privacy_level,
                        'disable_comment' => $postSchedule->tiktok_disable_comment ? true : false,
                        'disable_duet' => $postSchedule->tiktok_disable_duet ? true : false,
                        'disable_stitch' => $postSchedule->tiktok_disable_stitch ? true : false,
                        'video_cover_timestamp_ms' => 1000,
                    ],
                    'source_info' => [
                        'source' => 'PULL_FROM_URL',
                        'video_url' => url($postSchedule->tiktok_video),
                    ],
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getResponse() && $e->getResponse()->getStatusCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, [$postSchedule]);
            }

            return $this->handleError($e);
        }
    }

    /**
     * @param PostSchedule $postSchedule
     */
    public function publishContent($postSchedule)
    {
        try {
            $tiktok_contents = explode(',', $postSchedule->tiktok_content);
            $photo_images = [];
            foreach ($tiktok_contents as $content) {
                $photo_images[] = url($content);
            }

            $response = $this->client->post("$this->baseUrl/post/publish/content/init/", [
                'json' => [
                    'post_info' => [
                        'title' => $postSchedule->tiktok_title,
                        'description' => $postSchedule->tiktok_description,
                        'privacy_level' => $postSchedule->tiktok_privacy_level,
                        'disable_comment' => $postSchedule->tiktok_disable_comment ? true : false,
                        'video_cover_timestamp_ms' => 1000,
                    ],
                    'source_info' => [
                        'source' => 'PULL_FROM_URL',
                        'photo_cover_index' => 1,
                        'photo_images' => $photo_images,
                    ],
                    'post_mode' => 'DIRECT_POST',
                    'media_type' => 'PHOTO',
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getResponse() && $e->getResponse()->getStatusCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, [$postSchedule]);
            }

            return $this->handleError($e);
        }
    }

    public function getAllAnalytics()
    {
        $analytics = [
            'total' => [
                'followers' => 0,
                'following' => 0,
                // 'likes' => 0,
                'videos' => 0,
            ],
        ];

        try {
            $getUserInfo = $this->getUserInfo();
            $user = $getUserInfo['data']['user'];

            $analytics['total']['followers'] = $user['follower_count'];
            $analytics['total']['following'] = $user['following_count'];
            // $analytics['total']['likes'] = $user['heart_count'];
            $analytics['total']['videos'] = $user['video_count'];

            return $analytics;
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, []);
            }

            return $this->handleError($e);
        }
    }
}
