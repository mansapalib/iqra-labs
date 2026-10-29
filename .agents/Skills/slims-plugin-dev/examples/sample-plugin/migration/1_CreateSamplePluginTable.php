<?php
use SLiMS\Migration\Migration;
use SLiMS\DB;

class CreateSamplePluginTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `plugin_sample_data` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `title` VARCHAR(255) NOT NULL,
            `description` TEXT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        DB::getInstance()->exec($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::getInstance()->exec("DROP TABLE IF EXISTS `plugin_sample_data`;");
    }
}
