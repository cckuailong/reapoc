<?php

$access   	    = dokan_get_option( 'allow_wpuf_post', 'dokan_general' );
$selected_form = dokan_get_option( 'wpuf_post_forms', 'dokan_general' );

if ( $access != 'on' ) {
    echo esc_html( __( 'You are not allowed to submit post. Please contact admin', 'wp-user-frontend' ) );
} else {
    echo do_shortcode( '[wpuf_form id="' . $selected_form . '"]' );
}
