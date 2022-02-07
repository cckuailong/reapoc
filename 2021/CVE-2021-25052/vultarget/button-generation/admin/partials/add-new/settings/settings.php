<?php
/**
 * Button settings options parameters
 *
 * @package     Wow_Plugin
 * @subpackage  Add/Settings/Button
 * @author      Wow-Company <support@wow-company.com>
 * @copyright   2019 Wow-Company
 * @license     GNU Public License
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

include_once( 'icons.php' );

// create array of the icons
$icons_new = array();
foreach ( $icons as $key => $value ) {
	$icons_new[ $value ] = $value;
}

// Type
$type = array(
	'id'     => 'type',
	'name'   => 'param[type]',
	'type'   => 'select',
	'val'    => isset( $param['type'] ) ? $param['type'] : 'standard',
	'option' => array(
		'standard' => esc_attr__( 'Standard', $this->plugin['text'] ),
		'floating' => esc_attr__( 'Floating', $this->plugin['text'] ),
	),
	'func'   => 'buttontype();',
);

// Type helper
$type_help = array(
	'title' => esc_attr__( 'Select the type of button you want to use:', $this->plugin['text'] ),
	'ul'    => array(
		esc_attr__( '<strong>Standart</strong> - inserting a button only via shortcode into the content;', $this->plugin['text'] ),
		esc_attr__( '<strong>Floating</strong> - fixed floating button on the page;', $this->plugin['text'] ),
	),
);

// Appearance
$appearance = array(
	'id'     => 'appearance',
	'name'   => 'param[appearance]',
	'type'   => 'select',
	'val'    => isset( $param['appearance'] ) ? $param['appearance'] : 'text',
	'option' => array(
		'text'      => esc_attr__( 'Only Text', $this->plugin['text'] ),
		'text_icon' => esc_attr__( 'Text & Icon', $this->plugin['text'] ),
		'icon'      => esc_attr__( 'Icon', $this->plugin['text'] ),
	),
	'func'   => 'buttonappearance();',
);

// Appearance helper
$appearance_help = array(
	'text' => esc_attr__( 'Set the button appearance.', $this->plugin['text'] ),
);

// Location
$location = array(
	'id'     => 'location',
	'name'   => 'param[location]',
	'type'   => 'select',
	'val'    => isset( $param['location'] ) ? $param['location'] : 'center',
	'option' => array(
		'topLeft'      => esc_attr__( 'Top Left location on the page', $this->plugin['text'] ),
		'topCenter'    => esc_attr__( 'Top Center location on the page', $this->plugin['text'] ),
		'topRight'     => esc_attr__( 'Top Right location on the page', $this->plugin['text'] ),
		'bottomLeft'   => esc_attr__( 'Bottom Left location on the page', $this->plugin['text'] ),
		'bottomCenter' => esc_attr__( 'Bottom Center location on the page', $this->plugin['text'] ),
		'bottomRight'  => esc_attr__( 'Bottom Right location on the page', $this->plugin['text'] ),
		'left'         => esc_attr__( 'Left location on the page', $this->plugin['text'] ),
		'right'        => esc_attr__( 'Right location on the page', $this->plugin['text'] ),
	),
	'func'   => 'buttonlocation();',
);

// Location helper
$location_help = array(
	'text' => esc_attr__( 'Specify modal window location on screen.', $this->plugin['text'] ),
);

// Location Top
$location_top = array(
	'id'     => 'location_top',
	'name'   => 'param[location_top]',
	'type'   => 'number',
	'val'    => isset( $param['location_top'] ) ? $param['location_top'] : '0',
	'option' => array(
		'min'         => '-500',
		'max'         => '500',
		'step'        => '1',
		'placeholder' => '0',
	),
);

// Location Top helper
$location_top_help = array(
	'text' => esc_attr__( 'Distance from the top edge of the screen in px.', $this->plugin['text'] ),
);

// Location Bottom
$location_bottom = array(
	'id'     => 'location_bottom',
	'name'   => 'param[location_bottom]',
	'type'   => 'number',
	'val'    => isset( $param['location_bottom'] ) ? $param['location_bottom'] : '0',
	'option' => array(
		'min'         => '-500',
		'max'         => '500',
		'step'        => '1',
		'placeholder' => '0',
	),
);

// Location Top helper
$location_bottom_help = array(
	'text' => esc_attr__( 'Distance from the bottom  edge of the screen in px.', $this->plugin['text'] ),
);

// Location Left
$location_left = array(
	'id'     => 'location_left',
	'name'   => 'param[location_left]',
	'type'   => 'number',
	'val'    => isset( $param['location_left'] ) ? $param['location_left'] : '0',
	'option' => array(
		'min'         => '-500',
		'max'         => '500',
		'step'        => '1',
		'placeholder' => '0',
	),
);

// Location Top helper
$location_left_help = array(
	'text' => esc_attr__( 'Distance from the left edge of the screen in px.', $this->plugin['text'] ),
);

// Location Right
$location_right = array(
	'id'     => 'location_right',
	'name'   => 'param[location_right]',
	'type'   => 'number',
	'val'    => isset( $param['location_right'] ) ? $param['location_right'] : '0',
	'option' => array(
		'min'         => '-500',
		'max'         => '500',
		'step'        => '1',
		'placeholder' => '0',
	),
);

// Location Top helper
$location_right_help = array(
	'text' => esc_attr__( 'Distance from the right edge of the screen in px.', $this->plugin['text'] ),
);

// Button Text
$text = array(
	'id'     => 'text',
	'name'   => 'param[text]',
	'type'   => 'text',
	'val'    => isset( $param['text'] ) ? $param['text'] : esc_attr__( 'Text', $this->plugin['text'] ),
	'option' => array(
		'placeholder' => 'Text',
	),
);

// Button Text helper
$text_help = array(
	'text' => esc_attr__( 'Enter Text for button.', $this->plugin['text'] ),
);

// Rotating Text
$rotate_button = array(
	'id'     => 'rotate_button',
	'name'   => 'param[rotate_button]',
	'type'   => 'select',
	'val'    => isset( $param['rotate_button'] ) ? $param['rotate_button'] : '0deg',
	'option' => array(
		'0deg'   => esc_attr__( 'none', $this->plugin['text'] ),
		'90deg'  => esc_attr__( '90&deg;', $this->plugin['text'] ),
		'180deg' => esc_attr__( '180&deg;', $this->plugin['text'] ),
		'270deg' => esc_attr__( '270&deg;', $this->plugin['text'] ),
	),
);


// Button Icon
$icon = array(
	'id'     => 'icon',
	'name'   => 'param[icon]',
	'type'   => 'select',
	'val'    => isset( $param['icon'] ) ? $param['icon'] : '',
	'option' => $icons_new,
);

// Button Icon helper
$icon_help = array(
	'text' => esc_attr__( 'Select the Icon for button', $this->plugin['text'] ),
);

// Rotating Icons
$rotate_icon = array(
	'id'     => 'rotate_icon',
	'name'   => 'param[rotate_icon]',
	'type'   => 'select',
	'val'    => isset( $param['rotate_icon'] ) ? $param['rotate_icon'] : '',
	'option' => array(
		''              => esc_attr__( 'none', $this->plugin['text'] ),
		'fa-rotate-90'  => esc_attr__( '90&deg;', $this->plugin['text'] ),
		'fa-rotate-180' => esc_attr__( '180&deg;', $this->plugin['text'] ),
		'fa-rotate-270' => esc_attr__( '270&deg;', $this->plugin['text'] ),
	),
);

// Button Text location
$text_location = array(
	'id'     => 'text_location',
	'name'   => 'param[text_location]',
	'type'   => 'select',
	'val'    => isset( $param['text_location'] ) ? $param['text_location'] : 'before',
	'option' => array(
		'before' => esc_attr__( 'Before Icon', $this->plugin['text'] ),
		'after'  => esc_attr__( 'After Icon', $this->plugin['text'] ),
	),
);

$text_location_help = array(
	'text' => esc_attr__( 'Set where the button text will be displayed.', $this->plugin['text'] ),
);

// Button Class
$button_class = array(
	'id'   => 'button_class',
	'name' => 'param[button_class]',
	'type' => 'text',
	'val'  => isset( $param['button_class'] ) ? $param['button_class'] : '',
);


$button_class_help = array(
	'text' => esc_attr__( 'Add extra class to the button.', $this->plugin['text'] ),
);

// Button ID
$button_id = array(
	'id'   => 'button_id',
	'name' => 'param[button_id]',
	'type' => 'text',
	'val'  => isset( $param['button_id'] ) ? $param['button_id'] : '',
);


$button_id_help = array(
	'text' => esc_attr__( 'Add ID to the button.', $this->plugin['text'] ),
);

/*// Button URL
$button_url = array(
	'id'   => 'button_url',
	'name' => 'param[button_url]',
	'type' => 'text',
	'val'  => isset( $param['button_url'] ) ? $param['button_url'] : '',
);


