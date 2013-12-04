<?php

$config = array(
  //'date' => '2014-01-01 12:00:00', //Term period
  //'date' => '2014-01-02 12:00:00', //Submission period
  //'date' => '2014-01-03 12:00:00', //Auction period
  //'date' => '2014-01-04 12:00:00', //Review period
  //'date' => '2014-01-05 12:00:00', //Decision period
  'date' => '2014-01-06 12:00:00', //Publish period
  //'date' => '2014-01-10 12:00:00', //Meet period
  //'date' => '2014-01-13 12:00:00', //Post-meet period

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
