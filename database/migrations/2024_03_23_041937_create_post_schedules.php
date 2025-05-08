<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_schedules', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id');
            $table->string('title');
            $table->text('description');
            $table->date('date');
            $table->time('time');
            $table->boolean('published')->default(0);
            $table->boolean('media_backup')->default(0);
            $table->boolean('media_remove')->default(0);

            $table->boolean('linkedin')->nullable();
            $table->string('linkedin_container_type')->nullable();
            $table->string('linkedin_container_id')->nullable();
            $table->string('linkedin_post_type')->nullable();
            $table->text('linkedin_text')->nullable();
            $table->string('linkedin_media_title')->nullable();
            $table->string('linkedin_media_description')->nullable();
            $table->string('linkedin_link_url')->nullable();
            $table->string('linkedin_media')->nullable();
            $table->string('linkedin_image')->nullable();
            $table->string('linkedin_visibility')->nullable();
            $table->string('linkedin_post_id')->nullable();
            $table->string('linkedin_post_media_backup')->nullable();
            $table->string('linkedin_post_media_remove')->default(0);

            $table->boolean('facebook')->nullable();
            $table->string('facebook_container_type')->nullable();
            $table->string('facebook_container_id')->nullable();
            $table->string('facebook_post_type')->nullable();
            $table->text('facebook_message')->nullable();
            $table->text('facebook_link')->nullable();
            $table->string('facebook_media')->nullable();
            $table->string('facebook_post_id')->nullable();
            $table->string('facebook_post_media_backup')->nullable();
            $table->string('facebook_post_media_remove')->default(0);

            $table->boolean('instagram')->nullable();
            $table->string('instagram_post_type')->nullable();
            $table->text('instagram_caption')->nullable();
            $table->text('instagram_link')->nullable();
            $table->string('instagram_post_id')->nullable();
            $table->string('instagram_post_media_backup')->nullable();
            $table->string('instagram_post_media_remove')->default(0);

            $table->boolean('google_business')->nullable();
            $table->text('google_business_summary')->nullable();
            $table->text('google_business_media')->nullable();
            $table->string('google_business_post_id')->nullable();
            $table->string('google_business_post_media_backup')->nullable();
            $table->string('google_business_post_media_remove')->default(0);

            $table->boolean('youtube')->nullable();
            $table->text('youtube_video')->nullable();
            $table->text('youtube_video_title')->nullable();
            $table->text('youtube_video_description')->nullable();
            $table->string('youtube_privacy_status')->nullable();
            $table->string('youtube_category_id')->nullable();
            $table->string('youtube_tags')->nullable();
            $table->string('youtube_post_id')->nullable();
            $table->string('youtube_post_media_backup')->nullable();
            $table->string('youtube_post_media_remove')->default(0);

            $table->boolean('tiktok')->nullable();
            $table->string('tiktok_title')->nullable();
            $table->string('tiktok_privacy_level')->nullable();
            $table->string('tiktok_post_type')->nullable();
            $table->string('tiktok_video')->nullable();
            $table->text('tiktok_content')->nullable();
            $table->text('tiktok_description')->nullable();
            $table->boolean('tiktok_disable_duet')->nullable();
            $table->boolean('tiktok_disable_comment')->nullable();
            $table->boolean('tiktok_disable_stitch')->nullable();
            $table->string('tiktok_post_id')->nullable();
            $table->string('tiktok_post_media_backup')->nullable();
            $table->string('tiktok_post_media_remove')->default(0);

            $table->boolean('xiao_hong_shu')->nullable();
            $table->string('xiao_hong_shu_post_id')->nullable();
            $table->string('xiao_hong_shu_post_media_backup')->nullable();
            $table->string('xiao_hong_shu_post_media_remove')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_schedules');
    }
};
