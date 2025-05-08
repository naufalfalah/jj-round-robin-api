<?php

namespace App\Services;

use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class InstagramService
{
    protected $appId;

    protected $appSecret;

    protected $redirectUri;

    protected $scopes;

    protected $apiUrl = 'https://graph.facebook.com';

    protected $graphVersion = 'v19.0';

    protected $userId;

    protected $accessToken;

    protected $refreshToken;

    protected $client;

    public function __construct($authorization = null, $userId = null)
    {
        $this->appId = config('services.meta.app_id');
        $this->appSecret = config('services.meta.app_secret');
        $this->redirectUri = route('auth.facebook.callback');
        $this->scopes = [
            'instagram_basic',
            'instagram_content_publish',
            'instagram_manage_comments',
            'instagram_manage_insights',
            'pages_show_list',
            'pages_read_engagement',
        ];

        $this->userId = $userId;
        $this->accessToken = $authorization ? $authorization['access_token'] : null;
        // $this->refreshToken = $authorization ? $authorization['refresh_token'] : null;

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
        $authorization = $this->getLongLivedAccessToken();

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
        $url = "https://www.facebook.com/$this->graphVersion/dialog/oauth";
        $url .= "?client_id={$this->appId}";
        $url .= "&redirect_uri={$this->redirectUri}";
        $url .= '&scope='.implode(',', $this->scopes);

        return $url;
    }

    public function getAccessToken($code)
    {
        $client = new Client;

        $response = $client->post("$this->apiUrl/$this->graphVersion/oauth/access_token", [
            'form_params' => [
                'client_id' => $this->appId,
                'client_secret' => $this->appSecret,
                'redirect_uri' => $this->redirectUri,
                'code' => $code,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    public function getLongLivedAccessToken()
    {
        $client = new Client;

        $response = $client->post("$this->apiUrl/$this->graphVersion/oauth/access_token", [
            'form_params' => [
                'client_id' => $this->appId,
                'client_secret' => $this->appSecret,
                'grant_type' => 'fb_exchange_token',
                'fb_exchange_token' => $this->accessToken,
            ],
        ]);

        $authorization = json_decode($response->getBody(), true);

        // Save new authorization
        $user = User::find($this->userId);
        $user->facebook_access_token = json_encode($authorization);
        $user->save();

        // Set new access token
        $this->accessToken = $authorization['access_token'];

        return $authorization;
    }

    public function getProfile()
    {
        try {
            $response = $this->client->get("$this->apiUrl/$this->graphVersion/me");

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, []);
            }

            return $this->handleError($e);
        }
    }

    public function getProfilePicture()
    {
        try {
            $params = '?redirect=false';

            $response = $this->client->get("$this->apiUrl/$this->graphVersion/me/picture$params");

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, []);
            }

            return $this->handleError($e);
        }
    }

    public function getPages()
    {
        try {
            $response = $this->client->get("$this->apiUrl/$this->graphVersion/me/accounts");

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, []);
            }

            return $this->handleError($e);
        }
    }

    public function getBusinessAccount($pageAccessToken)
    {
        try {
            $params = '?access_token='.$pageAccessToken;
            $params .= '&fields=id,name,access_token,instagram_business_account,username';

            $response = $this->client->get("$this->apiUrl/$this->graphVersion/me/$params");

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, []);
            }

            return $this->handleError($e);
        }
    }

    public function media($instagramAccessToken, $instagramAccountId, $postSchedule)
    {
        try {
            $params = '?access_token='.$instagramAccessToken;

            if ($postSchedule->instagram_post_type == 'IMAGE') {
                $params .= '&image_url='.$postSchedule->instagram_link;
                $params .= '&caption='.$postSchedule->instagram_caption;
            }
            if ($postSchedule->instagram_post_type == 'REELS') {
                $params .= '&media_type=REELS';
                $params .= '&video_url='.$postSchedule->instagram_link;
                $params .= '&caption='.$postSchedule->instagram_caption;
            }
            if ($postSchedule->instagram_post_type == 'IMAGE STORIES') {
                $params .= '&media_type=STORIES';
                $params .= '&image_url='.$postSchedule->instagram_link;
            }
            if ($postSchedule->instagram_post_type == 'VIDEO STORIES') {
                $params .= '&media_type=STORIES';
                $params .= '&video_url='.$postSchedule->instagram_link;
            }

            $response = $this->client->post("$this->apiUrl/$this->graphVersion/$instagramAccountId/media$params");

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, []);
            }

            return $this->handleError($e);
        }
    }

    public function mediaPublish($instagramAccessToken, $instagramAccountId, $creationId)
    {
        try {
            $params = '?access_token='.$instagramAccessToken;
            $params .= '&creation_id='.$creationId;

            $response = $this->client->post("$this->apiUrl/$this->graphVersion/$instagramAccountId/media_publish$params");

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, []);
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
            // Get Page Access Token
            $getPages = $this->getPages();
            $dataArray = $getPages['data'];

            $pageAccessToken = null;
            if (count($dataArray)) {
                $pageAccessToken = $dataArray[0]['access_token'];
            }

            // Get Instagram Business Account Access Token & Id
            $getBusinessAccount = $this->getBusinessAccount($pageAccessToken);
            $instagramAccessToken = $getBusinessAccount['access_token'];
            if ($getBusinessAccount['instagram_business_account']) {
                $instagramAccountId = $getBusinessAccount['instagram_business_account']['id'];
            }

            // Publish Content
            $media = $this->media($instagramAccessToken, $instagramAccountId, $postSchedule);
            $mediaId = $media['id'];

            // Save post ID
            $postSchedule->instagram_post_id = $mediaId;
            $postSchedule->save();

            $mediaPublish = $this->mediaPublish($instagramAccessToken, $instagramAccountId, $mediaId);

            return $mediaPublish;
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, [$postSchedule]);
            }

            return $this->handleError($e);
        }
    }

    public function insights($instagramAccessToken, $instagramAccountId)
    {
        try {
            $params = '?access_token='.$instagramAccessToken;
            $params .= '&metric=impressions,reach,profile_views';
            $params .= '&period=day';

            $response = $this->client->get("$this->apiUrl/$this->graphVersion/$instagramAccountId/insights$params");

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, []);
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
            // Get Page Access Token
            $getPages = $this->getPages();
            $dataArray = $getPages['data'];

            $pageAccessToken = null;
            if (count($dataArray)) {
                $pageAccessToken = $dataArray[0]['access_token'];
            }

            // Get Instagram Business Account Access Token & Id
            $getBusinessAccount = $this->getBusinessAccount($pageAccessToken);
            $instagramAccessToken = $getBusinessAccount['access_token'];
            if ($getBusinessAccount['instagram_business_account']) {
                $instagramAccountId = $getBusinessAccount['instagram_business_account']['id'];
            }

            $insights = $this->insights($instagramAccessToken, $instagramAccountId);
            if ($insights['data']) {
                foreach ($insights['data'] as $metric) {
                    $metricValue = [];
                    switch ($metric['name']) {
                        case 'impressions':
                            foreach ($metric['values'] as $value) {
                                $analytics['data']['impressions'][] = $value['value'];
                                $analytics['total']['impressions'] += $value['value'];
                            }
                            break;
                        case 'reach':
                            foreach ($metric['values'] as $value) {
                                $analytics['data']['reach'][] = $value['value'];
                                $analytics['total']['reach'] += $value['value'];
                            }
                            break;
                        case 'profile_views':
                            foreach ($metric['values'] as $value) {
                                $analytics['data']['views'][] = $value['value'];
                                $analytics['total']['views'] += $value['value'];
                            }
                            break;
                    }
                }
            }
            $analytics['data']['impressions'] = array_reverse(array_pad($analytics['data']['impressions'], 7, 0));
            $analytics['data']['reach'] = array_reverse(array_pad($analytics['data']['reach'], 7, 0));
            $analytics['data']['views'] = array_reverse(array_pad($analytics['data']['views'], 7, 0));

            return $analytics;
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, []);
            }

            return $this->handleError($e);
        }
    }
}
