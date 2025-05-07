<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class WalletTopUp extends Model
{
    use HasFactory, DianujHashidsTrait, SoftDeletes;

    public function clients()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
