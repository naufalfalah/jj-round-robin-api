<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::namespace('App\Http\Controllers\Api')->prefix('lead_frequency')->group(function () {
    Route::post('/add_lead', 'FetchLeadsController@add_ppc_lead')->name('lead_frequency.add_lead');
    Route::get('/week_payment', 'FetchLeadsController@week_payment')->name('lead_frequency.week_payment');
    Route::get('/monthly_payment', 'FetchLeadsController@monthly_payment')->name('lead_frequency.monthly_payment');
    Route::get('/send_lead_on_discord/{leads_start_time}', 'FetchLeadsController@send_client_leads_on_discord')->name('lead_frequency.send_lead_on_discord');
    Route::post('/send_que_leads', 'FetchLeadsController@send_que_leads')->name('lead_frequency.send_que_leads');

    Route::post('/get_lead_form_website', 'FetchLeadsController@get_lead_form_website')->name('lead_frequency.get_lead_form_website');
});


Route::namespace('App\Http\Controllers\Api')->prefix('message_template')->group(function () {
    Route::get('/add_message_to_user', 'FetchLeadsController@add_message_to_user')->name('add_message_to_user');
    Route::get('assign_template_to_clients', 'FetchLeadsController@assign_template_to_clients')->name('assign_template_to_clients');
});

Route::post('/paynow-webhook', "App\Http\Controllers\Api\PayNowController@paynow_webhook")->name('api_paynow_webhook');

Route::middleware(['XSS'])->namespace('App\Http\Controllers\Api')->group(function () {
    Route::prefix('google_ads')->as('google_ads.')->controller('GoogleAdsController')->group(function () {
        Route::get('/campaign', 'google_ads_campaign')->name('campaign');
        Route::get('/ad_group', 'google_ads_ad_group')->name('ad_group');
        Route::get('/ad_group_ad', 'google_ads_ad_group_ad')->name('ad_group_ad');
        Route::get('/conversion_action', 'google_ads_conversion_action')->name('conversion_action');
        Route::get('/geo_target_constant', 'google_ads_geo_target_constant')->name('geo_target_constant');
        Route::get('/customer_id', 'google_ads_customer_id')->name('customer_id');
    });

    Route::prefix('tour')->as('tour.')->controller('TourController')->group(function () {
        Route::post('/', 'store')->name('store');
    });
});
