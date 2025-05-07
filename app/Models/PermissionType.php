<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DianujHashidsTrait;

class PermissionType extends Model
{
    use HasFactory, DianujHashidsTrait;

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'permission_type_id', 'id');
    }
}
