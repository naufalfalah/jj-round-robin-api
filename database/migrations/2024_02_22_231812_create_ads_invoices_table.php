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
        Schema::create('ads_invoices', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id')->nullable();
            $table->date('invoice_date')->nullable();
            $table->double('gst')->nullable();
            $table->double('total_amount')->nullable();
            $table->double('total_lead')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
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
        Schema::dropIfExists('ads_invoices');
    }
};
