<?php

function sems_validate($post, $rules, &$errors) {
  $errors = array();
  foreach ($rules as $fieldname => $rules) {
    if (!isset($post[$fieldname])) {
      if ($rules[0] == 'required') {
        $errors[$fieldname] = $fieldname.' is required.';
      }
      continue; //Skip the rules for this field
    }
    $value = $post[$fieldname];
    foreach ($rules as $rule) {
      if ($rule == 'match_confirm') {
        //Special rule requires comparing to other field
        $v = match_confirm($value, $post["{$fieldname}_confirm"]);
      }
      else {
        $v = call_user_func($rule, $value);
      }
      if ($v === false) {
        if (is_callable("{$rule}_err")) {
          $errors[$fieldname] = call_user_func("{$rule}_err", $fieldname, $value);
        }
        else {
          $errors[$fieldname] = $fieldname . " is invalid.";
        }
        break;
      }
    }
  }
  return count($errors) <= 0;
}

function required_err($fieldname, $value) { return "{$fieldname} is required."; }
function required($val) {
  return !is_null($val) && $val != "";
}

function match_confirm_err($fieldname, $value) { return $fieldname." does not match."; }
function match_confirm($val, $against) {
  return $val === $against;
}

function valid_country_id($value) {
  return sems_acquire_db(function($db) use(&$value) {
    return "1" === sone($db, "SELECT COUNT(1) FROM Country WHERE country_id=?", array($value));
  });
}

function valid_organization_id($value) {
  return sems_acquire_db(function($db) use(&$value) {
    return "1" === sone($db, "SELECT COUNT(1) FROM Organization WHERE organization_id=?", array($value));
  });
}

function valid_conference_type($value) {
  return $value == 'J' || $value == 'C';
}

function unique_email_err($fieldname, $value) { return "This email address is already registered."; }
function unique_email($value) {
  return sems_acquire_db(function($db) use(&$value) {
    return "0" === sone($db, "SELECT COUNT(1) FROM User WHERE email=?", array($value));
  });
}

function valid_email_err($fieldname, $value) { return "Not a valid email address."; }
function valid_email($value) {
  return filter_var($value, FILTER_VALIDATE_EMAIL);
}

function valid_user_email_err($fieldname, $value) { return "We could not find this email in our database."; }
function valid_user_email($value) {
  return sems_acquire_db(function($db) use(&$value) {
    return 0 < sone($db, "SELECT user_id FROM User WHERE email=?", array($value));
  });
}

function valid_ip($value) {
  return filter_var($value, FILTER_VALIDATE_IP);
}

function unique_conference_name_err($fieldname, $value) { return "A conference with this name already exists."; }
function unique_conference_name($value) {
  return sems_acquire_db(function($db) use(&$value) {
    return "0" === sone($db, "SELECT COUNT(1) FROM Conference WHERE name=? AND conference_id != ?", array($value, intval($GLOBALS['UNIQUE_ID_EXCEPTION'])));
  });
}
