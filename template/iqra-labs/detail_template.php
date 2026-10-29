<?php
/**
 * ============================================================================
 * MANSAPA Iqra' Labs — Redesigned Student-Friendly Book Detail Template
 * File: template/iqra-labs/detail_template.php
 *
 * Designed for High Clarity, Simplicity, and Student User Experience:
 * - Clear, prominent real-time book availability indicator
 * - Dedicated Shelf Location (Lokasi Rak) & Call Number (Nomor Panggil) spotlight
 * - Removes obscure RDA cataloging jargon (Content/Media/Carrier type)
 * - Clean metadata grid (Publisher, Year, ISBN, Language, Pages, Classification)
 * - Modern 2-column responsive layout with rich cover presentation
 * - 1-Click quick actions: Bookmark, Share, Add to Basket, Copy Call Number
 * - Collapsible / clean physical copies table with loan due dates
 * - Digital attachment / E-Book access callout (if available)
 * ============================================================================
 */

global $sysconf, $dbs;

if (!isset($biblio_id)) $biblio_id = 0;
if (!isset($image)) $image = '';
if (!isset($gmd_name)) $gmd_name = '';
if (!isset($title)) $title = '';
if (!isset($authors)) $authors = '';
if (!isset($notes)) $notes = '';
if (!isset($availability)) $availability = '';
if (!isset($series_title)) $series_title = '';
if (!isset($call_number)) $call_number = '';
if (!isset($publish_place)) $publish_place = '';
if (!isset($publisher_name)) $publisher_name = '';
if (!isset($publish_year)) $publish_year = '';
if (!isset($collation)) $collation = '';
if (!isset($language_name)) $language_name = '';
if (!isset($isbn_issn)) $isbn_issn = '';
if (!isset($classification)) $classification = '';
if (!isset($edition)) $edition = '';
if (!isset($subjects)) $subjects = '';
if (!isset($biblio_custom)) $biblio_custom = [];
if (!isset($related)) $related = '';
if (!isset($file_att)) $file_att = '';

// Fallback: If $copies array is not provided, query directly from database
if (empty($copies) && isset($dbs) && $biblio_id > 0) {
    $copies = [];
    $copy_q = $dbs->query(sprintf(
        "SELECT i.item_code, i.call_number, loc.location_name, stat.item_status_name, stat.no_loan, i.site 
         FROM item AS i
         LEFT JOIN mst_item_status AS stat ON i.item_status_id=stat.item_status_id
         LEFT JOIN mst_location AS loc ON i.location_id=loc.location_id
         WHERE i.biblio_id=%d", $biblio_id
    ));
    if ($copy_q && $copy_q->num_rows > 0) {
        while ($cd = $copy_q->fetch_assoc()) {
            $copies[] = $cd;
        }
        $copy_q->free_result();
    }
}

// ── PROCESS PHYSICAL COPIES & AVAILABILITY ──────────────────────────────────
$copy_items       = [];
$total_copies     = 0;
$available_copies = 0;
$loaned_copies    = 0;
$reference_copies = 0;
$nearest_due_date = null;
$primary_location = 'Perpustakaan MAN 1 Pangandaran';

if (!empty($copies) && is_array($copies)) {
    $total_copies = count($copies);
    foreach ($copies as $copy_d) {
        $c_code = htmlspecialchars($copy_d['item_code'] ?? '-');
        $c_call = htmlspecialchars(!empty($copy_d['call_number']) ? $copy_d['call_number'] : $call_number);
        $c_loc  = htmlspecialchars(!empty($copy_d['location_name']) ? $copy_d['location_name'] : 'Perpustakaan MAN 1 Pangandaran');
        if (!empty($copy_d['site'])) {
            $c_loc .= ' (' . htmlspecialchars($copy_d['site']) . ')';
        }
        if (!empty($copy_d['location_name'])) {
            $primary_location = $c_loc;
        }

        // Real-time check if copy is on loan
        $loan_q = $dbs->query("SELECT due_date FROM loan WHERE item_code='" . $dbs->escape_string($copy_d['item_code']) . "' AND is_lent=1 AND is_return=0 LIMIT 1");
        $is_loaned = ($loan_q && $loan_q->num_rows > 0);
        $due_str = '';

        if ($is_loaned) {
            $loan_d = $loan_q->fetch_row();
            $due_time = strtotime($loan_d[0]);
            $due_str = date('d M Y', $due_time);
            if (!$nearest_due_date || $due_time < $nearest_due_date) {
                $nearest_due_date = $due_time;
            }
            $loaned_copies++;
            $status_type = 'loaned';
            $status_label = 'Sedang Dipinjam';
            $status_badge = 'Kembali ' . $due_str;
        } elseif (!empty($copy_d['no_loan'])) {
            $reference_copies++;
            $status_type = 'reference';
            $status_label = 'Baca di Tempat';
            $status_badge = !empty($copy_d['item_status_name']) ? htmlspecialchars($copy_d['item_status_name']) : 'Koleksi Referensi / Tandon';
        } else {
            $available_copies++;
            $status_type = 'available';
            $status_label = 'Tersedia di Rak';
            $status_badge = !empty($copy_d['item_status_name']) ? htmlspecialchars($copy_d['item_status_name']) : 'Siap Dipinjam';
        }

        $copy_items[] = [
            'item_code'    => $c_code,
            'call_number'  => $c_call,
            'location'     => $c_loc,
            'status_type'  => $status_type,
            'status_label' => $status_label,
            'status_badge' => $status_badge,
        ];
    }
}

