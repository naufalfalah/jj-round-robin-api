<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\ClientWallet;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\WalletTopUp;
use App\Models\PaynowRequests;

class PayNowPaymentController extends Controller
{
    function payNowCheckout(Request $request){
        $client = auth('web')->user();
       
        $data = [
            'amount' => $request->price,
            'currency' => 'SGD',
            'email' => $client->email,
            'reference_number' => $client->id,
            'redirect_url' => route('user.paynow.checkout.success'),
            'webhook' => route('api_paynow_webhook')
        ];

        $payment_request = $this->Create_Payment_Request($data);  
        $PaynowRequests = new PaynowRequests();
        $PaynowRequests->request_id = $payment_request->id;
        $PaynowRequests->data = $payment_request;
        $PaynowRequests->save();
        
        return redirect($payment_request->url);
    }

    function Create_Payment_Request($data){
        
        $curl = curl_init();

        curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.sandbox.hit-pay.com/v1/payment-requests",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "X-BUSINESS-API-KEY:". env('PAYNOW_SECRET')
        ],
        CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return json_decode($response);
        }
    }

    function paynowCheckoutSuccess(){
        sleep(15); 
        session()->flash('success', 'Top Up Added Successfully');
        return redirect()->route('user.wallet.add');
    }
}