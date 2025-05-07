<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleAdsConversionAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'name',
        'type',
        'category',
        'website_url',
        'value',
        'counting_type',
        'click_through_days',
        'view_through_days',
        'resource_name',
    ];
}
