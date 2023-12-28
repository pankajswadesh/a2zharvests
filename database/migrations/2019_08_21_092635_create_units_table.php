<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUnitsTable extends Migration {

	public function up()
	{
		Schema::create('units', function(Blueprint $table) {
			$table->increments('id');
			$table->string('unit_name');
			$table->enum('status', array('Active', 'Inactive', 'Deleted'))->default('Active');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('units');
	}
}