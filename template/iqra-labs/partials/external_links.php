<?php
/**
 * =========================================================================
 * MANSAPA Iqra' Labs — external_links.php [Reference Portals Hub]
 * Place: template/iqra-labs/partials/external_links.php
 *
 * SECTION: "Tautan Eksternal" (Digital Reference & National Literacy Portals)
 * Minimalist showcase displaying official national library portal logos.
 * Palette: Clean White (#FFFFFF), Deep Navy (#0F172A), Teal Accent (#00A2B9).
 * =========================================================================
 */

$external_portals = [
    [
        'title' => 'Indonesia OneSearch',
        'url'   => 'https://onesearch.id',
        'image' => 'onesearch.jpg',
    ],
    [
        'title' => 'Perpustakaan Kemendikbud',
        'url'   => 'https://perpustakaan.kemendikdasmen.go.id',
        'image' => 'perpusdikbud.jpg',
    ],
    [
        'title' => 'Perpustakaan Nasional RI',
        'url'   => 'https://www.perpusnas.go.id',
        'image' => 'perpusnas.jpg',
    ],
    [
        'title' => 'Cendikia Kemenag',
        'url'   => 'https://cendikia.kemenag.go.id',
        'image' => 'cendikia.jpg',
    ],
    [
        'title' => 'BintangPusnas Edu',
        'url'   => 'https://bintangpusnas.perpusnas.go.id/konten/',
        'image' => 'bintangpusnas.jpg',
    ],
    [
        'title' => 'e-Resources Perpusnas',
        'url'   => 'https://e-resources.perpusnas.go.id',
        'image' => 'eresources.jpg',
    ],
];
?>

<!-- ================================================================
     SECTION 5: TAUTAN EKSTERNAL (Reference Portals Hub)
     Design Language: Swiss Modernism 2.0 (Deep Navy & Luxurious Gold)
     Layout: Clean, Simple & Practical (6 cards single row desktop, 3 tablet, 2 mobile)
================================================================ -->
<section class="section-external-links py-section scroll-reveal" id="section-external-links" aria-labelledby="external-links-heading">

    <!-- Ambient Interactive Background Canvas (Seamless with Hero & Curated Books) -->
    <div class="portal-bg-canvas" aria-hidden="true">
        <!-- Mathematical Dot Matrix Grid Overlay -->
        <div class="portal-bg-grid"></div>
        <!-- Luminous Ambient Mesh Glow Orbs -->
        <div class="portal-bg-orb portal-orb-gold"></div>
        <div class="portal-bg-orb portal-orb-navy"></div>
    </div>

    <div class="container iqra-container">

        <!-- ── Section Header matching Hero & Curated Books Hierarchy ───── -->
        <header class="external-links-header text-center animate-fade-down">

            <!-- Main Headline with Gold Gradient Highlight (Single Line Display) -->
            <h2 class="portal-section-title portal-title-nowrap animate-fade-down delay-1" id="external-links-heading">Portal &amp; Tautan <span class="portal-title-highlight">Perpustakaan Digital</span></h2>

            <!-- Subtitle Narrative -->
            <p class="portal-section-sub mb-0 animate-fade-down delay-2">
                Gerbang akses cepat ke katalog bersama, repositori riset ilmiah, dan sumber daya literasi digital nasional.
            </p>
        </header>

        <!-- ── Portal Grid (Exact same layout: 6 columns desktop, 3 tablet, 2 mobile) ── -->
        <div class="portal-grid animate-fade-up delay-3" role="list">
            <?php foreach ($external_portals as $portal): 
                $img_path = function_exists('assets') ? assets('images/' . $portal['image']) : 'template/iqra-labs/assets/images/' . $portal['image'];
            ?>
            <a href="<?= htmlspecialchars($portal['url']); ?>"
               target="_blank"
               rel="noopener noreferrer"
               class="portal-card"
               role="listitem"
               title="<?= htmlspecialchars($portal['title']); ?>"
               aria-label="<?= htmlspecialchars($portal['title']); ?> (Buka portal di tab baru)">
                
                <!-- Discreet External Link Arrow Micro-Indicator -->
                <span class="portal-card-corner-arrow" aria-hidden="true">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="7" y1="17" x2="17" y2="7"></line>
                        <polyline points="7 7 17 7 17 17"></polyline>
                    </svg>
                </span>

                <div class="portal-logo-wrap">
                    <img src="<?= htmlspecialchars($img_path); ?>"
                         alt="Logo <?= htmlspecialchars($portal['title']); ?>"
                         class="portal-logo-img"
                         loading="lazy"
                         width="160"
                         height="56"
                         onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='inline-flex';">
                    <span class="portal-logo-fallback" style="display: none;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px; vertical-align: -1px;"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                        <?= htmlspecialchars($portal['title']); ?>
                    </span>
                </div>
            </a>
            <?php endforeach; ?>
        </div><!-- /.portal-grid -->

    </div><!-- /.container -->
</section><!-- /.section-external-links -->
