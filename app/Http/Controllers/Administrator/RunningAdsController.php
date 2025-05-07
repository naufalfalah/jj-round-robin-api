<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\WalletTopUp;
use App\Models\Transections;
use App\Models\LeadClient;
use App\Models\Group;
use App\Models\Ads;
use App\Models\AgentDetail;
use App\Models\DailyAdsSpent;
use App\Models\ClientWallet;
use App\Models\GoogleAccount;
use App\Models\GoogleAdsAd;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\TaxCharge;
use App\Services\GoogleAdsService;
use App\Traits\AdsSpentTrait;
use Carbon\Carbon;
use App\Traits\GoogleTrait;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;

class RunningAdsController extends Controller
{
    use GoogleTrait, AdsSpentTrait;

    function index(Request $request)
    {
        if (Auth::user('admin')->can('lead-frequency-read') != TRUE ) {
            abort(403, 'Unauthorized action.');
        }

        $sub_account_id = request()->segment(3);
        session()->put('sub_account_id', $sub_account_id);

        $weekStartDate = now()->startOfWeek(Carbon::MONDAY)->subWeek()->setHour(10);
        $weekEndDate = $weekStartDate->clone()->addWeek()->endOfWeek(Carbon::MONDAY)->setHour(9);

        $currentDateTime = now();
        if ($currentDateTime->isAfter($weekEndDate)) {
            $weekStartDate = $currentDateTime->startOfWeek(Carbon::MONDAY)->setHour(10);
            $weekEndDate = $weekStartDate->clone()->addWeek()->endOfWeek(Carbon::MONDAY)->setHour(9);
        }

        $week_end_date = now();
        if (isset($request->client) && !empty($request->client)) {
            // Client Side
            $auth_id = hashids_decode($request->client);
            $remaining_balance = DB::table('transections')
                ->select('client_id', DB::raw('sum(amount_in) as amount_in, sum(amount_out) as amount_out'))
                ->where('client_id', $auth_id)
                ->where('deleted_at', null)
                ->groupBy('client_id')
                ->get();
            if (count($remaining_balance) != 0) {
                $remaining_amount = $remaining_balance[0]->amount_in - $remaining_balance[0]->amount_out;
            } else {
                $remaining_amount = 0;
            }

            $get_ppc_leads = LeadClient::where('client_id', $auth_id)->where('lead_type', 'ppc')->count();

            $get_weekly_total_leads = LeadClient::where('lead_type', 'ppc')->where('client_id', $auth_id)->whereBetween('created_at', [$weekStartDate->format('Y-m-d H:i:s'), $weekEndDate->format('Y-m-d H:i:s')])->count();
        } else {
            // Admin Side
            $sub_account_ids = User::where('sub_account_id', hashids_decode(session()->get('sub_account_id')))->pluck('id');

            $get_weekly_total_leads = LeadClient::where('lead_type', 'ppc')->whereIn('client_id', $sub_account_ids)->whereBetween('created_at', [$weekStartDate->format('Y-m-d H:i:s'), $weekEndDate->format('Y-m-d H:i:s')])->count();

            $remaining_balance = DB::table('transections')
                ->select(DB::raw('sum(amount_in) as amount_in, sum(amount_out) as amount_out'))->whereIn('client_id', $sub_account_ids)
                ->where('deleted_at', null)
                ->get();
            $remaining_amount = $remaining_balance[0]->amount_in - $remaining_balance[0]->amount_out;

            $get_ppc_leads = LeadClient::where('lead_type', 'ppc')->whereIn('client_id', $sub_account_ids)->count();
        }

        // Get all ads of all clients in sub account
        $sessionId = session()->get('sub_account_id');
        $subAccountId = hashids_decode($sessionId);
        $clients = User::where('sub_account_id', $subAccountId)->get();

        $googleAds = [];

        foreach ($clients as $client) {
            if (!Auth::user('admin')->google_access_token) {
                break;
            }

            if (!$client->customer_id) {
                continue;
            }

            $googleAdsService = new GoogleAdsService();
            $clientGoogleAds = GoogleAdsAd::with('client')->with('ad')->where('client_id', $client->id)->get();
            $customerId = $client->customer_id;

            // dd($clientGoogleAds);
            foreach ($clientGoogleAds as $clientGoogleAd) {
                if (!$clientGoogleAd->ad_resource_name) {
                    continue;
                }

                $googleAd = $googleAdsService->getAdByResourceName($customerId, $clientGoogleAd->ad_resource_name);

                if (isset($googleAd['errors'])) {
                    continue;
                }

                $googleAd['client'] = $client->client_name;
                $googleAd['ad_request'] = $clientGoogleAd?->ad->adds_title ?? "";
                array_push($googleAds, $googleAd);
            }
        }

        $clientIds = User::where('sub_account_id', $subAccountId)->pluck('id');
        if ($clientIds->isEmpty()) {
            $main_wallet_bls = 0;
            $sub_wallet = [];
        } else {
            $firstClientId = $clientIds->first();
            $wallet = ClientWallet::where('client_id', $firstClientId)->where('status','completed');
            $main_wallet_bls = ($wallet->sum('amount_in') - $wallet->sum('amount_out'));

            $sub_wallet = Ads::where('client_id', $clientIds[0])->latest()->get();
        }

        $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $endOfMonth = Carbon::now()->endOfMonth()->format('Y-m-d');

        $data = [
            'breadcrumb_main' => 'Leads Frequency',
            'breadcrumb' => 'Leads Frequency',
            'title' => 'Leads Frequency',
            'start_of_month' => $startOfMonth,
            'end_of_month' => $endOfMonth,
            'monthly_leads' => $this->get_monthly_client_leads($subAccountId),
            'monthly_ads_spents' => $this->get_monthly_ads_spent($subAccountId),
            'total_balance' => $remaining_amount,
            'total_ppc_leads' => $get_ppc_leads,
            'weekly_total_lead' => $get_weekly_total_leads,
            'dates_text' => "From: {$weekStartDate->format('d-M-Y H:i')} - To: {$week_end_date->format('d-M-Y H:i')}",
            'clients' => User::whereNotNull('email_verified_at')->where('sub_account_id', hashids_decode(session()->get('sub_account_id')))->latest()->get(['id','client_name','email']),
            'sub_account_id' => session()->get('sub_account_id'),
            'google_ads' => $googleAds,
            'sub_account_ids' => User::where('sub_account_id', hashids_decode(session()->get('sub_account_id')))->pluck('id'),
            'main_wallet_bls' => $main_wallet_bls,
            'ads' => Ads::with('client')->whereIn('client_id', $clientIds)->get(),
            'client_id' => $firstClientId ?? '',
            'sub_wallet' => $sub_wallet,
            'google_accounts' => GoogleAccount::all(),
        ];

        return view('admin.running_ads.index', $data);
    }

