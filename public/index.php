<?php

require_once('../config.php');
require_once('../library/icon.php');
require_once('../library/sqlite.php');

$query = isset($_GET['q'])  ? $_GET['q'] : false;
$ns    = isset($_GET['ns']) ? preg_replace('/[^a-zA-Z0-9]+/', '', $_GET['ns']) : false;
$tx    = isset($_GET['tx']) ? preg_replace('/[^a-zA-Z0-9]+/', '', $_GET['tx']) : false;
$page  = (int) isset($_GET['page']) ? $_GET['page'] : 0;

if (SEF_MODE && isset($_SERVER['QUERY_STRING'])) {

  $q = explode('/', $_SERVER['QUERY_STRING']);

  if (isset($q[1])) {
    if (strlen($q[1]) == 34) {
      $ns = preg_replace('/[^a-zA-Z0-9]+/', '', $q[1]);
    } else if (strlen($q[1]) > 34) {
      $tx = preg_replace('/[^a-zA-Z0-9]+/', '', $q[1]);
    } else {
      $page = (int) $q[1];
    }
  }

  if (isset($q[2])) {
    if (strlen($q[2]) == 34) {
      $ns = preg_replace('/[^a-zA-Z0-9]+/', '', $q[2]);
    } else {
      $page = (int) $q[2];
    }
  }
}

if ($page > 0) {
  $limit = PAGE_LIMIT * $page - PAGE_LIMIT;
} else {
  $limit = PAGE_LIMIT * $page;
}

$db = new SQLite(DB_NAME, DB_USERNAME, DB_PASSWORD);

if ($ns) {
  $namespaceValue = $db->getNamespaceName($ns);
} else {
  $namespaceValue = false;
}

$data = [];
foreach ($db->getData($ns, $tx, $query, $limit, PAGE_LIMIT) as $value) {
  $data[] = [
    'namehash' => $value['namehash'],
    'block'    => $value['block'],
    'txid'     => $value['txid'],
    'time'     => date('d-m-Y H:i', $value['time']),
    'key'      => nl2br(trim($value['key'])),
    'value'    => nl2br(trim($value['value'])),
  ];
}

if (SEF_MODE) {

  if (in_array($page, [0, 1])) {
    $newer = false;
  } else {
    if ($page == 2) {
      $newer = ($ns ? $ns : '');
    } else {
      $newer = ($ns ? $ns . '/' . ($page - 1) : ($page - 1));
    }
  }

  if ($data) {
    if (in_array($page, [0, 1])) {
      $older = ($ns ? $ns . '/2' : '/2');
    } else {
      $older = ($ns ? $ns . '/' . ($page + 1) : '/' . ($page + 1));
    }
  } else {
    $older = false;
  }

} else {

  if (in_array($page, [0, 1])) {
    $newer = false;
  } else {
    if ($page == 2) {
      $newer = ($ns ? '?ns=' . $ns : ($query ? '?q=' . $query : ''));
    } else {
      $newer = ($ns ? '?ns=' . $ns . '&page=' . ($page - 1) : '?page=' . ($page - 1) . ($query ? '&q=' . $query : ''));
    }
  }

  if ($data) {
    if (in_array($page, [0, 1])) {
      $older = ($ns ? '?ns=' . $ns . '&page=2' : '?page=2' . ($query ? '&q=' . $query : ''));
    } else {
      $older = ($ns ? '?ns=' . $ns . '&page=' . ($page + 1) : '?page=' . ($page + 1) . ($query ? '&q=' . $query : ''));
    }
  } else {
    $older = false;
  }
}

if ($ns) {
  if ($page) {
    $hrefThisPage = $ns . '/' . $page;
  } else {
    $hrefThisPage = $ns;
  }
} else {
  if ($page) {
    $hrefThisPage = $page;
  } else {
    $hrefThisPage = '';
  }
}

require_once('index.phtml');
