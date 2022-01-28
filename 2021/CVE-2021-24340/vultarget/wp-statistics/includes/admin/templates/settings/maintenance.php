<script type="text/javascript">
    function DBMaintWarning() {
        var checkbox = jQuery('#wps_schedule_dbmaint');
        if (checkbox.attr('checked') == 'checked') {
            if (!confirm('<?php _e('This will permanently delete data from the database each day, are you sure you want to enable this option?', 'wp-statistics'); ?>'))
                checkbox.attr('checked', false);
        }
    }
</script>
<table class="form-table">
    <tbody>
    <tr valign="top">
        <th scope="row" colspan="2"><h3><?php _e('Purge Old Data Daily', 'wp-statistics'); ?></h3></th>
    </tr>

    <tr valign="top">
        <th scope="row">
            <label for="wps_schedule_dbmaint"><?php _e('Enabled:', 'wp-statistics'); ?></label>
        </th>

        <td>
            <input id="wps_schedule_dbmaint" type="checkbox" name="wps_schedule_dbmaint" <?php echo WP_STATISTICS\Option::get('schedule_dbmaint') == true ? "checked='checked'" : ''; ?> onclick='DBMaintWarning();'>
            <label for="wps_schedule_dbmaint"><?php _e('Enable', 'wp-statistics'); ?></label>
            <p class="description"><?php _e('A WP Cron job will be run daily to purge any data older than a set number of days.', 'wp-statistics'); ?></p>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row">
            <label for="wps_schedule_dbmaint_days"><?php _e('Purge Data Older Than:', 'wp-statistics'); ?></label>
        </th>

        <td>
            <input type="text" class="small-text code" id="wps_schedule_dbmaint_days" name="wps_schedule_dbmaint_days" value="<?php echo htmlentities(WP_STATISTICS\Option::get('schedule_dbmaint_days', "365"), ENT_QUOTES); ?>"/>
            <?php _e('Days', 'wp-statistics'); ?>
            <p class="description"><?php echo __('The number of days to keep statistics for.', 'wp-statistics') . ' ' . __('The minimum value is 30 days.', 'wp-statistics') . ' ' . __('Invalid values will disable the daily maintenance.', 'wp-statistics'); ?></p>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row" colspan="2"><h3><?php _e('Purge High Hit Count Visitors Daily', 'wp-statistics'); ?></h3>
        </th>
    </tr>

    <tr valign="top">
        <th scope="row">
            <label for="wps_schedule_dbmaint_visitor"><?php _e('Enabled:', 'wp-statistics'); ?></label>
        </th>

        <td>
            <input id="wps_schedule_dbmaint_visitor" type="checkbox" name="wps_schedule_dbmaint_visitor" <?php echo WP_STATISTICS\Option::get('schedule_dbmaint_visitor') == true ? "checked='checked'" : ''; ?> onclick='DBMaintWarning();'>
            <label for="wps_schedule_dbmaint_visitor"><?php _e('Enable', 'wp-statistics'); ?></label>
            <p class="description"><?php _e('A WP Cron job will be run daily to purge any users statistics data where the user has more than the defined number of hits in a day (aka they are probably a bot).', 'wp-statistics'); ?></p>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row">
            <label for="wps_schedule_dbmaint_visitor_hits"><?php _e('Purge Visitors More Than:', 'wp-statistics'); ?></label>
        </th>

        <td>
            <input type="text" class="small-text code" id="wps_schedule_dbmaint_visitor_hits" name="wps_schedule_dbmaint_visitor_hits" value="<?php echo htmlentities(WP_STATISTICS\Option::get('schedule_dbmaint_visitor_hits', '50'), ENT_QUOTES); ?>"/>
            <?php _e('Hits', 'wp-statistics'); ?>
            <p class="description"><?php echo __('The number of hits required to delete the visitor.', 'wp-statistics') . ' ' . __('Minimum value is 10 hits.', 'wp-statistics') . ' ' . __('Invalid values will disable the daily maintenance.', 'wp-statistics'); ?></p>
        </td>
    </tr>

    </tbody>
</table>

<?php submit_button(__('Update', 'wp-statistics'), 'primary', 'submit');