<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitiesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    \DB::statement("
        CREATE TABLE `cities` (
            `id` bigint(20) UNSIGNED NOT NULL,
            `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `state_id` mediumint(8) UNSIGNED NOT NULL,
            `state_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `country_id` mediumint(8) UNSIGNED NOT NULL,
            `country_code` char(2) COLLATE utf8mb4_unicode_ci NOT NULL,
            `latitude` decimal(10,8) NOT NULL,
            `longitude` decimal(11,8) NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT '2014-01-01 01:01:01',
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            `active` tinyint(1) NOT NULL DEFAULT '1',
            `wikiDataId` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Rapid API GeoDB Cities'
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;
          ");
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    \DB::statement("DROP TABLE IF EXISTS `cities`;");
  }
}
