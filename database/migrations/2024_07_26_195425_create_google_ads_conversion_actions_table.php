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
        Schema::create('google_ads_conversion_actions', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id');
            $table->string('name');
            $table->string('type');
            $table->string('category');
            $table->string('value');
            $table->string('counting_type');
            $table->integer('click_through_days');
            $table->integer('view_through_days');
            $table->string('resource_name');
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
        Schema::dropIfExists('google_ads_conversion_actions');
    }
};
