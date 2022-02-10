<?php

/**
 * Add necessary metas for taxonomy restriction in pro version
 *
 * @return void
 */
function wpuf_upgrade_2_7_taxonomy_restriction() {
    wpuf_set_all_terms_as_allowed();
}

function wpuf_upgrade_2_7_unset_oembed_cache() {
    $post_types = get_post_types();
    unset( $post_types['oembed_cache'] );
}

function wpuf_upgrade_2_7_fallback_cost_migration() {
    $args = [
        'post_type'     => 'wpuf_forms',
        'post_status'   => 'publish',
    ];

    $allforms = get_posts( $args );

    if ( $allforms ) {
        foreach ( $allforms as $form ) {
            $old_form          = new WPUF_Form( $form->ID );
            $old_form_settings = $old_form->get_settings();

            unset( $old_form_settings['fallback_ppp_enable'] );
            unset( $old_form_settings['fallback_ppp_cost'] );

            $old_form_settings['fallback_ppp_enable'] = isset( $old_form_settings['fallback_ppp_enable'] ) ? $old_form_settings['fallback_ppp_enable'] : false;
            $old_form_settings['fallback_ppp_cost']   = isset( $old_form_settings['fallback_ppp_cost'] ) ? $old_form_settings['fallback_ppp_cost'] : 1;

            update_post_meta( $form->ID, 'wpuf_form_settings', $old_form_settings );
        }
    }
}

function wpuf_upgrade_2_7_update_new_options() {
    $wpuf_general          = get_option( 'wpuf_general' );
    $wpuf_payment          = get_option( 'wpuf_payment' );
    $wpuf_frontend_posting = [
        'edit_page_id'          => $wpuf_general['edit_page_id'],
        'default_post_owner'    => $wpuf_general['default_post_owner'],
        'cf_show_front'         => $wpuf_general['cf_show_front'],
        'insert_photo_size'     => $wpuf_general['insert_photo_size'],
        'insert_photo_type'     => $wpuf_general['insert_photo_type'],
        'image_caption'         => $wpuf_general['image_caption'],
        'default_post_form'     => $wpuf_general['default_post_form'],
    ];
    $wpuf_my_account = [
        'show_subscriptions'    => $wpuf_payment['show_subscriptions'],
    ];
    update_option( 'wpuf_frontend_posting', $wpuf_frontend_posting );
    update_option( 'wpuf_my_account', $wpuf_my_account );
}

wpuf_upgrade_2_7_taxonomy_restriction();
wpuf_upgrade_2_7_unset_oembed_cache();
wpuf_upgrade_2_7_fallback_cost_migration();
wpuf_upgrade_2_7_update_new_options();
