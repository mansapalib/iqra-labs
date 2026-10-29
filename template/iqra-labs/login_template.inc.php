<?php
/**
 * =========================================================================
 * MANSAPA Iqra' Labs — login_template.inc.php  [Manuscript & Catalog Card]
 * File: template/iqra-labs/login_template.inc.php
 *
 * Editorial, asymmetric admin login:
 * - Left "folio": ink-navy manuscript page (ruled lines, gold margin rule,
 *   newspaper-style masthead, oversized serif headline, randomized
 *   motivational quote with manual shuffle, اقرأ watermark).
 * - Right "desk": the login form rendered as a cream library catalog card
 *   (call number, "Staff Only" stamp, punch hole, highlighter-sweep inputs).
 *
 * The form markup itself comes from lib/contents/login.inc.php
 * (.heading1, .login_input, .loginButton, .homeButton) and is progressively
 * enhanced here (real <label>s, password toggle, Caps Lock hint, loading state).
 * =========================================================================
 */

/** @var array $sysconf */
global $sysconf;

if (isset($_GET['p']) && $_GET['p'] === 'visitor') {
    $imagesDisk = \SLiMS\Filesystems\Storage::images();
    include_once __DIR__ . "/classic.php";
    include "parts/header.php";
    echo $main_content;
    include __DIR__ . "/assets/js/vegas.js.php";
    echo '</body></html>';
    exit;
}

include_once __DIR__ . '/classic.php';

$imagesDisk     = \class_exists('\SLiMS\Filesystems\Storage') ? \SLiMS\Filesystems\Storage::images() : null;
$libraryName    = htmlspecialchars($sysconf['library_name'] ?? "MANSAPA Iqra' Labs");
$librarySubname = htmlspecialchars($sysconf['library_subname'] ?? 'Laboratorium Literasi & Gagasan');
$icon           = SWB . 'webicon.ico';
if (
    isset($sysconf['webicon']) &&
    !empty($sysconf['webicon']) &&
    isset($imagesDisk) && $imagesDisk &&
    $imagesDisk->isExists($path = 'default/' . $sysconf['webicon'])
) {
    $icon = SWB . 'lib/minigalnano/createthumb.php?filename=images/' . $path . '&width=130';
}

// ── LOGO RESOLUTION ─────────────────────────────────────────────────────────
$logoUrl = null;
if (
    isset($sysconf['logo_image']) &&
    $sysconf['logo_image'] !== '' &&
    isset($imagesDisk) && $imagesDisk &&
    $imagesDisk->isExists($logoPath = 'default/' . $sysconf['logo_image'])
) {
    $logoUrl = SWB . 'lib/minigalnano/createthumb.php?filename=images/' . $logoPath . '&width=120';
} elseif (file_exists(__DIR__ . '/assets/images/logo.png')) {
    $logoUrl = assets('images/logo.png');
}

// ── QUOTES MOTIVASI DENGAN METODE HARDCODE ARRAY & RANDOMIZER ───────────────
$quotes_motivasi = [
    "Buku adalah jendela dunia, dan pustakawan adalah pemegang kuncinya.",
    "Satu buku yang dibaca dengan baik dapat mengubah satu kehidupan.",
    "Literasi adalah jembatan dari kesengsaraan menuju harapan.",
    "Setiap hari adalah kesempatan baru untuk menyebarkan ilmu.",
    "Melayani pemustaka dengan senyum adalah ibadah tanpa suara.",
];

$quote_index  = array_rand($quotes_motivasi);
$random_quote = $quotes_motivasi[$quote_index];
$quote_total  = count($quotes_motivasi);

