<?php
/**
 * =========================================================================
 * MANSAPA Iqra' Labs — curated_books.php [Curated Books Showcase]
 * Place: template/iqra-labs/parts/curated_books.php
 *
 * SECTION 1: "Curated Books Showcase" (Kurasi Buku Pilihan)
 * Refined compact display:
 * - Exactly 6 books displayed.
 * - Desktop (>= 1024px): Single row of 6 compact cards.
 * - Mobile (< 768px): 2 rows, each row containing 3 columns (3 cols x 2 rows = 6 cards).
 * - Clean header without filter buttons or "Lihat Semua" CTA.
 * - Seamless flow from Hero with mathematical dot-matrix canvas & mesh orbs.
 * =========================================================================
 */

// -------------------------------------------------------------------------
// 1. CONFIGURABLE CURATED BIBLIO IDs (Exactly 6 Books)
// -------------------------------------------------------------------------
$curated_biblio_ids = [1, 2, 3, 4, 5, 6];

// -------------------------------------------------------------------------
// 2. DATABASE INTERACTION (Native SLiMS $dbs connection)
// -------------------------------------------------------------------------
global $dbs;
$dbs = $dbs ?? \SLiMS\DB::getInstance('mysqli');

$curated_books = [];
$fetched_ids   = [];

// Sanitize array values with intval to strictly prevent SQL injection
$clean_ids = array_values(array_unique(array_filter(array_map('intval', (array)$curated_biblio_ids), function ($id) {
    return $id > 0;
})));

if (!empty($clean_ids)) {
    $id_list = implode(',', $clean_ids);
    $sql_curated = "SELECT b.biblio_id, b.title, b.image, b.classification, b.call_number,
                           GROUP_CONCAT(DISTINCT a.author_name ORDER BY ba.level ASC SEPARATOR ', ') AS authors,
                           GROUP_CONCAT(DISTINCT t.topic SEPARATOR ', ') AS topic_name
                    FROM biblio b
                    LEFT JOIN biblio_author ba ON b.biblio_id = ba.biblio_id
                    LEFT JOIN mst_author a ON ba.author_id = a.author_id
                    LEFT JOIN biblio_topic bt ON b.biblio_id = bt.biblio_id
                    LEFT JOIN mst_topic t ON bt.topic_id = t.topic_id
                    WHERE b.biblio_id IN ($id_list)
                    GROUP BY b.biblio_id
                    ORDER BY FIELD(b.biblio_id, $id_list)";
    $res_curated = $dbs->query($sql_curated);
    if ($res_curated && $res_curated->num_rows > 0) {
        while ($row = $res_curated->fetch_assoc()) {
            $curated_books[] = $row;
            $fetched_ids[]   = (int)$row['biblio_id'];
        }
    }
}

// -------------------------------------------------------------------------
// 3. GRACEFUL FALLBACK (Guarantees exactly 6 visible cards)
// -------------------------------------------------------------------------
if (count($curated_books) < 6) {
    $needed = 6 - count($curated_books);
    $exclude_sql = !empty($fetched_ids) ? "AND b.biblio_id NOT IN (" . implode(',', $fetched_ids) . ")" : "";
    $sql_fallback = "SELECT b.biblio_id, b.title, b.image, b.classification, b.call_number,
                            GROUP_CONCAT(DISTINCT a.author_name ORDER BY ba.level ASC SEPARATOR ', ') AS authors,
                            GROUP_CONCAT(DISTINCT t.topic SEPARATOR ', ') AS topic_name
                     FROM biblio b
                     LEFT JOIN biblio_author ba ON b.biblio_id = ba.biblio_id
                     LEFT JOIN mst_author a ON ba.author_id = a.author_id
                     LEFT JOIN biblio_topic bt ON b.biblio_id = bt.biblio_id
                     LEFT JOIN mst_topic t ON bt.topic_id = t.topic_id
                     WHERE b.title IS NOT NULL AND TRIM(b.title) != '' $exclude_sql
                     GROUP BY b.biblio_id
                     ORDER BY (b.image IS NOT NULL AND TRIM(b.image) != '') DESC, b.last_update DESC, b.biblio_id DESC
                     LIMIT $needed";
    $res_fallback = $dbs->query($sql_fallback);
    if ($res_fallback && $res_fallback->num_rows > 0) {
        while ($row = $res_fallback->fetch_assoc()) {
            $curated_books[] = $row;
        }
    }
}

