<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Models\LeadActivity;
use App\Models\LeadClient;
use App\Models\TempActivity;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * @group Email Template
 */
class EmailTemplateController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware('check.menu:email_template');
    }

    public function get_email_template(Request $request)
    {
        $user_id = auth('api')->user()->id;
        $email_template = EmailTemplate::where('client_id', $user_id)->latest()->get();

        ActivityLogHelper::save_activity(auth('api')->id(), 'View Email Templates', 'EmailTemplate', 'app');

        if ($email_template->isNotEmpty()) {
            $formattedTemplate = [];
            foreach ($email_template as $temp) {
                $formattedTemplate[] = [
                    'id' => $temp->id,
                    'title' => $temp->title,
                    'description' => $temp->description,
                    'private_note' => $temp->private_note,
                    'created_at' => $temp->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $temp->updated_at->format('Y-m-d H:i:s'),
                ];
            }

            $data = [
                'email_template' => $formattedTemplate,
            ];

            return $this->sendSuccessResponse('Email Template Fetch Successfully', $data);
        } else {
            return $this->sendErrorResponse('No Email Template found.', 404);
        }
    }

    public function add_email_temp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            $errorMessages = [];
            foreach ($errors->all() as $message) {
                $errorMessages[] = $message;
            }

            return $this->sendErrorResponse(implode("\n ", $errorMessages), 400);
        }
        $user_id = auth('api')->user()->id;

        DB::beginTransaction();

        try {
            $email_temp = new EmailTemplate;
            $email_temp->client_id = $user_id;
            $email_temp->title = $request->title;
            $email_temp->description = $request->description;
            $email_temp->private_note = $request->private_note;
            $email_temp->save();
            $msg = 'Email Template Added Successfully';

            ActivityLogHelper::save_activity(auth('api')->id(), 'Add Email Template', 'EmailTemplate', 'app');
            // Commit the transaction
            DB::commit();

            return $this->sendSuccessResponse($msg, $email_temp);
        } catch (\Exception $e) {

            // Something went wrong, rollback the transaction
            DB::rollback();

            // Log or handle the exception as needed
            return response()->json(['error' => 'Error Templpate: '.$e->getMessage()], 500);
        }
    }

    public function edit_email_temp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'template_id' => 'required',
            'title' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            $errorMessages = [];
            foreach ($errors->all() as $message) {
                $errorMessages[] = $message;
            }

            return $this->sendErrorResponse(implode("\n ", $errorMessages), 400);
        }

        $user_id = auth('api')->user()->id;
        DB::beginTransaction();
        $email_temp = EmailTemplate::find($request->template_id);
        if ($email_temp) {
            try {

                $email_temp->client_id = $user_id;
                $email_temp->title = $request->title;
                $email_temp->description = $request->description;
                $email_temp->private_note = $request->private_notev ?? '';
                $email_temp->save();
                $msg = 'Email Template Updated Successfully';

                ActivityLogHelper::save_activity(auth('api')->id(), 'Update Email Template', 'EmailTemplate', 'app');
                // Commit the transaction
                DB::commit();

                return $this->sendSuccessResponse($msg, $email_temp);
            } catch (\Exception $e) {

                // Something went wrong, rollback the transaction
                DB::rollback();

                // Log or handle the exception as needed
                return response()->json(['error' => 'Error Templpate: '.$e->getMessage()], 500);
            }
        } else {
            return $this->sendErrorResponse('Email Template Not Found With The Given ID.', 400);
        }
    }

    public function delete_email_temp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'template_id' => 'required',
        ]);

        return response()->json($request);
        if ($validator->fails()) {
            $errors = $validator->errors()->first('template_id');

            return $this->sendErrorResponse($errors, 400);
        }
        $email_temp = EmailTemplate::find($request->template_id);
        ActivityLogHelper::save_activity(auth('api')->id(), 'Delete Email Template', 'EmailTemplate', 'app');
        if ($email_temp) {
            $email_temp->delete();

            return $this->sendSuccessResponse('Email Template Deleted Successfully');
        } else {
            return $this->sendErrorResponse('Email Template Not Found With The Given ID.', 404);
        }
    }

    public function send_email(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lead_id' => 'required',
            'email_template_id' => 'required',
            'title' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            $errorMessages = [];
            foreach ($errors->all() as $message) {
                $errorMessages[] = $message;
            }

            return $this->sendErrorResponse(implode("\n ", $errorMessages), 400);
        }
        $user_id = auth('api')->user()->id;

        // Start a database transaction
        DB::beginTransaction();

        try {

            $lead_client = LeadClient::find($request->lead_id);

            if (isset($lead_client) && !empty($lead_client->name)) {

                $email_template = EmailTemplate::find($request->email_template_id);

                if (isset($email_template) && !empty($email_template)) {
                    $message_activity = new TempActivity;

                    $message_activity->template_id = $request->email_template_id;
                    $message_activity->client_id = $request->lead_id;
                    $message_activity->template_type = 'email';
                    $message_activity->activity_route = 'api';
                    $message_activity->save();

                    $message_lead_activity = new LeadActivity;

                    $message_lead_activity->lead_client_id = $request->lead_id;
                    $message_lead_activity->title = $request->title;
                    $message_lead_activity->description = $request->description;
                    $message_lead_activity->date_time = now()->format('Y-m-d h:i');
                    $message_lead_activity->type = 'email';
                    $message_lead_activity->email_template_id = $request->email_template_id;
                    $message_lead_activity->activity_route = 'api';
                    $message_lead_activity->user_type = 'user';
                    $message_lead_activity->added_by_id = $user_id;
                    $message_lead_activity->save();
                    ActivityLogHelper::save_activity(auth('api')->id(), 'Send Email Template', 'EmailTemplate', 'app');
                    // Commit the transaction
                    DB::commit();

                    $msg = 'Email Send Successfully';

                    return $this->sendSuccessResponse($msg, $message_lead_activity);
                } else {
                    $errorMsg = 'Email Template Not Found With Given ID.';
                }
            } else {
                $errorMsg = 'Lead client Not Found With Given ID.';
            }

            // Rollback the transaction
            DB::rollBack();

            // Return an error response
            return response()->json(['error' => $errorMsg], 404);
        } catch (\Exception $e) {
            // Something went wrong, rollback the transaction
            DB::rollback();

            // Log or handle the exception as needed
            return response()->json(['error' => 'Error Templpate: '.$e->getMessage()], 500);
        }
    }

    public function get_single_templates(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'template_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->first('template_id');

            return $this->sendErrorResponse($errors, 400);
        }
        $user_id = auth('api')->user()->id;
        $client_leads = EmailTemplate::with('message_activity')->where('client_id', $user_id)->where('id', $request->template_id)->latest()->first();
        ActivityLogHelper::save_activity(auth('api')->id(), 'View Single Email Template', 'EmailTemplate', 'app');
        if ($client_leads) {
            return $this->sendSuccessResponse('Email Template fetch Successfully', $client_leads);
        } else {
            return $this->sendErrorResponse('Email Template Not Found With The Given ID.', 404);
        }
    }
}
