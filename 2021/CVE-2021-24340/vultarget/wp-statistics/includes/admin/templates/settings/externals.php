<table class="form-table">
    <tbody>
    <tr valign="top">
        <th scope="row" colspan="2"><h3><?php _e('GeoIP Settings', 'wp-statistics'); ?></h3></th>
    </tr>

    <tr valign="top">
        <th scope="row" colspan="2">
            <?php echo sprintf(__('IP location services are provided by data created by %s.', 'wp-statistics'), '<a href="http://www.maxmind.com" target=_blank>MaxMind</a>'); ?>
        </th>
    </tr>
    <?php
    if (WP_STATISTICS\GeoIP::IsSupport()) {
        ?>
        <tr valign="top">
            <th scope="row">
                <label for="geoip-enable"><?php _e('GeoIP Collection:', 'wp-statistics'); ?></label>
            </th>

            <td>
                <input id="geoip-enable" type="checkbox" name="wps_geoip" <?php echo(WP_STATISTICS\Option::get('geoip') === 'on' ? "checked='checked'" : ''); ?>>
                <label for="geoip-enable">
                    <?php _e('Enable', 'wp-statistics'); ?>
                    <form action="" method="post" style="display: inline;">
                        <input type="hidden" name="geoip_name" value="country">
                        <?php submit_button(__("Update Database", 'wp-statistics'), "secondary", "update_geoip", false); ?>
                    </form>
                </label>

                <p class="description"><?php _e('Enable this option to get more information and location (country) from a visitor.', 'wp-statistics'); ?></p>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <label for="geoip-city"><?php _e('GeoIP City:', 'wp-statistics'); ?></label>
            </th>

            <td>
                <input id="geoip-city" type="checkbox" name="wps_geoip_city" <?php echo(WP_STATISTICS\Option::get('geoip_city') == 'on' ? "checked='checked'" : ''); ?>>
                <label for="geoip-city">
                    <?php _e('Enable', 'wp-statistics'); ?>
                    <form action="" method="post" style="display: inline;">
                        <input type="hidden" name="geoip_name" value="city">
                        <?php submit_button(__("Update Database", 'wp-statistics'), "secondary", "update_geoip", false); ?>
                    </form>
                </label>
                <p class="description"><?php _e('Enable this option to see visitors\'city name', 'wp-statistics'); ?></p>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <label for="geoip-schedule"><?php _e('Schedule Monthly Update of GeoIP DB:', 'wp-statistics'); ?></label>
            </th>

            <td>
                <input id="geoip-schedule" type="checkbox" name="wps_schedule_geoip" <?php echo WP_STATISTICS\Option::get('schedule_geoip') == true ? "checked='checked'" : ''; ?>>
                <label for="geoip-schedule"><?php _e('Enable', 'wp-statistics'); ?></label>
                <?php
                if (WP_STATISTICS\Option::get('schedule_geoip')) {
                    echo '<p class="description">' . __('Next update will be', 'wp-statistics') . ': <code>';
                    $last_update = WP_STATISTICS\Option::get('last_geoip_dl');
                    $this_month  = strtotime(__('First Tuesday of this month', 'wp-statistics'));

                    if ($last_update > $this_month) {
                        $next_update = strtotime(__('First Tuesday of next month', 'wp-statistics')) + (86400 * 2);
                    } else {
                        $next_update = $this_month + (86400 * 2);
                    }

                    $next_schedule = wp_next_scheduled('wp_statistics_geoip_hook');
                    if ($next_schedule) {
                        echo \WP_STATISTICS\TimeZone::getLocalDate(get_option('date_format'), $next_update) .
                            ' @ ' .
                            \WP_STATISTICS\TimeZone::getLocalDate(get_option('time_format'), $next_schedule);
                    } else {
                        echo \WP_STATISTICS\TimeZone::getLocalDate(get_option('date_format'), $next_update) .
                            ' @ ' .
                            \WP_STATISTICS\TimeZone::getLocalDate(get_option('time_format'), time());
                    }

                    echo '</code></p>';
                }
                ?>
                <p class="description"><?php _e('Download of the GeoIP database will be scheduled for 2 days after the first Tuesday of the month.', 'wp-statistics'); ?></p>
                <p class="description"><?php _e('This option will also download the database if the local filesize is less than 1k (which usually means the stub that comes with the plugin is still in place).', 'wp-statistics'); ?></p>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <label for="geoip-schedule"><?php _e('Populate Missing GeoIP After Updating GeoIP DB:', 'wp-statistics'); ?></label>
            </th>

            <td>
                <input id="geoip-auto-pop" type="checkbox" name="wps_auto_pop" <?php echo WP_STATISTICS\Option::get('auto_pop') == true ? "checked='checked'" : ''; ?>>
                <label for="geoip-auto-pop"><?php _e('Enable', 'wp-statistics'); ?></label>
                <p class="description"><?php _e('Enable this option to update any missing GeoIP data after downloading a new database.', 'wp-statistics'); ?></p>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <label for="geoip-schedule"><?php _e('Country Code for Private IP Addresses:', 'wp-statistics'); ?></label>
            </th>

            <td>
                <input type="text" size="3" id="geoip-private-country-code" name="wps_private_country_code" value="<?php echo htmlentities(WP_STATISTICS\Option::get('private_country_code', \WP_STATISTICS\GeoIP::$private_country), ENT_QUOTES); ?>">
                <p class="description"><?php echo __('The international standard two letter country code (ie. US = United States, CA = Canada, etc.) for private (non-routable) IP addresses (ie. 10.0.0.1, 192.158.1.1, 127.0.0.1, etc.).', 'wp-statistics') . ' ' . __('Use "000" (three zeros) to use "Unknown" as the country code.', 'wp-statistics'); ?></p>
            </td>
        </tr>
        <?php
    } else {
        ?>
        <tr valign="top">
            <th scope="row" colspan="2">
                <?php
                echo __('GeoIP collection is disabled due to the following reasons:', 'wp-statistics') . '<br><br>';

                if (!function_exists('curl_init')) {
                    echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;* ';
                    _e('GeoIP collection requires the cURL PHP extension and it is not loaded on your version of PHP!', 'wp-statistics');
                    echo '<br>';
                }

                if (!function_exists('bcadd')) {
                    echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;* ';
                    _e('GeoIP collection requires the BC Math PHP extension and it is not loaded on your version of PHP!', 'wp-statistics');
                    echo '<br>';
                }

                if (ini_get('safe_mode')) {
                    echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;* ';
                    _e('PHP safe mode detected! GeoIP collection is not supported with PHP\'s safe mode enabled!', 'wp-statistics');
                    echo '<br>';
                }
                ?>
            </th>
        </tr>
        <?php
    } ?>

    <tr valign="top">
        <th scope="row" colspan="2">
            <h3><?php _e('Matomo Referrer Spam Blacklist Settings', 'wp-statistics'); ?></h3>
        </th>
    </tr>

    <tr valign="top">
        <th scope="row" colspan="2">
            <?php echo sprintf(__('Referrer spam blacklist is provided by Matomo, available from %s.', 'wp-statistics'), '<a href="https://github.com/matomo-org/referrer-spam-blacklist" target=_blank>https://github.com/matomo-org/referrer-spam-blacklist</a>'); ?>
        </th>
    </tr>

    <tr valign="top">
        <th scope="row">
            <label for="referrerspam-enable"><?php _e('Matomo Referrer Spam Blacklist Usage:', 'wp-statistics'); ?></label>
        </th>

        <td>
            <input id="referrerspam-enable" type="checkbox" name="wps_referrerspam" <?php echo WP_STATISTICS\Option::get('referrerspam') == true ? "checked='checked'" : ''; ?>>
            <label for="referrerspam-enable"><?php _e('Enable', 'wp-statistics'); ?></label>
            <p class="description"><?php _e('Enable this option to download The Matomo Referrer Spam Blacklist database and detect referrer spam.', 'wp-statistics'); ?></p>
        </td>
    </tr>

    <tr valign="top" class="referrerspam_field"<?php if (!WP_STATISTICS\Option::get('referrerspam')) {
        echo ' style="display:none;"';
    } ?>>
        <th scope="row">
            <label for="geoip-update"><?php _e('Update Matomo Referrer Spam Blacklist Info:', 'wp-statistics'); ?></label>
        </th>

        <td>
            <a href="<?php echo WP_STATISTICS\Menus::admin_url('settings', array('tab' => 'externals-settings', 'update-referrer-spam' => 'yes')) ?>" class="button"><?php _e('Update', 'wp-staitsitcs'); ?></a>
            <p class="description"><?php _e('Click button to download the update.', 'wp-statistics'); ?></p>
        </td>
    </tr>

    <tr valign="top" class="referrerspam_field"<?php if (!WP_STATISTICS\Option::get('referrerspam')) {
        echo ' style="display:none;"';
    } ?>>
        <th scope="row">
            <label for="referrerspam-schedule"><?php _e('Schedule weekly update of Matomo Referrer Spam Blacklist DB:', 'wp-statistics'); ?></label>
        </th>

        <td>
            <input id="referrerspam-schedule" type="checkbox" name="wps_schedule_referrerspam" <?php echo WP_STATISTICS\Option::get('schedule_referrerspam') == true ? "checked='checked'" : ''; ?>>
            <label for="referrerspam-schedule"><?php _e('Enable', 'wp-statistics'); ?></label>
            <?php
            if (WP_STATISTICS\Option::get('schedule_referrerspam')) {
                echo '<p class="description">' . __('Next update will be', 'wp-statistics') . ': <code>';
                $last_update = WP_STATISTICS\Option::get('schedule_referrerspam');
                if ($last_update == 0) {
                    $last_update = time();
                }
                $next_update = $last_update + (86400 * 7);

                $next_schedule = wp_next_scheduled('wp_statistics_referrerspam_hook');

                if ($next_schedule) {
                    echo date(get_option('date_format'), $next_schedule) .
                        ' @ ' .
                        date(get_option('time_format'), $next_schedule);
                } else {
                    echo date(get_option('date_format'), $next_update) .
                        ' @ ' .
                        date(get_option('time_format'), time());
                }

                echo '</code></p>';
            }
            ?>
            <p class="description"><?php _e('Download of the Matomo Referrer Spam Blacklist database will be scheduled for once a week.', 'wp-statistics'); ?></p>
        </td>
    </tr>

    </tbody>
</table>

<?php submit_button(__('Update', 'wp-statistics'), 'primary', 'submit');
