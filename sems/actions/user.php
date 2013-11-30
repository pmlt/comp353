<?php

function sems_login_url() { return SEMS_ROOT.'/login'; }
function sems_login() {
  //If already logged-in, redirect to home.
  if (sems_get_identity() != null) {
    return found(sems_home_url());
  }
  //If information was posted, try to login
  $smarty_vars = array();
  if (count($_POST) > 0) {
    $redirect = sems_db(function($db) {
      $email = $_POST['email'];
      $pwd = $_POST['password'];
      $user_id = sone($db, "SELECT user_id FROM User WHERE email=? AND password=?", array($email, sems_hash_password($pwd)));
      if ($user_id > 0) {
        //Login successful.
        sems_set_identity(sems_create_identity($db, $user_id));
        return found(sems_home_url());
      }
      return null;
    });
    if ($redirect) return $redirect;
    else $smarty_vars['login_failed'] = true;
  }
  return ok(sems_smarty_fetch('user/login.tpl', $smarty_vars));
}

function sems_logout_url() { return SEMS_ROOT.'/logout'; }
function sems_logout() {
  sems_clear_identity();
  return found(sems_home_url());
}

function sems_signup_url() { return SEMS_ROOT.'/signup'; }
function sems_signup() {
  //If already logged-in, redirect to home.
  if (sems_get_identity() != null) {
    return found(sems_home_url());
  }
  return sems_db(function($db) {
    //If information was posted, try to login
    $smarty_vars = array();
    if (count($_POST) > 0) {
      // First, validate all parameters.
      $rules = array(
        'first_name' => array('required'),
        'last_name' => array('required'),
        'country_id' => array('required','valid_country_id'),
        'organization_id' => array('required','valid_organization_id'),
        'department' => array('required'),
        'email' => array('required','valid_email','match_confirm','unique_email'),
        'password' => array('required','match_confirm'));
      $success = sems_validate($_POST, $rules, $errors);
      if ($success) {
        //Create user.
        //Hash password
        $_POST['password'] = sems_hash_password($_POST['password']);
        $_POST['date_created'] = 'NOW()'; //This will be interpreted literally
        list($sql, $sqlparams) = generate_insert($db, "User", array('date_created','title','first_name','middle_name','last_name','country_id','organization_id','department','address','city','province','postcode','email','password'), $_POST, array('date_created'));
        $user_id = insert($db, $sql, $sqlparams);
        sems_set_identity(sems_create_identity($db, $user_id));
        return found(sems_home_url());
      }
      else {
        //Validation error; just display errors
        $smarty_vars['errors'] = $errors;
      }
    }
    $smarty_vars['organizations'] = sassoc($db, "SELECT organization_id, name FROM Organization ORDER BY name");
    $smarty_vars['countries'] = sassoc($db, "SELECT country_id, name FROM Country ORDER BY name");
    return ok(sems_smarty_fetch('user/signup.tpl', $smarty_vars));
  });
}

function sems_confirm_url() { return SEMS_ROOT.'/confirm'; }
function sems_confirm() {
  //If NOT logged-in, give 404.
  $ident = sems_get_identity();
  if ($ident == null) {
    return sems_notfound();
  }
  
  return sems_db(function($db) use($ident) {
    $vars = array();
    if (count($_POST) > 0) {
      $n = affect($db, "UPDATE User SET email_sent_flag=b'1' WHERE user_id=?", array($ident->UserId));
      $vars['confirm_success'] = $n == 1;
      //Re-set identity whenever something in the user changes
      sems_set_identity(sems_create_identity($db, $ident->UserId));
    }
    return ok(sems_smarty_fetch("user/confirm.tpl", $vars));
  });
}

function sems_profile_url($uid) { return SEMS_ROOT . "/profile/{$uid}"; }
function sems_profile($uid) {
  return sems_db(function($db) use ($uid) {
    // Check that identity exists
    $user_id = sone($db, "SELECT user_id FROM User WHERE user_id=?", array($uid));
    if ($user_id <= 0) return sems_notfound();

    $vars = array();

    // Load all data
    $ident = sems_create_identity($db, $user_id);
    $vars['ident'] = $ident;

    // Load additional strings
    $vars['organization'] = sone($db, "SELECT name FROM Organization WHERE organization_id=?", array($ident->UserData['organization_id']));
    $vars['country'] = sone($db, "SELECT name FROM Country WHERE country_id=?", array($ident->UserData['country_id']));

    return ok(sems_smarty_fetch("user/profile.tpl", $vars));
  });
}

function sems_profile_edit_url($uid) { return SEMS_ROOT . "/profile/{$uid}/edit"; }
function sems_profile_edit($uid) {
  //If NOT logged-in, give 404.
  $ident = sems_get_identity();
  if ($ident == null) {
    return sems_notfound();
  }
  //If logged-in but trying to edit someone else, give 403
  if ($ident->UserId != $uid) {
    return sems_forbidden();
  }

  return sems_db(function($db) use ($ident) {
    $vars = array();
    $vars['ident'] = $ident;
    $vars['errors'] = array();
    if (count($_POST) > 0) {
      $rules = array(
        'first_name' => array('required'),
        'last_name' => array('required'),
        'country_id' => array('required','valid_country_id'),
        'organization_id' => array('required','valid_organization_id'),
        'department' => array('required'));
      $success = sems_validate($_POST, $rules, $errors);
      if ($success) {
        //Edit user data then return to profile
        list($sql, $sqlparams) = generate_update($db, "User", array('title','first_name','middle_name','last_name','country_id','organization_id','department','address','city','province','postcode'), $_POST, qeq('user_id', $ident->UserId));
        $rows_affected = affect($db, $sql, $sqlparams);
        sems_set_identity(sems_create_identity($db, $ident->UserId));
        return found(sems_profile_url($ident->UserId));
      }
      else {
        //Validation error; just display errors
        $vars['errors'] = $errors;
      }
    }
    $vars['organizations'] = sassoc($db, "SELECT organization_id, name FROM Organization ORDER BY name");
    $vars['countries'] = sassoc($db, "SELECT country_id, name FROM Country ORDER BY name");
    return ok(sems_smarty_fetch("user/profile_edit.tpl", $vars));
  });
}

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
}

function sems_get_identity() {
  if (!isset($_SESSION['sems_identity'])) return null;
  return $_SESSION['sems_identity'];
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
  $topics = stable($db, "SELECT Topic.topic_id, name, category FROM Topic,UserTopic WHERE Topic.topic_id=UserTopic.topic_id AND user_id=?", array($user_id));
  return new Identity($user_id, $roles, $data, $topics);
}

function sems_identity_data($field) {
  $ident = sems_get_identity();
  if ($ident && isset($ident->UserData[$field])) return $ident->UserData[$field];
  return null;
}

function sems_hash_password($pwd) {
  return sha1($pwd);
}
