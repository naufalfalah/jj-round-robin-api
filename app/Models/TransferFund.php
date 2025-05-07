<?php

namespace App\Models;

use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransferFund extends Model
{
    use HasFactory, SoftDeletes, DianujHashidsTrait;

    public function form_wallet()
    {
        return $this->belongsTo(Ads::class, 'from_wallet_id');
    }

    public function to_wallet()
    {
        return $this->belongsTo(Ads::class, 'to_wallet_id');
    }
}
