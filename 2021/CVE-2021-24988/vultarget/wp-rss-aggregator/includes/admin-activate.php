<?php

if (wprss_can_use_twig()) {
    /* The introduction page module */
    require_once(WPRSS_INC . 'admin-intro-page.php');
    /* The update page module */
    require_once(WPRSS_INC . 'admin-update-page.php');
}

/**
 * Detects an activation and redirects the user to the correct page (intro or update).
 *
 * @since 4.12.1
 */
add_action('admin_init', function () {
    // Continue only if during an activation redirect
    if (!get_transient('_wprss_activation_redirect')) {
        return;
    }

    // Delete the redirect transient
    delete_transient('_wprss_activation_redirect');

    // Continue only if activating from a non-network site and not bulk activating plugins
    $bulkActivate = filter_input(INPUT_GET, 'activate-multi');
    if (is_network_admin() || $bulkActivate) {
        return;
    }

    if (wprss_should_do_intro_page()) {
        wp_safe_redirect(wprss_get_intro_page_url());
        return;
    }

    if (wprss_should_do_update_page()) {
        wp_safe_redirect(wprss_get_update_page_url());
        return;
    }
});
