<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Models\LeadActivity;
use App\Models\LeadClient;
use App\Models\MessageTemplate;
use App\Models\TempActivity;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * @group File Manager
 *
 * @subgroup Message Template
 *
 * @authenticated
 */
class MessageTemplateController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware('check.menu:message_template');
    }

    public function getTemplates(Request $request)
    {
        $userId = auth('api')->user()->id;
        $messageTemplates = MessageTemplate::where('client_id', $userId)->latest()->get(['id', 'title', 'description', 'private_note', 'created_at', 'updated_at']);

        ActivityLogHelper::save_activity($userId, 'View Message Template', 'MessageTemplate', 'app');

        if ($messageTemplates->isEmpty()) {
            return $this->sendErrorResponse('No Message Template found.', 404);
        }

        $formattedTemplates = $messageTemplates->map(function ($template) {
            return [
                'id' => $template->id,
                'title' => $template->title,
                'description' => $template->description,
                'private_note' => $template->private_note,
                'created_at' => $template->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $template->updated_at->format('Y-m-d H:i:s'),
            ];
        });

        return $this->sendSuccessResponse('Message Template Fetch Successfully', ['message_template' => $formattedTemplates]);
    }

    public function getTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'template_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->first('template_id');

            return $this->sendErrorResponse($errors, 400);
        }

        $user_id = auth('api')->user()->id;

        $client_leads = MessageTemplate::with('message_activity')->where('client_id', $user_id)->where('id', $request->template_id)->latest()->first();
        if (!$client_leads) {
            return $this->sendErrorResponse('Message Template Not Found With The Given ID.', 404);
        }

        $data = [
            'client_leads' => $client_leads,
        ];

        ActivityLogHelper::save_activity(auth('api')->id(), 'View Single Template', 'MessageTemplate', 'app');

        return $this->sendSuccessResponse('Message Template fetch Successfully', $data);
    }

    /**
     * Create Message Template
     */
    public function createMessageTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendErrorResponse($validator->errors()->first(), 400);
        }

        $userId = auth('api')->user()->id;

        $messageTemplate = MessageTemplate::create([
            'client_id' => $userId,
            'title' => $request->title,
            'description' => $request->description,
            'private_note' => $request->private_note ?? '',
        ]);

        ActivityLogHelper::save_activity($userId, 'Add Message Template', 'MessageTemplate', 'app');

        return $this->sendSuccessResponse('Message Template Added Successfully', $messageTemplate);
    }

    public function editTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'template_id' => 'required',
            'title' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendErrorResponse($validator->errors()->first(), 400);
        }

        $messageTemplate = MessageTemplate::find($request->template_id);
        if (!$messageTemplate) {
            return $this->sendErrorResponse('Message Template Not Found With The Given ID.', 404);
        }

        $messageTemplate->update([
            'title' => $request->title,
            'description' => $request->description,
            'private_note' => $request->private_note ?? '',
        ]);

        ActivityLogHelper::save_activity(auth('api')->id(), 'Update Message Template', 'MessageTemplate', 'app');

        return $this->sendSuccessResponse('Message Template Updated Successfully', $messageTemplate);
    }

    public function deleteTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'template_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->first('template_id');

            return $this->sendErrorResponse($errors, 400);
        }

        $msg_temp = MessageTemplate::find($request->template_id);
        if (!$msg_temp) {
            return $this->sendErrorResponse('Message Template Not Found With The Given ID.', 404);
        }

        $msg_temp->delete();

        ActivityLogHelper::save_activity(auth('api')->id(), 'Delete Message Template', 'MessageTemplate', 'app');

        return $this->sendSuccessResponse('Message Template Deleted Successfully');
    }

    public function sendEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'subject' => 'required',
            'body' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendErrorResponse($validator->errors()->first(), 400);
        }

        send_email('send_mail', $request->email, $request->subject, ['subject' => $request->subject, 'body' => $request->body]);

        ActivityLogHelper::save_activity(auth('api')->id(), 'Email Sent', 'MessageTemplate', 'app');

        return $this->sendSuccessResponse('Email Sent Successfully');
    }

    public function sendWhatsappMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            $errorMessages = [];
            foreach ($errors->all() as $message) {
                $errorMessages[] = $message;
            }

            return $this->sendErrorResponse(implode("\n ", $errorMessages), 400);
        }

        send_whatsapp_msg($request->message, $request->phone_number);
        ActivityLogHelper::save_activity(auth('api')->id(), 'Send WhatsApp Message', 'MessageTemplate', 'app');

        return $this->sendSuccessResponse('Whatsapp message sent', []);
    }

    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lead_id' => 'required|exists:lead_clients,id',
            'msg_template_id' => 'required|exists:message_templates,id',
            'title' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendErrorResponse($validator->errors()->first(), 400);
        }

        DB::beginTransaction();
        try {
            $leadClient = LeadClient::findOrFail($request->lead_id);

            $messageActivity = TempActivity::create([
                'template_id' => $request->msg_template_id,
                'client_id' => $request->lead_id,
                'template_type' => 'message',
                'activity_route' => 'api',
            ]);

            $messageLeadActivity = LeadActivity::create([
                'lead_client_id' => $request->lead_id,
                'title' => $request->title,
                'description' => $request->description,
                'date_time' => now(),
                'type' => 'message',
                'message_template_id' => $request->msg_template_id,
                'activity_route' => 'api',
                'user_type' => 'user',
                'added_by_id' => auth('api')->user()->id,
            ]);

            ActivityLogHelper::save_activity(auth('api')->id(), 'Message Sent To Lead Client', 'MessageTemplate', 'app');
            DB::commit();

            return $this->sendSuccessResponse('Message Sent Successfully', $messageLeadActivity);
        } catch (\Exception $e) {
            DB::rollback();

            // TODO: Change to sendErrorResponse
            return response()->json(['error' => 'Error Template: '.$e->getMessage()], 500);
        }
    }
}
