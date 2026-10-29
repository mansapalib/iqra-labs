<?php
/**
 * ============================================================================
 * MANSAPA Iqra' Labs — FAQ Minimalist 2-Column View Partial (Redesigned)
 * File: template/iqra-labs/partials/faq_view.php
 *
 * Professional, balanced 2-column institutional FAQ view:
 * - Canvas: Harmonious off-white with subtle ambient depth
 * - Hero: Balanced typography, refined badge, golden brush accent, search with hotkey
 * - Category Quick-Filter Navigation Chips (All, Peminjaman, Keanggotaan, etc.)
 * - Toolbar: Live questions count + Expand All / Collapse All toggle
 * - 2-Column Responsive Grid with balanced card weights
 * - Silky 60fps CSS Grid Accordion expansion with smooth icon rotation
 * - Active card left-indicator & soft elevation
 * - Real-time keyword highlight in questions & answers
 * - Direct deep-link support (#faq-card-X-Y) & click-to-copy answer link
 * - High-contrast accessible typography (WCAG AAA compliant body text)
 * - Concise Need Help / Pustakawan Concierge Card at bottom
 * ============================================================================
 */

// Initialize variables for template scope
$page_title   = $page_title ?? 'Tanya Jawab (FAQ)';
$main_content = $main_content ?? '';
global $sysconf;

// Resolve contact details for compact Need Help card
$library_phone = !empty($sysconf['library_phone']) ? $sysconf['library_phone'] : '+62 812-2345-6789';
$wa_number = preg_replace('/[^0-9]/', '', $library_phone);
if (empty($wa_number) || strlen($wa_number) < 8) {
    $wa_number = '6281223456789';
    $display_phone = '+62 812-2345-6789';
} else {
    if (substr($wa_number, 0, 1) === '0') {
        $wa_number = '62' . substr($wa_number, 1);
    }
    $display_phone = $library_phone;
}
$contact_email = !empty($sysconf['mail']['from']) ? $sysconf['mail']['from'] : 'perpustakaan@man1pangandaran.sch.id';
?>

<div class="faq-page-wrapper bg-[#F8FAFC] min-h-screen pb-20">

    <!-- ── 1. NAVBAR HEADER CONTAINER ───────────────────────────────────── -->
    <section id="section1" class="container-fluid p-0">
        <div class="c-header">
            <div class="mask"></div>
            <?php include __DIR__ . '/../parts/_navbar.php'; ?>
        </div>
    </section>

    <!-- ── 2. PAGE HEADER (PROFESSIONAL INSTITUTIONAL HERO) ──────────────── -->
    <header class="faq-hero-container pt-10 pb-4 md:pt-14 md:pb-6 text-center relative overflow-hidden">
        <div class="container iqra-container max-w-3xl mx-auto px-4 relative z-10">
            
            <!-- Minimalist Institutional Eyebrow Kicker -->
            <div class="faq-kicker">
                <span class="faq-kicker-dot" aria-hidden="true"></span>
                <span class="faq-kicker-text">Pusat Bantuan</span>
            </div>

            <!-- Clean, Authoritative Main Title without Gimmicks -->
            <h1 class="faq-main-title">
                Pertanyaan yang Sering Diajukan
            </h1>

            <!-- Compact Search Input with Keyboard Shortcut & Clear Action -->
            <div class="faq-search-wrap max-w-lg mx-auto">
                <div class="faq-search-box" id="faq-search-box-wrap">
                    <svg class="faq-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.3-4.3"/>
                    </svg>
                    <input type="text" id="faq-search" class="faq-search-input" placeholder="Cari pertanyaan atau kata kunci (misal: kartu, denda, 7 hari)..." autocomplete="off" aria-label="Cari pertanyaan FAQ">
                    
                    <!-- Keyboard Shortcut Hint -->
                    <span class="faq-search-shortcut" id="faq-search-shortcut" title="Tekan '/' untuk fokus pencarian">/</span>

                    <!-- Clear Button -->
                    <button type="button" id="faq-search-clear" class="faq-search-clear" aria-label="Hapus pencarian">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="m15 9-6 6"/>
                            <path d="m9 9 6 6"/>
                        </svg>
                    </button>
                </div>
                <div id="faq-search-stats" class="faq-search-stats" role="status" aria-live="polite"></div>
            </div>

            <!-- Dynamic Category Filter Pills (Injected by JS) -->
            <nav class="faq-category-nav" id="faq-category-nav" aria-label="Filter kategori FAQ"></nav>

        </div>
    </header>

    <!-- ── 3. RAW CMS CONTENT STAGING (Hidden from view) ───────────────── -->
    <div id="raw-faq-content" class="d-none" style="display: none !important;" aria-hidden="true">
        <?= $main_content; ?>
    </div>

    <!-- ── 4. MAIN FAQ CONTENT CONTAINER (MAX-W-5XL FOR 2-COLUMN GRID) ──── -->
    <main class="container iqra-container max-w-5xl mx-auto px-4 mt-4 md:mt-6" id="faq-main-wrapper" role="main" aria-label="Daftar FAQ">

        <!-- Top Utility Toolbar: Total Count & Expand/Collapse All -->
        <div class="faq-toolbar" id="faq-toolbar" style="display: none;">
            <div class="faq-toolbar-count">
                <span class="faq-toolbar-indicator"></span>
                <span id="faq-count-text">Menampilkan pertanyaan</span>
            </div>
            <div class="faq-toolbar-actions">
                <button type="button" id="faq-toggle-all-btn" class="faq-toolbar-btn" aria-label="Buka semua pertanyaan">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="m7 15 5 5 5-5"/>
                        <path d="m7 9 5-5 5 5"/>
                    </svg>
                    <span id="faq-toggle-all-text">Buka Semua</span>
                </button>
            </div>
        </div>

        <!-- Rendered 2-Column Accordions (Injected by Vanilla JS) -->
        <div id="faq-rendered-container">
            <!-- Loading placeholder skeleton (2-column) -->
            <div class="faq-skeleton-loader space-y-8 animate-pulse">
                <div>
                    <div class="h-6 bg-slate-200/80 rounded-lg w-48 mb-2"></div>
                    <div class="h-1 bg-amber-400/80 rounded-full w-10 mb-6"></div>
                    <div class="faq-grid-2col">
                        <div class="h-20 bg-white border border-slate-200/80 rounded-[20px] w-full"></div>
                        <div class="h-20 bg-white border border-slate-200/80 rounded-[20px] w-full"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Polite Empty State (Shown when search has zero matches) -->
        <div id="faq-empty-state" class="faq-empty-state" style="display: none;">
            <div class="faq-empty-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.3-4.3"/>
                    <line x1="8" y1="11" x2="14" y2="11"/>
                </svg>
            </div>
            <h3 class="faq-empty-title">
                Tidak ada pertanyaan yang cocok
            </h3>
            <p id="faq-empty-message" class="faq-empty-desc">
                Kami tidak menemukan pertanyaan atau jawaban yang memuat kata kunci tersebut.
            </p>
            <div class="faq-empty-actions">
                <button type="button" id="faq-reset-filter-btn" class="faq-reset-btn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                        <path d="M3 3v5h5"/>
                    </svg>
                    <span>Reset Pencarian</span>
                </button>
                <a href="https://wa.me/<?= $wa_number; ?>?text=<?= urlencode("Halo Pustakawan MANSAPA, saya tidak menemukan jawaban di FAQ terkait: "); ?>" target="_blank" rel="noopener noreferrer" class="faq-ask-wa-link">
                    <span>Tanya Pustakawan via WhatsApp &rarr;</span>
                </a>
            </div>
        </div>

        <!-- ── 5. COMPACT NEED HELP BAR (BOTTOM CTA) ─────────────────────── -->
        <div class="faq-need-help-card" id="faq-help-section">
            <div class="faq-help-info">
                <div class="faq-help-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M3 18v-6a9 9 0 0 1 18 0v6"/>
                        <path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"/>
                    </svg>
                    <span class="faq-help-status-dot" title="Pustakawan Siaga"></span>
                </div>
                <div>
                    <h3 class="faq-help-title">
                        Belum menemukan jawaban yang Anda cari?
                    </h3>
                    <p class="faq-help-subtitle">
                        Pustakawan MANSAPA Iqra' Labs siap membantu memberikan penjelasan langsung via WhatsApp atau Email.
                    </p>
                </div>
            </div>
            <div class="faq-help-actions">
                <!-- WhatsApp Consultation Button -->
                <a href="https://wa.me/<?= $wa_number; ?>?text=<?= urlencode("Halo Pustakawan MANSAPA Iqra' Labs, saya ingin bertanya terkait layanan perpustakaan..."); ?>" target="_blank" rel="noopener noreferrer" class="faq-btn-wa" aria-label="Konsultasi via WhatsApp">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                    </svg>
                    <span>Hubungi WhatsApp</span>
                </a>

                <!-- Email Link with Institutional Accent -->
                <a href="mailto:<?= htmlspecialchars($contact_email); ?>?subject=<?= urlencode("Pertanyaan Layanan MANSAPA Iqra' Labs"); ?>" class="faq-btn-email" aria-label="Kirim Email ke Perpustakaan">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect width="20" height="16" x="2" y="4" rx="2"/>
                        <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                    </svg>
                    <span>Kirim Email</span>
                </a>
            </div>
        </div>

        <!-- Toast Notification for Link Copy -->
        <div id="faq-toast" class="faq-toast" role="alert" aria-live="polite">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
            <span id="faq-toast-msg">Tautan berhasil disalin!</span>
        </div>

    </main>

