<?php

namespace App\Models;

use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadActivity extends Model
{
    use HasFactory, DianujHashidsTrait, SoftDeletes;

    protected $fillable = [
        'lead_client_id', 'title', 'description', 'date_time','type', 'user_type','delete_by_type','delete_by_id' , 'added_by_id'
    ];

    public function lead()
    {
        return $this->belongsTo(LeadClient::class);
    }
}
