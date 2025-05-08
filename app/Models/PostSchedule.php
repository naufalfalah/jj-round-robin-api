<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'title',
        'description',
        'date',
        'time',
        'published',
        'media_backup',
        'media_remove',
        'linkedin_container_type',
        'linkedin_container_id',
        'linkedin_post_type',
        'linkedin_text',
        'linkedin_media_title',
        'linkedin_media_description',
        'linkedin_link_url',
        'linkedin_image',
        'linkedin_visibility',
        'linkedin_post_id',
        'linkedin_post_media_backup',
        'linkedin_post_media_remove',
        'facebook',
        'facebook_container_type',
        'facebook_container_id',
        'facebook_post_type',
        'facebook_message',
        'facebook_link',
        'facebook_media',
        'facebook_post_id',
        'facebook_post_media_backup',
        'facebook_post_media_remove',
        'instagram',
        'instagram_post_type',
        'instagram_caption',
        'instagram_link',
        'instagram_media',
        'instagram_post_id',
        'instagram_post_media_backup',
        'instagram_post_media_remove',
        'google_business',
        'google_business_summary',
        'google_business_media',
        'google_business_post_id',
        'google_business_post_media_backup',
        'google_business_post_media_remove',
        'youtube',
        'youtube_video',
        'youtube_video_title',
        'youtube_video_description',
        'youtube_privacy_status',
        'youtube_category_id',
        'youtube_tags',
        'youtube_post_id',
        'youtube_post_media_backup',
        'youtube_post_media_remove',
        'tiktok',
        'tiktok_title',
        'tiktok_description',
        'tiktok_privacy_level',
        'tiktok_post_type',
        'tiktok_video',
        'tiktok_content',
        'tiktok_disable_comment',
        'tiktok_disable_duet',
        'tiktok_disable_stitch',
        'tiktok_post_id',
        'tiktok_post_media_backup',
        'tiktok_post_media_remove',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
