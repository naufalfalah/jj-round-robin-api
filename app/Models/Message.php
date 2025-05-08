<?php

namespace App\Models;

use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class message extends Model
{
    use DianujHashidsTrait, HasFactory;

    public function user()
    {
        if ($this->user_type == 'admin') {
            return $this->belongsTo(Admin::class, 'user_id');
        }

        return $this->belongsTo(User::class, 'user_id');
    }
}
