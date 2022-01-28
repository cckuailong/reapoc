<div id="poststuff">
    <div id="post-body" class="metabox-holder columns-2">
        <div class="wp-list-table widefat widefat">
            <form id="wp-statistics-settings-form" method="post">
                <?php wp_nonce_field('update-options', 'wp-statistics-nonce'); ?>
                <div class="wp-statistics-container">
                    <ul class="tabs">
                        <?php if ($wps_admin) { ?>
                            <li class="tab-link current" data-tab="general-settings"><?php _e('General', 'wp-statistics'); ?></li>
                            <li class="tab-link" data-tab="visitor-ip-settings"><?php _e('Visitor IP', 'wp-statistics'); ?></li>
                            <li class="tab-link" data-tab="privacy-settings"><?php _e('Privacy', 'wp-statistics'); ?></li>
                            <li class="tab-link" data-tab="notifications-settings"><?php _e('Notifications', 'wp-statistics'); ?></li>
                            <li class="tab-link" data-tab="overview-display-settings"><?php _e('Dashboard', 'wp-statistics'); ?></li>
                            <li class="tab-link" data-tab="access-settings"><?php _e('Roles', 'wp-statistics'); ?></li>
                            <li class="tab-link" data-tab="exclusions-settings"><?php _e('Exclusions', 'wp-statistics'); ?></li>
                            <li class="tab-link" data-tab="wp-cli-settings"><?php _e('CLI', 'wp-statistics'); ?></li>
                            <li class="tab-link" data-tab="externals-settings"><?php _e('Externals', 'wp-statistics'); ?></li>
                            <li class="tab-link" data-tab="maintenance-settings"><?php _e('Maintenance', 'wp-statistics'); ?></li>
                            <li class="tab-link" data-tab="reset-settings"><?php _e('Reset', 'wp-statistics'); ?></li>
                            <li class="tab-link" data-tab="about"><?php _e('About', 'wp-statistics'); ?></li>
                        <?php } ?>
                    </ul>

                    <?php if ($wps_admin) { ?>
                        <div id="general-settings" class="tab-content current">
                            <?php include WP_STATISTICS_DIR . 'includes/admin/templates/settings/general.php'; ?>
                        </div>
                        <div id="visitor-ip-settings" class="tab-content">
                            <?php include WP_STATISTICS_DIR . 'includes/admin/templates/settings/visitor-ip.php'; ?>
                        </div>
                        <div id="privacy-settings" class="tab-content">
                            <?php include WP_STATISTICS_DIR . 'includes/admin/templates/settings/privacy.php'; ?>
                        </div>
                        <div id="notifications-settings" class="tab-content">
                            <?php include WP_STATISTICS_DIR . 'includes/admin/templates/settings/notifications.php'; ?>
                        </div>
                        <div id="overview-display-settings" class="tab-content">
                            <?php include WP_STATISTICS_DIR . 'includes/admin/templates/settings/overview-display.php'; ?>
                        </div>
                        <div id="access-settings" class="tab-content">
                            <?php include WP_STATISTICS_DIR . 'includes/admin/templates/settings/access-level.php'; ?>
                        </div>
                        <div id="exclusions-settings" class="tab-content">
                            <?php include WP_STATISTICS_DIR . 'includes/admin/templates/settings/exclusions.php'; ?>
                        </div>
                        <div id="wp-cli-settings" class="tab-content">
                            <?php include WP_STATISTICS_DIR . 'includes/admin/templates/settings/wp-cli.php'; ?>
                        </div>
                        <div id="externals-settings" class="tab-content">
                            <?php include WP_STATISTICS_DIR . 'includes/admin/templates/settings/externals.php'; ?>
                        </div>
                        <div id="maintenance-settings" class="tab-content">
                            <?php include WP_STATISTICS_DIR . 'includes/admin/templates/settings/maintenance.php'; ?>
                        </div>
                        <div id="reset-settings" class="tab-content">
                            <?php include WP_STATISTICS_DIR . 'includes/admin/templates/settings/reset.php'; ?>
                        </div>
                    <?php } ?>
                    <div id="about" class="tab-content">
                        <?php include WP_STATISTICS_DIR . 'includes/admin/templates/settings/about.php'; ?>
                    </div>
                </div><!-- container -->
            </form>
        </div>
        <?php include WP_STATISTICS_DIR . 'includes/admin/templates/postbox.php'; ?>
    </div>
</div>