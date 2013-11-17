<?php

define('SEMS_TEMPLATES_PATH', __DIR__.'/templates');

function sems_smarty($f) {
  $smarty = new Smarty();
  $smarty->setTemplateDir(SEMS_TEMPLATES_PATH);
  $smarty->setCompileDir(TMP_PATH.'/templates_c');
  $smarty->setCacheDir(TMP_PATH.'/cache');
  $smarty->muteExpectedErrors();
  return call_user_func($f, $smarty);
}

function sems_smarty_fetch($path, $variables=array()) {
  return sems_smarty(function($smarty) use(&$path, &$variables) {
    $smarty->assign($variables);
    return $smarty->fetch($path);
  });
}