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
            $table->string('transaction_id', 255)->nullable()->after('client_id');
            $table->longText('data')->nullable()->after('ads_id');
            $table->enum('status', ['processing', 'completed', 'canceled', 'declined'])->default('processing')->after('data');
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
