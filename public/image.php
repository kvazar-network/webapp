<?php

require_once('../config.php');
require_once('../library/icon.php');

if (isset($_GET['hash'])) {

  $hash   = md5($_GET['hash']);

  $width  = isset($_GET['width']) ? (int) $_GET['width'] : 60;
  $height = isset($_GET['height']) ? (int) $_GET['height'] : 60;

  $radius = isset($_GET['radius']) ? (int) $_GET['radius'] : 0;

  header("Content-Type: image/png");

  if (CACHE_ENABLED) {

    $filename = dirname(__FILE__) . '/../cache/' . $hash . '.png';

    if (!file_exists($filename)) {

      $icon = new Icon();

      file_put_contents($filename, $icon->generateImageResource($hash, $width, $height, false, $radius));
    }

    echo file_get_contents($filename);

  } else {

    $icon = new Icon();

    echo $icon->generateImageResource($hash, $width, $height, false, $radius);
  }
}
