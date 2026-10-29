<?php
# @Author: Waris Agung Widodo <user>
# @Filename: _result-search.php
# Redesigned for MANSAPA Iqra' Labs Modern Search Experience (UI/UX Pro Max)

$keywords = $keywords ?? ($_GET['keywords'] ?? '');
$num_rows = (int)$engine->getNumRows();
$view_mode = $_SESSION['LIST_VIEW'] ?? 'list';
$current_url_params = array_filter($_GET, fn($key) => $key !== 'csrf_token', ARRAY_FILTER_USE_KEY);
$toggle_action_url = $_SERVER['PHP_SELF'] . '?' . http_build_query($current_url_params);
$csrf_token = isset($opac) ? $opac->getCsrf() : ($_SESSION['csrf_token'] ?? '');
?>

<!-- ================================================================
     SEARCH RESULTS PAGE — MANSAPA IQRA' LABS
     Layout: 2-Column Responsive with Sticky Filter & Structured Cards
================================================================ -->
<div class="result-search-page">

    <!-- ── 1. NAVBAR HEADER CONTAINER ─────────────────────────────────── -->
    <section id="section1" class="container-fluid p-0">
        <div class="c-header">
            <?php include '_navbar.php'; ?>
        </div>
    </section>

    <!-- ── 2. SEARCH HEADER BAR (CLEAN & COMPACT) ──────────────────────── -->
    <header class="search-hero-banner py-3">
        <div class="container iqra-container">
            <div class="search-hero-intro text-center mb-2">
                <h1 class="search-hero-heading mb-0">
                    Katalog Perpustakaan
                </h1>
            </div>

            <!-- Integrated Vue.js Search Bar -->
            <div class="search-form-embedded">
                <?php include '_search-form.php'; ?>
            </div>
        </div>
    </header>

    <!-- ── 3. MAIN RESULTS & FILTER SECTION ──────────────────────────────── -->
    <main class="container iqra-container search-main-content py-3">

        <!-- Controls Toolbar Card -->
        <div class="search-toolbar-card mb-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                
                <!-- Left: Stats & Keywords Chips -->
                <div class="search-toolbar-left d-flex align-items-center flex-wrap gap-2">
                    <span class="search-stats-clean">
                        Menampilkan <strong><?= number_format($num_rows, 0, ',', '.') ?></strong> judul koleksi
                    </span>
                    <?php if (!empty($keywords)): ?>
                        <span class="search-keyword-chip" title="<?= htmlentities($keywords) ?>">
                            Kata kunci: <strong>&ldquo;<?= (strlen($keywords) > 28) ? htmlentities(substr($keywords, 0, 28)) . '...' : htmlentities($keywords) ?>&rdquo;</strong>
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Right: Sort, Mobile Filter Toggle, and View Segmented Switch -->
                <div class="search-toolbar-right d-flex align-items-center justify-content-between justify-content-md-end flex-wrap gap-2">
                    
                    <!-- Mobile Filter Drawer Toggle Button -->
                    <button class="btn btn-sm btn-filter-drawer-toggle d-md-none" type="button" data-toggle="collapse" data-target="#filterSidebarCollapse" aria-expanded="false" aria-controls="filterSidebarCollapse">
                        <i class="fas fa-sliders-h mr-1.5" aria-hidden="true"></i> Filter Koleksi
                    </button>

                    <!-- Sort Group -->
                    <div class="search-sort-group d-flex align-items-center">
                        <label class="sort-label mr-2 mb-0 text-xs font-weight-bold text-slate-600" for="search-order">
                            <?= __('Urutkan:') ?>
                        </label>
                        <select class="custom-select custom-select-sm search-sort-select" id="search-order">
                            <?= $sort_select ?>
                        </select>
                    </div>

                    <!-- View Switch (Segmented List & Grid) -->
                    <div class="btn-group btn-group-sm view-toggle-group" role="group" aria-label="<?= __('Pilihan Tampilan') ?>">
                        <form class="m-0 d-inline" method="POST" action="<?= $toggle_action_url ?>">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>"/>
                            <input type="hidden" name="view" value="list" />
                            <button type="submit" class="btn btn-view-segment <?= ($view_mode === 'list') ? 'is-active' : '' ?>" title="<?= __('Tampilan Daftar') ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-view-list mr-1" viewBox="0 0 16 16"><path d="M3 4.5h10a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2zm0 1a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1H3zM1 2a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1h-13A.5.5 0 0 1 1 2zm0 12a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1h-13A.5.5 0 0 1 1 14z"/></svg>
                                <span>Daftar</span>
                            </button>
                        </form>
                        <form class="m-0 d-inline" method="POST" action="<?= $toggle_action_url ?>">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>"/>
                            <input type="hidden" name="view" value="grid" />
                            <button type="submit" class="btn btn-view-segment <?= ($view_mode === 'grid') ? 'is-active' : '' ?>" title="<?= __('Tampilan Kisi') ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-grid-3x3-gap mr-1" viewBox="0 0 16 16"><path d="M4 2v2H2V2h2zm1 12v-2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1zm0-5V7a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1zm0-5V2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1zm5 10v-2a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1zm0-5V7a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1zm0-5V2a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1zM9 2v2H7V2h2zm5 0v2h-2V2h2zM4 7v2H2V7h2zm5 0v2H7V7h2zm5 0v2h-2V7h2zM4 12v2H2v-2h2zm5 0v2H7v-2h2zm5 0v2h-2v-2h2zM12 1a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1h-2zm-1 6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V7zm1 4a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1h-2z"/></svg>
                                <span>Kisi</span>
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!-- 2-Column Responsive Layout -->
        <div class="row align-items-start">
            
            <!-- Left: Sidebar Filter -->
            <aside class="col-lg-3 col-md-4 mb-4">
                <div class="search-filter-sticky-wrapper collapse d-md-block" id="filterSidebarCollapse">
                    <div class="filter-sidebar-card">
                        <div class="filter-sidebar-header d-flex justify-content-between align-items-center">
                            <h3 class="filter-sidebar-title mb-0">
                                <?= __('Filter Koleksi') ?>
                            </h3>
                            <a href="index.php?search=search&keywords=<?= urlencode($keywords) ?>" class="filter-reset-link" title="<?= __('Reset filter pencarian') ?>">
                                <?= __('Reset') ?>
                            </a>
                        </div>
                        <div class="filter-sidebar-body p-3">
                            <?= $engine->getFilter($opac, true) ?>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Right: Search Results Feed -->
            <section class="col-lg-9 col-md-8">
                <div class="search-results-wrapper">
                    <?php if (ENVIRONMENT == 'development' && !empty($engine->getError())): ?>
                        <div class="alert alert-danger mb-3 shadow-sm rounded-lg">
                            <i class="fas fa-exclamation-triangle mr-1.5" aria-hidden="true"></i> <?= $engine->getError() ?>
                        </div>
                    <?php endif; ?>

                    <?php if (trim(strip_tags($main_content)) === ''): ?>
                        <!-- Modern Friendly Empty State -->
                        <div class="search-empty-state-card text-center p-5 shadow-sm rounded-xl">
                            <div class="empty-state-icon-wrap mb-3">
                                <div class="empty-icon-circle mx-auto">
                                    <i class="fas fa-search-minus fa-3x text-gold" aria-hidden="true"></i>
                                </div>
                            </div>
                            <h3 class="empty-state-title mb-2"><?= __('Koleksi Tidak Ditemukan') ?></h3>
                            <p class="empty-state-desc text-slate-500 max-w-md mx-auto mb-4 text-sm">
                                <?= __('Maaf, tidak ada judul buku yang cocok dengan kata kunci') ?> &ldquo;<strong><?= htmlentities($keywords) ?></strong>&rdquo;.
                            </p>
                            <div class="empty-state-tips p-3 bg-light-soft rounded-lg text-left max-w-md mx-auto mb-4 text-xs text-slate-600">
                                <p class="font-weight-bold text-slate-800 mb-1.5"><i class="fas fa-lightbulb text-gold mr-1" aria-hidden="true"></i> Saran Pencarian:</p>
                                <ul class="pl-3 mb-0">
                                    <li>Periksa kembali kemungkinan salah ketik atau ejaan kata kunci.</li>
                                    <li>Gunakan kata kunci yang lebih umum (misalnya: <em>Sejarah</em>, <em>Fisika</em>, <em>Matematika</em>).</li>
                                    <li>Coba cari berdasarkan nama pengarang atau nomor ISBN.</li>
                                </ul>
                            </div>
                            <div class="empty-state-actions d-flex justify-content-center gap-2">
                                <a href="index.php?search=search" class="btn btn-sm btn-primary">
                                    <i class="fas fa-book-open mr-1.5" aria-hidden="true"></i> <?= __('Lihat Semua Koleksi') ?>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#adv-modal">
                                    <i class="fas fa-sliders-h mr-1.5" aria-hidden="true"></i> <?= __('Pencarian Spesifik') ?>
                                </button>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="biblio-results-feed">
                            <?= $main_content ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

        </div>
    </main>

