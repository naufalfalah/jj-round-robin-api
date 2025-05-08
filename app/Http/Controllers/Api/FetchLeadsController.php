<?php

namespace App\Http\Controllers\Api;

use App\Constants\LeadConstant;
use App\Http\Controllers\Controller;
use App\Models\Ads;
use App\Models\AdsInvoice;
use App\Models\ClientLeadFilter;
use App\Models\ClientMessageTemplate;
use App\Models\ClientWallet;
use App\Models\JunkLead;
use App\Models\LeadClient;
use App\Models\LeadData;
use App\Models\LeadSource;
use App\Models\SubAccount;
use App\Models\Transections;
use App\Models\User;
use App\Models\UserSubAccount;
use App\Models\WpMessageTemplate;
use App\Services\GoogleAdsService;
use App\Traits\AdsSpentTrait;
use App\Traits\GoogleTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @group Fetch Lead
 */
class FetchLeadsController extends Controller
{
    use AdsSpentTrait, GoogleTrait;

    public function save($client_id, Request $request)
    {
        // dd($request->all());
        $client_id = hashids_decode($client_id);
        $find_user = User::findOrfail($client_id);

        try {
            DB::beginTransaction();

            //   code for junk lead
            $is_admin_spam = LeadClient::where('email', $request->email)->orWhere('mobile_number', $request->mobile_number)->get();
            $is_admin_spam = $is_admin_spam->where('is_admin_spam', 1)->count();

            if ($is_admin_spam > 0) {

                $junk_lead = new JunkLead;
                $junk_lead->lead_data = json_encode($request->all());
                $junk_lead->lead_type = 'webhook';
                $junk_lead->save();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'msg' => 'Lead was flagged as spam and saved to junk leads.',
                ]);
            }
            // code for junk lead

