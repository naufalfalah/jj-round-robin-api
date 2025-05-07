<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ClientMessageTemplate;
use App\Models\WpMessageTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MessageTemplateController extends Controller
{
    //

    public function whatsapp_temp()
    {


        $clientId = auth()->user()->id;

        $check_msg_status = WpMessageTemplate::first();
        if ($check_msg_status->status === 'active') {
            $data = [
                'breadcrumb_main' => 'Message Template',
                'breadcrumb' => 'WhatsApp Message Template',
                'title' => 'WhatsApp Message Template',
                'whatsapp_temp' => ClientMessageTemplate::where('client_id', $clientId)->first(),
            ];

            return view('client.message_temp.index', $data);
        } else (abort(404));
    }

    public function wp_message_store(Request $request)
    {

        $rules = [
            'wp_message' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        DB::beginTransaction();

        try {
            $clientId = auth()->user()->id;
            // dd($clientId);

            $messageTemplate = $request->wp_message;

            $update = ClientMessageTemplate::updateOrCreate(
                ['client_id' => $clientId],
                ['message_template' => $messageTemplate]
            );

            if ($update) {
                DB::commit();
                $msg = [
                    'success' => 'WhatsApp Message Template Saved Successfully',
                    'reload' => true
                ];
                return response()->json($msg);
            } else {
                DB::rollback();
                return response()->json(['error' => 'No record found to update'], 404);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'WhatsApp Message Template: ' . $e->getMessage()], 500);
        }
    }
}
