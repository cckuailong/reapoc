<?php

$no_form_notice = __( 'No post form assigned yet by the administrator.', 'wp-user-frontend' );
$selected_form  = wpuf_get_option( 'default_post_form', 'wpuf_frontend_posting' );

if ( empty( $selected_form ) ) {
    echo wp_kses_post( '<div class="wpuf-info">' . $no_form_notice . '</div>' );

    return;
}

echo do_shortcode( '[wpuf_form id="' . $selected_form . '"]' );
