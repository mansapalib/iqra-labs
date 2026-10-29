<?php
/**
 * =========================================================================
 * MANSAPA Iqra' Labs — _navbar.php  [Minimalist Institutional Portal]
 * File: template/iqra-labs/parts/_navbar.php
 *
 * Semantic HTML5 Navigation Bar:
 * - Palette: Dark Slate (#0F172A / #0B1120) with warm gold/amber accents (#EAB308 / #D4AF37)
 * - Brand: "MANSAPA Iqra' Labs Pangandaran" with dynamic logo tags preserved
 * - Nav: Beranda, Katalog, Kabar & Agenda, Tentang Kami, Bantuan
 * - CTA: High-contrast pill "Masuk / Daftar" -> dynamically swapped to "Dashboard Pemustaka" / member name if authenticated
 * - Locale: SLiMS multi-language dropdown switcher preserved
 *
 * Core SLiMS PHP hooks & variables strictly preserved:
 * $is_login, $sysconf, $imagesDisk, $available_languages, $_SESSION, $member_image_path
 * =========================================================================
 */

// Initialize variables for static analysis & template isolation
$imagesDisk        = $imagesDisk ?? (\class_exists('\SLiMS\Filesystems\Storage') ? \SLiMS\Filesystems\Storage::images() : null);
$member_image_path = $member_image_path ?? '';

// Robust member authentication check
$isMemberLoggedIn = (!empty($is_login)) ||
                    (!empty($_SESSION['m_logged_in'])) ||
                    (isset($_SESSION['mid']) && !empty($_SESSION['m_name'])) ||
                    (class_exists('utility') && method_exists('utility', 'isMemberLogin') && utility::isMemberLogin());

$basketCount = count($_SESSION['m_mark_biblio'] ?? []);

// Navigation routing & active detection
$currentPage = $_GET['p'] ?? '';
$isSearch    = isset($_GET['search']);

$navLinks = [
    'home' => [
        'label'  => __('Beranda'),
        'url'    => 'index.php',
        'svg'    => '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="portal-nav-icon" aria-hidden="true"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>',
        'active' => (empty($currentPage) && !$isSearch),
    ],
    'katalog' => [
        'label'  => __('Katalog'),
        'url'    => 'index.php?p=opac',
        'svg'    => '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="portal-nav-icon" aria-hidden="true"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/><path d="M14 2v6l2.5-1.5L19 8V2"/></svg>',
        'active' => ($currentPage === 'opac' || $isSearch || $currentPage === 'show_detail'),
    ],
    'news' => [
        'label'  => __('Kabar & Agenda'),
        'url'    => 'index.php?p=news',
        'svg'    => '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="portal-nav-icon" aria-hidden="true"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/></svg>',
        'active' => ($currentPage === 'news'),
    ],
    'about' => [
        'label'    => __('Tentang Kami'),
        'url'      => '#',
        'svg'      => '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="portal-nav-icon" aria-hidden="true"><line x1="3" y1="21" x2="21" y2="21"/><line x1="6" y1="18" x2="6" y2="11"/><line x1="10" y1="18" x2="10" y2="11"/><line x1="14" y1="18" x2="14" y2="11"/><line x1="18" y1="18" x2="18" y2="11"/><polygon points="12 2 20 7 4 7"/></svg>',
        'active'   => in_array($currentPage, ['profil', 'tata-tertib', 'librarian', 'libinfo']),
        'dropdown' => [
            [
                'label'  => __('Profil & Sejarah'),
                'url'    => 'index.php?p=profil',
                'active' => ($currentPage === 'profil'),
                'svg'    => '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2 text-gold flex-shrink-0" style="vertical-align: -2px;" aria-hidden="true"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/><path d="M14 2v6l2.5-1.5L19 8V2"/></svg>',
            ],
            [
                'label'  => __('Jam Layanan & Tata Tertib'),
                'url'    => 'index.php?p=tata-tertib',
                'active' => ($currentPage === 'tata-tertib'),
                'svg'    => '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2 text-gold flex-shrink-0" style="vertical-align: -2px;" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
            ],
            [
                'label'  => __('Pustakawan'),
                'url'    => 'index.php?p=librarian',
                'active' => ($currentPage === 'librarian'),
                'svg'    => '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2 text-gold flex-shrink-0" style="vertical-align: -2px;" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
            ],
        ],
    ],
    'help' => [
        'label'  => __('FAQ'),
        'url'    => 'index.php?p=faq',
        'svg'    => '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="portal-nav-icon" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
        'active' => ($currentPage === 'help' || $currentPage === 'faq'),
    ],
];

