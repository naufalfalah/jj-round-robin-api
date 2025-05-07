<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ads;
use App\Models\Admin;
use App\Models\ClientWallet;
use App\Models\Transections;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use App\Models\TaxCharge;
use App\Models\LeadClient;
use App\Models\AdsInvoice;
use App\Models\AgentDetail;
use App\Models\ClientTour;
use App\Models\Tour;

class AdsManagementController extends Controller
{
    public function index(Request $request)
    {
        $auth_id = auth('web')->id();
        if ($request->ajax()) {
            return DataTables::of(Ads::query()->where('client_id', $auth_id)->latest())
                ->addIndexColumn()
                ->addColumn('adds_request', function ($data) {
                    return Str::limit($data->adds_title, 20, '...') ?? '---';
                })
                ->addColumn('type', function ($data) {
                    $types = explode(',', $data->type);
                    return Str::limit(ads_type_text($types), 30, '...');
                })
                ->addColumn('status', function ($data) {
                    return view('client.ads_management.include.status', ['data' => $data]);
                })
                ->addColumn('spend_amount', function ($data) {
                    if ($data->spend_type == 'daily') {
                        $day = 30;
                        if ($data->status == 'running') {
                            $days = floor($data->spend_amount / $data->daily_budget);
                            $day = min($days, 30);
                        }
                        if ($data->spend_amount != 0 && $data->status == 'running') {
                            $budget = $data->daily_budget * $day;
                        } else {
                            $budget = $data->daily_budget * 30;
                        }
                        return get_price($budget);
                    } else {
                        return get_price($data->daily_budget);
                    }
                })
                ->addColumn('launch_date', function ($data) {
                    return $data->launch_date != '' ? get_date($data->launch_date) : '-';
                })
                ->addColumn('no_lead', function ($data) {
                    return $data->no_lead ?? '-';
                })
                ->addColumn('action', function ($data) {
                    return view('client.ads_management.include.action_td', ['data' => $data]);
                })
                ->filter(function ($query) {
                    if (request()->input('search')) {
                        $query->where(function ($search_query) {
                            $search_query->whereLike(['adds_title', 'type'], request()->input('search'));
                        });
                    }
                })
                ->orderColumn('DT_RowIndex', function ($q, $o) {
                    $q->orderBy('id', $o);
                })
                ->make(true);
        }

        // Check if user has run the tour
        $tour = Tour::firstOrCreate(['code' => 'FINISH_1'], ['name' => 'Finish']);
        $client_tour = ClientTour::where(['client_id' => auth('web')->user()->id, 'tour_id' => $tour->id])->first();
        if ($client_tour) {
            // Check if user has run the tour
            $tour = Tour::firstOrCreate(['code' => 'FINISH_2'], ['name' => 'Finish']);
            $client_tour = ClientTour::where(['client_id' => auth('web')->user()->id, 'tour_id' => $tour->id])->first();
        } else {
            $tour = null;
            $client_tour = null;
        }

        $data = [
            'breadcrumb_main' => 'Ads Requests',
            'breadcrumb' => 'All Ads Requests',
            'title' => 'All Ads Requests',
            'tour' => $tour ?? null,
            'client_tour' => $client_tour ?? null,
        ];
        return view('client.ads_management.index', $data);
    }

    public function add_ads()
    {
        $user_id = auth('web')->id();
        $trans = Transections::where('client_id', $user_id);

        // Check if user has run the tour 'start 1'
        $tour = Tour::firstOrCreate(['code' => 'START_1'], ['name' => 'Get Started']);
        $client_tour = ClientTour::where(['client_id' => auth('web')->user()->id, 'tour_id' => $tour->id])->first();
        if ($client_tour) {
            // Check if user has run the tour 'start 2'
            $tour = Tour::firstOrCreate(['code' => 'START_2'], ['name' => 'Get Started']);
            $client_tour = ClientTour::where(['client_id' => auth('web')->user()->id, 'tour_id' => $tour->id])->first();
        } else {
            $tour = null;
            $client_tour = null;
        }

        $data = [
            'breadcrumb_main' => 'Ads Requests',
            'breadcrumb' => 'New Ad Request',
            'title' => 'New Ad Request',
            'remaining_amount' => ($trans->sum('amount_in') - $trans->sum('amount_out')),
            'nav_tab' => 'ads_add',
            'tour' => $tour ?? null,
            'client_tour' => $client_tour ?? null,
        ];

        return view('client.ads_management.add_new', $data);
    }