// ── EDITION DATE (BAHASA INDONESIA) ─────────────────────────────────────────
$hari_id  = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$bulan_id = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$edition_date = $hari_id[(int) date('w')] . ', ' . date('j') . ' ' . $bulan_id[(int) date('n')] . ' ' . date('Y');
?>
<!DOCTYPE html>
<html lang="<?= substr($sysconf['default_lang'] ?? 'id', 0, 2); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Masuk Pustakawan &mdash; <?= $libraryName; ?></title>
    <meta name="description" content="Halaman masuk staf pustakawan <?= $libraryName; ?> — sistem otomasi perpustakaan.">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#070F23">

    <!-- Favicon -->
    <link rel="shortcut icon" href="<?= $icon; ?>" type="image/x-icon">

    <!-- Google Fonts: Playfair Display (display) · Inter (UI) · JetBrains Mono (labels) · Amiri (Arabic) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Amiri:wght@700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500;600&family=Playfair+Display:ital,wght@0,500;0,600;1,400;1,500&display=swap">

    <!-- jQuery must load before $main_content (core runs jQuery('#userName').focus() inline) -->
    <script src="<?= assets('js/jquery.min.js'); ?>"></script>

    <style>
        /* =====================================================================
           MANSAPA IQRA' LABS — MANUSCRIPT & CATALOG CARD LOGIN
           Ink:   #050B1C · #070F23 · #0D1B3D · #162B5E
           Gold:  #D4AF37 · #F3E5AB · #7A5F14 (gold text on paper, AA)
           Paper: #FBF6EA · Stamp: #9B2C2C
           ===================================================================== */
        :root {
            --ink-950: #050B1C;
            --ink-900: #070F23;
            --ink-800: #0D1B3D;
            --ink-700: #162B5E;
            --gold-500: #D4AF37;
            --gold-300: #F3E5AB;
            --gold-700: #7A5F14;
            --gold-line: rgba(212, 175, 55, 0.28);
            --cream: #F4EFE3;
            --cream-muted: rgba(244, 239, 227, 0.74);
            --paper: #FBF6EA;
            --paper-back: #E9DFC8;
            --paper-muted: #4A5568;
            --stamp: #9B2C2C;

            --f-display: 'Playfair Display', Georgia, 'Times New Roman', serif;
            --f-body: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
            --f-mono: 'JetBrains Mono', ui-monospace, 'Cascadia Mono', Consolas, monospace;
            --f-arabic: 'Amiri', 'Traditional Arabic', serif;

            --ease-out: cubic-bezier(0.16, 1, 0.3, 1);
            --ease-spring: cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        *, *::before, *::after { box-sizing: border-box; }

        html, body {
            margin: 0;
            min-height: 100%;
            background: var(--ink-950);
            color: var(--cream);
            font-family: var(--f-body);
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
        }

        /* Film-grain overlay for a printed-paper feel */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 50;
            opacity: 0.07;
            mix-blend-mode: overlay;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='160' height='160'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
        }

        /* ── PAGE GRID ───────────────────────────────────────────────────── */
        .login-shell {
            min-height: 100vh;
            min-height: 100dvh;
            display: grid;
            grid-template-columns: minmax(0, 1.3fr) minmax(400px, 1fr);
        }

        /* ── REVEAL MOTION ───────────────────────────────────────────────── */
        .reveal {
            animation: rise 0.8s var(--ease-out) both;
            animation-delay: var(--d, 0s);
        }

        @keyframes rise {
            from { opacity: 0; translate: 0 22px; }
            to   { opacity: 1; translate: 0 0; }
        }

        /* =====================================================================
           LEFT — THE FOLIO (manuscript page)
           ===================================================================== */
        .folio {
            --margin-x: 4.5rem;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            padding: 2.25rem 4rem 2.25rem calc(var(--margin-x) + 2rem);
            background:
                radial-gradient(60% 45% at 18% 0%, rgba(212, 175, 55, 0.16), transparent 70%),
                radial-gradient(50% 60% at 100% 100%, rgba(22, 43, 94, 0.75), transparent 70%),
                linear-gradient(to right, transparent calc(var(--margin-x) - 1px), rgba(212, 175, 55, 0.32) calc(var(--margin-x) - 1px), rgba(212, 175, 55, 0.32) var(--margin-x), transparent var(--margin-x)),
                repeating-linear-gradient(to bottom, transparent 0 39px, rgba(244, 239, 227, 0.045) 39px 40px),
                var(--ink-900);
            border-right: 1px solid var(--gold-line);
        }

        /* Masthead */
        .folio-top {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            padding-bottom: 1.25rem;
            border-bottom: 3px double rgba(212, 175, 55, 0.38);
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 0.85rem;
            color: inherit;
            text-decoration: none;
            border-radius: 12px;
        }

        .brand:focus-visible { outline: 2px solid var(--gold-500); outline-offset: 6px; }

        .brand-logo {
            width: 46px;
            height: 46px;
            flex: none;
            border-radius: 12px;
            object-fit: contain;
            padding: 5px;
            background: rgba(244, 239, 227, 0.06);
            border: 1px solid var(--gold-line);
            transition: rotate 0.4s var(--ease-spring), border-color 0.25s ease;
        }

        .brand-monogram {
            display: grid;
            place-items: center;
            font-family: var(--f-display);
            font-weight: 600;
            font-size: 1.15rem;
            color: var(--gold-500);
        }

        .brand:hover .brand-logo { rotate: -6deg; border-color: var(--gold-500); }

        .brand-text { display: flex; flex-direction: column; gap: 2px; }

        .brand-name {
            font-family: var(--f-display);
            font-size: 1.08rem;
            font-weight: 600;
            line-height: 1.2;
            color: var(--cream);
        }

        .brand-sub,
        .edition-label {
            font-family: var(--f-mono);
            font-size: 0.66rem;
            font-weight: 500;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--cream-muted);
        }

        .edition { text-align: right; display: flex; flex-direction: column; gap: 3px; }

        .edition time {
            font-family: var(--f-display);
            font-style: italic;
            font-size: 0.95rem;
            color: var(--cream);
        }

        /* Body */
        .folio-body {
            position: relative;
            z-index: 1;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            max-width: 46rem;
            padding: 3.5rem 0 3rem;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.65rem;
            margin: 0;
            font-family: var(--f-mono);
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--gold-500);
        }

        .live-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--gold-500);
            box-shadow: 0 0 0 0 rgba(212, 175, 55, 0.6);
            animation: pulse 2.4s ease-out infinite;
        }

        @keyframes pulse {
            0%   { box-shadow: 0 0 0 0 rgba(212, 175, 55, 0.55); }
            70%  { box-shadow: 0 0 0 10px rgba(212, 175, 55, 0); }
            100% { box-shadow: 0 0 0 0 rgba(212, 175, 55, 0); }
        }

        .folio-title {
            margin: 1.25rem 0 2.75rem;
            font-family: var(--f-display);
            font-weight: 600;
            font-size: clamp(2.6rem, 4.7vw, 4.9rem);
            line-height: 1.02;
            letter-spacing: -0.025em;
            color: var(--cream);
            text-wrap: balance;
        }

        .folio-title em {
            font-style: italic;
            font-weight: 500;
            color: var(--gold-500);
        }

        /* Quote — editorial pull quote */
        .quote {
            position: relative;
            margin: 0;
            max-width: 36rem;
            padding: 0.25rem 0 0.25rem 1.75rem;
            border-left: 1px solid rgba(212, 175, 55, 0.45);
        }

        .quote-mark {
            position: absolute;
            top: -3.4rem;
            left: 1.1rem;
            font-family: var(--f-display);
            font-size: 6.5rem;
            line-height: 1;
            color: var(--gold-500);
            opacity: 0.9;
            pointer-events: none;
            user-select: none;
        }

        .quote-meta {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.9rem;
            font-family: var(--f-mono);
            font-size: 0.68rem;
            font-weight: 500;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--cream-muted);
        }

        .quote-meta::before {
            content: '';
            width: 22px;
            height: 1px;
            background: currentColor;
        }

        .quote-no { color: var(--gold-300); font-variant-numeric: tabular-nums; }

        .quote blockquote { margin: 0; }

        .quote-text {
            margin: 0;
            font-family: var(--f-display);
            font-style: italic;
            font-weight: 400;
            font-size: clamp(1.25rem, 1.75vw, 1.6rem);
            line-height: 1.45;
            color: var(--cream);
            text-wrap: pretty;
        }

        .quote-foot {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.75rem 1rem;
            margin-top: 1.25rem;
        }

        .quote-by {
            font-size: 0.86rem;
            color: var(--cream-muted);
        }

        .quote-text,
        .quote-by,
        .quote-no {
            transition: opacity 0.24s ease, filter 0.24s ease, translate 0.24s ease;
        }

        .quote.is-swapping .quote-text,
        .quote.is-swapping .quote-by,
        .quote.is-swapping .quote-no {
            opacity: 0;
            filter: blur(4px);
            translate: 0 6px;
        }

        .quote-shuffle {
            margin-left: auto;
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            min-height: 44px;
            padding: 0 1rem;
            border: 1px solid rgba(212, 175, 55, 0.45);
            border-radius: 999px;
            background: rgba(212, 175, 55, 0.07);
            color: var(--gold-300);
            font-family: var(--f-mono);
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            cursor: pointer;
            transition: background-color 0.25s ease, color 0.25s ease, border-color 0.25s ease;
        }

        .quote-shuffle svg { transition: rotate 0.5s var(--ease-spring); }
        .quote-shuffle:hover { background: var(--gold-500); border-color: var(--gold-500); color: var(--ink-900); }
        .quote-shuffle:hover svg { rotate: 180deg; }
        .quote-shuffle:focus-visible { outline: 2px solid var(--gold-500); outline-offset: 3px; }

        /* Footer + اقرأ watermark */
        .folio-foot {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 1rem;
            font-family: var(--f-mono);
            font-size: 0.66rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--cream-muted);
        }

        .iqra-caption { margin: 0; display: flex; align-items: baseline; gap: 0.6rem; }

        .iqra-caption [lang="ar"] {
            font-family: var(--f-arabic);
            font-size: 1.45rem;
            letter-spacing: 0;
            text-transform: none;
            color: var(--gold-500);
        }

        .iqra-watermark {
            position: absolute;
            z-index: 0;
            right: -1.5rem;
            bottom: -5.5rem;
            font-family: var(--f-arabic);
            font-weight: 700;
            font-size: clamp(11rem, 22vw, 23rem);
            line-height: 1;
            color: transparent;
            -webkit-text-stroke: 1.5px rgba(212, 175, 55, 0.2);
            pointer-events: none;
            user-select: none;
            animation: breathe 9s ease-in-out infinite;
        }

        @keyframes breathe {
            0%, 100% { opacity: 0.75; }
            50%      { opacity: 1; }
        }

        /* =====================================================================
           RIGHT — THE DESK (catalog card)
           ===================================================================== */
        .desk {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2rem;
            padding: 3rem 2.5rem;
            background:
                radial-gradient(55% 45% at 50% 46%, rgba(212, 175, 55, 0.13), transparent 72%),
                radial-gradient(rgba(244, 239, 227, 0.07) 1px, transparent 1.5px) 0 0 / 22px 22px,
                var(--ink-950);
        }

        .spine {
            position: absolute;
            left: 1.1rem;
            top: 50%;
            translate: 0 -50%;
            writing-mode: vertical-rl;
            rotate: 180deg;
            font-family: var(--f-mono);
            font-size: 0.62rem;
            letter-spacing: 0.32em;
            text-transform: uppercase;
            color: rgba(244, 239, 227, 0.4);
            white-space: nowrap;
            pointer-events: none;
        }

        /* Stack: a second card peeks out behind, fans out on interaction */
        .card-stack {
            position: relative;
            width: min(100%, 440px);
            rotate: -1.5deg;
            transition: rotate 0.6s var(--ease-spring);
        }

        .card-stack::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 8px;
            background: var(--paper-back);
            rotate: 4deg;
            box-shadow: 0 24px 40px -22px rgba(0, 0, 0, 0.8);
            transition: rotate 0.6s var(--ease-spring);
        }

        .card-stack:hover,
        .card-stack:focus-within { rotate: 0deg; }

        .card-stack:hover::before,
        .card-stack:focus-within::before { rotate: 6deg; }

        .catalog-card {
            position: relative;
            padding: 2rem 2.25rem 1.5rem;
            border-radius: 8px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.55), transparent 40%),
                var(--paper);
            color: var(--ink-800);
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.7),
                0 30px 60px -24px rgba(0, 0, 0, 0.85),
                0 14px 28px -18px rgba(0, 0, 0, 0.55);
        }

        .card-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            padding-bottom: 0.85rem;
            margin-bottom: 1.5rem;
            border-bottom: 1.5px solid rgba(155, 44, 44, 0.55);
        }

        .call-no { display: flex; flex-direction: column; gap: 2px; font-family: var(--f-mono); }

        .call-no span {
            font-size: 0.62rem;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--paper-muted);
        }

        .call-no strong { font-size: 0.9rem; font-weight: 600; letter-spacing: 0.06em; color: var(--ink-800); }

        .stamp {
            position: absolute;
            top: -30px;
            right: -22px;
            width: 112px;
            height: 112px;
            color: var(--stamp);
            rotate: -14deg;
            opacity: 0.82;
            mix-blend-mode: multiply;
            pointer-events: none;
            animation: stamp-in 0.55s var(--ease-spring) 1s both;
        }

        @keyframes stamp-in {
            from { opacity: 0; scale: 1.9; }
            to   { opacity: 0.82; scale: 1; }
        }

        .card-title {
            margin: 0 0 0.45rem;
            font-family: var(--f-display);
            font-size: 2.15rem;
            font-weight: 600;
            line-height: 1.08;
            letter-spacing: -0.015em;
            color: var(--ink-800);
        }

        .card-title em { font-style: italic; font-weight: 500; color: var(--gold-700); }

        .card-sub {
            margin: 0 0 1.75rem;
            font-size: 0.9rem;
            line-height: 1.55;
            color: var(--paper-muted);
        }

        /* ── CORE FORM MARKUP (lib/contents/login.inc.php) ─────────────── */
        #loginForm form { margin: 0; }

        #loginForm .heading1,
        #loginForm .field-label {
            display: block;
            margin: 0 0 0.2rem;
            font-family: var(--f-mono);
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--paper-muted);
            transition: color 0.2s ease;
        }

        #loginForm div.login_input {
            position: relative;
            margin-bottom: 1.4rem;
        }

        #loginForm .field-label:has(+ div.login_input :focus) { color: var(--ink-800); }

        #loginForm input.login_input {
            display: block;
            width: 100%;
            height: 50px;
            padding: 0 0.3rem;
            font-family: var(--f-body);
            font-size: 1rem;           /* ≥16px prevents iOS zoom */
            font-weight: 500;
            color: var(--ink-800);
            border: 0;
            border-bottom: 1.5px solid rgba(13, 27, 61, 0.38);
            border-radius: 0;
            outline: none;
            /* Highlighter sweep on focus */
            background: linear-gradient(to top, rgba(212, 175, 55, 0.34) 0 44%, transparent 44%) no-repeat left bottom / 0% 100%;
            transition: background-size 0.45s var(--ease-out), border-color 0.2s ease, box-shadow 0.2s ease;
        }

        #loginForm div.login_input.has-toggle input.login_input { padding-right: 3.25rem; }

        #loginForm input.login_input::placeholder { color: rgba(13, 27, 61, 0.42); font-weight: 400; }
        #loginForm input.login_input:hover { border-bottom-color: rgba(13, 27, 61, 0.65); }

        #loginForm input.login_input:focus {
            background-size: 100% 100%;
            border-bottom-color: var(--ink-800);
            box-shadow: 0 1.5px 0 0 var(--ink-800);
        }

        #loginForm input.login_input:-webkit-autofill {
            -webkit-text-fill-color: var(--ink-800);
            -webkit-box-shadow: 0 0 0 100px var(--paper) inset;
            caret-color: var(--ink-800);
        }

        @media (forced-colors: active) {
            #loginForm input.login_input:focus { outline: 2px solid CanvasText; }
        }

        /* Password visibility toggle (injected) */
        .pw-toggle {
            position: absolute;
            right: 0;
            bottom: 3px;
            width: 44px;
            height: 44px;
            display: grid;
            place-items: center;
            border: 0;
            border-radius: 10px;
            background: transparent;
            color: var(--ink-800);
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .pw-toggle:hover { background: rgba(13, 27, 61, 0.07); }
        .pw-toggle:focus-visible { outline: 2px solid var(--gold-700); outline-offset: 1px; }
        .pw-toggle .ic-off { display: none; }
        .pw-toggle[aria-pressed="true"] .ic-on { display: none; }
        .pw-toggle[aria-pressed="true"] .ic-off { display: block; }

        /* Caps Lock hint (injected) */
        .caps-hint {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            margin: -0.95rem 0 1.1rem;
            font-size: 0.78rem;
            font-weight: 500;
            color: var(--stamp);
        }

        .caps-hint[hidden] { display: none; }

        .captchaAdmin { margin: 0 0 1.25rem; max-width: 100%; overflow-x: auto; }

        #loginForm .marginTop {
            display: flex;
            flex-direction: column;
            gap: 0.9rem;
            margin-top: 0.4rem;
        }

        #loginForm .remember { display: flex; align-items: center; gap: 0.6rem; }
        #loginForm .remember:empty { display: none; }

        #loginForm .remember input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            flex: none;
            width: 20px;
            height: 20px;
            margin: 0;
            display: grid;
            place-content: center;
            border: 1.5px solid rgba(13, 27, 61, 0.5);
            border-radius: 5px;
            background: var(--paper);
            cursor: pointer;
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }

        #loginForm .remember input[type="checkbox"]::before {
            content: '';
            width: 11px;
            height: 11px;
            background: var(--paper);
            clip-path: polygon(14% 44%, 0 59%, 40% 100%, 100% 22%, 86% 8%, 39% 69%);
            scale: 0;
            transition: scale 0.25s var(--ease-spring);
        }

        #loginForm .remember input[type="checkbox"]:checked { background: var(--ink-800); border-color: var(--ink-800); }
        #loginForm .remember input[type="checkbox"]:checked::before { scale: 1; }
        #loginForm .remember input[type="checkbox"]:focus-visible { outline: 2px solid var(--gold-700); outline-offset: 2px; }

        #loginForm .remember label {
            font-size: 0.88rem;
            color: var(--paper-muted);
            cursor: pointer;
            user-select: none;
        }

        /* Primary action */
        #loginForm .loginButton {
            width: 100%;
            height: 54px;
            padding: 0 3rem 0 1.25rem;
            border: 0;
            border-radius: 12px;
            font-family: var(--f-body);
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            text-align: left;
            color: var(--paper);
            background-color: var(--ink-800);
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23FBF6EA' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M5 12h14M13 6l6 6-6 6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1.25rem center;
            background-size: 20px;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08), 0 14px 26px -14px rgba(7, 15, 35, 0.8);
            cursor: pointer;
            transition: background-color 0.25s ease, color 0.25s ease, background-position 0.3s var(--ease-out), box-shadow 0.25s ease, translate 0.2s ease;
        }

        #loginForm .loginButton:hover {
            color: var(--ink-900);
            background-color: var(--gold-500);
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23070F23' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M5 12h14M13 6l6 6-6 6'/%3E%3C/svg%3E");
            background-position: right 1rem center;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.3), 0 16px 30px -14px rgba(212, 175, 55, 0.65);
            translate: 0 -1px;
        }

        #loginForm .loginButton:active { translate: 0 0; }
        #loginForm .loginButton:focus-visible { outline: 3px solid var(--gold-500); outline-offset: 3px; }

        #loginForm form.is-loading .loginButton {
            pointer-events: none;
            cursor: progress;
            background-image: none;
            animation: loading 1.1s ease-in-out infinite;
        }

        @keyframes loading {
            0%, 100% { opacity: 1; }
            50%      { opacity: 0.72; }
        }

        /* Secondary action */
        #loginForm .homeButton {
            align-self: center;
            min-height: 44px;
            padding: 0 0.75rem;
            border: 0;
            background: none;
            font-family: var(--f-body);
            font-size: 0.86rem;
            font-weight: 500;
            color: var(--paper-muted);
            text-decoration: underline;
            text-decoration-color: transparent;
            text-underline-offset: 4px;
            cursor: pointer;
            transition: color 0.2s ease, text-decoration-color 0.2s ease;
        }

        #loginForm .homeButton:hover { color: var(--ink-800); text-decoration-color: var(--gold-500); }
        #loginForm .homeButton:focus-visible { outline: 2px solid var(--gold-700); outline-offset: 2px; border-radius: 6px; }

        /* Alerts (template/alert.php) */
        #loginForm .alert {
            margin: 0 0 1.4rem;
            padding: 0.8rem 1rem;
            border-left: 3px solid var(--stamp);
            border-radius: 0 8px 8px 0;
            background: rgba(155, 44, 44, 0.08);
            color: #6B1F1F;
            font-size: 0.88rem;
            line-height: 1.5;
        }

        #loginForm .alert-info,
        #loginForm .alert-success { border-left-color: var(--ink-700); background: rgba(22, 43, 94, 0.08); color: var(--ink-800); }
        #loginForm .alert-warning { border-left-color: var(--gold-700); background: rgba(212, 175, 55, 0.14); color: #4D3B0B; }
        #loginForm .alert-heading:empty { display: none; }
        #loginForm .alert p { margin: 0; }

        /* Card footer w/ catalog punch hole */
        .card-foot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-top: 1.4rem;
            padding-top: 1rem;
            border-top: 1px dashed rgba(13, 27, 61, 0.22);
            font-family: var(--f-mono);
            font-size: 0.62rem;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--paper-muted);
        }

        .punch-hole {
            width: 18px;
            height: 18px;
            flex: none;
            border-radius: 50%;
            background: #CDBF9F;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.45);
        }

        .desk-note {
            margin: 0;
            text-align: center;
            font-family: var(--f-mono);
            font-size: 0.66rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: rgba(244, 239, 227, 0.6);
        }

        /* ── RESPONSIVE ──────────────────────────────────────────────────── */
        @media (max-width: 1180px) {
            .folio { padding-right: 2.5rem; }
            .edition { display: none; }
        }

        @media (max-width: 960px) {
            .login-shell { grid-template-columns: 1fr; }

            .folio {
                --margin-x: 1.4rem;
                padding: 1.5rem 1.5rem 2.25rem calc(var(--margin-x) + 1.25rem);
                border-right: 0;
                border-bottom: 1px solid var(--gold-line);
            }

            .folio-body { padding: 2rem 0 0.5rem; }
            .eyebrow { font-size: 0.66rem; }
            .folio-title { font-size: clamp(2rem, 7.5vw, 2.9rem); margin: 0.9rem 0 2.75rem; }
            .quote-text { font-size: 1.15rem; }
            .quote-mark { font-size: 5rem; top: -2.7rem; }
            .folio-foot { display: none; }
            .iqra-watermark { font-size: 10rem; right: -1rem; bottom: -3rem; }

            .desk { padding: 3rem 1.25rem 2.5rem; }
            .spine { display: none; }
            .card-stack { rotate: 0deg; }
            .card-stack::before { rotate: 2.5deg; }
        }

        @media (max-width: 440px) {
            .catalog-card { padding: 1.75rem 1.4rem 1.25rem; }
            .card-title { font-size: 1.8rem; }
            .stamp { width: 88px; height: 88px; top: -22px; right: -8px; }
            .quote-shuffle { margin-left: 0; }
            .card-foot .foot-right { display: none; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
            .card-stack { rotate: 0deg; }
        }
    </style>
</head>
<body>

    <main class="login-shell">

        <!-- ── LEFT: THE FOLIO ──────────────────────────────────────────── -->
        <section class="folio" aria-labelledby="folio-title">
            <header class="folio-top reveal" style="--d:.05s">
                <a class="brand" href="index.php" aria-label="Kembali ke beranda <?= $libraryName; ?>">
                    <?php if ($logoUrl): ?>
                        <img class="brand-logo" src="<?= $logoUrl; ?>" alt="" width="46" height="46">
                    <?php else: ?>
                        <span class="brand-logo brand-monogram" aria-hidden="true">IL</span>
                    <?php endif; ?>
                    <span class="brand-text">
                        <span class="brand-name"><?= $libraryName; ?></span>
                        <span class="brand-sub"><?= $librarySubname; ?></span>
                    </span>
                </a>
                <div class="edition">
                    <span class="edition-label">Edisi Harian</span>
                    <time datetime="<?= date('Y-m-d'); ?>"><?= $edition_date; ?></time>
                </div>
            </header>

            <div class="folio-body">
                <p class="eyebrow reveal" style="--d:.15s">
                    <span class="live-dot" aria-hidden="true"></span>
                    Ruang Pustakawan &middot; Sistem Otomasi
                </p>

                <h1 id="folio-title" class="folio-title reveal" style="--d:.25s">
                    Selamat datang kembali, <em>Pustakawan.</em>
                </h1>

                <!-- Hardcoded Array & Randomizer Motivational Quote -->
                <figure class="quote reveal" id="quoteFigure" style="--d:.4s">
                    <span class="quote-mark" aria-hidden="true">&ldquo;</span>
                    <div class="quote-meta">
                        <span>Kutipan Hari Ini</span>
                        <span class="quote-no">No. <span id="quoteNo"><?= str_pad((string) ($quote_index + 1), 2, '0', STR_PAD_LEFT); ?></span> / <?= str_pad((string) $quote_total, 2, '0', STR_PAD_LEFT); ?></span>
                    </div>
                    <blockquote aria-live="polite">
                        <p class="quote-text" id="quoteText"><?= htmlspecialchars($random_quote); ?></p>
                    </blockquote>
                    <figcaption class="quote-foot">
                        <span class="quote-by">&mdash; <span id="quoteBy">Anonim</span></span>
                        <button type="button" class="quote-shuffle" id="quoteShuffle">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a9 9 0 0 1-15.5 6.2L3 16"/><path d="M3 21v-5h5"/><path d="M3 12a9 9 0 0 1 15.5-6.2L21 8"/><path d="M21 3v5h-5"/></svg>
                            Kutipan lain
                        </button>
                    </figcaption>
                </figure>
            </div>

            <footer class="folio-foot reveal" style="--d:.55s">
                <p class="iqra-caption">
                    <span lang="ar" dir="rtl">اقْرَأْ</span>
                    <span>&ldquo;Bacalah&rdquo; &mdash; QS. Al-&lsquo;Alaq [96]: 1</span>
                </p>
                <span>&copy; <?= date('Y'); ?> &middot; SLiMS</span>
            </footer>

            <div class="iqra-watermark" aria-hidden="true" lang="ar" dir="rtl">اقرأ</div>
        </section>

        <!-- ── RIGHT: THE DESK (catalog card) ───────────────────────────── -->
        <section class="desk" aria-labelledby="card-title">
            <div class="spine" aria-hidden="true">Perpustakaan &middot; <?= $libraryName; ?> &middot; Katalog Staf</div>

            <div class="card-stack reveal" style="--d:.3s">
                <article class="catalog-card">
                    <svg class="stamp" viewBox="0 0 120 120" aria-hidden="true">
                        <defs>
                            <path id="stampRing" d="M60,60 m-43,0 a43,43 0 1,1 86,0 a43,43 0 1,1 -86,0"/>
                        </defs>
                        <circle cx="60" cy="60" r="56" fill="none" stroke="currentColor" stroke-width="2.5"/>
                        <circle cx="60" cy="60" r="32" fill="none" stroke="currentColor" stroke-width="1.5"/>
                        <text font-family="JetBrains Mono, monospace" font-size="9.5" font-weight="600" letter-spacing="2.1" fill="currentColor">
                            <textPath href="#stampRing">KHUSUS PUSTAKAWAN &#8226; STAFF ONLY &#8226;</textPath>
                        </text>
                        <g transform="translate(48 48)" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 4h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                            <path d="M22 4h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                        </g>
                    </svg>

                    <header class="card-head">
                        <div class="call-no">
                            <span>No. Panggil</span>
                            <strong>025.04 &middot; ADM</strong>
                        </div>
                    </header>

                    <h2 class="card-title" id="card-title">Masuk ke <em>Ruang Kerja</em></h2>
                    <p class="card-sub">Gunakan kredensial akun pustakawan Anda untuk mengelola koleksi &amp; layanan.</p>

                    <!-- Form rendered from lib/contents/login.inc.php -->
                    <div class="card-form">
                        <?= $main_content; ?>
                    </div>

                    <footer class="card-foot">
                        <span>Akses Terbatas</span>
                        <span class="punch-hole" aria-hidden="true"></span>
                        <span class="foot-right">Sistem Otomasi</span>
                    </footer>
                </article>
            </div>

            <p class="desk-note reveal" style="--d:.6s">Lupa sandi? Hubungi administrator sistem.</p>
        </section>

    </main>

    <script>
    (function () {
        'use strict';

        var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        var ICON_EYE = '<svg class="ic-on" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>';
        var ICON_EYE_OFF = '<svg class="ic-off" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10.7 5.1A10.4 10.4 0 0 1 12 5c6.5 0 10 7 10 7a17.6 17.6 0 0 1-2.2 3.2"/><path d="M6.6 6.6A17.4 17.4 0 0 0 2 12s3.5 7 10 7a9.7 9.7 0 0 0 5.4-1.6"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/><path d="M2 2l20 20"/></svg>';
        var ICON_ALERT = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/><path d="M12 9v4M12 17h.01"/></svg>';

        /* ── Progressive enhancement of the core login form ─────────────── */
        var form = document.querySelector('#loginForm form');
        if (form) {
            // 1. Turn .heading1 <div>s into real <label for> elements
            form.querySelectorAll('.heading1').forEach(function (heading, i) {
                var wrap = heading.nextElementSibling;
                var input = wrap && wrap.querySelector('input');
                if (!input) return;
                if (!input.id || document.querySelectorAll('#' + input.id).length > 1) {
                    input.id = 'iqra-field-' + i;
                }
                var label = document.createElement('label');
                label.className = 'heading1 field-label';
                label.htmlFor = input.id;
                label.textContent = heading.textContent.trim();
                heading.replaceWith(label);
            });

            // 2. Password-manager friendly attributes (WCAG 2.2 accessible authentication)
            var user = form.querySelector('input[name="userName"]');
            if (user) {
                user.setAttribute('autocomplete', 'username');
                user.setAttribute('autocapitalize', 'none');
                user.setAttribute('spellcheck', 'false');
                user.placeholder = 'mis. pustakawan01';
            }

            var pass = form.querySelector('input[name="passWord"]');
            if (pass) {
                pass.setAttribute('autocomplete', 'current-password');
                pass.placeholder = '••••••••';
            }

            form.querySelectorAll('input[name="newPasswd"], input[name="newPasswd2"]').forEach(function (el) {
                el.setAttribute('autocomplete', 'new-password');
            });

            // 3. Show / hide password toggle + Caps Lock hint
            form.querySelectorAll('input[type="password"]').forEach(function (input, i) {
                if (!input.id) input.id = 'iqra-pw-' + i;
                var wrap = input.parentElement;
                wrap.classList.add('has-toggle');

                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'pw-toggle';
                btn.setAttribute('aria-label', 'Tampilkan kata sandi');
                btn.setAttribute('aria-pressed', 'false');
                btn.setAttribute('aria-controls', input.id);
                btn.innerHTML = ICON_EYE + ICON_EYE_OFF;
                btn.addEventListener('click', function () {
                    var show = input.type === 'password';
                    input.type = show ? 'text' : 'password';
                    btn.setAttribute('aria-pressed', String(show));
                    btn.setAttribute('aria-label', show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
                    input.focus({ preventScroll: true });
                });
                wrap.appendChild(btn);

                var hint = document.createElement('p');
                hint.className = 'caps-hint';
                hint.setAttribute('role', 'status');
                hint.hidden = true;
                hint.innerHTML = ICON_ALERT + '<span>Caps Lock sedang aktif</span>';
                wrap.insertAdjacentElement('afterend', hint);

                var checkCaps = function (e) {
                    if (e.getModifierState) hint.hidden = !e.getModifierState('CapsLock');
                };
                input.addEventListener('keydown', checkCaps);
                input.addEventListener('keyup', checkCaps);
                input.addEventListener('blur', function () { hint.hidden = true; });
            });

            // 4. Friendlier secondary action label
            form.querySelectorAll('.homeButton').forEach(function (b) {
                if (b.tagName === 'INPUT') b.value = '← Kembali ke beranda';
            });

            // 5. Loading state (button stays enabled so "logMeIn" is still posted)
            form.addEventListener('submit', function () {
                if (typeof form.checkValidity === 'function' && !form.checkValidity()) return;
                form.classList.add('is-loading');
                var submit = form.querySelector('.loginButton');
                if (submit) {
                    submit.value = 'Memverifikasi…';
                    submit.setAttribute('aria-busy', 'true');
                }
            });
        }

        /* ── Quote shuffle (keeps the PHP randomizer as the initial pick) ── */
        var QUOTES = <?= json_encode(array_values($quotes_motivasi), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        var idx = <?= (int) $quote_index; ?>;
        var fig = document.getElementById('quoteFigure');
        var txt = document.getElementById('quoteText');
        var num = document.getElementById('quoteNo');
        var shuffle = document.getElementById('quoteShuffle');

        if (fig && txt && shuffle && QUOTES.length > 1) {
            shuffle.addEventListener('click', function () {
                var next;
                do { next = Math.floor(Math.random() * QUOTES.length); } while (next === idx);
                idx = next;
                fig.classList.add('is-swapping');
                window.setTimeout(function () {
                    txt.textContent = QUOTES[idx];
                    if (num) num.textContent = String(idx + 1).padStart(2, '0');
                    fig.classList.remove('is-swapping');
                }, reduceMotion ? 0 : 240);
            });
        } else if (shuffle) {
            shuffle.hidden = true;
        }
    })();
    </script>
</body>
</html>
<?php
exit;