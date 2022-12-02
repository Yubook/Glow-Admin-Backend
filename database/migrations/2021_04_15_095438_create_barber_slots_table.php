<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarberSlotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barber_slots', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('barber_id');
            $table->unsignedBigInteger('timing_id');
            $table->date('date');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->tinyInteger('is_booked')->comment('0=open,1=booked')->default(0);
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();

            $table->foreign('barber_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('timing_id')->references('id')->on('timings')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('driver_slots');
    }
}
