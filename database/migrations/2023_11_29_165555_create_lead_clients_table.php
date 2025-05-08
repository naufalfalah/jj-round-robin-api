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
        Schema::create('lead_clients', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id');
            $table->integer('ads_id')->nullable();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('mobile_number')->nullable();
            $table->text('note')->nullable();
            $table->enum('status', ['new_lead', 'spam', 'junk', 'clear', 'unmarked', 'uncontacted', 'contacted'])->default('unmarked');
            $table->string('follow_up_date_time')->nullable();
            $table->tinyInteger('is_send_discord')->nullable();
            $table->tinyInteger('is_verified')->nullable();
            $table->enum('user_status', ['normal', 'agent'])->default('normal');
            $table->string('registration_no', 255)->nullable();
            $table->integer('added_by_id');
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
        Schema::dropIfExists('lead_clients');
    }
};
