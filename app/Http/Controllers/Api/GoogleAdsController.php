<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GoogleAccount;
use App\Models\GoogleAdsAd;
use App\Models\User;
use App\Services\GoogleAdsService;
use App\Traits\AdsSpentTrait;
use App\Traits\GoogleTrait;
use Illuminate\Http\Request;

class GoogleAdsController extends Controller
{
    use GoogleTrait, AdsSpentTrait;

    public function google_ads_campaign(Request $request)
    {
        $client = User::find($request->client_id);
        $customerId = $client->customer_id;
        $googleAccountId = $client->google_account_id;

        $googleAccount = GoogleAccount::find($googleAccountId);
        $this->checkRefreshTokenNew($googleAccount);
        
        $googleAdsService = new GoogleAdsService($googleAccount->access_token);
        $data = $googleAdsService->getCampaigns($customerId);

        // Filter only registered campaign on panel
        if (isset($data['results'])) {
            $googleAdsResoucesNames = GoogleAdsAd::where('client_id', $request->client_id)->pluck('campaign_resource_name')->toArray();
    
            $filteredResults = array_filter($data['results'], function ($result) use ($googleAdsResoucesNames) {
                return in_array($result['campaign']['resourceName'], $googleAdsResoucesNames);
            });

            // Store to database
            foreach ($filteredResults as $filteredResult) {
                $googleAd = GoogleAdsAd::where('campaign_resource_name', $filteredResult['campaign']['resourceName'])->first();
                $googleAd->campaign_json = $filteredResult;
                $googleAd->save();
            }

            $data['results'] = array_values($filteredResults);
            return $data;
        } else {
            // Get from database
            $googleAdsJson = GoogleAdsAd::where('client_id', $request->client_id)
                ->whereNotNull('campaign_json')
                ->pluck('campaign_json')
                ->map(fn ($item) => json_decode($item, true))
                ->toArray();
            
            $data = ['results' => $googleAdsJson];
            
            return $data;
        }
    }

    public function google_ads_ad_group(Request $request)
    {
        $client = User::find($request->client_id);
        $customerId = $client->customer_id;
        $googleAccountId = $client->google_account_id;

        $googleAccount = GoogleAccount::find($googleAccountId);
        $this->checkRefreshTokenNew($googleAccount);
        
        $googleAdsService = new GoogleAdsService($googleAccount->access_token);
        $data = $googleAdsService->getAdGroups($customerId);

        // Filter only registered campaign on panel
        if (isset($data['results'])) {
            $googleAdsResoucesNames = GoogleAdsAd::where('client_id', $request->client_id)->pluck('campaign_resource_name')->toArray();
    
            $filteredResults = array_filter($data['results'], function ($result) use ($googleAdsResoucesNames) {
                return in_array($result['campaign']['resourceName'], $googleAdsResoucesNames);
            });

            // Store to database
            foreach ($filteredResults as $filteredResult) {
                $googleAd = GoogleAdsAd::where('campaign_resource_name', $filteredResult['campaign']['resourceName'])->first();
                $googleAd->ad_group_json = $filteredResult;
                $googleAd->save();
            }

            $data['results'] = array_values($filteredResults);
            return $data;
        } else {
            // Get from database
            $googleAdsJson = GoogleAdsAd::where('client_id', $request->client_id)
                ->whereNotNull('ad_group_json')
                ->pluck('ad_group_json')
                ->map(fn ($item) => json_decode($item, true))
                ->toArray();
            
            $data = ['results' => $googleAdsJson];
            
            return $data;
        }
    }

    public function google_ads_ad_group_ad(Request $request)
    {
        $customerId = $request->customer_id;
        $googleAccountId = $request->google_account_id;

        $googleAccount = GoogleAccount::find($googleAccountId);
        $this->checkRefreshTokenNew($googleAccount);
    
        $accessToken = $googleAccount->access_token;
        $googleAdsService = new GoogleAdsService($accessToken);

        $data = $googleAdsService->getAds($customerId);

        return $data;
    }

    public function google_ads_conversion_action(Request $request)
    {
        $client = User::find($request->client_id);
        $customerId = $client->customer_id;
        $googleAccountId = $client->google_account_id;

        $googleAccount = GoogleAccount::find($googleAccountId);
        $this->checkRefreshTokenNew($googleAccount);
        
        $googleAdsService = new GoogleAdsService($googleAccount->access_token);
        $data = $googleAdsService->getConversionActions($customerId);

        return $data;
    }

    public function google_ads_geo_target_constant(Request $request)
    {
        $client = User::find($request->client_id);
        $customerId = $client->customer_id;
        $googleAccountId = $client->google_account_id;

        $googleAccount = GoogleAccount::find($googleAccountId);
        $this->checkRefreshTokenNew($googleAccount);
        
        $googleAdsService = new GoogleAdsService($googleAccount->access_token);
        $data = $googleAdsService->getGeoTargetConstant($customerId);

        return $data;
    }

    public function google_ads_customer_id(Request $request)
    {
        $googleAccount = GoogleAccount::find($request->google_account_id);
        $this->checkRefreshTokenNew($googleAccount);
        
        $customers = [];
        $googleAdsService = new GoogleAdsService($googleAccount->access_token);
        $getCustomers = $googleAdsService->getCustomers();
        if (isset($getCustomers['errors'])) {
            foreach ($getCustomers['errors'] as $error) {
                return response()->json([
                    'errors' => $error,
                ]);
            }
        }
        
        foreach ($getCustomers['resourceNames'] as $customer) {
            $customerId = removePrefix($customer);
            $getCustomerClients = $googleAdsService->getCustomerClients($customerId);
            
            if (!isset($getCustomerClients['results'])) {
                continue;
            }
            
            foreach ($getCustomerClients['results'] as $customerClient) {
                $customers[] = $customerClient;
            }
        }

        return $customers;
    }
}
