<?php
/**
 * Plugin Name: Sample Boilerplate Plugin
 * Plugin URI: https://github.com/slims/slims9_bulian
 * Description: Contoh boilerplate lengkap plugin SLiMS dengan menu admin, hook, dan migrasi database.
 * Version: 1.0.0
 * Author: SLiMS Developer
 * Author URI: https://slims.web.id
 */

defined('INDEX_AUTH') OR die('Akses langsung tidak diizinkan.');

use SLiMS\Plugins;

$plugin = Plugins::getInstance();

// 1. Daftarkan menu pada modul Membership
$plugin->registerMenu('membership', 'Sample Boilerplate', __DIR__ . '/index.php', 'Halaman contoh modul plugin');

// 2. Pasang hook ketika member baru berhasil disimpan
Plugins::hook(Plugins::MEMBERSHIP_AFTER_SAVE, function($member_id, $data) {
    // Jalankan logika tambahan setelah member disimpan
    // error_log("Member baru terdaftar: " . $member_id);
});
