<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeadClient;
use Illuminate\Support\Facades\DB;

class MigrateDataController extends Controller
{
    public function migrateUsers()
    {
        $subAccounts = DB::connection('old_db')->table('sub_accounts')->orderBy('created_at')->get();

        $subAccountIdMap = [];

        foreach ($subAccounts as $subAccount) {
            $newSubAccountId = DB::connection('new_db')->table('sub_accounts')->insertGetId([
                'sub_account_name' => $subAccount->sub_account_name,
                'sub_account_url' => $subAccount->sub_account_url,
                'status' => $subAccount->status,
                'created_at' => $subAccount->created_at,
                'updated_at' => $subAccount->updated_at,

            ]);

            $subAccountIdMap[$subAccount->id] = $newSubAccountId;
        }

        $users = DB::connection('old_db')->table('users')->orderBy('created_at')->get();

        $userIdMap = [];

        foreach ($users as $user) {
            $newSubAccountId = isset($subAccountIdMap[$user->sub_account_id]) ? $subAccountIdMap[$user->sub_account_id] : null;

            $newUserId = DB::connection('new_db')->table('users')->insertGetId([
                'sub_account_id' => $newSubAccountId,
                'client_name' => $user->client_name ?? '',
                'phone_number' => $user->phone_number ?? '',
                'agency_id' => $user->agency_id ?? '',
                'industry_id' => $user->industry_id ?? '',
                'package' => $user->package ?? '',
                'image' => $user->image ?? '',
                'email' => $user->email ?? '',
                'address' => $user->address ?? '',
                'email_verified_at' => $user->email_verified_at ?? '',
                'password' => $user->password ?? '',
                'remember_token' => $user->remember_token ?? '',
                'provider_id' => $user->provider_id ?? '',
                'provider_name' => $user->provider_name ?? '',
                'google_access_token' => $user->google_access_token ?? '',
                'spreadsheet_id' => $user->spreadsheet_id ?? '',
                'created_at' => $user->created_at ?? '',
                'updated_at' => $user->updated_at ?? '',
                'deleted_at' => $user->deleted_at ?? null,
            ]);

            // Map purane user ID ko naye ID ke sath
            $userIdMap[$user->id] = $newUserId;
        }

        $ads = DB::connection('old_db')->table('ads')->orderBy('created_at')->get();

        $adIdMap = [];

        foreach ($ads as $ad) {
            $newClientId = isset($userIdMap[$ad->client_id]) ? $userIdMap[$ad->client_id] : null;

            $newAdId = DB::connection('new_db')->table('ads')->insertGetId([
                'client_id' => $newClientId,
                'adds_title' => $ad->adds_title ?? '',
                'description' => $ad->description ?? '',
                'email' => $ad->email ?? '',
                'discord_link' => $ad->discord_link ?? '',
                'type' => $ad->type ?? '',
                'status' => $ad->status ?? '',
                'lead_status' => $ad->lead_status ?? '',
                'spend_amount' => $ad->spend_amount ?? null,
                'deleted_at' => $ad->deleted_at ?? null,
                'created_at' => $ad->created_at ?? '',
                'updated_at' => $ad->updated_at ?? '',
            ]);

            // Map purane ad ID ko naye ID ke sath
            $adIdMap[$ad->id] = $newAdId;
        }

        $walletTopUps = DB::connection('old_db')->table('wallet_top_ups')->orderBy('created_at')->get();

        $walletTopUpIdMap = [];

        foreach ($walletTopUps as $topUp) {
            $newClientId = isset($userIdMap[$topUp->client_id]) ? $userIdMap[$topUp->client_id] : null;

            $newTopUpId = DB::connection('new_db')->table('wallet_top_ups')->insertGetId([
                'client_id' => $newClientId,
                'topup_type' => $topUp->topup_type ?? '',
                'topup_amount' => $topUp->topup_amount ?? null,
                'status' => $topUp->status ?? '',
                'proof' => $topUp->proof ?? null,
                'added_by' => $topUp->added_by ?? null,
                'added_by_id' => $topUp->added_by_id ?? null,
                'approved_by' => $topUp->approved_by ?? null,
                'approve_at' => $topUp->approve_at ?? null,
                'deleted_at' => $topUp->deleted_at ?? null,
                'created_at' => $topUp->created_at ?? '',
                'updated_at' => $topUp->updated_at ?? '',
            ]);

            // Map purane wallet_top_up ID ko naye ID ke sath
            $walletTopUpIdMap[$topUp->id] = $newTopUpId;
        }

        $transactions = DB::connection('old_db')->table('transections')->orderBy('created_at')->get();

        foreach ($transactions as $transaction) {
            $newClientId = isset($userIdMap[$transaction->client_id]) ? $userIdMap[$transaction->client_id] : null;
            $newTopUpId = isset($walletTopUpIdMap[$transaction->topup_id]) ? $walletTopUpIdMap[$transaction->topup_id] : null;
            $newAdId = isset($adIdMap[$transaction->ads_id]) ? $adIdMap[$transaction->ads_id] : null;

            DB::connection('new_db')->table('transections')->insert([
                'client_id' => $newClientId,
                'amount_in' => $transaction->amount_in ?? null,
                'amount_out' => $transaction->amount_out ?? null,
                'vat_charges' => $transaction->vat_charges ?? null,
                'available_balance' => $transaction->available_balance ?? null,
                'topup_id' => $newTopUpId,
                'ads_id' => $newAdId,
                'deleted_at' => $transaction->deleted_at ?? null,
                'created_at' => $transaction->created_at ?? '',
                'updated_at' => $transaction->updated_at ?? '',
            ]);
        }

        $chunkSize = 1000;
        $leadIdMap = [];

        // Process lead_clients and populate leadIdMap
        DB::connection('old_db')->table('lead_clients')->orderBy('created_at')->chunk($chunkSize, function ($client_leads) use ($userIdMap, &$leadIdMap) {
            foreach ($client_leads as $lead) {
                $newClientId = isset($userIdMap[$lead->client_id]) ? $userIdMap[$lead->client_id] : null;

                $newLead = new LeadClient;
                $newLead->client_id = $newClientId;
                $newLead->name = $lead->name ?? '';
                $newLead->email = $lead->email ?? null;
                $newLead->mobile_number = $lead->mobile_number ?? null;
                $newLead->note = $lead->note ?? null;
                $newLead->status = $lead->status ?? 'unmarked';
                $newLead->lead_type = $lead->lead_type ?? 'manual';
                $newLead->follow_up_date_time = $lead->follow_up_date_time ?? null;
                $newLead->is_send_discord = $lead->is_send_discord ?? null;
                $newLead->is_verified = $lead->is_verified ?? null;
                $newLead->added_by_id = $newClientId ?? '';
                $newLead->user_type = $lead->user_type ?? 'user';
                $newLead->delete_by_type = $lead->delete_by_type ?? null;
                $newLead->delete_by_id = isset($userIdMap[$lead->delete_by_id]) ? $userIdMap[$lead->delete_by_id] : null;
                $newLead->admin_status = $lead->admin_status ?? null;
                $newLead->deleted_at = $lead->deleted_at ?? null;
                $newLead->created_at = $lead->created_at; // Ensure this is set correctly
                $newLead->updated_at = $lead->updated_at; // Ensure this is set correctly

                $newLead->save();

                $leadIdMap[$lead->id] = $newLead->id;
            }
        });

        DB::connection('old_db')->table('lead_data')->orderBy('created_at')->chunk($chunkSize, function ($client_leads) use ($userIdMap, &$leadIdMap) {
            foreach ($client_leads as $data) {
                $newLeadId = isset($leadIdMap[$data->lead_client_id]) ? $leadIdMap[$data->lead_client_id] : null;

                if ($newLeadId !== null) {
                    DB::connection('new_db')->table('lead_data')->insert([
                        'lead_client_id' => $newLeadId,
                        'key' => $data->key ?? '',
                        'value' => $data->value ?? null,
                        'user_type' => $data->user_type ?? 'user',
                        'added_by_id' => isset($userIdMap[$data->added_by_id]) ? $userIdMap[$data->added_by_id] : $data->lead_client_id,
                        'delete_by_type' => $data->delete_by_type ?? null,
                        'delete_by_id' => isset($userIdMap[$data->delete_by_id]) ? $userIdMap[$data->delete_by_id] : null,
                        'deleted_at' => $data->deleted_at ?? null,
                        'created_at' => $data->created_at ?? '',
                        'updated_at' => $data->updated_at ?? '',
                    ]);
                }
            }
        });

        echo 'Data migrated successfully!';
    }
}
