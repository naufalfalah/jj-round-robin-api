<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\FetchLeadsController;
use App\Models\Admin;
use App\Models\Ads;
use App\Models\AdsInvoice;
use App\Models\ClientWallet;
use App\Models\DailyAdsSpent;
use App\Models\GoogleAccount;
use App\Models\LeadClient;
use App\Models\Transections;
use App\Services\GoogleAdsService;
use App\Traits\AdsSpentTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckDailyAdsSpent extends Command
{
    use AdsSpentTrait;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily-ads-spents:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::channel('daily_ads_spent')->info('Run check daily ads spent');
        $today = Carbon::today();
        $date = $today->format('Y-m-d');
        
        // Check all running ads
        $ads = Ads::whereHas('client')->whereIn('status', ['running', 'test'])->get();
        foreach ($ads as $ad) {
            if ($ad->status !== 'test') {
                // Update daily ads spent
                Log::channel('daily_ads_spent')->warning("Ads ({$ad->id}) - {$ad->adds_title}: Live mode");

                $googleAccount = GoogleAccount::find($ad->google_account_id);
                if (!$googleAccount) {
                    Log::channel('daily_ads_spent')->warning("Ads ({$ad->id}) - {$ad->adds_title}: Google account not found");
                    continue;
                }

                $customerId = $ad->customer_id;
                if (!$customerId) {
                    Log::channel('daily_ads_spent')->warning("Ads ({$ad->id}) - {$ad->adds_title}: Have no Customer ID");
                    continue;
                }
    
                if (!isset($ad->google_ad)) {
                    Log::channel('daily_ads_spent')->warning("Ads ({$ad->id}) - {$ad->adds_title}: Have no related Google Ads");
                    continue;
                }
    
                // Check campaign spent
                $googleAdsService = new GoogleAdsService($googleAccount->access_token);
                $campaign = $googleAdsService->getCampaignByResourceName($customerId, $ad->google_ad->campaign_resource_name);
                if (isset($campaign['errors'])) {
                    Log::channel('daily_ads_spent')->warning("Ads ({$ad->id}) - {$ad->adds_title}: Get campaign (failed) -", ['campaign' => json_decode(json_encode($campaign), true)]);
                    continue;
                }
                $costMicros = $campaign['metrics']['costMicros'];
            } else {
                // Test mode
                Log::channel('daily_ads_spent')->warning("Ads ({$ad->id}) - {$ad->adds_title}: Test mode");

                $lastDailySpent = 0;
                if ($ad->e_wallet == 'deduct_balance_real_time') {
                    $dailyAdsSpents = DailyAdsSpent::where('ads_id', $ad->id)->get();
                    foreach ($dailyAdsSpents as $dailyAdsSpent) {
                        $lastDailySpent += $dailyAdsSpent->amount;
                    }
                }
                $lastDailySpent = $lastDailySpent * 1000000;
                Log::channel('daily_ads_spent')->warning("Ads ({$ad->id}) - {$ad->adds_title}: Last daily spent - $lastDailySpent");
                $mockCostMicros = random_int(1000000, 50000000);
                Log::channel('daily_ads_spent')->warning("Ads ({$ad->id}) - {$ad->adds_title}: Mock cost micros - $mockCostMicros");
                $costMicros = $lastDailySpent + $mockCostMicros;
            }
            Log::channel('daily_ads_spent')->warning("Ads ({$ad->id}) - {$ad->adds_title}: Cost micros - $costMicros");
            $cost = $costMicros ? round($costMicros / 1000000, 2) : 0;
            Log::channel('daily_ads_spent')->warning("Ads ({$ad->id}) - {$ad->adds_title}: Cost - $cost");
            
            $totalDailyAdsSpent = 0;
            if ($ad->e_wallet == 'deduct_balance_real_time') {
                $dailyAdsSpents = DailyAdsSpent::where('ads_id', $ad->id)->get();
                foreach ($dailyAdsSpents as $dailyAdsSpent) {
                    $totalDailyAdsSpent += $dailyAdsSpent->amount;
                }
            }
            Log::channel('daily_ads_spent')->warning("Ads ({$ad->id}) - {$ad->adds_title}: Total daily ads spent - $totalDailyAdsSpent");
            $lastSpendAmount = $totalDailyAdsSpent;
            Log::channel('daily_ads_spent')->warning("Ads ({$ad->id}) - {$ad->adds_title}: Last spend amount - $lastSpendAmount");
            
            $newSpent = $cost - $lastSpendAmount;
            Log::channel('daily_ads_spent')->warning("Ads ({$ad->id}) - {$ad->adds_title}: New spent - $newSpent");

            // Check if there is no spent update, skip
            if ($newSpent <= 0) {
                continue;
            }

            DB::beginTransaction();
            try {
                // Save daily ads spent record
                DailyAdsSpent::create([
                    'ads_id' => $ad->id,
                    'amount' => $newSpent,
                    'date' => $date,
                    'added_by_id' => 1,
                ]);
                Log::channel('daily_ads_spent')->info("Ads ({$ad->id}) - {$ad->adds_title}: Daily ads spent saved");

                if ($ad->e_wallet == 'deduct_balance_real_time') {
                    $monthlyPayment = $newSpent;
                } else {
                    // Monthly payment
                    $monthlyPayment = $this->monthly_client_payment($ad->client_id, $ad->id);
                }
                Log::channel('daily_ads_spent')->warning("Ads ({$ad->id}) - {$ad->adds_title}: Monthly payment - $monthlyPayment");

                // Fetch 1 Topups of client that FeeFlag = False            
                $topupFee = 0;
            
                // Check sub wallet topups
                $topups = Transections::where('ads_id', $ad->id)
                    ->where('fee_flag', 0)
                    ->whereIn('topup_type', ['stripe'])
                    ->where('status', 'completed')
                    ->get();

                if ($topups->isEmpty()) {
                    // Change to main wallet, check main wallet topups
                    $topups = ClientWallet::where('client_id', $ad->client_id)
                        ->where('fee_flag', 0)
                        ->whereIn('topup_type', ['stripe'])
                        ->where('status', 'completed')
                        ->limit(1)
                        ->get();
                }

                if ($topups->isNotEmpty()) {
                    foreach ($topups as $topup) {
                        $topupFee = round($topup->amount_in * (3.9 / 100) + 0.60, 2);

                        $topup->fee_flag = 1;
                        $topup->save();
                    }
                }
                Log::channel('daily_ads_spent')->warning("Ads ({$ad->id}) - {$ad->adds_title}: Topup fee - $topupFee");

                $paymentAmount = $monthlyPayment + $topupFee;
                Log::channel('daily_ads_spent')->warning("Ads ({$ad->id}) - {$ad->adds_title}: Payment amount - $paymentAmount");

                $transactionOut = Transections::where('client_id', $ad->client_id)
                    ->where('ads_id', $ad->id)
                    ->where('topup_type', 'google_spent')
                    ->where('status', 'completed');
                $totalAmount = $transactionOut->sum('amount_out') + $paymentAmount; // Total cost all the time
                Log::channel('daily_ads_spent')->warning("Ads ({$ad->id}) - {$ad->adds_title}: Total amount - $totalAmount");
                
                $gst = (($totalAmount) * (9 / 100));
                Log::channel('daily_ads_spent')->warning("Ads ({$ad->id}) - {$ad->adds_title}: GST - $gst");

                $totalPaymentAndGst = $totalAmount + $gst;
                Log::channel('daily_ads_spent')->warning("Ads ({$ad->id}) - {$ad->adds_title}: Payment & GST - $totalPaymentAndGst");

                // Check balance & budget
                $transactionIn = Transections::where('client_id', $ad->client_id)
                    ->where('ads_id', $ad->id)
                    ->where('status', 'completed');
                $totalTopup = $transactionIn->sum('amount_in');
                $balance = $totalTopup;
                Log::channel('daily_ads_spent')->warning("Ads ({$ad->id}) - {$ad->adds_title}: Balance - $balance");
                $budget = $ad->spend_type == 'daily' ? ($ad->daily_budget * 30) : $ad->daily_budget;
                Log::channel('daily_ads_spent')->warning("Ads ({$ad->id}) - {$ad->adds_title}: Budget - $budget");

                // $remainingAmount = $balance < $budget ? $balance : $budget;
                $remainingAmount = min(array_filter([$balance, $budget], fn($value) => $value > 0));
                Log::channel('daily_ads_spent')->warning("Ads ({$ad->id}) - {$ad->adds_title}: Remaining amount - $remainingAmount");

                $availableBalance = $remainingAmount - $totalPaymentAndGst;
                Log::channel('daily_ads_spent')->warning("Ads ({$ad->id}) - {$ad->adds_title}: Available balance - $availableBalance");

                if ($ad->e_wallet == 'deduct_balance_real_time') {
                    Transections::create([
                        'available_balance' => $availableBalance,
                        'client_id' => $ad->client_id,
                        'amount_out' => $paymentAmount,
                        'status' => 'completed',
                        'topup_type' => 'google_spent',
                        'ads_id' => $ad->id,
                    ]);

                    $ad->spend_amount = $availableBalance;
                    $ad->topup_fee = $ad->topup_fee + $topupFee;
                    $ad->save();
                }

                // Stop the campaign when the Wallet Balance is equal or less then 50$
                if ($availableBalance <= 50) {
                    // Log::channel('daily_ads_spent')->info("Ads ({$ad->id}) - {$ad->adds_title}: Available balance less than 1. Start pausing ad");
                    Log::channel('daily_ads_spent')->info("Ads ({$ad->id}) - {$ad->adds_title}: Pausing ad");
                    
                    if ($ad->status !== 'test') {
                        $updateRequestBody = [
                            'status' => 'PAUSED'
                        ];
                        $pauseCampaign = $googleAdsService->updateCampaign($customerId, $ad->google_ad->campaign_resource_name, $updateRequestBody);
                        if (isset($pauseCampaign['errors'])) {
                            Log::channel('daily_ads_spent')->warning("Ads ({$ad->id}) - {$ad->adds_title}: Pausing campaign via Google Ads Center (failed) -", ['pauseCampaign' => json_decode(json_encode($pauseCampaign), true)]);
                            // Temporary comment;
                            // continue;
                        }
                        Log::channel('daily_ads_spent')->info("Ads ({$ad->id}) - {$ad->adds_title}: Pausing campaign via Google Ads Center (success) -", ['pauseCampaign' => json_decode(json_encode($pauseCampaign), true)]);
                    }

                    $ad->status = 'pause';
                    $ad->save();

                    if ($monthlyPayment > 0) {
                        // Generate Invoice
                        Log::channel('daily_ads_spent')->info("Ads ({$ad->id}) - {$ad->adds_title}: Generate ads invoice");

                        $dates = $this->monthly_user_start_end_time($ad->client_id, $ad->id, 'ppc');
                        
                        // Temporary code
                        $adsInvoice = AdsInvoice::where('client_id', $ad->client_id)
                            ->where('ads_id', $ad->id)
                            ->latest()
                            ->first();
                        if (isset($adsInvoice) && !empty($adsInvoice)) {
                            $monthStartDate = Carbon::parse($adsInvoice->created_at);
                            $monthEndDate = now();
                        } else {
                            $leadClient = LeadClient::where('client_id', $ad->client_id)
                                ->where('ads_id', $ad->id)
                                ->where('lead_type', 'ppc')
                                ->first();
                            $monthStartDate = isset($leadClient->created_at) ? Carbon::parse($leadClient->created_at) : now();
                            $monthEndDate = now();
                        }
    
                        AdsInvoice::create([
                            'client_id' => $ad->client_id,
                            'ads_id' => $ad->id,
                            'invoice_date' => date('Y-m-d'),
                            'card_charge' => $ad->topup_fee,
                            'gst' => $gst,
                            'total_amount' => $totalAmount,
                            'total_lead' => $ad->status !== 'test' ? $this->monthly_client_leads($ad->client_id, $ad->id, 'ppc') : ($ad->no_lead ?? 0),
                            'start_date' => $monthStartDate,
                            'end_date' => $monthEndDate,
                        ]);
                        Log::channel('daily_ads_spent')->info("Ads ({$ad->id}) - {$ad->adds_title}: Ads invoice generated");
                    }

                    // Send Message;
                    Log::channel('daily_ads_spent')->info("Ads ({$ad->id}) - {$ad->adds_title}: Send message");
                    /*
                    $sendMessageToNumbers = [
                        'John' => '+6596183284',
                        'Janice' => '+6598213661',
                        'Joleen' => '+6586878984',
                        'Arpit' => '+919214858263',
                        'Client' => convertPhoneNumber($ad->client->phone_number),
                    ];

                    foreach ($sendMessageToNumbers as $name => $number) {
                        $this->send_wp_message($number, "Hi {$name}, Ads {$ad->adds_title} has been paused due to low balance");
                    }
                    */
                    Log::channel('daily_ads_spent')->info("Ads ({$ad->id}) - {$ad->adds_title}: Done");
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
            }
        }
        Log::channel('daily_ads_spent')->info("=========================");

        return Command::SUCCESS;
    }
}
