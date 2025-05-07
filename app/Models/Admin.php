<?php

namespace App\Models;

use App\Permissions\HasPermissionsTrait;
use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use DianujHashidsTrait, HasPermissionsTrait;

    protected $guard = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'first_name', 'last_name','image', 'user_type', 'google_access_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = ['user_permissions' => 'object'];

    public function getFullNameAttribute()
    {
        return ucwords($this->name);
    }

    public function getIsAdminAttribute()
    {
        return $this->user_type == 'admin';
    }

    public function scopeNotifiableAdmins()
    {
        return $this->where('user_type', 'admin')->get();
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
