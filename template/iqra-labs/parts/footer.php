    <?php
/**
 * =========================================================================
 * MANSAPA Iqra' Labs — footer.php  [FINAL PRODUCTION v2]
 * Place: template/mansapa iqra labs/parts/footer.php
 *
 * PRESERVED SLiMS HOOKS:
 *   include LIB . 'contents/chat.php'    — chat engine
 *   include '_modal_topic.php'           — topic browser modal
 *   include '_modal_advanced.php'        — advanced search modal
 *   include '_modal_social_media.php'    — social share modal
 *   highlight.js + $this->generateKeywords()
 *   assets(v('js/app.js'))               — Vue component definitions
 *   assets(v('js/app_jquery.js'))        — jQuery helpers
 *   assets('js/iqra-motion.js')          — custom scroll/animation
 *   CSRF token in mini-search form
 *   $sysconf social links, library info, chat_system
 * =========================================================================
 */
?>

<!-- ================================================================
     FOOTER — Navy Blue #0D1B3D, 3px Gold #D4AF37 top border
     Quote uses Cormorant Garamond (font-accent var)
================================================================ -->
<footer class="iqra-footer" role="contentinfo" aria-label="Footer MANSAPA Iqra' Labs">

    <!-- Accent quote banner (Compact, Balanced & Centered) -->
    <div class="footer-quote-section">
        <div class="container iqra-container">
            <div class="footer-quote-compact text-center mx-auto">
                <div class="footer-quote-header d-inline-flex align-items-center justify-content-center gap-2 mb-1">
                    <svg class="footer-quote-icon flex-shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 21c3 0 7-1 7-8V5c0-1.25-.75-2-2-2H4c-1.25 0-2 .75-2 2v6c0 1.25.75 2 2 2h4c-.5 2.5-2 4.5-5 5v3zm14 0c3 0 7-1 7-8V5c0-1.25-.75-2-2-2h-4c-1.25 0-2 .75-2 2v6c0 1.25.75 2 2 2h4c-.5 2.5-2 4.5-5 5v3z"/></svg>
                    <blockquote class="footer-quote-text m-0">
                        &ldquo;Jangan cuma baca, ceritakan juga.&rdquo;
                    </blockquote>
                </div>
                <p class="footer-quote-sub m-0 text-center mx-auto">
                    Jadikan setiap lembar yang kamu buka sebagai awal gagasan baru untuk berbagi kebaikan.
                </p>
            </div>
        </div>
    </div>

    <!-- Main footer content -->
    <div class="footer-main">
        <div class="container iqra-container">
            
            <!-- 12-Column Responsive Grid (Compact 3-Column Layout: 3 cols / 4 cols / 5 cols) -->
            <div class="footer-grid-3col grid grid-cols-1 md:grid-cols-12 gap-6 lg:gap-8 items-start">

                <!-- Column 1: Brand & Institutional Identity (md:col-span-3) -->
                <div class="footer-col-brand md:col-span-3 d-flex flex-column align-items-start">
                    <div class="footer-logo-wrap mb-1">
                        <?php
                        // Logo resolution chain (preserves natural aspect ratio without distortion)
                        if (
                            isset($sysconf['logo_image']) &&
                            $sysconf['logo_image'] !== '' &&
                            isset($imagesDisk) && $imagesDisk &&
                            $imagesDisk->isExists($lPath = 'default/' . $sysconf['logo_image'])
                        ) {
                            echo '<img class="footer-brand-logo" src="' . SWB . 'lib/minigalnano/createthumb.php?filename=images/' . $lPath . '&width=300" alt="Logo ' . htmlspecialchars($sysconf['library_name']) . '">';
                        } elseif (file_exists(__DIR__ . '/../assets/images/logo.png')) {
                            echo '<img class="footer-brand-logo" src="' . assets('images/logo.png') . '" alt="Logo MANSAPA">';
                        } else {
                            $libName = urlencode($sysconf['library_name'] ?? 'MANSAPA Iqra');
                            echo '<img class="footer-brand-logo footer-logo-avatar" src="https://ui-avatars.com/api/?name=' . $libName . '&background=162B5E&color=D4AF37&font-size=0.33&bold=true&size=100" alt="Logo MANSAPA">';
                        }
                        ?>
                    </div>
                    <h3 class="font-bold text-white tracking-tight footer-brand-heading">
                        <?= htmlspecialchars($sysconf['library_name'] ?? "MANSAPA Iqra' Labs"); ?>
                    </h3>
                    <p class="text-amber-400 font-medium text-xs tracking-wide uppercase footer-brand-tagline">
                        MAN 1 Pangandaran
                    </p>
                </div>

                <!-- Column 2: Tentang Kami (md:col-span-4) -->
                <div class="footer-col-about md:col-span-4">
                    <h4 class="font-semibold text-white footer-section-title">
                        <?= __('Tentang Kami'); ?>
                    </h4>
                    <p class="text-slate-300 footer-about-text">
                        <?= __('Perpustakaan MANSAPA Iqra\' Labs merupakan ruang eksplorasi pengetahuan, riset mandiri, dan penguatan literasi digital bagi seluruh sivitas akademika MAN 1 Pangandaran. Kami berkomitmen menyediakan ekosistem belajar yang adaptif, terbuka, dan inspiratif.'); ?>
                    </p>
                </div>

                <!-- Column 3: Statistik Pengunjung Widget (md:col-span-5) -->
                <div class="footer-col-stats md:col-span-5 w-100">
                    <div class="footer-visitor-widget" id="footer-visitor-card" role="region" aria-label="<?= __('Statistik Pengunjung'); ?>">
                        
                        <!-- Header with Pulse Indicator & Live Badge -->
                        <div class="visitor-widget-header d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center">
                                <span class="visitor-pulse-indicator me-2" aria-hidden="true" title="<?= __('Status: Live Analytics'); ?>">
                                    <span class="visitor-pulse-ring"></span>
                                    <span class="visitor-pulse-core"></span>
                                </span>
                                <h5 class="visitor-widget-title m-0 text-white font-semibold text-xs">
                                    <?= __('Statistik Pengunjung'); ?>
                                </h5>
                            </div>
                            <span class="badge badge-navy-gold text-xs px-2 py-0.5 font-medium" title="<?= __('Status: Aktif'); ?>">
                                <svg width="7" height="7" viewBox="0 0 24 24" fill="currentColor" class="text-emerald-400 me-1" style="display:inline-block; vertical-align:middle;" aria-hidden="true"><circle cx="12" cy="12" r="10"/></svg>Live
                            </span>
                        </div>

                        <!-- 3-Column Balanced Metric Grid -->
                        <div class="visitor-widget-grid">
                            
                            <!-- Tile 1: Hari ini -->
                            <div class="visitor-tile">
                                <span class="visitor-tile-label d-inline-flex align-items-center">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="text-amber-400 me-1" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                    <span><?= __('Hari ini'); ?></span>
                                </span>
                                <div class="visitor-tile-total text-amber-400 font-bold stat-skeleton" id="combined-today">-</div>
                                <div class="visitor-tile-sub">
                                    <span>Onl: <strong id="online-today" class="stat-skeleton">-</strong></span>
                                    <span class="mx-0.5 text-slate-600">&bull;</span>
                                    <span>Ons: <strong id="onsite-today" class="stat-skeleton">-</strong></span>
                                </div>
                            </div>

                            <!-- Tile 2: Bulan ini -->
                            <div class="visitor-tile">
                                <span class="visitor-tile-label d-inline-flex align-items-center">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="text-amber-400 me-1" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/><path d="M16 18h.01"/></svg>
                                    <span><?= __('Bulan ini'); ?></span>
                                </span>
                                <div class="visitor-tile-total text-amber-400 font-bold stat-skeleton" id="combined-month">-</div>
                                <div class="visitor-tile-sub">
                                    <span>Onl: <strong id="online-month" class="stat-skeleton">-</strong></span>
                                    <span class="mx-0.5 text-slate-600">&bull;</span>
                                    <span>Ons: <strong id="onsite-month" class="stat-skeleton">-</strong></span>
                                </div>
                            </div>

                            <!-- Tile 3: Total Keseluruhan -->
                            <div class="visitor-tile">
                                <span class="visitor-tile-label d-inline-flex align-items-center">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="text-amber-400 me-1" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                    <span><?= __('Total'); ?></span>
                                </span>
                                <div class="visitor-tile-total text-amber-400 font-bold stat-skeleton" id="combined-total">-</div>
                                <div class="visitor-tile-sub">
                                    <span>Onl: <strong id="online-total" class="stat-skeleton">-</strong></span>
                                    <span class="mx-0.5 text-slate-600">&bull;</span>
                                    <span>Ons: <strong id="onsite-total" class="stat-skeleton">-</strong></span>
                                </div>
                            </div>

                        </div><!-- /.visitor-widget-grid -->

                    </div><!-- /.footer-visitor-widget -->
                </div><!-- /col stats -->

            </div><!-- /.footer-grid-3col -->

            <!-- Bottom Bar (Single-line divider with subtle border-t) -->
            <div class="border-t border-slate-800/80 pt-3 mt-4 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400 footer-bottom-bar">
                <p class="m-0 text-slate-400 footer-copyright">
                    &copy; <?= date('Y'); ?> <strong>MANSAPA Iqra' Labs</strong> &mdash; Hak Cipta Dilindungi.
                </p>
                <div class="footer-attribution mt-2 sm:mt-0 d-flex align-items-center">
                    <span class="me-2 text-slate-400">Didukung oleh</span>
                    <code class="slims-badge px-2 py-0.5 rounded text-xs">SLiMS 9 Bulian</code>
                </div>
            </div>

        </div><!-- /.container -->
    </div><!-- /.footer-main -->

