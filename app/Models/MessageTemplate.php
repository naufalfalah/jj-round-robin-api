<?php

namespace App\Models;

use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageTemplate extends Model
{
    use DianujHashidsTrait, HasFactory;

    protected $fillable = [
        'client_id',
        'title',
        'description',
        'private_note',
    ];

    public function message_activity()
    {
        return $this->hasMany(TempActivity::class, 'template_id')->where('template_type', 'message')->latest();
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
