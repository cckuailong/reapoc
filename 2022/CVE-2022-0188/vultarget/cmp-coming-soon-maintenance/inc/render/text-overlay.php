<?php

$overlay_text_status = get_option('niteoCS_overlay_text[status]', '1');

$html = '';

// return if overlay is disabled
if ( $overlay_text_status == '0' || !in_array( $themeslug, $this->cmp_overlay_text_themes() ) ) {
    return;
}

$heading 			= $this->cmp_wpml_translate_string( stripslashes(get_option('niteoCS_overlay_text[heading]', 'NEW WEBSITE ON THE WAY!') ), 'Overlay Title' );
$paragraph 		    = $this->cmp_wpml_translate_string( stripslashes(get_option('niteoCS_overlay_text[paragraph]', '') ), 'Overlay Content' );
$button_text 		= $this->cmp_wpml_translate_string( stripslashes(get_option('niteoCS_overlay_text[button_text]', 'Call to Action!') ), 'Overlay Button Text' );
$button_url 		= get_option('niteoCS_overlay_text[button_url]', '');

$heading        = ( $heading == '' ) ? '' : '<h2 class="animated fadeInRight delay-small">'.esc_html( $heading ).'</h2>';
$paragraph      = ( $paragraph == '' ) ? '' : '<div class="animated fadeInRight delay-big">'. wpautop( esc_html( $paragraph ) ) . '</div>';
$button         = ( $button_text == '' ) ? '' : '<a href="'.esc_html( $button_url ).'" class="button animated fadeInRight delay-huge" target="_blank">'.esc_html( $button_text ).'</a>';

$html = '<div class="overlay-content">' . $heading . $paragraph . $button . '</div>';