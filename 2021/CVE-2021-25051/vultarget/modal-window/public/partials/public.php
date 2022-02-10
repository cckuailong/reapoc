<?php
/**
 * Public final
 *
 * @package     Wow_Pluign
 * @subpackage  Public
 * @copyright   Copyright (c) 2019, Dmytro Lobov
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$close_type     = ! empty( $param['close_type'] ) ? $param['close_type'] : 'text';
$close_location = ! empty( $param['close_location'] ) ? $param['close_location'] : 'topRight';

$rotate_icon = ! empty( $param['rotate_icon'] ) ? ' ' . $param['rotate_icon'] : '';
if ( $param['button_type'] === '1' || empty( $param['button_type'] ) ) {
	$button_text = ( ! empty( $param['umodal_button_text'] ) ) ? $param['umodal_button_text'] : esc_attr__( 'Feedback' );
}
if ( ! empty( $param['include_overlay'] ) ) {
	$classoverlow = 'class="wow-modal-overlay"';
	$overclose    = 'class="wow-modal-overclose"';
} else {
	$classoverlow = '';
	$overclose    = '';
}

$content = do_shortcode( $param['content'] );

$modal = '';
if ( $param['umodal_button'] === 'yes' ) {
	$modal .= '<div class="wow-modal-button-' . absint( $id ) . ' ' . esc_attr( $param['umodal_button_position'] ) . '" id="wow-modal-id-' . absint( $id ) . '">' . $button_text . '</div>';
}
$modal .= '<div id="wow-modal-overlay-' . absint( $id ) . '" ' . ( $classoverlow ) . ' style="display:none;">';
$modal .= '<div id="wow-modal-overclose-' . absint( $id ) . '" ' . ( $overclose ) . '></div>';
$modal .= '<div id="wow-modal-window-' . absint( $id ) . '" class="wow-modal-window" style="display:none;">';
$modal .= '<div id="wow-modal-close-' . absint( $id ) . '" class="mw-close-btn ' . esc_attr( $close_location ) . ' ' . esc_attr( $close_type ) . '"></div>';

$modal .= '<div class="modal-window-content">';
if ( ! empty( $param['popup_title'] ) ) {
	$modal .= '<div class="mw-title">' . esc_attr( $val->title ) . '</div>';
}

$modal .= $content;
$modal .= '</div></div></div>';
echo $modal;