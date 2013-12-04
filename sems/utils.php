<?php

function sems_time() {
  $date = sems_config('date');
  if (!empty($date)) return strtotime($date);
  return time();
}

function sems_datetime($date) {
  $time = strtotime($date);
  return date('j F Y', $time) . ' at ' . date('H:i', $time);
}

function sems_paper_download_url($paper_id, $revision) {
  return SEMS_ROOT."/uploads/".sems_paper_filename($paper_id, $revision);
}

function sems_paper_filename($paper_id, $revision) {
  $revision = date('Ymd-His', strtotime($revision));
  return sprintf("paper_%06d_%s.pdf", $paper_id, $revision);
}

function sems_save_membership(mysqli $db, $table, $field1, $field2, $id, $ids) {
  affect($db, "DELETE FROM {$table} WHERE {$field1}=?", array($id));
  foreach ($ids as $linked_id) {
    insert($db, "INSERT INTO {$table} ({$field1},{$field2}) VALUES(?,?)", array($id, $linked_id));
  }
}

/********** BREADCRUMB *****************/

function sems_breadcrumb() {
  return func_get_args();
}

function sems_bc($label, $url) {
  return array('label' => $label, 'url' => $url);
}

function sems_bc_home() { return sems_bc('SEMS Home', sems_home_url()); }

function sems_bc_profile(Identity $ident) {
  return sems_bc($ident->fullname(), sems_profile_url($ident->UserId));
}

function sems_bc_conference($conf) {
  return sems_bc($conf['name'], sems_conference_url($conf['conference_id']));
}

function sems_bc_event($conf, $event) {
  return sems_bc($event['title'], sems_event_url($conf['conference_id'], $event['event_id']));
}

function sems_bc_message($conf, $event, $message) {
  return sems_bc($message['title'], sems_message_url($conf['conference_id'], $event['event_id'], $message['message_id']));
}

function sems_bc_paper($conf, $event, $paper) {
  return sems_bc($paper['title'], sems_paper_url($conf['conference_id'], $event['event_id'], $paper['paper_id']));
}

function sems_bc_review($conf, $event, $paper, $review) {
  return sems_bc($paper['title'], sems_review_url($conf['conference_id'], $event['event_id'], $review['review_id']));
}


/********** USER SELECTION *************/

function sems_save_user_selection(mysqli $db, $table, $field, $id, $uids) {
  return sems_save_membership($db, $table, $field, 'user_id', $id, $uids);
}

/********** TOPICS **************/

function sems_topic_hierarchy(array $topics) {
  $h = array();
  foreach ($topics as $t) {
    if (!isset($h[$t['category']])) $h[$t['category']] = array();
    $h[$t['category']][] = $t;
  }
  return $h;
}

function sems_fetch_topics(mysqli $db) {
  return stable($db, "SELECT * FROM Topic ORDER BY category, name");
}

function sems_fetch_linked_topics(mysqli $db, $table, $id_field, $id) {
  return stable($db, "SELECT Topic.topic_id, name, category FROM Topic,{$table} WHERE Topic.topic_id = {$table}.topic_id AND {$table}.{$id_field}=?", array($id));
}

function sems_fetch_topic_selection(mysqli $db, $table, $id_field, $id) {
  return scol($db, "SELECT topic_id FROM {$table} WHERE {$id_field}=?", array($id));
}

function sems_save_topics(mysqli $db, $table, $id_field, $id, $post) {
  affect($db, "DELETE FROM {$table} WHERE {$id_field}=?", array($id));
  foreach ($post as $postname => $value) {
    if (0 === strpos($postname, 'topic_') && $value > 0) {
      list($sql,$params) = generate_insert($db, $table, array('topic_id',$id_field), array('topic_id' => $value, $id_field => $id));
      error_log($sql);
      error_log(print_r($params, 1));
      insert($db, $sql, $params);
    }
  }
}

function sems_filter_topics($topics, $selected) {
  return array_filter($topics, function($topic) use(&$selected) {
    return in_array($topic['topic_id'], $selected);
  });
}

function sems_select_topics($topics, $selected) {
  foreach ($topics as &$topic) {
    $topic['selected'] = in_array($topic['topic_id'], $selected);
  }
  return $topics;
}

