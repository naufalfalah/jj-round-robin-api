<?php

namespace App\Models;

use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EBook extends Model
{
    use DianujHashidsTrait, HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'image',
        'pdf',
    ];

    protected $appends = ['shareable_url'];

    public function getShareableUrlAttribute()
    {
        $url = route('ebook_file_view', ['app', $this->hashid]);

        return $url;
    }
}
