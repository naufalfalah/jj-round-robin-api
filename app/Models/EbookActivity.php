<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EbookActivity extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'last_open' => 'datetime',
    ];

    public function ebook()
    {
        return $this->belongsTo(EBook::class, 'ebook_id');
    }
}
