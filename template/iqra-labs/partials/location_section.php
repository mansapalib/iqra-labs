<?php
/**
 * =========================================================================
 * MANSAPA Iqra' Labs — location_section.php [Location & Service Hours Hub]
 * Place: template/iqra-labs/partials/location_section.php
 *
 * SECTION: "Lokasi & Jam Operasional"
 * Asymmetrical 2-Column Responsive Layout:
 *  - Left Column (7 cols): Clean Google Maps embed centered at MAN 1 Pangandaran.
 *  - Right Column (5 cols): Integrated Institutional Information & Service Card
 *    (Identity & Address, Operational Hours, and Compact Social Media Buttons).
 *
 * Palette: Pure White (#FFFFFF), Slate-50 (#F8FAFC), Deep Navy (#0F172A), Warm Gold (#D4AF37).
 * =========================================================================
 */

global $sysconf;

// Institutional identity and contact
$library_name    = $sysconf['library_name'] ?? "MANSAPA Iqra' Labs";
$library_address = "Kampus MAN 1 Pangandaran, Jl. Raya Cijulang No. 234, Kabupaten Pangandaran, Jawa Barat.";
$library_phone   = !empty($sysconf['library_phone']) ? $sysconf['library_phone'] : '+62 812-2345-6789';

// Clean numeric phone for WhatsApp link
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

// Google Maps Embed: Centered at MAN 1 Pangandaran with clean params (iwloc= suppresses balloons)
$default_map_url = 'https://maps.google.com/maps?q=MAN+1+Pangandaran%2C+Jl.+Raya+Cijulang+No.+234%2C+Kabupaten+Pangandaran&t=&z=16&ie=UTF8&iwloc=&output=embed';
$map_embed_url   = (!empty($sysconf['template']['classic_map_link']) && strpos($sysconf['template']['classic_map_link'], 'Kemendikbud') === false)
    ? $sysconf['template']['classic_map_link']
    : $default_map_url;

// Social media links
$social_media = [
    'facebook'  => [
        'url'   => $sysconf['template']['classic_fb_link'] ?? 'https://www.facebook.com/groups/senayan.slims',
        'icon'  => 'fa-facebook-f',
        'label' => 'Facebook'
    ],
    'instagram' => [
        'url'   => $sysconf['template']['classic_instagram_link'] ?? 'https://instagram.com/slims.sdc',
        'icon'  => 'fa-instagram',
        'label' => 'Instagram'
    ],
    'youtube'   => [
        'url'   => $sysconf['template']['classic_youtube_link'] ?? 'https://youtube.com',
        'icon'  => 'fa-youtube',
        'label' => 'YouTube'
    ],
    'twitter'   => [
        'url'   => $sysconf['template']['classic_twitter_link'] ?? 'https://twitter.com/slims_official',
        'icon'  => 'fa-x-twitter',
        'label' => 'X (Twitter)'
    ],
];
?>

<!-- ================================================================
     SECTION: LOKASI & JAM OPERASIONAL
     2-Column Grid: Maps on Left (7 cols), Info Card on Right (5 cols)
