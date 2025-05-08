<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormGroup extends Model
{
    use HasFactory;

    public function form_requests()
    {
        return $this->hasMany(FormRequest::class)
            ->orderBy('order', 'asc')
            ->where('to_team', auth('admin')->user()->team);
    }
}
