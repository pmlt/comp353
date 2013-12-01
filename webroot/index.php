<?php

error_reporting(E_ALL & ~E_NOTICE);

include '../dependencies.php';

//Always start the session, this is a very user-driven site
session_start();

$result = http_route(http_request_uri(), sems_routes())->send();
