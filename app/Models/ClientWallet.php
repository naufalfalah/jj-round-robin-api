<?php

namespace App\Models;

use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientWallet extends Model
{
    use DianujHashidsTrait, HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $fillable = [
        'status',
    ];

    public function ads()
    {
        return $this->belongsTo(Ads::class, 'ads_id');
    }
}
