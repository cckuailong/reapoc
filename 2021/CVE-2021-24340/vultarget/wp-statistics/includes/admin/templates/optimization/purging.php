<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery("#purge-data-submit").click(function () {

            var action = jQuery('#purge-data').val();

            if (action == 0)
                return false;

            var agree = confirm('<?php _e('Are you sure?', 'wp-statistics'); ?>');

            if (!agree)
                return false;

            jQuery("#purge-data-submit").attr("disabled", "disabled");
            jQuery("#purge-data-status").html("<img src='<?php echo plugins_url('wp-statistics'); ?>/assets/images/loading.gif'/>");
            jQuery.ajax({
                url: ajaxurl,
                type: 'post',
                data: {
                    'action': 'wp_statistics_purge_data',
                    'purge-days': action,
                    'wps_nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
                },
                datatype: 'json',
            })
                .always(function (result) {
                    jQuery("#purge-data-status").html("");
                    jQuery("#purge-data-result").html(result);
                    jQuery("#purge-data-submit").removeAttr("disabled");
                    jQuery("#wps_historical_purge").show();
                });
        });

        jQuery("#purge-visitor-hits-submit").click(function () {

            var action = jQuery('#purge-visitor-hits').val();

            if (action == 0)
                return false;

            var agree = confirm('<?php _e('Are you sure?', 'wp-statistics'); ?>');

            if (!agree)
                return false;

            jQuery("#purge-visitor-hits-submit").attr("disabled", "disabled");
            jQuery("#purge-visitor-hits-status").html("<img src='<?php echo plugins_url('wp-statistics'); ?>/assets/images/loading.gif'/>");
            jQuery.ajax({
                url: ajaxurl,
                type: 'post',
                data: {
                    'action': 'wp_statistics_purge_visitor_hits',
                    'purge-hits': action,
                    'wps_nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
                },
                datatype: 'json',
            })
                .always(function (result) {
                    jQuery("#purge-visitor-hits-status").html("");
                    jQuery("#purge-visitor-hits-result").html(result);
                    jQuery("#purge-visitor-hits-submit").removeAttr("disabled");
                });
        });

        jQuery("#empty-table-submit").click(function () {

            var action = jQuery('#empty-table').val();

            if (action == 0)
                return false;

            var agree = confirm('<?php _e('Are you sure?', 'wp-statistics'); ?>');

            if (!agree)
                return false;

            jQuery("#empty-table-submit").attr("disabled", "disabled");
            jQuery("#empty-status").html("<img src='<?php echo plugins_url('wp-statistics'); ?>/assets/images/loading.gif'/>");
            jQuery.ajax({
                url: ajaxurl,
                type: 'post',
                data: {
                    'action': 'wp_statistics_empty_table',
                    'table-name': action,
                    'wps_nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
                },
                datatype: 'json',
            })
                .always(function (result) {
                    jQuery("#empty-status").html("");
                    jQuery("#empty-result").html(result);
                    jQuery("#empty-table-submit").removeAttr("disabled");
                });
        });

        jQuery("#delete-agents-submit").click(function () {

            var action = jQuery('#delete-agent').val();

            if (action == 0)
                return false;

            var agree = confirm('<?php _e('Are you sure?', 'wp-statistics'); ?>');

            if (!agree)
                return false;

            jQuery("#delete-agents-submit").attr("disabled", "disabled");
            jQuery("#delete-agents-status").html("<img src='<?php echo plugins_url('wp-statistics'); ?>/assets/images/loading.gif'/>");
            jQuery.ajax({
                url: ajaxurl,
                type: 'post',
                data: {
                    'action': 'wp_statistics_delete_agents',
                    'agent-name': action,
                    'wps_nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
                },
                datatype: 'json',
            })
                .always(function (result) {
                    jQuery("#delete-agents-status").html("");
                    jQuery("#delete-agents-result").html(result);
                    jQuery("#delete-agents-submit").removeAttr("disabled");
                    aid = data['agent-name'].replace(/[^a-zA-Z]/g, "");
                    jQuery("#agent-" + aid + "-id").remove();
                });
        });

        jQuery("#delete-platforms-submit").click(function () {

            var action = jQuery('#delete-platform').val();

            if (action == 0)
                return false;

            var agree = confirm('<?php _e('Are you sure?', 'wp-statistics'); ?>');

            if (!agree)
                return false;

            jQuery("#delete-platforms-submit").attr("disabled", "disabled");
            jQuery("#delete-platforms-status").html("<img src='<?php echo plugins_url('wp-statistics'); ?>/assets/images/loading.gif'/>");
            jQuery.ajax({
                url: ajaxurl,
                type: 'post',
                data: {
                    'action': 'wp_statistics_delete_platforms',
                    'platform-name': action,
                    'wps_nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
                },
                datatype: 'json',
            })
                .always(function (result) {
                    jQuery("#delete-platforms-status").html("");
                    jQuery("#delete-platforms-result").html(result);
                    jQuery("#delete-platforms-submit").removeAttr("disabled");
                    pid = data['platform-name'].replace(/[^a-zA-Z]/g, "");
                    jQuery("#platform-" + pid + "-id").remove();
                });
        });

        jQuery("#delete-ip-submit").click(function () {

            var value = jQuery('#delete-ip').val();

            if (value == 0)
                return false;

            var agree = confirm('<?php _e('Are you sure?', 'wp-statistics'); ?>');

            if (!agree)
                return false;

            jQuery("#delete-ip-submit").attr("disabled", "disabled");
            jQuery("#delete-ip-status").html("<img src='<?php echo plugins_url('wp-statistics'); ?>/assets/images/loading.gif'/>");
            jQuery.ajax({
                url: ajaxurl,
                type: 'post',
                data: {
                    'action': 'wp_statistics_delete_ip',
                    'ip-address': value,
                    'wps_nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
                },
                datatype: 'json',
            })
                .always(function (result) {
                    jQuery("#delete-ip-status").html("");
                    jQuery("#delete-ip-result").html(result);
                    jQuery("#delete-ip-submit").removeAttr("disabled");
                    jQuery("#delete-ip").value('');
                });
        });
    });
