<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductImagesTable extends Migration {

	public function up()
	{
		Schema::create('product_images', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('product_id')->unsigned()->index();
			$table->string('image');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('product_images');
	}
}