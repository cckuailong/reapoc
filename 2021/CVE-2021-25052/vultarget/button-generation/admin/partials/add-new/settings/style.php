<?php
/**
 * Button style options parameters
 *
 * @package     Wow_Plugin
 * @subpackage  Add/Settings/Style
 * @author      Wow-Company <support@wow-company.com>
 * @copyright   2019 Wow-Company
 * @license     GNU Public License
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Width
$width = array(
	'id'     => 'width',
	'name'   => 'param[width]',
	'type'   => 'text',
	'val'    => isset( $param['width'] ) ? $param['width'] : '100px',
	'option' => array(
		'placeholder' => '100px',
	),
);

// Width helper
$width_help = array(
	'title' => esc_attr__( 'Specify button width. Can be:', $this->plugin['text'] ),
	'ul'    => array(
		esc_attr__( '<strong>any integer value in px</strong> (for example: "400px" will set button width to 400px);',
			$this->plugin['text'] ),
		esc_attr__( '<strong>any integer value in %</strong> (for example: "80%" will set button width to 80% of the window width);',
			$this->plugin['text'] ),
		esc_attr__( '<strong>auto</strong> - the browser calculates the button width.', $this->plugin['text'] ),
	),
);

// Height
$height = array(
	'id'     => 'height',
	'name'   => 'param[height]',
	'type'   => 'text',
	'val'    => isset( $param['height'] ) ? $param['height'] : '50px',
	'option' => array(
		'placeholder' => '50px',
	),
);

// Height helper
$height_help = array(
	'title' => esc_attr__( 'Specify button height. Can be:', $this->plugin['text'] ),
	'ul'    => array(
		esc_attr__( '<strong>any integer value in px</strong> (for example: "400px" will set button height to 400px);',
			$this->plugin['text'] ),
		esc_attr__( '<strong>any integer value in %</strong> (for example: "80%" will set button height to 80% of the window height);',
			$this->plugin['text'] ),
		esc_attr__( '<strong>auto</strong> - the browser calculates the button height.', $this->plugin['text'] ),
	),
);

// Z-index
$zindex = array(
	'id'     => 'zindex',
	'name'   => 'param[zindex]',
	'type'   => 'number',
	'val'    => isset( $param['zindex'] ) ? $param['zindex'] : '999',
	'option' => array(
		'min'         => '1',
		'max'         => '9999999',
		'step'        => '1',
		'placeholder' => '999',
	),
);

// Z-index helper
$zindex_help = array(
	'text' => esc_attr__( 'The z-index property specifies the stack order of an element. An element with greater stack order is always in front of an element with a lower stack order.',
		$this->plugin['text'] ),
);

// Text color
$color = array(
	'id'   => 'color',
	'name' => 'param[color]',
	'type' => 'color',
	'val'  => isset( $param['color'] ) ? $param['color'] : '#ffffff',
);

$color_help = array(
	'text' => esc_attr__( 'Specify button text color.', $this->plugin['text'] ),
);

// Background
$background = array(
	'id'   => 'background',
	'name' => 'param[background]',
	'type' => 'color',
	'val'  => isset( $param['background'] ) ? $param['background'] : '#1f9ef8',
);

// Background helper
$background_help = array(
	'text' => esc_attr__( 'Specify button background color.', $this->plugin['text'] ),
);


// Hover Text color
$hover_color = array(
	'id'   => 'hover_color',
	'name' => 'param[hover_color]',
	'type' => 'color',
	'val'  => isset( $param['hover_color'] ) ? $param['hover_color'] : '#ffffff',
);

$hover_color_help = array(
	'text' => esc_attr__( 'Specify button text color when hover on the button.', $this->plugin['text'] ),
);

// Hover Background
$hover_background = array(
	'id'   => 'hover_background',
	'name' => 'param[hover_background]',
	'type' => 'color',
	'val'  => isset( $param['hover_background'] ) ? $param['hover_background'] : '#0090f7',
);

// Hover Background helper
$hover_background_help = array(
	'text' => esc_attr__( 'Specify button background color when hover on the button.', $this->plugin['text'] ),
);

// Hover Effects
$hover_effects = array(
	'name'   => 'param[hover_effects]',
	'id'     => 'hover_effects',
	'class'  => '',
	'type'   => 'select',
	'val'    => isset( $param['hover_effects'] ) ? $param['hover_effects'] : '',
	'option' => array(
		'' => esc_attr__( 'None', $this->plugin['text'] ),

	),
	'func'   => '',
);

// Hover Effect helper
$hover_effects_help = array(
	'text' => esc_attr__( 'Choose hover effect for button.', $this->plugin['text'] ),
);

// Border Radius
$border_radius = array(
	'id'     => 'border_radius',
	'name'   => 'param[border_radius]',
	'type'   => 'text',
	'val'    => isset( $param['border_radius'] ) ? $param['border_radius'] : '1px',
	'option' => array(
		'placeholder' => '0',
	),
);

// Border Radius helper
$border_radius_help = array(
	'title' => esc_attr__( 'Set the radius of the corners of the element.', $this->plugin['text'] ),
	'ul'    => array(
		esc_attr__( 'any integer value in px (for example: "5px" will set button corners radius to 5px)',
			$this->plugin['text'] ),
		esc_attr__( 'if you enter 0, the button corners will have not radius', $this->plugin['text'] ),
		esc_attr__( 'when four values are specified, the radius apply to the top, right, bottom, and left in that order (clockwise). Example: 5px 0 5px 5px',
			$this->plugin['text'] ),
	),
);

// Border Style
$border_style = array(
	'id'     => 'border_style',
	'name'   => 'param[border_style]',
	'type'   => 'select',
	'val'    => isset( $param['border_style'] ) ? $param['border_style'] : 'none',
	'option' => array(
		'none'   => esc_attr__( 'None', $this->plugin['text'] ),
		'solid'  => esc_attr__( 'Solid', $this->plugin['text'] ),
		'dotted' => esc_attr__( 'Dotted', $this->plugin['text'] ),
		'dashed' => esc_attr__( 'Dashed', $this->plugin['text'] ),
		'double' => esc_attr__( 'Double', $this->plugin['text'] ),
		'groove' => esc_attr__( 'Groove', $this->plugin['text'] ),
		'inset'  => esc_attr__( 'Inset', $this->plugin['text'] ),
		'outset' => esc_attr__( 'Outset', $this->plugin['text'] ),
		'ridge'  => esc_attr__( 'Ridge', $this->plugin['text'] ),
	),
	'func'   => 'border();',
);

// Border Style helper
$border_style_help = array(
	'text' => esc_attr__( 'Choose a border style for your button.', $this->plugin['text'] ),
);

// Border Color
$border_color = array(
	'id'     => 'border_color',
	'name'   => 'param[border_color]',
	'type'   => 'color',
	'val'    => isset( $param['border_color'] ) ? $param['border_color'] : '#383838',
	'option' => array(
		'placeholder' => '#383838',
	),
);

// Border Width
$border_width = array(
	'id'     => 'border_width',
	'name'   => 'param[border_width]',
	'type'   => 'number',
	'val'    => isset( $param['border_width'] ) ? $param['border_width'] : '1',
	'option' => array(
		'min'         => '0',
		'max'         => '100',
		'step'        => '1',
		'placeholder' => '0',
	),
);

// Shadow
$shadow = array(
	'id'     => 'shadow',
	'name'   => 'param[shadow]',
	'type'   => 'select',
	'val'    => isset( $param['shadow'] ) ? $param['shadow'] : 'none',
	'option' => array(
		'none'   => esc_attr__( 'None', $this->plugin['text'] ),
		'outset' => esc_attr__( 'Outset', $this->plugin['text'] ),
		'inset'  => esc_attr__( 'Inset', $this->plugin['text'] ),
	),
	'func'   => 'shadowblock();',
);

$shadow_help = array(
	'title' => esc_attr__( 'Set the box shadow.', $this->plugin['text'] ),
	'ul'    => array(
		esc_attr__( 'None - no shadow is displayed', $this->plugin['text'] ),
		esc_attr__( 'Outset - outer shadow', $this->plugin['text'] ),
		esc_attr__( 'Inset - inner shadow', $this->plugin['text'] ),
	),
);

// Shadow Horizontal Position
$shadow_h_offset = array(
	'id'     => 'shadow_h_offset',
	'name'   => 'param[shadow_h_offset]',
	'type'   => 'number',
	'val'    => isset( $param['shadow_h_offset'] ) ? $param['shadow_h_offset'] : '0',
	'option' => array(
		'min'         => '-50',
		'max'         => '50',
		'step'        => '1',
		'placeholder' => '0',
	),
);

$shadow_h_offset_help = array(
	'text' => esc_attr__( 'The horizontal offset of the shadow. A positive value puts the shadow on the right side of the box, a negative value puts the shadow on the left side of the box.',
		$this->plugin['text'] ),
);

// Shadow Vertical Position
$shadow_v_offset = array(
	'id'     => 'shadow_v_offset',
	'name'   => 'param[shadow_v_offset]',
	'type'   => 'number',
	'val'    => isset( $param['shadow_v_offset'] ) ? $param['shadow_v_offset'] : '0',
	'option' => array(
		'min'         => '-50',
		'max'         => '50',
		'step'        => '1',
		'placeholder' => '0',
	),
);

$shadow_v_offset_help = array(
	'text' => esc_attr__( 'The vertical offset of the shadow. A positive value puts the shadow below the box, a negative value puts the shadow above the box.',
		$this->plugin['text'] ),
);

// Shadow Blur Radius
$shadow_blur = array(
	'id'     => 'shadow_blur',
	'name'   => 'param[shadow_blur]',
	'type'   => 'number',
	'val'    => isset( $param['shadow_blur'] ) ? $param['shadow_blur'] : '3',
	'option' => array(
		'min'         => '0',
		'max'         => '100',
		'step'        => '1',
		'placeholder' => '0',
	),
);

$shadow_blur_help = array(
	'text' => esc_attr__( 'The blur radius. The higher the number, the more blurred the shadow will be.',
		$this->plugin['text'] ),
);

// Shadow Spread
$shadow_spread = array(
	'id'     => 'shadow_spread',
	'name'   => 'param[shadow_spread]',
	'type'   => 'number',
	'val'    => isset( $param['shadow_spread'] ) ? $param['shadow_spread'] : '0',
	'option' => array(
		'min'         => '-100',
		'max'         => '100',
		'step'        => '1',
		'placeholder' => '0',
	),
);

$shadow_spread_help = array(
	'text' => esc_attr__( 'The spread radius. A positive value increases the size of the shadow, a negative value decreases the size of the shadow.',
		$this->plugin['text'] ),
);

// Shadow Color
$shadow_color = array(
	'id'     => 'shadow_color',
	'name'   => 'param[shadow_color]',
	'type'   => 'color',
	'val'    => isset( $param['shadow_color'] ) ? $param['shadow_color'] : '#020202',
	'option' => array(
		'placeholder' => '#020202',
	),
);

$shadow_color_help = array(
	'text' => esc_attr__( 'The color of the shadow.', $this->plugin['text'] ),
);

// Font size
$font_size = array(
	'id'     => 'font_size',
	'name'   => 'param[font_size]',
	'type'   => 'number',
	'val'    => isset( $param['font_size'] ) ? $param['font_size'] : '16',
	'option' => array(
		'min'         => '8',
		'max'         => '54',
		'step'        => '1',
		'placeholder' => '16',
	),
);

// Font size helper
$font_size_help = array(
	'text' => esc_attr__( 'Set the font size for button in px', $this->plugin['text'] ),
);

// Font Family
$font_family = array(
	'id'     => 'font_family',
	'name'   => 'param[font_family]',
	'type'   => 'select',
	'val'    => isset( $param['font_family'] ) ? $param['font_family'] : 'inherit',
	'option' => array(
		'inherit'         => esc_attr__( 'Use Your Themes', $this->plugin['text'] ),
		'Tahoma'          => 'Tahoma',
		'Georgia'         => 'Georgia',
		'Comic Sans MS'   => 'Comic Sans MS',
		'Arial'           => 'Arial',
		'Lucida Grande'   => 'Lucida Grande',
		'Times New Roman' => 'Times New Roman',
	),
);

// Font Weight
$font_weight = array(
	'id'     => 'font_weight',
	'name'   => 'param[font_weight]',
	'type'   => 'select',
	'val'    => isset( $param['font_weight'] ) ? $param['font_weight'] : 'normal',
	'option' => array(
		'normal' => 'Normal',
		'100'    => '100',
		'200'    => '200',
		'300'    => '300',
		'400'    => '400',
		'500'    => '500',
		'600'    => '600',
		'700'    => '700',
		'800'    => '800',
		'900'    => '900',
	),
);

// Font Style
$font_style = array(
	'id'     => 'font_style',
	'name'   => 'param[font_style]',
	'type'   => 'select',
	'val'    => isset( $param['font_style'] ) ? $param['font_style'] : 'normal',
	'option' => array(
		'normal' => 'Normal',
		'italic' => 'Italic',
	),
);
