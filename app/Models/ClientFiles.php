<?php

namespace App\Models;

use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientFiles extends Model
{
    use DianujHashidsTrait, HasFactory;

    protected $fillable = [
        'client_id',
        'file_name',
        'main_folder_id',
        'file_path',
    ];

    public function file_activity()
    {
        return $this->hasMany(TempActivity::class, 'template_id')->where('template_type', 'page')->latest();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
