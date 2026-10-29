<?php
/**
 * @Created by          : Waris Agung Widodo (ido.alit@gmail.com)
 * @Date                : 2019-01-29 10:43
 * @File name           : _other.php
 */

// Initialize variables for template scope and static analysis
$page_title   = $page_title ?? ($sysconf['library_name'] ?? 'Informasi');
$main_content = $main_content ?? '';
?>

<div class="result-search pb-5">
    <section id="section1 container-fluid">
        <div class="c-header">
            <div class="mask"></div>
          <?php
          // ----------------------------------------------------------------------
          // include navbar part
          // ----------------------------------------------------------------------
          include '_navbar.php'; ?>
        </div>
      <?php
      // ------------------------------------------------------------------------
      // include search form part (excluded on main menu pages: help, libinfo, news, faq, show_detail, profil, tata-tertib, librarian)
      // ------------------------------------------------------------------------
      $excluded_search_pages = ['help', 'libinfo', 'news', 'faq', 'show_detail', 'profil', 'tata-tertib', 'librarian'];
      if (!in_array($_GET['p'] ?? '', $excluded_search_pages)) {
          include '_search-form.php';
      }
      ?>
    </section>

    <section class="container mt-8">
      <?php
      if ($_GET['p'] === 'faq') {
        include __DIR__ . '/../partials/faq_view.php';
      } elseif ($_GET['p'] === 'news') {
        include __DIR__ . '/../partials/news_view.php';
      } elseif ($_GET['p'] === 'librarian') {
        include __DIR__ . '/../partials/librarian_view.php';
      } elseif ($_GET['p'] !== 'show_detail') {
        echo '<h2 class="mb-4">' . $page_title . '</h2><hr>';
        echo $main_content;
      } else {
        echo $main_content;
      }
      ?>
    </section>
</div>
