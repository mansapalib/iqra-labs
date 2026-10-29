<?php
/**
 * =========================================================================
 * MANSAPA Iqra' Labs — Minimalist Light Visitor Counter Template
 * File: template/iqra-labs/visitor_template.php
 *
 * Light theme with simple, minimalist aesthetics:
 * - Subtle low-opacity library background (assets/images/perpustakaan.jpg)
 * - Crisp white cards with hairline borders and airy elevation
 * - Real-time live digital clock and date display with tabular monospace typography
 * - Distraction-free, accessible check-in form with barcode scanner readiness
 * - Literary quote card and SLiMS 9 Bulian visitor API compatibility
 * =========================================================================
 */

/** @var array $sysconf */
global $sysconf;

include_once __DIR__ . '/classic.php';

$main_template_path = __DIR__ . '/login_template.inc.php';

// Set default language cookie handling
if (isset($_GET['select_lang'])) {
    $select_lang = trim(strip_tags($_GET['select_lang']));
    if (isset($_COOKIE['select_lang'])) {
        @setcookie('select_lang', $select_lang, [
            'expires'  => time() - 14400,
            'path'     => SWB,
            'domain'   => '',
            'secure'   => false,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
    @setcookie('select_lang', $select_lang, [
        'expires'  => time() + 14400,
        'path'     => SWB,
        'domain'   => '',
        'secure'   => false,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    $sysconf['default_lang'] = $select_lang;
} else if (isset($_COOKIE['select_lang'])) {
    $sysconf['default_lang'] = trim(strip_tags($_COOKIE['select_lang']));
}

$imagesDisk     = \class_exists('\SLiMS\Filesystems\Storage') ? \SLiMS\Filesystems\Storage::images() : null;
$libraryName    = htmlspecialchars($sysconf['library_name'] ?? 'MANSAPA Iqra\' Labs');
$librarySubname = htmlspecialchars($sysconf['library_subname'] ?? 'Laboratorium Literasi');

// Logo resolution
$logoUrl = null;
if (
    isset($sysconf['logo_image']) &&
    $sysconf['logo_image'] !== '' &&
    isset($imagesDisk) && $imagesDisk &&
    $imagesDisk->isExists($logoPath = 'default/' . $sysconf['logo_image'])
) {
    $logoUrl = SWB . 'lib/minigalnano/createthumb.php?filename=images/' . $logoPath . '&width=100';
} elseif (file_exists(__DIR__ . '/assets/images/logo.png')) {
    $logoUrl = assets('images/logo.png');
}
?>

<style>
/* ==========================================================================
   VISITOR COUNTER — MINIMALIST LIGHT DESIGN SYSTEM
   ========================================================================== */
:root {
  --v-bg:             #F8FAFC;
  --v-surface:        #FFFFFF;
  --v-surface-alt:    #F1F5F9;
  --v-border:         #E2E8F0;
  --v-border-subtle:  rgba(15, 23, 42, 0.06);
  --v-ink:            #0F172A;
  --v-ink-muted:      #475569;
  --v-ink-subtle:     #64748B;
  --v-navy:           #0D1B3D;
  --v-navy-mid:       #162B5E;
  --v-navy-light:     #1E3A8A;
  --v-gold:           #B48B25;
  --v-gold-light:     #F3E5AB;
  --v-gold-accent:    #D4AF37;
  --v-emerald:        #059669;
  --v-emerald-light:  #ECFDF5;
  --v-rose:           #DC2626;
  --v-rose-light:     #FEF2F2;

  --v-font-h:         'Poppins', system-ui, -apple-system, sans-serif;
  --v-font-b:         'Inter', system-ui, -apple-system, sans-serif;
  --v-font-mono:      'JetBrains Mono', ui-monospace, 'Cascadia Mono', Consolas, monospace;
  --v-font-q:         'Cormorant Garamond', Georgia, serif;
}

/* Base Viewport Reset — Crisp Light Theme */
html, body {
  min-height: 100vh;
  margin: 0;
  padding: 0;
  background-color: var(--v-bg) !important;
  font-family: var(--v-font-b);
  color: var(--v-ink);
  overflow-x: hidden;
  -webkit-font-smoothing: antialiased;
}

/* Deactivate Vegas Dark Sliders to preserve pure clean light aesthetic */
.vegas-slide,
.vegas-container,
.vegas-overlay {
  display: none !important;
  opacity: 0 !important;
}

/* ==========================================================================
   BACKGROUND: LIBRARY IMAGE WITH LOW OPACITY
   ========================================================================== */
.visitor-bg-canvas {
  position: fixed;
  inset: 0;
  width: 100vw;
  height: 100vh;
  z-index: 0;
  overflow: hidden;
  pointer-events: none;
  background-color: var(--v-bg);
}

.visitor-bg-photo {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center center;
  opacity: 0.22; /* Clearly visible, elegant low-opacity library photo */
  filter: saturate(0.85) contrast(1.02);
  display: block;
}

.visitor-bg-overlay {
  position: absolute;
  inset: 0;
  background: radial-gradient(ellipse 90% 70% at 50% 30%, rgba(248, 250, 252, 0.35) 0%, rgba(248, 250, 252, 0.70) 100%),
              linear-gradient(180deg, rgba(248, 250, 252, 0.15) 0%, rgba(248, 250, 252, 0.55) 100%);
  pointer-events: none;
}

/* Layout Container */
.visitor-wrapper {
  position: relative;
  min-height: 100vh;
  min-height: 100dvh;
  width: 100%;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 1.25rem 2rem;
  box-sizing: border-box;
  z-index: 2;
}

/* ==========================================================================
   1. TOP NAVIGATION & TIME DISPLAY HEADER
   ========================================================================== */
.visitor-topbar {
  width: 100%;
  max-width: 1080px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1.25rem;
  z-index: 10;
  animation: fadeInDown 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
}

@keyframes fadeInDown {
  from { opacity: 0; transform: translateY(-16px); }
  to   { opacity: 1; transform: translateY(0); }
}

.brand-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.85rem;
  background: var(--v-surface);
  border: 1px solid var(--v-border);
  padding: 0.45rem 1rem 0.45rem 0.65rem;
  border-radius: 50px;
  box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
  text-decoration: none;
  color: inherit;
  transition: all 0.25s ease;
}

.brand-badge:hover {
  border-color: var(--v-navy);
  box-shadow: 0 4px 16px rgba(13, 27, 61, 0.08);
  transform: translateY(-1px);
}

.brand-logo-holder {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background-color: #000000 !important;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
  border: 1px solid #000000;
  padding: 3px;
  overflow: hidden;
}

.brand-logo-img {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: contain;
  background-color: #000000 !important;
  display: block;
}

.brand-icon-wrap {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background-color: #000000 !important;
  border: 1px solid #000000;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #D4AF37;
  font-size: 0.95rem;
  flex-shrink: 0;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
}

.brand-meta {
  display: flex;
  flex-direction: column;
  line-height: 1.2;
}

.brand-name {
  font-family: var(--v-font-h);
  font-size: 0.94rem;
  font-weight: 700;
  color: var(--v-navy);
  letter-spacing: -0.01em;
}

.brand-sub {
  font-size: 0.68rem;
  font-weight: 600;
  color: var(--v-gold);
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.topbar-actions {
  display: flex;
  align-items: center;
  gap: 0.85rem;
}

/* Minimalist Live Clock & Date Pill */
.live-clock-pill {
  display: inline-flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 0.82rem;
  color: var(--v-ink);
  background: var(--v-surface);
  border: 1px solid var(--v-border);
  padding: 0.5rem 1.15rem;
  border-radius: 50px;
  box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
  transition: border-color 0.2s ease;
}

.live-indicator {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  font-family: var(--v-font-mono);
  font-size: 0.68rem;
  font-weight: 600;
  letter-spacing: 0.08em;
  color: var(--v-emerald);
  text-transform: uppercase;
}

.live-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background-color: var(--v-emerald);
  box-shadow: 0 0 0 0 rgba(5, 150, 105, 0.5);
  animation: pulseLive 2.2s infinite ease-out;
}

@keyframes pulseLive {
  0%   { box-shadow: 0 0 0 0 rgba(5, 150, 105, 0.6); }
  70%  { box-shadow: 0 0 0 8px rgba(5, 150, 105, 0); }
  100% { box-shadow: 0 0 0 0 rgba(5, 150, 105, 0); }
}

.clock-divider {
  width: 1px;
  height: 14px;
  background-color: var(--v-border);
}

.clock-date-text {
  font-weight: 500;
  color: var(--v-ink-muted);
}

.clock-time-text {
  font-family: var(--v-font-mono);
  font-weight: 600;
  color: var(--v-ink);
  font-variant-numeric: tabular-nums;
  letter-spacing: -0.01em;
}

/* ==========================================================================
   2. CENTER STAGE — MINIMALIST CHECK-IN CARD
   ========================================================================== */
.visitor-center-stage {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  padding: 2.25rem 0;
  z-index: 5;
}

.visitor-card {
  position: relative;
  width: 100%;
  max-width: 490px;
  background: var(--v-surface);
  border: 1px solid var(--v-border);
  border-radius: 24px;
  padding: 2.5rem 2.25rem 2.25rem;
  box-shadow:
    0 24px 48px -18px rgba(15, 23, 42, 0.08),
    0 4px 16px -4px rgba(15, 23, 42, 0.04);
  box-sizing: border-box;
  animation: cardEntrance 0.7s cubic-bezier(0.16, 1, 0.3, 1) both;
  overflow: hidden;
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

/* Subtle Gold Top Accent Line */
.visitor-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg, var(--v-navy), var(--v-gold-accent), var(--v-navy));
}

@keyframes cardEntrance {
  from { opacity: 0; transform: translateY(24px) scale(0.97); }
  to   { opacity: 1; transform: translateY(0) scale(1); }
}

/* Card Header Emblem & Heading */
.card-header-emblem {
  text-align: center;
  margin-bottom: 2rem;
}

.emblem-icon-ring {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 62px;
  height: 62px;
  border-radius: 18px;
  background: var(--v-surface-alt);
  border: 1.5px solid #CBD5E1;
  color: var(--v-navy);
  margin-bottom: 1.15rem;
  box-shadow: 0 4px 14px rgba(13, 27, 61, 0.05);
  transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.3s ease, color 0.3s ease, box-shadow 0.3s ease;
}

.emblem-svg {
  width: 30px;
  height: 30px;
  display: block;
}

.visitor-card:hover .emblem-icon-ring {
  transform: translateY(-2px) scale(1.04);
  border-color: var(--v-gold);
  color: var(--v-gold);
  box-shadow: 0 6px 18px rgba(180, 139, 37, 0.15);
}

.visitor-card-title {
  font-family: var(--v-font-h);
  font-size: 1.65rem;
  font-weight: 700;
  color: var(--v-navy);
  margin: 0 0 0.4rem;
  letter-spacing: -0.025em;
}

.visitor-card-subtitle {
  font-size: 0.88rem;
  color: var(--v-ink-subtle);
  margin: 0;
  line-height: 1.5;
}

/* Form Styles */
.visitor-form {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.input-field-group {
  display: flex;
  flex-direction: column;
  gap: 0.45rem;
}

.input-label {
  display: flex;
  align-items: center;
  gap: 0.45rem;
  font-family: var(--v-font-b);
  font-size: 0.84rem;
  font-weight: 600;
  color: var(--v-ink);
  margin: 0;
}

.input-label i {
  color: var(--v-navy);
  font-size: 0.86rem;
}

.input-shell {
  position: relative;
  display: flex;
  align-items: center;
  background: var(--v-surface-alt);
  border: 1.5px solid var(--v-border);
  border-radius: 12px;
  transition: all 0.2s ease;
}

.input-shell:hover {
  border-color: #CBD5E1;
  background: #FFFFFF;
}

.input-shell:focus-within {
  border-color: var(--v-navy);
  background: #FFFFFF;
  box-shadow: 0 0 0 3.5px rgba(13, 27, 61, 0.08), 0 2px 8px rgba(15, 23, 42, 0.04);
}

.input-icon-adornment {
  padding-left: 1.05rem;
  color: var(--v-ink-subtle);
  font-size: 0.95rem;
  transition: color 0.2s ease;
  pointer-events: none;
}

.input-shell:focus-within .input-icon-adornment {
  color: var(--v-navy);
}

.minimal-input {
  width: 100%;
  height: 48px;
  background: transparent !important;
  border: none !important;
  outline: none !important;
  padding: 0 0.85rem 0 0.75rem;
  font-family: var(--v-font-b);
  font-size: 0.96rem;
  font-weight: 500;
  color: var(--v-ink) !important;
  box-shadow: none !important;
}

.minimal-input::placeholder {
  color: #94A3B8;
  font-size: 0.9rem;
  font-weight: 400;
}

.clear-input-btn {
  background: transparent;
  border: none;
  color: #94A3B8;
  padding: 0 0.85rem;
  cursor: pointer;
  font-size: 0.9rem;
  transition: color 0.2s ease;
}

.clear-input-btn:hover {
  color: var(--v-ink);
}

.input-microhint {
  font-size: 0.75rem;
  color: var(--v-ink-subtle);
  line-height: 1.4;
  padding-left: 0.25rem;
}

/* Clean Minimalist Submit Button */
.btn-visitor-submit {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 50px;
  margin-top: 0.5rem;
  font-family: var(--v-font-b);
  font-size: 0.98rem;
  font-weight: 600;
  color: #FFFFFF !important;
  background: var(--v-navy);
  border: 1px solid var(--v-navy);
  border-radius: 12px;
  cursor: pointer;
  box-shadow: 0 4px 14px rgba(13, 27, 61, 0.16);
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  text-decoration: none;
}

.btn-visitor-submit:hover:not(:disabled) {
  background: var(--v-navy-mid);
  border-color: var(--v-navy-mid);
  transform: translateY(-1.5px);
  box-shadow: 0 8px 20px rgba(13, 27, 61, 0.25);
}

.btn-visitor-submit:active:not(:disabled) {
  transform: translateY(0);
}

.btn-visitor-submit:focus-visible {
  outline: 3px solid var(--v-gold);
  outline-offset: 3px;
}

.btn-visitor-submit:disabled {
  opacity: 0.65;
  cursor: not-allowed;
  transform: none !important;
}

.btn-label-content {
  display: inline-flex;
  align-items: center;
  gap: 0.6rem;
}

/* ==========================================================================
   FEEDBACK & GREETING MODAL (STATE B)
   ========================================================================== */
.checkin-success-modal {
  text-align: center;
  padding: 0.5rem 0;
  animation: successReveal 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
}

@keyframes successReveal {
  from { opacity: 0; transform: scale(0.96) translateY(10px); }
  to   { opacity: 1; transform: scale(1) translateY(0); }
}

.success-avatar-container {
  position: relative;
  display: inline-block;
  margin-bottom: 1.25rem;
}

.visitor-avatar-halo {
  width: 90px;
  height: 90px;
  border-radius: 50%;
  padding: 3px;
  background: var(--v-surface);
  border: 2px solid var(--v-border);
  box-shadow: 0 8px 22px rgba(15, 23, 42, 0.08);
}

.visitor-avatar-img {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
  background: var(--v-surface-alt);
}

.success-check-badge {
  position: absolute;
  bottom: 2px;
  right: 2px;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: var(--v-emerald);
  color: #FFFFFF;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.82rem;
  border: 2.5px solid #FFFFFF;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.success-message-text {
  font-family: var(--v-font-b);
  font-size: 1.05rem;
  font-weight: 500;
  color: var(--v-ink);
  margin-bottom: 1.5rem;
  line-height: 1.5;
}

.success-message-text .error,
.success-message-text .visitor-error {
  color: var(--v-rose) !important;
  font-size: 0.92rem;
  padding: 0.85rem 1rem;
  background: var(--v-rose-light);
  border: 1px solid #FECACA;
  border-radius: 10px;
}

.btn-next-visitor {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: var(--v-surface-alt);
  border: 1px solid var(--v-border);
  color: var(--v-ink);
  font-family: var(--v-font-b);
  font-weight: 600;
  font-size: 0.88rem;
  padding: 0.65rem 1.4rem;
  border-radius: 50px;
  cursor: pointer;
  transition: all 0.2s ease;
  margin-bottom: 1.25rem;
}

.btn-next-visitor:hover {
  background: var(--v-navy);
  border-color: var(--v-navy);
  color: #FFFFFF;
  transform: translateY(-1px);
}

.countdown-timer-wrap {
  width: 100%;
  max-width: 200px;
  height: 4px;
  background: var(--v-border);
  border-radius: 4px;
  margin: 0 auto;
  overflow: hidden;
}

.countdown-bar-fill {
  width: 100%;
  height: 100%;
  background: var(--v-gold);
  animation: countdownShrink 5s linear forwards;
}

@keyframes countdownShrink {
  from { width: 100%; }
  to   { width: 0%; }
}

/* ==========================================================================
   3. BOTTOM CENTER — LITERARY QUOTES & FOOTER
   ========================================================================== */
.visitor-bottom-section {
  width: 100%;
  max-width: 780px;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  z-index: 10;
  animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
}

@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(16px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* Minimalist Quotes Card */
.quotes-capsule {
  width: 100%;
  background: rgba(255, 255, 255, 0.88);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border: 1px solid var(--v-border);
  border-radius: 16px;
  padding: 1.1rem 1.75rem;
  box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
  margin-bottom: 0.85rem;
  transition: border-color 0.25s ease, box-shadow 0.25s ease;
}

.quotes-capsule:hover {
  border-color: var(--v-gold);
  box-shadow: 0 6px 24px rgba(15, 23, 42, 0.06);
}

.quote-content-body {
  font-family: var(--v-font-q);
  font-size: clamp(1.05rem, 2vw, 1.25rem);
  font-style: italic;
  font-weight: 500;
  color: var(--v-ink);
  line-height: 1.45;
  margin: 0 0 0.35rem;
}

.quote-ornament {
  color: var(--v-gold);
  font-size: 0.85em;
  opacity: 0.85;
}

.quote-author-tag {
  font-family: var(--v-font-b);
  font-size: 0.76rem;
  font-weight: 600;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--v-ink-subtle);
}

/* Quotes Fade Transition */
.quote-fade-enter-active,
.quote-fade-leave-active {
  transition: opacity 0.35s ease, transform 0.35s ease;
}

.quote-fade-enter {
  opacity: 0;
  transform: translateY(6px);
}

.quote-fade-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}

/* Minimalist Footer Attribution */
.visitor-footer-meta {
  font-size: 0.76rem;
  color: var(--v-ink-subtle);
}

.visitor-footer-meta strong {
  color: var(--v-ink);
  font-weight: 600;
}

.visitor-footer-meta code {
  color: var(--v-navy);
  background: var(--v-surface-alt);
  border: 1px solid var(--v-border);
  padding: 0.15rem 0.45rem;
  border-radius: 4px;
  font-family: var(--v-font-mono);
  font-size: 0.74rem;
}

/* ==========================================================================
   RESPONSIVE DESIGN BREAKPOINTS
   ========================================================================== */
@media (max-width: 768px) {
  .visitor-topbar {
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
  }
  
  .topbar-actions {
    flex-wrap: wrap;
    justify-content: center;
  }
}

@media (max-width: 640px) {
  .visitor-wrapper {
    padding: 1rem 0.85rem 1.5rem;
  }

  .visitor-card {
    padding: 1.75rem 1.35rem 1.5rem;
    border-radius: 20px;
  }

  .visitor-card-title {
    font-size: 1.4rem;
  }

  .live-clock-pill {
    font-size: 0.74rem;
    padding: 0.4rem 0.85rem;
    gap: 0.5rem;
  }

  .quotes-capsule {
    padding: 0.95rem 1.25rem;
    border-radius: 14px;
  }

  .quote-content-body {
    font-size: 1rem;
  }
}

@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}
</style>

<!-- Background: Library Image with Low Opacity -->
<div class="visitor-bg-canvas" aria-hidden="true">
    <img src="<?= assets('images/perpustakaan.jpg'); ?>"
         class="visitor-bg-photo"
         alt=""
         onerror="this.onerror=null; this.src='<?= assets('images/slide1.jpg'); ?>';">
    <div class="visitor-bg-overlay"></div>
</div>

<!-- Main Vue Container -->
<div class="visitor-wrapper" id="visitor-counter">

    <!-- 1. TOP HEADER BAR WITH LIVE TIME DISPLAY -->
    <header class="visitor-topbar">
        <a href="index.php" class="brand-badge" aria-label="Beranda <?= $libraryName ?>">
            <div class="brand-logo-holder">
                <?php if ($logoUrl): ?>
                    <img class="brand-logo-img" src="<?= $logoUrl; ?>" alt="" width="32" height="32">
                <?php else: ?>
                    <div class="brand-icon-wrap" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M6 2v14l3-2.5 3 2.5V2"/></svg>
                    </div>
                <?php endif; ?>
            </div>
            <div class="brand-meta">
                <span class="brand-name"><?= $libraryName ?></span>
                <span class="brand-sub"><?= $librarySubname ?></span>
            </div>
        </a>

        <div class="topbar-actions">
            <!-- Real-time Live Clock & Date Display -->
            <div class="live-clock-pill" v-if="currentTime">
                <span class="live-indicator">
                    <span class="live-dot" aria-hidden="true"></span>
                    <span>Live</span>
                </span>
                <span class="clock-divider" aria-hidden="true"></span>
                <span class="clock-date-text">{{ currentDate }}</span>
                <span class="clock-divider" aria-hidden="true"></span>
                <span class="clock-time-text">{{ currentTime }}</span>
            </div>
        </div>
    </header>

    <!-- 2. CENTER STAGE — MINIMALIST CHECK-IN CARD -->
    <main class="visitor-center-stage">
        <div class="visitor-card">

            <!-- State A: Form Input -->
            <div v-show="textInfo === ''">
                <!-- Card Header -->
                <div class="card-header-emblem">
                    <div class="emblem-icon-ring" aria-hidden="true">
                        <svg class="emblem-svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="16" rx="3"/>
                            <circle cx="9" cy="10" r="2.5"/>
                            <path d="M15 8h2"/>
                            <path d="M15 12h2"/>
                            <path d="M6 17a3 3 0 0 1 6 0"/>
                        </svg>
                    </div>
                    <h1 class="visitor-card-title"><?= __('Visitor Check-In') ?></h1>
                    <p class="visitor-card-subtitle"><?= __('Welcome! Please scan or enter your Member ID.') ?></p>
                </div>

                <!-- Form -->
                <form class="visitor-form" @submit.prevent="onSubmit">
                    <!-- Member ID Field -->
                    <div class="input-field-group">
                        <label class="input-label" for="memberIdInput">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <?= __('Member ID / Name') ?>
                        </label>
                        <div class="input-shell">
                            <div class="input-icon-adornment" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 5v14M8 5v14M12 5v14M17 5v14M21 5v14"/></svg>
                            </div>
                            <input
                                v-model="memberId"
                                ref="memberId"
                                autofocus
                                type="text"
                                class="minimal-input"
                                id="memberIdInput"
                                placeholder="<?= __('Scan card or type ID...') ?>"
                                autocomplete="off"
                                required
                            >
                            <button
                                type="button"
                                v-if="memberId"
                                @click="memberId = ''; $refs.memberId.focus()"
                                class="clear-input-btn"
                                title="<?= __('Clear') ?>"
                                aria-label="<?= __('Clear') ?>"
                            >
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Institution Field -->
                    <div class="input-field-group">
                        <label class="input-label" for="institutionInput">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21h18M3 10h18M5 10v11M19 10v11M9 10v11M15 10v11M12 3l9 7H3z"/></svg>
                            <?= __('Institution') ?>
                        </label>
                        <div class="input-shell">
                            <div class="input-icon-adornment" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                            </div>
                            <input
                                v-model="institution"
                                type="text"
                                class="minimal-input"
                                id="institutionInput"
                                placeholder="<?= __('School, university, or guest...') ?>"
                                autocomplete="off"
                            >
                        </div>
                        <span class="input-microhint">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px; margin-right: 4px;" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                            <?= __('Only required if you are not yet an active member.') ?>
                        </span>
                    </div>

                    <!-- Submit Check-In Button -->
                    <button type="submit" class="btn-visitor-submit" :disabled="isSubmitting">
                        <span class="btn-label-content">
                            <svg v-if="!isSubmitting" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                            <svg v-else class="fa-spin" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="10" stroke-opacity="0.25"/><path d="M12 2a10 10 0 0 1 10 10"/></svg>
                            <span>{{ isSubmitting ? '<?= __('Recording Visit...') ?>' : '<?= __('Check In Now') ?>' }}</span>
                        </span>
                    </button>
                </form>
            </div>

            <!-- State B: Success / Feedback Greeting View -->
            <div v-show="textInfo !== ''" class="checkin-success-modal">
                <div class="success-avatar-container">
                    <div class="visitor-avatar-halo">
                        <img :src="image" alt="Visitor Photo" class="visitor-avatar-img" @error="onImageError">
                    </div>
                    <div class="success-check-badge" aria-hidden="true">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                </div>

                <div class="success-message-text" v-html="textInfo"></div>

                <button type="button" class="btn-next-visitor" @click="clearFeedbackNow">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true" style="margin-right: 6px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    <span><?= __('Check In Next Visitor') ?></span>
                </button>

                <div class="countdown-timer-wrap">
                    <div class="countdown-bar-fill"></div>
                </div>
            </div>

        </div>
    </main>

    <!-- 3. BOTTOM CENTER — LITERARY QUOTES & FOOTER -->
    <footer class="visitor-bottom-section">
        <!-- Quotes Capsule -->
        <div class="quotes-capsule" v-if="quotes && quotes.content">
            <transition name="quote-fade" mode="out-in">
                <div :key="quotes.content">
                    <p class="quote-content-body">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" class="quote-ornament me-1" style="display:inline-block; vertical-align:middle;" aria-hidden="true"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                        {{ quotes.content }}
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" class="quote-ornament ms-1" style="display:inline-block; vertical-align:middle;" aria-hidden="true"><path d="M9.983 3v7.391c0 5.704-3.731 9.57-8.983 10.609l-.995-2.151c2.432-.917 3.995-3.638 3.995-5.849h-4v-10h9.983zm14.017 0v7.391c0 5.704-3.748 9.57-9 10.609l-.996-2.151c2.433-.917 3.996-3.638 3.996-5.849h-3.983v-10h9.983z"/></svg>
                    </p>
                    <div class="quote-author-tag" v-if="quotes.author">
                        &mdash; {{ quotes.author }}
                    </div>
                </div>
            </transition>
        </div>

        <!-- System Tagline -->
        <div class="visitor-footer-meta">
            <?= __('Powered by') ?> <code>SLiMS 9 Bulian</code> &bull; <?= $libraryName ?>
        </div>
    </footer>

</div>

<!-- Scripts -->
<script src="<?php echo ($sysconf['template']['dir'] ?? 'template').'/'.($sysconf['template']['theme'] ?? 'iqra-labs').'/assets/js/axios.min.js'; ?>"></script>
<script src="<?= JWB . 'he.js' ?>"></script>
<script>
    new Vue({
        el: '#visitor-counter',
        data() {
            return {
                memberId: '',
                institution: '',
                textInfo: '',
                image: './images/persons/photo.png',
                quotes: {
                    content: 'Buku adalah cermin jiwa yang memperkaya wawasan dan budi pekerti.',
                    author: 'Iqra Labs'
                },
                timeout: null,
                isSubmitting: false,
                currentTime: '',
                currentDate: '',
                clockTimer: null,
                csrfName: '<?= \Volnix\CSRF\CSRF::getTokenName() ?>',
                csrfToken: '<?= \Volnix\CSRF\CSRF::getToken() ?>'
            }
        },
        mounted() {
            this.focusInput()
            this.getQuotes()
            this.updateClock()
            this.clockTimer = setInterval(this.updateClock, 1000)
        },
        beforeDestroy() {
            if (this.clockTimer) clearInterval(this.clockTimer)
            if (this.timeout) clearTimeout(this.timeout)
        },
        methods: {
            focusInput: function() {
                this.$nextTick(() => {
                    if (this.$refs.memberId) {
                        this.$refs.memberId.focus()
                    }
                })
            },
            updateClock: function() {
                const now = new Date()
                const lang = '<?= substr($sysconf['default_lang'] ?? 'id', 0, 2) === 'id' ? 'id-ID' : 'en-US' ?>'
                this.currentTime = now.toLocaleTimeString(lang, { hour: '2-digit', minute: '2-digit', second: '2-digit' })
                this.currentDate = now.toLocaleDateString(lang, { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' })
            },
            onImageError: function() {
                this.image = './images/persons/photo.png'
            },
            getQuotes: function() {
                axios.get('https://slims.web.id/kutipan/')
                    .then(res => {
                        if (res.data && res.data.content) {
                            res.data.content = typeof he !== 'undefined' ? he.decode(res.data.content) : res.data.content
                            this.quotes = res.data
                        }
                    })
                    .catch(() => {
                        this.quotes = {
                            content: 'Membaca adalah jendela dunia, buku adalah jembatan peradaban.',
                            author: 'Perpustakaan MANSAPA'
                        }
                    })
            },
            onSubmit: function() {
                if (this.memberId === '' || this.isSubmitting) {
                    this.resetForm()
                    return
                }

                this.isSubmitting = true
                let url = 'index.php?p=visitor<?= trim(isset($_GET['room']) ? '&room=' . simbio_security::xssFree($_GET['room']) : '')  ?>'
                let data = new FormData()
                data.append('memberID', this.memberId)
                data.append('institution', this.institution)
                data.append('counter', 1)
                data.append(this.csrfName, this.csrfToken)

                axios({
                    url: url,
                    method: 'post',
                    data: data,
                    headers: { 'Content-Type': 'multipart/form-data', 'X-Response-With': 'application/json' }
                })
                    .then(res => {
                        this.textInfo = res.data.message || '<?= __('Thank you for your visit!') ?>'
                        this.image = `./images/persons/${res.data.image || 'photo.png'}`
                        if (res.data.new_token) {
                            this.csrfToken = res.data.new_token
                        }
                        <?php if ($sysconf['template']['visitor_log_voice'] ?? false) : ?>
                            this.textToSpeech(this.textInfo.replace(/(<([^>]+)>)/ig, ''))
                        <?php endif; ?>
                    })
                    .catch(err => {
                        console.error('Visitor Check-In Error:', err)
                        if (err.response && err.response.data && err.response.data.new_token) {
                            this.csrfToken = err.response.data.new_token
                        }
                        this.textInfo = '<div class="error visitor-error"><?= __('Error submitting visitor log. Please try again.') ?></div>'
                    })
                    .finally(() => {
                        this.isSubmitting = false
                        this.memberId = ''
                        this.institution = ''
                        clearTimeout(this.timeout)
                        this.timeout = setTimeout(() => {
                            this.clearFeedbackNow()
                        }, 5000)
                    })
            },
            clearFeedbackNow: function() {
                clearTimeout(this.timeout)
                this.textInfo = ''
                this.image = './images/persons/photo.png'
                this.getQuotes()
                this.focusInput()
            },
            resetForm: function() {
                this.memberId = ''
                this.institution = ''
                this.focusInput()
            },
            textToSpeech: function(message) {
                if (!('speechSynthesis' in window)) return
                var utter = new SpeechSynthesisUtterance(message)
                utter.volume = 1
                utter.rate = 1
                utter.pitch = 1
                utter.lang = '<?php echo str_replace('_', '-', $sysconf['default_lang'] ?? 'id-ID'); ?>'
                utter.voice = null
                speechSynthesis.cancel()
                speechSynthesis.speak(utter)
            }
        }
    })
</script>
