<?php

namespace App\Models;

use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PageWebsiteLink extends Model
{
    use DianujHashidsTrait, HasFactory, SoftDeletes;

    public function page()
    {
        return $this->belongsTo(PageTemplate::class, 'page_id', 'id');
    }
}
