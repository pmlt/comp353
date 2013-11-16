<?php

function sems_routes() {
  //XXX must define routes here.
  return array(
    '/.*/' => 'sems_notfound');
}

function sems_notfound() {
  return notFound("SEMS: Page not found.");
}