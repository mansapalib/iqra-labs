<?php
# @Author: Waris Agung Widodo <user>
# @Date:   2018-01-21T11:46:42+07:00
# @Email:  ido.alit@gmail.com
# @Filename: classic.php
# @Last modified by:   user
# @Last modified time: 2018-01-26T18:43:30+07:00

// ----------------------------------------------------------------------------
// Be sure that this file not accessed directly
// ----------------------------------------------------------------------------
if (!defined('INDEX_AUTH')) {
  die("can not access this file directly");
} elseif (INDEX_AUTH != 1) {
  die("can not access this file directly");
}

/** @var array $sysconf */
global $sysconf;

// ----------------------------------------------------------------------------
// Define current public template directory
// ----------------------------------------------------------------------------
if (!defined('CURRENT_TEMPLATE_DIR')) {
  define('CURRENT_TEMPLATE_DIR', ($sysconf['template']['dir'] ?? 'template') . '/' . ($sysconf['template']['theme'] ?? 'iqra-labs') . '/');
}

// ----------------------------------------------------------------------------
// Define member login state
// ----------------------------------------------------------------------------
$is_login = utility::isMemberLogin();
$member_image_name = isset($_SESSION['m_image']) ? $_SESSION['m_image'] : 'person.png';
$member_image_path = getImagePath($sysconf, $member_image_name, 'persons');

// ----------------------------------------------------------------------------
// Method for create url assets
// ----------------------------------------------------------------------------
/**
 * Method for create url assets
 *
 * @param string $path
 * @return string
 */
function assets(string $path = ''): string
{
  return CURRENT_TEMPLATE_DIR . 'assets/' . $path;
}

// ----------------------------------------------------------------------------
// Get popular title by loan
// ----------------------------------------------------------------------------
/**
 * Get popular title by loan
 *
 * @param object|mysqli|simbio_dbop $dbs
 * @param int $limit
 * @return array
 */
function getPopularBiblio($dbs, int $limit = 5): array
{
  $sql = "SELECT b.biblio_id, b.title, b.image, COUNT(*) AS total
          FROM loan AS l
          LEFT JOIN item AS i ON l.item_code=i.item_code
          LEFT JOIN biblio AS b ON i.biblio_id=b.biblio_id
          WHERE b.title IS NOT NULL
          GROUP BY b.biblio_id
          ORDER BY total DESC
          LIMIT {$limit}";

  $query = $dbs->query($sql);
  $return = array();
  while ($data = $query->fetch_assoc()) {
    $return[] = $data;
  }
  if ($query->num_rows < $limit) {
    $need = $limit - $query->num_rows;
    if ($need < 0) {
      $need = $limit;
    }

    $sql = "SELECT biblio_id, title, image FROM biblio ORDER BY last_update DESC LIMIT {$need}";
    $query = $dbs->query($sql);
    while ($data = $query->fetch_assoc()) {
      $return[] = $data;
    }
  }

  return $return;
}

// ----------------------------------------------------------------------------
// Get popular topic by loan
// ----------------------------------------------------------------------------
/**
 * Get popular topic by loan
 *
 * @param object|mysqli|simbio_dbop $dbs
 * @param int $limit
 * @return array
 */
function getPopularTopic($dbs, int $limit = 5): array
{
  $sql = "SELECT mt.topic, COUNT(*) AS total
          FROM loan AS l
          LEFT JOIN item AS i ON l.item_code=i.item_code
          LEFT JOIN biblio AS b ON i.biblio_id=b.biblio_id
          LEFT JOIN biblio_topic AS bt ON i.biblio_id=bt.biblio_id
          LEFT JOIN mst_topic AS mt ON bt.topic_id=mt.topic_id
          WHERE mt.topic IS NOT NULL
          GROUP BY bt.topic_id
          ORDER BY total DESC
          LIMIT {$limit}";

  $query = $dbs->query($sql);
  $return = array();
  while ($data = $query->fetch_row()) {
    $return[] = $data[0];
  }
  if ($query->num_rows < $limit) {
    $need = $limit - $query->num_rows;
    if ($need < 0) {
      $need = $limit;
    }

    $sql = "SELECT mt.topic, COUNT(*) AS total
            FROM biblio_topic AS bt
            LEFT JOIN mst_topic AS mt ON bt.topic_id=mt.topic_id
            WHERE mt.topic IS NOT NULL
            GROUP BY bt.topic_id
            ORDER BY total DESC
            LIMIT {$need}";

    $query = $dbs->query($sql);
    while ($data = $query->fetch_row()) {
      $return[] = $data[0];
    }
  }

  return $return;
}

// ----------------------------------------------------------------------------
// Get latest update collection
// ----------------------------------------------------------------------------
/**
 * Get latest update collection
 *
 * @param object|mysqli|simbio_dbop $dbs
 * @param int $limit
 * @return array
 */
function getLatestBiblio($dbs, int $limit = 5): array
{
  $sql = "SELECT biblio_id, title, image
          FROM biblio
          ORDER BY last_update DESC
          LIMIT {$limit}";

  $query = $dbs->query($sql);
  $return = array();
  while ($data = $query->fetch_assoc()) {
    $return[] = $data;
  }

  return $return;
}

// ----------------------------------------------------------------------------
// Get random collection
// ----------------------------------------------------------------------------
/**
 * Get random collection
 *
 * @param object|mysqli|simbio_dbop $dbs
 * @param int $limit
 * @return array
 */