</footer><!-- /.iqra-footer -->


<!-- ================================================================
     FLOATING CHAT BUTTON (SLiMS hook — preserved as-is)
================================================================ -->
<?php if (!empty($sysconf['chat_system']['enabled']) && !empty($sysconf['chat_system']['opac'])): ?>
    <div id="show-pchat2" class="chat-float-btn shadow" style="position:fixed;bottom:24px;right:24px;z-index:1055;">
        <button class="btn btn-primary btn-chat-pill">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="me-2" style="vertical-align: -2px;" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg><?= __('Chat'); ?>
        </button>
    </div>
<?php endif; ?>


<!-- ================================================================
     SLiMS CHAT ENGINE — hook preserved, do NOT remove
================================================================ -->
<?php include LIB . 'contents/chat.php'; ?>


<!-- ================================================================
     SLiMS MODAL PARTIALS — hooks preserved, do NOT remove
================================================================ -->
<?php include '_modal_topic.php'; ?>
<?php include '_modal_advanced.php'; ?>
<?php include '_modal_social_media.php'; ?>


<!-- ================================================================
     SLiMS HIGHLIGHT.JS — search result keyword highlighting
================================================================ -->
<script src="<?= JWB; ?>highlight.js"></script>
<?php if (isset($engine) && isset($this) && method_exists($this, 'generateKeywords') && ($searchableInJsArray = $this->generateKeywords($engine->searchable_fields))): ?>
<script>
    $(function() { $('.card-body > *').highlight(<?= $searchableInJsArray; ?>); });
