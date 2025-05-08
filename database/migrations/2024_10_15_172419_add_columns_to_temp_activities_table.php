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
        Schema::table('temp_activities', function (Blueprint $table) {
            $table->integer('total_shared')->nullable()->default(null); // Adds total_shared column
            $table->dateTime('last_open')->nullable()->default(null); // Adds last_open column
            $table->integer('total_views')->default(0); // Adds total_views column with a default value of 0
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('temp_activities', function (Blueprint $table) {
            //
        });
    }
};
