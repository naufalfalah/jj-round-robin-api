<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormFolder extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_folder_id',
        'client_id',
        'form_request_id',
        'form_subtask_id',
        'name',
    ];

    public function parentForlder()
    {
        return $this->belongsTo(self::class, 'parent_folder_id', 'id');
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

    public function childFolders()
    {
        return $this->hasMany(self::class, 'parent_folder_id', 'id');
    }

    public function formFiles()
    {
        return $this->hasMany(FormFile::class, 'form_folder_id', 'id');
    }

    public function totalFileCount()
    {
        return $this->formFiles->count();
    }
}
