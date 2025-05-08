<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GoogleAccount;
use App\Models\GoogleAd;
use App\Models\User;
use App\Services\GoogleAdsService;
use App\Traits\AdsSpentTrait;
use App\Traits\ApiResponseTrait;
use App\Traits\GoogleTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @group Google Ads
 */
class GoogleAdsController extends Controller
{
    use AdsSpentTrait, ApiResponseTrait, GoogleTrait;

    public function google_ads_campaign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'integer',
            'google_account_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendErrorResponse(implode("\n", $validator->errors()->all()), 400);
        }

        $userId = auth('api')->id() ?? $request->client_id;

        $client = User::find($userId);
        if (!$client) {
            return $this->sendErrorResponse('Client Not Found', 401);
        }

        $customerId = $client->customer_id;
        $googleAccountId = $client->google_account_id;

        $googleAccount = GoogleAccount::find($googleAccountId);
        $this->checkRefreshTokenNew($googleAccount);

        $googleAdsService = new GoogleAdsService($googleAccount->access_token);
        $data = $googleAdsService->getCampaigns($customerId);

        // Filter only registered campaign on panel
        if (isset($data['results'])) {
            $googleAdsResoucesNames = GoogleAd::where('client_id', $request->client_id)->pluck('campaign_resource_name')->toArray();

            $filteredResults = array_filter($data['results'], function ($result) use ($googleAdsResoucesNames) {
                return in_array($result['campaign']['resourceName'], $googleAdsResoucesNames);
            });

            // Store to database
            foreach ($filteredResults as $filteredResult) {
                $googleAd = googleAd::where('campaign_resource_name', $filteredResult['campaign']['resourceName'])->first();
                $googleAd->campaign_json = $filteredResult;
                $googleAd->save();
            }

            $data['results'] = array_values($filteredResults);
        } else {
            // Get from database
            $googleAdsJson = GoogleAd::where('client_id', $request->client_id)
                ->whereNotNull('campaign_json')
                ->pluck('campaign_json')
                ->map(fn ($item) => json_decode($item, true))
                ->toArray();

            $data = ['results' => $googleAdsJson];
        }

        return $data;
    }

    public function google_ads_ad_group(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'integer',
            'google_account_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendErrorResponse(implode("\n", $validator->errors()->all()), 400);
        }

        $userId = auth('api')->id() ?? $request->client_id;

        $client = User::find($userId);
        if (!$client) {
            return $this->sendErrorResponse('Client Not Found', 401);
        }
        $customerId = $client->customer_id;
        $googleAccountId = $client->google_account_id;

        $googleAccount = GoogleAccount::find($googleAccountId);
        $this->checkRefreshTokenNew($googleAccount);

        $googleAdsService = new GoogleAdsService($googleAccount->access_token);
        $data = $googleAdsService->getAdGroups($customerId);

        // Filter only registered campaign on panel
        if (isset($data['results'])) {
            $googleAdsResoucesNames = GoogleAd::where('client_id', $request->client_id)->pluck('campaign_resource_name')->toArray();

            $filteredResults = array_filter($data['results'], function ($result) use ($googleAdsResoucesNames) {
                return in_array($result['campaign']['resourceName'], $googleAdsResoucesNames);
            });

            // Store to database
            foreach ($filteredResults as $filteredResult) {
                $googleAd = googleAd::where('campaign_resource_name', $filteredResult['campaign']['resourceName'])->first();
                $googleAd->ad_group_json = $filteredResult;
                $googleAd->save();
            }

            $data['results'] = array_values($filteredResults);

            return $data;
        } else {
            // Get from database
            $googleAdsJson = GoogleAd::where('client_id', $request->client_id)
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
        $validator = Validator::make($request->all(), [
            'client_id' => 'integer',
            'google_account_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendErrorResponse(implode("\n", $validator->errors()->all()), 400);
        }

        $userId = auth('api')->id() ?? $request->client_id;

        $client = User::find($userId);
        if (!$client) {
            return $this->sendErrorResponse('Client Not Found', 401);
        }

        $customerId = $client->customer_id;
        $googleAccountId = $client->google_account_id;

        $googleAccount = GoogleAccount::find($googleAccountId);
        $this->checkRefreshTokenNew($googleAccount);

        $googleAdsService = new GoogleAdsService($googleAccount->access_token);
        $data = $googleAdsService->getAds($customerId);

        // Filter only registered campaign on panel
        if (isset($data['results'])) {
            $googleAdsResoucesNames = GoogleAd::where('client_id', $request->client_id)->pluck('campaign_resource_name')->toArray();

            $filteredResults = array_filter($data['results'], function ($result) use ($googleAdsResoucesNames) {
                return in_array($result['campaign']['resourceName'], $googleAdsResoucesNames);
            });

            // Store to database
            foreach ($filteredResults as $filteredResult) {
                $googleAd = googleAd::where('campaign_resource_name', $filteredResult['campaign']['resourceName'])->first();
                $googleAd->ad_json = $filteredResult;
                $googleAd->save();
            }

            $data['results'] = array_values($filteredResults);
        } else {
            // Get from database
            $googleAdsJson = GoogleAd::where('client_id', $request->client_id)
                ->whereNotNull('ad_json')
                ->pluck('ad_json')
                ->map(fn ($item) => json_decode($item, true))
                ->toArray();

            $data = ['results' => $googleAdsJson];
        }

        return $data;
    }

    public function google_ads_conversion_action(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'integer',
            'google_account_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendErrorResponse(implode("\n", $validator->errors()->all()), 400);
        }

        $userId = auth('api')->id() ?? $request->client_id;

        $client = User::find($userId);
        if (!$client) {
            return $this->sendErrorResponse('Client Not Found', 401);
        }
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
        $validator = Validator::make($request->all(), [
            'client_id' => 'integer',
            'google_account_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendErrorResponse(implode("\n", $validator->errors()->all()), 400);
        }

        $userId = auth('api')->id() ?? $request->client_id;

        $client = User::find($userId);
        if (!$client) {
            return $this->sendErrorResponse('Client Not Found', 401);
        }
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
        $validator = Validator::make($request->all(), [
            'google_account_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendErrorResponse(implode("\n", $validator->errors()->all()), 400);
        }

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
