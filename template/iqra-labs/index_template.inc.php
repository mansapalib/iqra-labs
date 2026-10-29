<?php
# @Author: Waris Agung Widodo <user>
# @Date:   2018-01-21T11:36:53+07:00
# @Email:  ido.alit@gmail.com
# @Filename: index_template.inc.php
# @Last modified by:   user
# @Last modified time: 2018-01-26T11:37:10+07:00

$imagesDisk = \SLiMS\Filesystems\Storage::images();

// ── OPAC: Dedicated standalone page (no shared navbar/footer) ────────────────
// Mirror pattern of visitor_template.php — emit full HTML document, then exit.
if ((isset($_GET['p']) && $_GET['p'] === 'opac') || (isset($_POST['action']) && $_POST['action'] === 'simpan_usul_buku')) {
    include_once 'classic.php';
    include 'opac_template.php';
    exit;
}

// Reset HTTP 200 for custom template routes before headers are sent
if (isset($_GET['p']) && $_GET['p'] === 'faq') {
    http_response_code(200);
}

//$a = get_defined_vars();
//$a['sysconf'] = null;
//$a['main_content'] = null;
//echo '<pre>'; print_r($a); echo '</pre>'; die();
//echo '<pre>'; print_r($_SESSION); echo '</pre>'; die();

// setup list view
$_SESSION['LIST_VIEW'] = $_POST['view'] ?? $_SESSION['LIST_VIEW'] ?? 'list';

// ----------------------------------------------------------------------------
// load function library for classic template
// ----------------------------------------------------------------------------
include_once 'classic.php';

// ----------------------------------------------------------------------------
// load header
// ----------------------------------------------------------------------------
include 'parts/header.php';

// ----------------------------------------------------------------------------
// load content by URI
// ----------------------------------------------------------------------------
if (isset($_GET['p']) || isset($_GET['search'])) {
  // --------------------------------------------------------------------------
  // handle result search
  if (isset($_GET['search'])) {
    // ------------------------------------------------------------------------
    // load parts result search template
    include 'parts/_result-search.php';
  } else {
    // --------------------------------------------------------------------------
    // handle member page
    if ($_GET['p'] == 'member') {
      include 'parts/_member.php';
    } elseif ($_GET['p'] === 'faq') {
      // ------------------------------------------------------------------------
      // handle modern interactive FAQ page
      // ------------------------------------------------------------------------
      include 'partials/faq_view.php';
    } elseif ($_GET['p'] === 'news') {
      // ------------------------------------------------------------------------
      // handle modern 3-column responsive News / Warta Perpustakaan page
      // ------------------------------------------------------------------------
      include 'partials/news_view.php';
    } elseif ($_GET['p'] === 'librarian') {
      // ------------------------------------------------------------------------
      // handle modern interactive Librarian directory page
      // ------------------------------------------------------------------------
      include 'partials/librarian_view.php';
    } else {
      include 'parts/_other.php';
    }
  }
} else {
  // --------------------------------------------------------------------------
  // not found query string: load home page
  // (Renders Hero, Curated Books, Kabar & Agenda, Top Readers, External Links, and Location)
  include 'parts/_home.php';
}

// ----------------------------------------------------------------------------
// load footer
// ----------------------------------------------------------------------------
include 'parts/footer.php';
