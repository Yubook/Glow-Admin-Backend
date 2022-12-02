<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsServiceAddedToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('is_service_added')->after('is_active')->default(0)->comment('For barber only : 0=service add pending,1=service added');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('users', 'is_service_added')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_service_added');
            });
        }
    }
}
