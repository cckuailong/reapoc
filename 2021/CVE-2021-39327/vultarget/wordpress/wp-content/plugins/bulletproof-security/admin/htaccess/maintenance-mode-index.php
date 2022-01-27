<?php
#############################################################
# If you manually edit this file make a backup of this file #
# this file will be replaced when you upgrade BPS Pro       #
#############################################################
define('WP_USE_THEMES', true);

# BEGIN BPS MAINTENANCE MODE IP
$bps_maintenance_ip = array('100.99.88.77', '200.66.55.44', '44.33.22.1');
# END BPS MAINTENANCE MODE IP

$one_octet = preg_match( "/\d{1,3}\./", $_SERVER['REMOTE_ADDR'], $matches_one );
$two_octets = preg_match( "/\d{1,3}\.\d{1,3}\./", $_SERVER['REMOTE_ADDR'], $matches_two );
$three_octets = preg_match( "/\d{1,3}\.\d{1,3}\.\d{1,3}\./", $_SERVER['REMOTE_ADDR'], $matches_three );

if ( in_array( $_SERVER['REMOTE_ADDR'], $bps_maintenance_ip ) || in_array( $matches_three[0], $bps_maintenance_ip ) || in_array( $matches_two[0], $bps_maintenance_ip ) || in_array( $matches_one[0], $bps_maintenance_ip )) {
require( dirname( __FILE__ ) . '/wp-blog-header.php' );
} else {
require( dirname( __FILE__ ) . '/bps-maintenance.php' );
}