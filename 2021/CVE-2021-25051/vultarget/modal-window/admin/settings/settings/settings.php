<?php
/**
 * Notification settings parameters
 *
 * @package     Wow_Plugin
 * @copyright   Copyright (c) 2018, Dmytro Lobov
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

//region Triggers
$modalAction = isset($param['modal_show']) ? $param['modal_show'] : 'load';
if ($modalAction == 'hoverid' || $modalAction == 'hoveranchor'){
	$modalAction = 'hover';
}

$modal_show = array(
	'label'   => esc_attr__( 'Triggers', 'modal-window' ),
	'attr'    => [
		'name'  => 'param[modal_show]',
		'id'    => 'modal_show',
		'value' => isset( $param['modal_show'] ) ? $param['modal_show'] : $modalAction,
	],
	'options' => [
		'click'        => esc_attr__('Click', 'modal-window'),
		'load'         => esc_attr__('Auto', 'modal-window'),
		'hover'        => esc_attr__('Hover', 'modal-window'),
		'close'        => esc_attr__('Exit', 'modal-window'),
		'scroll'       => esc_attr__('Scrolled', 'modal-window'),
	],
	'func'    => 'triggers()',
);

$modal_timer = array(
	'label' => esc_attr__( 'Delay', 'modal-window' ),
	'attr' => [
		'name'   => 'param[modal_timer]',
		'id'     => 'modal_timer',
		'value'    => isset( $param['modal_timer'] ) ? $param['modal_timer'] : '0',
		'min'         => '0',
		'step'        => '0.1',
	],
	'addon' =>  [
		'unit' => 'sec',
	],
	'tooltip' => esc_attr__( 'Delay time in seconds for opening popup.', 'modal-window'),
);

$reach_window = array(
	'label'    => esc_attr__( 'Reach', 'modal-window' ),
	'attr'     => [
		'name'  => 'param[reach_window]',
		'id'    => 'reach_window',
		'value' => isset( $param['reach_window'] ) ? $param['reach_window'] : '0',
		'min'   => '0',
		'step'  => '1',
	],
	'addon'    => [
		'name'    => 'param[reach_window_unit]',
		'value'   => isset( $param['reach_window_unit'] ) ? $param['reach_window_unit'] : 'px',
		'options' => [
			'px' => esc_attr__( 'px', 'modal-window' ),
			'%' => esc_attr__( '%', 'modal-window' ),
		],
	],
	'tooltip'     => esc_attr__( 'Distance from the top of the window for scrolled popup type.', 'modal-window' ),
);

$use_cookies = array(
	'label'   => esc_attr__( 'Show only once', 'modal-window' ),
	'attr'    => [
		'name'  => 'param[use_cookies]',
		'id'    => 'use_cookies',
		'value' => isset( $param['use_cookies'] ) ? $param['use_cookies'] : 'no',
	],
	'options' => [
		'no' => esc_attr__( 'no', 'modal-window' ),
		'yes' => esc_attr__( 'yes', 'modal-window' ),
	],
	'tooltip'    => esc_attr__( 'Defines if the popup will set a cookie and hide it self for a period of time from the user.  Set the cookie in days.', 'modal-window' ),
	'icon'    => '',
	'func'    => 'useCookies()',
);

$modal_cookies = array(
	'label'    => esc_attr__( 'Reset', 'modal-window' ),
	'attr'     => [
		'name'  => 'param[modal_cookies]',
		'id'    => 'modal_cookies',
		'value' => isset( $param['modal_cookies'] ) ? $param['modal_cookies'] : '0',
		'min'   => '0',
		'step'   => '0.01',
	],
	'addon'    => [
		'unit'  => 'days',
	],
);

//endregion

//region Close Popup
$close_button_remove = array(
	'label' => esc_attr__( 'Remove Close Button', 'modal-window' ),
	'attr' => [
		'name'   => 'param[close_button_remove]',
		'id'     => 'close_button_remove',
		'value'    => isset( $param['close_button_remove'] ) ? $param['close_button_remove'] : '',
	],
	'tooltip' => esc_attr__( 'Remove and don&rsquo;t show the popup close button.', 'modal-window' ),
);

$close_button_overlay = array(
	'label' => esc_attr__( 'Click Overlay to Close', 'modal-window' ),
	'attr'  => [
		'name'        => 'param[close_button_overlay]',
		'id'          => 'close_button_overlay',
		'value'       => isset( $param['close_button_overlay'] ) ? $param['close_button_overlay'] : '',
	],
	'tooltip'  => esc_attr__( 'Specify if overlay can close the modal or not.', 'modal-window' ),
	'icon'  => '',
);

$close_button_esc = array(
	'label' => esc_attr__( 'Press ESC to Close', 'modal-window' ),
	'attr'  => [
		'name'        => 'param[close_button_esc]',
		'id'          => 'close_button_esc',
		'value'       => isset( $param['close_button_esc'] ) ? $param['close_button_esc'] : '',
	],
	'tooltip'  => esc_attr__( 'Enabled the ESC key to close the popup.', 'modal-window' ),
	'icon'  => '',
);

$close_delay = array(
	'label'    => esc_attr__( 'Delay', 'modal-window' ),
	'attr'     => [
		'name'  => 'param[close_delay]',
		'id'    => 'close_delay',
		'value' => isset( $param['close_delay'] ) ? $param['close_delay'] : '0',
		'min'   => '0',
		'step'  => '0.1',
	],
	'addon'    => [
		 'unit' => 'sec',
	],
	'tooltip'     => esc_attr__( 'Delay time in seconds for showing popup close button.', 'modal-window' ),
);

$auto_close_delay = array(
	'label'    => esc_attr__( 'Auto close', 'modal-window' ),
	'attr'     => [
		'name'  => 'param[auto_close_delay]',
		'id'    => 'auto_close_delay',
		'value' => isset( $param['auto_close_delay'] ) ? $param['auto_close_delay'] : '0',
		'min'   => '0',
	],
	'checkbox' => [
		'name'  => 'param[modal_auto_close]',
		'id'    => 'modal_auto_close',
		'class' => 'checkLabel',
		'value' => isset( $param['modal_auto_close'] ) ? $param['modal_auto_close'] : '',
	],
	'addon'    => [
		 'unit' => 'sec',
	],
	'tooltip'     => esc_attr__( 'Set the time in seconds during which you want the modal window to be open.', 'modal-window' ),
);

$close_redirect = array(
	'label'    => esc_attr__( 'Redirect after close', 'modal-window' ),
	'attr'     => [
		'name'        => 'param[close_redirect]',
		'id'          => 'close_redirect',
		'value'       => isset( $param['close_redirect'] ) ? $param['close_redirect'] : '',
		'placeholder' => esc_attr__( 'Enter URL', 'modal-window' ),
	],
	'checkbox' => [
		'name'  => 'param[close_redirect_checkbox]',
		'id'    => 'close_redirect_checkbox',
		'class' => 'checkLabel checkBlock',
		'value' => isset( $param['close_redirect_checkbox'] ) ? $param['close_redirect_checkbox'] : 0,
	],
	'tooltip'  => esc_attr__( 'This option redirects the visitor after popup close.', 'modal-window' ),
);

$close_redirect_target = array(
	'label'   => esc_attr__( 'Redirect Target', 'modal-window' ),
	'attr'    => [
		'name'  => 'param[close_redirect_target]',
		'id'    => 'close_redirect_target',
		'value' => isset( $param['close_redirect_target'] ) ? $param['close_redirect_target'] : '_blank',
	],
	'options' => [
		'_blank' => esc_attr__( 'New tab', 'modal-window' ),
		'_self'  => esc_attr__( 'Same tab', 'modal-window' ),
	],
	'tooltip' => esc_attr__( 'Target for opening the redirected URL.', 'modal-window' ),
);

//endregion


//region Animation
$animations = array(
	'no'                         => __( 'no', 'modal-window' ),
	'blind:direction:up'         => __( 'Blind Up', 'modal-window' ),
	'blind:direction:down'       => __( 'Blind Down', 'modal-window' ),
	'blind:direction:left'       => __( 'Blind Left', 'modal-window' ),
	'blind:direction:right'      => __( 'Blind Right', 'modal-window' ),
	'bounce:times:3'             => __( 'Bounce', 'modal-window' ),
	'clip:direction:vertical'    => __( 'Clip Vertical', 'modal-window' ),
	'clip:direction:horizontal'  => __( 'Clip Horizontal', 'modal-window' ),
	'drop:direction:up'          => __( 'Drop Up', 'modal-window' ),
	'drop:direction:down'        => __( 'Drop Down', 'modal-window' ),
	'drop:direction:left'        => __( 'Drop Left', 'modal-window' ),
	'drop:direction:right'       => __( 'Drop Right', 'modal-window' ),
	'explode:pieces:4'           => __( 'Explode 4 Pieces', 'modal-window' ),
	'explode:pieces:9'           => __( 'Explode 9 Pieces', 'modal-window' ),
	'explode:pieces:12'          => __( 'Explode 12 Pieces', 'modal-window' ),
	'fade'                       => __( 'Fade', 'modal-window' ),
	'fold:size:5'                => __( 'Fold', 'modal-window' ),
	'highlight:color:#ffff99'    => __( 'Highlight', 'modal-window' ),
	'puff:percent:150'           => __( 'Puff', 'modal-window' ),
	'pulsate:times:5'            => __( 'Pulsate', 'modal-window' ),
	'scale:direction:both'       => __( 'Scale Both', 'modal-window' ),
	'scale:direction:vertical'   => __( 'Scale Vertical', 'modal-window' ),
	'scale:direction:horizontal' => __( 'Scale Horizontal', 'modal-window' ),
	'shake:direction:left'       => __( 'Shake Left', 'modal-window' ),
	'shake:direction:up'         => __( 'Shake Up', 'modal-window' ),
	'slide:direction:left'       => __( 'Slide Left', 'modal-window' ),
	'slide:direction:right'      => __( 'Slide Right', 'modal-window' ),
	'slide:direction:up'         => __( 'Slide Up', 'modal-window' ),
	'slide:direction:down'       => __( 'Slide Down', 'modal-window' ),
);

$window_animate = array(
	'label'   => esc_attr__( 'AnimateIn', 'modal-window' ),
	'attr'    => [
		'name'  => 'param[window_animate]',
		'id'    => 'window_animate',
		'value' => isset( $param['window_animate'] ) ? $param['window_animate'] : '',
	],
	'options' => $animations,
	'tooltip'    => esc_attr__( 'Specify modal window transition open effect.', 'modal-window' ),
);

$speed_window = array(
	'label' => esc_attr__( 'AnimateIn Speed', 'modal-window' ),
	'attr' => [
		'name'   => 'param[speed_window]',
		'id'     => 'speed_window',
		'value'    => isset( $param['speed_window'] ) ? $param['speed_window'] : '400',
		'min'         => '0',
		'step'        => '1',
	],
	'addon'    => [
		'unit' => 'ms',
	],
	'tooltip' => esc_attr__( 'Specify popup animation effect speed', 'modal-window' ),
);

$window_animate_out = array(
	'label'   => esc_attr__( 'AnimateOut', 'modal-window' ),
	'attr'    => [
		'name'  => 'param[window_animate_out]',
		'id'    => 'window_animate_out',
		'value' => isset( $param['window_animate_out'] ) ? $param['window_animate_out'] : '',
	],
	'options' => $animations,
	'tooltip'    => esc_attr__( 'Specify modal window transition close effect.', 'modal-window' ),
	'icon'    => '',
	'func'    => '',
);

$speed_window_out = array(
	'label' => esc_attr__( 'AnimateOut Speed', 'modal-window' ),
	'attr' => [
		'name'   => 'param[speed_window_out]',
		'id'     => 'speed_window_out',
		'value'    => isset( $param['speed_window_out'] ) ? $param['speed_window_out'] : '400',
		'min'         => '0',
		'step'        => '1',
	],
	'addon'    => [
		'unit' => 'ms',
	],
	'tooltip' => esc_attr__( 'Specify popup animation effect speed', 'modal-window' ),
);
//endregion

//region Youtube video


$video_support = array(
	'label'   => esc_attr__( 'Video support', 'modal-window' ),
	'attr'    => [
		'name'  => 'param[video_support]',
		'id'    => 'video_support',
		'value' => isset( $param['video_support'] ) ? $param['video_support'] : '1',
	],
	'options' => [
		'1' => esc_attr__( 'no', 'modal-window' ),
		'2' => esc_attr__( 'yes', 'modal-window' ),
	],
	'tooltip'    => esc_attr__( 'If enable checkbox, the modal will support Youtube video auto play and video stop on closing the modal.', 'modal-window' ),
	'icon'    => '',
	'func'    => 'youtubeSupport()',
);

$video_autoplay = array(
	'label'   => esc_attr__( 'Video autoplay', 'modal-window' ),
	'attr'    => [
		'name'  => 'param[video_autoplay]',
		'id'    => 'video_autoplay',
		'value' => isset( $param['video_autoplay'] ) ? $param['video_autoplay'] : '1',
	],
	'options' => [
		'1' => esc_attr__( 'no', 'modal-window' ),
		'2' => esc_attr__( 'yes', 'modal-window' ),
	],
	'tooltip'    => esc_attr__( 'If enable, the video will autoplay on modal opening.', 'modal-window' ),
);

$video_close = array(
	'label'   => esc_attr__( 'Stop on close', 'modal-window' ),
	'attr'    => [
		'name'  => 'param[video_close]',
		'id'    => 'video_close',
		'value' => isset( $param['video_close'] ) ? $param['video_close'] : '1',
	],
	'options' => [
		'1' => esc_attr__( 'no', 'modal-window' ),
		'2' => esc_attr__( 'yes', 'modal-window' ),
	],
	'tooltip'    => esc_attr__( 'If enable, the video will stop playing on modal closing.', 'modal-window' ),

);
//endregion