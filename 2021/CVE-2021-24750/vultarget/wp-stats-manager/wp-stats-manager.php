<?php
/**
 * Plugin Name: WP Visitor Statistics (Real Time Traffic)
 * Plugin URI: http://plugins-market.com/contact-us
 * Description: This plugin will track the web analytics for each page and show various analytics report in admin panel as well as in front end.
 * Version: 4.7
 * Author: osamaesh 
 * Author URI: http://plugins-market.com/
 * Developer: Prism I.T. Systems
 * Developer URI: http://www.prismitsystems.com
 * Text Domain: wp-stats-manager
 * Domain Path: /languages
 * Copyright:  Plugins-market 2017.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
**/


if ( ! defined( 'ABSPATH' ) ) {
    die( 'Access denied.' );
}

//require_once( ABSPATH. 'wp-includes/pluggable.php');
include_once('notifications.php');



define( 'WSM_PREFIX','wsm' );                       
define( 'WSM_NAME',__('Visitor Statistics (Free)','wp-stats-manager') );
define( 'WSM_DIR', plugin_dir_path( __FILE__ ) );
define( 'WSM_URL', plugin_dir_url( __FILE__ ) );
define( 'WSM_FILE', __FILE__ );
define( 'WSM_ONLINE_SESSION',15 ); //DEFINE ONLINE SESSION TIME IN MINUTES
define( 'WSM_PAGE_LIMIT',10 ); //DEFINE ONLINE SESSION TIME IN MINUTES
global $wsmAdminColors,$wsmAdminJavaScript,$wsmAdminPageHooks,$wsmRequestArray,$arrCashedStats;
$wsmAdminJavaScript='';
$wsmAdminPageHooks=array();
$wsmRequestArray=array();
if(isset($_REQUEST) && is_array($_REQUEST)){
    $wsmRequestArray=$_REQUEST;
}
include_once(WSM_DIR .'includes/'. WSM_PREFIX."_init.php");
wsmInitPlugin::initWsm();
add_action( 'plugins_loaded', function() { load_plugin_textdomain( 'wp-stats-manager', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );	} );