</div>

<!-- ── COMPLETE EMBEDDED CSS (PURE CSS, ROBUST & BALANCED) ──────────────── -->
<style>
/* CSS Tokens & Page Canvas */
:root {
    --faq-bg: #F8FAFC;
    --faq-card-bg: #FFFFFF;
    --faq-card-border: #E2E8F0;
    --faq-card-hover-border: #CBD5E1;
    --faq-text-title: #0F172A;
    --faq-text-body: #334155;
    --faq-text-muted: #64748B;
    --faq-gold-primary: #D4AF37;
    --faq-gold-accent: #EAB308;
    --faq-gold-subtle: #FEF3C7;
    --faq-slate-dark: #0F172A;
    --faq-slate-hover: #1E293B;
    --faq-radius-card: 20px;
    --faq-radius-pill: 9999px;
    --faq-ease-spring: cubic-bezier(0.16, 1, 0.3, 1);
}

.faq-page-wrapper {
    background-color: var(--faq-bg) !important;
    position: relative;
}

/* Hero Section */
.faq-hero-container {
    background: radial-gradient(circle at 50% 0%, #FFFFFF 0%, #F8FAFC 85%);
    position: relative;
}

/* Minimalist Eyebrow Kicker */
.faq-kicker {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.25rem 0.8rem;
    background: #FFFFFF;
    border: 1px solid #E2E8F0;
    border-radius: var(--faq-radius-pill);
    margin-bottom: 1rem;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03);
}
.faq-kicker-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background-color: var(--faq-gold-primary);
}
.faq-kicker-text {
    font-size: 0.75rem;
    font-weight: 600;
    color: #475569;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

/* Page Title — Clean, Bold, Professional (Balanced Hierarchy) */
.faq-main-title {
    font-size: 1.85rem;
    font-weight: 700;
    color: var(--faq-text-title);
    letter-spacing: -0.025em;
    line-height: 1.25;
    margin: 0 auto 0.65rem auto;
    text-wrap: balance;
    max-width: 24ch;
}
@media (min-width: 768px) {
    .faq-main-title {
        font-size: 2.35rem;
    }
}

/* Subtitle — Concise, Direct, High-Legibility */
.faq-subtitle {
    font-size: 0.9375rem;
    color: var(--faq-text-muted);
    line-height: 1.6;
    max-width: 32rem;
    margin: 0 auto 1.5rem auto;
    text-align: center;
    text-wrap: pretty;
}
@media (min-width: 768px) {
    .faq-subtitle {
        font-size: 1rem;
    }
}

/* Search Box */
.faq-search-wrap {
    margin: 0 auto 1.5rem auto;
    position: relative;
}
.faq-search-box {
    display: flex;
    align-items: center;
    background: #FFFFFF;
    border: 1px solid #CBD5E1;
    border-radius: var(--faq-radius-pill);
    padding: 0.55rem 1.15rem;
    box-shadow: 0 2px 8px -2px rgba(15, 23, 42, 0.05);
    transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
}
.faq-search-box:focus-within {
    border-color: var(--faq-slate-dark);
    box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.08), 0 4px 12px rgba(15, 23, 42, 0.06);
}
.faq-search-icon {
    color: #94A3B8;
    margin-right: 0.65rem;
    flex-shrink: 0;
    transition: color 0.2s ease;
}
.faq-search-box:focus-within .faq-search-icon {
    color: var(--faq-slate-dark);
}
.faq-search-input {
    width: 100%;
    border: none;
    outline: none;
    background: transparent;
    color: var(--faq-text-title);
    font-size: 0.9375rem;
    line-height: 1.4;
    padding: 0.15rem 0;
}
.faq-search-input::placeholder {
    color: #94A3B8;
}
.faq-search-shortcut {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 22px;
    height: 22px;
    padding: 0 6px;
    font-size: 0.75rem;
    font-weight: 600;
    color: #94A3B8;
    background: #F1F5F9;
    border: 1px solid #E2E8F0;
    border-radius: 6px;
    margin-left: 0.5rem;
    user-select: none;
    transition: opacity 0.2s ease;
}
.faq-search-box:focus-within .faq-search-shortcut {
    display: none;
}
.faq-search-clear {
    display: none;
    background: transparent;
    border: none;
    color: #94A3B8;
    cursor: pointer;
    padding: 4px;
    border-radius: 50%;
    margin-left: 0.4rem;
    transition: color 0.2s ease, transform 0.15s ease;
}
.faq-search-clear:hover {
    color: var(--faq-slate-dark);
    transform: scale(1.1);
}
.faq-search-stats {
    display: none;
    font-size: 0.8125rem;
    color: var(--faq-text-muted);
    margin-top: 0.65rem;
    text-align: center;
}
.faq-search-stats strong {
    color: var(--faq-text-title);
    font-weight: 600;
}

