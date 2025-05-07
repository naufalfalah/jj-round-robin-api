<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DianujHashidsTrait;

class MessageTemplate extends Model
{
    use HasFactory, DianujHashidsTrait;

    public function message_activity()
    {
        return $this->hasMany(TempActivity::class, 'template_id')->where('template_type', 'message')->latest();
    }

}
