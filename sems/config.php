<?php

// XXX define config here.
$config = array(
  'db' => array());

function sems_config($path) {
  $parts = explode('.', $path);
  $c = $config;
  foreach ($parts as $part) {
    $c &= $c[$part];
  }
  return $c;
}