</div>

<!-- ================================================================
     SEARCH RESULTS ENHANCED MODERN STYLESHEET (UI/UX PRO MAX)
================================================================ -->
<style>
/* ================================================================
   CLEAN & SEAMLESS OPAC SEARCH STYLESHEET (NO ANIMATIONS / HIGH CLARITY)
================================================================ */

/* ── 0. ELIMINATE ALL ANIMATIONS & LAG ───────────────────────────── */
*, *::before, *::after {
    animation: none !important;
    transition: none !important;
}

/* Allow instant color/background changes only */
a, button, input, select {
    transition: color 0.1s ease, background-color 0.1s ease, border-color 0.1s ease !important;
}

.animate-pulse {
    animation: none !important;
}

/* ── 1. COMPACT SEARCH HEADER ────────────────────────────────────── */
.search-hero-banner {
    background: #0F172A !important;
    border-bottom: 1px solid #1E293B !important;
    position: relative;
    z-index: 2;
}

.search-hero-heading {
    color: #F8FAFC !important;
    font-size: 1.25rem !important;
    font-weight: 700 !important;
    letter-spacing: -0.01em;
}

.search-form-embedded .search-input-card {
    border-radius: 8px !important;
    background: #FFFFFF !important;
    border: 1px solid #CBD5E1 !important;
    box-shadow: none !important;
}

