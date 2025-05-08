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
        Schema::table('facebook_reports', function (Blueprint $table) {
            $table->text('summary_graph')->nullable()->after('country_detail');
            $table->text('gender_graph_data')->nullable()->after('summary_graph');
            $table->text('age_graph_data')->nullable()->after('gender_graph_data');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('facebook_reports', function (Blueprint $table) {
            //
        });
    }
};
