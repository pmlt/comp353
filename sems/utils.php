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

function sems_topic_hierarchy(array $topics) {
  $h = array();
  foreach ($topics as $t) {
    if (!isset($h[$t['category']])) $h[$t['category']] = array();
    $h[$t['category']][] = $t;
  }
  return $h;
}

function sems_fetch_topics($db) {
  return stable($db, "SELECT * FROM Topic ORDER BY category, name");
}
