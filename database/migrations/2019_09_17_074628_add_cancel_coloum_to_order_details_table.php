<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCancelColoumToOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->enum('is_cancel', array('Yes', 'No'))->default('No');
            $table->string('tax_value')->after('unit');
            $table->string('discount_value')->after('tax');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn('is_cancel');
            $table->dropColumn('tax_value');
            $table->dropColumn('discount_value');
        });
    }
}