// Determine Overall Availability Tier
if ($total_copies === 0) {
    $overall_status_class = 'status-nodata';
    $overall_status_title = 'Belum Ada Eksemplar Fisik';
    $overall_status_desc  = 'Buku ini belum memiliki eksemplar fisik di perpustakaan atau merupakan koleksi daring.';
    $overall_badge_bg     = 'bg-slate-100 text-slate-700 border-slate-200';
} elseif ($available_copies > 0) {
    $overall_status_class = 'status-available';
    $overall_status_title = $available_copies . ' dari ' . $total_copies . ' Eksemplar Tersedia di Rak';
    $overall_status_desc  = 'Buku siap dipinjam langsung di perpustakaan dengan menunjukkan nomor panggil ke pustakawan.';
    $overall_badge_bg     = 'bg-emerald-50 text-emerald-800 border-emerald-200';
} elseif ($loaned_copies > 0) {
    $overall_status_class = 'status-loaned';
    $overall_status_title = 'Semua Eksemplar Sedang Dipinjam (' . $loaned_copies . ' Buku)';
    $overall_status_desc  = $nearest_due_date ? 'Perkiraan kembali paling cepat: ' . date('d M Y', $nearest_due_date) : 'Silakan cek kembali beberapa hari ke depan.';
    $overall_badge_bg     = 'bg-amber-50 text-amber-800 border-amber-200';
} else {
    $overall_status_class = 'status-reference';
    $overall_status_title = 'Koleksi Khusus Baca di Tempat';
    $overall_status_desc  = 'Hanya dapat dibaca di ruang perpustakaan dan tidak untuk dipinjamkan ke luar.';
    $overall_badge_bg     = 'bg-blue-50 text-blue-800 border-blue-200';
}

$isBookmarked = isset($_SESSION['bookmark']) && is_array($_SESSION['bookmark']) && in_array($biblio_id, $_SESSION['bookmark']);
?>

