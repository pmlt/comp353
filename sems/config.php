<?php

$config = array(
  //'date' => '2014-01-11 12:00:00',
  'db' => array(
    'host' => 'localhost',
    'db'   => 'comp353',
    'user' => 'comp353',
    'pwd'  => 'QyurjX7JX}c7:ZYnoT&PhKUFpV'
  ));

function sems_config($path) {
  global $config;
  $parts = explode('.', $path);
  $c = $config;
  foreach ($parts as $part) {
    $c = $c[$part];
  }
  return $c;
}
