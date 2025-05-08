<?php

namespace App\Models;

use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadActivity extends Model
{
    use DianujHashidsTrait, HasFactory, SoftDeletes;

    protected $fillable = [
        'lead_client_id', 'title', 'description', 'date_time', 'type', 'user_type', 'delete_by_type', 'delete_by_id', 'added_by_id',
    ];

    protected $casts = [
        'last_open' => 'datetime',
    ];

    public function lead()
    {
        return $this->belongsTo(LeadClient::class);
    }

    public function file_lead()
    {
        return $this->belongsTo(LeadClient::class, 'lead_client_id');
    }

    public function attachments()
    {
        return $this->hasMany(LeadActivityAttachments::class, 'activity_id');
    }

    public function client_file()
    {
        return $this->belongsTo(ClientFiles::class, 'file_id', 'id');
    }
}
