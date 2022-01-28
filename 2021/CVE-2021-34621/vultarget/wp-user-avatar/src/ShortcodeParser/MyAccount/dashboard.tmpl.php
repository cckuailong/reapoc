<?php

use ProfilePress\Core\ShortcodeParser\MyAccount\MyAccountTag;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$current_user = get_user_by('id', get_current_user_id());
?>

    <p>
        <?php
        printf(
        /* translators: 1: user display name 2: logout url */
            __('Hello %1$s (not %1$s? <a href="%2$s">Log out</a>)', 'wp-user-avatar'),
            '<strong>' . esc_html(ppress_var_obj($current_user, 'display_name')) . '</strong>',
            esc_url(wp_logout_url())
        );
        ?>
    </p>

    <p>
        <?php
        printf(
            __('From your account dashboard you can view your <a href="%1$s">change your password</a> and <a href="%2$s">edit your account details</a>.', 'wp-user-avatar'),
            esc_url(MyAccountTag::get_endpoint_url('change-password')),
            esc_url(MyAccountTag::get_endpoint_url('edit-profile'))
        );
        ?>
    </p>

<?php

do_action('ppress_myaccount_dashboard');
