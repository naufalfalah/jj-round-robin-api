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
        Schema::create('page_templates', function (Blueprint $table) {
            $table->id(); // This creates an auto-incrementing big integer (id) column
            $table->unsignedBigInteger('client_id'); // This creates an unsigned big integer column for client_id
            $table->string('title', 225); // This creates a varchar(225) column for title
            $table->text('description')->nullable(); // This creates a text column for description
            $table->text('private_note')->nullable(); // This creates a text column for private_note
            $table->string('google_maps', 191)->nullable(); // This creates a varchar(191) column for google_maps
            $table->text('cover_image')->nullable(); // This creates a text column for cover_image
            $table->timestamps(); // This creates created_at and updated_at columns
            $table->softDeletes(); // This creates a deleted_at column for soft deletes
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('page_templates');
    }
};
