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
        Schema::table('ads_invoices', function (Blueprint $table) {
            $table->string('billing_id', 12)->nullable()->after('client_id');
            $table->double('card_charge')->after('total_lead');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ads_invoices', function (Blueprint $table) {
            $table->dropColumn('billing_id');
            $table->dropColumn('card_charge');
        });
    }
};
