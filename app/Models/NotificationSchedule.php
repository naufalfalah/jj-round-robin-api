<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'notification_id',
        'published_to',
        'user_id',
        'published_at',
        'is_published',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
