<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDiscountsTable extends Migration {

	public function up()
	{
		Schema::create('discounts', function(Blueprint $table) {
			$table->increments('id');
			$table->string('discount_name');
			$table->enum('status', array('Active', 'Inactive', 'Deleted'))->default('Active');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('discounts');
	}
}