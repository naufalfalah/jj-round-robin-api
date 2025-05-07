<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'google_id',
        'access_token',
        'error_sent',
    ];
}