    public function edit_ads($id)
    {
        $data = [
            'breadcrumb_main' => 'Edit Request',
            'breadcrumb' => 'Edit Ad Request',
            'title' => 'Edit Ad Request',
            'edit' => Ads::where('id', hashids_decode($id))
                ->whereIn('status', ['pending', 'reject'])
                ->first(),
            'nav_tab' => 'ads_add'
        ];
        return view('client.ads_management.add_new', $data);
    }





    public function save(Request $request)
    {
        if ($request->domain_is == 'request_to_purchase') {
            $check_domain = $this->check_domain($request);
            if ($check_domain) {
                return ['error' => 'The domain is not available.'];
            }
        }
        if ($request->spend_type == 'monthly') {
            $daily_budget = number_format($request->spend_amount / 30, 2);
            $monthly_budget = number_format($request->spend_amount, 2);
        } else {
            $daily_budget = number_format($request->spend_amount, 2);
            $monthly_budget = number_format($request->spend_amount * 30, 2);
        }
        
        $client = auth('web')->id();
        $wallet = ClientWallet::where('client_id', $client)->where('status', 'completed');
        $check_balance = ($wallet->sum('amount_in') - $wallet->sum('amount_out'));
        if ($request->id && !empty($request->id)) {
            $check_balance = $check_balance + $request->old_spend_amount;
        }
        $days = 30;
        if ($request->spend_type == 'monthly') {
            $total_spend_amount = $request->spend_amount;
        } else {
            // if($check_balance < $request->spend_amount){
            //     return ['error' => 'You do not have enough balance to make a new ad request.'];
            // }
            $total_spend_amount = $request->spend_amount * 30;
            // if($check_balance < $total_spend_amount){
            // $days = floor($check_balance / $request->spend_amount);
            // $total_spend_amount = $request->spend_amount*$days;
            // }
        }
        if ($request->id && !empty($request->id)) {
            if ($request->spend_type == 'monthly') {
                $bls = $check_balance;
            } else {
                $bls = $check_balance + $request->old_spend_amount;
            }
            // if($bls < $total_spend_amount){
            //     return ['error' => 'You do not have enough balance to make a new ad request.'];
            // }
        } else {
            // if($check_balance < $total_spend_amount){
            //     return ['error' => 'You do not have enough balance to make a new ad request.'];
            // }
        }
        $rules = [
            'title' => 'required',
            'spend_amount' => 'required|integer|min:1'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }
        // Start a database transaction
        DB::beginTransaction();
        try {
            $ads_add = new Ads();
            $domian_hosting_pay = 0;
            if ($request->id && !empty($request->id)) {
                $ads_add = $ads_add->findOrfail($request->id);
                if ($total_spend_amount < $request->old_spend_amount) {
                    $new_in_amt = $request->old_spend_amount - $total_spend_amount;
                    // echo "spend_amount is less than old_spend_amount.";
                    // $wallet = new ClientWallet();
                    // $wallet->client_id = auth('web')->id();
                    // $wallet->ads_id =  $ads_add->id;
                    // $wallet->amount_in = $new_in_amt;
                    // $wallet->topup_type = 'back_to_wallet';
                    // $wallet->status = 'completed';
                    // $wallet->save();
                    // $add_transaction = new Transections;
                    // $add_transaction->client_id = auth('web')->id();
                    // $add_transaction->amount_out = $new_in_amt;
                    // $add_transaction->ads_id = $ads_add->id;
                    // $add_transaction->topup_type = 'back_to_main_wallet';
                    // $add_transaction->save();
                } elseif ($total_spend_amount > $request->old_spend_amount) {
                    $new_out_amt = $total_spend_amount - $request->old_spend_amount;
                    // // echo "this spend_amount is not less than old_spend_amount.";
                    // $wallet = new ClientWallet();
                    // $wallet->client_id = auth('web')->id();
                    // $wallet->ads_id =  $ads_add->id;
                    // $wallet->amount_out = $new_out_amt;
                    // $wallet->topup_type = 'add_to_subwallet';
                    // $wallet->status = 'completed';
                    // $wallet->save();
                    // $add_transaction = new Transections;
                    // $add_transaction->client_id = auth('web')->id();
                    // $add_transaction->amount_in = $new_out_amt;
                    // $add_transaction->ads_id = $ads_add->id;
                    // $add_transaction->save();
                }
                if ($request->domain_is != $ads_add->domain_is && $ads_add->is_domain_pay == 0 && $ads_add->spend_amount > 0 && $request->domain_is != 'i_have_my_own_domain') {
                    $domian_hosting_pay = $domian_hosting_pay + 20;
                    $add_transaction = new Transections;
                    $add_transaction->client_id = $ads_add->client_id;
                    $add_transaction->amount_out = 20;
                    $add_transaction->ads_id = $ads_add->id;
                    $add_transaction->topup_type = 'domain_payment';
                    $add_transaction->status = 'completed';
                    $add_transaction->save();
                    $ads_add->is_domain_pay = 1;
                }
                if ($request->hosting_is != $ads_add->hosting_is && $ads_add->is_hosting_pay == 0 && $ads_add->spend_amount > 0 && $request->hosting_is != 'i_have_my_own_hosting') {
                    $domian_hosting_pay = $domian_hosting_pay + 15;
                    $add_transaction = new Transections;
                    $add_transaction->client_id = $ads_add->client_id;
                    $add_transaction->amount_out = 15;
                    $add_transaction->ads_id = $ads_add->id;
                    $add_transaction->topup_type = 'hosting_payment';
                    $add_transaction->status = 'completed';
                    $add_transaction->save();
                    $ads_add->is_hosting_pay = 1;
                }
                
                
                $msg = [
                    'success' => 'Ads Updated Successfully',
                    'redirect' => route('user.ads.all'),
                ];
            } else {
                //                 $msg = "Hey Joleen,
                // A new subwallet has been created by a client named ".auth('web')->user()->client_name." in the e-wallet. Here are the details:
                // Title: ".$request->title."
                // Daily Budget: SGD ".$daily_budget."
                // Total Monthly Budget: SGD ".$monthly_budget."
                // Domain Name: ".$request->domain_name."
                // Domian Purchase Request : ".ucfirst(str_replace('_',' ',$request->domain_is))."
                // Hosting Purchase Request: ".ucfirst(str_replace('_',' ',$request->hosting_is))."
                // Hosting Details:
                // ".$request->hosting_name."";
                //                 $this->send_wp_message('+923483009096', $msg);
                $msg = [
                    'success' => 'Ads Added Successfully',
                    'redirect' => route('user.ads.all'),
                ];
            }
            $ads_add->client_id = auth('web')->id();
            $ads_add->adds_title = $request->title ?? '';
            $ads_add->daily_budget = $request->spend_amount;
            $ads_add->spend_amount = $ads_add->spend_amount - $domian_hosting_pay;
            $ads_add->domain_name = $request->domain_name;
            $selected_types = $request->type;
            // $ads = implode(',', $selected_types);
            // $ads_add->type = $ads;
            $ads_add->spend_type = $request->spend_type;
            $ads_add->status = 'pending';
            $ads_add->domain_is = $request->domain_is;
            $ads_add->hosting_is = $request->hosting_is;
            $ads_add->hosting_details = $request->hosting_name;

            $ads_add->save();
            // Commit the transaction
            DB::commit();
            // if (is_null($request->id) || is_null($request->old_spend_amount)) {
            //     $wallet = new ClientWallet();
            //     $wallet->client_id = auth('web')->id();
            //     $wallet->ads_id =  $ads_add->id;
            //     $wallet->amount_out = $total_spend_amount;
            //     $wallet->topup_type = 'add_to_subwallet';
            //     $wallet->status = 'completed';
            //     $wallet->save();
            //     $add_transaction = new Transections;
            //     $add_transaction->client_id = auth('web')->id();
            //     $add_transaction->amount_in = $total_spend_amount;
            //     $add_transaction->ads_id = $ads_add->id;
            //     $add_transaction->save();
            // }


            // Check if user has run the tour 'start 1'
            $tour = Tour::firstOrCreate(['code' => 'START_1'], ['name' => 'Get Started']);
            $client_tour = ClientTour::where(['client_id' => auth('web')->user()->id, 'tour_id' => $tour->id])->first();
            if ($client_tour) {
                // Check if user has run the tour
                $tour = Tour::firstOrCreate(['code' => 'START_2'], ['name' => 'Get Started']);
                $client_tour = ClientTour::where(['client_id' => auth('web')->user()->id, 'tour_id' => $tour->id])->first();
                if (!$client_tour) {
                    unset($msg['redirect']);
                }
            }

            return response()->json($msg);
        } catch (\Exception $e) {
            // Something went wrong, rollback the transaction
            DB::rollback();
            // Log or handle the exception as needed
            return response()->json(['error' => 'Error lead: ' . $e->getMessage()], 500);
        }
    }

