<?php
/*
	Plugin Name: Download Monitor
	Plugin URI: https://www.download-monitor.com
	Description: A full solution for managing and selling downloadable files, monitoring downloads and outputting download links and file information on your WordPress powered site.
	Version: 4.4.4
	Author: WPChill
	Author URI: https://wpchill.com
	Requires at least: 5.4
	Tested up to: 5.7
	Text Domain: download-monitor

	License: GPL v3

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.

	Original project created by Mike Jolley.
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

// Define DLM Version
define( 'DLM_VERSION', '4.4.4' );

// Define DLM FILE
define( 'DLM_PLUGIN_FILE', __FILE__ );

if ( version_compare( PHP_VERSION, '5.3.0' ) >= 0 ) {
	require_once plugin_dir_path( DLM_PLUGIN_FILE ) . 'includes/bootstrap.php';
}else {
	require_once plugin_dir_path( DLM_PLUGIN_FILE ) . 'includes/php-too-low.php';
}

