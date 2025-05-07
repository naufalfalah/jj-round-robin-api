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
        Schema::create('client_files', function (Blueprint $table) {
            $table->id();
            $table->integer('folder_id')->nullable();
            $table->integer('main_folder_id')->nullable();
            $table->integer('client_id')->nullable();
            $table->string('file_name', 191);
            $table->string('file_path', 191);
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
        Schema::dropIfExists('client_files');
    }
};
