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
        Schema::create('form_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_folder_id')->constrained('form_folders')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('form_request_id')->nullable()->constrained('form_requests')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('form_subtask_id')->nullable()->constrained('form_subtasks')->nullOnDelete()->cascadeOnUpdate();
            $table->string('filename');
            $table->string('filetype')->nullable();
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
        Schema::dropIfExists('form_files');
    }
};
