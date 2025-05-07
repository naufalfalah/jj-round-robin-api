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
        Schema::table('google_ads_accounts', function (Blueprint $table) {
            $table->string('account_id')->nullable()->after('account_title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('google_ads_accounts', function (Blueprint $table) {
            $table->dropColumn('account_id');
        });
    }
};
