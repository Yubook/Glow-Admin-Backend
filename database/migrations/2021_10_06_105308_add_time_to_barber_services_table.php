<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimeToBarberServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('barber_services', function (Blueprint $table) {
            $table->integer('time')->after('barber_id')->default(0)->comment('time for specific service in minutes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('barber_services', 'time')) {
            Schema::table('barber_services', function (Blueprint $table) {
                $table->dropColumn('time');
            });
        }
    }
}
