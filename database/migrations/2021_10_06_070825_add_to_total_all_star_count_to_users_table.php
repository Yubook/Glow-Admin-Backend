<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddToTotalAllStarCountToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger("fivestar")->after('total_reviews')->comment('for barber')->default(0);
            $table->bigInteger("fourstar")->after('fivestar')->comment('for barber')->default(0);
            $table->bigInteger("threestar")->after('fourstar')->comment('for barber')->default(0);
            $table->bigInteger("twostar")->after('threestar')->comment('for barber')->default(0);
            $table->bigInteger("onestar")->after('twostar')->comment('for barber')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('users', 'fivestar')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('fivestar');
                $table->dropColumn('fourstar');
                $table->dropColumn('threestar');
                $table->dropColumn('twostar');
                $table->dropColumn('onestar');
            });
        }
    }
}
