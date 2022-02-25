<?php

/**
* Core constants
*/
define('PRSV_BASE_PATH',dirname(__FILE__));

define('PRSV_BASE_PATH_CLASSES',PRSV_BASE_PATH.'/lib/classes');

define('PRSV_BASE_PATH_HELPERS',PRSV_BASE_PATH.'/lib/helpers');

define('PRSV_BASE_PATH_CONFIGS',PRSV_BASE_PATH.'/configs');

define('PRSV_BASE_PATH_RESOURCES',PRSV_BASE_PATH.'/resources');

define('PRSV_BASE_PATH_RESOURCES_BACKEND',PRSV_BASE_PATH_RESOURCES .'/backend');

define('PRSV_BASE_PATH_RESOURCES_FRONTEND',PRSV_BASE_PATH_RESOURCES .'/frontend');

/**
* Configurations
*/
require_once PRSV_BASE_PATH_CONFIGS. '/config.php';

/**
* Ps classes
*/
require_once PRSV_BASE_PATH_CLASSES . '/PerfectSurvey.php';

global $ps;/*@var $ps_configs PerfectSurvey*/

$ps  = new PerfectSurvey();

$ps->boot();

return $ps;
