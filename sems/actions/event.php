<?php

function sems_event_url($cid,$eid) { return SEMS_ROOT."/{$cid}/{$eid}"; }
function sems_event($cid, $eid) {
  return sems_db(function($db) use($cid,$eid) {
    $event = get_event($db, $cid, $eid);
    if (!$event) return sems_notfound();

    $conf = get_conference($db, $cid);
    if (!$conf) return sems_notfound();

    $vars = array();
    $vars['conf'] = $conf;
    $vars['event'] = $event;
    
    //Get the event chair
    $chair = sems_create_identity($db, $event['chair_id']);
    $vars['chair'] = $chair;

    //Get the whole event committee
    $vars['committee'] = get_event_committee($db, $eid);

    //Get the list of topics for this event
    $vars['hierarchy'] = sems_topic_hierarchy(sems_fetch_linked_topics($db, "EventTopic", "event_id", $eid));

    //Get the list of messages for this event
    $committee = get_event_committee_ids($db, $eid);
    $where = get_message_conditions($event, $committee);
    $vars['messages'] = stable($db, "SELECT * FROM Message WHERE ".$where->sql." ORDER BY publish_date DESC LIMIT 8", $where->params);

    //Get the list of papers for this event
    $where = get_paper_conditions($event, $committee);
    $vars['papers'] = stable($db, "SELECT paper_id, User.user_id, Paper.title, User.title AS user_title, first_name, last_name FROM Paper,User WHERE Paper.submitter_id=User.user_id AND ".$where->sql." ORDER BY publish_date DESC", $where->params);

    //Get the list of personal reviews for this event
    $where = get_review_conditions($event, $committee);
    $vars['reviews'] = stable($db, "SELECT review_id, Paper.title, score FROM PaperReview,Paper,User WHERE PaperReview.paper_id=Paper.paper_id AND PaperReview.reviewer_id=User.user_id AND " . $where->sql." ORDER BY PaperReview.score", $where->params);

    $vars['breadcrumb'] = sems_breadcrumb(
      sems_bc_home(),
      sems_bc_conference($conf),
      sems_bc_event($conf, $event));
    $vars['actions'] = sems_event_actions($conf, $event, $committee);

    return ok(sems_smarty_fetch('event/index.tpl', $vars));
  });
}


function sems_event_create_url($cid) { return sems_conference_url($cid)."/create"; }
function sems_event_create($cid) {
  if (!can_create_event()) {
    return sems_forbidden("You are not allowed to create new events.");
  }
  return sems_db(function($db) use($cid) {
    $conf = get_conference($db, $cid);
    if (!$conf) return sems_notfound();

    $vars = array();
    $vars['conf'] = $conf;
    if (count($_POST) > 0) {
      $rules = array(
        'title' => array('required', 'unique_event_title'),
        'description' => array('required'),
        'term_start_date' => array('required', 'valid_date'),
        'term_end_date' => array('required', 'valid_date'),
        'submit_start_date' => array('required', 'valid_date'),
        'submit_end_date' => array('required', 'valid_date'),
        'auction_start_date' => array('required', 'valid_date'),
        'auction_end_date' => array('required', 'valid_date'),
        'review_start_date' => array('required', 'valid_date'),
        'review_end_date' => array('required', 'valid_date'),
        'decision_date' => array('required', 'valid_date'),
        'start_date' => array('required', 'valid_date'),
        'end_date' => array('required', 'valid_date'),
        'chair_email' => array('required', 'valid_email', 'email_to_id'));
      list($success, $data) = sems_validate($_POST, $rules, $errors);
      if ($success) {
        $data['conference_id'] = $cid;
        $data['chair_id'] = $data['chair_email'];
        list($sql, $params) = generate_insert($db, 'Event', array(
          'conference_id',
          'title',
          'description',
          'term_start_date',
          'term_end_date',
          'submit_start_date',
          'submit_end_date',
          'auction_start_date',
          'auction_end_date',
          'review_start_date',
          'review_end_date',
          'decision_date',
          'start_date',
          'end_date',
          'chair_id'), $data);
        $event_id = insert($db, $sql, $params);
        if ($event_id > 0) {
          sems_save_topics($db, 'EventTopic', 'event_id', $event_id, $_POST);
        }
        return found(sems_event_url($cid, $event_id));
      }
      else {
        $vars['errors'] = $errors;
      }
    }
    $vars['hierarchy'] = sems_topic_hierarchy(sems_fetch_topics($db));
    $vars['breadcrumb'] = sems_breadcrumb(
      sems_bc_home(),
      sems_bc_conference($conf),
      sems_bc('Create a new event', sems_event_create_url($conf['conference_id'])));
    return ok(sems_smarty_fetch('event/create.tpl', $vars));
  });
}

