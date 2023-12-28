<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsInclusiveToTaxsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('taxs', function (Blueprint $table) {
            $table->enum('is_inclusive', array('Yes', 'No'))->default('No');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('taxs', function (Blueprint $table) {
            $table->dropColumn('is_inclusive');
        });
    }
}
