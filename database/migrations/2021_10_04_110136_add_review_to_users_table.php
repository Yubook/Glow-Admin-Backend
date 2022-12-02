<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReviewToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('average_rating')->after('profile_approved')->default(0)->comment('1 to 5 star');
            $table->integer('total_reviews')->after('average_rating')->default(0)->comment('given by users count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('users', 'average_rating')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('average_rating');
            });
        }
        if (Schema::hasColumn('users', 'total_reviews')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('total_reviews');
            });
        }
    }
}
