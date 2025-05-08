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
        Schema::create('wallet_top_ups', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id');
            $table->enum('topup_type', ['manual', 'online'])->default('manual');
            $table->double('topup_amount')->nullable();
            $table->enum('status', ['approve', 'pending', 'rejected', 'canceled'])->default('pending');
            $table->text('proof')->nullable()->comment('Deposit Slip Image');
            $table->enum('added_by', ['admin', 'client'])->nullable();
            $table->longText('data')->nullable();
            $table->integer('added_by_id')->nullable();
            $table->integer('approved_by')->nullable();
            $table->dateTime('approve_at')->nullable();
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
        Schema::dropIfExists('wallet_top_ups');
    }
};