<div class="iqra-detail-wrapper w-100 pb-8">
    <div class="book-detail-inner w-100">

        <!-- ── BREADCRUMB & BACK BUTTON ─────────────────────────────────── -->
        <nav class="book-breadcrumb d-flex align-items-center justify-content-between mb-6" aria-label="Navigasi Halaman">
            <ol class="breadcrumb-list d-flex align-items-center mb-0 p-0 list-unstyled">
                <li><a href="index.php" class="breadcrumb-link">Beranda</a></li>
                <li class="breadcrumb-sep" aria-hidden="true">&rsaquo;</li>
                <li><a href="index.php?p=opac" class="breadcrumb-link">Katalog</a></li>
                <li class="breadcrumb-sep" aria-hidden="true">&rsaquo;</li>
                <li class="breadcrumb-current text-truncate" style="max-width: 320px;" aria-current="page"><?= htmlspecialchars(stripslashes($title)); ?></li>
            </ol>
            <a href="javascript:history.back()" class="btn-back-catalog d-inline-flex align-items-center">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="me-1" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                <span>Kembali</span>
            </a>
        </nav>

        <!-- ── MAIN BOOK DETAIL 2-COLUMN GRID ────────────────────────────── -->
        <div class="book-detail-grid">

            <!-- ── LEFT COLUMN: COVER & STUDENT QUICK ACTIONS ───────────── -->
            <aside class="book-sidebar">
                
                <!-- Book Cover Card -->
                <div class="book-cover-card">
                    <div class="book-cover-wrap">
                        <?= $image; ?>
                        <?php if (!empty($gmd_name)): ?>
                            <span class="book-gmd-badge" title="Bentuk Media: <?= htmlspecialchars($gmd_name); ?>">
                                <?= htmlspecialchars($gmd_name); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Student Quick Actions Bar -->
                <div class="book-actions-card mt-4">
                    <!-- Bookmark Action -->
                    <a href="#" data-id="<?= $biblio_id; ?>" data-detail="true" class="bookMarkBook action-btn-pill <?= $isBookmarked ? 'bg-success text-white' : 'btn-outline-action'; ?>" aria-label="Simpan buku ke bookmark">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="<?= $isBookmarked ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m19 21-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16z"/></svg>
                        <span id="label-<?= $biblio_id; ?>"><?= $isBookmarked ? __('Bookmarked') : __('Simpan Buku'); ?></span>
                    </a>

                    <!-- Add to Loan Basket (Keranjang Koleksi) -->
                    <button type="button" class="action-btn-pill btn-basket-action add-to-chart-button" data-biblio="<?= $biblio_id; ?>" aria-label="Tambah ke keranjang peminjaman">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 11-1 9"/><path d="m19 11-4-7"/><path d="M2 11h20"/><path d="m3.5 11 1.6 7.4a2 2 0 0 0 2 1.6h9.8c.9 0 1.8-.7 2-1.6l1.6-7.4"/><path d="m9 11 1 9"/><path d="M5 11l4-7"/></svg>
                        <span>+ Keranjang Pinjam</span>
                    </button>

                    <!-- Share Action -->
                    <a href="javascript:void(0)" data-toggle="modal" data-id="<?= $biblio_id; ?>" data-title="<?= htmlspecialchars($title); ?>" data-target="#mediaSocialModal" class="action-btn-pill btn-outline-action" aria-label="Bagikan informasi buku ini">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                        <span>Bagikan</span>
                    </a>

                    <!-- Copy Book Info Button -->
                    <button type="button" id="btn-copy-book-info" class="action-btn-pill btn-copy-info" data-title="<?= htmlspecialchars(stripslashes($title)); ?>" data-call="<?= htmlspecialchars($call_number); ?>" aria-label="Salin judul dan nomor panggil">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                        <span>Salin No. Panggil</span>
                    </button>
                </div>

                <!-- Digital File / E-Book Access (If Available) -->
                <?php if (!empty($file_att) && stripos($file_att, 'No Data') === false): ?>
                    <div class="digital-file-box mt-4">
                        <div class="digital-file-header">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-600" aria-hidden="true"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                            <span class="digital-file-title">Berkas Digital / E-Book</span>
                        </div>
                        <div class="digital-file-content">
                            <?= $file_att; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Student Guidance Tip -->
                <div class="student-tip-card mt-4">
                    <div class="student-tip-header">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="text-amber-500 me-2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                        <span class="student-tip-title">Panduan Siswa</span>
                    </div>
                    <p class="student-tip-text">
                        Untuk meminjam, catat <strong>Nomor Panggil</strong> lalu temui pustakawan di Meja Sirkulasi dengan membawa kartu anggota perpustakaan.
                    </p>
                </div>

            </aside>

            <!-- ── RIGHT COLUMN: CORE BOOK INFO, AVAILABILITY & SHELF LOCATION ─ -->
            <main class="book-content" role="main">

                <!-- 1. Header Title & Authors -->
                <div class="book-header-section mb-6">
                    <!-- Topics / Subject Tag Chips -->
                    <?php if (!empty($subjects)): ?>
                        <div class="book-tags-list mb-2">
                            <?= $subjects; ?>
                        </div>
                    <?php endif; ?>

                    <h1 class="book-main-title">
                        <?= htmlspecialchars(stripslashes($title)); ?>
                    </h1>

                    <?php if (!empty($series_title)): ?>
                        <p class="book-series-title">
                            <span class="text-muted">Seri:</span> <?= htmlspecialchars($series_title); ?>
                        </p>
                    <?php endif; ?>

                    <div class="book-authors-line">
                        <span class="authors-label">Penulis:</span>
                        <span class="authors-names"><?= str_replace('<br />', ', ', $authors); ?></span>
                    </div>
                </div>

                <!-- 2. HIGHLIGHT SPOTLIGHT: LOKASI RAK & STATUS KETERSEDIAAN ─── -->
                <section class="shelf-location-card mb-6" aria-label="Informasi Ketersediaan dan Lokasi Rak">
                    
                    <!-- Availability Status Banner -->
                    <div class="location-status-banner <?= $overall_badge_bg; ?>">
                        <div class="status-indicator-dot <?= $overall_status_class; ?>"></div>
                        <div>
                            <h2 class="status-title"><?= $overall_status_title; ?></h2>
                            <p class="status-desc"><?= $overall_status_desc; ?></p>
                        </div>
                    </div>

                    <!-- Shelf Location Spotlight Grid -->
                    <div class="location-spotlight-grid">
                        
                        <!-- Box 1: Nomor Panggil (Book Spine / Shelf Callout) -->
                        <div class="spotlight-box spotlight-callno">
                            <div class="spotlight-label">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1" aria-hidden="true"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/></svg>
                                <span>Nomor Panggil di Rak</span>
                            </div>
                            <div class="spotlight-value-wrap">
                                <span class="spotlight-callno-badge" id="spotlight-call-text">
                                    <?= !empty($call_number) ? htmlspecialchars($call_number) : '-'; ?>
                                </span>
                                <?php if (!empty($call_number)): ?>
                                    <button type="button" class="btn-mini-copy" id="btn-copy-callno" title="Salin nomor panggil ini" aria-label="Salin nomor panggil">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                                    </button>
                                <?php endif; ?>
                            </div>
                            <span class="spotlight-hint">Cari kode ini pada label punggung buku di rak.</span>
                        </div>

                        <!-- Box 2: Lokasi Perpustakaan / Ruangan -->
                        <div class="spotlight-box spotlight-room">
                            <div class="spotlight-label">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1 text-emerald-600" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                <span>Lokasi Fisik</span>
                            </div>
                            <div class="spotlight-room-name">
                                <?= $primary_location; ?>
                            </div>
                            <span class="spotlight-hint">MAN 1 Pangandaran &bull; Laboratorium Literasi</span>
                        </div>

                        <!-- Box 3: Aturan Peminjaman Siswa -->
                        <div class="spotlight-box spotlight-rules">
                            <div class="spotlight-label">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1 text-blue-600" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <span>Masa Peminjaman</span>
                            </div>
                            <div class="spotlight-rules-text">
                                <?= ($reference_copies > 0 && $available_copies === 0) ? 'Hanya Baca di Tempat' : '7 Hari Kalender'; ?>
                            </div>
                            <span class="spotlight-hint"><?= ($reference_copies > 0 && $available_copies === 0) ? 'Bahan rujukan / tandon' : 'Dapat diperpanjang 1 kali jika tidak dipesan'; ?></span>
                        </div>

                    </div>

                    <!-- Physical Copies Table (Daftar Eksemplar Fisik) -->
                    <?php if (!empty($copy_items)): ?>
                        <div class="copies-section mt-4">
                            <div class="copies-header d-flex align-items-center justify-content-between mb-2">
                                <h3 class="copies-title">
                                    Daftar Eksemplar Buku (<?= $total_copies; ?> Fisik)
                                </h3>
                                <?php if ($total_copies > 4): ?>
                                    <button type="button" id="btn-toggle-copies" class="btn-toggle-copies">
                                        <span id="toggle-copies-text">Lihat Semua (<?= $total_copies; ?>)</span>
                                    </button>
                                <?php endif; ?>
                            </div>

                            <div class="copies-table-wrap">
                                <table class="copies-table" aria-label="Daftar eksemplar fisik buku">
                                    <thead>
                                        <tr>
                                            <th scope="col">Kode Barcode</th>
                                            <th scope="col">Nomor Panggil</th>
                                            <th scope="col">Lokasi / Ruang</th>
                                            <th scope="col" class="text-right">Status Ketersediaan</th>
                                        </tr>
                                    </thead>
                                    <tbody id="copies-table-body">
                                        <?php foreach ($copy_items as $idx => $item): ?>
                                            <tr class="copy-row <?= $idx >= 4 ? 'copy-row-extra d-none' : ''; ?>">
                                                <td class="copy-code">
                                                    <code><?= $item['item_code']; ?></code>
                                                </td>
                                                <td class="copy-call">
                                                    <?= $item['call_number']; ?>
                                                </td>
                                                <td class="copy-loc">
                                                    <?= $item['location']; ?>
                                                </td>
                                                <td class="copy-status text-right">
                                                    <?php if ($item['status_type'] === 'available'): ?>
                                                        <span class="badge-status badge-available">
                                                            <span class="badge-dot bg-emerald-500"></span> Tersedia
                                                        </span>
                                                    <?php elseif ($item['status_type'] === 'loaned'): ?>
                                                        <span class="badge-status badge-loaned" title="<?= $item['status_badge']; ?>">
                                                            <span class="badge-dot bg-amber-500"></span> Dipinjam
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge-status badge-reference">
                                                            <span class="badge-dot bg-blue-500"></span> Baca di Tempat
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

                </section>

                <!-- 3. SINOPSIS & RINGKASAN BUKU ────────────────────────────── -->
                <section class="book-notes-section mb-6">
                    <h2 class="section-heading mb-3">Sinopsis / Ringkasan Buku</h2>
                    <div class="book-synopsis-body">
                        <?php if (!empty($notes)): ?>
                            <?= $notes; ?>
                        <?php else: ?>
                            <p class="text-muted font-italic mb-0">Belum ada sinopsis atau deskripsi untuk judul buku ini.</p>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- 4. INFORMASI PENTING BUKU (STUDENT-RELEVANT METADATA) ─────── -->
                <section class="book-meta-section mb-6">
                    <h2 class="section-heading mb-3">Informasi Buku</h2>
                    
                    <div class="meta-info-grid">
                        
                        <!-- Penerbit & Kota -->
                        <?php if (!empty($publisher_name) || !empty($publish_place)): ?>
                            <div class="meta-item">
                                <span class="meta-label">Penerbit</span>
                                <span class="meta-value">
                                    <?= htmlspecialchars($publisher_name); ?>
                                    <?= !empty($publish_place) ? ' &bull; ' . htmlspecialchars($publish_place) : ''; ?>
                                </span>
                            </div>
                        <?php endif; ?>

                        <!-- Tahun Terbit -->
                        <?php if (!empty($publish_year)): ?>
                            <div class="meta-item">
                                <span class="meta-label">Tahun Terbit</span>
                                <span class="meta-value"><?= htmlspecialchars($publish_year); ?></span>
                            </div>
                        <?php endif; ?>

                        <!-- ISBN / ISSN -->
                        <?php if (!empty($isbn_issn) && $isbn_issn !== '-'): ?>
                            <div class="meta-item">
                                <span class="meta-label">ISBN / ISSN</span>
                                <span class="meta-value font-mono"><?= htmlspecialchars($isbn_issn); ?></span>
                            </div>
                        <?php endif; ?>

                        <!-- Bahasa -->
                        <?php if (!empty($language_name)): ?>
                            <div class="meta-item">
                                <span class="meta-label">Bahasa</span>
                                <span class="meta-value"><?= htmlspecialchars($language_name); ?></span>
                            </div>
                        <?php endif; ?>

                        <!-- Deskripsi Fisik / Halaman -->
                        <?php if (!empty($collation) && $collation !== '-'): ?>
                            <div class="meta-item">
                                <span class="meta-label">Halaman &amp; Dimensi</span>
                                <span class="meta-value"><?= htmlspecialchars($collation); ?></span>
                            </div>
                        <?php endif; ?>

                        <!-- Klasifikasi DDC -->
                        <?php if (!empty($classification) && $classification !== '-'): ?>
                            <div class="meta-item">
                                <span class="meta-label">Klasifikasi DDC</span>
                                <span class="meta-value font-mono"><?= htmlspecialchars($classification); ?></span>
                            </div>
                        <?php endif; ?>

                        <!-- Edisi (Jika ada) -->
                        <?php if (!empty($edition) && $edition !== '-'): ?>
                            <div class="meta-item">
                                <span class="meta-label">Edisi</span>
                                <span class="meta-value"><?= htmlspecialchars($edition); ?></span>
                            </div>
                        <?php endif; ?>

                    </div>
                </section>

                <!-- 5. BUKU TERKAIT / EDISI LAIN (HANYA TAMPIL JIKA ADA) ──────── -->
                <?php if (!empty($related) && stripos($related, 'No other') === false): ?>
                    <section class="book-related-section mb-6">
                        <h2 class="section-heading mb-3">Edisi / Versi Lainnya</h2>
                        <div class="related-books-wrap">
                            <?= $related; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- 6. ULASAN PEMUSTAKA / KOMENTAR (JIKA AKTIF) ───────────────── -->
                <?php if (isset($sysconf['comment']['enable']) && $sysconf['comment']['enable']): ?>
                    <section class="book-comment-section mb-6">
                        <h2 class="section-heading mb-3">Ulasan Pemustaka</h2>
                        <div class="comment-container">
                            <?php echo showComment($biblio_id); ?>
                            <?php if (!isset($_SESSION['mid'])): ?>
                                <div class="comment-login-cta mt-3 p-3 bg-slate-50 border border-slate-200 rounded-xl d-flex align-items-center justify-content-between">
                                    <span class="text-sm text-slate-600">Ingin memberikan ulasan atau rekomendasi buku ini?</span>
                                    <a href="index.php?p=member" class="btn btn-sm btn-outline-dark font-weight-bold">Masuk sebagai Pemustaka</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </section>
                <?php endif; ?>

            </main>

        </div>

    </div>
