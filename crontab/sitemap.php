<?php

require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/../library/mysql.php');

$db = new MySQL();

// Generate url sets
$transaction  = '<?xml version="1.0" encoding="UTF-8"?>';
$transaction .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';

$namespace    = '<?xml version="1.0" encoding="UTF-8"?>';
$namespace   .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';

$namespaces   = [];
$transactions = [];

foreach ($db->getData(false, false, false, 0, 1000000) as $value) {

  if (!in_array($value['namehash'], $namespaces)) {
    $namespace .= '<loc>' . BASE_URL . '/' . $value['namehash'] . '</loc>';
  }


  if (!in_array($value['namehash'], $transactions)) {
    $transaction .= '<loc>' . BASE_URL . '/' . $value['txid'] . '</loc>';
  }

  $namespaces[]   = $value['namehash'];
  $transactions[] = $value['txid'];
}

$namespace   .= '</urlset>';
$transaction .= '</urlset>';

$handle = fopen(dirname(__FILE__) . '/../public/sitemap.transaction.xml', 'w');
fwrite($handle, $transaction);
fclose($handle);


$handle = fopen(dirname(__FILE__) . '/../public/sitemap.namespace.xml', 'w');
fwrite($handle, $namespace);
fclose($handle);


// Sitemap
$sitemap  = '<?xml version="1.0" encoding="UTF-8"?>';
$sitemap .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
$sitemap .= '  <sitemap>';
$sitemap .= '    <loc>' . BASE_URL . '/sitemap.namespace.xml</loc>';
$sitemap .= '  </sitemap>';
$sitemap .= '  <sitemap>';
$sitemap .= '    <loc>' . BASE_URL . '/sitemap.transaction.xml</loc>';
$sitemap .= '  </sitemap>';
$sitemap .= '</sitemapindex>';

$handle = fopen(dirname(__FILE__) . '/../public/sitemap.xml', 'w');
fwrite($handle, $sitemap);
fclose($handle);
