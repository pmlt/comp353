<?php

use ra\domains as rd;

class Conference extends Relation
{
  public static function attributes()
  {
    return array(
      'type' => rd\enum(Conference::getTypes()),
      'name' => rd\str(),
      'description' => rd\text(),
      'hierarchy' => rd\enum(Conference::getHierarchyClasses()),
      'start_date' => rd\datetime(),
      'end_date' => rd\datetime(),
      'chair_email' => rd\email());
  }
  public static function getTypes() {
    return array('Conference','Journal');
  }
  public static function getHierarchyClasses() {
    return array('SCM');
  }
}

?>
