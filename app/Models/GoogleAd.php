<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleAd extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'ad_request_id',
        'campaign_budget_resource_name',

        'campaign_name',
        'campaign_type',
        'campaign_target_url',
        'campaign_budget_type',
        'campaign_budget_amount',
        'campaign_start_date',
        'campaign_end_date',
        'campaign_resource_name',

        'locations',
        'languages',

        'ad_group_name',
        'ad_group_bid_amount',
        'ad_group_resource_name',

        'keywords',
        'keyword_match_types',

        'ad_name',
        'ad_final_url',
        'ad_headlines',
        'ad_descriptions',
        'ad_sitelinks',
        'ad_callouts',
        'ad_resource_name',

        'google_account_id',
        'customer_id',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function ad()
    {
        return $this->belongsTo(Ads::class, 'ad_request_id');
    }
}
