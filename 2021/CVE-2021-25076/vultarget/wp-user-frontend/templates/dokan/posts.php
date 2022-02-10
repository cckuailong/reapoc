<div class="dokan-dashboard-wrap">

    <?php

        /**
         *  dokan_dashboard_content_before hook
         *  dokan_dashboard_support_content_before
         *
         *  @hooked get_dashboard_side_navigation
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_before' );
        do_action( 'dokan_dashboard_support_content_before' );
    ?>

    <div class="dokan-dashboard-content dokan-wpuf-dashboard">
			<?php
                $action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';

                if ( $action == 'new-post' ) {
                    require_once WPUF_ROOT . '/templates/dokan/new-post.php';
                } elseif ( $action == 'edit-post' ) {
                    require_once WPUF_ROOT . '/templates/dokan/edit-post.php';
                } else {
                    require_once WPUF_ROOT . '/templates/dokan/post-listing.php';
                }
            ?>

    </div><!-- .dokan-dashboard-content -->

    <?php

        /**
         *  dokan_dashboard_content_after hook
         *  dokan_dashboard_support_content_after hook
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_after' );
        do_action( 'dokan_dashboard_support_content_after' );
    ?>

</div><!-- .dokan-dashboard-wrap -->