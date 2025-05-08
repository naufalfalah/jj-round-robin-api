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
        Schema::create('facebook_reports', function (Blueprint $table) {
            $table->id();
            $table->string('act_id', 255)->nullable();
            $table->text('campaigns')->nullable();
            $table->text('adsets_daily_budget')->nullable();
            $table->text('adsets')->nullable();
            $table->text('ads')->nullable();
            $table->text('country_detail')->nullable();
            $table->text('summary_detail')->nullable();
            $table->date('start_date');
            $table->date('end_date');
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
        Schema::dropIfExists('facebook_reports');
    }
};
