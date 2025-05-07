<?php

namespace App\Traits;

use App\Models\DailyAdsSpent;
use App\Models\AdsInvoice;
use App\Models\LeadClient;
use App\Models\TaxCharge;
use App\Models\Ads;
use App\Models\User;
use Carbon\Carbon;

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
    
        return DailyAdsSpent::where('sub_account_id', $sub_account_id)->whereBetween('date', [$weekStartDate->format('Y-m-d'),$weekEndDate->format('Y-m-d')])->sum('amount');
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

    public function get_monthly_ads_spent($subAccountId)
    {
        $clientIds = User::where('sub_account_id', $subAccountId)->pluck('id');
        $adIds = Ads::whereIn('client_id', $clientIds)->pluck('id');
    
        $totalAdsSpent = DailyAdsSpent::with('ads')->whereIn('ads_id', $adIds)
            ->whereBetween('date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->sum('amount');

        return number_format($totalAdsSpent, 2);
    }

    public function get_monthly_client($subAccountId)
    {
        $clientIds = User::where('sub_account_id', $subAccountId)->pluck('id');
    
        $uniqueClientsCount = Ads::with('client')->whereIn('client_id', $clientIds)
            ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->distinct('client_id')
            ->count('client_id');

        return $uniqueClientsCount;
    }

    public function get_monthly_client_leads($subAccountId)
    {
        $clientIds = User::where('sub_account_id', $subAccountId)->pluck('id');
    
        $uniqueClientsCount = LeadClient::with('client')->whereIn('client_id', $clientIds)
            ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->count('client_id');

        return $uniqueClientsCount;
    }

    /**
     * Calculates the total monthly payment from a client based on the number of leads and the average cost per lead.
     *
     * @param int $client_id The client ID
     * @param int $ad_id The ad ID
     * @param string $lead_type The type of lead, default is 'ppc'
     * @return float The total monthly payment for the client
     */
    public function monthly_client_payment($client_id, $ad_id, $lead_type = 'ppc')
    {
        return @($this->monthly_client_leads($client_id, $ad_id, $lead_type) * $this->monthly_avg_single_leads_amount($ad_id));
    }

    /**
     * Retrieves the number of leads for a specific client and ad within a month.
     *
     * @param int $client_id The client ID
     * @param int $ad_id The ad ID
     * @param string $lead_type The type of lead, default is 'ppc'
     * @return int The number of leads generated for the client
     */
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

    /**
     * Determines the start and end date of a client's ad activity for the current month.
     * It uses the latest invoice or lead entry if no invoice is found.
     *
     * @param int $client_id The client ID
     * @param int $ad_id The ad ID
     * @param string $lead_type The type of lead, default is 'ppc'
     * @return array An array containing 'start_date' and 'end_date'
     */
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

    /**
     * Calculates the average cost per lead, including VAT charges.
     *
     * @param int $ad_id The ad ID
     * @return float The average cost per lead with VAT
     */
    public function monthly_avg_single_leads_amount($ad_id)
    {
        return round($this->monthly_avg_amount_of_total_lead($ad_id)) + round($this->monthly_single_leads_vat_charges($ad_id));
    }

    /**
     * Calculates the average amount spent per lead for a specific ad.
     *
     * @param int $ad_id The ad ID
     * @return float The average amount spent per lead
     */
    public function monthly_avg_amount_of_total_lead($ad_id)
    {
        $total_leads_until_of_this_today = $this->monthly_total_leads_until_of_this_today($ad_id);
        return $total_leads_until_of_this_today > 0 ? ($this->monthly_sum_of_daily_ads_spent($ad_id) / $total_leads_until_of_this_today) : 0;
    }

    /**
     * Retrieves the total number of leads generated for a specific ad within the current month.
     *
     * @param int $ad_id The ad ID
     * @return int The total number of leads for the current month
     */
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

    /**
     * Calculates the VAT charges for a single lead.
     *
     * @param int $ad_id The ad ID
     * @return float The VAT charges for a single lead
     */
    public function monthly_single_leads_vat_charges($ad_id)
    {
        $get_vat_charges = TaxCharge::where('status', 'active')->latest()->first();
        $avg_amount_of_total_lead = $this->monthly_avg_amount_of_total_lead($ad_id);
        return $get_vat_charges ? ($avg_amount_of_total_lead * ($get_vat_charges->charges / 100)) : 0;
    }

    /**
     * Retrieves the total amount spent on ads for a specific ad within the current month.
     *
     * @param int $ad_id The ad ID
     * @return float The total ad spend for the current month
     */
    public function monthly_sum_of_daily_ads_spent($ad_id)
    {
        $startOfMonth = now()->startOfMonth()->setHour(0)->setMinute(0)->setSecond(0);
        $endOfMonth = now()->endOfMonth()->setHour(23)->setMinute(59)->setSecond(59);
    
        return DailyAdsSpent::where('ads_id', $ad_id)
            ->whereBetween('date', [$startOfMonth->format('Y-m-d H:i:s'), $endOfMonth->format('Y-m-d H:i:s')])
            ->sum('amount');
    }
}
