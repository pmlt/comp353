<?php
/**************** Authentication functions *******************/

class Identity {
  public $UserId;
  public $Roles;
  public $UserData;
  public $Topics;
  public function __construct($user_id, array $roles, $data, $topics) {
    $this->UserId = $user_id;
    $this->Roles = $roles;
    $this->UserData = $data;
    $this->Topics = $topics;
  }
  public function fullname() {
    if (!is_array($this->UserData)) return "";
    $fn = "";
    if (isset($this->UserData['title'])) $fn .= ucfirst($this->UserData['title']) . ". ";
    if (!empty($this->UserData['first_name'])) $fn .= $this->UserData['first_name'] . ' ';
    if (!empty($this->UserData['middle_name'])) $fn .= $this->UserData['middle_name'] . ' ';
    if (!empty($this->UserData['last_name'])) $fn .= $this->UserData['last_name'];
    return $fn;
  }
  public function hastopic($topic_id) {
    if (!is_array($this->Topics)) return false;
    foreach ($this->Topics as $t) {
      if ($t['topic_id'] == $topic_id) return true;
    }
    return false;
  }
}

function sems_get_identity() {
  if (!isset($_SESSION['sems_identity'])) return new Identity(0, array(), array(), array());
  return $_SESSION['sems_identity'];
}

function sems_is_anonymous() {
  return !isset($_SESSION['sems_identity']);
}

function sems_set_identity(Identity $identity) {
  $_SESSION['sems_identity'] = $identity;
}

function sems_clear_identity() {
  unset($_SESSION['sems_identity']);
}

function sems_create_identity($db, $user_id) {
  $roles = scol($db, "SELECT role FROM UserRole WHERE user_id=?", array($user_id));
  $data = srow($db, "SELECT date_created,title,first_name,middle_name,last_name,country_id,organization_id,department,address,city,province,postcode,email,email_sent_flag,last_event_id FROM User WHERE user_id=?", array($user_id));
  $topics = scol($db, "SELECT topic_id FROM UserTopic WHERE user_id=?", array($user_id));
  return new Identity($user_id, $roles, $data, $topics);
}

function sems_identity_actions(Identity $identity, $actions) {
  return array_filter($actions, function($action) use(&$identity) {
    return sems_identity_permission($identity, $action['permission']);
  });
}

function sems_identity_permission(Identity $identity, array $permission) {
  if (in_array('admin', $identity->Roles)) return true; // Admin has EVERYTHING
  list($type,$object) = $permission;
  switch($type) {
    case 'role': return in_array($object, $identity->Roles);
    case 'user_id': return $object == $identity->UserId;
  }
  return false;
}

function sems_identity_data($field) {
  $ident = sems_get_identity();
  if ($ident && isset($ident->UserData[$field])) return $ident->UserData[$field];
  return null;
}

function sems_hash_password($pwd) {
  return sha1($pwd);
}

function sems_save_identity_topics($db, $user_id, $post) {
  return sems_save_topics($db, 'UserTopic', 'user_id', $user_id, $post);
}

