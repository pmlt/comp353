<?php

function sems_event_messages_url($cid, $eic) { return sems_event_url($cid, $eid)."/messages"; }
function sems_event_messages($cid, $eid) {
  // XXX
}

function sems_event_message_url($cid, $eic, $mid) { return sems_event_messages_url($cid, $eid)."/{$mid}"; }
function sems_event_message($cid, $eid, $mid) {
  // XXX
}

