<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DianujHashidsTrait;

class WpMessageTemplate extends Model
{
    use HasFactory, DianujHashidsTrait;

    protected $fillable = [
        'id',
        'wp_message',
        'from_number',
        'added_by_id',
        'status',
    ];
}
