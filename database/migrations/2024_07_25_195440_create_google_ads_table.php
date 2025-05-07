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
        Schema::create('google_ads', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id');
            $table->integer('ad_request_id');
            $table->string('campaign_budget_resource_name')->nullable();
            
            $table->string('campaign_name');
            $table->string('campaign_type');
            $table->string('campaign_target_url')->nullable();
            $table->string('campaign_budget_type')->nullable();
            $table->string('campaign_budget_amount')->nullable();
            $table->string('campaign_start_date')->nullable();
            $table->string('campaign_end_date')->nullable();
            $table->string('campaign_resource_name')->nullable();

            $table->string('ad_group_name');
            $table->string('ad_group_bid_amount')->nullable();
            $table->string('ad_group_resource_name')->nullable();

            $table->text('keywords')->nullable()->nullable();
            $table->string('keyword_match_types')->nullable();

            $table->string('ad_name')->nullable();
            $table->string('ad_final_url')->nullable();
            $table->text('ad_headlines')->nullable();
            $table->text('ad_descriptions')->nullable();
            $table->text('ad_sitelinks')->nullable();
            $table->string('ad_resource_name')->nullable();
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
        Schema::dropIfExists('google_ads');
    }
};
