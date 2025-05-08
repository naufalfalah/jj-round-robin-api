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
        Schema::create('lead_activities', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('page_id')->nullable();
            $table->integer('lead_client_id');
            $table->text('title');
            $table->text('description')->nullable();
            $table->dateTime('last_open')->nullable();
            $table->string('date_time')->nullable();
            $table->enum('type', ['add', 'phone', 'message', 'meeting', 'note', 'email', 'attachment', 'file'])->default('add')->nullable();
            $table->integer('added_by_id');
            $table->text('activity_url')->nullable();
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
        Schema::dropIfExists('lead_activities');
    }
};
