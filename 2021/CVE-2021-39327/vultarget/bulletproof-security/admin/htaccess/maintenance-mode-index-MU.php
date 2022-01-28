<?php
#############################################################
# If you manually edit this file make a backup of this file #
# this file will be replaced when you upgrade BPS Pro       #
#############################################################
#
# BEGIN BPS INCLUDE
if ( file_exists( dirname( __FILE__ ) . '/bps-maintenance-values.php' ) ) {
include( dirname( __FILE__ ) . '/bps-maintenance-values.php' );
}
# END BPS INCLUDE

define('WP_USE_THEMES', true);

# BEGIN PRIMARY SITE STATUS
$primary_site_status = 'Off';
# END PRIMARY SITE STATUS

# BEGIN SUBSITE STATUS
# END SUBSITE STATUS

# BEGIN SUBSITE URI
# END SUBSITE URI

/*
NOTES:
The orginal WP index.php file is backed up to /wp-content/bps-backup/master-backups/backup_index.php the first time mmode is turned On.
These BPS files need to exist in the root folder permanently: bps-maintenance.php, bps-maintenance-values.php and the bps index.php file (this file).
New Switch cases for subsites are created if they do not already exist when mmode is turned On.
The Subsite URI and Subsite STATUS variables are created when mmode is turned On.
The Subsite STATUS variable is Off when mmode is turned Off.
The Primary site STATUS variable is static. The value can only be set by the Primary site when mmode is turned On.
Each subsite has its own bps-maintenance-{subsite-uri}.php file created when Save Options is clicked.
Each subsite has its own bps-maintenance-values-{subsite-uri}.php file created when Save Options is clicked.
These BPS Subsite files are deleted on Turn Off: bps-maintenance-{subsite-uri}.php & bps-maintenance-values-{subsite-uri}.php
Override: If $all_sites or $all_subsites == 1 then bps-maintenance.php is loaded instead of bps-maintenance-{subsite-uri}.php
All BPS index.php writing occurs in the maintenance-mode-index-MU.php Master file & then it is copied to the root index.php file again.
For Network GWIOD site types the site root directory index file is written to directly.
Network subdomain sites use a HTTP_HOST instead of REQUEST_URI condition in the switch and the root domain/subdomain is used for checking.
*/

$one_octet = preg_match( "/\d{1,3}\./", $_SERVER['REMOTE_ADDR'], $matches_one );
$two_octets = preg_match( "/\d{1,3}\.\d{1,3}\./", $_SERVER['REMOTE_ADDR'], $matches_two );
$three_octets = preg_match( "/\d{1,3}\.\d{1,3}\.\d{1,3}\./", $_SERVER['REMOTE_ADDR'], $matches_three );

// Used in Network Subdomain sites only: The switch condition is $subdomain instead of $_SERVER['REQUEST_URI']
@$subdomain = array_shift( explode( "." , str_replace( 'www.', "", $_SERVER['HTTP_HOST'] ) ) );

switch ( $_SERVER['REQUEST_URI'] ) {
    case $primary_site_uri:
		# BEGIN BPS MAINTENANCE MODE PRIMARY IP
		$bps_maintenance_ip = array('127.0.0.1');
		# END BPS MAINTENANCE MODE PRIMARY IP
		if ( $all_sites == '1' ) {
		require( dirname( __FILE__ ) . '/bps-maintenance.php' );
		} else {		
		if ( in_array( $_SERVER['REMOTE_ADDR'], $bps_maintenance_ip ) || in_array( $matches_three[0], $bps_maintenance_ip ) || in_array( $matches_two[0], $bps_maintenance_ip ) || in_array( $matches_one[0], $bps_maintenance_ip ) || $primary_site_status == 'Off' || $all_subsites == '1' && $primary_site_status != 'On' ) {
		require( dirname( __FILE__ ) . '/wp-blog-header.php' );
		} else {
		require( dirname( __FILE__ ) . '/bps-maintenance.php' );
		}		
		}
		break;
	default:
		if ( $all_sites == '1' || $all_subsites == '1' ) {
		require( dirname( __FILE__ ) . '/bps-maintenance.php' );
		} else {
		require( dirname( __FILE__ ) . '/wp-blog-header.php' );
		}
	# END BPS SWITCH
	}