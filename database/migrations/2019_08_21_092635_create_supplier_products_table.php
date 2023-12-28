<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSupplierProductsTable extends Migration {

	public function up()
	{
		Schema::create('supplier_products', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index();
			$table->integer('product_id')->unsigned();
			$table->integer('quantity');
			$table->decimal('price', 10,2);
			$table->integer('discount_id')->unsigned();
			$table->enum('status', array('Active', 'Inactive', 'Deleted'))->default('Active');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('supplier_products');
	}
}