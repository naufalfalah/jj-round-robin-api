<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DianujHashidsTrait;

class Permission extends Model
{
    use HasFactory , DianujHashidsTrait;

    /**
     * Get the permission_type that owns the Permission
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function permission_type()
    {
        return $this->belongsTo(PermissionType::class, 'permission_type_id', 'id');
    }
}