</script>
<?php endif; ?>


<!-- ================================================================
     SLiMS VUE APP COMPONENTS
     app.js     — registers <slims-collection>, <slims-group-subject>
     app_jquery — jQuery-based helpers
     Load AFTER body content so custom elements exist in DOM.
================================================================ -->
<script src="<?= assets('js/app.js?v=' . (file_exists(__DIR__ . '/../assets/js/app.js') ? filemtime(__DIR__ . '/../assets/js/app.js') : date('YmdHis'))); ?>"></script>
<script src="<?= assets('js/app_jquery.js?v=' . (file_exists(__DIR__ . '/../assets/js/app_jquery.js') ? filemtime(__DIR__ . '/../assets/js/app_jquery.js') : date('YmdHis'))); ?>"></script>


<!-- ================================================================
     MANSAPA IQRA' LABS — Motion & Scroll Reveal
     Path: template/mansapa iqra labs/assets/js/iqra-motion.js
================================================================ -->
<script src="<?= assets('js/iqra-motion.js'); ?>"></script>


<!-- ================================================================
     SLiMS CHAT TOGGLE LOGIC — preserved
================================================================ -->
<?php if (!empty($sysconf['chat_system']['enabled']) && !empty($sysconf['chat_system']['opac'])): ?>
<script>
    $(function() {
        $('#show-pchat').on('click', function() { $('.s-chat').hide(); $('#show-pchat2').show(); });
        $('#show-pchat2').on('click', function() { $('.s-chat').show(300, function() { $('#show-pchat2').hide(); }); });
    });
