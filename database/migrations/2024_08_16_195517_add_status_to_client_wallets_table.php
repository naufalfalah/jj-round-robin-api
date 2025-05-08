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
        Schema::table('client_wallets', function (Blueprint $table) {
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
        Schema::table('client_wallets', function (Blueprint $table) {
            //
        });
    }
};
