<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\CampaignNote;
use App\Models\GoogleAd;
use App\Models\GoogleAdsAccount;
use App\Models\GoogleReport;
use App\Traits\ApiResponseTrait;
use App\Traits\GoogleTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @group Google Ads Report
 */
class GoogleAdsReportController extends Controller
{
    use ApiResponseTrait, GoogleTrait;

    public function __construct()
    {
        $this->middleware('check.menu:report');
    }

    public function update_act_expiry_date(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => 'required|integer',
            'expiry_date' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendErrorResponse(implode("\n", $validator->errors()->all()), 400);
        }

        $google_ads_account = GoogleAdsAccount::where('account_id', $request->account_id)->first();
        if (!$google_ads_account) {
            return response()->json([
                'success' => 'Google Ads account not found',
                'reload' => false,
            ]);
        }

        if ($google_ads_account) {
            $google_ads_account->account_expiry_date = $request->expiry_date;
            $google_ads_account->save();

            $message = 'Google Ads Account Expiry Date Updated Successfully';
        } else {
            $message = 'Google Ads Account Not Found';
        }

        return response()->json([
            'success' => $message,
            'reload' => true,
        ]);
    }

    public function google_ads_report(Request $request)
    {
        $admin = Admin::first();
        $check_google_customer_exists = GoogleAdsAccount::count();
        if (
            !empty($admin->provider_id) &&
            !empty($admin->google_access_token) &&
            $check_google_customer_exists == 0
        ) {
            $this->save_customer_account_detail();
        }

        $selectedAd = $request->filter ? GoogleAd::find($request->filter) : null;

        $userId = auth('web')->user()->id ?? auth('api')->user()->id;
        $user = auth('web')->user() ?? auth('api')->user();

        $get_google_report = GoogleReport::where('client_id', $userId)->first();

        if (!$get_google_report || isset($selectedAd)) {
            $start_date = $request->act_start_date ?? Carbon::today()->toDateString();
            $end_date = $request->act_end_date ?? Carbon::today()->toDateString();

            $devloper_token = config('services.google.developer_token');
            $acct_id = $user->customer_id;

            $get_client = $this->getAdminClient();
            $get_access_token = $get_client->getAccessToken();
            $access_token = $get_access_token['access_token'];

            $filterResourceName = $selectedAd?->campaign_resource_name ?? null;

            $get_google_campaigns = $this->get_google_campaigns($start_date, $end_date, $acct_id, $access_token, $devloper_token, $filterResourceName);
            $get_google_ads_group = $this->get_google_ads_group($start_date, $end_date, $acct_id, $access_token, $devloper_token, $filterResourceName);
            $get_google_keywords = $this->get_google_keywords($start_date, $end_date, $acct_id, $access_token, $devloper_token, $filterResourceName);
            $get_google_ads = $this->get_google_ads($start_date, $end_date, $acct_id, $access_token, $devloper_token, $filterResourceName);
            $get_performance_devices = $this->get_performance_devices($start_date, $end_date, $acct_id, $access_token, $devloper_token, $filterResourceName);
            $get_summary_graph_data = $this->get_summary_graph_data($start_date, $end_date, $acct_id, $access_token, $devloper_token, $filterResourceName);
            $get_performance_data = $this->get_performance_data_for_graph($start_date, $end_date, $acct_id, $access_token, $devloper_token, $filterResourceName);

            $get_google_report = GoogleReport::updateOrCreate([
                'act_id' => $acct_id,
                'client_id' => $user->id,
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
                    $dates .= "'".$summary_graph_v."',";
                    $clicks .= "'".round($summary_graph['clicks'][$key])."',";
                    $impressions .= "'".round($summary_graph['impressions'][$key])."',";
                    $conversations .= "'".round($summary_graph['conversions'][$key])."',";
                    $average_cpc .= "'".round($summary_graph['average_cpc'][$key])."',";
                    $cost .= "'".round($summary_graph['cost'][$key])."',";
                    $conversation_rate .= "'".round($summary_graph['conversation_rate'][$key] * 100, 2)."',";
                    $cost_per_con .= "'".round($summary_graph['cost_per_conversion'][$key])."',";
                    $ctr .= "'".round(round($summary_graph['clicks'][$key]) / round($summary_graph['impressions'][$key]) * 100)."',";
                }
            }

            if (is_array($performance_graph) && !empty($performance_graph['dates'])) {
                foreach ($performance_graph['dates'] as $key => $performance_graph_v) {
                    $performance_dates .= "'".$performance_graph_v."',";
                    $costs .= "'".round($performance_graph['cost'][$key])."',";
                    $cost_per_1000_imp .= "'".round($performance_graph['cost_per_1000_impressions'][$key])."',";
                    $cost_per_click .= "'".round($performance_graph['cost_per_click'][$key])."',";
                    $reveneu_per_click .= "'".round($performance_graph['revenue_per_click'][$key])."',";
                    $total_value .= "'".round($performance_graph['total_value'][$key])."',";
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
                    'campaign_notes' => $campaign_notes_for_campaign,
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

        $googleAds = GoogleAd::where('client_id', $user->id)->get();

        return response()->json([
            'campaign' => $campaign ?? [],
            'campaign_notes' => $campaign_notes,
            'ads_group' => $ads_group ?? [],
            'keywords' => $keywords ?? [],
            'ads' => $ads ?? [],
            'performance_device' => $devices ?? [],
            'get_customers' => GoogleAdsAccount::get(),
            'customer_account_id' => $user->customer_id ?? '',
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
            'googleAds' => $googleAds,
        ]);
    }

    public function save_google_report(Request $request)
    {
        $user = auth('web')->user() ?? auth('api')->user();

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

        $devloper_token = config('services.google.developer_token');
        $acct_id = $user->customer_id;

        $get_client = $this->getAdminClient();
        $get_access_token = $get_client->getAccessToken();
        $access_token = $get_access_token['access_token'];

        if (!empty($access_token)) {
            $get_google_campaigns = $this->get_google_campaigns($start_date, $end_date, $acct_id, $access_token, $devloper_token);
            $get_google_ads_group = $this->get_google_ads_group($start_date, $end_date, $acct_id, $access_token, $devloper_token);
            $get_google_keywords = $this->get_google_keywords($start_date, $end_date, $acct_id, $access_token, $devloper_token);
            $get_google_ads = $this->get_google_ads($start_date, $end_date, $acct_id, $access_token, $devloper_token);
            $get_performance_devices = $this->get_performance_devices($start_date, $end_date, $acct_id, $access_token, $devloper_token);
            $get_summary_graph_data = $this->get_summary_graph_data($start_date, $end_date, $acct_id, $access_token, $devloper_token);
            $get_performance_data = $this->get_performance_data_for_graph($start_date, $end_date, $acct_id, $access_token, $devloper_token);

            GoogleReport::updateOrCreate([
                'act_id' => $acct_id,
                'client_id' => $user->id,
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

            return response()->json([
                'success' => 'Google Report Data Fetch Successfully',
                'reload' => true,
            ]);
        }
    }

    public function campaign_note_save(Request $request)
    {
        $rules = [
            'note_date' => 'required',
            'campaign' => 'required',
            'notes' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $campaign_note = new CampaignNote;
        $campaign_note->note_date = $request->note_date;
        $campaign_note->campaign_name = $request->campaign;
        $campaign_note->note = $request->notes;
        $campaign_note->ads_report = 'google_ads_report';
        $campaign_note->save();

        return response()->json([
            'success' => 'Campaign Notes Added Successfully',
            'redirect' => route('user.google-ads-report.google_ads_report'),
        ]);
    }

    public function campaign_note_delete($id)
    {
        $campaign_note = CampaignNote::hashidFind($id);
        $campaign_note->delete();

        return response()->json([
            'success' => 'Campaign Note Deleted Successfully',
            'redirect' => route('client.google-ads-report.google_ads_report'),
        ]);
    }
}
