<?php
/**
 * =========================================================================
 * MANSAPA Iqra' Labs — Warta Perpustakaan [news_view.php]
 * File: template/iqra-labs/partials/news_view.php
 *
 * Route: index.php?p=news
 * 3-Column Responsive Card Grid for Library News & Announcements
 * Palette: Deep Navy (#0F172A), Warm Gold (#D4AF37), Slate-50 (#F8FAFC)
 * =========================================================================
 */

global $dbs, $sysconf;
$dbs = $dbs ?? \SLiMS\DB::getInstance('mysqli');

// -------------------------------------------------------------------------
// 1. QUERY & PAGINATION PARAMETERS
// -------------------------------------------------------------------------
$keywords = isset($_GET['keywords']) ? trim(strip_tags($_GET['keywords'])) : '';
$page     = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

// 9 items per page fits 3-column desktop grid perfectly (3 rows x 3 cols)
$limit = !empty($sysconf['news']['num_each_page']) ? (int)$sysconf['news']['num_each_page'] : 9;
if ($limit <= 0) {
    $limit = 9;
}
$offset = ($page - 1) * $limit;

// -------------------------------------------------------------------------
// 2. FETCH NEWS ARTICLES (SQL_CALC_FOUND_ROWS)
// -------------------------------------------------------------------------
$sql = "SELECT SQL_CALC_FOUND_ROWS content_id, content_title, content_desc, content_path, is_news, is_draft, input_date, last_update, publish_date 
        FROM content 
        WHERE is_news = 1 AND is_draft = 0";

if ($keywords !== '') {
    $escaped_kw = $dbs->escape_string($keywords);
    $sql .= " AND (content_title LIKE '%$escaped_kw%' OR content_desc LIKE '%$escaped_kw%')";
}

$sql .= " ORDER BY last_update DESC LIMIT $limit OFFSET $offset";
$res_news = $dbs->query($sql);

$total_res = $dbs->query("SELECT FOUND_ROWS()");
$total_row = $total_res ? $total_res->fetch_row() : [0];
$total_news = (int)($total_row[0] ?? 0);

// -------------------------------------------------------------------------
// 3. PROCESS NEWS ITEMS & EXTRACT THUMBNAILS
// -------------------------------------------------------------------------
$indonesian_months = [
    1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
    7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
];

$news_items = [];

