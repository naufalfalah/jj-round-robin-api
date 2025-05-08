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
        Schema::create('ebook_activities', function (Blueprint $table) {
            $table->id();
            $table->integer('ebook_id');
            $table->string('ip_address');
            $table->string('date_time');
            $table->dateTime('last_open')->nullable();
            $table->string('total_views');
            $table->enum('activity_route', ['web', 'app'])->default('web')->nullable();
            $table->timestamps();
            $table->SoftDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ebook_activities');
    }
};
