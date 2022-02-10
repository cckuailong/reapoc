<div class="wrap">
    <h1><?php _e( 'Wicked Folders Settings', 'wicked-folders' ); ?></h1>
    <h2 class="nav-tab-wrapper wp-clearfix">
        <?php foreach ( $tabs as $tab ) : ?>
            <a class="nav-tab<?php if ( $active_tab == $tab['slug'] ) echo ' nav-tab-active'; ?>" href="options-general.php?page=wicked_folders_settings&tab=<?php echo esc_attr( $tab['slug'] ); ?>"><?php echo esc_html( $tab['label'] ); ?></a>
        <?php endforeach; ?>
    </h2>
    <div class="wicked-settings wicked-clearfix">
        <div class="wicked-left">
            <?php foreach ( $tabs as $tab ) : ?>
                <?php if ( $active_tab == $tab['slug'] ) : ?>
                    <?php call_user_func( $tab['callback'] ); ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <div class="wicked-right">
            <div class="wicked-logo">
                <a href="https://wickedplugins.com/?utm_source=core_settings&utm_campaign=wicked_plugins&utm_content=settings_page_logo" target="_blank"><img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ); ?>images/wicked-plugins-logo.png" alt="Wicked Plugins" width="300" height="223" /></a>
            </div>
            <div class="wicked-rate">
                <h4><?php _e( 'Thanks for using Wicked Folders!', 'wicked-folders' ); ?></h4>
                <p>Please <a href="https://wordpress.org/support/plugin/wicked-folders/reviews/#new-post" target="_blank">rate Wicked Folders<br /><span class="stars">★★★★★</span><span class="screen-reader-text">five stars</span></a><br /> to help spread the word!</p>
            </div>
            <hr />
            <?php if ( ! $is_pro_active && Wicked_Folders::is_upsell_enabled() ) : ?>
                <h4><?php _e( 'Media Library Folders!', 'wicked-folders' ); ?></h4>
                <p class="wicked-pro-screenshot"><a href="https://wickedplugins.com/plugins/wicked-folders/?utm_source=core_settings&utm_campaign=wicked_folders&utm_content=sidebar_media_library_photo" target="_blank"><img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ); ?>images/media-library-folders.jpg?r=2" alt="<?php esc_attr_e( 'Screenshot of media library using Wicked Folders Pro', 'wicked-folders' ); ?>" width="800" height="465" /></a></p>
                <p><?php _e( 'Upgrade to <a href="https://wickedplugins.com/plugins/wicked-folders/?utm_source=core_settings&utm_campaign=wicked_folders&utm_content=sidebar_text_link" target="_blank">Wicked Folders Pro</a> to organize your media library using folders!', 'wicked-folders' ); ?></p>
                <hr />
            <?php endif; ?>
            <h4><?php _e( 'About Wicked Plugins', 'wicked-folders' ); ?></h4>
            <p><?php _e( 'Wicked Plugins specializes in crafting high-quality, reliable plugins that extend WordPress in powerful ways while being simple and intuitive to use.  We’re full-time developers who know WordPress inside and out and our customer happiness engineers offer friendly support for all our products.  <a href="https://wickedplugins.com/?utm_source=core_settings&utm_campaign=wicked_plugins&utm_content=about_link" target="_blank">Visit our website</a> to learn more about us.', 'wicked-folders' ); ?>
        </div>
    </div>
</div>
