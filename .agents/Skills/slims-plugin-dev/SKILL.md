---
name: plugin-dev
description: >-
  Comprehensive guide and runbook for developing, debugging, and maintaining SLiMS (Senayan Library
  Management System) plugins, modules, hooks, database migrations, and admin controllers. Activate
  when creating or modifying plugins in plugins/, adding admin menus, hooking into circulation/biblio/membership,
  or writing SLiMS database migrations and action scripts.
---

# SLiMS Plugin & Module Development

This skill provides full architectural guidance, standard conventions, API patterns, and recipes for building high-quality, secure plugins and custom modules in **SLiMS (Senayan Library Management System)**.

---

## 1. Plugin Architecture Overview

SLiMS plugins are stored in the `plugins/` directory. Each plugin must either be a standalone file ending in `.plugin.php` or reside in a dedicated folder with an entrypoint matching `*.plugin.php` (strongly recommended).

```text
plugins/<plugin_slug>/
├── <plugin_slug>.plugin.php   # Main entry point with manifest header
├── index.php                  # Default admin controller / page view
├── pages/                     # (Optional) Hierarchical sub-pages
├── migration/                 # (Optional) Database migrations
│   └── 1_CreatePluginTable.php
├── action/                    # (Optional) Pre/post installation scripts
│   ├── pre.php
│   └── post.php
└── vendor/                    # (Optional) Plugin-specific Composer dependencies
```

---

## 2. Plugin Manifest & Security Header

Every plugin entry file **must** begin with the manifest metadata block and security guard:

```php
<?php
/**
 * Plugin Name: Member Card Customizer
 * Plugin URI: https://github.com/your-org/slims-member-card
 * Description: Modul kustomisasi tata letak dan pencetakan kartu anggota siswa & guru.
 * Version: 1.0.0
 * Author: SLiMS Developer Team
 * Author URI: https://github.com/your-org
 */

// Block direct URL execution
defined('INDEX_AUTH') OR die('Direct access not permitted.');

// Get SLiMS plugin instance
$plugin = \SLiMS\Plugins::getInstance();
```

---

## 3. Registering Menus & Submenus

### 3.1 Single Submenu in an Existing Module

```php
use SLiMS\Plugins;

// Syntax: Plugins::menu($module, $label, $absoluteFilePath, $description = null)
Plugins::menu('membership', 'Custom Card Print', __DIR__ . '/index.php', 'Cetak kartu anggota kustom');

// Or via fluent shorthand:
Plugins::membership('Custom Card Print', __DIR__ . '/index.php');
```

Common target core modules:
- `'bibliography'`
- `'circulation'`
- `'membership'`
- `'master_file'`
- `'reporting'`
- `'serial_control'`
- `'system'`
- `'opac'` (registers an OPAC-accessible page via `index.php?p=<slug>`)

### 3.2 Grouped Submenus & Ordering

```php
use SLiMS\Plugins;

// Group multiple submenus together and position relative to core menus:
Plugins::group('Kartu & Cetak', function() {
    Plugins::membership('Desain Kartu', __DIR__ . '/pages/design.php');
    Plugins::membership('Batch Print', __DIR__ . '/pages/batch.php');
})->after(__('Member List')); // or ->before(...)
```

### 3.3 Automatic Page Discovery (`registerPages`)

If your plugin directory has a `pages/` folder structured by module name:
```text
pages/
├── membership/
│   ├── GROUP_after_TOOLS/
│   │   └── export_csv.php
│   └── card_setting.php
```

Simply call:
```php
\SLiMS\Plugins::registerPages();
```

---

## 4. Hooking into SLiMS Lifecycle

Hooks allow you to inject custom behavior when events occur in SLiMS (e.g., after book save, during member creation, after loan circulation).

### 4.1 Closure-Based Hook

```php
use SLiMS\Plugins;

// Hooking into circulation transaction completion
Plugins::hook(Plugins::CIRCULATION_AFTER_SUCCESSFUL_TRANSACTION, function($member_id, $item_code, $transaction_type) {
    // Custom notification, logging, or Telegram/WhatsApp dispatch
});

// Hooking into biblio save
Plugins::hook(Plugins::BIBLIOGRAPHY_AFTER_SAVE, function($biblio_id, $data) {
    // Custom sync or indexing logic
});
```