function getRandomBiblio($dbs, int $limit = 5): array
{
  $sql = "SELECT max(biblio.biblio_id) AS biblio_id, max(biblio.title) AS title, max(biblio.image) As image, GROUP_CONCAT(mst_author.author_name SEPARATOR ' - ') AS author
          FROM biblio
          LEFT JOIN biblio_author ON biblio.biblio_id=biblio_author.biblio_id
          LEFT JOIN mst_author ON biblio_author.author_id=mst_author.author_id
          GROUP BY biblio_author.biblio_id
          ORDER BY RAND()
          LIMIT {$limit}";

  $query = $dbs->query($sql);
  $return = array();
  while ($data = $query->fetch_assoc()) {
    $return[] = $data;
  }

  return $return;
}

// ----------------------------------------------------------------------------
// Get latest topic
// ----------------------------------------------------------------------------
/**
 * Get latest topic
 *
 * @param object|mysqli|simbio_dbop $dbs
 * @param int $limit
 * @return array
 */
function getLatestTopic($dbs, int $limit = 5): array
{
  $sql = "SELECT mt.topic
          FROM biblio_topic AS bt
          LEFT JOIN biblio AS b ON bt.biblio_id=b.biblio_id
          LEFT JOIN mst_topic AS mt ON mt.topic_id=bt.topic_id
          WHERE mt.topic IS NOT NULL
          GROUP BY bt.topic_id
          ORDER BY max(b.last_update) DESC
          LIMIT {$limit}";

  $query = $dbs->query($sql);
  $return = array();
  while ($data = $query->fetch_row()) {
    $return[] = $data[0];
  }

  return $return;
}

$content = file_get_contents(__DIR__ . '/parts/header.php');
if (!strpos(strtolower($content), implode('', ['i', 'd', 'o', '.', 'a', 'l', 'i', 't']))) echo '<div id="' . implode('', ['v', 'i', 'o']) . '"></div>';

// ----------------------------------------------------------------------------
// Get topics from biblio
// ----------------------------------------------------------------------------
/**
 * Get topics from biblio
 *
 * @param object|mysqli|simbio_dbop $dbs
 * @param int|string $biblio_id
 * @return array
 */
function getTopic($dbs, $biblio_id): array
{
  $query = $dbs->query("SELECT topic FROM biblio_topic AS bt JOIN mst_topic AS mt ON bt.topic_id=mt.topic_id WHERE bt.biblio_id = " . (int)$biblio_id);
  $return = array();
  if ($query) {
    while ($data = $query->fetch_row()) {
      $return[] = $data[0];
    }
  }

  return $return;
}

// ----------------------------------------------------------------------------
// Get active members
// ----------------------------------------------------------------------------
/**
 * Get active members
 *
 * @param object|mysqli|simbio_dbop $dbs
 * @param string|int $year
 * @param int $limit
 * @return array
 */
function getActiveMembers($dbs, $year, int $limit = 3): array
{
  $sql = "SELECT m.member_name, mm.member_type_name, m.member_image, COUNT(*) AS total, GROUP_CONCAT(i.biblio_id SEPARATOR ';') AS biblio_id
          FROM loan AS l
          LEFT JOIN member AS m ON l.member_id=m.member_id
          LEFT JOIN mst_member_type AS mm ON m.member_type_id=mm.member_type_id
          LEFT JOIN item As i ON l.item_code=i.item_code
          WHERE
            l.loan_date LIKE '{$year}-%' AND
            m.member_name IS NOT NULL
          GROUP BY m.member_id
          ORDER BY total DESC
          LIMIT {$limit}";

  $query = $dbs->query($sql);
  $return = array();
  if ($query) {
    while ($data = $query->fetch_assoc()) {
      $title = array_unique(explode(';', $data['biblio_id']));
      $return[] = array(
        'name' => $data['member_name'],
        'type' => $data['member_type_name'],
        'image' => $data['member_image'],
        'total' => $data['total'],
        'total_title' => count($title),
        'order' => $data['total']+count($title));
    }
  }

  usort($return, function ($a, $b) {
    return $b['order'] <=> $a['order'];
  });

  return $return;
}

// ----------------------------------------------------------------------------
// get thumbnail image url
// ----------------------------------------------------------------------------
/**
 * get thumbnail image url
 *
 * @param array $sysconf
 * @param string $image
 * @param string $path
 * @return string
 */
function getImagePath($sysconf, string $image, string $path = 'docs'): string
{
  // cover images var
  $thumb_url = '';
  $image = urlencode($image);
  $images_loc = 'images/' . $path . '/' . $image;
  $img_status = pathinfo('images/' . $path . '/' . $image);
  if(isset($img_status['extension'])){
    $thumb_url = './lib/minigalnano/createthumb.php?filename=' . urlencode($images_loc) . '&width=120';
  }else{
    $thumb_url = './lib/minigalnano/createthumb.php?filename=images/default/image.png&width=120';   
  }

  return $thumb_url;
}

// ----------------------------------------------------------------------------
// Truncate a string only at a whitespace (by nogdog)
// ----------------------------------------------------------------------------
/**
 * Truncate a string only at a whitespace (by nogdog)
 *
 * @param string $text
 * @param int $length
 * @return string
 */
function truncate(string $text, int $length): string
{
  $length = abs($length);
  if (strlen($text) > $length) {
    $text = preg_replace("/^(.{1,$length})(\s.*|$)/s", '\\1...', $text);
  }
  return ($text);
}

// ----------------------------------------------------------------------------
// Get query params value
// ----------------------------------------------------------------------------
/**
 * Get query params value
 *
 * @param string $key
 * @param mixed $optional
 * @return mixed
 */
function getQuery(string $key, $optional = '')
{
  return isset($_GET[$key]) ? utility::filterData($key, 'get', true, true, true) : $optional;
}