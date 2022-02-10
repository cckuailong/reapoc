<?php
/**
 * The template for displaying WC Marketplace vendor submit post content
 *
 * Override guideline: https://wedevs.com/docs/wp-user-frontend-pro/tutorials/how-to-override-dashboard-templates/
 *
 * @since   3.0
 */
global $WCMp;
?>
<div class="col-md-12 wpuf-wcmp-integration-content">
    <div class="panel panel-default">
        <div class="panel-body">
            <?php
                $action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';

                if ( $action == 'new-post' ) {
                    require_once WPUF_ROOT . '/templates/wc-marketplace/new-post.php';
                } elseif ( $action == 'edit-post' ) {
                    require_once WPUF_ROOT . '/templates/wc-marketplace/edit-post.php';
                } else {
                    require_once WPUF_ROOT . '/templates/wc-marketplace/post-listing.php';
                }
            ?>
        </div>
    </div>
</div>