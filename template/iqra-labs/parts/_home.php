<?php
/**
 * =========================================================================
 * MANSAPA Iqra' Labs — _home.php  [FINAL PRODUCTION v2]
 * Place: template/mansapa iqra labs/parts/_home.php
 *
 * PRESERVED SLiMS hooks:
 *   $sysconf['template']['classic_popular_collection']
 *   $sysconf['template']['classic_new_collection']
 *   $sysconf['template']['classic_top_reader']
 *   $sysconf['template']['classic_map']
 *   <slims-collection>, <slims-group-subject>, <slims-group-member>
 *   $_GET['keywords'], htmlentities()
 * =========================================================================
 */
?>

<!-- Link Modern Homepage Design System (Pure CSS, 60fps animations & rich background canvas) -->
<link rel="stylesheet" href="<?= assets('css/home-modern.css?v=' . (file_exists(__DIR__ . '/../assets/css/home-modern.css') ? filemtime(__DIR__ . '/../assets/css/home-modern.css') : date('YmdHis'))); ?>">

<?php
// ------------------------------------------------------------------------
// Include Navbar
// ------------------------------------------------------------------------
include '_navbar.php';
?>

<!-- ================================================================
     HERO SECTION — Swiss Modernism 2.0 (Deep Navy & Luxurious Gold)
     2-Column Dynamic Layout: Narrative & CTAs on Left, Visual on Right
