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
        Schema::create('client_wallets', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id');
            $table->string('transaction_id', 255)->nullable();
            $table->integer('ads_id')->nullable();
            $table->double('amount_in')->nullable();
            $table->double('amount_out')->nullable();
            $table->enum('topup_type', ['manual', 'online', 'stripe', 'back_to_wallet', 'add_to_subwallet', 'paynow', 'closed_subwallet'])->default('manual');
            $table->double('transaction_fee')->nullable();
            $table->double('currency_conversion')->nullable();
            $table->double('total_amount')->nullable();
            $table->longText('data')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_wallets');
    }
};
