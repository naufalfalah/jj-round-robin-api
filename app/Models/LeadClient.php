<?php

namespace App\Models;

use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadClient extends Model
{
    use DianujHashidsTrait, HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'name',
        'email',
        'mobile_number',
        'user_type',
        'added_by_id',
    ];

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
                'added_by_id' => $leadClient->added_by_id,
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
        return $this->belongsTo(User::class, 'client_id', 'id'); // 'client_id' in LeadClient matches 'id' in User
    }

    public function activites()
    {
        return $this->hasMany(LeadClient::class, 'lead_id', 'id');
    }

    public function assign()
    {
        return $this->hasOne(LeadAssign::class, 'lead_id', 'id');
    }

    public function lead_source()
    {
        return $this->belongsTo(LeadSource::class, 'source_type_id', 'id');
    }

    public function ads()
    {
        return $this->belongsTo(Ads::class, 'ads_id', 'id');
    }
}
