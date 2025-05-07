<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\DianujHashidsTrait;

class Transections extends Model
{
    use HasFactory, SoftDeletes, DianujHashidsTrait;

    protected $fillable = [
        'available_balance',
        'client_id',
        'amount_out',
        'status',
        'topup_type',
        'ads_id',
    ];

    public function get_top_up()
    {
        return $this->belongsTo(WalletTopUp::class, 'topup_id', 'id');
    }

    public function get_ads()
    {
        return $this->belongsTo(Ads::class, 'ads_id', 'id');
    }

    public function clients()
    {
        return $this->belongsTo(User::class, 'client_id');
    }


}
