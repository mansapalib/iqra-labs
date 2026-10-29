<?php
/**
 * Template for Biblio List (Search Results & OPAC Catalog)
 * Redesigned for MANSAPA Iqra' Labs
 *
 * Senior Frontend & UI/UX Refactoring:
 * - Breathable, non-bulky 2-column key-value metadata table
 * - Monochrome inline SVGs replacing emojis
 * - Warm-gold highlight pill for call number
 * - Audited 4-button quick-action toolbar without empty placeholder
 * - Full accessibility with tooltips & cohesive states
 */
use SLiMS\Url;

$label_cache = array();

/**
 * Format bibliographic item list for OPAC display
 *
 * @param   object $dbs
 * @param   array  $biblio_detail
 * @param   int    $n
 * @param   array  $settings
 * @param   array  $return_back
 *
 * @return string
 */
function biblio_list_format($dbs, $biblio_detail, $n, $settings = array(), &$return_back = array()) {
    global $label_cache, $sysconf;
    $output = '';

    $title      = $biblio_detail['title'] ?? '-';
    $biblio_id  = (int)($biblio_detail['biblio_id'] ?? 0);
    $detail_url = SWB . 'index.php?p=show_detail&id=' . $biblio_id . '&keywords=' . urlencode($settings['keywords'] ?? '');
    $cite_url   = SWB . 'index.php?p=cite&id=' . $biblio_id . '&keywords=' . urlencode($settings['keywords'] ?? '');
    $raw_title  = htmlspecialchars(strip_tags($title), ENT_QUOTES, 'UTF-8');

    // Image thumbnail
    $images_loc = 'images/docs/' . ($biblio_detail['image'] ?? '');
    if (empty($biblio_detail['image']) || !file_exists(SB . $images_loc)) {
        $images_loc = 'images/default/image.png';
    }
    $thumb_url = './lib/minigalnano/createthumb.php?filename=' . urlencode($images_loc) . '&width=260';

    // Availability
    $availability = getAvailability($dbs, $biblio_id, $sysconf);
    $is_available = ($availability > 0);

    // Authors
    // Authors (Clean, standard text links without bulky badges or oversized SVGs)
    $_authors = isset($biblio_detail['author']) ? $biblio_detail['author'] : biblio_list_model::getAuthors($dbs, $biblio_id, true);
    $_authors_string = '';
    $_first_author = '';
    if (!empty($_authors)) {
        if (!is_array($_authors)) {
            $_authors = explode('-', $_authors);
        }
        $author_links = [];
        foreach ($_authors as $a) {
            $a = trim($a);
            if ($a === '') continue;
            if (empty($_first_author)) $_first_author = $a;
            $author_links[] = '<a href="index.php?author=' . urlencode($a) . '&search=Search" class="author-clean-link" title="' . __('Cari koleksi penulis: ') . htmlspecialchars($a) . '">' . htmlspecialchars($a) . '</a>';
        }
        $_authors_string = implode(', ', $author_links);
    }
    if (empty($_authors_string)) {
        $_authors_string = '<span class="text-slate-400">' . __('Penulis Tidak Terdata') . '</span>';
        $_first_author = __('Penulis Tidak Terdata');
    }

    // Call Number & Metadata
    $call_number = trim($biblio_detail['call_number'] ?? '');
    $isbn_issn   = trim($biblio_detail['isbn_issn'] ?? '');
    $edition     = trim($biblio_detail['edition'] ?? '');
    $collation   = trim($biblio_detail['collation'] ?? '');
    $series      = trim($biblio_detail['series_title'] ?? '');
    $publisher   = trim($biblio_detail['publisher_name'] ?? '');
    $publish_yr  = trim($biblio_detail['publish_year'] ?? '');

    $pub_val = '';
    if ($publisher !== '' || $publish_yr !== '') {
        $pub_val = htmlspecialchars($publisher);
        if ($publish_yr !== '') {
            $pub_val .= ($publisher !== '' ? ', ' : '') . htmlspecialchars($publish_yr);
        }
    }

    // Notes / Synopsis
    $notes = getNotes($dbs, $biblio_id);

    // Bookmark state
    $isBookmarked = in_array($biblio_id, $_SESSION['bookmark'] ?? []);
    $bookmarkLabel = $isBookmarked ? __('Tersimpan') : __('Bookmark');
    $bookmarkClass = $isBookmarked ? 'is-active' : '';
    $bookmarkTitle = $isBookmarked ? __('Tersimpan di Bookmark Anda') : __('Simpan ke Bookmark');

    // View mode detection
    $view_mode = $_POST['view'] ?? $_SESSION['LIST_VIEW'] ?? 'list';

    if ($view_mode === 'list'):

        // ── Render Clean, Simple List Item Card ──────────────────────────
        $output .= '<article id="card-' . $biblio_id . '" class="biblio-card book-card">';
        
        // ── Cover Column (Left) ──────────────────────────────────────────
        $output .= '  <div class="biblio-card-cover">';
        $output .= '    <a href="' . $detail_url . '" class="biblio-cover-link" title="' . __('Lihat detail: ') . $raw_title . '">';
        $output .= '      <img loading="lazy" src="' . $thumb_url . '" alt="' . $raw_title . '" class="biblio-thumb ' . ($is_available ? '' : 'is-borrowed') . '" width="88" height="122" />';
        $output .= '    </a>';
        if (!$is_available) {
            $output .= '    <span class="cover-tag-borrowed">' . __('Dipinjam') . '</span>';
        }
        $output .= '  </div>';

        // ── Main Body Column (Right) ─────────────────────────────────────
        $output .= '  <div class="biblio-card-body">';
        
        // 1. Header: Title & Author
        $output .= '    <div class="biblio-header-block">';
        $output .= '      <h3 class="biblio-title">';
        $output .= '        <a href="' . $detail_url . '" title="' . $raw_title . '">' . htmlspecialchars(addEllipsis($title, 120)) . '</a>';
        $output .= '      </h3>';
        $output .= '      <div class="biblio-author-line">';
        $output .= '        <span class="author-prefix">' . __('Penulis:') . '</span> ';
        $output .= '        <span class="author-names">' . $_authors_string . '</span>';
        $output .= '      </div>';
        $output .= '    </div>';

        // 2. Metadata Grid (Clean Key-Value Pairs, High Clarity)
        $output .= '    <div class="biblio-meta-grid">';
        if ($call_number !== '' && $call_number !== '-') {
            $output .= '      <div class="meta-item">';
            $output .= '        <span class="meta-lbl">' . __('No. Panggil:') . '</span>';
            $output .= '        <span class="meta-val meta-callnum">' . htmlspecialchars($call_number) . '</span>';
            $output .= '      </div>';
        }
        if ($pub_val !== '') {
            $output .= '      <div class="meta-item">';
            $output .= '        <span class="meta-lbl">' . __('Penerbit & Tahun:') . '</span>';
            $output .= '        <span class="meta-val">' . $pub_val . '</span>';
            $output .= '      </div>';
        }
        if ($collation !== '' && $collation !== '-') {
            $output .= '      <div class="meta-item">';
            $output .= '        <span class="meta-lbl">' . __('Deskripsi Fisik:') . '</span>';
            $output .= '        <span class="meta-val">' . htmlspecialchars($collation) . '</span>';
            $output .= '      </div>';
        }
        if ($isbn_issn !== '' && $isbn_issn !== '-') {
            $output .= '      <div class="meta-item">';
            $output .= '        <span class="meta-lbl">' . __('ISBN/ISSN:') . '</span>';
            $output .= '        <span class="meta-val font-mono">' . htmlspecialchars($isbn_issn) . '</span>';
            $output .= '      </div>';
        }
        if ($edition !== '' && $edition !== '-') {
            $output .= '      <div class="meta-item">';
            $output .= '        <span class="meta-lbl">' . __('Edisi:') . '</span>';
            $output .= '        <span class="meta-val">' . htmlspecialchars($edition) . '</span>';
            $output .= '      </div>';
        }
        if ($series !== '' && $series !== '-') {
            $output .= '      <div class="meta-item">';
            $output .= '        <span class="meta-lbl">' . __('Judul Seri:') . '</span>';
            $output .= '        <span class="meta-val">' . htmlspecialchars($series) . '</span>';
            $output .= '      </div>';
        }
        $output .= '    </div>';

        // 3. Synopsis snippet (if available)
        if (!empty($notes)) {
            $output .= '    <div class="biblio-synopsis-clean">';
            $output .= htmlspecialchars($notes);
            $output .= '    </div>';
        }

        // 4. Footer Bar: Status (Left) & Actions (Right)
        $output .= '    <div class="biblio-footer-bar">';
        
        // Availability Status
        $output .= '      <div class="biblio-status-wrap">';
        if ($is_available) {
            $output .= '        <span class="status-badge is-avail">';
            $output .= '          <span class="status-dot"></span> ' . __('Tersedia di rak') . ' (<strong>' . $availability . '</strong> ' . __('Eksemplar') . ')';
            $output .= '        </span>';
        } else {
            $output .= '        <span class="status-badge is-unavail">';
            $output .= '          <span class="status-dot"></span> ' . __('Sedang Dipinjam') . ' (0 ' . __('Tersedia') . ')';
            $output .= '        </span>';
        }
        $output .= '      </div>';

        // Action Buttons
        $output .= '      <div class="biblio-actions-wrap">';
        
        // Tool group (Bookmark, Cite, Share)
        $output .= '        <div class="tool-icons-group">';
        $output .= '          <a href="javascript:void(0)" data-id="' . $biblio_id . '" class="bookMarkBook clean-tool-btn ' . $bookmarkClass . '" title="' . $bookmarkTitle . '">';
        $output .= '            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>';
        $output .= '            <span id="label-' . $biblio_id . '" class="sr-only">' . $bookmarkLabel . '</span>';
        $output .= '          </a>';
        $output .= '          <a href="' . $cite_url . '" class="openPopUp citationLink clean-tool-btn" title="' . __('Kutip / Sitasi') . '" target="_blank">';
        $output .= '            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>';
        $output .= '          </a>';
        $output .= '          <a href="javascript:void(0)" data-id="' . $biblio_id . '" data-title="' . $raw_title . '" data-toggle="modal" data-target="#mediaSocialModal" class="clean-tool-btn" title="' . __('Bagikan') . '">';
        $output .= '            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>';
        $output .= '          </a>';
        $output .= '        </div>';

        // Cart button
        $output .= '        <button type="button" class="btn-clean-cart add-to-chart-button" data-biblio="' . $biblio_id . '" title="' . __('Tambah ke Keranjang') . '">';
        $output .= '          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>';
        $output .= '          <span>+ Keranjang</span>';
        $output .= '        </button>';

        // Detail button
        $output .= '        <a href="' . $detail_url . '" class="btn-clean-detail">';
        $output .= '          <span>' . __('Lihat Detail') . '</span>';
        $output .= '          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>';
        $output .= '        </a>';

        $output .= '      </div>'; // .biblio-actions-wrap
        $output .= '    </div>';   // .biblio-footer-bar

        $output .= '  </div>';     // .biblio-card-body
        $output .= '</article>';

    else:

        // ── Render Clean Grid Item Card ─────────────────────────────────────
        $availBadgeGrid = $is_available ? 'grid-badge-avail' : 'grid-badge-unavail';
        $availGridText  = $is_available ? $availability . ' Eks' : 'Dipinjam';

        $output .= '<div class="col-12 col-sm-6 col-md-4 col-lg-3 px-2 mb-3 grid-item">';
        $output .= '  <div class="card biblio-grid-card h-100">';
        
        // Media Cover Area
        $output .= '    <div class="grid-card-media position-relative text-center p-3">';
        $output .= '      <a href="' . $detail_url . '" class="d-inline-block position-relative" title="' . $raw_title . '">';
        $output .= '        <img loading="lazy" src="' . $thumb_url . '" alt="' . $raw_title . '" class="img-fluid grid-card-thumb rounded ' . ($is_available ? '' : 'is-borrowed') . '"/>';
        $output .= '      </a>';

        // Stock Badge Tag
        $output .= '      <span class="grid-stock-tag ' . $availBadgeGrid . '">' . $availGridText . '</span>';
        $output .= '    </div>';

        // Grid Card Body
        $output .= '    <div class="card-body p-3 d-flex flex-column justify-content-between">';
        $output .= '      <div>';
        if ($call_number !== '' && $call_number !== '-') {
            $output .= '        <span class="grid-call-badge mb-1.5 d-inline-block">' . htmlspecialchars($call_number) . '</span>';
        }
        $output .= '        <h3 class="grid-item-title mb-1">';
        $output .= '          <a href="' . $detail_url . '" title="' . $raw_title . '">' . htmlspecialchars(addEllipsis($title, 70)) . '</a>';
        $output .= '        </h3>';
        $output .= '        <p class="grid-item-author text-muted text-xs mb-2">';
        $output .= '          ' . htmlspecialchars(addEllipsis($_first_author, 40));
        $output .= '        </p>';
        $output .= '      </div>';

        // Grid Card Footer Buttons
        $output .= '      <div class="grid-item-footer pt-2.5 mt-2 border-top d-flex align-items-center gap-2">';
        $output .= '        <a href="' . $detail_url . '" class="btn btn-sm btn-outline-dark flex-fill">' . __('Detail') . '</a>';
        $output .= '        <button type="button" class="btn btn-sm btn-dark add-to-chart-button" data-biblio="' . $biblio_id . '" title="' . __('Tambah ke Keranjang') . '">';
        $output .= '          <i class="fas fa-cart-plus" aria-hidden="true"></i>';
        $output .= '        </button>';
        $output .= '      </div>';

        $output .= '    </div>'; // -- close card-body
        $output .= '  </div>';   // -- close card
        $output .= '</div>';     // -- close col

    endif;

    return $output;
}