</script>
<?php endif; ?>


<!-- ================================================================
     STATISTIK PENGUNJUNG — WIDGET STYLES & ASYNC JS
================================================================ -->
<style>
/* ==========================================================================
   FOOTER COMPACT & BALANCED RESPONSIVE GRID SYSTEM & INSTITUTIONAL STYLES
   ========================================================================== */
.iqra-footer {
    background: #0B1329 !important;
    border-top: 2px solid #D4AF37 !important;
}

.footer-quote-section {
    padding: 1.1rem 1rem !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    background: rgba(11, 19, 41, 0.5) !important;
    text-align: center !important;
}

.footer-quote-compact {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    text-align: center !important;
    max-width: 680px;
    margin: 0 auto !important;
}

.footer-quote-header {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 0.5rem;
}

.footer-quote-icon {
    color: #F59E0B !important;
    opacity: 0.95;
    flex-shrink: 0;
}

.footer-quote-text {
    font-family: var(--font-accent, 'Cormorant Garamond', Georgia, serif) !important;
    font-style: italic !important;
    font-size: 1.18rem !important;
    font-weight: 600 !important;
    color: #FACC15 !important;
    letter-spacing: -0.01em;
    line-height: 1.35 !important;
    text-align: center !important;
}

.footer-quote-sub {
    font-size: 0.82rem !important;
    color: #94A3B8 !important;
    max-width: 560px !important;
    line-height: 1.5 !important;
    text-align: center !important;
    margin: 0.2rem auto 0 auto !important;
}

.footer-main {
    padding: 1.75rem 0 1rem !important;
}

.footer-grid-3col {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}

@media (min-width: 768px) {
    .footer-grid-3col {
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: 1.5rem;
    }
    .footer-col-brand {
        grid-column: span 3 / span 3;
    }
    .footer-col-about {
        grid-column: span 4 / span 4;
    }
    .footer-col-stats {
        grid-column: span 5 / span 5;
    }
}

@media (min-width: 1024px) {
    .footer-grid-3col {
        gap: 2rem;
    }
}

/* Brand Logo Styling */
.footer-logo-wrap {
    display: inline-flex;
    align-items: center;
    justify-content: flex-start;
}

.footer-brand-logo {
    height: 44px;
    width: auto;
    max-width: 140px;
    object-fit: contain;
    display: block;
    filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.3));
    transition: transform 0.25s ease;
}

.footer-brand-logo:hover {
    transform: translateY(-1px) scale(1.02);
}

.footer-brand-logo.footer-logo-avatar {
    height: 42px;
    width: 42px;
    border-radius: 50%;
    border: 1.5px solid #D4AF37;
}

/* Institutional Typography & Brand Hierarchy */
.footer-brand-heading {
    font-size: 1rem;
    font-weight: 700;
    color: #FFFFFF;
    letter-spacing: -0.015em;
    margin-top: 0.4rem;
    margin-bottom: 0;
    line-height: 1.25;
}

.footer-brand-tagline {
    color: #FBBF24 !important; /* text-amber-400 */
    font-weight: 600;
    font-size: 0.68rem;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    margin-top: 0.15rem;
    margin-bottom: 0;
}

.footer-section-title {
    font-size: 0.92rem;
    font-weight: 600;
    color: #FFFFFF;
    border-bottom: 2px solid rgba(251, 191, 36, 0.8) !important;
    display: inline-block;
    padding-bottom: 0.2rem;
    margin-bottom: 0.5rem;
}

