<?php
/**
 * Main Settings param
 *
 * @package     Wow_Plugin
 * @copyright   Copyright (c) 2018, Dmytro Lobov
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// Main Settings

// Position of the menu
$menu = array(
	'id'     => 'position',
	'name'   => 'param[menu]',
	'type'   => 'select',
	'val'    => isset( $param['menu'] ) ? $param['menu'] : 'left',
	'option' => array(
		'left'  => esc_attr__( 'Left', $this->plugin['text'] ),
		'right' => esc_attr__( 'Right', $this->plugin['text'] ),
	),
);

// Menu position help
$menu_help = array(
	'text' => esc_attr__( 'Specify menu position on screen.', $this->plugin['text'] ),
);

// Top Offset from start position
$top_offset = array(
	'name'   => 'param[top_offset]',
	'type'   => 'number',
	'val'    => isset( $param['top_offset'] ) ? $param['top_offset'] : '0',
	'option' => array(
		'placeholder' => '0',
	),
);

$top_offset_help = array(
	'text' => esc_attr__( 'Top Offset from base position on screen in (px).', $this->plugin['text'] ),
);

// Offset from start position
$side_offset = array(
	'name'   => 'param[side_offset]',
	'type'   => 'number',
	'val'    => isset( $param['side_offset'] ) ? $param['side_offset'] : '0',
	'option' => array(
		'placeholder' => '0',
	),
);

$side_offset_help = array(
	'text' => esc_attr__( 'Side Offset from base position on screen in (px).', $this->plugin['text'] ),
);

// Menu Vertical alignment
$align = array(
	'name'   => 'param[align]',
	'id'     => 'align',
	'class'  => '',
	'type'   => 'select',
	'val'    => isset( $param['align'] ) ? $param['align'] : 'center',
	'option' => array(
		'top'    => esc_attr__( 'Top', $this->plugin['text'] ),
		'center' => esc_attr__( 'Center', $this->plugin['text'] ),
		'bottom' => esc_attr__( 'Bottom', $this->plugin['text'] ),
	),
);

// Menu Vertical alignment help
$align_help = array(
	'text' => esc_attr__( 'Specify the vertical positioning of the menu.', $this->plugin['text'] ),
);


// Shape for menu item
$shape = array(
	'name'   => 'param[shape]',
	'class'  => '',
	'type'   => 'select',
	'val'    => isset( $param['shape'] ) ? $param['shape'] : 'square',
	'option' => array(
		'square'      => esc_attr__( 'Square', $this->plugin['text'] ),
		'round'       => esc_attr__( 'Round', $this->plugin['text'] ),
		'rounded'     => esc_attr__( 'Rounded', $this->plugin['text'] ),
		'rounded-out' => esc_attr__( 'Rounded-out', $this->plugin['text'] ),
	),
);

// Shape help
$shape_help = array(
	'text' => esc_attr__( 'The shape of the buttons. It also determines the shape of the labels.', $this->plugin['text'] ),
);

// Side Space
$sideSpace = array(
	'name'   => 'param[sideSpace]',
	'class'  => '',
	'type'   => 'select',
	'val'    => isset( $param['sideSpace'] ) ? $param['sideSpace'] : 'true',
	'option' => array(
		'true'  => esc_attr__( 'Yes', $this->plugin['text'] ),
		'false' => esc_attr__( 'No', $this->plugin['text'] ),
	),
);

// Side Space help
$sideSpace_help = array(
	'text' => esc_attr__( 'If there should be space on the side of the bar.', $this->plugin['text'] ),
);

// Button Space
$buttonSpace = array(
	'name'   => 'param[buttonSpace]',
	'class'  => '',
	'type'   => 'select',
	'val'    => isset( $param['buttonSpace'] ) ? $param['buttonSpace'] : 'true',
	'option' => array(
		'true'  => esc_attr__( 'Yes', $this->plugin['text'] ),
		'false' => esc_attr__( 'No', $this->plugin['text'] ),
	),
);

// Button Space help
$buttonSpace_help = array(
	'text' => esc_attr__( 'If there should be space between the buttons.', $this->plugin['text'] ),
);

// Label On
$labelsOn = array(
	'name'   => 'param[labelsOn]',
	'class'  => '',
	'type'   => 'select',
	'val'    => isset( $param['labelsOn'] ) ? $param['labelsOn'] : 'true',
	'option' => array(
		'true'  => esc_attr__( 'Yes', $this->plugin['text'] ),
		'false' => esc_attr__( 'No', $this->plugin['text'] ),
	),
);

// Label On help
$labelsOn_help = array(
	'text' => esc_attr__( 'If the labels should be enabled.', $this->plugin['text'] ),
);

// Label Space
$labelSpace = array(
	'name'   => 'param[labelSpace]',
	'class'  => '',
	'type'   => 'select',
	'val'    => isset( $param['labelSpace'] ) ? $param['labelSpace'] : 'true',
	'option' => array(
		'true'  => esc_attr__( 'Yes', $this->plugin['text'] ),
		'false' => esc_attr__( 'No', $this->plugin['text'] ),
	),
);

// Label Space help
$labelSpace_help = array(
	'text' => esc_attr__( 'If there should be space between the label and the button.', $this->plugin['text'] ),
);

// Label Connected
$labelConnected = array(
	'name'   => 'param[labelConnected]',
	'class'  => '',
	'type'   => 'select',
	'val'    => isset( $param['labelConnected'] ) ? $param['labelConnected'] : 'true',
	'option' => array(
		'true'  => esc_attr__( 'Yes', $this->plugin['text'] ),
		'false' => esc_attr__( 'No', $this->plugin['text'] ),
	),
);

// Label Connected help
$labelConnected_help = array(
	'text' => esc_attr__( 'If the button and label should be visually connected or not. If they are connected, when the label appears, it looks like it expands from the button.',
		$this->plugin['text'] ),
);

// Label Animate
$labelEffect = array(
	'name'   => 'param[labelEffect]',
	'class'  => '',
	'type'   => 'select',
	'val'    => isset( $param['labelEffect'] ) ? $param['labelEffect'] : 'none',
	'option' => array(
		'none'           => esc_attr__( 'None', $this->plugin['text'] ),
		'fade'           => esc_attr__( 'Fade', $this->plugin['text'] ),
		'slide'          => esc_attr__( 'Slide', $this->plugin['text'] ),
		'slide-out'      => esc_attr__( 'Slide-out', $this->plugin['text'] ),
		'slide-out-fade' => esc_attr__( 'Slide-out-fade', $this->plugin['text'] ),
		'slide-in'       => esc_attr__( 'Slide-in', $this->plugin['text'] ),
		'slide-out-out'  => esc_attr__( 'Slide-out-out', $this->plugin['text'] ),
		'slide-in-in'    => esc_attr__( 'Slide-in-in', $this->plugin['text'] ),
	),
);

// Label Connected help
$labelEffect_help = array(
	'text' => esc_attr__( 'The appearance effect of the button label', $this->plugin['text'] ),
);

// Label Speed
$labelSpeed = array(
	'name'   => 'param[labelSpeed]',
	'type'   => 'number',
	'val'    => isset( $param['labelSpeed'] ) ? $param['labelSpeed'] : '400',
	'option' => array(
		'min'         => '0',
		'step'        => '1',
		'placeholder' => '400',
	),
);

$labelSpeed_help = array(
	'text' => esc_attr__( 'Set the time is in milliseconds.', $this->plugin['text'] ),
);

// Sub Menu Settings

// Sub Position
$subPosition = array(
	'name'   => 'param[subPosition]',
	'class'  => '',
	'type'   => 'select',
	'val'    => isset( $param['subPosition'] ) ? $param['subPosition'] : 'under',
	'option' => array(
		'under'    => esc_attr__( 'Under', $this->plugin['text'] ),
		'side'     => esc_attr__( 'Side', $this->plugin['text'] ),
		'circular' => esc_attr__( 'Сircular', $this->plugin['text'] ),
	),
);

// Sub Position help
$subPosition_help = array(
	'text' => esc_attr__( 'The position of the subbar.', $this->plugin['text'] ),
);

// Sub Space
$subSpace = array(
	'name'   => 'param[subSpace]',
	'class'  => '',
	'type'   => 'select',
	'val'    => isset( $param['subSpace'] ) ? $param['subSpace'] : 'true',
	'option' => array(
		'true'  => esc_attr__( 'Yes', $this->plugin['text'] ),
		'false' => esc_attr__( 'No', $this->plugin['text'] ),
	),
);

// Sub Space help
$subSpace_help = array(
	'text' => esc_attr__( 'If there should be space between the subbar and the button.', $this->plugin['text'] ),
);

// Sub Open
$subOpen = array(
	'name'   => 'param[subOpen]',
	'class'  => '',
	'type'   => 'select',
	'val'    => isset( $param['subOpen'] ) ? $param['subOpen'] : 'mouseover',
	'option' => array(
		'mouseover' => esc_attr__( 'Mouseover', $this->plugin['text'] ),
		'click'     => esc_attr__( 'Click', $this->plugin['text'] ),
	),
);

// Sub Space help
$subOpen_help = array(
	'text' => esc_attr__( 'If the subbar should be opened on mouseover or on click.', $this->plugin['text'] ),
);


// Sub Effect
$subEffect = array(
	'name'   => 'param[subEffect]',
	'class'  => '',
	'type'   => 'select',
	'val'    => isset( $param['subEffect'] ) ? $param['subEffect'] : 'none',
	'option' => array(
		'none'         => esc_attr__( 'None', $this->plugin['text'] ),
		'fade'         => esc_attr__( 'Fade', $this->plugin['text'] ),
		'slide'        => esc_attr__( 'Slide', $this->plugin['text'] ),
		'linear-fade'  => esc_attr__( 'Linear-fade', $this->plugin['text'] ),
		'linear-slide' => esc_attr__( 'Linear-slide', $this->plugin['text'] ),
	),
);

// Sub Space help
$subEffect_help = array(
	'text' => esc_attr__( 'The appearance effect of the subbar.', $this->plugin['text'] ),
);

// Sub Speed (ms)
$subSpeed = array(
	'name'   => 'param[subSpeed]',
	'type'   => 'number',
	'val'    => isset( $param['subSpeed'] ) ? $param['subSpeed'] : '400',
	'option' => array(
		'min'         => '0',
		'step'        => '1',
		'placeholder' => '400',
	),
);

$subSpeed_help = array(
	'text' => esc_attr__( 'Set the time is in milliseconds.', $this->plugin['text'] ),
);

// Popup Settings

// Horizontal position
$windowhorizontalPosition = array(
	'name'   => 'param[windowhorizontalPosition]',
	'class'  => '',
	'type'   => 'select',
	'val'    => isset( $param['windowhorizontalPosition'] ) ? $param['windowhorizontalPosition'] : 'center',
	'option' => array(
		'center' => esc_attr__( 'Center', $this->plugin['text'] ),
		'left'   => esc_attr__( 'Left', $this->plugin['text'] ),
		'right'  => esc_attr__( 'Right', $this->plugin['text'] ),
	),
);

$windowhorizontalPosition_help = array(
	'text' => esc_attr__( 'Set the horizontal position of the window.', $this->plugin['text'] ),
);

// Vertical position
$windowverticalPosition = array(
	'name'   => 'param[windowverticalPosition]',
	'class'  => '',
	'type'   => 'select',
	'val'    => isset( $param['windowverticalPosition'] ) ? $param['windowverticalPosition'] : 'center',
	'option' => array(
		'center' => esc_attr__( 'Center', $this->plugin['text'] ),
		'top'    => esc_attr__( 'Top', $this->plugin['text'] ),
		'bottom' => esc_attr__( 'Bottom', $this->plugin['text'] ),
	),
);

$windowverticalPosition_help = array(
	'text' => esc_attr__( 'Set the vertical position of the window.', $this->plugin['text'] ),
);

// Corners
$windowCorners = array(
	'name'   => 'param[windowCorners]',
	'class'  => '',
	'type'   => 'select',
	'val'    => isset( $param['windowCorners'] ) ? $param['windowCorners'] : 'match',
	'option' => array(
		'match'  => esc_attr__( 'Match', $this->plugin['text'] ),
		'square' => esc_attr__( 'Square', $this->plugin['text'] ),
		'round'  => esc_attr__( 'Round', $this->plugin['text'] ),
	),
);

$windowCorners_help = array(
	'text' => esc_attr__( 'The type of the window corners.', $this->plugin['text'] ),
);

// Color
$windowColor = array(
	'name'   => 'param[windowColor]',
	'class'  => '',
	'type'   => 'select',
	'val'    => isset( $param['windowColor'] ) ? $param['windowColor'] : 'default',
	'option' => array(
		'default' => esc_attr__( 'Default', $this->plugin['text'] ),
		'black'   => esc_attr__( 'Black', $this->plugin['text'] ),
		'red'     => esc_attr__( 'Red', $this->plugin['text'] ),
		'yellow'  => esc_attr__( 'Yellow', $this->plugin['text'] ),
		'blue'    => esc_attr__( 'Blue', $this->plugin['text'] ),
	),
);

$windowColor_help = array(
	'text' => esc_attr__( 'The color of the header of the window.', $this->plugin['text'] ),
);

// Icon & Label Size

// Icon size (px)
$iconSize = array(
	'name'   => 'param[iconSize]',
	'type'   => 'number',
	'val'    => isset( $param['iconSize'] ) ? $param['iconSize'] : '24',
	'option' => array(
		'min'         => '0',
		'step'        => '1',
		'placeholder' => '24',
	),
);

$iconSize_help = array(
	'text' => esc_attr__( 'Set the size for menu icons.', $this->plugin['text'] ),
);

// Icon size for mobile
$mobiliconSize = array(
	'name'   => 'param[mobiliconSize]',
	'type'   => 'number',
	'val'    => isset( $param['mobiliconSize'] ) ? $param['mobiliconSize'] : '24',
	'option' => array(
		'min'         => '0',
		'step'        => '1',
		'placeholder' => '24',
	),
);

$mobiliconSize_help = array(
	'text' => esc_attr__( 'Set the Icons size on mobile devices.', $this->plugin['text'] ),
);

// Mobile Screen (px)
$mobilieScreen = array(
	'name'   => 'param[mobilieScreen]',
	'type'   => 'number',
	'val'    => isset( $param['mobilieScreen'] ) ? $param['mobilieScreen'] : '480',
	'option' => array(
		'min'         => '0',
		'step'        => '1',
		'placeholder' => '480',
	),
);

$mobilieScreen_help = array(
	'text' => esc_attr__( 'Set the size screen for mobile devices when use Icon size for mobile.', $this->plugin['text'] ),
);

// Label size (px)
$labelSize = array(
	'name'   => 'param[labelSize]',
	'type'   => 'number',
	'val'    => isset( $param['labelSize'] ) ? $param['labelSize'] : '15',
	'option' => array(
		'min'         => '0',
		'step'        => '1',
		'placeholder' => '24',
	),
);

$labelSize_help = array(
	'text' => esc_attr__( 'Set the size for menu labels.', $this->plugin['text'] ),
);

// Label size for mobile (px)
$mobillabelSize = array(
	'name'   => 'param[mobillabelSize]',
	'type'   => 'number',
	'val'    => isset( $param['mobillabelSize'] ) ? $param['mobillabelSize'] : '15',
	'option' => array(
		'min'         => '0',
		'step'        => '1',
		'placeholder' => '24',
	),
);

$mobillabelSize_help = array(
	'text' => esc_attr__( 'Set the Labels size on mobile devices.', $this->plugin['text'] ),
);


// Show After Position
$showAfterPosition = array(
	'name'   => 'param[showAfterPosition]',
	'type'   => 'number',
	'val'    => isset( $param['showAfterPosition'] ) ? $param['showAfterPosition'] : '0',
	'option' => array(
		'min'         => '0',
		'step'        => '1',
		'placeholder' => '0',
	),
);

// Show After Position helper
$showAfterPosition_help = array(
	'text' => esc_attr__( 'If the sidebar should be shown only after the page was scrolled beyond a certain point.',
		$this->plugin['text'] ),
);


// Z-index
$z_index = array(
	'name'   => 'param[zindex]',
	'type'   => 'number',
	'val'    => isset( $param['zindex'] ) ? $param['zindex'] : '9999',
	'option' => array(
		'min'  => '0',
		'step' => '1',
	),
);

// Show After Position
$hideAfterPosition = array(
	'name'   => 'param[hideAfterPosition]',
	'type'   => 'number',
	'val'    => isset( $param['hideAfterPosition'] ) ? $param['hideAfterPosition'] : '0',
	'option' => array(
		'min'         => '0',
		'step'        => '1',
		'placeholder' => '0',
	),
);

// Show After Position helper
$hideAfterPosition_help = array(
	'text' => esc_attr__( 'If the sidebar should be hide only after the page was scrolled beyond a certain point.',
		$this->plugin['text'] ),
);