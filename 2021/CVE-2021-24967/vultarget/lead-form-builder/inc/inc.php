<?php
  if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
// file include
global $wpdb;
define('LFB_FORM_FIELD_TBL', $wpdb->prefix . 'lead_form');
define('LFB_FORM_DATA_TBL', $wpdb->prefix . 'lead_form_data');
include_once( plugin_dir_path(__FILE__) . 'lfb-color-settings.php' );
include_once( plugin_dir_path(__FILE__) . 'lf-install.php' );
include_once( plugin_dir_path(__FILE__) . 'lf-shortcode.php' );
if ( is_admin() ) {
include_once( plugin_dir_path(__FILE__) . 'edit-delete-form.php' );
include_once( plugin_dir_path(__FILE__) . 'create-lead-form.php' );
}
include_once( plugin_dir_path(__FILE__) . 'lfb-get-formdata.php' );
include_once( plugin_dir_path(__FILE__) . 'email-setting.php' );
include_once( plugin_dir_path(__FILE__) . 'show-forms-backend.php' );
include_once( plugin_dir_path(__FILE__) . 'front-end.php' );
include_once( plugin_dir_path(__FILE__) . 'show-lead.php' );
include_once( plugin_dir_path(__FILE__) . 'lead-store-type.php' );
include_once( plugin_dir_path(__FILE__) . 'ajax-functions.php' );

?>