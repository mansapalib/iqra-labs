<?php
/**
 * =========================================================================
 * MANSAPA Iqra' Labs — Direktori Pustakawan & Pengelola [librarian_view.php]
 * File: template/iqra-labs/partials/librarian_view.php
 *
 * Route: index.php?p=librarian
 * Modern, Stylish & Institutional Card Showcase for Library Team
 * Palette: Deep Navy (#0F172A), Warm Gold (#D4AF37, #EAB308), Slate-50 (#F8FAFC)
 * =========================================================================
 */

global $dbs, $sysconf;
$dbs = $dbs ?? \SLiMS\DB::getInstance('mysqli');

// -------------------------------------------------------------------------
// 1. RESOLVE REPOSITORY & CONTACT DATA
// -------------------------------------------------------------------------
$library_name  = !empty($sysconf['library_name']) ? $sysconf['library_name'] : "MANSAPA Iqra' Labs";
$library_sub   = !empty($sysconf['library_subname']) ? $sysconf['library_subname'] : 'MAN 1 Pangandaran';
$library_phone = !empty($sysconf['library_phone']) ? $sysconf['library_phone'] : '+62 812-2345-6789';

// Sanitize phone for WhatsApp direct link
$wa_digits = preg_replace('/[^0-9]/', '', $library_phone);
if (empty($wa_digits) || strlen($wa_digits) < 8) {
    $wa_digits = '6281223456789';
} elseif (substr($wa_digits, 0, 1) === '0') {
    $wa_digits = '62' . substr($wa_digits, 1);
}
$wa_url = 'https://wa.me/' . $wa_digits . '?text=' . urlencode("Halo Pustakawan " . $library_name . ", saya ingin bertanya mengenai layanan perpustakaan.");

$contact_email = !empty($sysconf['mail']['from']) ? $sysconf['mail']['from'] : 'perpustakaan@man1pangandaran.sch.id';

// -------------------------------------------------------------------------
// 2. QUERY LIBRARIAN & STAFF MEMBERS (user_type IN 1, 2, 3)
// -------------------------------------------------------------------------
$sql = "SELECT user_id, username, realname, email, user_type, user_image, social_media 
        FROM user 
        WHERE user_type IN (1, 2, 3) 
        ORDER BY CASE user_type WHEN 2 THEN 1 WHEN 1 THEN 2 WHEN 3 THEN 3 ELSE 4 END ASC, realname ASC";
$res = $dbs->query($sql);

$librarians = [];
$count_all = 0;
$count_librarian = 0;
$count_staff = 0;

// Gradient palette presets for vibrant modern initial avatars
$avatar_gradients = [
    ['from' => '#0F172A', 'to' => '#1E293B', 'accent' => '#F59E0B'], // Navy & Gold
    ['from' => '#065F46', 'to' => '#047857', 'accent' => '#34D399'], // Emerald & Mint
    ['from' => '#1E3A8A', 'to' => '#2563EB', 'accent' => '#60A5FA'], // Royal Blue
    ['from' => '#4C1D95', 'to' => '#6D28D9', 'accent' => '#A78BFA'], // Purple Indigo
    ['from' => '#7C2D12', 'to' => '#C2410C', 'accent' => '#FDBA74'], // Amber Warm
    ['from' => '#0E7490', 'to' => '#0891B2', 'accent' => '#38BDF8'], // Cyan Teal
];

