<?php

namespace App\Models;

use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubAccount extends Model
{
    use DianujHashidsTrait, HasFactory;

    protected $fillable = [
        'sub_account_name',
        'sub_account_url',
    ];
}
