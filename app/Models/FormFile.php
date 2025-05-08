<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_folder_id',
        'client_id',
        'form_request_id',
        'form_subtask_id',
        'filename',
        'filetype',
    ];

    public function folder()
    {
        return $this->belongsTo(FormFolder::class, 'form_folder_id', 'id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id', 'id');
    }

    public function formRequest()
    {
        return $this->belongsTo(FormRequest::class, 'form_request_id', 'id');
    }

    public function formSubtask()
    {
        return $this->belongsTo(FormSubtask::class, 'form_subtask_id', 'id');
    }
}
