<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Models\Ads;
use App\Models\ClientTour;
use App\Models\ClientWallet;
use App\Models\Tour;
use App\Models\Transections;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @group Wallet
 *
 * @subgroup Stripe
 *
 * @authenticated
 */
class StripeController extends Controller
{
    public function stripeCheckout($price, $product, $ad_id = '')
    {
        try {
            $user_id = auth('api')->id();
            $get_email = auth('api')->user()->email;

            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

            $redirectUrl = route('api.stripe.checkout.success', ['price' => $price, 'session_id' => '{CHECKOUT_SESSION_ID}']);

            $response = $stripe->checkout->sessions->create([
                'success_url' => $redirectUrl,
                'customer_email' => $get_email,
                'payment_method_types' => ['link', 'card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'product_data' => [
                                'name' => 'TopUp',
                                'description' => '4% card charge will be deducted from your total amount',
                            ],
                            'unit_amount' => 100 * $price,
                            'currency' => 'SGD',
                        ],
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'allow_promotion_codes' => true,
                'metadata' => [
                    'ad_id' => $ad_id,
                ],
            ]);

            ActivityLogHelper::save_activity($user_id, "Initiated Stripe checkout for $product with price $price", 'StripeController', 'app');

            // TODO: Change to sendSuccessResponse
            return response()->json(['payment_url' => $response['url']], 200);
        } catch (\Exception $e) {
            ActivityLogHelper::save_activity(auth('api')->id(), 'Stripe checkout error: '.$e->getMessage(), 'StripeController', 'app');

            // TODO: Change to sendErrorResponse
            return response()->json(['error' => 'An error occurred during checkout: '.$e->getMessage()], 500);
        }
    }

    public function stripeCheckoutSuccess(Request $request)
    {
        $price = $request->price;

        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        $response = $stripe->checkout->sessions->retrieve($request->session_id);
        $customerEmail = $response->customer_email;

        $user = User::where('email', $customerEmail)->first();
        $user_id = $user->id;

        if (empty($response->metadata->ad_id)) {
            ClientWallet::create([
                'client_id' => $user_id,
                'transaction_id' => $response->id,
                'amount_in' => $price,
                'topup_type' => 'stripe',
                'data' => $response,
                'status' => 'completed',
            ]);

            // Log the top-up success
            ActivityLogHelper::save_activity($user_id, "Stripe top-up completed for client [$customerEmail] with amount $price", 'StripeController', 'app');
        } else {
            $ads = Ads::find($response->metadata->ad_id);
            $this->handleAdTransactions($user, $response, $price, $ads);

            // Log the ad payment transaction
            $ad_id = $ads->id;
            ActivityLogHelper::save_activity($user_id, "Ad payment completed for client [$customerEmail] and Ad ID $ad_id", 'StripeController', 'app');
        }

        $this->markClientTourAsComplete($user);

        // Log the client tour completion
        ActivityLogHelper::save_activity($user_id, "Client tour completed for client [$customerEmail]", 'StripeController', 'app');

        // TODO: Change to sendSuccessResponse
        return response()->json([
            'status' => 'success',
            'message' => 'Top Up Added Successfully',
        ], 200);
    }

    private function handleAdTransactions($user, $response, &$price, $ads)
    {
        $user_id = $user->id;
        $ads_id = $ads->id;

        Transections::create([
            'client_id' => $user_id,
            'transaction_id' => $response->id,
            'amount_in' => $price,
            'ads_id' => $ads_id,
            'topup_type' => 'stripe',
            'status' => 'completed',
        ]);

        // Log the ad transaction
        ActivityLogHelper::save_activity($user_id, "Ad transaction created for ad ID $ads_id with amount $price", 'StripeController', 'app');

        // Handle domain payment if necessary
        if ($ads->domain_is == 'request_to_purchase' && $ads->is_domain_pay == 0) {
            sleep(1);
            $price -= 20;
            Transections::create([
                'client_id' => $user->id,
                'amount_out' => 20,
                'ads_id' => $ads->id,
                'topup_type' => 'domain_payment',
                'status' => 'completed',
            ]);
            $ads->is_domain_pay = 1;
        }

        // Log the domain payment
        ActivityLogHelper::save_activity($user_id, "Domain payment of 20 completed for ad ID $ads_id", 'StripeController', 'app');

        // Handle hosting payment if necessary
        if ($ads->hosting_is == 'request_to_purchase_hosting' && $ads->is_hosting_pay == 0) {
            sleep(1);
            $price -= 15;
            Transections::create([
                'client_id' => $user->id,
                'amount_out' => 15,
                'ads_id' => $ads->id,
                'topup_type' => 'hosting_payment',
                'status' => 'completed',
            ]);
            $ads->is_hosting_pay = 1;

            // Log the hosting payment
            ActivityLogHelper::save_activity($user_id, "Hosting payment of 15 completed for ad ID  $ads_id", 'StripeController', 'app');
        }

        // Update ad spending and status
        $total_amt = $ads->spend_amount + $price;
        $amt = $ads->spend_type == 'daily' ? $ads->daily_budget * 30 : $ads->daily_budget;
        $ads->payment_status = ($total_amt >= $amt) ? 1 : $ads->payment_status;
        $ads->spend_amount = $total_amt;
        $ads->save();

        // Log the ad spending update
        ActivityLogHelper::save_activity($user_id, "Ad spend updated for ad ID $ads_id with total spend ".$ads->spend_amount, 'StripeController', 'app');
    }

    private function markClientTourAsComplete($user)
    {
        $tour = Tour::firstOrCreate(['code' => 'START_3'], ['name' => 'Get Started']);
        ClientTour::firstOrCreate([
            'client_id' => $user->id,
            'tour_id' => $tour->id,
        ]);
    }
}
