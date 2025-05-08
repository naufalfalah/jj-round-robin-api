<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

/**
 * @group Message
 */
class MessageController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsappService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function fetchMessages(Request $request)
    {
        $validated = $request->validate([
            'to_phone_number' => 'required|string',
            'page' => 'integer|min:1',
        ]);

        $countryCode = '+65';
        $phone_number = $validated['to_phone_number'];
        $page = $validated['page'] ?? 1;

        // Modification the request
        $messagesPerPage = 5;
        $apiMessagePerPage = 100;
        $pagination = $apiMessagePerPage / $messagesPerPage;
        $apiPage = floor(($page - 1) / $pagination);

        $messages = $this->whatsappService->getAllMessages($countryCode.$phone_number, $apiPage);

        // Modification the response
        $startIndex = ($page - 1) * $messagesPerPage;

        $filteredMessages = [];
        if (isset($messages) && isset($messages['messages'])) {
            $filteredMessages = array_slice($messages['messages'], $startIndex, $messagesPerPage);
        }

        return response()->json([
            'success' => true,
            'data' => $filteredMessages,
        ]);
    }

    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'to_phone_number' => 'required|string',
            'message' => 'required|string',
        ]);

        $response = $this->whatsappService->sendMessage($validated['to_phone_number'], $validated['message']);

        return response()->json([
            'success' => isset($response['success']) ? $response['success'] : false,
            'data' => $response,
        ]);
    }

    public function getMessageStatus(Request $request)
    {
        $validated = $request->validate([
            'session_key' => 'required|string',
            'message_uuid' => 'required|string',
        ]);

        $status = $this->whatsappService->getMessageStatus($validated['session_key'], $validated['message_uuid']);

        return response()->json([
            'success' => isset($status['success']) ? $status['success'] : false,
            'data' => $status,
        ]);
    }

    public function getAllNumbers()
    {
        $numbers = $this->whatsappService->getAllNumbers();

        return response()->json([
            'success' => isset($numbers['success']) ? $numbers['success'] : false,
            'data' => $numbers,
        ]);
    }

    public function checkNumber(Request $request)
    {
        $validated = $request->validate([
            'phone_number' => 'required|string',
        ]);

        $response = $this->whatsappService->checkNumber($validated['phone_number']);

        return response()->json([
            'success' => isset($response['success']) ? $response['success'] : false,
            'data' => $response,
        ]);
    }
}
