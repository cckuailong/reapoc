<?php
/**
 * The template for displaying WC Vendors vendor submit post content
 *
 * Override guideline: https://wedevs.com/docs/wp-user-frontend-pro/tutorials/how-to-override-dashboard-templates/
 *
 * @since   3.0
 */
?>
<div class="wpuf-wc-vendors-submit-post-page">
    <?php
        $action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';

        if ( $action == 'new-post' ) {
            require_once WPUF_ROOT . '/templates/wc-vendors/new-post.php';
        } elseif ( $action == 'edit-post' ) {
            require_once WPUF_ROOT . '/templates/wc-vendors/edit-post.php';
        } else {
            require_once WPUF_ROOT . '/templates/wc-vendors/post-listing.php';
        }
    ?>
</div>