if ($res && $res->num_rows > 0) {
    $idx = 0;
    while ($row = $res->fetch_assoc()) {
        $count_all++;
        $user_type = (int)$row['user_type'];

        // Categorize into Pustakawan (1, 2) vs Staf Layanan (3)
        if ($user_type === 2 || $user_type === 1) {
            $count_librarian++;
            $category_key = 'pustakawan';
        } else {
            $count_staff++;
            $category_key = 'staff';
        }

        // Designation & institutional role info
        $role_title = $sysconf['system_user_type'][$user_type] ?? 'Pengelola Perpustakaan';
        if ($user_type === 2) {
            $role_badge = 'Kepala / Pustakawan Utama';
            $role_class = 'badge-senior';
            $spec_text  = 'Manajemen tata kelola, transformasi otomasi, pengembangan koleksi digital, dan kebijakan kurikulum madrasah.';
        } elseif ($user_type === 1) {
            $role_badge = 'Pustakawan Ahli';
            $role_class = 'badge-librarian';
            $spec_text  = 'Layanan sirkulasi, konsultasi referensi, pengolahan klasifikasi DDC/RDA, serta literasi informasi civitas.';
        } else {
            $role_badge = 'Staf Layanan & Sirkulasi';
            $role_class = 'badge-staff';
            $spec_text  = 'Pendampingan pemustaka, administrasi bebas pustaka, inventarisasi koleksi, dan penataan ruang baca.';
        }

        // Name and initials
        $name = trim($row['realname'] ?? $row['username']);
        $words = preg_split('/\s+/', $name);
        $initials = '';
        if (count($words) >= 2) {
            $initials = mb_strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1));
        } else {
            $initials = mb_strtoupper(mb_substr($name, 0, 2));
        }

        // Photo resolution
        $photo_url = '';
        if (!empty($row['user_image'])) {
            $check_path = __DIR__ . '/../../../images/persons/' . $row['user_image'];
            if (file_exists($check_path)) {
                $photo_url = SWB . 'images/persons/' . htmlspecialchars($row['user_image']);
            }
        }

        // Social media unserialization
        $social_links = [];
        if (!empty($row['social_media'])) {
            $unserialized = @unserialize($row['social_media']);
            if (is_array($unserialized) && !empty($sysconf['social'])) {
                foreach ($sysconf['social'] as $s_id => $s_name) {
                    if (!empty($unserialized[$s_id])) {
                        $social_links[] = [
                            'id'    => $s_id,
                            'name'  => $s_name,
                            'value' => htmlspecialchars($unserialized[$s_id])
                        ];
                    }
                }
            }
        }

        $grad = $avatar_gradients[$idx % count($avatar_gradients)];
        $idx++;

        $librarians[] = [
            'id'           => (int)$row['user_id'],
            'username'     => htmlspecialchars($row['username']),
            'name'         => htmlspecialchars($name),
            'initials'     => $initials,
            'email'        => !empty($row['email']) ? htmlspecialchars($row['email']) : '',
            'user_type'    => $user_type,
            'category'     => $category_key,
            'role_title'   => $role_title,
            'role_badge'   => $role_badge,
            'role_class'   => $role_class,
            'spec_text'    => $spec_text,
            'photo_url'    => $photo_url,
            'gradient'     => $grad,
            'social_links' => $social_links,
        ];
    }
}
?>

<style>
/* ==========================================================================
   MANSAPA IQRA' LABS — LIBRARIAN DIRECTORY STYLES
   ========================================================================== */

.librarian-page-wrapper {
    background-color: #F8FAFC;
    min-height: 85vh;
    padding-bottom: 5rem;
    font-family: 'Inter', sans-serif;
    color: #1E293B;
}

/* ── Hero Section ──────────────────────────────────────────────────────── */
.librarian-hero-section {
    padding: 3rem 1rem 2rem 1rem;
    position: relative;
    text-align: center;
    background: radial-gradient(circle at 50% 0%, rgba(212, 175, 55, 0.08) 0%, rgba(248, 250, 252, 0) 70%);
}

.librarian-kicker {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.35rem 0.9rem;
    background: rgba(234, 179, 8, 0.12);
    border: 1px solid rgba(234, 179, 8, 0.3);
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    color: #B45309;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    margin-bottom: 1rem;
}

.librarian-kicker-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background-color: #EAB308;
    box-shadow: 0 0 8px #EAB308;
    animation: libPulse 2s infinite ease-in-out;
}

@keyframes libPulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.4; transform: scale(0.85); }
}

.librarian-main-title {
    font-family: 'Poppins', sans-serif;
    font-weight: 800;
    font-size: 2rem;
    line-height: 1.2;
    letter-spacing: -0.025em;
    color: #0F172A;
    margin-bottom: 0.75rem;
}

@media (min-width: 768px) {
    .librarian-main-title {
        font-size: 2.5rem;
    }
}

.librarian-sub-title {
    color: #475569;
    font-size: 1rem;
    line-height: 1.6;
    max-width: 44rem;
    margin: 0 auto 1.75rem auto;
}

