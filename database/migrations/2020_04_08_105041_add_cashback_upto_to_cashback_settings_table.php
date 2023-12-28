<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCashbackUptoToCashbackSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cashback_settings', function (Blueprint $table) {
           $table->double('cashback_upto',[8,2])->after('cashback_percent');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cashback_settings', function (Blueprint $table) {
           $table->dropColumn('cashback_upto');
        });
    }
}
