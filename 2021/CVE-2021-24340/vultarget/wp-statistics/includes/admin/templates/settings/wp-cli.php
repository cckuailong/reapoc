<table class="form-table">
    <tbody>

    <tr valign="top">
        <th scope="row" colspan="2"><h3><?php _e('WP-CLI', 'wp-statistics'); ?></h3></th>
    </tr>

    <tr valign="top">
        <th scope="row"><label for="wps-wp_cli"><?php _e('Enable WP-CLI:', 'wp-statistics'); ?></label>
        </th>
        <td>
            <input id="wps-wp_cli" type="checkbox" value="1" name="wps_wp_cli" <?php echo WP_STATISTICS\Option::get('wp_cli') == true ? "checked='checked'" : ''; ?>>
            <label for="wps-wp_cli"><?php _e('Enable', 'wp-statistics'); ?></label>
            <p class="description"><?php echo __('This feature enables you to get WP-Statistics reporting in the WP-CLI.', 'wp-statistics'); ?></p>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row" colspan="2"><h3><?php _e('Commands', 'wp-statistics'); ?></h3></th>
    </tr>

    <tr valign="top">
        <th scope="row"><label for="wps-wp_cli_summary"><?php _e('Summary:', 'wp-statistics'); ?></label></th>
        <td>
            <input id="wps-wp_cli_summary" type="checkbox" value="1" name="wps_wp_cli_summary" <?php echo WP_STATISTICS\Option::get('wp_cli_summary') == true ? "checked='checked'" : ''; ?>>
            <label for="wps-wp_cli_summary"><?php _e('Enable', 'wp-statistics'); ?></label>
            <p class="description">
                <?php echo __('Show a summary of statistics.', 'wp-statistics'); ?><br/>
                <?php echo __('Usage: ', 'wp-statistics'); ?> <span dir="ltr"> wp statistics summary </span> </p>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><label for="wps-wp_cli_user_online"><?php _e('User Online:', 'wp-statistics'); ?></label>
        </th>
        <td>
            <input id="wps-wp_cli_user_online" type="checkbox" value="1" name="wps_wp_cli_user_online" <?php echo WP_STATISTICS\Option::get('wp_cli_user_online') == true ? "checked='checked'" : ''; ?>>
            <label for="wps-wp_cli_user_online"><?php _e('Enable', 'wp-statistics'); ?></label>
            <p class="description"><?php echo __('Show Users Online List.', 'wp-statistics'); ?><br/>
                <?php echo __('Usage: ', 'wp-statistics'); ?> <span dir="ltr"> wp statistics online --number=[integer] </span></p>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><label for="wps-wp_cli_visitors"><?php _e('Visitors:', 'wp-statistics'); ?></label>
        </th>
        <td>
            <input id="wps-wp_cli_visitors" type="checkbox" value="1" name="wps_wp_cli_visitors" <?php echo WP_STATISTICS\Option::get('wp_cli_visitors') == true ? "checked='checked'" : ''; ?>>
            <label for="wps-wp_cli_visitors"><?php _e('Enable', 'wp-statistics'); ?></label>
            <p class="description"><?php echo __('Show list of Visitors.', 'wp-statistics'); ?><br/>
                <?php echo __('Usage: ', 'wp-statistics'); ?> <span dir="ltr"> wp statistics visitors --number=[integer] </span></p>
        </td>
    </tr>

    </tbody>
</table>

<?php submit_button(__('Update', 'wp-statistics'), 'primary', 'submit');