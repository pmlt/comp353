<?php

$config = array(

  'date' => '2013-12-06 12:00:00',
  //'date' => '2013-12-07 12:00:00',
  //'date' => '2013-12-08 12:00:00',
  //'date' => '2013-12-09 12:00:00',
  //'date' => '2013-12-10 12:00:00',
  //'date' => '2013-12-11 12:00:00',
  //'date' => '2013-12-12 12:00:00',
  //'date' => '2013-12-13 12:00:00',
  //'date' => '2013-12-14 12:00:00',
  //'date' => '2013-12-15 12:00:00',

  'db' => array(
    'host' => 'clipper.encs.concordia.ca',
    'db'   => 'nyc353_2',
    'user' => 'nyc353_2',
    'pwd'  => 'e99Yd4'
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
