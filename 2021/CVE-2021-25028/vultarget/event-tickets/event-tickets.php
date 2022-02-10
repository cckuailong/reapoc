<?php
/*
Plugin Name: Event Tickets
Plugin URI:  https://evnt.is/1acb
Description: Event Tickets allows you to sell basic tickets and collect RSVPs from any post, page, or event.
Version: 5.2.1
Author: The Events Calendar
Author URI: https://evnt.is/1aor
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: event-tickets
Domain Path: /lang/
 */
/*
 Copyright 2010-2012 by Modern Tribe Inc and the contributors

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License
 as published by the Free Software Foundation; either version 2
 of the License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

define( 'EVENT_TICKETS_DIR', dirname( __FILE__ ) );
define( 'EVENT_TICKETS_MAIN_PLUGIN_FILE', __FILE__ );

// Load the required php min version functions
require_once dirname( EVENT_TICKETS_MAIN_PLUGIN_FILE ) . '/src/functions/php-min-version.php';

// Load the Composer autoload file.
require_once dirname( EVENT_TICKETS_MAIN_PLUGIN_FILE ) . '/vendor/autoload.php';

/**
 * Verifies if we need to warn the user about min PHP version and bail to avoid fatals
 */
if ( tribe_is_not_min_php_version() ) {
	tribe_not_php_version_textdomain( 'event-tickets', EVENT_TICKETS_MAIN_PLUGIN_FILE );

	/**
	 * Include the plugin name into the correct place
	 *
	 * @since  4.10
	 *
	 * @param  array $names current list of names
	 *
	 * @return array
	 */
	function tribe_tickets_not_php_version_plugin_name( $names ) {
		$names['event-tickets'] = esc_html__( 'Event Tickets', 'event-tickets' );
		return $names;
	}

	add_filter( 'tribe_not_php_version_names', 'tribe_tickets_not_php_version_plugin_name' );
	if ( ! has_filter( 'admin_notices', 'tribe_not_php_version_notice' ) ) {
		add_action( 'admin_notices', 'tribe_not_php_version_notice' );
	}
	return false;
}

// the main plugin class
require_once EVENT_TICKETS_DIR . '/src/Tribe/Main.php';

Tribe__Tickets__Main::instance();
