<?php

include 'actions/user.php';
include 'actions/conference.php';

define('SEMS_ROOT', '/comp353');
$HTTP_ROOT = '/comp353';

function sems_routes() {
  //XXX must define routes here.
  global $HTTP_ROOT;
  return array(
    //SEMS Administration routes
    "|^{$HTTP_ROOT}/admin$|" => 'sems_admin',
    "|^{$HTTP_ROOT}/admin/conferences$|" => 'sems_admin_conferences',
    "|^{$HTTP_ROOT}/admin/conferences/create$|" => 'sems_admin_conferences_create',
    "|^{$HTTP_ROOT}/admin/conferences/edit$|" => 'sems_admin_conferences_create',
    
    //User management routes
    "|^{$HTTP_ROOT}/login$|" => 'sems_login',
    "|^{$HTTP_ROOT}/logout$|" => 'sems_logout',
    "|^{$HTTP_ROOT}/signup$|" => 'sems_signup',
    "|^{$HTTP_ROOT}/confirm$|" => 'sems_confirm',
    "|^{$HTTP_ROOT}/profile/(\d+)$|" => 'sems_profile',
    "|^{$HTTP_ROOT}/profile/(\d+)/edit$|" => 'sems_profile_edit',
    
    //Conference routes
    "|^{$HTTP_ROOT}/(\w+)$|" => 'sems_conference',
    "|^{$HTTP_ROOT}/(\w+)/search$|" => 'sems_conference_search',
    
    //Event routes
    "|^{$HTTP_ROOT}/(\w+)/(\w+)$|" => 'sems_event',
    "|^{$HTTP_ROOT}/(\w+)/(\w+)/search$|" => 'sems_event_search',
    "|^{$HTTP_ROOT}/(\w+)/(\w+)/committee$|" => 'sems_event_committee',
    "|^{$HTTP_ROOT}/(\w+)/(\w+)/papers$|" => 'sems_event_papers',
    "|^{$HTTP_ROOT}/(\w+)/(\w+)/papers/(\w+)$|" => 'sems_event_paper',
    "|^{$HTTP_ROOT}/(\w+)/(\w+)/reviews$|" => 'sems_event_reviews',
    "|^{$HTTP_ROOT}/(\w+)/(\w+)/review/(\w+)$|" => 'sems_event_review',
    
    //Home route
    "|^{$HTTP_ROOT}/$|"    => 'sems_home',
    
    //Catch-all 404 route
    '|.*|' => 'sems_notfound');
}

function sems_root() { global $HTTP_ROOT; return $HTTP_ROOT; }

function sems_home_url() { return sems_root()."/"; }
function sems_home() {
  return sems_smarty(function($smarty) {
    $body = $smarty->fetch('home.tpl');
    return ok($body);
  });
}

function sems_notfound() {
  return sems_smarty(function($smarty) {
    return notFound($smarty->fetch('notfound.tpl'));
  });
}