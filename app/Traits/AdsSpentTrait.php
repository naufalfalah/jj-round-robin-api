<?php

namespace App\Traits;

use App\Models\Ads;
use App\Models\AdsInvoice;
use App\Models\ClientMessageTemplate;
use App\Models\DailyAdsSpent;
use App\Models\JunkLead;
use App\Models\LeadClient;
use App\Models\LeadData;
use App\Models\SubAccount;
use App\Models\TaxCharge;
use App\Models\User;
use App\Models\UserSubAccount;
use App\Models\WpMessageTemplate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait AdsSpentTrait
{
    public function sum_of_daily_ads_spent($sub_account_id)
    {
        $weekStartDate = now()->startOfWeek(Carbon::MONDAY)->subWeek()->setHour(10);
        $weekEndDate = $weekStartDate->clone()->addWeek()->endOfWeek(Carbon::MONDAY)->setHour(9);

        $currentDateTime = now();
        if ($currentDateTime->isAfter($weekEndDate)) {
            $weekStartDate = $currentDateTime->startOfWeek(Carbon::MONDAY)->setHour(10);
            $weekEndDate = $weekStartDate->clone()->addWeek()->endOfWeek(Carbon::MONDAY)->setHour(9);
        }

        return DailyAdsSpent::where('sub_account_id', $sub_account_id)->whereBetween('date', [$weekStartDate->format('Y-m-d'), $weekEndDate->format('Y-m-d')])->sum('amount');
    }

    public function total_leads_until_of_this_today($sub_account_id)
    {
        $weekStartDate = now()->startOfWeek(Carbon::MONDAY)->subWeek()->setHour(10);
        $weekEndDate = $weekStartDate->clone()->addWeek()->endOfWeek(Carbon::MONDAY)->setHour(9);

        $currentDateTime = now();
        if ($currentDateTime->isAfter($weekEndDate)) {
            $weekStartDate = $currentDateTime->startOfWeek(Carbon::MONDAY)->setHour(10);
            $weekEndDate = $weekStartDate->clone()->addWeek()->endOfWeek(Carbon::MONDAY)->setHour(9);
        }

        $sub_account_ids = User::where('sub_account_id', $sub_account_id)->pluck('id');

        return LeadClient::where('lead_type', 'ppc')->whereIn('client_id', $sub_account_ids)->whereBetween('created_at', [$weekStartDate->format('Y-m-d H:i:s'), $weekEndDate->format('Y-m-d H:i:s')])->count();
    }

    public function avg_amount_of_total_lead($sub_account_id)
    {
        $total_leads_until_of_this_today = $this->total_leads_until_of_this_today($sub_account_id);

        return $total_leads_until_of_this_today > 0 ? $this->sum_of_daily_ads_spent($sub_account_id) / $total_leads_until_of_this_today : 0;
    }

    public function single_leads_vat_charges($sub_account_id)
    {
        $get_vat_charges = TaxCharge::where('status', 'active')->latest()->first();
        $avg_amount_of_total_lead = $this->avg_amount_of_total_lead($sub_account_id);

        return $get_vat_charges ? ($avg_amount_of_total_lead * ($get_vat_charges->charges / 100)) : 0;
    }

    public function avg_single_leads_amount($sub_account_id)
    {
        return round($this->avg_amount_of_total_lead($sub_account_id)) + round($this->single_leads_vat_charges($sub_account_id));
    }

    public function this_week_client_leads($client_id, $lead_type = 'ppc')
    {
        $dates = $this->get_user_start_end_time($client_id);
        $weekStartDate = $dates['start_date']->format('Y-m-d H:i:s');
        $weekEndDate = $dates['end_date']->format('Y-m-d H:i:s');

        return LeadClient::where('client_id', $client_id)->where('lead_type', $lead_type)->whereBetween('created_at', [$weekStartDate, $weekEndDate])->count();
    }

    public function client_payment($client_id, $sub_account_id, $lead_type = 'ppc')
    {
        return @($this->this_week_client_leads($client_id, $lead_type) * $this->avg_single_leads_amount($sub_account_id));
    }

    public function get_user_start_end_time($client_id, $lead_type = 'ppc')
    {
        $get_dates = AdsInvoice::where('client_id', $client_id)->latest()->first();
        if (isset($get_dates) && !empty($get_dates)) {
            $weekStartDate = Carbon::parse($get_dates->created_at);
            $weekEndDate = now();
        } else {
            $get_dates = LeadClient::where('client_id', $client_id)->where('lead_type', $lead_type)->first();
            $weekStartDate = Carbon::parse($get_dates->created_at);
            $weekEndDate = now();
        }

        return ['start_date' => $weekStartDate, 'end_date' => $weekEndDate];
    }

    public function check_user_ads($client_id)
    {
        $ads_check = Ads::where('client_id', $client_id)->latest()->first();
        if ($ads_check) {
            return true;
        } else {
            return false;
        }
    }

    public function get_monthly_ads_spent($userSubAccountId)
    {
        $userSubAccount = UserSubAccount::find($userSubAccountId);

        if (!$userSubAccount) {
            return number_format(0, 2);
        }

        $clientIds = $userSubAccount->pluck('client_id')->toArray();

        if (empty($clientIds)) {
            return number_format(0, 2);
        }

        $adIds = Ads::whereIn('client_id', $clientIds)->pluck('id')->toArray();

        if (empty($adIds)) {
            return number_format(0, 2);
        }

        $totalAdsSpent = DailyAdsSpent::whereIn('ads_id', $adIds)
            ->whereBetween('date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->sum('amount');

        return number_format($totalAdsSpent, 2);
    }

    public function get_monthly_client($userSubAccountId)
    {
        $userSubAccount = UserSubAccount::find($userSubAccountId);

        if (!$userSubAccount) {
            return 0;
        }

        $clientIds = $userSubAccount->pluck('client_id')->toArray();

        if (empty($clientIds)) {
            return 0;
        }

        $uniqueClientsCount = Ads::whereIn('client_id', $clientIds)
            ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->distinct('client_id')
            ->count('client_id');

        return $uniqueClientsCount;
    }

    public function get_monthly_client_leads($userSubAccountId)
    {
        $userSubAccount = UserSubAccount::find($userSubAccountId);

        if (!$userSubAccount) {
            return 0;
        }

        $clientIds = $userSubAccount->pluck('client_id')->toArray();

        if (empty($clientIds)) {
            return 0;
        }

        $uniqueClientsCount = LeadClient::whereIn('client_id', $clientIds)
            ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->distinct('client_id')
            ->count('client_id');

        return $uniqueClientsCount;
    }

    public function monthly_client_payment($client_id, $ad_id, $lead_type = 'ppc')
    {
        return @($this->monthly_client_leads($client_id, $ad_id, $lead_type) * $this->monthly_avg_single_leads_amount($ad_id));
    }

    public function monthly_client_leads($client_id, $ad_id, $lead_type = 'ppc')
    {
        $dates = $this->monthly_user_start_end_time($client_id, $ad_id, 'ppc');
        $monthStartDate = $dates['start_date']->format('Y-m-d H:i:s');
        $monthEndDate = $dates['end_date']->format('Y-m-d H:i:s');

        return LeadClient::where('client_id', $client_id)
            ->where('lead_type', $lead_type)
            ->where('ads_id', $ad_id)
            ->whereBetween('created_at', [$monthStartDate, $monthEndDate])
            ->count();
    }

    public function monthly_user_start_end_time($client_id, $ad_id, $lead_type = 'ppc')
    {
        $adsInvoice = AdsInvoice::where('client_id', $client_id)
            ->where('ads_id', $ad_id)
            ->latest()
            ->first();
        if (isset($adsInvoice) && !empty($adsInvoice)) {
            $startDate = Carbon::parse($adsInvoice->created_at);
            $endDate = now();
        } else {
            $leadClient = LeadClient::where('client_id', $client_id)
                ->where('ads_id', $ad_id)
                ->where('lead_type', $lead_type)
                ->first();
            $startDate = isset($leadClient->created_at) ? Carbon::parse($leadClient->created_at) : now();
            $endDate = now();
        }

        return ['start_date' => $startDate, 'end_date' => $endDate];
    }

    public function monthly_avg_single_leads_amount($ad_id)
    {
        return round($this->monthly_avg_amount_of_total_lead($ad_id)) + round($this->monthly_single_leads_vat_charges($ad_id));
    }

    public function monthly_avg_amount_of_total_lead($ad_id)
    {
        $total_leads_until_of_this_today = $this->monthly_total_leads_until_of_this_today($ad_id);

        return $total_leads_until_of_this_today > 0 ? ($this->monthly_sum_of_daily_ads_spent($ad_id) / $total_leads_until_of_this_today) : 0;
    }

    public function monthly_total_leads_until_of_this_today($ad_id)
    {
        $startOfMonth = now()->startOfMonth()->setHour(0)->setMinute(0)->setSecond(0);
        $endOfMonth = now()->endOfMonth()->setHour(23)->setMinute(59)->setSecond(59);

        $ad = Ads::find($ad_id);

        $totalLeads = LeadClient::where('lead_type', 'ppc')
            ->where('ads_id', $ad_id)
            ->whereIn('client_id', [$ad->client_id])
            ->whereBetween('created_at', [$startOfMonth->format('Y-m-d H:i:s'), $endOfMonth->format('Y-m-d H:i:s')])
            ->count();

        if ($totalLeads == 0 && $ad->e_wallet == 'deduct_balance_real_time') {
            $totalLeads = 1;
        }

        return $totalLeads;
    }

    public function monthly_single_leads_vat_charges($ad_id)
    {
        $get_vat_charges = TaxCharge::where('status', 'active')->latest()->first();
        $avg_amount_of_total_lead = $this->monthly_avg_amount_of_total_lead($ad_id);

        return $get_vat_charges ? ($avg_amount_of_total_lead * ($get_vat_charges->charges / 100)) : 0;
    }

    public function monthly_sum_of_daily_ads_spent($ad_id)
    {
        $startOfMonth = now()->startOfMonth()->setHour(0)->setMinute(0)->setSecond(0);
        $endOfMonth = now()->endOfMonth()->setHour(23)->setMinute(59)->setSecond(59);

        return DailyAdsSpent::where('ads_id', $ad_id)
            ->whereBetween('date', [$startOfMonth->format('Y-m-d H:i:s'), $endOfMonth->format('Y-m-d H:i:s')])
            ->sum('amount');
    }

    public function add_ppc_lead(Request $request, $que_lead = null)
    {
        if ($que_lead == null) {
            $referer = $this->extractBaseUrlPattern($request->source_url);
            $username = $request->header('PHP_AUTH_USER');
            $password = $request->header('PHP_AUTH_PW');
            if ($username !== 'Client Management Portal' || $password !== '123456') {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        } else {

            $request = json_decode($que_lead->lead_data);
            $referer = $this->extractBaseUrlPattern($que_lead->sub_account_url);
        }

        $check_referer_exist = SubAccount::where('sub_account_url', $referer)->where('status', 'Active')->first();

        if ($check_referer_exist) {
            $user_ids = User::where('sub_account_id', $check_referer_exist->id)->pluck('id');
        } else {
            $junk_lead = new JunkLead;
            $junk_lead->lead_data = json_encode($request);
            $junk_lead->status = 'que_lead';
            $junk_lead->sub_account_url = $request->source_url ?? '';
            $junk_lead->save();

            return response()->json([
                'success' => true,
                'msg' => 'No Sub Account Match! Lead Save In a Que',
            ]);
        }

        try {
            $adsList = $this->findRunningAds($user_ids, $check_referer_exist->id);

            if (!isset($adsList->client_id)) {
                $junk_lead = new JunkLead;
                $junk_lead->lead_data = json_encode($request->all());
                $junk_lead->status = 'que_lead';
                $junk_lead->sub_account_id = $check_referer_exist->id ?? '';
                $junk_lead->sub_account_url = $request->source_url ?? '';
                $junk_lead->save();

                return $adsList;
            }

            DB::beginTransaction();

            $check_email_exist = LeadClient::whereIn('client_id', $user_ids)
                ->where(function ($query) use ($request) {
                    $query->where('email', $request->email)
                        ->orWhere('mobile_number', $request->mobile_number);
                })
                ->count();

            if ($check_email_exist > 0) {
                $junk_lead = new JunkLead;
                if ($que_lead == null) {
                    $junk_lead->lead_data = json_encode($request->all());
                    $junk_lead->sub_account_id = $check_referer_exist->id ?? '';
                    $junk_lead->sub_account_url = $request->source_url ?? '';
                } else {
                    $junk_lead->lead_data = $que_lead->lead_data;
                    $upd_status = JunkLead::find($que_lead->id);
                    $upd_status->is_send = 1;
                    $upd_status->update();
                }
                $junk_lead->save();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'msg' => 'This Is Junk Lead Save Successfully',
                ]);
            } else {
                if ($que_lead == null) {
                    $leads = $request->all();
                } else {
                    $leads = $request;
                }

                if (!empty($adsList) && !empty($leads)) {
                    $ads_lead = new LeadClient;
                    if ($request->status == 'DNC Registry') {
                        $ads_lead->client_id = 0;
                        $ads_lead->status = 'DNC Registry';
                    } else {
                        $ads_lead->client_id = $adsList->client_id;
                    }
                    $ads_lead->name = $request->name ?? '';
                    $ads_lead->email = $request->email ?? '';
                    $ads_lead->mobile_number = $request->mobile_number ?? '';
                    $ads_lead->lead_type = 'ppc';
                    $ads_lead->added_by_id = $adsList->client_id;
                    $ads_lead->save();

                    $sheet_name = 'Leads Frequency';
                    $find_user = User::find($adsList->client_id);
                    $spreadsheet_id = $find_user->spreadsheet_id;
                    if (!empty($spreadsheet_id) && $request->status != 'DNC Registry') {
                        if (isset($request->email) && !empty($request->email)) {
                            $this->saveToGoogleSheet($request->name, $request->email, $request->mobile_number, $request->additional_data, $sheet_name, $spreadsheet_id);
                        } else {
                            $this->saveToGoogleSheet($request->name, $request->email ?? '', $request->mobile_number, $request->additional_data, $sheet_name, $spreadsheet_id);
                        }
                    }

                    $lead_key_data = [];

                    if (!empty($request->additional_data) && count($request->additional_data) > 0) {
                        foreach ($request->additional_data as $k => $val) {

                            if (is_array($val)) {
                                $lead_key_data[] = [
                                    'lead_client_id' => $ads_lead->id,
                                    'key' => $val['key'],
                                    'value' => $val['value'],
                                    'added_by_id' => $ads_lead->client_id,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                            } else {
                                $lead_key_data[] = [
                                    'lead_client_id' => $ads_lead->id,
                                    'key' => $val->key,
                                    'value' => $val->value,
                                    'added_by_id' => $ads_lead->client_id,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                            }
                        }
                        LeadData::insert($lead_key_data);
                    }

                    if ($que_lead != null) {
                        $upd_status = JunkLead::find($que_lead->id);
                        $upd_status->is_send = 1;
                        $upd_status->update();
                    }

                    if (isset($ads_lead->email) && !empty($ads_lead->email)) {
                        $leadMessage = 'New Lead Please take note!
===========================
Hello '.$adsList->client->client_name.", you have a new lead:
- Name: {$ads_lead->name}
- Email: {$ads_lead->email}
- Mobile Number: https://wa.me/+65{$ads_lead->mobile_number}";
                    } else {
                        $leadMessage = 'New Lead Please take note!
===========================
Hello '.$adsList->client->client_name.", you have a new lead:
- Name: {$ads_lead->name}
- Mobile Number: https://wa.me/+65{$ads_lead->mobile_number}";
                    }

                    if (!empty($request->additional_data) && count($request->additional_data) > 0) {
                        foreach ($request->additional_data as $val) {
                            if (is_array($val)) {
                                $leadMessage .= "\n- {$val['key']}: {$val['value']}";
                            } else {
                                $leadMessage .= "\n- {$val->key}: {$val->value}";
                            }
                        }
                    }

                    $url = $adsList->discord_link;
                    if ($request->status != 'DNC Registry') {
                        $send_descord_msg = $this->send_discord_msg($url, $leadMessage);
                    }

                    if (empty($send_descord_msg)) {
                        $ads_lead->is_send_discord = 1;
                    } else {
                        $ads_lead->is_send_discord = 0;
                    }
                    $ads_lead->save();

                    $check_msg_status = WpMessageTemplate::first();
                    // whatsapp msg send code
                    if ($check_msg_status->status === 'active') {

                        if ($request->status != 'DNC Registry') {
                            if ($request->mobile_number && !empty($request->mobile_number)) {
                                $get_message = ClientMessageTemplate::where('client_id', $adsList->client_id)->first();
                                if ($get_message) {
                                    $replece_name = str_replace('@clientName', $ads_lead->name, $get_message->message_template);
                                    $replece_email = str_replace('@email', $ads_lead->email, $replece_name);
                                    $replece_phone = str_replace('@phone', $ads_lead->mobile_number, $replece_email);

                                    $send_message = $this->send_wp_message($request->mobile_number, $replece_phone);
                                } else {
                                    $get_message = WpMessageTemplate::first();
                                    $replece_name = str_replace('@clientName', $ads_lead->name, $get_message->wp_message);
                                    $replece_email = str_replace('@email', $ads_lead->email, $replece_name);
                                    $replece_phone = str_replace('@phone', $ads_lead->mobile_number, $replece_email);

                                    $send_message = $this->send_wp_message($request->mobile_number, $replece_phone);

                                }
                            }
                        }
                    }

                    // whatsapp msg send code
                    $adsList->lead_status = 1;
                    $adsList->save();
                }

                DB::commit();

                $response = $this->verifySalesperson($request->mobile_number);
                if (@$response->result) {
                    $ads_lead->user_status = 'agent';
                    $ads_lead->registration_no = $response->data->reg_number;
                    $ads_lead->save();
                }

                return true;
            }
        } catch (\Exception $e) {
            // Something went wrong, rollback the transaction
            DB::rollback();

            // Access the error message
            $errorMessage = $e->getMessage();

            // You can log or handle the error message as needed
            // For example, you can throw a custom exception with a more specific message
            throw new \Exception("Error receiving lead: $errorMessage");
        }
    }
}
