<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Models\ClientWallet;
use App\Models\PaynowRequests;
use App\Traits\PayNowTrait;
use Illuminate\Http\Request;

/**
 * @group Wallet
 *
 * @subgroup PayNow
 *
 * @authenticated
 */
class PayNowController extends Controller
{
    use PayNowTrait;

    public function paynow_webhook(Request $request)
    {
        $user_id = auth('api')->id();
        ActivityLogHelper::save_activity($user_id, 'Received PayNow webhook notification.', 'PayNowController', 'app');

        // Get raw POST data
        $raw_post_data = $request->getContent();

        // Parse URL-encoded data
        parse_str($raw_post_data, $parsed_data);

        if ($parsed_data['status'] == 'completed') {
            $payment_request_id = $parsed_data['payment_request_id'];
            $PaynowRequests = PaynowRequests::where('request_id', $payment_request_id)->where('status', 'pending')->first(); // dd($PaynowRequests['data']->reference_number);

            ActivityLogHelper::save_activity($user_id, "Payment completed, updating wallet for request ID: [$payment_request_id]", 'PayNowController', 'app');

            $create = ClientWallet::create([
                'client_id' => $PaynowRequests['data']->reference_number,
                'transaction_id' => $PaynowRequests['data']->id,
                'amount_in' => str_replace(',', '', $PaynowRequests['data']->amount),
                'topup_type' => 'paynow',
                'data' => $PaynowRequests,
            ]);
        }
    }

    public function payNowCheckout(Request $request)
    {
        $price = $request->price;
        $client = auth('api')->user();
        $user_id = auth('api')->id();

        ActivityLogHelper::save_activity($user_id, "Initiated PayNow checkout with amount: $price", 'PayNowController', 'app');

        $data = [
            'amount' => $price,
            'currency' => 'SGD',
            'email' => $client->email,
            'reference_number' => $client->id,
            'redirect_url' => route('user.paynow.checkout.success'),
            'webhook' => route('api_paynow_webhook'),
        ];

        $payment_request = $this->createPaymentRequest($data);
        $PaynowRequests = new PaynowRequests;
        $PaynowRequests->request_id = $payment_request->id;
        $PaynowRequests->data = $payment_request;
        $PaynowRequests->save();

        ActivityLogHelper::save_activity($user_id, 'Created PayNow payment request with ID: '.$payment_request->id, 'PayNowController', 'app');

        // TODO: Change to sendSuccessResponse
        return response()->json([
            'status' => 'success',
            'payment_url' => $payment_request->url,
        ]);
    }

    public function paynowCheckoutSuccess()
    {
        $user_id = auth('api')->id();
        ActivityLogHelper::save_activity($user_id, 'PayNow checkout completed successfully.', 'PayNowController', 'app');

        sleep(15);

        // TODO: Change to sendSuccessResponse
        return response()->json([
            'status' => 'success',
            'message' => 'Top Up Added Successfully',
        ]);
    }
}