/* ── Highlights Stat Ribbon ────────────────────────────────────────────── */
.librarian-stat-ribbon {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
    gap: 0.75rem;
    margin: 0 auto 2.25rem auto;
    max-width: 52rem;
}

.librarian-stat-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: #FFFFFF;
    border: 1px solid #E2E8F0;
    border-radius: 9999px;
    font-size: 0.8125rem;
    font-weight: 500;
    color: #334155;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}

.librarian-stat-pill strong {
    font-weight: 700;
    color: #0F172A;
}

.librarian-stat-pill svg {
    color: #D4AF37;
    flex-shrink: 0;
}

/* ── Interactive Toolbar: Category Pills + Search ──────────────────────── */
.librarian-toolbar-wrap {
    max-width: 60rem;
    margin: 0 auto 2.5rem auto;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

@media (min-width: 768px) {
    .librarian-toolbar-wrap {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }
}

.librarian-filter-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    justify-content: center;
}

.lib-tab-btn {
    border: 1px solid #E2E8F0;
    background: #FFFFFF;
    color: #475569;
    font-size: 0.8125rem;
    font-weight: 600;
    padding: 0.45rem 1rem;
    border-radius: 9999px;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
}

.lib-tab-btn .badge-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 1.25rem;
    height: 1.25rem;
    padding: 0 0.35rem;
    font-size: 0.7rem;
    font-weight: 700;
    border-radius: 9999px;
    background: #F1F5F9;
    color: #64748B;
    transition: all 0.2s ease;
}

.lib-tab-btn:hover {
    border-color: #CBD5E1;
    color: #0F172A;
    background: #F8FAFC;
    transform: translateY(-1px);
}

.lib-tab-btn.active {
    background: #0F172A;
    color: #FACC15;
    border-color: #0F172A;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
}

.lib-tab-btn.active .badge-count {
    background: rgba(250, 204, 21, 0.2);
    color: #FACC15;
}

/* Quick Search Input */
.librarian-search-box {
    position: relative;
    min-width: 260px;
}

.librarian-search-input {
    width: 100%;
    height: 42px;
    padding: 0 2.2rem 0 2.4rem;
    font-size: 0.84rem;
    color: #0F172A;
    background: #FFFFFF;
    border: 1px solid #E2E8F0;
    border-radius: 9999px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    outline: none;
    transition: all 0.2s ease;
}

.librarian-search-input:focus {
    border-color: #EAB308;
    box-shadow: 0 0 0 3px rgba(234, 179, 8, 0.15);
}

.librarian-search-icon {
    position: absolute;
    left: 0.85rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94A3B8;
    pointer-events: none;
}

.librarian-search-clear {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    border: none;
    color: #94A3B8;
    cursor: pointer;
    padding: 0.2rem;
    display: none;
}
.librarian-search-clear:hover {
    color: #475569;
}

/* ── Card Grid Layout ──────────────────────────────────────────────────── */
.librarian-grid {
    display: grid;
    grid-template-columns: repeat(1, minmax(0, 1fr));
    gap: 1.5rem;
    max-width: 68rem;
    margin: 0 auto;
}

@media (min-width: 640px) {
    .librarian-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (min-width: 1024px) {
    .librarian-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

/* ── Individual Librarian Card ─────────────────────────────────────────── */
.librarian-card {
    background: #FFFFFF;
    border: 1px solid #E2E8F0;
    border-radius: 1.25rem;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.04), 0 2px 4px -2px rgba(15, 23, 42, 0.03);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}

.librarian-card:hover {
    transform: translateY(-6px);
    border-color: rgba(234, 179, 8, 0.5);
    box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.04), 0 0 15px rgba(234, 179, 8, 0.1);
}

/* Card Decorative Header Banner */
.librarian-card-banner {
    height: 90px;
    background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
    position: relative;
    overflow: hidden;
}

.librarian-card-banner::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: radial-gradient(rgba(234, 179, 8, 0.18) 1px, transparent 1px);
    background-size: 14px 14px;
    opacity: 0.6;
}

.librarian-card-banner-accent {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #EAB308, #F59E0B, #CA8A04);
}

/* Card Avatar Container */
.librarian-avatar-wrap {
    position: relative;
    margin: -48px auto 0.75rem auto;
    width: 96px;
    height: 96px;
    z-index: 2;
}

