<?php

require_once('../config.php');
require_once('../library/icon.php');

if (isset($_GET['hash'])) {

  header("Content-Type: image/jpeg");

  if (CACHE_ENABLED) {

    $filename = dirname(__FILE__) . '/../cache/' . $_GET['hash'] . '.jpeg';

    if (!file_exists($filename)) {

      $icon = new Icon();

      file_put_contents($filename, $icon->generateImageResource(md5($_GET['hash']), 60, 60, false));
    }

    echo file_get_contents($filename);

  } else {

    $icon = new Icon();

    echo $icon->generateImageResource(md5($_GET['hash']), 60, 60, false);
  }
}
