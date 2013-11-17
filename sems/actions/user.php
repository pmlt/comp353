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
  return ok(sems_smarty_fetch('login.tpl', $smarty_vars));
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
        list($sql, $sqlparams) = generate_insert($db, "User", array('title','first_name','middle_name','last_name','country_id','organization_id','department','address','city','province','postcode','email','password'), $_POST);
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
    return ok(sems_smarty_fetch('signup.tpl', $smarty_vars));
  });
}

function sems_confirm($code) {
}

function sems_profile($uid) {
}

function sems_profile_edit($uid) {
}

/**************** Authentication functions *******************/

class Identity {
  public $UserId;
  public $Roles;
  public function __construct($user_id, array $roles) {
    $this->UserId = $user_id;
    $this->Roles = $roles;
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
  return new Identity($user_id, $roles);
}

function sems_hash_password($pwd) {
  return sha1($pwd);
}