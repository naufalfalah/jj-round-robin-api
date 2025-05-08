<?php

use App\Http\Controllers\Api\AgencyController;
use App\Http\Controllers\Api\AIChatController;
use App\Http\Controllers\Api\AIVoiceChatController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AssignTaskController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CalendarController;
use App\Http\Controllers\Api\CloudTalkController;
use App\Http\Controllers\Api\ContentWritingController;
use App\Http\Controllers\Api\EbookController;
use App\Http\Controllers\Api\EmailTemplateController;
use App\Http\Controllers\Api\FetchLeadsController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\FolderController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\GoogleAdsController;
use App\Http\Controllers\Api\GoogleAdsReportController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\IndustryController;
use App\Http\Controllers\Api\LeadClientController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\MessageTemplateController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\PageTemplateController;
use App\Http\Controllers\Api\PayNowController;
use App\Http\Controllers\Api\PostScheduleController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\SocialMediaMarketingController;
use App\Http\Controllers\Api\StripeController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\TelemarketingController;
use App\Http\Controllers\Api\TourController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\WhatsappTemplateController;
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

Route::middleware(['api', 'XSS'])
    ->group(function () {
        // Authentication
        Route::controller(RegisterController::class)
            ->prefix('auth')
            ->group(function () {
                Route::post('/register', 'register');
                Route::post('/otp/resend', 'resendOtp');
                Route::post('/otp/verify', 'verifyOtp');
            });

        Route::controller(AuthController::class)
            ->prefix('auth')
            ->group(function () {
                Route::post('/login', 'login');
                Route::post('/login/google', 'redirectToGoogle');
                Route::get('/login/google/callback', 'handleGoogleCallback');
                Route::post('/login/facebook', 'redirectToFacebook');
                Route::get('/login/facebook/callback', 'handleFacebookCallback');
            });

        Route::controller(AuthController::class)
            ->middleware('auth:api')
            ->group(function () {
                Route::post('/refresh-token', 'refresh');
                Route::post('/logout', 'logout');
            });

        Route::controller(ForgotPasswordController::class)
            ->prefix('forget-password')
            ->group(function () {
                Route::post('/check-email', 'checkEmail');
                Route::post('/reset-password', 'resetPassword');
                Route::post('/otp/resend', 'resendOtp');
                Route::post('/otp/verify', 'verifyOtp');
            });

        // Main Menu
        Route::controller(HomeController::class)
            ->middleware('auth:api')
            ->prefix('home')
            ->group(function () {
                Route::get('/', 'index');
                Route::get('/started', 'getStartedTasks');
            });

        Route::controller(ProfileController::class)
            ->middleware('auth:api')
            ->group(function () {
                Route::post('/user-profile-update', 'userProfile');
                Route::post('/change-password', 'changePassword');
                Route::post('/change-password/otp/resend', 'resendOtp');
                Route::post('/change-password/otp/verify', 'verifyOtp');
                Route::post('/user-device-token', 'deviceToken');
                Route::get('/user-notification-types', 'userNotification');
                Route::post('/user-notification-types', 'userNotificationStore');
            });

        Route::controller(AgencyController::class)
            ->group(function () {
                Route::get('/get-agencies', 'getAgencies');
            });

        Route::controller(IndustryController::class)
            ->group(function () {
                Route::prefix('industry')
                    ->group(function () {
                        Route::get('/industries', 'getIndustries');
                    });
                Route::middleware('auth:api')->post('/update-industry', 'saveIndustry');
            });

        Route::controller(NotificationController::class)
            ->group(function () {
                Route::middleware('auth:api')->get('/notifications', 'index');
                Route::get('/notification-types', 'types');
            });

        // Wallet
        Route::controller(PackageController::class)
            ->prefix('package')
            ->as('package.')
            ->group(function () {
                Route::get('/add_paynow_transaction_id', 'add_paynow_transaction_id')->name('add_paynow_transaction_id');
                Route::post('/buy', 'buy')->name('buy');
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}', 'show')->name('show');
            });
        Route::controller(WalletController::class)
            ->prefix('wallet')
            ->as('wallet.')
            ->group(function () {
                Route::get('/main-balance', 'getMainWalletBalance');
                Route::get('/sub-wallets', 'getSubWallets');
                Route::get('/top-up-transactions', 'getTopUpTransactions');
                Route::get('/tour-status', 'checkUserTourStatus');
                Route::get('/page-data', 'getWalletPageData');
            });

        Route::controller(StripeController::class)
            ->prefix('stripe')
            ->as('stripe.')
            ->group(function () {
                Route::get('/checkout/{price}/{product}/{ad_id}', 'stripeCheckout')->name('checkout');
                Route::get('/checkout/success', 'stripeCheckoutSuccess')->name('checkout.success');
            });

        Route::controller(PayNowController::class)
            ->group(function () {
                Route::post('/paynow-webhook', 'paynow_webhook')->name('api_paynow_webhook');

                Route::prefix('paynow')
                    ->as('paynow.')
                    ->group(function () {
                        Route::get('/checkout', 'payNowCheckout')->name('checkout');
                        Route::get('/checkout/success', 'paynowCheckoutSuccess')->name('checkout.success');
                    });
            });

        // File Manager
        Route::controller(FolderController::class)
            ->group(function () {
                Route::get('/get-folders', 'getFolders');
                Route::post('/create-folder', 'createFolder');
                Route::post('/rename-folder', 'renameFolder');
            });

        Route::controller(FileController::class)
            ->group(function () {
                Route::get('/get-files', 'getFiles');
                Route::post('/upload-file', 'uploadFile');
                Route::post('/rename-file', 'renameFile');
                Route::post('/delete-file', 'deleteFile');
            });

        Route::controller(MessageTemplateController::class)
            ->group(function () {
                Route::get('/get-templates', 'getTemplates');
                Route::post('/add-new-template', 'createMessageTemplate');
                Route::post('/get-single-templates', 'getTemplate');
                Route::post('/edit-template', 'editTemplate');
                Route::post('/delete-template', 'deleteTemplate');
                Route::post('/email_send', 'sendEmail');
                Route::post('/send_whatsapp_msg', 'sendWhatsappMessage');
                Route::post('/send-message', 'sendMessage');
            });

        // Lead
        Route::controller(GroupController::class)
            ->prefix('groups')
            ->as('groups.')
            ->group(function () {
                Route::get('/all', 'getGroups');
                Route::post('/create', 'createGroup');
                Route::post('/delete', 'delete_group');
                Route::get('/get_lead_source', 'get_source');
                Route::get('/get_uncontacted_leads', 'get_uncontacted_leads');
                Route::get('/get_recently_viewed_content', 'get_recently_viewed_content');
                Route::post('/get_lead_by_source', 'get_lead_by_source');
                Route::get('/single', 'single');
                Route::post('/lead-assign', 'group_lead_save');
            });

        Route::controller(LeadClientController::class)
            ->group(function () {
                Route::get('/get-client-list', 'get_client_leads');
                Route::get('/get-new-leads', 'get_client_new_leads');
                Route::get('/get-follow-up', 'get_follow_up');
                Route::post('/add-new-client', 'add_new_client');
                Route::post('/edit-client', 'edit_client');
                Route::post('/add-follow-up', 'add_follow_up');
                Route::post('/schedule-an-activity', 'schedule_an_activity');
                Route::post('/quick_response_activity', 'quick_response_activity');
                Route::post('/delete-follow-up', 'delete_follow_up');
                Route::post('/get-single-lead', 'get_single_lead');
                Route::post('/delete-client', 'delete_client');
                Route::post('/update-lead-status', 'upd_lead_status');
                Route::post('/import-clients', 'import_clients_lead');
                Route::get('/get-teams', 'get_teams');
                Route::get('/get-assigned-leads', 'get_assigned_leads');
                Route::get('/get-unassigned-leads', 'get_unassigned_leads');
                Route::post('/assign-leads', 'assign_leads');
                Route::post('/get-lead-activities', 'get_leads_activities');
                Route::post('/get-client-activities', 'get_client_activities');
                Route::post('/get_file_log', 'get_file_info');
            });

        // Appointment
        Route::controller(AppointmentController::class)
            ->middleware('auth:api')
            ->prefix('appointments')
            ->as('appointments.')
            ->group(function () {
                Route::get('/', 'index');
                Route::get('/{id}', 'show');
            });

        // Task
        Route::controller(TaskController::class)
            ->middleware('auth:api')
            ->prefix('task')
            ->as('task.')
            ->group(function () {
                Route::get('/', 'getTasks');
                Route::get('/{id}', 'getTask');
            });

        Route::controller(AssignTaskController::class)
            ->middleware('auth:api')
            ->group(function () {
                Route::get('/get_designers', 'getDesignersWithImages');
                Route::get('/get_designer_details', 'getDesignersWithImagestwo');
            });

        // Others
        Route::controller(TourController::class)
            ->middleware('auth:api')
            ->prefix('tour')
            ->as('tour.')
            ->group(function () {
                Route::post('/', 'store')->name('store');
                Route::post('/restart', 'restart')->name('restart');
            });

        Route::controller(EbookController::class)
            ->middleware('auth:api')
            ->prefix('ebook')
            ->as('ebook.')
            ->group(function () {
                Route::get('/all_ebooks', 'all_ebooks')->name('all_ebooks');
                Route::get('/file-view/{id}', 'file_view')->name('file_view');
            });

        Route::controller(FetchLeadsController::class)
            ->group(function () {
                Route::prefix('webhook')
                    ->group(function () {
                        Route::post('/save-data/{id}', 'save')->name('webhook.save_data');
                    });

                Route::prefix('lead_frequency')
                    ->group(function () {
                        Route::post('/add_lead', 'add_ppc_lead')->name('lead_frequency.add_lead');
                        Route::get('/week_payment', 'week_payment')->name('lead_frequency.week_payment');
                        Route::get('/monthly_payment', 'monthly_payment')->name('lead_frequency.monthly_payment');
                        Route::get('/send_lead_on_discord/{leads_start_time}', 'send_client_leads_on_discord')->name('lead_frequency.send_lead_on_discord');
                        Route::post('/send_que_leads', 'send_que_leads')->name('lead_frequency.send_que_leads');
                        Route::post('/get_lead_form_website', 'get_lead_form_website')->name('lead_frequency.get_lead_form_website');
                    });

                Route::prefix('message_template')
                    ->group(function () {
                        Route::get('/add_message_to_user', 'add_message_to_user')->name('add_message_to_user');
                        Route::get('assign_template_to_clients', 'assign_template_to_clients')->name('assign_template_to_clients');
                    });
            });

        Route::controller(GoogleAdsController::class)
            ->prefix('google_ads')
            ->as('google_ads.')
            ->group(function () {
                Route::get('/campaign', 'google_ads_campaign')->name('campaign');
                Route::get('/ad_group', 'google_ads_ad_group')->name('ad_group');
                Route::get('/ad_group_ad', 'google_ads_ad_group_ad')->name('ad_group_ad');
                Route::get('/conversion_action', 'google_ads_conversion_action')->name('conversion_action');
                Route::get('/geo_target_constant', 'google_ads_geo_target_constant')->name('geo_target_constant');
                Route::get('/customer_id', 'google_ads_customer_id')->name('customer_id');
            });

        Route::controller(GoogleAdsReportController::class)
            ->prefix('google_ads_report')
            ->as('google_ads_report.')
            ->group(function () {
                Route::get('/', 'google_ads_report');
                Route::post('/', 'save_google_report');
                Route::post('/update-act-expiry-date', 'update_act_expiry_date');
                Route::post('/save-campaign-note', 'campaign_note_save');
                Route::delete('/delete-campaign-note/{id}', 'campaign_note_delete');
            });

        Route::controller(PageTemplateController::class)
            ->group(function () {
                Route::get('/get-page-templates', 'get_template');
                Route::post('/get-single-template-detail', 'get_single_templates');
                Route::post('/get-single-page-preview', 'get_page_preview');

                Route::post('/save-page', 'page_save');
                Route::post('/send-template', 'send_template');
            });

        Route::controller(MessageController::class)
            ->prefix('message')
            ->as('message.')
            ->group(function () {
                Route::get('/', 'fetchMessages')->name('index');
                Route::post('/send-message', 'sendMessage');
                Route::get('/message-status', 'getMessageStatus');
                Route::get('/numbers', 'getAllNumbers');
                Route::post('/check-number', 'checkNumber');
            });

        Route::controller(EmailTemplateController::class)
            ->group(function () {
                Route::get('/get-email-templates', 'get_email_template');
                Route::post('/add-email-template', 'add_email_temp');
                Route::post('/edit-email-template', 'edit_email_temp');
                Route::post('/send-email', 'send_email');
                Route::post('/get-single-email-templates', 'get_single_templates');
                Route::post('/delete-email-template', 'delete_email_temp');
            });

        Route::controller(WhatsappTemplateController::class)
            ->prefix('whatsap_message_template')
            ->as('whatsap_message_template.')
            ->group(function () {
                Route::post('/', 'wpMessageUpdate')->name('wp_message_update');
            });

        Route::controller(CalendarController::class)
            ->group(function () {
                Route::prefix('calendar')
                    ->as('calendar.')
                    ->group(function () {
                        Route::get('/', 'index');
                        Route::post('/events', 'storeEvent');
                        Route::get('/events', 'showEvent');
                        Route::put('/events', 'updateEvent');
                        Route::delete('/events', 'destroyEvent');
                    });

                Route::prefix('google-calendar')
                    ->as('google-calendar.')
                    ->group(function () {
                        Route::get('/', 'checkGoogleCalendar');
                        Route::get('/auth-url', 'getAuthUrl');
                        Route::post('/oauth', 'oauth');
                        Route::delete('/disconnect', 'disconnectGoogleCalendar');
                    });
            });

        Route::controller(CloudTalkController::class)
            ->prefix('cloudtalk')
            ->as('cloudtalk.')
            ->group(function () {
                Route::get('/statistics', 'callStatistics')->name('statistics');
            });

        Route::controller(AIChatController::class)
            ->prefix('ai-chat')
            ->as('ai-chat.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/generate-content', 'generateMessage')->name('generate-content');
            });

        Route::controller(AIVoiceChatController::class)
            ->prefix('ai-voice-chat')
            ->as('ai-voice-chat.')
            ->group(function () {
                Route::post('/', 'sendMessage')->name('sendMessage');
            });

        // Request Service
        Route::controller(TelemarketingController::class)
            ->middleware('auth:api')
            ->prefix('telemarketing')
            ->as('telemarketing.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
            });

        Route::controller(SocialMediaMarketingController::class)
            ->middleware('auth:api')
            ->prefix('social-media-marketing')
            ->as('social-media-marketing.')
            ->group(function () {
                Route::get('/platform', 'getPlatforms')->name('platform');
                Route::get('/user-social-media', 'getUserSocialMedia')->name('user-social-media');
                Route::get('/user-access-token', 'checkAccessToken')->name('user-access-token');
                Route::delete('/user-disconnect-platform', 'disconnectPlatform')->name('user-disconnect-platform');
                Route::get('/ads', 'indexAds')->name('ads.index');
                Route::get('/analytics', 'indexAnalytics')->name('analytics.index');
            });

        Route::controller(PostScheduleController::class)
            ->middleware('auth:api')
            ->prefix('post-schedules')
            ->as('post-schedules.')
            ->group(function () {
                Route::get('/', 'indexPostSchedules')->name('index');
                Route::post('/', 'storePostSchedules')->name('store');
                Route::get('/{id}', 'showPostSchedule')->name('show');
                Route::put('/{id}', 'updatePostSchedule')->name('update');
                Route::delete('/{id}', 'destroyPostSchedule')->name('destroy');
            });

        Route::controller(ContentWritingController::class)
            ->middleware('auth:api')
            ->prefix('content-writing')
            ->as('content-writing.')
            ->group(function () {
                Route::post('/generate/from-scratch', 'generateFromScratch')->name('generateFromScratch');
                Route::post('/generate/blog-post', 'generateBlogPost')->name('generateBlogPost');
            });
    });
