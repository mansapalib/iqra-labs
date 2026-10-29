<?php
/**
 * =========================================================================
 * MANSAPA Iqra' Labs — top_readers.php [Leaderboard & Hall of Fame]
 * Place: template/iqra-labs/parts/top_readers.php
 *
 * SECTION: "Leaderboard & Hall of Fame" (Peringkat Pembaca Teraktif)
 * Asymmetrical 2-Column Section:
 *  - Left Column: Photographic showcase of student activity (diskusi.jpg)
 *    with a floating motivational quote card.
 *  - Right Column: Vertical Leaderboard displaying Top 3 active members
 *    calculated via weighted scoring: (visits * 1) + (loans * 3).
 *
 * Palette: Pure White (#FFFFFF), Deep Navy (#0F172A), Warm Gold (#D4AF37).
 * =========================================================================
 */

// -------------------------------------------------------------------------
// 1. DATABASE CONNECTION (Native SLiMS $dbs)
// -------------------------------------------------------------------------
global $dbs;
$dbs = $dbs ?? \SLiMS\DB::getInstance('mysqli');

$top_readers = [];

// Determine visitor table name (SLiMS Bulian uses visitor_count, legacy/custom uses visitor_log)
$visitor_table = 'visitor_count';
$chk_table = $dbs->query("SHOW TABLES LIKE 'visitor_log'");
if ($chk_table && $chk_table->num_rows > 0) {
    $visitor_table = 'visitor_log';
} else {
    $chk_table2 = $dbs->query("SHOW TABLES LIKE 'visitor_count'");
    if ($chk_table2 && $chk_table2->num_rows > 0) {
        $visitor_table = 'visitor_count';
    }
}

// -------------------------------------------------------------------------
// 2. WEIGHTED SCORING QUERY
// Formula: (total_visits * 1) + (total_loans * 3) AS total_score
// Aggregates visitor records and loan/loan_history records
// -------------------------------------------------------------------------
$sql_scores = "
    SELECT 
        m.member_id,
        m.member_name,
        m.member_image,
        m.member_image AS m_image,
        m.gender,
        m.inst_name,
        COALESCE(v.total_visits, 0) AS total_visits,
        COALESCE(l.total_loans, 0) AS total_loans,
        (COALESCE(v.total_visits, 0) * 1 + COALESCE(l.total_loans, 0) * 3) AS total_score
    FROM member m
    LEFT JOIN (
        SELECT member_id, COUNT(*) AS total_visits
        FROM {$visitor_table}
        WHERE member_id IS NOT NULL AND member_id != ''
        GROUP BY member_id
    ) v ON m.member_id = v.member_id
    LEFT JOIN (
        SELECT member_id, COUNT(*) AS total_loans
        FROM (
            SELECT member_id FROM loan WHERE member_id IS NOT NULL AND member_id != ''
            UNION ALL
            SELECT member_id FROM loan_history WHERE member_id IS NOT NULL AND member_id != ''
        ) all_loans
        GROUP BY member_id
    ) l ON m.member_id = l.member_id
    WHERE (COALESCE(v.total_visits, 0) * 1 + COALESCE(l.total_loans, 0) * 3) > 0
      AND (m.is_pending = 0 OR m.is_pending IS NULL)
    ORDER BY total_score DESC, total_loans DESC, total_visits DESC
    LIMIT 3
";

$res_scores = $dbs->query($sql_scores);
if ($res_scores && $res_scores->num_rows > 0) {
    while ($row = $res_scores->fetch_assoc()) {
        $top_readers[] = $row;
    }
}

