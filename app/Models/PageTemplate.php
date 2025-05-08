<?php

namespace App\Models;

use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PageTemplate extends Model
{
    use DianujHashidsTrait, HasFactory, SoftDeletes;

    protected $table = 'page_templates';

    protected $guarded = [];

    public function galleries()
    {
        return $this->hasMany(PageTemplateGallery::class, 'page_id');
    }

    public function page_website_links()
    {
        return $this->hasMany(PageWebsiteLink::class, 'page_id');
    }

    public function page_youtube_links()
    {
        return $this->hasMany(PageYoutubeLink::class, 'page_id');
    }

    public function page_activity()
    {
        return $this->hasMany(TempActivity::class, 'template_id')->where('template_type', 'page')->latest();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'client_id', 'id');
    }

    public function page_lead_activity()
    {
        return $this->hasMany(LeadActivity::class, 'page_id');
    }
}