if ($res_news && $res_news->num_rows > 0) {
    while ($row = $res_news->fetch_assoc()) {
        $raw_desc = stripslashes($row['content_desc'] ?? '');

        // 1. Featured Image Extraction: Parse first <img> tag from content string
        $extracted_image = '';
        if (preg_match('/<img.+?src=[\'"]([^\'"]+)[\'"].*?>/i', $raw_desc, $matches)) {
            $extracted_image = trim($matches[1]);
            // Rewrite legacy host paths if migrated from mansapalabs
            $extracted_image = preg_replace('#^https?://[^/]+/mansapalabs/#i', SWB, $extracted_image);
        }

        // 2. Date Formatting (Local Indonesian format, e.g. "24 Okt 2026")
        $date_field = !empty($row['last_update']) && $row['last_update'] !== '0000-00-00 00:00:00'
            ? $row['last_update']
            : (!empty($row['publish_date']) ? $row['publish_date'] : $row['input_date']);
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

        // 3. Clean Excerpt: Strip HTML tags, decode entities, limit to 110-130 characters
        $clean_desc = preg_replace('/\s+/', ' ', trim(strip_tags(html_entity_decode($raw_desc, ENT_QUOTES, 'UTF-8'))));
        $excerpt = mb_strimwidth($clean_desc, 0, 120, '...');

        // 4. Detail Link
        $content_path = trim($row['content_path'] ?? '');
        $detail_url = SWB . 'index.php?p=' . urlencode($content_path);

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

// -------------------------------------------------------------------------
// 4. SLiMS NATIVE PAGINATION GENERATION
// -------------------------------------------------------------------------
$pagination_html = '';
if ($total_news > $limit) {
    require_once SIMBIO . 'simbio_GUI/paging/simbio_paging.inc.php';
    $pagination_html = simbio_paging::paging($total_news, $limit, 5);
}
?>

<!-- Scoped Styles for Warta Perpustakaan (Ensures 100% fidelity on legacy SLiMS Tailwind) -->
<style>
/* ── Canvas & Wrapper ─────────────────────────────────────────────────── */
.news-page-wrapper {
    background-color: #F8FAFC;
    min-height: 80vh;
    padding-bottom: 4rem;
}

/* ── Section Header ──────────────────────────────────────────────────── */
.news-header-section {
    padding-top: 2.5rem;
    padding-bottom: 1.5rem;
    position: relative;
}

.news-pill-tag {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.3rem 0.85rem;
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.025em;
    border-radius: 9999px;
    background-color: rgba(212, 175, 55, 0.12);
    color: #B45309;
    border: 1px solid rgba(212, 175, 55, 0.35);
    margin-bottom: 0.75rem;
}

.news-main-heading {
    font-family: 'Poppins', sans-serif;
    font-weight: 800;
    font-size: 1.75rem;
    line-height: 1.25;
    letter-spacing: -0.025em;
    color: #0F172A;
    margin-bottom: 0.5rem;
}
@media (min-width: 768px) {
    .news-main-heading {
        font-size: 2.25rem;
    }
}

.news-sub-heading {
    color: #475569;
    font-size: 0.9375rem;
    line-height: 1.5;
    max-width: 38rem;
    margin: 0 auto 1.25rem auto;
}

/* ── Search Bar & Filter ─────────────────────────────────────────────── */
.news-search-toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-top: 1.25rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #E2E8F0;
}

.news-search-form {
    position: relative;
    width: 100%;
    max-width: 24rem;
}

.news-search-box {
    display: flex;
    align-items: center;
    background-color: #FFFFFF;
    border: 1px solid #CBD5E1;
    border-radius: 9999px;
    padding: 0.25rem 0.35rem 0.25rem 1rem;
    transition: all 0.2s ease;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
}
.news-search-box:focus-within {
    border-color: #D4AF37;
    box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.18);
}

.news-search-input {
    border: none;
    outline: none;
    background: transparent;
    font-size: 0.875rem;
    color: #0F172A;
    width: 100%;
    padding: 0.35rem 0.5rem 0.35rem 0.25rem;
}

.news-search-clear {
    color: #94A3B8;
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    text-decoration: none !important;
    transition: color 0.15s;
}
.news-search-clear:hover {
    color: #EF4444;
}

.news-search-btn {
    background-color: #0F172A;
    color: #FFFFFF;
    border: none;
    border-radius: 9999px;
    padding: 0.4rem 1rem;
    font-size: 0.8125rem;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.2s ease;
}
.news-search-btn:hover {
    background-color: #1E293B;
}

/* ── 3-Column Responsive Grid ────────────────────────────────────────── */
.news-cards-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
    margin-top: 1.75rem;
    margin-bottom: 2.5rem;
}
@media (min-width: 768px) {
    .news-cards-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (min-width: 1024px) {
    .news-cards-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

/* ── Card Component ──────────────────────────────────────────────────── */
.news-card-item {
    background-color: #FFFFFF;
    border: 1px solid rgba(226, 232, 240, 0.9);
    border-radius: 1rem;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.05);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    height: 100%;
    position: relative;
}
.news-card-item:hover {
    transform: translateY(-4px);
    border-color: #FBBF24;
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
}

.news-card-media {
    position: relative;
    width: 100%;
    aspect-ratio: 16 / 10;
    overflow: hidden;
    background-color: #F1F5F9;
}

.news-card-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}
.news-card-item:hover .news-card-img {
    transform: scale(1.05);
}

