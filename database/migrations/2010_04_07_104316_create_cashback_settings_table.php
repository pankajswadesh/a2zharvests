<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCashbackSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cashback_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->double('min_amount',[8,2]);
            $table->double('cashback_percent',[8,2]);
            $table->enum('status', array('Active', 'Inactive'))->default('Active');
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
        Schema::dropIfExists('cashback_settings');
    }
}
