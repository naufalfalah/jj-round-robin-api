<?php

namespace App\Services;

use App\Models\CampaignNote;
use App\Models\GoogleAdsReport;
use App\Traits\GoogleAdsReportTrait;
use Carbon\Carbon;
use Spatie\Browsershot\Browsershot;

class GoogleAdsReportService
{
    use GoogleAdsReportTrait;

    public function saveGoogleAdsReport($clientId, $start_date, $end_date, $acct_id, $access_token, $developer_token)
    {
        $get_google_campaigns = $this->get_google_campaigns($start_date, $end_date, $acct_id, $access_token, $developer_token);
        $get_google_ads_group = $this->get_google_ads_group($start_date, $end_date, $acct_id, $access_token, $developer_token);
        $get_google_keywords = $this->get_google_keywords($start_date, $end_date, $acct_id, $access_token, $developer_token);
        $get_google_ads = $this->get_google_ads($start_date, $end_date, $acct_id, $access_token, $developer_token);
        $get_performance_devices = $this->get_performance_devices($start_date, $end_date, $acct_id, $access_token, $developer_token);
        $get_summary_graph_data = $this->get_summary_graph_data($start_date, $end_date, $acct_id, $access_token, $developer_token);
        $get_performance_data = $this->get_performance_data_for_graph($start_date, $end_date, $acct_id, $access_token, $developer_token);
        
        GoogleAdsReport::updateOrCreate([
            'client_id' => $clientId,
            'act_id' => $acct_id,
        ], [
            'campaign' => $get_google_campaigns,
            'ads_group' => $get_google_ads_group,
            'keywords' => $get_google_keywords,
            'ads' => $get_google_ads,
            'performance_device' => $get_performance_devices,
            'summary_graph_data' => $get_summary_graph_data,
            'performance_graph_data' => $get_performance_data,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'last_update' => now(),
        ]);

        return true;
    }

