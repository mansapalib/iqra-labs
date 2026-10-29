<?php
/**
 * @Created by          : Waris Agung Widodo (ido.alit@gmail.com)
 * @Redesigned by       : MANSAPA Iqra' Labs Team
 * @File name           : news_template.php
 *
 * Renders an individual modern card item for SLiMS news listings.
 * Palette: Deep Navy (#0F172A), Warm Gold (#D4AF37), Slate-50 Canvas
 */

function news_list_tpl($title, $path, $date, $summary, $raw_content = '') {
    global $sysconf;
    static $card_counter = 0;
    $card_counter++;

    if (isset($_COOKIE['select_lang'])) {
        $sysconf['default_lang'] = trim(strip_tags($_COOKIE['select_lang']));
    }

    // 1. Featured Image Extraction: Parse first <img> tag from raw content or summary
    $content_to_scan = !empty($raw_content) ? $raw_content : $summary;
    $extracted_image = '';
    if (preg_match('/<img.+?src=[\'"]([^\'"]+)[\'"].*?>/i', $content_to_scan, $matches)) {
        $extracted_image = trim($matches[1]);
        $extracted_image = preg_replace('#^https?://[^/]+/mansapalabs/#i', SWB, $extracted_image);
    }
    $has_image = !empty($extracted_image);

    // 2. Clean Excerpt: Strip HTML and sanitize
    $clean_excerpt = preg_replace('/\s+/', ' ', trim(strip_tags(html_entity_decode($summary, ENT_QUOTES, 'UTF-8'))));
    $clean_excerpt = mb_strimwidth($clean_excerpt, 0, 120, '...');

    // 3. Localized Indonesian Date Formatting
    $indonesian_months = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
        7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
    ];
    $timestamp = strtotime($date);
    if ($timestamp) {
        $month_num = (int)date('n', $timestamp);
        $month_name = $indonesian_months[$month_num] ?? date('M', $timestamp);
        $formatted_date = date('j', $timestamp) . ' ' . $month_name . ' ' . date('Y', $timestamp);
        $datetime_attr  = date('Y-m-d', $timestamp);
    } else {
        $formatted_date = 'Terbaru';
        $datetime_attr  = date('Y-m-d');
    }

    $detail_url = SWB . 'index.php?p=' . urlencode($path);
    $card_id = $card_counter;
    ?>

    <article class="news-card-item group" style="background:#fff; border:1px solid rgba(226,232,240,0.9); border-radius:1rem; box-shadow:0 1px 3px rgba(0,0,0,0.05); display:flex; flex-direction:column; overflow:hidden; height:100%; transition:all 0.3s ease;">
        
        <!-- Top Image Container (aspect-ratio 16/10) -->
        <div style="position:relative; width:100%; aspect-ratio:16/10; overflow:hidden; background-color:#F1F5F9;">
            
            <!-- Floating Date Tag -->
            <div style="position:absolute; top:0.75rem; left:0.75rem; z-index:10; background:rgba(15,23,42,0.85); backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px); color:#fff; font-size:11px; font-weight:500; padding:0.25rem 0.65rem; border-radius:0.5rem; display:flex; align-items:center; gap:0.375rem; border:1px solid rgba(255,255,255,0.12);">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#D4AF37" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <time datetime="<?= htmlspecialchars($datetime_attr) ?>"><?= htmlspecialchars($formatted_date) ?></time>
            </div>

            <?php if ($has_image): ?>
                <img src="<?= htmlspecialchars($extracted_image) ?>" 
                     alt="<?= htmlspecialchars($title) ?>" 
                     style="width:100%; height:100%; object-fit:cover; display:block; transition:transform 0.5s ease;"
                     loading="lazy"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                
                <!-- Branded Fallback Placeholder (on image load error) -->
                <div style="display:none; width:100%; height:100%; position:absolute; top:0; left:0; background:linear-gradient(135deg, #0F172A 0%, #1E293B 60%, #0F172A 100%); flex-direction:column; align-items:center; justify-content:center;">
                    <div style="width:3rem; height:3rem; border-radius:0.75rem; background:rgba(212,175,55,0.15); border:1px solid rgba(212,175,55,0.35); display:flex; align-items:center; justify-content:center; color:#D4AF37; margin-bottom:0.5rem;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M6 6h10"/><path d="M6 10h10"/></svg>
                    </div>
                    <span style="font-family:monospace; font-size:11px; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:rgba(212,175,55,0.9);">MANSAPA Iqra' Labs</span>
                </div>
            <?php else: ?>
                <!-- Branded Fallback Placeholder (when no image exists) -->
                <div style="width:100%; height:100%; position:absolute; top:0; left:0; background:linear-gradient(135deg, #0F172A 0%, #1E293B 60%, #0F172A 100%); display:flex; flex-direction:column; align-items:center; justify-content:center;">
                    <div style="width:3rem; height:3rem; border-radius:0.75rem; background:rgba(212,175,55,0.15); border:1px solid rgba(212,175,55,0.35); display:flex; align-items:center; justify-content:center; color:#D4AF37; margin-bottom:0.5rem;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M6 6h10"/><path d="M6 10h10"/></svg>
                    </div>
                    <span style="font-family:monospace; font-size:11px; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:rgba(212,175,55,0.9);">MANSAPA Iqra' Labs</span>
                </div>
            <?php endif; ?>

        </div>

        <!-- Card Body -->
        <div style="padding:1.25rem; display:flex; flex-direction:column; flex-grow:1; justify-content:space-between;">
            <div>
                <h3 style="font-family:'Poppins', sans-serif; font-weight:700; font-size:1.0625rem; line-height:1.4; color:#0F172A; margin-bottom:0.5rem; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">
                    <a href="<?= $detail_url ?>" style="color:inherit; text-decoration:none;">
                        <?= htmlspecialchars($title) ?>
                    </a>
                </h3>
                <p style="color:#475569; font-size:0.875rem; line-height:1.6; margin-bottom:1rem; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden;">
                    <?= htmlspecialchars($clean_excerpt) ?>
                </p>
            </div>

            <!-- Card Action -->
            <div style="display:flex; align-items:center; justify-content:space-between; margin-top:auto; padding-top:0.875rem; border-top:1px solid #F1F5F9;">
                <a href="<?= $detail_url ?>" style="font-size:0.75rem; font-weight:600; color:#0F172A; text-decoration:none; display:inline-flex; align-items:center; gap:0.25rem;">
                    <span>Baca Selengkapnya</span>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            </div>
        </div>

    </article>

    <?php
}