            $check_spam_exist = LeadClient::where('client_id', $client_id)
                ->where('status', 'spam')
                ->where(function ($query) use ($request) {
                    $query->where('email', $request->email)
                        ->orWhere('mobile_number', $request->mobile_number);
                })->count();
            if ($check_spam_exist > 0) {
                $junk_lead = new JunkLead;
                $junk_lead->lead_data = json_encode($request->all());
                $junk_lead->lead_type = 'webhook';
                $junk_lead->save();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'msg' => 'This Is Junk Lead Save Successfully',
                ]);
            }

            $lead_source = LeadSource::where('key', $request->source_type)->first();
            $source_type_id = $lead_source ? $lead_source->id : LeadSource::where('key', 'Unknown')->first()->id;

            $existing_lead = LeadClient::where('client_id', $client_id)
                ->where('source_type_id', $source_type_id)
                ->where(function ($query) use ($request) {
                    $query->where('email', $request->email)
                        ->orWhere('mobile_number', $request->mobile_number);
                })->count();

            if ($existing_lead > 0) {
                $junk_lead = new JunkLead;
                $junk_lead->lead_data = json_encode($request->all());
                $junk_lead->save();

                return response()->json([
                    'success' => false,
                    'msg' => 'Lead already exists with the same email, mobile number, and source type',
                ]);
            }

            $client_lead = new LeadClient;
            $client_lead->client_id = $find_user->id;
            $client_lead->name = $request->name;
            $client_lead->email = $request->email;
            $client_lead->mobile_number = $request->mobile_number;
            $client_lead->lead_type = 'webhook';
            $client_lead->source_type_id = $source_type_id;
            $client_lead->added_by_id = $find_user->id;
            $client_lead->save();

            $sheet_name = 'webhook';
            $spreadsheet_id = $find_user->spreadsheet_id;
            if (!empty($spreadsheet_id)) {
                $this->saveToGoogleSheet($request->name, $request->email, $request->mobile_number, $request->additional_data, $sheet_name, $spreadsheet_id);
            }

            $lead_data = [];
            if (!empty($request->additional_data) && count($request->additional_data) > 0) {
                foreach ($request->additional_data as $k => $val) {
                    $lead_data[] = [
                        'lead_client_id' => $client_lead->id,
                        'key' => $val['key'],
                        'value' => $val['value'],
                        'added_by_id' => $find_user->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                LeadData::insert($lead_data);
            }

            $lead_id = hashids_encode($client_lead->id);
            send_whatsapp_msg('Thank You', $client_lead->mobile_number);
            send_push_notification('New Lead', 'New Lead Add From webhook', $client_lead->client_id, $client_lead->id);

            DB::commit();

            return response()->json([
                'success' => true,
                'msg' => 'Lead Added Successfully',
            ]);

        } catch (\Exception $e) {

            DB::rollback();

            $errorMessage = $e->getMessage();

            throw new \Exception("Error saving lead webhook: $errorMessage");
        }
    }

    public function get_lead_form_website(Request $request, $que_lead = null)
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

        $check_referer_exist = Ads::where('website_url', $referer)->where('status', 'running')->first();

        if ($check_referer_exist) {

            //   code for junk lead
            $is_admin_spam = LeadClient::where('is_admin_spam', 1)
                ->where(function ($query) use ($request) {
                    $query->where('email', $request->email)
                        ->orWhere('mobile_number', $request->mobile_number);
                })->count();

            if ($is_admin_spam > 0) {

                $junk_lead = new JunkLead;
                $junk_lead->lead_data = json_encode($request->all());
                $junk_lead->lead_type = 'webhook';
                $junk_lead->save();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'msg' => 'Lead was flagged as spam and saved to junk leads.',
                ]);
            }
            // code for junk lead

            $check_spam_exist = LeadClient::where('client_id', $check_referer_exist->client_id)
                ->where('status', 'spam')
                ->where(function ($query) use ($request) {
                    $query->where('email', $request->email)
                        ->orWhere('mobile_number', $request->mobile_number);
                })->count();
            if ($check_spam_exist > 0) {
                $junk_lead = new JunkLead;
                $junk_lead->lead_data = json_encode($request->all());
                $junk_lead->sub_account_id = $check_referer_exist->id ?? '';
                $junk_lead->sub_account_url = $request->source_url ?? '';
                $junk_lead->save();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'msg' => 'This Is Junk Lead Save Successfully',
                ]);
            }

            $check_email_exist = LeadClient::where('client_id', $check_referer_exist->client_id)
                ->where('ads_id', $check_referer_exist->id)
                ->where(function ($query) use ($request) {
                    $query->where('email', $request->email)
                        ->orWhere('mobile_number', $request->mobile_number);
                })->count();
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

                if (!empty($check_referer_exist) && !empty($leads)) {
                    $ads_lead = new LeadClient;
                    if ($request->status == 'DNC Registry') {
                        $ads_lead->client_id = 0;
                        $ads_lead->status = 'DNC Registry';
                    } else {
                        $ads_lead->client_id = $check_referer_exist->client_id;
                        $ads_lead->ads_id = $check_referer_exist->id;
                    }
                    $ads_lead->name = $request->name ?? '';
                    $ads_lead->email = $request->email ?? '';
                    $ads_lead->mobile_number = $request->mobile_number ?? '';
                    $ads_lead->lead_type = 'ppc';
                    $ads_lead->added_by_id = $check_referer_exist->client_id;
                    $ads_lead->save();

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
                        send_push_notification('New Lead', 'New Lead Add From PPC', $ads_lead->client_id, $ads_lead->id);
                    }

                    if ($que_lead != null) {
                        $upd_status = JunkLead::find($que_lead->id);
                        $upd_status->is_send = 1;
                        $upd_status->update();
                    }

                    if (isset($ads_lead->email) && !empty($ads_lead->email)) {
                        $leadMessage = 'New Lead Please take note!
===========================
Hello '.$check_referer_exist->client->client_name.", you have a new lead:
- Name: {$ads_lead->name}
- Email: {$ads_lead->email}
- Mobile Number: https://wa.me/+65{$ads_lead->mobile_number}";
                    } else {
                        $leadMessage = 'New Lead Please take note!
===========================
Hello '.$check_referer_exist->client->client_name.", you have a new lead:
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

                    $url = $check_referer_exist->discord_link;
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
                    if (isset($check_msg_status->status) && $check_msg_status->status === 'Enable') {

                        if ($request->status != 'DNC Registry') {
                            if ($request->mobile_number && !empty($request->mobile_number)) {
                                $get_message = ClientMessageTemplate::where('client_id', $ads_lead->id)->first();
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
                }

                DB::commit();

                $response = $this->verifySalesperson($request->mobile_number);
                if (@$response->result) {
                    $ads_lead->user_status = 'agent';
                    $ads_lead->registration_no = $response->data->reg_number;
                    $ads_lead->save();
                }

                return response()->json([
                    'success' => true,
                    'msg' => 'Lead Send Successfully',
                ]);
            }

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

                return response()->json([
                    'success' => true,
                    'msg' => 'Lead Send Successfully',
                ]);
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

    public function week_payment()
    {
        $get_sub_accounts = SubAccount::get();

        foreach ($get_sub_accounts as $sub_account) {
            $user_sub_accounts = UserSubAccount::where('sub_account_id', $sub_account->id)->get();

            foreach ($user_sub_accounts as $user_sub_account) {
                $ads = Ads::where('user_sub_account_id', $user_sub_account->id)->whereIn('status', ['running', 'pause'])->get();

                foreach ($ads as $ad) {

                    $this_weak_payment = $this->client_payment($ad->client_id, $sub_account->id);
                    if ($this_weak_payment <= 0) {
                        continue;
                    }

                    $dates = $this->get_user_start_end_time($ad->client_id);
                    $weekStartDate = $dates['start_date']->format('Y-m-d H:i:s');
                    $weekEndDate = $dates['end_date']->format('Y-m-d H:i:s');

                    $trans = Transections::where('client_id', $ad->client_id);
                    $remaining_amount = ($trans->sum('amount_in') - $trans->sum('amount_out'));
                    $available_balance = $remaining_amount - $this_weak_payment;

                    $add_transaction = new Transections;
                    $add_transaction->available_balance = $available_balance;
                    $add_transaction->client_id = $ad->client_id;
                    $add_transaction->amount_out = $this_weak_payment;
                    $add_transaction->ads_id = $ad->id;
                    $add_transaction->save();
                    if ($available_balance <= 50) {
                        $ads = Ads::find($ad->id);
                        $ads->status = 'pause';
                        $ads->spend_amount = ($ads->spend_amount + $this_weak_payment);
                        $ads->save();
                    }

                    $dates = $this->get_user_start_end_time($ad->client_id);
                    $weekStartDate = $dates['start_date']->format('Y-m-d');
                    $weekEndDate = $dates['end_date']->format('Y-m-d');

                    $ads_invoice = new AdsInvoice;
                    $ads_invoice->client_id = $ad->client_id;
                    $ads_invoice->invoice_date = date('Y-m-d');
                    $ads_invoice->gst = ($this_weak_payment * (9 / 100));
                    $ads_invoice->total_amount = $this_weak_payment;
                    $ads_invoice->total_lead = $this->this_week_client_leads($ad->client_id);
                    $ads_invoice->start_date = $weekStartDate;
                    $ads_invoice->end_date = $weekEndDate;
                    $ads_invoice->save();
                }
            }
        }
    }

    public function monthly_payment()
    {
        $googleAdsService = new GoogleAdsService;
        $ads = Ads::whereIn('status', ['running', 'pause'])->get();

        foreach ($ads as $ad) {
            $checkAdsInvoice = AdsInvoice::where('ads_id', $ad->id)->first();
            if ($checkAdsInvoice) {
                continue;
            }

            // Monthly payment
            $monthly_payment = $this->monthly_client_payment($ad->client_id, $ad->id);

            // Fetch 1 Topups of client that FeeFlag = False
            $topupFee = 0;

            // Check sub wallet
            $topups = Transections::where('ads_id', $ad->id)
                ->where('fee_flag', 0)
                ->whereIn('topup_type', ['stripe'])
                ->where('status', 'completed')
                ->get();

            if ($topups->isEmpty()) {
                // Change to main wallet
                $topups = ClientWallet::where('client_id', $ad->client_id)
                    ->where('fee_flag', 0)
                    ->whereIn('topup_type', ['stripe'])
                    ->where('status', 'completed')
                    ->limit(1)
                    ->get();
            }

            if ($topups->isNotEmpty()) {
                foreach ($topups as $topup) {
                    if ($topup->topup_type == 'stripe') {
                        $topupFee = round($topup->amount_in * (3.9 / 100) + 0.60);
                    }
                }
                $topup->transaction_fee = $topupFee;
                $topup->save();
            }

            if ($monthly_payment <= 0) {
                continue;
            }

            $totalAmount = $monthly_payment + $topupFee;

            if ($totalAmount <= 0) {
                continue;
            }

            // Check balance & budget
            $transactions = Transections::where('client_id', $ad->client_id)
                ->where('ads_id', $ad->id)
                ->where('status', 'completed');
            $balance = ($transactions->sum('amount_in') - $transactions->sum('amount_out'));
            if ($ad->spend_type == 'daily') {
                $budget = ($ad->daily_budget * 30) ?? $ad->daily_budget;
            }
            $remaining_amount = $balance < $budget ? $balance : $budget;
            $available_balance = $remaining_amount - $totalAmount;

            $gst = (($totalAmount - $topupFee) * (9 / 100));

            $add_transaction = new Transections;
            $add_transaction->available_balance = $available_balance;
            $add_transaction->client_id = $ad->client_id;
            $add_transaction->amount_out = $totalAmount;
            $add_transaction->status = 'completed';
            $add_transaction->topup_type = 'invoice_payment';
            $add_transaction->ads_id = $ad->id;
            $add_transaction->save();

            $ad->spend_amount = $available_balance;
            $ad->save();

            // Set the Fee Flag true as Fee is deducted
            if ($topup) {
                $topup->fee_flag = true;
                $topup->save();
            }

            if ($available_balance <= 50) {
                $ad->status = 'pause';
                $ad->save();

                $customerId = $ad->client->customer_id;
                $activeCampaigns = $googleAdsService->getActiveCampaigns($customerId);
                if (isset($activeCampaigns['results'])) {
                    foreach ($activeCampaigns['results'] as $activeCampaign) {
                        $campaignResourceName = $activeCampaign['campaign']['resourceName'];
                        $updateRequestBody = [
                            'status' => 'PAUSED',
                        ];

                        $googleAdsService->updateCampaign($customerId, $campaignResourceName, $updateRequestBody);
                    }
                }
            }

            // Generate Invoice
            $dates = $this->monthly_user_start_end_time($ad->client_id, $ad->id, 'ppc');
            $monthStartDate = $dates['start_date']->format('Y-m-d');
            $monthEndDate = $dates['end_date']->format('Y-m-d');

            $ads_invoice = new AdsInvoice;
            $ads_invoice->ads_id = $ad->id;
            $ads_invoice->client_id = $ad->client_id;
            $ads_invoice->invoice_date = date('Y-m-d');
            $ads_invoice->card_charge = $topupFee;
            $ads_invoice->gst = $gst;
            $ads_invoice->total_amount = $totalAmount;
            $ads_invoice->total_lead = $this->monthly_client_leads($ad->client_id, $ad->id, 'ppc');
            $ads_invoice->start_date = $monthStartDate;
            $ads_invoice->end_date = $monthEndDate;
            $ads_invoice->save();
        }

        return true;
    }

    private function findRunningAds($user_ids, $get_sub_account_id)
    {
        // Check if there are any running ads
        $ids = $user_ids;
        $sub_account_id = $get_sub_account_id;
        $runningAdsCount = Ads::where('status', 'running')->whereIn('client_id', $ids)->count();
        if ($runningAdsCount == 0) {
            return response()->json([
                'success' => true,
                'msg' => 'Running Ads Not Found! Que Lead Save Successfully',
            ]);
        }

        // Find the first running ad with lead_status '0'
        $adsList = Ads::where('status', 'running')->whereIn('client_id', $ids)->where('lead_status', '0')->latest()->first(['id', 'client_id', 'lead_status', 'discord_link']);

        // If no ads found with lead_status '0', update all running ads to have lead_status '0' and retry
        if (empty($adsList)) {
            Ads::where('status', 'running')->whereIn('client_id', $ids)->update(['lead_status' => 0]);

            return $this->findRunningAds($ids, $sub_account_id);
            // Recursively call the function to retry finding ads
        }

        $this_weak_payment = $this->client_payment($adsList->client_id, $sub_account_id);

        $trans = Transections::where('client_id', $adsList->client_id);

        $remaining_amount = ($trans->sum('amount_in') - $trans->sum('amount_out'));

        $available_balance = $remaining_amount - $this_weak_payment;

        if ($available_balance <= 50) {
            $ads = Ads::find($adsList->id);
            $ads->status = 'pause';
            $ads->lead_status = 0;
            $ads->save();

            return $this->findRunningAds($ids, $sub_account_id);
        }

        return $adsList;
    }

    public function send_client_leads_on_discord($leads_start_time)
    {
        $get_leads = LeadClient::with('lead_data', 'clients')
            ->where('lead_type', 'ppc')->where('is_send_discord', '0')
            ->where('created_at', '>', $leads_start_time)
            ->limit(15)->get();

        if ($get_leads->count() > 0) {
            foreach ($get_leads as $key => $lead) {
                $clientLeadFilter = ClientLeadFilter::where('client_id', $lead->client_id)
                    ->first();

                if ($clientLeadFilter) {
                    $leadFilter = explode(',', $clientLeadFilter->lead_filters);

                    if (in_array(LeadConstant::FILTERS['junk']['value'], $leadFilter) && $this->is_junk($lead)) {
                        continue;
                    }

                    if (in_array(LeadConstant::FILTERS['duplicate']['value'], $leadFilter) && $this->is_duplicate($lead)) {
                        continue;
                    }

                    if (in_array(LeadConstant::FILTERS['dnc']['value'], $leadFilter) && $this->is_dnc($lead)) {
                        continue;
                    }

                    if (in_array(LeadConstant::FILTERS['bad_words']['value'], $leadFilter) && $this->has_bad_words($lead)) {
                        continue;
                    }
                }

                $get_discord_link = Ads::where('client_id', $lead->client_id)->latest()->first(['discord_link']);

                $leadMessage = 'New Lead Please take note!
===========================
Hello '.$lead->clients->client_name.", you have a new lead:
- Name: {$lead->name}
- Email: {$lead->email}
- Mobile Number: https://wa.me/+65{$lead->mobile_number}";

                if (!empty($lead->lead_data) && count($lead->lead_data) > 0) {
                    foreach ($lead->lead_data as $val) {
                        $leadMessage .= "\n- {$val['key']}: {$val['value']}";
                    }
                }

                $url = $get_discord_link->discord_link;
                $send_discord_msg = $this->send_discord_msg($url, $leadMessage);

                if (empty($send_discord_msg)) {
                    $lead->is_send_discord = 1;
                } else {
                    $lead->is_send_discord = 0;
                }
                $lead->save();
            }
        } else {
            return response()->json(['error' => 'No leads found.'], 422);
        }
    }

    private function is_junk($lead)
    {
        $junkLead = JunkLead::where('lead_data', 'LIKE', '%'.$lead->name.'%')
            ->orWhere('lead_data', 'LIKE', '%'.$lead->email.'%')
            ->orWhere('lead_data', 'LIKE', '%'.$lead->mobile_number.'%')
            ->first();

        return $junkLead !== null;
    }

    private function is_duplicate($lead)
    {
        return LeadClient::where('email', $lead->email)
            ->orWhere('mobile_number', $lead->mobile_number)
            ->exists();
    }

    private function is_dnc($lead)
    {
        return in_array($lead->email, $this->dnc_list());
    }

    private function dnc_list()
    {
        return [
            'example@example.com',
            'test@domain.com',
        ];
    }

    private function has_bad_words($lead)
    {
        foreach ([$lead->name, $lead->email, $lead->mobile_number] as $field) {
            foreach ($this->bad_words_list() as $badWord) {
                if (stripos($field, $badWord) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    private function bad_words_list()
    {
        return [
            'spam',
            'fraud',
            'scam',
        ];
    }

    public function send_que_leads()
    {

        $check_running_ads = Ads::where('status', 'running')->count();
        if ($check_running_ads == 0) {
            return response()->json([
                'success' => false,
                'message' => 'There Is No Running Ads.',
            ]);
        } else {
            $get_que_lead = JunkLead::where('status', 'que_lead')->where('is_send', '0')->first();
            if ($get_que_lead) {
                $request = new Request;
                $send_que_lead = $this->add_ppc_lead($request, $get_que_lead);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'There Is No Leads In Que.',
                ]);
            }
        }
    }

    private function send_discord_msg($url, $data)
    {
        $post_array = [
            'content' => $data,
            'embeds' => null,
            'attachments' => [],
        ];
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($post_array),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Cookie: __dcfduid=8ec71370974011ed9aeb96cee56fe4d4; __sdcfduid=8ec71370974011ed9aeb96cee56fe4d49deabe12bc0fc3d686d23eaa0b49af957ffe68eadec722cff5170d5c750b00ea',
            ],
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }

    private function extractBaseUrlPattern($url)
    {
        $parsedUrl = parse_url($url);

        return $parsedUrl['scheme'].'://'.$parsedUrl['host'].'/';
    }

    private function send_wp_message($client_number, $message)
    {

        $curl = curl_init();
        $api_key = config('app.wp_api_key');
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.p.2chat.io/open/whatsapp/send-message',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode([
                'to_number' => '+65'.$client_number,
                'from_number' => '+6589469107',
                'text' => $message,
            ]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'X-User-API-Key: '.$api_key,
            ],
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            echo 'Curl error: '.curl_error($curl);
        }

        curl_close($curl);

        return $response;
    }

    public function add_message_to_user(Request $request)
    {

        $get_clients = User::all();

        $get_message = ClientMessageTemplate::first();

        if ($get_message) {
            $data = [];

            foreach ($get_clients as $client) {
                $data[] = [
                    'client_id' => $client->id,
                    'message_template' => $get_message->message_template,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            ClientMessageTemplate::insert($data);

            return response()->json(['message' => 'Messages assigned to all users successfully.']);
        } else {
            return response()->json(['message' => 'No message template found.'], 404);
        }
    }

    private function verifySalesperson($mobileNumber)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.jomejourney-portal.com/api/verify-salesperson',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
            "phone":"'.$mobileNumber.'"
        }',
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);
    }

    public function assign_template_to_clients()
    {

        $adminTemplate = WpMessageTemplate::latest()->first();

        if (!$adminTemplate) {
            return response()->json(['error' => 'No admin message template found'], 404);
        }

        $clients = User::all();

        if ($clients->isEmpty()) {
            return response()->json(['error' => 'No clients found'], 404);
        }

        ClientMessageTemplate::truncate();

        $clientTemplates = [];
        foreach ($clients as $client) {
            $clientTemplates[] = [
                'client_id' => $client->id,
                'message_template' => $adminTemplate->wp_message,
                'from_number' => $adminTemplate->from_number,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        $client_message_template = ClientMessageTemplate::insert($clientTemplates);
        if ($client_message_template) {
            return response()->json(['message' => 'Messages assigned to all users successfully.']);
        } else {
            return response()->json(['error' => 'Failed to assign admin message template: '], 500);
        }
    }
}
