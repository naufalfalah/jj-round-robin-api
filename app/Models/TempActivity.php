<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id',
    ];

    public function lead_client()
    {
        return $this->belongsTo(LeadClient::class, 'client_id', 'id');
    }

    public function page()
    {
        return $this->belongsTo(PageTemplate::class, 'template_id', 'id');
    }
}