function sems_event_edit_url($cid, $eid) { return sems_event_url($cid, $eid)."/edit"; }
function sems_event_edit($cid, $eid) {
  return sems_db(function($db) use($cid, $eid) {
    $event = get_event($db, $cid, $eid);
    $conf = get_conference($db, $cid);
    if (!$conf || !$event) return sems_notfound();
    if (!can_edit_event($event)) {
      return sems_forbidden("You are not allowed to modify this event.");
    }

    $chair = sems_create_identity($db, $event['chair_id']);
    $event['chair_email'] = $chair->UserData['email'];

    $committee = get_event_committee_ids($db, $eid);

    $vars = array();
    $vars['event'] = $event;
    $vars['conf'] = $conf;
    $vars['chair'] = $chair;

    if (count($_POST) > 0) {
      $GLOBALS['UNIQUE_ID_EXCEPTION'] = $eid;
      $rules = array(
        'title' => array('required', 'unique_event_title'),
        'description' => array('required'),
        'term_start_date' => array('required', 'valid_date'),
        'term_end_date' => array('required', 'valid_date'),
        'submit_start_date' => array('required', 'valid_date'),
        'submit_end_date' => array('required', 'valid_date'),
        'auction_start_date' => array('required', 'valid_date'),
        'auction_end_date' => array('required', 'valid_date'),
        'review_start_date' => array('required', 'valid_date'),
        'review_end_date' => array('required', 'valid_date'),
        'decision_date' => array('required', 'valid_date'),
        'start_date' => array('required', 'valid_date'),
        'end_date' => array('required', 'valid_date'),
        'chair_email' => array('required', 'valid_email', 'email_to_id'));
      list($success, $data) = sems_validate($_POST, $rules, $errors);
      if ($success) {
        $data['chair_id'] = $data['chair_email'];
        list($sql, $params) = generate_update($db, 'Event', array(
          'title',
          'description',
          'term_start_date',
          'term_end_date',
          'submit_start_date',
          'submit_end_date',
          'auction_start_date',
          'auction_end_date',
          'review_start_date',
          'review_end_date',
          'decision_date',
          'start_date',
          'end_date',
          'chair_id'), $data, qeq('event_id', $eid));
        $rows_affected = affect($db, $sql, $params);
        sems_save_topics($db, 'EventTopic', 'event_id', $eid, $_POST);
        return found(sems_event_url($cid, $eid));
      }
      else {
        $vars['errors'] = $errors;
      }
    }
    $vars['hierarchy'] = sems_topic_hierarchy(sems_select_topics(sems_fetch_topics($db), sems_fetch_topic_selection($db, 'EventTopic', 'event_id', $eid)));
    $vars['breadcrumb'] = sems_breadcrumb(
      sems_bc_home(),
      sems_bc_conference($conf),
      sems_bc_event($conf, $event),
      sems_bc('Modify details', sems_event_edit_url($conf['conference_id'], $event['event_id'])));
    $vars['actions'] = sems_event_actions($conf, $event, $committee);
    return ok(sems_smarty_fetch('event/edit.tpl', $vars));
  });
}

function sems_event_committee_url($cid, $eid) { return sems_event_url($cid, $eid)."/committee"; }
function sems_event_committee($cid, $eid) {
  return sems_db(function($db) use($cid,$eid) {
    $conf = get_conference($db, $cid);
    $event = get_event($db, $cid, $eid);
    if (!$conf || !$event) return sems_notfound();

    if (!can_manage_committee($event)) {
      return sems_forbidden("You may not manage this event's committee membership");
    }
    $vars = array();
    $vars['event'] = $event;
    $vars['conf'] = $conf;

    if (isset($_POST['committee'])) {
      $rules = array('committee' => array('valid_user_references'));
      list($success, $data) = sems_validate($_POST, $rules, $errors);
      if ($success) {
        //Save in DB
        sems_save_user_selection($db, 'CommitteeMembership', 'event_id', $eid, $data['committee']);
        $vars['success'] = true;
      }
      else {
        $vars['errors'] = $errors['committee'];
      }
    }

    //Get fresh version
    $committee = get_event_committee_ids($db, $eid);

    // Fetch committee
    $vars['committee'] = get_event_committee($db, $eid);
    $vars['breadcrumb'] = sems_breadcrumb(
      sems_bc_home(),
      sems_bc_conference($conf),
      sems_bc_event($conf, $event),
      sems_bc('Manage committee', sems_event_committee_url($cid, $eid)));
    $vars['actions'] = sems_event_actions($conf, $event, $committee);
    return ok(sems_smarty_fetch('event/committee.tpl', $vars));
  });
}



function get_event(mysqli $db, $cid, $eid) {
  return srow($db, "SELECT * FROM Event WHERE conference_id=? AND event_id=?", array($cid,$eid));
}

function get_event_committee(mysqli $db, $eid) {
  return stable($db, "SELECT User.user_id, User.first_name, User.last_name, User.email FROM User,CommitteeMembership AS ec WHERE ec.user_id=User.user_id AND ec.event_id=?", array($eid));
}

function get_event_committee_ids(mysqli $db, $eid) {
  return scol($db, "SELECT user_id FROM CommitteeMembership WHERE event_id=?", array($eid));
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

$event_state_names = array(
  'init' => 'Initial preparations',
  'term' => 'Term period',
  'term_ended' => 'End of term period',
  'submit' => 'Paper submission period',
  'submit_ended' => 'End of paper submission period',
  'auction' => 'Paper review auction period',
  'auction_ended' => 'End of paper review auction period',
  'review' => 'Paper review period',
  'review_ended' => 'Decision period',
  'decision' => 'Announcement of paper decisions',
  'meet' => 'Meeting period',
  'ended' => 'Event is over');
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

function sems_event_state_name_str($state) {
  global $event_state_names;
  return $event_state_names[$state];
}

function sems_event_actions($conf, $event, $committee) {
  return sems_actions(
    sems_action_edit_event($conf, $event),
    sems_action_manage_committee($conf, $event),
    sems_action_submit_paper($conf, $event, $committee),
    sems_action_post_message($conf, $event),
    sems_action_bid($conf, $event, $committee),
    sems_action_assign($conf, $event),
    sems_action_accept($conf, $event),
    sems_action_epublish($conf, $event));
}
