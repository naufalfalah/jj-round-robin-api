<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadAssign extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'assign_to',
    ];

    public function lead()
    {
        return $this->belongsTo(LeadClient::class);
    }
}
