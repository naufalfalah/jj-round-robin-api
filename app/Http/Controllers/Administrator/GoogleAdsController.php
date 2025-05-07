<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Ads;
use App\Models\GoogleAccount;
use App\Models\GoogleAdsAd;
use App\Models\GoogleAdsConversionAction;
use App\Models\User;
use App\Services\GoogleAdsService;
use App\Traits\GoogleTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GoogleAdsController extends Controller
{
    use GoogleTrait;

    public function google_ads_campaign(Request $request)
    {
        $sessionId = session()->get('sub_account_id');
        $client = User::where('sub_account_id', hashids_decode($sessionId))->first();

        $customerIds = GoogleAdsAd::where('client_id', $client->id)
            ->select('google_account_id', 'customer_id')
            ->distinct()
            ->pluck('google_account_id', 'customer_id')
            ->filter(function ($google_account_id, $customer_id) {
                return !is_null($google_account_id) && !is_null($customer_id);
            })
            ->toArray();
        $googleAdsResoucesNames = GoogleAdsAd::where('client_id', $client->id)
            ->pluck('campaign_resource_name')
            ->filter()
            ->toArray();

        $campaigns = [];
        // Fetch by customerIds
        foreach ($customerIds as $customerId => $googleAccountId) {
            $googleAccount = GoogleAccount::find($googleAccountId);
            $this->checkRefreshTokenNew($googleAccount);
        
            $accessToken = $googleAccount->access_token;
            $googleAdsService = new GoogleAdsService($accessToken);
            $campaignDatas = $googleAdsService->getCampaigns($customerId);

            if (isset($campaignDatas['results'])) {
                // Filter campaign data
                $filteredResults = array_filter($campaignDatas['results'], function ($result) use ($googleAdsResoucesNames) {
                    return in_array($result['campaign']['resourceName'], $googleAdsResoucesNames);
                });
    
                $campaignDatas['results'] = array_values($filteredResults);
                foreach ($filteredResults as $result) {
                    $result['google_account_id'] = $googleAccountId;
                    $result['customer_id'] = $customerId;
                    $campaigns[] = $result;
                }
            }
        }
        
        return view('admin.client_management.google_ads_campaign.index', [
            'breadcrumb_main' => 'Clients Management',
            'breadcrumb' => 'Google Ads Campaign',
            'title' => 'Google Ads Campaign',
            'sub_account_id' => $sessionId,
            'client' => $client,
            'campaigns' => $campaigns,
        ]);
    }

    public function google_ads_campaign_edit(Request $request)
    {
        $sessionId = session()->get('sub_account_id');
        $client = User::where('sub_account_id', hashids_decode($sessionId))->first();

        $googleAccountId = (int) $request->google_account_id;
        $customerId = $request->customer_id;
        $campaignResourceName = $request->campaign_resource_name;
        
        $googleAccount = GoogleAccount::find($googleAccountId);
        if (!$googleAccount || !$customerId || !$campaignResourceName) {
            return redirect()->back()->with('error', 'Invalid data');
        }
        
        $accessToken = $googleAccount->access_token;
        $googleAdsService = new GoogleAdsService($accessToken);
        $campaign = $googleAdsService->getCampaignByResourceName($customerId, $campaignResourceName);
        
        return view('admin.client_management.google_ads_campaign.edit', [
            'breadcrumb_main' => 'Clients Management',
            'breadcrumb' => 'Google Ads Campaign',
            'title' => 'Google Ads Campaign',
            'sub_account_id' => $sessionId,
            'google_account_id' => $googleAccountId,
            'customer_id' => $customerId,
            'campaign_resource_name' => $campaignResourceName,
            'campaign' => $campaign,
        ]);
    }

    public function google_ads_campaign_update(Request $request)
    {
        $sessionId = session()->get('sub_account_id');
        $client = User::where('sub_account_id', hashids_decode($sessionId))->first();

        try {
            // Processing request body
            $googleAccountId = (int) $request->google_account_id;
            $customerId = $request->customer_id;
            $campaignResourceName = $request->campaign_resource_name;
            $requestBodyCampaign = [];
            $requestBodyCampaign['name'] = $request->campaign_name;
            $requestBodyCampaign['status'] = $request->campaign_status;
            $requestBodyCampaign['startDate'] = $request->campaign_start_date;
            $requestBodyCampaign['endDate'] = $request->campaign_end_date;

            $campaignBudgetResourceName = $request->campaign_budget_resource_name;
            $requestBodyCampaignBudget = [];
            $requestBodyCampaignBudget['name'] = $request->campaign_name;
            $requestBodyCampaignBudget['amountMicros'] = (int) $request->campaign_budget_amount * 1000000;
            
            // Update Google Ads campaign
            $googleAccount = GoogleAccount::find($googleAccountId);
            $accessToken = $googleAccount->access_token;
            $googleAdsService = new GoogleAdsService($accessToken);
            $googleAdsService->updateCampaign($customerId, $campaignResourceName, $requestBodyCampaign);

            $googleAdsService->updateCampaignBudget($customerId, $campaignBudgetResourceName, $requestBodyCampaignBudget);

            // Update Google Ads in internal DB
            $googleAd = GoogleAdsAd::where('campaign_resource_name', $campaignResourceName)->first();
            if ($googleAd) {
                $googleAd->campaign_name = $requestBodyCampaign['name'];
                $googleAd->campaign_start_date = $requestBodyCampaign['startDate'];
                $googleAd->campaign_end_date = $requestBodyCampaign['endDate'];
                $googleAd->campaign_budget_amount = $requestBodyCampaignBudget['amountMicros'];
                $googleAd->save();
            }

            return redirect()->back()->with('success', 'Google ads campaign updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update campaign: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }

    public function google_ads_ad_group(Request $request)
    {
        $sessionId = session()->get('sub_account_id');
        $client = User::where('sub_account_id', hashids_decode($sessionId))->first();
        
        $customerIds = GoogleAdsAd::where('client_id', $client->id)
            ->select('google_account_id', 'customer_id')
            ->distinct()
            ->pluck('google_account_id', 'customer_id')
            ->filter(function ($google_account_id, $customer_id) {
                return !is_null($google_account_id) && !is_null($customer_id);
            })
            ->toArray();
        $googleAdsResoucesNames = GoogleAdsAd::where('client_id', $client->id)
            ->pluck('ad_group_resource_name')
            ->filter()
            ->toArray();

        $adGroups = [];
        // Fetch by customerIds
        foreach ($customerIds as $customerId => $googleAccountId) {
            $googleAccount = GoogleAccount::find($googleAccountId);
            $this->checkRefreshTokenNew($googleAccount);
        
            $accessToken = $googleAccount->access_token;
            $googleAdsService = new GoogleAdsService($accessToken);
            $adGroupDatas = $googleAdsService->getAdGroups($customerId);

            if (isset($adGroupDatas['results'])) {
                // Filter ad group data
                $filteredResults = array_filter($adGroupDatas['results'], function ($result) use ($googleAdsResoucesNames) {
                    return in_array($result['adGroup']['resourceName'], $googleAdsResoucesNames);
                });
    
                $adGroupDatas['results'] = array_values($filteredResults);
                foreach ($filteredResults as $result) {
                    $result['google_account_id'] = $googleAccountId;
                    $result['customer_id'] = $customerId;
                    $adGroups[] = $result;
                }
            }
        }

        return view('admin.client_management.google_ads_ad_group.index', [
            'breadcrumb_main' => 'Clients Management',
            'breadcrumb' => 'Google Ads Ad Group',
            'title' => 'Google Ads Ad Group',
            'sub_account_id' => $sessionId,
            'client' => $client,
            'ad_groups' => $adGroups,
        ]);
    }

    public function google_ads_ad_group_show(Request $request)
    {
        $sessionId = session()->get('sub_account_id');
        $client = User::where('sub_account_id', hashids_decode($sessionId))->first();
        
        $googleAccountId = (int) $request->google_account_id;
        $customerId = $request->customer_id;
        $adGroupResourceName = $request->ad_group_resource_name;
        
        $googleAccount = GoogleAccount::find($googleAccountId);
        if (!$googleAccount || !$customerId || !$adGroupResourceName) {
            return redirect()->back()->with('error', 'Invalid data');
        }

        $accessToken = $googleAccount->access_token;
        $googleAdsService = new GoogleAdsService($accessToken);
        $adGroup = $googleAdsService->getAdGroupByResourceName($customerId, $adGroupResourceName);
        
        return view('admin.client_management.google_ads_ad_group.show', [
            'breadcrumb_main' => 'Clients Management',
            'breadcrumb' => 'Google Ads Ad Group',
            'title' => 'Google Ads Ad Group',
            'sub_account_id' => $sessionId,
            'customer_id' => $customerId,
            'ad_group_resource_name' => $adGroupResourceName,
            'ad_group' => $adGroup,
        ]);
    }

    public function google_ads_ad_group_ad(Request $request)
    {
        $sessionId = session()->get('sub_account_id');
        $client = User::where('sub_account_id', hashids_decode($sessionId))->first();
        
        $customerIds = GoogleAdsAd::where('client_id', $client->id)
            ->select('google_account_id', 'customer_id')
            ->distinct()
            ->pluck('google_account_id', 'customer_id')
            ->filter(function ($google_account_id, $customer_id) {
                return !is_null($google_account_id) && !is_null($customer_id);
            })
            ->toArray();
        $googleAdsResoucesNames = GoogleAdsAd::where('client_id', $client->id)
            ->pluck('ad_resource_name')
            ->filter()
            ->toArray();
        
        $adGroupAds = [];
        // Fetch by customerIds
        foreach ($customerIds as $customerId => $googleAccountId) {
            $googleAccount = GoogleAccount::find($googleAccountId);
            $this->checkRefreshTokenNew($googleAccount);
        
            $accessToken = $googleAccount->access_token;
            $googleAdsService = new GoogleAdsService($accessToken);
            $adGroupAdDatas = $googleAdsService->getAds($customerId);

            if (isset($adGroupAdDatas['results'])) {
                // Filter ad group data
                $filteredResults = array_filter($adGroupAdDatas['results'], function ($result) use ($googleAdsResoucesNames) {
                    return in_array($result['adGroupAd']['resourceName'], $googleAdsResoucesNames);
                });
    
                $adGroupAdDatas['results'] = array_values($filteredResults);
                foreach ($filteredResults as $result) {
                    $googleAd = GoogleAdsAd::where('client_id', $client->id)
                        ->where('ad_resource_name', $result['adGroupAd']['resourceName'])
                        ->first();
                    $result['ad_request'] = $googleAd->ad->adds_title;

                    $result['google_account'] = $googleAccount->email;
                    $result['customer_id'] = $customerId;
                    $adGroupAds[] = $result;
                }
            }
        }
        
        $ads = Ads::where('client_id', $client->id)
            ->whereNotNull('google_account_id')
            ->whereNotNull('customer_id')
            ->get();

        return view('admin.client_management.google_ads_ad_group_ad.index', [
            'breadcrumb_main' => 'Clients Management',
            'breadcrumb' => 'Google Ads Ad Group',
            'title' => 'Google Ads Ad Group',
            'sub_account_id' => $sessionId,
            'client' => $client,
            'ad_group_ads' => $adGroupAds,
            'ad_requests' => $ads,
        ]);
    }

    public function google_ads_create()
    {
        $sessionId = session()->get('sub_account_id');
        $client = User::where('sub_account_id', hashids_decode($sessionId))->first();

        return view('admin.client_management.google_ads.create', [
            'breadcrumb_main' => 'Clients Management',
            'breadcrumb' => 'Create Google Ads',
            'title' => 'Create Google Ads',
            'sub_account_id' => $sessionId,
            'client' => $client,
            'ads_requests' => Ads::with('client')->where('client_id', $client->id)->latest()->get(),
            'google_accounts' => GoogleAccount::all(),
        ]);
    }

    public function google_ads_store(Request $request)
    {
        $sessionId = session()->get('sub_account_id');
        $client = User::where('sub_account_id', hashids_decode($sessionId))->first();

        try {
            $adRequest = Ads::find($request->ad_request_id);
            $googleAccountId = $adRequest->google_account_id;
            $customerId = $adRequest->customer_id;
            
            $googleAccount = GoogleAccount::find($googleAccountId);
            if (!$googleAccount) {
                return redirect()->back()->with('error', 'Google Ads not found.');
            }

            // Validate request
            $requestBody = $request->validate([
                'ad_request_id' => 'required|exists:ads,id',

                'campaign_name' => 'required|string|max:255',
                'campaign_type' => 'required|in:SEARCH,PERFORMANCE_MAX',
                'campaign_budget_type' => 'required|in:LIFETIME,DAILY',
                'campaign_budget_amount' => 'required|numeric|min:1',
                'campaign_start_date' => 'nullable|date',
                'campaign_end_date' => 'nullable|date|after_or_equal:campaign_start_date',
                
                'keywords' => 'required|string',
                
                'ad_name' => 'required|string|max:255',
                'ad_url' => 'required|string',
                'ad_headlines' => 'required|array',
                'ad_headlines.*' => 'nullable|distinct|string|max:30',
                'ad_descriptions' => 'required|array',
                'ad_descriptions.*' => 'nullable|distinct|string|max:90',
                'sitelinks' => 'array',
                'sitelinks.*.text' => 'nullable|string',
                'sitelinks.*.url' => 'nullable|string',
                'ad_callouts' => 'required|array',
                'ad_callouts.*' => 'nullable|distinct|string|max:25',
            ]);

            // Processing request body
            $today = Carbon::today();
            $startDate = $today->format('Y-m-d');
            $endDate = $today->format('Y-m-d');
            if ($request->has('campaign_duration')) {
                $duration = $request->campaign_duration;
                
                if ($duration === 'week') {
                    $endDate = $today->addDays(7)->format('Y-m-d');
                } elseif ($duration === 'month') {
                    $endDate = $today->addDays(30)->format('Y-m-d');
                } elseif ($duration === 'custom') {
                    $startDate = $request->start_date;
                    $endDate = $request->end_date;
                }
            }
            $requestBody['start_date'] = $startDate;
            $requestBody['end_date'] = $endDate;
            $requestBody['campaign_budget_amount'] = (int) $request->campaign_budget_amount * 1000000; // Need to be multiplied by 1 million
            
            // Location
            if ($request->location == 'SINGAPORE') {
                $requestBody['locations'] = ['geoTargetConstants/2702'];
            } else {
                $requestBody['locations'] = $request->locations;
            }

            // Language
            $requestBody['language'] = 'languageConstants/1000';

            // Format keyword
            $keywords = array_filter(array_map('trim', explode("\r\n", $request->keywords)), 'strlen');
            $formattedKeywords = array_map(function ($keyword) {
                $match_type = 'BROAD';
                if (preg_match('/^\[.*\]$/', $keyword)) {
                    $match_type = 'EXACT';
                    $keyword = trim($keyword, '[]');
                } elseif (preg_match('/^".*"$/', $keyword)) {
                    $match_type = 'PHRASE';
                    $keyword = trim($keyword, '"');
                } else {
                    $keyword = trim($keyword);
                }
                return [
                    'text' => $keyword,
                    'match_type' => $match_type
                ];
            }, $keywords);
            $requestBody['keywords'] = $formattedKeywords;

            $requestBody['ad_url_1'] = $request->ad_url_1;
            $requestBody['ad_url_2'] = $request->ad_url_2;

            $requestBody['sitelinks'] = $request->sitelinks ?? [];
            
            // Initiate google ads service
            $googleAccount = GoogleAccount::find($googleAccountId);
            $this->checkRefreshTokenNew($googleAccount);
            
            $googleAdsService = new GoogleAdsService($googleAccount->access_token);
            // Create campaign budget
            $campaignBudget = $googleAdsService->createCampaignBudget($customerId, $requestBody);
            if (is_array($campaignBudget)) {
                if (isset($campaignBudget['errors'])) {
                    foreach ($campaignBudget['errors'] as $error) {
                        return redirect()->back()->with('error', $error);
                    }
                }
            }
            Log::channel('google_ads')->info("Google Ads - {$requestBody['campaign_name']}: Campaign budget created");
            
            // Create campaign
            $campaign = $googleAdsService->createCampaign($customerId, $campaignBudget, $requestBody);
            if (is_array($campaign)) {
                if (isset($campaign['errors'])) {
                    foreach ($campaign['errors'] as $error) {
                        return redirect()->back()->with('error', $error);
                    }
                }
            }
            Log::channel('google_ads')->info("Google Ads - {$requestBody['campaign_name']}: Campaign created");

            // Add campaign criteria
            $googleAdsService->createCampaignCriteria($customerId, $campaign, $requestBody);
            Log::channel('google_ads')->info("Google Ads - {$requestBody['campaign_name']}: Campaign criteria created");

            // Create ad group
            $adGroup = $googleAdsService->createAdGroup($customerId, $campaign, $requestBody);
            if (is_array($adGroup)) {
                if (isset($adGroup['errors'])) {
                    foreach ($adGroup['errors'] as $error) {
                        return redirect()->back()->with('error', $error);
                    }
                }
            }
            Log::channel('google_ads')->info("Google Ads - {$requestBody['campaign_name']}: Ad group created");

            // Add keyword
            $keywords = $googleAdsService->addKeywordToAdGroup($customerId, $adGroup, $requestBody);
            if (is_array($keywords)) {
                if (isset($keywords['errors'])) {
                    foreach ($keywords['errors'] as $error) {
                        return redirect()->back()->with('error', $error);
                    }
                }
            }
            Log::channel('google_ads')->info("Google Ads - {$requestBody['campaign_name']}: Keyword created");

            // Create ad
            $ad = $googleAdsService->createAd($customerId, $adGroup, $requestBody);
            if (is_array($ad)) {
                if (isset($ad['errors'])) {
                    foreach ($ad['errors'] as $error) {
                        return redirect()->back()->with('error', $error);
                    }
                }
            }
            Log::channel('google_ads')->info("Google Ads - {$requestBody['campaign_name']}: Ad created");

            $hasNull = in_array(null, $requestBody['sitelinks'][1], true);
            if (count($requestBody['sitelinks']) && !$hasNull) {
                $sitelinks = $googleAdsService->createAsset($customerId, $requestBody);
                if (is_array($sitelinks)) {
                    if (isset($sitelinks['errors'])) {
                        foreach ($sitelinks['errors'] as $error) {
                            return redirect()->back()->with('error', $error);
                        }
                    }
                }
            }
            Log::channel('google_ads')->info("Google Ads - {$requestBody['campaign_name']}: Assets created");

            GoogleAdsAd::create([
                'client_id' => $adRequest->client_id,
                'ad_request_id' => $adRequest->id,
                'campaign_budget_resource_name' => $campaignBudget ?? '',
            
                'campaign_name' => $request->campaign_name,
                'campaign_type' => $request->campaign_type,
                'campaign_budget_type' => $request->campaign_budget_type,
                'campaign_budget_amount' => $requestBody['campaign_budget_amount'],
                'campaign_target_url' => $request->campaign_target_url ?? '',
                'campaign_start_date' => $startDate,
                'campaign_end_date' => $endDate,
                'campaign_resource_name' => $campaign ?? '',
                
                'locations' => json_encode($requestBody['locations']),
                'languages' => $requestBody['language'],
                
                'ad_group_name' => $request->campaign_name,
                'ad_group_bid_amount' => $requestBody['campaign_budget_amount'],
                'ad_group_resource_name' => $adGroup ?? '',
            
                'keywords' => json_encode($requestBody['keywords']),
                'keyword_match_types' => $request->keyword_match_types ?? '',
            
                'ad_name' => $request->ad_name,
                'ad_final_url' => $request->ad_url,
                'ad_headlines' => json_encode($request->ad_headlines),
                'ad_descriptions' => json_encode($request->ad_descriptions),
                'ad_sitelinks' => json_encode($requestBody['sitelinks']),
                'ad_resource_name' => $ad ?? '',

                'google_account_id' => $adRequest->google_account_id,
                'customer_id' => $adRequest->customer_id,
            ]);

            return redirect()->back()->with('success', 'Google ads created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to Google ad: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }

    public function google_ads_sync(Request $request)
    {
        $sessionId = session()->get('sub_account_id');
        $client = User::where('sub_account_id', hashids_decode($sessionId))->first();

        try {
            $adRequest = Ads::find($request->ad_request_id);
            $googleAccountId = $adRequest->google_account_id;
            $customerId = $adRequest->customer_id;

            $googleAccount = GoogleAccount::find($googleAccountId);
            $this->checkRefreshTokenNew($googleAccount);
            if (!$googleAccount) {
                return redirect()->back()->with('error', 'Google Ads not found.');
            }
            $campaignResourceName = $request->ad_id;
            $checkAd = GoogleAdsAd::where('ad_resource_name', $campaignResourceName)->first();
            if ($checkAd) {
                return redirect()->back()->with('error', 'Google Ad already exist.');
            }

            $accessToken = $googleAccount->access_token;
            $googleAdsService = new GoogleAdsService($accessToken);
            $googleAd = $googleAdsService->getAdByResourceName($customerId, $campaignResourceName);

            GoogleAdsAd::create([
                'client_id' => $client->id,
                'ad_request_id' => $request->ad_request_id,
                'campaign_budget_resource_name' => null,
            
                'campaign_name' => $googleAd['campaign']['name'] ?? null,
                'campaign_type' => $googleAd['campaign']['advertisingChannelType'] ?? null,
                'campaign_budget_type' => null,
                'campaign_budget_amount' => null,
                'campaign_target_url' => null,
                'campaign_start_date' => $googleAd['campaign']['startDate'] ?? null,
                'campaign_end_date' => $googleAd['campaign']['endDate'] ?? null,
                'campaign_resource_name' => $googleAd['campaign']['resourceName'] ?? null,
                
                'locations' => null,
                'languages' => null,
                
                'ad_group_name' => $googleAd['adGroup']['name'] ?? null,
                'ad_group_bid_amount' => $googleAd['adGroup']['cpcBidMicros'] ?? null,
                'ad_group_resource_name' => $googleAd['adGroup']['resourceName'] ?? null,
            
                'keywords' => null,
                'keyword_match_types' => null,
            
                'ad_name' => 'Imported Ad',
                'ad_final_url' => $googleAd['adGroupAd']['ad']['finalUrls'][0] ?? null,
                'ad_headlines' => null,
                'ad_descriptions' => null,
                'ad_sitelinks' => null,
                'ad_resource_name' => $googleAd['adGroupAd']['resourceName'] ?? null,

                'google_account_id' => $googleAccountId,
                'customer_id' => $customerId,
            ]);

            return redirect()->back()->with('success', 'Google ads synced successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to Google ad: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }

    public function google_ads_conversion_action(Request $request)
    {
        $sessionId = session()->get('sub_account_id');
        $client = User::where('sub_account_id', hashids_decode($sessionId))->first();
        
        return view('admin.client_management.google_ads_conversion_action.index', [
            'breadcrumb_main' => 'Clients Management',
            'breadcrumb' => 'Google Ads Conversion Action',
            'title' => 'Google Ads Conversion Action',
            'sub_account_id' => $sessionId,
            'client' => $client,
            'clients' => User::where('sub_account_id', hashids_decode($sessionId))->get(),
        ]);
    }

    public function google_ads_conversion_action_create()
    {
        $sessionId = session()->get('sub_account_id');
        $client = User::where('sub_account_id', hashids_decode($sessionId))->first();

        return view('admin.client_management.google_ads_conversion_action.create', [
            'breadcrumb_main' => 'Clients Management',
            'breadcrumb' => 'Create Google Ads Conversion Action',
            'title' => 'Create Google Ads Conversion Action',
            'sub_account_id' => $sessionId,
            'client' => $client,
            'clients' => User::where('sub_account_id', hashids_decode($sessionId))->get(),
        ]);
    }

    public function google_ads_conversion_action_store(Request $request)
    {
        $sessionId = session()->get('sub_account_id');
        $client = User::where('sub_account_id', hashids_decode($sessionId))->first();

        try {
            if (!Auth::user('admin')->google_access_token) {
                return redirect()->back()->with('error', 'Google Ads not connected yet.');
            }
            
            // Validate request
            $requestBody = $request->validate([
                'client_id' => 'required|exists:users,id',
                'name' => 'array',
                'name.*' => 'required|string|max:255',
                'type' => 'array',
                'type.*' => 'required|string',
                'category' => 'array',
                'category.*' => 'required|string',
                'website_url' => 'array',
                'website_url.*' => 'required|string',
                'counting_type' => 'array',
                'counting_type.*' => 'required|string',
                'click_through_days' => 'array',
                'click_through_days.*' => 'required|integer',
                'view_through_days' => 'array',
                'view_through_days.*' => 'required|integer',
            ]);
            
            // Find client
            $client = User::findOrFail($request->client_id);

            // Check customer id
            if (is_null($client->customer_id)) {
                return redirect()->back()->with('error', 'The client does not have a customer ID.');
            }
            $customerId = $client->customer_id;
            
            // Initiate google ads service
            $googleAdsService = new GoogleAdsService();
            // Create conversion action
            $createConversionAction = $googleAdsService->createConversionAction($customerId, $requestBody);
            if (!isset($createConversionAction['results'][0]['resourceName'])) {
                return redirect()->back()->with('error', 'Failed to create conversion action. No resource name returned.');
            }

            for ($i = 0; $i < count($requestBody['name']); $i++) {
                GoogleAdsConversionAction::create([
                    'client_id' => $requestBody['client_id'],
                    'name' => $requestBody['name'][$i],
                    'type' => $requestBody['type'][$i],
                    'category' => $requestBody['category'][$i],
                    'website_url' => $requestBody['website_url'][$i],
                    'counting_type' => $requestBody['counting_type'][$i],
                    'click_through_days' => $requestBody['click_through_days'][$i],
                    'view_through_days' => $requestBody['view_through_days'][$i],
                    'resource_name' => $createConversionAction['results'][$i]['resourceName'],
                    'customer_id' => $customerId,
                ]);
            }
            
            $parts = explode('/', $createConversionAction['results'][0]['resourceName']);
            $conversionActionId = end($parts);

            return redirect()->back()->with('success', 'Google ads conversion action created successfully.')->with('conversionActionId', $conversionActionId);
        } catch (\Exception $e) {
            Log::error('Failed to create conversion action: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }
}
