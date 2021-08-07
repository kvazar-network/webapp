<?php

require_once('../library/icon.php');

if (isset($_GET['hash'])) {

  $icon = new Icon();

  header("Content-Type: image/jpeg");
  echo $icon->generateImageResource(md5($_GET['hash']), 60, 60, false);
}