.footer-about-text {
    color: #CBD5E1 !important; /* text-slate-300 */
    font-size: 0.8125rem;       /* 13px */
    line-height: 1.55;
    margin-bottom: 0;
}

.footer-bottom-bar {
    border-top: 1px solid rgba(255, 255, 255, 0.08) !important;
}

.slims-badge {
    background-color: rgba(30, 41, 59, 0.75);
    border: 1px solid rgba(51, 65, 85, 0.6);
    color: #CBD5E1;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
}

/* ==========================================================================
   STATISTIK PENGUNJUNG — COMPACT & BALANCED WIDGET STYLES
   ========================================================================== */
.footer-visitor-widget {
    background-color: rgba(15, 23, 42, 0.65);
    border: 1px solid rgba(51, 65, 85, 0.6);
    border-radius: 0.875rem;
    padding: 0.75rem 0.85rem;
    box-shadow: inset 0 1px 3px 0 rgba(0, 0, 0, 0.35);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    transition: border-color 0.25s ease, box-shadow 0.25s ease;
}

.footer-visitor-widget:hover {
    border-color: rgba(212, 175, 55, 0.4);
    box-shadow: inset 0 1px 3px 0 rgba(0, 0, 0, 0.4), 0 4px 10px rgba(15, 23, 42, 0.2);
}

.visitor-pulse-indicator {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 9px;
    height: 9px;
}

.visitor-pulse-ring {
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 9999px;
    background-color: #10B981;
    opacity: 0.75;
    animation: visitorPulsePing 1.8s cubic-bezier(0, 0, 0.2, 1) infinite;
}

.visitor-pulse-core {
    position: relative;
    display: inline-block;
    width: 7px;
    height: 7px;
    border-radius: 9999px;
    background-color: #059669;
}

@keyframes visitorPulsePing {
    0%   { transform: scale(0.9); opacity: 0.8; }
    75%  { transform: scale(2.2); opacity: 0; }
    100% { transform: scale(2.2); opacity: 0; }
}

.badge-navy-gold {
    background: rgba(212, 175, 55, 0.12);
    border: 1px solid rgba(212, 175, 55, 0.3);
    color: #F8FAFC;
    font-size: 0.68rem;
    font-weight: 600;
    border-radius: 9999px;
    letter-spacing: 0.02em;
}

.visitor-widget-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0.45rem;
}

@media (max-width: 480px) {
    .visitor-widget-grid {
        grid-template-columns: 1fr;
    }
}

.visitor-tile {
    background: rgba(30, 41, 59, 0.45);
    border: 1px solid rgba(51, 65, 85, 0.45);
    border-radius: 0.625rem;
    padding: 0.45rem 0.55rem;
    transition: background 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
    display: flex;
    flex-direction: column;
}

.visitor-tile:hover {
    background: rgba(30, 41, 59, 0.75);
    border-color: rgba(212, 175, 55, 0.35);
    transform: translateY(-1px);
}

.visitor-tile-label {
    font-size: 0.7rem;
    color: #CBD5E1;
    font-weight: 500;
}

.visitor-tile-total {
    font-size: 1.05rem;
    color: #FBBF24;
    font-weight: 700;
    line-height: 1.2;
    letter-spacing: -0.01em;
    font-variant-numeric: tabular-nums;
    margin-top: 0.15rem;
}

