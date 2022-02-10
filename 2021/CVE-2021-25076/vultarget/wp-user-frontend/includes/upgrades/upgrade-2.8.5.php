<?php

function wpuf_upgrade_2_8_5_option_changes() {
    if ( 'yes' == wpuf_get_option( 'show_subscriptions', 'wpuf_my_account' ) ) {
        wpuf_update_option( 'show_subscriptions', 'wpuf_my_account', 'on' );
    } elseif ( 'no' == wpuf_get_option( 'show_subscriptions', 'wpuf_my_account' ) ) {
        wpuf_update_option( 'show_subscriptions', 'wpuf_my_account', 'off' );
    }

    if ( 'yes' == wpuf_get_option( 'enable_invoices', 'wpuf_payment_invoices' ) ) {
        wpuf_update_option( 'enable_invoices', 'wpuf_payment_invoices', 'on' );
    } elseif ( 'no' == wpuf_get_option( 'enable_invoices', 'wpuf_payment_invoices' ) ) {
        wpuf_update_option( 'enable_invoices', 'wpuf_payment_invoices', 'off' );
    }

    if ( 'yes' == wpuf_get_option( 'show_invoices', 'wpuf_payment_invoices' ) ) {
        wpuf_update_option( 'show_invoices', 'wpuf_payment_invoices', 'on' );
    } elseif ( 'no' == wpuf_get_option( 'show_invoices', 'wpuf_payment_invoices' ) ) {
        wpuf_update_option( 'show_invoices', 'wpuf_payment_invoices', 'off' );
    }
}
/**
 * TODO
 * Change to all user
 *
 * @return [type] [description]
 */
function wpuf_upgrade_2_8_5_change_address_meta() {
    $users = get_users( [
        'fields'   => 'ID',
        'meta_key' => 'address_fields',
    ] );

    foreach ( $users as $user_id ) {
        $address_fields = get_user_meta( $user_id, 'address_fields', true );
        update_user_meta( $user_id, 'wpuf_address_fields', $address_fields );
    }
}

function wpuf_upgrade_2_8_5_transaction_table_update() {
    global $wpdb;

    $wpdb->query( "ALTER TABLE {$wpdb->prefix}wpuf_transaction ADD COLUMN `subtotal` varchar(255) NOT NULL DEFAULT '', ADD COLUMN `tax` varchar(255) NOT NULL DEFAULT ''" );
}

wpuf_upgrade_2_8_5_transaction_table_update();
wpuf_upgrade_2_8_5_option_changes();
wpuf_upgrade_2_8_5_change_address_meta();
