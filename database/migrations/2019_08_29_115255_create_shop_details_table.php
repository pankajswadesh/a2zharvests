<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShopDetailsTable extends Migration {

	public function up()
	{
		Schema::create('shop_details', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index();
			$table->string('business_name')->nullable();
			$table->string('business_id')->nullable();
			$table->string('gst_no')->nullable();
			$table->string('fsssi_no')->nullable();
			$table->string('start_time')->nullable();
			$table->string('end_time')->nullable();
			$table->string('alt_phone_no')->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('shop_details');
	}
}