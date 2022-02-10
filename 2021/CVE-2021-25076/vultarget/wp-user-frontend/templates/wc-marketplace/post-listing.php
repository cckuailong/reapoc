<header class="wpuf-dashboard-header">
    <span class="pull-right">
        <a class="btn btn-default" href="<?php echo esc_url_raw( wcmp_get_vendor_dashboard_endpoint_url( get_wcmp_vendor_settings( 'wcmp_vendor_submit_post_endpoint', 'vendor', 'general', 'submit-post' ) ) ); ?>?action=new-post" class="dokan-btn dokan-btn-theme"><?php esc_html_e( '+ Add Post', 'wp-user-frontend' ); ?></a>
    </span>
</header><!-- .dokan-dashboard-header -->

<?php echo do_shortcode( '[wpuf_dashboard]' ); ?>