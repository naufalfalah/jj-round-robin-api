<?php

namespace App\Models;

use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientCalendarEvent extends Model
{
    use DianujHashidsTrait, HasFactory;

    protected $fillable = [
        'client_id',
        'title',
        'location',
        'description',
        'event_date',
        'start_time',
        'end_time',
        'calendar_event_id',
        'added_by_id',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
