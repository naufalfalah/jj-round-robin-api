<?php

namespace App\Imports;

use App\Models\LeadClient;
use App\Models\LeadData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class AdminLeadImport implements ToCollection
{
    private $client_id;

    public function __construct($client_id)
    {
        $this->client_id = $client_id;
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        if (!empty($collection)) {
            try {
                DB::beginTransaction();

                foreach ($collection as $key => $value) {
                    if ($key > 0) {

                        $client_lead = new LeadClient;
                        $client_lead->client_id = $this->client_id;
                        $client_lead->name = $value[0];
                        $client_lead->email = $value[1];
                        $client_lead->mobile_number = $value[2];
                        $client_lead->user_type = 'admin';
                        $client_lead->added_by_id = auth('admin')->id();
                        $client_lead->save();

                        for ($i = 3; $i < count($value); $i++) {
                            if (isset($collection[0][$i],$value[$i]) && !empty($value[$i])) {
                                $lead_data = new LeadData();
                                $lead_data->lead_client_id = $client_lead->id;
                                $lead_data->key = $collection[0][$i] ?? '';
                                $lead_data->value = $value[$i] ?? '';
                                $lead_data->user_type = 'admin';
                                $lead_data->added_by_id = auth('admin')->id();
                                $lead_data->save();
                            }
                        }
                    }
                }

                DB::commit();
            } catch (\Exception $e) {
                // Something went wrong, rollback the transaction
                DB::rollback();

                // Access the error message
                $errorMessage = $e->getMessage();

                // You can log or handle the error message as needed
                // For example, you can throw a custom exception with a more specific message
                throw new \Exception("Error saving lead clients: $errorMessage");
            }
        }
    }
}
