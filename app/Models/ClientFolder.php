<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DianujHashidsTrait;

class ClientFolder extends Model
{
    use HasFactory, DianujHashidsTrait;

    public function client_files()
    {
        return $this->hasMany(ClientFile::class, 'folder_id', 'id');
    }
}
