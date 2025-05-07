<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Agency;
use App\Models\Industry;
use App\Models\Transections;
use App\Models\WalletTopUp;
use App\Models\Ads;
use App\Models\GoogleAccount;
use App\Services\GoogleAdsService;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\Traits\GoogleTrait;

class ClientManagmentController extends Controller
{
    use GoogleTrait;

    public function index()
    {
        if (Auth::user('admin')->can('user-add') == true) {
            $data = [
                'breadcrumb_main' => 'Clients Management',
                'breadcrumb' => 'All Clients',
                'title' => 'All Clients',
                'all_users' => User::with('Agencices', 'Industries')->where('sub_account_id', hashids_decode(session()->get('sub_account_id')))->latest()->get(),
                'sub_account_id' => session()->get('sub_account_id'),
            ];
            return view('admin.client_management.clients.all_clients')->with($data);
        } else {
            return response()->json([
                'error' => 'You do not have permission to access this resource.'
            ], 403);
        }
    }

    public function all_clients(Request $request)
    {
        if ($request->ajax()) {
            if (isset($request->user_id)) {
                $url = route('admin.sub_account.client-management.clone_client', ['sub_account_id' => session()->get('sub_account_id'), 'id' => $request->user_id]);
                return response()->json(['redirect_url' => $url]);
            }
        }
        
        $data = [
            'breadcrumb_main' => 'Clients Management',
            'breadcrumb' => 'Import Clients',
            'title' => 'Import Clients',
            'agencies' => Agency::all(),
            'industries' => Industry::all(),
            'users' => User::where('sub_account_id', '!=', hashids_decode(session()->get('sub_account_id')))->with('user_agency')->latest()->get(),
            'sub_account_id' => session()->get('sub_account_id'),
        ];

        return view('admin.client_management.clients.import_clients', $data);
    }

    public function add()
    {
        if (Auth::user('admin')->can('user-add') == true) {
            $customers = [];
            if (Auth::user('admin')->google_access_token) {
                $googleAdsService = new GoogleAdsService();
                $getCustomers = $googleAdsService->getCustomers();
                foreach ($getCustomers['resourceNames'] as $customer) {
                    $customerId = removePrefix($customer);
                    $getCustomerClients = $googleAdsService->getCustomerClients($customerId);
                    
                    foreach ($getCustomerClients['results'] as $customerClient) {
                        $customers[] = $customerClient;
                    }
                }
            }
            
            $data = [
                'breadcrumb_main' => 'Clients Management',
                'breadcrumb' => 'Add Client',
                'title' => 'Add Client',
                'agencies' => Agency::all(),
                'industries' => Industry::all(),
                'sub_account_id' => session()->get('sub_account_id'),
                'customers' => $customers,
            ];
            return view('admin.client_management.clients.add_client')->with($data);
        } else {
            return response()->json([
                'error' => 'You do not have permission to access this resource.'
            ], 403);
        }
    }

    public function get_agency_address(Request $request)
    {
        if ($request->ajax()) {
            $agencyId = $request->agency_id;

            if ($agencyId) {
                $agency = Agency::find($agencyId);

                if ($agency && $agency->address) {
                    return response()->json(['success' => true, 'address' => $agency->address]);
                }
            }
        }

        return response()->json(['error' => 'Agency address not found'], 404);
    }

