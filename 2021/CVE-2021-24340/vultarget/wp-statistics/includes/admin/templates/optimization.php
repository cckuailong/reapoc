<div id="poststuff">
    <div id="post-body" class="metabox-holder columns-2">
        <div class="wp-list-table widefat widefat">
            <div class="wp-statistics-container">
                <ul class="tabs">
                    <li class="tab-link current" data-tab="resources"><?php _e('Resources/Information', 'wp-statistics'); ?></li>
                    <li class="tab-link" data-tab="export"><?php _e('Export', 'wp-statistics'); ?></li>
                    <li class="tab-link" data-tab="purging"><?php _e('Purging', 'wp-statistics'); ?></li>
                    <li class="tab-link" data-tab="database"><?php _e('Database', 'wp-statistics'); ?></li>
                    <li class="tab-link" data-tab="updates"><?php _e('Updates', 'wp-statistics'); ?></li>
                    <li class="tab-link" data-tab="historical"><?php _e('Historical', 'wp-statistics'); ?></li>
                </ul>

                <div id="resources" class="tab-content current">
                    <?php include(WP_STATISTICS_DIR . 'includes/admin/templates/optimization/resources.php'); ?>
                </div>
                <div id="export" class="tab-content">
                    <?php include(WP_STATISTICS_DIR . 'includes/admin/templates/optimization/export.php'); ?>
                </div>
                <div id="purging" class="tab-content">
                    <?php include(WP_STATISTICS_DIR . 'includes/admin/templates/optimization/purging.php'); ?>
                </div>
                <div id="database" class="tab-content">
                    <?php include(WP_STATISTICS_DIR . 'includes/admin/templates/optimization/database.php'); ?>
                </div>
                <div id="updates" class="tab-content">
                    <?php include(WP_STATISTICS_DIR . 'includes/admin/templates/optimization/updates.php'); ?>
                </div>
                <div id="historical" class="tab-content">
                    <?php include(WP_STATISTICS_DIR . 'includes/admin/templates/optimization/historical.php'); ?>
                </div>
            </div><!-- container -->
        </div>

        <?php include WP_STATISTICS_DIR . "includes/admin/templates/postbox.php"; ?>
    </div>
</div>