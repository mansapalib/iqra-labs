<?php
/**
 * =========================================================================
 * MANSAPA Iqra' Labs — opac_template.php [Dedicated OPAC Book Search Page]
 * Place: template/iqra-labs/opac_template.php
 *
 * Standalone dedicated OPAC page — no shared navbar or footer.
 * Pattern mirrors visitor_template.php: owns its full HTML document.
 *
 * Fixes applied:
 * 1. Advanced Search modal now INLINED (was never rendered — footer skipped)
 * 2. Carousel ← → buttons now have working JS scroll logic
 * 3. Book recommendations use getPopularBiblio() — ranked by borrow count
 * =========================================================================
 */

/** @var array $sysconf */
global $sysconf, $dbs;

// ── USUL BUKU: Backend Handler (Connected to plugin Manajemen Usul Buku OPAC) ─
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'simpan_usul_buku') {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    header('Content-Type: application/json; charset=utf-8');

    if (!isset($dbs) || !$dbs) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Koneksi database tidak tersedia.'
        ]);
        exit;
    }

    // Pastikan tabel usul_buku ada (sesuai struktur plugin Manajemen Usul Buku OPAC)
    $dbs->query("CREATE TABLE IF NOT EXISTS `usul_buku` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `judul` varchar(255) NOT NULL,
        `pengarang` varchar(255) DEFAULT NULL,
        `penerbit` varchar(255) DEFAULT NULL,
        `nama` varchar(255) NOT NULL,
        `identitas` varchar(100) DEFAULT NULL,
        `alasan` text DEFAULT NULL,
        `status` varchar(20) NOT NULL DEFAULT 'pending',
        `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $judul     = trim(strip_tags($_POST['judul'] ?? ''));
    $nama      = trim(strip_tags($_POST['nama'] ?? ''));
    $pengarang = trim(strip_tags($_POST['pengarang'] ?? ''));
    $penerbit  = trim(strip_tags($_POST['penerbit'] ?? ''));
    $tahun     = trim(strip_tags($_POST['tahunterbit'] ?? ''));
    $identitas = trim(strip_tags($_POST['identitas'] ?? ''));
    $alasan    = trim(strip_tags($_POST['alasan'] ?? ''));

    if (empty($judul) || empty($nama)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Judul buku dan nama pengusul wajib diisi.'
        ]);
        exit;
    }

    if (!empty($tahun)) {
        $penerbit = !empty($penerbit) ? "{$penerbit} ({$tahun})" : "Tahun: {$tahun}";
    }

    $inserted = false;

    // Gunakan prepared statement jika tersedia
    $stmt = $dbs->prepare("INSERT INTO usul_buku (judul, pengarang, penerbit, nama, identitas, alasan, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())");
    if ($stmt) {
        $stmt->bind_param('ssssss', $judul, $pengarang, $penerbit, $nama, $identitas, $alasan);
        $inserted = $stmt->execute();
        $stmt->close();
    } else {
        $judul_esc     = $dbs->escape_string($judul);
        $pengarang_esc = $dbs->escape_string($pengarang);
        $penerbit_esc  = $dbs->escape_string($penerbit);
        $nama_esc      = $dbs->escape_string($nama);
        $identitas_esc = $dbs->escape_string($identitas);
        $alasan_esc    = $dbs->escape_string($alasan);
        $sql = "INSERT INTO usul_buku (judul, pengarang, penerbit, nama, identitas, alasan, status, created_at)
                VALUES ('{$judul_esc}', '{$pengarang_esc}', '{$penerbit_esc}', '{$nama_esc}', '{$identitas_esc}', '{$alasan_esc}', 'pending', NOW())";
        $inserted = (bool)$dbs->query($sql);
    }

    if ($inserted) {
        echo json_encode([
            'status'  => 'success',
            'message' => 'Terima kasih! Usulan buku berhasil dikirim dan tersimpan di sistem perpustakaan.'
        ]);
    } else {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal menyimpan usulan: ' . ($dbs->error ?: 'Terjadi kesalahan basis data.')
        ]);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?= substr($sysconf['default_lang'] ?? 'id', 0, 2); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>OPAC &mdash; MANSAPA Iqra' Labs</title>
    <meta name="description" content="Katalog Online (OPAC) Perpustakaan MANSAPA Iqra' Labs &mdash; Cari, temukan, dan usulkan koleksi buku.">
    <meta name="theme-color" content="#0D1B3D">

    <!-- Favicon -->
    <link rel="shortcut icon" href="<?= SWB; ?>webicon.ico" type="image/x-icon">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,400;1,600&family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800;900&display=swap">

    <!-- Font Awesome (Local SLiMS + CDN Fallback) -->
    <link rel="stylesheet" href="<?= assets('plugin/font-awesome/css/fontawesome-all.min.css'); ?>">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
          crossorigin="anonymous" referrerpolicy="no-referrer">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="<?= assets('css/bootstrap.min.css'); ?>">

    <!-- Toastr -->
    <link rel="stylesheet" href="<?= JWB; ?>toastr/toastr.min.css">

    <!-- MANSAPA Iqra' Labs Brand Stylesheet -->
    <link rel="stylesheet" href="<?= assets('css/style.css?v=' . date('YmdH')); ?>">

    <!-- JS (head-loaded so Bootstrap modals work immediately) -->
    <script src="<?= assets('js/jquery.min.js'); ?>"></script>
    <script src="<?= assets('js/bootstrap.bundle.min.js'); ?>"></script>
    <script src="<?= JWB; ?>toastr/toastr.min.js"></script>
</head>
<body id="iqra-body">
<?php

// ── RECOMMENDED BOOKS: most borrowed via getPopularBiblio() ─────────────────
// getPopularBiblio() joins loan → item → biblio, orders by borrow count DESC.
// Falls back to most-recently-updated books if loan table is empty.
$recommendedBooks = [];

// Use getPopularBiblio() (classic.php) — ranks by actual borrow count via
// loan JOIN item JOIN biblio. Returns up to 16 most-borrowed titles with
// graceful fallback to recently-updated records when loan table is sparse.
if (isset($dbs) && function_exists('getPopularBiblio')) {
    $popular = getPopularBiblio($dbs, 16);
    // First pass: prefer items that have a cover image
    foreach ($popular as $row) {
        if (empty($row['image'])) continue;
        $coverUrl = SWB . 'lib/minigalnano/createthumb.php?filename=images/docs/' . urlencode($row['image']) . '&width=260';
        $recommendedBooks[] = [
            'id'    => $row['biblio_id'],
            'title' => $row['title'],
            'image' => $coverUrl,
            'url'   => 'index.php?p=show_detail&id=' . $row['biblio_id'],
        ];
    }
    // Second pass: include no-image books if we still need to fill the rail
    if (count($recommendedBooks) < 8) {
        foreach ($popular as $row) {
            if (!empty($row['image'])) continue;
            $recommendedBooks[] = [
                'id'    => $row['biblio_id'],
                'title' => $row['title'],
                'image' => 'https://ui-avatars.com/api/?name=' . urlencode($row['title']) . '&background=0D1B3D&color=D4AF37&size=300',
                'url'   => 'index.php?p=show_detail&id=' . $row['biblio_id'],
            ];
        }
    }
} elseif (isset($dbs)) {
    // Fallback: getPopularBiblio not available — use most-recent with covers
    $recSql = "SELECT biblio_id, title, image
               FROM biblio
               WHERE image IS NOT NULL AND image != ''
               ORDER BY last_update DESC
               LIMIT 16";
    $recQuery = $dbs->query($recSql);
    if ($recQuery) {
        while ($row = $recQuery->fetch_assoc()) {
            $coverUrl = SWB . 'lib/minigalnano/createthumb.php?filename=images/docs/' . urlencode($row['image']) . '&width=260';
            $recommendedBooks[] = [
                'id'    => $row['biblio_id'],
                'title' => $row['title'],
                'image' => $coverUrl,
                'url'   => 'index.php?p=show_detail&id=' . $row['biblio_id'],
            ];
        }
    }
}

// Curated static fallback — fills carousel when live collection has < 8 covers
$fallbackCurated = [
    ['title' => 'Menanti Restu Langit',           'image' => SWB . 'images/docs/cover_moga-bunda-disayang-allah-20260827075708.png',            'query' => 'Restu Langit'],
    ['title' => 'Perspektif Pendidikan',           'image' => SWB . 'images/docs/cover_pendidikan-pancasila-sma-ma-kelas-x-20260827134320.png',  'query' => 'Pendidikan'],
    ['title' => 'Seni Menikmati Kesendirian',      'image' => SWB . 'images/docs/cover_sendiri-20260827075547.png',                              'query' => 'Kesendirian'],
    ['title' => 'Pulang',                          'image' => SWB . 'images/docs/cover_pulang-20260827075255.png',                               'query' => 'Pulang'],
    ['title' => 'Untuk Negeriku',                  'image' => SWB . 'images/docs/cover_etika-dan-logika-berpolitik-20260827075008.png',           'query' => 'Untuk Negeriku'],
    ['title' => 'Madilog',                         'image' => SWB . 'images/docs/cover_kajian-filosofis-pendidikan-barat-dan-islam-20260827135224.png', 'query' => 'Madilog'],
    ['title' => 'Tenggelamnya Kapal Van der Wijck','image' => SWB . 'images/docs/cover_cinta-antara-jakarta-kuala-lumpur-20260827075204.png',    'query' => 'Van der Wijck'],
    ['title' => 'Hujan',                           'image' => SWB . 'images/docs/cover_hujan-20260827075437.png',                                'query' => 'Hujan'],
    ['title' => 'Bumi',                            'image' => SWB . 'images/docs/cover_bumi-20260827080053.png',                                 'query' => 'Bumi'],
    ['title' => 'Bulan',                           'image' => SWB . 'images/docs/cover_bulan-20260827080650.png',                                'query' => 'Bulan'],
];

foreach ($fallbackCurated as $f) {
    if (count($recommendedBooks) >= 12) break;
    $recommendedBooks[] = [
        'id'    => '',
        'title' => $f['title'],
        'image' => $f['image'],
        'url'   => 'index.php?search=search&keywords=' . urlencode($f['query']),
    ];
}
?>

<main class="opac-page-hero" id="opacSearchPage" role="main" aria-label="Halaman Pencarian Buku OPAC">

    <!-- ── ABSTRACT GEOMETRIC BACKGROUND ───────────────────────────── -->
    <div class="opac-bg-canvas" aria-hidden="true">
        <div class="opac-geo-shape geo-1"></div>
        <div class="opac-geo-shape geo-2"></div>
        <div class="opac-geo-shape geo-3"></div>
        <div class="opac-geo-shape geo-4"></div>
        <div class="opac-geo-shape geo-5"></div>
        <div class="opac-geo-shape geo-6"></div>
    </div>

    <!-- ── TOP ACTION & BRAND BAR (Reference UI) ───────────────────── -->
    <header class="opac-top-bar-wrap">
        <div class="container iqra-container">
            <div class="opac-top-bar d-flex justify-content-between align-items-center">
                
                <!-- Left: Brand mark (links back to main page) -->
                <a href="index.php" class="opac-ref-brand d-flex align-items-center gap-2 text-decoration-none" aria-label="<?= htmlspecialchars($sysconf['library_name'] ?? 'MANSAPA Iqra Labs'); ?> — Beranda">
                    <div class="opac-brand-icon-box" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    </div>
                    <div class="opac-brand-titles">
                        <span class="opac-brand-main d-block"><?= htmlspecialchars($sysconf['library_name'] ?? 'MAN 1 Pangandaran'); ?></span>
                        <span class="opac-brand-sub d-block">Iqra&rsquo; Labs</span>
                    </div>
                </a>

                <!-- Right: Usul Buku Button -->
                <div class="opac-top-actions">
                    <button type="button" 
                            class="btn-usul-buku" 
                            data-toggle="modal" 
                            data-target="#usulBukuModal"
                            aria-label="Usulkan buku baru ke perpustakaan">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 7px;">
                            <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/>
                            <path d="M10 2v10l3-2.5 3 2.5V2"/>
                        </svg>
                        <span>Usul Buku</span>
                    </button>
                </div>

            </div>
        </div>
    </header>

    <!-- ── HERO CENTER: tagline + search bar (vertically centered) ───────── -->
    <div class="opac-hero-center">
        <div class="container iqra-container d-flex flex-column align-items-center">

            <!-- Headline -->
            <h1 class="opac-hero-tagline text-center">Temukan <span>Buku</span> yang<br>Ingin Kamu Baca</h1>
            <p class="opac-hero-sub text-center">Jelajahi ribuan koleksi perpustakaan &mdash; dari fiksi hingga referensi ilmiah.</p>

            <!-- Massive Pill Search Bar -->
            <div class="opac-search-container">
                <div class="opac-search-center-wrap">
                    <form action="index.php" method="get" class="opac-hero-search-form" role="search">
                        <input type="hidden" name="search" value="search">
                        <div class="opac-hero-search-pill">
                            <input type="text"
                                   name="keywords"
                                   id="opac-hero-input"
                                   class="opac-hero-search-input"
                                   placeholder="Buku apa yang ingin kamu cari ?"
                                   value="<?= isset($_GET['keywords']) ? htmlspecialchars($_GET['keywords'], ENT_QUOTES, 'UTF-8') : ''; ?>"
                                   autocomplete="off"
                                   autofocus
                                   aria-label="Buku apa yang ingin kamu cari ?">
                            <button type="submit" class="opac-hero-search-btn" aria-label="Cari koleksi">
                                <img src="<?= assets('images/icon/ios7-search.png'); ?>" alt="" class="opac-search-icon-img" aria-hidden="true">
                            </button>
                        </div>
                    </form>

                    <!-- Advanced Search trigger -->
                    <div class="opac-adv-link-box">
                        <a href="#"
                           class="opac-adv-link"
                           data-toggle="modal"
                           data-target="#adv-modal"
                           aria-haspopup="dialog">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 5px; vertical-align: -1px;">
                                <line x1="4" y1="21" x2="4" y2="14"/>
                                <line x1="4" y1="10" x2="4" y2="3"/>
                                <line x1="12" y1="21" x2="12" y2="12"/>
                                <line x1="12" y1="8" x2="12" y2="3"/>
                                <line x1="20" y1="21" x2="20" y2="16"/>
                                <line x1="20" y1="12" x2="20" y2="3"/>
                                <line x1="1" y1="14" x2="7" y2="14"/>
                                <line x1="9" y1="8" x2="15" y2="8"/>
                                <line x1="17" y1="16" x2="23" y2="16"/>
                            </svg>
                            Advanced Search
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div><!-- /.opac-hero-center -->

    <!-- ── REKOMENDASI BUKU (3D CAROUSEL SECTION) ──────────────────── -->
    <section class="opac-rec-section" aria-label="Rekomendasi Buku Terpopuler">
        <div class="container iqra-container">
            
            <!-- Section Header -->
            <div class="opac-rec-head-row">
                <h2 class="opac-rec-heading">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="text-gold me-2" style="display:inline-block; vertical-align:-2px;" aria-hidden="true"><path d="M12 2c.5 3 2.5 5 4 7 1.5 2 2 4.5 1 7-1 2.5-3.5 4-6 4s-5.5-2-5.5-5.5c0-3 2.5-5.5 3.5-7.5 1-2 1.5-3.5 1-5h2z"/></svg>
                    Rekomendasi Buku
                </h2>
                <span class="opac-rec-subhead">Paling banyak dipinjam</span>
            </div>

            <!-- 3D Book Slider Wrapper with Navigation Buttons -->
            <div class="opac-slider-outer">
                
                <!-- Left Arrow Button -->
                <button type="button" 
                        class="opac-slider-nav opac-nav-prev" 
                        id="opacSliderPrev" 
                        aria-label="Geser ke buku sebelumnya">
                    <img src="<?= assets('images/icon/ios7-arrow-thin-right.png'); ?>" alt="Sebelumnya" class="opac-slider-icon-img opac-slider-icon-prev" aria-hidden="true">
                </button>

                <!-- Book Track -->
                <div class="opac-books-carousel" id="opacBooksCarousel" tabindex="0" role="region" aria-label="Daftar Rekomendasi Buku">
                    <?php foreach ($recommendedBooks as $index => $book): ?>
                    <div class="opac-book-item" data-index="<?= $index; ?>">
                        <a href="<?= $book['url']; ?>" class="book-3d-wrap text-decoration-none" title="<?= htmlspecialchars($book['title']); ?>">
                            
                            <!-- 3D Stand-up Book Card -->
                            <div class="book-3d">
                                <img src="<?= $book['image']; ?>" 
                                     alt="Sampul <?= htmlspecialchars($book['title']); ?>"
                                     class="book-3d-cover"
                                     loading="lazy"
                                     onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=<?= urlencode($book['title']); ?>&background=0D1B3D&color=D4AF37&size=300';">
                                <div class="book-spine-lighting" aria-hidden="true"></div>
                                <div class="book-page-edges" aria-hidden="true"></div>
                            </div>

                            <!-- Subtitle tooltip on hover -->
                            <div class="book-3d-caption">
                                <span class="book-3d-title"><?= htmlspecialchars($book['title']); ?></span>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Right Arrow Button -->
                <button type="button" 
                        class="opac-slider-nav opac-nav-next" 
                        id="opacSliderNext" 
                        aria-label="Geser ke buku berikutnya">
                    <img src="<?= assets('images/icon/ios7-arrow-thin-right.png'); ?>" alt="Berikutnya" class="opac-slider-icon-img" aria-hidden="true">
                </button>

            </div><!-- /.opac-slider-outer -->

        </div>
    </section><!-- /.opac-rec-section -->

    <!-- ── COPYRIGHT FOOTER ───────────────────────────────────────── -->
    <footer class="opac-footer" role="contentinfo">
        <p class="opac-footer-copy">
            &copy; <?= date('Y'); ?> <strong>MANSAPA Iqra&rsquo; Labs</strong>
            &mdash; <?= htmlspecialchars($sysconf['library_name'] ?? 'MAN 1 Pangandaran'); ?>
        </p>
    </footer>

</main>


<!-- ================================================================
     MODAL: ADVANCED SEARCH
     Included from shared parts/_modal_advanced.php (Single Source of Truth)
================================================================ -->
<?php include __DIR__ . '/parts/_modal_advanced.php'; ?>



<!-- ================================================================
     MODAL: USUL BUKU (Interactive Book Suggestion - Concise & Clear Redesign)
================================================================ -->
<style>
/* ── MODAL USUL BUKU (Redesigned Compact & Clear with Pure Inline SVGs) ──── */
#usulBukuModal {
    z-index: 1060;
}
#usulBukuModal .modal-dialog {
    max-width: 530px;
    margin: 1.5rem auto;
}
@media (max-width: 576px) {
    #usulBukuModal .modal-dialog {
        margin: 0.5rem;
        max-width: calc(100% - 1rem);
    }
}
.usul-modal-content {
    border: none;
    border-radius: 16px;
    background: #FFFFFF;
    box-shadow: 0 20px 45px -10px rgba(13, 27, 61, 0.3), 0 0 0 1px rgba(13, 27, 61, 0.05);
    overflow: hidden;
}
.usul-modal-header {
    background: linear-gradient(135deg, #0D1B3D 0%, #162B5E 100%);
    border-bottom: 2px solid #D4AF37;
    padding: 0.95rem 1.25rem;
    position: relative;
}
.usul-icon-box {
    width: 36px;
    height: 36px;
    border-radius: 9px;
    background: rgba(212, 175, 55, 0.15);
    border: 1px solid rgba(212, 175, 55, 0.35);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #D4AF37;
    flex-shrink: 0;
    margin-right: 0.75rem;
}
.usul-icon-box svg {
    display: block;
}
.usul-modal-title {
    font-family: var(--font-h, 'Poppins', sans-serif);
    font-weight: 700;
    font-size: 1.02rem;
    color: #FFFFFF;
    margin-bottom: 2px;
    line-height: 1.2;
}
.usul-modal-desc {
    font-family: var(--font-body, 'Inter', sans-serif);
    font-size: 0.76rem;
    color: rgba(255, 255, 255, 0.78);
    margin: 0;
    line-height: 1.25;
}
.usul-close-btn {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    color: rgba(255, 255, 255, 0.85);
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    padding: 0;
    flex-shrink: 0;
    margin-left: auto;
}
.usul-close-btn:hover {
    background: rgba(212, 175, 55, 0.25);
    border-color: #D4AF37;
    color: #FFFFFF;
    transform: rotate(90deg);
}
.usul-close-btn svg {
    display: block;
}
.usul-modal-body {
    padding: 1.05rem 1.25rem 0.65rem;
    background: #FFFFFF;
}
.usul-sec-divider {
    display: flex;
    align-items: center;
    font-family: var(--font-h, 'Poppins', sans-serif);
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #0D1B3D;
    margin-bottom: 0.65rem;
    padding-bottom: 0.3rem;
    border-bottom: 1px dashed #E2E8F0;
}
.usul-sec-divider svg {
    color: #D4AF37;
    margin-right: 0.45rem;
    flex-shrink: 0;
}
.usul-field-group {
    margin-bottom: 0.65rem;
}
.usul-label-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.3rem;
}
.usul-label {
    font-family: var(--font-h, 'Poppins', sans-serif);
    font-size: 0.76rem;
    font-weight: 600;
    color: #1E293B;
    margin: 0;
}
.usul-tag-req {
    font-family: var(--font-body, 'Inter', sans-serif);
    font-size: 0.65rem;
    font-weight: 600;
    color: #DC2626;
    background: #FEF2F2;
    padding: 1px 6px;
    border-radius: 4px;
    border: 1px solid #FECACA;
    line-height: 1.2;
}
.usul-tag-opt {
    font-family: var(--font-body, 'Inter', sans-serif);
    font-size: 0.65rem;
    font-weight: 500;
    color: #64748B;
    background: #F1F5F9;
    padding: 1px 6px;
    border-radius: 4px;
    border: 1px solid #E2E8F0;
    line-height: 1.2;
}
.usul-input-box {
    position: relative;
    display: flex;
    align-items: center;
}
.usul-input-box .input-icon {
    position: absolute;
    left: 0.8rem;
    color: #94A3B8;
    pointer-events: none;
    transition: color 0.2s ease;
    flex-shrink: 0;
}
.usul-input-box:focus-within .input-icon {
    color: #0D1B3D;
}
.usul-control {
    font-family: var(--font-body, 'Inter', sans-serif);
    font-size: 0.82rem;
    color: #0F172A;
    background: #F8FAFC;
    border: 1.5px solid #E2E8F0;
    border-radius: 8px;
    padding-left: 2.3rem;
    padding-right: 0.75rem;
    height: 38px;
    width: 100%;
    transition: all 0.2s ease;
}
.usul-control:focus {
    background: #FFFFFF;
    border-color: #0D1B3D;
    box-shadow: 0 0 0 3px rgba(13, 27, 61, 0.08);
    outline: none;
}
.usul-control::placeholder {
    color: #94A3B8;
    font-size: 0.8rem;
}
.usul-textarea {
    font-family: var(--font-body, 'Inter', sans-serif);
    font-size: 0.82rem;
    color: #0F172A;
    background: #F8FAFC;
    border: 1.5px solid #E2E8F0;
    border-radius: 8px;
    padding: 0.5rem 0.75rem;
    width: 100%;
    resize: none;
    line-height: 1.4;
    transition: all 0.2s ease;
}
.usul-textarea:focus {
    background: #FFFFFF;
    border-color: #0D1B3D;
    box-shadow: 0 0 0 3px rgba(13, 27, 61, 0.08);
    outline: none;
}
.usul-textarea::placeholder {
    color: #94A3B8;
    font-size: 0.8rem;
}
.usul-modal-footer {
    background: #F8FAFC;
    border-top: 1px solid #E2E8F0;
    padding: 0.7rem 1.25rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.usul-footer-hint {
    font-family: var(--font-body, 'Inter', sans-serif);
    font-size: 0.73rem;
    color: #64748B;
    display: flex;
    align-items: center;
}
.usul-footer-hint svg {
    color: #D4AF37;
    margin-right: 0.4rem;
    flex-shrink: 0;
}
.usul-btn-ghost {
    font-family: var(--font-h, 'Poppins', sans-serif);
    font-size: 0.82rem;
    font-weight: 500;
    color: #64748B;
    background: transparent;
    border: 1px solid #CBD5E1;
    border-radius: 8px;
    padding: 0.4rem 1rem;
    cursor: pointer;
    transition: all 0.2s ease;
}
.usul-btn-ghost:hover {
    background: #E2E8F0;
    color: #0F172A;
}
.usul-btn-send {
    font-family: var(--font-h, 'Poppins', sans-serif);
    font-size: 0.82rem;
    font-weight: 600;
    color: #FFFFFF;
    background: #0D1B3D;
    border: 1px solid #0D1B3D;
    border-radius: 8px;
    padding: 0.42rem 1.25rem;
    cursor: pointer;
    box-shadow: 0 3px 10px rgba(13, 27, 61, 0.22);
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
}
.usul-btn-send:hover {
    background: #D4AF37;
    border-color: #D4AF37;
    color: #0D1B3D;
    box-shadow: 0 5px 14px rgba(212, 175, 55, 0.38);
    transform: translateY(-1px);
}
.usul-btn-send svg {
    display: block;
    margin-right: 6px;
    flex-shrink: 0;
}
.usul-btn-send:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}
@keyframes fa-spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
.fa-spin {
    animation: fa-spin 1s infinite linear;
}
</style>

<div class="modal fade" id="usulBukuModal" tabindex="-1" role="dialog" aria-labelledby="usulBukuTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content usul-modal-content">
            
            <!-- Modal Header -->
            <div class="usul-modal-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="usul-icon-box">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/>
                            <path d="M10 2v10l3-2.5 3 2.5V2"/>
                        </svg>
                    </div>
                    <div>
                        <h5 class="usul-modal-title" id="usulBukuTitle">Usulkan Buku Baru</h5>
                        <p class="usul-modal-desc">Rekomendasikan buku untuk koleksi perpustakaan MANSAPA</p>
                    </div>
                </div>
                <button type="button" class="usul-close-btn" data-dismiss="modal" aria-label="Tutup">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Form -->
            <form id="formUsulBuku" method="post" action="index.php?p=opac" onsubmit="event.preventDefault(); handleUsulBukuSubmit(this);">
                <input type="hidden" name="action" value="simpan_usul_buku">
                
                <div class="modal-body usul-modal-body">
                    
                    <!-- Section 1: Data Buku -->
                    <div class="usul-sec-divider">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                        </svg>
                        <span>Informasi Buku</span>
                    </div>

                    <!-- Judul Buku (Wajib) -->
                    <div class="usul-field-group">
                        <div class="usul-label-row">
                            <label for="usulJudul" class="usul-label">
                                Judul Buku <span class="text-danger" title="Wajib diisi">*</span>
                            </label>
                            <span class="usul-tag-req">Wajib</span>
                        </div>
                        <div class="usul-input-box">
                            <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/>
                                <path d="M6 6h10"/>
                                <path d="M6 10h10"/>
                            </svg>
                            <input type="text" name="judul" class="usul-control" id="usulJudul" required 
                                   placeholder="Contoh: Filosofi Teras, Laskar Pelangi...">
                        </div>
                    </div>

                    <!-- Row: Penulis & Penerbit / Tahun -->
                    <div class="form-row">
                        <div class="col-sm-6 usul-field-group">
                            <div class="usul-label-row">
                                <label for="usulPenulis" class="usul-label">Penulis / Pengarang</label>
                                <span class="usul-tag-opt">Opsional</span>
                            </div>
                            <div class="usul-input-box">
                                <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
                                    <path d="m15 5 4 4"/>
                                </svg>
                                <input type="text" name="pengarang" class="usul-control" id="usulPenulis" 
                                       placeholder="Nama penulis / pengarang">
                            </div>
                        </div>

                        <div class="col-sm-6 usul-field-group">
                            <div class="usul-label-row">
                                <label for="usulPenerbit" class="usul-label">Penerbit &amp; Tahun</label>
                                <span class="usul-tag-opt">Opsional</span>
                            </div>
                            <div class="form-row">
                                <div class="col-7 pr-1">
                                    <div class="usul-input-box">
                                        <svg class="input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="4" y="2" width="16" height="20" rx="2" ry="2"/>
                                            <path d="M9 22v-4h6v4"/>
                                            <path d="M8 6h.01M16 6h.01M8 10h.01M16 10h.01"/>
                                        </svg>
                                        <input type="text" name="penerbit" class="usul-control" id="usulPenerbit" 
                                               placeholder="Penerbit">
                                    </div>
                                </div>
                                <div class="col-5 pl-1">
                                    <div class="usul-input-box">
                                        <svg class="input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                            <line x1="16" y1="2" x2="16" y2="6"/>
                                            <line x1="8" y1="2" x2="8" y2="6"/>
                                            <line x1="3" y1="10" x2="21" y2="10"/>
                                        </svg>
                                        <input type="text" name="tahunterbit" class="usul-control text-center px-1" id="usulTahun" 
                                               maxlength="4" placeholder="Tahun">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Data Pengusul -->
                    <div class="usul-sec-divider mt-2">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <polyline points="16 11 18 13 22 9"/>
                        </svg>
                        <span>Identitas Pengusul</span>
                    </div>

                    <!-- Row: Nama & Identitas -->
                    <div class="form-row">
                        <div class="col-sm-6 usul-field-group">
                            <div class="usul-label-row">
                                <label for="usulNama" class="usul-label">
                                    Nama Pengusul <span class="text-danger" title="Wajib diisi">*</span>
                                </label>
                                <span class="usul-tag-req">Wajib</span>
                            </div>
                            <div class="usul-input-box">
                                <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                                <input type="text" name="nama" class="usul-control" id="usulNama" required 
                                       placeholder="Nama lengkap Anda">
                            </div>
                        </div>

                        <div class="col-sm-6 usul-field-group">
                            <div class="usul-label-row">
                                <label for="usulIdentitas" class="usul-label">NIS / NIP / Kelas</label>
                                <span class="usul-tag-opt">Opsional</span>
                            </div>
                            <div class="usul-input-box">
                                <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="16" rx="3"/>
                                    <circle cx="9" cy="10" r="2.5"/>
                                    <path d="M15 8h2"/>
                                    <path d="M15 12h2"/>
                                    <path d="M6 17a3 3 0 0 1 6 0"/>
                                </svg>
                                <input type="text" name="identitas" class="usul-control" id="usulIdentitas" 
                                       placeholder="Contoh: XII MIPA 1 / 12345">
                            </div>
                        </div>
                    </div>

                    <!-- Alasan Pengusulan (Opsional) -->
                    <div class="usul-field-group mb-0">
                        <div class="usul-label-row">
                            <label for="usulAlasan" class="usul-label">Alasan Pengusulan</label>
                            <span class="usul-tag-opt">Opsional</span>
                        </div>
                        <textarea name="alasan" class="usul-textarea" id="usulAlasan" rows="2" 
                                  placeholder="Mengapa buku ini bermanfaat untuk koleksi madrasah?"></textarea>
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="usul-modal-footer">
                    <span class="usul-footer-hint d-none d-sm-inline-flex">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <path d="m9 12 2 2 4-4"/>
                        </svg>
                        <span>Usulan ditinjau pustakawan</span>
                    </span>
                    <div class="d-flex align-items-center ml-auto" style="gap: 8px;">
                        <button type="button" class="usul-btn-ghost" data-dismiss="modal">Batal</button>
                        <button type="submit" class="usul-btn-send font-weight-bold">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="22" y1="2" x2="11" y2="13"/>
                                <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                            </svg>
                            <span>Kirim Usulan</span>
                        </button>
                    </div>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
/* ================================================================
   OPAC CAROUSEL — Scroll logic for the ← → nav buttons & drag.
   Scrolls track by proportional chunk per click. Supports keyboard
   arrows and mouse drag on desktop.
================================================================ */
(function () {
    var track   = document.getElementById('opacBooksCarousel');
    var btnPrev = document.getElementById('opacSliderPrev');
    var btnNext = document.getElementById('opacSliderNext');
    if (!track || !btnPrev || !btnNext) return;

    function getStep() {
        var item = track.querySelector('.opac-book-item');
        if (!item) return 300;
        var gap = parseFloat(window.getComputedStyle(track).gap || window.getComputedStyle(track).columnGap) || 22;
        var singleStep = item.offsetWidth + gap;
        var visibleBooks = Math.floor(track.clientWidth / singleStep);
        var count = Math.max(1, Math.min(3, visibleBooks - 1 || 1));
        return singleStep * count;
    }

    function refreshState() {
        var maxScroll = track.scrollWidth - track.clientWidth;
        var atStart = track.scrollLeft <= 4;
        var atEnd   = Math.ceil(track.scrollLeft) >= maxScroll - 4 || maxScroll <= 0;
        btnPrev.disabled      = atStart;
        btnNext.disabled      = atEnd;
        btnPrev.style.opacity = atStart ? '0.35' : '1';
        btnNext.style.opacity = atEnd   ? '0.35' : '1';
        btnPrev.style.pointerEvents = atStart ? 'none' : 'auto';
        btnNext.style.pointerEvents = atEnd   ? 'none' : 'auto';
    }

    btnPrev.addEventListener('click', function () {
        track.scrollBy({ left: -getStep(), behavior: 'smooth' });
    });
    btnNext.addEventListener('click', function () {
        track.scrollBy({ left:  getStep(), behavior: 'smooth' });
    });

    // Arrow-key navigation when carousel is focused
    track.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowLeft')  { track.scrollBy({ left: -getStep(), behavior: 'smooth' }); e.preventDefault(); }
        if (e.key === 'ArrowRight') { track.scrollBy({ left:  getStep(), behavior: 'smooth' }); e.preventDefault(); }
    });

    // Mouse drag-to-scroll for desktop
    var isDown = false, startX = 0, scrollLeft = 0;
    track.addEventListener('mousedown', function (e) {
        if (e.button !== 0) return;
        isDown = true;
        track.classList.add('is-dragging');
        startX = e.pageX - track.offsetLeft;
        scrollLeft = track.scrollLeft;
    });
    window.addEventListener('mouseup', function () {
        if (!isDown) return;
        isDown = false;
        track.classList.remove('is-dragging');
    });
    track.addEventListener('mousemove', function (e) {
        if (!isDown) return;
        e.preventDefault();
        var x = e.pageX - track.offsetLeft;
        var walk = (x - startX) * 1.4;
        track.scrollLeft = scrollLeft - walk;
    });

    track.addEventListener('scroll', refreshState, { passive: true });
    window.addEventListener('resize', refreshState);
    refreshState();
})();


/* ================================================================
   USUL BUKU — Form submission via Fetch API
================================================================ */
function handleUsulBukuSubmit(form) {
    var judul       = document.getElementById('usulJudul') ? document.getElementById('usulJudul').value.trim() : '';
    var pengarang   = document.getElementById('usulPenulis') ? document.getElementById('usulPenulis').value.trim() : '';
    var penerbit    = document.getElementById('usulPenerbit') ? document.getElementById('usulPenerbit').value.trim() : '';
    var tahun       = document.getElementById('usulTahun') ? document.getElementById('usulTahun').value.trim() : '';
    var nama        = document.getElementById('usulNama') ? document.getElementById('usulNama').value.trim() : '';
    var identitas   = document.getElementById('usulIdentitas') ? document.getElementById('usulIdentitas').value.trim() : '';
    var alasan      = document.getElementById('usulAlasan') ? document.getElementById('usulAlasan').value.trim() : '';
    var submitBtn   = form.querySelector('button[type="submit"]');

    if (!judul || !nama) {
        if (window.toastr) {
            toastr.error('Nama pengusul dan judul buku wajib diisi.', 'Perhatian');
        } else {
            alert('Nama pengusul dan judul buku wajib diisi.');
        }
        return;
    }

    var originalBtnHtml = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<svg class="fa-spin" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" style="margin-right: 6px;"><circle cx="12" cy="12" r="10" stroke-opacity="0.25"/><path d="M12 2a10 10 0 0 1 10 10"/></svg> Mengirim usulan...';

    var formData = new FormData(form);
    formData.set('action', 'simpan_usul_buku');
    formData.set('judul', judul);
    formData.set('pengarang', pengarang);
    formData.set('penerbit', penerbit);
    formData.set('tahunterbit', tahun);
    formData.set('nama', nama);
    formData.set('identitas', identitas);
    formData.set('alasan', alasan);

    fetch('<?= SWB; ?>index.php?p=opac', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(function(res) {
        return res.text().then(function(text) {
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Invalid server response (non-JSON):', text);
                throw new Error('Respon server tidak valid.');
            }
        });
    })
    .then(function(data) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnHtml;

        if (data.status === 'success') {
            if (window.toastr) {
                toastr.success(data.message, 'Berhasil Disimpan');
            } else {
                alert(data.message);
            }
            form.reset();
            if (window.jQuery && typeof $('#usulBukuModal').modal === 'function') {
                $('#usulBukuModal').modal('hide');
            } else {
                var modalEl = document.getElementById('usulBukuModal');
                if (modalEl) {
                    modalEl.classList.remove('show');
                    modalEl.style.display = 'none';
                    document.body.classList.remove('modal-open');
                    var backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) backdrop.remove();
                }
            }
        } else {
            if (window.toastr) {
                toastr.error(data.message || 'Terjadi kesalahan.', 'Gagal');
            } else {
                alert(data.message || 'Terjadi kesalahan saat menyimpan.');
            }
        }
    })
    .catch(function(err) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnHtml;
        console.error('Error saving usul buku:', err);
        var msg = err && err.message ? err.message : 'Terjadi kesalahan jaringan saat menyimpan usulan.';
        if (window.toastr) {
            toastr.error(msg, 'Koneksi Gagal');
        } else {
            alert(msg);
        }
    });
}
</script>

</body>
</html>
