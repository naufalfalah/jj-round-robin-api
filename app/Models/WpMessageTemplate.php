<?php

namespace App\Models;

use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WpMessageTemplate extends Model
{
    use DianujHashidsTrait, HasFactory;

    protected $fillable = [
        'id',
        'wp_message',
        'from_number',
        'added_by_id',
        'status',
    ];
}