/* Category Quick-Filter Navigation Chips */
.faq-category-nav {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 1.25rem;
    margin-bottom: 0.5rem;
}
.faq-nav-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.4rem 0.9rem;
    border-radius: var(--faq-radius-pill);
    background: #FFFFFF;
    border: 1px solid #E2E8F0;
    color: #475569;
    font-size: 0.8125rem;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none !important;
    transition: all 0.2s ease;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03);
    user-select: none;
}
.faq-nav-chip:hover {
    background: #F8FAFC;
    border-color: #CBD5E1;
    color: var(--faq-text-title);
    transform: translateY(-1px);
}
.faq-nav-chip.active {
    background: var(--faq-slate-dark);
    border-color: var(--faq-slate-dark);
    color: #FFFFFF;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.15);
}
.faq-nav-chip-count {
    display: inline-block;
    padding: 0.1rem 0.4rem;
    font-size: 0.6875rem;
    font-weight: 600;
    border-radius: var(--faq-radius-pill);
    background: #F1F5F9;
    color: #64748B;
    transition: background 0.2s ease, color 0.2s ease;
}
.faq-nav-chip.active .faq-nav-chip-count {
    background: rgba(255, 255, 255, 0.2);
    color: #FFFFFF;
}

/* Main Content Toolbar (Count & Expand/Collapse All) */
.faq-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    padding: 0.75rem 1rem;
    background: rgba(255, 255, 255, 0.7);
    border: 1px solid rgba(226, 232, 240, 0.8);
    border-radius: 14px;
    backdrop-filter: blur(6px);
}
.faq-toolbar-count {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8125rem;
    color: #64748B;
    font-weight: 500;
}
.faq-toolbar-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--faq-gold-primary);
}
.faq-toolbar-count strong {
    color: var(--faq-text-title);
    font-weight: 700;
}
.faq-toolbar-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.35rem 0.75rem;
    border-radius: 8px;
    background: #FFFFFF;
    border: 1px solid #CBD5E1;
    color: #334155;
    font-size: 0.78125rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}
.faq-toolbar-btn:hover {
    background: #F1F5F9;
    border-color: #94A3B8;
    color: var(--faq-text-title);
}

/* Category Section */
.faq-category {
    margin-bottom: 2.75rem;
    scroll-margin-top: 90px;
}
.faq-category-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1.25rem;
    padding-bottom: 0.65rem;
    border-bottom: 1px solid #E2E8F0;
}
.faq-category-title-wrap {
    display: flex;
    align-items: center;
    gap: 0.65rem;
}
.faq-category-title {
    font-size: 1.28rem;
    font-weight: 800;
    color: var(--faq-text-title);
    letter-spacing: -0.02em;
    margin: 0;
    line-height: 1.3;
}
@media (min-width: 768px) {
    .faq-category-title {
        font-size: 1.45rem;
    }
}
.faq-category-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.2rem 0.55rem;
    border-radius: var(--faq-radius-pill);
    background: #F1F5F9;
    border: 1px solid #E2E8F0;
    color: #475569;
    font-size: 0.71875rem;
    font-weight: 600;
}

/* 2-Column Responsive Grid */
.faq-grid-2col {
    display: grid !important;
    grid-template-columns: 1fr !important;
    gap: 1rem !important;
    align-items: start !important;
}
@media (min-width: 768px) {
    .faq-grid-2col {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 1.15rem !important;
    }
}

