<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeadClient;
use App\Models\LeadData;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FetchLeadsFromSheetController extends Controller
{
    // public function get_save_lead() {
    //     $jsonData = '[
    //         {
    //             "AGENT NAME": "JEREMY TAN",
    //             "DATE CALLED": "19/11/2023",
    //             "CLIENT NAME": "PEARLYN GOH",
    //             "CLIENT NUMBER": "81610752",
    //             "EMAIL": "goh.pearlyn@yahoo.com",
    //             "TAB NAME": "ECOMMERCE BUY\/SELL\/RENT",
    //             "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1r8XWAuyKHTQRTFEXbinUXJw9lfu973-4\/edit#gid=1544010329-",
    //             "REMARKS": "Dialed once, spoke to her, but she just directly said not interested\n\n- CALLED ONCE\n- 22\/04\/2024",
    //             "PDPC COMPLAINT DATE": "22\/04\/2024\n10:52AM",
    //             "REMARKS .1": "Telemarketing call.",
    //             "AD ACCOUNT": null,
    //             "WEBSITE": null
    //         },
    //         {
    //             "AGENT NAME": "CHIA YEI",
    //             "DATE CALLED": "18/04/2024",
    //             "CLIENT NAME": "GOH SWEE CHOO VERONICA",
    //             "CLIENT NUMBER": "96838071",
    //             "EMAIL": "veronveron8896@gmail.com",
    //             "TAB NAME": "23 FEB-JOME",
    //             "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1fmuySMpZ5VT3caPVUovt5-2auth24wS7\/edit?gid=856480102#gid=856480102",
    //             "REMARKS": "We had a smooth conversation with the client and asked her properly about her property.\n\n- (CALLED ONCE)\n- APRIL 18, 2024 (THURSDAY)",
    //             "PDPC COMPLAINT DATE": "18\/04\/2024\n03:06PM",
    //             "REMARKS .1": "Caller identified herself as being from Propnex. She asked if I had any property to sell or rent. When I answered no, she then asked if I was looking for any property. replied no, and she ended the call.",
    //             "AD ACCOUNT": null,
    //             "WEBSITE": null
    //         },
    //         {
    //             "AGENT NAME": "ALVIN CHOO AND CAROLYN CHOO",
    //             "DATE CALLED": "12/04/2024",
    //             "CLIENT NAME": "CHAI XIAN YANG",
    //             "CLIENT NUMBER": "93369220",
    //             "EMAIL": "NO EMAIL: 6220chai.xianyang@gmail.com",
    //             "TAB NAME": "CAVEATS 16",
    //             "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1v9S1GOOUBRhtNgkhr6-i2ctSq3fIz7yku8gxBHDp0J4\/edit?usp=drive_web&ouid=109735040497214292428",
    //             "REMARKS": "MISMATCH\n\nHowever, I dialed this contact on May 17, 2024, called once, spoke to him and asked and discussed his inquiry about, and ask him is he is still looking for a property, however, he said he is not looking, and he is under DNC list, I tried to say I would update our system but the call was end.",
    //             "PDPC COMPLAINT DATE": "12\/04\/2024\n05:30PM",
    //             "REMARKS .1": "Contacted me on sale of property. Informed that I was on DNC and abruptly put down. This has been happening for the past few years.",
    //             "AD ACCOUNT": null,
    //             "WEBSITE": null
    //         },
    //         {
    //             "AGENT NAME": "JASLIN AND GEORGE",
    //             "DATE CALLED": "10/04/2024",
    //             "CLIENT NAME": "EUNICE",
    //             "CLIENT NUMBER": "91993401",
    //             "EMAIL": "eunice5@gmail.com",
    //             "TAB NAME": "LENTOR",
    //             "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1JPNgo9UTxTVFdChgiLgYzSX8SZA90CXoeODqThYVWfU\/edit?gid=147076075#gid=147076075",
    //             "REMARKS": "We have smooth conversation and she just said not interested\n\n-CALLED TWICE\nFEBRUARY 02, 2024- NOT INTERESTED\nAPRIL 10, 2024 - NOT INTERESTED",
    //             "PDPC COMPLAINT DATE": "10\/04\/2024\n02:37PM",
    //             "REMARKS .1": "a lady with PH accent called and already knows my name. no idea how she got my name and phone number. she say she is from some company and called me if i am interested to view condo showflat.",
    //             "AD ACCOUNT": null,
    //             "WEBSITE": null
    //         },
    //         {
    //             "AGENT NAME": "Robbie x Jen",
    //             "DATE CALLED": "25/03/2024",
    //             "CLIENT NAME": "HENG WENG SOON IAN",
    //             "CLIENT NUMBER": "96827440",
    //             "EMAIL": "ianalmighty66@gmail.com",
    //             "TAB NAME": "PRIVATE OWNER MID 2023 (CALLED ONCE)",
    //             "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1Q_hBsjTtJf0XYFM2MOKzvfx7JZkJ2O7mStr1qLtSqO8\/edit#gid=0",
    //             "REMARKS": "I identified myself as calling from Propnex. I inquired if he had any plans to sell or rent his property. He mentioned he`s on the DNC list and that we breached PDPA. I apologized and assured him that I would update our list, but he stated he would report it.\n\n- CALLED ONCE\n- 25\/03\/2024",
    //             "PDPC COMPLAINT DATE": "25\/03\/2024\n05:52PM",
    //             "REMARKS .1": "A stranger Called saying she is from Propnex (confirmed 3 times), she asked if I want to sell my property, told her I am registered with DNC and will report this breach.",
    //             "AD ACCOUNT": null,
    //             "WEBSITE": null
    //         },
    //         {
    //             "AGENT NAME": "CHRIS (HUTTONS)",
    //             "DATE CALLED": "22/03/2024",
    //             "CLIENT NAME": "STEVE TAN WEI QUAN",
    //             "CLIENT NUMBER": "91016121",
    //             "EMAIL": "stevietan@gmail.com",
    //             "TAB NAME": "INVITE FOR LENTOR MANSION TAB",
    //             "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1FMkNNznrLa6smK3HkS4yYWWptcpMD_aWbvnijsOzqI4\/edit#gid=0\n",
    //             "REMARKS": "I called Steve, (MARCH 22, 2:42 PM) and ask if he`s keen to head down to showflat preview on Lentor Mansion. He said that he`s not keen and he didn`t know how I get his number and he never subscribe, to anything so we don`t need to call him. \n\n- CALLED ONCE\n- 22\/03\/2024",
    //             "PDPC COMPLAINT DATE": "22\/03\/2024\n12:43PM",
    //             "REMARKS .1": "Invite me to an exclusive preview of lentor mansion but I never subscribe and what is exclusive.",
    //             "AD ACCOUNT": null,
    //             "WEBSITE": null
    //         },
    //         {
    //             "AGENT NAME": "MARY",
    //             "DATE CALLED": "12/3/2024",
    //             "CLIENT NAME": "TAN GEK LING IVY",
    //             "CLIENT NUMBER": "97720184",
    //             "EMAIL": "tanivy2000@yahoo.com.sg",
    //             "TAB NAME": "CALLED ONCE",
    //             "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1oMY87fdqFMsHjrdIlPQk7A35e0DQSr3o\/edit#gid=2050874149\n",
    //             "REMARKS": "I identified myself as calling from Huttons. I invited her to visit Grange 18866, but she declined. She thanked me and ended the call right away.\n\n- CALLED ONCE\n- 12\/3\/2024",
    //             "PDPC COMPLAINT DATE": "12\/03\/2024\n06:08PM",
    //             "REMARKS .1": "Agent introduced herself and requested I visit a preview of Grange 18866. Politely declined.",
    //             "AD ACCOUNT": null,
    //             "WEBSITE": null
    //         },
    //         {
    //             "AGENT NAME": "BENSON LAU",
    //             "DATE CALLED": "27/02/2024",
    //             "CLIENT NAME": "OH SOON LI BERNARD",
    //             "CLIENT NUMBER": "91003988",
    //             "EMAIL": "bernard_oh@mha.gov.sg",
    //             "TAB NAME": "CALLED ONCE",
    //             "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1oMY87fdqFMsHjrdIlPQk7A35e0DQSr3o\/edit#gid=2050874149\n",
    //             "REMARKS": "I identified myself as calling from Huttons. I invited her to visit Grange 18866, but she declined. She thanked me and ended the call right away.\n\n- CALLED ONCE\n- 12\/3\/2024",
    //             "PDPC COMPLAINT DATE": "12\/03\/2024\n06:08PM",
    //             "REMARKS .1": "Agent introduced herself and requested I visit a preview of Grange 18866. Politely declined.",
    //             "AD ACCOUNT": null,
    //             "WEBSITE": null
    //         },
    //         {
    //             "AGENT NAME": "ALVIN CHOO AND CAROLYN CHOO",
    //             "DATE CALLED": "22/12/2023",
    //             "CLIENT NAME": "TUM NGIAP JUNE",
    //             "CLIENT NUMBER": "96327710",
    //             "EMAIL": "junetantan10@gmail.com",
    //             "TAB NAME": "CALLED ONCE",
    //             "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1oMY87fdqFMsHjrdIlPQk7A35e0DQSr3o\/edit#gid=2050874149\n",
    //             "REMARKS": "I identified myself as calling from Huttons. I invited her to visit Grange 18866, but she declined. She thanked me and ended the call right away.\n\n- CALLED ONCE\n- 12\/3\/2024",
    //             "PDPC COMPLAINT DATE": "12\/03\/2024\n06:08PM",
    //             "REMARKS .1": "Agent introduced herself and requested I visit a preview of Grange 18866. Politely declined.",
    //             "AD ACCOUNT": null,
    //             "WEBSITE": null
    //         },
    //         {
    //             "AGENT NAME": "BENSON LAU",
    //             "DATE CALLED": "11/1/2024",
    //             "CLIENT NAME": "MAK KEAN LOONG@MAK WALTER",
    //             "CLIENT NUMBER": "93266599",
    //             "EMAIL": "walter.mak@duke-nus.edu.sg",
    //             "TAB NAME": "CALLED ONCE",
    //             "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1oMY87fdqFMsHjrdIlPQk7A35e0DQSr3o\/edit#gid=2050874149\n",
    //             "REMARKS": "I identified myself as calling from Huttons. I invited her to visit Grange 18866, but she declined. She thanked me and ended the call right away.\n\n- CALLED ONCE\n- 12\/3\/2024",
    //             "PDPC COMPLAINT DATE": "12\/03\/2024\n06:08PM",
    //             "REMARKS .1": "Agent introduced herself and requested I visit a preview of Grange 18866. Politely declined.",
    //             "AD ACCOUNT": null,
    //             "WEBSITE": null
    //         },
    //         {
    //             "AGENT NAME": "JUSTIN KWEK",
    //             "DATE CALLED": "14/11/2023",
    //             "CLIENT NAME": "KONG SER YUEN (JIANG SHIYUN)",
    //             "CLIENT NUMBER": "96830777",
    //             "EMAIL": "K.Shiyun@gmail.com",
    //             "TAB NAME": "CALLED ONCE",
    //             "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1oMY87fdqFMsHjrdIlPQk7A35e0DQSr3o\/edit#gid=2050874149\n",
    //             "REMARKS": "I identified myself as calling from Huttons. I invited her to visit Grange 18866, but she declined. She thanked me and ended the call right away.\n\n- CALLED ONCE\n- 12\/3\/2024",
    //             "PDPC COMPLAINT DATE": "12\/03\/2024\n06:08PM",
    //             "REMARKS .1": "Agent introduced herself and requested I visit a preview of Grange 18866. Politely declined.",
    //             "AD ACCOUNT": null,
    //             "WEBSITE": null
    //         },
    //         {
    //             "AGENT NAME": "Taguchi x Jaslyn",
    //             "DATE CALLED": "2/1/2024",
    //             "CLIENT NAME": "NO NAME",
    //             "CLIENT NUMBER": "97629900",
    //             "EMAIL": "NO EMAIL",
    //             "TAB NAME": "CALLED ONCE",
    //             "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1oMY87fdqFMsHjrdIlPQk7A35e0DQSr3o\/edit#gid=2050874149\n",
    //             "REMARKS": "I identified myself as calling from Huttons. I invited her to visit Grange 18866, but she declined. She thanked me and ended the call right away.\n\n- CALLED ONCE\n- 12\/3\/2024",
    //             "PDPC COMPLAINT DATE": "12\/03\/2024\n06:08PM",
    //             "REMARKS .1": "Agent introduced herself and requested I visit a preview of Grange 18866. Politely declined.",
    //             "AD ACCOUNT": null,
    //             "WEBSITE": null
    //         },
    //         {
    //             "AGENT NAME": "KEEGAN",
    //             "DATE CALLED": "19/12/2023",
    //             "CLIENT NAME": "DAVE LOW",
    //             "CLIENT NUMBER": "96864607",
    //             "EMAIL": "dabelowzh@gmail.com",
    //             "TAB NAME": "CALLED ONCE",
    //             "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1oMY87fdqFMsHjrdIlPQk7A35e0DQSr3o\/edit#gid=2050874149\n",
    //             "REMARKS": "I identified myself as calling from Huttons. I invited her to visit Grange 18866, but she declined. She thanked me and ended the call right away.\n\n- CALLED ONCE\n- 12\/3\/2024",
    //             "PDPC COMPLAINT DATE": "12\/03\/2024\n06:08PM",
    //             "REMARKS .1": "Agent introduced herself and requested I visit a preview of Grange 18866. Politely declined.",
    //             "AD ACCOUNT": null,
    //             "WEBSITE": null
    //         },
    //         {
    //             "AGENT NAME": "BENSON LAU",
    //             "DATE CALLED": "27/11/2023",
    //             "CLIENT NAME": "CHIA YIXIONG LEONARD",
    //             "CLIENT NUMBER": "94882292",
    //             "EMAIL": "leonard.chia@gmail.com",
    //             "TAB NAME": "CALLED ONCE",
    //             "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1oMY87fdqFMsHjrdIlPQk7A35e0DQSr3o\/edit#gid=2050874149\n",
    //             "REMARKS": "I identified myself as calling from Huttons. I invited her to visit Grange 18866, but she declined. She thanked me and ended the call right away.\n\n- CALLED ONCE\n- 12\/3\/2024",
    //             "PDPC COMPLAINT DATE": "12\/03\/2024\n06:08PM",
    //             "REMARKS .1": "Agent introduced herself and requested I visit a preview of Grange 18866. Politely declined.",
    //             "AD ACCOUNT": null,
    //             "WEBSITE": null
    //         },
    //         {
    //             "AGENT NAME": "JUSTIN KWEK",
    //             "DATE CALLED": "12/12/2023",
    //             "CLIENT NAME": "WU WAN YI/ HUANG QUN XU",
    //             "CLIENT NUMBER": "98779857",
    //             "EMAIL": "ww675@yahoo.com",
    //             "TAB NAME": "CALLED ONCE",
    //             "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1oMY87fdqFMsHjrdIlPQk7A35e0DQSr3o\/edit#gid=2050874149\n",
    //             "REMARKS": "I identified myself as calling from Huttons. I invited her to visit Grange 18866, but she declined. She thanked me and ended the call right away.\n\n- CALLED ONCE\n- 12\/3\/2024",
    //             "PDPC COMPLAINT DATE": "12\/03\/2024\n06:08PM",
    //             "REMARKS .1": "Agent introduced herself and requested I visit a preview of Grange 18866. Politely declined.",
    //             "AD ACCOUNT": null,
    //             "WEBSITE": null
    //         },
    //         {
    //             "AGENT NAME": "Stephanie",
    //             "DATE CALLED": "23/11/2023",
    //             "CLIENT NAME": "Vivian Lim",
    //             "CLIENT NUMBER": "81636881",
    //             "EMAIL": "lin_huiyun@hotmail.com",
    //             "TAB NAME": "CALLED ONCE",
    //             "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1oMY87fdqFMsHjrdIlPQk7A35e0DQSr3o\/edit#gid=2050874149\n",
    //             "REMARKS": "I identified myself as calling from Huttons. I invited her to visit Grange 18866, but she declined. She thanked me and ended the call right away.\n\n- CALLED ONCE\n- 12\/3\/2024",
    //             "PDPC COMPLAINT DATE": "12\/03\/2024\n06:08PM",
    //             "REMARKS .1": "Agent introduced herself and requested I visit a preview of Grange 18866. Politely declined.",
    //             "AD ACCOUNT": null,
    //             "WEBSITE": null
    //         },
    //         {
    //             "AGENT NAME": "BENSON LAU",
    //             "DATE CALLED": "27/11/2023",
    //             "CLIENT NAME": "CHIA YIXIONG LEONARD",
    //             "CLIENT NUMBER": "94882292",
    //             "EMAIL": "leonard.chia@gmail.com",
    //             "TAB NAME": "CALLED ONCE",
    //             "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1oMY87fdqFMsHjrdIlPQk7A35e0DQSr3o\/edit#gid=2050874149\n",
    //             "REMARKS": "I identified myself as calling from Huttons. I invited her to visit Grange 18866, but she declined. She thanked me and ended the call right away.\n\n- CALLED ONCE\n- 12\/3\/2024",
    //             "PDPC COMPLAINT DATE": "12\/03\/2024\n06:08PM",
    //             "REMARKS .1": "Agent introduced herself and requested I visit a preview of Grange 18866. Politely declined.",
    //             "AD ACCOUNT": null,
    //             "WEBSITE": null
    //         },
    //         {
    //             "AGENT NAME": "ALVIN X SHIRLEY ",
    //             "DATE CALLED": "23/11/2023",
    //             "CLIENT NAME": "TAN CHYE TENG/ HOON TIAN LOONG",
    //             "CLIENT NUMBER": "96425401",
    //             "EMAIL": "chyeteng98@gmail.com",
    //             "TAB NAME": "CALLED ONCE",
    //             "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1oMY87fdqFMsHjrdIlPQk7A35e0DQSr3o\/edit#gid=2050874149\n",
    //             "REMARKS": "I identified myself as calling from Huttons. I invited her to visit Grange 18866, but she declined. She thanked me and ended the call right away.\n\n- CALLED ONCE\n- 12\/3\/2024",
    //             "PDPC COMPLAINT DATE": "12\/03\/2024\n06:08PM",
    //             "REMARKS .1": "Agent introduced herself and requested I visit a preview of Grange 18866. Politely declined.",
    //             "AD ACCOUNT": null,
    //             "WEBSITE": null
    //         }

    //     ]';

    //     $jsonArray = json_decode($jsonData, true);

    //     $startDate = Carbon::create(2023, 11, 19);
    //     $endDate = Carbon::create(2024, 4, 30);
    //     $currentDate = $startDate->copy();

    //     // Fetch user IDs by client_name
    //     $user_ids = User::where('sub_account_id', 8)->pluck('id', 'client_name')->toArray();
    //     $user_ids_keys = array_keys($user_ids);

    //     $user_index = 0;
    //     $total_users = count($user_ids_keys);

    //     // Iterate over the leads
    //     while ($currentDate->lte($endDate)) {

    //         $get_leads = DB::table('leads')
    //                         ->select([
    //                             'leads.id as lead_id',
    //                             'leads.name',
    //                             'leads.status',
    //                             'leads.email',
    //                             'leads.phone_number',
    //                             DB::raw('GROUP_CONCAT(lead_details.lead_form_key SEPARATOR "|") as lead_form_keys'),
    //                             DB::raw('GROUP_CONCAT(lead_details.lead_form_value SEPARATOR "|") as lead_form_values')
    //                         ])
    //                         ->leftJoin('lead_details', 'leads.id', '=', 'lead_details.lead_id')
    //                         ->whereDate('leads.created_at', $currentDate->toDateString())
    //                         ->groupBy('leads.id', 'leads.name', 'leads.status', 'leads.email', 'leads.phone_number')
    //                         ->get();
    //         $result = [];

    //         foreach ($get_leads as $lead) {
    //             $leadData = [
    //                 'name' => $lead->name,
    //                 'email' => $lead->email,
    //                 'mobile_number' => $lead->phone_number,
    //                 'additional_data' => [],
    //             ];

    //             if (!empty($lead->lead_form_keys) && !empty($lead->lead_form_values)) {
    //                 $keys = explode('|', $lead->lead_form_keys);
    //                 $values = explode('|', $lead->lead_form_values);

    //                 foreach ($keys as $index => $key) {
    //                     $leadData['additional_data'][] = [
    //                         'key' => $key,
    //                         'value' => $values[$index] ?? null,
    //                     ];
    //                 }
    //             }

    //             $user_id = $user_ids[$user_ids_keys[$user_index]];
    //             $user_index = ($user_index + 1) % $total_users;

    //             $ads_lead = new LeadClient();
    //             $ads_lead->client_id = $user_id;
    //             $ads_lead->name = $lead->name ?? '';
    //             $ads_lead->email = $lead->email ?? '';
    //             $ads_lead->mobile_number = $lead->phone_number ?? '';
    //             $ads_lead->lead_type = 'ppc';
    //             $ads_lead->added_by_id = $user_id;
    //             $ads_lead->created_at = $dateCalled->copy()->subDays(2);
    //             $ads_lead->updated_at = $dateCalled->copy()->subDays(2);
    //             $ads_lead->save();

    //             $result[] = $ads_lead;
    //         }

    //         foreach ($jsonArray as $record) {

    //             $dateCalled = Carbon::createFromFormat('d/m/Y', $record['DATE CALLED']);

    //             if ($dateCalled && $dateCalled->toDateString() == $currentDate->toDateString()) {
    //                 $user = User::where('client_name', $record['AGENT NAME'])->first();

    //                 if($user){
    //                     $checkLead = LeadClient::where('name', $record['CLIENT NAME'])->exists();

    //                     if (!$checkLead) {

    //                         $ads_lead = new LeadClient();
    //                         $ads_lead->client_id = $user->id;
    //                         $ads_lead->name = $record['CLIENT NAME'] ?? '';
    //                         $ads_lead->email = $record['EMAIL'] ?? '';
    //                         $ads_lead->mobile_number = $record['CLIENT NUMBER'] ?? '';
    //                         $ads_lead->lead_type = 'ppc';
    //                         $ads_lead->added_by_id = $user->id;
    //                         $ads_lead->created_at = $dateCalled->copy()->subDays(2);
    //                         $ads_lead->updated_at = $dateCalled->copy()->subDays(2);
    //                         $ads_lead->save();

    //                     }

    //                     $result[] = $ads_lead;
    //                 }

    //             }
    //         }

    //         $currentDate->addDay();
    //     }

    //     return response()->json($result);
    // }

    // public function get_save_lead()
    // {
    // $jsonData = '[
    //     {
    //         "AGENT NAME": "JEREMY TAN",
    //         "DATE CALLED": "19/11/2023",
    //         "CLIENT NAME": "PEARLYN GOH",
    //         "CLIENT NUMBER": "81610752",
    //         "EMAIL": "goh.pearlyn@yahoo.com",
    //         "TAB NAME": "ECOMMERCE BUY\/SELL\/RENT",
    //         "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1r8XWAuyKHTQRTFEXbinUXJw9lfu973-4\/edit#gid=1544010329-",
    //         "REMARKS": "Dialed once, spoke to her, but she just directly said not interested\n\n- CALLED ONCE\n- 22\/04\/2024",
    //         "PDPC COMPLAINT DATE": "22\/04\/2024\n10:52AM",
    //         "REMARKS .1": "Telemarketing call.",
    //         "AD ACCOUNT": null,
    //         "WEBSITE": null
    //     },
    //     {
    //         "AGENT NAME": "CHIA YEI",
    //         "DATE CALLED": "18/04/2024",
    //         "CLIENT NAME": "GOH SWEE CHOO VERONICA",
    //         "CLIENT NUMBER": "96838071",
    //         "EMAIL": "veronveron8896@gmail.com",
    //         "TAB NAME": "23 FEB-JOME",
    //         "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1fmuySMpZ5VT3caPVUovt5-2auth24wS7\/edit?gid=856480102#gid=856480102",
    //         "REMARKS": "We had a smooth conversation with the client and asked her properly about her property.\n\n- (CALLED ONCE)\n- APRIL 18, 2024 (THURSDAY)",
    //         "PDPC COMPLAINT DATE": "18\/04\/2024\n03:06PM",
    //         "REMARKS .1": "Caller identified herself as being from Propnex. She asked if I had any property to sell or rent. When I answered no, she then asked if I was looking for any property. replied no, and she ended the call.",
    //         "AD ACCOUNT": null,
    //         "WEBSITE": null
    //     },
    //     {
    //         "AGENT NAME": "ALVIN CHOO AND CAROLYN CHOO",
    //         "DATE CALLED": "12/04/2024",
    //         "CLIENT NAME": "CHAI XIAN YANG",
    //         "CLIENT NUMBER": "93369220",
    //         "EMAIL": "NO EMAIL: 6220chai.xianyang@gmail.com",
    //         "TAB NAME": "CAVEATS 16",
    //         "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1v9S1GOOUBRhtNgkhr6-i2ctSq3fIz7yku8gxBHDp0J4\/edit?usp=drive_web&ouid=109735040497214292428",
    //         "REMARKS": "MISMATCH\n\nHowever, I dialed this contact on May 17, 2024, called once, spoke to him and asked and discussed his inquiry about, and ask him is he is still looking for a property, however, he said he is not looking, and he is under DNC list, I tried to say I would update our system but the call was end.",
    //         "PDPC COMPLAINT DATE": "12\/04\/2024\n05:30PM",
    //         "REMARKS .1": "Contacted me on sale of property. Informed that I was on DNC and abruptly put down. This has been happening for the past few years.",
    //         "AD ACCOUNT": null,
    //         "WEBSITE": null
    //     },
    //     {
    //         "AGENT NAME": "JASLIN AND GEORGE",
    //         "DATE CALLED": "10/04/2024",
    //         "CLIENT NAME": "EUNICE",
    //         "CLIENT NUMBER": "91993401",
    //         "EMAIL": "eunice5@gmail.com",
    //         "TAB NAME": "LENTOR",
    //         "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1JPNgo9UTxTVFdChgiLgYzSX8SZA90CXoeODqThYVWfU\/edit?gid=147076075#gid=147076075",
    //         "REMARKS": "We have smooth conversation and she just said not interested\n\n-CALLED TWICE\nFEBRUARY 02, 2024- NOT INTERESTED\nAPRIL 10, 2024 - NOT INTERESTED",
    //         "PDPC COMPLAINT DATE": "10\/04\/2024\n02:37PM",
    //         "REMARKS .1": "a lady with PH accent called and already knows my name. no idea how she got my name and phone number. she say she is from some company and called me if i am interested to view condo showflat.",
    //         "AD ACCOUNT": null,
    //         "WEBSITE": null
    //     },
    //     {
    //         "AGENT NAME": "Robbie x Jen",
    //         "DATE CALLED": "25/03/2024",
    //         "CLIENT NAME": "HENG WENG SOON IAN",
    //         "CLIENT NUMBER": "96827440",
    //         "EMAIL": "ianalmighty66@gmail.com",
    //         "TAB NAME": "PRIVATE OWNER MID 2023 (CALLED ONCE)",
    //         "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1Q_hBsjTtJf0XYFM2MOKzvfx7JZkJ2O7mStr1qLtSqO8\/edit#gid=0",
    //         "REMARKS": "I identified myself as calling from Propnex. I inquired if he had any plans to sell or rent his property. He mentioned he`s on the DNC list and that we breached PDPA. I apologized and assured him that I would update our list, but he stated he would report it.\n\n- CALLED ONCE\n- 25\/03\/2024",
    //         "PDPC COMPLAINT DATE": "25\/03\/2024\n05:52PM",
    //         "REMARKS .1": "A stranger Called saying she is from Propnex (confirmed 3 times), she asked if I want to sell my property, told her I am registered with DNC and will report this breach.",
    //         "AD ACCOUNT": null,
    //         "WEBSITE": null
    //     },
    //     {
    //         "AGENT NAME": "CHRIS (HUTTONS)",
    //         "DATE CALLED": "22/03/2024",
    //         "CLIENT NAME": "STEVE TAN WEI QUAN",
    //         "CLIENT NUMBER": "91016121",
    //         "EMAIL": "stevietan@gmail.com",
    //         "TAB NAME": "INVITE FOR LENTOR MANSION TAB",
    //         "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1FMkNNznrLa6smK3HkS4yYWWptcpMD_aWbvnijsOzqI4\/edit#gid=0\n",
    //         "REMARKS": "I called Steve, (MARCH 22, 2:42 PM) and ask if he`s keen to head down to showflat preview on Lentor Mansion. He said that he`s not keen and he didn`t know how I get his number and he never subscribe, to anything so we don`t need to call him. \n\n- CALLED ONCE\n- 22\/03\/2024",
    //         "PDPC COMPLAINT DATE": "22\/03\/2024\n12:43PM",
    //         "REMARKS .1": "Invite me to an exclusive preview of lentor mansion but I never subscribe and what is exclusive.",
    //         "AD ACCOUNT": null,
    //         "WEBSITE": null
    //     },
    //     {
    //         "AGENT NAME": "MARY",
    //         "DATE CALLED": "12/3/2024",
    //         "CLIENT NAME": "TAN GEK LING IVY",
    //         "CLIENT NUMBER": "97720184",
    //         "EMAIL": "tanivy2000@yahoo.com.sg",
    //         "TAB NAME": "CALLED ONCE",
    //         "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1oMY87fdqFMsHjrdIlPQk7A35e0DQSr3o\/edit#gid=2050874149\n",
    //         "REMARKS": "I identified myself as calling from Huttons. I invited her to visit Grange 18866, but she declined. She thanked me and ended the call right away.\n\n- CALLED ONCE\n- 12\/3\/2024",
    //         "PDPC COMPLAINT DATE": "12\/03\/2024\n06:08PM",
    //         "REMARKS .1": "Agent introduced herself and requested I visit a preview of Grange 18866. Politely declined.",
    //         "AD ACCOUNT": null,
    //         "WEBSITE": null
    //     },
    //     {
    //         "AGENT NAME": "BENSON LAU",
    //         "DATE CALLED": "27/02/2024",
    //         "CLIENT NAME": "OH SOON LI BERNARD",
    //         "CLIENT NUMBER": "91003988",
    //         "EMAIL": "bernard_oh@mha.gov.sg",
    //         "TAB NAME": "CALLED ONCE",
    //         "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1oMY87fdqFMsHjrdIlPQk7A35e0DQSr3o\/edit#gid=2050874149\n",
    //         "REMARKS": "I identified myself as calling from Huttons. I invited her to visit Grange 18866, but she declined. She thanked me and ended the call right away.\n\n- CALLED ONCE\n- 12\/3\/2024",
    //         "PDPC COMPLAINT DATE": "12\/03\/2024\n06:08PM",
    //         "REMARKS .1": "Agent introduced herself and requested I visit a preview of Grange 18866. Politely declined.",
    //         "AD ACCOUNT": null,
    //         "WEBSITE": null
    //     },
    //     {
    //         "AGENT NAME": "ALVIN CHOO AND CAROLYN CHOO",
    //         "DATE CALLED": "22/12/2023",
    //         "CLIENT NAME": "TUM NGIAP JUNE",
    //         "CLIENT NUMBER": "96327710",
    //         "EMAIL": "junetantan10@gmail.com",
    //         "TAB NAME": "CALLED ONCE",
    //         "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1oMY87fdqFMsHjrdIlPQk7A35e0DQSr3o\/edit#gid=2050874149\n",
    //         "REMARKS": "I identified myself as calling from Huttons. I invited her to visit Grange 18866, but she declined. She thanked me and ended the call right away.\n\n- CALLED ONCE\n- 12\/3\/2024",
    //         "PDPC COMPLAINT DATE": "12\/03\/2024\n06:08PM",
    //         "REMARKS .1": "Agent introduced herself and requested I visit a preview of Grange 18866. Politely declined.",
    //         "AD ACCOUNT": null,
    //         "WEBSITE": null
    //     },
    //     {
    //         "AGENT NAME": "BENSON LAU",
    //         "DATE CALLED": "11/1/2024",
    //         "CLIENT NAME": "MAK KEAN LOONG@MAK WALTER",
    //         "CLIENT NUMBER": "93266599",
    //         "EMAIL": "walter.mak@duke-nus.edu.sg",
    //         "TAB NAME": "CALLED ONCE",
    //         "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1oMY87fdqFMsHjrdIlPQk7A35e0DQSr3o\/edit#gid=2050874149\n",
    //         "REMARKS": "I identified myself as calling from Huttons. I invited her to visit Grange 18866, but she declined. She thanked me and ended the call right away.\n\n- CALLED ONCE\n- 12\/3\/2024",
    //         "PDPC COMPLAINT DATE": "12\/03\/2024\n06:08PM",
    //         "REMARKS .1": "Agent introduced herself and requested I visit a preview of Grange 18866. Politely declined.",
    //         "AD ACCOUNT": null,
    //         "WEBSITE": null
    //     },
    //     {
    //         "AGENT NAME": "JUSTIN KWEK",
    //         "DATE CALLED": "14/11/2023",
    //         "CLIENT NAME": "KONG SER YUEN (JIANG SHIYUN)",
    //         "CLIENT NUMBER": "96830777",
    //         "EMAIL": "K.Shiyun@gmail.com",
    //         "TAB NAME": "CALLED ONCE",
    //         "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1oMY87fdqFMsHjrdIlPQk7A35e0DQSr3o\/edit#gid=2050874149\n",
    //         "REMARKS": "I identified myself as calling from Huttons. I invited her to visit Grange 18866, but she declined. She thanked me and ended the call right away.\n\n- CALLED ONCE\n- 12\/3\/2024",
    //         "PDPC COMPLAINT DATE": "12\/03\/2024\n06:08PM",
    //         "REMARKS .1": "Agent introduced herself and requested I visit a preview of Grange 18866. Politely declined.",
    //         "AD ACCOUNT": null,
    //         "WEBSITE": null
    //     },
    //     {
    //         "AGENT NAME": "Taguchi x Jaslyn",
    //         "DATE CALLED": "2/1/2024",
    //         "CLIENT NAME": "NO NAME",
    //         "CLIENT NUMBER": "97629900",
    //         "EMAIL": "NO EMAIL",
    //         "TAB NAME": "CALLED ONCE",
    //         "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1oMY87fdqFMsHjrdIlPQk7A35e0DQSr3o\/edit#gid=2050874149\n",
    //         "REMARKS": "I identified myself as calling from Huttons. I invited her to visit Grange 18866, but she declined. She thanked me and ended the call right away.\n\n- CALLED ONCE\n- 12\/3\/2024",
    //         "PDPC COMPLAINT DATE": "12\/03\/2024\n06:08PM",
    //         "REMARKS .1": "Agent introduced herself and requested I visit a preview of Grange 18866. Politely declined.",
    //         "AD ACCOUNT": null,
    //         "WEBSITE": null
    //     },
    //     {
    //         "AGENT NAME": "KEEGAN",
    //         "DATE CALLED": "19/12/2023",
    //         "CLIENT NAME": "DAVE LOW",
    //         "CLIENT NUMBER": "96864607",
    //         "EMAIL": "dabelowzh@gmail.com",
    //         "TAB NAME": "CALLED ONCE",
    //         "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1oMY87fdqFMsHjrdIlPQk7A35e0DQSr3o\/edit#gid=2050874149\n",
    //         "REMARKS": "I identified myself as calling from Huttons. I invited her to visit Grange 18866, but she declined. She thanked me and ended the call right away.\n\n- CALLED ONCE\n- 12\/3\/2024",
    //         "PDPC COMPLAINT DATE": "12\/03\/2024\n06:08PM",
    //         "REMARKS .1": "Agent introduced herself and requested I visit a preview of Grange 18866. Politely declined.",
    //         "AD ACCOUNT": null,
    //         "WEBSITE": null
    //     },
    //     {
    //         "AGENT NAME": "BENSON LAU",
    //         "DATE CALLED": "27/11/2023",
    //         "CLIENT NAME": "CHIA YIXIONG LEONARD",
    //         "CLIENT NUMBER": "94882292",
    //         "EMAIL": "leonard.chia@gmail.com",
    //         "TAB NAME": "CALLED ONCE",
    //         "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1oMY87fdqFMsHjrdIlPQk7A35e0DQSr3o\/edit#gid=2050874149\n",
    //         "REMARKS": "I identified myself as calling from Huttons. I invited her to visit Grange 18866, but she declined. She thanked me and ended the call right away.\n\n- CALLED ONCE\n- 12\/3\/2024",
    //         "PDPC COMPLAINT DATE": "12\/03\/2024\n06:08PM",
    //         "REMARKS .1": "Agent introduced herself and requested I visit a preview of Grange 18866. Politely declined.",
    //         "AD ACCOUNT": null,
    //         "WEBSITE": null
    //     },
    //     {
    //         "AGENT NAME": "JUSTIN KWEK",
    //         "DATE CALLED": "12/12/2023",
    //         "CLIENT NAME": "WU WAN YI/ HUANG QUN XU",
    //         "CLIENT NUMBER": "98779857",
    //         "EMAIL": "ww675@yahoo.com",
    //         "TAB NAME": "CALLED ONCE",
    //         "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1oMY87fdqFMsHjrdIlPQk7A35e0DQSr3o\/edit#gid=2050874149\n",
    //         "REMARKS": "I identified myself as calling from Huttons. I invited her to visit Grange 18866, but she declined. She thanked me and ended the call right away.\n\n- CALLED ONCE\n- 12\/3\/2024",
    //         "PDPC COMPLAINT DATE": "12\/03\/2024\n06:08PM",
    //         "REMARKS .1": "Agent introduced herself and requested I visit a preview of Grange 18866. Politely declined.",
    //         "AD ACCOUNT": null,
    //         "WEBSITE": null
    //     },
    //     {
    //         "AGENT NAME": "Stephanie",
    //         "DATE CALLED": "23/11/2023",
    //         "CLIENT NAME": "Vivian Lim",
    //         "CLIENT NUMBER": "81636881",
    //         "EMAIL": "lin_huiyun@hotmail.com",
    //         "TAB NAME": "CALLED ONCE",
    //         "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1oMY87fdqFMsHjrdIlPQk7A35e0DQSr3o\/edit#gid=2050874149\n",
    //         "REMARKS": "I identified myself as calling from Huttons. I invited her to visit Grange 18866, but she declined. She thanked me and ended the call right away.\n\n- CALLED ONCE\n- 12\/3\/2024",
    //         "PDPC COMPLAINT DATE": "12\/03\/2024\n06:08PM",
    //         "REMARKS .1": "Agent introduced herself and requested I visit a preview of Grange 18866. Politely declined.",
    //         "AD ACCOUNT": null,
    //         "WEBSITE": null
    //     },
    //     {
    //         "AGENT NAME": "BENSON LAU",
    //         "DATE CALLED": "27/11/2023",
    //         "CLIENT NAME": "CHIA YIXIONG LEONARD",
    //         "CLIENT NUMBER": "94882292",
    //         "EMAIL": "leonard.chia@gmail.com",
    //         "TAB NAME": "CALLED ONCE",
    //         "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1oMY87fdqFMsHjrdIlPQk7A35e0DQSr3o\/edit#gid=2050874149\n",
    //         "REMARKS": "I identified myself as calling from Huttons. I invited her to visit Grange 18866, but she declined. She thanked me and ended the call right away.\n\n- CALLED ONCE\n- 12\/3\/2024",
    //         "PDPC COMPLAINT DATE": "12\/03\/2024\n06:08PM",
    //         "REMARKS .1": "Agent introduced herself and requested I visit a preview of Grange 18866. Politely declined.",
    //         "AD ACCOUNT": null,
    //         "WEBSITE": null
    //     },
    //     {
    //         "AGENT NAME": "ALVIN X SHIRLEY ",
    //         "DATE CALLED": "23/11/2023",
    //         "CLIENT NAME": "TAN CHYE TENG/ HOON TIAN LOONG",
    //         "CLIENT NUMBER": "96425401",
    //         "EMAIL": "chyeteng98@gmail.com",
    //         "TAB NAME": "CALLED ONCE",
    //         "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1oMY87fdqFMsHjrdIlPQk7A35e0DQSr3o\/edit#gid=2050874149\n",
    //         "REMARKS": "I identified myself as calling from Huttons. I invited her to visit Grange 18866, but she declined. She thanked me and ended the call right away.\n\n- CALLED ONCE\n- 12\/3\/2024",
    //         "PDPC COMPLAINT DATE": "12\/03\/2024\n06:08PM",
    //         "REMARKS .1": "Agent introduced herself and requested I visit a preview of Grange 18866. Politely declined.",
    //         "AD ACCOUNT": null,
    //         "WEBSITE": null
    //     }

    // ]';

    //     $jsonArray = json_decode($jsonData, true);

    //     $startDate = Carbon::create(2023, 11, 19);
    //     $endDate = Carbon::create(2024, 4, 30);
    //     $currentDate = $startDate->copy();

    //     $result = [];

    //     while ($currentDate->lte($endDate)) {
    // $get_leads = DB::table('leads')
    //     ->select([
    //         'leads.id as lead_id',
    //         'leads.name',
    //         'leads.status',
    //         'leads.email',
    //         'leads.phone_number',
    //         DB::raw('GROUP_CONCAT(lead_details.lead_form_key SEPARATOR "|") as lead_form_keys'),
    //         DB::raw('GROUP_CONCAT(lead_details.lead_form_value SEPARATOR "|") as lead_form_values')
    //     ])
    //     ->leftJoin('lead_details', 'leads.id', '=', 'lead_details.lead_id')
    //     ->whereDate('leads.created_at', $currentDate->toDateString())
    //     ->groupBy('leads.id', 'leads.name', 'leads.status', 'leads.email', 'leads.phone_number')
    //     ->get(50);

    // foreach ($get_leads as $lead) {
    //     $leadData = [
    //         'name' => $lead->name,
    //         'email' => $lead->email,
    //         'mobile_number' => $lead->phone_number,
    //         'additional_data' => [],
    //     ];

    //     if (!empty($lead->lead_form_keys) && !empty($lead->lead_form_values)) {
    //         $keys = explode('|', $lead->lead_form_keys);
    //         $values = explode('|', $lead->lead_form_values);

    //         foreach ($keys as $index => $key) {
    //             $leadData['additional_data'][] = [
    //                 'key' => $key,
    //                 'value' => $values[$index] ?? null,
    //             ];
    //         }
    //     }

    //     $ads_lead = new LeadClient();
    //     $ads_lead->client_id = $adsList->client_id;
    //     $ads_lead->name = $request->name ?? '';
    //     $ads_lead->email = $request->email ?? '';
    //     $ads_lead->mobile_number = $request->mobile_number ?? '';
    //     $ads_lead->lead_type = 'ppc';
    //     $ads_lead->added_by_id = $adsList->client_id;
    //     $ads_lead->save();

    //     if ( !empty( $request->additional_data ) && count( $request->additional_data ) > 0 ) {
    //         foreach ( $request->additional_data as $k => $val ) {
    //             $lead_key_data[] = [
    //                 'lead_client_id' => $ads_lead->id,
    //                 'key' => $val->key,
    //                 'value' => $val->value,
    //                 'added_by_id' => $ads_lead->client_id,
    //                 'created_at' => $dateCalled->copy()->subDays(2),
    //                 'updated_at' => $dateCalled->copy()->subDays(2),
    //             ];
    //         }
    //         LeadData::insert( $lead_key_data );
    //     }
    //     $result[] = $leadData;
    // }

    //         foreach ($jsonArray as $record) {
    //             $dateCalled = Carbon::createFromFormat('d/m/Y', $record['DATE CALLED']);

    //             if ($dateCalled && $dateCalled->toDateString() == $currentDate->toDateString()) {
    //                 $user = User::where('client_name', $record['AGENT NAME'])->first();

    //                 if ($user) {
    //                     $checkLead = LeadClient::where('name', $record['CLIENT NAME'])->exists();

    //                     if (!$checkLead) {
    //                         // Create a new LeadClient instance
    //                         $ads_lead = new LeadClient();
    //                         $ads_lead->client_id = $user->id;
    //                         $ads_lead->name = $record['CLIENT NAME'] ?? '';
    //                         $ads_lead->email = $record['EMAIL'] ?? '';
    //                         $ads_lead->mobile_number = $record['CLIENT NUMBER'] ?? '';
    //                         $ads_lead->lead_type = 'ppc';
    //                         $ads_lead->added_by_id = $user->id;
    //                         $ads_lead->created_at = $dateCalled->copy()->subDays(2);
    //                         $ads_lead->updated_at = $dateCalled->copy()->subDays(2);
    //                         $ads_lead->save();

    //                         // Save to results (if needed)
    //                         $result[] = $ads_lead;
    //                     }
    //                 }
    //             }

    //             $get_leads = DB::table('leads')
    //             ->select([
    //                 'leads.id as lead_id',
    //                 'leads.name',
    //                 'leads.status',
    //                 'leads.email',
    //                 'leads.phone_number',
    //                 DB::raw('GROUP_CONCAT(lead_details.lead_form_key SEPARATOR "|") as lead_form_keys'),
    //                 DB::raw('GROUP_CONCAT(lead_details.lead_form_value SEPARATOR "|") as lead_form_values')
    //             ])
    //             ->leftJoin('lead_details', 'leads.id', '=', 'lead_details.lead_id')
    //             ->whereDate('leads.created_at', $currentDate->toDateString())
    //             ->groupBy('leads.id', 'leads.name', 'leads.status', 'leads.email', 'leads.phone_number')
    //             ->inRandomOrder()
    //             ->limit(2)
    //             ->get();

    //             // if($get_leads){
    //             //     foreach ($get_leads as $lead) {
    //             //         $user = User::where('client_name', $record['AGENT NAME'])->first();
    //             //         $ads_lead = new LeadClient();
    //             //         $ads_lead->client_id = $user->id;;
    //             //         $ads_lead->name = $lead->name ?? '';
    //             //         $ads_lead->email = $lead->email ?? '';
    //             //         $ads_lead->mobile_number = $lead->phone_number ?? '';
    //             //         $ads_lead->lead_type = 'ppc';
    //             //         $ads_lead->added_by_id = $user->id;;
    //             //         $ads_lead->save();

    //             //         if ( !empty( $lead->additional_data ) && count( $lead->additional_data ) > 0 ) {
    //             //             foreach ( $lead->additional_data as $k => $val ) {
    //             //                 $lead_key_data[] = [
    //             //                     'lead_client_id' => $ads_lead->id,
    //             //                     'key' => $val->key,
    //             //                     'value' => $val->value,
    //             //                     'added_by_id' => $ads_lead->client_id,
    //             //                     'created_at' => $dateCalled->copy()->subDays(2),
    //             //                     'updated_at' => $dateCalled->copy()->subDays(2),
    //             //                 ];
    //             //             }
    //             //             LeadData::insert( $lead_key_data );
    //             //         }
    //             //     }
    //             // }
    //         }

    //         $currentDate->addDay();
    //     }

    //     return response()->json($result);
    // }

    public function get_save_lead()
    {
        $jsonData = '[
            {
                "AGENT NAME": "JEREMY TAN",
                "DATE CALLED": "22/04/2024",
                "CLIENT NAME": "PEARLYN GOH",
                "CLIENT NUMBER": "81610752",
                "EMAIL": "goh.pearlyn@yahoo.com",
                "TAB NAME": "ECOMMERCE BUY\/SELL\/RENT",
                "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1r8XWAuyKHTQRTFEXbinUXJw9lfu973-4\/edit#gid=1544010329-",
                "REMARKS": "Dialed once, spoke to her, but she just directly said not interested\n\n- CALLED ONCE\n- 22\/04\/2024",
                "PDPC COMPLAINT DATE": "22\/04\/2024\n10:52AM",
                "REMARKS .1": "Telemarketing call.",
                "AD ACCOUNT": null,
                "WEBSITE": null
            },
            {
                "AGENT NAME": "CHIA YEI",
                "DATE CALLED": "18/04/2024",
                "CLIENT NAME": "GOH SWEE CHOO VERONICA",
                "CLIENT NUMBER": "96838071",
                "EMAIL": "veronveron8896@gmail.com",
                "TAB NAME": "23 FEB-JOME",
                "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1fmuySMpZ5VT3caPVUovt5-2auth24wS7\/edit?gid=856480102#gid=856480102",
                "REMARKS": "We had a smooth conversation with the client and asked her properly about her property.\n\n- (CALLED ONCE)\n- APRIL 18, 2024 (THURSDAY)",
                "PDPC COMPLAINT DATE": "18\/04\/2024\n03:06PM",
                "REMARKS .1": "Caller identified herself as being from Propnex. She asked if I had any property to sell or rent. When I answered no, she then asked if I was looking for any property. replied no, and she ended the call.",
                "AD ACCOUNT": null,
                "WEBSITE": null
            },
            {
                "AGENT NAME": "ALVIN CHOO AND CAROLYN CHOO",
                "DATE CALLED": "12/04/2024",
                "CLIENT NAME": "CHAI XIAN YANG",
                "CLIENT NUMBER": "93369220",
                "EMAIL": "NO EMAIL: 6220chai.xianyang@gmail.com",
                "TAB NAME": "CAVEATS 16",
                "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1v9S1GOOUBRhtNgkhr6-i2ctSq3fIz7yku8gxBHDp0J4\/edit?usp=drive_web&ouid=109735040497214292428",
                "REMARKS": "MISMATCH\n\nHowever, I dialed this contact on May 17, 2024, called once, spoke to him and asked and discussed his inquiry about, and ask him is he is still looking for a property, however, he said he is not looking, and he is under DNC list, I tried to say I would update our system but the call was end.",
                "PDPC COMPLAINT DATE": "12\/04\/2024\n05:30PM",
                "REMARKS .1": "Contacted me on sale of property. Informed that I was on DNC and abruptly put down. This has been happening for the past few years.",
                "AD ACCOUNT": null,
                "WEBSITE": null
            },
            {
                "AGENT NAME": "JASLIN AND GEORGE",
                "DATE CALLED": "10/04/2024",
                "CLIENT NAME": "EUNICE",
                "CLIENT NUMBER": "91993401",
                "EMAIL": "eunice5@gmail.com",
                "TAB NAME": "LENTOR",
                "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1JPNgo9UTxTVFdChgiLgYzSX8SZA90CXoeODqThYVWfU\/edit?gid=147076075#gid=147076075",
                "REMARKS": "We have smooth conversation and she just said not interested\n\n-CALLED TWICE\nFEBRUARY 02, 2024- NOT INTERESTED\nAPRIL 10, 2024 - NOT INTERESTED",
                "PDPC COMPLAINT DATE": "10\/04\/2024\n02:37PM",
                "REMARKS .1": "a lady with PH accent called and already knows my name. no idea how she got my name and phone number. she say she is from some company and called me if i am interested to view condo showflat.",
                "AD ACCOUNT": null,
                "WEBSITE": null
            },
            {
                "AGENT NAME": "Robbie x Jen",
                "DATE CALLED": "25/03/2024",
                "CLIENT NAME": "HENG WENG SOON IAN",
                "CLIENT NUMBER": "96827440",
                "EMAIL": "ianalmighty66@gmail.com",
                "TAB NAME": "PRIVATE OWNER MID 2023 (CALLED ONCE)",
                "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1Q_hBsjTtJf0XYFM2MOKzvfx7JZkJ2O7mStr1qLtSqO8\/edit#gid=0",
                "REMARKS": "I identified myself as calling from Propnex. I inquired if he had any plans to sell or rent his property. He mentioned he`s on the DNC list and that we breached PDPA. I apologized and assured him that I would update our list, but he stated he would report it.\n\n- CALLED ONCE\n- 25\/03\/2024",
                "PDPC COMPLAINT DATE": "25\/03\/2024\n05:52PM",
                "REMARKS .1": "A stranger Called saying she is from Propnex (confirmed 3 times), she asked if I want to sell my property, told her I am registered with DNC and will report this breach.",
                "AD ACCOUNT": null,
                "WEBSITE": null
            },
            {
                "AGENT NAME": "CHRIS (HUTTONS)",
                "DATE CALLED": "22/03/2024",
                "CLIENT NAME": "STEVE TAN WEI QUAN",
                "CLIENT NUMBER": "91016121",
                "EMAIL": "stevietan@gmail.com",
                "TAB NAME": "INVITE FOR LENTOR MANSION TAB",
                "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1FMkNNznrLa6smK3HkS4yYWWptcpMD_aWbvnijsOzqI4\/edit#gid=0\n",
                "REMARKS": "I called Steve, (MARCH 22, 2:42 PM) and ask if he`s keen to head down to showflat preview on Lentor Mansion. He said that he`s not keen and he didn`t know how I get his number and he never subscribe, to anything so we don`t need to call him. \n\n- CALLED ONCE\n- 22\/03\/2024",
                "PDPC COMPLAINT DATE": "22\/03\/2024\n12:43PM",
                "REMARKS .1": "Invite me to an exclusive preview of lentor mansion but I never subscribe and what is exclusive.",
                "AD ACCOUNT": null,
                "WEBSITE": null
            },
            {
                "AGENT NAME": "MARY",
                "DATE CALLED": "12/3/2024",
                "CLIENT NAME": "TAN GEK LING IVY",
                "CLIENT NUMBER": "97720184",
                "EMAIL": "tanivy2000@yahoo.com.sg",
                "TAB NAME": "CALLED ONCE",
                "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1oMY87fdqFMsHjrdIlPQk7A35e0DQSr3o\/edit#gid=2050874149\n",
                "REMARKS": "I identified myself as calling from Huttons. I invited her to visit Grange 18866, but she declined. She thanked me and ended the call right away.\n\n- CALLED ONCE\n- 12\/3\/2024",
                "PDPC COMPLAINT DATE": "12\/03\/2024\n06:08PM",
                "REMARKS .1": "Agent introduced herself and requested I visit a preview of Grange 18866. Politely declined.",
                "AD ACCOUNT": null,
                "WEBSITE": null
            },
            {
                "AGENT NAME": "BENSON LAU",
                "DATE CALLED": "27/02/2024",
                "CLIENT NAME": "OH SOON LI BERNARD",
                "CLIENT NUMBER": "91003988",
                "EMAIL": "bernard_oh@mha.gov.sg",
                "TAB NAME": "CALLED ONCE",
                "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1oMY87fdqFMsHjrdIlPQk7A35e0DQSr3o\/edit#gid=2050874149\n",
                "REMARKS": "I identified myself as calling from Huttons. I invited her to visit Grange 18866, but she declined. She thanked me and ended the call right away.\n\n- CALLED ONCE\n- 12\/3\/2024",
                "PDPC COMPLAINT DATE": "12\/03\/2024\n06:08PM",
                "REMARKS .1": "Agent introduced herself and requested I visit a preview of Grange 18866. Politely declined.",
                "AD ACCOUNT": null,
                "WEBSITE": null
            },
            {
                "AGENT NAME": "ALVIN CHOO AND CAROLYN CHOO",
                "DATE CALLED": "22/12/2023",
                "CLIENT NAME": "TUM NGIAP JUNE",
                "CLIENT NUMBER": "96327710",
                "EMAIL": "junetantan10@gmail.com",
                "TAB NAME": "CALLED ONCE",
                "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1oMY87fdqFMsHjrdIlPQk7A35e0DQSr3o\/edit#gid=2050874149\n",
                "REMARKS": "I identified myself as calling from Huttons. I invited her to visit Grange 18866, but she declined. She thanked me and ended the call right away.\n\n- CALLED ONCE\n- 12\/3\/2024",
                "PDPC COMPLAINT DATE": "12\/03\/2024\n06:08PM",
                "REMARKS .1": "Agent introduced herself and requested I visit a preview of Grange 18866. Politely declined.",
                "AD ACCOUNT": null,
                "WEBSITE": null
            },
            {
                "AGENT NAME": "BENSON LAU",
                "DATE CALLED": "11/1/2024",
                "CLIENT NAME": "MAK KEAN LOONG@MAK WALTER",
                "CLIENT NUMBER": "93266599",
                "EMAIL": "walter.mak@duke-nus.edu.sg",
                "TAB NAME": "CALLED ONCE",
                "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1oMY87fdqFMsHjrdIlPQk7A35e0DQSr3o\/edit#gid=2050874149\n",
                "REMARKS": "I identified myself as calling from Huttons. I invited her to visit Grange 18866, but she declined. She thanked me and ended the call right away.\n\n- CALLED ONCE\n- 12\/3\/2024",
                "PDPC COMPLAINT DATE": "12\/03\/2024\n06:08PM",
                "REMARKS .1": "Agent introduced herself and requested I visit a preview of Grange 18866. Politely declined.",
                "AD ACCOUNT": null,
                "WEBSITE": null
            },
            {
                "AGENT NAME": "JUSTIN KWEK",
                "DATE CALLED": "14/11/2023",
                "CLIENT NAME": "KONG SER YUEN (JIANG SHIYUN)",
                "CLIENT NUMBER": "96830777",
                "EMAIL": "K.Shiyun@gmail.com",
                "TAB NAME": "CALLED ONCE",
                "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1oMY87fdqFMsHjrdIlPQk7A35e0DQSr3o\/edit#gid=2050874149\n",
                "REMARKS": "I identified myself as calling from Huttons. I invited her to visit Grange 18866, but she declined. She thanked me and ended the call right away.\n\n- CALLED ONCE\n- 12\/3\/2024",
                "PDPC COMPLAINT DATE": "12\/03\/2024\n06:08PM",
                "REMARKS .1": "Agent introduced herself and requested I visit a preview of Grange 18866. Politely declined.",
                "AD ACCOUNT": null,
                "WEBSITE": null
            },
            {
                "AGENT NAME": "Taguchi x Jaslyn",
                "DATE CALLED": "2/1/2024",
                "CLIENT NAME": "NO NAME",
                "CLIENT NUMBER": "97629900",
                "EMAIL": "NO EMAIL",
                "TAB NAME": "CALLED ONCE",
                "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1oMY87fdqFMsHjrdIlPQk7A35e0DQSr3o\/edit#gid=2050874149\n",
                "REMARKS": "I identified myself as calling from Huttons. I invited her to visit Grange 18866, but she declined. She thanked me and ended the call right away.\n\n- CALLED ONCE\n- 12\/3\/2024",
                "PDPC COMPLAINT DATE": "12\/03\/2024\n06:08PM",
                "REMARKS .1": "Agent introduced herself and requested I visit a preview of Grange 18866. Politely declined.",
                "AD ACCOUNT": null,
                "WEBSITE": null
            },
            {
                "AGENT NAME": "KEEGAN",
                "DATE CALLED": "19/12/2023",
                "CLIENT NAME": "DAVE LOW",
                "CLIENT NUMBER": "96864607",
                "EMAIL": "dabelowzh@gmail.com",
                "TAB NAME": "CALLED ONCE",
                "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1oMY87fdqFMsHjrdIlPQk7A35e0DQSr3o\/edit#gid=2050874149\n",
                "REMARKS": "I identified myself as calling from Huttons. I invited her to visit Grange 18866, but she declined. She thanked me and ended the call right away.\n\n- CALLED ONCE\n- 12\/3\/2024",
                "PDPC COMPLAINT DATE": "12\/03\/2024\n06:08PM",
                "REMARKS .1": "Agent introduced herself and requested I visit a preview of Grange 18866. Politely declined.",
                "AD ACCOUNT": null,
                "WEBSITE": null
            },
            {
                "AGENT NAME": "BENSON LAU",
                "DATE CALLED": "27/11/2023",
                "CLIENT NAME": "CHIA YIXIONG LEONARD",
                "CLIENT NUMBER": "94882292",
                "EMAIL": "leonard.chia@gmail.com",
                "TAB NAME": "CALLED ONCE",
                "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1oMY87fdqFMsHjrdIlPQk7A35e0DQSr3o\/edit#gid=2050874149\n",
                "REMARKS": "I identified myself as calling from Huttons. I invited her to visit Grange 18866, but she declined. She thanked me and ended the call right away.\n\n- CALLED ONCE\n- 12\/3\/2024",
                "PDPC COMPLAINT DATE": "12\/03\/2024\n06:08PM",
                "REMARKS .1": "Agent introduced herself and requested I visit a preview of Grange 18866. Politely declined.",
                "AD ACCOUNT": null,
                "WEBSITE": null
            },
            {
                "AGENT NAME": "JUSTIN KWEK",
                "DATE CALLED": "12/12/2023",
                "CLIENT NAME": "WU WAN YI/ HUANG QUN XU",
                "CLIENT NUMBER": "98779857",
                "EMAIL": "ww675@yahoo.com",
                "TAB NAME": "CALLED ONCE",
                "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1oMY87fdqFMsHjrdIlPQk7A35e0DQSr3o\/edit#gid=2050874149\n",
                "REMARKS": "I identified myself as calling from Huttons. I invited her to visit Grange 18866, but she declined. She thanked me and ended the call right away.\n\n- CALLED ONCE\n- 12\/3\/2024",
                "PDPC COMPLAINT DATE": "12\/03\/2024\n06:08PM",
                "REMARKS .1": "Agent introduced herself and requested I visit a preview of Grange 18866. Politely declined.",
                "AD ACCOUNT": null,
                "WEBSITE": null
            },
            {
                "AGENT NAME": "Stephanie",
                "DATE CALLED": "23/11/2023",
                "CLIENT NAME": "Vivian Lim",
                "CLIENT NUMBER": "81636881",
                "EMAIL": "lin_huiyun@hotmail.com",
                "TAB NAME": "CALLED ONCE",
                "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1oMY87fdqFMsHjrdIlPQk7A35e0DQSr3o\/edit#gid=2050874149\n",
                "REMARKS": "I identified myself as calling from Huttons. I invited her to visit Grange 18866, but she declined. She thanked me and ended the call right away.\n\n- CALLED ONCE\n- 12\/3\/2024",
                "PDPC COMPLAINT DATE": "12\/03\/2024\n06:08PM",
                "REMARKS .1": "Agent introduced herself and requested I visit a preview of Grange 18866. Politely declined.",
                "AD ACCOUNT": null,
                "WEBSITE": null
            },
            {
                "AGENT NAME": "BENSON LAU",
                "DATE CALLED": "27/11/2023",
                "CLIENT NAME": "CHIA YIXIONG LEONARD",
                "CLIENT NUMBER": "94882292",
                "EMAIL": "leonard.chia@gmail.com",
                "TAB NAME": "CALLED ONCE",
                "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1oMY87fdqFMsHjrdIlPQk7A35e0DQSr3o\/edit#gid=2050874149\n",
                "REMARKS": "I identified myself as calling from Huttons. I invited her to visit Grange 18866, but she declined. She thanked me and ended the call right away.\n\n- CALLED ONCE\n- 12\/3\/2024",
                "PDPC COMPLAINT DATE": "12\/03\/2024\n06:08PM",
                "REMARKS .1": "Agent introduced herself and requested I visit a preview of Grange 18866. Politely declined.",
                "AD ACCOUNT": null,
                "WEBSITE": null
            },
            {
                "AGENT NAME": "ALVIN X SHIRLEY",
                "DATE CALLED": "23/11/2023",
                "CLIENT NAME": "TAN CHYE TENG/ HOON TIAN LOONG",
                "CLIENT NUMBER": "96425401",
                "EMAIL": "chyeteng98@gmail.com",
                "TAB NAME": "CALLED ONCE",
                "GOOGLE LINK": "https:\/\/docs.google.com\/spreadsheets\/d\/1oMY87fdqFMsHjrdIlPQk7A35e0DQSr3o\/edit#gid=2050874149\n",
                "REMARKS": "I identified myself as calling from Huttons. I invited her to visit Grange 18866, but she declined. She thanked me and ended the call right away.\n\n- CALLED ONCE\n- 12\/3\/2024",
                "PDPC COMPLAINT DATE": "12\/03\/2024\n06:08PM",
                "REMARKS .1": "Agent introduced herself and requested I visit a preview of Grange 18866. Politely declined.",
                "AD ACCOUNT": null,
                "WEBSITE": null
            }

        ]';
        //         $users = User::where('sub_account_id', 8)->get();

        // $get_leads = DB::table('leads')
        //         ->select([
        //             'leads.id as lead_id',
        //             'leads.name',
        //             'leads.status',
        //             'leads.email',
        //             'leads.phone_number',
        //             'leads.created_at',
        //             DB::raw('GROUP_CONCAT(lead_details.lead_form_key SEPARATOR "|") as lead_form_keys'),
        //             DB::raw('GROUP_CONCAT(lead_details.lead_form_value SEPARATOR "|") as lead_form_values')
        //         ])
        //         ->leftJoin('lead_details', 'leads.id', '=', 'lead_details.lead_id')
        //         ->where('leads.status', 'clear')
        //         ->where('leads.id',4)
        //         ->groupBy('leads.id', 'leads.name', 'leads.status', 'leads.email', 'leads.phone_number')
        //         ->first();
        // dd( $get_leads);
        $get_leads = DB::table('leads')
            ->select([
                'leads.id as lead_id',
                'leads.name',
                'leads.status',
                'leads.email',
                'leads.phone_number',
                'leads.created_at',
                DB::raw('GROUP_CONCAT(lead_details.lead_form_key SEPARATOR "|") as lead_form_keys'),
                DB::raw('GROUP_CONCAT(lead_details.lead_form_value SEPARATOR "|") as lead_form_values'),
            ])
            ->leftJoin('lead_details', 'leads.id', '=', 'lead_details.lead_id')
            ->where('leads.status', 'clear')
            ->groupBy('leads.id', 'leads.name', 'leads.status', 'leads.email', 'leads.phone_number')
            ->limit(2)
            ->get();

        $jsonArray = json_decode($jsonData, true);

        $startDate = Carbon::create(2023, 11, 13);
        $endDate = Carbon::create(2024, 4, 30);
        $currentDate = $startDate->copy();

        $result = [];

        $lead_id = 0;
        while ($currentDate->lte($endDate)) {
            $users = User::where('sub_account_id', 8)->get();

            foreach ($users as $user) {
                if ($lead_id == 0) {
                    foreach ($get_leads as $get_leads) {
                        $leadData = [
                            'name' => $get_leads->name,
                            'email' => $get_leads->email,
                            'mobile_number' => $get_leads->phone_number,
                            'additional_data' => [],
                        ];
                        if (!empty($get_leads->lead_form_keys) && !empty($get_leads->lead_form_values)) {
                            $keys = explode('|', $get_leads->lead_form_keys);
                            $values = explode('|', $get_leads->lead_form_values);

                            foreach ($keys as $index => $key) {
                                $leadData['additional_data'][] = [
                                    'key' => $key,
                                    'value' => $values[$index] ?? null,
                                ];
                            }
                        }

                        $save_lead = new LeadClient;
                        $save_lead->client_id = $user->id;
                        $save_lead->name = $get_leads->name ?? '';
                        $save_lead->email = $get_leads->email ?? '';
                        $save_lead->mobile_number = $get_leads->phone_number ?? '';
                        $save_lead->lead_type = 'ppc';
                        $save_lead->added_by_id = $user->id;
                        $save_lead->created_at = $currentDate->toDateString();
                        $save_lead->updated_at = $currentDate->toDateString();
                        $save_lead->save();

                        if (!empty($leadData['additional_data']) && count($leadData['additional_data']) > 0) {
                            $lead_key_data = [];
                            foreach ($leadData['additional_data'] as $data) {
                                $lead_key_data[] = [
                                    'lead_client_id' => $save_lead->id,
                                    'key' => $data['key'],
                                    'value' => $data['value'],
                                    'added_by_id' => $save_lead->client_id,
                                    'created_at' => $currentDate->toDateString(),
                                    'updated_at' => $currentDate->toDateString(),
                                ];
                            }
                            LeadData::insert($lead_key_data);
                        }
                        $lead_id = $get_leads->lead_id;
                    }

                } else {
                    $get_leads = DB::table('leads')
                        ->select([
                            'leads.id as lead_id',
                            'leads.name',
                            'leads.status',
                            'leads.email',
                            'leads.phone_number',
                            'leads.created_at',
                            DB::raw('GROUP_CONCAT(lead_details.lead_form_key SEPARATOR "|") as lead_form_keys'),
                            DB::raw('GROUP_CONCAT(lead_details.lead_form_value SEPARATOR "|") as lead_form_values'),
                        ])
                        ->leftJoin('lead_details', 'leads.id', '=', 'lead_details.lead_id')
                        ->where('leads.status', 'clear')
                        ->where('leads.id', '>', $lead_id)
                        ->groupBy('leads.id', 'leads.name', 'leads.status', 'leads.email', 'leads.phone_number')
                        ->limit(2)
                        ->get();

                    foreach ($get_leads as $get_leads) {
                        $leadData = [
                            'name' => $get_leads->name,
                            'email' => $get_leads->email,
                            'mobile_number' => $get_leads->phone_number,
                            'additional_data' => [],
                        ];
                        if (!empty($get_leads->lead_form_keys) && !empty($get_leads->lead_form_values)) {
                            $keys = explode('|', $get_leads->lead_form_keys);
                            $values = explode('|', $get_leads->lead_form_values);

                            foreach ($keys as $index => $key) {
                                $leadData['additional_data'][] = [
                                    'key' => $key,
                                    'value' => $values[$index] ?? null,
                                ];
                            }
                        }

                        $save_lead = new LeadClient;
                        $save_lead->client_id = $user->id;
                        $save_lead->name = $get_leads->name ?? '';
                        $save_lead->email = $get_leads->email ?? '';
                        $save_lead->mobile_number = $get_leads->phone_number ?? '';
                        $save_lead->lead_type = 'ppc';
                        $save_lead->added_by_id = $user->id;
                        $save_lead->created_at = $currentDate->toDateString();
                        $save_lead->updated_at = $currentDate->toDateString();
                        $save_lead->save();

                        if (!empty($leadData['additional_data']) && count($leadData['additional_data']) > 0) {
                            $lead_key_data = [];
                            foreach ($leadData['additional_data'] as $data) {
                                $lead_key_data[] = [
                                    'lead_client_id' => $save_lead->id,
                                    'key' => $data['key'],
                                    'value' => $data['value'],
                                    'added_by_id' => $save_lead->client_id,
                                    'created_at' => $currentDate->toDateString(),
                                    'updated_at' => $currentDate->toDateString(),
                                ];
                            }
                            LeadData::insert($lead_key_data);
                        }
                        $lead_id = $get_leads->lead_id;
                    }
                }

            }

            // $queryCounter = 0;

            // foreach ($jsonArray as $record) {
            //     $dateCalled = Carbon::createFromFormat('d/m/Y', $record['DATE CALLED']);

            //     if ($dateCalled && $dateCalled->toDateString() == $currentDate->toDateString()) {
            //         $user = User::where('client_name', $record['AGENT NAME'])->first();

            //         if ($user) {
            //             $checkLead = LeadClient::where('name', $record['CLIENT NAME'])->exists();

            //             if (!$checkLead) {
            //                 $ads_lead = new LeadClient();
            //                 $ads_lead->client_id = $user->id;
            //                 $ads_lead->name = $record['CLIENT NAME'] ?? '';
            //                 $ads_lead->email = $record['EMAIL'] ?? '';
            //                 $ads_lead->mobile_number = $record['CLIENT NUMBER'] ?? '';
            //                 $ads_lead->lead_type = 'ppc';
            //                 $ads_lead->added_by_id = $user->id;
            //                 $ads_lead->created_at = $dateCalled->copy()->subDays(2);
            //                 $ads_lead->updated_at = $dateCalled->copy()->subDays(2);
            //                 $ads_lead->save();

            //             }

            //             // if ($queryCounter < 2) {
            //             //     $get_leads = DB::table('leads')
            //             //         ->select([
            //             //             'leads.id as lead_id',
            //             //             'leads.name',
            //             //             'leads.status',
            //             //             'leads.email',
            //             //             'leads.phone_number',
            //             //             'leads.created_at',
            //             //             DB::raw('GROUP_CONCAT(lead_details.lead_form_key SEPARATOR "|") as lead_form_keys'),
            //             //             DB::raw('GROUP_CONCAT(lead_details.lead_form_value SEPARATOR "|") as lead_form_values')
            //             //         ])
            //             //         ->leftJoin('lead_details', 'leads.id', '=', 'lead_details.lead_id')
            //             //         ->whereDate('leads.created_at', $dateCalled->toDateString())
            //             //         ->groupBy('leads.id', 'leads.name', 'leads.status', 'leads.email', 'leads.phone_number')
            //             //         ->limit(2)
            //             //         ->get();

            //             //         foreach ($get_leads as $lead) {
            //             //             $leadData = [
            //             //                 'name' => $lead->name,
            //             //                 'email' => $lead->email,
            //             //                 'mobile_number' => $lead->phone_number,
            //             //                 'additional_data' => [],
            //             //             ];

            //             //             if (!empty($lead->lead_form_keys) && !empty($lead->lead_form_values)) {
            //             //                 $keys = explode('|', $lead->lead_form_keys);
            //             //                 $values = explode('|', $lead->lead_form_values);

            //             //                 foreach ($keys as $index => $key) {
            //             //                     $leadData['additional_data'][] = [
            //             //                         'key' => $key,
            //             //                         'value' => $values[$index] ?? null,
            //             //                     ];
            //             //                 }
            //             //             }

            //             //             $save_lead = new LeadClient();
            //             //             $save_lead->client_id = $user->id;
            //             //             $save_lead->name = $lead->name ?? '';
            //             //             $save_lead->email = $lead->email ?? '';
            //             //             $save_lead->mobile_number = $lead->phone_number ?? '';
            //             //             $save_lead->lead_type = 'ppc';
            //             //             $save_lead->added_by_id = $user->id;
            //             //             $save_lead->created_at = $dateCalled->copy()->subDays(2);
            //             //             $save_lead  ->updated_at = $dateCalled->copy()->subDays(2);
            //             //             $save_lead->save();

            //             //             if ( !empty( $lead->additional_data ) && count( $lead->additional_data ) > 0 ) {
            //             //                 foreach ( $lead->additional_data as $k => $val ) {
            //             //                     $lead_key_data[] = [
            //             //                         'lead_client_id' => $save_lead->id,
            //             //                         'key' => $val->key,
            //             //                         'value' => $val->value,
            //             //                         'added_by_id' => $save_lead->client_id,
            //             //                         'created_at' => $dateCalled->copy()->subDays(2),
            //             //                         'updated_at' => $dateCalled->copy()->subDays(2),
            //             //                     ];
            //             //                 }
            //             //                 LeadData::insert( $lead_key_data );
            //             //             }
            //             //             $result[] = $leadData;
            //             //         }

            //             //     $queryCounter++;
            //             // }
            //         }
            //     }
            // }

            // $get_leads = DB::table('leads')
            //     ->select([
            //         'leads.id as lead_id',
            //         'leads.name',
            //         'leads.status',
            //         'leads.email',
            //         'leads.phone_number',
            //         'leads.created_at',
            //         DB::raw('GROUP_CONCAT(lead_details.lead_form_key SEPARATOR "|") as lead_form_keys'),
            //         DB::raw('GROUP_CONCAT(lead_details.lead_form_value SEPARATOR "|") as lead_form_values')
            //     ])
            //     ->leftJoin('lead_details', 'leads.id', '=', 'lead_details.lead_id')
            //     ->whereDate('leads.created_at', $currentDate->toDateString())
            //     ->groupBy('leads.id', 'leads.name', 'leads.status', 'leads.email', 'leads.phone_number')
            //     ->inRandomOrder()
            //     ->limit(2)
            //     ->get();

            // $users = User::where('sub_account_id', 8)->pluck('id')->toArray();

            // foreach ($get_leads as $lead) {
            //     $randomUserId = $users[array_rand($users)];

            //     $leadData = [
            //         'name' => $lead->name,
            //         'email' => $lead->email,
            //         'mobile_number' => $lead->phone_number,
            //         'additional_data' => [],
            //     ];

            //     if (!empty($lead->lead_form_keys) && !empty($lead->lead_form_values)) {
            //         $keys = explode('|', $lead->lead_form_keys);
            //         $values = explode('|', $lead->lead_form_values);

            //         foreach ($keys as $index => $key) {
            //             $leadData['additional_data'][] = [
            //                 'key' => $key,
            //                 'value' => $values[$index] ?? null,
            //             ];
            //         }
            //     }

            //     $save_lead = new LeadClient();
            //     $save_lead->client_id = $randomUserId;
            //     $save_lead->name = $lead->name ?? '';
            //     $save_lead->email = $lead->email ?? '';
            //     $save_lead->mobile_number = $lead->phone_number ?? '';
            //     $save_lead->lead_type = 'ppc';
            //     $save_lead->added_by_id = $randomUserId;
            //     $save_lead->created_at = $currentDate->toDateString();
            //     $save_lead->updated_at = $currentDate->toDateString();
            //     $save_lead->save();

            //     if (!empty($leadData['additional_data']) && count($leadData['additional_data']) > 0) {
            //         $lead_key_data = [];
            //         foreach ($leadData['additional_data'] as $data) {
            //             $lead_key_data[] = [
            //                 'lead_client_id' => $save_lead->id,
            //                 'key' => $data['key'],
            //                 'value' => $data['value'],
            //                 'added_by_id' => $save_lead->client_id,
            //                 'created_at' => $currentDate->toDateString(),
            //                 'updated_at' => $currentDate->toDateString(),
            //             ];
            //         }
            //         LeadData::insert($lead_key_data);
            //     }
            //     $result[] = $leadData;
            // }

            $currentDate->addDay();
        }

        return response()->json($result);
    }

    public function save_excel_sheet_data()
    {
        $jsonData = '[
                        {
                            "AGENT NAME":"JEREMY TAN",
                            "DATE CALLED":"22/04/2024",
                            "CLIENT NAME":"PEARLYN GOH",
                            "CLIENT NUMBER":81610752,
                            "EMAIL":"NO EMAIL: goh.pearlyn@yahoo.com"
                        },
                        {
                            "AGENT NAME":"CHIA YEI",
                            "DATE CALLED":"18/04/2024",
                            "CLIENT NAME":"GOH SWEE CHOO VERONICA",
                            "CLIENT NUMBER":96838071,
                            "EMAIL":"NO EMAIL: veronveron8896@gmail.com"
                        },
                        {
                            "AGENT NAME":"ALVIN CHOO AND CAROLYN CHOO",
                            "DATE CALLED":"12/4/2024",
                            "CLIENT NAME":"CHAI XIAN YANG",
                            "CLIENT NUMBER":93369220,
                            "EMAIL":"NO EMAIL: 6220chai.xianyang@gmail.com"
                        },
                        {
                            "AGENT NAME":"JASLIN AND GEORGE",
                            "DATE CALLED":"10/04/2024",
                            "CLIENT NAME":"EUNICE",
                            "CLIENT NUMBER":91993401,
                            "EMAIL":"NO EMAIL: eunice5@gmail.com"
                        },
                        {
                            "AGENT NAME":"Robbie x Jen",
                            "DATE CALLED":"25/03/2024",
                            "CLIENT NAME":"HENG WENG SOON IAN",
                            "CLIENT NUMBER":96827440,
                            "EMAIL":"NO EMAIL: ianalmighty66@gmail.com"
                        },
                        {
                            "AGENT NAME":"CHRIS (HUTTONS)",
                            "DATE CALLED":"22/03/2024",
                            "CLIENT NAME":"STEVE TAN WEI QUAN",
                            "CLIENT NUMBER":91016121,
                            "EMAIL":"NO EMAIL: stevietan@gmail.com"
                        },
                        {
                            "AGENT NAME":"MARY",
                            "DATE CALLED":"12/3/2024",
                            "CLIENT NAME":"TAN GEK LING IVY",
                            "CLIENT NUMBER":97720184,
                            "EMAIL":"tanivy2000@yahoo.com.sg"
                        },
                        {
                            "AGENT NAME":"BENSON LAU",
                            "DATE CALLED":"27/02/2024",
                            "CLIENT NAME":"OH SOON LI BERNARD",
                            "CLIENT NUMBER":91003988,
                            "EMAIL":"bernard_oh@mha.gov.sg"
                        },
                        {
                            "AGENT NAME":"ALVIN CHOO AND CAROLYN CHOO",
                            "DATE CALLED":"22/12/2023",
                            "CLIENT NAME":"TUM NGIAP JUNE",
                            "CLIENT NUMBER":96327710,
                            "EMAIL":"NO EMAIL : junetantan10@gmail.com"
                        },
                        {
                            "AGENT NAME":"BENSON LAU",
                            "DATE CALLED":"11/1/2024",
                            "CLIENT NAME":"MAK KEAN LOONG@MAK WALTER",
                            "CLIENT NUMBER":93266599,
                            "EMAIL":"walter.mak@duke-nus.edu.sg"
                        },
                        {
                            "AGENT NAME":"JUSTIN KWEK",
                            "DATE CALLED":"14/11/2023",
                            "CLIENT NAME":"KONG SER YUEN (JIANG SHIYUN)",
                            "CLIENT NUMBER":96830777,
                            "EMAIL":"K.Shiyun@gmail.com"
                        },
                        {
                            "AGENT NAME":"Taguchi x Jaslyn",
                            "DATE CALLED":"01/02/2024",
                            "CLIENT NAME":"NO NAME",
                            "CLIENT NUMBER":97629900,
                            "EMAIL":"NO EMAIL",
                            "TAB NAME":null
                        },
                        {
                            "AGENT NAME":"KEEGAN",
                            "DATE CALLED":"19/12/2023",
                            "CLIENT NAME":"DAVE LOW",
                            "CLIENT NUMBER":96864607,
                            "EMAIL":"NO EMAIL: dabelowzh@gmail.com"
                        },
                        {
                            "AGENT NAME":"BENSON LAU",
                            "DATE CALLED":"27/11/2023",
                            "CLIENT NAME":"CHIA YIXIONG LEONARD",
                            "CLIENT NUMBER":94882292,
                            "EMAIL":"leonard.chia@gmail.com"
                        },
                        {
                            "AGENT NAME":"JUSTIN KWEK",
                            "DATE CALLED":"12/12/2023",
                            "CLIENT NAME":"WU WAN YI\/ HUANG QUN XU",
                            "CLIENT NUMBER":98779857,
                            "EMAIL":"NO EMAIL ww675@yahoo.com"
                        },
                        {
                            "AGENT NAME":"Stephanie",
                            "DATE CALLED":"23/11/2023",
                            "CLIENT NAME":"Vivian Lim",
                            "CLIENT NUMBER":81636881,
                            "EMAIL":"lin_huiyun@hotmail.com"
                        },
                        {
                            "AGENT NAME":"BENSON LAU",
                            "DATE CALLED":"27/11/2023",
                            "CLIENT NAME":"CHIA YIXIONG LEONARD",
                            "CLIENT NUMBER":94882292,
                            "EMAIL":"leonard.chia@gmail.com"
                        },
                        {
                            "AGENT NAME":"ALVIN X SHIRLEY",
                            "DATE CALLED":"23/11/2023",
                            "CLIENT NAME":"TAN CHYE TENG\/ HOON TIAN LOONG",
                            "CLIENT NUMBER":96425401,
                            "EMAIL":"NO EMAIL: chyeteng98@gmail.com"
                        }
                    ]';

        $records = json_decode($jsonData, true);
        // dd($records);
        foreach ($records as $record) {
            try {
                $dateCalled = Carbon::createFromFormat('d/m/Y', $record['DATE CALLED']);

                if ($dateCalled) {
                    $user = User::where('client_name', $record['AGENT NAME'])->first();

                    if ($user) {
                        $ads_lead = new LeadClient;
                        $ads_lead->client_id = $user->id;
                        $ads_lead->name = $record['CLIENT NAME'] ?? '';
                        $ads_lead->email = $record['EMAIL'] ?? '';
                        $ads_lead->mobile_number = $record['CLIENT NUMBER'] ?? '';
                        $ads_lead->lead_type = 'ppc';
                        $ads_lead->added_by_id = $user->id;
                        $ads_lead->created_at = $dateCalled->copy()->subDays(2);
                        $ads_lead->updated_at = $dateCalled->copy()->subDays(2);
                        $ads_lead->save();
                    }
                }
            } catch (Exception $e) {
                return response()->json(['message' => 'Error processing record: '.$e->getMessage()]);
            }
        }

        return response()->json(['message' => 'Data saved successfully']);
    }

    public function save_leads()
    {

        $startDate = Carbon::create(2023, 11, 13);
        $endDate = Carbon::create(2024, 4, 30);

        // $startDate = Carbon::create(2023, 11, 13);
        // $endDate = Carbon::create(2023, 11, 15);

        $currentDate = $startDate->copy();
        $last_lead_id_processed = 0;

        while ($currentDate->lte($endDate)) {

            for ($loop_run_per_date = 0; $loop_run_per_date < 2; $loop_run_per_date++) {
                if ($loop_run_per_date === 0) {
                    $users = User::where('sub_account_id', 8)->limit(18)->get();

                } else {
                    $randomNumber = rand(5, 18);
                    $users = User::where('sub_account_id', 8)->inRandomOrder()->limit($randomNumber)->get();
                }

                foreach ($users as $user) {
                    $lead = DB::table('leads')
                        ->select([
                            'leads.id as lead_id',
                            'leads.name',
                            'leads.status',
                            'leads.email',
                            'leads.phone_number',
                            'leads.created_at',
                            DB::raw('GROUP_CONCAT(lead_details.lead_form_key SEPARATOR "|") as lead_form_keys'),
                            DB::raw('GROUP_CONCAT(lead_details.lead_form_value SEPARATOR "|") as lead_form_values'),
                        ])
                        ->leftJoin('lead_details', 'leads.id', '=', 'lead_details.lead_id')
                        ->where('leads.status', 'clear')
                        ->whereDate('leads.created_at', $currentDate->toDateString())
                        ->where('leads.id', '>', $last_lead_id_processed ?? 0) // Ensure we fetch leads sequentially
                        ->orderBy('leads.id')
                        ->groupBy('leads.id')
                        ->first();

                    if ($lead) {
                        $statuses = ['Appointment Set', 'Follow Up', 'Not Interested'];
                        $selected_status = $statuses[array_rand($statuses)];

                        $last_lead_id_processed = $lead->lead_id;

                        // Save the lead to LeadClient
                        $save_lead = new LeadClient;
                        $save_lead->client_id = $user->id;
                        $save_lead->name = $lead->name ?? '';
                        $save_lead->email = $lead->email ?? '';
                        $save_lead->mobile_number = $lead->phone_number ?? '';
                        $save_lead->lead_type = 'ppc';
                        $save_lead->added_by_id = $user->id;
                        $save_lead->created_at = $currentDate;
                        $save_lead->updated_at = $currentDate;
                        $save_lead->admin_status = $selected_status;
                        $save_lead->save();

                        // Save lead details to LeadData
                        $leadKeys = explode('|', $lead->lead_form_keys);
                        $leadValues = explode('|', $lead->lead_form_values);
                        $leadData = [];
                        foreach ($leadKeys as $index => $key) {
                            if (!isset($leadValues[$index])) {
                                $leadData = [];

                                continue;
                            }
                            $leadData[] = [
                                'lead_client_id' => $save_lead->id,
                                'key' => $key,
                                'value' => $leadValues[$index],
                                'added_by_id' => $save_lead->client_id,
                                'created_at' => $currentDate,
                                'updated_at' => $currentDate,
                            ];
                        }
                        LeadData::insert($leadData);
                    }
                }
            }

            $currentDate->addDay();
        }

        return response()->json([
            'success' => true,
            'message' => 'Leads Save Successfully',
        ]);

    }
}