$button_url_help = array(
	'text' => esc_attr__( 'Enter URL for the button or leave empty.', $this->plugin['text'] ),
);*/

$item_type = array(
	'name'   => 'param[item_type]',
	'type'   => 'select',
	'class'  => 'item-type',
	'val'    => isset( $param['item_type'] ) ? $param['item_type'] : '',
	'option' => array(
		'link'         => esc_attr__( 'Link', $this->plugin['text'] ),
	),
	'func'   => 'itemtype(this);',
);

$item_type_help = array(
	'text' => esc_attr__( 'Select the type of menu item. Explanation of some types:', $this->plugin['text'] ),
	'ul' => array (
		esc_attr__('<strong>Smooth Scroll</strong> - Smooth scrolling of the page to the specified anchors on the page. Enter Link like #anchor', $this->plugin['text']),
	),
);


$button_url = !empty( $param['button_url'] ) ? $param['button_url'] : '' ;

// Link URL
$item_link = array(
	'name' => 'param[item_link]',
	'type' => 'text',
	'val'  => isset( $param['item_link'] ) ? $param['item_link'] : $button_url,
);


// Open link in a new window
$new_tab = array(
	'name'  => 'param[new_tab]',
	'class' => '',
	'type'  => 'checkbox',
	'val'   => isset( $param['new_tab'] ) ? $param['new_tab'] : 0,
	'func'  => '',
	'sep'   => '',
);