.librarian-avatar {
    width: 96px;
    height: 96px;
    border-radius: 50%;
    border: 4px solid #FFFFFF;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
    object-fit: cover;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Poppins', sans-serif;
    font-size: 1.85rem;
    font-weight: 700;
    color: #FFFFFF;
    letter-spacing: -0.02em;
    user-select: none;
    transition: transform 0.3s ease;
}

.librarian-card:hover .librarian-avatar {
    transform: scale(1.05);
}

/* Live Active Duty Status Badge */
.librarian-status-beacon {
    position: absolute;
    bottom: 2px;
    right: 4px;
    width: 18px;
    height: 18px;
    background: #10B981;
    border: 3px solid #FFFFFF;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
}

.librarian-status-beacon::after {
    content: '';
    position: absolute;
    inset: -3px;
    border-radius: 50%;
    border: 1px solid #10B981;
    animation: beaconPulse 2s infinite ease-out;
}

@keyframes beaconPulse {
    0% { transform: scale(0.9); opacity: 0.9; }
    100% { transform: scale(1.6); opacity: 0; }
}

/* Card Body Content */
.librarian-card-body {
    padding: 0 1.5rem 1.5rem 1.5rem;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    text-align: center;
}

.librarian-card-name {
    font-family: 'Poppins', sans-serif;
    font-size: 1.15rem;
    font-weight: 700;
    color: #0F172A;
    margin-bottom: 0.35rem;
    line-height: 1.3;
}

/* Role Badges */
.librarian-role-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.35rem;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    margin: 0 auto 0.85rem auto;
}

.badge-senior {
    background: rgba(234, 179, 8, 0.15);
    color: #92400E;
    border: 1px solid rgba(234, 179, 8, 0.4);
}

.badge-librarian {
    background: rgba(37, 99, 235, 0.1);
    color: #1E40AF;
    border: 1px solid rgba(37, 99, 235, 0.25);
}

.badge-staff {
    background: rgba(100, 116, 139, 0.1);
    color: #334155;
    border: 1px solid rgba(100, 116, 139, 0.25);
}

.librarian-card-desc {
    font-size: 0.835rem;
    line-height: 1.55;
    color: #64748B;
    margin-bottom: 1.25rem;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Metadata Info List */
.librarian-info-list {
    background: #F8FAFC;
    border: 1px solid #F1F5F9;
    border-radius: 0.75rem;
    padding: 0.75rem 0.85rem;
    margin-bottom: 1.25rem;
    text-align: left;
    display: flex;
    flex-direction: column;
    gap: 0.45rem;
    font-size: 0.78rem;
}

.librarian-info-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #475569;
}

.librarian-info-row svg {
    color: #D4AF37;
    flex-shrink: 0;
}

.librarian-info-row span {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Card Action Footer */
.librarian-card-footer {
    margin-top: auto;
    padding-top: 1rem;
    border-top: 1px solid #F1F5F9;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.btn-lib-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.45rem;
    width: 100%;
    padding: 0.55rem 1rem;
    border-radius: 0.625rem;
    font-size: 0.8125rem;
    font-weight: 600;
    text-decoration: none !important;
    transition: all 0.2s ease;
}

.btn-lib-primary {
    background: #0F172A;
    color: #FACC15;
    border: 1px solid #0F172A;
}

.btn-lib-primary:hover {
    background: #1E293B;
    color: #FFFFFF;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(15, 23, 42, 0.15);
}

.btn-lib-secondary {
    background: #FFFFFF;
    color: #334155;
    border: 1px solid #CBD5E1;
}

.btn-lib-secondary:hover {
    background: #F8FAFC;
    color: #0F172A;
    border-color: #94A3B8;
}

/* ── Services Showcase Section ─────────────────────────────────────────── */
.librarian-services-section {
    max-width: 68rem;
    margin: 4rem auto 0 auto;
    padding: 0 1rem;
}

.librarian-services-header {
    text-align: center;
    margin-bottom: 2.25rem;
}

.services-grid {
    display: grid;
    grid-template-columns: repeat(1, minmax(0, 1fr));
    gap: 1.25rem;
}

@media (min-width: 640px) {
    .services-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (min-width: 1024px) {
    .services-grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }
}

.service-card {
    background: #FFFFFF;
    border: 1px solid #E2E8F0;
    border-radius: 1rem;
    padding: 1.5rem 1.25rem;
    transition: all 0.25s ease;
    display: flex;
    flex-direction: column;
}

.service-card:hover {
    transform: translateY(-4px);
    border-color: #FBBF24;
    box-shadow: 0 10px 20px -5px rgba(15, 23, 42, 0.06);
}

.service-icon-box {
    width: 44px;
    height: 44px;
    border-radius: 0.75rem;
    background: rgba(234, 179, 8, 0.12);
    color: #B45309;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
}

.service-title {
    font-family: 'Poppins', sans-serif;
    font-size: 0.95rem;
    font-weight: 700;
    color: #0F172A;
    margin-bottom: 0.45rem;
    line-height: 1.35;
}

.service-desc {
    font-size: 0.8125rem;
    color: #64748B;
    line-height: 1.5;
    margin: 0;
}

/* ── Concierge Help Banner (Bottom) ────────────────────────────────────── */
.librarian-concierge-card {
    max-width: 68rem;
    margin: 3.5rem auto 0 auto;
    background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
    border-radius: 1.5rem;
    padding: 2.5rem 1.75rem;
    color: #FFFFFF;
    position: relative;
    overflow: hidden;
    box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.25);
    border: 1px solid rgba(234, 179, 8, 0.25);
}

