<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTermsPolicyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('terms_policy', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('selection', ['Terms', 'Privacy'])->default('Terms');
            $table->longText('description');
            $table->tinyInteger('for')->comment('2=Barber,3=User');
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('terms_policy');
    }
}
