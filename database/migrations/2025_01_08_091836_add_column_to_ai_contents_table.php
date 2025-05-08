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
        Schema::table('ai_contents', function (Blueprint $table) {
            $table->text('converted_content')->after('generated_content');
            $table->string('audio_path')->after('converted_content');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ai_contents', function (Blueprint $table) {
            $table->dropColumn('converted_content');
            $table->dropColumn('audio_path');
        });
    }
};
