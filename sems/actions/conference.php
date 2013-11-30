<?php

function sems_conference_url($cid) { return SEMS_ROOT."/{$cid}"; }
function sems_conference($cid) {
  return sems_db(function($db) use($cid) {
    $conf = get_conference($db, $cid);
    if (!$conf) return sems_notfound();

    $vars = array();
    $vars['conf'] = $conf;

    //Get the program chair
    $chair = sems_create_identity($db, $conf['chair_id']);
    $vars['chair'] = $chair;

    //Get the list of events for this conference
    $vars['events'] = stable($db, "SELECT event_id, title, description, start_date FROM Event WHERE conference_id=? ORDER BY start_date", array($cid));
    return ok(sems_smarty_fetch('conference/index.tpl', $vars));
  });
}

function sems_conference_search($conf) {
  // XXX check if conference exists.
  return sems_notfound();
}

function get_conference($db, $cid) {
  return srow($db, "SELECT * FROM Conference WHERE conference_id=?", array($cid));
}
