<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WhatsappTemplateRequest;
use App\Models\ClientMessageTemplate;

/**
 * @group Whatsapp Template
 */
class WhatsappTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('check.menu:whatsapp_template');
    }

    public function wpMessageUpdate(WhatsappTemplateRequest $request)
    {
        $clientId = auth('api')->id() ?? auth('web')->id();

        $clientMessageTemplate = ClientMessageTemplate::where('client_id', $clientId)
            ->first();

        if (!$clientMessageTemplate) {
            // TODO: Change to sendErrorResponse
            return response()->json(['error' => 'No record found to update'], 404);
        }

        ClientMessageTemplate::where('client_id', $clientId)
            ->update(['message_template' => $request->wp_message]);

        // TODO: Change to sendSuccessResponse
        return response()->json([
            'success' => 'WhatsApp Message Template Saved Successfully',
            'reload' => true,
        ]);
    }
}
