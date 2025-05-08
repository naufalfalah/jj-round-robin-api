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
        Schema::create('page_template_galleries', function (Blueprint $table) {
            $table->id(); // Creates an auto-incrementing bigint (id) column
            $table->unsignedBigInteger('page_id'); // Creates an unsigned bigint column for page_id
            $table->string('title', 191)->nullable(); // Creates a varchar(191) column for title, allowing null
            $table->string('images', 191)->nullable(); // Creates a varchar(191) column for images, allowing null
            $table->timestamps(); // Creates created_at and updated_at columns
            $table->softDeletes(); // Creates deleted_at column for soft deletes
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('page_template_galleries');
    }
};
