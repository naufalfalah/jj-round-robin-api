<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_request_id',
        'admin_id',
        'description',
        'field',
        'target_id',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function target()
    {
        return $this->belongsTo(Admin::class, 'target_id');
    }
}
