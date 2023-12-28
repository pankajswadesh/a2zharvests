<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePaymentsTable extends Migration {

	public function up()
	{
		Schema::create('payments', function(Blueprint $table) {
			$table->increments('id');
			$table->string('payment_method');
			$table->enum('payment_status', array('Pending', 'Completed', 'Decliend'));
			$table->datetime('payment_date_time');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('payments');
	}
}