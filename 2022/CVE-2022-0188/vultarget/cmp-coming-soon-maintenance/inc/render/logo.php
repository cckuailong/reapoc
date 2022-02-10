<?php

$logo_type = get_option('niteoCS_logo_type', 'text');

$html = '';
$class = ( $class != '' ) ? ' ' . $class : $class;
$logo_link = get_option('niteoCS_logo_link', get_site_url() );
switch ( $logo_type ) {
    case 'graphic':
        // get logo id
        $logo_id = get_option('niteoCS_logo_id');

        // get logo
        if ( $logo_id != '' ) {
            $logo_url = wp_get_attachment_image_src( $logo_id, 'full' );
        }

        if ( isset($logo_url[0]) ) {
            $html = '<div class="logo-wrapper image' . esc_attr( $class ) . '"><a href="'. esc_url( $logo_link ) .'" style="text-decoration:none"><img src="'.esc_url( $logo_url[0] ).'" class="graphic-logo" alt="logo"></a></div>';
        }
        break;

    case 'text':
        $text_logo = stripslashes(get_option('niteoCS_text_logo', get_bloginfo( 'name', 'display' )));

        $html = '<div class="logo-wrapper text text-logo-wrapper' . esc_attr( $class ) . '"><a href="'. esc_url( $logo_link) .'" style="text-decoration:none;color:inherit"><h1 class="text-logo">'.esc_html( $text_logo ).'</h1></a></div>';
        break;

    case 'disabled':
    default:
        break;
} 