.search-form-embedded .search-input-card:focus-within {
    border-color: #3B82F6 !important;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2) !important;
}

.search-form-embedded #search-input {
    font-size: 0.9rem !important;
    color: #0F172A !important;
    border: none !important;
    outline: none !important;
    background: transparent !important;
}

.btn-search-go {
    background: #0F172A !important;
    color: #FFFFFF !important;
    font-size: 0.85rem !important;
    font-weight: 600 !important;
    padding: 0.45rem 1rem !important;
    border-radius: 6px !important;
    border: 1px solid #0F172A !important;
}

.btn-search-go:hover {
    background: #1E293B !important;
    color: #FFFFFF !important;
}

/* ── 1.2 SEARCH DROPDOWN & ADVANCED PANEL (HIGH VISIBILITY & CONTRAST) ── */
#advanced-wraper,
.advanced-wraper {
    background: #1E293B !important;
    border: 1px solid #334155 !important;
    border-radius: 12px !important;
    padding: 1.1rem 1.35rem !important;
    margin-top: 0.75rem !important;
    box-shadow: 0 16px 36px -4px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.05) !important;
    color: #F8FAFC !important;
    position: relative !important;
    z-index: 50 !important;
}

#advanced-wraper .label,
#advanced-wraper .search-section-label {
    color: #CBD5E1 !important;
    font-size: 0.82rem !important;
    font-weight: 600 !important;
    letter-spacing: 0.02em !important;
    margin-bottom: 0.65rem !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
}

#advanced-wraper .fa-times-circle {
    color: #EF4444 !important;
    font-size: 1.15rem !important;
    cursor: pointer !important;
    opacity: 0.9 !important;
}

#advanced-wraper .fa-times-circle:hover {
    color: #F87171 !important;
    opacity: 1 !important;
}

#advanced-wraper .d-flex.flex-wrap {
    gap: 0.5rem !important;
}

#advanced-wraper .btn {
    font-size: 0.8rem !important;
    font-weight: 600 !important;
    padding: 0.38rem 0.85rem !important;
    border-radius: 6px !important;
    margin: 0 !important;
    line-height: 1.4 !important;
    text-decoration: none !important;
}

#advanced-wraper .btn-primary,
#advanced-wraper .btn-primary.text-white {
    background-color: #2563EB !important;
    border-color: #3B82F6 !important;
    color: #FFFFFF !important;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.4) !important;
}

#advanced-wraper .btn-primary:hover {
    background-color: #1D4ED8 !important;
    border-color: #2563EB !important;
    color: #FFFFFF !important;
}

#advanced-wraper .btn-outline-secondary,
#advanced-wraper .search-scope-btn:not(.btn-primary) {
    background-color: rgba(255, 255, 255, 0.06) !important;
    border: 1px solid #475569 !important;
    color: #F1F5F9 !important;
}

#advanced-wraper .btn-outline-secondary:hover,
#advanced-wraper .search-scope-btn:not(.btn-primary):hover {
    background-color: rgba(255, 255, 255, 0.14) !important;
    border-color: #94A3B8 !important;
    color: #FFFFFF !important;
}

#advanced-wraper .btn-light,
#advanced-wraper button.btn-light[disabled],
#advanced-wraper .search-ortry-btn {
    background-color: transparent !important;
    border: 1px dashed #475569 !important;
    color: #94A3B8 !important;
    font-size: 0.72rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.06em !important;
    padding: 0.38rem 0.75rem !important;
    opacity: 1 !important;
    cursor: default !important;
    pointer-events: none !important;
}

#advanced-wraper .btn-outline-primary,
#advanced-wraper .search-adv-btn {
    background-color: rgba(59, 130, 246, 0.12) !important;
    border: 1px solid #3B82F6 !important;
    color: #93C5FD !important;
}

#advanced-wraper .btn-outline-primary:hover,
#advanced-wraper .search-adv-btn:hover {
    background-color: #2563EB !important;
    border-color: #2563EB !important;
    color: #FFFFFF !important;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.4) !important;
}

#advanced-wraper .search-history-heading {
    color: #94A3B8 !important;
    font-size: 0.75rem !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.06em !important;
    margin-top: 1.15rem !important;
    margin-bottom: 0.5rem !important;
    padding-top: 0.75rem !important;
    border-top: 1px solid #334155 !important;
    display: block !important;
}

#advanced-wraper a.text-grey-darkest,
#advanced-wraper a.search-history-link,
#advanced-wraper a[class*="text-grey-darkest"] {
    color: #F1F5F9 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    padding: 0.4rem 0.6rem !important;
    border-radius: 6px !important;
    text-decoration: none !important;
    font-size: 0.85rem !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.04) !important;
}

#advanced-wraper a.text-grey-darkest:last-of-type,
#advanced-wraper a.search-history-link:last-of-type {
    border-bottom: none !important;
}

#advanced-wraper a.text-grey-darkest:hover,
#advanced-wraper a.search-history-link:hover {
    background-color: rgba(255, 255, 255, 0.08) !important;
    color: #60A5FA !important;
}

#advanced-wraper a.text-grey-darkest i.fa-clock,
#advanced-wraper a.search-history-link .search-history-icon,
#advanced-wraper .text-grey-dark.fa-clock {
    color: #94A3B8 !important;
    font-size: 0.85rem !important;
    margin-right: 0.5rem !important;
}