    public function downloadPdf($clientId)
    {
        $get_google_report = GoogleAdsReport::where('client_id', $clientId)->first();

        $campaign = '';
        $ads_group = '';
        $keywords = '';
        $ads = '';
        $devices = '';

        $dates = '';
        $clicks = '';
        $impressions = '';
        $conversations = '';

        $performance_dates = '';
        $costs = '';
        $cost_per_1000_imp = '';
        $cost_per_click = '';
        $reveneu_per_click = '';
        $total_value = '';

        if ($get_google_report) {
            $campaign = json_decode($get_google_report->campaign, true);
            $ads_group = json_decode($get_google_report->ads_group, true);
            $keywords = json_decode($get_google_report->keywords, true);
            $ads = json_decode($get_google_report->ads, true);
            $devices = json_decode($get_google_report->performance_device, true);
            $format_last_update = Carbon::parse($get_google_report->last_update);
            $summary_graph = json_decode($get_google_report->summary_graph_data, true);
            $performance_graph = json_decode($get_google_report->performance_graph_data, true);

            if (is_array($summary_graph) && !empty($summary_graph['dates'])) {
                foreach ($summary_graph['dates'] as $key => $summary_graph_v) {
                    $dates .= "'" . $summary_graph_v . "',";
                    $clicks .= "'" . round($summary_graph['clicks'][$key]) . "',";
                    $impressions .= "'" . round($summary_graph['impressions'][$key]) . "',";
                    $conversations .= "'" . round($summary_graph['conversions'][$key]) . "',";
                }
            }

            if (is_array($performance_graph) && !empty($performance_graph['dates'])) {
                foreach ($performance_graph['dates'] as $key => $performance_graph_v) {
                    $performance_dates .= "'" . $performance_graph_v . "',";
                    $costs .= "'" . round($performance_graph['cost'][$key]) . "',";
                    $cost_per_1000_imp .= "'" . round($performance_graph['cost_per_1000_impressions'][$key]) . "',";
                    $cost_per_click .= "'" . round($performance_graph['cost_per_click'][$key]) . "',";
                    $reveneu_per_click .= "'" . round($performance_graph['revenue_per_click'][$key]) . "',";
                    $total_value .= "'" . round($performance_graph['total_value'][$key]) . "',";
                }
            }

            $campaign_notes = CampaignNote::where('ads_report', 'google_ads_report')->get();
            $campaign_with_notes = [];

            if (isset($campaign['results']) && !empty($campaign['results'])) {
                foreach ($campaign['results'] as $item) {
                    $campaigns = $item['campaign'];
                    $metrics = $item['metrics'];
                    $campaign_budget = $item['campaignBudget'];
                    $campaign_ctr = ($metrics['impressions'] > 0) ? ($metrics['clicks'] / $metrics['impressions'] * 100) : 0;
                    $campaign_cost = ($metrics['costMicros'] > 0) ? ($metrics['costMicros'] / 1000000) : 0;
                    $campaign_start_date = $campaigns['startDate'];
                    $format_start_date = date('jS M Y', strtotime($campaign_start_date));
                    $cost_per_conversation = ($metrics['conversions'] > 0) ? ($campaign_cost / $metrics['conversions']) : 0;
                    $cal_campaign_budget = ($campaign_budget['amountMicros'] > 0) ? ($campaign_budget['amountMicros'] / 1000000) : 0;

                    // calculate daily budget

                    // $daily_budg_start_date = new DateTime($campaign_start_date);
                    // $daily_budg_end_date = new DateTime($end_date);
                    // $interval = $daily_budg_start_date->diff($daily_budg_end_date);
                    // $total_days = $interval->days;
                    // $daily_budget = ($total_days > 0) ? ($cost / $total_days * 100) : 0;

                    $campaign_notes_for_campaign = $campaign_notes->where('campaign_name', $campaigns['name'])->pluck('note')->toArray();

                    $campaign_with_notes[] = [
                        'campaign_id' => $campaigns['id'],
                        'name' => $campaigns['name'],
                        'date' => $format_start_date,
                        'total_leads' => $metrics['conversions'] ?? '0',
                        'cost_per_conversation' => $cost_per_conversation ?? '0',
                        'spend' => $campaign_cost ?? '0',
                        'campaign_budget' => $cal_campaign_budget ?? '0',
                        'campaign_notes' => $campaign_notes_for_campaign
                    ];
                }
            }

            if (isset($ads['results']) && !empty($ads['results']) && !empty($campaign_with_notes)) {
                foreach ($ads['results'] as $ad) {
                    $campaign_id = $ad['campaign']['id'];
                    $ad_final_url = $ad['adGroupAd']['ad']['finalUrls'][0] ?? 'No Website URL Found';
                    
                    foreach ($campaign_with_notes as &$campaign_note) {
                        if ($campaign_note['campaign_id'] == $campaign_id) {
                            $campaign_note['final_url'] = $ad_final_url ?? 'No Website URL Found';
                            
                            break;
                        }
                    }
                }
            }
        }

        $data = [
            'breadcrumb_main' => 'Google Ads Report',
            'breadcrumb' => 'Google Ads Report',
            'title' => 'Google Ads Report',
            'campaign' => $campaign ?? '',
            'campaign_notes' => $campaign_notes ?? [],
            'ads_group' => $ads_group ?? '',
            'keywords' => $keywords ?? '',
            'ads' => $ads ?? '',
            'performance_device' => $devices ?? '',
            'ad_account_name' => $get_add_account_name->account_title ?? '',
            'generated_on' => Carbon::now()->format('M-d-Y H:i A'),
            'start_date' => $get_google_report->start_date ?? '',
            'end_date' => $get_google_report->end_date ?? '',
            'campaign_with_notes' => $campaign_with_notes ?? '',

            // summary graph data
            'summary_graph_dates' => $dates,
            'summary_graph_clicks' => $clicks,
            'summary_graph_impressions' => $impressions,
            'summary_graph_conversations' => $conversations,

            // performance graph data
            'performance_graph_dates' => $performance_dates,
            'performance_graph_costs' => $costs,
            'performance_graph_cost_per_1000_imp' => $cost_per_1000_imp,
            'performance_graph_cost_per_click' => $cost_per_click,
            'performance_graph_reveneu_per_click' => $reveneu_per_click,
            'performance_graph_total_value' => $total_value,

            // widget graph data
            'total_impressions' => array_sum($summary_graph['impressions'] ?? [0]),
            'total_clicks' => array_sum($summary_graph['clicks'] ?? [0]),
            'total_conversions' => array_sum($summary_graph['conversions'] ?? [0]),
            'total_cost' => array_sum($summary_graph['cost'] ?? [0]),
        ];

        $html = view('client.google_report.pdf_file')->with($data)->render();
        $nmp_path = config('app.nmp_path');
        sleep(10);
        $timeout = 50000;
        $pdf = Browsershot::html($html)
            ->setTimeout($timeout)
            ->setIncludePath('$PATH:'.$nmp_path)
            ->waitUntilNetworkIdle()
            ->format('A4')
            ->pdf();

        $accountTitle = '';
        if (isset($get_add_account_name->account_title)) {
            $accountTitle = $get_add_account_name->account_title ?? '';
        }
        $fileName = $accountTitle . ' - ' . Carbon::now()->format('Y-m-d') . '.pdf';

        $pdfPath = storage_path('app/public/' . $fileName);
        file_put_contents($pdfPath, $pdf);

        return response()->download($pdfPath)->deleteFileAfterSend(true);
    }
}