// Prepare cards data
$prepared_cards = [];
foreach ($curated_books as $book) {
    $biblio_id  = (int)$book['biblio_id'];
    $title      = trim($book['title'] ?? '');
    $authors    = trim($book['authors'] ?? '');
    if (empty($authors)) {
        $authors = 'Tim Pustakawan MANSAPA';
    }
    $detail_url = 'index.php?p=show_detail&id=' . $biblio_id;

    // Category derivation from topic or classification
    $category = 'Rekomendasi';
    if (!empty($book['topic_name'])) {
        $topics_arr = explode(',', $book['topic_name']);
        $category = trim($topics_arr[0]);
    } elseif (!empty($book['classification'])) {
        $c = substr(trim($book['classification']), 0, 1);
        $ddc_map = [
            '0' => 'Karya Umum', '1' => 'Filsafat', '2' => 'Pendidikan Islam',
            '3' => 'Ilmu Sosial', '4' => 'Bahasa', '5' => 'Sains Murni',
            '6' => 'Teknologi', '7' => 'Seni & Olahraga', '8' => 'Karya Sastra', '9' => 'Sejarah & Biografi'
        ];
        $category = $ddc_map[$c] ?? 'Akademik';
    }

    // Call number formatting
    $short_call = '';
    $raw_call = trim($book['call_number'] ?? '');
    if (!empty($raw_call)) {
        if (preg_match('/^([0-9]{3}(?:\.[0-9]+)?)/', $raw_call, $matches)) {
            $short_call = 'DDC ' . $matches[1];
        } else {
            $short_call = mb_strimwidth($raw_call, 0, 10, '..');
        }
    } elseif (!empty($book['classification'])) {
        $short_call = 'DDC ' . trim($book['classification']);
    }

    // Initial character for fallback cover
    $first_char = mb_substr($title, 0, 1, 'UTF-8');
    if (empty($first_char) || !preg_match('/[a-zA-Z0-9]/', $first_char)) {
        $first_char = 'B';
    } else {
        $first_char = mb_strtoupper($first_char, 'UTF-8');
    }

    // Check cover image existence on SLiMS storage disk
    $has_real_image = false;
    $cover_src = '';
    $image_filename = trim($book['image'] ?? '');
    if (!empty($image_filename)) {
        $storage_path = defined('IMGBS') ? IMGBS . 'docs/' . $image_filename : (defined('SB') ? SB . 'images/docs/' . $image_filename : 'images/docs/' . $image_filename);
        if (file_exists($storage_path)) {
            $has_real_image = true;
            $cover_src = (defined('SWB') ? SWB : '') . 'lib/minigalnano/createthumb.php?filename=' . urlencode('images/docs/' . $image_filename) . '&width=260';
        }
    }

    $prepared_cards[] = [
        'id'             => $biblio_id,
        'title'          => $title,
        'authors'        => $authors,
        'detail_url'     => $detail_url,
        'category'       => $category,
        'short_call'     => $short_call,
        'raw_call'       => $raw_call,
        'first_char'     => $first_char,
        'has_real_image' => $has_real_image,
        'cover_src'      => $cover_src,
    ];
}
?>

<!-- ================================================================
     SECTION 1: CURATED BOOKS SHOWCASE (Koleksi Pilihan Pustakawan)
     Seamless flow from Hero: 6 Compact Cards (Single Row on Desktop)
