<?php
/**
 * Settings
 *
 * @package     Wow_Plugin
 * @copyright   Copyright (c) 2018, Dmytro Lobov
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

include_once( 'icons.php' );
$icons_new = array();
foreach ( $icons as $key => $value ) {
	$icons_new[ $value ] = $value;
}

$count_i = ( ! empty( $param['menu_1']['item_type'] ) ) ? count( $param['menu_1']['item_type'] ) : '0';
if ( $count_i > 0 ) {
	for ( $i = 0; $i < $count_i; $i ++ ) {

		// Icon
		$item_icon_[ $i ] = array(
			'name'   => 'param[menu_1][item_icon][]',
			'class'  => 'icons',
			'type'   => 'select',
			'val'    => isset( $param['menu_1']['item_icon'][ $i ] ) ? $param['menu_1']['item_icon'][ $i ]
				: 'fas fa-hand-point-up',
			'option' => $icons_new,
		);

		// Label for item
		$item_tooltip_[ $i ] = array(
			'name'  => 'param[menu_1][item_tooltip][]',
			'class' => 'item-tooltip',
			'type'  => 'text',
			'val'   => isset( $param['menu_1']['item_tooltip'][ $i ] ) ? $param['menu_1']['item_tooltip'][ $i ] : '',
		);

		// Type of the item
		$item_type_[ $i ] = array(
			'name'   => 'param[menu_1][item_type][]',
			'type'   => 'select',
			'class'  => 'item-type',
			'val'    => isset( $param['menu_1']['item_type'][ $i ] ) ? $param['menu_1']['item_type'][ $i ] : 'link',
			'option' => array(
				'link'         => esc_attr__( 'Link', $this->plugin['text'] ),
			),

		);


		// Link URL
		$item_link_[ $i ] = array(
			'name' => 'param[menu_1][item_link][]',
			'type' => 'text',
			'val'  => isset( $param['menu_1']['item_link'][ $i ] ) ? $param['menu_1']['item_link'][ $i ] : '',
		);


		// Open link in a new window
		$new_tab_[ $i ] = array(
			'name'  => 'param[menu_1][new_tab][]',
			'class' => '',
			'type'  => 'checkbox',
			'val'   => isset( $param['menu_1']['new_tab'][ $i ] ) ? $param['menu_1']['new_tab'][ $i ] : 0,
			'func'  => '',
			'sep'   => '',
		);

		// Font color
		$color_[ $i ] = array(
			'name' => 'param[menu_1][color][]',
			'type' => 'color',
			'val'  => isset( $param['menu_1']['color'][ $i ] ) ? $param['menu_1']['color'][ $i ] : '#ffffff',
		);

		// Font hover color
		$hcolor_[ $i ] = array(
			'name' => 'param[menu_1][hcolor][]',
			'type' => 'color',
			'val'  => isset( $param['menu_1']['hcolor'][ $i ] ) ? $param['menu_1']['hcolor'][ $i ] : '#ffffff',
		);


		// Background
		$bcolor_[ $i ] = array(
			'name' => 'param[menu_1][bcolor][]',
			'type' => 'color',
			'val'  => isset( $param['menu_1']['bcolor'][ $i ] ) ? $param['menu_1']['bcolor'][ $i ] : '#128be0',
		);



		$button_id_[ $i ] = array(
			'name' => 'param[menu_1][button_id][]',
			'type' => 'text',
			'val'  => isset( $param['menu_1']['button_id'][ $i ] ) ? $param['menu_1']['button_id'][ $i ] : '',
		);

		$button_class_[ $i ] = array(
			'name' => 'param[menu_1][button_class][]',
			'type' => 'text',
			'val'  => isset( $param['menu_1']['button_class'][ $i ] ) ? $param['menu_1']['button_class'][ $i ] : '',
		);

		$link_rel_[ $i ] = array(
			'name' => 'param[menu_1][link_rel][]',
			'type' => 'text',
			'val'  => isset( $param['menu_1']['link_rel'][ $i ] ) ? $param['menu_1']['link_rel'][ $i ] : '',
		);


	}

}

$item_tooltip_help = array(
	'text' => esc_attr__( 'Set the text for menu item.', $this->plugin['text'] ),
);

$item_type_help = array(
	'title' => esc_attr__( 'Types of the button which can be select', $this->plugin['text'] ),
	'ul'    => array(
		esc_attr__( 'Link - insert any link', $this->plugin['text'] ),
	),
);

$button_id_help = array(
	'text' => esc_attr__( 'Set the attribute ID for the menu item or left empty.', $this->plugin['text'] ),
);

$button_class_help = array(
	'text' => esc_attr__( 'Set the attribute CLASS for the menu item or left empty.', $this->plugin['text'] ),
);