#advanced-wraper a.text-grey-darkest span.italic,
#advanced-wraper a.search-history-link .search-history-text {
    color: #F8FAFC !important;
    font-style: normal !important;
    font-weight: 500 !important;
    letter-spacing: 0.01em !important;
}

#advanced-wraper a.text-grey-darkest:hover span.italic,
#advanced-wraper a.search-history-link:hover .search-history-text {
    color: #60A5FA !important;
}

#advanced-wraper a.text-grey-darkest i.fa-angle-right,
#advanced-wraper a.search-history-link .search-history-arrow,
#advanced-wraper .text-grey-dark.fa-angle-right {
    color: #64748B !important;
    font-size: 0.8rem !important;
}

#advanced-wraper a.text-grey-darkest:hover i.fa-angle-right,
#advanced-wraper a.search-history-link:hover .search-history-arrow {
    color: #94A3B8 !important;
    transform: translateX(3px) !important;
}

/* ── 1.5 MASK REMOVAL (ZERO FLOATING LAYERS) ─────────────────────── */
.c-header .mask {
    display: none !important;
}

/* ── 2. CONTROLS TOOLBAR (FLAT, NO FLOATING CARD) ────────────────── */
.search-toolbar-card {
    background: transparent !important;
    border: none !important;
    border-bottom: 1px solid #E2E8F0 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    padding: 0.5rem 0 1rem 0 !important;
}

.search-stats-clean {
    font-size: 0.875rem !important;
    color: #475569 !important;
}

.search-stats-clean strong {
    color: #0F172A !important;
    font-weight: 700 !important;
}

.search-keyword-chip {
    background: #F1F5F9 !important;
    border: 1px solid #E2E8F0 !important;
    color: #1E293B !important;
    font-size: 0.8rem !important;
    padding: 0.25rem 0.6rem !important;
    border-radius: 4px !important;
}

.search-sort-select {
    border-radius: 6px !important;
    border-color: #CBD5E1 !important;
    font-size: 0.82rem !important;
    height: calc(2rem + 2px) !important;
    background-color: #FFFFFF !important;
    color: #0F172A !important;
    font-weight: 500 !important;
}

.view-toggle-group {
    background: #F1F5F9 !important;
    padding: 2px !important;
    border-radius: 6px !important;
}

.btn-view-segment {
    display: inline-flex !important;
    align-items: center !important;
    border-radius: 4px !important;
    border: none !important;
    background: transparent !important;
    color: #64748B !important;
    font-size: 0.8rem !important;
    font-weight: 600 !important;
    padding: 0.3rem 0.65rem !important;
}

.btn-view-segment:hover {
    color: #0F172A !important;
}

.btn-view-segment.is-active {
    background: #0F172A !important;
    color: #FFFFFF !important;
}

.btn-filter-drawer-toggle {
    background: #0F172A !important;
    color: #FFFFFF !important;
    font-size: 0.8rem !important;
    font-weight: 600 !important;
    padding: 0.35rem 0.8rem !important;
    border-radius: 6px !important;
}

/* ── 3. CLEAN FILTER SIDEBAR (STATIC, NO FLOATING STICKY) ────────── */
.search-filter-sticky-wrapper {
    position: static !important;
    top: auto !important;
    z-index: auto !important;
}

.filter-sidebar-card {
    background: #FFFFFF !important;
    border: 1px solid #E2E8F0 !important;
    border-radius: 8px !important;
    overflow: hidden !important;
}

.filter-sidebar-header {
    background: #F8FAFC !important;
    padding: 0.75rem 1rem !important;
    border-bottom: 1px solid #E2E8F0 !important;
}

.filter-sidebar-title {
    font-size: 0.875rem !important;
    font-weight: 700 !important;
    color: #0F172A !important;
}

.filter-reset-link {
    font-size: 0.75rem !important;
    font-weight: 600 !important;
    color: #64748B !important;
    text-decoration: none !important;
}

.filter-reset-link:hover {
    color: #0F172A !important;
}

.filter-sidebar-body .list-group-item {
    border-color: #F1F5F9 !important;
    padding: 0.65rem 0.25rem !important;
}

.filter-sidebar-body strong.text-sm {
    color: #1E293B !important;
    font-weight: 600 !important;
    font-size: 0.82rem !important;
}

/* IonRangeSlider Minimal & Clean */
.irs--flat {
    height: 48px !important;
}

.irs--flat .irs-line {
    top: 24px !important;
    height: 4px !important;
    background-color: #E2E8F0 !important;
    border-radius: 2px !important;
    border: none !important;
}

.irs--flat .irs-bar {
    top: 24px !important;
    height: 4px !important;
    background: #0F172A !important;
    border-radius: 2px !important;
}

.irs--flat .irs-handle {
    top: 17px !important;
    width: 18px !important;
    height: 18px !important;
    background-color: #FFFFFF !important;
    border: 2px solid #0F172A !important;
    border-radius: 50% !important;
    box-shadow: none !important;
    cursor: grab !important;
    transform: none !important;
}

.irs--flat .irs-handle:hover,
.irs--flat .irs-handle.state_hover {
    background-color: #0F172A !important;
    transform: none !important;
}

