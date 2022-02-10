<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Modal button settings
 *
 * @package     Wow_Plugin
 * @subpackage  Settings
 * @copyright   Copyright (c) 2018, Dmytro Lobov
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

//region General
$umodal_button = array(
	'label'   => esc_attr__( 'Show button', 'modal-window' ),
	'attr'    => [
		'name'  => 'param[umodal_button]',
		'id'    => 'umodal_button',
		'value' => isset( $param['umodal_button'] ) ? $param['umodal_button'] : 'no',
	],
	'options' => [
		'no'  => __( 'no', 'modal-window' ),
		'yes' => __( 'yes', 'modal-window' ),
	],
	'func'    => 'displayButton()',
);

$button_type = array(
	'label'   => esc_attr__( 'Appearance', 'modal-window' ),
	'attr'    => [
		'name'  => 'param[button_type]',
		'id'    => 'button_type',
		'value' => isset( $param['button_type'] ) ? $param['button_type'] : '1',
	],
	'options' => [
		'1' => __( 'Only Text', 'modal-window' ),
	],
	'tooltip' => esc_attr__( 'Set the button appearance.', 'modal-window' ),
	'func'    => 'buttonType()',
);

$umodal_button_text = array(
	'label'   => esc_attr__( 'Text', 'modal-window' ),
	'attr'    => [
		'name'        => 'param[umodal_button_text]',
		'id'          => 'umodal_button_text',
		'value'       => isset( $param['umodal_button_text'] ) ? $param['umodal_button_text'] : 'Feedback',
		'placeholder' => esc_attr__( 'Enter Text', 'modal-window' ),
	],
	'tooltip' => esc_attr__( 'Enter Text for button.', 'modal-window' ),
	'icon'    => '',
);
//endregion

//region Button Icon
$button_icon = array(
	'label'   => esc_attr__( 'Icon', 'modal-window' ),
	'attr'    => [
		'name'  => 'param[button_icon]',
		'id'    => 'button_icon',
		'class' => 'icons',
		'value' => isset( $param['button_icon'] ) ? $param['button_icon'] : '',
	],
	'options' => $icons_new,
	'tooltip' => esc_attr__( 'Select the Icon for button', 'modal-window' ),
	'icon'    => '',
	'func'    => '',
);

$rotate_icon = array(
	'label'   => esc_attr__( 'Rotate icon', 'modal-window' ),
	'attr'    => [
		'name'  => 'param[rotate_icon]',
		'id'    => 'rotate_icon',
		'value' => isset( $param['rotate_icon'] ) ? $param['rotate_icon'] : '',
	],
	'options' => [
		''              => esc_attr__( 'none', 'modal-window' ),
		'fa-rotate-90'  => esc_attr__( '90&deg;', 'modal-window' ),
		'fa-rotate-180' => esc_attr__( '180&deg;', 'modal-window' ),
		'fa-rotate-270' => esc_attr__( '270&deg;', 'modal-window' ),
	],
);

$button_icon_after = array(
	'label'   => esc_attr__( 'Text location', 'modal-window' ),
	'attr'    => [
		'name'  => 'param[button_icon_after]',
		'id'    => 'button_icon_after',
		'value' => isset( $param['button_icon_after'] ) ? $param['button_icon_after'] : '0',
	],
	'options' => [
		'0' => esc_attr__( 'Before Icon', 'modal-window' ),
		'1' => esc_attr__( 'After Icon', 'modal-window' ),
	],
	'tooltip' => esc_attr__( 'Set where the button text will be displayed.', 'modal-window' ),
);