// Social Networks
$item_share = array(
	'name'   => 'param[item_share]',
	'type'   => 'select',
	'val'    => isset( $param['item_share'] ) ? $param['item_share'] : '',
	'option' => array(
		'Facebook'      => esc_attr__( 'Facebook', $this->plugin['text'] ),
		'VK'            => esc_attr__( 'VK', $this->plugin['text'] ),
		'Twitter'       => esc_attr__( 'Twitter', $this->plugin['text'] ),
		'Linkedin'      => esc_attr__( 'Linkedin', $this->plugin['text'] ),
		'Odnoklassniki' => esc_attr__( 'Odnoklassniki', $this->plugin['text'] ),
		'Google'        => esc_attr__( 'Google', $this->plugin['text'] ),
		'Pinterest'     => esc_attr__( 'Pinterest', $this->plugin['text'] ),
		'xing'          => esc_attr__( 'XING', $this->plugin['text'] ),
		'myspace'       => esc_attr__( 'Myspace', $this->plugin['text'] ),
		'weibo'         => esc_attr__( 'Weibo', $this->plugin['text'] ),
		'buffer'        => esc_attr__( 'Buffer', $this->plugin['text'] ),
		'stumbleupon'   => esc_attr__( 'StumbleUpon', $this->plugin['text'] ),
		'reddit'        => esc_attr__( 'Reddit', $this->plugin['text'] ),
		'tumblr'        => esc_attr__( 'Tumblr', $this->plugin['text'] ),
		'blogger'       => esc_attr__( 'Blogger', $this->plugin['text'] ),
		'livejournal'   => esc_attr__( 'LiveJournal', $this->plugin['text'] ),
		'pocket'        => esc_attr__( 'Pocket', $this->plugin['text'] ),
		'telegram'      => esc_attr__( 'Telegram', $this->plugin['text'] ),
		'skype'         => esc_attr__( 'Skype', $this->plugin['text'] ),
		'email'         => esc_attr__( 'Email', $this->plugin['text'] ),
	),
	'func'   => '',
);