.news-card-date-badge {
    position: absolute;
    top: 0.75rem;
    left: 0.75rem;
    z-index: 10;
    background: rgba(15, 23, 42, 0.85);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    color: #FFFFFF;
    font-size: 11px;
    font-weight: 500;
    padding: 0.25rem 0.65rem;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.375rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.12);
}

/* Fallback Branded Placeholder */
.news-card-placeholder {
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    background: linear-gradient(135deg, #0F172A 0%, #1E293B 60%, #0F172A 100%);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.news-card-placeholder-icon {
    width: 3rem;
    height: 3rem;
    border-radius: 0.75rem;
    background: rgba(212, 175, 55, 0.15);
    border: 1px solid rgba(212, 175, 55, 0.35);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #D4AF37;
    margin-bottom: 0.5rem;
    position: relative;
    z-index: 2;
}

.news-card-placeholder-brand {
    font-family: monospace;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: rgba(212, 175, 55, 0.9);
    position: relative;
    z-index: 2;
}

/* ── Card Body & Typography ──────────────────────────────────────────── */
.news-card-body {
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    justify-content: space-between;
}

.news-card-title {
    font-family: 'Poppins', sans-serif;
    font-weight: 700;
    font-size: 1.0625rem;
    line-height: 1.4;
    color: #0F172A;
    margin-bottom: 0.5rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    transition: color 0.2s ease;
}
.news-card-item:hover .news-card-title {
    color: #D97706;
}

.news-card-excerpt {
    color: #475569;
    font-size: 0.875rem;
    line-height: 1.6;
    margin-bottom: 1rem;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.news-card-action {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: auto;
    padding-top: 0.875rem;
    border-top: 1px solid #F1F5F9;
}

.news-readmore-link {
    font-size: 0.75rem;
    font-weight: 600;
    color: #0F172A;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    transition: all 0.2s ease;
}
.news-readmore-link svg {
    transition: transform 0.2s ease;
}
.news-card-item:hover .news-readmore-link {
    color: #D97706;
}
.news-card-item:hover .news-readmore-link svg {
    transform: translateX(4px);
}

/* ── Empty State ─────────────────────────────────────────────────────── */
.news-empty-container {
    background-color: #FFFFFF;
    border: 1px solid #E2E8F0;
    border-radius: 1.25rem;
    padding: 3.5rem 1.5rem;
    text-align: center;
    margin: 2.5rem 0;
}

.news-empty-icon {
    width: 4rem;
    height: 4rem;
    border-radius: 1rem;
    background-color: #F8FAFC;
    border: 1px solid #E2E8F0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #94A3B8;
    margin-bottom: 1rem;
}

/* ── Pagination Styling (Native SLiMS Integration) ───────────────────── */
.news-pagination-wrap {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 2rem;
    margin-bottom: 1.5rem;
}

.news-pagination-wrap .pagingList {
    display: inline-flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
}

.news-pagination-wrap .pagingList b,
.news-pagination-wrap .pagingList a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 2.35rem;
    height: 2.35rem;
    padding: 0 0.65rem;
    border-radius: 0.625rem;
    font-size: 0.8125rem;
    font-weight: 600;
    text-decoration: none !important;
    transition: all 0.2s ease;
}

.news-pagination-wrap .pagingList b {
    background-color: #0F172A;
    color: #D4AF37;
    border: 1px solid #0F172A;
    box-shadow: 0 2px 4px rgba(15, 23, 42, 0.15);
}

.news-pagination-wrap .pagingList a {
    background-color: #FFFFFF;
    color: #334155;
    border: 1px solid #CBD5E1;
}
.news-pagination-wrap .pagingList a:hover {
    border-color: #FBBF24;
    color: #B45309;
    background-color: #FFFDF5;
    transform: translateY(-1px);
}
</style>

<div class="news-page-wrapper">

    <!-- ── 1. NAVBAR HEADER CONTAINER ───────────────────────────────────── -->
    <section id="section1" class="container-fluid p-0">
        <div class="c-header">
            <div class="mask"></div>
            <?php include __DIR__ . '/../parts/_navbar.php'; ?>
        </div>
    </section>

    <!-- ── 2. MAIN CONTAINER ────────────────────────────────────────────── -->
    <main class="container iqra-container max-w-6xl mx-auto px-4">

        <!-- ── Page Header ─────────────────────────────────────────────── -->
        <header class="news-header-section text-center">
            
            <!-- Category Tag -->
            <div>
                <span class="news-pill-tag">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></svg>
                    <span>Warta &amp; Publikasi</span>
                </span>
            </div>

            <!-- Title & Subtitle -->
            <h1 class="news-main-heading">Warta Perpustakaan</h1>
            <p class="news-sub-heading">Kabar kegiatan, literasi, dan informasi resmi seputar MANSAPA Iqra' Labs.</p>

            <!-- Search & Status Toolbar -->
            <div class="news-search-toolbar">
                
                <!-- Status Badge (Replaces generic alert-info) -->
                <div>
                    <span class="text-xs font-semibold px-3 py-1 bg-amber-50 text-amber-700 border border-amber-200/60 rounded-full inline-flex items-center gap-1.5 shadow-sm">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse" aria-hidden="true"></span>
                        <span>
                            <?= empty($keywords)
                                ? sprintf('Menampilkan %d Warta Terbaru', $total_news)
                                : sprintf('Menampilkan %d Warta untuk &ldquo;%s&rdquo;', $total_news, htmlspecialchars($keywords)) ?>
                        </span>
                    </span>
                </div>

                <!-- Live Search Form -->
                <form action="<?= SWB ?>index.php" method="get" class="news-search-form" role="search" aria-label="Cari warta perpustakaan">
                    <input type="hidden" name="p" value="news">
                    <div class="news-search-box">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="margin-right: 4px;"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        <input type="text" name="keywords" value="<?= htmlspecialchars($keywords) ?>" placeholder="Cari warta kegiatan..." class="news-search-input" aria-label="Kata kunci warta">
                        <?php if (!empty($keywords)): ?>
                            <a href="<?= SWB ?>index.php?p=news" class="news-search-clear" title="Reset pencarian" aria-label="Reset pencarian">&times;</a>
                        <?php endif; ?>
                        <button type="submit" class="news-search-btn">Cari</button>
                    </div>
                </form>

            </div>
        </header>

        <!-- ── 3. 3-COLUMN RESPONSIVE CARD GRID ────────────────────────── -->
        <?php if (!empty($news_items)): ?>
            <div class="news-cards-grid" role="list">
                <?php foreach ($news_items as $news):
                    $title      = htmlspecialchars($news['title']);
                    $date       = htmlspecialchars($news['date']);
                    $excerpt    = htmlspecialchars($news['excerpt']);
                    $detail_url = htmlspecialchars($news['url']);
                    $image_src  = $news['image'];
                    $has_image  = !empty($image_src);
                    $card_id    = $news['id'];
                ?>
                <article class="news-card-item group" role="listitem">
                    
                    <!-- Top Image Container (aspect-ratio 16/10) -->
                    <div class="news-card-media">
                        
                        <!-- Floating Date Tag -->
                        <div class="news-card-date-badge">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#D4AF37" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <time datetime="<?= htmlspecialchars($news['datetime_attr']) ?>"><?= $date ?></time>
                        </div>

                        <?php if ($has_image): ?>
                            <img src="<?= htmlspecialchars($image_src) ?>" 
                                 alt="<?= $title ?>" 
                                 class="news-card-img" 
                                 loading="lazy"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            
                            <!-- Branded Fallback Placeholder (Triggered on image load failure) -->
                            <div class="news-card-placeholder" style="display: none;" aria-hidden="true">
                                <svg class="absolute inset-0 w-full h-full opacity-10" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                                    <defs>
                                        <pattern id="cardPatFb<?= $card_id ?>" width="24" height="24" patternUnits="userSpaceOnUse">
                                            <path d="M 24 0 L 0 0 0 24" fill="none" stroke="#D4AF37" stroke-width="1"/>
                                        </pattern>
                                    </defs>
                                    <rect width="100%" height="100%" fill="url(#cardPatFb<?= $card_id ?>)" />
                                </svg>
                                <div class="news-card-placeholder-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M6 6h10"/><path d="M6 10h10"/></svg>
                                </div>
                                <span class="news-card-placeholder-brand">MANSAPA Iqra' Labs</span>
                            </div>

                        <?php else: ?>
                            <!-- Branded Fallback Placeholder (When no image tag exists in content) -->
                            <div class="news-card-placeholder" aria-hidden="true">
                                <svg class="absolute inset-0 w-full h-full opacity-10" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                                    <defs>
                                        <pattern id="cardPat<?= $card_id ?>" width="24" height="24" patternUnits="userSpaceOnUse">
                                            <path d="M 24 0 L 0 0 0 24" fill="none" stroke="#D4AF37" stroke-width="1"/>
                                        </pattern>
                                    </defs>
                                    <rect width="100%" height="100%" fill="url(#cardPat<?= $card_id ?>)" />
                                </svg>
                                <div class="news-card-placeholder-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M6 6h10"/><path d="M6 10h10"/></svg>
                                </div>
                                <span class="news-card-placeholder-brand">MANSAPA Iqra' Labs</span>
                            </div>
                        <?php endif; ?>

                    </div><!-- /.news-card-media -->

                    <!-- Card Body -->
                    <div class="news-card-body">
                        <div>
                            <h2 class="news-card-title">
                                <a href="<?= $detail_url ?>" class="text-inherit hover:text-amber-600 transition-colors" style="text-decoration: none;">
                                    <?= $title ?>
                                </a>
                            </h2>
                            <p class="news-card-excerpt">
                                <?= $excerpt ?>
                            </p>
                        </div>

                        <!-- Card Action -->
                        <div class="news-card-action">
                            <a href="<?= $detail_url ?>" class="news-readmore-link" aria-label="Baca selengkapnya mengenai <?= $title ?>">
                                <span>Baca Selengkapnya</span>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
                            </a>
                        </div>
                    </div><!-- /.news-card-body -->

                </article>
                <?php endforeach; ?>
            </div><!-- /.news-cards-grid -->

            <!-- ── Native SLiMS Pagination ──────────────────────────────── -->
            <?php if (!empty($pagination_html)): ?>
                <nav class="news-pagination-wrap" aria-label="Navigasi Halaman Warta Perpustakaan">
                    <?= $pagination_html ?>
                </nav>
            <?php endif; ?>

        <?php else: ?>
            <!-- ── Empty State ─────────────────────────────────────────── -->
            <div class="news-empty-container">
                <div class="news-empty-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/></svg>
                </div>
                <h3 style="font-family: 'Poppins', sans-serif; font-weight: 700; color: #0F172A; margin-bottom: 0.5rem;">Tidak Ada Warta</h3>
                <p style="color: #64748B; font-size: 0.875rem; margin-bottom: 1.25rem;">
                    <?= !empty($keywords) 
                        ? 'Tidak ditemukan warta yang cocok dengan kata kunci "' . htmlspecialchars($keywords) . '".' 
                        : 'Belum ada warta atau pengumuman resmi yang dipublikasikan saat ini.' ?>
                </p>
                <?php if (!empty($keywords)): ?>
                    <a href="<?= SWB ?>index.php?p=news" class="btn btn-dark btn-sm rounded-pill px-4">
                        Tampilkan Semua Warta
                    </a>
                <?php else: ?>
                    <a href="<?= SWB ?>" class="btn btn-dark btn-sm rounded-pill px-4">
                        Kembali ke Beranda
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </main>

</div>
