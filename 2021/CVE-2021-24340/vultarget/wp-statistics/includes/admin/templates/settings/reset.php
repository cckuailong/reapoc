<table class="form-table">
    <tbody>
    <tr valign="top">
        <th scope="row" colspan="2"><h3><?php _e('WP Statistics Reset Options', 'wp-statistics'); ?></h3></th>
    </tr>

    <tr valign="top">
        <th scope="row">
            <label for="reset-plugin"><?php _e('Reset Options:', 'wp-statistics'); ?></label>
        </th>

        <td>
            <input id="reset-plugin" type="checkbox" name="wps_reset_plugin">
            <label for="reset-plugin"><?php _e('Reset', 'wp-statistics'); ?></label>
            <p class="description"><?php _e('Reset all the options to default. Resetting the options will remove all user and global settings but will keep all other data. This action cannot be undone. Note: For multisite installs, this will reset all sites to the default settings.', 'wp-statistics'); ?></p>
        </td>
    </tr>

    </tbody>
</table>

<?php submit_button(__('Update', 'wp-statistics'), 'primary', 'submit'); ?>