<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBrandsTable extends Migration {

	public function up()
	{
		Schema::create('brands', function(Blueprint $table) {
			$table->increments('id');
			$table->string('brand_name');
			$table->enum('status', array('Active', 'Inactive', 'Deleted'))->default('Active');
			$table->string('url');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('brands');
	}
}