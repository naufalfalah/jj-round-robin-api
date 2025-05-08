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
        Schema::create('form_folders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_folder_id')->nullable();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('form_request_id')->nullable()->constrained('form_requests')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('form_subtask_id')->nullable()->constrained('form_subtasks')->nullOnDelete()->cascadeOnUpdate();
            $table->string('name');
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
        Schema::dropIfExists('form_folders');
    }
};