</script>
<div class="wrap wps-wrap">
    <table class="form-table">
        <tbody>
        <tr valign="top">
            <th scope="row" colspan="2"><h3><?php _e('Data', 'wp-statistics'); ?></h3></th>
        </tr>

        <tr valign="top">
            <th scope="row">
                <label for="empty-table"><?php _e('Empty Table:', 'wp-statistics'); ?></label>
            </th>

            <td>
                <select dir="<?php echo (is_rtl() ? 'rtl' : 'ltr'); ?>" id="empty-table" name="empty-table">
                    <option value="0"><?php _e('Please select', 'wp-statistics'); ?></option>
                    <?php
                    foreach (WP_STATISTICS\DB::table('all', 'historical') as $tbl_key => $tbl_name) {
                        echo '<option value="' . $tbl_key . '">' . $tbl_name . '</option>';
                    }
                    ?>
                    <option value="all"><?php echo __('All', 'wp-statistics'); ?></option>
                </select>

                <p class="description"><?php _e('All data table will be lost.', 'wp-statistics'); ?></p>
                <input id="empty-table-submit" class="button button-primary" type="submit" value="<?php _e('Clear now!', 'wp-statistics'); ?>" name="empty-table-submit" Onclick="return false;"/>
                <span id="empty-status"></span>
                <div id="empty-result"></div>
            </td>
        </tr>

        <tr>
            <th scope="row">
                <label for="purge-data"><?php _e('Purge records older than:', 'wp-statistics'); ?></label>
            </th>

            <td>
                <input type="text" class="small-text code" id="purge-data" name="wps_purge_data" value="365"/>
                <label for="purge-data"><?php _e('Days', 'wp-statistics'); ?></label>

                <p class="description"><?php echo __('Delete user statistics data older than the selected number of days.', 'wp-statistics') . ' ' . __('Minimum value is 30 days.', 'wp-statistics'); ?></p>
                <input id="purge-data-submit" class="button button-primary" type="submit" value="<?php _e('Purge now!', 'wp-statistics'); ?>" name="purge-data-submit" Onclick="return false;"/>
                <span id="purge-data-status"></span>
                <div id="purge-data-result"></div>
            </td>
        </tr>

        <tr>
            <th scope="row">
                <label for="purge-visitor-hits"><?php _e('Purge visitors with more than:', 'wp-statistics'); ?></label>
            </th>

            <td>
                <input type="text" class="small-text code" id="purge-visitor-hits" name="wps_purge_visitor_hits" value="10"/>
                <label for="purge-visitor-hits"><?php _e('Hits', 'wp-statistics'); ?></label>

                <p class="description"><?php echo __('Delete user statistics data where the user has more than the defined number of hits in a day.', 'wp-statistics') . ' ' . __('This can be useful to clear up old data when your site has been hit by a bot.', 'wp-statistics') . ' ' . __('This will remove the visitor and their hits to the site, however it will not remove individual page hits as that data is not recorded on a per use basis.', 'wp-statistics') . ' ' . __('Minimum value is 10 hits.', 'wp-statistics'); ?></p>
                <input id="purge-visitor-hits-submit" class="button button-primary" type="submit" value="<?php _e('Purge now!', 'wp-statistics'); ?>" name="purge-visitor-hits-submit" Onclick="return false;"/>
                <span id="purge-visitor-hits-status"></span>
                <div id="purge-visitor-hits-result"></div>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row" colspan="2"><h3><?php _e('Delete User Agent Types', 'wp-statistics'); ?></h3></th>
        </tr>

        <tr valign="top">
            <th scope="row">
                <label for="delete-agent"><?php _e('Delete Agents:', 'wp-statistics'); ?></label>
            </th>

            <td>
                <select dir="ltr" id="delete-agent" name="delete-agent">
                    <option value="0"><?php _e('Please select', 'wp-statistics'); ?></option>
                    <?php
                    $agents = wp_statistics_ua_list();
                    foreach ($agents as $agent) {
                        $aid = preg_replace("/[^a-zA-Z]/", "", $agent);
                        echo "<option value='$agent' id='agent-" . $aid . "-id'>" . $agent . "</option>";
                    }
                    ?>
                </select>

                <p class="description"><?php _e('All visitor data will be lost for this agent type.', 'wp-statistics'); ?></p>
                <input id="delete-agents-submit" class="button button-primary" type="submit" value="<?php _e('Delete now!', 'wp-statistics'); ?>" name="delete-agents-submit" Onclick="return false;">
                <span id="delete-agents-status"></span>
                <div id="delete-agents-result"></div>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <label for="delete-platform"><?php _e('Delete Platforms:', 'wp-statistics'); ?></label>
            </th>

            <td>
                <select dir="ltr" id="delete-platform" name="delete-platform">
                    <option value="0"><?php _e('Please select', 'wp-statistics'); ?></option>
                    <?php
                    $platforms = wp_statistics_platform_list();
                    foreach ($platforms as $platform) {
                        if (!empty($platform)) {
                            $pid = preg_replace("/[^a-zA-Z]/", "", $platform);
                            echo "<option value='$platform' id='platform-" . $pid . "-id'>" . $platform . "</option>";
                        }
                    }
                    ?>
                </select>

                <p class="description"><?php _e('All visitor data will be lost for this platform type.', 'wp-statistics'); ?></p>
                <input id="delete-platforms-submit" class="button button-primary" type="submit" value="<?php _e('Delete now!', 'wp-statistics'); ?>" name="delete-platforms-submit" Onclick="return false;">
                <span id="delete-platforms-status"></span>
                <div id="delete-platforms-result"></div>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <label for="delete-ip"><?php _e('Delete IP:', 'wp-statistics'); ?></label>
            </th>

            <td>
                <input dir="ltr" id="delete-ip" type="text" name="delete-ip"/>

                <p class="description"><?php _e('All visitor data will be lost for this IP.', 'wp-statistics'); ?></p>
                <input id="delete-ip-submit" class="button button-primary" type="submit" value="<?php _e('Delete now!', 'wp-statistics'); ?>" name="delete-ip-submit" Onclick="return false;">
                <span id="delete-ip-status"></span>
                <div id="delete-ip-result"></div>
            </td>
        </tr>
        </tbody>
    </table>
</div>