$button_shape = array(
	'label'   => esc_attr__( 'Shape', 'modal-window' ),
	'attr'    => [
		'name'  => 'param[button_shape]',
		'id'    => 'button_shape',
		'class' => 'icons',
		'value' => isset( $param['button_shape'] ) ? $param['button_shape'] : '',
	],
	'options' => [
		''                   => 'none',
		'fas fa-circle'      => 'fas fa-circle',
		'far fa-circle'      => 'far fa-circle',
		'fas fa-square'      => 'fas fa-square',
		'far fa-square'      => 'far fa-square',
		'fas fa-bookmark'    => 'fas fa-bookmark',
		'far fa-bookmark'    => 'far fa-bookmark',
		'fas fa-calendar'    => 'fas fa-calendar',
		'far fa-calendar'    => 'far fa-calendar',
		'fas fa-certificate' => 'fas fa-certificate',
		'fas fa-ban'         => 'fas fa-ban',
	],
);
//endregion

//region Location
$umodal_button_position = array(
	'label'   => esc_attr__( 'Location ', 'modal-window' ),
	'attr'    => [
		'name'  => 'param[umodal_button_position]',
		'id'    => 'umodal_button_position',
		'value' => isset( $param['umodal_button_position'] ) ? $param['umodal_button_position'] : 'wow_modal_button_right',
	],
	'options' => [
		'wow_modal_button_right'  => __( 'Right', 'modal-window' ),
		'wow_modal_button_left'   => __( 'Left', 'modal-window' ),
		'wow_modal_button_top'    => __( 'Top', 'modal-window' ),
		'wow_modal_button_bottom' => __( 'Bottom', 'modal-window' ),
	],
	'tooltip'    => esc_attr__( 'Set where the button text will be displayed.', 'modal-window' ),
	'icon'    => '',
	'func'    => 'buttonPosition()',
);

$button_position = array(
	'label'    => esc_attr__( 'Top position', 'modal-window' ),
	'attr'     => [
		'name'  => 'param[button_position]',
		'id'    => 'button_position',
		'value' => isset( $param['button_position'] ) ? $param['button_position'] : '50',
		'min'   => '0',
	],
	'addon'    => [
		 'unit' => '%',
	],
);

$button_margin = array(
	'label'    => esc_attr__( 'Margin-right', 'modal-window' ),
	'attr'     => [
		'name'  => 'param[button_margin]',
		'id'    => 'button_margin',
		'value' => isset( $param['button_margin'] ) ? $param['button_margin'] : '-4',
		'step'  => '0.1',
	],
	'addon'    => [
		'unit' => 'px',
	],
);
//endregion

//region Animation
$button_animate = array(
	'label'   => esc_attr__( 'Type', 'modal-window' ),
	'attr'    => [
		'name'  => 'param[button_animate]',
		'id'    => 'button_animate',
		'value' => isset( $param['button_animate'] ) ? $param['button_animate'] : 'no',
	],
	'options' => [
		'no'         => esc_attr__( 'no', 'modal-window' ),
		'bounce'     => esc_attr__( 'bounce', 'modal-window' ),
		'flash'      => esc_attr__( 'flash', 'modal-window' ),
		'pulse'      => esc_attr__( 'pulse', 'modal-window' ),
		'rubberBand' => esc_attr__( 'rubberBand', 'modal-window' ),
		'shake'      => esc_attr__( 'shake', 'modal-window' ),
		'swing'      => esc_attr__( 'swing', 'modal-window' ),
		'tada'       => esc_attr__( 'tada', 'modal-window' ),
		'wobble'     => esc_attr__( 'wobble', 'modal-window' ),
		'jello'      => esc_attr__( 'jello', 'modal-window' ),
	],
);

$button_animate_duration = array(
	'label'    => esc_attr__( 'Duration ', 'modal-window' ),
	'attr'     => [
		'name'  => 'param[button_animate_duration]',
		'id'    => 'button_animate_duration',
		'value' => isset( $param['button_animate_duration'] ) ? $param['button_animate_duration'] : '5',
		'min'   => '0',
		'step'  => '0.01',
	],
	'addon'    => [
		'unit' => 'sec',
	],
);

$button_animate_time = array(
	'label'    => esc_attr__( 'Time', 'modal-window' ),
	'attr'     => [
		'name'  => 'param[button_animate_time]',
		'id'    => 'button_animate_time',
		'value' => isset( $param['button_animate_time'] ) ? $param['button_animate_time'] : '5',
		'min'   => '0',
		'step'  => '0.01',
	],
	'addon'    => [
		'unit' => 'sec',
	],
);

