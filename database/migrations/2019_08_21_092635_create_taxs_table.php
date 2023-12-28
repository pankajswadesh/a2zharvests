<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTaxsTable extends Migration {

	public function up()
	{
		Schema::create('taxs', function(Blueprint $table) {
			$table->increments('id');
			$table->string('tax_name');
			$table->string('tax_value')->nullable()->default('0');
			$table->enum('status', array('Active', 'Inactive', 'Deleted'))->default('Active');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('taxs');
	}
}