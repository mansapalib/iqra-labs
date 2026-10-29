# SLiMS Database Migration Guide

This guide details how SLiMS manages schema versioning for plugins, how the `\SLiMS\Migration\Runner` operates, and best practices for creating bulletproof database changes.

---

## 1. How SLiMS Discovers and Tracks Migrations

1. When a plugin directory contains a `migration/` subfolder, SLiMS marks `is_exist = true` for migrations.
2. SLiMS scans the `migration/` folder for files matching:
   ```text
   <version>_<ClassName>.php
   ```
   Example: `1_CreateActivityTable.php`, `2_AddStatusIndex.php`.
3. SLiMS checks the `plugins` table in MySQL/MariaDB for the plugin's registered JSON `options`:
   ```json
   { "db_version": 1 }
   ```
4. If higher version numbers exist in the `migration/` directory, the SLiMS Plugin Manager shows an **Upgrade / Migrasi** button in the admin interface (`System > Plugins`).
5. When the upgrade is triggered, `\SLiMS\Migration\Runner::runUp()` requires each new migration file in ascending numerical order and runs its `up()` method, updating `db_version` upon completion.

---

## 2. Anatomy of a Migration File

File: `plugins/my_plugin/migration/1_CreateMyPluginTable.php`

```php
<?php
use SLiMS\Migration\Migration;
use SLiMS\DB;

class CreateMyPluginTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        $db = DB::getInstance();
        
        $sql = "CREATE TABLE IF NOT EXISTS `plugin_my_settings` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `setting_key` VARCHAR(100) NOT NULL UNIQUE,
            `setting_value` TEXT NULL,
            `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $db->exec($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        $db = DB::getInstance();
        $db->exec("DROP TABLE IF EXISTS `plugin_my_settings`;");
    }
}
```

---

## 3. Best Practices for SLiMS Migrations

1. **Use `IF NOT EXISTS` / `IF EXISTS`**:
   Ensure migrations can run idempotently without throwing fatal duplicate table/index errors if rerun.

2. **Always Use `utf8mb4` Character Set**:
   Ensure support for extended characters, Arabic script, emojis, and diverse metadata:
   ```sql
   ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
   ```

3. **Prefix Custom Tables**:
   Always prefix custom plugin tables with `plugin_` or your unique plugin slug (e.g. `plugin_rollover_logs`) to avoid colliding with core SLiMS tables (`biblio`, `member`, `loan`, `mst_author`, etc.).

4. **Foreign Key Restraints Caution**:
   Core SLiMS tables often use MyISAM or InnoDB depending on the legacy setup. If adding foreign keys to core tables, verify engine compatibility or manage relational integrity within application logic.

5. **Column Additions**:
   When altering existing tables:
   ```php
   public function up()
   {
       $db = DB::getInstance();
       // Check if column already exists before adding
       $check = $db->query("SHOW COLUMNS FROM `member` LIKE 'custom_status'")->fetch();
       if (!$check) {
           $db->exec("ALTER TABLE `member` ADD COLUMN `custom_status` VARCHAR(20) DEFAULT 'active' AFTER `is_pending`;");
       }
   }
   ```