    public function save(Request $request)
    {
        if (!Auth::user()->can('user-add') && !Auth::user()->can('user-update')) {
            return response()->json([
                'error' => 'Unauthorized action.',
            ], 403);
        }

        $userId = $request->id ? hashids_decode($request->id) : null;

        $rules = [
            'client_name' => 'required|string',
            'phone_number' => 'required|numeric',
            'email' => 'required|email|unique:users,email,' . ($userId ?? 'NULL') . ',id,deleted_at,NULL',
            'agency_id' => 'required|integer',
            'package' => 'required|string',
            'address' => 'required|string',
            'industry_id' => 'required|integer',
            'profile_image' => 'nullable|image|mimes:jpeg,jpg,png',
            'password' => 'nullable|min:8',
            'confirm_password' => 'nullable|same:password',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        if ($userId) {
            $user = User::findOrFail($userId);
            $message = 'User updated successfully';
        } else {
            $user = new User();
            $message = 'User created successfully';
        }

        $googleAccountId = (int) $request->input('google_account_id');
        
        $user->sub_account_id = hashids_decode(session()->get('sub_account_id'));

        $user->client_name = $request->client_name;
        $user->phone_number = $request->phone_number;
        $user->email = $request->email;
        $user->agency_id = $request->agency_id;
        $user->package = $request->package;
        $user->address = $request->address;
        $user->industry_id = $request->industry_id;
        $user->google_account_id = $googleAccountId;
        $user->customer_id = $request->customer_id;
        $user->email_verified_at = now();

        if (empty($userId)) {
            if ($request->hasFile('image')) {
                $profile_img = uploadSingleFile($request->file('image'), 'uploads/profile_images/', 'png,jpeg,jpg');
                if (is_array($profile_img)) {
                    return response()->json($profile_img);
                }
                $user->image = $profile_img;
            } else {
                if (!isset($request->client_image)) {
                    return response()->json(['error' => 'Profile image is required for new user'], 400);
                }
            }
        } else {
            if ($request->hasFile('image')) {
                if (file_exists($user->image)) {
                    @unlink($user->image);
                }
                $profile_img = uploadSingleFile($request->file('image'), 'uploads/profile_images/', 'png,jpeg,jpg');
                if (is_array($profile_img)) {
                    return response()->json($profile_img);
                }
                $user->image = $profile_img;
            }
        }

        if (isset($request->client_image) && !$request->hasFile('image')) {
            $user->image = $request->client_image;
        }
        
        if (!Hash::needsRehash($request->password)) {
            $user->password = $request->password;
        } else {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        $check_admin_account = Admin::where('user_type', 'admin')
            ->where('role_name', 'super_admin')
            ->whereNotNull('google_access_token')
            ->count();
        if ($check_admin_account > 0) {
            $create_client_sheet = $this->createNewSpreadsheet($request->client_name, $user->id);
            $user->spreadsheet_id = $create_client_sheet;
            $user->save();
        }

        return response()->json([
            'success' => $message,
            'reload' => true,
        ]);
    }

    public function clone_client($sub_account_id, $id)
    {
        
        $data = [
            'breadcrumb_main' => 'Clients Management',
            'breadcrumb' => 'Add Client',
            'title' => 'Add Client',
            'agencies' => Agency::all(),
            'industries' => Industry::all(),
            'clone_client' => User::hashidFind($id),
            'last_inserted_id' => User::latest()->value('id'),
            'users' => User::where('sub_account_id', '!=', hashids_decode(session()->get('sub_account_id')))->with('user_agency')->latest()->get(),
            'sub_account_id' => session()->get('sub_account_id'),
        ];
        
        return view('admin.client_management.clients.import_clients', $data);
    }

    public function edit($sub_account_id)
    {
        if (Auth::user('admin')->can('user-update') != true) {
            abort(403, 'Unauthorized action.');
        }
        
        $customers = [];
        if (Auth::user('admin')->google_access_token) {
            $googleAdsService = new GoogleAdsService();
            $getCustomers = $googleAdsService->getCustomers();
            foreach ($getCustomers['resourceNames'] as $customer) {
                $customerId = removePrefix($customer);
                $getCustomerClients = $googleAdsService->getCustomerClients($customerId);
                
                if (!isset($getCustomerClients['results'])) {
                    continue;
                }
                
                foreach ($getCustomerClients['results'] as $customerClient) {
                    $customers[] = $customerClient;
                }
            }
        }

        $subAccountId = hashids_decode($sub_account_id);
        $client = User::where('sub_account_id', $subAccountId)->first();
        
        $data = [
            'breadcrumb_main' => 'Clients Management',
            'breadcrumb' => 'Edit Client',
            'title' => 'Edit Client',
            'agencies' => Agency::all(),
            'industries' => Industry::all(),
            'edit' => $client,
            'sub_account_id' => $sub_account_id,
            'customers' => $customers,
            'google_accounts' => GoogleAccount::all(),
        ];

        return view('admin.client_management.clients.add_client', $data);
    }

    public function delete($sub_account_id, $id)
    {
        $client = User::findOrFail(hashids_decode($id))->delete();

        return response()->json([
            'success' => 'Client deleted successfully',
            'remove_tr' => true
        ]);
    }

    public function update_password(Request $request)
    {
        if (!Auth::user()->can('user-add') && !Auth::user()->can('user-update')) {
            return response()->json([
                'error' => 'Unauthorized action.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6|max:12|confirmed',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $user = User::hashidFind($request->client_id);
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => 'User password has been updated',
            'reload' => true,
        ]);
    }

    public function top_Up(Request $request)
    {
        if (Auth::user('admin')->can('topup-add') == true) {

            $sub_account_ids = User::where('sub_account_id', hashids_decode(session()->get('sub_account_id')))->pluck('id');

            $data = [
                'breadcrumb_main' => 'Clients Management',
                'breadcrumb' => 'All Top Up',
                'title' => 'All TopUps',
                'all_users' => User::where('sub_account_id', hashids_decode(session()->get('sub_account_id')))->get(),
                'all_wallet_topup' => WalletTopUp::with('clients')->whereIn('client_id', $sub_account_ids)->latest()->get(),
                'sub_account_id' => session()->get('sub_account_id'),
            ];
            return view('admin.client_management.TopUp.all_topup')->with($data);
        } else {
            return response()->json([
                'error' => 'You do not have permission to access this resource.'
            ], 403);
        }
    }

    public function topup_save(Request $request)
    {
        if (!Auth::user()->can('topup-add') && !Auth::user()->can('topup-update')) {
            return response()->json([
                'error' => 'Unauthorized action.',
            ], 403);
        }

        $rules = [
            'client_id' => 'required|numeric',
            'topup_amount' => 'required|numeric',
            'proof' => 'nullable|array',
            'proof.*' => 'image|mimes:jpeg,jpg,png',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $topup = empty($request->id) ? new WalletTopUp() : WalletTopUp::findOrFail(hashids_decode($request->id));

        $topup->client_id = $request->client_id;
        $topup->topup_amount = $request->topup_amount;
        $topup->status = 'approve';
        $topup->approved_by = Auth::id();
        $topup->approve_at = Carbon::now();
        $topup->added_by_id = Auth::user('admin')->id;
        $topup->added_by = Auth::user('admin')->user_type;

        if ($request->hasFile('proof')) {
            $deposit_slips = [];
            foreach ($request->file('proof') as $file) {
                $deposit_slip = uploadSingleFile($file, 'uploads/client/clients_topups/', 'png,jpeg,jpg');
                if (is_array($deposit_slip)) {
                    return response()->json($deposit_slip);
                }
                $deposit_slips[] = $deposit_slip;
            }

            $topup->proof = implode(',', $deposit_slips);
        }

        $topup->save();

        $trans = Transections::where('client_id', $topup->client_id);
        $remaining_amount = ($trans->sum('amount_in') - $trans->sum('amount_out'));

        $transection = new Transections;
        $transection->client_id = $topup->client_id;
        $transection->amount_in = $topup->topup_amount;
        $transection->available_balance = $topup->topup_amount + $remaining_amount;
        $transection->topup_id = $topup->id;
        $transection->save();

        return response()->json([
            'success' => empty($request->id) ? 'Top Up created successfully' : 'Top Up updated successfully',
            'redirect' => route('admin.sub_account.client-management.top_up', ['sub_account_id' => session()->get('sub_account_id')]),
        ]);
    }

    public function topup_edit($sub_account_id, $id)
    {
        if (Auth::user('admin')->can('topup-update') != true) {
            abort(403, 'Unauthorized action.');
        }

        $sub_account_id = User::where('sub_account_id', hashids_decode(session()->get('sub_account_id')))->pluck('id');

        $data = [
            'breadcrumb_main' => 'Clients Management',
            'breadcrumb' => 'Edit Top Up',
            'title' => 'Edit Top Up',
            'edit' => WalletTopUp::hashidFind($id),
            'all_users' => User::where('sub_account_id', hashids_decode(session()->get('sub_account_id')))->get(),
            'all_wallet_topup' => WalletTopUp::with('clients')->whereIn('client_id', $sub_account_id)->latest()->get(),
            'sub_account_id' => session()->get('sub_account_id'),
        ];

        return view('admin.client_management.TopUp.all_topup')->with($data);
    }

    public function topup_delete($id)
    {
        if (Auth::user('admin')->can('topup-delete') != true) {
            abort(403, 'Unauthorized action.');
        }

        $delete = WalletTopUp::find($id);
        $delete->delete();

        return response()->json([
            'success' => 'Record Delete Successfully',
            'redirect' => route('admin.sub_account.client-management.Top_Up', ['sub_account_id' => session()->get('sub_account_id')]),
        ]);
    }

    public function all_ads($sub_account_id, Request $request)
    {
        $decoded_sub_account_id = hashids_decode($sub_account_id);
        $sub_account_ids = User::where('sub_account_id', $decoded_sub_account_id)->pluck('id');

        if ($request->ajax()) {
            $ads = Ads::with('client')->whereIn('client_id', $sub_account_ids)->latest()->get();
            return DataTables::of($ads)
                ->addIndexColumn()
                ->addColumn('client_name', fn ($data) => $data->client->client_name ?? '-')
                ->addColumn('adds_title', fn ($data) => Str::limit($data->adds_title, 20, '...') ?? '---')
                ->addColumn('type', fn ($data) => Str::limit(ads_type_text(explode(',', $data->type)), 30, '...'))
                ->addColumn('status', fn ($data) => view('admin.ads_management.include.status', ['data' => $data, 'sub_account_id' => $sub_account_id]))
                ->addColumn('action', fn ($data) => view('admin.ads_management.include.action_td', ['data' => $data, 'sub_account_id' => $sub_account_id]))
                ->filter(function ($query) {
                    if ($search = request('search')) {
                        $query->where(fn ($search_query) => $search_query->whereLike(['adds_title', 'type'], $search));
                    }
                })
                ->make(true);
        }

        $all_add_requests = Ads::with('client')->whereIn('client_id', $sub_account_ids)->latest()->get();
        $all_users = User::where('sub_account_id', $decoded_sub_account_id)->get();

        $data = [
            'breadcrumb_main' => 'Clients Management',
            'breadcrumb' => 'All Ads',
            'title' => 'All Ads',
            'all_add_requests' => $all_add_requests,
            'all_users' => $all_users,
            'sub_account_id' => $sub_account_id,
        ];

        return view('admin.client_management.ads.index', $data);
    }

    public function ads_create(Request $request)
    {
        $sub_account_id = hashids_decode(session('sub_account_id'));
        $sub_account_ids = User::where('sub_account_id', $sub_account_id)->pluck('id');
        
        $data = [
            'breadcrumb_main' => 'Clients Management',
            'breadcrumb' => 'Create Ads Request',
            'title' => 'Create Ads Request',
            'all_add_requests' => Ads::with('client')->whereIn('client_id', $sub_account_ids)->latest()->get(),
            'all_users' => User::where('sub_account_id', $sub_account_id)->get(),
            'sub_account_id' => session('sub_account_id'),
        ];

        return view('admin.client_management.ads.create', $data);
    }

    public function ads_save($sub_account_id, Request $request)
    {
        $trans = Transections::where('client_id', $request->client_id);
        $remaining_amount = ($trans->sum('amount_in') - $trans->sum('amount_out'));

        if ($remaining_amount <= 50) {
            return response()->json([
                'error' => 'You do not have enough balance to make a new ad request.',
            ], 402);
        }

        $rules = [
            'descord_link' => 'required|url',
            'type' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        // Start a database transaction
        DB::beginTransaction();
        try {
            $ads_add = $request->id ? Ads::findOrFail(hashids_decode($request->id)) : new Ads();
            $msg = $request->id ? 'Ads Updated Successfully' : 'Ads Added Successfully';
                
            $ads_add->fill([
                'client_id' => $request->client_id,
                'adds_title' => $request->title ?? '',
                'discord_link' => $request->descord_link,
                'type' => implode(',', $request->type),
                'status' => $request->status,
            ])->save();

            // Commit the transaction
            DB::commit();

            return response()->json([
                'success' => $msg,
                'redirect' => route('admin.sub_account.client-management.all_ads', ['sub_account_id' => $sub_account_id]),
            ]);
        } catch (\Exception $e) {
            // Something went wrong, rollback the transaction
            DB::rollback();

            // Log or handle the exception as needed
            return response()->json([
                'error' => 'Error lead: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function ads_edit($sub_account_id, $id)
    {
        if (Auth::user('admin')->can('user-update') != true) {
            abort(403, 'Unauthorized action.');
        }

        $sub_account_id = hashids_decode(session()->get('sub_account_id'));
        $sub_account_ids = User::where('sub_account_id', $sub_account_id)->pluck('id');

        $data = [
            'breadcrumb_main' => 'Clients Management',
            'breadcrumb' => 'Edit Ads Request',
            'title' => 'Edit Ads Request',
            'all_add_requests' => Ads::with('client')->whereIn('client_id', $sub_account_ids)->latest()->get(),
            'all_users' => User::where('sub_account_id', $sub_account_id)->get(),
            'sub_account_id' => session()->get('sub_account_id'),
            'edit' => Ads::hashidFind($id),
        ];

        return view('admin.client_management.ads.create', $data);
    }

    public function ads_delete($sub_account_id, $id)
    {
        $ad = Ads::find(hashids_decode($id));

        if (!$ad) {
            return response()->json([
                'error' => 'Ad not found',
            ], 404);
        }
    
        // Delete the ad
        $ad->delete();
    
        return response()->json([
            'success' => 'Ad deleted successfully',
            'remove_tr' => true,
        ]);
    }
}
