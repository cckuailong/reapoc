<?php
/**
 * Plugin Name: Advanced Cron Manager
 * Description: View, pause, remove, edit and add WP Cron events.
 * Version: 2.4.1
 * Author: BracketSpace
 * Author URI: https://bracketspace.com
 * License: GPL3
 * Text Domain: advanced-cron-manager
 *
 * @package advanced-cron-manager
 */

$plugin_version = '2.4.1';
$plugin_file    = __FILE__;

/**
 * Fire up Composer's autoloader
 */
require_once __DIR__ . '/vendor/autoload.php';

$requirements = new underDEV_Requirements( __( 'Advanced Cron Manager', 'advanced-cron-manager' ), array(
	'php'         => '5.3',
	'wp'          => '3.6',
	'old_plugins' => array(
		'advanced-cron-manager-pro/acm-pro.php' => array(
			'name'    => 'Advanced Cron Manager PRO',
			'version' => '2.0',
		),
	),
) );

/**
 * Check if old plugins are active
 *
 * @param  array  $plugins Array with plugins,
 *                         where key is the plugin file and value is the version.
 * @param  object $r       Requirements object.
 * @return void
 */
function acm_check_old_plugins( $plugins, $r ) {

	foreach ( $plugins as $plugin_file => $plugin_data ) {

		if ( ! file_exists( WP_PLUGIN_DIR . '/' . $plugin_file ) ) {
			continue;
		}

		// phpcs:ignore
		$plugin_api_data = @get_file_data( WP_PLUGIN_DIR . '/' . $plugin_file, array( 'Version' ) );

		if ( ! isset( $plugin_api_data[0] ) ) {
			continue;
		}

		$old_plugin_version = $plugin_api_data[0];

		if ( ! empty( $old_plugin_version ) && version_compare( $old_plugin_version, $plugin_data['version'], '<' ) ) {
			$r->add_error( sprintf( '%s plugin at least in version %s', $plugin_data['name'], $plugin_data['version'] ) );
		}
	}

}

if ( method_exists( $requirements, 'add_check' ) ) {
	$requirements->add_check( 'old_plugins', 'acm_check_old_plugins' );
}

if ( ! $requirements->satisfied() ) {

	add_action( 'admin_notices', array( $requirements, 'notice' ) );
	return;

}

/**
 * Instances and Closures
 */

$files = new underDEV\Utils\Files( $plugin_file );

$view = function() use ( $files ) {
	return new underDEV\Utils\View( $files );
};

$ajax = function() {
	return new underDEV\Utils\Ajax();
};

$server_settings = function() use ( $view, $ajax ) {
	return new underDEV\AdvancedCronManager\Server\Settings( $view(), $ajax() );
};

$misc = function() use ( $view ) {
	return new underDEV\AdvancedCronManager\Misc( $view() );
};

$server_processor = function() use ( $server_settings ) {
	return new underDEV\AdvancedCronManager\Server\Processor( $server_settings() );
};

$schedules_library = new underDEV\AdvancedCronManager\Cron\SchedulesLibrary( $ajax() );

$schedules = function() use ( $schedules_library ) {
	return new underDEV\AdvancedCronManager\Cron\Schedules( $schedules_library );
};

$schedules_actions = function() use ( $ajax, $schedules_library ) {
	return new underDEV\AdvancedCronManager\Cron\SchedulesActions( $ajax(), $schedules_library );
};

$events = function() use ( $schedules ) {
	return new underDEV\AdvancedCronManager\Cron\Events( $schedules() );
};

$events_library = function() use ( $schedules, $events ) {
	return new underDEV\AdvancedCronManager\Cron\EventsLibrary( $schedules(), $events() );
};

$events_actions = function() use ( $ajax, $events, $events_library, $schedules ) {
	return new underDEV\AdvancedCronManager\Cron\EventsActions( $ajax(), $events(), $events_library(), $schedules() );
};

$internationalization = function() use ( $files ) {
	return new underDEV\AdvancedCronManager\Internationalization( $files );
};

$admin_screen = function() use ( $view, $ajax, $schedules, $events ) {
	return new underDEV\AdvancedCronManager\AdminScreen( $view(), $ajax(), $schedules(), $events() );
};

