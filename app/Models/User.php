<?php

namespace App\Models;

use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use DianujHashidsTrait, HasApiTokens, HasFactory, SoftDeletes;

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
        'calendar_id',
        'provider',
        'provider_id',
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
        return ucwords($this->agency.'-'.$this->client_name);
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

    public function sub_accounts()
    {
        return $this->belongsToMany(UserSubAccount::class, 'user_id', 'sub_account_id');
    }

    public function google_account()
    {
        return $this->hasOne(GoogleAccount::class, 'id');
    }

    // public function leads()
    // {
    //     return $this->hasMany(LeadClient::class, 'client_id', 'id');
    // }

    public function leads()
    {
        return $this->hasMany(LeadClient::class, 'client_id', 'id'); // 'client_id' in LeadClient matches 'id' in User
    }

    public function getAccessibleMenus()
    {
        return $this->userSubAccounts()
            ->with('package.menus')
            ->get()
            ->flatMap(function ($userSubAccount) {
                return $userSubAccount->package->getMenus() ?? [];
            })
            ->unique()
            ->values()
            ->toArray();
    }

    public function userSubAccounts()
    {
        return $this->hasMany(UserSubAccount::class, 'client_id');
    }

    public function socialMedias()
    {
        return $this->hasMany(UserSocialMedia::class, 'client_id', 'id');
    }

    public function getSocialMediaByProvider($provider)
    {
        return $this->socialMedias()->where('provider', $provider)->first();
    }
}
