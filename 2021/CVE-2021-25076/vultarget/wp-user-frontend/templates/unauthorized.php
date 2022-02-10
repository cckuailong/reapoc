<?php

if ( empty( $message ) ) {
    $msg = '<div class="wpuf-message">' . sprintf( __( 'This page is restricted. Please %s to view this page.', 'wp-user-frontend' ), wp_loginout( get_permalink(), false ) ) . '</div>';
    echo wp_kses_post( apply_filters( 'wpuf_account_unauthorized', $msg ) );
} else {
    echo esc_html( $message );
}
