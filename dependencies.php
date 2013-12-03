<?php

define('PROJECT_PATH', __DIR__);
define('TMP_PATH', PROJECT_PATH.'/tmp');

include 'Smarty-3.1.15/libs/Smarty.class.php';
include 'web/http.php';
include 'sems/config.php';
include 'sems/identity.php';
include 'sems/permissions.php';
include 'sems/routes.php';
include 'sems/db.php';
include 'sems/validation.php';
include 'sems/templates.php';
include 'sems/utils.php';
