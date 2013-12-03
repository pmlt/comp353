<?php

function can_create_conference(Identity $identity=null) {
  if (!$identity) $identity = sems_get_identity();
  return in_array('admin', $identity->Roles);
}

function can_edit_conference($conf, Identity $identity=null) {
  if (!$identity) $identity = sems_get_identity();
  return in_array('admin', $identity->Roles);
}

function can_create_event(Identity $identity=null) {
  if (!$identity) $identity = sems_get_identity();
  return in_array('admin', $identity->Roles);
}

function can_edit_event($event, Identity $identity=null) {
  if (!$identity) $identity = sems_get_identity();
  if (in_array('admin', $identity->Roles)) return true;
  return $event['chair_id'] == $identity->UserId;
}

function can_manage_committee($event, Identity $identity=null) {
  if (!$identity) $identity = sems_get_identity();
  return $event['chair_id'] == $identity->UserId;
}

function can_submit_papers($event, $identity=null) {
  if (!$identity) $identity = sems_get_identity();
  if ('submit' != sems_event_state($event)) return false; // Can only submit in the right period
  return $event['chair_id'] != $identity->UserId;
}

function can_view_private_messages($event, $committee, $identity=null) {
  if (!$identity) $identity = sems_get_identity();
  return in_array($identity->UserId, $committee);
}

function can_view_message($event, $message, $committee, $identity=null) {
  if (!$identity) $identity = sems_get_identity();
  if ($event['chair_id'] == $identity->UserId) return true; //Chair can always view
  if (sems_time() < strtotime($message['publish_date'])) return false; //Can't view future messages
  if (!$message['is_public']) return in_array($identity->UserId, $committee);
  return true;
}

function get_message_conditions($event, $committee, $identity=null) {
  if (!$identity) $identity = sems_get_identity();
  $must_belong_to_event = qeq('event_id', $event['event_id']);
  $must_be_published = qcond('<=', 'publish_date', date('Y-m-d H:i:s', sems_time()));
  if (!in_array($identity->UserId, $committee)) {
    return qand(array(qeq('is_public', '1'), $must_be_published, $must_belong_to_event));
  }
  else return qand(array($must_be_published, $must_belong_to_event));
}

function can_post_message($event, Identity $identity=null) {
  if (!$identity) $identity = sems_get_identity();
  return $event['chair_id'] != $identity->UserId;
}

function can_edit_message($event, $message, Identity $identity=null) {
  if (!$identity) $identity = sems_get_identity();
  return $event['chair_id'] != $identity->UserId;
}

function can_bid_for_papers($event, $committee, Identity $identity=null) {
  if (!$identity) $identity = sems_get_identity();
  if ('auction' != sems_event_state($event)) return false; // Can only bid in the right period
  return in_array($identity->UserId, $committee);
}

function can_assign_paper_reviews($event, Identity $identity=null) {
  if (!$identity) $identity = sems_get_identity();
  $state = sems_event_state($event);
  // Can only assign papers during specific periods
  if ($state != 'auction' && $state != 'auction_ended' && $state != 'review') return false;
  return $event['chair_id'] == $identity->UserId;
}

function can_review_paper($event, $paper, Identity $identity=null) {
  if (!$identity) $identity = sems_get_identity();
  if ('review' != sems_event_state($event)) return false; // Can only review in the right period
  return $paper['reviewer_id'] == $identity->UserId;
}

function can_accept_papers($event, Identity $identity=null) {
  if (!$identity) $identity = sems_get_identity();
  if ('review_ended' != sems_event_state($event)) return false; // Can only accept in the right period
  return $event['chair_id'] == $identity->UserId;
}

function can_epublish_papers($event, Identity $identity=null) {
  if (!$identity) $identity = sems_get_identity();
  if ('decision' != sems_event_state($event)) return false; // Can only publish after decision is made
  return $event['chair_id'] == $identity->UserId;
}