================================================================ -->
<section class="iqra-hero" aria-label="Hero Utama">

    <!-- Ambient Interactive Background Elements (Pure CSS, Zero Jitter) -->
    <div class="iqra-hero-bg-canvas" aria-hidden="true">
        <!-- Mathematical Dot Matrix Grid Overlay -->
        <div class="iqra-bg-grid"></div>
        <!-- Luminous Ambient Mesh Glow Orbs -->
        <div class="iqra-bg-orb iqra-orb-gold"></div>
        <div class="iqra-bg-orb iqra-orb-navy"></div>
        <div class="iqra-bg-orb iqra-orb-cyan"></div>

        <!-- Decorative Geometric Floating Elements -->
        <svg class="iqra-deco-shape iqra-shape-orbit" width="340" height="340" viewBox="0 0 340 340" fill="none">
            <circle cx="170" cy="170" r="150" stroke="rgba(212, 175, 55, 0.2)" stroke-width="1.5" stroke-dasharray="6 8"/>
            <circle cx="170" cy="170" r="105" stroke="rgba(13, 27, 61, 0.08)" stroke-width="1"/>
            <circle cx="295" cy="170" r="5" fill="#D4AF37" opacity="0.65"/>
            <circle cx="45" cy="170" r="3.5" fill="#0D1B3D" opacity="0.45"/>
        </svg>

        <svg class="iqra-deco-shape iqra-shape-crosshair" width="80" height="80" viewBox="0 0 80 80" fill="none">
            <line x1="40" y1="0" x2="40" y2="80" stroke="rgba(212, 175, 55, 0.28)" stroke-width="1"/>
            <line x1="0" y1="40" x2="80" y2="40" stroke="rgba(212, 175, 55, 0.28)" stroke-width="1"/>
            <circle cx="40" cy="40" r="12" stroke="rgba(13, 27, 61, 0.12)" stroke-width="1"/>
            <circle cx="40" cy="40" r="3" fill="#D4AF37"/>
        </svg>

        <svg class="iqra-deco-shape iqra-shape-plus" width="32" height="32" viewBox="0 0 32 32" fill="none">
            <path d="M16 6V26M6 16H26" stroke="rgba(13, 27, 61, 0.15)" stroke-width="2" stroke-linecap="round"/>
        </svg>
    </div><!-- /.iqra-hero-bg-canvas -->

    <div class="container iqra-container">
        <div class="hero-grid">

            <!-- ── LEFT COLUMN: Narrative & Call to Action ───────────── -->
            <div class="hero-content-col">

                <!-- Top Badge: Shimmer Pill with Live Pulsing Dot -->
                <div class="hero-badge animate-fade-down">
                    <span class="hero-badge-pulse" aria-hidden="true">
                        <span class="hero-badge-ring"></span>
                        <span class="hero-badge-dot"></span>
                    </span>
                    <span class="hero-badge-text">Laboratorium Literasi &amp; Gagasan</span>
                    <span class="hero-badge-sparkle" aria-hidden="true">&#10022;</span>
                </div>

                <!-- Main Headline (H1): High-Impact Typography with Gold Highlight -->
                <h1 class="hero-title animate-fade-down delay-1">
                    Singgah, Baca, <span class="hero-title-highlight">Cerita.</span>
                </h1>

                <!-- Subtitle / Intro Narrative (WCAG AAA) -->
                <p class="hero-subtitle animate-fade-down delay-2">
                    Selamat datang di <strong>MANSAPA Iqra' Labs</strong>. Ruang temu literasi, eksplorasi referensi akademik, dan laboratorium gagasan siswa.
                </p>

                <!-- Actions: Primary & Secondary CTAs with Guaranteed Inline SVGs -->
                <div class="hero-actions animate-fade-up delay-3">
                    <div class="hero-cta-wrap">
                        <a href="index.php?p=opac" id="hero-opac-cta" class="hero-primary-cta" role="button" aria-label="Jelajahi Katalog Buku (OPAC)">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="cta-svg-icon" aria-hidden="true">
                                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                            </svg>
                            <span>Jelajahi Katalog Buku (OPAC)</span>
                            <span class="cta-arrow" aria-hidden="true">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                            </span>
                        </a>
                    </div>
                </div>

                <!-- Trust / Features Badges: Enhanced Interactive Glassmorphic Chips with Inline SVGs -->
                <div class="hero-features animate-fade-up delay-4">
                    <div class="hero-feature-chip">
                        <div class="chip-icon-wrap" aria-hidden="true">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                <path d="M14 2v6l2.5-1.5L19 8V2"/>
                            </svg>
                        </div>
                        <div class="chip-text-wrap">
                            <span class="chip-title">Ribuan Koleksi</span>
                            <span class="chip-sub">Buku &amp; Jurnal Terkurasi</span>
                        </div>
                    </div>
                    <div class="hero-feature-chip">
                        <div class="chip-icon-wrap" aria-hidden="true">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <div class="chip-text-wrap">
                            <span class="chip-title">Akses Sivitas</span>
                            <span class="chip-sub">Terbuka Guru &amp; Siswa</span>
                        </div>
                    </div>
                    <div class="hero-feature-chip">
                        <div class="chip-icon-wrap" aria-hidden="true">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </div>
                        <div class="chip-text-wrap">
                            <span class="chip-title">Hari Kerja</span>
                            <span class="chip-sub">Layanan Pustaka Aktif</span>
                        </div>
                    </div>
                </div>

            </div><!-- /.hero-content-col -->

            <!-- ── RIGHT COLUMN: Library Showcase Visual Anchor ──────── -->
            <div class="hero-visual-col animate-fade-up delay-2">
                <div class="hero-visual-container">
                    <!-- Architectural Tech Corner Accents -->
                    <span class="hero-corner corner-tl" aria-hidden="true"></span>
                    <span class="hero-corner corner-br" aria-hidden="true"></span>

                    <!-- Main Photo Frame -->
                    <div class="hero-visual-frame">
                        <img src="<?= assets('images/gedung.jpg'); ?>"
                             alt="Gedung Laboratorium dan Perpustakaan Terpadu MAN 1 Pangandaran"
                             class="hero-visual-img"
                             loading="eager"
                             fetchpriority="high"
                             width="800"
                             height="600">
                        <div class="hero-visual-overlay" aria-hidden="true"></div>

                        <!-- Glassmorphic Tag in image corner with Inline SVG -->
                        <div class="hero-image-badge" aria-hidden="true">
                            <span class="image-badge-dot"></span>
                            <span>Kampus MAN 1 Pangandaran</span>
                        </div>
                    </div>

                    <!-- Single Compact Floating Card: Gedung Perpustakaan Terpadu -->
                    <div class="hero-floating-card hero-building-card">
                        <div class="floating-card-icon" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 21h18"/>
                                <path d="M5 21V10"/>
                                <path d="M19 21V10"/>
                                <path d="M9 21V10"/>
                                <path d="M15 21V10"/>
                                <path d="M2 10h20"/>
                                <path d="M12 3L2 10h20L12 3z"/>
                            </svg>
                        </div>
                        <div class="floating-card-text">
                            <strong class="d-block">Gedung Perpustakaan Terpadu</strong>
                            <span class="d-block">Laboratorium &amp; Riset Literasi Siswa</span>
                        </div>
                    </div>

                    <!-- Ambient Glow Behind Image -->
                    <div class="hero-visual-glow" aria-hidden="true"></div>
                </div><!-- /.hero-visual-container -->
            </div><!-- /.hero-visual-col -->

        </div><!-- /.hero-grid -->
    </div><!-- /.container -->
</section><!-- /.iqra-hero -->


<!-- ================================================================
     MAIN CONTENT — All homepage sections below the hero
