<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->tinyInteger('type')->after('id')->default(1)->comment('1=order,2=chat');
            $table->unsignedBigInteger('order_id')->after('message')->nullable();
            $table->tinyInteger('order_status')->after('order_id')->nullable();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('notifications', 'type')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
        if (Schema::hasColumn('notifications', 'order_status')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dropColumn('order_status');
            });
        }
        if (Schema::hasColumn('notifications', 'order_id')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dropForeign('notifications_order_id_foreign');
                $table->dropColumn('order_id');
            });
        }
    }
}
