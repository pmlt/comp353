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

    //Get the list of topics for this event
    $vars['hierarchy'] = sems_topic_hierarchy(sems_fetch_linked_topics($db, "EventTopic", "event_id", $eid));

    return ok(sems_smarty_fetch('event/index.tpl', $vars));
  });
}


function sems_event_create_url($cid) { return sems_conference_url($cid)."/create"; }
function sems_event_create($cid) {
  if (!sems_identity_permission(sems_get_identity(), array('role', 'admin'))) {
    return sems_forbidden();
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
        'chair_email' => array('required', 'valid_email', 'valid_user_email')); // XXX
      $success = sems_validate($_POST, $rules, $errors);
      var_dump($success);
      var_dump($errors);
      if ($success) {
        $_POST['conference_id'] = $cid;
        $_POST['chair_id'] = sone($db, "SELECT user_id FROM User WHERE email=?", array($_POST['chair_email']));
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
          'chair_id'), $_POST);
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
    return ok(sems_smarty_fetch('event/create.tpl', $vars));
  });
}

function sems_event_edit_url($cid, $eid) { return sems_event_url($cid, $eid)."/edit"; }
function sems_event_edit($cid, $eid) {
  if (!sems_identity_permission(sems_get_identity(), array('role', 'admin'))) {
    return sems_forbidden();
  }
  return sems_db(function($db) use($cid, $eid) {
    $event = get_event($db, $cid, $eid);
    if (!$event) return sems_notfound();

    $chair = sems_create_identity($db, $event['chair_id']);
    $conf['chair_email'] = $chair->UserData['email'];

    $vars = array();
    $vars['event'] = $event;
    $vars['conf'] = get_conference($db, $cid);
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
        'chair_email' => array('required', 'valid_email', 'valid_user_email')); // XXX
      $success = sems_validate($_POST, $rules, $errors);
      if ($success) {
        $_POST['chair_id'] = sone($db, "SELECT user_id FROM User WHERE email=?", array($_POST['chair_email']));
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
          'chair_id'), $_POST, qeq('event_id', $eid));
        $rows_affected = affect($db, $sql, $params);
        sems_save_topics($db, 'EventTopic', 'event_id', $eid, $_POST);
        return found(sems_event_url($cid, $eid));
      }
      else {
        $vars['errors'] = $errors;
      }
    }
    $vars['hierarchy'] = sems_topic_hierarchy(sems_select_topics(sems_fetch_topics($db), sems_fetch_topic_selection($db, 'EventTopic', 'event_id', $eid)));
    return ok(sems_smarty_fetch('event/edit.tpl', $vars));
  });
}

function sems_event_committee_url($cid, $eic) { return sems_event_url($cid, $eid)."/committee"; }
function sems_event_committee($cid, $eid) {
  return sems_db(function($db) use($cid,$eid) {
    $event = get_event($db, $cid, $eid);
    if (!$event) return sems_notfound();

    if (!sems_identity_permission(sems_get_identity(), array('user_id', $event['chair_id']))) {
      return sems_forbidden();
    }
    $vars = array();
    $vars['event'] = $event;
    $vars['conf'] = get_conference($db, $cid);

    if (isset($_POST['committee'])) {
      $errors = array();
      $new_members = array_filter(array_map(function($email) use(&$db, &$errors) {
        if (empty($email)) return null;
        $user_id = sone($db, "SELECT user_id FROM User WHERE email=?", array($email));
        if (!$user_id) $errors[] = $email . ' cannot be found in database.';
        return $user_id;
      }, explode('|', $_POST['committee'])));
      if (count($errors) != 0) {
        $vars['errors'] = $errors;
      }
      else {
        //Save in DB
        affect($db, "DELETE FROM CommitteeMembership WHERE event_id=?", array($eid));
        foreach ($new_members as $uid) {
          insert($db, "INSERT INTO CommitteeMembership (event_id,user_id) VALUES(?,?)", array($eid, $uid));
        }
        $vars['success'] = true;
      }
    }

    // Fetch committee
    $vars['committee'] = get_event_committee($db, $eid);
    return ok(sems_smarty_fetch('event/committee.tpl', $vars));
  });
}



function get_event(mysqli $db, $cid, $eid) {
  return srow($db, "SELECT * FROM Event WHERE conference_id=? AND event_id=?", array($cid,$eid));
}

function get_event_committee(mysqli $db, $eid) {
  return stable($db, "SELECT User.user_id, User.email FROM User,CommitteeMembership AS ec WHERE ec.user_id=User.user_id AND ec.event_id=?", array($eid));
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
