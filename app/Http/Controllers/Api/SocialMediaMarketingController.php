<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FacebookPostService;
use App\Services\GoogleBusinessService;
use App\Services\InstagramService;
use App\Services\LinkedInService;
use App\Services\TikTokAdsService;
use App\Services\TikTokService;
use App\Services\YouTubeService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @group Request Service
 *
 * @subgroup Social Media Marketing
 *
 * @authenticated
 */
class SocialMediaMarketingController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get Platforms
     */
    public function getPlatforms(): JsonResponse
    {
        $data = [
            [
                'name' => 'linkedin',
                'label' => 'LinkedIn',
                'icon' => 'fa-brands fa-linkedin linkedin',
                'type' => 'planning',
                'title' => 'Access your LinkedIn page data',
                'description' => 'Attract talent towards your brand or customers if you have a B2B business, keeping an active and optimized presence on LinkedIn.',
                'bg_color' => 'bg-linkedin',
                'access_token' => 'linkedin_access_token',
                'provider' => 'linkedin',
                'custom_image' => true,
                'button' => [
                    'account_label' => 'Page',
                    'text' => 'Connect a LinkedIn account',
                    'color' => 'info',
                    // 'route' => url('/auth/linkedin'),
                ],
                'disable' => false,
            ],
            [
                'name' => 'facebook',
                'label' => 'Facebook',
                'icon' => 'fa-brands fa-facebook facebook',
                'type' => 'planning',
                'title' => 'All your Facebook page analytics',
                'description' => 'Track the daily evolution of your Facebook page and the effect of your posts on its growth. Get your audience’s demographic data and review the stats related to the impact of each post.',
                'bg_color' => 'bg-facebook',
                'access_token' => 'facebook_access_token',
                'provider' => 'facebook',
                'custom_image' => true,
                'button' => [
                    'account_label' => 'Page',
                    'text' => 'Connect a Facebook page',
                    'color' => 'primary',
                    // 'route' => url('/auth/facebook'),
                ],
                'disable' => false,
            ],
            [
                'name' => 'instagram',
                'label' => 'Instagram',
                'icon' => 'fa-brands fa-instagram instagram',
                'type' => 'planning',
                'title' => 'Analyze your Instagram metrics',
                'description' => 'Finally you can know about your Instagram community and how it evolves, also keep unlimited storage of all of your publications and related metrics.',
                'bg_color' => 'bg-instagram',
                'access_token' => 'facebook_access_token',
                'provider' => 'facebook',
                'custom_image' => true,
                'button' => [
                    'account_label' => 'Professional Account',
                    'text' => 'Connect an Instagram professional account',
                    'color' => 'primary',
                    // 'route' => url('/auth/facebook'),
                ],
                'disable' => false,
            ],
            [
                'name' => 'google_business',
                'label' => 'Google Business Profile',
                'icon' => 'fas fa-store google_business',
                'type' => 'planning',
                'title' => 'Conquer the map with analytics for Google Business Profile',
                'description' => 'Monitor how your customer value your local business and how photos or videos generate more views of your brand.',
                'bg_color' => 'bg-google_business',
                'access_token' => 'google_business_access_token',
                'provider' => 'google_business',
                'custom_image' => false,
                'button' => [
                    'account_label' => 'Account',
                    'text' => 'Connect a Google Business account',
                    'color' => 'primary',
                    // 'route' => url('/auth/google_business'),
                ],
                'disable' => false,
            ],
            [
                'name' => 'youtube',
                'label' => 'YouTube',
                'icon' => 'fa-brands fa-youtube youtube',
                'type' => 'planning',
                'title' => 'Analyze your video content and maximize YouTube’s potential',
                'description' => 'Get to know your subscribers and follow the progress of your channel. Monitor the evolution of your videos, the generated revenue and the engagement of your audience. Create reports and observe what your competitors are up to.',
                'bg_color' => 'bg-youtube',
                'access_token' => 'youtube_access_token',
                'provider' => 'youtube',
                'custom_image' => true,
                'button' => [
                    'account_label' => 'Account',
                    'text' => 'Connect a YouTube account',
                    'color' => 'danger',
                    // 'route' => url('/auth/youtube'),
                ],
                'disable' => false,
            ],
            [
                'name' => 'tiktok',
                'label' => 'TikTok',
                'icon' => 'fa-brands fa-tiktok tiktok',
                'type' => 'planning',
                'title' => 'Connect your Tiktok and extract all the analytics',
                'description' => 'Extract the analytics related to your TikTok account and improve your strategy based on the data.',
                'access_token' => 'tiktok_access_token',
                'bg_color' => 'bg-tiktok',
                'provider' => 'tiktok',
                'custom_image' => false,
                'button' => [
                    'account_label' => 'Personal account',
                    'text' => 'Connect a TikTok personal account',
                    'color' => 'dark',
                    // 'route' => url('/auth/tiktok'),
                ],
                'disable' => false,
            ],
            [
                'name' => 'xiao_hong_shu',
                'label' => 'Xiao Hong Shu',
                'icon' => 'fas fa-book xiao_hong_shu',
                'type' => 'planning',
                'title' => 'Connect your Xiao Hong Shu and extract all the analytics',
                'description' => 'Extract the analytics related to your Xiao Hong Shu account and improve your strategy based on the data.',
                'access_token' => 'xiao_hong_shu_access_token',
                'bg_color' => 'bg-xiao_hong_shu',
                'provider' => 'xiao_hong_shu',
                'custom_image' => false,
                'button' => [
                    'account_label' => 'Account',
                    'text' => 'Connect a Xiao Hong Shu account',
                    'color' => 'dark',
                    // 'route' => url('/auth/xiao_hong_shu'),
                ],
                'disable' => true,
            ],
            [
                'name' => 'facebook_ads',
                'label' => 'Facebook Ads',
                'icon' => 'fas fa-bullhorn facebook',
                'type' => 'ads',
                'title' => 'Visualize and optimize your investment in advertisement on Facebook Ads',
                'description' => 'Analyze the performance of your campaigns on Facebook Ads. Everything in one place so you don’t miss the whole picture.',
                'bg_color' => 'bg-facebook',
                'access_token' => 'facebook_access_token',
                'provider' => 'facebook',
                'custom_image' => true,
                'button' => [
                    'account_label' => 'Ads Account',
                    'text' => 'Connect a Facebook account',
                    'color' => 'primary',
                    // 'route' => url('/auth/facebook'),
                ],
                'disable' => false,
            ],
            [
                'name' => 'google_ads',
                'label' => 'Google Ads',
                'icon' => 'fa-brands fa-google google_ads-logo',
                'type' => 'ads',
                'title' => 'Visualize and optimize your investment in advertisement on Google Ads',
                'description' => 'Analyze the performance of your campaigns on Google Ads. Everything in one place so you don’t miss the whole picture.',
                'bg_color' => 'bg-google_ads',
                'access_token' => 'google_ads_access_token',
                'provider' => 'google_ads',
                'custom_image' => false,
                'button' => [
                    'account_label' => 'Ads Account',
                    'text' => 'Connect a Google Ads account',
                    'color' => 'primary',
                    // 'route' => url('/auth/google_ads'),
                ],
                'disable' => true,
            ],
            [
                'name' => 'tiktok_ads',
                'label' => 'TikTok Ads',
                'icon' => 'fa-brands fa-tiktok tiktok',
                'type' => 'ads',
                'title' => 'Connect your TikTok Ads account and extract all the analytics',
                'description' => 'Extract the analytics related to your TikTok Ads account and improve your strategy based on the data.',
                'bg_color' => 'bg-tiktok',
                'access_token' => 'tiktok_ads_access_token',
                'provider' => 'tiktok_ads',
                'custom_image' => false,
                'button' => [
                    'account_label' => 'Ads Account',
                    'text' => 'Connect a TikTok Ads Account',
                    'color' => 'dark',
                    // 'route' => url('/auth/tiktok_ads'),
                ],
                'disable' => false,
            ],
        ];

        return $this->sendSuccessResponse('Platform fetched successfully', $data);
    }

    /**
     * Get User Social Media
     */
    public function getUserSocialMedia(Request $request): JsonResponse
    {
        // $validator = Validator::make($request->all(), [
        //     'client_id' => 'required|integer',
        // ], [
        //     'client_id.required' => 'The client_id field is required.',
        //     'client_id.integer' => 'The client_id must be an integer.',
        // ]);

        // if ($validator->fails()) {
        //     $errors = $validator->errors();

        //     $errorMessages = [];
        //     foreach ($errors->all() as $message) {
        //         $errorMessages[] = $message;
        //     }

        //     return $this->sendErrorResponse(implode("\n ", $errorMessages), 400);
        // }

        $clientId = auth('api')->user()->id;
        $user = User::find($clientId);
        if (!$user) {
            return $this->sendErrorResponse('No User Found.', 404);
        }

        $platformsResponse = $this->getPlatforms();
        $platforms = json_decode($platformsResponse->getContent(), true);

        $clientPlatform = [
            'brand' => [
                'profile_picture' => $user->brand_picture ?? null,
            ],
        ];

        foreach ($platforms['data'] as $platform) {
            $provider = $platform['provider'];
            $access_token = $platform['access_token'];

            if (isset($clientPlatform[$provider])) {
                continue;
            }

            if ($platform['type'] == 'planning') {
                $socialMedia = $user->getSocialMediaByProvider($provider);

                $clientPlatform[$provider] = [
                    'status' => $user[$access_token] ? true : false,
                    'profile_picture' => $platform['custom_image'] ? ($socialMedia ? $socialMedia->profile_picture : null) : $user->brand_picture,
                ];
            } else {
                $clientPlatform[$provider] = [
                    'status' => $user[$access_token] ? true : false,
                ];
            }
        }

        return $this->sendSuccessResponse('User social media fetched successfully', $clientPlatform);
    }

    /**
     * Check Access Token
     */
    public function checkAccessToken(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'required|string',
        ], [
            'platform.required' => 'The platform field is required.',
            'platform.integer' => 'The platform must be an string.',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            $errorMessages = [];
            foreach ($errors->all() as $message) {
                $errorMessages[] = $message;
            }

            return $this->sendErrorResponse(implode("\n ", $errorMessages), 400);
        }

        $clientId = auth('api')->user()->id;
        $user = User::find($clientId);
        if (!$user) {
            return $this->sendErrorResponse('No User Found.', 404);
        }

        $checkAccessToken = false;
        if ($request->platform == 'linkedin') {
            $checkAccessToken = $user->linkedin_access_token ? true : false;
            if ($checkAccessToken) {
                $authorization = json_decode($user->linkedin_access_token, true);
                $linkedinService = new LinkedInService($authorization, $user->id);
                $result = $linkedinService->getProfile();

                ActivityLogHelper::save_activity($clientId, 'User linkedin access', 'LinkedInService', 'app');

                if (isset($result->error)) {
                    $checkAccessToken = false;
                }
            }
        }
        if ($request->platform == 'facebook' || $request->platform == 'instagram' || $request->platform == 'facebook_ads') {
            $checkAccessToken = $user->facebook_access_token ? true : false;
            if ($checkAccessToken) {
                $authorization = json_decode($user->facebook_access_token, true);
                $facebookService = new FacebookPostService($authorization, $user->id);
                $result = $facebookService->getProfile();

                ActivityLogHelper::save_activity($clientId, 'User Facebook access', 'FacebookPostService', 'app');

                if (isset($result->error)) {
                    $checkAccessToken = false;
                }
            }
        }
        if ($request->platform == 'google_business') {
            $checkAccessToken = $user->google_business_access_token ? true : false;
            if ($checkAccessToken) {
                $authorization = json_decode($user->google_business_access_token, true);
                $googleBusinessService = new GoogleBusinessService($authorization, $user->google_business_refresh_token, $user->id);
                $result = $googleBusinessService->getProfile();

                if (isset($result->error)) {
                    $checkAccessToken = false;
                }
            }
        }
        if ($request->platform == 'youtube') {
            $checkAccessToken = $user->youtube_access_token ? true : false;
            if ($checkAccessToken) {
                $authorization = json_decode($user->youtube_access_token, true);
                $youtubeService = new YouTubeService($authorization, $user->youtube_refresh_token, $user->id);
                $result = $youtubeService->getProfile();

                if (isset($result->error)) {
                    $checkAccessToken = false;
                }
            }
        }
        if ($request->platform == 'tiktok') {
            $checkAccessToken = $user->tiktok_access_token ? true : false;
            if ($checkAccessToken) {
                $authorization = json_decode($user->tiktok_access_token, true);
                $tiktokService = new TikTokService($authorization, $user->id);
                $result = $tiktokService->getUserInfo();

                if (isset($result->error)) {
                    $checkAccessToken = false;
                }
            }
        }
        if ($request->platform == 'xiao_hong_shu_ads') {
            // TODO: Revisit after xiao hong shu app
        }
        if ($request->platform == 'google_ads') {
            // TODO: Revisit after google ads app
        }
        if ($request->platform == 'tiktok_ads') {
            $checkAccessToken = $user->tiktok_ads_access_token ? true : false;
            if ($checkAccessToken) {
                $authorization = json_decode($user->tiktok_ads_access_token, true);
                $tiktokAdsService = new TikTokAdsService($authorization, $user->id);
                $result = $tiktokAdsService->getAdvertiser();

                if (isset($result->error)) {
                    $checkAccessToken = false;
                }
            }
        }
        $data = [
            'checkAccessToken' => $checkAccessToken,
        ];

        return $this->sendSuccessResponse('Access token checked successfully', $data);
    }

    /**
     * Disconnect Platform
     */
    public function disconnectPlatform(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'required|string',
        ], [
            'platform.required' => 'The platform field is required.',
            'platform.integer' => 'The platform must be an string.',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            $errorMessages = [];
            foreach ($errors->all() as $message) {
                $errorMessages[] = $message;
            }

            return $this->sendErrorResponse(implode("\n ", $errorMessages), 400);
        }

        $clientId = auth('api')->user()->id;
        $user = User::find($clientId);
        if (!$user) {
            return $this->sendErrorResponse('No User Found.', 404);
        }

        if ($request->platform == 'linkedin') {
            $user->linkedin_access_token = null;
        }
        if ($request->platform == 'facebook' || $request->platform == 'instagram' || $request->platform == 'facebook_ads') {
            $user->facebook_access_token = null;
        }
        if ($request->platform == 'google_business') {
            $user->google_business_access_token = null;
            $user->google_business_refresh_token = null;
        }
        if ($request->platform == 'youtube') {
            $user->youtube_access_token = null;
            $user->youtube_refresh_token = null;
        }
        if ($request->platform == 'tiktok') {
            $user->tiktok_access_token = null;
        }
        if ($request->platform == 'xiao_hong_shu') {
            $user->xiao_hong_shu_access_token = null;
        }
        if ($request->platform == 'google_ads') {
            $user->google_ads_access_token = null;
            $user->google_ads_refresh_token = null;
        }
        if ($request->platform == 'tiktok_ads') {
            $user->tiktok_ads_access_token = null;
        }
        $user->save();

        return $this->sendSuccessResponse('Platform disconnected successfully');
    }

    /**
     * Get Ads
     */
    public function indexAds(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'required|string',
        ], [
            'platform.required' => 'The platform field is required.',
            'platform.integer' => 'The platform must be an string.',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            $errorMessages = [];
            foreach ($errors->all() as $message) {
                $errorMessages[] = $message;
            }

            return $this->sendErrorResponse(implode("\n ", $errorMessages), 400);
        }

        $clientId = auth('api')->user()->id;
        $user = User::find($clientId);
        if (!$user) {
            return $this->sendErrorResponse('No User Found.', 404);
        }

        $data = [];
        if ($request->platform == 'facebook_ads') {
            if (!$user->facebook_access_token) {
                return $this->sendErrorResponse('Token Is Invalid.', 403);
            }

            $authorization = json_decode($user->facebook_access_token, true);
            $facebookService = new FacebookPostService($authorization, $user->id);
            $data = $facebookService->getAllCampaings();
        }
        if ($request->platform == 'google_ads') {
            if (!$user->google_access_token) {
                return $this->sendErrorResponse('Token Is Invalid.', 403);
            }
        }
        if ($request->platform == 'tiktok_ads') {
            if (!$user->tiktok_ads_access_token) {
                return $this->sendErrorResponse('Token Is Invalid.', 403);
            }

            $authorization = json_decode($user->tiktok_ads_access_token, true);
            $tiktokAdsService = new TikTokAdsService($authorization, $user->id);
            $data = $tiktokAdsService->getAllCampaigns();
        }

        return $this->sendSuccessResponse('Ads fetched successfully', $data);
    }

    /**
     * Analytics
     */
    public function indexAnalytics(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'required|string',
        ], [
            'platform.required' => 'The platform field is required.',
            'platform.integer' => 'The platform must be an string.',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            $errorMessages = [];
            foreach ($errors->all() as $message) {
                $errorMessages[] = $message;
            }

            return $this->sendErrorResponse(implode("\n ", $errorMessages), 400);
        }

        $clientId = auth('api')->user()->id;
        $user = User::find($clientId);
        if (!$user) {
            return $this->sendErrorResponse('No User Found.', 404);
        }

        $data = [];
        if ($request->platform == 'linkedin') {
            if (!$user->linkedin_access_token) {
                return $this->sendErrorResponse('Token Is Invalid.', 403);
            }

            $authorization = json_decode($user->linkedin_access_token, true);
            $linkedinService = new LinkedInService($authorization, $user->id);
            $data = $linkedinService->getAllAnalytics();
        }
        if ($request->platform == 'facebook') {
            if (!$user->facebook_access_token) {
                return $this->sendErrorResponse('Token Is Invalid.', 403);
            }

            $authorization = json_decode($user->facebook_access_token, true);
            $facebookService = new FacebookPostService($authorization, $user->id);
            $data = $facebookService->getAllAnalytics();
        }
        if ($request->platform == 'instagram') {
            if (!$user->facebook_access_token) {
                return $this->sendErrorResponse('Token Is Invalid.', 403);
            }

            $authorization = json_decode($user->facebook_access_token, true);
            $instagramService = new InstagramService($authorization, $user->id);
            $data = $instagramService->getAllAnalytics();
        }
        if ($request->platform == 'google_business') {
            if (!$user->google_business_access_token) {
                return $this->sendErrorResponse('Token Is Invalid.', 403);
            }

            $authorization = json_decode($user->google_business_access_token, true);
            $googleBusinessService = new GoogleBusinessService($authorization, $user->google_business_refresh_token, $user->id);
            $data = $googleBusinessService->getAllAnalytics();
        }
        if ($request->platform == 'youtube') {
            if (!$user->youtube_access_token) {
                return $this->sendErrorResponse('Token Is Invalid.', 403);
            }

            $authorization = json_decode($user->youtube_access_token, true);
            $youtubeService = new YouTubeService($authorization, $user->youtube_refresh_token, $user->id);
            $data = $youtubeService->getAllAnalytics();
        }
        if ($request->platform == 'tiktok') {
            if (!$user->tiktok_access_token) {
                return $this->sendErrorResponse('Token Is Invalid.', 403);
            }

            $authorization = json_decode($user->tiktok_access_token, true);
            $tiktokService = new TikTokService($authorization, $user->id);
            $data = $tiktokService->getAllAnalytics();
        }
        if ($request->platform == 'facebook_ads') {
            if (!$user->facebook_access_token) {
                return $this->sendErrorResponse('Token Is Invalid.', 403);
            }

            $authorization = json_decode($user->facebook_access_token, true);
            $facebookService = new FacebookPostService($authorization, $user->id);
            $data = $facebookService->getAllAdsAnalytics();
        }
        if ($request->platform == 'tiktok_ads') {
            if (!$user->tiktok_ads_access_token) {
                return $this->sendErrorResponse('Token Is Invalid.', 403);
            }

            $authorization = json_decode($user->tiktok_ads_access_token, true);
            $tiktokAdsService = new TikTokAdsService($authorization, $user->id);
            $data = $tiktokAdsService->getAllAnalytics();
        }

        return $this->sendSuccessResponse('Analytics fetched successfully', $data);
    }
}
