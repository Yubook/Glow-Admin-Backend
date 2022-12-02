<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {

    \DB::statement("
          CREATE TABLE `states` (
            `id` bigint(20) UNSIGNED NOT NULL,
            `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `country_id` mediumint(8) UNSIGNED NOT NULL,
            `country_code` char(2) COLLATE utf8mb4_unicode_ci NOT NULL,
            `fips_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `iso2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `latitude` decimal(10,8) DEFAULT NULL,
            `longitude` decimal(11,8) DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
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
    \DB::statement("DROP TABLE IF EXISTS `states`;");
  }
}
