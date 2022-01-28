<div class="postbox-container" id="wps-big-postbox">
    <div class="metabox-holder">
        <div class="meta-box-sortables">
            <div class="postbox" id="<?php echo \WP_STATISTICS\Meta_Box::getMetaBoxKey('top-pages-chart'); ?>">
                <button class="handlediv" type="button" aria-expanded="true">
                    <span class="screen-reader-text"><?php printf(__('Toggle panel: %s', 'wp-statistics'), __('Top 5 Pages Trends', 'wp-statistics')); ?></span>
                    <span class="toggle-indicator" aria-hidden="true"></span>
                </button>
                <h2 class="hndle wps-d-inline-block"><span><?php _e('Top 5 Pages Trends', 'wp-statistics'); ?></span>
                </h2>
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
            <div class="postbox" id="<?php echo \WP_STATISTICS\Meta_Box::getMetaBoxKey('pages'); ?>">
                <button class="handlediv" type="button" aria-expanded="true">
                    <span class="screen-reader-text"><?php printf(__('Toggle panel: %s', 'wp-statistics'), $title); ?></span>
                    <span class="toggle-indicator" aria-hidden="true"></span>
                </button>
                <h2 class="hndle wps-d-inline-block"><span><?php echo $title; ?></span></h2>
                <div class="inside">

                    <?php
                    if (empty($lists)) {
                        _e('No data to display', 'wp-statistics');
                    } else {
                        ?>
                        <table width="100%" class="widefat table-stats wps-report-table">
                            <tbody>
                            <tr>
                                <td width='10%'><?php _e('ID', 'wp-statistics'); ?></td>
                                <td width='40%'><?php _e('Title', 'wp-statistics'); ?></td>
                                <td width='40%'><?php _e('Link', 'wp-statistics'); ?></td>
                                <td width='10%'><?php _e('Visits', 'wp-statistics'); ?></td>
                            </tr>

                            <?php
                            $i = 1;
                            foreach ($lists as $li) {
                                ?>

                                <tr>
                                    <td style='text-align: left;'><?php echo $i; ?></td>
                                    <td style='text-align: left;'>
                                        <span title='<?php echo $li['title']; ?>'
                                              class='wps-cursor-default wps-text-wrap'>
                                            <?php echo $li['title']; ?>
                                        </span>
                                    </td>
                                    <td style='text-align: left;'>
                                        <a href="<?php echo $li['link']; ?>" title="<?php echo $li['title']; ?>"
                                           target="_blank"><?php echo $li['str_url']; ?>
                                        </a>
                                    </td>
                                    <td style="text-align: left">
                                        <a href="<?php echo $li['hits_page']; ?>" class="wps-text-danger">
                                            <?php echo $li['number']; ?>
                                        </a>
                                    </td>
                                </tr>

                                <?php
                                $i++;
                            }
                            ?>
                            </tbody>
                        </table>
                        <?php
                    }
                    ?>


                </div>
            </div>
            <?php echo isset($pagination) ? $pagination : ''; ?>
        </div>
    </div>
</div>