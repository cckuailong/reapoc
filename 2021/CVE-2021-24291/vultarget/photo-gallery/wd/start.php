<?php
if ( !defined('ABSPATH') ) {
  exit;
}
define('TEN_WEB_NEW_LIB_API_PLUGIN_DATA_PATH', 'https://api.web-dorado.com/v2/_id_/plugindata');
define('TEN_WEB_NEW_LIB_SUBSCRIBE_URL', 'https://core.10web.io/api/wp-subscribe');
define('TEN_WEB_NEW_LIB_DEACTIVATION_URL', 'https://core.10web.io/api/deactivation_reasons');
require_once dirname(__FILE__) . '/config.php';
/**
 * @param options for Plugin details.
 * prefix;
 * wd_plugin_id;
 * plugin_title;
 * plugin_dir;
 * plugin_main_file;
 * description;
 * plugin_features;
 * video_youtube_id;
 * plugin_wd_url;
 * plugin_wd_demo_link;
 * plugin_wd_addons_link;
 * plugin_wizard_link;
 * after_subscribe;
 * plugin_menu_title;
 * plugin_menu_icon;
 * custom_post;
 */
function ten_web_new_lib_init( $options ) {

  // load files
  require_once dirname(__FILE__) . '/wd.php';
  $wd = new TenWebLibNew();
  $wd->wd_init($options);
}
