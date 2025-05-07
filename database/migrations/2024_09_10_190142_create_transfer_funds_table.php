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
        Schema::create('transfer_funds', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id');
            $table->integer('from_wallet_id')->nullable();
            $table->integer('to_wallet_id')->nullable();
            $table->double('amount')->nullable();
            $table->enum('status', ['processing', 'approved'])->default('approved');
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
        Schema::dropIfExists('transfer_funds');
    }
};
