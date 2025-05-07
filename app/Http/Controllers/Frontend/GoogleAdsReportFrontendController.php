<?php

namespace App\Http\Controllers\Frontend;

use App\Constants\CampaignConstants;
use App\Http\Controllers\Controller;
use App\Models\Ads;
use App\Models\CampaignNote;
use App\Models\GoogleAdsAd;
use App\Models\GoogleAdsReport;
use App\Models\GoogleAdsAccount;
use App\Services\GoogleAdsReportService;
use App\Traits\GoogleAdsReportTrait;
use App\Traits\GoogleTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GoogleAdsReportFrontendController extends Controller
{
    use GoogleTrait;
    use GoogleAdsReportTrait;

    protected $googleAdsReportService;
    protected $developer_token;

    public function __construct(GoogleAdsReportService $googleAdsReportService)
    {
        $this->googleAdsReportService = $googleAdsReportService;
        $this->developer_token = config('services.google.developer_token');
    }

    public function index(Request $request)
    {
        $client = auth('web')->user();
        $clientId = $client->id;

        $get_google_report = GoogleAdsReport::where('client_id', $clientId)->first();
        $filter = $request->filter;
        $start_date = $request->act_start_date ?? Carbon::today()->toDateString();
        $end_date = $request->act_end_date ?? Carbon::today()->toDateString();

        if (!$get_google_report || $filter) {
            $this->saveGooglAdsReport($clientId, $filter, $start_date, $end_date);
        }

        $format_last_update = null;

        $dates = '';
        $clicks = '';
        $impressions = '';
        $conversations = '';
        $average_cpc = '';
        $cost = '';
        $conversation_rate = '';
        $cost_per_con = '';
        $ctr = '';

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
            $summary_graph = json_decode($get_google_report->summary_graph_data, true);
            $performance_graph = json_decode($get_google_report->performance_graph_data, true);
            $format_last_update = Carbon::parse($get_google_report->last_update);
            
            if (is_array($summary_graph) && !empty($summary_graph['dates'])) {
                foreach ($summary_graph['dates'] as $key => $summary_graph_v) {
                    $dates .= "'" . $summary_graph_v . "',";
                    $clicks .= "'" . round($summary_graph['clicks'][$key]) . "',";
                    $impressions .= "'" . round($summary_graph['impressions'][$key]) . "',";
                    $conversations .= "'" . round($summary_graph['conversions'][$key]) . "',";
                    $average_cpc .= "'" . round($summary_graph['average_cpc'][$key]) . "',";
                    $cost .= "'" . round($summary_graph['cost'][$key]) . "',";
                    $conversation_rate .= "'" . round($summary_graph['conversation_rate'][$key] * 100, 2)  . "',";
                    $cost_per_con .= "'" . round($summary_graph['cost_per_conversion'][$key]) . "',";
                    $ctr .= "'" . round(round($summary_graph['clicks'][$key]) / round($summary_graph['impressions'][$key]) * 100) . "',";
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

        $googleAds = GoogleAdsAd::where('client_id', $clientId)
            ->whereNotNull('google_account_id')
            ->whereNotNull('customer_id')
            ->get();
        $adRequests = Ads::where('client_id', $clientId)
            ->whereNotNull('google_account_id')
            ->whereNotNull('customer_id')
            ->get();

        $data = [
            'breadcrumb_main' => 'Google Ads Report',
            'breadcrumb' => 'Google Ads Report',
            'title' => 'Google Ads Report',

            'campaign' => $campaign ?? '',
            'campaign_notes' => $campaign_notes,
            'ads_group' => $ads_group ?? '',
            'keywords' => $keywords ?? '',
            'ads' => $ads ?? '',
            'performance_device' => $devices ?? '',
            'get_customers' => GoogleAdsAccount::get(),
            'customer_account_id' => auth('web')->user()->customer_id ?? '',
            'start_date' => $get_google_report->start_date ?? '',
            'end_date' => $get_google_report->end_date ?? '',
            'last_updated' => isset($format_last_update) ? $format_last_update->diffForHumans() : 'No Data Found',
            'last_updated_date' => isset($format_last_update) ? $format_last_update->format('M d, Y') : 'No Data Found',
            'get_facebook_ads_account' => GoogleAdsAccount::where('account_id', $get_google_report->act_id ?? '')->first(),
            'campaign_with_notes' => $campaign_with_notes,
            
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
            
            'widget_graph_average_cpc' => $average_cpc,
            'widget_graph_cost' => $cost,
            'widget_graph_conversation_rate' => $conversation_rate,
            'widget_graph_cost_per_conversion' => $cost_per_con,
            'widget_graph_ctr' => $ctr,

            'google_ads' => $googleAds,
            'ad_requests' => $adRequests,
        ];
        
        return view('client.google_report.index', $data);
    }

    public function save_google_report(Request $request)
    {
        $clientId = auth('web')->user()->id;

        $filter = $request->filter;
        if ($request->daterange) {
            $dates = explode('-', $request->daterange);
            $startDate = trim($dates[0]);
            $endDate = trim($dates[1]);

            $startDateFormatted = date('Y-m-d', strtotime($startDate));
            $endDateFormatted = date('Y-m-d', strtotime($endDate));

            $start_date = $startDateFormatted;
            $end_date = $endDateFormatted;
        } else {
            $start_date = $request->act_start_date ?? Carbon::today()->toDateString();
            $end_date = $request->act_end_date ?? Carbon::today()->toDateString();
        }

        $this->saveGooglAdsReport($clientId, $filter, $start_date, $end_date);

        return response()->json([
            'success' => 'Google Report Data Fetch Successfully',
            'reload' => true,
        ]);
    }

    public function campaign_note_save(Request $request)
    {
        $request->validate([
            'note_date' => 'required',
            'campaign' => 'required',
            'notes' => 'required',
        ]);
        
        CampaignNote::create([
            'note_date' => $request->note_date,
            'campaign_name' => $request->campaign,
            'note' => $request->notes,
            'ads_report' => CampaignConstants::GOOGLE_ADS_REPORT
        ]);

        return response()->json([
            'success' => 'Campaign Notes Added Successfully',
            'redirect' => route('client.google-ads-report.index')
        ]);
    }

    public function campaign_note_delete($id)
    {
        $campaign_note = CampaignNote::hashidFind($id);
        $campaign_note->delete();
        
        return response()->json([
            'success' => 'Campaign Note Deleted Successfully',
            'redirect' => route('client.google-ads-report.index')
        ]);
    }

    public function download_pdf()
    {
        $clientId = auth('web')->user()->id;

        return $this->googleAdsReportService->downloadPdf($clientId);
    }
}