.irs--flat .irs-handle > i:first-child {
    display: none !important;
}

.irs--flat .irs-from,
.irs--flat .irs-to,
.irs--flat .irs-single {
    font-size: 0.7rem !important;
    font-weight: 600 !important;
    color: #FFFFFF !important;
    background-color: #1E293B !important;
    border-radius: 4px !important;
    padding: 1px 5px !important;
    border: none !important;
    box-shadow: none !important;
}

.irs--flat .irs-from:before,
.irs--flat .irs-to:before,
.irs--flat .irs-single:before {
    border-top-color: #1E293B !important;
}

.irs--flat .irs-min,
.irs--flat .irs-max {
    font-size: 0.68rem !important;
    color: #94A3B8 !important;
    background: transparent !important;
}

.filter-sidebar-body .js-input-from,
.filter-sidebar-body .js-input-to {
    height: 32px !important;
    font-size: 0.82rem !important;
    border-radius: 6px !important;
    border: 1px solid #CBD5E1 !important;
    color: #0F172A !important;
    text-align: center !important;
}

.filter-sidebar-body .form-check-label {
    font-size: 0.82rem !important;
    color: #334155 !important;
    cursor: pointer !important;
}

/* ── HIDE TOP PAGINATION (MATCH DEFAULT SLIMS BEHAVIOR) ───────────── */
.biblioPagingTop {
    display: none !important;
}

/* ── 4. FLAT CLEAN BIBLIO RESULT CARD (ZERO FLOATING / GLOW LAYERS) ─ */
.search-results-wrapper,
.biblio-results-feed,
.biblioResult,
.biblioResult > *,
.biblioPaging {
    background: transparent !important;
    background-color: transparent !important;
    border: none !important;
    box-shadow: none !important;
    border-radius: 0 !important;
    filter: none !important;
    transform: none !important;
    overflow: visible !important;
}

.biblioResult:hover,
.biblio-results-feed:hover,
.search-results-wrapper:hover {
    background: transparent !important;
    background-color: transparent !important;
    box-shadow: none !important;
    transform: none !important;
    filter: none !important;
}

.biblio-card,
.book-card,
.biblio-card-list {
    display: flex !important;
    flex-direction: row !important;
    background: transparent !important;
    background-color: transparent !important;
    border: none !important;
    border-bottom: 1px solid #E2E8F0 !important;
    border-radius: 0 !important;
    padding: 1.25rem 0 !important;
    margin-bottom: 0 !important;
    gap: 1.25rem !important;
    box-shadow: none !important;
    position: static !important;
    overflow: visible !important;
    transform: none !important;
    animation: none !important;
    transition: none !important;
    filter: none !important;
}

.biblio-card::before,
.biblio-card::after,
.book-card::before,
.book-card::after,
.biblioResult::before,
.biblioResult::after,
.biblio-results-feed::before,
.biblio-results-feed::after {
    display: none !important;
    content: none !important;
    box-shadow: none !important;
    border: none !important;
    background: transparent !important;
}

.biblio-card:last-child,
.book-card:last-child,
.biblio-card-list:last-child {
    border-bottom: none !important;
}

.biblio-card:hover,
.book-card:hover,
.biblio-card-list:hover {
    border-color: #CBD5E1 !important;
    box-shadow: none !important;
    transform: none !important;
    background: transparent !important;
    background-color: transparent !important;
    filter: none !important;
}

/* Compact & Balanced Cover Column */
.biblio-card-cover {
    width: 88px !important;
    min-width: 88px !important;
    max-width: 88px !important;
    flex-shrink: 0 !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
}

.biblio-cover-link {
    display: block !important;
    width: 88px !important;
    height: 122px !important;
    overflow: hidden !important;
    border-radius: 6px !important;
    background-color: #F8FAFC !important;
    border: 1px solid #E2E8F0 !important;
}

.biblio-thumb {
    width: 100% !important;
    height: 100% !important;
    max-height: 122px !important;
    aspect-ratio: 1 / 1.38 !important;
    object-fit: cover !important;
    display: block !important;
    border: none !important;
    border-radius: 0 !important;
    animation: none !important;
    transition: none !important;
}

.cover-tag-borrowed {
    display: block !important;
    width: 100% !important;
    text-align: center !important;
    margin-top: 0.35rem !important;
    font-size: 0.65rem !important;
    font-weight: 600 !important;
    color: #DC2626 !important;
    background: #FEF2F2 !important;
    border: 1px solid #FECACA !important;
    padding: 0.1rem 0.25rem !important;
    border-radius: 4px !important;
    line-height: 1.2 !important;
}

/* Right Body Column: Natural Flow, No Tubes */
.biblio-card-body {
    flex: 1 1 0% !important;
    min-width: 0 !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
    background: transparent !important;
}

/* ── STRICT RESET: PURGE ALL TUBE / CAPSULE / SKELETON BAR ARTIFACTS ── */
.biblio-card-body,
.biblio-header-block,
.biblio-title,
.biblio-title a,
.biblio-author-line,
.author-prefix,
.author-names,
.author-clean-link,
.biblio-meta-grid,
.meta-item,
.meta-lbl,
.meta-val,
.biblio-synopsis-clean,
.biblio-footer-bar,
.biblio-status-wrap {
    background: transparent !important;
    background-color: transparent !important;
    border: none !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    outline: none !important;
    text-shadow: none !important;
}

