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
            $table->dropColumn('agency');
            $table->integer('agency_id')->after('phone_number');
            $table->integer('industry_id')->nullable()->after('agency_id');
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
            $table->string('agency'); // Add the data type if necessary
            $table->dropColumn('agency_id');
            $table->dropColumn('industry_id');
        });
    }
};
