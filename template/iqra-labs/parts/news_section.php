<?php
/**
 * =========================================================================
 * MANSAPA Iqra' Labs — news_section.php [Publication Hub]
 * Place: template/iqra-labs/parts/news_section.php
 *
 * SECTION 2: "Kabar & Agenda Perpustakaan"
 * Directly beneath Section 1 (Curated Books Showcase).
 * Dynamically fetches the latest 3 news items from the SLiMS `content` table.
 * Light Theme Palette: Pure White, Deep Navy (#0F172A), Warm Gold (#D4AF37).
 * =========================================================================
 */

// -------------------------------------------------------------------------
// 1. DATABASE INTERACTION (Native SLiMS $dbs connection)
// -------------------------------------------------------------------------
global $dbs;
$dbs = $dbs ?? \SLiMS\DB::getInstance('mysqli');

$sql_news = "SELECT content_id, content_title, content_desc, content_path, is_news, input_date, last_update 
             FROM content 
             WHERE is_news = 1 
             ORDER BY last_update DESC 
             LIMIT 3";

$res_news = $dbs->query($sql_news);

$news_items = [];

if ($res_news && $res_news->num_rows > 0) {
    $indonesian_months = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
        7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
    ];

    while ($row = $res_news->fetch_assoc()) {
        $raw_desc = stripslashes($row['content_desc'] ?? '');

        // 1. Image Extraction: find first <img> tag inside content_desc
        $extracted_image = '';
        if (preg_match('/<img[^>]+src=["\']?([^"\'\s>]+)["\']?/i', $raw_desc, $img_match)) {
            $extracted_image = trim($img_match[1]);
        }

        // 2. Date Formatting: Indonesian localized date (e.g., "30 Sep 2026")
        $date_field = !empty($row['last_update']) && $row['last_update'] !== '0000-00-00 00:00:00' 
            ? $row['last_update'] 
            : $row['input_date'];
        $timestamp = strtotime($date_field);
        if ($timestamp) {
            $month_num = (int)date('n', $timestamp);
            $month_name = $indonesian_months[$month_num] ?? date('M', $timestamp);
            $formatted_date = date('j', $timestamp) . ' ' . $month_name . ' ' . date('Y', $timestamp);
            $datetime_attr  = date('Y-m-d', $timestamp);
        } else {
            $formatted_date = 'Terbaru';
            $datetime_attr  = date('Y-m-d');
        }

        // 3. Excerpt Generation: Strip HTML tags and truncate to ~120 characters
        $clean_text = preg_replace('/\s+/', ' ', trim(strip_tags($raw_desc)));
        $excerpt = mb_strimwidth($clean_text, 0, 120, '...');

        // 4. Detail Link: SLiMS content routing
        $content_path = trim($row['content_path'] ?? '');
        $detail_url = 'index.php?p=' . urlencode($content_path);

        $news_items[] = [
            'id'            => (int)$row['content_id'],
            'title'         => trim($row['content_title'] ?? ''),
            'date'          => $formatted_date,
            'datetime_attr' => $datetime_attr,
            'image'         => $extracted_image,
            'excerpt'       => $excerpt,
            'url'           => $detail_url
        ];
    }
}
?>

<!-- ================================================================
     SECTION 2: KABAR & AGENDA LITERASI (Publication Hub)
