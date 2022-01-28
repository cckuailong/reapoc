<div class="postbox-container" id="wps-big-postbox">
    <div class="metabox-holder">
        <div class="meta-box-sortables">
            <div class="postbox" id="<?php echo \WP_STATISTICS\Meta_Box::getMetaBoxKey('exclusions'); ?>">
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
                    <table width="auto" class="widefat table-stats wps-summary-stats" id="summary-stats" data-table="exclusions">
                        <tbody>
                        <tr>
                            <th></th>
                            <th class="th-center"><?php _e('Exclusions', 'wp-statistics'); ?></th>
                        </tr>

                        <tr>
                            <th><?php _e('Chart Total:', 'wp-statistics'); ?></th>
                            <th class="th-center"><span id="number-total-chart-exclusions"></span></th>
                        </tr>

                        <tr>
                            <th class="wps-text-danger"><?php _e('All Time Total:', 'wp-statistics'); ?></th>
                            <th class="th-center"><span style="color: #DC3545 !important;"><?php echo number_format_i18n($total_exclusions); ?></span></th>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>