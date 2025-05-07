<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\WalletTopUp;
use App\Models\Transections;
use App\Models\Admin;
use App\Models\Ads;
use App\Models\AdsInvoice;
use App\Models\ClientTour;
use App\Models\ClientWallet;
use App\Models\TopupSetting;
use App\Models\Tour;
use App\Models\TransferFund;
use App\Traits\AdsSpentTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class WalletController extends Controller
{
    use AdsSpentTrait;

    public function add_top_up(Request $request){

        $auth_id = auth('web')->id();
        $wallet = ClientWallet::where('client_id', $auth_id)->where('status','completed');
        $main_wallet_bls = ($wallet->sum('amount_in') - $wallet->sum('amount_out'));
        $last_transaction_date = $wallet->orderBy('created_at', 'desc')->value('updated_at');
        $sub_wallets = Ads::where('client_id', $auth_id)->latest()->get();


        if ($request->ajax()) {
            return DataTables::of(ClientWallet::query()->where('client_id', $auth_id)
            ->where('amount_in', '!=', '')->latest())
                ->addIndexColumn()
                ->addColumn('amount', function ($data) {
                    return get_price($data->amount_in);
                })
                ->addColumn('topup_type', function ($data) {
                    return str_replace('_',' ',ucfirst($data->topup_type));
                })
                ->addColumn('created_at', function ($data) {
                    return get_fulltime($data->created_at);
                })
                ->filter(function ($query) {
                    if (request()->input('search')) {
                        $query->where(function ($search_query) {
                            $search_query->whereLike(['status','amount_in'], request()->input('search'));
                        });
                    }
                })
                ->orderColumn('DT_RowIndex', function ($q, $o) {
                    $q->orderBy('id', $o);
                })
            ->make(true);
        }

        // Check if user has run the tour 'start 1'
        $tour = Tour::firstOrCreate(['code' => 'START_1'], ['name' => 'Get Started']);
        $client_tour = ClientTour::where(['client_id' => auth('web')->user()->id, 'tour_id' => $tour->id])->first();
        if ($client_tour) {
            // Check if user has run the tour 'start 2'
            $tour = Tour::firstOrCreate(['code' => 'START_2'], ['name' => 'Get Started']);
            $client_tour = ClientTour::where(['client_id' => auth('web')->user()->id, 'tour_id' => $tour->id])->first();
            if ($client_tour) {
                // Check if user has run the tour 'start 3'
                $tour = Tour::firstOrCreate(['code' => 'START_3'], ['name' => 'Get Started']);
                $client_tour = ClientTour::where(['client_id' => auth('web')->user()->id, 'tour_id' => $tour->id])->first();
                if ($client_tour) {
                    // Check if user has run the tour 'after topup'
                    $tour = Tour::firstOrCreate(['code' => 'AFTER_TOPUP'], ['name' => 'After Topup']);
                    $client_tour = ClientTour::where(['client_id' => auth('web')->user()->id, 'tour_id' => $tour->id])->first();
                }
            } else {
                $tour = null;
                $client_tour = null;
            }
        }

        $data = array(
            'breadcrumb_main' => 'Wallet',
            'breadcrumb' => 'Top Up Detail',
            'title' => 'Wallet',
            'top_setting' => TopupSetting::first(),
            'total_balance' => $main_wallet_bls,
            'sub_wallets' => $sub_wallets,
            'ads' => Ads::where('status','pending')->where('client_id',$auth_id)->where('payment_status',0)->get(),
            'last_transaction_date' => $last_transaction_date,
            "nav_tab" => 'add_wallet',
            'tour' => $tour ?? null,
            'client_tour' => $client_tour ?? null,
            'first_wallet' => Ads::where('client_id', auth('web')->user()->id)->first() ?? null,
        );

        return view('client.wallet.add_top_up', $data);
    }

    function save(Request $request)
    {  //dd($request->all());
        if($request->amount <= 0){
            return ['error' => 'Please enter an amount greater than 0.'];
        }
        $rules = [
            'amount' => 'required',
            'deposit_slip.*' => 'required|mimes:jpeg,png,jpg|max:2048',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            $wallet = new WalletTopUp();
            $msg = [
                'success' => 'Wallet TopUp Added Successfully',
                'reload' => true,
            ];

            if ($request->hasFile('deposit_slip')) {
                $deposit_slips = [];
                foreach ($request->file('deposit_slip') as $file) {
                    $deposit_slip = uploadSingleFile($file, 'uploads/client/profile_images/', 'png,jpeg,jpg');
                    if (is_array($deposit_slip)) {
                        return response()->json($deposit_slip);
                    }
                    $deposit_slips[] = $deposit_slip;
                }

                $wallet->proof = implode(',', $deposit_slips);
            }

            $wallet->client_id = auth('web')->id();
            $wallet->transaction_id = session('paynow_transaction_id');
            $wallet->ad_id = $request->ad_id;
            $wallet->topup_type = 'manual';
            $wallet->topup_amount = $request->amount;
            $wallet->status =  'pending';
            $wallet->added_by = 'client';
            $wallet->added_by_id = '-';
            $wallet->save();

            if($request->ad_id == '' || $request->ad_id == null){
                $create =  ClientWallet::create([
                    'client_id' => auth('web')->id(),
                    'transaction_id' => session('paynow_transaction_id'),
                    'amount_in' => $request->amount,
                    'topup_type'  => 'paynow',
                ]);
            }else{
                $add_transaction = new Transections;
                $add_transaction->client_id = auth('web')->id();
                $add_transaction->transaction_id = session('paynow_transaction_id');
                $add_transaction->amount_in = $request->amount;
                $add_transaction->ads_id = $request->ad_id;
                $add_transaction->topup_type = 'paynow';
                $add_transaction->save();
            }


            session()->forget('paynow_transaction_id');

            $auth_client = auth('web')->user()->client_name;

            $title = "TopUp Add";
            $description = "$auth_client added TopUp to Wallet";
            $admins = Admin::where('user_type', 'admin')->pluck('id')->toArray();
            $client_id = hashids_encode(auth('web')->id());
            send_push_notification_to_admin($title, $description, $admins, $client_id);

            // Commit the transaction
            DB::commit();

            // Check if user has run the tour 'start 2'
            $tour = Tour::firstOrCreate(['code' => 'START_2'], ['name' => 'Get Started']);
            $client_tour = ClientTour::where(['client_id' => auth('web')->user()->id, 'tour_id' => $tour->id])->first();
            if ($client_tour) {
                // Check if user has run the tour
                $tour = Tour::firstOrCreate(['code' => 'START_3'], ['name' => 'Get Started']);
                $client_tour = ClientTour::where(['client_id' => auth('web')->user()->id, 'tour_id' => $tour->id])->first();
                if (!$client_tour) {
                    unset($msg['reload']);
                }
            }

            return response()->json($msg);
        } catch (\Exception $e) {
            // Something went wrong, rollback the transaction
            DB::rollback();

            // Log or handle the exception as needed
            return response()->json(['error' => 'Error TopUp: ' . $e->getMessage()], 500);
        }
    }

    function transaction_table()
    {
        $auth_id = auth('web')->id();

        $wallet = request()->input('wallet');

        $wallet_transactions = ClientWallet::with('ads')->where('client_id', $auth_id)->where('status', 'completed')->orderBy('updated_at', 'desc')->get();
        $ads_transactions = Transections::with('get_ads')->where('client_id', $auth_id)->where('status', 'completed')->orderBy('updated_at', 'desc')->get();

        $merged_transactions = $wallet_transactions->concat($ads_transactions);
        $final_array = $merged_transactions->sortByDesc('updated_at')->values()->all();

        if ($wallet == 'all') {
            $wallets_data = $final_array;
        }
        if ($wallet == 'main') {
            $wallets_data = $wallet_transactions;
        }
        if ($wallet != 'all' && $wallet != 'main') {
            $wallets_data = Transections::with('get_ads')->where('client_id', $auth_id)->where('ads_id', $wallet)->where('status', 'completed')->orderBy('updated_at', 'desc')->get();
        }

        return DataTables::of(collect($wallets_data))
            ->addIndexColumn()
            ->addColumn('wallet', function ($data) {
                if (isset($data->get_ads)) {
                    $wallet_name = 'Sub Wallet (' . @$data->get_ads->adds_title . ')';
                } else {
                    $wallet_name = 'Main Wallet';
                }
                return $wallet_name;
            })
            ->addColumn('amount_in', function ($data) {
                return $data->amount_in != '' ? get_price($data->amount_in) : '-';
            })
            ->addColumn('amount_out', function ($data) {
                return $data->amount_out != '' ? get_price($data->amount_out) : '-';
            })
            ->addColumn('description', function ($data) {

                if ($data->topup_type == 'add_to_subwallet') {
                    $ads_name = $data->ads?->adds_title ?? 'N/A';
                    return str_replace('_', ' ', ucfirst($data->topup_type)) . ' (' . $ads_name . ')';
                } elseif ($data->topup_type == 'closed_subwallet') {
                    $ads_name = $data->ads?->adds_title ?? 'N/A';
                    return str_replace('_', ' ', ucfirst($data->topup_type)) . ' (' . $ads_name . ')';
                } else {
                    if ($data->topup_type == 'back_to_wallet') {
                        $ads_name = $data->ads?->adds_title ?? 'N/A';
                        return str_replace('_', ' ', ucfirst($data->topup_type)) . ' (' . $ads_name . ')';
                    } else {
                        return str_replace('_', ' ', ucfirst($data->topup_type));
                    }
                }
            })
            ->addColumn('status', function ($data) {
                return '<span class="badge bg-success">Completed</span> ';
            })
            ->addColumn('created_at', function ($data) {
                return get_fulltime($data->updated_at);
            })
            ->filter(function ($query) {
                if (request()->input('search')) {
                    $query->where(function ($search_query) {
                        $search_query->whereLike(['subaccount_name'], request()->input('search'));
                    });
                }
            })
            ->rawColumns(['status'])
            ->make(true);
    }

    function transactions(Request $request){
        $auth_id = auth('web')->id();

        $wallet = ClientWallet::where('client_id', $auth_id);
        $main_wallet_bls = ($wallet->sum('amount_in') - $wallet->sum('amount_out'));

        $sub_wallet = Transections::where('client_id', $auth_id)->where('ads_id',hashids_decode($request->ads_id));
        $sub_wallet_remaining = ($sub_wallet->sum('amount_in') - $sub_wallet->sum('amount_out'));

        $data = array(
            'breadcrumb_main' => 'Sub Wallet',
            'breadcrumb' => 'Transactions',
            'title' => 'Transactions',
            'ads_id' => hashids_decode($request->ads_id),
            'main_wallet_bls' => $main_wallet_bls,
            'sub_wallet_budget' => Ads::where('id',hashids_decode($request->ads_id))->first(),
            'sub_wallet_remaining' => $sub_wallet_remaining,
        );
        return view('client.wallet.transactions', $data);
    }

    function add_topup_subwallet(Request $request){
        $auth_id = auth('web')->id();
        $ads = Ads::findOrFail($request->ads_id);
        if($ads->status == 'pending'){
            return ['error' => 'This ad status is pending You will be able to add balance after status change.'];
        }
        if($request->main_wallet_amt < $request->topup){
            return ['error' => 'You do not have enough balance to make a new ad request.'];
        }

        $wallet = new ClientWallet();
        $wallet->client_id = $auth_id;
        $wallet->ads_id =  $request->ads_id;
        $wallet->amount_out = $request->topup;
        $wallet->save();

        $add_transaction = new Transections;
        $add_transaction->client_id = $auth_id;
        $add_transaction->amount_in = $request->topup;
        $add_transaction->ads_id = $request->ads_id;
        $add_transaction->save();

        $ads->spend_amount = $ads->spend_amount+$request->topup;
        $ads->save();

        return response()->json([
            'success' => 'Balance add Successfully',
            'reload' => true,
        ]);
    }

    function sub_wallets_transactions(Request $request){
        $auth_id = auth('web')->id();
        return DataTables::of(Transections::where('client_id', $auth_id)
            ->where('ads_id', $request->ads_id))
                ->addIndexColumn()
                ->addColumn('amount_in', function ($data) {
                    return $data->amount_in != '' ? get_price($data->amount_in) : '-';
                })
                ->addColumn('transaction_id', function ($data) {
                    return '<span title="'.$data->transaction_id.'" style="cursor: pointer;"  onclick="copyToClipboard(\''.htmlspecialchars($data->transaction_id, ENT_QUOTES, 'UTF-8').'\')">' . Str::limit($data->transaction_id, 20, '...').'</span>';
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
                ->addColumn('status', function ($data) {
                    return '<span class="badge bg-'.pay_topup_badge($data->status).'">'.ucfirst($data->status).'</span>';
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
                ->rawColumns(['transaction_id','status'])
            ->make(true);
    }
    public function sub_wallets(Request $request)
    {
        $auth_id = auth('web')->id();
        $get_ads = Ads::where('client_id', $auth_id)->latest()->get();
        $data = array(
            'breadcrumb' => 'Sub Wallets',
            'title' => "Sub Wallets",
            'get_ads' => $get_ads
        );

        return view('client.wallet.all_wallets')->with($data);


    }

    function transfer_funds(){
        $auth_id = auth('web')->id();
        $wallet = ClientWallet::where('client_id', $auth_id)->where('status','completed');
        $main_wallet_bls = ($wallet->sum('amount_in') - $wallet->sum('amount_out'));
        $last_transaction_date = $wallet->orderBy('created_at', 'desc')->value('updated_at');
        $from_sub_wallets = Ads::where('client_id', $auth_id)->where('status','!=','running')->where('status','!=','close')->latest()->get();
        $to_sub_wallets = Ads::where('client_id', $auth_id)->where('status','!=','close')->latest()->get();

        // Check if user has run the tour 'after topup'
        $tour = Tour::firstOrCreate(['code' => 'AFTER_TOPUP'], ['name' => 'After Topup']);
        $client_tour = ClientTour::where(['client_id' => auth('web')->user()->id, 'tour_id' => $tour->id])->first();
        if ($client_tour) {
            // Check if user has run the tour
            $tour = Tour::firstOrCreate(['code' => 'FINISH_1'], ['name' => 'Finish']);
            $client_tour = ClientTour::where(['client_id' => auth('web')->user()->id, 'tour_id' => $tour->id])->first();
        } else {
            $tour = null;
            $client_tour = null;
        }

        $data = array(
            'breadcrumb_main' => 'Transfer Funds',
            'breadcrumb' => 'Transfer Funds',
            'title' => 'Wallet',
            'total_balance' => $main_wallet_bls,
            'from_sub_wallets' => $from_sub_wallets,
            'to_sub_wallets' => $to_sub_wallets,
            'last_transaction_date' => $last_transaction_date,
            "nav_tab" => 'transfer_funds',
            'tour' => $tour ?? null,
            'client_tour' => $client_tour ?? null,
            'wallet' => WalletTopUp::where('client_id', auth('web')->id())->first() ?? null, // identify user first topup
        );

        return view('client.wallet.transfer_funds', $data);
    }

    function funds_save(Request $request){

        if($request->spend_amount <= 0){
            return ['error' => 'Please enter an amount greater than 0.'];
        }

        if($request->form_wallet == $request->to_wallet){
            return ['error' => 'Transfer failed! You cannot transfer funds between the same wallet. Please select a different wallet.'];
        }

        $auth_id = auth('web')->id();
        if($request->form_wallet == 'main_wallet'){
            $wallet = ClientWallet::where('client_id', $auth_id)->where('status','completed');
            $main_wallet_bls = ($wallet->sum('amount_in') - $wallet->sum('amount_out'));

            if($main_wallet_bls < $request->spend_amount){
                return ['error' => 'You do not have enough balance to make a Transfer Funds request.'];
            }
            $ads = Ads::findOrFail($request->to_wallet); 
            $wallet = new ClientWallet();
            $wallet->client_id = $auth_id;
            $wallet->ads_id =  $request->to_wallet;
            $wallet->amount_out = $request->spend_amount;
            $wallet->topup_type = 'add_to_subwallet';
            $wallet->status = 'completed';
            $wallet->save();

            $add_transaction = new Transections;
            $add_transaction->client_id = $auth_id;
            $add_transaction->amount_in = $request->spend_amount;
            $add_transaction->ads_id = $request->to_wallet;
            $add_transaction->topup_type = 'add_from_main_wallet';
            $add_transaction->status = "completed";
            $add_transaction->save();
            
            $price = 0;
            if($ads->domain_is == 'request_to_purchase' && $ads->is_domain_pay == 0 ){
                $price = $price+20;
                $add_transaction = new Transections;
                $add_transaction->client_id = $ads->client_id;
                $add_transaction->amount_out = 20;
                $add_transaction->ads_id = $ads->id;
                $add_transaction->topup_type = 'domain_payment';
                $add_transaction->status = 'completed';
                $add_transaction->save();
                $ads->is_domain_pay = 1;
            }
            if($ads->hosting_is == 'request_to_purchase_hosting' && $ads->is_hosting_pay == 0 ){
                $price = $price+15;
                $add_transaction = new Transections;
                $add_transaction->client_id = $ads->client_id;
                $add_transaction->amount_out = 15;
                $add_transaction->ads_id = $ads->id;
                $add_transaction->topup_type = 'hosting_payment';
                $add_transaction->status = 'completed';
                $add_transaction->save();
                $ads->is_hosting_pay = 1;
            }

            $ads->spend_amount = $ads->spend_amount+$request->spend_amount-$price;
            $ads->save();

        }if($request->to_wallet == 'main_wallet'){
            $ads = Ads::findOrFail($request->form_wallet);
            if($ads->spend_amount < $request->spend_amount){
                return ['error' => 'You do not have enough balance to make a Transfer Funds request.'];
            }
 
            $wallet = new ClientWallet();
            $wallet->client_id = $auth_id;
            $wallet->ads_id =  $request->form_wallet;
            $wallet->amount_in = $request->spend_amount;
            $wallet->topup_type = 'back_to_wallet';
            $wallet->status = 'completed';
            $wallet->save();

            $add_transaction = new Transections;
            $add_transaction->client_id = $auth_id;
            $add_transaction->amount_out = $request->spend_amount;
            $add_transaction->ads_id = $request->form_wallet;
            $add_transaction->topup_type = 'back_to_main_wallet';
            $add_transaction->status = "completed";
            $add_transaction->save();
            if($ads->spend_type == 'daily'){
                $ad_amt = $ads->daily_budget*30;
            }else{
                $ad_amt = $ads->daily_budget;
            }
            
            if($ad_amt > ($ad_amt-$request->spend_amount)){
                $ads->payment_status = 0;
            }
            $ads->spend_amount = $ads->spend_amount-$request->spend_amount;
            $ads->save();
        }if($request->form_wallet != 'main_wallet' && $request->to_wallet != 'main_wallet'){
            $form_ad = Ads::findOrFail($request->form_wallet);

            if($form_ad->spend_amount < $request->spend_amount){
                return ['error' => 'You do not have enough balance to make a Transfer Funds request.'];
            }

            $add_transaction = new Transections;
            $add_transaction->client_id = $auth_id;
            $add_transaction->amount_out = $request->spend_amount;
            $add_transaction->ads_id = $request->form_wallet;
            $add_transaction->topup_type = 'transfer_to_subwallet';
            $add_transaction->status = "completed";
            $add_transaction->form_wallet_id = $request->form_wallet;
            $add_transaction->to_wallet_id = $request->to_wallet;
            $add_transaction->save();

            if($form_ad->spend_type == 'daily'){
                $ad_amt = $form_ad->daily_budget*30;
            }else{
                $ad_amt = $form_ad->daily_budget;
            }
            
            if($ad_amt > ($ad_amt-$request->spend_amount)){
                $form_ad->payment_status = 0;
            }
            $form_ad->spend_amount = $form_ad->spend_amount-$request->spend_amount;
            $form_ad->save();

            $to_ad = Ads::findOrFail($request->to_wallet);

            $add_transaction = new Transections;
            $add_transaction->client_id = $auth_id;
            $add_transaction->amount_in = $request->spend_amount;
            $add_transaction->ads_id = $request->to_wallet;
            $add_transaction->topup_type = 'transfer_from_subwallet';
            $add_transaction->status = "completed";
            $add_transaction->form_wallet_id = $request->form_wallet;
            $add_transaction->to_wallet_id = $request->to_wallet;
            $add_transaction->save();

            $price = 0;
            if($to_ad->domain_is == 'request_to_purchase' && $to_ad->is_domain_pay == 0 ){
                // Pause for 1 seconds
                sleep(1);
                $price = $price+20;
                $add_transaction = new Transections;
                $add_transaction->client_id = $to_ad->client_id;
                $add_transaction->amount_out = 20;
                $add_transaction->ads_id = $to_ad->id;
                $add_transaction->topup_type = 'domain_payment';
                $add_transaction->status = 'completed';
                $add_transaction->save();
                $to_ad->is_domain_pay = 1;
            }
            if($to_ad->hosting_is == 'request_to_purchase_hosting' && $to_ad->is_hosting_pay == 0 ){
                // Pause for 1 seconds
                sleep(1);
                $price = $price+15;
                $add_transaction = new Transections;
                $add_transaction->client_id = $to_ad->client_id;
                $add_transaction->amount_out = 15;
                $add_transaction->ads_id = $to_ad->id;
                $add_transaction->topup_type = 'hosting_payment';
                $add_transaction->status = 'completed';
                $add_transaction->save();
                $to_ad->is_hosting_pay = 1;
            }

            $to_ad->spend_amount = $to_ad->spend_amount+$request->spend_amount-$price;
            $to_ad->save();
        }


        $trans_funds = new TransferFund();
        $trans_funds->client_id = $auth_id;
        $trans_funds->amount = $request->spend_amount;
        $trans_funds->from_wallet_id = ($request->form_wallet === 'main_wallet') ? 0 : $request->form_wallet;
        $trans_funds->to_wallet_id = ($request->to_wallet === 'main_wallet') ? 0 : $request->to_wallet;
        $trans_funds->status = 'approved';
        $trans_funds->save();


        $msg = [
            'success' => 'Funds add Successfully',
            'reload' => true,
        ];

        // Check if user has run the tour
        $tour = Tour::firstOrCreate(['code' => 'AFTER_TOPUP'], ['name' => 'After Topup']);
        $client_tour = ClientTour::where(['client_id' => auth('web')->user()->id, 'tour_id' => $tour->id])->first();
        if ($client_tour) {
            $tour = Tour::firstOrCreate(['code' => 'FINISH_1'], ['name' => 'Finish']);
            $client_tour = ClientTour::where(['client_id' => auth('web')->user()->id, 'tour_id' => $tour->id])->first();
            if (!$client_tour) {
                unset($msg['reload']);
            }
        }

        return response()->json($msg);

        die();
    }


    function view_fund_transections(){
        $auth_id = auth('web')->id();
        return DataTables::of(TransferFund::with(['to_wallet', 'form_wallet'])->where('client_id', $auth_id)->latest())
                ->addIndexColumn()
                ->addColumn('date', function ($data) {
                    return get_fulltime($data->created_at);
                })
                ->addColumn('from_wallet_id', function ($data) {
                    if(empty($data->form_wallet)){
                        return "Main Wallet";
                    }else{
                        return ucfirst($data->form_wallet->adds_title);
                    }
                })
                ->addColumn('to_wallet_id', function ($data) {
                    if(empty($data->to_wallet)){
                        return "Main Wallet";
                    }else{
                        return ucfirst($data->to_wallet->adds_title);
                    }
                   
                })
                ->addColumn('amount', function ($data) {
                    return get_price($data->amount);
                })
                ->addColumn('status', function ($data) {
                    return $data->status;
                })

                ->filter(function ($query) {
                    if (request()->input('search')) {
                        $query->where(function ($search_query) {
                            $search_query->whereLike(['status','amount'], request()->input('search'));
                        });
                    }
                })
                ->orderColumn('DT_RowIndex', function ($q, $o) {
                    $q->orderBy('id', $o);
                })
            ->make(true);
    }


    function transaction_report(Request $request){
        $auth_id = auth('web')->id();
        $wallet_transactions = ClientWallet::where('client_id', $auth_id)
        ->whereIn('topup_type', ['stripe', 'paynow'])
        ->orderBy('created_at', 'desc')
        ->get();

        $ads_transactions = Transections::with('get_ads')->where('client_id', $auth_id)
        ->whereIn('topup_type', ['stripe', 'paynow'])
        ->orderBy('created_at', 'desc')
        ->get();

        $merged_transactions = $wallet_transactions->concat($ads_transactions);
        $final_array = $merged_transactions->sortByDesc('created_at')->values()->all();

        if ($request->ajax()) {
            return DataTables::of(ClientWallet::query()->where('client_id', $auth_id)
            ->where('amount_in', '!=', '')->latest())
                ->addIndexColumn()
                ->addColumn('amount', function ($data) {
                    return get_price($data->amount_in);
                })
                ->addColumn('topup_type', function ($data) {
                    return str_replace('_',' ',ucfirst($data->topup_type));
                })
                ->addColumn('created_at', function ($data) {
                    return get_fulltime($data->created_at);
                })
                ->filter(function ($query) {
                    if (request()->input('search')) {
                        $query->where(function ($search_query) {
                            $search_query->whereLike(['status','amount_in'], request()->input('search'));
                        });
                    }
                })
                ->orderColumn('DT_RowIndex', function ($q, $o) {
                    $q->orderBy('id', $o);
                })
            ->make(true);
        }

        // Check if user has run the tour 'start 3'
        $tour = Tour::firstOrCreate(['code' => 'START_3'], ['name' => 'Get Started']);
        $client_tour = ClientTour::where(['client_id' => auth('web')->user()->id, 'tour_id' => $tour->id])->first();
        if ($client_tour) {
            // Check if user has run the tour
            $tour = Tour::firstOrCreate(['code' => 'AFTER_TOPUP'], ['name' => 'After Topup']);
            $client_tour = ClientTour::where(['client_id' => auth('web')->user()->id, 'tour_id' => $tour->id])->first();
        } else {
            $tour = null;
            $client_tour = null;
        }

        $data = array(
            'breadcrumb_main' => 'Transaction Report',
            'breadcrumb' => 'Transaction Report',
            'title' => 'Transaction Report',
            "nav_tab" => 'transaction_report',
            "wallet_transactions" => $final_array,
            "paynow_requests" => WalletTopUp::where('status','!=','approve')->where('client_id',$auth_id)->latest()->get(),
            "subwallets" => ads::where('client_id',$auth_id)->latest()->get(),
            'tour' => $tour ?? null,
            'client_tour' => $client_tour ?? null,
        );
        return view('client.wallet.transaction_report', $data);
    }

    function add_paynow_transaction_id() {
        $transactionId = strtotime(now());
        session(['paynow_transaction_id' => $transactionId]);
        return $transactionId;
    }

    function walletClose(Request $request)
    { 
        $id = $request->input('id'); 
        $ads_delete = Ads::findOrFail($id);

        $wallet = new ClientWallet();
        $wallet->client_id = $ads_delete->client_id;
        $wallet->ads_id =  $ads_delete->id;
        $wallet->amount_in = $ads_delete->spend_amount;
        $wallet->topup_type = 'closed_subwallet';
        $wallet->status = 'completed';
        $wallet->save();

        // Monthly payment
        $monthly_payment = $this->monthly_client_payment($ads_delete->client_id, $ads_delete->id);
        
        // Fetch 1 Topups of client that FeeFlag = False  
        $topupFee = 0;

        $topups = Transections::where('ads_id', $ads_delete->id)
            ->where('fee_flag', 0)
            ->whereIn('topup_type', ['stripe'])
            ->where('status', 'completed')
            ->get();
    
        // Check sub wallet
        if ($topups->isEmpty()) {
            // Change to main wallet
            $topups = ClientWallet::where('client_id', $ads_delete->client_id)
                ->where('fee_flag', 0)
                ->whereIn('topup_type', ['stripe'])
                ->where('status', 'completed')
                ->limit(1)
                ->get();
        }

        if ($topups->isNotEmpty()) {
            foreach ($topups as $topup) {
                if ($topup->topup_type == 'stripe') {
                    $topupFee = round($topup->amount_in * ( 3.9 / 100) + 0.60);
                }
            }
            $topup->transaction_fee = $topupFee;
            $topup->save();
        }

        if ($monthly_payment <= 0) {
            $ads_delete->status = 'close';
            $ads_delete->save();

            return response()->json([
                'success' => 'Ads close Successfully',
                'redirect' => route('user.wallet.add'),
            ]);
        }

        $totalAmount = $monthly_payment + $topupFee;
        
        if ($totalAmount <= 0) {
            $ads_delete->status = 'close';
            $ads_delete->save();

            return response()->json([
                'success' => 'Ads close Successfully',
                'redirect' => route('user.wallet.add'),
            ]);
        }

        // Check balance & budget
        $transactions = Transections::where('client_id', $ads_delete->client_id)
            ->where('ads_id', $ads_delete->id)
            ->where('status', 'completed');
        $balance = ($transactions->sum('amount_in') - $transactions->sum('amount_out'));
        if ($ads_delete->spend_type == 'daily') {
            $budget = ($ads_delete->daily_budget * 30) ?? $ads_delete->daily_budget;
        }
        $remaining_amount = $balance < $budget ? $balance : $budget;
        $available_balance = $remaining_amount - $totalAmount;

        $gst = (($totalAmount - $topupFee) * (9 / 100));
        
        $add_transaction = new Transections;
        $add_transaction->client_id = $ads_delete->client_id;
        $add_transaction->amount_out = $ads_delete->spend_amount;
        $add_transaction->ads_id = $ads_delete->id;
        $add_transaction->topup_type = 'closed_and_back_to_main_wallet';
        $add_transaction->status = 'completed';
        $add_transaction->save();
    
        $ads_delete->spend_amount = $available_balance;
        $ads_delete->save();

        // Set the Fee Flag true as Fee is deducted
        if ($topup) {
            $topup->fee_flag = true;
            $topup->save();
        }
        
        // Generate Invoice
        $dates = $this->monthly_user_start_end_time($ads_delete->client_id, $ads_delete->id, 'ppc');
        $monthStartDate = $dates['start_date']->format('Y-m-d');
        $monthEndDate = $dates['end_date']->format('Y-m-d');

        $ads_invoice = new AdsInvoice;
        $ads_invoice->ads_id = $ads_delete->id;
        $ads_invoice->client_id = $ads_delete->client_id;
        $ads_invoice->invoice_date = date('Y-m-d');
        $ads_invoice->card_charge = $topupFee;
        $ads_invoice->gst = $gst;
        $ads_invoice->total_amount = $totalAmount;
        $ads_invoice->total_lead = $this->monthly_client_leads($ads_delete->client_id, $ads_delete->id, 'ppc');
        $ads_invoice->start_date = $monthStartDate;
        $ads_invoice->end_date = $monthEndDate;
        $ads_invoice->save();

        $ads_delete->spend_amount = 0;
        $ads_delete->status = 'close';
        $ads_delete->save();

        return response()->json([
            'success' => 'Ads close Successfully',
            'redirect' => route('user.wallet.add'),
        ]);
    }
}