================================================================ -->
<section class="section-news py-section scroll-reveal" aria-labelledby="news-heading">
    <!-- Ambient Section Background Elements (Pure CSS, 0 layout shifts) -->
    <div class="section-bg-deco" aria-hidden="true">
        <div class="deco-mesh-orb deco-orb-right"></div>
        <svg class="deco-grid-dots" style="left: 20px; top: 30px; right: auto;" width="160" height="100" viewBox="0 0 160 100" fill="none">
            <pattern id="dotPatternNews" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                <circle cx="2" cy="2" r="1.5" fill="rgba(212, 175, 55, 0.16)"/>
            </pattern>
            <rect width="160" height="100" fill="url(#dotPatternNews)"/>
        </svg>
    </div>
    <div class="container iqra-container">

        <!-- ── Section Header ──────────────────────────────────────── -->
        <header class="section-header-row mb-4">
            <div class="section-header-info">
                <span class="section-pill">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px; vertical-align: -1px;" aria-hidden="true"><path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></svg>
                    <span>Kabar Terkini</span>
                </span>
                <h2 class="section-h" id="news-heading">Kabar &amp; Agenda Literasi</h2>
                <p class="section-sub mb-0">Informasi terbaru seputar kegiatan literasi, agenda, dan pengumuman resmi MANSAPA Iqra' Labs.</p>
            </div>
            <a href="index.php?p=news" class="btn-see-all" title="Lihat seluruh arsip publikasi dan warta literasi">
                <span>Lihat Semua Kabar</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 4px; vertical-align: -1px;" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </header>

        <!-- ── Responsive 3-Card Grid (1 col mobile, 2 cols tablet, 3 cols desktop) ─ -->
        <?php if (!empty($news_items)): ?>
        <div class="news-grid" role="list">
            <?php foreach ($news_items as $news):
                $title         = htmlspecialchars($news['title']);
                $date          = htmlspecialchars($news['date']);
                $excerpt       = htmlspecialchars($news['excerpt']);
                $detail_url    = htmlspecialchars($news['url']);
                $image_src     = $news['image'];
                $has_image     = !empty($image_src);
            ?>
            <article class="news-card" role="listitem">
                <a href="<?= $detail_url ?>" class="news-card-link" aria-label="<?= $title ?>">
                    
                    <!-- Banner Container: Aspect ratio 4:3, overflow hidden -->
                    <figure class="news-banner-frame">
                        <?php if ($has_image): ?>
                            <img src="<?= htmlspecialchars($image_src) ?>"
                                 alt="<?= $title ?>"
                                 class="news-banner-img"
                                 loading="lazy"
                                 width="400"
                                 height="300"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="news-banner-placeholder" style="display: none;" aria-hidden="true">
                                <svg class="news-placeholder-svg" viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <defs>
                                        <linearGradient id="newsGradFallback<?= $news['id'] ?>" x1="0%" y1="0%" x2="100%" y2="100%">
                                            <stop offset="0%" stop-color="#0F172A" />
                                            <stop offset="60%" stop-color="#1E293B" />
                                            <stop offset="100%" stop-color="#0F172A" />
                                        </linearGradient>
                                        <pattern id="newsPatFallback<?= $news['id'] ?>" width="28" height="28" patternUnits="userSpaceOnUse">
                                            <path d="M 28 0 L 0 0 0 28" fill="none" stroke="rgba(212,175,55,0.09)" stroke-width="1"/>
                                        </pattern>
                                    </defs>
                                    <rect width="400" height="300" fill="url(#newsGradFallback<?= $news['id'] ?>)" />
                                    <rect width="400" height="300" fill="url(#newsPatFallback<?= $news['id'] ?>)" />
                                </svg>
                                <div class="news-placeholder-body">
                                    <div class="news-placeholder-badge">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/></svg>
                                    </div>
                                    <span class="news-placeholder-brand">MANSAPA Iqra' Labs</span>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="news-banner-placeholder" aria-hidden="true">
                                <svg class="news-placeholder-svg" viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <defs>
                                        <linearGradient id="newsGrad<?= $news['id'] ?>" x1="0%" y1="0%" x2="100%" y2="100%">
                                            <stop offset="0%" stop-color="#0F172A" />
                                            <stop offset="60%" stop-color="#1E293B" />
                                            <stop offset="100%" stop-color="#0F172A" />
                                        </linearGradient>
                                        <pattern id="newsPat<?= $news['id'] ?>" width="28" height="28" patternUnits="userSpaceOnUse">
                                            <path d="M 28 0 L 0 0 0 28" fill="none" stroke="rgba(212,175,55,0.09)" stroke-width="1"/>
                                        </pattern>
                                    </defs>
                                    <rect width="400" height="300" fill="url(#newsGrad<?= $news['id'] ?>)" />
                                    <rect width="400" height="300" fill="url(#newsPat<?= $news['id'] ?>)" />
                                </svg>
                                <div class="news-placeholder-body">
                                    <div class="news-placeholder-badge">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/></svg>
                                    </div>
                                    <span class="news-placeholder-brand">MANSAPA Iqra' Labs</span>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Category Pill Badge -->
                        <div class="news-badge-pill">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 3px; vertical-align: -1px;" aria-hidden="true"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                            <span>Kabar Terbaru</span>
                        </div>
                    </figure>

                    <!-- Content Body -->
                    <div class="news-card-body">
                        <!-- Date Metadata -->
                        <div class="news-card-date">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px; vertical-align: -1px;" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <time datetime="<?= htmlspecialchars($news['datetime_attr']) ?>"><?= $date ?></time>
                        </div>

                        <!-- Headline Title -->
                        <h3 class="news-card-title" title="<?= $title ?>">
                            <?= $title ?>
                        </h3>

                        <!-- Card Footer: "Baca Selengkapnya →" link with arrow animation -->
                        <div class="news-card-footer">
                            <span class="news-read-more">
                                <span>Baca Selengkapnya</span>
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="news-arrow" style="margin-left: 4px; vertical-align: -1px;" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                            </span>
                        </div>
                    </div>

                </a>
            </article>
            <?php endforeach; ?>
        </div><!-- /.news-grid -->
        <?php else: ?>
        <div class="news-empty-state text-center py-5">
            <div class="news-empty-icon mb-3">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="text-slate-400" aria-hidden="true"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/></svg>
            </div>
            <p class="text-muted">Belum ada publikasi terbaru saat ini.</p>
        </div>
        <?php endif; ?>

    </div><!-- /.container -->
</section><!-- /.section-news -->