</div>

<!-- ── DETAIL TEMPLATE STYLES (PURE CSS & DESIGN SYSTEM ALIGNED) ────────── -->
<style>
/* CSS Tokens & Root Canvas */
:root {
    --detail-bg: #FFFFFF;
    --detail-border: #E2E8F0;
    --detail-slate-900: #0F172A;
    --detail-slate-800: #1E293B;
    --detail-slate-700: #334155;
    --detail-slate-600: #475569;
    --detail-slate-500: #64748B;
    --detail-gold-primary: #D4AF37;
    --detail-radius-lg: 16px;
    --detail-radius-xl: 20px;
    --detail-radius-pill: 9999px;
    --detail-ease: cubic-bezier(0.16, 1, 0.3, 1);
}

.iqra-detail-wrapper {
    background-color: transparent;
    color: var(--detail-slate-800);
}

/* Breadcrumb Navigation */
.book-breadcrumb {
    font-size: 0.84375rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid var(--detail-border);
}
.breadcrumb-list {
    gap: 0.5rem;
    color: var(--detail-slate-500);
}
.breadcrumb-link {
    color: var(--detail-slate-600);
    text-decoration: none;
    transition: color 0.15s ease;
}
.breadcrumb-link:hover {
    color: var(--detail-slate-900);
    text-decoration: underline;
}
.breadcrumb-sep {
    color: #94A3B8;
    user-select: none;
}
.breadcrumb-current {
    color: var(--detail-slate-900);
    font-weight: 600;
}
.btn-back-catalog {
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--detail-slate-600);
    background: #FFFFFF;
    border: 1px solid var(--detail-border);
    border-radius: var(--detail-radius-pill);
    padding: 0.35rem 0.85rem;
    text-decoration: none !important;
    transition: all 0.2s ease;
}
.btn-back-catalog:hover {
    background: #F8FAFC;
    color: var(--detail-slate-900);
    border-color: #CBD5E1;
    transform: translateX(-2px);
}

