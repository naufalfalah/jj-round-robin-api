<?php

namespace App\Services;

use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Illuminate\Support\Facades\Storage;

class GoogleTextToSpeechService
{
    public function synthesizeSpeech(string $text, string $outputFile = 'output.mp3', string $languageCode = 'en-SG', string $voiceName = 'en-SG-Standard-A')
    {
        $client = new TextToSpeechClient([
            'credentials' => base_path(config('services.google.credentials')),
        ]);

        $input = new SynthesisInput();
        $input->setText($text);

        $voice = new VoiceSelectionParams();
        $voice->setLanguageCode($languageCode);
        $voice->setName($voiceName);

        $audioConfig = new AudioConfig();
        $audioConfig->setAudioEncoding(AudioEncoding::MP3);

        $response = $client->synthesizeSpeech($input, $voice, $audioConfig);

        $audioContent = $response->getAudioContent();
        Storage::disk('ai-content')->put($outputFile, $audioContent);

        $client->close();

        return asset("storage/{$outputFile}");
    }
}