// -------------------------------------------------------------------------
// 3. FALLBACK MECHANISM (If fewer than 3 active records exist)
// -------------------------------------------------------------------------
if (count($top_readers) < 3) {
    $needed = 3 - count($top_readers);
    $exclude_ids = array_map(function ($r) use ($dbs) {
        return "'" . $dbs->escape_string($r['member_id']) . "'";
    }, $top_readers);
    $exclude_sql = !empty($exclude_ids) ? "WHERE member_id NOT IN (" . implode(',', $exclude_ids) . ")" : "";

    $sql_fallback = "SELECT member_id, member_name, member_image, member_image AS m_image, gender, inst_name,
                            2 AS total_visits, 1 AS total_loans, 5 AS total_score
                     FROM member
                     $exclude_sql
                     ORDER BY register_date DESC, member_id DESC
                     LIMIT $needed";
    $res_fb = $dbs->query($sql_fallback);
    if ($res_fb && $res_fb->num_rows > 0) {
        while ($row = $res_fb->fetch_assoc()) {
            $top_readers[] = $row;
        }
    }
}

// Complete fallback if database has zero members
if (count($top_readers) < 3) {
    $sample_data = [
        ['member_id' => 'SAMPLE01', 'member_name' => "Vina Ni'matul Maula", 'member_image' => '', 'm_image' => '', 'gender' => 0, 'inst_name' => 'XII MIPA 1', 'total_visits' => 12, 'total_loans' => 8, 'total_score' => 36],
        ['member_id' => 'SAMPLE02', 'member_name' => 'Alma Yuna Arumi', 'member_image' => '', 'm_image' => '', 'gender' => 0, 'inst_name' => 'XI IPS 2', 'total_visits' => 10, 'total_loans' => 5, 'total_score' => 25],
        ['member_id' => 'SAMPLE03', 'member_name' => 'Rahma Agustina', 'member_image' => '', 'm_image' => '', 'gender' => 0, 'inst_name' => 'X-A', 'total_visits' => 8, 'total_loans' => 4, 'total_score' => 20],
    ];
    foreach ($sample_data as $sample) {
        if (count($top_readers) >= 3) break;
        $top_readers[] = $sample;
    }
}

// -------------------------------------------------------------------------
// 4. HELPER: Student Initials Generator (Fallback Avatar)
// -------------------------------------------------------------------------
if (!function_exists('iqra_get_initials')) {
    function iqra_get_initials($name) {
        $clean = trim(preg_replace('/[^a-zA-Z0-9\s]/', '', $name));
        $words = preg_split('/\s+/', $clean);
        if (empty($words) || empty($words[0])) {
            return 'P';
        }
        if (count($words) >= 2) {
            return strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1));
        }
        return strtoupper(mb_substr($words[0], 0, min(2, mb_strlen($words[0]))));
    }
}

// -------------------------------------------------------------------------
// 5. PHOTOGRAPH SHOWCASE ASSET PATH (Real library photograph)
// -------------------------------------------------------------------------
$diskusi_img_url = 'template/iqra-labs/images/diskusi.jpg';
if (file_exists(__DIR__ . '/../images/diskusi.jpg')) {
    $diskusi_img_url = 'template/' . basename(dirname(__DIR__)) . '/images/diskusi.jpg';
} elseif (file_exists(__DIR__ . '/../assets/images/diskusi.jpg')) {
    $diskusi_img_url = 'template/' . basename(dirname(__DIR__)) . '/assets/images/diskusi.jpg';
}
?>

<!-- ================================================================
     SECTION: LEADERBOARD & HALL OF FAME (Pembaca Teraktif)
     Asymmetrical 2-Column: Left 5 Cols (Showcase) | Right 7 Cols (Stack)
