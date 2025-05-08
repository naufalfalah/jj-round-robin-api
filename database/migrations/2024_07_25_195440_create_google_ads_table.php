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
            $table->string('campaign_target_url');
            $table->string('campaign_budget_type');
            $table->string('campaign_budget_amount');
            $table->string('campaign_start_date');
            $table->string('campaign_end_date');
            $table->string('campaign_resource_name')->nullable();

            $table->string('ad_group_name');
            $table->string('ad_group_bid_amount');
            $table->string('ad_group_resource_name')->nullable();

            $table->text('keywords');
            $table->string('keyword_match_types');

            $table->string('ad_name');
            $table->string('ad_final_url');
            $table->text('ad_headlines');
            $table->text('ad_descriptions');
            $table->text('ad_sitelinks');
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
