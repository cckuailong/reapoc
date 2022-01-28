<div class="postbox-container" id="wps-big-postbox">
    <div class="metabox-holder">
        <div class="meta-box-sortables">
            <div class="postbox" id="<?php echo \WP_STATISTICS\Meta_Box::getMetaBoxKey('hits'); ?>">
                <div class="inside">
                    <!-- Do Js -->
                </div>
            </div>
        </div>
    </div>
</div>

<div class="postbox-container wps-postbox-full">
    <div class="metabox-holder">
        <div class="meta-box-sortables">
            <div class="postbox">
                <div class="inside">
                    <table width="auto" class="widefat table-stats wps-summary-stats" id="summary-stats">
                        <tbody>
                        <tr>
                            <th></th>
                            <?php if (\WP_STATISTICS\Option::get('visits')) { ?>
                                <th class="th-center"><?php _e('Visits', 'wp-statistics'); ?></th> <?php } ?>
                            <?php if (\WP_STATISTICS\Option::get('visitors')) { ?>
                                <th class="th-center"><?php _e('Visitors', 'wp-statistics'); ?></th> <?php } ?>
                        </tr>

                        <tr>
                            <th><?php _e('Chart Total:', 'wp-statistics'); ?></th>
                            <?php if (\WP_STATISTICS\Option::get('visits')) { ?>
                                <th class="th-center"><span id="number-total-chart-visits"></span></th> <?php } ?>
                            <?php if (\WP_STATISTICS\Option::get('visitors')) { ?>
                                <th class="th-center"><span id="number-total-chart-visitors"></span></th> <?php } ?>
                        </tr>

                        <tr>
                            <th><?php _e('All Time Total:', 'wp-statistics'); ?></th>
                            <?php if (\WP_STATISTICS\Option::get('visits')) { ?>
                                <th class="th-center"><span><?php echo number_format_i18n($total_visits); ?></span></th> <?php } ?>
                            <?php if (\WP_STATISTICS\Option::get('visitors')) { ?>
                                <th class="th-center"><span><?php echo number_format_i18n($total_visitors); ?></span></th> <?php } ?>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>