<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_reviews', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('barber_id')->unsigned();
            //  $table->bigInteger('order_id')->unsigned();
            $table->integer('service')->nullable()->comment('1 to 5 star');
            $table->integer('hygiene')->nullable()->comment('1 to 5 star');
            $table->integer('value')->nullable()->comment('1 to 5 star');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('barber_id')->references('id')->on('users');
            //$table->foreign('order_id')->references('id')->on('orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_reviews');
    }
}
