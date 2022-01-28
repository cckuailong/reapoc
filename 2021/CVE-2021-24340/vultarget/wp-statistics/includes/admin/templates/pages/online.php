<div class="postbox-container" id="wps-big-postbox">
    <div class="metabox-holder">
        <div class="meta-box-sortables">
            <div class="postbox">
                <div class="inside">
                    <?php if (!is_array($user_online_list)) { ?>
                        <div class='wps-center wps-m-top-20'><?php echo $user_online_list; ?></div>
                    <?php } else { ?>
                        <table width="100%" class="widefat table-stats">
                            <tr>
                                <td><?php _e('Browser', 'wp-statistics'); ?></td>
                                <?php if (WP_STATISTICS\GeoIP::active()) { ?>
                                    <td><?php _e('Country', 'wp-statistics'); ?></td>
                                <?php } ?>
                                <?php if (WP_STATISTICS\GeoIP::active('city')) { ?>
                                    <td><?php _e('City', 'wp-statistics'); ?></td>
                                <?php } ?>
                                <td><?php _e('IP', 'wp-statistics'); ?></td>
                                <td><?php _e('Online For', 'wp-statistics'); ?></td>
                                <td><?php _e('Page', 'wp-statistics'); ?></td>
                                <td><?php _e('Referrer', 'wp-statistics'); ?></td>
                                <td><?php _e('User', 'wp-statistics'); ?></td>
                                <td></td>
                            </tr>

                            <?php foreach ($user_online_list as $item) { ?>
                                <tr>
                                    <td style="text-align: left">
                                        <a href="<?php echo $item['browser']['link']; ?>" title="<?php echo $item['browser']['name']; ?>"><img src="<?php echo $item['browser']['logo']; ?>" alt="<?php echo $item['browser']['name']; ?>" class="log-tools" title="<?php echo $item['browser']['name']; ?>"/></a>
                                    </td>
                                    <?php if (WP_STATISTICS\GeoIP::active()) { ?>
                                        <td style="text-align: left">
                                            <img src="<?php echo $item['country']['flag']; ?>" alt="<?php echo $item['country']['name']; ?>" title="<?php echo $item['country']['name']; ?>" class="log-tools"/>
                                        </td>
                                    <?php } ?>
                                    <?php if (WP_STATISTICS\GeoIP::active('city')) { ?>
                                        <td><?php echo $item['city']; ?></td>
                                    <?php } ?>
                                    <td style='text-align: left'><?php echo(isset($item['hash_ip']) ? $item['hash_ip'] : "<a href='" . $item['ip']['link'] . "'>" . $item['ip']['value'] . "</a>"); ?></td>
                                    <td style='text-align: left'><span><?php echo $item['online_for']; ?></span></td>
                                    <td style='text-align: left'><?php echo ($item['page']['link'] != '' ? '<a href="' . $item['page']['link'] . '" target="_blank" class="wps-text-danger">' : '') . $item['page']['title'] . ($item['page']['link'] != '' ? '</a>' : ''); ?></td>
                                    <td style='text-align: left'><?php echo $item['referred']; ?></td>
                                    <td style='text-align: left'>
                                        <?php if (isset($item['user']) and isset($item['user']['ID']) and $item['user']['ID'] > 0) { ?><?php _e('ID', 'wp-statistics'); ?>:<a href="<?php echo get_edit_user_link($item['user']['ID']); ?>" target="_blank" class="wps-text-success"><?php echo $item['user']['ID']; ?></a><br/>
                                            <?php _e('Email', 'wp-statistics'); ?>: <?php echo $item['user']['user_email']; ?><?php } else { ?><?php echo \WP_STATISTICS\Admin_Template::UnknownColumn(); ?><?php } ?>
                                    </td>
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