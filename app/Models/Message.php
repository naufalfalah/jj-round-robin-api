<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DianujHashidsTrait;

class Message extends Model
{
    use HasFactory, DianujHashidsTrait;

    public function user()
    {
        if ($this->user_type == 'admin') {
            return $this->belongsTo(Admin::class, 'user_id');
        }
        return $this->belongsTo(User::class, 'user_id');
    }

}
