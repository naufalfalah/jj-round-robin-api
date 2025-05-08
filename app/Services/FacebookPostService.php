<?php

namespace App\Services;

use App\Models\User;
use DateInterval;
use DatePeriod;
use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class FacebookPostService
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
        $this->appId = config('services.facebook.app_id');
        $this->appSecret = config('services.facebook.app_secret');
        $this->redirectUri = route('auth.facebook.callback');
        $this->scopes = [
            'ads_management',
            'ads_read',
            'business_management',
            'page_events',
            'pages_manage_ads',
            'pages_manage_engagement',
            'pages_manage_posts',
            'pages_read_engagement',
            'pages_read_user_content',
            'pages_show_list',
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

    /**
     * @param PostSchedule $postSchedule
     */
    public function getPageFeed($postSchedule)
    {
        try {
            $pageId = $postSchedule->facebook_container_id;

            $data = $this->getPages();
            $dataArray = $data['data'];

            $pageAccessToken = null;
            foreach ($dataArray as $item) {
                if ($item['id'] === $pageId) {
                    $pageAccessToken = $item['access_token'];
                }
            }
            $params = '?access_token='.$pageAccessToken;

            $response = $this->client->get("$this->apiUrl/$pageId/feed$params");

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, [$postSchedule]);
            }

            return $this->handleError($e);
        }
    }

    /**
     * @param string $pageId
     * @param string $params
     */
    public function postFeed($pageId, $params)
    {
        try {
            $response = $this->client->post("$this->apiUrl/$this->graphVersion/$pageId/feed$params");

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, [$pageId, $params]);
            }

            return $this->handleError($e);
        }
    }

    /**
     * @param string $pageId
     * @param string $params
     */
    public function postPhoto($pageId, $params)
    {
        try {
            $response = $this->client->post("$this->apiUrl/$this->graphVersion/$pageId/photos$params");

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, [$pageId, $params]);
            }

            return $this->handleError($e);
        }
    }

    /**
     * @param string $pageId
     * @param string $pageAccessToken
     * @param int $videoSize
     */
    public function postVideoStart($pageId, $pageAccessToken, $videoSize)
    {
        try {
            $params = '?upload_phase=start';
            $params .= "&access_token={$pageAccessToken}";
            $params .= "&file_size={$videoSize}";

            $response = $this->client->post("$this->apiUrl/$this->graphVersion/$pageId/videos$params");

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, [$pageId, $pageAccessToken, $videoSize]);
            }

            return $this->handleError($e);
        }
    }

    /**
     * @param string $pageId
     * @param string $pageAccessToken
     * @param string $videoPath
     */
    public function postVideoTransfer($pageId, $pageAccessToken, $uploadSessionId, $videoPath)
    {
        try {
            $response = $this->client->post("$this->apiUrl/$this->graphVersion/$pageId/videos", [
                'multipart' => [
                    [
                        'name' => 'upload_phase',
                        'contents' => 'transfer',
                    ],
                    [
                        'name' => 'access_token',
                        'contents' => $pageAccessToken,
                    ],
                    [
                        'name' => 'upload_session_id',
                        'contents' => $uploadSessionId,
                    ],
                    [
                        'name' => 'start_offset',
                        'contents' => 0,
                    ],
                    [
                        'name' => 'video_file_chunk',
                        'contents' => fopen($videoPath, 'r'),
                        'headers' => [
                            'Content-Type' => 'video/*',
                        ],
                    ],
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, [$pageId, $pageAccessToken, $uploadSessionId]);
            }

            return $this->handleError($e);
        }
    }

    /**
     * @param string $pageId
     * @param string $pageAccessToken
     * @param string $uploadSessionId
     */
    public function postVideoFinish($pageId, $pageAccessToken, $uploadSessionId)
    {
        try {
            $params = '?upload_phase=finish';
            $params .= "&access_token={$pageAccessToken}";
            $params .= "&upload_session_id={$uploadSessionId}";

            $response = $this->client->post("$this->apiUrl/$this->graphVersion/$pageId/videos$params");

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, [$pageId, $pageAccessToken, $uploadSessionId]);
            }

            return $this->handleError($e);
        }
    }

    /**
     * @param string $pageId
     * @param string $params
     * @param string $photoId
     */
    public function postPhotoStories($pageId, $params, $body)
    {
        try {
            $response = $this->client->post("$this->apiUrl/$this->graphVersion/$pageId/photo_stories$params", [
                'json' => $body,
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, [$pageId, $params, $body]);
            }

            return $this->handleError($e);
        }
    }

    /**
     * @param string $pageId
     * @param string $params
     * @param array $body
     */
    public function postVideoStories($pageId, $params, $body = [])
    {
        try {
            $response = $this->client->post("$this->apiUrl/$this->graphVersion/$pageId/video_stories$params", [
                'json' => $body,
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, [$pageId, $params, $body]);
            }

            return $this->handleError($e);
        }
    }

    /**
     * @param string $url
     * @param string $params
     * @param string $fileUrl
     */
    public function uploadVideo($url, $params, $fileUrl)
    {
        try {
            $client = new Client;
            $response = $client->post($url.$params, [
                'headers' => [
                    'file_url' => $fileUrl,
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, [$url, $params, $fileUrl]);
            }

            return $this->handleError($e);
        }
    }

    /**
     * @param PostSchedule $postSchedule
     */
    public function createPagePost($postSchedule)
    {
        try {
            $pageId = $postSchedule->facebook_container_id;

            // Get Page Id
            $data = $this->getPages();
            $dataArray = $data['data'];

            $pageAccessToken = null;
            foreach ($dataArray as $item) {
                if ($item['id'] === $pageId) {
                    $pageAccessToken = $item['access_token'];
                }
            }

            // Post Content
            $params = '?access_token='.$pageAccessToken;
            if ($postSchedule->facebook_post_type == 'FEED') {
                $params .= '&message='.$postSchedule->facebook_message;
                $response = $this->postFeed($pageId, $params);
            } elseif ($postSchedule->facebook_post_type == 'PHOTO') {
                $params .= '&message='.$postSchedule->facebook_message;
                $params .= '&url='.$postSchedule->facebook_link;
                $response = $this->postPhoto($pageId, $params);
            } elseif ($postSchedule->facebook_post_type == 'VIDEO') {
                $videoPath = public_path($postSchedule->facebook_media);
                $videoSize = filesize($videoPath);
                $responseStart = $this->postVideoStart($pageId, $pageAccessToken, $videoSize);

                // Save post ID
                $postSchedule->facebook_post_id = $responseStart['video_id'];
                $postSchedule->save();

                $uploadSessionId = $responseStart['upload_session_id'];
                $this->postVideoTransfer($pageId, $pageAccessToken, $uploadSessionId, $videoPath);
                $response = $this->postVideoFinish($pageId, $pageAccessToken, $uploadSessionId);
            } elseif ($postSchedule->facebook_post_type == 'PHOTO STORIES') {
                $params .= '&published=false';
                $params .= '&url='.$postSchedule->facebook_link;
                $photo = $this->postPhoto($pageId, $params);

                // Save post ID
                $postSchedule->facebook_post_id = $photo['id'];
                $postSchedule->save();

                $params = '?access_token='.$pageAccessToken;
                $body = [
                    'photo_id' => $photo['id'],
                ];
                $response = $this->postPhotoStories($pageId, $params, $body);
            } else {
                $body = [
                    'upload_phase' => 'start',
                ];
                $startVideoStories = $this->postVideoStories($pageId, $params, $body);
                if (isset($startVideoStories['error'])) {
                    dd('startVideoStories', $startVideoStories);
                }

                // Save post ID
                $postSchedule->facebook_post_id = $startVideoStories['video_id'];
                $postSchedule->save();

                $fileUrl = $postSchedule->facebook_link;
                $uploadVideoStories = $this->uploadVideo($startVideoStories['upload_url'], $params, $fileUrl);
                if (isset($uploadVideoStories['error'])) {
                    dd('uploadVideoStories', $uploadVideoStories);
                }

                $body = [
                    'video_id' => $startVideoStories['video_id'],
                    'upload_phase' => 'finish',
                ];
                $response = $this->postVideoStories($pageId, $params, $body);
            }
            if (isset($response['error'])) {
                dd('response', $response);
            }

            return $response;
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, [$postSchedule]);
            }

            return $this->handleError($e);
        }
    }

    public function getPost($pageAccessToken, $postId)
    {
        try {
            $params = '?access_token='.$pageAccessToken;
            $params .= '&fields=id,message,created_time,attachments{media_type,media,subattachments}';

            $response = $this->client->get("$this->apiUrl/$this->graphVersion/$postId/".$params);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, [$postId]);
            }

            return $this->handleError($e);
        }
    }

    public function getMedia($pageAccessToken, $postId)
    {
        try {
            $params = '?access_token='.$pageAccessToken;
            $params .= '&redirect=0';

            $response = $this->client->get("$this->apiUrl/$this->graphVersion/$postId/picture".$params);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, [$postId]);
            }

            return $this->handleError($e);
        }
    }

    /**
     * @param PostSchedule $postSchedule
     */
    public function getPagePost($postSchedule)
    {
        try {
            $pageId = $postSchedule->facebook_container_id;

            // Get Page Id
            $data = $this->getPages();
            $dataArray = $data['data'];

            $pageAccessToken = null;
            foreach ($dataArray as $item) {
                if ($item['id'] === $pageId) {
                    $pageAccessToken = $item['access_token'];
                }
            }

            if ($postSchedule->facebook_post_type == 'PHOTO') {
                $response = $this->getPost($pageAccessToken, $postSchedule->facebook_post_id);
            }
            if ($postSchedule->facebook_post_type == 'VIDEO') {
                $response = $this->getPost($pageAccessToken, $postSchedule->facebook_post_id);
            }
            if ($postSchedule->facebook_post_type == 'PHOTO STORIES') {
                $response = $this->getMedia($pageAccessToken, $postSchedule->facebook_post_id);
            }
            if ($postSchedule->facebook_post_type == 'VIDEO STORIES') {
                $response = $this->getMedia($pageAccessToken, $postSchedule->facebook_post_id);
            }
            if (isset($response['error'])) {
                dd('response', $response);
            }

            return $response;
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, [$postSchedule]);
            }

            return $this->handleError($e);
        }
    }

    public function getPageInsightsPageFan($pageAccessToken, $pageId)
    {
        try {
            $params = '?access_token='.$pageAccessToken;
            $params .= '&period=day';

            $response = $this->client->get("$this->apiUrl/$this->graphVersion/$pageId/insights/page_fans/$params");

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, []);
            }

            return $this->handleError($e);
        }
    }

    public function getPageInsightsPageViewsTotal($pageAccessToken, $pageId)
    {
        try {
            $params = '?access_token='.$pageAccessToken;
            $params .= '&period=day';

            $response = $this->client->get("$this->apiUrl/$this->graphVersion/$pageId/insights/page_views_total/$params");

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, []);
            }

            return $this->handleError($e);
        }
    }

    public function getPageInsightsPageImpressions($pageAccessToken, $pageId)
    {
        try {
            $params = '?access_token='.$pageAccessToken;
            $params .= '&period=day';

            $response = $this->client->get("$this->apiUrl/$this->graphVersion/$pageId/insights/page_impressions/$params");

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, []);
            }

            return $this->handleError($e);
        }
    }

    public function getPageInsightsPagePostImpressions($pageAccessToken, $pageId)
    {
        try {
            $params = '?access_token='.$pageAccessToken;
            $params .= '&period=day';

            $response = $this->client->get("$this->apiUrl/$this->graphVersion/$pageId/insights/page_posts_impressions/$params");

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, []);
            }

            return $this->handleError($e);
        }
    }

    public function getPageInsightsPagePostReactions($pageAccessToken, $pageId)
    {
        try {
            $params = '?access_token='.$pageAccessToken;
            $params .= '&period=day';

            $response = $this->client->get("$this->apiUrl/$this->graphVersion/$pageId/insights/page_actions_post_reactions_total/$params");

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
                'likes' => 0,
                'views' => 0,
                'impressions' => 0,
                'post_impressions' => 0,
                'post_reactions' => 0,
            ],
            'data' => [
                'likes' => [],
                'views' => [],
                'impressions' => [],
                'post_impressions' => [],
                'post_reactions' => [],
            ],
        ];

        try {
            $getPages = $this->getPages();
            $page = $getPages['data'][0];

            $pageId = $page['id'];
            $pageAccessToken = $page['access_token'];

            $getPageInsightsPageFan = $this->getPageInsightsPageFan($pageAccessToken, $pageId);
            if ($getPageInsightsPageFan['data']) {
                foreach ($getPageInsightsPageFan['data'][0]['values'] as $metric) {
                    $analytics['data']['likes'][] = $metric['value'];
                    $analytics['total']['likes'] += $metric['value'];
                }
            }
            $analytics['data']['likes'] = array_reverse(array_pad($analytics['data']['likes'], 7, 0));

            $getPageInsightsPageViewsTotal = $this->getPageInsightsPageViewsTotal($pageAccessToken, $pageId);
            if ($getPageInsightsPageViewsTotal['data']) {
                foreach ($getPageInsightsPageViewsTotal['data'][0]['values'] as $metric) {
                    $analytics['data']['views'][] = $metric['value'];
                    $analytics['total']['views'] += $metric['value'];
                }
            }
            $analytics['data']['views'] = array_reverse(array_pad($analytics['data']['views'], 7, 0));

            $getPageInsightsPageImpressions = $this->getPageInsightsPageImpressions($pageAccessToken, $pageId);
            if ($getPageInsightsPageImpressions['data']) {
                foreach ($getPageInsightsPageImpressions['data'][0]['values'] as $metric) {
                    $analytics['data']['impressions'][] = $metric['value'];
                    $analytics['total']['impressions'] += $metric['value'];
                }
            }
            $analytics['data']['impressions'] = array_reverse(array_pad($analytics['data']['impressions'], 7, 0));

            $getPageInsightsPagePostImpressions = $this->getPageInsightsPagePostImpressions($pageAccessToken, $pageId);
            if ($getPageInsightsPagePostImpressions['data']) {
                foreach ($getPageInsightsPagePostImpressions['data'][0]['values'] as $metric) {
                    $analytics['data']['post_impressions'][] = $metric['value'];
                    $analytics['total']['post_impressions'] += $metric['value'];
                }
            }
            $analytics['data']['post_impressions'] = array_reverse(array_pad($analytics['data']['post_impressions'], 7, 0));

            $getPageInsightsPagePostReactions = $this->getPageInsightsPagePostReactions($pageAccessToken, $pageId);
            if ($getPageInsightsPagePostReactions['data']) {
                foreach ($getPageInsightsPagePostReactions['data'][0]['values'] as $metric) {
                    if ($metric['value']) {
                        $analytics['data']['post_reactions'][] = $metric['value'];
                        $analytics['total']['post_reactions'] += $metric['value'];
                    }
                }
            }
            $analytics['data']['post_reactions'] = array_pad($analytics['data']['post_reactions'], 7, 0);

            return $analytics;
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, []);
            }

            return $this->handleError($e);
        }
    }

    /**
     * Ad Account
     */
    public function getAdAccounts()
    {
        try {
            $response = $this->client->get("$this->apiUrl/$this->graphVersion/me/adaccounts?fields=id,name,objective,start_time,effective_status,business");

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, []);
            }

            return $this->handleError($e);
        }
    }

    public function getAdAccountsInsight($adAccountId, $timeRanges)
    {
        try {
            $params = '?fields=impressions,clicks,reach,spend,cpm,cpc,ctr,actions';
            $params .= '&level=account';
            if (count($timeRanges)) {
                $params .= '&time_ranges='.json_encode($timeRanges);
            }

            $response = $this->client->get("$this->apiUrl/$this->graphVersion/$adAccountId/insights");

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, []);
            }

            return $this->handleError($e);
        }
    }

    public function getCampaigns($adAccountId)
    {
        try {
            $params = '?fields=id,name,status,start_time,stop_time,daily_budget,lifetime_budget';

            $response = $this->client->get("$this->apiUrl/$this->graphVersion/$adAccountId/campaigns$params");

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, [$adAccountId]);
            }

            return $this->handleError($e);
        }
    }

    public function postCampaigns($adAccountId, $body)
    {
        try {
            $response = $this->client->post("$this->apiUrl/$this->graphVersion/$adAccountId/campaigns", $body);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, [$adAccountId, $body]);
            }

            return $this->handleError($e);
        }
    }

    public function getCampaignInsight($campaignId)
    {
        try {
            $params = '?fields=impressions,clicks,spend,cpm,cpc,ctr,actions';

            $response = $this->client->get("$this->apiUrl/$this->graphVersion/$campaignId/insights$params");

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, [$campaignId]);
            }

            return $this->handleError($e);
        }
    }

    public function getAdsets($adAccountId)
    {
        try {
            $params = '?fields=id,name,daily_budget,lifetime_budget,bid_amount';

            $response = $this->client->get("$this->apiUrl/$this->graphVersion/$adAccountId/adsets$params");

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, [$adAccountId]);
            }

            return $this->handleError($e);
        }
    }

    public function getAdsetInisght($adsetId)
    {
        try {
            $params = '?fields=impressions,clicks,spend,cpm,cpc,ctr,actions';

            $response = $this->client->get("$this->apiUrl/$this->graphVersion/$adsetId/insights$params");

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, [$adsetId]);
            }

            return $this->handleError($e);
        }
    }

    public function getAds($adAccountId)
    {
        try {
            $params = '?fields=id,name,adset_id,creative';

            $response = $this->client->get("$this->apiUrl/$this->graphVersion/$adAccountId/ads$params");

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, [$adAccountId]);
            }

            return $this->handleError($e);
        }
    }

    public function getAdInsight($adId)
    {
        try {
            $params = '?fields=impressions,clicks,spend,cpm,cpc,ctr,actions';

            $response = $this->client->get("$this->apiUrl/$this->graphVersion/$adId/insights$params");

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, [$adId]);
            }

            return $this->handleError($e);
        }
    }

    public function getAllCampaings()
    {
        $adAccounts = $this->getAdAccounts();

        $result = [
            'campaigns' => [],
            'adsets' => [],
            'ads' => [],
        ];

        foreach ($adAccounts['data'] as $adAccount) {
            $campaigns = $this->getCampaigns($adAccount['id']);
            foreach ($campaigns['data'] as $campaign) {
                $campaignInisght = $this->getCampaignInsight($campaign['id']);
                $result['campaigns'][] = array_merge($campaign, $campaignInisght['data']);
            }
            $adsets = $this->getAdsets($adAccount['id']);
            foreach ($adsets['data'] as $adset) {
                $adsetInsight = $this->getAdsetInisght($adset['id']);
                $result['adsets'][] = array_merge($adset, $adsetInsight['data']);
            }
            $ads = $this->getAds($adAccount['id']);
            foreach ($ads['data'] as $ad) {
                $adInsight = $this->getAdInsight($ad['id']);
                $result['ads'][] = array_merge($ad, $adInsight['data']);
            }
        }

        return $result;
    }

    public function getAllAdsAnalytics()
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
            $getAdAccounts = $this->getAdAccounts();
            $adAccountId = $getAdAccounts['data'][0]['id'];

            $endDate = new DateTime;
            $interval = new DateInterval('P1D');
            $startDate = (clone $endDate)->sub(new DateInterval('P6D'));
            $dateRange = new DatePeriod($startDate, $interval, $endDate->add($interval));

            $timeRanges = [];
            foreach ($dateRange as $date) {
                $currentDate = $date->format('Y-m-d');
                $timeRanges[] = [
                    'since' => $date,
                    'until' => $date,
                ];
            }

            $getReport = $this->getAdAccountsInsight($adAccountId, $timeRanges);

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

            return $analytics;
        } catch (RequestException $e) {
            return $this->handleError($e);
        }
    }
}