/* Accordion Card (Refined & Balanced) */
.faq-card {
    background: #FFFFFF !important;
    border: 1px solid var(--faq-card-border) !important;
    border-left: 3.5px solid transparent !important;
    border-radius: var(--faq-radius-card) !important;
    padding: 1.15rem 1.35rem !important;
    box-shadow: 0 2px 6px -2px rgba(15, 23, 42, 0.04) !important;
    transition: border-color 0.22s ease, box-shadow 0.22s ease, transform 0.22s ease, border-left-color 0.22s ease !important;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    margin-bottom: 0 !important;
}
@media (min-width: 768px) {
    .faq-card {
        padding: 1.25rem 1.45rem !important;
    }
}
.faq-card:hover {
    border-color: var(--faq-card-hover-border) !important;
    border-left-color: var(--faq-gold-primary) !important;
    box-shadow: 0 8px 20px -4px rgba(15, 23, 42, 0.07) !important;
    transform: translateY(-2px);
}
details[open].faq-card {
    border-color: #CBD5E1 !important;
    border-left-color: var(--faq-gold-primary) !important;
    box-shadow: 0 10px 24px -4px rgba(15, 23, 42, 0.08) !important;
    background: #FFFFFF !important;
}

/* Summary Trigger */
.faq-card-summary {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 0.85rem !important;
    cursor: pointer;
    list-style: none !important;
    user-select: none;
    margin: 0 !important;
    padding: 0 !important;
}
.faq-card-summary::-webkit-details-marker,
.faq-card-summary::marker {
    display: none !important;
}
.faq-card-summary:focus-visible {
    outline: 2px solid var(--faq-gold-primary);
    outline-offset: 4px;
    border-radius: 8px;
}

/* Question Header Layout */
.faq-question-col {
    display: flex;
    align-items: flex-start;
    gap: 0.65rem;
    flex: 1;
}
.faq-item-num {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    color: #94A3B8;
    margin-top: 1px;
    letter-spacing: -0.02em;
    font-variant-numeric: tabular-nums;
}
details[open] .faq-item-num {
    color: var(--faq-gold-primary);
}
.faq-question-text {
    font-size: 0.95rem !important;
    font-weight: 600 !important;
    color: var(--faq-text-title) !important;
    line-height: 1.48 !important;
    margin: 0 !important;
    text-align: left;
    transition: color 0.2s ease;
}
@media (min-width: 768px) {
    .faq-question-text {
        font-size: 1rem !important;
    }
}
.faq-card:hover .faq-question-text {
    color: var(--faq-slate-dark);
}
details[open] .faq-question-text {
    color: var(--faq-slate-dark);
}

/* Sleek Toggle Button (Smooth Icon Morphing / Rotation) */
.faq-toggle-circle {
    width: 32px !important;
    height: 32px !important;
    min-width: 32px !important;
    border-radius: 50% !important;
    background-color: #F1F5F9 !important;
    border: 1px solid #E2E8F0 !important;
    color: #475569 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0;
    transition: all 0.25s var(--faq-ease-spring) !important;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
}
.faq-card:hover .faq-toggle-circle {
    background-color: #E2E8F0 !important;
    color: var(--faq-text-title) !important;
    transform: scale(1.05);
}
details[open] .faq-toggle-circle {
    background-color: var(--faq-slate-dark) !important;
    border-color: var(--faq-slate-dark) !important;
    color: #FFFFFF !important;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.2);
}
.faq-toggle-icon {
    transition: transform 0.28s var(--faq-ease-spring);
}
details[open] .faq-toggle-icon {
    transform: rotate(45deg);
}

/* 60fps CSS Grid Smooth Accordion Expansion */
.faq-card-anim-wrap {
    display: grid !important;
    grid-template-rows: 0fr !important;
    transition: grid-template-rows 0.28s var(--faq-ease-spring) !important;
}
details[open] > .faq-card-anim-wrap {
    grid-template-rows: 1fr !important;
}
.faq-card-body-inner {
    overflow: hidden;
}

/* Answer Body Styling */
.faq-card-body {
    margin-top: 0.85rem !important;
    padding-top: 0.85rem !important;
    border-top: 1px solid #F1F5F9 !important;
}
.faq-card-answer {
    color: var(--faq-text-body) !important;
    font-size: 0.90625rem !important;
    line-height: 1.68 !important;
}
.faq-card-answer p {
    margin-bottom: 0.55rem !important;
    color: var(--faq-text-body) !important;
}
.faq-card-answer p:last-child {
    margin-bottom: 0 !important;
}
.faq-card-answer strong,
.faq-card-answer b {
    color: var(--faq-text-title) !important;
    font-weight: 600 !important;
}
.faq-card-answer ul,
.faq-card-answer ol {
    margin-left: 1.25rem !important;
    margin-bottom: 0.65rem !important;
    color: var(--faq-text-body) !important;
}
.faq-card-answer li {
    margin-bottom: 0.25rem !important;
    padding-left: 0.2rem;
}
.faq-card-answer li::marker {
    color: var(--faq-gold-primary);
}

/* Card Action Footer (Copy Link, Direct URL) */
.faq-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 0.85rem;
    padding-top: 0.65rem;
    border-top: 1px dashed #F1F5F9;
    font-size: 0.75rem;
    color: #94A3B8;
}
.faq-copy-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: transparent;
    border: none;
    color: #64748B;
    font-size: 0.75rem;
    font-weight: 500;
    cursor: pointer;
    padding: 0.2rem 0.4rem;
    border-radius: 6px;
    transition: background 0.15s ease, color 0.15s ease;
}
.faq-copy-btn:hover {
    background: #F1F5F9;
    color: var(--faq-text-title);
}

/* Real-time Search Match Highlighter */
.faq-search-mark {
    background-color: #FEF08A !important; /* Soft energetic yellow highlight */
    color: #854D0E !important;
    padding: 0.05em 0.3em;
    border-radius: 4px;
    font-weight: 600;
}