    public function get_all_leads(Request $request)
    {
        $sub_account_ids = User::where('sub_account_id', hashids_decode(session()->get('sub_account_id')))->pluck('id');

        // for enum
        $enum_type = DB::select(DB::raw("SHOW COLUMNS FROM lead_clients WHERE Field = 'admin_status'"))[0]->Type;
        $enum_values = str_replace(['enum(', ')', "'"], '', $enum_type);
        $enum_values = explode(',', $enum_values);
        // for enum

        if (isset($request->client) && !empty($request->client)) {
            $auth_id = hashids_decode($request->client);
            $client_leads = LeadClient::with('lead_data','clients')->where('client_id', $auth_id)->latest()->get();
        } else {
            $client_leads = LeadClient::with('lead_data','clients')->whereIn('client_id', $sub_account_ids)->latest()->get();
        }

        return DataTables::of($client_leads)
            ->addIndexColumn()
            ->addColumn('client_name', function ($lead) {
                return $lead->clients->client_name ?? '-';
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
                $actionsHtml = "<a href='javascript:void(0)' class='text-primary view_lead_detail_id'  data-data='{$data}' data-id='{$lead->id}' title='View Lead Detail'><i class='bi bi-eye-fill'></i></a>";
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

    public function get_ppc_leads(Request $request){

        $sub_account_ids = User::where('sub_account_id', hashids_decode(session()->get('sub_account_id')))->pluck('id');

        // for enum
        $enum_type = DB::select(DB::raw("SHOW COLUMNS FROM lead_clients WHERE Field = 'admin_status'"))[0]->Type;
        $enum_values = str_replace(['enum(', ')', "'"], '', $enum_type);
        $enum_values = explode(',', $enum_values);
        // for enum

        $client_leads = LeadClient::with('lead_data', 'clients')
            ->where('lead_type', 'ppc');

        if (isset($request->client) && !empty($request->client)) {
            $auth_id = hashids_decode($request->client);
            $client_leads = $client_leads->where('client_id', $auth_id);
        } else {
            $client_leads = $client_leads->whereIn('client_id', $sub_account_ids);
        }

        if ($request->ads_id) {
            $client_leads = $client_leads->where('ads_id', $request->ads_id);
        }

        $client_leads = $client_leads->latest()->get();

        return DataTables::of($client_leads)
                ->addIndexColumn()
                ->addColumn('client_name', function ($lead) {
                    return $lead->clients->client_name ?? '-';
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

    function get_topups(Request $request){
        if(isset($request->client)  && !empty($request->client)){
           $query = WalletTopUp::where('client_id', hashids_decode($request->client))->latest();
        }else{
            $sub_account_ids = User::where('sub_account_id', hashids_decode(session()->get('sub_account_id')))->pluck('id');
            $query = WalletTopUp::whereIn('client_id', $sub_account_ids)->latest();
        }
        if ($request->ajax()) {
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('transaction_id', function ($data) {
                    return $data->transaction_id;
                })
                ->addColumn('client_name', function ($data) {
                    return $data->clients->client_name;
                })
                ->addColumn('topup_amount', function ($data) {
                    return get_price($data->topup_amount);
                })
                ->addColumn('method', function ($data) {
                    return "PayNow";
                })
                ->addColumn('status', function ($data) {
                    return view('admin.client_management.include.status',['data'=>$data]);
                })
                ->addColumn('slip', function ($data) {
                    return view('admin.client_management.include.slip',['data'=>$data]);
                })
                ->addColumn('action', function ($data) {
                    return view('admin.client_management.include.topup_status',['data'=>$data]);
                })
                ->filter(function ($query) {
                    if (request()->input('search')) {
                        $query->where(function ($search_query) {
                            $search_query->whereLike(['status','topup_amount'], request()->input('search'));
                        });
                    }
                })
                ->orderColumn('DT_RowIndex', function ($q, $o) {
                    $q->orderBy('id', $o);
                })
            ->make(true);
        }
    }

    function change_status(Request $request){
        if (Auth::user('admin')->can('lead-frequency-update') != TRUE ) {
            abort(403, 'Unauthorized action.');
        }
        $rules = [
            'topup_status' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            $wallet = WalletTopUp::find($request->wallet_id);

            if($wallet->ad_id == '' || $wallet->ad_id == null){
                $create = ClientWallet::where('transaction_id',$wallet->transaction_id)->first();
            }else{
                $create = Transections::where('transaction_id',$wallet->transaction_id)->first();
                $ads = Ads::find($wallet->ad_id); 
                if($request->topup_status == "approve"){
                    $total_amt = $ads->spend_amount+$wallet->topup_amount; 
                    if($ads->spend_type == 'daily'){
                        $amt = $ads->daily_budget*30;
                    }else{
                        $amt = $ads->daily_budget;
                    }
                
                    if($total_amt == $amt){
                        $ads->payment_status = 1;
                    } 
                    $domian_hosting_pay = 0;
                    if($ads->hosting_is == 'request_to_purchase_hosting' && $ads->is_hosting_pay == 0){
                        // Pause for 1 seconds
                        sleep(1);
                        $domian_hosting_pay = $domian_hosting_pay+15;
                        $add_transaction = new Transections;
                        $add_transaction->client_id = $ads->client_id;
                        // $add_transaction->transaction_id = $wallet->transaction_id;
                        $add_transaction->amount_out = 15;
                        $add_transaction->ads_id = $ads->id;
                        $add_transaction->topup_type = 'hosting_payment';
                        $add_transaction->status = 'completed';
                        $add_transaction->save();
                        $ads->is_hosting_pay = 1;
                    }
                    if($ads->domain_is == 'request_to_purchase' && $ads->is_domain_pay == 0){
                        // Pause for 1 seconds
                        sleep(1);
                        $domian_hosting_pay = $domian_hosting_pay+20;
                        $add_transaction = new Transections;
                        $add_transaction->client_id = $ads->client_id;
                        // $add_transaction->transaction_id = $wallet->transaction_id;
                        $add_transaction->amount_out = 20;
                        $add_transaction->ads_id = $ads->id;
                        $add_transaction->topup_type = 'domain_payment';
                        $add_transaction->status = 'completed';
                        $add_transaction->save();
                        $ads->is_domain_pay = 1;
                    }

                    $ads->spend_amount = $total_amt-$domian_hosting_pay;
                    $ads->save();
                }
            }  
            if($request->topup_status == "approve"){
                $create->status = 'completed';
                $create->save();
            }
            elseif($request->topup_status == "rejected"){
                $create->status = 'declined';
                $create->save();
            }
            elseif($request->topup_status == "pending"){
                $create->status = 'processing';
                $create->save();
            }
            elseif($request->topup_status == "canceled"){
                $create->status = 'canceled';
                $create->save();
            }


            $msg = [
                'success' => 'TopUp Status Change Successfully',
                'reload' => true,
            ];
            $wallet->status =  $request->topup_status;
            $wallet->save();



            // Commit the transaction
            DB::commit();

            return response()->json($msg);
        } catch (\Exception $e) {
            // Something went wrong, rollback the transaction
            DB::rollback();

            // Log or handle the exception as needed
            return response()->json(['error' => 'Error TopUp: ' . $e->getMessage()], 500);
        }
    }

    function get_ads(Request $request)
    {
        if (isset($request->client)  && !empty($request->client)) {
            $query = Ads::where('client_id', hashids_decode($request->client))->where('status','!=','pause')->latest();
         } else {
            $sub_account_ids = User::where('sub_account_id', hashids_decode(session()->get('sub_account_id')))->pluck('id');
            $query = Ads::where('status','!=','pause')->whereIn('client_id', $sub_account_ids)->latest();
         }

        if ($request->ajax()) {
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('client_name', function ($data) {
                    return $data->client->client_name;
                })
                ->addColumn('adds_title', function ($data) {
                    return Str::limit($data->adds_title, 20, '...') ?? '-';
                })
                ->addColumn('type', function ($data) {
                    // $types = explode(',', $data->type);
                    // return Str::limit(ads_type_text($types), 30, '...');
                    return $data->spend_type == 'monthly' ? ucfirst($data->spend_type) : ucfirst($data->spend_type).' ('.get_price($data->daily_budget).')' ;
                })
                ->addColumn('status', function ($data) {
                    return view('admin.client_management.include.ads_status', ['data' => $data]);
                })
                ->addColumn('spend_amount', function ($data) {
                    if($data->spend_type == 'daily'){
                        $day = 30;
                        if($data->status == 'running'){
                            $days = floor($data->spend_amount / $data->daily_budget);
                            $day = min($days, 30);
                        }
                        if($data->spend_amount != 0 && $data->status == 'running'){
                            $budget = $data->daily_budget*$day;
                        }else{
                            $budget = $data->daily_budget*30;
                        }
                        return get_price($budget);
                    }else{
                        return get_price($data->daily_budget);
                    }
                })
                ->addColumn('e_wallet', function ($data) {
                    return view('admin.client_management.include.e_wallet_td', ['data' => $data]);
                })
                // ->addColumn('domain', function ($data) {
                //     return $data->domain;
                // })
                ->addColumn('google_account', function ($data) {
                    return $data->google_account_id ? $data->google_account->email : '-';
                })
                ->addColumn('customer_id', function ($data) {
                    return $data->customer_id ?? '-';
                })
                ->addColumn('action', function ($data) {
                    return view('admin.client_management.include.ads_action_td',['data'=>$data]);
                })
                ->filter(function ($query) {
                    if (request()->input('search')) {
                        $query->where(function ($search_query) {
                            $search_query->whereLike(['adds_title','type'], request()->input('search'));
                        });
                    }
                })
                ->orderColumn('DT_RowIndex', function ($q, $o) {
                    $q->orderBy('id', $o);
                })
            ->make(true);
        }
    }

    function get_main_wallet(Request $request){
        return DataTables::of(ClientWallet::with('ads')->where('client_id', $request->client))
            ->addIndexColumn()
            ->addColumn('amount_in', function ($data) {
                return $data->amount_in != '' ? get_price($data->amount_in) : '-';
            })
            ->addColumn('amount_out', function ($data) {
                return $data->amount_out != '' ? get_price($data->amount_out) : '-';
            })
            ->addColumn('description', function ($data) {
                return str_replace('_',' ',ucfirst($data->topup_type));
            })
            ->addColumn('created_at', function ($data) {
                return get_fulltime($data->created_at);
            })
            ->filter(function ($query) {
                if (request()->input('search')) {
                    $query->where(function ($search_query) {
                        $search_query->whereLike(['subaccount_name'], request()->input('search'));
                    });
                }
            })
            ->orderColumn('DT_RowIndex', function ($q, $o) {
                $q->orderBy('id', $o);
            })
        ->make(true);
    }

    function get_sub_wallet_transactions(Request $request){
        $client_id = hashids_decode($request->client_id);
        $ads_id = hashids_decode($request->ads_id);

        $wallet = ClientWallet::where('client_id', $client_id);
        $main_wallet_bls = ($wallet->sum('amount_in') - $wallet->sum('amount_out'));

        $sub_wallet = Transections::where('client_id', $client_id)->where('ads_id',$ads_id);
        // dd($sub_wallet);
        $sub_wallet_remaining = ($sub_wallet->sum('amount_in') - $sub_wallet->sum('amount_out'));

        $data = array(
            'breadcrumb_main' => 'Sub Wallet',
            'breadcrumb' => 'Transactions',
            'title' => 'Transactions',
            'main_wallet_bls' => $main_wallet_bls,
            'sub_wallet_budget' => Ads::where('id',$ads_id)->first(),
            'sub_wallet_remaining' => $sub_wallet_remaining,
            'client_id' => $client_id,
            'total_topup' => $sub_wallet->sum('amount_in'),
            'total_deducted' => $sub_wallet->sum('amount_out'),
            'total_spend' => $sub_wallet->where('topup_type','google_spent')->sum('amount_out'),
            'subwallets' =>  $sub_wallet = Ads::where('client_id', $client_id)->where('id','!=',$ads_id)->latest()->get(),
            'ads_id' => $ads_id,
        );
        return view('admin.running_ads.sub_wallet_transactions', $data);
    }

    function sub_wallets_bls_update(Request $request){
        $transection = new Transections();
        
        if($request->type == 'add_from_main_wallet'){
            $wallet = ClientWallet::where('client_id', $request->client_id);
            $main_wallet_bls = ($wallet->sum('amount_in') - $wallet->sum('amount_out'));
            if($main_wallet_bls <  $request->amt){
                $msg = [
                    'error' => 'Your main wallet balance is less than the amount you entered. Please add an amount equal to your main wallet balance.',
                ];
                return response()->json($msg);
            }
            $main_wallet = new ClientWallet();
            $main_wallet->client_id = $request->client_id;
            $main_wallet->ads_id = $request->ads_id;
            $main_wallet->amount_out  = $request->amt;
            $main_wallet->topup_type  = 'add_to_subwallet';
            $main_wallet->status = 'completed';
            $main_wallet->save();

            $transection->client_id = $request->client_id;
            $transection->amount_in = $request->amt;
            $transection->ads_id = $request->ads_id;
            $transection->topup_type = 'add_from_main_wallet';
            $transection->status = 'completed';
            $transection->save();
            
            $msg = [
                'success' => 'Balance has been successfully added.',
                'reload' => true,
            ];
    
            return response()->json($msg);
        }

        if($request->type == 'back_to_main_wallet'){
            if($request->amt > $request->sub_wallet_remaining){
                $msg = [
                    'error' => 'Your amount exceeds your current balance. Please add an amount equal to your available balance.',
                ];
                return response()->json($msg);
            }

            $main_wallet = new ClientWallet();
            $main_wallet->client_id = $request->client_id;
            $main_wallet->ads_id = $request->ads_id;
            $main_wallet->amount_in  = $request->amt;
            $main_wallet->topup_type  = 'back_to_wallet';
            $main_wallet->status = 'completed';
            $main_wallet->save();

            $transection->client_id = $request->client_id;
            $transection->amount_out = $request->amt;
            $transection->ads_id = $request->ads_id;
            $transection->topup_type = 'back_to_main_wallet';
            $transection->status = 'completed';
            $transection->save();
            
            $msg = [
                'success' => 'Balance has been successfully added.',
                'reload' => true,
            ];
    
            return response()->json($msg);
        }

        if($request->type == 'transfer_to_subwallet'){
            if($request->amt > $request->sub_wallet_remaining){
                $msg = [
                    'error' => 'Your amount exceeds your current balance. Please add an amount equal to your available balance.',
                ];
                return response()->json($msg);
            }
            
            $transection->client_id = $request->client_id;
            $transection->amount_out = $request->amt;
            $transection->ads_id = $request->ads_id;
            $transection->topup_type = 'transfer_to_subwallet';
            $transection->status = 'completed';
            $transection->save();
            
            $main_wallet = new Transections();
            $main_wallet->client_id = $request->client_id;
            $main_wallet->ads_id = $request->sub_wallet;
            $main_wallet->amount_in  = $request->amt;
            $main_wallet->topup_type  = 'transfer_from_subwallet';
            $main_wallet->status = 'completed';
            $main_wallet->save();

            $msg = [
                'success' => 'Balance has been successfully added.',
                'reload' => true,
            ];
    
            return response()->json($msg);
        }

        if($request->type == 'transfer_from_subwallet'){
            $wallet = Transections::where('ads_id', $request->sub_wallet);
            $main_wallet_bls = ($wallet->sum('amount_in') - $wallet->sum('amount_out')); 
            if($main_wallet_bls <  $request->amt){
                $msg = [
                    'error' => 'Your subwallet balance is less than the amount you entered. Please add an amount equal to your subwallet balance.',
                ];
                return response()->json($msg);
            }
            
            $transection->client_id = $request->client_id;
            $transection->amount_in = $request->amt;
            $transection->ads_id = $request->ads_id;
            $transection->topup_type = 'transfer_from_subwallet';
            $transection->status = 'completed';
            $transection->save();
            
            $main_wallet = new Transections();
            $main_wallet->client_id = $request->client_id;
            $main_wallet->ads_id = $request->sub_wallet;
            $main_wallet->amount_out  = $request->amt;
            $main_wallet->topup_type  = 'transfer_to_subwallet';
            $main_wallet->status = 'completed';
            $main_wallet->save();

            $msg = [
                'success' => 'Balance has been successfully added.',
                'reload' => true,
            ];
    
            return response()->json($msg);
        }

        if($request->type == 'google_spent'){
            if($request->amt > $request->sub_wallet_remaining){
                $msg = [
                    'error' => 'Your amount exceeds your current balance. Please add an amount equal to your available balance.',
                ];
                return response()->json($msg);
            }
            $transection->client_id = $request->client_id;
            $transection->amount_out = $request->amt;
            $transection->ads_id = $request->ads_id;
            $transection->topup_type = 'google_spent';
            $transection->status = 'completed';
            $transection->save();
            
            $msg = [
                'success' => 'Balance has been successfully added.',
                'reload' => true,
            ];
    
            return response()->json($msg);
        }

    }

    function sub_wallets_transactions(Request $request){

        $auth_id = $request->client_id;
        return DataTables::of(Transections::where('client_id', $auth_id)
            ->where('ads_id', $request->ads_id))
                ->addIndexColumn()
                ->addColumn('amount_in', function ($data) {
                    return $data->amount_in != '' ? get_price($data->amount_in) : '-';
                })
                ->addColumn('description', function ($data) {
                    return str_replace('_',' ',ucfirst($data->topup_type));
                })
                ->addColumn('amount_out', function ($data) {
                    return $data->amount_out != '' ? get_price($data->amount_out) : '-';
                })
                ->addColumn('created_at', function ($data) {
                    return get_fulltime($data->created_at);
                })
                ->filter(function ($query) {
                    if (request()->input('search')) {
                        $query->where(function ($search_query) {
                            $search_query->whereLike(['subaccount_name'], request()->input('search'));
                        });
                    }
                })
                ->orderColumn('DT_RowIndex', function ($q, $o) {
                    $q->orderBy('id', $o);
                })
            ->make(true);
    }
    function get_low_bls_ads(Request $request){
        if(isset($request->client)  && !empty($request->client)){
            $query = Ads::where('client_id',hashids_decode($request->client))->where('status','pause')->latest();
         }else{
            $sub_account_ids = User::where('sub_account_id', hashids_decode(session()->get('sub_account_id')))->pluck('id');
            $query = Ads::whereIn('client_id', $sub_account_ids)->where('status','pause')->latest();
         }
        if ($request->ajax()) {
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('client_name', function ($data) {
                    return $data->client->client_name;
                })
                ->addColumn('adds_title', function ($data) {
                    return Str::limit($data->adds_title, 20, '...') ?? '-';
                })
                ->addColumn('type', function ($data) {
                    $types = explode(',', $data->type);
                    return Str::limit(ads_type_text($types), 30, '...');
                })
                ->addColumn('status', function ($data) {
                    return view('admin.client_management.include.ads_status', ['data' => $data]);
                })
                ->addColumn('action', function ($data) {
                    return view('admin.client_management.include.ads_action_td',['data'=>$data]);
                })
                ->filter(function ($query) {
                    if (request()->input('search')) {
                        $query->where(function ($search_query) {
                            $search_query->whereLike(['adds_title','type'], request()->input('search'));
                        });
                    }
                })
                ->orderColumn('DT_RowIndex', function ($q, $o) {
                    $q->orderBy('id', $o);
                })
            ->make(true);
        }
    }

    function get_user_bls(Request $request){

        $data = Transections::where('client_id',$request->clientid)->where('ads_id',$request->adsid);
        $ads_remaining = ($data->sum('amount_in') - $data->sum('amount_out'));
        $msg = [
            'success' => true,
            'bls' => get_price($ads_remaining),
            'amt' => $ads_remaining,
        ];

        return response()->json($msg);
    }

    function change_ads_running_status(Request $request){

        $rules = [
            'ads_status' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        // if($request->ads_status == 'pause'){
        //     return response()->json(['error' => "The status is already set to 'pause'. Please try another status."], 500);
        // }

        $ads = Ads::find($request->ads_id);
        $ads->status = $request->ads_status;
        $ads->save();
        if($request->ads_status == 'running'){
            if($request->user_bls <= 50){
                return response()->json(['error' => 'This amount is less than 50. Please update your wallet and try again.'], 500);
            }
            $msg = [
                'success' => 'Ads Status Change Successfully',
                'reload' => true,
            ];
            $ads->status = $request->ads_status;
            $ads->save();
            return response()->json($msg);
        }else{
            $msg = [
                'success' => 'Ads Status Change Successfully',
                'reload' => true,
            ];
            $ads->status = $request->ads_status;
            $ads->save();
            return response()->json($msg);
        }

    }

    function ads_remaining_balance_refund(Request $request){
       $transections = new Transections();
       $transections->client_id = $request->client_id;
       $transections->amount_out = $request->refund_amt;
       $transections->ads_id = $request->ads_id;
       $transections->topup_type = 'back_to_main_wallet';
       $transections->save();

       $client_wallet = new ClientWallet();
       $client_wallet->client_id = $request->client_id;
       $client_wallet->ads_id = $request->ads_id;
       $client_wallet->amount_in = $request->refund_amt;
       $client_wallet->topup_type = 'back_to_wallet';
       $client_wallet->save();

        $msg = [
            'success' => 'Reremaining Balance Refund Successfully',
            'reload' => true,
        ];

        return response()->json($msg);
    }

    function edit_add(Request $request) {
        $mytime = Carbon::now();
        $rules = [];
        $ads = Ads::find($request->ads_id);

        if($ads->domain_name != $request->name_domain){
            $rules['name_domain'] = 'required|url|unique:ads,domain_name';
        }
        if($ads->website_url != $request->website_url){
            $rules['website_url'] = 'required|url|unique:ads,website_url';
        }

        $rules['ads_id'] = 'required';
        $rules['discord_link'] = 'required';

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }


        if (!$ads) {
            return response()->json(['error' => 'Ad not found'], 404);
        }

        if($request->ads_status == 'running'){
            if($ads->spend_amount == 0 || $ads->spend_amount == ''){
                return ['error' => 'The ad can’t go live because subwallet amount is zero'];
            }
        }


        $ads->status = $request->ads_status;
        $ads->domain_name = $request->name_domain;
        $ads->discord_link = $request->discord_link;
        $ads->website_url = $request->website_url;
        $ads->e_wallet = $request->e_wallet !== 'normal' ? $request->e_wallet : null;
        if($request->ads_status == 'running'){
            $ads->launch_date = $mytime->toDateTimeString();
        }
        $ads->google_account_id = $request->google_account_id;
        $ads->customer_id = $request->customer_id;
        $ads->save();

        $msg = [
            'success' => 'Add Updated Successfully',
            'reload' => true,
        ];

        return response()->json($msg);
    }



    function change_ads_status(Request $request){
        if (Auth::user('admin')->can('lead-frequency-update') != TRUE ) {
            abort(403, 'Unauthorized action.');
        } 
        $rules = [
            'ads_status' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            $ads = Ads::find($request->ads_id);

            $msg = [
                'success' => 'Ads Status Change Successfully',
                'reload' => true,
            ];
            if($request->ads_status == 'reject'){
                // $transaction = Transections::where('ads_id', $request->ads_id)->latest()->first();
                // $transaction->delete();
            }
            $ads->status =  $request->ads_status;
            $ads->save();

            // Commit the transaction
            DB::commit();

            return response()->json($msg);
        } catch (\Exception $e) {
            // Something went wrong, rollback the transaction
            DB::rollback();

            // Log or handle the exception as needed
            return response()->json(['error' => 'Error TopUp: ' . $e->getMessage()], 500);
        }
    }

    function lead_status(Request $request){
        if (Auth::user('admin')->can('lead-frequency-update') != TRUE ) {
            abort(403, 'Unauthorized action.');
        }
        $rules = [
            'lead_status' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            $lead = LeadClient::find($request->lead_id);

            $msg = [
                'success' => 'Lead Status Change Successfully',
                'reload' => true,
            ];

            $lead->admin_status =  $request->lead_status;
            $lead->save();

            // Commit the transaction
            DB::commit();

            return response()->json($msg);
        } catch (\Exception $e) {
            // Something went wrong, rollback the transaction
            DB::rollback();

            // Log or handle the exception as needed
            return response()->json(['error' => 'Error Lead Status: ' . $e->getMessage()], 500);
        }
    }

    function lead_admin_status(Request $request){
        if (Auth::user('admin')->can('lead-frequency-update') != TRUE ) {
            abort(403, 'Unauthorized action.');
        }

        $lead_client = LeadClient::find($request->lead_id);

        if (!$lead_client) {
            return response()->json([
                'error' => 'Lead not found.'
            ], 404);
        }

        $lead_client->admin_status = $request->admin_status;
        $lead_client->save();

        return response()->json([
            'success' => 'Status Changed Successfully',
        ]);
    }

    function transactions(Request $request){
        if(isset($request->client)  && !empty($request->client)){
            $query = Transections::with('get_top_up', 'get_ads')->where('client_id', hashids_decode($request->client));
         }else{
            $sub_account_ids = User::where('sub_account_id', hashids_decode(session()->get('sub_account_id')))->pluck('id');
            $query = Transections::with('get_top_up', 'get_ads')->whereIn('client_id', $sub_account_ids);
         }

        if ($request->ajax()) {
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('client_name', function ($data) {
                    return $data->clients->client_name;
                })
                ->addColumn('date', function ($data) {
                    return $data->created_at->format('M-d-Y');
                })
                ->addColumn('trans_type', function ($data) {
                    return view('admin.client_management.include.trans_type',['data'=>$data]);
                })
                ->addColumn('debit', function ($data) {
                    return view('admin.client_management.include.debit',['data'=>$data]);
                })
                ->addColumn('credit', function ($data) {
                    return view('admin.client_management.include.credit',['data'=>$data]);
                })
                ->addColumn('vat_charges', function ($data) {
                    return view('admin.client_management.include.vat_charges',['data'=>$data]);
                })
                ->addColumn('available_balance', function ($data) {
                    return view('admin.client_management.include.avail_amount',['data'=>$data]);
                })
                ->filter(function ($query) {
                    if (request()->input('search')) {
                        $query->where(function ($search_query) {
                            $search_query->whereLike(['available_balance','amount_in', 'amount_out'], request()->input('search'));
                        });
                    }
                })
                ->orderColumn('DT_RowIndex', function ($q, $o) {
                    $q->orderBy('id', $o);
                })
            ->make(true);
        }
    }

    function get_daily_ads_spent(Request $request)
    {
        $subAccountId = hashids_decode(session()->get('sub_account_id'));
        $clientIds = User::where('sub_account_id', $subAccountId)->pluck('id');
        $adIds = Ads::whereIn('client_id', $clientIds)->pluck('id');

        $query = DailyAdsSpent::selectRaw('ads_id, DATE(date) as date, SUM(amount) as total_amount')
            ->with('ads')
            ->with('ads.client')
            ->whereIn('ads_id', $adIds)
            ->groupBy('ads_id', 'date')
            ->orderBy('date', 'desc');

        if ($request->ajax()) {
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('client_name', function ($data) {
                    return $data->ads ? $data->ads->client->client_name : 'N/A';
                })
                ->addColumn('ad_name', function ($data) {
                    return $data->ads ? $data->ads->adds_title : 'N/A';
                })
                ->addColumn('date', function ($data) {
                    return get_fulltime($data->date, 'M-d-Y');
                })
                ->addColumn('amount', function ($data) {
                    return number_format($data->total_amount, 2);
                })
                ->filter(function ($query) {
                    $search = request()->input('search.value');
                    $adsId = request()->input('ads_id');

                    if ($search) {
                        $query->where(function ($search_query) use ($search) {
                            $search_query->where('total_amount', 'like', "%$search%")
                                ->orWhere('date', 'like', "%$search%")
                                ->orWhereHas('ads', function ($q) use ($search) {
                                    $q->where('adds_title', 'like', "%$search%");
                                });
                        });
                    }
                    if ($adsId) {
                        $query->whereHas('ads', function ($q) use ($adsId) {
                            $q->where('id', $adsId);
                        });
                    }
                })
                ->orderColumn('ad_name', function ($query, $order) {
                    $query->orderBy('ads.adds_title', $order);
                })
                ->make(true);
        } else {
            return $query->get();
        }
    }

    function daily_ads_spent_save(Request $request){
        $rules = [
            'amount' => 'required|numeric|gt:0',
            'date' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $existingData = DailyAdsSpent::where('ads_id', $request->ads_id)->where('date', $request->date)->first();
        if ($existingData) {
            return response()->json(['error' => 'Spend Amount for this date already exists.'], 400);
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            $daily_ads_spent = new DailyAdsSpent();
            if ($request->id && !empty($request->id)) {
                $daily_ads_spent = $daily_ads_spent->findOrfail($request->id);
                $msg = [
                    'success' => 'Daily Ads Spent Updated Successfully',
                ];
            } else {
                $msg = [
                    'success' => 'Daily Ads Spent Added Successfully',
                ];
            }

            $daily_ads_spent->sub_account_id = hashids_decode(session()->get('sub_account_id'));
            $daily_ads_spent->ads_id = $request->ads_id;
            $daily_ads_spent->amount = $request->amount;
            $daily_ads_spent->date = $request->date;
            $daily_ads_spent->added_by_id = auth('admin')->id();
            $daily_ads_spent->save();

            $this->stop_low_budget_ads();

            // Commit the transaction
            DB::commit();

            return response()->json($msg);
        } catch (\Exception $e) {
            // Something went wrong, rollback the transaction
            DB::rollback();

            // Log or handle the exception as needed
            return response()->json(['error' => 'Error Daily Ads Spent: ' . $e->getMessage()], 500);
        }
    }

    private function stop_low_budget_ads() {
        $users = Ads::where('status','running')->get();
        foreach ($users as $key => $value) {
            $this_weak_payment = $this->client_payment($value->client_id, hashids_decode(session()->get('sub_account_id')));

            $trans = Transections::where('client_id',$value->client_id);
            $remaining_amount = ($trans->sum('amount_in') - $trans->sum('amount_out'));
            $available_balance = $remaining_amount - $this_weak_payment;
            if($available_balance <= 50){
                $ads = Ads::find($value->id);
                $ads->status = 'pause';
                $ads->save();
            }
        }
    }

    public function view_progress($client_id ,$ads_id)
    {
        $ads_id = hashids_decode($ads_id);
        $client_id = hashids_decode($client_id);
        $trans = Transections::where('client_id',$client_id)->where('ads_id',$ads_id);
        $remaining_amount = ($trans->sum('amount_in') - $trans->sum('amount_out'));
        $get_ppc_leads = LeadClient::where('client_id', $client_id)->where('lead_type', 'ppc')->where('ads_id',$ads_id)->count();
        $data = array(
            'breadcrumb' => 'View Ads Progress',
            'title' => 'View Ads Progress',
            'ads_id' => $ads_id,
            'client_id' => $client_id,
            'remaining_amount' => $remaining_amount,
            'ads_budget' => Ads::where('id',$ads_id)->first(),
            'total_ppc_leads' => $get_ppc_leads,
        );

        return view('admin.running_ads.view_progress')->with($data);
    }
    function get_leads_data(Request $request){
        $client_id = $request->client_id;
        $ads_id = $request->ads_id;
        // for enum
        $enum_type = DB::select(DB::raw("SHOW COLUMNS FROM lead_clients WHERE Field = 'admin_status'"))[0]->Type;
        $enum_values = str_replace(['enum(', ')', "'"], '', $enum_type);
        $enum_values = explode(',', $enum_values);
        // for enum

        $client_leads = LeadClient::with('lead_data','clients')->where('client_id', $client_id)->where('lead_type', 'ppc')->where('ads_id',$ads_id)->latest()->get();

        return DataTables::of($client_leads)
                ->addIndexColumn()
                ->addColumn('client_name', function ($lead) {
                    return $lead->clients->client_name ?? '-';
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
}
