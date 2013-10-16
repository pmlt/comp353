<?php

use ra\domains as rd;

class Event extends Relation
{
  public static function attributes()
  {
    return array(
      'title' => rd\str(),
      'description' => rd\text(),
      'hierarchy' => rd\enum(Conference::getHierarchyClasses()),
      'start_date' => rd\datetime(),
      'end_date' => rd\datetime(),
      'scheduling_template' => rd\enum(Event::getSchedulingTemplates()),
      'copy_policy' => rd\enum(Event::getCopyPolicies()));
    // XXX how to model parameters
  }
  
  public static function getSchedulingTemplates() {
    return array('D' => 'Use the system default');
  }
  public static function getCopyPolicies() {
    return array('N' => 'Don\'t copy');
  }
}
