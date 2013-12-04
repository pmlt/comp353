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

function can_view_paper($event, $paper, $committee, $identity=null) {
  if (!$identity) $identity = sems_get_identity();
  if ($event['chair_id'] == $identity->UserId) return true; //Chair can always view
  if (in_array($identity->UserId, $committee)) return true; //Committee members can always view
  if (sems_time() < strtotime($message['publish_date'])) return false; //Can't view unpublished papers
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
  return $event['chair_id'] == $identity->UserId;
}

function can_edit_message($event, $message, Identity $identity=null) {
  if (!$identity) $identity = sems_get_identity();
  return $event['chair_id'] == $identity->UserId;
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

function can_review_paper($event, $review, Identity $identity=null) {
  if (!$identity) $identity = sems_get_identity();
  if ('review' != sems_event_state($event)) return false; // Can only review in the right period
  if ($identity->UserId <= 0) return false;
  return $review['reviewer_id'] == $identity->UserId || $review['external_reviewer_id'] == $identity->UserId;
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

/************** UTILITY FUNCTIONS *************/

function sems_actions() {
  return array_filter(func_get_args());
}

function sems_action_create_conference() {
  if (!can_create_conference()) return null;
  return array(
    'label' => 'Create a new conference', 
    'url' => sems_conference_create_url());
}

function sems_action_edit_conference($conf) {
  if (!can_edit_conference($conf)) return null;
  return array(
    'label' => 'Modify this conference',
    'url' => sems_conference_edit_url($conf['conference_id']));
}

function sems_action_create_event($conf) {
  if (!can_create_event()) return null;
  return array(
    'label' => 'Create an event for this conference',
    'url' => sems_event_create_url($conf['conference_id']));
}

function sems_action_edit_event($conf, $event) {
  if (!can_edit_event($event)) return null;
  return array(
    'label' => 'Modify this event',
    'url' => sems_event_edit_url($conf['conference_id'], $event['event_id']));
}

function sems_action_manage_committee($conf, $event) {
  if (!can_manage_committee($event)) return null;
  return array(
    'label' => 'Manage this event\'s committee',
    'url' => sems_event_committee_url($conf['conference_id'], $event['event_id']));
}

function sems_action_submit_paper($conf, $event) {
  if (!can_submit_papers($event)) return null;
  return array(
    'label' => 'Submit a paper for this event',
    'url' => sems_papers_submit_url($conf['conference_id'], $event['event_id']));
}

function sems_action_post_message($conf, $event) {
  if (!can_post_message($event)) return null;
  return array(
    'label' => 'Post a new message',
    'url' => sems_messages_create_url($conf['conference_id'], $event['event_id']));
}

function sems_action_bid($conf, $event, $committee) {
  if (!can_bid_for_papers($event, $committee)) return null;
  return array(
    'label' => 'Bid for paper reviews',
    'url' => sems_reviews_auction_url($conf['conference_id'], $event['event_id']));
}

function sems_action_assign($conf, $event) {
  if (!can_assign_paper_reviews($event)) return null;
  return array(
    'label' => 'Assign paper reviews',
    'url' => sems_reviews_assign_url($conf['conference_id'], $event['event_id']));
}

function sems_action_review($conf, $event, $review) {
  if (!can_review_paper($event, $review)) return null;
  return array(
    'label' => 'Review this paper',
    'url' => sems_review_url($conf['conference_id'], $event['event_id'], $review['review_id']));
}

function sems_action_accept($conf, $event) {
  if (!can_accept_papers($event)) return null;
  return array(
    'label' => 'Accept or reject papers',
    'url' => sems_papers_decision_url($conf['conference_id'], $event['event_id']));
}

function sems_action_epublish($conf, $event) {
  if (!can_epublish_papers($event)) return null;
  return array(
    'label' => 'ePublish accepted papers',
    'url' => sems_papers_epublish_url($conf['conference_id'], $event['event_id']));

}

function sems_action_edit_message($conf, $event, $message) {
  if (!can_edit_message($event, $message)) return null;
  return array(
    'label' => 'Modify this message',
    'url' => sems_message_edit_url($conf['conference_id'], $event['event_id'], $message['message_id']));
}

