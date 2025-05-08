<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientLeadFilter extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'lead_filters',
    ];
}
