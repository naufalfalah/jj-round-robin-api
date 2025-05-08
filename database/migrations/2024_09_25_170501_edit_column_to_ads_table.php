<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `ads` MODIFY `status` ENUM('pending', 'running', 'pause', 'reject', 'complete', 'inactive', 'test') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `ads` MODIFY `status` ENUM('pending', 'running', 'pause', 'reject', 'complete', 'inactive') NOT NULL DEFAULT 'pending'");
    }
};
