<?php

namespace App\Models;

use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use DianujHashidsTrait , HasFactory;

    /**
     * Get the permission_type that owns the Permission
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function permission_type()
    {
        return $this->belongsTo(PremissionType::class, 'permission_type_id', 'id');
    }
}
