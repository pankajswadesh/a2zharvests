<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrdersTable extends Migration {

	public function up()
	{
		Schema::create('orders', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->integer('payment_id')->unsigned();
			$table->integer('shipping_id')->unsigned();
			$table->decimal('total_amount', 10,2);
			$table->decimal('total_discount', 10,2);
			$table->decimal('total_tax', 10,2);
			$table->datetime('datetime');
			$table->enum('status', array('Pending', 'Processing', 'Delivered'));
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('orders');
	}
}