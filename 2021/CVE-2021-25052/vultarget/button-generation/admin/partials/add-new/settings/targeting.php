<?php
/**
 * Triggers options parameters
 *
 * @package     Wow_Plugin
 * @subpackage  Settings/Targeting
 * @author      Wow-Company <support@wow-company.com>
 * @copyright   2019 Wow-Company
 * @license     GNU Public License
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Enable Don’t show on screens more than
$include_more_screen = array(
	'id'   => 'include_more_screen',
	'name' => 'param[include_more_screen]',
	'type' => 'checkbox',
	'val'  => isset( $param['include_more_screen'] ) ? $param['include_more_screen'] : 0,
	'func' => 'screen_more(this);',
);

// Show on screens helper
$show_screen_help = array(
	'text' => esc_attr__( 'Specify the window breakpoint in px when the button will be shown.', $this->plugin['text'] ),
);

// Max screen value
$screen_more = array(
	'id'     => 'screenmore',
	'name'   => 'param[screen_more]',
	'type'   => 'number',
	'val'    => isset( $param['screen_more'] ) ? $param['screen_more'] : 1024,
	'option' => array(
		'min'         => '0',
		'step'        => '0.1',
		'placeholder' => '1024',
	),
);

// Enable Don’t show on screens less than
$include_mobile = array(
	'id'   => 'include_mobile',
	'name' => 'param[include_mobile]',
	'type' => 'checkbox',
	'val'  => isset( $param['include_mobile'] ) ? $param['include_mobile'] : 0,
	'func' => 'screen_less(this);',
);

// Enable Don’t show on screens less than helper
$include_mobile_help = array(
	'text' => esc_attr__( 'Specify the window breakpoint ( mix width) in px.', $this->plugin['text'] ),
);

// Min screen value
$screen = array(
	'id'     => 'screen',
	'name'   => 'param[screen]',
	'type'   => 'number',
	'val'    => isset( $param['screen'] ) ? $param['screen'] : 480,
	'option' => array(
		'min'         => '0',
		'step'        => '0.1',
		'placeholder' => '480',
	),
);

// Disable FontAwesome on front-end of the site
$disable_fontawesome = array(
	'id'   => 'disable_fontawesome',
	'name' => 'param[disable_fontawesome]',
	'type' => 'checkbox',
	'val'  => isset( $param['disable_fontawesome'] ) ? $param['disable_fontawesome'] : 0,
);

$disable_fontawesome_help = array(
	'title' => esc_attr__( 'Disable Font Awesome 5 style on front-end of the site.', $this->plugin['text'] ),
	'ul'    => array(
		esc_attr__( 'If you already have a Font Awesome 5 installed on the site, you can disable the include the Font Awesome 5 style.',
			$this->plugin['text'] ),
	),
);
