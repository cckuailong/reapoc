<?php

function wpuf_upgrade_2_8_2_default_cat_migration() {
    $args = [
        'post_type'     => 'wpuf_forms',
        'post_status'   => 'publish',
    ];

    $allforms = get_posts( $args );

    if ( $allforms ) {
        foreach ( $allforms as $form ) {
            $currentform   = new WPUF_Form( $form->ID );
            $form_settings = $currentform->get_settings();

            $old_default_cat              = $form_settings['default_cat'];
            $form_settings['default_cat'] = (array) $old_default_cat;
            delete_post_meta( $form->ID, 'wpuf_form_settings' );

            update_post_meta( $form->ID, 'wpuf_form_settings', $form_settings );
        }
    }
}

wpuf_upgrade_2_8_2_default_cat_migration();
