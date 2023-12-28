<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductsTable extends Migration {

	public function up()
	{
		Schema::create('products', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('category_id')->unsigned()->index();
			$table->integer('brand_id')->unsigned();
			$table->string('product_name');
			$table->string('print_name');
			$table->string('product_image');
			$table->text('product_description');
			$table->string('product_company');
			$table->integer('unit_id')->unsigned();
			$table->integer('department_id')->unsigned()->index();
			$table->integer('tax_id')->unsigned();
            $table->enum('status', array('Active', 'Inactive','Deleted'))->default('Active')->index();
			$table->string('url');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('products');
	}
}