<?php if(!defined('ABSPATH')) exit;

/**
* Plugin Name
*/
define('PRSV_PLUGIN_NAME','Perfect Survey');

/**
* Plugin short code
*/
define('PRSV_PLUGIN_CODE','ps');

/**
* Plugin option code
*/
define('PRSV_OPTION_CODE','ps_options');

/*
* Global option field
*/
define('PRSV_GLOBAL_OPTION','ps_all_global_options');

/*
* Define tinymce function
*/
define('PRSV_DEFAULT_EDITOR','tinymce');

/**
* Version of plugin
*/
define('PRSV_PLUGIN_VERSION','1.5.0');

/**
* Define text domain
*/
define ('PRSV_TEXTDOMAIN', PRSV_PLUGIN_CODE);

/**
* Define plugin name folder
*/
define('PRSV_NAMING', 'perfect-survey');

/**
* Post type for perfect survey plugin
*/
define('PRSV_POST_TYPE', PRSV_PLUGIN_CODE);

/**
* Short code name
*/
define('PRSV_SHORTCODE_NAME','perfect_survey');

/**
* Short code format, %s is the post_id
*/
define('PRSV_SHORTCODE','['.PRSV_SHORTCODE_NAME.' id="%s"]');

/**
* Nouce field name
*/
define('PRSV_NOUNCE_FIELD_NAME','wp_ps_nounce');

/**
* Wp nounce field value
*/
define('PRSV_NOUNCE_FIELD_VALUE','wp_ps_survey');

/**
* Return name url
*/
define('PRSV_URL_WEBSITE','getperfectsurvey');

/*
* Return created
*/
define('PRSV_URL_EDITOR_DANKO','Created');