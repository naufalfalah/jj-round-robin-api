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
        Schema::create('wp_message_templates', function (Blueprint $table) {
            $table->id();
            $table->text('wp_message');
            $table->string('from_number')->nullable();
            $table->integer('added_by_id');
            $table->enum('status', ['Enable', 'Disable'])->default('Enable');
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
        Schema::dropIfExists('wp_message_templates');
    }
};