================================================================ -->
<section class="section-location py-section scroll-reveal" id="section-location" aria-labelledby="location-heading">
    <!-- Ambient Section Background Elements (Pure CSS, 0 layout shifts) -->
    <div class="section-bg-deco" aria-hidden="true">
        <div class="deco-mesh-orb deco-orb-left"></div>
    </div>
    <div class="container iqra-container">

        <!-- ── Section Header ──────────────────────────────────────── -->
        <header class="section-header text-center mb-5">
            <span class="section-pill location-pill">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px; vertical-align: -1px;" aria-hidden="true"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/><line x1="8" y1="2" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="22"/></svg> LOKASI &amp; LAYANAN
            </span>
            <h2 class="section-h" id="location-heading">
                Lokasi &amp; Jam Operasional
            </h2>
            <p class="section-sub">
                Kunjungi kampus utama MANSAPA Iqra' Labs dan nikmati layanan ruang baca terpadu kami.
            </p>
        </header>

        <!-- ── 2-Column Responsive Container (7 cols map, 5 cols card) ─ -->
        <div class="location-grid">

            <!-- ── Left Column: Google Maps Embed (6-7 cols) ─────────── -->
            <figure class="location-map-col">
                <div class="map-embed-container">
                    <iframe
                        src="<?= htmlspecialchars($map_embed_url); ?>"
                        class="map-iframe"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Peta Lokasi MAN 1 Pangandaran di Google Maps">
                    </iframe>
                    <div class="map-footer-bar">
                        <a href="https://www.google.com/maps/search/?api=1&query=MAN+1+Pangandaran"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="map-direct-btn"
                           aria-label="Buka petunjuk arah menuju MAN 1 Pangandaran di Google Maps">
                            <span>Buka Petunjuk Arah di Google Maps</span>
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="ms-1" style="vertical-align: -1px;" aria-hidden="true"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                        </a>
                    </div>
                </div>
            </figure><!-- /.location-map-col -->

            <!-- ── Right Column: Institutional Information & Hours Card ─ -->
            <div class="location-info-col">
                <article class="location-info-card">

                    <!-- Sub-item A: Identity & Address -->
                    <div class="location-identity-block">
                        <div class="identity-header">
                            <div class="identity-icon-wrap" aria-hidden="true">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                            </div>
                            <div class="identity-title-wrap">
                                <h3 class="identity-name">MANSAPA Iqra' Labs</h3>
                                <span class="identity-badge">Perpustakaan Terpadu</span>
                            </div>
                        </div>

                        <address class="identity-address-box">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="address-pin-icon" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <span class="address-text"><?= htmlspecialchars($library_address); ?></span>
                        </address>

                        <div class="identity-contact-row">
                            <a href="https://wa.me/<?= $wa_number; ?>?text=Halo%20Admin%20Perpustakaan%20MANSAPA%20Iqra%27%20Labs"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="contact-link-chip"
                               title="Hubungi Admin Layanan Perpustakaan melalui WhatsApp"
                               aria-label="Hubungi WhatsApp <?= htmlspecialchars($display_phone); ?>">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="contact-wa-icon" aria-hidden="true"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                <span class="contact-number"><?= htmlspecialchars($display_phone); ?></span>
                                <span class="contact-tag">Layanan WA</span>
                            </a>
                        </div>
                    </div>

                    <!-- Sub-item B: Jam Buka Layanan (Operational Hours) -->
                    <div class="operational-hours-block">
                        <div class="hours-heading-row">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="hours-icon" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            <h4 class="hours-heading">Jam Buka Layanan</h4>
                        </div>

                        <ul class="hours-schedule-list" role="list">
                            <li class="schedule-item" role="listitem">
                                <span class="schedule-days">
                                    <span class="day-bullet bullet-open" aria-hidden="true"></span>
                                    Senin &ndash; Kamis
                                </span>
                                <span class="schedule-time">07.30 &ndash; 15.30 WIB</span>
                            </li>
                            <li class="schedule-item" role="listitem">
                                <span class="schedule-days">
                                    <span class="day-bullet bullet-open" aria-hidden="true"></span>
                                    Jumat
                                </span>
                                <span class="schedule-time">07.30 &ndash; 11.30 WIB</span>
                            </li>
                            <li class="schedule-item is-closed" role="listitem">
                                <span class="schedule-days">
                                    <span class="day-bullet bullet-closed" aria-hidden="true"></span>
                                    Sabtu, Minggu &amp; Hari Libur
                                </span>
                                <span class="schedule-time closed-badge">Tutup</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Sub-item C: Media Sosial (Follow Social Buttons) -->
                    <div class="location-social-block">
                        <span class="social-label">Ikuti:</span>
                        <div class="social-buttons-grid">
                            <?php foreach ($social_media as $soc_key => $soc): ?>
                            <a href="<?= htmlspecialchars($soc['url']); ?>"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="social-btn-square"
                               title="<?= htmlspecialchars($soc['label']); ?>"
                               aria-label="<?= htmlspecialchars($soc['label']); ?>">
                                <?php if ($soc_key === 'facebook'): ?>
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                <?php elseif ($soc_key === 'instagram'): ?>
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                                <?php elseif ($soc_key === 'youtube'): ?>
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                <?php else: ?>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                <?php endif; ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </article><!-- /.location-info-card -->
            </div><!-- /.location-info-col -->

        </div><!-- /.location-grid -->

    </div><!-- /.container -->
</section><!-- /.section-location -->
