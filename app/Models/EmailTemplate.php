<?php

namespace App\Models;

use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use DianujHashidsTrait, HasFactory;

    public function message_activity()
    {
        return $this->hasMany(TempActivity::class, 'template_id')->where('template_type', 'email')->latest();
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
