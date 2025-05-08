<?php

namespace App\Models;

use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientFolder extends Model
{
    use DianujHashidsTrait, HasFactory;

    protected $fillable = [
        'client_id',
        'folder_name',
        'parent_folder_id',
    ];

    public function client_files()
    {
        return $this->hasMany(ClientFiles::class, 'folder_id', 'id');
    }

    public function client_main_folder_files()
    {
        return $this->hasMany(ClientFiles::class, 'main_folder_id', 'id');
    }
}
