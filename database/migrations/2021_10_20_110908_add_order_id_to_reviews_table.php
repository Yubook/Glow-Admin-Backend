<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderIdToReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_reviews', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->after('id')->nullable();

            $table->foreign('order_id')->references('id')->on('orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('user_reviews', 'order_id')) {
            Schema::table('user_reviews', function (Blueprint $table) {
                $table->dropForeign('user_reviews_order_id_foreign');
                $table->dropColumn('order_id');
            });
        }
    }
}
