<ul class="subsubsub wp-statistics-sub-fullwidth">
    <?php
    foreach ($sub as $key => $item) {
        ?>
        <li class="all">
            <a <?php if ($item['active'] === true) { ?> class="current" <?php } ?> href="<?php echo $item['link']; ?>">
                <?php echo $item['title']; ?>
                <span class='count'>(<?php echo number_format_i18n($item['count']); ?>)</span>
            </a>
        </li>
        <?php $sub_keys = array_keys($sub);
        if (end($sub_keys) != $key) { ?> | <?php } ?><?php } ?>
</ul>

<div class="postbox-container" id="wps-big-postbox">
    <div class="metabox-holder">
        <div class="meta-box-sortables">
            <div class="postbox">
                <div class="inside">
                    <?php if (!is_array($list) || (is_array($list) and count($list) < 1)) { ?>
                        <div class='wps-center wps-m-top-20'><?php _e("No information is available.", "wp-statistics"); ?></div>
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
                                <td>
                                    <a href="<?php echo add_query_arg('order', ((isset($_GET['order']) and $_GET['order'] == "asc") ? 'desc' : 'asc')); ?>">
                                        <?php _e('Date', 'wp-statistics'); ?>
                                        <span class="dashicons dashicons-arrow-<?php echo((isset($_GET['order']) and $_GET['order'] == "asc") ? 'up' : 'down'); ?>"></span>
                                    </a>
                                </td>
                                <td><?php _e('IP', 'wp-statistics'); ?></td>
                                <td><?php _e('Platform', 'wp-statistics'); ?></td>
                                <td><?php _e('User', 'wp-statistics'); ?></td>
                                <?php
                                if (\WP_STATISTICS\Option::get('visitors_log')) {
                                    ?>
                                    <td class="tbl-page-column"><?php _e('Page', 'wp-statistics'); ?></td>
                                    <?php
                                }
                                ?>
                                <td><?php _e('Referrer', 'wp-statistics'); ?></td>
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
                                    <?php
                                    if (\WP_STATISTICS\Option::get('visitors_log')) {
                                        ?>
                                        <td style='text-align: left;' class="tbl-page-column">
                                            <span class="txt-overflow" title="<?php echo($item['page']['title'] != "" ? $item['page']['title'] : ''); ?>"><?php echo ($item['page']['link'] != '' ? '<a href="' . $item['page']['link'] . '" target="_blank" class="wps-text-danger">' : '') . ($item['page']['title'] != "" ? $item['page']['title'] : \WP_STATISTICS\Admin_Template::UnknownColumn()) . ($item['page']['link'] != '' ? '</a>' : ''); ?></span>
                                        </td>
                                        <?php
                                    }
                                    ?>
                                    <td style='text-align: left'><?php echo $item['referred']; ?></td>
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