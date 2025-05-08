<?php

namespace App\Models;

use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PageTemplateGallery extends Model
{
    use DianujHashidsTrait, HasFactory,SoftDeletes;

    protected $table = 'page_template_galleries';

    protected $guarded = [];

    public function pageTemplate()
    {
        return $this->belongsTo(PageTemplate::class, 'page_id');
    }
}
