<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTaxValuesTable extends Migration {

	public function up()
	{
		Schema::create('tax_values', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('tax_id')->unsigned();
			$table->string('ledger_name');
			$table->integer('value');
			$table->enum('status', array('Active', 'Inactive', 'Deleted'))->default('Active');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('tax_values');
	}
}