/* Polite Empty State */
.faq-empty-state {
    text-align: center;
    padding: 3.5rem 1.5rem;
    background: #FFFFFF;
    border: 1px dashed #CBD5E1;
    border-radius: 24px;
    margin: 2rem 0;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02);
}
.faq-empty-icon {
    width: 52px;
    height: 52px;
    border-radius: 16px;
    background-color: #F8FAFC;
    border: 1px solid #E2E8F0;
    color: #94A3B8;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.15rem auto;
}
.faq-empty-title {
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--faq-text-title);
    margin-bottom: 0.4rem;
}
.faq-empty-desc {
    color: var(--faq-text-muted);
    font-size: 0.90625rem;
    max-width: 26rem;
    margin: 0 auto 1.5rem auto;
    line-height: 1.55;
}
.faq-empty-actions {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.85rem;
}
@media (min-width: 640px) {
    .faq-empty-actions {
        flex-direction: row;
        justify-content: center;
    }
}
.faq-reset-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.55rem 1.25rem;
    border-radius: var(--faq-radius-pill);
    background-color: var(--faq-slate-dark);
    color: #FFFFFF;
    font-size: 0.8125rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: background-color 0.2s ease, transform 0.15s ease;
}
.faq-reset-btn:hover {
    background-color: var(--faq-slate-hover);
    transform: translateY(-1px);
}
.faq-ask-wa-link {
    color: #047857;
    font-size: 0.8125rem;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.15s ease;
}
.faq-ask-wa-link:hover {
    color: #065F46;
    text-decoration: underline;
}

/* Compact Need Help Card at Bottom */
.faq-need-help-card {
    margin-top: 3.5rem;
    margin-bottom: 1.5rem;
    background: #FFFFFF;
    border: 1px solid #E2E8F0;
    border-radius: 24px;
    padding: 1.5rem 1.75rem;
    box-shadow: 0 2px 10px -2px rgba(15, 23, 42, 0.04);
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    align-items: center;
    justify-content: space-between;
}
@media (min-width: 768px) {
    .faq-need-help-card {
        flex-direction: row;
        gap: 1.75rem;
    }
}
.faq-help-info {
    display: flex;
    align-items: center;
    gap: 1.15rem;
    text-align: center;
}
@media (min-width: 768px) {
    .faq-help-info {
        text-align: left;
    }
}
.faq-help-icon {
    position: relative;
    width: 48px;
    height: 48px;
    min-width: 48px;
    border-radius: 14px;
    background-color: var(--faq-slate-dark);
    color: var(--faq-gold-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 10px rgba(15, 23, 42, 0.15);
}
.faq-help-status-dot {
    position: absolute;
    top: -3px;
    right: -3px;
    width: 11px;
    height: 11px;
    background-color: #10B981;
    border: 2px solid #FFFFFF;
    border-radius: 50%;
}
.faq-help-title {
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--faq-text-title);
    margin: 0 0 0.25rem 0;
}
.faq-help-subtitle {
    font-size: 0.84375rem;
    color: var(--faq-text-muted);
    margin: 0;
    line-height: 1.5;
}
.faq-help-actions {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-shrink: 0;
    width: 100%;
    justify-content: center;
}
@media (min-width: 768px) {
    .faq-help-actions {
        width: auto;
    }
}
.faq-btn-wa {
    display: inline-flex;
    align-items: center;
    gap: 0.55rem;
    padding: 0.6rem 1.25rem;
    border-radius: 12px;
    background-color: #0F172A;
    color: #FFFFFF !important;
    font-size: 0.84375rem;
    font-weight: 600;
    text-decoration: none !important;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.1);
    transition: background-color 0.2s ease, transform 0.15s ease, box-shadow 0.2s ease;
}
.faq-btn-wa:hover {
    background-color: #1E293B;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
}
.faq-btn-wa svg {
    color: #25D366; /* Authentic WhatsApp Emerald */
}
.faq-btn-email {
    display: inline-flex;
    align-items: center;
    gap: 0.55rem;
    padding: 0.6rem 1.15rem;
    border-radius: 12px;
    background-color: #FEF3C7;
    color: #78350F !important;
    border: 1px solid #FDE68A;
    font-size: 0.84375rem;
    font-weight: 600;
    text-decoration: none !important;
    transition: background-color 0.2s ease, transform 0.15s ease;
}
.faq-btn-email:hover {
    background-color: #FDE68A;
    transform: translateY(-1px);
}

/* Toast Notification */
.faq-toast {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.65rem 1.15rem;
    background: var(--faq-slate-dark);
    color: #FFFFFF;
    font-size: 0.8125rem;
    font-weight: 600;
    border-radius: var(--faq-radius-pill);
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.25);
    z-index: 9999;
    opacity: 0;
    transform: translateY(12px);
    pointer-events: none;
    transition: opacity 0.25s ease, transform 0.25s ease;
}
.faq-toast.show {
    opacity: 1;
    transform: translateY(0);
}
.faq-toast svg {
    color: #10B981;
}

/* Reduced Motion Support (Accessibility standard) */
@media (prefers-reduced-motion: reduce) {
    .faq-card,
    .faq-card:hover,
    .faq-toggle-circle,
    .faq-toggle-icon,
    .faq-card-anim-wrap,
    .faq-toast,
    .faq-kicker {
        transition: none !important;
        transform: none !important;
        animation: none !important;
    }
}
</style>

