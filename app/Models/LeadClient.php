<?php

namespace App\Models;

use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadClient extends Model
{
    use HasFactory, DianujHashidsTrait, SoftDeletes;

    protected static function boot()
    {
        parent::boot();

        static::created(function ($leadClient) {
            LeadActivity::create([
                'lead_client_id' => $leadClient->id,
                'title' => 'Client added to '.config('app.name'),
                'description' => '',
                'date_time' => now()->format('Y-m-d h:i'),
                'type' => 'add',
                'user_type' => $leadClient->user_type,
                'added_by_id' => $leadClient->added_by_id
            ]);
        });
    }

    public function activity()
    {
        return $this->hasMany(LeadActivity::class);
    }

    public function lead_data()
    {
        return $this->hasMany(LeadData::class);
    }

    public function lead_groups()
    {
        return $this->hasMany(LeadGroup::class, 'lead_id');
    }

    public function clients()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
    public function ads()
    {
        return $this->belongsTo(Ads::class, 'ads_id');
    }
}
