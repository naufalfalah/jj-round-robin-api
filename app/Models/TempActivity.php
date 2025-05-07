<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempActivity extends Model
{
    use HasFactory;

    public function lead_client()
    {
        return $this->belongsTo(LeadClient::class, 'client_id', 'id');
    }
}
