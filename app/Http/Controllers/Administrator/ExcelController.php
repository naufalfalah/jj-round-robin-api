<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeadClient;
use App\Models\User;
use Carbon\Carbon;
use App\Exports\LeadClientsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use App\Imports\LeadImport;

class ExcelController extends Controller
{
    public function index()
    {
        $data = [
            'breadcrumb_main' => 'Lead Export ',
            'breadcrumb' => 'Lead Export',
            'title' => 'Lead Export',
            'leads' => User::with('sub_account')->get(),
        ];
        return view('admin.export.index')->with($data);
    }


    public function export_to_excel(Request $request)
    {
        $rules = [
            'client' => 'required',
            'daterange' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $dates = explode(' - ', $request->daterange);

        $start_date = Carbon::createFromFormat('m/d/Y', $dates[0])->startOfDay();
        $end_date = Carbon::createFromFormat('m/d/Y', $dates[1])->endOfDay();

        $leadData = LeadClient::with('clients', 'lead_data')
            ->where('client_id', $request->client)
            ->whereBetween('created_at', [$start_date, $end_date])
            ->get();
       

        $lead_array[] = [];

        foreach ($leadData as $lead) {
            $forCallStatus = Carbon::now()->diffInDays($lead->created_at) > 30 ? 'Please Check DNC' : 'Ready to Call';
            $additionalData = $lead->lead_data->map(function ($item) {
                return $item->key . ': ' . $item->value;
            })->implode(', ');

            $lead_array[] = [
                'Client Name' => $lead->clients->client_name ?? '',
                'Name' => $lead->name ?? '-',
                'Email' => $lead->email ?? '-',
                'Phone' => $lead->mobile_number ?? '-',
                'Additional Data' => $additionalData ?? '-',
                'Lead Date & Time' => $lead->created_at->format('Y-m-d H:i:s') ?? '-',
                'Status' => $lead->admin_status ?? '-',
                'For Call Status' => $forCallStatus
            ];
        }

        $client_data = User::with('sub_account')->where('id', $request->client)->first();
        $fileName = $client_data->client_name . '_'.str_replace(' ', '_', $client_data->sub_account->sub_account_name).'.xlsx';

        return Excel::download(new LeadClientsExport($lead_array), $fileName);
    }


    public function import_leads(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_import' => 'required',
            'import_leads' => 'required|file|mimes:xlsx',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $client_id = $request->client_import;

        $file = $request->file('import_leads');

        try {
            $import = new LeadImport($client_id);
            Excel::import($import, $file);

            return response()->json(['success' => 'Leads imported successfully', 'reload' => true]);
        } catch (\Exception $e) {
            return response()->json(['error', 'Error importing leads: ' . $e->getMessage()]);
        }
    }

    public function updateLeadStatusOnServer($domain_url, $mobile_number, $status)
    {

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://janicez87.sg-host.com/update_lead_status.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => http_build_query([
                'domain_url' => $domain_url,
                'mobile_number' => $mobile_number,
                'status' => $status
            ]),
        ]);

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }
}
