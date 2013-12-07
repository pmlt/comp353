<?php

function sems_message_url($cid, $eid, $mid) { return sems_messages_url($cid, $eid)."/{$mid}"; }
function sems_message($cid, $eid, $mid) {
  return sems_db(function($db) use($cid, $eid, $mid) {
    $conf = get_conference($db, $cid);
    $event = get_event($db, $cid, $eid);
    $message = get_message($db, $mid);
    if (!$conf || !$event || !$message) return sems_notfound();

    $committee = get_event_committee_ids($db, $eid);
    if (!can_view_message($event, $message, $committee)) {
      return sems_forbidden("You may not view this message's details.");
    }

    $author = sems_create_identity($db, $message['user_id']);

    $vars = array(
      'conf' => $conf,
      'event' => $event,
      'message' => $message,
      'author' => $author, 
      'breadcrumb' => sems_breadcrumb(
        sems_bc_home(),
        sems_bc_conference($conf),
        sems_bc_event($conf, $event),
        sems_bc_message($conf, $event, $message)),
      'actions' => array_merge(
        sems_event_actions($conf, $event, $committee), 
        sems_actions(
          sems_action_edit_message($conf, $event, $message))));
    return ok(sems_smarty_fetch('message/details.tpl', $vars));
  });
}

function sems_messages_url($cid, $eid) { return sems_event_url($cid, $eid)."/messages"; }
function sems_messages_create_url($cid, $eid) { return sems_messages_url($cid, $eid)."/create"; }
function sems_messages_create($cid, $eid) {
  return sems_db(function($db) use($cid, $eid) {
    $conf = get_conference($db, $cid);
    $event = get_event($db, $cid, $eid);
    if (!$conf || !$event) return sems_notfound();

    // Check that this user can create a new message
    if (!can_post_message($event)) {
      return sems_forbidden("You may not post new messages for this event.");
    }

    $vars = array();
    $vars['conf'] = $conf;
    $vars['event'] = $event;
    if (count($_POST) > 0) {
      if (!isset($_POST['is_public'])) $_POST['is_public'] = false;
      $rules = array(
        'publish_date' => array('required', 'valid_date'),
        'is_public' => array('valid_boolean'),
        'title' => array('required'),
        'excerpt' => array('required'),
        'body' => array('required'));
      list($success, $data) = sems_validate($_POST, $rules, $errors);
      if ($success) {
        $data['event_id'] = $eid;
        $data['user_id'] = sems_get_identity()->UserId;
        list($sql, $params) = generate_insert($db, 'Message', array(
          'event_id',
          'user_id',
          'publish_date',
          'is_public',
          'title',
          'excerpt',
          'body'), $data);
        $message_id = insert($db, $sql, $params);
        return found(sems_message_url($cid, $eid, $message_id));
      }
      else {
        $vars['errors'] = $errors;
      }
    }
    $vars['breadcrumb'] = sems_breadcrumb(
      sems_bc_home(),
      sems_bc_conference($conf),
      sems_bc_event($conf, $event),
      sems_bc('Post a new message', sems_messages_create_url($cid, $eid)));
    return ok(sems_smarty_fetch('message/create.tpl', $vars));
  });
}

function sems_message_edit_url($cid, $eid, $mid) { return sems_message_url($cid, $eid, $mid)."/edit"; }
function sems_message_edit($cid, $eid, $mid) {
  return sems_db(function($db) use($cid, $eid, $mid) {
    $conf = get_conference($db, $cid);
    $event = get_event($db, $cid, $eid);
    $message = get_message($db, $mid);
    if (!$conf && !$event && !$message) return sems_notfound();

    if (!can_edit_message($event, $message)) {
      return sems_forbidden("You may not edit this message's details.");
    }

    $vars = array();
    $vars['conf'] = $conf;
    $vars['event'] = $event;
    $vars['message'] = $message;
    if (count($_POST) > 0) {
      $rules = array(
        'publish_date' => array('required', 'valid_date'),
        'is_public' => array('valid_boolean'),
        'title' => array('required'),
        'excerpt' => array('required'),
        'body' => array('required'));
      list($success, $data) = sems_validate($_POST, $rules, $errors);
      if ($success) {
        list($sql, $params) = generate_update($db, 'Message', array(
          'publish_date',
          'is_public',
          'title',
          'excerpt',
          'body'), $data, qeq('message_id', $mid));
        $message_id = affect($db, $sql, $params);
        return found(sems_message_url($cid, $eid, $mid));
      }
      else {
        $vars['errors'] = $errors;
      }
    }
    $vars['breadcrumb'] = sems_breadcrumb(
        sems_bc_home(),
        sems_bc_conference($conf),
        sems_bc_event($conf, $event),
        sems_bc_message($conf, $event, $message),
        sems_bc('Modify details', sems_message_edit_url($cid, $eid, $mid)));
    $vars['actions'] = sems_event_actions($conf, $event);
    return ok(sems_smarty_fetch('message/edit.tpl', $vars));
  });
}

function get_message(mysqli $db, $mid) {
  return srow($db, "SELECT * FROM Message WHERE message_id=?", array($mid));
}
