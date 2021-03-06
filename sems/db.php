<?php

$SEMS_DB_HANDLE = null;

function sems_db($f) {
  global $SEMS_DB_HANDLE;
  $conf = sems_config('db');
  $SEMS_DB_HANDLE = new mysqli($conf['host'],$conf['user'],$conf['pwd'],$conf['db']);
  if ($SEMS_DB_HANDLE->connect_errno) {
    throw new Exception("Failed to connect to MySQL: (" . $SEMS_DB_HANDLE->connect_errno . ") " . $SEMS_DB_HANDLE->connect_error);
  }
  if (!$SEMS_DB_HANDLE->set_charset('utf8')) {
    sems_throw_db($SEMS_DB_HANDLE, "Could not set charset");
  }
  try {
    $result = call_user_func($f, $SEMS_DB_HANDLE);
  }
  catch (Exception $e) {
    $SEMS_DB_HANDLE->close(); //Make sure to close properly at all times
    throw $e;
  }
  $SEMS_DB_HANDLE->close();
  $SEMS_DB_HANDLE = null;
  return $result;
}

function sems_acquire_db($f) {
  global $SEMS_DB_HANDLE;
  if (!($SEMS_DB_HANDLE instanceof mysqli)) throw new Exception("sems_acquire_db invoked when not in DB context!");
  return call_user_func($f, $SEMS_DB_HANDLE);
}

function sems_throw_db(mysqli $db, $prefix) {
  throw new Exception($prefix.": (" . $db->errno . ") " . $db->error);
}

class SqlCond {
  public $sql = "";
  public $params = array();
  public function __construct($sql, $params) {
    $this->sql = $sql;
    $this->params = $params;
  }
}

function qeq($field, $value) { return qcond('=', $field, $value); }
function qcond($op, $field, $value) {
  return new SqlCond("$field $op ?", array($value));
}
function qand(array $conds) { return qgroup('AND', $conds); }
function qor(array $conds) { return qgroup('OR', $conds); }
function qgroup($separator, array $conds) {
  $sql = '(' . implode(" {$separator} ", array_map(function(SqlCond $cond) {
    return $cond->sql;
  }, $conds)) . ')';
  $params = call_user_func_array('array_merge', array_map(function(SqlCond $cond) {
    return $cond->params;
  }, $conds));
  return new SqlCond($sql, $params);
}

function generate_insert(mysqli $db, $table, $fields, $values, $literal_exceptions=array()) {
  $safe_table = $db->escape_string($table);
  $safe_fields = implode(',', array_map(function($field) use(&$db) {
    return '`'.$db->escape_string($field).'`';
  }, $fields));
  $safe_placeholders = implode(',', array_map(function($field) use(&$values,&$literal_exceptions) {
    if (in_array($field, $literal_exceptions)) {
      return isset($values[$field]) ? $values[$field] : null;
    } else {
      return "?";
    }
  }, $fields));
  $sql = "INSERT INTO `$safe_table` ($safe_fields) VALUES ($safe_placeholders)";
  $sqlparams = array();
  foreach ($fields as $field) {
    if (!in_array($field, $literal_exceptions)) {
      $sqlparams[] = isset($values[$field]) ? $values[$field] : null;
    }
  }
  return array($sql, $sqlparams);
}

function generate_update(mysqli $db, $table, $fields, $values, SqlCond $where=null) {
  $safe_table = $db->escape_string($table);
  $modified_fields = implode(',', array_map(function($field) use(&$db,&$values) {
    return '`'.$db->escape_string($field).'`=?';
  }, $fields));
  $sqlparams = array_map(function($field) use(&$values) {
    return isset($values[$field]) ? $values[$field] : null;
  }, $fields);
  $sql = "UPDATE `{$safe_table}` SET {$modified_fields}";
  if (!is_null($where)) {
    $sql .= " WHERE " . $where->sql;
    $sqlparams = array_merge($sqlparams, $where->params);
  }
  return array($sql,$sqlparams);
}

function insert(mysqli $db, $sql, $sqlparams) {
  return query(function($res) use(&$db) {
    return $db->insert_id;
  }, $db, $sql, $sqlparams);
}

function affect(mysqli $db, $sql, $sqlparams) {
  return query(function($res) use(&$db) {
    return $db->affected_rows;
  }, $db, $sql, $sqlparams);
}

// Return the resultset as an array of rows.
function stable(mysqli $db, $sql, array $params=array(), $mode=MYSQLI_ASSOC) {
  return smap(function($row) {
    return $row;
  }, $db, $sql, $params, $mode);
}

// Return the values of the first column of the resultset.
function scol(mysqli $db, $sql, array $params=array()) {
  return smap(function($row) {
    return $row[0];
  }, $db, $sql, $params, MYSQLI_NUM);
}

// Return the first row of the resultset.
function srow(mysqli $db, $sql, array $params=array()) {
  return query(function($res) {
    $firstrow = $res->fetch_array(MYSQLI_ASSOC);
    if ($firstrow) return $firstrow;
    return null;
  }, $db, $sql, $params);
}

// Return the value of the first column of the first row of the resultset.
function sone(mysqli $db, $sql, array $params=array()) {
  return query(function($res) {
    $firstrow = $res->fetch_array(MYSQLI_NUM);
    if ($firstrow) return $firstrow[0];
    return null;
  }, $db, $sql, $params);
}

