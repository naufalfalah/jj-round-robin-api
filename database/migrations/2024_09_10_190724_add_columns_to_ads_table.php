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
        Schema::table('ads', function (Blueprint $table) {
            $table->enum('hosting_is', ['i_have_my_own_hosting', 'request_to_purchase_hosting'])->default('i_have_my_own_hosting');
            $table->text('hosting_details')->nullable();
            $table->integer('is_domain_pay')->nullable()->default(0);
            $table->integer('is_hosting_pay')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ads', function (Blueprint $table) {
            //
        });
    }
};
