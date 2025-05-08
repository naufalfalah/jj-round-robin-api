<?php

namespace App\Services;

use App\Helpers\ActivityLogHelper;
use App\Helpers\TextHelper;
use App\Models\AiContent;
use App\Models\AppSetting;
use GeminiAPI\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\DB;
use OpenAI\Laravel\Facades\OpenAI;

class AIChatService
{
    public function generateMessage($message, $adminId = null, $templateId = null)
    {
        $aiAssistant = AppSetting::where('slug', 'ai_assistant')->first();

        if (!$aiAssistant) {
            throw new \Exception('AI Assistant not correctly configured');
        }

        DB::beginTransaction();
        try {
            // Log activity
            ActivityLogHelper::save_activity(auth('api')->id(), 'AI Content Generation', 'AppSetting', 'app');

            if ($aiAssistant->value == 'open_ai') {
                $messages = [
                    ['role' => 'system', 'content' => 'Reply only with one emoticon.'],
                    ['role' => 'user', 'content' => $message],
                ];
                $result = OpenAI::chat()->create([
                    'model' => 'gpt-3.5-turbo',
                    'messages' => $messages,
                ]);
            } elseif ($aiAssistant->value == 'gemini') {
                $result = Gemini::generateText($message);
            }

            $convertedContent = TextHelper::convertToPlainText($result);

            // Save generated content
            $aiContent = AiContent::create([
                'addedby_id' => $adminId,
                'template_id' => $templateId,
                'property' => $message,
                'generated_content' => $result,
                'converted_content' => $convertedContent,
            ]);

            DB::commit();

            return $aiContent;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception('Unsupported AI Assistant');
        }
    }
}
