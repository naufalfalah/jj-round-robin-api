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
        Schema::table('transections', function (Blueprint $table) {
            $table->integer('to_wallet_id')->after('status')->nullable();
            $table->integer('form_wallet_id')->after('to_wallet_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transections', function (Blueprint $table) {
            //
        });
    }
};