/* 2-Column Responsive Layout */
.book-detail-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 2rem;
    align-items: start;
}
@media (min-width: 992px) {
    .book-detail-grid {
        grid-template-columns: 320px 1fr;
        gap: 2.5rem;
    }
}

/* Cover Image Card */
.book-cover-card {
    background: #FFFFFF;
    border: 1px solid var(--detail-border);
    border-radius: var(--detail-radius-xl);
    padding: 1.25rem;
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08);
    text-align: center;
}
.book-cover-wrap {
    position: relative;
    display: inline-block;
    max-width: 100%;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.12);
}
.book-cover-wrap img {
    display: block;
    max-width: 100%;
    height: auto;
    max-height: 420px;
    object-fit: cover;
    margin: 0 auto;
    border-radius: 8px;
}
.book-gmd-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background: rgba(15, 23, 42, 0.85);
    color: #FFFFFF;
    font-size: 0.71875rem;
    font-weight: 600;
    padding: 0.2rem 0.6rem;
    border-radius: var(--detail-radius-pill);
    backdrop-filter: blur(4px);
    letter-spacing: 0.02em;
}

/* Action Buttons */
.book-actions-card {
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
}
.action-btn-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
    padding: 0.6rem 1rem;
    border-radius: 12px;
    font-size: 0.875rem;
    font-weight: 600;
    text-decoration: none !important;
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
}
.btn-outline-action {
    background: #FFFFFF;
    border: 1px solid var(--detail-border) !important;
    color: var(--detail-slate-700);
}
.btn-outline-action:hover {
    background: #F8FAFC;
    border-color: #CBD5E1 !important;
    color: var(--detail-slate-900);
    transform: translateY(-1px);
}
.btn-basket-action {
    background: var(--detail-slate-900);
    color: #FFFFFF !important;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.15);
}
.btn-basket-action:hover {
    background: var(--detail-slate-800);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.2);
}
.btn-copy-info {
    background: #F8FAFC;
    border: 1px dashed #CBD5E1 !important;
    color: var(--detail-slate-600);
    font-size: 0.8125rem;
}
.btn-copy-info:hover {
    background: #F1F5F9;
    color: var(--detail-slate-900);
}

