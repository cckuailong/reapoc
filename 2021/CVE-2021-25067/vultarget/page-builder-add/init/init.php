<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

function ulpb_Load_admin_Class() {
	$ulpb_load_admin_class = new ULPB_AdminClass();
	$ulpb_load_ajax_requests = new ULPB_Ajax_Requests();
}

ulpb_Load_admin_Class();

?>