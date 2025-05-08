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
        Schema::create('page_website_links', function (Blueprint $table) {
            $table->id(); // Creates an auto-incrementing bigint (id) column
            $table->unsignedBigInteger('page_id'); // Creates an unsigned bigint column for page_id
            $table->string('link_title', 191); // Creates a varchar(191) column for link_title
            $table->string('website_link', 191); // Creates a varchar(191) column for website_link
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
        Schema::dropIfExists('page_website_links');
    }
};
