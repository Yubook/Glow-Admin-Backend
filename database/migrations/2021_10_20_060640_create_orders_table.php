<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('barber_id');
            $table->string('latitude')->nullable()->comment('order location');
            $table->string('longitude')->nullable()->comment('order location');
            $table->string('address')->nullable()->comment('order address');
            $table->string('stripe_key')->nullable();
            $table->string('transaction_number')->nullable();
            $table->integer('discount')->comment('In %')->default(0);
            $table->string('amount')->comment('In Euro')->nullable();
            $table->tinyInteger('payment_type')->default(1)->comment('1=card,2=fade_coin');
            $table->json('stripe_response')->comment('for payment_type 1')->nullable();
            $table->tinyInteger('is_order_complete')->default(0)->comment('0=incomplete,1=complete,2=rejected');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('barber_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
