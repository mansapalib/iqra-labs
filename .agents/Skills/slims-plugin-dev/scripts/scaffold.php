<?php
/**
 * SLiMS Plugin Scaffolder CLI
 * Usage: php scaffold.php <plugin-slug> [Plugin Name] [target-module]
 * Example: php scaffold.php visitor_kiosk "Visitor Kiosk Self-Service" membership
 */

if (php_sapi_name() !== 'cli') {
    die("This script must be run from the command line.\n");
}

$slug = $argv[1] ?? null;
if (!$slug) {
    echo "Usage: php scaffold.php <plugin-slug> [Plugin Name] [target-module]\n";
    echo "Example: php scaffold.php card_designer \"Card Designer Pro\" membership\n";
    exit(1);
}

$slug = strtolower(preg_replace('/[^a-zA-Z0-9_-]/', '', $slug));
$name = $argv[2] ?? ucwords(str_replace(['-', '_'], ' ', $slug));
$module = $argv[3] ?? 'membership';

// Resolve plugins root directory
$pluginsDir = realpath(__DIR__ . '/../../../../plugins');
if (!$pluginsDir || !is_dir($pluginsDir)) {
    echo "Error: Cannot locate plugins directory at {$pluginsDir}\n";
    exit(1);
}

$targetDir = $pluginsDir . DIRECTORY_SEPARATOR . $slug;
if (is_dir($targetDir)) {
    echo "Error: Plugin directory already exists at: {$targetDir}\n";
    exit(1);
}

// Create directories
mkdir($targetDir, 0755, true);
mkdir($targetDir . DIRECTORY_SEPARATOR . 'migration', 0755, true);
mkdir($targetDir . DIRECTORY_SEPARATOR . 'pages', 0755, true);
mkdir($targetDir . DIRECTORY_SEPARATOR . 'action', 0755, true);

// 1. Entry plugin file
$entryFile = $targetDir . DIRECTORY_SEPARATOR . "{$slug}.plugin.php";
$entryContent = <<<PHP
<?php
/**
 * Plugin Name: {$name}
 * Plugin URI: https://github.com/
 * Description: Modul plugin {$name} untuk perpustakaan SLiMS.
 * Version: 1.0.0
 * Author: SLiMS Developer
 * Author URI: https://slims.web.id
 */

defined('INDEX_AUTH') OR die('Akses langsung tidak diizinkan.');

use SLiMS\\Plugins;

\$plugin = Plugins::getInstance();

// Daftarkan submenu ke modul {$module}
\$plugin->registerMenu('{$module}', '{$name}', __DIR__ . '/index.php', 'Kelola {$name}');
PHP;
file_put_contents($entryFile, $entryContent);

// 2. index.php
$indexFile = $targetDir . DIRECTORY_SEPARATOR . 'index.php';
$indexContent = <<<PHP
<?php
/**
 * Controller & View for {$name}
 */

defined('INDEX_AUTH') OR die('Akses langsung tidak diizinkan.');

\$can_read = utility::havePrivilege('{$module}', 'r');
if (!\$can_read) {
    die('<div class="alert alert-danger">' . __('Access Denied') . '</div>');
}

\$can_write = utility::havePrivilege('{$module}', 'w');
?>

<div class="menuBox">
    <div class="menuBoxInner">
        <div class="per_title">
            <h2>{$name}</h2>
        </div>
        <div class="sub_section">
            <div class="btn-group">
                <a href="<?= AWB; ?>plugin_container.php?mod={$module}&id=<?= md5(__FILE__); ?>" class="btn btn-default active">Beranda</a>
            </div>
        </div>
    </div>
</div>

<div class="contentArea">
    <div class="card p-4 shadow-sm">
        <h4 class="mb-3">Selamat Datang di {$name}</h4>
        <p class="text-muted">Plugin berhasil terpasang dan siap dikembangkan.</p>
    </div>
</div>
PHP;
file_put_contents($indexFile, $indexContent);

// 3. Migration
$cleanClassName = 'Create' . str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $slug))) . 'Table';
$migFile = $targetDir . DIRECTORY_SEPARATOR . 'migration' . DIRECTORY_SEPARATOR . "1_{$cleanClassName}.php";
$migContent = <<<PHP
<?php
use SLiMS\\Migration\\Migration;
use SLiMS\\DB;

class {$cleanClassName} extends Migration
{
    public function up()
    {
        \$sql = "CREATE TABLE IF NOT EXISTS `plugin_{$slug}` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        DB::getInstance()->exec(\$sql);
    }

    public function down()
    {
        DB::getInstance()->exec("DROP TABLE IF EXISTS `plugin_{$slug}`;");
    }
}
PHP;
file_put_contents($migFile, $migContent);

echo "Success! Plugin {$name} successfully scaffolded at:\n";
echo "-> {$targetDir}\n";
echo "Files created:\n";
echo " - {$slug}.plugin.php\n";
echo " - index.php\n";
echo " - migration/1_{$cleanClassName}.php\n";
