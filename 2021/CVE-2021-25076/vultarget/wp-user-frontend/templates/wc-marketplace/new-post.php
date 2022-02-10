<?php

$access        = $WCMp->vendor_caps->vendor_general_settings( 'allow_wpuf_post' );
$selected_form = ( get_wcmp_vendor_settings( 'wpuf_post_forms', 'general' ) ) ? get_wcmp_vendor_settings( 'wpuf_post_forms', 'general' ) : '';

if ( $access != 'yes' ) {
    echo esc_attr( __( 'You are not allowed to submit post. Please contact admin', 'wp-user-frontend' ) );
} else {
    echo do_shortcode( '[wpuf_form id="' . $selected_form . '"]' );
}