/**
 * Retrieve notes for a given biblio ID
 */
function getNotes($dbs, $biblio_id)
{
    $query = $dbs->query('SELECT notes FROM biblio WHERE biblio_id = ' . (int)$biblio_id);
    $data = $query ? $query->fetch_row() : null;
    return addEllipsis($data[0] ?? '', 260);
}

/**
 * Helper to add ellipsis to strings
 */
function addEllipsis($string, $length, $end = '…')
{
    if (strlen($string ?? '') > $length) {
        $length -= strlen($end);
        $string  = substr($string, 0, $length);
        $string .= $end;
    }
    return $string;
}

/**
 * Retrieve availability count for a given biblio ID
 */
function getAvailability($dbs, $biblio_id, $sysconf)
{
    $_item_q = $dbs->query('SELECT COUNT(*) FROM item WHERE biblio_id=' . (int)$biblio_id);
    $_item_c = $_item_q ? $_item_q->fetch_row() : [0];

    $_borrowed_q = $dbs->query('SELECT COUNT(*) FROM loan AS l INNER JOIN item AS i'
        . ' ON l.item_code=i.item_code WHERE l.is_lent=1 AND l.is_return=0 AND i.biblio_id=' . (int)$biblio_id);
    $_borrowed_c = $_borrowed_q ? $_borrowed_q->fetch_row() : [0];

    $_total_avail = (int)($_item_c[0] ?? 0) - (int)($_borrowed_c[0] ?? 0);
    return max(0, $_total_avail);
}