// Brand Identity resolution
$brandTitle = (!empty($sysconf['library_name']) && $sysconf['library_name'] !== 'Senayan') 
    ? $sysconf['library_name'] 
    : "MANSAPA Iqra' Labs";

$brandSub = (!empty($sysconf['library_subname']) && strtolower($sysconf['library_subname']) !== 'senayan')
    ? $sysconf['library_subname']
    : 'Pangandaran';

// Ensure "Pangandaran" is clearly rendered in institutional identity
if (stripos($brandTitle, 'Pangandaran') === false && stripos($brandSub, 'Pangandaran') === false) {
    $brandSub = 'Pangandaran' . (!empty($brandSub) && $brandSub !== 'Pangandaran' ? ' &bull; ' . $brandSub : '');
}

// Locale Switcher resolution: strictly Indonesian, English, and Arabic
$allowedLanguages = [
    'id_ID' => ['label' => 'Indonesian', 'flag' => 'id', 'short' => 'ID'],
    'en_US' => ['label' => 'English',    'flag' => 'us', 'short' => 'EN'],
    'ar_SA' => ['label' => 'Arabic',     'flag' => 'sa', 'short' => 'AR'],
];

$availableLanguages = $available_languages ?? ($localisation ?? null ? $localisation->getLanguages() : []);
$currentLangCode    = $_COOKIE['select_lang'] ?? ($sysconf['default_lang'] ?? 'id_ID');

// Fallback to default if current cookie is not in the allowed three
if (!isset($allowedLanguages[$currentLangCode])) {
    $currentLangCode = isset($sysconf['default_lang']) && isset($allowedLanguages[$sysconf['default_lang']]) 
        ? $sysconf['default_lang'] 
        : 'id_ID';
}

$currentLangDisplay = $allowedLanguages[$currentLangCode]['short'] ?? 'ID';
$currentFlagCode    = $allowedLanguages[$currentLangCode]['flag'] ?? 'id';

// Index language names from SLiMS localisation if provided
$slimsLangNames = [];
if (!empty($availableLanguages) && is_array($availableLanguages)) {
    foreach ($availableLanguages as $entry) {
        if (!empty($entry[0]) && !empty($entry[1])) {
            $slimsLangNames[$entry[0]] = $entry[1];
        }
    }
}

$langDropdownItems = '';
foreach ($allowedLanguages as $code => $info) {
    $isCurrent = ($code === $currentLangCode);
    $name      = $slimsLangNames[$code] ?? $info['label'];
    $flagCode  = $info['flag'];

    $langDropdownItems .= sprintf(
        '<a class="dropdown-item portal-dropdown-item%s" href="index.php?select_lang=%s"><span class="flag-icon flag-icon-%s me-2" style="border-radius: 2px;"></span>%s</a>',
        $isCurrent ? ' active' : '',
        htmlspecialchars($code),
        htmlspecialchars($flagCode),
        htmlspecialchars($name)
    );
}
?>

