<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\LeadActivity;
use App\Models\LeadClient;
use App\Models\LeadGroup;
use App\Traits\ApiResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * @group Lead
 *
 * @subgroup Group
 *
 * @authenticated
 */
class GroupController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get Groups
     */
    public function getGroups()
    {
        $groups = Group::with('group_leads')
            ->where('client_id', auth('api')->id())
            ->orderBy('group_title')
            ->get([
                'id', 'group_title', 'background_color',
            ]);

        if ($groups->isEmpty()) {
            return $this->sendErrorResponse('No Groups found.', 404);
        }

        $count_uncontacted_leads = LeadClient::where('client_id', auth('api')->id())->where('status', 'uncontacted')->count();
        $by_lead_source = LeadClient::where('client_id', auth('api')->id())->count();
        $get_lead_ids = LeadClient::where('client_id', auth('api')->id())->pluck('id');
        $seven_days_ago = Carbon::now()->subDays(7);
        $recently_viewed_content = LeadActivity::whereIn('lead_client_id', $get_lead_ids)->where('last_open', '>=', $seven_days_ago)->count();

        $groupsWithLeadCount = $groups->map(function ($group) {
            $group->lead_count = $group->group_leads->count();
            unset($group->group_leads);

            return $group;
        });

        ActivityLogHelper::save_activity(auth('api')->id(), 'View All Groups', 'Group', 'app');

        $data = [
            'groups' => $groupsWithLeadCount,
            'uncontacted_leads' => $count_uncontacted_leads,
            'by_lead_source' => $by_lead_source,
            'recently_viewed_content' => $recently_viewed_content,
        ];

        return $this->sendSuccessResponse('Groups Fetch Successfully', $data);
    }

    /**
     * Create Group
     */
    public function createGroup(Request $request)
    {
        $client_id = auth('api')->id();

        if (isset($request->group_id) && !empty($request->group_id)) {
            $rules = [
                'group_title' => 'required|unique:groups,group_title,'.$request->group_id.',id,client_id,'.$client_id.',deleted_at,NULL',
            ];
        } else {
            $rules = [
                'group_title' => 'required|unique:groups,group_title,NULL,id,client_id,'.$client_id.',deleted_at,NULL',
            ];
        }

        $rules['background_color'] = 'required|in:primary,secondary,success,info,warning,delete';

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendErrorResponse($validator->errors()->first(), 400);
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            $group = new Group;
            if (isset($request->group_id) && !empty($request->group_id)) {
                $group = $group->findorfail($request->group_id);
                $msg = 'Group Updated Successfully';
                ActivityLogHelper::save_activity(auth('api')->id(), 'Update Group', 'Group', 'app');
            } else {
                $group->added_by_id = auth('api')->id();
                $msg = 'Group Added Successfully';
                ActivityLogHelper::save_activity(auth('api')->id(), 'Add Group', 'Group', 'app');
            }

            $group->client_id = $client_id;
            $group->group_title = $request->group_title;
            $group->background_color = $request->background_color;
            $group->save();

            // Commit the transaction
            DB::commit();

            $data = [
                'group' => [
                    'id' => $group->id,
                    'group_title' => $group->group_title,
                    'background_color' => $group->background_color,
                ],
            ];

            return $this->sendSuccessResponse($msg, $data);
        } catch (\Exception $e) {
            // Something went wrong, rollback the transaction
            DB::rollback();

            // Log or handle the exception as needed
            return $this->sendErrorResponse('Error Group: '.$e->getMessage());
        }
    }

    public function get_source()
    {

        $manual_leads = LeadClient::where('client_id', auth('api')->id())->where('lead_type', 'manual')->count();
        $ppc_leads = LeadClient::where('client_id', auth('api')->id())->where('lead_type', 'ppc')->count();
        $zapier_leads = LeadClient::where('client_id', auth('api')->id())->where('lead_type', 'zapier')->count();

        ActivityLogHelper::save_activity(auth('api')->id(), 'Get Lead Count By source', 'LeadClient', 'app');

        $data = [
            'manual' => $manual_leads,
            'zapier' => $zapier_leads,
            'ppc' => $ppc_leads,
        ];

        return $this->sendSuccessResponse('Lead Sources Fetch Successfully', $data);
    }

    public function get_uncontacted_leads()
    {

        $user_id = auth('api')->user()->id;

        $client_leads = LeadClient::with([
            'activity' => function ($query) {
                $query->orderBy('id', 'desc');
            },
            'lead_data',
            'lead_groups',
        ])
            ->where('client_id', $user_id)->where('status', 'uncontacted')
            ->latest()->get();

        ActivityLogHelper::save_activity(auth('api')->id(), 'View Uncontacted Leads', 'LeadClient', 'app');

        if ($client_leads->isNotEmpty()) {
            $formattedFiles = [];

            foreach ($client_leads as $lead) {

                $formattedFiles[] = [
                    'id' => $lead->id,
                    'name' => $lead->name,
                    'lead_type' => $lead->lead_type,
                    'status' => $lead->status,
                    'latest_activity' => [],
                ];

                foreach ($lead->activity as $activity) {

                    if ($activity->type == 'file') {
                        $total_viewed = $activity->total_views;
                        if ($activity->total_views == 0) {
                            $last_open = 'Unopend';
                        } else {
                            $format_date = Carbon::parse($activity->last_open);
                            $last_open = $format_date->diffForHumans();
                        }

                        $latestActivity = [
                            'id' => $activity->id,
                            'title' => $activity->title,
                            'description' => $activity->description,
                            'file_id' => $activity->file_id,
                            'total_views' => $total_viewed,
                            'last_open' => $last_open,
                            'type' => $activity->type,
                        ];
                    } else {

                        $latestActivity = [
                            'id' => $activity->id,
                            'title' => $activity->title,
                            'description' => $activity->description,
                            'date_time' => $activity->date_time,
                            'type' => $activity->type,
                        ];
                    }

                    $formattedFiles[count($formattedFiles) - 1]['latest_activity'][] = $latestActivity;
                }
            }

            $data = [
                'clients_leads' => $formattedFiles,
            ];

            return $this->sendSuccessResponse('Client Uncontacted Leads Fetch Successfully', $data);
        } else {
            return $this->sendErrorResponse('No Uncontacted Leads found.', 404);
        }
    }

    public function get_recently_viewed_content()
    {

        $user_id = auth('api')->user()->id;

        $seven_days_ago = Carbon::now()->subDays(7);

        $client_leads = LeadClient::where('client_id', $user_id)
            ->whereHas('activity', function ($query) use ($seven_days_ago) {
                $query->where('last_open', '>=', $seven_days_ago);
            })
            ->with([
                'activity' => function ($query) use ($seven_days_ago) {
                    $query->where('last_open', '>=', $seven_days_ago)->orderBy('id', 'desc');
                },
                'lead_data',
                'lead_groups',
            ])
            ->latest()
            ->get();

        ActivityLogHelper::save_activity(auth('api')->id(), 'View Recentlty Viewed Content', 'LeadClient', 'app');

        if ($client_leads->isNotEmpty()) {
            $formattedFiles = [];

            foreach ($client_leads as $lead) {

                $formattedFiles[] = [
                    'id' => $lead->id,
                    'name' => $lead->name,
                    'lead_type' => $lead->lead_type,
                    'status' => $lead->status,
                    'latest_activity' => [],
                ];

                foreach ($lead->activity as $activity) {

                    if ($activity->type == 'file') {
                        $total_viewed = $activity->total_views;
                        if ($activity->total_views == 0) {
                            $last_open = 'Unopend';
                        } else {
                            $format_date = Carbon::parse($activity->last_open);
                            $last_open = $format_date->diffForHumans();
                        }

                        $latestActivity = [
                            'id' => $activity->id,
                            'title' => $activity->title,
                            'description' => $activity->description,
                            'file_id' => $activity->file_id,
                            'total_views' => $total_viewed,
                            'last_open' => $last_open,
                            'type' => $activity->type,
                        ];
                    } else {

                        $latestActivity = [
                            'id' => $activity->id,
                            'title' => $activity->title,
                            'description' => $activity->description,
                            'date_time' => $activity->date_time,
                            'type' => $activity->type,
                        ];
                    }

                    $formattedFiles[count($formattedFiles) - 1]['latest_activity'][] = $latestActivity;
                }
            }

            $data = [
                'clients_leads' => $formattedFiles,
            ];

            return $this->sendSuccessResponse('Client Recently viewed content Fetch Successfully', $data);
        } else {
            return $this->sendErrorResponse('No Recently viewed content found.', 404);
        }
    }

    public function get_lead_by_source(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'source_name' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->first('source_name');

            return $this->sendErrorResponse($errors, 400);
        }

        $user_id = auth('api')->user()->id;
        $client_leads = LeadClient::with([
            'activity' => function ($query) {
                $query->orderBy('id', 'desc');
            },
            'lead_data',
            'lead_groups',
        ])
            ->where('client_id', $user_id)->where('lead_type', $request->source_name)
            ->orderBy('lead_clients.id', $request->get('order', 'desc'))
            ->get();

        ActivityLogHelper::save_activity(auth('api')->id(), 'View Lead By Source', 'LeadClient', 'app');

        if ($client_leads->isNotEmpty()) {
            $formattedFiles = [];

            foreach ($client_leads as $lead) {

                $formattedFiles[] = [
                    'id' => $lead->id,
                    'name' => $lead->name,
                    'lead_type' => $lead->lead_type,
                    'status' => $lead->status,
                    'latest_activity' => [],
                ];

                foreach ($lead->activity as $activity) {

                    if ($activity->type == 'file') {
                        $total_viewed = $activity->total_views;
                        if ($activity->total_views == 0) {
                            $last_open = 'Unopend';
                        } else {
                            $format_date = Carbon::parse($activity->last_open);
                            $last_open = $format_date->diffForHumans();
                        }

                        $latestActivity = [
                            'id' => $activity->id,
                            'title' => $activity->title,
                            'description' => $activity->description,
                            'file_id' => $activity->file_id,
                            'total_views' => $total_viewed,
                            'last_open' => $last_open,
                            'type' => $activity->type,
                        ];
                    } else {

                        $latestActivity = [
                            'id' => $activity->id,
                            'title' => $activity->title,
                            'description' => $activity->description,
                            'date_time' => $activity->date_time,
                            'type' => $activity->type,
                        ];
                    }
                    $formattedFiles[count($formattedFiles) - 1]['latest_activity'][] = $latestActivity;
                }
            }

            $data = [
                'clients_leads' => $formattedFiles,
            ];

            return $this->sendSuccessResponse('Client Leads By Source Fetch Successfully', $data);
        } else {
            return $this->sendErrorResponse('No Leads found.', 404);
        }
    }

    public function single(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $errorMessage = implode("\n ", $errors);

            return $this->sendErrorResponse($errorMessage, 400);
        }

        try {
            $group = Group::findOrFail($request->id);
            $get_group_lead_ids = LeadGroup::where('group_id', $request->id)->pluck('lead_id');
            $get_leads_by_ids = LeadClient::whereIn('id', $get_group_lead_ids)->latest()->get();

            ActivityLogHelper::save_activity(auth('api')->id(), 'View Single Group With Leads', 'Group', 'app');

            $data = [
                'group' => [
                    'id' => $group->id,
                    'group_title' => $group->group_title,
                    'background_color' => $group->background_color,
                    'leads' => [],
                ],
            ];

            if ($get_leads_by_ids->isEmpty()) {
                $data['group']['leads'] = 'Groups Leads Are Not Found';
            } else {
                foreach ($get_leads_by_ids as $lead) {
                    $leadData = [
                        'name' => $lead->name,
                        'status' => $lead->status,
                        'id' => $lead->id,
                    ];
                    $data['group']['leads'][] = $leadData;
                }
            }

            return $this->sendSuccessResponse('Group Fetch Successfully', $data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error Group: '.$e->getMessage()], 500);
        }
    }

    public function delete_group(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            $errorMessages = [];
            foreach ($errors->all() as $message) {
                $errorMessages[] = $message;
            }

            return $this->sendErrorResponse(implode("\n ", $errorMessages), 400);
        }

        DB::beginTransaction();

        try {

            $group = Group::findOrfail($request->id);
            $group->delete_by_type = 'user';
            $group->delete_by_id = auth('api')->id();
            $group->save();

            $lead_group = LeadGroup::where('group_id', $request->id)->update([
                'delete_by_type' => 'user',
                'delete_by_id' => auth('api')->id(),
            ]);
            ActivityLogHelper::save_activity(auth('api')->id(), 'Group Delete', 'Group', 'app');
            LeadGroup::where('group_id', $request->id)->delete();

            Group::findOrfail($request->id)->delete();
            DB::commit();

            return $this->sendSuccessResponse('Group Deleted', []);
        } catch (\Exception $e) {
            // Something went wrong, rollback the transaction
            DB::rollback();

            // Log or handle the exception as needed
            return $this->sendErrorResponse('Error Group: '.$e->getMessage());
        }
    }

    public function group_lead_save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lead_id' => 'required',
            'groups' => 'required|array',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            $errorMessages = [];
            foreach ($errors->all() as $message) {
                $errorMessages[] = $message;
            }

            return $this->sendErrorResponse(implode("\n ", $errorMessages), 400);
        }

        $client_id = auth('api')->id();

        $lead_id = $request->lead_id;

        DB::beginTransaction();
        try {
            if (arrayHasEmptyValue($request->groups)) {
                return $this->sendErrorResponse('Some groups item are empty');
            }

            // Check if all group IDs exist in the groups table
            $existingGroups = Group::whereIn('id', $request->groups)
                ->where('client_id', $client_id)
                ->pluck('id')
                ->toArray();
            ActivityLogHelper::save_activity(auth('api')->id(), 'Group Assign To Lead', 'Group', 'app');
            // If any of the provided group IDs do not exist, return an error
            $nonExistingGroups = array_diff($request->groups, $existingGroups);
            if (!empty($nonExistingGroups)) {
                return $this->sendErrorResponse('One or more group IDs do not exist.', 400);
            }

            $check_lead = LeadClient::where('id', $request->lead_id)->where('client_id', $client_id)->count();
            if ($check_lead === 0) {
                return $this->sendErrorResponse('Lead Client are not found with the given ID.', 400);
            }
            LeadGroup::where('lead_id', $lead_id)->update([
                'delete_by_type' => 'user',
                'delete_by_id' => $client_id,
            ]);

            LeadGroup::where('lead_id', $lead_id)->delete();

            if (!empty($request->groups)) {
                foreach ($request->groups as $lead_group) {
                    $client_lead_group = new LeadGroup;
                    $client_lead_group->group_id = $lead_group;
                    $client_lead_group->lead_id = $lead_id;
                    $client_lead_group->added_by_id = $client_id;
                    $client_lead_group->save();
                }

                $msg = 'Groups assigned to Lead';
            } else {
                $msg = 'Groups removed from Lead';
            }

            // Commit the transaction
            DB::commit();

            return $this->sendSuccessResponse($msg);
        } catch (\Exception $e) {
            DB::rollback();

            return $this->sendErrorResponse('Error lead Group: '.$e->getMessage());
        }
    }
}
