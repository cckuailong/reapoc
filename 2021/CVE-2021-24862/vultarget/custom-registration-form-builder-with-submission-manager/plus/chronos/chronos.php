<?php
/**
 * @package rm_chronos
 * @version alpha
 */
/*
Plugin Name: Chronos for RM
Plugin URI: 
Description: What do you think??
Author: terrific
Version: alpha
*/
define('RM_CHRONOS_ACTION_EMAIL', 201);
define('RM_CHRONOS_DB_VERSION', 1.0);
define('RM_CHRONOS_VERSION', 1.0);

include "libs/rm_chronos.php"; //This does not fit the autoloader structure. :P
function rm_chronos_autoloader($class_name) {
    $file_name = substr($class_name, strlen("RM_Chronos_"));
    
    if(!$file_name)
        return;

    $file_name = strtolower($file_name);
    $base_dir = plugin_dir_path(__FILE__);
    if(file_exists("{$base_dir}/common/{$file_name}.php"))
        include_once "{$base_dir}/common/{$file_name}.php";
    else if(file_exists("{$base_dir}/controllers/{$file_name}.php"))
        include_once "{$base_dir}/controllers/{$file_name}.php";
    else if(file_exists("{$base_dir}/libs/{$file_name}.php"))
        include_once "{$base_dir}/libs/{$file_name}.php";
    else if(file_exists("{$base_dir}/services/{$file_name}.php"))
        include_once "{$base_dir}/services/{$file_name}.php";
}
spl_autoload_register('rm_chronos_autoloader');

RM_Chronos::init();
