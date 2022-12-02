<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRadiusToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('min_radius')->after('gender')->nullable()->comment('for barber serach in miles');
            $table->integer('max_radius')->after('min_radius')->nullable()->comment('for barber serach in miles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('users', 'min_radius')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('min_radius');
            });
        }
        if (Schema::hasColumn('users', 'max_radius')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('max_radius');
            });
        }
    }
}
