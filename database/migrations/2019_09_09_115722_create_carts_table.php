<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCartsTable extends Migration {

	public function up()
	{
		Schema::create('carts', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->integer('supplier_id')->unsigned();
			$table->integer('product_id')->unsigned();
			$table->integer('quantity');
			$table->decimal('price', 10,2);
			$table->decimal('discount', 10,2);
			$table->string('tax');
			$table->datetime('datetime');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('carts');
	}
}