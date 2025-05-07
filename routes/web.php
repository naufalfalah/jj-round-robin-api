<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ReminderController;
use App\Models\Role;

Auth::routes(['verify' => true]);

Route::get('/', function () {
    if (auth('admin')->check()) {
        return redirect()->route('admin.home');
    } else {
        return redirect()->route('user.dashboard');
    }
});

//admin auth routes
Route::prefix('web_admin')->middleware('XSS')->controller(AdminLoginController::class)->as('admin.')->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/logout', 'logout')->name('logout');
    Route::post('/login', 'login')->name('login.submit');

    Route::get('/change_password', 'change_password')->name('change_password');
    Route::get('/forget_password', 'forget_password')->name('forget_password');
    Route::post('/send_email', 'send_email')->name('send_email');
    Route::get('/add_new_pasword/{id}', 'password_screen')->name('add_new_pasword');
    Route::post('/save_new_password', 'save_new_password')->name('save_new_password');

});
Route::post('check_email', [RegisterController::class, 'check_email'])->name('check_email');

Route::middleware(['admin'])->prefix('web_admin')->as('admin.')->namespace('App\Http\Controllers\Administrator')->group(function () {
    Route::get('/', 'DashboardController@index')->name('home');
    Route::post('/save_sub_account', 'DashboardController@save_sub_account')->name('save_sub_account');
    Route::get('/notifications', 'DashboardController@notifications')->name('notifications');
    Route::get('/update_notifications', 'DashboardController@update_notifications')->name('update_notifications');
    Route::get('/update-sub-account-status/{id}/{status}', 'DashboardController@update_sub_account_status')->name('update_sub_account_status');
    Route::get('/sub_account/{id}', 'DashboardController@subAccountShow')->name('sub_account');

    // // user Management
    // Route::middleware(['XSS'])->prefix('user-management')->as('user-management.')->controller('EmployeeManagmentController')->group(function () {
    //     Route::get('/add-user', 'add')->name('add-user');
    //     Route::post('/save', 'save')->name('save');
    //     Route::get('/view', 'view')->name('view');
    //     Route::get('/edit/{id}', 'edit')->name('edit');
    //     Route::get('/delete/{id}', 'delete')->name('delete');
    //     Route::post('/update-password', 'update_password')->name('update-password');
    // });

    // // Permission Type
    // Route::middleware(['XSS'])->prefix('permission_type')->as('permission_type.')->controller('PermissionTypeController')->group(function () {
    //     Route::get('/', 'permissionType')->name('permission_type');
    //     Route::post('/save', 'save')->name('save');
    //     Route::get('/edit/{id}', 'edit')->name('edit');
    //     Route::get('/delete{id}', 'delete')->name('delete');
    // });

    // // Permission
    // Route::middleware(['XSS'])->prefix('permission')->as('permission.')->controller('PermissionController')->group(function () {
    //     Route::get('/', 'index')->name('permission');
    //     Route::post('/save', 'save')->name('save');
    //     Route::get('/edit/{id}', 'edit')->name('edit');
    //     Route::get('/delete{id}', 'delete')->name('delete');
    // });

    // // Role
    // Route::middleware(['XSS'])->prefix('role')->as('role.')->controller('RoleController')->group(function () {
    //     Route::get('/all', 'index')->name('all');
    //     Route::get('/add', 'add')->name('add');
    //     Route::post('/save', 'save')->name('save');
    //     Route::get('/edit/{id}', 'edit')->name  ('edit');
    //     Route::get('/delete{id}', 'delete')->name('delete');
    // });

    Route::prefix('profile')->as('profile.')->group(function () {
        Route::get('/', 'DashboardController@edit_profile')->name('edit');
        Route::post('/update', 'DashboardController@update_profile')->name('update');
        Route::post('/set_token', 'DashboardController@save_device_token')->name('save_device_token');
    });

    Route::prefix('sub_account/{sub_account_id}')->as('sub_account.')->group(function () {
        // Client Running Ads
        Route::middleware(['XSS'])->prefix('advertisements')->as('advertisements.')->controller('RunningAdsController')->group(function () {
            Route::get('/running_ads', 'index')->name('running_ads');
            Route::get('/get_topups', 'get_topups')->name('get_topups');
            Route::get('/get_ads', 'get_ads')->name('get_ads');
            Route::get('/get_main_wallet', 'get_main_wallet')->name('get_main_wallet');
            Route::get('/get_low_bls_ads', 'get_low_bls_ads')->name('get_low_bls_ads');
            Route::post('/get_user_bls', 'get_user_bls')->name('get_user_bls');
            Route::post('/save', 'event_save')->name('event_save');
            Route::post('/change-status', 'change_status')->name('change-status');
            Route::post('/change-ads-status', 'change_ads_status')->name('change-ads-status');
            Route::post('/change-ads_running-status', 'change_ads_running_status')->name('change-ads_running-status');
            Route::post('/ads-remaining-balance-refund', 'ads_remaining_balance_refund')->name('ads-remaining-balance-refund');
            Route::post('/edit_add', 'edit_add')->name('edit_add');
            Route::get('/all-clients', 'transactions')->name('transactions');
            Route::get('/get_lead', 'get_leads')->name('get_lead');
            Route::get('/get_follow_ups', 'get_follow_ups')->name('get_follow_ups');
            Route::get('/lead_detail/{id}', 'lead_detail')->name('lead_detail');
            Route::post('/lead_status', 'lead_status')->name('lead_status');
            Route::post('/lead_admin_status', 'lead_admin_status')->name('lead_admin_status');
            Route::post('/get_all_leads', 'get_all_leads')->name('get_all_leads');
            Route::post('/get_ppc_leads', 'get_ppc_leads')->name('get_ppc_leads');
            Route::get('/get_daily_ads_spent', 'get_daily_ads_spent')->name('get_daily_ads_spent');
            Route::post('/daily_ads_spent_save', 'daily_ads_spent_save')->name('daily_ads_spent_save');
            Route::get('/get_monthly_ads_spent', 'get_monthly_ads_spent')->name('get_monthly_ads_spent');
            Route::get('/get_monthly_client', 'get_monthly_client')->name('get_monthly_client');
            Route::get('/view_progress/{ads_id}/{client_id}', 'view_progress')->name('view_progress');
            Route::post('/progress/get_leads_data', 'get_leads_data')->name('get_leads_data');
            Route::post('/lead_admin_status', 'lead_admin_status')->name('lead_admin_status');
            Route::get('/get_sub_wallet_transactions/{client_id}/{ads_id}', 'get_sub_wallet_transactions')->name('get_sub_wallet_transactions');
            Route::post('/sub_wallets_transactions', 'sub_wallets_transactions')->name('sub_wallets_transactions');
            Route::post('/sub_wallets_bls_update', 'sub_wallets_bls_update')->name('sub_wallets_bls_update');
        });

        // Client Management
        Route::middleware(['XSS'])->prefix('client-management')->as('client-management.')->controller('ClientManagmentController')->group(function () {
            Route::controller('ClientManagmentController')->group(function () {
                Route::get('/', 'index')->name('all');
                Route::get('all_clients/', 'all_clients')->name('all_clients');
                Route::get('/clone_client/{id}', 'clone_client')->name('clone_client');
                Route::get('/add', 'add')->name('add');
                Route::post('/save', 'save')->name('save');
                Route::post('/get_agency_address', 'get_agency_address')->name('get_agency_address');
                Route::get('/edit', 'edit')->name('edit');
                Route::get('/delete/{id}', 'delete')->name('delete');
                Route::post('/update-password', 'update_password')->name('update-password');
    
                // Top Up
                Route::get('/top_up', 'top_up')->name('top_up');
                Route::post('/topup_save', 'topup_save')->name('topup_save');
                Route::get('/topup_edit/{id}', 'topup_edit')->name('topup_edit');
                Route::get('/topup_delete{id}', 'topup_delete')->name('topup_delete');
    
                // Ads Request route
                Route::get('/ads', 'all_ads')->name('all_ads');
                Route::get('/ads/create', 'ads_create')->name('ads_create');
                Route::post('/ads_save', 'ads_save')->name('ads_save');
                Route::get('/ads_edit/{id}', 'ads_edit')->name('ads_edit');
                // Route::post('/get_adds', 'get_adds')->name('get_adds');
                Route::get('/ads_delete/{id}', 'ads_delete')->name('ads_delete');
            });

            Route::controller('GoogleAdsController')->group(function () {
                // Google Ads
                Route::get('/google_ads_campaign', 'google_ads_campaign')->name('google_ads_campaign');
                Route::get('/google_ads_campaign/edit', 'google_ads_campaign_edit')->name('google_ads_campaign.edit');
                Route::put('/google_ads_campaign', 'google_ads_campaign_update')->name('google_ads_campaign.update');
    
                Route::get('/google_ads_ad_group', 'google_ads_ad_group')->name('google_ads_ad_group');
                Route::get('/google_ads_ad_group/show', 'google_ads_ad_group_show')->name('google_ads_ad_group.show');
    
                Route::get('/google_ads_ad_group_ad', 'google_ads_ad_group_ad')->name('google_ads_ad_group_ad');
                Route::get('/google_ads_ad_group_ad/create', 'google_ads_create')->name('google_ads.create');
                Route::post('/google_ads_ad_group_ad', 'google_ads_store')->name('google_ads.store');
                Route::post('/google_ads_ad_group_ad/sync', 'google_ads_sync')->name('google_ads.sync');
    
                Route::get('/google_ads_conversion_action', 'google_ads_conversion_action')->name('google_ads_conversion_action');
                Route::get('/google_ads_conversion_action/create', 'google_ads_conversion_action_create')->name('google_ads_conversion_action.create');
                Route::post('/google_ads_conversion_action', 'google_ads_conversion_action_store')->name('google_ads_conversion_action.store');
            });
        });

        // Google Ads Report
        Route::middleware(['XSS'])->prefix('google-ads-report')->as('google-ads-report.')->controller('GoogleAdsReportController')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/download_pdf', 'download_pdf')->name('download_pdf');
            Route::post('/save_google_report', 'save_google_report')->name('save_google_report');
            Route::post('/campaign_note_save', 'campaign_note_save')->name('campaign_note_save');
            Route::get('/campaign_note_delete/{id}', 'campaign_note_delete')->name('campaign_note_delete');
            Route::get('/google_act_disconnect', 'google_act_disconnect')->name('google_act_disconnect');
            Route::post('update_act_expiry_date', 'update_act_expiry_date')->name('update_act_expiry_date');
        });
    });

    //Settings
    Route::middleware(['XSS'])->prefix('setting')->as('setting.')->group(function () {
        Route::controller('GoogleAccountController')->group(function () {
            // Admin Google Connectivity
            Route::get('/google_account', 'index')->name('google_account');
            Route::get('/connect', 'getAuthUrl')->name('connect');
            Route::get('/oauth', 'oauth')->name('oauth');
            Route::get('/disconnect', 'disconnect')->name('disconnect');
            Route::get('/refresh_token/{id}', 'refresh_token')->name('refresh_token');
        });

        //Taxes & vat charges
        Route::get('/taxes', 'SettingController@tex_vat_charges')->name('taxes');
        Route::post('/tax_store', 'SettingController@tax_store')->name('tax_store');

        //topup setting
        Route::post('/topup_store', 'SettingController@topup_store')->name('topup_store');

        //WhatsApp Message Template
        Route::get('/whatsapp_temp', 'SettingController@whatsapp_temp')->name('whatsapp_temp');
        Route::post('/wp_message_store', 'SettingController@wp_message_store')->name('wp_message_store');

        // assign template to client
        Route::get('/assign_template_to_clients', 'SettingController@assign_template_to_clients')->name('assign_template_to_clients');
    });

    Route::middleware(['XSS'])->prefix('agency')->as('agency.')->controller('AgencyController')->group(function () {
        Route::get('/', 'index')->name('all');
        Route::post('/save', 'save')->name('save');
        Route::get('/edit/{id}', 'edit')->name('edit');
    });
});


