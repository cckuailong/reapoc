<ul class="subsubsub">
    <li class="all">
        <a href="<?php echo \WP_STATISTICS\Menus::admin_url('referrers'); ?>"><?php _e('All', 'wp-statistics'); ?></a>
    </li>
    |
    <li>
        <a class="current" href="<?php echo add_query_arg(array('referr' => $args['domain'])); ?>">
            <?php echo $args['domain']; ?>
            <span class="count">(<?php echo number_format_i18n($total); ?>)</span>
        </a>
    </li>
</ul>

<div class="postbox-container" id="wps-big-postbox">
    <div class="metabox-holder">
        <div class="meta-box-sortables">
            <div class="postbox">
                <button class="handlediv" type="button" aria-expanded="true">
                    <span class="screen-reader-text"><?php echo sprintf(__('Toggle panel: %s', 'wp-statistics'), $title); ?></span>
                    <span class="toggle-indicator" aria-hidden="true"></span>
                </button>
                <h2 class="hndle wps-d-inline-block"><span><?php echo $title; ?></span></h2>
                <div class="inside">
                    <?php if (count($list) < 1) { ?>
                        <div class='wps-center'><?php _e("No information is available.", "wp-statistics"); ?></div>
                    <?php } else { ?>
                        <table width="100%" class="widefat table-stats" id="top-referring">
                            <tr>
                                <td><?php _e('Link', 'wp-statistics'); ?></td>
                                <td><?php _e('IP', 'wp-statistics'); ?></td>
                                <td><?php _e('Browser', 'wp-statistics'); ?></td>
                                <?php if (\WP_STATISTICS\GeoIP::active()) { ?>
                                    <td><?php _e('Country', 'wp-statistics'); ?></td>
                                <?php } ?>
                                <td><?php _e('Date', 'wp-statistics'); ?></td>
                                <td></td>
                            </tr>
                            <?php foreach ($list as $item) { ?>
                                <tr>
                                    <td style="text-align: left">
                                        <a href="<?php echo $item['refer']; ?>" target="_blank" title="<?php echo $item['refer']; ?>"><?php echo preg_replace("(^https?://)", "", trim($item['refer'])); ?></a>
                                    </td>
                                    <td style='text-align: left;'><?php echo(isset($item['hash_ip']) ? $item['hash_ip'] : "<a href='" . $item['ip']['link'] . "' class='wps-text-success'>" . $item['ip']['value'] . "</a>"); ?></td>
                                    <td style="text-align: left">
                                        <a href="<?php echo $item['browser']['link']; ?>" title="<?php echo $item['browser']['name']; ?>"><img src="<?php echo $item['browser']['logo']; ?>" alt="<?php echo $item['browser']['name']; ?>" class="log-tools" title="<?php echo $item['browser']['name']; ?>"/></a>
                                    </td>
                                    <?php if (WP_STATISTICS\GeoIP::active()) { ?>
                                        <td style="text-align: left">
                                            <img src="<?php echo $item['country']['flag']; ?>" alt="<?php echo $item['country']['name']; ?>" title="<?php echo $item['country']['name']; ?>" class="log-tools"/>
                                        </td>
                                    <?php } ?>
                                    <td style="text-align: left"><?php echo $item['date']; ?></td>
                                    <td style='text-align: center'><?php echo(isset($item['map']) ? "<a class='wps-text-muted' href='" . $item['ip']['link'] . "'>" . WP_STATISTICS\Admin_Template::icons('dashicons-visibility') . "</a><a class='show-map wps-text-muted' href='" . $item['map'] . "' target='_blank' title='" . __('Map', 'wp-statistics') . "'>" . WP_STATISTICS\Admin_Template::icons('dashicons-location-alt') . "</a>" : ""); ?></td>
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