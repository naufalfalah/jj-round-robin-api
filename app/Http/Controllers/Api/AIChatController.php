<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiContent;
use App\Services\AIChatService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

/**
 * @group AI Chat
 */
class AIChatController extends Controller
{
    use ApiResponseTrait;

    protected $aiChatService;

    public function __construct(AIChatService $aiChatService)
    {
        $this->aiChatService = $aiChatService;
    }

    public function index()
    {
        $userId = auth('api')->user()->id;
        $chats = AiContent::where('addedby_id', $userId)
            ->orderBy('created_at')
            ->get();

        return $this->sendSuccessResponse('AI chat fetched successfully', $chats);
    }

    public function generateMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        try {
            $result = $this->aiChatService->generateMessage(
                $request->message,
                $request->admin_id,
                $request->template_id
            );

            return response()->json([
                'success' => true,
                'content' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'content' => $e->getMessage(),
            ], 422);
        }
    }
}
