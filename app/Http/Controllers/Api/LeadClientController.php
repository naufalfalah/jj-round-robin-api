<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ClientFiles;
use App\Models\LeadActivity;
use App\Models\LeadAssign;
use App\Models\LeadClient;
use App\Models\LeadData;
use App\Models\LeadGroup;
use App\Models\Notification;
use App\Traits\ApiResponseTrait;
use App\Traits\GoogleTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * @group Lead
 *
 * @subgroup Client Lead
 *
 * @authenticated
 */
class LeadClientController extends Controller
{
    use ApiResponseTrait, GoogleTrait;

    /**
     * Get Client Leads
     */
    public function getClientLeads(Request $request)
    {
        $user_id = auth('api')->id();
        $client_leads = LeadClient::with(['activity' => function ($query) {
            $query->orderBy('id', 'desc');
        }, 'lead_data', 'lead_groups'])
            ->where('client_id', $user_id)
            ->orderBy('lead_clients.id', $request->get('order', 'desc'))
            ->when($request->filled('keyword'), function ($query) use ($request, $user_id) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%'.$request->keyword.'%')
                        ->orWhere('email', 'LIKE', '%'.$request->keyword.'%');
                })->where('client_id', $user_id);
            })
            ->when($request->assign, function ($query) use ($request) {
                $request->assign === 'assigned' ? $query->has('assign') : $query->doesntHave('assign');
            })
            ->get();

        ActivityLogHelper::save_activity($user_id, 'View Client Leads', 'LeadClient', 'app');

        if ($client_leads->isEmpty()) {
            return $this->sendErrorResponse('No Leads found.', 404);
        }

        $formattedFiles = $client_leads->map(function ($lead) {
            $latestActivity = $lead->activity->map(function ($activity) {
                return $activity->type === 'file' ? [
                    'id' => $activity->id,
                    'title' => $activity->title,
                    'description' => $activity->description,
                    'file_id' => $activity->file_id,
                    'total_views' => $activity->total_views,
                    'last_open' => $activity->total_views == 0 ? 'Unopened' : Carbon::parse($activity->last_open)->diffForHumans(),
                    'type' => $activity->type,
                ] : [
                    'id' => $activity->id,
                    'title' => $activity->title,
                    'description' => $activity->description,
                    'date_time' => $activity->date_time,
                    'type' => $activity->type,
                ];
            });

            return [
                'id' => $lead->id,
                'name' => $lead->name,
                'status' => $lead->status,
                'latest_activity' => $latestActivity,
            ];
        });

        return $this->sendSuccessResponse('Client Leads Fetch Successfully', ['clients_leads' => $formattedFiles]);
    }

    public function get_client_new_leads(Request $request)
    {
        $user_id = auth('api')->id();
        $client_new_leads = LeadClient::with('activity', 'lead_data', 'lead_groups')
            ->where('client_id', $user_id)
            ->where('status', 'new_lead')
            ->latest()
            ->get();

        ActivityLogHelper::save_activity($user_id, 'View Client New Leads', 'LeadClient', 'app');

        if ($client_new_leads->isEmpty()) {
            return $this->sendErrorResponse('No Leads found.', 404);
        }

        $formattedFiles = $client_new_leads->map(function ($lead) {
            return [
                'id' => $lead->id,
                'name' => $lead->name,
                'email' => $lead->email,
                'mobile_number' => $lead->mobile_number,
                'lead_added_by' => $lead->user_type,
                'addition_field_1' => $lead->addition_field_1,
                'addition_field_2' => $lead->addition_field_2,
                'addition_field_3' => $lead->addition_field_3,
                'note' => $lead->note,
                'status' => $lead->status,
                'follow_up_date_time' => $lead->follow_up_date_time,
                'created_at' => $lead->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $lead->updated_at->format('Y-m-d H:i:s'),
                'activity' => $lead->activity,
                'lead_data' => $lead->lead_data,
                'lead_groups' => $lead->lead_groups,
            ];
        });

        return $this->sendSuccessResponse('Client Leads Fetch Successfully', ['clients_new_leads' => $formattedFiles]);
    }

    public function get_follow_up(Request $request)
    {
        $user_id = auth('api')->user()->id;
        $client_leads_follow_up = LeadClient::with('activity', 'lead_data', 'lead_groups.group')
            ->where('client_id', $user_id)
            ->whereNotNull('follow_up_date_time')
            ->whereDate('follow_up_date_time', '>=', now()->format('Y-m-d'))
            ->latest()
            ->get();

        ActivityLogHelper::save_activity($user_id, 'View Client Follow Up', 'LeadClient', 'app');

        if ($client_leads_follow_up->isEmpty()) {
            return $this->sendErrorResponse('No Follow Up found.', 404);
        }

        $formattedLeads = $client_leads_follow_up->map(function ($lead) {
            $leadGroups = $lead->lead_groups->isEmpty()
                ? 'Leads Groups Are Not Found'
                : $lead->lead_groups->map(function ($group) {
                    return [
                        'group_title' => $group->group->group_title,
                        'background_color' => $group->group->background_color,
                        'group_id' => $group->group_id,
                    ];
                });

            return [
                'id' => $lead->id,
                'name' => $lead->name,
                'follow_up_date_time' => $lead->follow_up_date_time,
                'lead_groups' => $leadGroups,
            ];
        });

        return $this->sendSuccessResponse('Follow Up Fetch Successfully', ['clients_follow_up' => $formattedLeads]);
    }

    public function add_follow_up(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'follow_up_date_time' => 'required|date_format:Y-m-d H:i',
        ]);

        if ($validator->fails()) {
            return $this->sendErrorResponse($validator->errors()->implode("\n"), 400);
        }

        $lead = LeadClient::find($request->id);
        if (!$lead) {
            return $this->sendErrorResponse('Client Not Found With The Given Id.', 401);
        }

        $lead->follow_up_date_time = $request->follow_up_date_time;
        $lead->save();

        ActivityLogHelper::save_activity(auth('api')->id(), 'Add Follow Up Leads', 'LeadClient', 'app');

        return $this->sendSuccessResponse('Follow up added successfully', ['data' => $lead]);
    }

    public function delete_follow_up(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendErrorResponse($validator->errors()->implode("\n"), 400);
        }

        $lead = LeadClient::find($request->id);
        if (!$lead) {
            return $this->sendErrorResponse('Client Not Found With The Given Id.', 401);
        }

        $lead->follow_up_date_time = null;
        $lead->save();

        ActivityLogHelper::save_activity(auth('api')->id(), 'Delete Follow Up', 'LeadClient', 'app');

        return $this->sendSuccessResponse('Follow up unset successfully', ['data' => $lead]);
    }

    public function add_new_client(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'mobile_number' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendErrorResponse($validator->errors()->implode("\n"), 400);
        }

        $user_id = auth('api')->user()->id;
        DB::beginTransaction();

        try {
            $client_lead = LeadClient::create([
                'client_id' => $user_id,
                'name' => $request->name,
                'email' => $request->email,
                'mobile_number' => $request->mobile_number,
                'user_type' => 'user',
                'added_by_id' => $user_id,
            ]);

            ActivityLogHelper::save_activity($user_id, 'Add New Lead Client', 'LeadClient', 'app');

            // $spreadsheet_id = auth('api')->user()->spreadsheet_id;
            // if ($spreadsheet_id) {
            //     $this->saveToGoogleSheet(
            //         $request->name,
            //         $request->email,
            //         $request->mobile_number,
            //         $request->additional_data,
            //         'Zapier',
            //         $spreadsheet_id
            //     );
            // }

            if (!empty($request->additional_data)) {
                $lead_data = array_map(function ($data) use ($client_lead, $user_id) {
                    return [
                        'lead_client_id' => $client_lead->id,
                        'key' => $data['key'],
                        'value' => $data['value'],
                        'user_type' => 'user',
                        'added_by_id' => $user_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }, $request->additional_data);

                LeadData::insert($lead_data);
            }

            send_push_notification('New Lead', 'New Lead Added', $client_lead->client_id, $client_lead->id);

            DB::commit();

            return $this->sendSuccessResponse('Lead Added Successfully', $client_lead);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(['error' => 'Error lead: '.$e->getMessage()], 500);
        }
    }

    public function edit_client(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lead_id' => 'required',
            'name' => 'required',
            'mobile_number' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendErrorResponse($validator->errors()->all(), 400);
        }

        $user_id = auth('api')->user()->id;
        DB::beginTransaction();

        try {
            $client_lead = LeadClient::find($request->lead_id);

            if (!$client_lead) {
                return $this->sendErrorResponse('Lead Not Found With The Given ID.', 404);
            }

            $client_lead->update([
                'client_id' => $user_id,
                'name' => $request->name,
                'email' => $request->email,
                'mobile_number' => $request->mobile_number,
            ]);
            ActivityLogHelper::save_activity($user_id, 'Edit Lead Client', 'LeadClient', 'app');

            if (!empty($request->additional_data)) {
                $client_lead->lead_data()->delete();
                $lead_data = collect($request->additional_data)->map(fn ($val) => [
                    'lead_client_id' => $client_lead->id,
                    'key' => $val['key'],
                    'value' => $val['value'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->toArray();
                LeadData::insert($lead_data);
            }

            DB::commit();

            return $this->sendSuccessResponse('Lead Updated Successfully', $client_lead);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(['error' => 'Error lead: '.$e->getMessage()], 500);
        }
    }

    public function getActivitiesLead(Request $request)
    {
        $id = $request->get('lead_client_id');
        $activities = $id ? LeadActivity::where('lead_client_id', $id)->get() : LeadActivity::all();

        return $this->sendSuccessResponse('Success', $activities, 200);
    }

    public function schedule_an_activity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lead_id' => 'required',
            'title' => 'required',
            'activity_type' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendErrorResponse($validator->errors()->all(), 400);
        }

        $user_id = auth('api')->user()->id;
        DB::beginTransaction();

        try {
            $client_lead = LeadClient::find($request->lead_id);

            if ($client_lead) {
                $lead_activity = LeadActivity::create([
                    'lead_client_id' => $request->lead_id,
                    'title' => $request->title,
                    'description' => $request->description ?? '',
                    'date_time' => $request->date_time ?? '',
                    'type' => $request->activity_type,
                    'activity_route' => 'api',
                    'user_type' => 'user',
                    'added_by_id' => $user_id,
                ]);
                ActivityLogHelper::save_activity($user_id, 'Add Lead Activity', 'LeadClient', 'app');

                if ($client_lead->status === 'uncontacted') {
                    $client_lead->update(['status' => 'contacted']);
                }

                $msg = 'Lead Activity Added Successfully';
            } else {
                $msg = 'No Lead Found With The Given ID';
            }

            DB::commit();

            return $this->sendSuccessResponse($msg, $lead_activity);
        } catch (\Exception $e) {
            DB::rollback();

            return $this->sendErrorResponse($e->getMessage());
        }
    }

    public function quick_response_activity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lead_id' => 'required',
            'file_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendErrorResponse(implode("\n ", $validator->errors()->all()), 400);
        }

        $user_id = auth('api')->user()->id;
        DB::beginTransaction();

        try {
            $client_lead = LeadClient::find($request->lead_id);
            if (!$client_lead) {
                return $this->sendErrorResponse('No Lead Found With The Given ID');
            }

            $client_file = ClientFiles::find($request->file_id);
            if (!$client_file) {
                return $this->sendErrorResponse('No File Found With The Given ID');
            }

            if (!LeadActivity::where('lead_client_id', $request->lead_id)->where('file_id', $request->file_id)->exists()) {
                $lead_activity = LeadActivity::create([
                    'lead_client_id' => $request->lead_id,
                    'title' => $client_file->file_name,
                    'description' => $request->description ?? '',
                    'file_id' => $request->file_id,
                    'type' => 'file',
                    'activity_route' => 'api',
                    'user_type' => 'user',
                    'added_by_id' => $user_id,
                    'activity_url' => route('client.response', LeadActivity::hashid),
                ]);

                if ($client_lead->status == 'uncontacted') {
                    $client_lead->update(['status' => 'contacted']);
                }

                ActivityLogHelper::save_activity($user_id, 'Send File Activity', 'LeadClient', 'app');
                DB::commit();

                return $this->sendSuccessResponse('Quick Response Lead Activity Added Successfully', $lead_activity);
            }

            return $this->sendErrorResponse('File Already Sent To This Client');
        } catch (\Exception $e) {
            DB::rollback();

            return $this->sendErrorResponse('An error occurred');
        }
    }

    public function get_single_lead(Request $request)
    {
        $validator = Validator::make($request->all(), ['lead_id' => 'required']);

        if ($validator->fails()) {
            return $this->sendErrorResponse($validator->errors()->first('lead_id'), 400);
        }

        $user_id = auth('api')->user()->id;
        $client_leads = LeadClient::with('activity.client_file', 'lead_data', 'lead_groups.group')
            ->where('client_id', $user_id)->where('id', $request->lead_id)->latest()->first();

        ActivityLogHelper::save_activity($user_id, 'View Single Lead', 'LeadClient', 'app');

        if ($client_leads) {
            $formattedFiles[] = [
                'id' => $client_leads->id,
                'name' => $client_leads->name,
                'email' => $client_leads->email,
                'mobile_number' => $client_leads->mobile_number,
                'addition_field_1' => $client_leads->addition_field_1,
                'addition_field_2' => $client_leads->addition_field_2,
                'addition_field_3' => $client_leads->addition_field_3,
                'note' => $client_leads->note,
                'status' => $client_leads->status,
                'follow_up_date_time' => $client_leads->follow_up_date_time,
                'is_send_discord' => $client_leads->is_send_discord,
                'is_verified' => $client_leads->is_verified,
                'user_type' => $client_leads->user_type,
                'activity' => collect($client_leads->activity)->map(function ($activity) {
                    return [
                        'id' => $activity->id,
                        'title' => $activity->title,
                        'description' => $activity->description,
                        'file_id' => $activity->file_id ?? null,
                        'total_views' => $activity->total_views ?? null,
                        'last_open' => optional(Carbon::parse($activity->last_open))->diffForHumans(),
                        'type' => $activity->type,
                    ];
                })->toArray(),
                'lead_data' => $client_leads->lead_data->map(fn ($data) => ['key' => $data->key, 'value' => $data->value])->toArray(),
                'lead_groups' => $client_leads->lead_groups->map(fn ($group) => [
                    'group_title' => $group->group->group_title,
                    'background_color' => $group->group->background_color,
                    'group_id' => $group->group_id,
                ])->toArray(),
            ];

            return $this->sendSuccessResponse('Lead fetch Successfully', $formattedFiles);
        }

        return $this->sendErrorResponse('Client Lead Not Found With The Given ID.', 404);
    }

    public function delete_client(Request $request)
    {
        $validator = Validator::make($request->all(), ['lead_id' => 'required']);

        if ($validator->fails()) {
            return $this->sendErrorResponse($validator->errors()->first('lead_id'), 400);
        }

        $client = LeadClient::with('activity', 'lead_data', 'lead_groups')->find($request->lead_id);
        if (!$client) {
            return $this->sendErrorResponse('Lead Not Found With The Given ID.', 404);
        }

        $user_id = auth('api')->id();
        $client->update(['delete_by_type' => 'user', 'delete_by_id' => $user_id]);

        foreach (['activity', 'lead_data', 'lead_groups'] as $relation) {
            $client->$relation()->update(['delete_by_type' => 'user', 'delete_by_id' => $user_id]);
        }

        ActivityLogHelper::save_activity($user_id, 'Delete Lead Client', 'LeadClient', 'app');
        $client->delete();

        return $this->sendSuccessResponse('Lead Deleted Successfully');
    }

    public function upd_lead_status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lead_id' => 'required',
            'status' => ['required', Rule::in(['new_lead', 'spam', 'junk', 'clear', 'unmarked'])],
        ]);

        if ($validator->fails()) {
            return $this->sendErrorResponse(implode("\n ", $validator->errors()->all()), 400);
        }

        $find_lead = LeadClient::find($request->lead_id);
        if (!$find_lead) {
            return $this->sendErrorResponse('Lead Not Found With The Given ID');
        }

        $find_lead->update(['status' => $request->status]);
        ActivityLogHelper::save_activity(auth('api')->id(), 'Update Lead Status', 'LeadClient', 'app');

        return $this->sendSuccessResponse('Lead Status Updated Successfully', $find_lead);
    }

    public function import_clients_lead(Request $request)
    {
        $client_leads_data = $request->json()->all();
        $user_id = auth('api')->user()->id;

        $client_leads = array_map(function ($userData) use ($user_id) {
            $client_lead = LeadClient::create([
                'client_id' => $user_id,
                'name' => $userData['name'],
                'email' => $userData['email'] ?? '',
                'mobile_number' => $userData['mobile_number'] ?? '',
                'user_type' => 'user',
                'added_by_id' => $user_id,
            ]);

            if (!empty($userData['additional_data'])) {
                LeadData::insert(array_map(fn ($val) => [
                    'lead_client_id' => $client_lead->id,
                    'key' => $val['key'],
                    'value' => $val['value'],
                    'added_by_id' => $user_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], $userData['additional_data']));
            }

            return $client_lead;
        }, $client_leads_data);

        ActivityLogHelper::save_activity($user_id, 'Import Lead Client', 'LeadClient', 'app');

        return $this->sendSuccessResponse('Client Leads Import Successfully', $client_leads);
    }

    public function get_teams(Request $request)
    {
        $userId = auth('api')->user()->id;
        $leads = LeadGroup::where('added_by_id', $userId)
            ->whereNull('deleted_at')
            ->where('user_type', 'user')
            ->with('lead');

        $data = [
            'total_my_teams' => $leads->count(),
            'total_unassigned_leads' => LeadClient::whereDoesntHave('lead_groups')->count(),
            'teams_data' => $leads->get()->map(function ($lead) {
                return $lead->lead ? [
                    'id' => $lead->lead->id,
                    'name' => $lead->lead->name,
                    'email' => $lead->lead->email,
                    'total_clients' => LeadGroup::where('lead_id', $lead->lead->id)->count(),
                ] : null;
            })->filter()->values(),
        ];

        ActivityLogHelper::save_activity($userId, 'View Team', 'LeadClient', 'app');

        return $this->sendSuccessResponse('Teams Fetched Successfully', $data);
    }

    public function get_assigned_leads()
    {
        $unassignedLeads = LeadClient::has('assign')->get();

        return $unassignedLeads->isEmpty()
            ? $this->sendErrorResponse('No Assigned Leads Found.', 404)
            : $this->sendSuccessResponse('Assigned Leads Fetched Successfully', $unassignedLeads);
    }

    public function get_unassigned_leads()
    {
        $unassignedLeads = LeadClient::doesntHave('assign')->get();

        return $unassignedLeads->isEmpty()
            ? $this->sendErrorResponse('No Unassigned Leads Found.', 404)
            : $this->sendSuccessResponse('Unassigned Leads Fetched Successfully', $unassignedLeads);
    }

    public function assign_leads(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lead_ids' => 'required|array',
            'lead_ids.*' => 'integer',
        ]);

        if ($validator->fails()) {
            return $this->sendErrorResponse(implode("\n", $validator->errors()->all()), 400);
        }

        $leadIds = $request->lead_ids;
        $newLeadIds = array_diff($leadIds, LeadAssign::whereIn('lead_id', $leadIds)->pluck('lead_id')->toArray());

        $admin = Admin::where('role_name', 'ISA Team')->first();
        foreach ($newLeadIds as $lead_id) {
            LeadAssign::create(['lead_id' => $lead_id]);
            Notification::create([
                'user_id' => $admin->id,
                'title' => 'Lead Assigned',
                'body' => 'New lead assigned to you',
                'user_type' => 'admin',
                'lead_url' => route('admin.isa_clients.all'),
            ]);
        }

        return $this->sendSuccessResponse('Leads Assigned Successfully');
    }

    public function get_leads_activities(Request $request)
    {
        $validator = Validator::make($request->all(), ['lead_id' => 'required']);
        if ($validator->fails()) {
            return $this->sendErrorResponse($validator->errors()->first('lead_id'), 400);
        }

        $lead_activities = LeadActivity::where('lead_client_id', $request->lead_id)
            ->latest('created_at')->get();
        if ($lead_activities->isEmpty()) {
            return $this->sendErrorResponse('Lead Activity Not Found With The Given ID.', 404);
        }

        ActivityLogHelper::save_activity(auth('api')->id(), 'View Single Lead Activities', 'LeadClient', 'app');
        $formattedFiles = [['activity' => $lead_activities->map(function ($activity) {
            return [
                'id' => $activity->id,
                'title' => $activity->title,
                'description' => $activity->description,
                'file_id' => $activity->file_id,
                'total_views' => $activity->total_views,
                'last_open' => $activity->total_views > 0 ? Carbon::parse($activity->last_open)->diffForHumans() : 'Unopened',
                'type' => $activity->type,
                'client_id' => $activity->lead_client_id,
                'date_time' => $activity->date_time,
            ];
        })->all()]];

        return $this->sendSuccessResponse('Lead Activity fetched Successfully', $formattedFiles);
    }

    public function get_client_activities(Request $request)
    {
        $user_id = auth('api')->user()->id;

        if (!$user_id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $leadIds = LeadClient::where('client_id', $user_id)->pluck('id');
        $leadActivities = LeadActivity::whereIn('lead_client_id', $leadIds)
            ->with('lead')
            ->latest('created_at')
            ->get()
            ->groupBy(['lead_client_id', function ($item) {
                return $item->created_at->toDateString();
            }]);

        ActivityLogHelper::save_activity($user_id, 'View All Client Lead Activities', 'LeadClient', 'app');
        $response = $leadActivities->map(function ($dates, $leadClientId) {
            return [
                'lead_client_id' => $leadClientId,
                'date_activities' => $dates->map(function ($activities, $date) {
                    return [
                        'date' => $date,
                        'activities' => $activities->map(function ($activity) {
                            return [
                                'lead_client_id' => $activity->lead_client_id,
                                'client_name' => $activity->lead->name,
                                'title' => $activity->title,
                                'description' => $activity->description,
                                'file_id' => $activity->file_id,
                                'total_views' => $activity->total_views,
                                'last_open' => $activity->total_views > 0 ? Carbon::parse($activity->last_open)->diffForHumans() : 'Unopened',
                                'created_at' => $activity->created_at,
                                'type' => $activity->type,
                            ];
                        }),
                    ];
                })->values(),
            ];
        })->values();

        return response()->json($response);
    }

    public function get_file_info(Request $request)
    {
        $validator = Validator::make($request->all(), ['file_id' => 'required']);
        if ($validator->fails()) {
            return $this->sendErrorResponse($validator->errors()->first('file_id'), 400);
        }

        $data = DB::select(DB::raw('
            SELECT
                COUNT(id) AS Total_Shared,
                SUM(total_views) AS opened,
                COUNT(CASE WHEN total_views = 0 THEN 1 END) AS unopend,
                COUNT(CASE WHEN created_at BETWEEN :seven_days_ago AND :currentDateTime THEN 1 END) AS viewed_in_last_7_days,
                COUNT(CASE WHEN total_views > 1 THEN 1 END) AS viewed_multiple_times
            FROM lead_activities
            WHERE file_id = :file_id
        '), [
            'seven_days_ago' => now()->subDays(7),
            'currentDateTime' => now(),
            'file_id' => $request->file_id,
        ]);

        ActivityLogHelper::save_activity(auth('api')->id(), 'View File Log', 'LeadClient', 'app');

        return !empty($data)
            ? $this->sendSuccessResponse('Data fetched successfully', $data)
            : $this->sendErrorResponse('No data found', 404);
    }
}
