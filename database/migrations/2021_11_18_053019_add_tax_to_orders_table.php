<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaxToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('stripe_fee')->after('amount')->comment('stripe fees')->nullable();
            $table->string('admin_fee')->after('stripe_fee')->comment('admin charges')->nullable();
            $table->string('net_order_price')->after('admin_fee')->comment('net order price')->nullable();
            $table->string('barber_amount')->after('net_order_price')->comment('barber get amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('orders', 'net_order_price')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('net_order_price');
            });
        }
        if (Schema::hasColumn('orders', 'stripe_fee')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('stripe_fee');
            });
        }
        if (Schema::hasColumn('orders', 'admin_fee')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('admin_fee');
            });
        }
        if (Schema::hasColumn('orders', 'barber_amount')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('barber_amount');
            });
        }
    }
}
