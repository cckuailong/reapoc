<?php
/**
 * Notification script generation
 *
 * @package     WP_Plugin
 * @copyright   Copyright (c) 2018, Dmytro Lobov
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$animationOpen       = ! empty( $param['window_animate'] ) ? $param['window_animate'] : 'no';
$animationClose      = ! empty( $param['window_animate_out'] ) ? $param['window_animate_out'] : 'no';
$animationSpeedOpen  = ! empty( $param['speed_window'] ) ? $param['speed_window'] : '400';
$animationSpeedClose = ! empty( $param['speed_window_out'] ) ? $param['speed_window_out'] : '400';
$script['animation'] = [ $animationOpen, $animationSpeedOpen, $animationClose, $animationSpeedClose ];

if ( ! empty( $param['include_overlay'] ) ) {
	$script['overlay']     = true;
	$overlay_color         = ! empty( $param['overlay_color'] ) ? $param['overlay_color'] : 'rgba(0, 0, 0, 0.7)';
	$script['overlay_css'] = [ $overlay_color ];
} else {
	$script['overlay']     = false;
}


if ( ! empty( $param['video_support'] && $param['video_support'] == 2 ) ) {
	$videoAutoPlay    = ( ! empty( $param['video_autoplay'] ) && $param['video_autoplay'] == 2 ) ? true : false;
	$videoStopOnClose = ( ! empty( $param['video_close'] ) && $param['video_close'] == 2 ) ? true : false;
	$script['video']  = [ true, $videoAutoPlay, $videoStopOnClose ];
}

$modalAction = ! empty( $param['modal_show'] ) ? $param['modal_show'] : 'load';
if ( $modalAction == 'hoverid' || $modalAction == 'hoveranchor' ) {
	$modalAction = 'hover';
}
$delayModalWindow = ! empty( $param['modal_timer'] ) ? $param['modal_timer'] : '0';

$script['action'] = [ $modalAction, $delayModalWindow ];

if ( $modalAction === 'scroll' ) {
	$modalScrollDistance = ! empty( $param['reach_window'] ) ? $param['reach_window'] : '0';
	$modalScrollUnit     = ! empty( $param['reach_window_unit'] ) ? $param['reach_window_unit'] : 'px';
	$script['scrolled']  = [ $modalScrollDistance, $modalScrollUnit ];
}

$closeButtonRemove  = ! empty( $param['close_button_remove'] ) ? true : false;
$delayCloseButton   = ! empty( $param['close_delay'] ) ? $param['close_delay'] : '0';
$script['closeBtn'] = [ $closeButtonRemove, $delayCloseButton ];

if ( ! empty( $param['modal_auto_close'] ) ) {
	$closeModalAutoDelay = ! empty( $param['auto_close_delay'] ) ? $param['auto_close_delay'] : '5';
	$script['autoClose'] = [ true, $closeModalAutoDelay ];
}

$closeOverlay          = ! empty( $param['close_button_overlay'] ) ? true : false;
$closeEsc              = ! empty( $param['close_button_esc'] ) ? true : false;
$script['closeAction'] = [ $closeOverlay, $closeEsc ];

if ( ! empty( $param['include_more_screen'] ) ) {
	$screen              = ! empty( $param['screen_more'] ) ? $param['screen_more'] : '1024';
	$script['screenMax'] = [ true, $screen ];
}

if ( ! empty( $param['include_mobile'] ) ) {
	$screen              = ! empty( $param['screen'] ) ? $param['screen'] : '480';
	$script['screenMin'] = [ true, $screen ];
}

if ( $param['umodal_button'] === 'yes' && $param['button_animate'] !== 'no' ) {
	$buttonAnimation     = ! empty( $param['button_animate'] ) ? $param['button_animate'] : 'flash';
	$buttonAnimationTime = ! empty( $param['button_animate_time'] ) ? $param['button_animate_time'] : '5';
	if ( $param['umodal_button_position'] == 'wow_modal_button_right' ) {
		$buttonPosition = 'right';
	} else if ( $param['umodal_button_position'] == 'wow_modal_button_left' ) {
		$buttonPosition = 'left';
	} else {
		$buttonPosition = '';
	}
	$buttonId             = 'wow-modal-botton-' . $id;
	$buttonAnimationClass = 'wow-animated-' . $id;

	$script['floatBtn'] = [
		true,
		$buttonAnimation,
		$buttonAnimationTime,
		$buttonPosition,
		$buttonId,
		$buttonAnimationClass
	];
}

if ( $param['use_cookies'] === 'yes' ) {
	$cookieDays       = $param['modal_cookies'];
	$cookieName       = 'wow-modal-id-' . $id;
	$script['cookie'] = [ true, $cookieDays, $cookieName ];
}

if ( ! empty( $param['close_redirect_checkbox'] ) ) {
	$script['closeRedirect'] = [ true, $param['close_redirect'], $param['close_redirect_target'] ];
}

$modalOpen   = 'wow-modal-id-' . $id;
$modalClose  = 'wow-modal-close-' . $id;
$closeCustom = 'wow-button-close-' . $id;

$script['triggers'] = [ $modalOpen, $modalClose, $closeCustom ];

if ( $param['modal_zindex'] !== '999999' && ! empty( $param['modal_zindex'] ) ) {
	$script['zindex'] = $param['modal_zindex'];
}


//region Modal CSS
$script['modal_css'] = array();
$modal_width         = ! empty( $param['modal_width'] ) ? $param['modal_width'] : '662';
$modal_width_par     = ( $param['modal_width_par'] == 'pr' ) ? '%' : 'px';
$script['modal_css']['width'] = $modal_width . $modal_width_par;


if ( empty( $param['modal_height'] ) ) {
	$modal_height = 'auto';
} else {
	switch ( $param['modal_height_par'] ) {
		case 'pr':
			$modal_height = $param['modal_height'] . '%';
			break;
		case 'auto':
			$modal_height = 'auto';
			break;
		default:
			$modal_height = $param['modal_height'] . 'px';

	}
}

$script['modal_css']['height'] = $modal_height;

if ( ! empty( $param['include_modal_top'] ) ) {
	$unit                       = ! empty( $param['modal_top_unit'] ) ? $param['modal_top_unit'] : '%';
	$script['modal_css']['top'] = $param['modal_top'] . $unit;
}

if ( ! empty( $param['include_modal_bottom'] ) ) {
	$unit                          = ! empty( $param['modal_bottom_unit'] ) ? $param['modal_bottom_unit'] : '%';
	$script['modal_css']['bottom'] = $param['modal_bottom'] . $unit;
}

if ( ! empty( $param['include_modal_left'] ) ) {
	$unit                        = ! empty( $param['modal_left_unit'] ) ? $param['modal_left_unit'] : '%';
	$script['modal_css']['left'] = $param['modal_left'] . $unit;
}

if ( ! empty( $param['include_modal_right'] ) ) {
	$unit                         = ! empty( $param['modal_right_unit'] ) ? $param['modal_right_unit'] : '%';
	$script['modal_css']['right'] = $param['modal_right'] . $unit;
}

if ( empty( $param['modal_padding'] ) ) {
	$script['modal_css']['padding'] = 0;
} else {
	$script['modal_css']['padding'] = $param['modal_padding'] . 'px';
}

if ( empty( $param['border_width'] ) ) {
	$script['modal_css']['border-width'] = 0;
} else {
	$script['modal_css']['border-width'] = $param['border_width'] . 'px';
}

if ( ! empty( $param['border_style'] ) ) {
	$script['modal_css']['border-style'] = $param['border_style'];
} else {
	$script['modal_css']['border-style'] = 'none';
}

if ( ! empty( $param['border_color'] ) ) {
	$script['modal_css']['border-color'] = $param['border_color'];
}

$script['modal_css']['position'] = $param['modal_position'];

$script['modal_css']['border-radius']    = $param['border_radius'] . 'px';
$script['modal_css']['background-color'] = $param['bg_color'];

if ( ! empty( $param['modal_background_img'] ) ) {
	$script['modal_css']['background-image'] = 'url(' . $param['modal_background_img'] . ')';
	$script['modal_css']['background-size']  = 'cover';
}

// Modal window shadows
$shadow          = ! empty( $param['shadow'] ) ? $param['shadow'] : 'none';
$shadow_h_offset = ! empty( $param['shadow_h_offset'] ) ? $param['shadow_h_offset'] . 'px' : '0';
$shadow_v_offset = ! empty( $param['shadow_v_offset'] ) ? $param['shadow_v_offset'] . 'px' : '0';
$shadow_blur     = ! empty( $param['shadow_blur'] ) ? $param['shadow_blur'] . 'px' : '0';
$shadow_spread   = ! empty( $param['shadow_spread'] ) ? $param['shadow_spread'] . 'px' : '0';
$shadow_color    = ! empty( $param['shadow_color'] ) ? $param['shadow_color'] : '#020202';
switch ( $shadow ) {
	case 'none':
		$script['modal_css']['box-shadow'] = 'none';
		break;
	case 'outset':
		$script['modal_css']['box-shadow'] = $shadow_h_offset . ' ' . $shadow_v_offset . ' ' . $shadow_blur . ' ' . $shadow_spread . ' ' . $shadow_color;
		break;
	default:
		$script['modal_css']['box-shadow'] = 'inset ' . $shadow_h_offset . ' ' . $shadow_v_offset . ' ' . $shadow_blur . ' ' . $shadow_spread . ' ' . $shadow_color;
}
//endregion


// Content Style
$script['content_css']                = array();
$script['content_css']['font-family'] = ! empty( $param['content_font'] ) ? $param['content_font'] : 'inherit';
$script['content_css']['font-size']   = ! empty( $param['content_size'] ) ? $param['content_size'] . 'px' : '16px';

if ( ! empty( $param['popup_title'] ) ) {
	$script['title_css']                = array();
	$script['title_css']['font-family'] = ! empty( $param['title_font'] ) ? $param['title_font'] : 'inherit';
	$script['title_css']['font-size']   = ! empty( $param['title_size'] ) ? $param['title_size'] . 'px' : '32px';
	$script['title_css']['line-height'] = ! empty( $param['title_line_height'] ) ? $param['title_line_height'] . 'px' : '36px';
	$script['title_css']['font-weight'] = ! empty( $param['title_font_weight'] ) ? $param['title_font_weight'] : 'normal';
	$script['title_css']['font-style']  = ! empty( $param['title_font_style'] ) ? $param['title_font_style'] : 'normal';
	$script['title_css']['text-align']  = ! empty( $param['title_align'] ) ? $param['title_align'] : 'center';
	$script['title_css']['color']       = ! empty( $param['title_color'] ) ? $param['title_color'] : '#383838';
}





