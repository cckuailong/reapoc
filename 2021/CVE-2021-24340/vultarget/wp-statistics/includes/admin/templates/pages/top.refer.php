<ul class="subsubsub">
    <li class="all">
        <a class="current" href="<?php echo \WP_STATISTICS\Menus::admin_url('referrers'); ?>">
            <?php _e('All', 'wp-statistics'); ?>
            <span class="count">(<?php echo number_format_i18n($total); ?>)</span>
        </a>
    </li>
</ul>
<div class="postbox-container" id="wps-big-postbox">
    <div class="metabox-holder">
        <div class="meta-box-sortables">
            <div class="postbox">
                <div class="inside">
                    <?php if (count($list) < 1) { ?>
                        <div class='wps-center'><?php _e("No information is available.", "wp-statistics"); ?></div>
                    <?php } else { ?>
                        <table width="100%" class="widefat table-stats" id="top-referring">
                            <tr>
                                <td><?php _e('Rating', 'wp-statistics'); ?></td>
                                <td><?php _e('Site Url', 'wp-statistics'); ?></td>
                                <td><?php _e('Site Title', 'wp-statistics'); ?></td>
                                <td><?php _e('Server IP', 'wp-statistics'); ?></td>
                                <?php if (\WP_STATISTICS\GeoIP::active()) { ?>
                                    <td><?php _e('Country', 'wp-statistics'); ?></td>
                                <?php } ?>
                                <td><?php _e('References', 'wp-statistics'); ?></td>
                                <td></td>
                            </tr>
                            <?php foreach ($list as $item) { ?>

                                <tr>
                                    <td><?php echo number_format_i18n($item['rate']); ?></td>
                                    <td><?php echo WP_STATISTICS\Helper::show_site_icon($item['domain']) . " " . \WP_STATISTICS\Referred::get_referrer_link($item['domain'], $item['title']); ?>
                                    </td>
                                    <td><?php echo(trim($item['title']) == "" ? \WP_STATISTICS\Admin_Template::UnknownColumn() : $item['title']); ?>
                                    </td>
                                    <td><?php echo(trim($item['ip']) == "" ? \WP_STATISTICS\Admin_Template::UnknownColumn() : $item['ip']); ?></td>
                                    <?php if (\WP_STATISTICS\GeoIP::active()) { ?>
                                        <td><?php echo(trim($item['country']) == "" ? \WP_STATISTICS\Admin_Template::UnknownColumn() : "<img src='" . $item['flag'] . "' title='" . $item['country'] . "' alt='" . $item['country'] . "' class='log-tools'/>"); ?></td>
                                    <?php } ?>
                                    <td>
                                        <a class='wps-text-success' href='<?php echo $item['page_link']; ?>'><?php echo $item['number']; ?></a>
                                    </td>
                                </tr>

                            <?php } ?>
                        </table>
                    <?php } ?>
                </div>
            </div>
            <?php echo isset($pagination) ? $pagination : ''; ?>
        </div>
    </div>
</div>