/* Digital File Box */
.digital-file-box {
    background: #F0FDF4;
    border: 1px solid #BBF7D0;
    border-radius: var(--detail-radius-lg);
    padding: 1rem;
}
.digital-file-header {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    margin-bottom: 0.5rem;
}
.digital-file-title {
    font-size: 0.84375rem;
    font-weight: 700;
    color: #166534;
}
.digital-file-content ul {
    margin-bottom: 0;
    padding-left: 0;
}
.digital-file-content li {
    font-size: 0.8125rem;
}

/* Student Tip Card */
.student-tip-card {
    background: #FFFBEB;
    border: 1px solid #FDE68A;
    border-radius: var(--detail-radius-lg);
    padding: 0.95rem 1.15rem;
}
.student-tip-header {
    display: flex;
    align-items: center;
    margin-bottom: 0.35rem;
}
.student-tip-title {
    font-size: 0.8125rem;
    font-weight: 700;
    color: #92400E;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.student-tip-text {
    font-size: 0.8125rem;
    color: #78350F;
    line-height: 1.5;
    margin-bottom: 0;
}

/* Main Content Header */
.book-tags-list a {
    display: inline-block;
    padding: 0.2rem 0.65rem;
    background: #F1F5F9;
    color: var(--detail-slate-700);
    border: 1px solid var(--detail-border);
    border-radius: var(--detail-radius-pill);
    font-size: 0.75rem;
    font-weight: 600;
    text-decoration: none;
    margin-right: 0.35rem;
    margin-bottom: 0.35rem;
    transition: all 0.15s ease;
}
.book-tags-list a:hover {
    background: var(--detail-slate-900);
    color: #FFFFFF;
    border-color: var(--detail-slate-900);
}
.book-main-title {
    font-size: 1.65rem;
    font-weight: 800;
    color: var(--detail-slate-900);
    letter-spacing: -0.025em;
    line-height: 1.25;
    margin-bottom: 0.45rem;
}
@media (min-width: 768px) {
    .book-main-title {
        font-size: 2.15rem;
    }
}
.book-series-title {
    font-size: 0.95rem;
    color: var(--detail-slate-600);
    margin-bottom: 0.5rem;
}
.book-authors-line {
    font-size: 0.9375rem;
    color: var(--detail-slate-600);
}
.authors-label {
    font-weight: 600;
    margin-right: 0.35rem;
    color: #64748B;
}
.authors-names a {
    color: var(--detail-slate-900);
    font-weight: 600;
    text-decoration: none;
}
.authors-names a:hover {
    text-decoration: underline;
    color: var(--detail-gold-primary);
}

