<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FetchLeadsController;
use App\Http\Controllers\Api\PayNowController;
use App\Http\Controllers\Api\GoogleAdsController;
use App\Http\Controllers\Api\TourController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Routes with XSS Middleware
Route::middleware(['XSS', 'api'])->group(function () {
    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::post('/register', 'register')->name('auth.register');
        Route::post('/login', 'login')->name('auth.login');
        Route::post('/refresh-token', 'refresh')->name('auth.refresh-token');
    });

    // Lead Frequency Routes
    Route::prefix('lead_frequency')->as('lead_frequency.')->controller(FetchLeadsController::class)->group(function () {
        Route::post('/add_lead', 'add_ppc_lead')->name('add_lead');
        Route::get('/week_payment', 'week_payment')->name('week_payment');
        Route::get('/monthly_payment', 'monthly_payment')->name('monthly_payment');
        Route::get('/send_lead_on_discord/{leads_start_time}', 'send_client_leads_on_discord')->name('send_lead_on_discord');
        Route::post('/send_que_leads', 'send_que_leads')->name('send_que_leads');
        Route::post('/get_lead_form_website', 'get_lead_form_website')->name('get_lead_form_website');
    });
    
    // Message Template Routes
    Route::prefix('message_template')->controller(FetchLeadsController::class)->group(function () {
        Route::get('/add_message_to_user', 'add_message_to_user')->name('add_message_to_user');
        Route::get('/assign_template_to_clients', 'assign_template_to_clients')->name('assign_template_to_clients');
    });
    
    // PayNow Webhook
    Route::prefix('paynow-webhook')->controller(PayNowController::class)->group(function () {
        Route::post('/', 'paynow_webhook')->name('api_paynow_webhook');
    });

    // Google Ads Routes
    Route::prefix('google_ads')->as('google_ads.')->controller(GoogleAdsController::class)->group(function () {
        Route::get('/campaign', 'google_ads_campaign')->name('campaign');
        Route::get('/ad_group', 'google_ads_ad_group')->name('ad_group');
        Route::get('/ad_group_ad', 'google_ads_ad_group_ad')->name('ad_group_ad');
        Route::get('/conversion_action', 'google_ads_conversion_action')->name('conversion_action');
        Route::get('/geo_target_constant', 'google_ads_geo_target_constant')->name('geo_target_constant');
        Route::get('/customer_id', 'google_ads_customer_id')->name('customer_id');
    });

    // Tour Routes
    Route::prefix('tour')->as('tour.')->controller(TourController::class)->group(function () {
        Route::post('/', 'store')->name('store');
    });
});
