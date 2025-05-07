<?php

namespace App\Models;

use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormRequest extends Model
{
    use HasFactory, DianujHashidsTrait;

    public function form_data()
    {
        return $this->hasMany(FormData::class);
    }

    public function clients()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
