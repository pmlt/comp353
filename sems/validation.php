<?php

function sems_validate($post, $rules, &$errors) {
  $errors = array();
  $data = array();
  foreach ($rules as $fieldname => $rules) {
    if (empty($post[$fieldname])) {
      if ($rules[0] == 'required') {
        $errors[$fieldname] = $fieldname.' is required.';
      }
      continue; //Skip the rules for this field
    }
    $value = $post[$fieldname];
    foreach ($rules as $rule) {
      if ($rule == 'match_confirm') {
        //Special rule requires comparing to other field
        list($v,$value) = match_confirm($value, $post["{$fieldname}_confirm"]);
      }
      else {
        if (!is_callable($rule)) throw new Exception("Invalid rule $rule");
        list($v,$value) = call_user_func($rule, $value);
      }
      if ($v === false) {
        $errors[$fieldname] = $value;
        break;
      }
    }
    $data[$fieldname] = $value;
  }
  return array(count($errors) == 0, $data);
}

function required($val) {
  if (!is_null($val) && $val != "") {
    return array(true, $val);
  } else {
    return array(false, "This field is required.");
  }
}

function match_confirm($val, $against) {
  if ($val === $against) {
    return array(true, $val);
  }
  else {
    return array(false, "This field does not match.");
  }
}

function valid_country_id($value) {
  return sems_acquire_db(function($db) use(&$value) {
    if ("1" === sone($db, "SELECT COUNT(1) FROM Country WHERE country_id=?", array($value))) {
      return array(true, $value);
    } else {
      return array(false, "This country ID is invalid.");
    }
  });
}

function valid_organization_id($value) {
  return sems_acquire_db(function($db) use(&$value) {
    if ("1" === sone($db, "SELECT COUNT(1) FROM Organization WHERE organization_id=?", array($value))) {
      return array(true, $value);
    } else {
      return array(false, "This organization ID is invalid.");
    }
  });
}

function valid_conference_type($value) {
  if ($value == 'J' || $value == 'C') {
    return array(true, $value);
  }
  else {
    return array(false, "This is not a valid conference type.");
  }
}

function unique_email($value) {
  return sems_acquire_db(function($db) use(&$value) {
    if ("0" === sone($db, "SELECT COUNT(1) FROM User WHERE email=?", array($value))) {
      return array(true, $value);
    }
    else {
      return array(false, "This email address is already registered.");
    }
  });
}

function valid_email($value) {
  if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
    return array(true, $value);
  }
  else {
    return array(false, "Not a valid email address.");
  }
}

function email_to_id($value) {
  return sems_acquire_db(function($db) use(&$value) {
    $uid = sone($db, "SELECT user_id FROM User WHERE email=?", array($value));
    if ($uid > 0) {
      return array(true, $uid);
    }
    else {
      return array(false, "We could not find {$value} in our database.");
    }
  });
}

function valid_ip($value) {
  if (filter_var($value, FILTER_VALIDATE_IP)) {
    return array(true, $value);
  }
  else {
    return array(false, "This IP is invalid.");
  }
}

function unique_conference_name($value) {
  return sems_acquire_db(function($db) use(&$value) {
    if ("0" === sone($db, "SELECT COUNT(1) FROM Conference WHERE name=? AND conference_id != ?", array($value, intval($GLOBALS['UNIQUE_ID_EXCEPTION'])))) {
      return array(true, $value);
    }
    else {
      return array(false, "A conference with this name already exists.");
    }
  });
}

function unique_event_title($value) {
  return sems_acquire_db(function($db) use(&$value) {
    if("0" === sone($db, "SELECT COUNT(1) FROM Event WHERE title=? AND event_id != ?", array($value, intval($GLOBALS['UNIQUE_ID_EXCEPTION'])))) {
      return array(true, $value);
    }
    else {
      return array(false, "An event with this title already exists.");
    }
  });
}

function valid_date($value) {
  $time = strtotime($value);
  if ($time === FALSE) {
    return array(false, "{$value} is not a valid date and time.");
  }
  else {
    return array(true, date('Y-m-d H:i:s', $time));
  }
}

function valid_user_references($value) {
  $uids = array();
  $errors = array();
  foreach (explode('|', $value) as $email) {
    if (empty($email)) continue;
    list($v, $e) = email_to_id($email);
    if ($v) $uids[] = $e;
    else $errors[$email] = $e;
  }
  if (count($errors) > 0) {
    return array(false, $errors);
  }
  else {
    return array(true, $uids);
  }
}

function valid_upload($value) {
  $upload_ok = isset($_FILES['file']) &&
               $_FILES['file']['error'] == UPLOAD_ERR_OK &&
               is_uploaded_file($_FILES['file']['tmp_name']);
  if ($upload_ok) {
    return array(true, $value);
  }
  else {
    return array(false, "File upload failed");
  }
}

function valid_pdf($value) {
  // Cannot invoke file on Concordia server; fall back to extension detection.
  $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
  if ($ext == 'pdf') {
    return array(true, $value);
  }
  else {
    return array(false, "File {$_FILES['file']['name']} is not a PDF.");
  }

  //Here is the original detection logic
  $file = exec('file ' . escapeshellarg($_FILES['file']['tmp_name']), $output);
  foreach ($output as $line) {
    if (FALSE !== strpos($line, 'PDF document')) {
      return array(true, $value);
    }
  }
  return array(false, "File {$_FILES['file']['name']} is not a PDF: '{$file}'");
}

function valid_boolean($value) {
  return array(true, $value == TRUE);
}

function valid_scale($value) {
  if ($value > 0 && $value <= 10) {
    return array(true, (int)$value);
  }
  else {
    return array(false, "Must be an integer from 1 to 10.");
  }
}

function valid_originality($value) {
  if (in_array($value, array('good','bad','mediocre'))) {
    return array(true, $value);
  }
  else {
    return array(false, "Must be either 'good','bad','mediocre'");
  }
}

