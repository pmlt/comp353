<?php

function sems_event_papers_url($cid, $eic) { return sems_event_url($cid, $eid)."/papers"; }
function sems_event_papers($cid, $eid) {
  // XXX
}

function sems_event_paper_url($cid, $eic, $pid) { return sems_event_papers_url($cid, $eid)."/{$pid}"; }
function sems_event_paper($cid, $eid, $pid) {
  // XXX
}

function sems_event_reviews_url($cid, $eic) { return sems_event_url($cid, $eid)."/reviews"; }
function sems_event_reviews($cid, $eid) {
  // XXX
}

function sems_event_review_url($cid, $eic, $rid) { return sems_event_reviews_url($cid, $eid)."/{$rid}"; }
function sems_event_review($cid, $eid, $rid) {
  // XXX
}