<header class="portal-header sticky-top" id="portalHeader" role="banner">
    <nav class="navbar navbar-expand-lg portal-navbar" id="mainNavbar" aria-label="<?= __('Navigasi Utama'); ?>">
        <div class="container iqra-container d-flex align-items-center justify-content-between">

            <!-- ── BRAND IDENTITY (LEFT) ─────────────────────────────────── -->
            <a class="navbar-brand portal-brand d-flex align-items-center" href="index.php" aria-label="MANSAPA Iqra' Labs Pangandaran — Beranda">
                <div class="portal-brand-logo-wrap me-2">
                    <?php
                    // Priority 1: Logo uploaded via SLiMS Admin -> System -> System Configuration -> Logo
                    if (
                        isset($sysconf['logo_image']) &&
                        $sysconf['logo_image'] !== '' &&
                        isset($imagesDisk) && $imagesDisk &&
                        $imagesDisk->isExists($logoPath = 'default/' . $sysconf['logo_image'])
                    ) {
                        $logoUrl = SWB . 'lib/minigalnano/createthumb.php?filename=images/' . $logoPath . '&width=350';
                        echo '<img class="portal-logo-img" src="' . $logoUrl . '" alt="Logo ' . htmlspecialchars($sysconf['library_name'] ?? 'MANSAPA') . '">';
                    }
                    // Priority 2: Custom logo.png in theme assets folder
                    elseif (file_exists(__DIR__ . '/../assets/images/logo.png')) {
                        echo '<img class="portal-logo-img" src="' . assets('images/logo.png') . '" alt="Logo MANSAPA Iqra\' Labs Pangandaran">';
                    }
                    // Priority 3: Dynamic fallback avatar
                    else {
                        $libName = urlencode($sysconf['library_name'] ?? 'MANSAPA Iqra');
                        echo '<img class="portal-logo-img portal-logo-avatar" src="https://ui-avatars.com/api/?name=' . $libName . '&background=0F172A&color=EAB308&font-size=0.33&bold=true&size=80" alt="Logo ' . htmlspecialchars($sysconf['library_name'] ?? 'MANSAPA') . '">';
                    }
                    ?>
                </div>
                <div class="portal-brand-text">
                    <span class="portal-brand-title d-block"><?= htmlspecialchars($brandTitle); ?></span>
                    <span class="portal-brand-subtitle d-block"><?= $brandSub; ?></span>
                </div>
            </a>

            <!-- ── MOBILE HAMBURGER TOGGLE ───────────────────────────────── -->
            <button class="navbar-toggler portal-toggler border-0"
                    type="button"
                    data-toggle="collapse"
                    data-target="#portalNavCollapse"
                    aria-controls="portalNavCollapse"
                    aria-expanded="false"
                    aria-label="<?= __('Buka/tutup navigasi'); ?>">
                <span class="portal-burger-box" aria-hidden="true">
                    <span class="portal-burger-bar"></span>
                    <span class="portal-burger-bar"></span>
                    <span class="portal-burger-bar"></span>
                </span>
            </button>

            <!-- ── COLLAPSIBLE NAVIGATION ────────────────────────────────── -->
            <div class="collapse navbar-collapse" id="portalNavCollapse">

                <!-- Main Navigation (Center/Right) -->
                <ul class="navbar-nav portal-nav-links mx-auto">
                    <?php foreach ($navLinks as $key => $item): ?>
                    <?php if (!empty($item['dropdown'])): ?>
                    <li class="nav-item dropdown <?= $item['active'] ? 'active' : ''; ?>">
                        <a class="nav-link portal-nav-link dropdown-toggle d-inline-flex align-items-center"
                           href="<?= $item['url']; ?>"
                           id="navDropdown<?= ucfirst($key); ?>"
                           role="button"
                           data-toggle="dropdown"
                           aria-haspopup="true"
                           aria-expanded="false"
                           <?= $item['active'] ? 'aria-current="page"' : ''; ?>>
                            <?= $item['svg']; ?>
                            <span><?= $item['label']; ?></span>
                        </a>
                        <div class="dropdown-menu portal-dropdown-menu portal-nav-dropdown" aria-labelledby="navDropdown<?= ucfirst($key); ?>">
                            <?php foreach ($item['dropdown'] as $subItem): ?>
                            <a class="dropdown-item portal-dropdown-item <?= !empty($subItem['active']) ? 'active' : ''; ?>"
                               href="<?= $subItem['url']; ?>"
                               <?= !empty($subItem['active']) ? 'aria-current="page"' : ''; ?>>
                                <?= $subItem['svg'] ?? ''; ?>
                                <span><?= $subItem['label']; ?></span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </li>
                    <?php else: ?>
                    <li class="nav-item <?= $item['active'] ? 'active' : ''; ?>">
                        <a class="nav-link portal-nav-link d-inline-flex align-items-center"
                           href="<?= $item['url']; ?>"
                           <?= $item['active'] ? 'aria-current="page"' : ''; ?>>
                            <?= $item['svg']; ?>
                            <span><?= $item['label']; ?></span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </ul>

                <!-- Right Action Cluster: CTA + Locale Switcher -->
                <ul class="navbar-nav portal-nav-actions align-items-lg-center">

                    <?php if ($isMemberLoggedIn): 
                        $memberName = !empty($_SESSION['m_name']) ? htmlspecialchars($_SESSION['m_name']) : __('Dashboard Pemustaka');
                        $memberActive = ($currentPage === 'member') ? 'active' : '';
                    ?>
                        <!-- Basket Quick Link (Item marked count) -->
                        <li class="nav-item portal-basket-item <?= $memberActive; ?>">
                            <a class="nav-link portal-basket-btn" href="index.php?p=member&sec=title_basket" title="<?= __('Keranjang Koleksi'); ?>" aria-label="<?= __('Keranjang Koleksi'); ?>">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 11-1 9"/><path d="m19 11-4-7"/><path d="M2 11h20"/><path d="m3.5 11 1.6 7.4a2 2 0 0 0 2 1.6h9.8c.9 0 1.8-.7 2-1.6l1.6-7.4"/><path d="m9 11 1 9"/><path d="M5 11l4-7"/></svg>
                                <?php if ($basketCount > 0): ?>
                                    <span class="portal-basket-badge" id="count-basket" aria-label="<?= $basketCount; ?> koleksi"><?= $basketCount; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>

                        <!-- Authenticated Member CTA: Dynamically swapped pill displaying Member Name / Dashboard Pemustaka -->
                        <li class="nav-item dropdown portal-member-item <?= $memberActive; ?>">
                            <a class="portal-cta-pill portal-cta-member dropdown-toggle d-inline-flex align-items-center"
                               href="index.php?p=member"
                               id="memberPortalMenuBtn"
                               role="button"
                               data-toggle="dropdown"
                               aria-haspopup="true"
                               aria-expanded="false"
                               title="<?= __('Dashboard Pemustaka'); ?>"
                               aria-label="<?= __('Menu Pemustaka: ') . $memberName; ?>">
                                <?php if (!empty($member_image_path) && file_exists($member_image_path)): ?>
                                    <img src="<?= $member_image_path; ?>" class="portal-member-avatar" alt="Foto Profil">
                                <?php else: ?>
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="portal-member-icon" aria-hidden="true"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="10" r="3"/><path d="M7 20.662V19a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v1.662"/></svg>
                                <?php endif; ?>
                                <span class="portal-member-name text-truncate"><?= $memberName; ?></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right portal-dropdown-menu" aria-labelledby="memberPortalMenuBtn">
                                <div class="portal-dropdown-header">
                                    <span class="portal-dropdown-name d-block"><?= htmlspecialchars($_SESSION['m_name'] ?? 'Anggota Perpustakaan'); ?></span>
                                    <span class="portal-dropdown-meta d-block"><?= htmlspecialchars($_SESSION['m_email'] ?? ($_SESSION['mid'] ?? 'ID: ' . ($_SESSION['m_member_id'] ?? 'Pemustaka'))); ?></span>
                                </div>
                                <div class="dropdown-divider portal-dropdown-divider"></div>
                                <a class="dropdown-item portal-dropdown-item" href="index.php?p=member">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2 text-gold" style="vertical-align: -2px;" aria-hidden="true"><path d="m12 14 4-4"/><path d="M3.34 19a10 10 0 1 1 17.32 0"/></svg> <?= __('Dashboard Pemustaka'); ?>
                                </a>
                                <a class="dropdown-item portal-dropdown-item" href="index.php?p=member&sec=bookmark">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2 text-gold" style="vertical-align: -2px;" aria-hidden="true"><path d="m19 21-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16z"/></svg> <?= __('Koleksi Tersimpan'); ?>
                                </a>
                                <a class="dropdown-item portal-dropdown-item" href="index.php?p=member&sec=title_basket">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2 text-gold" style="vertical-align: -2px;" aria-hidden="true"><path d="m15 11-1 9"/><path d="m19 11-4-7"/><path d="M2 11h20"/><path d="m3.5 11 1.6 7.4a2 2 0 0 0 2 1.6h9.8c.9 0 1.8-.7 2-1.6l1.6-7.4"/><path d="m9 11 1 9"/><path d="M5 11l4-7"/></svg> <?= __('Keranjang Koleksi'); ?>
                                </a>
                                <div class="dropdown-divider portal-dropdown-divider"></div>
                                <a class="dropdown-item portal-dropdown-item text-danger" href="index.php?p=member&logout=1">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2" style="vertical-align: -2px;" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg> <?= __('Keluar'); ?>
                                </a>
                            </div>
                        </li>

                    <?php else: ?>
                        <!-- Guest Call-to-Action: High-contrast warm gold/amber pill button -->
                        <li class="nav-item portal-cta-item">
                            <a class="portal-cta-pill portal-cta-guest d-inline-flex align-items-center"
                               href="index.php?p=member"
                               role="button"
                               aria-label="<?= __('Masuk atau Daftar sebagai Pemustaka'); ?>">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="portal-cta-icon" style="margin-right: 5px;" aria-hidden="true"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                                <span><?= __('Masuk / Daftar'); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- Locale Switcher Dropdown (Far Right) -->
                    <?php if (!empty($langDropdownItems)): ?>
                    <li class="nav-item dropdown portal-lang-item">
                        <a class="nav-link dropdown-toggle portal-lang-btn d-inline-flex align-items-center"
                           href="#"
                           id="portalLangDropdown"
                           role="button"
                           data-toggle="dropdown"
                           aria-haspopup="true"
                           aria-expanded="false"
                           title="<?= __('Pilih Bahasa'); ?>">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="portal-lang-globe" style="margin-right: 5px;" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                            <span class="portal-lang-code font-weight-bold"><?= $currentLangDisplay; ?></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right portal-dropdown-menu" aria-labelledby="portalLangDropdown">
                            <h6 class="dropdown-header portal-dropdown-header-sm text-uppercase"><?= __('Pilih Bahasa'); ?></h6>
                            <?= $langDropdownItems; ?>
                        </div>
                    </li>
                    <?php endif; ?>

                </ul><!-- /.portal-nav-actions -->

            </div><!-- /#portalNavCollapse -->

        </div><!-- /.container -->
    </nav><!-- /.portal-navbar -->
</header><!-- /#portalHeader -->

<script>
(function() {
    // Lightweight sync for mobile toggler aria-expanded attribute
    function initPortalNav() {
        var toggler = document.querySelector('.portal-toggler');
        var collapse = document.getElementById('portalNavCollapse');
        if (!toggler || !collapse) return;

        if (window.jQuery) {
            window.jQuery(collapse).on('show.bs.collapse', function() {
                toggler.setAttribute('aria-expanded', 'true');
            }).on('hide.bs.collapse', function() {
                toggler.setAttribute('aria-expanded', 'false');
            });
        }
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPortalNav);
    } else {
        initPortalNav();
    }
})();
</script>
