<?php

namespace App\Models;

use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use DianujHashidsTrait, HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'title',
        'body',
        'is_read',
        'lead_url',
        'lead_id',
    ];

    public function user()
    {
        if ($this->user_type == 'user') {
            return $this->belongsTo(User::class, 'user_id');
        }

        return $this->belongsTo(Admin::class, 'user_id');
    }

    public function lead()
    {
        return $this->belongsTo(LeadClient::class, 'lead_id');
    }
}
