<div class="postbox-container" id="wps-big-postbox">
    <div class="metabox-holder">
        <div class="meta-box-sortables">
            <div class="postbox" id="<?php echo \WP_STATISTICS\Meta_Box::getMetaBoxKey('pages-chart'); ?>">
                <button class="handlediv" type="button" aria-expanded="true">
                    <span class="screen-reader-text"><?php printf(__('Toggle panel: %s', 'wp-statistics'), __('Category Statistics Chart', 'wp-statistics')); ?></span>
                    <span class="toggle-indicator" aria-hidden="true"></span>
                </button>
                <h2 class="hndle wps-d-inline-block"><span><?php _e('Category Statistics Chart', 'wp-statistics'); ?></span></h2>
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
                <button class="handlediv" type="button" aria-expanded="true">
                    <span class="screen-reader-text"><?php printf(__('Toggle panel: %s', 'wp-statistics'), __('Category Statistics Summary', 'wp-statistics')); ?></span>
                    <span class="toggle-indicator" aria-hidden="true"></span>
                </button>
                <h2 class="hndle wps-d-inline-block"><span><?php _e('Category Statistics Summary', 'wp-statistics'); ?></span></h2>
                <div class="inside">
                    <table class="widefat table-stats wps-summary-stats" id="summary-stats">
                        <tbody>
                        <tr>
                            <th></th>
                            <th class="th-center"><?php _e('Count', 'wp-statistics'); ?></th>
                        </tr>

                        <?php
                        if (isset($number_post_in_category)) {
                            ?>
                            <tr>
                                <th><?php _e('The Number of Posts in Category:', 'wp-statistics'); ?></th>
                                <th class="th-center">
                                    <span><?php echo number_format_i18n($number_post_in_category); ?></span></th>
                            </tr>
                            <?php
                        }
                        ?>

                        <tr>
                            <th><?php _e('Chart Visits:', 'wp-statistics'); ?></th>
                            <th class="th-center"><span id="number-total-chart-visits"></span></th>
                        </tr>

                        <tr>
                            <th><?php _e('All Time Visits:', 'wp-statistics'); ?></th>
                            <th class="th-center"><span id="number-total-visits"></span></th>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (count($top_list) > 0) { ?>
    <div class="postbox-container wps-postbox-full">
        <div class="metabox-holder">
            <div class="meta-box-sortables">
                <div class="postbox">
                    <button class="handlediv" type="button" aria-expanded="true">
                        <span class="screen-reader-text"><?php printf(__('Toggle panel: %s', 'wp-statistics'), $top_title); ?></span>
                        <span class="toggle-indicator" aria-hidden="true"></span>
                    </button>
                    <h2 class="hndle wps-d-inline-block"><span><?php echo $top_title; ?></span></h2>
                    <div class="inside">
                        <table class="widefat table-stats wps-summary-stats" id="summary-stats">
                            <tbody>
                            <tr>
                                <th></th>
                                <th class="th-center"><?php _e('Count', 'wp-statistics'); ?></th>
                            </tr>
                            <?php
                            foreach ($top_list as $item) {
                                ?>
                                <tr>
                                    <th>
                                        <a href="<?php echo $item['link']; ?>" title="<?php echo $item['name']; ?>"><?php echo $item['name']; ?></a>
                                    </th>
                                    <th class="th-center">
                                        <span><?php echo number_format_i18n($item['count_visit']); ?></span></th>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>