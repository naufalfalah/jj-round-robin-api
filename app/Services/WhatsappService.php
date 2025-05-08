<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class WhatsappService
{
    protected $client;
    protected $baseUrl;
    protected $apiKey;
    protected $phoneNumber;

    public function __construct()
    {
        $this->client = new Client();
        $this->baseUrl = 'https://api.p.2chat.io/open/whatsapp';
        $this->apiKey = config('services.2chat.api_key');
        $this->phoneNumber = config('services.2chat.phone_number');
    }

    /**
     * Fetch all Whatsapp number.
     *
     * @param string $sessionKey
     * @return array
     */
    public function getAllNumbers()
    {
        try {
            $response = $this->client->get("{$this->baseUrl}/get-numbers", [
                'headers' => [
                    'X-User-API-Key' => $this->apiKey,
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Set Whatsapp status.
     *
     * @param string $status
     * @return array
     */
    public function setStatus($status)
    {
        try {
            $response = $this->client->post("{$this->baseUrl}/set-status/{$this->phoneNumber}", [
                'headers' => [
                    'X-User-API-Key' => $this->apiKey,
                ],
                'json' => [
                    'status' => $status,
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Set Whatsapp profile picture.
     *
     * @param string $url
     * @return array
     */
    public function setProfilePicture($url)
    {
        try {
            $response = $this->client->post("{$this->baseUrl}/set-status/{$this->phoneNumber}", [
                'headers' => [
                    'X-User-API-Key' => $this->apiKey,
                ],
                'json' => [
                    'url' => $url,
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Send a WhatsApp message via 2Chat API.
     *
     * @param string $toPhoneNumber
     * @param string $message
     * @return array
     */
    public function sendMessage($toPhoneNumber, $message)
    {
        try {
            $response = $this->client->post("{$this->baseUrl}/send-message", [
                'headers' => [
                    'X-User-API-Key' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'to_number' => $toPhoneNumber,
                    'from_number' => $this->phoneNumber,
                    'text' => $message,
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Fetch all messages from phone number.
     *
     * @param string $phoneNumber
     * @return array
     */
    public function getAllMessages($toPhoneNumber, $page = 0)
    {
        try {
            $response = $this->client->get("{$this->baseUrl}/messages/{$this->phoneNumber}/{$toPhoneNumber}", [
                'headers' => [
                    'X-User-API-Key' => $this->apiKey,
                ],
                'query' => [
                    'page_number' => $page,
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Fetch message status (sent, received, read).
     *
     * @param string $sessionKey
     * @param string $messageUUID
     * @return array
     */
    public function getMessageStatus($sessionKey, $messageUUID)
    {
        try {
            $response = $this->client->get("{$this->baseUrl}/message/{$sessionKey}/{$messageUUID}", [
                'headers' => [
                    'X-User-API-Key' => $this->apiKey,
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Check if a phone number is on Whatsapp.
     *
     * @param string $phoneNumber
     * @param string $checkPhoneNumber
     * @return array
     */
    public function checkNumber($checkPhoneNumber)
    {
        try {
            $response = $this->client->get("{$this->baseUrl}/check-number/{$this->phoneNumber}/{$checkPhoneNumber}", [
                'headers' => [
                    'X-User-API-Key' => $this->apiKey,
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Handle Guzzle exceptions and return a formatted error response.
     *
     * @param RequestException $e
     * @return array
     */
    protected function handleException(RequestException $e)
    {
        if ($e->hasResponse()) {
            return [
                'success' => false,
                'error' => json_decode($e->getResponse()->getBody(), true),
            ];
        }

        return [
            'success' => false,
            'error' => 'An error occurred while making the request',
        ];
    }
}
