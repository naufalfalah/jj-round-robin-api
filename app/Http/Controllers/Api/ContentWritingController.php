<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AIChatService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

/**
 * @group Request Service
 *
 * @subgroup Content Writing
 *
 * @authenticated
 */
class ContentWritingController extends Controller
{
    use ApiResponseTrait;

    protected $aiChatService;

    protected $openApiKey;

    public function __construct(AIChatService $aiChatService)
    {
        $this->aiChatService = $aiChatService;
        $this->openApiKey = config('services.open_ai.api_key');
    }

    /**
     * Generate From Scratch
     */
    public function generateFromScratch(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'tone' => 'required|string',
            'keywords' => 'required|string',
            'language' => 'required|string',
            'length' => 'required|string|in:short,medium,long',
        ]);

        $title = $request->title;
        $description = $request->description;
        $tone = $request->tone;
        $keywords = $request->keywords;
        $language = $request->language;
        $length = $request->length;

        $prompt = "Write a {$length} content in {$language} with the following details:
        - Title: {$title}
        - Description: {$description}
        - Tone: {$tone}
        - Keywords: {$keywords}.";

        $generatedContent = $this->aiChatService->generateMessage($prompt);

        return $this->sendSuccessResponse('Content generated successfully', $generatedContent);
    }

    private function determineMaxTokens($length)
    {
        return match ($length) {
            'short' => 500,
            'medium' => 1000,
            'long' => 1500,
            default => 1000,
        };
    }

    /**
     * Generate Content
     */
    public function generateBlogPost(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'detail' => 'required|string',
        ]);

        $category = $request->category;
        $detail = $request->detail;

        $prompt = $this->generatePrompt($category, $detail);

        $generatedContent = $this->aiChatService->generateMessage($prompt);

        return $this->sendSuccessResponse('Content generated successfully', $generatedContent);
    }

    private function generatePrompt($category, $detail)
    {
        $prompts = [
            'content_improver' => "Improve the following content: \n\n{$detail}",
            'blog_post_outline' => "Create an outline for a blog post about: \n\n{$detail}",
            'blog_post_topic_ideas' => "Generate blog post topic ideas for: \n\n{$detail}",
            'blog_post_intro_paragraph' => "Write an intro paragraph for a blog post about: \n\n{$detail}",
            'blog_post_conclusion_paragraph' => "Write a conclusion paragraph for a blog post about: \n\n{$detail}",
            'seo_blog_post' => "Write a detailed SEO-friendly blog post about: \n\n{$detail}",
        ];

        return $prompts[$category] ?? "Please process the following detail: \n\n{$detail}";
    }
}
