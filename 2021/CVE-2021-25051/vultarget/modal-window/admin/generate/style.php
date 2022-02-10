<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

include( 'style_data.php' );

// Close Button

if ( $close_type == 'text' ) {
	$close_css       = '
			content: "' . $btn_text . '";
			color: ' . $btn_color . ';
			padding: ' . $btn_text_padding . ';
			font-family: ' . $btn_font . ';
			font-size: ' . $btn_size . 'px;
			font-weight: ' . $btn_font_weight . ';
			font-style: ' . $btn_font_style . ';
			background: linear-gradient(to right, ' . $btn_background_hover . ' 50%, ' . $btn_background . ' 50%);
			background-size: 200% 100%;
			background-position: right bottom;
			border-radius: ' . $btn_border_radius . ';
		';
	$close_hover_css = '
			color: ' . $btn_color_hover . ';
			background-position: left bottom;
		';
} else {
	$close_css       = '
			content: "\00d7";	
			text-align: center;
			width: ' . $btn_box_size . 'px;
			height: ' . $btn_box_size . 'px;
			line-height: ' . $btn_box_size . 'px;
			color: ' . $btn_color . ';			
			font-family: ' . $btn_font . ';
			font-size: ' . $btn_size . 'px;
			font-weight: ' . $btn_font_weight . ';
			font-style: ' . $btn_font_style . ';
			background: ' . $btn_background . ';	
			border-radius: ' . $btn_border_radius . ';
		';
	$close_hover_css = '
			color: ' . $btn_color_hover . ';
			background: ' . $btn_background_hover . ';
		';

}


$css = '';

$css .= '
	@media only screen and (max-width: ' . $screen_size . 'px){
		#wow-modal-window-' . $id . ' {
			width:' . $mobile_width . $mobile_width_par . ' !important;
		}
	}
	';

// Close Button
$css .= '
	#wow-modal-close-' . $id . ' {
		' . $btn_loc . '
	}';


$css .= '
	#wow-modal-close-' . $id . '.mw-close-btn.' . $close_type . ':before {
		' . $close_css . '
	}	
	#wow-modal-close-' . $id . '.mw-close-btn.' . $close_type . ':hover:before {
		' . $close_hover_css . '
	}	
	';

if ( $param['umodal_button'] === 'yes' ) {
	$css .= '	
	.wow-modal-button-' . $id . ' {
		' . $button_position . '		
		font-size: ' . $button_text_size . ';		
	}
	.wow-animated-' . $id . ' {
		-webkit-animation-duration: ' . $button_animate_duration . 's;
		animation-duration: ' . $button_animate_duration . 's;
		-webkit-animation-fill-mode: both;
		animation-fill-mode: both;    
	}
	';

	if ( $param['button_type'] == 3 ) {
		$css .= '
		.wow-modal-button-' . $id . ' .wow-icon-parent-' . $id . '{
			color: ' . $umodal_button_color . ';		
		}
		.wow-modal-button-' . $id . ' .wow-icon-child-' . $id . '{
			color: ' . $button_text_color . ';		
		}
		
		.wow-modal-button-' . $id . ':hover .wow-icon-parent-' . $id . '{
			color: ' . $umodal_button_hover . ';		
		}
		.wow-modal-button-' . $id . ':hover .wow-icon-child-' . $id . '{
			color: ' . $button_text_hcolor . ';		
		}	
		
		';
	} else {
		$css .= '
		.wow-modal-button-' . $id . ' {
			color: ' . $button_text_color . ';	
			border-radius: ' . $button_radius . 'px;
			padding: ' . $button_padding_top . 'px ' . $button_padding_left . 'px;
			line-height: ' . $button_padding_top . 'px;
			background: ' . $umodal_button_color . '; 
			}
		.wow-modal-button-' . $id . ':hover {	
			background: ' . $umodal_button_hover . '; 
			color: ' . $button_text_hcolor . ';
		}		
		';
	}
}

$content = $param['content'];
$needle  = '{form}';

$pos = strripos( $content, $needle );

if ($pos !== false) {
	$css .= '
	#smwform-' . $id . ' { 
		 width: ' . $form_width . ';	 
		 padding: ' . $form_padding . ';  
		 background: ' . $form_background . '; 
		 border: ' . $form_border . ' solid ' . $form_border_color . ';
		 border-radius: ' . $form_radius . ';
		 overflow: auto;
		 margin: ' . $form_margin . ';
	}
	#smwform-' . $id . ' .smw-input, #smwform-' . $id . ' textarea{ 
		 width: calc( 100% - ' . $field_border . '); 		 
		 background: ' . $field_background . ';
		 border: ' . $field_border . ' solid ' . $field_border_color . ';
		 border-radius: ' . $field_radius . ';		 		 
		 color: ' . $form_text_color . ';
		 font-size:' . $form_text_size . ';		 
	} 
	#smwform-' . $id . ' .smw-input {
	    height: ' . $form_input_height . ';
	    line-height: ' . $form_input_height . ';
	}
	#smwform-' . $id . ' input[type=submit]{
		padding: 10px;
		width: calc( 100% - ' . $field_border . ');
		display: inline-block; 
		color: ' . $form_button_text_color . '; 
		background: ' . $button_background_color . ';
		border: ' . $field_border . ' solid ' . $field_border_color . ';
		border-radius: ' . $field_radius . ';
		font-size: ' . $form_button_size . ';
		text-decoration: none;
	}  
	#smwform-' . $id . ' input[type=submit]:hover {
		cursor: pointer; 
		background: ' . $button_hover_color . '; 
	}
	#smwform-' . $id . ' textarea{ 
		height: ' . $form_textarea_height . ';	
		color: ' . $form_text_color . ';
		overflow: auto;
	}	
	#smwform-' . $id . ' textarea::-webkit-input-placeholder,
	#smwform-' . $id . ' textarea::-moz-placeholder,
	#smwform-' . $id . ' textarea :-ms-input-placeholder, 
	#smwform-' . $id . ' .smw-input::-webkit-input-placeholder,
	#smwform-' . $id . ' .smw-input::-moz-placeholder,
	#smwform-' . $id . ' .smw-input:-ms-input-placeholder { 
       color: ' . $form_text_color . ';
       opacity: 0.75;
    }';
}
if ( ! empty( $param['include_mobile'] ) ) {
	$css .= '
			@media only screen and (max-width: ' . $screen . 'px){
			.wow-modal-button-' . $id . ' {
					display:none !important;
				}
			}
		';
}

if ( ! empty( $param['include_more_screen'] ) ) {
	$css .= '
			@media only screen and (min-width: ' . $screen_more . 'px){
			.wow-modal-button-' . $id . ' {
					display:none !important;
				}
			}
		';
}

$css = trim( preg_replace( '~\s+~s', ' ', $css ) );
