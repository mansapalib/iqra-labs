<?php
/**
 * =========================================================================
 * MANSAPA Iqra' Labs — header.php  [FINAL PRODUCTION v2]
 * Place: template/mansapa iqra labs/parts/header.php
 *
 * Opens <!DOCTYPE html> through <body>.
 * Loads Google Fonts, Font Awesome 6.4, Bootstrap, and custom stylesheet.
 * All SLiMS PHP hooks are strictly preserved.
 * =========================================================================
 */

// XSS-safe REQUEST_URI for meta tags
$request_uri = urlencode(strip_tags(urldecode($_SERVER['REQUEST_URI'])));

// Favicon resolution — falls back to SLiMS default
$icon = SWB . 'webicon.ico';
if (
    isset($sysconf['webicon']) &&
    !empty($sysconf['webicon']) &&
    isset($imagesDisk) && $imagesDisk &&
    $imagesDisk->isExists($path = 'default/' . $sysconf['webicon'])
) {
    $icon = SWB . 'lib/minigalnano/createthumb.php?filename=images/' . $path . '&width=130';
}
?>
<!DOCTYPE html>
<html lang="<?= substr($sysconf['default_lang'] ?? 'id', 0, 2); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Page Title -->
    <title><?= htmlspecialchars($page_title ?? ''); ?> &mdash; MANSAPA Iqra' Labs</title>

    <!-- SLiMS plugin meta injection hook -->
    <?= $metadata ?? ''; ?>

    <!-- SEO Meta -->
    <?php if (isset($_GET['p']) && $_GET['p'] === 'show_detail'): ?>
        <meta name="description" content="<?= substr(strip_tags($notes ?? ''), 0, 155); ?>">
        <meta name="keywords"    content="<?= htmlspecialchars($subject ?? ''); ?>">
        <meta property="og:type"  content="book">
        <meta property="og:image" content="//<?= $_SERVER['SERVER_NAME'] . SWB . ($image_src ?? ''); ?>">
    <?php else: ?>
        <meta name="description" content="MANSAPA Iqra' Labs &mdash; Singgah, Baca, Cerita. Laboratorium Literasi dan Gagasan.">
        <meta name="keywords"    content="perpustakaan, MANSAPA, literasi, katalog, <?= htmlspecialchars($sysconf['library_subname'] ?? ''); ?>">
        <meta property="og:type"  content="website">
    <?php endif; ?>

    <!-- Open Graph common -->
    <meta name="theme-color"      content="#0D1B3D">
    <meta name="generator"        content="<?= SENAYAN_VERSION ?? 'SLiMS 9 Bulian'; ?>">
    <meta property="og:locale"    content="<?= str_replace('-', '_', $sysconf['default_lang'] ?? 'id_ID'); ?>">
    <meta property="og:title"     content="<?= htmlspecialchars($page_title ?? ''); ?> &mdash; MANSAPA Iqra' Labs">
    <meta property="og:site_name" content="<?= htmlspecialchars($sysconf['library_name'] ?? "MANSAPA Iqra' Labs"); ?>">
    <meta property="og:url"       content="//<?= $_SERVER['SERVER_NAME'] . $request_uri; ?>">
    <meta property="og:description" content="Laboratorium Literasi dan Gagasan MANSAPA. Singgah, Baca, Cerita.">

    <!-- Favicon -->
    <link rel="shortcut icon" href="<?= $icon; ?>" type="image/x-icon">

    <!-- ================================================================
         GOOGLE FONTS — preconnect for performance
         Poppins        : Headings, nav, CTAs, hero tagline
         Inter          : Body copy, metadata, UI labels
         Cormorant Garamond : Footer quote, accent text
    ================================================================ -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,400;1,600&family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800;900&display=swap">

    <!-- ================================================================
         FONT AWESOME 6.4 & Local Fallback (for strict CSP compliance)
    ================================================================ -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
          crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="<?= assets('plugin/font-awesome/css/fontawesome-all.min.css'); ?>">

    <!-- ================================================================
         SLiMS CORE FRAMEWORK CSS (load order critical)
    ================================================================ -->
    <link rel="stylesheet" href="<?= assets('css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?= assets('css/tailwind.min.css'); ?>">
    <link rel="stylesheet" href="<?= assets('css/flag-icon.min.css'); ?>">
    <link rel="stylesheet" href="<?= JWB; ?>toastr/toastr.min.css">
    <link rel="stylesheet" href="<?= JWB; ?>colorbox/colorbox.css">
    <link rel="stylesheet" href="<?= JWB; ?>ion.rangeSlider/css/ion.rangeSlider.min.css">

    <!-- ================================================================
         MANSAPA IQRA' LABS — Brand Stylesheet (loaded LAST for priority)
         Cache-busted hourly; change date('YmdH') to date('Ymd') on prod.
    ================================================================ -->
    <link rel="stylesheet" href="<?= assets('css/style.css?v=' . (file_exists(__DIR__ . '/../assets/css/style.css') ? filemtime(__DIR__ . '/../assets/css/style.css') : date('YmdHis'))); ?>">
    <link rel="stylesheet" href="<?= assets('css/custom-mansapa.css?v=' . (file_exists(__DIR__ . '/../assets/css/custom-mansapa.css') ? filemtime(__DIR__ . '/../assets/css/custom-mansapa.css') : date('YmdHis'))); ?>">

    <!-- ================================================================
         JAVASCRIPT — Head-loaded because SLiMS Vue elements need jQuery
         and Vue before DOMContentLoaded fires.
    ================================================================ -->
    <script src="<?= assets('js/vue.min.js'); ?>"></script>
    <script src="<?= assets('js/jquery.min.js'); ?>"></script>
    <script src="<?= assets('js/bootstrap.bundle.min.js'); ?>"></script>
    <script src="<?= JWB; ?>toastr/toastr.min.js"></script>
    <script src="<?= JWB; ?>colorbox/jquery.colorbox-min.js"></script>
    <script src="<?= JWB . v('gui.js'); ?>"></script>
    <script src="<?= JWB; ?>ion.rangeSlider/js/ion.rangeSlider.min.js"></script>

    <!-- SLiMS inline JS hook — do NOT remove (plugins inject here) -->
    <?php if (isset($js)) echo $js; ?>

</head>
<body id="iqra-body">
<!-- header.php END — _navbar.php | content partial | footer.php follow -->
