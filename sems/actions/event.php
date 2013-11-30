<?php

function sems_event_url($cid,$eid) { return SEMS_ROOT."/{$cid}/{$eid}"; }
function sems_event($cid, $eid) {
  return sems_db(function($db) use($cid,$eid) {
    $event = get_event($db, $cid, $eid);
    if (!$event) return sems_notfound();

    $vars = array();
    $vars['event'] = $event;
    
    //Get the event chair
    $chair = sems_create_identity($db, $event['chair_id']);
    $vars['chair'] = $chair;

    return ok(sems_smarty_fetch('event/index.tpl', $vars));
  });
}

function get_event($db, $cid, $eid) {
  return srow($db, "SELECT * FROM Event WHERE conference_id=? AND event_id=?", array($cid,$eid));
}

$event_states = array(
  'init' => 'Initial preparations underway.',
  'term' => 'In term.',
  'term_ended' => 'Will soon accept paper submissions.',
  'submit' => 'Accepting paper submissions.',
  'submit_ended' => 'Will soon begin auction for paper reviews.',
  'auction' => 'Auctioning paper reviews.',
  'auction_ended' => 'Will soon begin paper reviews.',
  'review' => 'Reviewing submitted papers.',
  'review_ended' => 'Will soon announce final decisions.',
  'decision' => 'Final decisions are in.',
  'meet' => 'Currently underway.',
  'ended' => 'Event over. Thank you for coming!');

function sems_event_state($event) {
  $time = sems_time();
  if ($time < strtotime($event['term_start_date'])) return 'init';
  if ($time < strtotime($event['term_end_date'])) return 'term';
  if ($time < strtotime($event['submit_start_date'])) return 'term_ended';
  if ($time < strtotime($event['submit_end_date'])) return 'submit';
  if ($time < strtotime($event['auction_start_date'])) return 'submit_ended';
  if ($time < strtotime($event['auction_end_date'])) return 'auction';
  if ($time < strtotime($event['review_start_date'])) return 'auction_ended';
  if ($time < strtotime($event['review_end_date'])) return 'review';
  if ($time < strtotime($event['decision_date'])) return 'review_ended';
  if ($time < strtotime($event['start_date'])) return 'decision';
  if ($time < strtotime($event['end_date'])) return 'meet';
  return 'ended';
}

function sems_event_state_str($state) {
  global $event_states;
  return $event_states[$state];
}