.biblio-card-body:hover,
.biblio-header-block:hover,
.biblio-title:hover,
.biblio-author-line:hover,
.author-names:hover,
.author-prefix:hover,
.biblio-meta-grid:hover,
.meta-item:hover,
.meta-lbl:hover,
.meta-val:hover,
.biblio-synopsis-clean:hover,
.biblio-footer-bar:hover,
.biblio-status-wrap:hover {
    background: transparent !important;
    background-color: transparent !important;
    border: none !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    outline: none !important;
    transform: none !important;
}

/* Title & Author: Clean Natural Text */
.biblio-header-block {
    margin-bottom: 0.35rem !important;
    padding: 0 !important;
    border: none !important;
}

.biblio-title {
    font-size: 1.05rem !important;
    font-weight: 700 !important;
    line-height: 1.35 !important;
    margin: 0 0 0.2rem 0 !important;
    padding: 0 !important;
    color: #0F172A !important;
    border: none !important;
    word-break: break-word !important;
}

.biblio-title a {
    color: #0F172A !important;
    text-decoration: none !important;
}

.biblio-title a:hover {
    color: #2563EB !important;
    text-decoration: underline !important;
}

.biblio-author-line {
    font-size: 0.8rem !important;
    color: #475569 !important;
    margin: 0 !important;
    padding: 0 !important;
    border: none !important;
}

.author-prefix {
    color: #64748B !important;
    font-weight: 500 !important;
}

.author-clean-link {
    color: #1E293B !important;
    font-weight: 600 !important;
    text-decoration: none !important;
}

.author-clean-link:hover {
    color: #2563EB !important;
    text-decoration: underline !important;
}

/* Metadata Grid: Clean Inline Flex Flow, No Borders or Tube Containers */
.biblio-meta-grid {
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 0.35rem 1.25rem !important;
    padding: 0.25rem 0 !important;
    margin: 0.25rem 0 0.45rem 0 !important;
    border: none !important;
    border-top: none !important;
    border-bottom: none !important;
    font-size: 0.78rem !important;
}

.meta-item {
    display: inline-flex !important;
    align-items: baseline !important;
    gap: 0.35rem !important;
    border: none !important;
    padding: 0 !important;
}

.meta-lbl {
    color: #64748B !important;
    font-weight: 500 !important;
    border: none !important;
}

.meta-val {
    color: #1E293B !important;
    font-weight: 600 !important;
    border: none !important;
}

/* Call Number: Clean Discreet Minimal Tag */
.meta-callnum {
    background-color: #F1F5F9 !important;
    border: 1px solid #CBD5E1 !important;
    color: #0F172A !important;
    font-size: 0.72rem !important;
    font-weight: 700 !important;
    padding: 0.05rem 0.4rem !important;
    border-radius: 4px !important;
    font-family: monospace, ui-monospace, sans-serif !important;
}

/* Synopsis: Clean Natural Paragraph Flow */
.biblio-synopsis-clean {
    font-size: 0.78rem !important;
    color: #475569 !important;
    line-height: 1.45 !important;
    margin: 0 0 0.5rem 0 !important;
    padding: 0 !important;
    border: none !important;
    display: -webkit-box !important;
    -webkit-line-clamp: 2 !important;
    -webkit-box-orient: vertical !important;
    overflow: hidden !important;
}

/* Footer Bar */
.biblio-footer-bar {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    justify-content: space-between !important;
    flex-wrap: wrap !important;
    gap: 0.75rem !important;
    padding-top: 0.4rem !important;
    margin-top: auto !important;
    border: none !important;
}

/* Status Indicator — Free of Tube Capsule Styling */
.status-badge {
    display: inline-flex !important;
    align-items: center !important;
    gap: 0.4rem !important;
    font-size: 0.78rem !important;
    padding: 0 !important;
    border: none !important;
    border-radius: 0 !important;
    background: transparent !important;
    font-weight: 500 !important;
}

.status-badge.is-avail {
    color: #047857 !important;
    background: transparent !important;
    border: none !important;
}

.status-badge.is-unavail {
    color: #B91C1C !important;
    background: transparent !important;
    border: none !important;
}

.status-dot {
    width: 7px !important;
    height: 7px !important;
    border-radius: 50% !important;
    display: inline-block !important;
    flex-shrink: 0 !important;
}

.status-badge.is-avail .status-dot {
    background-color: #10B981 !important;
}

.status-badge.is-unavail .status-dot {
    background-color: #EF4444 !important;
}

/* Action Buttons */
.biblio-actions-wrap {
    display: inline-flex !important;
    align-items: center !important;
    gap: 0.4rem !important;
    flex-wrap: wrap !important;
}

.tool-icons-group {
    display: inline-flex !important;
    align-items: center !important;
    gap: 0.25rem !important;
}

.clean-tool-btn {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 30px !important;
    height: 30px !important;
    border-radius: 6px !important;
    background: #F8FAFC !important;
    border: 1px solid #E2E8F0 !important;
    color: #64748B !important;
    text-decoration: none !important;
}