================================================================ -->
<section class="section-leaderboard py-section scroll-reveal" id="section-leaderboard" aria-labelledby="leaderboard-heading">
    <!-- Ambient Section Background Elements (Pure CSS, 0 layout shifts) -->
    <div class="section-bg-deco" aria-hidden="true">
        <div class="deco-mesh-orb deco-orb-left"></div>
        <div class="deco-mesh-orb deco-orb-right"></div>
    </div>
    <div class="container iqra-container">
        <div class="leaderboard-grid">

            <!-- ── LEFT COLUMN: Showcase & Motivational Card (5 cols) ────── -->
            <div class="leaderboard-showcase-col">
                <div class="showcase-card-wrapper">
                    <!-- Relative container with rounded-2xl overflow-hidden shadow-md -->
                    <div class="showcase-img-container">
                        <img src="<?= htmlspecialchars($diskusi_img_url); ?>"
                             alt="Aktivitas Literasi dan Diskusi Siswa di MANSAPA Iqra' Labs"
                             class="showcase-img"
                             loading="lazy"
                             width="600"
                             height="720">
                        <div class="showcase-img-overlay" aria-hidden="true"></div>
                    </div>

                    <!-- Floating Motivational Quote Card (pure white / glassmorphism) -->
                    <div class="showcase-floating-card">
                        <div class="floating-badge-wrap">
                            <span class="floating-gold-badge">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 4px; vertical-align: -1px;" aria-hidden="true"><path d="M3 21c3 0 7-1 7-8V5c0-1.25-.75-2-2-2H4c-1.25 0-2 .75-2 2v6c0 1.25.75 2 2 2h4c-.5 2.5-2 4.5-5 5v3zm14 0c3 0 7-1 7-8V5c0-1.25-.75-2-2-2h-4c-1.25 0-2 .75-2 2v6c0 1.25.75 2 2 2h4c-.5 2.5-2 4.5-5 5v3z"/></svg> Motivasi Pekan Ini
                            </span>
                        </div>
                        <blockquote class="floating-quote-text">
                            &ldquo;Gemar Membaca, Cerdas Berkarya.&rdquo;
                        </blockquote>
                        <p class="floating-quote-sub">
                            Perpustakaan adalah ruang tumbuh gagasan dan prestasimu.
                        </p>
                    </div>
                </div>
            </div><!-- /.leaderboard-showcase-col -->

            <!-- ── RIGHT COLUMN: Vertical Leaderboard (7 cols) ──────────── -->
            <div class="leaderboard-content-col">

                <!-- Section Header -->
                <header class="leaderboard-header mb-4">
                    <div class="leaderboard-pill-wrap mb-2">
                        <span class="section-pill reader-badge-pill">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px; vertical-align: -1px;" aria-hidden="true"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.45 1-1 1H7v4h10v-4h-2c-.55 0-1-.45-1-1v-2.34"/><path d="M6 4h12v7a6 6 0 0 1-12 0V4z"/></svg> PENGHARGAAN PEMUSTAKA
                        </span>
                    </div>
                    <h2 class="leaderboard-title" id="leaderboard-heading">
                        Peringkat Pembaca Teraktif
                    </h2>
                    <p class="leaderboard-subtitle">
                        Dihitung berdasarkan keaktifan kunjungan fisik dan peminjaman literatur.
                    </p>
                </header>

                <!-- 3-Row Vertical Leaderboard Stack (space-y-4) -->
                <div class="leaderboard-stack" role="list">
                    <?php
                    foreach ($top_readers as $idx => $reader):
                        $rank = $idx + 1;

                        // 1. Data Sanitization: Replace raw backticks or clean quotes
                        $raw_name = $reader['member_name'] ?? 'Pemustaka';
                        $clean_name = trim(str_replace(['`', '´', '’', '‘'], "'", $raw_name));
                        $initials = iqra_get_initials($clean_name);

                        // Format Class & Institution
                        $inst = trim($reader['inst_name'] ?? '');
                        if (empty($inst)) {
                            $class_info = 'Siswa MAN 1 Pangandaran';
                        } elseif (preg_match('/^(kelas|x|xi|xii)/i', $inst)) {
                            $class_info = 'Kelas ' . preg_replace('/^kelas\s*/i', '', $inst) . ' &bull; MAN 1 Pangandaran';
                        } else {
                            $class_info = 'Kelas ' . $inst . ' &bull; MAN 1 Pangandaran';
                        }

                        // Statistics
                        $total_visits = (int)($reader['total_visits'] ?? 0);
                        $total_loans  = (int)($reader['total_loans'] ?? 0);
                        $total_score  = (int)($reader['total_score'] ?? (($total_visits * 1) + ($total_loans * 3)));

                        // 2. Image Resolution: Check images/persons/ via m_image on physical disk
                        $img_file = trim($reader['m_image'] ?? ($reader['member_image'] ?? ''));
                        $has_photo = false;
                        $photo_url = '';
                        if (!empty($img_file)) {
                            $candidates = [
                                defined('SB') ? SB . 'images/persons/' . $img_file : '',
                                defined('IMGBS') ? IMGBS . 'persons/' . $img_file : '',
                                __DIR__ . '/../../../../images/persons/' . $img_file,
                                'images/persons/' . $img_file
                            ];
                            foreach ($candidates as $cand) {
                                if (!empty($cand) && file_exists($cand)) {
                                    $has_photo = true;
                                    $photo_url = (defined('SWB') ? SWB : '') . 'images/persons/' . rawurlencode($img_file);
                                    break;
                                }
                            }
                        }
                    ?>
                    <article class="leaderboard-card rank-<?= $rank; ?>" role="listitem">
                        <div class="card-left-group">

                            <!-- Rank Badge (#1, #2, #3) -->
                            <div class="rank-badge-wrap">
                                <div class="rank-badge badge-<?= $rank; ?>" aria-label="Peringkat <?= $rank; ?>">
                                    <?php if ($rank === 1): ?>
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 3px; vertical-align: -1px;" aria-hidden="true"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
                                    <?php endif; ?>
                                    <span>#<?= $rank; ?></span>
                                </div>
                            </div>

                            <!-- Avatar Container: Photo or Circular Monogram Initials -->
                            <div class="leaderboard-avatar-wrap">
                                <?php if ($has_photo): ?>
                                    <img src="<?= htmlspecialchars($photo_url); ?>"
                                         alt="Foto <?= htmlspecialchars($clean_name); ?>"
                                         class="leaderboard-avatar-img"
                                         loading="lazy"
                                         width="56"
                                         height="56"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="leaderboard-avatar-initials avatar-fallback-<?= $rank; ?>" style="display: none;" aria-hidden="true">
                                        <?= htmlspecialchars($initials); ?>
                                    </div>
                                <?php else: ?>
                                    <div class="leaderboard-avatar-initials avatar-fallback-<?= $rank; ?>" aria-label="Inisial <?= htmlspecialchars($clean_name); ?>">
                                        <?= htmlspecialchars($initials); ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Member Name & Institution / Class -->
                            <div class="leaderboard-member-details">
                                <h3 class="member-name" title="<?= htmlspecialchars($clean_name); ?>">
                                    <?= htmlspecialchars($clean_name); ?>
                                </h3>
                                <p class="member-institution">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px; vertical-align: -1px;" aria-hidden="true"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                                    <?= $class_info; ?>
                                </p>
                            </div>

                        </div><!-- /.card-left-group -->

                        <!-- Score & Activity Stats (Right-aligned) -->
                        <div class="leaderboard-score-wrap">
                            <div class="score-points">
                                <span class="points-val"><?= $total_score; ?></span>
                                <span class="points-unit">Poin</span>
                            </div>
                            <div class="score-subtitle">
                                <?= $total_visits; ?> Kunjungan &bull; <?= $total_loans; ?> Pinjaman
                            </div>
                        </div>

                    </article>
                    <?php endforeach; ?>
                </div><!-- /.leaderboard-stack -->

            </div><!-- /.leaderboard-content-col -->

        </div><!-- /.leaderboard-grid -->
    </div><!-- /.container -->
</section><!-- /.section-leaderboard -->
