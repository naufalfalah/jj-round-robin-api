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
        Schema::table('users', function (Blueprint $table) {
            $table->text('linkedin_access_token')->nullable();
            $table->text('facebook_access_token')->nullable();
            $table->text('google_ads_access_token')->nullable();
            $table->text('google_ads_refresh_token')->nullable();
            $table->text('google_business_access_token')->nullable();
            $table->text('google_business_refresh_token')->nullable();
            $table->text('youtube_access_token')->nullable();
            $table->text('youtube_refresh_token')->nullable();
            $table->text('tiktok_access_token')->nullable();
            $table->text('tiktok_ads_access_token')->nullable();
            $table->text('xiao_hong_shu_access_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('linkedin_access_token');
            $table->dropColumn('facebook_access_token');
            $table->dropColumn('youtube_access_token');
            $table->dropColumn('youtube_refresh_token');
            $table->dropColumn('tiktok_access_token');
            $table->dropColumn('tiktok_ads_access_token');
        });
    }
};
