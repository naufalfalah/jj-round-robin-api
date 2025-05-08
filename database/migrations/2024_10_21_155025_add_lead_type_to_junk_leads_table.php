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
        Schema::table('junk_leads', function (Blueprint $table) {
            $table->enum('lead_type', ['pcc', 'webhook'])->default('pcc')->after('sub_account_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('junk_leads', function (Blueprint $table) {
            //
        });
    }
};
