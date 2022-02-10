<?php

function wpuf_upgrade_2_9_2_migration() {
    $args = [
        'post_type'     => 'wpuf_profile',
        'post_status'   => 'publish',
    ];

    $profile_forms = get_posts( $args );

    if ( empty( $profile_forms ) ) {
        return;
    }

    foreach ( $profile_forms as $form ) {
        $form_id = $form->ID;

        if ( !empty( $form_id ) ) {
            $form_settings      = wpuf_get_form_settings( $form_id );
            $email_verification = isset( $form_settings['enable_email_verification'] ) ? $form_settings['enable_email_verification'] : 'no';
            $user_status        = isset( $form_settings['wpuf_user_status'] ) ? $form_settings['wpuf_user_status'] : 'approved';

            $form_settings['user_notification']  = 'on';
            $form_settings['admin_notification'] = 'on';

            if ( $email_verification == 'yes' ) {
                $form_settings['notification_type'] = 'email_verification';
            } else {
                $form_settings['notification_type'] = 'welcome_email';
            }

            $confirmation_mail_subject    = wpuf_get_option( 'confirmation_mail_subject', 'wpuf_mails' );
            $confirmation_mail_body       = wpuf_get_option( 'confirmation_mail_body', 'wpuf_mails' );
            $admin_email_subject          = wpuf_get_option( 'new_user_email_subject_admin', 'wpuf_mails' );
            $admin_email_body             = wpuf_get_option( 'new_user_email_body_admin', 'wpuf_mails' );
            $welcome_email_subject        = wpuf_get_option( 'approved_user_email_subject', 'wpuf_mails' );
            $welcome_email_body           = wpuf_get_option( 'approved_user_email_body', 'wpuf_mails' );

            if ( $user_status == 'pending' ) {
                $welcome_email_subject  = wpuf_get_option( 'pending_user_email_subject', 'wpuf_mails' );
                $welcome_email_body     = wpuf_get_option( 'pending_user_email_body', 'wpuf_mails' );
            } elseif ( $user_status == 'denied' ) {
                $welcome_email_subject  = wpuf_get_option( 'denied_user_email_subject', 'wpuf_mails' );
                $welcome_email_body     = wpuf_get_option( 'denied_user_email_body', 'wpuf_mails' );
            }

            $form_settings['notification']['verification_subject']  = $confirmation_mail_subject;
            $form_settings['notification']['verification_body']     = $confirmation_mail_body;
            $form_settings['notification']['welcome_email_subject'] = $welcome_email_subject;
            $form_settings['notification']['welcome_email_body']    = $welcome_email_body;
            $form_settings['notification']['admin_email_subject']   = $admin_email_subject;
            $form_settings['notification']['admin_email_body']      = $admin_email_body;

            // Update form settings
            update_post_meta( $form_id, 'wpuf_form_settings', $form_settings );
        }
    }
}

wpuf_upgrade_2_9_2_migration();
