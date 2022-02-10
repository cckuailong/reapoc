<?php

$access        = get_option( 'allow_wcvendors_wpuf_post', 'no' );
$selected_form = get_option( 'wcvendors_wpuf_allowed_post_form', '' );

if ( $access != 'yes' ) {
    echo esc_attr( __( 'You are not allowed to submit post. Please contact admin', 'wp-user-frontend' ) );
} else {
    echo do_shortcode( '[wpuf_form id="' . $selected_form . '"]' );
}
