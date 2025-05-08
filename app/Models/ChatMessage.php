<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_request_id',
        'user_type',
        'user_id',
        'message',
        'read_at',
        'attachments',
        'voice_note',
        'to_user_type',
        'to_user_id',
    ];

    public function formRequest()
    {
        return $this->belongsTo(FormRequest::class, 'form_request_id', 'id');
    }

    public function user()
    {
        return $this->morphTo('user');
    }

    public function toUser()
    {
        return $this->morphTo('to_user');
    }
}
