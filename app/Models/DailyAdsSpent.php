<?php

namespace App\Models;

use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyAdsSpent extends Model
{
    use DianujHashidsTrait, HasFactory;

    protected $fillable = [
        'ads_id',
        'amount',
        'date',
        'added_by_id',
    ];

    public function ads()
    {
        return $this->belongsTo(Ads::class, 'ads_id');
    }
}
