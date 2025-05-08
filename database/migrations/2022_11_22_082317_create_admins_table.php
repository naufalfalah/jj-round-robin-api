<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->text('image')->nullable();
            $table->enum('user_type', ['admin', 'normal'])->default('normal');
            $table->text('user_permissions')->nullable();
            $table->string('password');
            $table->boolean('is_active')->default('1');
            $table->bigInteger('role_id')->nullable();
            $table->string('role_name')->nullable();
            $table->bigInteger('added_by_id')->nullable();
            $table->text('device_token')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admins');
    }
}