================================================================ -->
<main class="iqra-main" id="slims-home">

    <!-- ============================================================
         SECTION 1: CURATED BOOKS SHOWCASE (Kurasi Buku Pilihan)
         6 curated books in a single row on desktop (3 cols x 2 rows on mobile)
    ============================================================ -->
    <?php include __DIR__ . '/curated_books.php'; ?>

    <!-- ============================================================
         SECTION 2: KABAR & AGENDA LITERASI (Publication Hub)
         Latest 3 news articles in 3-column responsive card layout
    ============================================================ -->
    <?php include __DIR__ . '/news_section.php'; ?>


    <!-- ============================================================
         SECTION 3 — NEW ARRIVALS (Koleksi Terbaru & Segar)
         Preserves SLiMS Vue hooks: <slims-group-subject> & <slims-collection>
    ============================================================ -->
    <?php if ($sysconf['template']['classic_new_collection'] ?? 1): ?>
    <section class="section-books py-section scroll-reveal" aria-labelledby="new-h">

        <!-- Ambient Atmospheric Canvas (Geometric Grid, Radiant Mesh Orbs & Architectural Accents) -->
        <div class="new-arrivals-bg-canvas" aria-hidden="true">
            <div class="new-arrivals-bg-grid"></div>
            <div class="new-arrivals-orb orb-navy"></div>
            <div class="new-arrivals-orb orb-gold"></div>

            <!-- Decorative Architectural SVG Accents -->
            <svg class="new-arrivals-deco-shape deco-shape-orbit" width="220" height="220" viewBox="0 0 220 220" fill="none">
                <circle cx="110" cy="110" r="95" stroke="rgba(212, 175, 55, 0.16)" stroke-width="1.2" stroke-dasharray="4 6"/>
                <circle cx="110" cy="110" r="65" stroke="rgba(13, 27, 61, 0.05)" stroke-width="1"/>
                <circle cx="195" cy="110" r="3" fill="#D4AF37" opacity="0.6"/>
                <circle cx="25" cy="110" r="2" fill="#0D1B3D" opacity="0.35"/>
            </svg>
            <svg class="new-arrivals-deco-shape deco-shape-cross" width="50" height="50" viewBox="0 0 50 50" fill="none">
                <line x1="25" y1="0" x2="25" y2="50" stroke="rgba(212, 175, 55, 0.2)" stroke-width="1"/>
                <line x1="0" y1="25" x2="50" y2="25" stroke="rgba(212, 175, 55, 0.2)" stroke-width="1"/>
                <circle cx="25" cy="25" r="6" stroke="rgba(13, 27, 61, 0.08)" stroke-width="1"/>
                <circle cx="25" cy="25" r="2" fill="#D4AF37"/>
            </svg>
        </div><!-- /.new-arrivals-bg-canvas -->

        <div class="container iqra-container">

            <!-- High-Impact Section Header -->
            <header class="new-arrivals-header animate-fade-down">
                <div class="new-arrivals-header-main">
                    <!-- Shimmer Pill Badge with Live Pulsing Dot -->
                    <div class="new-arrivals-badge">
                        <span class="new-arrivals-badge-pulse" aria-hidden="true">
                            <span class="new-arrivals-badge-ring"></span>
                            <span class="new-arrivals-badge-dot"></span>
                        </span>
                        <span class="new-arrivals-badge-text">Baru Ditambahkan</span>
                        <span class="new-arrivals-badge-sparkle" aria-hidden="true">&#10022;</span>
                    </div>

                    <h2 class="new-arrivals-title" id="new-h">
                        Koleksi Buku <span class="new-arrivals-title-highlight">Terbaru &amp; Segar</span>
                    </h2>
                    <p class="new-arrivals-sub mb-0">
                        Bahan pustaka dan literatur mutakhir yang siap memperkaya wawasan serta referensi akademik civitas madrasah.
                    </p>
                </div>

                <div class="new-arrivals-header-action">
                    <a href="index.php?search=search&sort=date" class="new-arrivals-btn-all" aria-label="Lihat Semua Koleksi Terbaru">
                        <span>Lihat Semua Katalog</span>
                        <span class="btn-arrow-circle" aria-hidden="true">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </span>
                    </a>
                </div>
            </header>

            <!-- Topics Filter Bar Wrap -->
            <div class="new-arrivals-topics-wrap animate-fade-down delay-1">
                <div class="new-arrivals-topics-lead">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="topics-lead-icon" aria-hidden="true"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                    <span>Topik Populer:</span>
                </div>
                <!-- SLiMS API Subject Pills Component — preserved exactly -->
                <slims-group-subject url="index.php?p=api/subject/latest"></slims-group-subject>
            </div>

            <!-- SLiMS API Collection Grid Component — preserved exactly -->
            <div class="new-arrivals-grid-container animate-fade-up delay-2">
                <slims-collection url="index.php?p=api/biblio/latest"></slims-collection>
            </div>

        </div><!-- /.container.iqra-container -->
    </section>
    <?php endif; ?>


    <!-- ============================================================
         SECTION 4: LEADERBOARD & HALL OF FAME (Pembaca Teraktif)
         Live circulation & visitor statistics directly from SLiMS database
    ============================================================ -->
    <?php include __DIR__ . '/top_readers.php'; ?>

    <!-- ============================================================
         SECTION 5: TAUTAN EKSTERNAL (Reference Portals Hub)
    ============================================================ -->
    <?php include __DIR__ . '/../partials/external_links.php'; ?>

    <!-- ============================================================
         SECTION 6: LOKASI & JAM OPERASIONAL (Maps & Service Hours)
    ============================================================ -->
    <?php include __DIR__ . '/../partials/location_section.php'; ?>

</main><!-- /#slims-home -->

