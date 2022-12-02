<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryToServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->after('name')->nullable();
            $table->unsignedBigInteger('subcategory_id')->after('category_id')->nullable();

            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('subcategory_id')->references('id')->on('subcategories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('services', 'category_id')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropForeign('services_category_id_foreign');
                $table->dropColumn('category_id');
            });
        }
        if (Schema::hasColumn('services', 'subcategory_id')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropForeign('services_subcategory_id_foreign');
                $table->dropColumn('subcategory_id');
            });
        }
    }
}
