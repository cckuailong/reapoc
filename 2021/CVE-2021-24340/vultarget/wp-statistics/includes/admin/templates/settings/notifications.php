<script type="text/javascript">
    function ToggleStatOptions() {
        jQuery('[id^="wps_stats_report_option"]').fadeToggle();
    }
</script>

<table class="form-table">
    <tbody>
    <tr valign="top">
        <th scope="row" colspan="2"><h3><?php _e('Common Report Options', 'wp-statistics'); ?></h3></th>
    </tr>

    <tr valign="top">
        <td scope="row" style="vertical-align: top;">
            <label for="email-report"><?php _e('Email Addresses:', 'wp-statistics'); ?></label>
        </td>

        <td>
            <input dir="ltr" type="text" id="email_list" name="wps_email_list" size="30" value="<?php if (WP_STATISTICS\Option::get('email_list') == '') {
                $wp_statistics_options['email_list'] = get_bloginfo('admin_email');
            }
            echo htmlentities(WP_STATISTICS\Option::get('email_list'), ENT_QUOTES); ?>"/>
            <p class="description"><?php _e('Add email addresses you want to receive reports and separate them with a comma.', 'wp-statistics'); ?></p>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row" colspan="2"><h3><?php _e('Update Reports', 'wp-statistics'); ?></h3></th>
    </tr>

    <tr valign="top">
        <td scope="row">
            <label for="geoip-report"><?php _e('GeoIP:', 'wp-statistics'); ?></label>
        </td>

        <td>
            <input id="geoip-report" type="checkbox" value="1" name="wps_geoip_report" <?php echo WP_STATISTICS\Option::get('geoip_report') == true ? "checked='checked'" : ''; ?>>
            <label for="geoip-report"><?php _e('Enable', 'wp-statistics'); ?></label>
            <p class="description"><?php _e('Enable it to send a report whenever the GeoIP database is updated.', 'wp-statistics'); ?></p>
        </td>
    </tr>

    <tr valign="top">
        <td scope="row">
            <label for="prune-report"><?php _e('Pruning:', 'wp-statistics'); ?></label>
        </td>

        <td>
            <input id="prune-report" type="checkbox" value="1" name="wps_prune_report" <?php echo WP_STATISTICS\Option::get('prune_report') == true ? "checked='checked'" : ''; ?>>
            <label for="prune-report"><?php _e('Enable', 'wp-statistics'); ?></label>
            <p class="description"><?php _e('Enable it to send a report whenever the pruning of the database is run.', 'wp-statistics'); ?></p>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row" colspan="2"><h3><?php _e('Statistical reporting', 'wp-statistics'); ?></h3></th>
    </tr>

    <tr valign="top">
        <th scope="row">
            <label for="stats-report"><?php _e('Statistical Reports:', 'wp-statistics'); ?></label>
        </th>

        <td>
            <input id="stats-report" type="checkbox" value="1" name="wps_stats_report" <?php echo WP_STATISTICS\Option::get('stats_report') == true ? "checked='checked'" : ''; ?> onClick='ToggleStatOptions();'>
            <label for="stats-report"><?php _e('Enable', 'wp-statistics'); ?></label>
            <p class="description"><?php _e('Enable this option to receive stats report via email', 'wp-statistics'); ?></p>
        </td>
    </tr>

    <?php if (WP_STATISTICS\Option::get('stats_report')) {
        $hidden = "";
    } else {
        $hidden = " style='display: none;'";
    } ?>
    <tr valign="top"<?php echo $hidden; ?> id='wps_stats_report_option'>
        <td scope="row" style="vertical-align: top;">
            <label for="time-report"><?php _e('Schedule:', 'wp-statistics'); ?></label>
        </td>

        <td>
            <select name="wps_time_report" id="time-report">
                <option value="0" <?php selected(WP_STATISTICS\Option::get('time_report'), '0'); ?>><?php _e('Please select', 'wp-statistics'); ?></option>
                <?php
                function wp_statistics_schedule_sort($a, $b)
                {
                    if ($a['interval'] == $b['interval']) {
                        return 0;
                    }
                    return ($a['interval'] < $b['interval']) ? -1 : 1;
                }

                //Get List Of Schedules Wordpress
                $schedules = wp_get_schedules();
                uasort($schedules, 'wp_statistics_schedule_sort');
                $schedules_item = array();

                foreach ($schedules as $key => $value) {
                    if (!in_array($value, $schedules_item)) {
                        echo '<option value="' . $key . '" ' . selected(WP_STATISTICS\Option::get('time_report'), $key) . '>' . $value['display'] . '</option>';
                        $schedules_item[] = $value;
                    }
                }
                ?>
            </select>
            <p class="description"><?php _e('Select how often to receive statistical report.', 'wp-statistics'); ?></p>
        </td>
    </tr>

    <tr valign="top"<?php echo $hidden; ?> id='wps_stats_report_option'>
        <td scope="row" style="vertical-align: top;">
            <label for="send-report"><?php _e('Send reports via:', 'wp-statistics'); ?></label>
        </td>

        <td>
            <select name="wps_send_report" id="send-report">
                <option value="0" <?php selected(WP_STATISTICS\Option::get('send_report'), '0'); ?>><?php _e('Please select', 'wp-statistics'); ?></option>
                <option value="mail" <?php selected(WP_STATISTICS\Option::get('send_report'), 'mail'); ?>><?php _e('Email', 'wp-statistics'); ?></option>
                <?php if (is_plugin_active('wp-sms/wp-sms.php') || is_plugin_active('wp-sms-pro/wp-sms.php')) { ?>
                    <option value="sms" <?php selected(WP_STATISTICS\Option::get('send_report'), 'sms'); ?>><?php _e('SMS', 'wp-statistics'); ?></option>
                <?php } ?>
            </select>

            <p class="description"><?php _e('Select delivery method for statistical report.', 'wp-statistics'); ?></p>
            <?php if (!is_plugin_active('wp-sms/wp-sms.php')) { ?>
                <p class="description note"><?php echo sprintf(__('Note: To send SMS text messages please install the %s plugin.', 'wp-statistics'), '<a href="http://wordpress.org/extend/plugins/wp-sms/" target="_blank">' . __('WordPress SMS', 'wp-statistics') . '</a>'); ?></p>
            <?php } ?>
        </td>
    </tr>

    <tr valign="top"<?php echo $hidden; ?> id='wps_stats_report_option'>
        <td scope="row" style="vertical-align: top;">
            <label for="content-report"><?php _e('Message body:', 'wp-statistics'); ?></label>
        </td>

        <td>
            <?php wp_editor(WP_STATISTICS\Option::get('content_report'), 'content-report', array('media_buttons' => false, 'textarea_name' => 'wps_content_report', 'textarea_rows' => 5,)); ?>
            <p class="description"><?php _e('Enter the contents of the report.', 'wp-statistics'); ?></p>

            <p class="description data">
                <?php _e('Any shortcode supported by your installation of WordPress, include all shortcodes for WP Statistics (see the documentation for a list of codes available) are supported in the body of the message. Here are some examples:', 'wp-statistics'); ?>
                <br><br>&nbsp;
                <?php _e('Online User', 'wp-statistics'); ?>:
                <code>[wpstatistics stat=usersonline]</code><br>
                <?php _e('Today\'s Visitors', 'wp-statistics'); ?>:
                <code>[wpstatistics stat=visitors time=today]</code><br>
                <?php _e('Today\'s Visits', 'wp-statistics'); ?>:
                <code>[wpstatistics stat=visits time=today]</code><br>
                <?php _e('Yesterday\'s Visitors', 'wp-statistics'); ?>:
                <code>[wpstatistics stat=visitors time=yesterday]</code><br>
                <?php _e('Yesterday\'s Visits', 'wp-statistics'); ?>:
                <code>[wpstatistics stat=visits time=yesterday]</code><br>
                <?php _e('Total Visitors', 'wp-statistics'); ?>:
                <code>[wpstatistics stat=visitors time=total]</code><br>
                <?php _e('Total Visits', 'wp-statistics'); ?>:
                <code>[wpstatistics stat=visits time=total]</code><br>
            </p>

            <h4>Looking for chart reporting? check out <a target="_blank" href="https://wp-statistics.com/product/wp-statistics-advanced-reporting/">Advanced Reporting</a>. </h4>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row" colspan="2"><h3><?php _e('Admin Notices', 'wp-statistics'); ?></h3></th>
    </tr>

    <tr valign="top">
        <td scope="row">
            <label for="admin-notices"><?php _e('All Notices:', 'wp-statistics'); ?></label>
        </td>

        <td>
            <input id="admin-notices" type="checkbox" value="1" name="wps_admin_notices" <?php echo WP_STATISTICS\Option::get('admin_notices') == true ? "checked='checked'" : ''; ?>>
            <label for="admin-notices"><?php _e('Enable', 'wp-statistics'); ?></label>
            <p class="description"><?php _e('Enable this option to show all notices and suggestions from WP-Statistics in the admin.', 'wp-statistics'); ?></p>
        </td>
    </tr>
    </tbody>
</table>

<?php submit_button(__('Update', 'wp-statistics'), 'primary', 'submit'); ?>