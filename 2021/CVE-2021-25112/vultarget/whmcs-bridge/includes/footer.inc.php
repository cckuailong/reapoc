<?php

function cc_whmcs_bridge_footer($nodisplay='') {
	$bail_out = ( ( defined( 'WP_ADMIN' ) && WP_ADMIN == true ) || ( strpos( $_SERVER[ 'PHP_SELF' ], 'wp-admin' ) !== false ) );
	if ( $bail_out ) return $footer;
	if (get_option('cc_whmcs_bridge_sso_active')) return;
	if (get_option('cc_whmcs_bridge_footer')=='None') return;
	
	$msg='<center style="margin-top:0px;font-size:small">';
	$msg.='WordPress and WHMCS integration by <a href="http://i-plugins.com" target="_blank">i-Plugins</a>';
	$msg.='</center>';
	$cc_footer=true;
	if ($nodisplay===true) return $msg;
	else echo $msg;

}
