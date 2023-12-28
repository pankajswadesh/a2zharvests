<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShippingTable extends Migration {

	public function up()
	{
		Schema::create('shipping', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index();
			$table->string('name');
			$table->string('email');
			$table->string('phone_no');
			$table->text('address');
			$table->string('latitude');
			$table->string('longitude');
			$table->string('pincode');
			$table->string('landmark');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('shipping');
	}
}