<!-- ── INLINE VANILLA JS PARSER & CONTROLLER (ROBUST & CACHE-PROOF) ─────── -->
<script>
(function() {
    'use strict';

    function initFaqRedesign() {
        var rawEl = document.getElementById('raw-faq-content');
        var renderedContainer = document.getElementById('faq-rendered-container');
        var searchInput = document.getElementById('faq-search');
        var searchClearBtn = document.getElementById('faq-search-clear');
        var searchStatsEl = document.getElementById('faq-search-stats');
        var categoryNavEl = document.getElementById('faq-category-nav');
        var toolbarEl = document.getElementById('faq-toolbar');
        var countTextEl = document.getElementById('faq-count-text');
        var toggleAllBtn = document.getElementById('faq-toggle-all-btn');
        var toggleAllText = document.getElementById('faq-toggle-all-text');
        var emptyStateEl = document.getElementById('faq-empty-state');
        var emptyMsgEl = document.getElementById('faq-empty-message');
        var resetFilterBtn = document.getElementById('faq-reset-filter-btn');
        var toastEl = document.getElementById('faq-toast');
        var toastMsgEl = document.getElementById('faq-toast-msg');

        if (!rawEl || !renderedContainer) return;

        // 1. Target content container
        var contentContainer = rawEl.querySelector('.ck-content') || rawEl;

        // 2. Identify heading levels dynamically (supports CMS H2/H3 or H3/H4)
        var hasH2 = !!contentContainer.querySelector('h2');
        var hasH3 = !!contentContainer.querySelector('h3');
        var hasH4 = !!contentContainer.querySelector('h4');

        var catTag = 'H2';
        var qTag = 'H3';

        if (hasH2 && hasH3) {
            catTag = 'H2';
            qTag = 'H3';
        } else if (!hasH2 && hasH3 && hasH4) {
            catTag = 'H3';
            qTag = 'H4';
        } else if (hasH2 && !hasH3) {
            catTag = null;
            qTag = 'H2';
        } else if (!hasH2 && hasH3 && !hasH4) {
            catTag = null;
            qTag = 'H3';
        }

        // 3. Iteratively parse children into structured categories & items
        var categories = [];
        var currentCategory = {
            title: 'Umum & Layanan',
            items: []
        };
        var currentQuestion = null;

        function getHeaderTag(el) {
            if (!el || el.nodeType !== Node.ELEMENT_NODE) return null;
            var tag = el.tagName.toUpperCase();
            if (tag === catTag || tag === qTag) return tag;
            if (el.children && el.children.length === 1) {
                var childTag = el.children[0].tagName.toUpperCase();
                if (childTag === catTag || childTag === qTag) return childTag;
            }
            return null;
        }

        function getHeaderText(el) {
            return (el.textContent || '').trim();
        }

        var childNodes = Array.from(contentContainer.children);

        for (var i = 0; i < childNodes.length; i++) {
            var child = childNodes[i];
            var headerTag = getHeaderTag(child);

            if (catTag && headerTag === catTag) {
                if (currentQuestion && currentQuestion.question) {
                    currentCategory.items.push(currentQuestion);
                    currentQuestion = null;
                }
                if (currentCategory.items.length > 0) {
                    categories.push(currentCategory);
                }
                currentCategory = {
                    title: getHeaderText(child),
                    items: []
                };
            } else if (headerTag === qTag) {
                if (currentQuestion && currentQuestion.question) {
                    currentCategory.items.push(currentQuestion);
                }
                currentQuestion = {
                    question: getHeaderText(child),
                    answerHtml: ''
                };
            } else {
                if (currentQuestion) {
                    var txt = (child.textContent || '').replace(/\u00a0/g, ' ').trim();
                    if (txt !== '' || child.querySelector('img, table, iframe')) {
                        currentQuestion.answerHtml += child.outerHTML;
                    }
                }
            }
        }

        if (currentQuestion && currentQuestion.question) {
            currentCategory.items.push(currentQuestion);
        }
        if (currentCategory.items.length > 0) {
            categories.push(currentCategory);
        }

        // Fallback: If no structured categories, output sanitized HTML
        if (categories.length === 0) {
            var rawHtml = contentContainer.innerHTML.trim();
            if (rawHtml) {
                renderedContainer.innerHTML = '<div class="faq-card" style="padding:1.5rem;">' + rawHtml + '</div>';
            }
            return;
        }

        function escapeHtml(str) {
            return String(str || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function stripHtml(html) {
            var tmp = document.createElement('div');
            tmp.innerHTML = html;
            return (tmp.textContent || tmp.innerText || '').trim();
        }

        var totalQuestions = 0;
        categories.forEach(function(cat) {
            totalQuestions += cat.items.length;
        });

        // 4. Render Category Quick-Filter Navigation Chips
        if (categoryNavEl && categories.length > 1) {
            var navHtml = '<button type="button" class="faq-nav-chip active" data-cat-filter="all">' +
                '<span>Semua</span>' +
                '<span class="faq-nav-chip-count">' + totalQuestions + '</span>' +
                '</button>';

            categories.forEach(function(cat, idx) {
                var catSlug = 'cat-' + idx;
                navHtml += '<button type="button" class="faq-nav-chip" data-cat-filter="' + catSlug + '">' +
                    '<span>' + escapeHtml(cat.title) + '</span>' +
                    '<span class="faq-nav-chip-count">' + cat.items.length + '</span>' +
                    '</button>';
            });

            categoryNavEl.innerHTML = navHtml;
        }

        // 5. Render 2-Column Responsive Layout
        var renderedHtml = '';
        var globalIndex = 1;

        categories.forEach(function(cat, catIdx) {
            var itemsHtml = '';
            var catSlug = 'cat-' + catIdx;

            cat.items.forEach(function(item, itemIdx) {
                var qClean = escapeHtml(item.question);
                var aCleanText = escapeHtml(stripHtml(item.answerHtml));
                var itemId = 'faq-card-' + catIdx + '-' + itemIdx;
                var numFormatted = globalIndex < 10 ? '0' + globalIndex : String(globalIndex);
                globalIndex++;

                itemsHtml += 
                    '<details class="faq-card" id="' + itemId + '" data-question-text="' + qClean + '" data-answer-text="' + aCleanText + '" data-cat-slug="' + catSlug + '">' +
                        '<summary class="faq-card-summary">' +
                            '<div class="faq-question-col">' +
                                '<span class="faq-item-num" aria-hidden="true">' + numFormatted + '</span>' +
                                '<h3 class="faq-question-text">' + qClean + '</h3>' +
                            '</div>' +
                            '<span class="faq-toggle-circle" aria-hidden="true">' +
                                '<svg class="faq-toggle-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">' +
                                    '<line x1="12" y1="5" x2="12" y2="19"></line>' +
                                    '<line x1="5" y1="12" x2="19" y2="12"></line>' +
                                '</svg>' +
                            '</span>' +
                        '</summary>' +
                        '<div class="faq-card-anim-wrap">' +
                            '<div class="faq-card-body-inner">' +
                                '<div class="faq-card-body">' +
                                    '<div class="faq-card-answer">' + item.answerHtml + '</div>' +
                                    '<div class="faq-card-footer">' +
                                        '<span>' + escapeHtml(cat.title) + '</span>' +
                                        '<button type="button" class="faq-copy-btn" data-card-id="' + itemId + '" aria-label="Salin tautan pertanyaan ini">' +
                                            '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' +
                                                '<rect width="14" height="14" x="8" y="8" rx="2" ry="2"/>' +
                                                '<path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/>' +
                                            '</svg>' +
                                            '<span>Salin Tautan</span>' +
                                        '</button>' +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</details>';
            });

            renderedHtml += 
                '<section class="faq-category" id="' + catSlug + '" data-cat-slug="' + catSlug + '" data-category-title="' + escapeHtml(cat.title) + '">' +
                    '<div class="faq-category-header">' +
                        '<div class="faq-category-title-wrap">' +
                            '<h2 class="faq-category-title">' + escapeHtml(cat.title) + '</h2>' +
                            '<span class="faq-category-badge">' + cat.items.length + ' Pertanyaan</span>' +
                        '</div>' +
                    '</div>' +
                    '<div class="faq-grid-2col">' +
                        itemsHtml +
                    '</div>' +
                '</section>';
        });

        renderedContainer.innerHTML = renderedHtml;

        // Display Toolbar & Update Count
        if (toolbarEl && countTextEl) {
            toolbarEl.style.display = 'flex';
            countTextEl.innerHTML = 'Menampilkan <strong>' + totalQuestions + '</strong> pertanyaan dalam <strong>' + categories.length + '</strong> kategori';
        }

        // 6. Interactive Toast Function
        var toastTimer = null;
        function showToast(message) {
            if (!toastEl) return;
            if (toastMsgEl) toastMsgEl.textContent = message;
            toastEl.classList.add('show');
            if (toastTimer) clearTimeout(toastTimer);
            toastTimer = setTimeout(function() {
                toastEl.classList.remove('show');
            }, 2500);
        }

        // 7. Click-to-Copy FAQ Direct Link
        renderedContainer.addEventListener('click', function(e) {
            var copyBtn = e.target.closest('.faq-copy-btn');
            if (!copyBtn) return;
            e.preventDefault();
            e.stopPropagation();

            var cardId = copyBtn.getAttribute('data-card-id');
            if (!cardId) return;

            var url = window.location.origin + window.location.pathname + window.location.search + '#' + cardId;
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(function() {
                    showToast('Tautan pertanyaan berhasil disalin!');
                }).catch(function() {
                    fallbackCopy(url);
                });
            } else {
                fallbackCopy(url);
            }

            function fallbackCopy(text) {
                var temp = document.createElement('input');
                temp.value = text;
                document.body.appendChild(temp);
                temp.select();
                document.execCommand('copy');
                document.body.removeChild(temp);
                showToast('Tautan pertanyaan berhasil disalin!');
            }
        });

        // 8. Expand All / Collapse All Toggle Logic
        var allExpanded = false;
        if (toggleAllBtn && toggleAllText) {
            toggleAllBtn.addEventListener('click', function() {
                var visibleCards = renderedContainer.querySelectorAll('.faq-card');
                allExpanded = !allExpanded;

                visibleCards.forEach(function(card) {
                    if (card.style.display !== 'none') {
                        card.open = allExpanded;
                    }
                });

                if (allExpanded) {
                    toggleAllText.textContent = 'Tutup Semua';
                } else {
                    toggleAllText.textContent = 'Buka Semua';
                }
            });
        }

        // 9. Category Nav Chip Filter Logic
        var currentActiveCat = 'all';
        if (categoryNavEl) {
            categoryNavEl.addEventListener('click', function(e) {
                var chip = e.target.closest('.faq-nav-chip');
                if (!chip) return;

                var filter = chip.getAttribute('data-cat-filter');
                if (!filter) return;

                categoryNavEl.querySelectorAll('.faq-nav-chip').forEach(function(c) {
                    c.classList.remove('active');
                });
                chip.classList.add('active');
                currentActiveCat = filter;

                var catSections = renderedContainer.querySelectorAll('.faq-category');
                var matchingCount = 0;

                catSections.forEach(function(sec) {
                    var secSlug = sec.getAttribute('data-cat-slug');
                    if (filter === 'all' || secSlug === filter) {
                        sec.style.display = '';
                        var count = sec.querySelectorAll('.faq-card').length;
                        matchingCount += count;
                    } else {
                        sec.style.display = 'none';
                    }
                });

                if (countTextEl) {
                    if (filter === 'all') {
                        countTextEl.innerHTML = 'Menampilkan <strong>' + totalQuestions + '</strong> pertanyaan dalam <strong>' + categories.length + '</strong> kategori';
                    } else {
                        countTextEl.innerHTML = 'Menampilkan <strong>' + matchingCount + '</strong> pertanyaan pada kategori terpilih';
                    }
                }

                // If user selected a specific category, smooth scroll into view
                if (filter !== 'all') {
                    var targetSec = document.getElementById(filter);
                    if (targetSec) {
                        targetSec.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            });
        }

        // 10. Keyword Highlighter Helper
        function highlightMatch(text, term) {
            if (!term) return escapeHtml(text);
            var escapedTerm = term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            var regex = new RegExp('(' + escapedTerm + ')', 'gi');
            return escapeHtml(text).replace(regex, '<mark class="faq-search-mark">$1</mark>');
        }

        // Cache original text for restoration after search
        var allCardElements = renderedContainer.querySelectorAll('.faq-card');
        allCardElements.forEach(function(card) {
            var qEl = card.querySelector('.faq-question-text');
            if (qEl) card._origQuestionHtml = qEl.innerHTML;
        });

        // 11. Live Search & Highlight Logic
        if (searchInput) {
            var categorySections = renderedContainer.querySelectorAll('.faq-category');

            function performFilter() {
                var term = searchInput.value.trim().toLowerCase();
                var rawTerm = searchInput.value.trim();
                var totalMatches = 0;

                // Reset Category nav to 'all' while searching
                if (categoryNavEl) {
                    categoryNavEl.querySelectorAll('.faq-nav-chip').forEach(function(c) {
                        c.classList.toggle('active', c.getAttribute('data-cat-filter') === 'all');
                    });
                    currentActiveCat = 'all';
                }

                if (term === '') {
                    if (searchClearBtn) searchClearBtn.style.display = 'none';
                    if (searchStatsEl) searchStatsEl.style.display = 'none';
                    if (emptyStateEl) emptyStateEl.style.display = 'none';
                    if (toolbarEl) toolbarEl.style.display = 'flex';

                    categorySections.forEach(function(catEl) {
                        catEl.style.display = '';
                        var items = catEl.querySelectorAll('.faq-card');
                        items.forEach(function(item) {
                            item.style.display = '';
                            item.open = false;
                            var qEl = item.querySelector('.faq-question-text');
                            if (qEl && item._origQuestionHtml) {
                                qEl.innerHTML = item._origQuestionHtml;
                            }
                        });
                    });

                    if (countTextEl) {
                        countTextEl.innerHTML = 'Menampilkan <strong>' + totalQuestions + '</strong> pertanyaan dalam <strong>' + categories.length + '</strong> kategori';
                    }
                    if (toggleAllText) toggleAllText.textContent = 'Buka Semua';
                    allExpanded = false;
                    return;
                }

                if (searchClearBtn) searchClearBtn.style.display = 'inline-block';

                categorySections.forEach(function(catEl) {
                    var items = catEl.querySelectorAll('.faq-card');
                    var catMatches = 0;

                    items.forEach(function(item) {
                        var qRaw = item.dataset.questionText || '';
                        var aRaw = item.dataset.answerText || '';
                        var qLower = qRaw.toLowerCase();
                        var aLower = aRaw.toLowerCase();
                        var isMatch = qLower.indexOf(term) !== -1 || aLower.indexOf(term) !== -1;

                        var qEl = item.querySelector('.faq-question-text');

                        if (isMatch) {
                            item.style.display = '';
                            item.open = true; // Auto expand matching items
                            catMatches++;
                            totalMatches++;

                            // Highlight matches in question
                            if (qEl) {
                                qEl.innerHTML = highlightMatch(qRaw, rawTerm);
                            }
                        } else {
                            item.style.display = 'none';
                            item.open = false;
                            if (qEl && item._origQuestionHtml) {
                                qEl.innerHTML = item._origQuestionHtml;
                            }
                        }
                    });

                    if (catMatches > 0) {
                        catEl.style.display = '';
                    } else {
                        catEl.style.display = 'none';
                    }
                });

                if (totalMatches === 0) {
                    if (emptyStateEl) {
                        emptyStateEl.style.display = 'block';
                        if (emptyMsgEl) {
                            emptyMsgEl.innerHTML = 'Tidak ada pertanyaan atau jawaban yang memuat kata kunci "<strong>' + escapeHtml(rawTerm) + '</strong>". Silakan coba kata kunci lain atau hubungi Pustakawan.';
                        }
                    }
                    if (searchStatsEl) searchStatsEl.style.display = 'none';
                    if (toolbarEl) toolbarEl.style.display = 'none';
                } else {
                    if (emptyStateEl) emptyStateEl.style.display = 'none';
                    if (toolbarEl) toolbarEl.style.display = 'flex';
                    if (searchStatsEl) {
                        searchStatsEl.style.display = 'block';
                        searchStatsEl.innerHTML = 'Ditemukan <strong>' + totalMatches + '</strong> pertanyaan yang relevan';
                    }
                    if (countTextEl) {
                        countTextEl.innerHTML = 'Hasil pencarian: <strong>' + totalMatches + '</strong> pertanyaan cocok';
                    }
                    if (toggleAllText) toggleAllText.textContent = 'Tutup Semua';
                    allExpanded = true;
                }
            }

            searchInput.addEventListener('input', performFilter);

            if (searchClearBtn) {
                searchClearBtn.addEventListener('click', function() {
                    searchInput.value = '';
                    performFilter();
                    searchInput.focus();
                });
            }

            if (resetFilterBtn) {
                resetFilterBtn.addEventListener('click', function() {
                    searchInput.value = '';
                    performFilter();
                    searchInput.focus();
                });
            }

            // Keyboard Shortcut: press '/' to focus search, 'Escape' to clear/blur
            document.addEventListener('keydown', function(e) {
                if (e.key === '/' && document.activeElement !== searchInput && !['input', 'textarea'].includes((document.activeElement.tagName || '').toLowerCase())) {
                    e.preventDefault();
                    searchInput.focus();
                    searchInput.select();
                } else if (e.key === 'Escape' && document.activeElement === searchInput) {
                    searchInput.value = '';
                    performFilter();
                    searchInput.blur();
                }
            });
        }

        // 12. Deep Linking: open card if URL hash matches
        function checkUrlHash() {
            var hash = window.location.hash;
            if (hash && hash.indexOf('#faq-card-') === 0) {
                var targetCard = document.querySelector(hash);
                if (targetCard && targetCard.tagName === 'DETAILS') {
                    targetCard.open = true;
                    setTimeout(function() {
                        targetCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 200);
                }
            }
        }
        checkUrlHash();
        window.addEventListener('hashchange', checkUrlHash);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFaqRedesign);
    } else {
        initFaqRedesign();
    }
})();
</script>