    public function update($id, Request $request)
    {
        $ads = Ads::find($id);
        $ads->no_lead = $request->no_lead;
        $ads->save();

        return redirect()->route('user.ads.all');
    }

    public function delete($id)
    {
        $ads_delete = Ads::hashidFind($id);


        $wallet = new ClientWallet();
        $wallet->client_id = $ads_delete->client_id;
        $wallet->ads_id = $ads_delete->id;
        $wallet->amount_in = $ads_delete->spend_amount;
        $wallet->topup_type = 'back_to_wallet';
        $wallet->status = 'completed';
        $wallet->save();

        $add_transaction = new Transections;
        $add_transaction->client_id = $ads_delete->client_id;
        $add_transaction->amount_out = $ads_delete->spend_amount;
        $add_transaction->ads_id = $ads_delete->id;
        $add_transaction->topup_type = 'back_to_main_wallet';
        $add_transaction->status = 'completed';
        $add_transaction->save();


        $ads_delete->delete();

        return response()->json([
            'success' => 'Ads Deleted Successfully',
            'redirect' => route('user.ads.all'),
        ]);
    }

    public function view_progress($ads_id)
    {
        $auth_id = auth('web')->id();

        $check_user_ads = $auth_id;

        if ($check_user_ads === true) {
            $check_user_report = AdsInvoice::where('client_id', $auth_id)->latest()->first();
            if ($check_user_report) {
                $dates = $this->get_user_start_end_time($auth_id);
                $weekStartDate = $dates['start_date']->format('d-M-Y H:i');
                $weekEndDate = $dates['end_date']->format('d-M-Y H:i');
                $this_week_client_leads = $this->this_week_client_leads($auth_id);
                $this_weak_payment = $this->client_payment($auth_id, auth('web')->user()->sub_account_id);
            } else {
                $weekStartDate = 0;
                $weekEndDate = 0;
                $this_week_client_leads = 0;
                $this_weak_payment = 0;
            }

            $check_user_leads = LeadClient::where('client_id', $auth_id)->where('lead_type', 'ppc')->first();
            if ($check_user_leads) {
                $dates = $this->get_user_start_end_time($auth_id);
                $weekStartDate = $dates['start_date']->format('d-M-Y H:i');
                $weekEndDate = $dates['end_date']->format('d-M-Y H:i');
                $this_week_client_leads = $this->this_week_client_leads($auth_id);
                $this_weak_payment = $this->client_payment($auth_id, auth('web')->user()->sub_account_id);
            } else {
                $weekStartDate = 0;
                $weekEndDate = 0;
                $this_week_client_leads = 0;
                $this_weak_payment = 0;
            }
        } else {
            $weekStartDate = 0;
            $weekEndDate = 0;
            $this_week_client_leads = 0;
            $this_weak_payment = 0;
        }

        // $avg_single_leads_amount = $this->avg_single_leads_amount();

        $trans = Transections::where('client_id', $auth_id)->where('ads_id', hashids_decode($ads_id));

        $remaining_amount = ($trans->sum('amount_in') - $trans->sum('amount_out'));

        $get_ppc_leads = LeadClient::where('client_id', $auth_id)->where('lead_type', 'ppc')->where('ads_id', hashids_decode($ads_id))->count();

        $data = [
            'breadcrumb' => 'View Ads Progress',
            'title' => 'View Ads Progress',
            'ads_id' => $ads_id,
            'ads_budget' => Ads::hashidfind($ads_id),
            'remaining_balance' => $remaining_amount,
            'total_ppc_leads' => $get_ppc_leads,
            'this_weak_payment' => $this_weak_payment,
            'dates_text' => "From: {$weekStartDate} - To: {$weekEndDate}"
        ];

        return view('client.ads_management.view_progress')->with($data);
    }

