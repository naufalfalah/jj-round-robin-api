<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AIVoiceChatRequest;
use App\Services\AIChatService;
use App\Services\ElevenLabsService;
use App\Services\GoogleTextToSpeechService;
use Illuminate\Support\Facades\Storage;

/**
 * @group AI Voice Chat
 */
class AIVoiceChatController extends Controller
{
    protected $aiChatService;

    protected $elevenLabsService;

    protected $googleTextToSpeechService;

    public function __construct(AIChatService $aiChatService, ElevenLabsService $elevenLabsService, GoogleTextToSpeechService $googleTextToSpeechService)
    {
        $this->aiChatService = $aiChatService;
        $this->elevenLabsService = $elevenLabsService;
        $this->googleTextToSpeechService = $googleTextToSpeechService;
    }

    public function sendMessage(AIVoiceChatRequest $request)
    {
        try {
            $aiContent = $this->aiChatService->generateMessage($request->message);

            // Generate text to speech
            $audioFileName = "ai_content_{$aiContent->id}_audio_".time().'.mp3';
            $audioData = $this->googleTextToSpeechService->synthesizeSpeech($aiContent->property, $audioFileName);

            // Save audio
            $audioUrl = null;
            if ($audioData) {
                $audioUrl = Storage::disk('ai-content')->url($audioFileName);
                $aiContent->audio_path = $audioFileName;
                $aiContent->save();
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Audio generation failed.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'aiResponse' => $aiContent->text,
                    'audioUrl' => $audioUrl,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
