<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ElevenLabsService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.elevenlabs.api_key');
    }

    public function generateVoice($text, $voice_id)
    {
        $response = Http::withHeaders([
            'xi-api-key' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])
            ->post('https://api.elevenlabs.io/v1/text-to-speech/'.$voice_id, [
                'text' => $text,
            ]);

        if ($response->successful()) {
            return $response->body();
        }

        Log::error('ElevenLabs API Error:', $response->json());

        return null;
    }
}
