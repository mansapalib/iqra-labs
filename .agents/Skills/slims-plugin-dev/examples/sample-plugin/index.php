<?php
/**
 * Main Controller / View for Sample Boilerplate Plugin
 */

defined('INDEX_AUTH') OR die('Akses langsung tidak diizinkan.');

// Cek hak akses baca modul
$can_read = utility::havePrivilege('membership', 'r');
if (!$can_read) {
    die('<div class="alert alert-danger">' . __('Access Denied') . '</div>');
}

$can_write = utility::havePrivilege('membership', 'w');

// Query contoh data
$dbs = \SLiMS\DB::getInstance();
$stats = $dbs->query("SELECT COUNT(*) AS total FROM member")->fetch(\PDO::FETCH_ASSOC);
$total_member = $stats['total'] ?? 0;
?>

<div class="menuBox">
    <div class="menuBoxInner memberIcon">
        <div class="per_title">
            <h2>Sample Boilerplate Plugin</h2>
        </div>
        <div class="sub_section">
            <div class="btn-group">
                <a href="<?= AWB; ?>plugin_container.php?mod=membership&id=<?= md5(__FILE__); ?>" class="btn btn-default active">Dashboard</a>
            </div>
        </div>
    </div>
</div>

<div class="contentArea">
    <div class="card p-4 shadow-sm mb-4">
        <h4 class="mb-3">Informasi Sistem</h4>
        <p class="text-muted">Total anggota terdaftar di perpustakaan: <strong><?= number_format($total_member); ?></strong></p>
        
        <?php if ($can_write): ?>
            <div class="alert alert-success">
                Anda memiliki hak akses tulis (write) untuk modul ini.
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                Anda hanya memiliki hak akses baca (read-only).
            </div>
        <?php endif; ?>
    </div>
</div>
