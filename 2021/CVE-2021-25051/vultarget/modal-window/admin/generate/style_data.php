<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Close Button Style

$close_type     = ! empty( $param['close_type'] ) ? $param['close_type'] : 'text';
$close_location = ! empty( $param['close_location'] ) ? $param['close_location'] : 'topRight';

$close_top_position   = ! empty( $param['close_top_position'] ) ? $param['close_top_position'] : '0';
$close_right_position = ! empty( $param['close_right_position'] ) ? $param['close_right_position'] : '0';

$close_bottom_position = ! empty( $param['close_bottom_position'] ) ? $param['close_bottom_position'] : '-10';
$close_left_position   = ! empty( $param['close_left_position'] ) ? $param['close_left_position'] : '0';

switch ( $close_location ) {
	case 'topLeft':
		$btn_loc = 'top: ' . $close_top_position . 'px;';
		$btn_loc .= 'left: ' . $close_left_position . 'px;';
		break;
	case 'topRight':
		$btn_loc = 'top: ' . $close_top_position . 'px;';
		$btn_loc .= 'right: ' . $close_right_position . 'px;';
		break;
	case 'bottomLeft':
		$btn_loc = 'bottom: ' . $close_bottom_position . 'px;';
		$btn_loc .= 'left: ' . $close_left_position . 'px;';
		break;
	case 'bottomRight':
		$btn_loc = 'bottom: ' . $close_bottom_position . 'px;';
		$btn_loc .= 'right: ' . $close_right_position . 'px;';
		break;
	default:
		$btn_loc = '';
}

$btn_text             = ! empty( $param['close_content'] ) ? $param['close_content'] : 'Close';
$btn_text_padding     = ! empty( $param['close_padding'] ) ? $param['close_padding'] : '6px 12px';
$btn_box_size         = ! empty( $param['close_box_size'] ) ? $param['close_box_size'] : '0';
$btn_size             = ! empty( $param['close_size'] ) ? $param['close_size'] : '0';
$btn_font             = ! empty( $param['close_font'] ) ? $param['close_font'] : 'inherit';
$btn_font_weight      = ! empty( $param['close_weight'] ) ? $param['close_weight'] : 'normal';
$btn_font_style       = ! empty( $param['close_font_style'] ) ? $param['close_font_style'] : 'normal';
$btn_color            = ! empty( $param['close_content_color'] ) ? $param['close_content_color'] : '#fff';
$btn_color_hover      = ! empty( $param['close_content_color_hover'] ) ? $param['close_content_color_hover'] : '#000';
$btn_background       = ! empty( $param['close_background_color'] ) ? $param['close_background_color'] : '#000';
$btn_background_hover = ! empty( $param['close_background_hover'] ) ? $param['close_background_hover'] : '#fff';
$btn_border_radius    = ! empty( $param['close_border_radius'] ) ? $param['close_border_radius'] . 'px' : '0';


// Popup Title style
$title_font        = ! empty( $param['title_font'] ) ? $param['title_font'] : 'inherit';
$title_size        = ! empty( $param['title_size'] ) ? $param['title_size'] : '32';
$title_line_height = ! empty( $param['title_line_height'] ) ? $param['title_line_height'] : '36';
$title_font_weight = ! empty( $param['title_font_weight'] ) ? $param['title_font_weight'] : 'normal';
$title_font_style  = ! empty( $param['title_font_style'] ) ? $param['title_font_style'] : 'normal';
$title_align       = ! empty( $param['title_align'] ) ? $param['title_align'] : 'center';
$title_color       = ! empty( $param['title_color'] ) ? $param['title_color'] : '#383838';


$margin   = ! empty( $param['button_margin'] ) ? $param['button_margin'] : '-4';
$position = ! empty( $param['button_position'] ) ? $param['button_position'] : '50';

