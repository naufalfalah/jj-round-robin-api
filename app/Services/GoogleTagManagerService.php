<?php

namespace App\Services;

use App\Models\Admin;
use App\Traits\GoogleTrait;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class GoogleAdsService
{
    use GoogleTrait;

    protected $baseUrl = 'https://tagmanager.googleapis.com/v2';
    protected $client;

    public function __construct()
    {
        $this->getAdminClient();
        $this->initializeClient();
    }

    private function initializeClient()
    {
        $admin = Admin::find(1);
        $authorization = json_decode($admin->google_access_token, true);
        $accessToken = $authorization['access_token'];

        $this->client = new Client([
            'headers' => [
                'Authorization' => "Bearer $accessToken",
                'Content-Type' => 'application/json',
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
                $this->refreshToken();
                $response = $this->client->{$method}($url, $options);
            } elseif ($statusCode == 403) {
                $response = $e->getResponse();
            } else {
                throw $e;
            }
        }

        $response = json_decode($response->getBody(), true);

        if (isset($response['error'])) {
            $errorCode = $response['error']['details'][0]['errors'][0]['errorCode'];
            return [
                'errors' => $errorCode,
            ];
        }

        return $response;
    }

    public function listAccounts()
    {
        $response = $this->handleRequest('get', "$this->baseUrl/accounts");
        
        return $response;
    }

    public function getAccount($accountId)
    {
        $response = $this->handleRequest('get', "$this->baseUrl/accounts/{$accountId}");
        
        return $response;
    }

    public function updateAccount($accountId, $requestBody)
    {
        $response = $this->handleRequest('put', "$this->baseUrl/accounts/{$accountId}", [
            'json' => [
                'name' => $requestBody['name'],
                'shareData' => true,
            ],
        ]);
        
        return $response;
    }

    public function listUserPermissions($accountId)
    {
        $response = $this->handleRequest('get', "$this->baseUrl/accounts/{$accountId}");
        
        return $response;
    }

    public function listContainers($accountId)
    {
        $response = $this->handleRequest('get', "$this->baseUrl/accounts/{$accountId}");
        
        return $response;
    }

    public function createContainer($accountId, $requestBody = null)
    {
        $response = $this->handleRequest('post', "$this->baseUrl/accounts/{$accountId}/containers", [
            'json' => [
                'name' => $requestBody['name'],
                'usageContext' => ['web'],
            ],
        ]);

        return $response;
    }

    public function listWorkspaces($accountId, $containerId)
    {
        $response = $this->handleRequest('get', "$this->baseUrl/accounts/{$accountId}/containers/{$containerId}/workspaces");
        
        return $response;
    }

    public function listEnvironments($accountId, $containerId)
    {
        $response = $this->handleRequest('get', "$this->baseUrl/accounts/{$accountId}/containers/{$containerId}/environments");
        
        return $response;
    }

    public function listBuiltInVariables($accountId, $containerId, $workspaceId)
    {
        $response = $this->handleRequest('get', "$this->baseUrl/accounts/{$accountId}/containers/{$containerId}/workspaces/{$workspaceId}/built_in_variables");
        
        return $response;
    }

    public function listClients($accountId, $containerId, $workspaceId)
    {
        $response = $this->handleRequest('get', "$this->baseUrl/accounts/{$accountId}/containers/{$containerId}/workspaces/{$workspaceId}/clients");
        
        return $response;
    }

    public function listFolders($accountId, $containerId, $workspaceId)
    {
        $response = $this->handleRequest('get', "$this->baseUrl/accounts/{$accountId}/containers/{$containerId}/workspaces/{$workspaceId}/folders");
        
        return $response;
    }

    public function listGtagConfigs($accountId, $containerId, $workspaceId)
    {
        $response = $this->handleRequest('get', "$this->baseUrl/accounts/{$accountId}/containers/{$containerId}/workspaces/{$workspaceId}/gtag_config");
        
        return $response;
    }

    public function listTags($accountId, $containerId, $workspaceId)
    {
        $response = $this->handleRequest('get', "$this->baseUrl/accounts/{$accountId}/containers/{$containerId}/workspaces/{$workspaceId}/tags");
        
        return $response;
    }

    public function createTags($accountId, $containerId, $workspaceId, $requestBody = null)
    {
        $response = $this->handleRequest('post', "$this->baseUrl/accounts/{$accountId}/containers/{$containerId}/workspaces/{$workspaceId}/tags", [
            'json' => [
                'name' => $requestBody['name'],
                'type' => $requestBody['type'],
                'parameter' => [
                    [
                        'key' => 'conversionId',
                        'type' => 'template',
                        'value' => $requestBody['conversion_id']
                    ],
                    [
                        'key' => 'conversionLabel',
                        'type' => 'template',
                        'value' => $requestBody['conversion_label']
                    ]
                ],
                'firingTriggerId' => [
                    $requestBody['trigger_id']
                ]
            ],
        ]);

        return $response;
    }

    public function listTemplates($accountId, $containerId, $workspaceId)
    {
        $response = $this->handleRequest('get', "$this->baseUrl/accounts/{$accountId}/containers/{$containerId}/workspaces/{$workspaceId}/templates");
        
        return $response;
    }

    public function listTransformations($accountId, $containerId, $workspaceId)
    {
        $response = $this->handleRequest('get', "$this->baseUrl/accounts/{$accountId}/containers/{$containerId}/workspaces/{$workspaceId}/transformations");
        
        return $response;
    }

    public function listTriggers($accountId, $containerId, $workspaceId)
    {
        $response = $this->handleRequest('get', "$this->baseUrl/accounts/{$accountId}/containers/{$containerId}/workspaces/{$workspaceId}/triggers");
        
        return $response;
    }

    public function createTriggers($accountId, $containerId, $workspaceId, $requestBody = null)
    {
        $response = $this->handleRequest('post', "$this->baseUrl/accounts/{$accountId}/containers", [
            'json' => [
                'name' => $requestBody['name'],
                'type' => 'PAGEVIEW',
                'filter' => []
            ],
        ]);

        return $response;
    }

    public function listVariables($accountId, $containerId, $workspaceId)
    {
        $response = $this->handleRequest('get', "$this->baseUrl/accounts/{$accountId}/containers/{$containerId}/workspaces/{$workspaceId}/variables");
        
        return $response;
    }

    public function listZones($accountId, $containerId, $workspaceId)
    {
        $response = $this->handleRequest('get', "$this->baseUrl/accounts/{$accountId}/containers/{$containerId}/workspaces/{$workspaceId}/zones");
        
        return $response;
    }
}
