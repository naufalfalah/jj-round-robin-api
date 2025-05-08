<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormSubtask extends Model
{
    use HasFactory;

    protected $fillable = [
        'done',
        'priority',
        'admin_id',
        'due_date',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function formFolder()
    {
        return $this->hasMany(FormFolder::class, 'form_subtask_id', 'id');
    }

    public function formFile()
    {
        return $this->hasMany(FormFile::class, 'form_subtask_id', 'id');
    }
}
