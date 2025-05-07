<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\PaynowRequests;
use App\Models\ClientWallet;
class PayNowController extends Controller
{
    function paynow_webhook(Request $request){
        // Get raw POST data
        $raw_post_data = $request->getContent();

        // Parse URL-encoded data
        parse_str($raw_post_data, $parsed_data);

        if($parsed_data['status'] == 'completed'){
            $PaynowRequests = PaynowRequests::where('request_id',$parsed_data['payment_request_id'])->where('status','pending')->first();// dd($PaynowRequests['data']->reference_number);
           
            $create =  ClientWallet::create([
                'client_id' => $PaynowRequests['data']->reference_number,
                'transaction_id' => $PaynowRequests['data']->id,
                'amount_in' => str_replace(',', '', $PaynowRequests['data']->amount),
                'topup_type'  => 'paynow',
                'data' => $PaynowRequests
                
            ]);
        }        
    }    
}