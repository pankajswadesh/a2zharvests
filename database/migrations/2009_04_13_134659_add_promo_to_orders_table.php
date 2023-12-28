<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPromoToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('applied_promo_code')->after('total_tax')->nullable();
            $table->double('promo_discount',[8,2])->after('applied_promo_code')->nullable();
            $table->double('cashback_amount',[8,2])->after('promo_discount')->nullable();
            $table->enum('cashback_status',['Processing','Ready','Transferred'])->after('cashback_amount')->default('Processing');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('applied_promo_code');
            $table->dropColumn('promo_discount');
            $table->dropColumn('cashback_amount');
            $table->dropColumn('cashback_status');
        });
    }
}
