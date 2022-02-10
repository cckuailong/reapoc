<?php

function wpuf_upgrade_2_8_update_new_options() {
    $wpuf_general = get_option( 'wpuf_general' );
    switch ( $wpuf_general['admin_access'] ) {
        case 'manage_options':
            $roles = [ 'administrator' => 'administrator' ];
            break;

        case 'edit_others_posts':
            $roles = [ 'administrator' => 'administrator', 'editor' => 'editor' ];
            break;

        case 'publish_posts':
            $roles = [ 'administrator' => 'administrator', 'editor' => 'editor', 'author' => 'author' ];
            break;

        case 'edit_posts':
        case 'read':
        default:
            $roles = [ 'administrator' => 'administrator', 'editor' => 'editor', 'author' => 'author', 'contributor' => 'contributor' ];
            break;
    }

    $wpuf_general['show_admin_bar'] = $roles;

    update_option( 'wpuf_general', $wpuf_general );
}

function wpuf_upgrade_2_8_update_mail_options() {
    $old_mail_options = get_option( 'wpuf_guest_mails' );

    if ( !empty( $old_mail_options ) ) {
        add_option( 'wpuf_mails', $old_mail_options );
    }
}

wpuf_upgrade_2_8_update_new_options();
wpuf_upgrade_2_8_update_mail_options();