### 4.2 Class-Based Hook Handler

```php
namespace MyPlugin;

use SLiMS\Plugins;

class EventHandler {
    public function onMemberSave($member_id, $data) {
        // Handle member save
    }
}

// Attach class handler:
Plugins::use(EventHandler::class)->for(function($plugins) {
    $plugins->registerHook(Plugins::MEMBERSHIP_AFTER_SAVE, 'onMemberSave');
});
```

For complete lists of hook constants and argument signatures, check [references/hooks-reference.md](./references/hooks-reference.md).

---

## 5. Database Migrations

SLiMS features an automatic migration runner for plugins that handles version tracking in the `plugins` table.

### 5.1 Structure & Naming Rule

Place migration files inside `<plugin_dir>/migration/`:
- Pattern: `<version>_<ClassName>.php` (e.g., `1_CreatePluginTable.php`, `2_AddStatusColumn.php`)
- `<version>` must be an incrementing integer (`1`, `2`, `3`...).
- The class name inside the file **must exactly match** `<ClassName>` and extend `\SLiMS\Migration\Migration`.

### 5.2 Example Migration Class

```php
<?php
use SLiMS\Migration\Migration;
use SLiMS\DB;

class CreatePluginTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `plugin_custom_log` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `member_id` VARCHAR(50) NOT NULL,
            `action` VARCHAR(100) NOT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_member` (`member_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        DB::getInstance()->exec($sql);
    }

    /**
     * Reverse the migrations (rollback).
     */
    public function down()
    {
        DB::getInstance()->exec("DROP TABLE IF EXISTS `plugin_custom_log`;");
    }
}
```

When an admin activates or upgrades the plugin under **System > Plugins**, SLiMS runs the `up()` method and records the current version in the plugin's metadata.

---

## 6. Pre and Post Installation Actions

If files need copying or special environment setup during plugin installation, use the `action/` directory:
- `action/pre.php` -> class `Pre` with method matching action type (`install`, `uninstall`).
- `action/post.php` -> class `Post` with method matching action type (`install`, `uninstall`).

```php
<?php
// action/post.php
class Post
{
    public function install()
    {
        // Code executed right after plugin is activated/installed
    }

    public function uninstall()
    {
        // Code executed when plugin is uninstalled
    }
}
```

---

## 7. Admin Controller Best Practices

When building pages loaded inside the SLiMS admin interface (`AWB . 'plugin_container.php'`):

1. **Permission Check**: Verify user privileges if the action requires write/update rights:
   ```php
   defined('INDEX_AUTH') OR die('Akses langsung tidak diizinkan.');
   $can_read = utility::havePrivilege($mod_name, 'r');
   $can_write = utility::havePrivilege($mod_name, 'w');
   if (!$can_read) {
       die('<div class="alert alert-danger">' . __('Access Denied') . '</div>');
   }
   ```
2. **Database Queries**: Always use `\SLiMS\DB::getInstance()` with PDO prepared statements:
   ```php
   $dbs = \SLiMS\DB::getInstance();
   $stmt = $dbs->prepare('SELECT * FROM member WHERE member_id = :id');
   $stmt->execute([':id' => $member_id]);
   $member = $stmt->fetch(\PDO::FETCH_ASSOC);
   ```
3. **Escaping & Sanitization**: Always sanitize input using `\SLiMS\Sanitizer` or `htmlspecialchars()` for XSS protection.
4. **SLiMS UI Components**: Use SLiMS CSS classes (`s-table`, `btn`, `btn-primary`, `card`, `alert alert-info`) and helper methods like `simbio_GUI_form` to ensure seamless visual integration.

---

## 8. Development Recipes

- Check [references/hooks-reference.md](./references/hooks-reference.md) for available hook events.
- Check [references/migration-guide.md](./references/migration-guide.md) for database schema best practices in MySQL/MariaDB.
- Check [examples/sample-plugin/](./examples/sample-plugin/) for an end-to-end boilerplate plugin.
- Run `php .agents/skills/slims-plugin-dev/scripts/scaffold.php <plugin-name>` to instantly generate a clean scaffold.
