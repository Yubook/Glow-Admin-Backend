<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBarberIdToReviewImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('review_images', function (Blueprint $table) {
            $table->unsignedBigInteger('barber_id')->after('user_reviews_id')->nullable();

            $table->foreign('barber_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('review_images', 'barber_id')) {
            Schema::table('review_images', function (Blueprint $table) {
                $table->dropForeign('review_images_barber_id_foreign');
                $table->dropColumn('barber_id');
            });
        }
    }
}
