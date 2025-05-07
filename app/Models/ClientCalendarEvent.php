<?php

namespace App\Models;

use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientCalendarEvent extends Model
{
    use HasFactory, DianujHashidsTrait;

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
