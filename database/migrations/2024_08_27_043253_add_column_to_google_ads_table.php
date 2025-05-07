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
        Schema::table('google_ads', function (Blueprint $table) {
            $table->text('campaign_json')->after('campaign_resource_name')->nullable();
            $table->text('ad_group_json')->after('ad_group_resource_name')->nullable();
            $table->text('ad_json')->after('ad_resource_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('google_ads', function (Blueprint $table) {
            $table->dropColumn('campaign_json');
            $table->dropColumn('ad_group_json');
            $table->dropColumn('ad_json');
        });
    }
};
