<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubAccount extends Model
{
    use HasFactory;

    public function client()
    {
        return $this->hasOne(User::class, 'id', 'client_id');
    }

    public function sub_account()
    {
        return $this->belongsTo(SubAccount::class, 'sub_account_id', 'id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id', 'id');
    }
}
