<form method="get" style="margin-top: 15px;">
    <?php _e('Date', 'wp-statistics'); ?>:
    <input type="hidden" name="page" value="<?php echo $pageName; ?>">
    <input type="text" size="18" name="day" data-wps-date-picker="day" value="<?php echo $day; ?>" autocomplete="off" placeholder="YYYY-MM-DD">
    <input type="submit" value="<?php _e('Go', 'wp-statistics'); ?>" class="button-primary">
</form>
<div class="wp-clearfix"></div>
<div class="postbox-container" id="wps-big-postbox">
    <div class="metabox-holder">
        <div class="meta-box-sortables">
            <div class="postbox">
                <div class="inside">
                    <?php if (!is_array($list) || (is_array($list) and count($list) < 1)) { ?>
                        <div class='wps-center wps-m-top-20'><?php _e("No information is available for this day.", "wp-statistics"); ?></div>
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
                                <td><?php _e('Date', 'wp-statistics'); ?></td>
                                <td><?php _e('IP', 'wp-statistics'); ?></td>
                                <td><?php _e('Platform', 'wp-statistics'); ?></td>
                                <td><?php _e('User', 'wp-statistics'); ?></td>
                                <td><?php _e('Referrer', 'wp-statistics'); ?></td>
                                <td><?php _e('Hits', 'wp-statistics'); ?></td>
                            </tr>

                            <?php foreach ($list as $item) { ?>
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
                                    <td style='text-align: left'><span><?php echo $item['date']; ?></span></td>
                                    <td style='text-align: left'><?php echo(isset($item['hash_ip']) ? $item['hash_ip'] : "<a href='" . $item['ip']['link'] . "' class='wps-text-danger'>" . $item['ip']['value'] . "</a>"); ?></td>
                                    <td style='text-align: left'><?php echo $item['platform']; ?></td>
                                    <td style='text-align: left'>
                                        <?php if (isset($item['user']) and isset($item['user']['ID']) and $item['user']['ID'] > 0) { ?>
                                            <a href="<?php echo \WP_STATISTICS\Menus::admin_url('visitors', array('user_id' => $item['user']['ID'])); ?>" class="wps-text-success"><?php echo $item['user']['user_login']; ?></a>
                                        <?php } else { ?><?php echo \WP_STATISTICS\Admin_Template::UnknownColumn(); ?><?php } ?>
                                    </td>
                                    <td style='text-align: left'><?php echo $item['referred']; ?></td>
                                    <td style='text-align: left'><?php echo $item['hits']; ?></td>
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