.concierge-content {
    position: relative;
    z-index: 2;
    text-align: center;
    max-width: 42rem;
    margin: 0 auto;
}

.concierge-title {
    font-family: 'Poppins', sans-serif;
    font-size: 1.5rem;
    font-weight: 700;
    color: #FFFFFF;
    margin-bottom: 0.65rem;
}

.concierge-desc {
    font-size: 0.92rem;
    color: #CBD5E1;
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.concierge-actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
}

.btn-concierge-wa {
    background: #10B981;
    color: #FFFFFF;
    border: 1px solid #10B981;
    padding: 0.65rem 1.4rem;
    border-radius: 9999px;
    font-weight: 600;
    font-size: 0.86rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none !important;
    transition: all 0.2s ease;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.btn-concierge-wa:hover {
    background: #059669;
    color: #FFFFFF;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
}

.btn-concierge-link {
    background: rgba(255, 255, 255, 0.08);
    color: #FACC15;
    border: 1px solid rgba(250, 204, 21, 0.3);
    padding: 0.65rem 1.3rem;
    border-radius: 9999px;
    font-weight: 600;
    font-size: 0.86rem;
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    text-decoration: none !important;
    transition: all 0.2s ease;
}

.btn-concierge-link:hover {
    background: rgba(250, 204, 21, 0.15);
    color: #FFFFFF;
    border-color: #FACC15;
}

/* ── Empty State ───────────────────────────────────────────────────────── */
.librarian-empty-state {
    display: none;
    text-align: center;
    padding: 4rem 1.5rem;
    background: #FFFFFF;
    border: 1px solid #E2E8F0;
    border-radius: 1.25rem;
    max-width: 32rem;
    margin: 2rem auto;
}

.librarian-empty-icon {
    width: 48px;
    height: 48px;
    margin: 0 auto 1rem auto;
    color: #94A3B8;
}
</style>

<div class="librarian-page-wrapper">

    <!-- ── 1. NAVBAR HEADER CONTAINER ───────────────────────────────────── -->
    <section id="section1" class="container-fluid p-0">
        <div class="c-header">
            <div class="mask"></div>
            <?php include __DIR__ . '/../parts/_navbar.php'; ?>
        </div>
    </section>

    <!-- ── 2. HERO SECTION ──────────────────────────────────────────────── -->
    <header class="librarian-hero-section">
        <div class="container iqra-container max-w-4xl mx-auto px-4">
            
            <!-- Eyebrow Kicker -->
            <div>
                <span class="librarian-kicker">
                    <span class="librarian-kicker-dot" aria-hidden="true"></span>
                    <span>Profil Lembaga &bull; Tim Pustakawan</span>
                </span>
            </div>

            <!-- Page Title -->
            <h1 class="librarian-main-title">
                Pustakawan &amp; Pengelola Perpustakaan
            </h1>

            <!-- Subtitle -->
            <p class="librarian-sub-title">
                Mengenal lebih dekat para pengabdi literasi di <strong><?= htmlspecialchars($library_name); ?></strong> yang berdedikasi mendampingi kebutuhan penelusuran referensi ilmiah, sirkulasi koleksi, dan pengembangan wawasan civitas madrasah.
            </p>

            <!-- Stat Ribbon -->
            <div class="librarian-stat-ribbon">
                <div class="librarian-stat-pill">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <span><strong><?= $count_all; ?></strong> Pengelola &amp; Staf</span>
                </div>
                <div class="librarian-stat-pill">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span>Senin &ndash; Jumat &bull; <strong>07.00 &ndash; 16.00 WIB</strong></span>
                </div>
                <div class="librarian-stat-pill">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 11 3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                    <span>Konsultasi &amp; Penelusuran <strong>Gratis</strong></span>
                </div>
            </div>

        </div>
    </header>

    <!-- ── 3. MAIN DIRECTORY CONTAINER ─────────────────────────────────── -->
    <main class="container iqra-container max-w-6xl mx-auto px-4">

        <!-- Toolbar: Category filter + quick search -->
        <div class="librarian-toolbar-wrap">
            
            <!-- Category Tabs -->
            <div class="librarian-filter-tabs" role="tablist" aria-label="Filter kategori tim">
                <button type="button" class="lib-tab-btn active" data-filter="all">
                    <span>Semua Tim</span>
                    <span class="badge-count"><?= $count_all; ?></span>
                </button>
                <button type="button" class="lib-tab-btn" data-filter="pustakawan">
                    <span>Pustakawan</span>
                    <span class="badge-count"><?= $count_librarian; ?></span>
                </button>
                <?php if ($count_staff > 0): ?>
                <button type="button" class="lib-tab-btn" data-filter="staff">
                    <span>Staf Layanan</span>
                    <span class="badge-count"><?= $count_staff; ?></span>
                </button>
                <?php endif; ?>
            </div>

            <!-- Real-time Live Search Input -->
            <div class="librarian-search-box">
                <svg class="librarian-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.3-4.3"/>
                </svg>
                <input type="text" id="libSearchInput" class="librarian-search-input" placeholder="Cari nama atau jabatan..." aria-label="Cari pustakawan">
                <button type="button" id="libSearchClear" class="librarian-search-clear" aria-label="Hapus kata kunci">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>
                </button>
            </div>

        </div>

        <!-- ── Cards Grid ──────────────────────────────────────────────── -->
        <?php if (!empty($librarians)): ?>
        <div class="librarian-grid" id="librarianGrid">
            <?php foreach ($librarians as $lib): ?>
            <article class="librarian-card" 
                     data-category="<?= $lib['category']; ?>" 
                     data-name="<?= strtolower($lib['name']); ?>"
                     data-role="<?= strtolower($lib['role_title'] . ' ' . $lib['role_badge']); ?>">
                
                <!-- Card Header Banner -->
                <div class="librarian-card-banner">
                    <div class="librarian-card-banner-accent"></div>
                </div>

                <!-- Avatar Profile with Status Beacon -->
                <div class="librarian-avatar-wrap">
                    <?php if (!empty($lib['photo_url'])): ?>
                        <img src="<?= $lib['photo_url']; ?>" alt="<?= $lib['name']; ?>" class="librarian-avatar">
                    <?php else: ?>
                        <div class="librarian-avatar" style="background: linear-gradient(135deg, <?= $lib['gradient']['from']; ?>, <?= $lib['gradient']['to']; ?>); color: <?= $lib['gradient']['accent']; ?>;">
                            <?= $lib['initials']; ?>
                        </div>
                    <?php endif; ?>
                    <span class="librarian-status-beacon" title="Aktif bertugas" aria-label="Status: Aktif bertugas"></span>
                </div>

                <!-- Card Body -->
                <div class="librarian-card-body">
                    
                    <!-- Role Pill -->
                    <div>
                        <span class="librarian-role-pill <?= $lib['role_class']; ?>">
                            <?= $lib['role_badge']; ?>
                        </span>
                    </div>

                    <!-- Full Name -->
                    <h2 class="librarian-card-name"><?= $lib['name']; ?></h2>

                    <!-- Scope / Specialization -->
                    <p class="librarian-card-desc"><?= $lib['spec_text']; ?></p>

                    <!-- Info Meta List -->
                    <div class="librarian-info-list">
                        <div class="librarian-info-row">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                            <span>Layanan Sirkulasi &amp; Referensi</span>
                        </div>
                        <div class="librarian-info-row">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                            <span><?= htmlspecialchars($library_sub); ?></span>
                        </div>
                        <?php if (!empty($lib['email'])): ?>
                        <div class="librarian-info-row" title="<?= $lib['email']; ?>">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                            <span><?= $lib['email']; ?></span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Action Footer -->
                    <div class="librarian-card-footer">
                        <?php if (!empty($lib['email'])): ?>
                        <a href="mailto:<?= $lib['email']; ?>?subject=<?= urlencode('Konsultasi Layanan Perpustakaan - ' . $library_name); ?>" class="btn-lib-action btn-lib-primary">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                            <span>Hubungi via Email</span>
                        </a>
                        <?php else: ?>
                        <a href="<?= $wa_url; ?>" target="_blank" rel="noopener noreferrer" class="btn-lib-action btn-lib-primary">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                            <span>Konsultasi Layanan</span>
                        </a>
                        <?php endif; ?>
                    </div>

                </div>

            </article>
            <?php endforeach; ?>
        </div>

        <!-- Empty State (When search yields no results) -->
        <div id="librarianEmptyState" class="librarian-empty-state">
            <svg class="librarian-empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="11" cy="11" r="8"/>
                <path d="m21 21-4.3-4.3"/>
                <line x1="8" y1="11" x2="14" y2="11"/>
            </svg>
            <h3 style="font-family: 'Poppins', sans-serif; font-size: 1.1rem; font-weight: 700; color: #0F172A; margin-bottom: 0.35rem;">Pustakawan Tidak Ditemukan</h3>
            <p style="font-size: 0.85rem; color: #64748B; margin-bottom: 1rem;">Tidak ada anggota tim yang cocok dengan kata kunci pencarian Anda.</p>
            <button type="button" id="btnResetFilter" class="lib-tab-btn" style="margin: 0 auto;">Tampilkan Semua Tim</button>
        </div>

        <?php else: ?>
        <div class="librarian-empty-state" style="display: block;">
            <p style="color: #64748B; font-size: 0.95rem;">Data pustakawan dan pengelola belum tersedia.</p>
        </div>
        <?php endif; ?>

        <!-- ── 4. LIBRARIAN SERVICES HIGHLIGHTS ────────────────────────── -->
        <section class="librarian-services-section">
            <div class="librarian-services-header">
                <span class="librarian-kicker" style="margin-bottom: 0.5rem;">
                    <span>Dedikasi &bull; Layanan Kami</span>
                </span>
                <h2 style="font-family: 'Poppins', sans-serif; font-weight: 800; font-size: 1.6rem; color: #0F172A; margin-bottom: 0.5rem;">
                    Bagaimana Pustakawan Kami Membantu Anda?
                </h2>
                <p style="font-size: 0.9rem; color: #64748B; max-width: 36rem; margin: 0 auto;">
                    Kami siap melayani kebutuhan akademik, penelusuran pustaka, dan pengembangan minat baca seluruh civitas madrasah.
                </p>
            </div>

            <div class="services-grid">
                
                <div class="service-card">
                    <div class="service-icon-box">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </div>
                    <h3 class="service-title">Bimbingan Penelusuran OPAC</h3>
                    <p class="service-desc">Panduan mencari koleksi buku fisik, e-book, dan karya referensi secara cepat melalui katalog online perpustakaan.</p>
                </div>

                <div class="service-card">
                    <div class="service-icon-box">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/><path d="M14 2v6l2.5-1.5L19 8V2"/></svg>
                    </div>
                    <h3 class="service-title">Dukungan Riset &amp; KIR</h3>
                    <p class="service-desc">Bantuan kurasi bahan rujukan dan sitasi untuk karya tulis ilmiah remaja, riset madrasah, dan penugasan guru.</p>
                </div>

                <div class="service-card">
                    <div class="service-icon-box">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M7 7h10"/><path d="M7 12h10"/><path d="M7 17h10"/></svg>
                    </div>
                    <h3 class="service-title">Sirkulasi &amp; Keanggotaan</h3>
                    <p class="service-desc">Pelayanan peminjaman 2&ndash;3 eksemplar, pengembalian buku, perpanjangan masa pinjam, serta penerbitan bebas pustaka.</p>
                </div>

                <div class="service-card">
                    <div class="service-icon-box">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    </div>
                    <h3 class="service-title">Konsultasi Terbuka</h3>
                    <p class="service-desc">Ruang diskusi terbuka bagi pemustaka yang ingin mengajukan usulan pengadaan buku baru atau konsultasi literasi.</p>
                </div>

            </div>
        </section>

        <!-- ── 5. CONCIERGE HELP BANNER (Bottom) ───────────────────────── -->
        <aside class="librarian-concierge-card" aria-label="Bantuan dan Konsultasi Langsung">
            <div class="concierge-content">
                <h2 class="concierge-title">Butuh Rekomendasi Buku atau Bantuan Riset?</h2>
                <p class="concierge-desc">
                    Pustakawan kami senantiasa siap menyambut Anda di meja layanan lantai perpustakaan atau secara daring melalui saluran komunikasi resmi madrasah.
                </p>
                <div class="concierge-actions">
                    <a href="<?= $wa_url; ?>" target="_blank" rel="noopener noreferrer" class="btn-concierge-wa">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                        <span>WhatsApp Meja Layanan</span>
                    </a>
                    <a href="index.php?p=tata-tertib" class="btn-concierge-link">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span>Jam Layanan &amp; Tata Tertib</span>
                    </a>
                    <a href="index.php?p=faq" class="btn-concierge-link">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        <span>FAQ / Pusat Bantuan</span>
                    </a>
                </div>
            </div>
        </aside>

    </main>

</div>

<!-- ── 6. INSTANT CLIENT-SIDE FILTERING JAVASCRIPT ──────────────────────── -->
<script>
(function() {
    'use strict';

    var tabButtons   = document.querySelectorAll('.lib-tab-btn[data-filter]');
    var searchInput  = document.getElementById('libSearchInput');
    var searchClear  = document.getElementById('libSearchClear');
    var librarianCards = document.querySelectorAll('.librarian-card');
    var emptyState   = document.getElementById('librarianEmptyState');
    var btnReset     = document.getElementById('btnResetFilter');

    var currentCategory = 'all';
    var currentKeyword  = '';

    function filterCards() {
        var visibleCount = 0;
        var query = currentKeyword.toLowerCase().trim();

        librarianCards.forEach(function(card) {
            var category = card.getAttribute('data-category') || '';
            var name     = card.getAttribute('data-name') || '';
            var role     = card.getAttribute('data-role') || '';

            var matchCategory = (currentCategory === 'all' || category === currentCategory);
            var matchSearch   = (query === '' || name.indexOf(query) !== -1 || role.indexOf(query) !== -1);

            if (matchCategory && matchSearch) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (emptyState) {
            emptyState.style.display = (visibleCount === 0) ? 'block' : 'none';
        }
    }

    // Tab buttons click handler
    tabButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            tabButtons.forEach(function(b) { b.classList.remove('active'); });
            this.classList.add('active');
            currentCategory = this.getAttribute('data-filter') || 'all';
            filterCards();
        });
    });

    // Real-time search handler
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            currentKeyword = this.value;
            if (searchClear) {
                searchClear.style.display = (currentKeyword.length > 0) ? 'block' : 'none';
            }
            filterCards();
        });
    }

    // Clear search handler
    if (searchClear) {
        searchClear.addEventListener('click', function() {
            if (searchInput) {
                searchInput.value = '';
                currentKeyword = '';
                searchInput.focus();
            }
            this.style.display = 'none';
            filterCards();
        });
    }

    // Reset button in empty state
    if (btnReset) {
        btnReset.addEventListener('click', function() {
            currentCategory = 'all';
            currentKeyword = '';
            if (searchInput) {
                searchInput.value = '';
            }
            if (searchClear) {
                searchClear.style.display = 'none';
            }
            tabButtons.forEach(function(b) {
                if (b.getAttribute('data-filter') === 'all') {
                    b.classList.add('active');
                } else {
                    b.classList.remove('active');
                }
            });
            filterCards();
        });
    }
})();
</script>
