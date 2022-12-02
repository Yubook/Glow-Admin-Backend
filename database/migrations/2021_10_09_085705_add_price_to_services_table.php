<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceToServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->integer('time')->comment('in minutes')->after('name')->default(0);
        });
        Schema::table('barber_services', function (Blueprint $table) {
            $table->dropColumn('time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('services', 'time')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropColumn('time');
            });
        }
        Schema::table('barber_services', function (Blueprint $table) {
            $table->integer('time')->comment('in minutes')->after('barber_id')->default(0);
        });
    }
}
