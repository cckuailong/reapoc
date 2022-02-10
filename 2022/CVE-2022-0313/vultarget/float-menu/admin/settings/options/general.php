<?php
/**
 * Main Settings
 *
 * @package     Wow_Plugin
 * @copyright   Copyright (c) 2018, Dmytro Lobov
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$menu_status = array(
	'id'   => 'menu_status',
	'name' => 'param[menu_status]',
	'type' => 'checkbox',
	'val'  => isset( $param['menu_status'] ) ? $param['menu_status'] : 1,
);

$menu_status_help = array(
	'text' => esc_attr__( 'If check - the menu will show on the frontend. If uncheck - menu not displayed on the frontend.', $this->plugin['text'] ),
);

$test_mode = array(
	'id'   => 'test_mode',
	'name' => 'param[test_mode]',
	'type' => 'checkbox',
	'val'  => isset( $param['test_mode'] ) ? $param['test_mode'] : 0,
);

$test_mode_help = array(
	'text' => esc_attr__( 'If test mode is enabled, the menu will show for admin only.', $this->plugin['text'] ),
);

$tax_args   = array(
	'public'   => true,
	'_builtin' => false
);
$output     = 'names';
$operator   = 'and';
$taxonomies = get_taxonomies( $tax_args, $output, $operator );

$show_option = array(
	'all'        => esc_attr__( 'All posts and pages', 'float-menu' ),
	'shortecode' => esc_attr__( 'Shortcode', 'float-menu' ),
);


$show = array(
	'id'     => 'show',
	'name'   => 'param[show]',
	'type'   => 'select',
	'val'    => isset( $param['show'] ) ? $param['show'] : 'all',
	'option' => $show_option,
	'func'   => 'showchange(this);',
	'sep'    => 'p',
);

$show_help = array(
	'text' => esc_attr__( 'Choose a condition to target to specific content.', $this->plugin['text'] ),
);