switch ( $param['umodal_button_position'] ) {
	case 'wow_modal_button_right':
		$button_position = 'top:' . $position . '%; right:' . $margin . 'px;';
		break;
	case 'wow_modal_button_left':
		$button_position = 'top:' . $position . '%; left:' . $margin . 'px;';
		break;
	case 'wow_modal_button_top':
		$button_position = 'left:' . $position . '%; top:' . $margin . 'px;';
		break;
	case 'wow_modal_button_bottom':
		$button_position = 'left:' . $position . '%; bottom:' . $margin . 'px;';
		break;
}

$button_text_size_unit = ! empty( $param['button_text_size_unit'] ) ? $param['button_text_size_unit'] : 'em';
$button_text_size      = ! empty( $param['button_text_size'] ) ? $param['button_text_size'] . $button_text_size_unit : '1.2' . $button_text_size_unit;

$umodal_button_color     = ! empty( $param['umodal_button_color'] ) ? $param['umodal_button_color'] : '#383838';
$button_text_color       = ! empty( $param['button_text_color'] ) ? $param['button_text_color'] : '#ffffff';
$form_button_text_color  = ! empty( $param['form_button_text_color'] ) ? $param['form_button_text_color'] : '#ffffff';
$umodal_button_hover     = ! empty( $param['umodal_button_hover'] ) ? $param['umodal_button_hover'] : '#797979';
$button_text_hcolor      = ! empty( $param['button_text_hcolor'] ) ? $param['button_text_hcolor'] : '#ffffff';
$button_radius           = ! empty( $param['button_radius'] ) ? $param['button_radius'] : '4';
$button_padding_top      = ! empty( $param['button_padding_top'] ) ? $param['button_padding_top'] : '14';
$button_padding_left     = ! empty( $param['button_padding_left'] ) ? $param['button_padding_left'] : '14';
$button_animate_duration = ! empty( $param['button_animate_duration'] ) ? $param['button_animate_duration'] : '1';
$screen_size             = ! empty( $param['screen_size'] ) ? $param['screen_size'] : '1024';
$mobile_width            = ! empty( $param['mobile_width'] ) ? $param['mobile_width'] : '85';
$mobile_width_par        = ( $param['mobile_width_par'] == 'pr' ) ? '%' : 'px';


$form_width   = ! empty( $param['form_width'] ) ? $param['form_width'] : '100%';
$form_padding = ! empty( $param['form_padding'] ) ? $param['form_padding'] : '10px';
$form_margin  = ! empty( $param['form_margin'] ) ? $param['form_margin'] : '0 auto';

$form_background         = ! empty( $param['form_background'] ) ? $param['form_background'] : '#ffffff';
$form_border             = ! empty( $param['form_border'] ) ? $param['form_border'] : '1px';
$form_border_color       = ! empty( $param['form_border_color'] ) ? $param['form_border_color'] : '#ffffff';
$form_radius             = ! empty( $param['form_radius'] ) ? $param['form_radius'] : '0px';
$field_background        = ! empty( $param['field_background'] ) ? $param['field_background'] : '#ffffff';
$field_border            = ! empty( $param['field_border'] ) ? $param['field_border'] : '1px';
$field_border_color      = ! empty( $param['field_border_color'] ) ? $param['field_border_color'] : '#383838';
$field_radius            = ! empty( $param['field_radius'] ) ? $param['field_radius'] : '0px';
$form_button_size        = ! empty( $param['form_button_size'] ) ? $param['form_button_size'] : '16px';
$button_hover_color      = ! empty( $param['button_hover_color'] ) ? $param['button_hover_color'] : '#d45041';
$form_textarea_height    = ! empty( $param['form_textarea_height'] ) ? $param['form_textarea_height'] : '36px';
$screen                  = ! empty( $param['screen'] ) ? $param['screen'] : '480';
$screen_more             = ! empty( $param['screen_more'] ) ? $param['screen_more'] : '1400';
$form_input_height       = ! empty( $param['form_input_height'] ) ? $param['form_input_height'] : '36px';
$form_text_color         = ! empty( $param['form_text_color'] ) ? $param['form_text_color'] : '#383838';
$form_text_size          = ! empty( $param['form_text_size'] ) ? $param['form_text_size'] : '16px';
$button_background_color = ! empty( $param['button_background_color'] ) ? $param['button_background_color'] : '#e95645';


	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