// Return an associative array where the keys are taken from the first column and the values are taken from the second column.
function sassoc(mysqli $db, $sql, array $params=array())  {
  return sreduce(function($assoc, $row) use(&$assoc) {
    $assoc[$row[0]] = $row[1];
    return $assoc;
  }, array(), $db, $sql, $params, MYSQLI_NUM);
}

// Just get the number of rows returned by a query
function scount(mysqli $db, $sql, array $params=array()) {
  return query(function($res){ return $res->num_rows; }, $db, $sql, $params);
}

// Map callback to every result row
function smap($f, mysqli $db, $sql, array $params=array(), $mode=MYSQLI_ASSOC) {
  return query(function($res) use (&$f, $mode) {
    $mapped = array();
    while($row = $res->fetch_array($mode)) {
      $mapped[] = call_user_func($f, $row);
    }
    return $mapped;
  }, $db, $sql, $params);
}

// Reduce resultset to a single result using callback
function sreduce($f, $init, mysqli $db, $sql, array $params=array(), $mode=MYSQLI_ASSOC) {
  return query(function($res) use (&$f, &$init, $mode) {
    $reduced = $init;
    while($row = $res->fetch_array($mode)) {
      $reduced = call_user_func($f, $reduced, $row);
    }
    return $reduced;
  }, $db, $sql, $params);
}

// Execute query, ensuring cleanup
function query($f, mysqli $db, $sql, array $params=array()) {
  $sql = prepare($db, $sql, $params);
  $res = $db->query($sql);
  if ($res === FALSE) {
    sems_throw_db($db, "MySQL query failed");
  }
  try {
    $result = call_user_func($f, $res);
  }
  catch (Exception $e) {
    if (is_object($res)) $res->free();
    throw $e;
  }
  if (is_object($res)) $res->free();
  return $result;
}

function prepare(mysqli $db, $sql, array $sqlparams=array()) {
  static $PrepareCache = array();
  if(isset($PrepareCache[$sql])) {
    $positions = $PrepareCache[$sql];
  } else  {
    $positions = prepare_positions($sql);
    $PrepareCache[$sql] = $positions;
  }

  $pos_c = count($positions);
  if ($pos_c != count($sqlparams)) throw new Exception("Mismatch in placeholder count!");
  for($i = $pos_c-1; $i >= 0; $i--) {
    $position = $positions[$i];
    $replacement = sems_quote($db, $sqlparams[$i]);
    $sql = substr_replace($sql, $replacement, $position, 1);
  }

  return $sql;
}

function sems_quote(mysqli $dbh, $value)
{
  $type = gettype($value);
  switch ($type) {
    case "NULL";
      return "NULL";
    case "boolean":
      return $value ? 1 : 0;
    case "integer":
      return (int)$value;
    case "double":
      return $value;
    case "array":
      $value = serialize($value);
      //Fall through to default
    default: //includes string, object, resource
      //String representation of value
      return "'" . sems_escape($dbh, "$value") . "'";
  }
}

function sems_escape(mysqli $dbh, $value)
{
  if (empty($value)) return "";
  $res = $dbh->real_escape_string($value);
  if (!$res) sems_throw_db($dbh);
  return $res;
}

function prepare_positions($sql) {
  $scan_pos = 0;
  $sql_len = strlen($sql);
  $end_ignore_string_len = 0;
  $end_ignore_string = null;
  $can_escape = 0;

  $positions = array();

  //Step through each character in the string
  while ($scan_pos !== FALSE && $scan_pos < $sql_len)
  {
    $step = 1;
    $chr = $sql[$scan_pos];

    if (!$end_ignore_string_len) {
      //NOT currently in ignored section
      //Actively looking for placeholder
      if ($chr == '?') {
        $positions[] = $scan_pos;
      }
      else if ($chr == '\'') {
        //Start a string literal that ends with '
        $end_ignore_string_len = 1;
        $end_ignore_string = '\'';
        $can_escape = 1;
      }
      else if ($chr == '"') {
        //Start a string literal that ends with "
        $end_ignore_string_len = 1;
        $end_ignore_string = '"';
        $can_escape = 1;
      }
      else if ($chr == '-' && $sql[$scan_pos+1] == '-') {
        //Start a comment line that ends with a newline
        $end_ignore_string_len = 1;
        $end_ignore_string = "\n";
        $can_escape = 0;
        $step = 2;
      }
      else if ($chr == '/' && $sql[$scan_pos+1] == '*') {
        //Start a comment block that ends with slash star
        $end_ignore_string_len = 2;
        $end_ignore_string = '*/';
        $can_escape = 0;
        $step = 2;
      }
    }
    else {
      // Currently in an ignored section; just keep skipping till we find
      // the end section marker
      if ($can_escape && $sql[$scan_pos] == '\\') {
        //Jump over escaped character
        $step = 2;
      }
      else {
        //Check if we are at the end of the ignored section
        $candidate = substr($sql, $scan_pos, $end_ignore_string_len);
        if ($candidate == $end_ignore_string) {
          //Exit the ignored section
          $step = $end_ignore_string_len;
          $end_ignore_string = null;
          $end_ignore_string_len = 0;
          $can_escape = 0;
        }
      }
    }
    $scan_pos = $scan_pos + $step;
  }

  return $positions;
}
