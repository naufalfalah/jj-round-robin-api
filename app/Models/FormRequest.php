<?php

namespace App\Models;

use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormRequest extends Model
{
    use DianujHashidsTrait, HasFactory;

    protected $fillable = [
        'title',
        'form_group_id',
        'order',
        'slug',
        'icon',
        'priority',
        'description',
        'due_date',
        'start_date',
        'end_date',
        'client_id',
        'project_id',
        'to_team',
    ];

    protected $appends = [
        'subtasks_count',
        'done_subtasks_count',
        'done_subtasks_percentage',
    ];

    public function form_data()
    {
        return $this->hasMany(FormData::class);
    }

    public function assigns()
    {
        return $this->hasMany(FormAssign::class);
    }

    public function formFolders()
    {
        return $this->hasMany(FormFolder::class, 'form_request_id', 'id');
    }

    public function formFiles()
    {
        return $this->hasMany(FormFile::class, 'form_request_id', 'id');
    }

    public function is_task_assign_to_admin()
    {
        return FormAssign::where('form_request_id', $this->id)
            ->where('admin_id', auth()->user()->id)
            ->exists();
    }

    public function clients()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function subtasks()
    {
        return $this->hasMany(FormSubtask::class);
    }

    public function formGroup()
    {
        return $this->belongsTo(FormGroup::class, 'form_group_id', 'id');
    }

    public function activities()
    {
        return $this->hasMany(FormActivity::class);
    }

    public function done_subtasks()
    {
        return $this->subtasks()->where('done', 1);
    }

    public function subtasksCount()
    {
        return $this->subtasks()->count();
    }

    public function pendingSubtasksCount()
    {
        return $this->subtasks->where('done', 0)->count();
    }

    public function doneSubtasksCount()
    {
        return $this->subtasks->where('done', 1)->count();
    }

    public function doneSubtasksPercentage()
    {
        return $this->subtasksCount() ? $this->doneSubtasksCount() / $this->subtasksCount() * 100 : 0;
    }

    public function getSubtasksCountAttribute()
    {
        return $this->subtasksCount();
    }

    public function getDoneSubtasksCountAttribute()
    {
        return $this->doneSubtasksCount();
    }

    public function getDoneSubtasksPercentageAttribute()
    {
        return $this->doneSubtasksPercentage();
    }

    public function chats()
    {
        return $this->hasMany(ChatMessage::class, 'form_request_id', 'id');
    }
}
