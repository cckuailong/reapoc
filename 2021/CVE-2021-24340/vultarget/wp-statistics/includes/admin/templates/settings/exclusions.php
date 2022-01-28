<table class="form-table">
    <tbody>

    <tr valign="top">
        <th scope="row" colspan="2"><h3><?php _e('Exclusions', 'wp-statistics'); ?></h3></th>
    </tr>

    <tr valign="top">
        <th scope="row"><label for="wps-exclusions"><?php _e('Record Exclusions:', 'wp-statistics'); ?></label>
        </th>
        <td>
            <input id="wps-exclusions" type="checkbox" value="1" name="wps_record_exclusions" <?php echo WP_STATISTICS\Option::get('record_exclusions') == true ? "checked='checked'" : ''; ?>><label for="wps-exclusions"><?php _e('Enable', 'wp-statistics'); ?></label>
            <p class="description"><?php echo __('This option will record all the excluded hits in a separate table with the reasons for excluding (but no other information).', 'wp-statistics') . ' ' . __('It generates a lot of data, not only actual user visits but also shows the total number of your site’s hits.', 'wp-statistics'); ?></p>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row" colspan="2"><h3><?php _e('Exclude User Roles', 'wp-statistics'); ?></h3></th>
    </tr>
    <?php
    $role_option_list = '';
    foreach (\WP_STATISTICS\User::get_role_list() as $role) {
        $store_name       = 'exclude_' . str_replace(" ", "_", strtolower($role));
        $option_name      = 'wps_' . $store_name;
        $role_option_list .= $option_name . ',';

        $translated_role_name = translate_user_role($role);
        ?>

        <tr valign="top">
            <th scope="row"><label for="<?php echo $option_name; ?>"><?php echo $translated_role_name; ?>:</label>
            </th>
            <td>
                <input id="<?php echo $option_name; ?>" type="checkbox" value="1" name="<?php echo $option_name; ?>" <?php echo WP_STATISTICS\Option::get($store_name) == true ? "checked='checked'" : ''; ?>><label for="<?php echo $option_name; ?>"><?php _e('Exclude', 'wp-statistics'); ?></label>
                <p class="description"><?php echo sprintf(__('Exclude %s role from data collection.', 'wp-statistics'), $translated_role_name); ?></p>
            </td>
        </tr>
    <?php } ?>

    <tr valign="top">
        <th scope="row" colspan="2"><h3><?php _e('IP/Robot Exclusions', 'wp-statistics'); ?></h3></th>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Robot List:', 'wp-statistics'); ?></th>
        <td>
            <textarea name="wps_robotlist" class="code" dir="ltr" rows="10" cols="60" id="wps_robotlist"><?php
                $robotlist = WP_STATISTICS\Option::get('robotlist');
                if ($robotlist == '') {
                    $robotlist = WP_STATISTICS\Helper::get_robots_list();
                    update_option('wps_robotlist', $robotlist);
                }
                echo htmlentities($robotlist, ENT_QUOTES);
                ?>
            </textarea>
            <p class="description"><?php echo __('It is a list of words (one per line) to match against to detect robots. Entries must be at least four characters long, or they will be ignored.', 'wp-statistics'); ?></p>
            <a onclick="var wps_robotlist = getElementById('wps_robotlist'); wps_robotlist.value = '<?php echo \WP_STATISTICS\Helper::get_robots_list(); ?>';" class="button"><?php _e('Reset to Default', 'wp-statistics'); ?></a>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row">
            <label for="force_robot_update"><?php _e('Force Robot List Update After Upgrades:', 'wp-statistics'); ?></label>
        </th>
        <td>
            <input id="force_robot_update" type="checkbox" value="1" name="wps_force_robot_update" <?php echo WP_STATISTICS\Option::get('force_robot_update') == true ? "checked='checked'" : ''; ?>><label for="force_robot_update"><?php _e('Enable', 'wp-statistics'); ?></label>
            <p class="description"><?php echo sprintf(__('Force the robot list to reset itself to the default after WP-Statistics updated. Note that any custom robots added to the list will be lost if this option is enabled.', 'wp-statistics'), $role); ?></p>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row">
            <label for="wps_robot_threshold"><?php _e('Robot Visit Threshold:', 'wp-statistics'); ?></label>
        </th>
        <td>
            <input id="wps_robot_threshold" type="text" size="5" name="wps_robot_threshold" value="<?php echo WP_STATISTICS\Option::get('robot_threshold'); ?>">
            <p class="description"><?php echo __('Treat visitors with more than this number of visits per day as robots. 0 = disabled.', 'wp-statistics'); ?></p>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Excluded IP Address List:', 'wp-statistics'); ?></th>
        <td>
            <textarea id="wps_exclude_ip" name="wps_exclude_ip" rows="5" cols="60" class="code" dir="ltr"><?php echo htmlentities(WP_STATISTICS\Option::get('exclude_ip'), ENT_QUOTES); ?></textarea>
            <p class="description"><?php echo __('You can add a list of IP addresses and subnet masks (one per line) to exclude from the statistics collection.', 'wp-statistics'); ?></p>
            <p class="description"><?php echo __('For IPv4 addresses, both 192.168.0.0/24 and 192.168.0.0/255.255.255.0 formats are acceptable. To specify an IP address, use a subnet value of 32 or 255.255.255.255.', 'wp-statistics'); ?></p>
            <p class="description"><?php echo __('For IPv6 addresses, use the fc00::/7 format.', 'wp-statistics'); ?></p>
            <?php
            foreach (\WP_STATISTICS\IP::$private_SubNets as $ip) {
                ?>
                <a onclick="var wps_exclude_ip = getElementById('wps_exclude_ip'); if( wps_exclude_ip != null ) { wps_exclude_ip.value = jQuery.trim( wps_exclude_ip.value + '\n<?php echo $ip; ?>' ); }" class="button"><?php _e('Add', 'wp-statistics'); ?><?php echo $ip; ?></a>
                <?php
            }
            ?>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Use Honey Pot:', 'wp-statistics'); ?></th>
        <td>
            <input id="use_honeypot" type="checkbox" value="1" name="wps_use_honeypot" <?php echo WP_STATISTICS\Option::get('use_honeypot') == true ? "checked='checked'" : ''; ?>><label for="wps_use_honeypot"><?php _e('Enable', 'wp-statistics'); ?></label>
            <p class="description"><?php echo __('Enable this option for identifying robots by the Honey Pot page.', 'wp-statistics'); ?></p>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><label for="honeypot_postid"><?php _e('Honey Pot Post ID', 'wp-statistics'); ?></label>
        </th>
        <td>
            <input id="honeypot_postid" type="text" value="<?php echo htmlentities(WP_STATISTICS\Option::get('honeypot_postid'), ENT_QUOTES); ?>" size="5" name="wps_honeypot_postid">
            <p class="description"><?php echo __('Write a post ID for the Honey Pot page.Create a new Honey Pot page', 'wp-statistics'); ?></p>
            <input id="wps_create_honeypot" type="checkbox" value="1" name="wps_create_honeypot"><label for="wps_create_honeypot"><?php _e('Create a new Honey Pot page', 'wp-statistics'); ?></label>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row">
            <label for="corrupt_browser_info"><?php _e('Treat Corrupt Browser Info as a Bot:', 'wp-statistics'); ?></label>
        </th>
        <td>
            <input id="corrupt_browser_info" type="checkbox" value="1" name="wps_corrupt_browser_info" <?php echo WP_STATISTICS\Option::get('corrupt_browser_info') == true ? "checked='checked'" : ''; ?>><label for="wps_corrupt_browser_info"><?php _e('Enable', 'wp-statistics'); ?></label>
            <p class="description"><?php echo __('Treat any visitor with corrupt browser info (missing IP address or empty user agent string) as a robot.', 'wp-statistics'); ?></p>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row" colspan="2"><h3><?php _e('GeoIP Exclusions', 'wp-statistics'); ?></h3></th>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Excluded Countries:', 'wp-statistics'); ?></th>
        <td>
            <textarea id="wps_excluded_countries" name="wps_excluded_countries" rows="5" cols="50" class="code" dir="ltr"><?php echo htmlentities(WP_STATISTICS\Option::get('excluded_countries'), ENT_QUOTES); ?></textarea>
            <p class="description"><?php echo __('Add the country codes (one per line, two letters each) to exclude them from statistics collection.', 'wp-statistics') . ' ' . __('Use "000" (three zeros) to exclude unknown countries.', 'wp-statistics') . ' ' . sprintf(__('(%1$sISO 3166 Country Codes%2$s)', 'wp-statistics'), '<a href="' . esc_url('https://dev.maxmind.com/geoip/legacy/codes/iso3166/') . '" target="_blank">', '</a>'); ?></p>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Included Countries:', 'wp-statistics'); ?></th>
        <td>
            <textarea id="wps_included_countries" name="wps_included_countries" rows="5" cols="50" class="code" dir="ltr"><?php echo htmlentities(WP_STATISTICS\Option::get('included_countries'), ENT_QUOTES); ?></textarea>
            <p class="description"><?php echo __('Add the country codes (one per line, two letters each) to include them in statistics collection.', 'wp-statistics') . ' ' . __('Use "000" (three zeros) to exclude unknown countries.', 'wp-statistics') . ' ' . sprintf(__('(%1$sISO 3166 Country Codes%2$s)', 'wp-statistics'), '<a href="' . esc_url('https://dev.maxmind.com/geoip/legacy/codes/iso3166/') . '" target="_blank">', '</a>'); ?></p>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row" colspan="2"><h3><?php _e('Host Exclusions', 'wp-statistics'); ?></h3></th>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Excluded Hosts:', 'wp-statistics'); ?></th>
        <td>
            <textarea id="wps_excluded_hosts" name="wps_excluded_hosts" rows="5" cols="80" class="code" dir="ltr"><?php echo htmlentities(WP_STATISTICS\Option::get('excluded_hosts'), ENT_QUOTES); ?></textarea>
            <p class="description"><?php echo __('You can add a list of fully qualified host names (i.e. server.example.com, one per line) to exclude from statistics collection.', 'wp-statistics'); ?></p><br>
            <p class="description"><?php echo __('Note: This option will NOT perform a reverse DNS lookup on each page load but instead cache the provided hostnames’ IP address for one hour. If you exclude dynamically assigned hosts, you may find some overlap when the host changes its IP address and when the cache is updated, resulting in some hits recorded.', 'wp-statistics'); ?></p>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row" colspan="2"><h3><?php _e('Site URL Exclusions', 'wp-statistics'); ?></h3></th>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Excluded Login Page:', 'wp-statistics'); ?></th>
        <td>
            <input id="wps-exclude-loginpage" type="checkbox" value="1" name="wps_exclude_loginpage" <?php echo WP_STATISTICS\Option::get('exclude_loginpage') == true ? "checked='checked'" : ''; ?>><label for="wps-exclude-loginpage"><?php _e('Exclude', 'wp-statistics'); ?></label>
            <p class="description"><?php _e('Exclude the login page for registering as a hit.', 'wp-statistics'); ?></p>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e('Excluded RSS Feeds:', 'wp-statistics'); ?></th>
        <td>
            <input id="wps-exclude-feeds" type="checkbox" value="1" name="wps_exclude_feeds" <?php echo WP_STATISTICS\Option::get('exclude_feeds') == true ? "checked='checked'" : ''; ?>><label for="wps-exclude-feeds"><?php _e('Exclude', 'wp-statistics'); ?></label>
            <p class="description"><?php _e('Exclude the RSS feeds for registering as a hit.', 'wp-statistics'); ?></p>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e('Excluded 404 Pages:', 'wp-statistics'); ?></th>
        <td>
            <input id="wps-exclude-404s" type="checkbox" value="1" name="wps_exclude_404s" <?php echo WP_STATISTICS\Option::get('exclude_404s') == true ? "checked='checked'" : ''; ?>><label for="wps-exclude-404s"><?php _e('Exclude', 'wp-statistics'); ?></label>
            <p class="description"><?php _e('Exclude any URL that returns a "404 - Not Found" message.', 'wp-statistics'); ?></p>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e('Excluded URLs:', 'wp-statistics'); ?></th>
        <td>
            <textarea id="wps_excluded_urls" name="wps_excluded_urls" rows="5" cols="80" class="code" dir="ltr"><?php echo htmlentities(WP_STATISTICS\Option::get('excluded_urls'), ENT_QUOTES); ?></textarea>
            <p class="description"><?php echo __('You can add a list of local URLs (i.e.  /wordpress/about, one per line) to exclude from statistics collection.', 'wp-statistics'); ?></p><br>
            <p class="description"><?php echo __('Note: This option will NOT handle URL parameters (anything after the ?), only to the script name. Entries less than two characters will be ignored.', 'wp-statistics'); ?></p>
        </td>
    </tr>

    </tbody>
</table>

<?php submit_button(__('Update', 'wp-statistics'), 'primary', 'submit');