/* ── HERO AVAILABILITY & SHELF LOCATION SPOTLIGHT ───────────────────────── */
.shelf-location-card {
    background: #FFFFFF;
    border: 1.5px solid var(--detail-border);
    border-radius: var(--detail-radius-xl);
    padding: 1.4rem;
    box-shadow: 0 4px 15px -2px rgba(15, 23, 42, 0.05);
}

/* Status Banner */
.location-status-banner {
    display: flex;
    align-items: flex-start;
    gap: 0.85rem;
    padding: 0.85rem 1.15rem;
    border: 1px solid;
    border-radius: 12px;
    margin-bottom: 1.25rem;
}
.status-indicator-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin-top: 5px;
    flex-shrink: 0;
}
.status-available .status-indicator-dot {
    background-color: #10B981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
}
.status-loaned .status-indicator-dot {
    background-color: #F59E0B;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2);
}
.status-reference .status-indicator-dot {
    background-color: #3B82F6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
}
.status-nodata .status-indicator-dot {
    background-color: #94A3B8;
}
.status-title {
    font-size: 1rem;
    font-weight: 700;
    margin: 0 0 0.15rem 0;
    line-height: 1.35;
}
.status-desc {
    font-size: 0.8125rem;
    margin: 0;
    opacity: 0.9;
    line-height: 1.45;
}

/* Spotlight 3-Box Grid */
.location-spotlight-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 0.85rem;
}
@media (min-width: 640px) {
    .location-spotlight-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}
.spotlight-box {
    background: #F8FAFC;
    border: 1px solid var(--detail-border);
    border-radius: 12px;
    padding: 0.95rem;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.spotlight-callno {
    background: #FFFDF5;
    border-color: #FDE68A;
}
.spotlight-label {
    display: flex;
    align-items: center;
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--detail-slate-600);
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin-bottom: 0.45rem;
}
.spotlight-value-wrap {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    margin-bottom: 0.35rem;
}
.spotlight-callno-badge {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: 1.05rem;
    font-weight: 800;
    color: #92400E;
    background: #FEF3C7;
    border: 1px solid #FCD34D;
    padding: 0.25rem 0.65rem;
    border-radius: 6px;
    letter-spacing: -0.01em;
}
.btn-mini-copy {
    background: #FFFFFF;
    border: 1px solid #FCD34D;
    color: #B45309;
    padding: 4px 6px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.15s ease;
}
.btn-mini-copy:hover {
    background: #FEF3C7;
    transform: scale(1.05);
}
.spotlight-room-name {
    font-size: 0.9375rem;
    font-weight: 700;
    color: var(--detail-slate-900);
    margin-bottom: 0.35rem;
    line-height: 1.35;
}
.spotlight-rules-text {
    font-size: 0.9375rem;
    font-weight: 700;
    color: var(--detail-slate-900);
    margin-bottom: 0.35rem;
}
.spotlight-hint {
    font-size: 0.71875rem;
    color: var(--detail-slate-500);
    line-height: 1.4;
}

