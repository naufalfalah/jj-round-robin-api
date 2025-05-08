<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ads;
use App\Models\ClientTour;
use App\Models\ClientWallet;
use App\Models\TopupSetting;
use App\Models\Tour;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

/**
 * @group Wallet
 *
 * @subgroup Main Wallet & Sub Wallet
 *
 * @authenticated
 */
class WalletController extends Controller
{
    public function __construct()
    {
        $this->middleware('check.menu:wallet');
    }

    /**
     * Get Main Wallet Balance
     */
    public function getMainWalletBalance(Request $request)
    {
        $clientId = auth('api')->id();
        $wallet = ClientWallet::where('client_id', $clientId)
            ->where('status', 'completed');

        $totalBalance = $wallet->sum('amount_in') - $wallet->sum('amount_out');
        $lastTransactionDate = $wallet->orderBy('created_at', 'desc')->value('updated_at');

        // TODO: Change to sendSuccessResponse
        return response()->json([
            'total_balance' => $totalBalance,
            'last_transaction_date' => $lastTransactionDate,
        ]);
    }

    /**
     * Get Sub Wallets
     */
    public function getSubWallets(Request $request)
    {
        $clientId = auth('api')->id();
        $sub_wallets = Ads::where('client_id', $clientId)
            ->latest()->get();

        // TODO: Change to sendSuccessResponse
        return response()->json([
            'sub_wallets' => $sub_wallets,
        ]);
    }

    public function getTopUpTransactions(Request $request)
    {
        $auth_id = auth('api')->id();

        if ($request->ajax()) {
            $query = ClientWallet::query()->where('client_id', $auth_id)
                ->where('amount_in', '!=', '')
                ->latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('amount', fn ($data) => get_price($data->amount_in))
                ->addColumn('topup_type', fn ($data) => str_replace('_', ' ', ucfirst($data->topup_type)))
                ->addColumn('created_at', fn ($data) => get_fulltime($data->created_at))
                ->filter(function ($query) {
                    if (request()->input('search')) {
                        $query->where(function ($search_query) {
                            $search_query->whereLike(['status', 'amount_in'], request()->input('search'));
                        });
                    }
                })
                ->orderColumn('DT_RowIndex', fn ($q, $o) => $q->orderBy('id', $o))
                ->make(true);
        }

        // TODO: Change to sendErrorResponse
        return response()->json(['error' => 'Invalid request'], 400);
    }

    public function checkUserTourStatus()
    {
        $auth_id = auth('api')->id();
        $tour_codes = ['START_1', 'START_2', 'START_3', 'AFTER_TOPUP'];
        $completed_tours = [];

        foreach ($tour_codes as $code) {
            $tour = Tour::firstOrCreate(['code' => $code], ['name' => 'Get Started']);
            $client_tour = ClientTour::where(['client_id' => $auth_id, 'tour_id' => $tour->id])->first();
            $completed_tours[$code] = $client_tour ? true : false;
        }

        // TODO: Change to sendSuccessResponse
        return response()->json($completed_tours);
    }

    public function getWalletPageData()
    {
        $auth_id = auth('api')->id();

        // TODO: Change to sendSuccessResponse
        return response()->json([
            'top_setting' => TopupSetting::first(),
            'pending_ads' => Ads::where('status', 'pending')->where('client_id', $auth_id)->where('payment_status', 0)->get(),
            'first_wallet' => Ads::where('client_id', $auth_id)->first(),
        ]);
    }
}
