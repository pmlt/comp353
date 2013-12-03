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
    
    //Possible actions
    $actions = array(
      array(
        'url' => sems_conference_edit_url($cid),
        'label' => 'Modify this conference',
        'permission' => array('role', 'admin')));
    $vars['actions'] = sems_identity_actions(sems_get_identity(), $actions);

    //Get the list of topics for this conference
    $vars['hierarchy'] = sems_topic_hierarchy(sems_fetch_linked_topics($db, "ConferenceTopic", "conference_id", $cid));

    //Get the list of events for this conference
    $vars['events'] = stable($db, "SELECT event_id, title, description, start_date FROM Event WHERE conference_id=? ORDER BY start_date", array($cid));
    return ok(sems_smarty_fetch('conference/index.tpl', $vars));
  });
}

function sems_conference_search($conf) {
  // XXX check if conference exists.
  return sems_notfound();
}

function sems_conference_create_url() { return SEMS_ROOT.'/create'; }
function sems_conference_create() {
  if (!can_create_conference()) {
    return sems_forbidden("You are not allowed to create new conferences.");
  }
  return sems_db(function($db) {
    $vars = array();
    if (count($_POST) > 0) {
      $rules = array(
        'name' => array('required', 'unique_conference_name'),
        'type' => array('required', 'valid_conference_type'),
        'description' => array('required'),
        'chair_email' => array('required', 'valid_email', 'email_to_id'));
      list($success, $data) = sems_validate($_POST, $rules, $errors);
      if ($success) {
        $data['chair_id'] = $data['chair_email'];
        list($sql, $params) = generate_insert($db, "Conference", array('name', 'type', 'description', 'chair_id'), $data);
        $conference_id = insert($db, $sql, $params);
        if ($conference_id > 0) {
          sems_save_topics($db, 'ConferenceTopic', 'conference_id', $conference_id, $_POST);
        }
        return found(sems_conference_url($conference_id));
      }
      else {
        $vars['errors'] = $errors;
      }
    }
    $vars['hierarchy'] = sems_topic_hierarchy(sems_fetch_topics($db));
    return ok(sems_smarty_fetch('conference/create.tpl', $vars));
  });
}

function sems_conference_edit_url($cid) { return sems_conference_url($cid) . "/edit"; }
function sems_conference_edit($cid) {
  return sems_db(function($db) use($cid) {
    $conf = get_conference($db, $cid);
    if (!$conf) return sems_notfound();

    if (!can_edit_conference($conf)) {
      return sems_forbidden("You are not allowed to modify this conference.");
    }

    //Get the program chair
    $chair = sems_create_identity($db, $conf['chair_id']);
    $conf['chair_email'] = $chair->UserData['email'];

    $vars = array();
    $vars['conf'] = $conf;
    $vars['chair'] = $chair;

    if (count($_POST) > 0) {
      $GLOBALS['UNIQUE_ID_EXCEPTION'] = $cid;
      $rules = array(
        'name' => array('required', 'unique_conference_name'),
        'type' => array('required', 'valid_conference_type'),
        'description' => array('required'),
        'chair_email' => array('required', 'valid_email', 'email_to_id'));
      list($success, $data) = sems_validate($_POST, $rules, $errors);
      if ($success) {
        $data['chair_id'] = $data['chair_email'];
        list($sql, $params) = generate_update($db, "Conference", array('name', 'type', 'description', 'chair_id'), $data, qeq('conference_id', $cid));
        $rows_affected = affect($db, $sql, $params);
        sems_save_topics($db, 'ConferenceTopic', 'conference_id', $cid, $_POST);
        return found(sems_conference_url($cid));
      }
      else {
        $vars['errors'] = $errors;
      }
    }
    $vars['hierarchy'] = sems_topic_hierarchy(sems_select_topics(sems_fetch_topics($db), sems_fetch_topic_selection($db, 'ConferenceTopic', 'conference_id', $cid)));
    return ok(sems_smarty_fetch('conference/edit.tpl', $vars));
  });
}

function get_conference(mysqli $db, $cid) {
  return srow($db, "SELECT * FROM Conference WHERE conference_id=?", array($cid));
}

function conference_actions($cid) {
  return array(
    'conference' => array(
      'edit' => array(
        'Modify this conference' => sems_conference_edit_url($cid))));
}
