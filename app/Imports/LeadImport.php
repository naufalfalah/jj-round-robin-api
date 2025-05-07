<?php

namespace App\Imports;

use App\Models\LeadClient;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LeadImport implements ToModel, WithHeadingRow
{
    protected $client_id;

    public function __construct($client_id)
    {
        $this->client_id = $client_id;
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
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
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

    public function model(array $row)
    {
        $lead = LeadClient::with('clients')->where('mobile_number', $row['phone'])
                         ->where('client_id', $this->client_id)
                         ->first();


        if ($lead) {
            $lead->admin_status = $row['status'];
            $lead->save();

            $this->updateLeadStatusOnServer($lead->clients->sub_account->sub_account_url, $row['phone'], $row['status']);
        }

        return $lead;
    }

}
