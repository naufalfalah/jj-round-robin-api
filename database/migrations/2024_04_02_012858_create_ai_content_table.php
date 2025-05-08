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
        Schema::create('ai_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('addedby_id')->nullable()->constrained('admins')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('template_id')->nullable()->constrained('prompt_templates')->cascadeOnDelete()->cascadeOnUpdate();
            $table->text('property')->nullable();
            $table->text('generated_content');
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
        Schema::dropIfExists('ai_content');
    }
};