$screen_registerer = new underDEV\AdvancedCronManager\ScreenRegisterer( $admin_screen() );

$assets = new underDEV\AdvancedCronManager\Assets( $plugin_version, $files, $screen_registerer );

$form_provider = function () use ( $view, $ajax, $schedules_library, $schedules ) {
	return new underDEV\AdvancedCronManager\FormProvider( $view(), $ajax(), $schedules_library, $schedules() );
};

/**
 * Actions
 */

// Load textdomain.
add_action( 'plugins_loaded', array( $internationalization(), 'load_textdomain' ) );

// Add plugin's screen in the admin.
add_action( 'admin_menu', array( $screen_registerer, 'register_screen' ) );

// Add main section parts on the admin screen.
add_action( 'advanced-cron-manager/screen/main', array( $admin_screen(), 'load_searchbox_part' ), 10, 1 );
add_action( 'advanced-cron-manager/screen/main', array( $admin_screen(), 'load_events_table_part' ), 20, 1 );

// Add sidebar section parts on the admin screen.
add_action( 'advanced-cron-manager/screen/sidebar', array( $admin_screen(), 'load_schedules_table_part' ), 10, 1 );

// Add general parts on the admin screen.
add_action( 'advanced-cron-manager/screen/wrap/after', array( $admin_screen(), 'load_slidebar_part' ), 10, 1 );

// Add tabs to event details.
add_filter( 'advanced-cron-manager/screen/event/details/tabs', array( $admin_screen(), 'add_default_event_details_tabs' ), 10, 1 );

// Enqueue assets.
add_action( 'admin_enqueue_scripts', array( $assets, 'enqueue_admin' ), 10, 1 );

// Forms.
add_action( 'wp_ajax_acm/schedule/add/form', array( $form_provider(), 'add_schedule' ) );
add_action( 'wp_ajax_acm/schedule/edit/form', array( $form_provider(), 'edit_schedule' ) );
add_action( 'wp_ajax_acm/event/add/form', array( $form_provider(), 'add_event' ) );

// Schedules.
add_filter( 'cron_schedules', array( $schedules_library, 'register' ), 10, 1 ); // phpcs:ignore
add_action( 'wp_ajax_acm/rerender/schedules', array( $admin_screen(), 'ajax_rerender_schedules_table' ) );
add_action( 'wp_ajax_acm/schedule/insert', array( $schedules_actions(), 'insert' ) );
add_action( 'wp_ajax_acm/schedule/edit', array( $schedules_actions(), 'edit' ) );
add_action( 'wp_ajax_acm/schedule/remove', array( $schedules_actions(), 'remove' ) );

// Events.
add_filter( 'advanced-cron-manager/events/array', array( $events_library(), 'register_paused' ), 10, 1 );
add_action( 'wp_ajax_acm/rerender/events', array( $admin_screen(), 'ajax_rerender_events_table' ) );
add_action( 'wp_ajax_acm/event/insert', array( $events_actions(), 'insert' ) );
add_action( 'wp_ajax_acm/event/run', array( $events_actions(), 'run' ) );
add_action( 'wp_ajax_acm/event/remove', array( $events_actions(), 'remove' ) );
add_action( 'wp_ajax_acm/event/pause', array( $events_actions(), 'pause' ) );
add_action( 'wp_ajax_acm/event/unpause', array( $events_actions(), 'unpause' ) );

// Server scheduler.
add_action( 'advanced-cron-manager/screen/sidebar', array( $server_settings(), 'load_settings_part' ), 10, 1 );
add_action( 'wp_ajax_acm/server/settings/save', array( $server_settings(), 'save_settings' ) );
add_action( 'plugins_loaded', array( $server_processor(), 'block_cron_executions' ), 10, 1 );

// Plugin row actions.
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $misc(), 'plugin_action_link' ) );

// Notification promo.
add_action( 'plugins_loaded', function() use ( $misc ) {
	if ( ! function_exists( 'register_trigger' ) ) {
		add_action( 'advanced-cron-manager/screen/sidebar', array( $misc(), 'load_notification_promo_part' ), 1000, 1 );
	}
}, 10, 1 );
