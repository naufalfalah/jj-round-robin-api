<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Imports\LeadClientImport;
use App\Models\LeadClient;
use App\Models\Ads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use App\Traits\GoogleTrait;

class LeadController extends Controller
{
    public function ppc_leads()
    {
        $auth_id = auth('web')->id();
        $data = [
            'breadcrumb' => 'PPC Leads',
            'title' => 'PPC Leads',
            'sub_accounts' => Ads::where('client_id', $auth_id)->latest()->get(),
        ];
        return view('client.lead_management.ppc_leads')->with($data);
    }
    public function get_leads(Request $request)
    {
        $auth_id = auth('web')->id();
        // for enum
        $enum_type = DB::select(DB::raw("SHOW COLUMNS FROM lead_clients WHERE Field = 'admin_status'"))[0]->Type;
        $enum_values = str_replace(['enum(', ')', "'"], '', $enum_type);
        $enum_values = explode(',', $enum_values);
        // for enum
       
        if (empty($request->ads_id)) {
            $client_leads = LeadClient::with('lead_data', 'clients', 'ads')->where('client_id', $auth_id)->where('lead_type', 'ppc')->latest()->get();
        } else {
            $client_leads = LeadClient::with('lead_data', 'clients', 'ads')->where('client_id', $auth_id)->where('ads_id', hashids_decode($request->ads_id))->where('lead_type', 'ppc')->latest()->get();
        }
        

        return DataTables::of($client_leads)
                ->addIndexColumn()
                ->addColumn('client_name', function ($lead) {
                    return $lead->clients->client_name ?? '-';
                })
                ->addColumn('ads_name', function ($lead) {
                    return $lead->ads->adds_title ?? '-';
                })
                ->addColumn('name', function ($lead) {
                    return $lead->name ?? '-';
                })
                ->addColumn('email', function ($lead) {
                    return $lead->email ?? '-';
                })
                ->addColumn('mobile_number', function ($lead) {
                    return $lead->mobile_number ?? '-';
                })
                ->addColumn('lead_data', function ($lead) {
                    return $lead->lead_data->map(function ($item) {
                        $key = Str::limit($item->key, 10);
                        $value = Str::limit($item->value, 10);
                        return "<span>$key: $value</span>";
                    })->implode('<br>');

                })

                ->addColumn('actions', function ($lead) {
                    $data = json_encode([
                        'name' => $lead->name,
                        'email' => $lead->email,
                        'mobile_number' => $lead->mobile_number,
                        'admin_status' => $lead->admin_status,
                        'lead_data' => $lead->lead_data
                    ]);
                    $actionsHtml = "<a href='javascript:void(0)' class='text-primary view_lead_detail_id'  data-data='{$data}' data-id='{$lead->id}' title='View Lead Detail'><i class='bi bi-eye-fill'></i></a>
                    ";

                    if ($lead->user_status == 'agent') {
                        $agent_detail = AgentDetail::where('registration_no', $lead->registration_no)->first();
                        $agentdata = json_encode([
                            'salesperson_name' => $agent_detail->salesperson_name ?? '-',
                            'registration_no' => $agent_detail->registration_no ?? '-',
                            'registration_start_date' => $agent_detail->registration_start_date ?? '-',
                            'registration_end_date' => $agent_detail->registration_end_date ?? '-',
                            'estate_agent_name' => $agent_detail->estate_agent_name ?? '-',
                            'estate_agent_license_no' => $agent_detail->estate_agent_license_no ?? '-'
                        ]);
                        $actionsHtml .= "&nbsp;&nbsp <a href='javascript:void(0)' class='text-success agent_specific_action' data-data='{$agentdata}' data-registration='{$lead->registration_no}' data-id='{$lead->id}' title='Agent Specific Action'><i class='bi bi-info-circle-fill'></i></a>";
                    }

                    return $actionsHtml;
                })


              ->addColumn('admin_status', function ($lead) use ($enum_values) {
                  $dropdown = '<select class="admin-status-dropdown form-select" name="admin_status" data-id="' . $lead->id . '">';
                  foreach ($enum_values as $value) {
                      $selected = $lead->admin_status == $value ? 'selected' : '';
                      $dropdown .= "<option value='{$value}' {$selected}>{$value}</option>";
                  }
                  $dropdown .= '</select>';
                  return $dropdown;
              })
                ->filter(function ($query) {
                    if (request()->input('search')) {
                        $query->where(function ($search_query) {
                            $search_query->whereLike(['status','topup_amount'], request()->input('search'));
                        });
                    }
                })
                ->rawColumns(['actions','lead_data','admin_status'])
            ->make(true);
    }
    public function lead_status(Request $request)
    {
        $lead_client = LeadClient::find($request->lead_id);
        $lead_client->admin_status = $request->admin_status;
        $lead_client->save();

        return response()->json([
            'success' => 'Status Changed Successfully',
        ]);
    }
}