    public function get_leads_data(Request $request)
    {
        $auth_id = auth('web')->id();
        // for enum
        $enum_type = DB::select(DB::raw("SHOW COLUMNS FROM lead_clients WHERE Field = 'admin_status'"))[0]->Type;
        $enum_values = str_replace(['enum(', ')', "'"], '', $enum_type);
        $enum_values = explode(',', $enum_values);
        // for enum

        $client_leads = LeadClient::with('lead_data', 'clients')->where('client_id', $auth_id)->where('lead_type', 'ppc')->where('ads_id', hashids_decode($request->ads_id))->latest()->get();

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
                    $agent_detail = AgentDetail::where('registration_no', $lead->registration_no)->first();
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
                        $search_query->whereLike(['status', 'topup_amount'], request()->input('search'));
                    });
                }
            })
            ->rawColumns(['actions', 'lead_data', 'admin_status'])
            ->make(true);
    }

    public function lead_admin_status(Request $request)
    {

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

    public function check_domain(Request $request)
    {
        $domain = strtolower($request->domain_name);
        $domain = preg_replace('/^https?:\/\//', '', $domain);

        // Remove www. prefix if it exists
        $domain = preg_replace('/^www\./', '', $domain);

        $domain = rtrim($domain, '/');
        $domain = strtok($domain, '/');

        $ip = '13.55.97.236';
        $apiUser = 'jomejourney';
        $username = 'jomejourney';
        $apiKey = '9e4b47c12fe849b09dac578aa2048883';

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.namecheap.com/xml.response?ApiUser=$apiUser&ApiKey=$apiKey&UserName=$username&Command=namecheap.domains.check&ClientIp=$ip&DomainList=$domain",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response = curl_exec($curl);

        curl_close($curl);
        // Parse the XML response
        $xml = simplexml_load_string($response);
        if (!$xml) {
            return true;
        }
        // Check the Available attribute
        $available = (string) $xml->CommandResponse->DomainCheckResult['Available'];

        // Return the result based on availability
        if ($available === 'false') {
            return true;
        }

    }

    private function send_wp_message($client_number, $message)
    {
       
        $curl = curl_init();
        $api_key = config('app.wp_api_key');
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.p.2chat.io/open/whatsapp/send-message',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode([
                'to_number' => $client_number,
                'from_number' => '+6589469107',
                'text' => $message
            ]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'X-User-API-Key: ' . $api_key
            ],
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            echo 'Curl error: ' . curl_error($curl);
        }

        curl_close($curl);
        return $response;
    }
}
