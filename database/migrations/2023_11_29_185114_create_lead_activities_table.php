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
            $table->integer('lead_client_id');
            $table->text('title');
            $table->text('description')->nullable();
            $table->string('date_time')->nullable();
            $table->enum('type',['add','phone','message','meeting','note','email'])->default('add')->nullable();
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
        Schema::dropIfExists('lead_activities');
    }
};
