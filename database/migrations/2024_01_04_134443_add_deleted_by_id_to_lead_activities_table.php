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
            $table->enum('user_type', ['admin', 'user'])->nullable()->default('user')->after('added_by_id');
            $table->enum('delete_by_type', ['user', 'admin'])->nullable()->after('user_type');
            $table->integer('delete_by_id')->nullable()->after('delete_by_type');
            $table->softDeletes()->after('delete_by_id');
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
