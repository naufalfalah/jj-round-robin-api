<?php

namespace App\Services;

use App\Models\Admin;
use App\Traits\GoogleTrait;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class GoogleAdsService
{
    use GoogleTrait;

    protected $baseUrl = 'https://googleads.googleapis.com/v16';
    protected $client;

    public function __construct($access_token = null)
    {
        $this->getAdminClient();
        $this->initializeClient($access_token);
    }

    private function initializeClient($access_token)
    {
        if ($access_token) {
            $authorization = json_decode($access_token, true);
        } else {
            $admin = Admin::find(1);
            $authorization = json_decode($admin->google_access_token, true);
        }
        $accessToken = $authorization['access_token'];

        $this->client = new Client([
            'headers' => [
                'Authorization' => "Bearer $accessToken",
                'Content-Type' => 'application/json',
                'developer-token' => config('services.google.developer_token'),
            ]
        ]);
    }

    private function refreshToken()
    {
        $admin = Admin::find(1);
        if (isset($admin->google_refresh_token)) {
            $refreshToken = $admin->google_refresh_token;
            $clientId = config('services.google.client_id');
            $clientSecret = config('services.google.client_secret');
    
            $client = new Client();
            $response = $client->post('https://oauth2.googleapis.com/token', [
                'form_params' => [
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refreshToken,
                ],
            ]);
    
            $newTokenData = json_decode($response->getBody(), true);
            $admin->google_access_token = json_encode($newTokenData);
            $admin->save();
        }

        $this->initializeClient();
    }

    private function handleRequest($method, $url, $options = [])
    {
        try {
            $response = $this->client->{$method}($url, $options);
        } catch (RequestException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
    
            if ($statusCode == 400) {
                $response = $e->getResponse();
            } elseif ($statusCode == 401) {
                $response = $e->getResponse();
            } elseif ($statusCode == 403) {
                $response = $e->getResponse();
            } else {
                throw $e;
            }
        }

        $response = json_decode($response->getBody(), true);
        
        if (isset($response['error'])) {
            if (isset($response['error']['status'])) {
                return [
                    'errors' => $response['error']['status'],
                ];
            }
            $errorCode = $response['error']['details'][0]['errors'][0]['errorCode'];
            return [
                'errors' => $errorCode,
            ];
        }

        return $response;
    }

    public function getCustomers()
    {
        $response = $this->handleRequest('get', "$this->baseUrl/customers:listAccessibleCustomers");
        
        return $response;
    }

    public function getCustomerClients($customerId)
    {
        $response = $this->handleRequest('post', "$this->baseUrl/customers/$customerId/googleAds:search", [
            'json' => [
                'query' => 'SELECT customer.id, customer.descriptive_name, customer.currency_code, customer.manager, customer.status FROM customer_client'
            ]
        ]);

        return $response;
    }

    public function createCampaignBudget($customerId, $requestBody = null)
    {
        $response = $this->handleRequest('post', "$this->baseUrl/customers/{$customerId}/campaignBudgets:mutate", [
            'json' => [
                'operations' => [
                    [
                        'create' => [
                            'name' => $requestBody['campaign_name'],
                            'amountMicros' => $requestBody['campaign_budget_amount'],
                            'deliveryMethod' => 'STANDARD',
                            'explicitlyShared' => false
                        ],
                    ],
                ],
            ],
        ]);

        if (isset($response['errors'])) {
            return $response;
        }

        return $response['results'][0]['resourceName'];
    }

    public function updateCampaignBudget($customerId, $campaignBudgetResourceName, $requestBody = null)
    {
        $updateMask = implode(',', array_keys($requestBody));
        
        $response = $this->handleRequest('post', "$this->baseUrl/customers/{$customerId}/campaignBudgets:mutate", [
            'json' => [
                'operations' => [
                    [
                        'update' => [
                            'resourceName' => $campaignBudgetResourceName,
                            'name' => $requestBody['name'] ?? '',
                            'amountMicros' => $requestBody['amountMicros'] ?? 0,
                        ],
                        'updateMask' => $updateMask,
                    ],
                ],
            ],
        ]);
    
        if (isset($response['errors'])) {
            return $response;
        }

        return $response['results'][0]['resourceName'];
    }

    public function getCampaigns($customerId)
    {
        $response = $this->handleRequest('post', "$this->baseUrl/customers/$customerId/googleAds:search", [
            'json' => [
                'query' => "SELECT campaign.id, campaign.name, campaign.start_date, campaign.end_date, campaign.target_roas.target_roas, campaign_budget.amount_micros, campaign.status, metrics.clicks, metrics.impressions, metrics.ctr, metrics.conversions, metrics.cost_micros FROM campaign WHERE campaign.status != 'REMOVED'"
            ]
        ]);

        return $response;
    }

    public function getActiveCampaigns($customerId)
    {
        $response = $this->handleRequest('post', "$this->baseUrl/customers/$customerId/googleAds:search", [
            'json' => [
                'query' => "SELECT campaign.id, campaign.name, campaign.status FROM campaign WHERE campaign.status NOT IN ('REMOVED', 'PAUSED')"
            ]
        ]);

        return $response;
    }

    public function getCampaignByResourceName($customerId, $campaignResourceName)
    {
        $response = $this->handleRequest('post', "$this->baseUrl/customers/$customerId/googleAds:search", [
            'json' => [
                'query' => "SELECT campaign.id, campaign.name, campaign.status, campaign.start_date, campaign.end_date, campaign.advertising_channel_type, campaign_budget.amount_micros, metrics.cost_micros FROM campaign WHERE campaign.resource_name = '$campaignResourceName'"
            ],
        ]);

        if (isset($response['errors'])) {
            return $response;
        }
        
        return $response['results'][0];
    }

    public function createCampaign($customerId, $campaignBudgetResourceName, $requestBody = null)
    {
        if ($requestBody['campaign_type'] === 'SEARCH') {
            $operations = [
                [
                    'create' => [
                        'name' => $requestBody['campaign_name'],
                        'campaignBudget' => $campaignBudgetResourceName,
                        'advertisingChannelType' => 'SEARCH',
                        'status' => 'ENABLED',
                        // 'advertisingChannelSubType' => 'LEAD_FORM',
                        'biddingStrategyType' => 'MAXIMIZE_CONVERSIONS',
                        'maximizeConversions' => [
                            'targetCpaMicros' => 0 * 1000000 // 1:1000000,
                        ],
                        'startDate' => $requestBody['start_date'],
                        'endDate' => $requestBody['end_date'],
                        'networkSettings' => [
                            // 'targetGoogleSearch' => true,
                            // 'targetSearchNetwork' => true,
                            // 'targetContentNetwork' => false,
                            'targetPartnerSearchNetwork' => false,
                        ],
                        'targetingSetting' => [
                            'targetRestrictions' => [
                                [
                                    'targetingDimension' => 'AUDIENCE',
                                    'bidOnly' => false,
                                ]
                            ],
                        ],
                    ],
                ],
            ];
        } elseif ($requestBody['campaign_type'] === 'PERFORMANCE_MAX') {
            $operations = [
                [
                    'create' => [
                        'name' => $requestBody['campaign_name'],
                        'campaignBudget' => $campaignBudgetResourceName,
                        'advertisingChannelType' => 'PERFORMANCE_MAX',
                        'status' => 'ENABLED',
                        // 'advertisingChannelSubType' => 'LEAD_FORM',
                        'biddingStrategyType' => 'MAXIMIZE_CONVERSIONS',
                        'maximizeConversions' => [
                            'targetCpaMicros' => 0 * 1000000 // 1 dollar,
                        ],
                        'startDate' => $requestBody['start_date'],
                        'endDate' => $requestBody['end_date'],
                        'networkSettings' => [
                            // 'targetGoogleSearch' => true,
                            // 'targetSearchNetwork' => true,
                            // 'targetContentNetwork' => false,
                            'targetPartnerSearchNetwork' => false,
                        ],
                        'targetingSetting' => [
                            'targetRestrictions' => [
                                [
                                    'targetingDimension' => 'AUDIENCE',
                                    'bidOnly' => false,
                                ]
                            ],
                        ],
                    ],
                ],
            ];
        }
        $response = $this->handleRequest('post', "$this->baseUrl/customers/{$customerId}/campaigns:mutate", [
            'json' => [
                'operations' => $operations,
            ],
        ]);

        if (isset($response['errors'])) {
            return $response;
        }

        return $response['results'][0]['resourceName'];
    }

    public function updateCampaign($customerId, $campaignResourceName, $requestBody = null)
    {
        $updateMask = implode(',', array_keys($requestBody));
        
        $response = $this->handleRequest('post', "$this->baseUrl/customers/{$customerId}/campaigns:mutate", [
            'json' => [
                'operations' => [
                    [
                        'update' => [
                            'resourceName' => $campaignResourceName,
                            'name' => $requestBody['name'] ?? '',
                            'status' => $requestBody['status'] ?? '',
                            'startDate' => $requestBody['startDate'] ?? '',
                            'endDate' => $requestBody['endDate'] ?? '',
                        ],
                        'updateMask' => $updateMask,
                    ],
                ],
            ],
        ]);
    
        if (isset($response['errors'])) {
            return $response;
        }

        return $response['results'][0]['resourceName'];
    }

    public function createCampaignCriteria($customerId, $campaignResourceName, $requestBody)
    {
        $operations = [];
        if (isset($requestBody['locations'])) {
            foreach ($requestBody['locations'] as $location) {
                array_push($operations, [
                    [
                        'create' => [
                            'campaign' => $campaignResourceName,
                            'location' => [
                                'geoTargetConstant' => $location,
                            ],
                        ]
                    ]
                ]);
            }
        }

        if (isset($requestBody['language'])) {
            array_push($operations, [
                [
                    'create' => [
                        'campaign' => $campaignResourceName,
                        'language' => [
                            'languageConstant' => $requestBody['language'],
                        ],
                    ]
                ]
            ]);
        }

        $response = $this->handleRequest('post', "$this->baseUrl/customers/{$customerId}/campaignCriteria:mutate", [
            'json' => [
                'operations' => $operations,
            ],
        ]);

        if (isset($response['errors'])) {
            return $response;
        }

        return $response['results'][0]['resourceName'];
    }

    public function getAdGroups($customerId)
    {
        $response = $this->handleRequest('post', "$this->baseUrl/customers/$customerId/googleAds:search", [
            'json' => [
                'query' => "SELECT ad_group.id, ad_group.name, ad_group.status, campaign.id, campaign.name, metrics.impressions, metrics.clicks, metrics.ctr, metrics.cost_micros, metrics.conversions FROM ad_group WHERE ad_group.status != 'PAUSED'"
            ],
        ]);

        return $response;
    }

    public function getAdGroupByResourceName($customerId, $adGroupResourceName)
    {
        $response = $this->handleRequest('post', "$this->baseUrl/customers/$customerId/googleAds:search", [
            'json' => [
                'query' => "SELECT ad_group.id, ad_group.name, ad_group.status, campaign.id, campaign.name, metrics.impressions, metrics.clicks, metrics.ctr, metrics.cost_micros, metrics.conversions FROM ad_group WHERE ad_group.resource_name = '$adGroupResourceName'"
            ],
        ]);

        if (isset($response['errors'])) {
            return $response;
        }

        return $response['results'][0];
    }

    public function createAdGroup($customerId, $campaignResourceName, $requestBody = null)
    {
        $response = $this->handleRequest('post', "$this->baseUrl/customers/{$customerId}/adGroups:mutate", [
            'json' => [
                'operations' => [
                    [
                        'create' => [
                            'name' => $requestBody['campaign_name'],
                            'campaign' => $campaignResourceName,
                            'status' => 'ENABLED',
                            // 'cpcBidMicros' => 1 * 1000000 // 1 dollar,
                        ],
                    ],
                ],
            ],
        ]);

        if (isset($response['errors'])) {
            return $response;
        }

        return $response['results'][0]['resourceName'];
    }

    public function addKeywordToAdGroup($customerId, $adGroupResourceName, $requestBody = null)
    {
        $operations = [];
        foreach ($requestBody['keywords'] as $keyword) {
            array_push($operations, [
                [
                    'create' => [
                        'adGroup' => $adGroupResourceName,
                        'keyword' => [
                            'text' => $keyword['text'],
                            'matchType' => $keyword['match_type']
                        ],
                        'status' => 'ENABLED',
                    ]
                ]
            ]);
        }

        $response = $this->handleRequest('post', "$this->baseUrl/customers/{$customerId}/adGroupCriteria:mutate", [
            'json' => [
                'operations' => $operations,
            ]
        ]);

        return $response;
    }

    public function getAds($customerId)
    {
        $response = $this->handleRequest('post', "$this->baseUrl/customers/$customerId/googleAds:search", [
            'json' => [
                'query' => 'SELECT campaign.id, campaign.name, campaign.advertising_channel_type, ad_group.id, ad_group.name, ad_group_ad.ad.id, ad_group_ad.ad.name, ad_group_ad.ad.final_urls, ad_group_ad.status, metrics.impressions, metrics.clicks, metrics.ctr, metrics.cost_micros, metrics.average_cpc, metrics.average_cpm, metrics.conversions FROM ad_group_ad'
            ],
        ]);

        return $response;
    }

    public function getAdByResourceName($customerId, $adResourceName)
    {
        $response = $this->handleRequest('post', "$this->baseUrl/customers/$customerId/googleAds:search", [
            'json' => [
                'query' => "SELECT
                campaign.id, 
                campaign.name, 
                campaign.advertising_channel_type, 
                campaign.start_date, 
                campaign.end_date, 
                ad_group.id, 
                ad_group.name, 
                ad_group.cpc_bid_micros, 
                ad_group.cpm_bid_micros, 
                ad_group_ad.ad.id, 
                ad_group_ad.ad.name, 
                ad_group_ad.ad.final_urls, 
                ad_group_ad.status, 
                ad_group_ad.ad.name,  -- Ad Name
                ad_group_ad.ad.expanded_text_ad.headline_part1,  -- Ad Headline Part 1
                ad_group_ad.ad.expanded_text_ad.headline_part2,  -- Ad Headline Part 2
                ad_group_ad.ad.expanded_text_ad.description,  -- Ad Description
                ad_group_ad.ad.expanded_text_ad.description2,  -- Ad Description 2
                metrics.impressions, 
                metrics.clicks, 
                metrics.ctr, 
                metrics.cost_micros, 
                metrics.average_cpc, 
                metrics.average_cpm, 
                metrics.conversions 
                FROM 
                ad_group_ad 
                WHERE 
                ad_group_ad.resource_name = '$adResourceName'"
            ],
        ]);

        if (isset($response['errors'])) {
            return $response;
        }

        return $response['results'][0];
    }

    public function createAd($customerId, $adGroupResourceName, $requestBody = null)
    {
        $headlines = [];
        foreach ($requestBody['ad_headlines'] as $headline) {
            if ($headline) {
                array_push($headlines, [
                    'text' => $headline,
                ]);
            }
        }

        $descriptions = [];
        foreach ($requestBody['ad_descriptions'] as $description) {
            if ($description) {
                array_push($descriptions, [
                    'text' => $description,
                ]);
            }
        }

        $response = $this->handleRequest('post', "$this->baseUrl/customers/{$customerId}/adGroupAds:mutate", [
            'json' => [
                'operations' => [
                    [
                        'create' => [
                            'adGroup' => $adGroupResourceName,
                            'status' => 'ENABLED',
                            'ad' => [
                                'finalUrls' => [
                                    $requestBody['ad_url']
                                ],
                                'responsiveSearchAd' => [
                                    'path1' => $requestBody['ad_url_1'],
                                    'path2' => $requestBody['ad_url_2'],
                                    'headlines' => $headlines,
                                    'descriptions' => $descriptions,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        if (isset($response['errors'])) {
            return $response;
        }

        return $response['results'][0]['resourceName'];
    }

    public function getConversionActions($customerId)
    {
        $response = $this->handleRequest('post', "$this->baseUrl/customers/{$customerId}/googleAds:search", [
            'json' => [
                'query' => "SELECT conversion_action.resource_name, conversion_action.id, conversion_action.name, conversion_action.status, conversion_action.type, conversion_action.category FROM conversion_action WHERE conversion_action.status != 'REMOVED'"
            ],
        ]);

        return $response;
    }

    public function createConversionAction($customerId, $requestBody = null)
    {
        $operations = [];
        for ($i = 0; $i < count($requestBody['name']); $i++) {
            array_push($operations, [
                [
                    'create' => [
                        'name' => $requestBody['name'][$i],
                        'type' => $requestBody['type'][$i],
                        'category' => $requestBody['category'][$i],
                        'status' => 'ENABLED',
                        /**
                         * FEEDBACK: Not set value
                         */
                        // 'valueSettings' => [
                        //     'defaultValue' => number_format((float) $requestBody['value'][$i], 1, '.', ''),
                        //     'alwaysUseDefaultValue' => true,
                        // ],
                        'countingType' => $requestBody['counting_type'][$i],
                        'clickThroughLookbackWindowDays' => $requestBody['click_through_days'][$i],
                        'viewThroughLookbackWindowDays' => $requestBody['view_through_days'][$i]
                    ]
                ]
            ]);
        }

        $response = $this->handleRequest('post', "$this->baseUrl/customers/{$customerId}/conversionActions:mutate", [
            'json' => [
                'operations' => $operations,
            ]
        ]);

        return $response;
    }

    public function getAssets($customerId)
    {
        $response = $this->handleRequest('post', "$this->baseUrl/customers/{$customerId}/googleAds:search", [
            'json' => [
                'query' => 'SELECT assets.id FROM assets'
            ],
        ]);

        return $response;
    }

    public function createAsset($customerId, $requestBody = null)
    {
        $operations = [];
        for ($i = 0; $i < count($requestBody['sitelinks']); $i++) {
            if (in_array(null, $requestBody['sitelinks'][$i + 1], true)) {
                continue;
            }

            array_push($operations, [
                [
                    'create' => [
                        'sitelink_asset' => [
                            'link_text' => $requestBody['sitelinks'][$i + 1]['text'],
                            'description1' => $requestBody['sitelinks'][$i + 1]['line1'],
                            'description2' => $requestBody['sitelinks'][$i + 1]['line2'],
                        ],
                        'final_urls' => [
                            $requestBody['sitelinks'][$i + 1]['url'],
                        ],
                    ]
                ]
            ]);
        }

        $response = $this->handleRequest('post', "$this->baseUrl/customers/{$customerId}/assets:mutate", [
            'json' => [
                'operations' => $operations,
            ],
        ]);

        return $response;
    }

    public function getGeoTargetConstant($customerId)
    {
        $response = $this->handleRequest('post', "$this->baseUrl/customers/{$customerId}/googleAds:search", [
            'json' => [
                'query' => "SELECT geo_target_constant.resource_name, geo_target_constant.id, geo_target_constant.name, geo_target_constant.country_code FROM geo_target_constant WHERE geo_target_constant.status = 'ENABLED' AND geo_target_constant.country_code = 'SG'"
            ],
        ]);

        return $response;
    }
}
