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
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id');
            $table->string('adds_title', 191);
            $table->text('description')->nullable();
            $table->string('email', 191);
            $table->string('discord_link', 191)->nullable();
            $table->string('domain_name')->nullable();
            $table->string('website_url')->nullable();
            $table->text('type')->nullable();
            $table->enum('status', ['pending', 'running', 'pause', 'reject', 'complete', 'close'])->default('pending');
            $table->boolean('lead_status')->default(false);
            $table->double('spend_amount')->nullable();
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
        Schema::dropIfExists('ads');
    }
};
