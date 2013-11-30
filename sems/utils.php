<?php

function sems_time() {
  $date = sems_config('date');
  if (!empty($date)) return strtotime($date);
  return time();
}

function sems_datetime($date) {
  $time = strtotime($date);
  return date('j F Y', $time) . ' at ' . date('H:i', $time);
}
