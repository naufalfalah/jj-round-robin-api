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
        Schema::table('lead_activities', function (Blueprint $table) {
            $table->integer('file_id')->nullable()->after('email_template_id');
            $table->dateTime('last_open')->nullable()->after('file_id');
            $table->string('total_views', 191)->default(0)->after('last_open');
            $table->text('activity_url')->nullable()->after('total_views');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lead_activities', function (Blueprint $table) {
            //
        });
    }
};