.clean-tool-btn:hover {
    background: #F1F5F9 !important;
    color: #0F172A !important;
    border-color: #CBD5E1 !important;
}

.clean-tool-btn.is-active {
    background: #0F172A !important;
    color: #FFFFFF !important;
    border-color: #0F172A !important;
}

.btn-clean-cart {
    display: inline-flex !important;
    align-items: center !important;
    gap: 0.35rem !important;
    background: #FFFFFF !important;
    border: 1px solid #CBD5E1 !important;
    color: #334155 !important;
    font-size: 0.78rem !important;
    font-weight: 600 !important;
    padding: 0.35rem 0.7rem !important;
    border-radius: 6px !important;
    cursor: pointer !important;
}

.btn-clean-cart:hover {
    background: #F8FAFC !important;
    border-color: #94A3B8 !important;
    color: #0F172A !important;
}

.btn-clean-detail {
    display: inline-flex !important;
    align-items: center !important;
    gap: 0.35rem !important;
    background: #0F172A !important;
    border: 1px solid #0F172A !important;
    color: #FFFFFF !important;
    font-size: 0.78rem !important;
    font-weight: 600 !important;
    padding: 0.35rem 0.8rem !important;
    border-radius: 6px !important;
    text-decoration: none !important;
}

.btn-clean-detail:hover {
    background: #1E293B !important;
    color: #FFFFFF !important;
    border-color: #1E293B !important;
}

/* ── 5. GRID VIEW CARD ───────────────────────────────────────────── */
.biblio-grid-card {
    background: #FFFFFF !important;
    border-radius: 8px !important;
    border: 1px solid #E2E8F0 !important;
    box-shadow: none !important;
}

.biblio-grid-card:hover {
    border-color: #94A3B8 !important;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05) !important;
}

.grid-card-media {
    background: #F8FAFC !important;
    border-bottom: 1px solid #F1F5F9 !important;
}

.grid-card-thumb {
    width: 120px !important;
    aspect-ratio: 1 / 1.45 !important;
    object-fit: cover !important;
}

.grid-stock-tag {
    position: absolute !important;
    bottom: 8px !important;
    left: 12px !important;
    font-size: 0.68rem !important;
    font-weight: 600 !important;
    padding: 0.15rem 0.5rem !important;
    border-radius: 4px !important;
}

.grid-badge-avail {
    background: #10B981 !important;
    color: #FFFFFF !important;
}

.grid-badge-unavail {
    background: #EF4444 !important;
    color: #FFFFFF !important;
}

.grid-call-badge {
    background: #F1F5F9 !important;
    border: 1px solid #CBD5E1 !important;
    color: #0F172A !important;
    font-size: 0.72rem !important;
    font-weight: 700 !important;
    padding: 0.05rem 0.4rem !important;
    border-radius: 4px !important;
    font-family: monospace, ui-monospace, sans-serif !important;
}

.grid-item-title {
    font-size: 0.95rem !important;
    font-weight: 700 !important;
    line-height: 1.35 !important;
}

.grid-item-title a {
    color: #0F172A !important;
    text-decoration: none !important;
}

.grid-item-title a:hover {
    color: #2563EB !important;
}

/* ── 6. PAGINATION ───────────────────────────────────────────────── */
.biblioPaging {
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    padding: 1.5rem 0 1rem 0 !important;
    margin-top: 1rem !important;
    border-top: 1px solid #E2E8F0 !important;
}

.biblioPaging .pagingList {
    display: inline-flex !important;
    flex-wrap: wrap !important;
    align-items: center !important;
    gap: 0.35rem !important;
}

.biblioPaging .pagingList b,
.biblioPaging .pagingList a {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-width: 34px !important;
    height: 34px !important;
    padding: 0 0.65rem !important;
    border-radius: 6px !important;
    font-size: 0.82rem !important;
    font-weight: 500 !important;
    text-decoration: none !important;
    box-sizing: border-box !important;
}

.biblioPaging .pagingList a {
    background: #FFFFFF !important;
    border: 1px solid #E2E8F0 !important;
    color: #334155 !important;
}

.biblioPaging .pagingList a:hover {
    background: #F1F5F9 !important;
    color: #0F172A !important;
    border-color: #CBD5E1 !important;
}

.biblioPaging .pagingList b {
    background: #0F172A !important;
    border: 1px solid #0F172A !important;
    color: #FFFFFF !important;
    font-weight: 700 !important;
}

.biblioPaging .pagingList a.first_link,
.biblioPaging .pagingList a.last_link,
.biblioPaging .pagingList a.prev_link,
.biblioPaging .pagingList a.next_link {
    font-weight: 600 !important;
    font-size: 0.8rem !important;
    background: #F8FAFC !important;
    border-color: #CBD5E1 !important;
    color: #475569 !important;
}

.biblioPaging .pagingList a.first_link:hover,
.biblioPaging .pagingList a.last_link:hover,
.biblioPaging .pagingList a.prev_link:hover,
.biblioPaging .pagingList a.next_link:hover {
    background: #0F172A !important;
    border-color: #0F172A !important;
    color: #FFFFFF !important;
}

/* ── 7. EMPTY STATE ──────────────────────────────────────────────── */
.search-empty-state-card {
    background: #FFFFFF !important;
    border: 1px solid #E2E8F0 !important;
    border-radius: 8px !important;
}

