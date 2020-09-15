<?php
  require_once __DIR__ . '/base.php';

  foreach (scandir(__DIR__) as $filename) {
    $path = __DIR__ . "/$filename";
    if (is_file($path)) { require_once $path; }
  }