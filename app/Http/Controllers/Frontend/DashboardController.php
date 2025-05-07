<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\LeadClient;
use App\Models\User;
use App\Models\Ads;
use App\Models\Industry;
use Illuminate\Http\Request;
use App\Models\DailyAdsSpent;
use App\Models\Notification;
use App\Models\UserDeviceToken;
use App\Models\Transections;
use App\Models\AdsInvoice;
use App\Models\ClientTour;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\TaxCharge;
use App\Models\WalletTopUp;
use App\Models\ClientWallet;
use App\Models\Tour;
use App\Traits\AdsSpentTrait;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    use AdsSpentTrait;

    public function __construct()
    {
        $this->middleware('auth:web');
    }

    public function index()
    {
        $auth_id = auth('web')->id();
        $check_user_ads = $this->check_user_ads($auth_id);
        $sub_account = Ads::where('client_id', $auth_id)->latest()->get();

        $wallet = ClientWallet::where('client_id', $auth_id)->where('status','completed');
        $last_transaction_date = $wallet->orderBy('created_at', 'desc')->value('updated_at');
        $main_wallet_bls = ($wallet->sum('amount_in') - $wallet->sum('amount_out'));

        // $tour = Tour::firstOrCreate(['code' => 'DASHBOARD'], ['name' => 'Dashboard']);
        // $client_tour = ClientTour::where(['client_id' => auth('web')->user()->id, 'tour_id' => $tour->id])->first();

        $data = array(
            'breadcrumb_main' => 'Dashboard',
            'breadcrumb' => 'Dashboard',
            'title' => 'Dashboard',
            'main_wallet_bls' => $main_wallet_bls,
            'sub_accounts' => Ads::where('client_id', $auth_id)->latest()->get(),
            'remaining_balance' => $remaining_amount ?? '',
            'total_ppc_leads' => $this_week_client_leads ?? '',
            'this_weak_payment' => $this_weak_payment ?? '',
            'dates_text' => "From:",
            'last_transaction_date' => $last_transaction_date,
            'tour' => $tour ?? null,
            'client_tour' => $client_tour ?? null,
        );
        return view('client.dashboard')->with($data);
    }

    public function get_leads(Request $request){
        $auth_id = auth('web')->id();
        // for enum
        $enum_type = DB::select(DB::raw("SHOW COLUMNS FROM lead_clients WHERE Field = 'admin_status'"))[0]->Type;
        $enum_values = str_replace(['enum(', ')', "'"], '', $enum_type);
        $enum_values = explode(',', $enum_values);
        // for enum

        if(empty($request->ads_id)){
            $client_leads = LeadClient::with('lead_data','clients','ads')->where('client_id', $auth_id)->where('lead_type', 'ppc')->latest()->take(100)->get();
        }else{
            $client_leads = LeadClient::with('lead_data','clients','ads')->where('client_id', $auth_id)->where('ads_id',hashids_decode($request->ads_id))->where('lead_type', 'ppc')->latest()->take(100)->get();
        }


        return DataTables::of($client_leads)
                ->addIndexColumn()
                ->addColumn('client_name', function ($lead) {
                    return $lead->clients->client_name ?? '-';
                })
                ->addColumn('ads_name', function ($lead) {
                    return $lead->ads->adds_title ?? '-';
                })
                ->addColumn('name', function ($lead) {
                    return $lead->name ?? '-';
                })
                ->addColumn('email', function ($lead) {
                    return $lead->email ?? '-';
                })
                ->addColumn('mobile_number', function ($lead) {
                    return $lead->mobile_number ?? '-';
                })
                ->addColumn('lead_data', function ($lead) {
                    return $lead->lead_data->map(function ($item) {
                        $key = Str::limit($item->key, 10);
                        $value = Str::limit($item->value, 10);
                        return "<span>$key: $value</span>";
                    })->implode('<br>');

                })

                ->addColumn('actions', function ($lead) {
                    $data = json_encode([
                                'name' => $lead->name,
                                'email' => $lead->email,
                                'mobile_number' => $lead->mobile_number,
                                'admin_status' => $lead->admin_status,
                                'lead_data' => $lead->lead_data
                            ]);
                    $actionsHtml = "<a href='javascript:void(0)' class='text-primary view_lead_detail_id'  data-data='{$data}' data-id='{$lead->id}' title='View Lead Detail'><i class='bi bi-eye-fill'></i></a>
                    ";

                    if ($lead->user_status == 'agent') {
                        $agent_detail = AgentDetail::where('registration_no',$lead->registration_no)->first();
                        $agentdata = json_encode([
                            'salesperson_name' => $agent_detail->salesperson_name ?? '-',
                            'registration_no' => $agent_detail->registration_no ?? '-',
                            'registration_start_date' => $agent_detail->registration_start_date ?? '-',
                            'registration_end_date' => $agent_detail->registration_end_date ?? '-',
                            'estate_agent_name' => $agent_detail->estate_agent_name ?? '-',
                            'estate_agent_license_no' => $agent_detail->estate_agent_license_no ?? '-'
                        ]);
                        $actionsHtml .= "&nbsp;&nbsp <a href='javascript:void(0)' class='text-success agent_specific_action' data-data='{$agentdata}' data-registration='{$lead->registration_no}' data-id='{$lead->id}' title='Agent Specific Action'><i class='bi bi-info-circle-fill'></i></a>";
                    }

                    return $actionsHtml;
                })


              ->addColumn('admin_status', function ($lead) use ($enum_values) {
                $dropdown = '<select class="admin-status-dropdown form-select" name="admin_status" data-id="' . $lead->id . '">';
                foreach ($enum_values as $value) {
                    $selected = $lead->admin_status == $value ? 'selected' : '';
                    $dropdown .= "<option value='{$value}' {$selected}>{$value}</option>";
                }
                $dropdown .= '</select>';
                return $dropdown;
            })
                ->filter(function ($query) {
                    if (request()->input('search')) {
                        $query->where(function ($search_query) {
                            $search_query->whereLike(['status','topup_amount'], request()->input('search'));
                        });
                    }
                })
                ->rawColumns(['actions','lead_data','admin_status'])
            ->make(true);
    }

    public function edit_profile()
    {
        $data = array(
            'breadcrumb' => 'Profile',
            'title' => 'Profile',
            'edit' => auth('web')->user(),
            'industries' => Industry::latest()->get(),
            'agencies' => Agency::where('status',1)->where('added_by_id','!=',0)->latest()->get(),
            'user_agencies' => Agency::where('id',auth('web')->user()->agency_id)->first(),
        );
        return view('client.profile')->with($data);
    }

    public function update_profile(Request $request)
    {
        $rules = [
            'client_name' => 'required|string|max:50',
            // 'agency' => 'required|string|unique:users,agency,'.$request->id,
            'agency' => 'required',
            'package' => 'required',
            'address' => 'required',
            'phone_number' => 'required|unique:users,phone_number,'.$request->id,
            'industry' => 'required',
        ];

        if (isset($request->image) && !empty($request->image)) {
           $rules['image'] = 'image|mimes:png,jpeg,jpg,max:2048';
        }

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        try {
            // Start a database transaction
            DB::beginTransaction();

            $user = User::find($request->id);

            $clickup_folder_id = $user->clickup_folder_id;

            // $folder_new_name = $request->agency.'-'.$request->client_name;
            // $folder_new_name = $user->client_name.'-'.$user->id;

            // $get_local_folder = ClientFolder::where('client_id', auth('web')->id())->where('parent_folder_id', null)->latest()->first();

            // if (!empty($get_local_folder)) {
            //     $get_local_folder->folder_name = $folder_new_name;
            //     $get_local_folder->save();
            // }

            $user->client_name = $request->client_name;
            $user->phone_number = $request->phone_number;
            $user->agency_id = $request->agency;
            $user->package = $request->package;
            $user->address = $request->address;
            $user->industry_id = $request->industry;
            if ($request->hasFile('profile_image')) {
                $profile_img = uploadSingleFile($request->file('profile_image'), 'uploads/client/profile_images/', 'png,jpeg,jpg');
                if (is_array($profile_img)) {
                    return response()->json($profile_img);
                }
                if (file_exists($user->image)) {
                    @unlink($user->image);
                }
                $user->image = $profile_img;
            }

            if (!empty($request->password)) {
                $user->password = bcrypt($request->password);
            }

            $user->save();

            $msg = [
                'success' => 'Profile Updated Successfully',
                'reload' => true,
            ];
            DB::commit();
            return response()->json($msg);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Update Profile: ' . $e->getMessage()], 500);
        }
    }

    public function update_password(Request $request){
        $rules = [
            'current_password' => 'required',
            'password' => 'required|min:8',
        ];

        $messages = [
            'password.regex' => 'Invalid Format. Password should be 8 characters, with at least 1 number and special characters.',
        ];

        $validator = Validator::make($request->all(),$rules, $messages);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $user = User::hashidFind($request->user_id);
        if (!Hash::check($request->current_password, $user->password)) {
            $msg = [
                'error' => 'Current Password Not Match',
                // 'reload' => true,
            ];
        }else{
            $user->password = Hash::make($request->password);
            $user->save();
            $msg = [
                'success' => 'User password has been updated',
                'reload' => true,
            ];
        }
        return response()->json($msg);
    }

    public function save_device_token(Request $request) {

        $rules = [
            'device_token' => 'required',
        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        UserDeviceToken::firstOrCreate(['user_id'=>auth('web')->user()->id,'device_token'=>$request->device_token]);

        return response()->json(true);
    }

    public function notifications() {
        $notifications = Notification::where('user_id',auth('web'))->id()->where('user_type', 'user')->latest()->limit(100);
        $unread = Notification::where('user_id',auth('web')->id())->where('user_type', 'user')->where('is_read',0)->count();
        return response()->json([
            'count' => $unread,
            'view_data' => view('components.include.notification_list',['notifications'=>$notifications->get()])->render()
        ]);
    }

    public function update_notifications() {
        Notification::where('user_id',auth('web')->id())->where('user_type', 'user')->update(['is_read'=>1]);
        return response()->json(true);
    }
}