================================================================ -->
<section class="section-curated-books py-section scroll-reveal" aria-labelledby="curated-heading">

    <!-- Seamless Atmospheric Background Canvas continuing from Hero -->
    <div class="curated-bg-canvas" aria-hidden="true">
        <!-- Mathematical Dot Matrix Grid Overlay -->
        <div class="curated-bg-grid"></div>

        <!-- Luminous Ambient Mesh Glow Orbs echoing Hero canvas -->
        <div class="curated-bg-orb curated-orb-navy"></div>
        <div class="curated-bg-orb curated-orb-gold"></div>
        <div class="curated-bg-orb curated-orb-cyan"></div>

        <!-- Decorative Geometric Floating Elements (Swiss Modernism 2.0) -->
        <svg class="curated-deco-shape curated-shape-orbit" width="260" height="260" viewBox="0 0 260 260" fill="none">
            <circle cx="130" cy="130" r="115" stroke="rgba(212, 175, 55, 0.18)" stroke-width="1.2" stroke-dasharray="5 7"/>
            <circle cx="130" cy="130" r="80" stroke="rgba(13, 27, 61, 0.06)" stroke-width="1"/>
            <circle cx="230" cy="130" r="3.5" fill="#D4AF37" opacity="0.6"/>
            <circle cx="35" cy="130" r="2.5" fill="#0D1B3D" opacity="0.4"/>
        </svg>

        <svg class="curated-deco-shape curated-shape-crosshair" width="60" height="60" viewBox="0 0 60 60" fill="none">
            <line x1="30" y1="0" x2="30" y2="60" stroke="rgba(212, 175, 55, 0.2)" stroke-width="1"/>
            <line x1="0" y1="30" x2="60" y2="30" stroke="rgba(212, 175, 55, 0.2)" stroke-width="1"/>
            <circle cx="30" cy="30" r="8" stroke="rgba(13, 27, 61, 0.1)" stroke-width="1"/>
            <circle cx="30" cy="30" r="2" fill="#D4AF37"/>
        </svg>
    </div><!-- /.curated-bg-canvas -->

    <div class="container iqra-container">

        <!-- ── Section Header matching Hero Design Language (Focused & Clean) ─ -->
        <header class="curated-section-header">
            <div class="curated-header-info">
                <!-- Top Shimmer Pill with Live Pulsing Dot matching Hero Badge -->
                <div class="curated-badge animate-fade-down">
                    <span class="curated-badge-pulse" aria-hidden="true">
                        <span class="curated-badge-ring"></span>
                        <span class="curated-badge-dot"></span>
                    </span>
                    <span class="curated-badge-text">Kurasi Pustakawan</span>
                    <span class="curated-badge-sparkle" aria-hidden="true">&#10022;</span>
                </div>

                <h2 class="curated-section-title animate-fade-down delay-1" id="curated-heading">
                    Koleksi Buku <span class="curated-title-highlight">Pilihan Spesial</span>
                </h2>
                <p class="curated-section-sub mb-0 animate-fade-down delay-2">
                    Bahan bacaan kurasi unggulan rekomendasi tim pustakawan MANSAPA Iqra' Labs untuk riset dan literasi sivitas madrasah.
                </p>
            </div>
        </header>

        <!-- ── Compact 6-Card Grid (6 cols desktop single row, 3 cols x 2 rows mobile) ─ -->
        <div class="curated-books-grid animate-fade-up delay-3" id="curatedBooksGrid" role="list">
            <?php foreach ($prepared_cards as $card): ?>
            <article class="curated-book-card" role="listitem">
                <a href="<?= htmlspecialchars($card['detail_url']); ?>" class="curated-card-inner" aria-label="<?= htmlspecialchars($card['title']); ?> oleh <?= htmlspecialchars($card['authors']); ?>">

                    <!-- Top Cover Showcase with Architectural Corners -->
                    <div class="curated-cover-wrapper">
                        <!-- Architectural Corner Accents matching Hero Photo Frame -->
                        <span class="curated-corner corner-tl" aria-hidden="true"></span>
                        <span class="curated-corner corner-br" aria-hidden="true"></span>

                        <figure class="curated-cover-frame">
                            <?php if ($card['has_real_image']): ?>
                                <img src="<?= htmlspecialchars($card['cover_src']); ?>"
                                     alt="<?= htmlspecialchars($card['title']); ?>"
                                     class="curated-cover-img"
                                     loading="lazy"
                                     width="200"
                                     height="300"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="curated-cover-placeholder" style="display: none;" aria-hidden="true">
                                    <div class="placeholder-spine"></div>
                                    <div class="placeholder-body">
                                        <span class="placeholder-initial"><?= htmlspecialchars($card['first_char']); ?></span>
                                        <span class="placeholder-title"><?= htmlspecialchars(mb_strimwidth($card['title'], 0, 24, '...')); ?></span>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="curated-cover-placeholder" aria-hidden="true">
                                    <div class="placeholder-spine"></div>
                                    <div class="placeholder-body">
                                        <span class="placeholder-initial"><?= htmlspecialchars($card['first_char']); ?></span>
                                        <span class="placeholder-title"><?= htmlspecialchars(mb_strimwidth($card['title'], 0, 24, '...')); ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Realistic 3D Spine Lighting Fold -->
                            <div class="curated-spine-edge" aria-hidden="true"></div>

                            <!-- Floating Category / Accent Badge: Frosted Gold/Navy -->
                            <div class="curated-badge-pill" title="Topik: <?= htmlspecialchars($card['category']); ?>">
                                <span><?= htmlspecialchars(mb_strimwidth($card['category'], 0, 11, '..')); ?></span>
                            </div>

                            <!-- Hover Overlay with Peek CTA -->
                            <div class="curated-cover-overlay" aria-hidden="true">
                                <span class="curated-peek-btn">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                                    <span>Buka</span>
                                </span>
                            </div>
                        </figure>
                    </div><!-- /.curated-cover-wrapper -->

                    <!-- Bottom Meta Card Body -->
                    <div class="curated-meta-body">
                        <h3 class="curated-title" title="<?= htmlspecialchars($card['title']); ?>">
                            <?= htmlspecialchars($card['title']); ?>
                        </h3>

                        <p class="curated-author" title="<?= htmlspecialchars($card['authors']); ?>">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                            <span><?= htmlspecialchars($card['authors']); ?></span>
                        </p>

                        <!-- Bottom Quick Status & Action Row -->
                        <div class="curated-footer-row">
                            <span class="curated-status-tag">
                                <span class="curated-status-dot" aria-hidden="true"></span>
                                <span>Tersedia</span>
                            </span>
                            <span class="curated-action-link" aria-hidden="true">
                                <span>Detail</span>
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </span>
                        </div>
                    </div><!-- /.curated-meta-body -->

                </a>
            </article>
            <?php endforeach; ?>
        </div><!-- /.curated-books-grid -->

    </div><!-- /.container -->
</section><!-- /.section-curated-books -->
