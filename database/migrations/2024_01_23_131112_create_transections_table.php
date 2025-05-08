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
        Schema::create('transections', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id');
            $table->double('amount_in')->nullable();
            $table->double('amount_out')->nullable();
            $table->integer('topup_id')->nullable();
            $table->enum('topup_type', ['add_from_main_wallet', 'back_to_main_wallet', 'invoice_payment', 'stripe', 'paynow', 'transfer_to_subwallet', 'transfer_from_subwallet', 'hosting_payment', 'domain_payment', 'closed_and_back_to_main_wallet'])->default('add_from_main_wallet');
            $table->integer('ads_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transections');
    }
};
