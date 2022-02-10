<?php
/**
 * Uninstall file
 * Called when plugin is uninstalled
 *
 * Tasks:
 * 1. reschedules paused events
 * 2. removes acm_* options from wp_options
 *
 * @package advanced-cron-manager
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

$plugin_version = 'x';
$plugin_file    = dirname( __FILE__ ) . '/advanced-cron-manager.php';

/**
 * Fire up Composer's autoloader
 */
require_once __DIR__ . '/vendor/autoload.php';

/**
 * Bootstrap plugin
 */

$ajax = function() {
	return new underDEV\Utils\Ajax();
};

$schedules_library = new underDEV\AdvancedCronManager\Cron\SchedulesLibrary( $ajax() );

$schedules = function() use ( $schedules_library ) {
	return new underDEV\AdvancedCronManager\Cron\Schedules( $schedules_library );
};

$events = function() use ( $schedules ) {
	return new underDEV\AdvancedCronManager\Cron\Events( $schedules() );
};

$events_library = new underDEV\AdvancedCronManager\Cron\EventsLibrary( $schedules(), $events() );

// 1.

$paused_events = $events_library->register_paused( array() );

foreach ( $paused_events as $event ) {
	$events_library->unpause( $event );
}

// 2.

delete_option( 'acm_paused_events' );
delete_option( 'acm_schedules' );
delete_option( 'acm_server_settings' );
