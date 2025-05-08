<?php

namespace App\Models;

use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JunkLead extends Model
{
    protected $table = 'junk_leads';

    protected $fillable = [
        'lead_data',
        'status',
        'created_at',
        'updated_at',
    ];

    use DianujHashidsTrait, HasFactory;
}
