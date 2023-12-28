<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_name');
            $table->string('api_token')->unique();
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('location');
            $table->string('password');
            $table->string('latitude');
            $table->string('longitude');
            $table->string('image_url')->default('avatar.png');
            $table->enum('status', array('Active', 'Inactive','Deleted'))->default('Active')->index();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
