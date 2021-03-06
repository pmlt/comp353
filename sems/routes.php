<?php

include 'actions/user.php';
include 'actions/conference.php';
include 'actions/event.php';
include 'actions/message.php';
include 'actions/paper.php';
include 'actions/review.php';

define('SEMS_ROOT', '/comp353');
$HTTP_ROOT = '/comp353';

function sems_routes() {
  global $HTTP_ROOT;
  return array(
    //CSS
    "|^{$HTTP_ROOT}/css/sems.css$|" => 'sems_css',

    //User management routes
    "|^{$HTTP_ROOT}/login$|" => 'sems_login',
    "|^{$HTTP_ROOT}/logout$|" => 'sems_logout',
    "|^{$HTTP_ROOT}/signup$|" => 'sems_signup',
    "|^{$HTTP_ROOT}/confirm$|" => 'sems_confirm',
    "|^{$HTTP_ROOT}/profile/(\d+)$|" => 'sems_profile',
    "|^{$HTTP_ROOT}/profile/(\d+)/edit$|" => 'sems_profile_edit',
    
    //Conference routes
    "|^{$HTTP_ROOT}/create$|" => 'sems_conference_create',
    "|^{$HTTP_ROOT}/(\w+)$|" => 'sems_conference',
    "|^{$HTTP_ROOT}/(\w+)/edit$|" => 'sems_conference_edit',
    
    //Event routes
    "|^{$HTTP_ROOT}/(\w+)/create$|" => 'sems_event_create',
    "|^{$HTTP_ROOT}/(\w+)/(\w+)$|" => 'sems_event',
    "|^{$HTTP_ROOT}/(\w+)/(\w+)/search$|" => 'sems_event_search',
    "|^{$HTTP_ROOT}/(\w+)/(\w+)/committee$|" => 'sems_event_committee',
    "|^{$HTTP_ROOT}/(\w+)/(\w+)/edit$|" => 'sems_event_edit',

    //Paper routes
    "|^{$HTTP_ROOT}/(\w+)/(\w+)/papers/submit$|" => 'sems_papers_submit',
    "|^{$HTTP_ROOT}/(\w+)/(\w+)/papers/decision$|" => 'sems_papers_decision',
    "|^{$HTTP_ROOT}/(\w+)/(\w+)/papers/epublish$|" => 'sems_papers_epublish',
    "|^{$HTTP_ROOT}/(\w+)/(\w+)/papers/(\w+)$|" => 'sems_paper',

    //Review routes
    "|^{$HTTP_ROOT}/(\w+)/(\w+)/reviews/auction$|" => 'sems_reviews_auction',
    "|^{$HTTP_ROOT}/(\w+)/(\w+)/reviews/assign$|" => 'sems_reviews_assign',
    "|^{$HTTP_ROOT}/(\w+)/(\w+)/reviews/(\w+)$|" => 'sems_review',

    //Message routes
    "|^{$HTTP_ROOT}/(\w+)/(\w+)/messages/create$|" => 'sems_messages_create',
    "|^{$HTTP_ROOT}/(\w+)/(\w+)/messages/(\w+)$|" => 'sems_message',
    "|^{$HTTP_ROOT}/(\w+)/(\w+)/messages/(\w+)/edit$|" => 'sems_message_edit',
    
    //Home route
    "|^{$HTTP_ROOT}/$|"    => 'sems_home',
    
    //Catch-all 404 route
    '|.*|' => 'sems_notfound');
}

function sems_root() { global $HTTP_ROOT; return $HTTP_ROOT; }

function sems_css() { return ok(sems_smarty_fetch('css/sems.tpl.css'), array('Content-Type: text/css')); }

function sems_home_url() { return sems_root()."/"; }
function sems_home() {
  return sems_db(function($db) {
    // Fetch list of conferences for easy selection.
    $vars = array();
    $vars['conferences'] = stable($db, "SELECT conference_id, name, description FROM Conference ORDER BY name");

    $actions = array(
      array(
        'url' => sems_conference_create_url(),
        'label' => 'Create a new conference',
        'permission' => array('role','admin')));

    $vars['breadcrumb'] = sems_breadcrumb(
      sems_bc_home());
    $vars['actions'] = sems_actions(
      sems_action_create_conference());
    return ok(sems_smarty_fetch('home.tpl', $vars));
  });
}

function sems_notfound() {
  return sems_smarty(function($smarty) {
    return notFound($smarty->fetch('notfound.tpl'));
  });
}

function sems_forbidden($reason="") {
  return sems_smarty(function($smarty) use($reason) {
    $smarty->assign('reason', $reason);
    return forbidden($smarty->fetch('forbidden.tpl'));
  });
}

