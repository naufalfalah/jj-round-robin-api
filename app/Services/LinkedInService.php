<?php

namespace App\Services;

use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class LinkedInService
{
    protected $clientId;

    protected $clientSecret;

    protected $redirectUri;

    protected $scopes;

    protected $oauthUrl = 'https://www.linkedin.com/oauth/v2';

    protected $apiUrl = 'https://api.linkedin.com/v2';

    protected $userId;

    protected $accessToken;

    protected $refreshToken;

    protected $linkedinUserId;

    protected $client;

    public function __construct($authorization = null, $userId = null)
    {
        $this->clientId = config('services.linkedin.client_id');
        $this->clientSecret = config('services.linkedin.client_secret');
        $this->redirectUri = route('auth.linkedin.callback');
        $this->scopes = [
            'openid',
            'profile',
            'email',
            'r_basicprofile',
            'w_member_social',
            'r_organization_social',
            'w_organization_social',
            'r_organization_admin',
            'rw_organization_admin',
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
        $url = "$this->oauthUrl/authorization";
        $url .= '?response_type=code';
        $url .= "&client_id={$this->clientId}";
        $url .= "&redirect_uri={$this->redirectUri}";
        $url .= '&scope='.implode(' ', $this->scopes);

        return $url;
    }

    public function getAccessToken($code)
    {
        $response = $this->client->post("$this->oauthUrl/accessToken", [
            'form_params' => [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'redirect_uri' => $this->redirectUri,
                'grant_type' => 'authorization_code',
                'code' => $code,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    public function refreshAccessToken()
    {
        $response = $this->client->post("$this->oauthUrl/accessToken", [
            'form_params' => [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'refresh_token',
                'refresh_token' => $this->refreshToken,
            ],
        ]);

        $authorization = json_decode($response->getBody(), true);

        // Save new authorization
        $user = User::find($this->userId);
        $user->linkedin_access_token = json_encode($authorization);
        $user->save();

        // Set new access token
        $this->accessToken = $authorization['access_token'];

        return $authorization;
    }

    public function getProfile()
    {
        try {
            $response = $this->client->get("$this->apiUrl/me", [
                'X-Restli-Protocol-Version' => '2.0.0',
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getResponse() && $e->getResponse()->getStatusCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, []);
            }

            return $this->handleError($e);
        }
    }

    public function getUserInfo()
    {
        try {
            $response = $this->client->get("$this->apiUrl/userinfo", [
                'X-Restli-Protocol-Version' => '2.0.0',
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getResponse() && $e->getResponse()->getStatusCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, []);
            }

            return $this->handleError($e);
        }
    }

    public function getOrganizations()
    {
        try {
            $params = '?q=roleAssignee';
            $params .= '&role=ADMINISTRATOR';
            $params .= '&projection=(elements*(*,roleAssignee~(localizedFirstName, localizedLastName), organization~(localizedName)))';

            $response = $this->client->get("$this->apiUrl/organizationAcls$params", [
                'X-Restli-Protocol-Version' => '2.0.0',
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getResponse() && $e->getResponse()->getStatusCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, []);
            }

            return $this->handleError($e);
        }
    }

    public function getOrganization($id)
    {
        try {
            $response = $this->client->get("$this->apiUrl/organizations/$id", [
                'X-Restli-Protocol-Version' => '2.0.0',
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getResponse() && $e->getResponse()->getStatusCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, [$id]);
            }

            return $this->handleError($e);
        }
    }

    public function getGroups()
    {
        try {
            $linkedinUserId = $this->getUserInfo();
            $this->linkedinUserId = 'urn:li:person:'.$linkedinUserId['sub'];

            $params = '?q=member';
            $params .= '&membershipStatuses=OWNER';
            $params .= "&member={$this->linkedinUserId}";
            $params .= '&projection=(elements*(group~:groupDefinition))';

            $response = $this->client->get("$this->apiUrl/groupMemberships$params");

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getResponse() && $e->getResponse()->getStatusCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, []);
            }

            return $this->handleError($e);
        }
    }

    /**
     * @param string $id
     */
    public function getGroup($id)
    {
        try {
            $response = $this->client->get("$this->apiUrl/groups/$id");

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getResponse() && $e->getResponse()->getStatusCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, [$id]);
            }

            return $this->handleError($e);
        }
    }

    /**
     * @param PostSchedule $postSchedule
     */
    public function createPost($postSchedule)
    {
        $postType = $postSchedule->linkedin_post_type;
        $text = $postSchedule->linkedin_text;
        $visibility = $postSchedule->linkedin_visibility;

        $linkedinUserId = $this->getUserInfo();

        $postData = [];

        if ($postSchedule->linkedin_container_type == 'Organization') {
            $postData['author'] = 'urn:li:organization:'.$postSchedule->linkedin_container_id;
        } else {
            $postData['author'] = 'urn:li:person:'.$linkedinUserId['sub'];
        }
        $this->linkedinUserId = $postData['author'];

        if ($postSchedule->linkedin_container_type == 'Group') {
            $postData['containerEntity'] = 'urn:li:group:'.$postSchedule->linkedin_container_id;
        }

        $postData['lifecycleState'] = 'PUBLISHED';
        $postData['specificContent'] = [
            'com.linkedin.ugc.ShareContent' => [
                'shareCommentary' => [
                    'text' => $text,
                    'attributes' => [],
                ],
                'shareMediaCategory' => $postType == 'TEXT' ? 'NONE' : $postType,
            ],
        ];
        $postData['visibility'] = [
            'com.linkedin.ugc.MemberNetworkVisibility' => $visibility,
        ];

        if ($postType == 'ARTICLE') {
            $postData['specificContent']['com.linkedin.ugc.ShareContent']['media'] = [
                [
                    'status' => 'READY',
                    'description' => [
                        'text' => $postSchedule->linkedin_media_description,
                    ],
                    'originalUrl' => $postSchedule->linkedin_link_url,
                    'title' => [
                        'text' => $postSchedule->linkedin_media_title,
                    ],
                ],
            ];
        }

        if ($postType == 'IMAGE' || $postType == 'VIDEO') {
            $filename = basename($postSchedule->linkedin_image);
            $fileExtension = Str::lower(File::extension($filename));

            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
            $videoExtensions = ['mp4', 'avi', 'mov', 'wmv'];

            if (in_array($fileExtension, $imageExtensions)) {
                $mediaType = 'image';
            } elseif (in_array($fileExtension, $videoExtensions)) {
                $mediaType = 'video';
            }

            $imageUploadData = $this->registerImage($postSchedule->linkedin_image, $mediaType);
        }

        if ($postType == 'IMAGE') {
            $postData['specificContent']['com.linkedin.ugc.ShareContent']['media'] = [
                [
                    'status' => 'READY',
                    'media' => "urn:li:digitalmediaAsset:$imageUploadData",
                ],
            ];
        }

        if ($postType == 'VIDEO') {
            $postData['specificContent']['com.linkedin.ugc.ShareContent']['media'] = [
                [
                    'status' => 'READY',
                    'media' => "urn:li:digitalmediaAsset:$imageUploadData",
                    'title' => [
                        'text' => $postSchedule->linkedin_media_title,
                        'attributes' => [],
                    ],
                ],
            ];
        }

        $response = $this->sendPostRequest($postData);

        return true;
    }

    public function registerImage(string $imageUrl, string $mediaType)
    {
        $response = $this->client->post("$this->apiUrl/assets?action=registerUpload", [
            'headers' => [
                'Authorization' => "Bearer $this->accessToken",
                'X-Restli-Protocol-Version' => '2.0.0',
            ],
            'json' => [
                'registerUploadRequest' => [
                    'recipes' => [
                        "urn:li:digitalmediaRecipe:feedshare-$mediaType",
                    ],
                    'owner' => $this->linkedinUserId,
                    'serviceRelationships' => [
                        [
                            'relationshipType' => 'OWNER',
                            'identifier' => 'urn:li:userGeneratedContent',
                        ],
                    ],
                ],
            ],
        ]);

        $uploadResponse = json_decode($response->getBody(), true);

        $uploadUrl = $uploadResponse['value']['uploadMechanism']['com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest']['uploadUrl'];
        $imageUrl = public_path($imageUrl);

        $imageUploadResponse = $this->client->put($uploadUrl, [
            'body' => fopen($imageUrl, 'r'),
            'headers' => [
                'Authorization' => "Bearer $this->accessToken",
                'Content-Type' => 'application/octet-stream',
            ],
        ]);
        $imageUploadData = json_decode($imageUploadResponse->getBody(), true);

        $imageUploadData = $uploadResponse['value']['asset'];
        $imageUploadData = substr($imageUploadData, strlen('urn:li:digitalmediaAsset:'));

        return $imageUploadData;
    }

    /**
     * @param json $postData
     */
    private function sendPostRequest($postData)
    {
        $response = $this->client->post("$this->apiUrl/ugcPosts", [
            'headers' => [
                'Authorization' => "Bearer $this->accessToken",
                'X-Restli-Protocol-Version' => '2.0.0',
            ],
            'json' => $postData,
        ]);

        return json_decode($response->getBody(), true);
    }

    public function organizationFollowerStatistics($organizationId)
    {
        try {
            $params = '?q=organizationalEntity';
            $params .= "&organizationalEntity=$organizationId";

            $response = $this->client->get("$this->apiUrl/organizationalEntityFollowerStatistics$params", [
                'X-Restli-Protocol-Version' => '2.0.0',
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getResponse() && $e->getResponse()->getStatusCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, [$organizationId]);
            }

            return $this->handleError($e);
        }
    }

    public function organizationPageStatistics($organizationId)
    {
        try {
            $params = '?q=organization';
            $params .= "&organization=$organizationId";

            $response = $this->client->get("$this->apiUrl/organizationPageStatistics$params", [
                'X-Restli-Protocol-Version' => '2.0.0',
                'LinkedIn-Version' => '202402',
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getResponse() && $e->getResponse()->getStatusCode() === 401) {
                return $this->refreshTokenAndRetry(__FUNCTION__, [$organizationId]);
            }

            return $this->handleError($e);
        }
    }

    public function getAllAnalytics()
    {
        $analytics = [
            'total' => [
                'organic_followers' => 0,
                'paid_followers' => 0,
                'page_views' => 0,
            ],
            'data' => [
                'organic_follower' => [],
                'paid_followers' => [],
                'page_views' => [],
            ],
        ];

        try {
            $getOrganizations = $this->getOrganizations();
            $organizationId = $getOrganizations['elements'][0]['organization'];

            $organizationFollowerStatistics = $this->organizationFollowerStatistics($organizationId);
            $followerCounts = $organizationFollowerStatistics['elements'][0]['followerCountsByFunction'][0]['followerCounts'];
            $analytics['total']['organic_followers'] = $followerCounts['organicFollowerCount'];
            $analytics['total']['paid_followers'] = $followerCounts['paidFollowerCount'];

            $organizationPageStatistics = $this->organizationPageStatistics($organizationId);
            $clicks = $organizationPageStatistics['elements'][0]['totalPageStatistics']['clicks'];
            $views = $organizationPageStatistics['elements'][0]['totalPageStatistics']['views'];
            $analytics['total']['page_views'] = $views['allPageViews']['pageViews'];

            return $analytics;
        } catch (RequestException $e) {
            return $this->handleError($e);
        }
    }
}