// web user auth routes start

Route::prefix('auth')->middleware('XSS')->as('auth.')->namespace('App\Http\Controllers\Auth')->group(function () {
    Route::get('/', 'LoginController@showLoginForm')->name('login');
    Route::post('/login/submit', 'LoginController@login')->name('login.submit');
    Route::post('/logout', 'LoginController@logout')->name('logout');
    Route::get('/forget_password', 'ForgetPasswordController@forget_password')->name('forget_password');
    Route::post('/send_email', 'ForgetPasswordController@send_email')->name('send_email');
    Route::get('/add_new_pasword/{id}', 'ForgetPasswordController@password_screen')->name('add_new_pasword');
    Route::post('/save_new_password', 'ForgetPasswordController@save_new_password')->name('save_new_password');

    Route::get('/register', 'RegisterController@showRegistrationForm')->name('register');
    Route::post('/register/submit', 'RegisterController@register')->name('register.submit');
    Route::get('/success_message', 'RegisterController@success_message')->name('success_message');
    Route::get('/verify/{id}', 'RegisterController@password_screen')->name('verify');
    Route::post('/password/submit', 'RegisterController@password_save')->name('password.submit');

    Route::get('/regenerate-sheet', 'RegisterController@regenerateSheet');
});

Route::prefix('user')->as('user.')->middleware(['client'])->namespace('App\Http\Controllers\Frontend')->group(function () {
    Route::get('/', 'DashboardController@index')->name('dashboard');
    Route::post('/get_latest_leads_dashboard', 'DashboardController@get_leads')->name('get_latest_leads_dashboard');

    Route::prefix('profile')->as('profile.')->group(function () {
        Route::get('/', 'DashboardController@edit_profile')->name('edit');
        Route::post('/update', 'DashboardController@update_profile')->name('update');
        Route::post('/update_password', 'DashboardController@update_password')->name('update_password');
        Route::post('/set_token', 'DashboardController@save_device_token')->name('save_device_token');
    });

    Route::prefix('client_tour')->as('client_tour.')->controller('ClientTourController')->group(function () {
        Route::get('/restart', 'restart')->name('restart');
    });

    Route::get('/notifications', 'DashboardController@notifications')->name('notifications');
    Route::get('/update_notifications', 'DashboardController@update_notifications')->name('update_notifications');

    // Leads Management
    Route::middleware(['XSS'])->prefix('leads-management')->as('leads-management.')->controller('LeadController')->group(function () {
        Route::get('/all', 'index')->name('all');
    });

    Route::middleware(['XSS'])->prefix('leads-management')->as('leads-management.')->controller('LeadController')->group(function () {
        Route::get('/leads', 'ppc_leads')->name('leads');
        Route::post('/lead_status', 'lead_status')->name('lead_status');
        Route::post('/lead_status', 'get_leads')->name('get_leads_all');
    });

    // Client Wallet
    Route::middleware(['XSS'])->prefix('wallet')->as('wallet.')->controller('WalletController')->group(function () {
        Route::get('/all', 'index')->name('all');
        Route::get('/add', 'add_top_up')->name('add');
        Route::post('/save', 'save')->name('save');
        Route::get('/transaction_table', 'transaction_table')->name('transaction_table');
        Route::get('transactions', 'transactions')->name('transactions');
        Route::get('sub_wallets', 'sub_wallets')->name('sub_wallets');
        Route::post('sub_wallets_transactions', 'sub_wallets_transactions')->name('sub_wallets_transactions');
        Route::post('add_topup_subwallet', 'add_topup_subwallet')->name('add_topup_subwallet');
        Route::get('transfer-funds', 'transfer_funds')->name('transfer_funds');
        Route::Post('funds_save', 'funds_save')->name('funds_save');
        Route::get('/view_fund_transections', 'view_fund_transections')->name('view_fund_transections');
        Route::get('/transaction_report', 'transaction_report')->name('transaction_report');
        Route::get('/add_paynow_transaction_id', 'add_paynow_transaction_id')->name('add_paynow_transaction_id');
        Route::post('/wallet_close', 'walletClose')->name('wallet_close');
    });

    // Stripe Payment
    Route::middleware(['XSS'])->prefix('stripe')->as('stripe.')->controller('StripePaymentController')->group(function () {
        Route::get('/checkout/{price}/{product}/{ad_id}', 'stripeCheckout')->name('checkout');
        Route::get('/checkout/success', 'stripeCheckoutSuccess')->name('checkout.success');
    });

    // PayNow Payment
    Route::middleware(['XSS'])->prefix('paynow')->as('paynow.')->controller('PayNowPaymentController')->group(function () {
        Route::get('/checkout', 'payNowCheckout')->name('checkout');
        Route::get('/checkout/success', 'paynowCheckoutSuccess')->name('checkout.success');
    });

    // Client Ads
    Route::middleware(['XSS'])->prefix('ads')->as('ads.')->controller('AdsManagementController')->group(function () {
        Route::get('/all', 'index')->name('all');
        Route::get('/add', 'add_ads')->name('add');
        Route::get('/delete/{id}', 'delete')->name('delete');
        Route::get('/edit/{id}', 'edit_ads')->name('edit');
        Route::put('/update/{id}', 'update')->name('update');
        Route::post('/save', 'save')->name('save');
        Route::get('/view/progress/{id}', 'view_progress')->name('view_progress');
        Route::post('/view/progress/get_leads_data', 'get_leads_data')->name('get_leads_data');
        Route::post('/view/progress/lead_admin_status', 'lead_admin_status')->name('lead_admin_status');
        Route::get('/check_domain', 'check_domain')->name('check_domain');

    });

    // Client Ads Report
    Route::middleware(['XSS'])->prefix('report')->as('report.')->controller('AdsReportController')->group(function () {
        Route::get('/', 'index')->name('view');
        Route::get('/slip/{id}', 'slip')->name('slip');
    });


    Route::middleware(['XSS'])->prefix('message_template')->as('message_template.')->controller('MessageTemplateController')->group(function () {
        Route::get('/', 'whatsapp_temp')->name('whatsapp_temp');
        Route::post('/wp_message_store', 'wp_message_store')->name('wp_message_store');

    });

    // Google Ads Report
    Route::middleware(['XSS'])->prefix('google-ads-report')->as('google-ads-report.')->controller('GoogleAdsReportFrontendController')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/download_pdf', 'download_pdf')->name('download_pdf');
        Route::post('/save_google_report', 'save_google_report')->name('save_google_report');
        Route::post('/campaign_note_save', 'campaign_note_save')->name('campaign_note_save');
        Route::get('/campaign_note_delete/{id}', 'campaign_note_delete')->name('campaign_note_delete');
        Route::post('update_act_expiry_date', 'update_act_expiry_date')->name('update_act_expiry_date');
    });
});
