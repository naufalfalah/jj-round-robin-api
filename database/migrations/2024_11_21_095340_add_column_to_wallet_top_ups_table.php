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
        Schema::table('wallet_top_ups', function (Blueprint $table) {
            $table->integer('sub_account_id')->after('client_id');
            $table->integer('user_sub_account_id')->after('sub_account_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wallet_top_ups', function (Blueprint $table) {
            $table->dropColumn('sub_account_id');
            $table->dropColumn('user_sub_account_id');
        });
    }
};
