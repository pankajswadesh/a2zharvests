<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBankDetailsTable extends Migration {

	public function up()
	{
		Schema::create('bank_details', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->string('holder_name')->nullable();
			$table->string('account_no')->nullable();
			$table->string('branch_name')->nullable();
			$table->string('ifsc_code')->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('bank_details');
	}
}