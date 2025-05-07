<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, DianujHashidsTrait, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_name',
        'phone_number',
        'agency_id',
        'industry_id',
        'package',
        'image',
        'email',
        'address',
        'email_verified_at',
        'password',
        'google_account_id',
        'customer_id',
    ];

    
    public function Agencices()
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }

    public function Industries()
    {
        return $this->belongsTo(Industry::class, 'industry_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getFullNameAttribute()
    {
        return ucwords($this->agency . '-' . $this->client_name);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function user_industry()
    {
        return $this->belongsTo(Industry::class, 'industry_id', 'id');
    }

    public function user_agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id', 'id');
    }

    public function sub_account()
    {
        return $this->belongsTo(SubAccount::class, 'sub_account_id', 'id');
    }

    public function google_account()
    {
        return $this->hasOne(GoogleAccount::class, 'id');
    }
}
