<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Models\LeadActivity;
use App\Models\LeadClient;
use App\Models\PageTemplate;
use App\Models\PageTemplateGallery;
use App\Models\PageWebsiteLink;
use App\Models\PageYoutubeLink;
use App\Models\TempActivity;
use App\Traits\ApiResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * @group Page Template
 */
class PageTemplateController extends Controller
{
    use ApiResponseTrait;

    public function page_save(Request $request)
    {
        if ($request->type != 'note') {
            $rules = [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'website_title.*' => 'string|nullable',
                'website_link.*' => 'url|nullable',
                'youtube_title.*' => 'string|nullable',
                'youtube_link.*' => 'url|nullable',
                'pages_images.*' => 'file|mimes:png,jpeg,jpg|nullable',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return ['errors' => $validator->errors()];
            }
        }

        DB::beginTransaction();

        try {

            if ($request->id && !empty($request->id)) {

                if ($request->type == 'note') {

                    $pageNote = PageTemplate::find($request->id);

                    ActivityLogHelper::save_activity(auth('api')->id(), 'Private Note Page save', 'PageTemplate');

                    $pageNote->private_note = $request->note;
                    $pageNote->save();
                    DB::commit();
                    $pageTemplate = PageTemplate::find($request->id);
                    $msg = 'Private Note Updated';
                }

            } else {

                if (isset($request->edit_id) && !empty($request->edit_id)) {
                    $pageTemplate = PageTemplate::find($request->edit_id);
                    $msg = 'Page Updated Successfully';
                } else {
                    $pageTemplate = new PageTemplate;
                    $msg = 'Page Created Successfully';
                }

                $pageTemplate->client_id = auth('api')->id();
                $pageTemplate->title = $request->title;
                $pageTemplate->description = $request->description ?? null;
                $pageTemplate->google_maps = $request->google_location ?? null;
                $pageTemplate->save();

                if (isset($request->website_title) && is_array($request->website_title)) {
                    if (isset($request->edit_id) && !empty($request->edit_id)) {
                        PageWebsiteLink::where('page_id', $request->edit_id)->delete();
                    }
                    foreach ($request->website_title as $key => $title) {
                        if (!empty($title) && !empty($request->website_link[$key])) {
                            $pageWebsiteLink = new PageWebsiteLink;
                            $pageWebsiteLink->page_id = $pageTemplate->id;
                            $pageWebsiteLink->link_title = $title;
                            $pageWebsiteLink->website_link = $request->website_link[$key];
                            $pageWebsiteLink->save();
                        }
                    }
                }

                if (isset($request->youtube_title) && is_array($request->youtube_title)) {  // Corrected isset typo
                    if (isset($request->edit_id) && !empty($request->edit_id)) {
                        PageYoutubeLink::where('page_id', $request->edit_id)->delete();
                    }
                    foreach ($request->youtube_title as $key => $title) {
                        if (!empty($title) && !empty($request->youtube_link[$key])) {
                            $pageYoutubeLink = new PageYoutubeLink;
                            $pageYoutubeLink->page_id = $pageTemplate->id;
                            $pageYoutubeLink->link_title = $title;
                            $pageYoutubeLink->youtube_link = $request->youtube_link[$key];
                            $pageYoutubeLink->save();
                        }
                    }
                }

                if ($request->hasFile('pages_images')) {
                    if (isset($request->edit_id) && !empty($request->edit_id)) {
                        PageTemplateGallery::where('page_id', $request->edit_id)->delete();
                    }
                    foreach ($request->file('pages_images') as $key => $image) {

                        $originalName = $image->getClientOriginalName();

                        $imgPath = uploadSingleFile($image, 'uploads/client_files/page_images/');

                        if ($key === 0) {
                            $pageTemplate->cover_image = $imgPath;
                            $pageTemplate->save();
                        }

                        $pageTemplateGallery = new PageTemplateGallery;
                        $pageTemplateGallery->page_id = $pageTemplate->id;
                        $pageTemplateGallery->title = $originalName;
                        $pageTemplateGallery->images = $imgPath;
                        $pageTemplateGallery->save();
                    }
                }

                ActivityLogHelper::save_activity(auth('api')->id(), 'Page Creation', 'PageTemplate');
            }

            DB::commit();

            return $this->sendSuccessResponse($msg, $pageTemplate);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => 'Page Creation: '.$e->getMessage()], 500);
        }
    }

    public function get_template(Request $request)
    {
        $user_id = auth('api')->user()->id;
        $page_template = PageTemplate::with('page_activity')->where('client_id', $user_id)->latest()->get();
        ActivityLogHelper::save_activity(auth('api')->id(), 'View Page Template', 'PageTemplate', 'app');
        if ($page_template->isNotEmpty()) {
            $formattedTemplate = [];
            foreach ($page_template as $temp) {
                $formattedTemplate[] = [
                    'id' => $temp->id,
                    'title' => $temp->title,
                    'description' => $temp->description ?? '',
                    'private_note' => $temp->private_note ?? '',
                    'cover_image' => $temp->cover_image,
                    'total_share' => $temp->page_activity->count(),
                    'created_at' => $temp->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $temp->updated_at->format('Y-m-d H:i:s'),
                ];
            }

            $data = [
                'page_template' => $formattedTemplate,
            ];

            return $this->sendSuccessResponse('Page Template Fetch Successfully', $data);
        } else {
            return $this->sendErrorResponse('No Page Template found.', 404);
        }
    }

    public function get_single_templates(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'page_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->first('page_id');

            return $this->sendErrorResponse($errors, 400);
        }

        $user_id = auth('api')->user()->id;

        $seven_days_ago = Carbon::now()->subDays(7);
        $currentDateTime = date('Y-m-d H:i:s');
        $oneWeekAgoDateTime = date('Y-m-d H:i:s', strtotime('-7 days'));

        $page_details = DB::select(DB::raw('
            SELECT
                COUNT(id) AS Total_Shared,
                SUM(total_views) AS opened,
                COUNT(CASE WHEN total_views = 0 THEN 1 ELSE NULL END) AS unopend,
                SUM(CASE WHEN created_at BETWEEN :seven_days_ago AND :currentDateTime THEN total_views ELSE 0 END) AS viewed_in_last_7_days,
                COUNT(CASE WHEN total_views > 1 THEN 1 ELSE NULL END) AS viewed_multiple_times
            FROM `temp_activities`
            WHERE template_id = :template_id;'), [
            'seven_days_ago' => $seven_days_ago,
            'currentDateTime' => $currentDateTime,
            'template_id' => $request->page_id,
        ]);

        $page = PageTemplate::with('page_activity', 'page_lead_activity.lead')->where('client_id', $user_id)->where('id', $request->page_id)->latest()->first();
        if ($page) {
            $formattedTemplate = [];
            $sharingHistory = [];
            $highPotentialClient = [];
            $formattedTemplate[] = [
                'id' => $page->id,
                'title' => $page->title,
                'cover_image' => $page->cover_image,
                'total_share' => $page->page_activity->count(),
                'created_at' => $page->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $page->updated_at->format('Y-m-d H:i:s'),
            ];

            $sharingHistory[] = [
                'total_share' => $page_details[0]->Total_Shared,
                'opened' => $page_details[0]->opened,
                'unopened' => $page_details[0]->unopend,
            ];

            $highPotentialClient[] = [
                'viewed_in_last_7_days' => $page_details[0]->viewed_in_last_7_days,
                'viewed_multiple_times' => $page_details[0]->viewed_multiple_times,
            ];

            $pageLeadDetail = [];

            foreach ($page->page_activity as $lead) {
                $pageLeadDetail[] = [
                    'title' => $lead->lead_client->name,
                    'phone_number' => $lead->lead_client->mobile_number,
                    'email' => $lead->lead_client->email,
                ];
            }

            $data = [
                'page_template' => $formattedTemplate,
                'shareing_history' => $sharingHistory,
                'high_potential_client' => $highPotentialClient,
                'page_lead_detail' => $pageLeadDetail,

            ];

            return $this->sendSuccessResponse('Page Template Fetch Successfully', $data);
        }

        ActivityLogHelper::save_activity(auth('api')->id(), 'View Single Page Template Detail', 'PageTemplate', 'app');

        if ($page) {
            return $this->sendSuccessResponse('Page Template fetch Successfully', $page);
        } else {
            return $this->sendErrorResponse('Page Template Not Found With The Given ID.', 404);
        }
    }

    public function get_page_preview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->first('page_id');

            return $this->sendErrorResponse($errors, 400);
        }
        $user_id = auth('api')->user()->id;
        $client_leads = PageTemplate::with('galleries', 'page_website_links', 'page_youtube_links', 'page_activity', 'user')->where('client_id', $user_id)->where('id', $request->page_id)->latest()->first();
        ActivityLogHelper::save_activity(auth('api')->id(), 'View Page Preview', 'PageTemplate', 'app');
        if ($client_leads) {
            return $this->sendSuccessResponse('Page preview fetch Successfully', $client_leads);
        } else {
            return $this->sendErrorResponse('Page preview Not Found With The Given ID.', 404);
        }
    }

    public function send_template(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required',
            'page_id' => 'required',
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

            $lead_client = LeadClient::find($request->client_id);

            if (isset($lead_client) && !empty($lead_client->name)) {

                $page_template = PageTemplate::find($request->page_id);

                if (isset($page_template) && !empty($page_template)) {
                    $page_activity = new TempActivity;

                    $page_activity->template_id = $request->page_id;
                    $page_activity->client_id = $request->client_id;
                    $page_activity->template_type = 'page';
                    $page_activity->activity_route = 'api';
                    $page_activity->save();

                    $page_lead_activity = new LeadActivity;

                    $page_lead_activity->lead_client_id = $request->client_id;
                    $page_lead_activity->title = $page_template->title;
                    $page_lead_activity->date_time = now()->format('Y-m-d h:i');
                    $page_lead_activity->type = 'message';
                    $page_lead_activity->page_id = $request->page_id;
                    $page_lead_activity->activity_url = route('client.page_view', [hashids_encode($request->page_id), hashids_encode($request->client_id)]);
                    $page_lead_activity->activity_route = 'api';
                    $page_lead_activity->user_type = 'user';
                    $page_lead_activity->added_by_id = auth('api')->user()->id;
                    $page_lead_activity->save();

                    ActivityLogHelper::save_activity(auth('api')->id(), 'Send Message To Lead Client', 'PageTemplate', 'app');
                    // Commit the transaction
                    DB::commit();

                    $msg = 'Page Template Send Successfully';

                    return $this->sendSuccessResponse($msg, $page_lead_activity);
                } else {
                    $errorMsg = 'PageTemplate Template Not Found With Given ID.';
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

    public function delete_temp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->first('page_id');

            return $this->sendErrorResponse($errors, 400);
        }
        $page_temp = PageTemplate::find($request->page_id);
        if ($page_temp) {

            $pageWebsiteLink = PageWebsiteLink::where('page_id', $page_temp->id)->delete();
            $PageYoutubeLink = PageYoutubeLink::where('page_id', $page_temp->id)->delete();
            $PageTemplateGallery = PageTemplateGallery::where('page_id', $page_temp->id)->delete();

            $page_temp->delete();

            ActivityLogHelper::save_activity(auth('api')->id(), 'Delete Page Template', 'PageTemplate', 'app');

            return $this->sendSuccessResponse('Page Template Deleted Successfully');
        } else {
            return $this->sendErrorResponse('Page Template Not Found With The Given ID.', 404);
        }
    }
}
