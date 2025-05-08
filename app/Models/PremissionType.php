<?php

namespace App\Models;

use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PremissionType extends Model
{
    use DianujHashidsTrait, HasFactory;

    protected $table = 'permission_types';

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'permission_type_id', 'id');
    }
}
