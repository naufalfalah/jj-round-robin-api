<?php

namespace App\Models;

use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory, DianujHashidsTrait;

    protected $guarded = [];

    public function user()
    {
        if ($this->user_type == 'user') {
            return $this->belongsTo(User::class, 'user_id');
        }
        return $this->belongsTo(Admin::class, 'user_id');
    }
}