.empty-icon-circle {
    width: 64px !important;
    height: 64px !important;
    border-radius: 50% !important;
    background: #F1F5F9 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.empty-icon-circle i {
    color: #64748B !important;
}

/* ── 8. RESPONSIVE BREAKDOWN ─────────────────────────────────────── */
@media (max-width: 767.98px) {
    .biblio-card,
    .book-card,
    .biblio-card-list {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 0.75rem !important;
    }
    .biblio-card-cover {
        width: 88px !important;
        min-width: 88px !important;
        max-width: 88px !important;
        margin-bottom: 0.25rem !important;
    }
    .biblio-footer-bar {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 0.5rem !important;
    }
    .biblio-actions-wrap {
        justify-content: space-between !important;
        width: 100% !important;
    }
    .btn-clean-cart, .btn-clean-detail {
        flex: 1 1 auto !important;
        justify-content: center !important;
    }
}
</style>

<!-- ================================================================
     CLIENT-SIDE ENHANCEMENTS, SLIDER CALIBRATION & GRID MASONRY
================================================================ -->
<script>
$(document).ready(function () {
    // 1. Initialize Bootstrap tooltips for action buttons
    if (typeof $.fn.tooltip !== 'undefined') {
        $('[data-toggle="tooltip"]').tooltip({ boundary: 'window' });
    }

    // 2. Publication Year Slider Recalibration (Dynamic Floor & Manual Input Sync)
    function calibrateYearSlider() {
        var $slider = $('.input-slider[name="years"]');
        if (!$slider.length || !$.fn.ionRangeSlider) return;

        var sliderInstance = $slider.data('ionRangeSlider');
        if (!sliderInstance) return;

        var catalogMin = parseInt($slider.data('min'), 10) || 1900;
        var catalogMax = parseInt($slider.data('max'), 10) || new Date().getFullYear();
        var defaultFloor = 1990;

        var $inputFrom = $('.js-input-from');
        var $inputTo = $('.js-input-to');

        var currentFrom = parseInt($inputFrom.val(), 10) || sliderInstance.result.from || catalogMin;
        var currentTo = parseInt($inputTo.val(), 10) || sliderInstance.result.to || catalogMax;

        // Dynamic clamping of outlier catalog min (e.g. 1519) to defaultFloor (1990)
        var hasActiveFilter = window.location.search.indexOf('filter=') !== -1;
        var effectiveMin = (catalogMin < defaultFloor) ? (hasActiveFilter && currentFrom < defaultFloor ? currentFrom : defaultFloor) : catalogMin;
        var normalizedFrom = (currentFrom < defaultFloor && !hasActiveFilter) ? defaultFloor : currentFrom;

        sliderInstance.update({
            min: effectiveMin,
            max: catalogMax,
            from: normalizedFrom,
            to: currentTo,
            onFinish: function (data) {
                $inputFrom.val(data.from);
                $inputTo.val(data.to);
                $slider.val(data.from + ';' + data.to);
                if (typeof filter === 'function') {
                    filter();
                }
            }
        });

        $inputFrom.val(normalizedFrom);
        $inputTo.val(currentTo);

        // Keep manual number inputs editable for typing historical years (< 1990)
        $inputFrom.off('change.manualYear').on('change.manualYear', function () {
            var typedVal = parseInt($(this).val(), 10);
            if (isNaN(typedVal)) return;
            if (typedVal < sliderInstance.options.min) {
                sliderInstance.update({
                    min: Math.max(catalogMin, typedVal),
                    from: typedVal
                });
            } else {
                sliderInstance.update({
                    from: Math.min(typedVal, currentTo)
                });
            }
            $slider.val($(this).val() + ';' + $inputTo.val());
        });

        $inputTo.off('change.manualYear').on('change.manualYear', function () {
            var typedVal = parseInt($(this).val(), 10);
            if (isNaN(typedVal)) return;
            if (typedVal > sliderInstance.options.max) {
                sliderInstance.update({
                    max: Math.max(catalogMax, typedVal),
                    to: typedVal
                });
            } else {
                sliderInstance.update({
                    to: Math.max(typedVal, parseInt($inputFrom.val(), 10))
                });
            }
            $slider.val($inputFrom.val() + ';' + $(this).val());
        });
    }

    calibrateYearSlider();

    // 3. Grid View Masonry Handling
    <?php if ($view_mode === 'grid'): ?>
    let imagesLoaded = 0;
    let totalImages = $(".grid-card-thumb").length;

    if (totalImages > 0) {
        $(".grid-card-thumb").each(function (idx, img) {
            $("<img>").on("load error", function() {
                imagesLoaded++;
                if (imagesLoaded >= totalImages) {
                    initGridMasonry();
                }
            }).attr("src", $(img).attr("src"));
        });
    } else {
        initGridMasonry();
    }

    function initGridMasonry() {
        if (typeof $.fn.masonry !== 'undefined') {
            $('.biblioResult').addClass('row').masonry({
                itemSelector: '.grid-item',
                columnWidth: '.grid-item',
                percentPosition: true
            });
        }
        if (typeof $.fn.dropdown !== 'undefined') {
            $('.dropdown-toggle').dropdown();
        }
    }
    <?php endif; ?>
});
</script>