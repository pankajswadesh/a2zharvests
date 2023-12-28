<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCategoriesTable extends Migration {

	public function up()
	{
		Schema::create('categories', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('parent_id')->unsigned();
			$table->string('category_name');
			$table->string('category_image');
			$table->string('url');
			$table->enum('status', array('Active', 'Inactive', 'Deleted'))->default('Active')->index();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('categories');
	}
}