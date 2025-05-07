<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\ClientTour;
use App\Models\ClientWallet;
use App\Models\Tour;
use App\Models\User;
use App\Models\Ads;
use App\Models\Transections;
use Illuminate\Http\Request;
use Stripe;
use App\Models\WalletTopUp;

class StripePaymentController extends Controller
{
    public function stripeCheckout($price,$product,$ad_id = '')
    { 
        try {
            $get_email = auth('web')->user()->email;

            $stripe = new \Stripe\StripeClient((env('STRIPE_SECRET')));

            $redirectUrl = route('user.stripe.checkout.success', ['price' => $price]) . '&session_id={CHECKOUT_SESSION_ID}';


            $response = $stripe->checkout->sessions->create([
                'success_url' => $redirectUrl,

                'customer_email' => $get_email,

                'payment_method_types' => ['link', 'card'],

                'line_items' => [
                    [
                        'price_data' => [
                            'product_data' => [
                                'name' => 'TopUp',
                                'description' => '4% card charge will be deducted from your total amount',
                            ],
                            'unit_amount' => 100 * $price,
                            'currency' => 'SGD',
                        ],
                        'quantity' => 1
                    ],
                ],

                'mode' => 'payment',
                'allow_promotion_codes' => true,
                'metadata' => [
                    'ad_id' => $ad_id
                ],
            ]);


            return redirect($response['url']);

        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred during checkout: ' . $e->getMessage());
        }
    }

    public function stripeCheckoutSuccess(Request $request)
    {
        $price = $request->price;
        $fee = $request->fee;

        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

        $response = $stripe->checkout->sessions->retrieve($request->session_id);
        $ads = Ads::find($response->metadata->ad_id);

        $customerEmail = $response->customer_email;

        $user = User::where('email', $customerEmail)->first();
        if($response->metadata == '' || $response->metadata->ad_id == 0){
            $create =  ClientWallet::create([
                'client_id' => $user->id,
                'transaction_id' => $response->id,
                'amount_in' => $price,
                'topup_type'  => 'stripe',
                'data' => $response,
                'status' => 'completed'
                
            ]);
        }else{
            $ads = Ads::find($response->metadata->ad_id);
        
            $add_transaction = new Transections;
            $add_transaction->client_id = $user->id;
            $add_transaction->transaction_id = $response->id;
            $add_transaction->amount_in = $price;
            $add_transaction->ads_id = $ads->id;
            $add_transaction->topup_type = 'stripe';
            $add_transaction->status = 'completed';
            $add_transaction->save();
            
            if($ads->domain_is == 'request_to_purchase' && $ads->is_domain_pay == 0){
                // Pause for 1 seconds
                sleep(1);
                $price = $price-20;
                $add_transaction = new Transections;
                $add_transaction->client_id = $user->id;
                // $add_transaction->transaction_id = $response->id;
                $add_transaction->amount_out = 20;
                $add_transaction->ads_id = $ads->id;
                $add_transaction->topup_type = 'domain_payment';
                $add_transaction->status = 'completed';
                $add_transaction->save();
                $ads->is_domain_pay = 1;
            }
            if($ads->hosting_is == 'request_to_purchase_hosting' && $ads->is_hosting_pay == 0){
                // Pause for 1 seconds
                sleep(1);
                $price = $price-15;
                $add_transaction = new Transections;
                $add_transaction->client_id = $user->id;
                // $add_transaction->transaction_id = $response->id;
                $add_transaction->amount_out = 15;
                $add_transaction->ads_id = $ads->id;
                $add_transaction->topup_type = 'hosting_payment';
                $add_transaction->status = 'completed';
                $add_transaction->save();
                $ads->is_hosting_pay = 1;
            }
          
            $total_amt = $ads->spend_amount+$price; 
            if($ads->spend_type == 'daily'){
                $amt = $ads->daily_budget*30;
            }else{
                $amt = $ads->daily_budget;
            }
           
            if($total_amt == $amt || $total_amt > $amt){ 
                $ads->payment_status = 1;
            }

            $ads->spend_amount = $total_amt;
            $ads->save();
        }

        // Set user finish topup tour
        $tour = Tour::firstOrCreate(['code' => 'START_3'], ['name' => 'Get Started']);
        ClientTour::firstOrCreate([
            'client_id' => $user->id,
            'tour_id' => $tour->id,
        ]);

        session()->flash('success', 'Top Up Added Successfully');
        return redirect()->route('user.wallet.add');
    }
}
