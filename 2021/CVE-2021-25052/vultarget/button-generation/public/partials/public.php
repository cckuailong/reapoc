<?php
/**
 * Display the Item on the frontend
 *
 * @package     Wow_Plugin
 * @subpackage  Public/Partials/Frontend
 * @author      Wow-Company <support@wow-company.com>
 * @copyright   2019 Wow-Company
 * @license     GNU Public License
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$text = sanitize_text_field( $param['text'] );
$icon = sanitize_text_field($param['icon']);
$rotate_icon = sanitize_text_field($param['rotate_icon']);

if ( $param['appearance'] == 'text' ) {
	$btn_text = $text;
} elseif ( $param['appearance'] == 'text_icon' ) {
	if ( $param['text_location'] == 'after' ) {

		$btn_text = '<i class="' . $icon . ' ' . $rotate_icon . ' hvr-icon"></i> ' . $text;
	} else {
		$btn_text = $text . ' <i class="' . $icon . ' ' . $rotate_icon . ' hvr-icon"></i>';
	}

} elseif ( $param['appearance'] == 'icon' ) {
	$btn_text = '<i class="' . $icon . ' ' . $rotate_icon . ' hvr-icon"></i>';
}

$float = '';
if ( $param['type'] == 'floating' ) {
	$float = ' btg-' . $param['location'];
}

$class = '';
if ( ! empty( $param['button_class'] ) ) {
	$class = ' ' . sanitize_text_field( $param['button_class'] );
}



$button_id = '';
if ( ! empty( $param['button_id'] ) ) {
	$button_id = ' id="' . sanitize_text_field( $param['button_id'] ) . '"';
}


$action = 'link';

$button_url = !empty( $param['button_url'] ) ? $param['button_url'] : '' ;

$link   = isset( $param['item_link'] ) ? $param['item_link'] : $button_url;

$button_url    = ! empty( $link ) ? ' data-url="' . esc_url( $link ) . '"' : '';
$button_action = ! empty( $action ) ? ' data-action="' . $action . '"' : '';

$button = '<button class="btg-button btg-button-' . $id . $float . $class . '"' . $button_id .
          $button_url . $button_action . ' onclick="btnaction(' . $id . ')">' . $btn_text .'</button>';

echo $button;
