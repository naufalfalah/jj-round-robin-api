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
        Schema::create('pie_chart_data', function (Blueprint $table) {
            $table->id();
            $table->string('act_id', 255)->nullable();
            $table->string('clicks', 255);
            $table->string('impressions', 255);
            $table->string('ctr', 255);
            $table->string('cpc', 255);
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
        Schema::dropIfExists('pie_chart_data');
    }
};
