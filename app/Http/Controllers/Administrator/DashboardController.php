<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdsInvoice;
use App\Models\LeadClient;
use App\Models\User;
use App\Models\SubAccount;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Notification;
use App\Models\Transections;
use App\Models\UserDeviceToken;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;


class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request){
        $data = array(
            'breadcrumb' => 'Dashboard',
            'title' => "Dashboard",
            'sub_account' => SubAccount::get()
        );
        return view('admin.dashboard')->with($data);
    }

    public function save_sub_account(Request $request)
    {
        $rules = [
            'sub_account_url' =>  'required|url|unique:sub_accounts,sub_account_url',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $sub_account_save = new SubAccount;

        if(isset($request->sub_account_name) && !empty($request->sub_account_name)){

            $sub_account_save->sub_account_name = $request->sub_account_name;

        }else{

            $parsed_url = parse_url($request->sub_account_url);
            $host_parts = explode('.', $parsed_url['host']);
            if (count($host_parts) >= 2) {
                $sub_account_name = count($host_parts) > 2 ? $host_parts[count($host_parts) - 2] : $host_parts[0];
            }else{
                $sub_account_name = $parsed_url['host'];
            }

            $sub_account_save->sub_account_name = $sub_account_name;
        }

        $sub_account_save->sub_account_url = $request->sub_account_url;
        $sub_account_save->save();

        return response()->json([
            'success' => 'Sub Account URL Add Successfully',
            'reload' => true,
        ]);
    }

    public function update_profile(Request $request)
    {
        $rules = [
            'name' => 'string|max:50',
            'email' => 'email',
        ];

        if ($request->hasFile('product_image')) {
            $rules['product_image'] = 'image|mimes:jpeg,png,jpg';
        }


        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }
        $user = Admin::find($request->id);
        $user->name = $request->name;
        if ($request->hasFile('profile_image')) {
            $profile_img = uploadSingleFile($request->file('profile_image'), 'uploads/profile_images/', 'png,jpeg,jpg');
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

        return response()->json([
            'success' => 'Profile Updated Successfully',
            'reload' => true,
        ]);
    }

    public function edit_profile()
    {
        $data = array(
            'breadcrumb_main' => 'User Profile',
            'breadcrumb' => 'User Profile',
            'title' => 'Profile',
            'edit' => Auth('admin')->user(),
        );

        return view('admin.profile')->with($data);
    }

    public function save_device_token(Request $request) {

        $rules = [
            'device_token' => 'required',
        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        UserDeviceToken::firstOrCreate(
            [
                'user_id' => auth('admin')->user()->id,
                'user_type' => 'admin',
                'device_token'=>$request->device_token
            ]
        );

        return response()->json(true);
    }

    public function notifications() {
        $notifications = Notification::where('user_id',auth('admin')->id())->where('user_type', 'admin')->latest()->limit(100);
        $unread = Notification::where('user_id',auth('admin')->id())->where('user_type', 'admin')->where('is_read',0)->count();
        return response()->json([
            'count' => $unread,
            'view_data' => view('components.include.admin_notification_list',['notifications'=>$notifications->get()])->render()
        ]);
    }

    public function update_notifications() {
        Notification::where('user_id',auth('admin')->id())->where('user_type', 'admin')->update(['is_read'=>1]);
        return response()->json(true);
    }

    public function update_sub_account_status($id, $status)
    {
        $sub = SubAccount::findOrFail($id);

        $sub->status = $status;
        $sub->save();

        return response()->json([
            'status' => $sub->status,
            'success' => 'The status has been updated successfully.',
            'reload' => true
        ]);
    }

    public function subAccountShow(Request $request){
        $data = array(
            'breadcrumb' => 'Sub Account',
            'title' => "Sub_Account",
        );
        return view('admin.sub_account')->with($data);
    }





    public function view_progress($id, $subAccountId)
    {   
        // dd(hashids_decode($id), hashids_decode($subAccountId));
        $data = array(
            'breadcrumb_main' => 'Ads Requests',
            'breadcrumb' => 'All Ads Requests',
            'title' => 'All Ads Requests',
        );

        return view('admin.running_ads.view_progress', compact('id', 'subAccountId'));
    }


    function view_progress_datatable(Request $request, $id, $subAccountId)
    {
        $query = LeadClient::where('client_id', $subAccountId)->where('ads_id', $id)->get();

        if ($request->ajax()) {
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('name', function ($data) {
                    return $data->name;
                })
                ->addColumn('email', function ($data) {
                    return $data->email;
                })
                ->addColumn('mobile_number', function ($data) {
                    return $data->mobile_number;
                })
                ->addColumn('user_type', function ($data) {
                    return $data->user_type;
                })
                // ->addColumn('action', function ($data) {
                //     return view('admin.client_management.include.ads_action_td', ['data' => $data]);
                // })
                ->filter(function ($query) {
                    if (request()->input('search')) {
                        $query->where(function ($search_query) {
                            $search_query->whereLike(['adds_title', 'type'], request()->input('search'));
                        });
                    }
                })

                ->make(true);
        }
    }






}