/* Copies Table */
.copies-section {
    border-top: 1px dashed var(--detail-border);
    padding-top: 1rem;
}
.copies-title {
    font-size: 0.875rem;
    font-weight: 700;
    color: var(--detail-slate-800);
    margin: 0;
}
.btn-toggle-copies {
    background: transparent;
    border: none;
    color: #0284C7;
    font-size: 0.78125rem;
    font-weight: 600;
    cursor: pointer;
    padding: 0;
}
.btn-toggle-copies:hover {
    text-decoration: underline;
}
.copies-table-wrap {
    overflow-x: auto;
    border: 1px solid var(--detail-border);
    border-radius: 10px;
    background: #FFFFFF;
}
.copies-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.8125rem;
}
.copies-table th {
    background: #F8FAFC;
    color: var(--detail-slate-600);
    font-weight: 600;
    padding: 0.6rem 0.85rem;
    border-bottom: 1px solid var(--detail-border);
    text-align: left;
}
.copies-table td {
    padding: 0.65rem 0.85rem;
    border-bottom: 1px solid #F1F5F9;
    vertical-align: middle;
}
.copies-table tr:last-child td {
    border-bottom: none;
}
.copy-code code {
    background: #F1F5F9;
    color: var(--detail-slate-800);
    padding: 0.2rem 0.45rem;
    border-radius: 4px;
    font-size: 0.78125rem;
}
.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.25rem 0.6rem;
    border-radius: var(--detail-radius-pill);
    font-size: 0.71875rem;
    font-weight: 600;
}
.badge-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
}
.badge-available {
    background: #ECFDF5;
    color: #065F46;
    border: 1px solid #A7F3D0;
}
.badge-loaned {
    background: #FFFBEB;
    color: #92400E;
    border: 1px solid #FDE68A;
}
.badge-reference {
    background: #EFF6FF;
    color: #1E40AF;
    border: 1px solid #BFDBFE;
}

/* General Section Headings */
.section-heading {
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--detail-slate-900);
    letter-spacing: -0.015em;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid var(--detail-border);
}

/* Synopsis Body */
.book-synopsis-body {
    font-size: 0.9375rem;
    color: var(--detail-slate-700);
    line-height: 1.75;
}
.book-synopsis-body p {
    margin-bottom: 0.75rem;
}

/* Metadata Info Grid */
.meta-info-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 0.75rem;
}
@media (min-width: 640px) {
    .meta-info-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (min-width: 992px) {
    .meta-info-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}
.meta-item {
    background: #F8FAFC;
    border: 1px solid var(--detail-border);
    border-radius: 10px;
    padding: 0.75rem 0.95rem;
    display: flex;
    flex-direction: column;
}
.meta-label {
    font-size: 0.71875rem;
    font-weight: 700;
    color: var(--detail-slate-500);
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin-bottom: 0.2rem;
}
.meta-value {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--detail-slate-900);
}
.font-mono {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
}
</style>

<!-- ── INLINE JAVASCRIPT FOR DETAIL ACTIONS ────────────────────────────── -->
<script>
(function() {
    'use strict';

    function initBookDetailUX() {
        // 1. Toggle Extra Copies (> 4 copies)
        var toggleBtn = document.getElementById('btn-toggle-copies');
        var toggleText = document.getElementById('toggle-copies-text');
        var extraRows = document.querySelectorAll('.copy-row-extra');
        var isExpanded = false;

        if (toggleBtn && extraRows.length > 0) {
            toggleBtn.addEventListener('click', function() {
                isExpanded = !isExpanded;
                extraRows.forEach(function(row) {
                    if (isExpanded) {
                        row.classList.remove('d-none');
                    } else {
                        row.classList.add('d-none');
                    }
                });
                toggleText.textContent = isExpanded ? 'Sembunyikan' : 'Lihat Semua (' + (extraRows.length + 4) + ')';
            });
        }

        // 2. 1-Click Copy Call Number & Book Info
        function copyText(text, successMsg) {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(function() {
                    if (window.toastr) {
                        toastr.success(successMsg);
                    } else {
                        alert(successMsg);
                    }
                }).catch(function() {
                    fallbackCopy(text, successMsg);
                });
            } else {
                fallbackCopy(text, successMsg);
            }
        }

        function fallbackCopy(text, successMsg) {
            var temp = document.createElement('input');
            temp.value = text;
            document.body.appendChild(temp);
            temp.select();
            document.execCommand('copy');
            document.body.removeChild(temp);
            if (window.toastr) {
                toastr.success(successMsg);
            } else {
                alert(successMsg);
            }
        }

        // Copy Call Number
        var copyCallBtn = document.getElementById('btn-copy-callno');
        var callTextEl = document.getElementById('spotlight-call-text');
        if (copyCallBtn && callTextEl) {
            copyCallBtn.addEventListener('click', function() {
                var callNo = callTextEl.textContent.trim();
                copyText(callNo, 'Nomor Panggil disalin: ' + callNo);
            });
        }

        // Copy Full Book Summary
        var copyInfoBtn = document.getElementById('btn-copy-book-info');
        if (copyInfoBtn) {
            copyInfoBtn.addEventListener('click', function() {
                var bTitle = copyInfoBtn.getAttribute('data-title');
                var bCall = copyInfoBtn.getAttribute('data-call');
                var summary = bTitle + ' (No. Panggil: ' + bCall + ') - Perpustakaan MAN 1 Pangandaran';
                copyText(summary, 'Info buku berhasil disalin!');
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBookDetailUX);
    } else {
        initBookDetailUX();
    }
})();
</script>