$button_animate_pause = array(
	'label'    => esc_attr__( 'Pause', 'modal-window' ),
	'attr'     => [
		'name'  => 'param[button_animate_pause]',
		'id'    => 'button_animate_pause',
		'value' => isset( $param['button_animate_pause'] ) ? $param['button_animate_pause'] : '5',
		'min'   => '0',
		'step'  => '0.01',
	],
	'addon'    => [
		'unit' => 'sec',
	],
);
//endregion

//region Style
$button_text_size = array(
	'label'    => esc_attr__( 'Text size', 'modal-window' ),
	'attr'     => [
		'name'  => 'param[button_text_size]',
		'id'    => 'button_text_size',
		'value' => isset( $param['button_text_size'] ) ? $param['button_text_size'] : '1.2',
		'min'   => '0',
		'step'  => '0.1',
	],
	'addon'    => [
		'name'    => 'param[button_text_size_unit]',
		'value'   => isset( $param['button_text_size_unit'] ) ? $param['button_text_size_unit'] : 'em',
		'options' => [
			'em' => esc_attr__( 'em', 'modal-window' ),
			'px' => esc_attr__( 'px', 'modal-window' ),
		],
	],
	'tooltip'     => esc_attr__( 'Set the font size for close button content.', 'modal-window' ),
);

$button_padding_top = isset( $param['button_padding_top'] ) ? $param['button_padding_top'] . 'px' : '14px';
$button_padding_left = isset( $param['button_padding_left'] ) ? $param['button_padding_left'] . 'px' : '14px';
$button_padding_old = $button_padding_top . ' ' . $button_padding_left;

$button_padding = array(
	'label' => esc_attr__( 'Padding', 'modal-window' ),
	'attr'  => [
		'name'        => 'param[button_padding]',
		'id'          => 'button_padding',
		'value'       => isset( $param['button_padding'] ) ? $param['button_padding'] : $button_padding_old,
		'placeholder' => '',
	],
	'tooltip'  => esc_attr__( 'Specify button text inner padding.', 'modal-window' ),
);

$button_radius = array(
	'label'    => '',
	'attr'     => [
		'name'  => 'param[button_radius]',
		'id'    => 'button_radius',
		'value' => isset( $param['button_radius'] ) ? $param['button_radius'] : '4',
		'min'   => '0',
	],
	'addon'    => [
		 'unit' => 'px',
	],
	'tooltip'     => esc_attr__( 'Specify modal window border radius.', 'modal-window' ),
);

$button_text_color = array(
	'label' => esc_attr__( 'Text color', 'modal-window' ),
	'attr'  => [
		'name'        => 'param[button_text_color]',
		'id'          => 'button_text_color',
		'value'       => isset( $param['button_text_color'] ) ? $param['button_text_color'] : '#ffffff',
	],
);

$button_text_hcolor = array(
	'label' => esc_attr__( 'Text hover color', 'modal-window' ),
	'attr'  => [
		'name'        => 'param[button_text_hcolor]',
		'id'          => 'button_text_hcolor',
		'value'       => isset( $param['button_text_hcolor'] ) ? $param['button_text_hcolor'] : '#ffffff',
	],
);

$umodal_button_color = array(
	'label' => esc_attr__( 'Background', 'modal-window' ),
	'attr'  => [
		'name'        => 'param[umodal_button_color]',
		'id'          => 'umodal_button_color',
		'value'       => isset( $param['umodal_button_color'] ) ? $param['umodal_button_color'] : '#383838',
	],
);

$umodal_button_hover = array(
	'label' => esc_attr__( 'Hover Background', 'modal-window' ),
	'attr'  => [
		'name'        => 'param[umodal_button_hover]',
		'id'          => 'umodal_button_hover',
		'value'       => isset( $param['umodal_button_hover'] ) ? $param['umodal_button_hover'] : '#797979',
	],
);
//endregion