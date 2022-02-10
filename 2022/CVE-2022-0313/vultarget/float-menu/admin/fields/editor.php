<?php
/**
 * Template for field editor
 *
 * @package     Wow_Plugin
 * @copyright   Copyright (c) 2018, Dmytro Lobov
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$settings = array(
	'wpautop'       => 0,
	'textarea_name' => '' . $name . '',
	'textarea_rows' => 15,
);

wp_editor( $val, $id, $settings );