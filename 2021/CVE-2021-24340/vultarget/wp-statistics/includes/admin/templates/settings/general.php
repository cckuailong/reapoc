<script type="text/javascript">
    function ToggleShowHitsOptions() {
        jQuery('[id^="wps_show_hits_option"]').fadeToggle();
    }
</script>

<table class="form-table">
    <tbody>
    <tr valign="top">
        <th scope="row" colspan="2"><h3><?php _e('Online Users', 'wp-statistics'); ?></h3></th>
    </tr>

    <tr valign="top">
        <th scope="row">
            <label for="useronline"><?php _e('Online User:', 'wp-statistics'); ?></label>
        </th>

        <td>
            <input id="useronline" type="checkbox" value="1" name="wps_useronline" <?php echo WP_STATISTICS\Option::get('useronline') == true ? "checked='checked'" : ''; ?>>
            <label for="useronline"><?php _e('Enable', 'wp-statistics'); ?></label>
            <p class="description"><?php _e('Enable this feature to show online users', 'wp-statistics'); ?></p>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row">
            <label for="check_online"><?php _e('Check for Online Users Every:', 'wp-statistics'); ?></label>
        </th>

        <td>
            <input type="text" class="small-text code" id="check_online" name="wps_check_online" value="<?php echo htmlentities(WP_STATISTICS\Option::get('check_online'), ENT_QUOTES); ?>"/>
            <?php _e('Seconds', 'wp-statistics'); ?>
            <p class="description"><?php echo sprintf(__('Time for checking out accurate online users on the site. Now: %s Seconds', 'wp-statistics'), WP_STATISTICS\Option::get('check_online')); ?></p>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row">
            <label for="allonline"><?php _e('Record All Users:', 'wp-statistics'); ?></label>
        </th>

        <td>
            <input id="allonline" type="checkbox" value="1" name="wps_all_online" <?php echo WP_STATISTICS\Option::get('all_online') == true ? "checked='checked'" : ''; ?>>
            <label for="allonline"><?php _e('Enable', 'wp-statistics'); ?></label>
            <p class="description"><?php _e('Enable this option to ignore the exclusion settings and record all online users (including self referrals and robots). Should only be used for troubleshooting.', 'wp-statistics'); ?></p>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row" colspan="2"><h3><?php _e('Visits', 'wp-statistics'); ?></h3></th>
    </tr>

    <tr valign="top">
        <th scope="row">
            <label for="visits"><?php _e('Visits:', 'wp-statistics'); ?></label>
        </th>

        <td>
            <input id="visits" type="checkbox" value="1" name="wps_visits" <?php echo WP_STATISTICS\Option::get('visits') == true ? "checked='checked'" : ''; ?>>
            <label for="visits"><?php _e('Enable', 'wp-statistics'); ?></label>
            <p class="description"><?php _e('Enable this option to show the number of Page Views', 'wp-statistics'); ?></p>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row" colspan="2"><h3><?php _e('Visitors', 'wp-statistics'); ?></h3></th>
    </tr>

    <tr valign="top" id="visitors_tr">
        <th scope="row">
            <label for="visitors"><?php _e('Visitors:', 'wp-statistics'); ?></label>
        </th>
        <td>
            <input id="visitors" type="checkbox" value="1" name="wps_visitors" <?php echo WP_STATISTICS\Option::get('visitors') == true ? "checked='checked'" : ''; ?>>
            <label for="visitors"><?php _e('Enable', 'wp-statistics'); ?></label>
            <p class="description"><?php _e('Enable this option to show the number of Page Hits (It is the number of unique users which have visited your site)', 'wp-statistics'); ?></p>
        </td>
    </tr>

    <tr valign="top" data-view="visitors_log_tr" <?php echo(WP_STATISTICS\Option::get('visitors') == false ? 'style="display:none;"' : '') ?>>
        <th scope="row">
            <label for="visitors_log"><?php _e('Visitors Logs:', 'wp-statistics'); ?></label>
        </th>
        <td>
            <input id="visitors_log" type="checkbox" value="1" name="wps_visitors_log" <?php echo WP_STATISTICS\Option::get('visitors_log') == true ? "checked='checked'" : ''; ?>>
            <label for="visitors_log"><?php _e('Enable', 'wp-statistics'); ?></label>
            <p class="description"><?php _e('Enable this option to receive a report of each user’s visits to the pages', 'wp-statistics'); ?></p>
        </td>
    </tr>

    <tr valign="top" data-view="visitors_log_tr" <?php echo(WP_STATISTICS\Option::get('visitors') == false ? 'style="display:none;"' : '') ?>>
        <th scope="row">
            <label for="enable_user_column"><?php _e('User Visits Column', 'wp-statistics'); ?></label>
        </th>
        <td>
            <input id="enable_user_column" type="checkbox" value="1" name="wps_enable_user_column" <?php echo WP_STATISTICS\Option::get('enable_user_column') == true ? "checked='checked'" : ''; ?>>
            <label for="enable_user_column"><?php _e('Enable', 'wp-statistics'); ?></label>
            <p class="description"><?php _e('Enable this option to show the list of user visits, link in the WordPress admin user list page.', 'wp-statistics'); ?></p>
        </td>
    </tr>

    <tr valign="top" data-view="visitors_log_tr" <?php echo(WP_STATISTICS\Option::get('visitors') == false ? 'style="display:none;"' : '') ?>>
        <th scope="row">
            <label for="coefficient"><?php _e('Coefficient per Visitor:', 'wp-statistics'); ?></label>
        </th>
        <td>
            <input type="text" class="small-text code" id="coefficient" name="wps_coefficient" value="<?php echo htmlentities(WP_STATISTICS\Option::get('coefficient'), ENT_QUOTES); ?>"/>
            <p class="description"><?php echo sprintf(__('This feature multiplies the number of each visitor. Currently %s.', 'wp-statistics'), WP_STATISTICS\Option::get('coefficient')); ?></p>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row" colspan="2"><h3><?php _e('Pages and Posts', 'wp-statistics'); ?></h3></th>
    </tr>

    <tr valign="top">
        <th scope="row">
            <label for="pages"><?php _e('Pages:', 'wp-statistics'); ?></label>
        </th>
        <td>
            <input id="pages" type="checkbox" value="1" name="wps_pages" <?php echo WP_STATISTICS\Option::get('pages') == true ? "checked='checked'" : ''; ?>>
            <label for="pages"><?php _e('Enable', 'wp-statistics'); ?></label>
            <p class="description"><?php _e('Enable this option to count the Pages visits', 'wp-statistics'); ?></p>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row">
            <label for="all_pages"><?php _e('Track All Pages:', 'wp-statistics'); ?></label>
        </th>
        <td>
            <input id="all_pages" type="checkbox" value="1" name="wps_track_all_pages" <?php echo WP_STATISTICS\Option::get('track_all_pages') == true ? "checked='checked'" : ''; ?>>
            <label for="all_pages"><?php _e('Enable', 'wp-statistics'); ?></label>
            <p class="description"><?php _e('Enable or disable this feature', 'wp-statistics'); ?></p>
            <p class="description"><?php echo sprintf(__('Track all WordPress pages, contains Category, Post Tags, Author, Custom Taxonomy, etc.', 'wp-statistics'), admin_url('options-permalink.php')); ?></p>
        </td>
    </tr>

    <?php
    if (!$disable_strip_uri_parameters) {
        ?>
        <tr valign="top">
            <th scope="row">
                <label for="strip_uri_parameters"><?php _e('Strip URL Parameters:', 'wp-statistics'); ?></label>
            </th>

            <td>
                <input id="strip_uri_parameters" type="checkbox" value="1" name="wps_strip_uri_parameters" <?php echo WP_STATISTICS\Option::get('strip_uri_parameters') == true ? "checked='checked'" : ''; ?>>
                <label for="strip_uri_parameters"><?php _e('Enable', 'wp-statistics'); ?></label>
                <p class="description"><?php _e('Enable this option to remove everything after the “?” in a URL', 'wp-statistics'); ?></p>
            </td>
        </tr>
        <?php
    }
    ?>

    <tr valign="top">
        <th scope="row">
            <label for="disable-editor"><?php _e('Hits Chart', 'wp-statistics'); ?></label>
        </th>

        <td>
            <input id="disable-editor" type="checkbox" value="1" name="wps_disable_editor" <?php echo WP_STATISTICS\Option::get('disable_editor') == true ? "checked='checked'" : ''; ?>>
            <label for="disable-editor"><?php _e('Disable', 'wp-statistics'); ?></label>
            <p class="description"><?php _e('Show Hits Chart in the WordPress page/post editor.', 'wp-statistics'); ?></p>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row">
            <label for="disable_column"><?php _e('Hits Column', 'wp-statistics'); ?></label>
        </th>
        <td>
            <input id="disable_column" type="checkbox" value="1" name="wps_disable_column" <?php echo WP_STATISTICS\Option::get('disable_column') == true ? "checked='checked'" : ''; ?>>
            <label for="disable_column"><?php _e('Disable', 'wp-statistics'); ?></label>
            <p class="description"><?php _e('Enable this option to add Hits Column', 'wp-statistics'); ?></p>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row">
            <label for="hit_post_metabox"><?php _e('Hits Metabox Chart:', 'wp-statistics'); ?></label>
        </th>

        <td>
            <input id="hit_post_metabox" type="checkbox" value="1" name="wps_hit_post_metabox" <?php echo WP_STATISTICS\Option::get('hit_post_metabox') == true ? "checked='checked'" : ''; ?>>
            <label for="hit_post_metabox"><?php _e('Enable', 'wp-statistics'); ?></label>
            <p class="description"><?php _e('Enable this option to show hits metabox chart on the edit page of all post types', 'wp-statistics'); ?></p>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row">
            <label for="show_hits"><?php _e('Hits in Single Page:', 'wp-statistics'); ?></label>
        </th>

        <td>
            <input id="show_hits" type="checkbox" value="1" name="wps_show_hits" <?php echo WP_STATISTICS\Option::get('show_hits') == true
                ? "checked='checked'" : ''; ?> onClick='ToggleShowHitsOptions();'>
            <label for="show_hits"><?php _e('Enable', 'wp-statistics'); ?></label>
            <p class="description"><?php _e('Enable it to show hits in content', 'wp-statistics'); ?></p>
        </td>
    </tr>

    <?php if (WP_STATISTICS\Option::get('show_hits')) {
        $hidden = "";
    } else {
        $hidden = " style='display: none;'";
    } ?>
    <tr valign="top"<?php echo $hidden; ?> id='wps_show_hits_option'>
        <td scope="row" style="vertical-align: top;">
            <label for="display_hits_position"><?php _e('Display position:', 'wp-statistics'); ?></label>
        </td>

        <td>
            <select name="wps_display_hits_position" id="display_hits_position">
                <option value="0" <?php selected(WP_STATISTICS\Option::get('display_hits_position'), '0'); ?>><?php _e('Please select', 'wp-statistics'); ?></option>
                <option value="before_content" <?php selected(WP_STATISTICS\Option::get('display_hits_position'), 'before_content'); ?>><?php _e('Before Content', 'wp-statistics'); ?></option>
                <option value="after_content" <?php selected(WP_STATISTICS\Option::get('display_hits_position'), 'after_content'); ?>><?php _e('After Content', 'wp-statistics'); ?></option>
            </select>
            <p class="description"><?php _e('Choose the position to show Hits.', 'wp-statistics'); ?></p>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row" colspan="2"><h3><?php _e('Cache Plugin', 'wp-statistics'); ?></h3></th>
    </tr>

    <tr valign="top">
        <th scope="row">
            <label for="use_cache_plugin"><?php _e('Use Cache Plugin:', 'wp-statistics'); ?></label>
        </th>

        <td>
            <input id="use_cache_plugin" type="checkbox" value="1" name="wps_use_cache_plugin" <?php echo WP_STATISTICS\Option::get('use_cache_plugin') == true ? "checked='checked'" : ''; ?>>
            <label for="use_cache_plugin"><?php _e('Yes', 'wp-statistics'); ?></label>
            <p class="description"><?php _e('Enable this option if you use WordPress Cache Plugins.', 'wp-statistics'); ?></p>
            <p class="description"><?php echo sprintf(__('To register WP-Statistics REST API endpoint ( %s ) , go to the <a href="%s">Permalink page</a> and update the permalink by pressing Save Changes.', 'wp-statistics'), WP_STATISTICS\RestAPI::$namespace, admin_url('options-permalink.php')); ?></p>
            <p class="description"><?php echo __('Don\'t forget to clear your enabled plugin cache.', 'wp-statistics'); ?></p>
        </td>
    </tr>


    <tr valign="top">
        <th scope="row" colspan="2"><h3><?php _e('Miscellaneous', 'wp-statistics'); ?></h3></th>
    </tr>

    <tr valign="top">
        <th scope="row">
            <label for="menu-bar"><?php _e('Show Stats in Menu Bar:', 'wp-statistics'); ?></label>
        </th>

        <td>
            <select name="wps_menu_bar" id="menu-bar">
                <option value="0" <?php selected(WP_STATISTICS\Option::get('menu_bar'), '0'); ?>><?php _e('No', 'wp-statistics'); ?></option>
                <option value="1" <?php selected(WP_STATISTICS\Option::get('menu_bar'), '1'); ?>><?php _e('Yes', 'wp-statistics'); ?></option>
            </select>
            <p class="description"><?php _e('Select Yes to show stats in the admin menu bar', 'wp-statistics'); ?></p>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row">
            <label for="hide_notices"><?php _e('Hide Admin Notices About Non-active Features:', 'wp-statistics'); ?></label>
        </th>

        <td>
            <input id="hide_notices" type="checkbox" value="1" name="wps_hide_notices" <?php echo WP_STATISTICS\Option::get('hide_notices') == true ? "checked='checked'" : ''; ?>>
            <label for="hide_notices"><?php _e('Enable', 'wp-statistics'); ?></label>
            <p class="description"><?php _e('WP-Statistics displays an alert if any of the core features are disabled. To hide these notices, enable this option.', 'wp-statistics'); ?></p>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row" colspan="2"><h3><?php _e('Search Engines', 'wp-statistics'); ?></h3></th>
    </tr>

    <tr valign="top">
        <th scope="row">
            <label for="addsearchwords"><?php _e('Add Page Title to Empty Search Words:', 'wp-statistics'); ?></label>
        </th>

        <td>
            <input id="addsearchwords" type="checkbox" value="1" name="wps_addsearchwords" <?php echo WP_STATISTICS\Option::get('addsearchwords') == true ? "checked='checked'" : ''; ?>>
            <label for="addsearchwords"><?php _e('Enable', 'wp-statistics'); ?></label>
            <p class="description"><?php _e('If a search engine is identified as the referrer but it does not include the search query this option will substitute the page title in quotes preceded by "~:" as the search query to help identify what the user may have been searching for.', 'wp-statistics'); ?></p>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row" colspan="2">
            <p class="description"><?php _e('Disabling all search engines is not allowed. Doing so will result in all search engines being active.', 'wp-statistics'); ?></p>
        </th>
    </tr>
    <?php
    $se_option_list = '';

    foreach ($selist as $se) {
        $option_name    = 'wps_disable_se_' . $se['tag'];
        $store_name     = 'disable_se_' . $se['tag'];
        $se_option_list .= $option_name . ',';
        ?>

        <tr valign="top">
            <th scope="row">
                <label for="<?php echo $option_name; ?>"><?php _e($se['name'], 'wp-statistics'); ?>:</label>
            </th>
            <td>
                <input id="<?php echo $option_name; ?>" type="checkbox" value="1" name="<?php echo $option_name; ?>" <?php echo WP_STATISTICS\Option::get($store_name) == true ? "checked='checked'" : ''; ?>><label for="<?php echo $option_name; ?>"><?php _e('Disable', 'wp-statistics'); ?></label>
                <p class="description"><?php echo sprintf(__('Disable %s from data collection and reporting.', 'wp-statistics'), $se['name']); ?></p>
            </td>
        </tr>
    <?php } ?>

    <tr valign="top">
        <th scope="row" colspan="2"><h3><?php _e('Charts', 'wp-statistics'); ?></h3></th>
    </tr>

    <tr valign="top">
        <th scope="row">
            <label for="chart-totals"><?php _e('Include Totals:', 'wp-statistics'); ?></label>
        </th>
        <td>
            <input id="chart-totals" type="checkbox" value="1" name="wps_chart_totals" <?php echo WP_STATISTICS\Option::get('chart_totals') == true ? "checked='checked'" : ''; ?>>
            <label for="chart-totals"><?php _e('Enable', 'wp-statistics'); ?></label>
            <p class="description"><?php _e('Add a total line to charts with multiple values, like the search engine referrals', 'wp-statistics'); ?></p>
        </td>
    </tr>

    </tbody>
</table>

<?php submit_button(__('Update', 'wp-statistics'), 'primary', 'submit');