.visitor-tile-sub {
    font-size: 0.68rem;
    color: #94A3B8;
    line-height: 1.25;
    margin-top: 0.2rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.visitor-tile-sub strong {
    color: #F1F5F9;
    font-weight: 600;
    font-variant-numeric: tabular-nums;
}

/* Skeleton Loading Shimmer */
.stat-skeleton {
    display: inline-block;
    min-width: 16px;
    color: transparent !important;
    background: linear-gradient(90deg, rgba(255, 255, 255, 0.08) 25%, rgba(255, 255, 255, 0.2) 50%, rgba(255, 255, 255, 0.08) 75%);
    background-size: 200% 100%;
    animation: statSkeletonShimmer 1.5s infinite;
    border-radius: 3px;
    vertical-align: middle;
}

@keyframes statSkeletonShimmer {
    0%   { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

@media (max-width: 767.98px) {
    .footer-visitor-widget {
        margin-top: 0.5rem;
        padding: 0.75rem;
    }
}
</style>

<script>
(function() {
    'use strict';

    function initVisitorCounter() {
        // Multi-resolution endpoint paths
        const primaryUrl  = '<?= SWB; ?>visitorcounterrest.php';
        const fallbackUrl = '<?= SWB; ?>index.php?p=visitorcounterrest';

        const ids = [
            'online-today', 'onsite-today', 'combined-today',
            'online-month', 'onsite-month', 'combined-month',
            'online-total', 'onsite-total', 'combined-total'
        ];

        function clearSkeletons(dataMap) {
            ids.forEach(function(id) {
                const el = document.getElementById(id);
                if (!el) return;
                el.classList.remove('stat-skeleton');
                if (dataMap && dataMap[id] !== undefined) {
                    el.textContent = dataMap[id];
                } else if (!el.textContent || el.textContent === '-' || el.textContent === '') {
                    el.textContent = '0';
                }
            });
        }

        async function fetchStats() {
            let response = null;

            // 1. Attempt primary REST endpoint
            try {
                response = await fetch(primaryUrl, {
                    headers: { 'Accept': 'application/json' },
                    cache: 'default'
                });
                if (!response.ok) throw new Error('Primary HTTP ' + response.status);
            } catch (err1) {
                // 2. Fallback to SLiMS native plugin route if direct file path fails
                try {
                    response = await fetch(fallbackUrl, {
                        headers: { 'Accept': 'application/json' },
                        cache: 'default'
                    });
                    if (!response.ok) throw new Error('Fallback HTTP ' + response.status);
                } catch (err2) {
                    clearSkeletons();
                    return;
                }
            }

            try {
                const json = await response.json();
                if (!json || json.status === 'error') {
                    throw new Error(json && json.message ? json.message : 'Invalid payload');
                }

                const formatter = new Intl.NumberFormat('id-ID');
                const fmt = function(num) {
                    return formatter.format(Math.max(0, Number(num) || 0));
                };

                // Online values (supports both json.online.today and json.today.online)
                const onToday = Number(json.online?.today ?? json.today?.online ?? 0) || 0;
                const onMonth = Number(json.online?.month ?? json.month?.online ?? 0) || 0;
                const onTotal = Number(json.online?.total ?? json.total?.online ?? json.all?.online ?? 0) || 0;

                // Onsite values (supports both json.onsite.today and json.today.onsite)
                const osToday = Number(json.onsite?.today ?? json.today?.onsite ?? 0) || 0;
                const osMonth = Number(json.onsite?.month ?? json.month?.onsite ?? 0) || 0;
                const osTotal = Number(json.onsite?.total ?? json.total?.onsite ?? json.all?.onsite ?? 0) || 0;

                // Combined totals (supports combined object, top-level legacy keys, or calculated sum)
                const cbToday = Number(json.combined?.today ?? (typeof json.today === 'number' ? json.today : null) ?? (onToday + osToday)) || 0;
                const cbMonth = Number(json.combined?.month ?? (typeof json.month === 'number' ? json.month : null) ?? (onMonth + osMonth)) || 0;
                const cbTotal = Number(json.combined?.total ?? json.all ?? json.total ?? (onTotal + osTotal)) || 0;

                const values = {
                    'online-today': fmt(onToday),
                    'onsite-today': fmt(osToday),
                    'combined-today': fmt(cbToday),
                    'online-month': fmt(onMonth),
                    'onsite-month': fmt(osMonth),
                    'combined-month': fmt(cbMonth),
                    'online-total': fmt(onTotal),
                    'onsite-total': fmt(osTotal),
                    'combined-total': fmt(cbTotal)
                };

                clearSkeletons(values);

            } catch (parseError) {
                clearSkeletons();
            }
        }

        fetchStats();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initVisitorCounter);
    } else {
        initVisitorCounter();
    }
})();
</script>

</body>
</html>