/**
 * Legacy createButton helper preserved for backward compatibility
 */
function createButton(int $biblio_id, string $title)
{
    $commentUrlCondition = (utility::isMemberLogin() ? 
        Url::getSlimsBaseUri('?p=show_detail&id=' . $biblio_id . '#comment') : 
        Url::getSlimsBaseUri('?p=member&destination=' . Url::getSlimsBaseUri('?p=show_detail&id=' . $biblio_id . '#comment')->encode()));

    list($comment, $bookmark, $share) = [__('Comment'), (in_array($biblio_id, $_SESSION['bookmark'] ?? []) ? __('Bookmarked') : __('Bookmark')), __('Share')];
    $setBookmarked = trim(isset($_SESSION['bookmark'][$biblio_id]) ? 'bg-success text-white rounded-lg' : 'text-secondary');

    return <<<HTML
    <div class="d-flex flex-row text-xs my-1">
        <a href="{$commentUrlCondition}" style="cursor: pointer" class="text-decoration-none text-secondary font-weight-bolder mr-1 px-2 py-1">
            <i class="fas fa-comment-dots mr-1" aria-hidden="true"></i> {$comment}
        </a>
        <a href="javascript:void(0)" style="cursor: pointer" data-id="{$biblio_id}" class="bookMarkBook text-decoration-none font-weight-bolder mr-1 px-2 py-1 {$setBookmarked}">
            <i class="fas fa-bookmark mr-1" aria-hidden="true"></i> <label id="label-{$biblio_id}" style="cursor: pointer !important" class="m-0">{$bookmark}</label>
        </a>
        <a href="javascript:void(0)" style="cursor: pointer" data-id="{$biblio_id}" data-title="{$title}" data-toggle="modal" data-target="#mediaSocialModal" class="text-decoration-none text-secondary font-weight-bolder mr-1 px-2 py-1">
            <i class="fas fa-share-alt mr-1" aria-hidden="true"></i> {$share}
        </a>
    </div>
HTML;
}
