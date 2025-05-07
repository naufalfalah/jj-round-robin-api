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
        Schema::table('google_reports', function (Blueprint $table) {
            $table->string('act_id')->nullable();
            $table->text('campaign')->nullable();
            $table->text('ads_group')->nullable();
            $table->text('keywords')->nullable();
            $table->text('ads')->nullable();
            $table->text('summary_graph_data')->nullable();
            $table->text('performance_graph_data')->nullable();
            $table->text('performance_device')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->datetime('last_update')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('google_reports', function (Blueprint $table) {
            $table->dropColumn('act_id');
            $table->dropColumn('campaign');
            $table->dropColumn('ads_group');
            $table->dropColumn('keywords');
            $table->dropColumn('ads');
            $table->dropColumn('summary_graph_data');
            $table->dropColumn('performance_graph_data');
            $table->dropColumn('performance_device');
            $table->dropColumn('start_date');
            $table->dropColumn('end_date');
            $table->dropColumn('last_update');
        });
    }
};
