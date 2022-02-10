<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
function uaf_client_assets() {
	$uaf_upload 	= wp_upload_dir();
	$uaf_upload_url = set_url_scheme($uaf_upload['baseurl']);
	$uaf_upload_url = $uaf_upload_url . '/useanyfont/';
	wp_register_style( 'uaf_client_css', $uaf_upload_url.'uaf.css', array(),get_option('uaf_css_updated_timestamp'));
	wp_enqueue_style( 'uaf_client_css' );
}

if (!function_exists('array_key_first')) { // FOR OLDER VERSION PHP SUPPORT
    function array_key_first(array $arr) {
        foreach($arr as $key => $unused) {
			return $key;
		}
		return NULL;
	}
}