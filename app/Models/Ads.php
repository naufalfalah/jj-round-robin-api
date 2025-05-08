<?php

namespace App\Models;

use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ads extends Model
{
    use DianujHashidsTrait, HasFactory, SoftDeletes;

    protected $table = 'ads';

    protected $fillable = [
        'client_id',
        'adds_title',
        'discord_link',
        'type',
        'status',
        'lead_status',
        'daily_budget',
        'spend_amount',
        'e_wallet',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function google_ad()
    {
        return $this->hasOne(GoogleAd::class, 'ad_request_id');
    }
}
