<?php

$abdc_options             = get_option('ppress_abdc_options', []);
$disable_admin_bar        = ppress_var($abdc_options, 'disable_admin_bar', '', true);
$disable_dashboard_access = ppress_var($abdc_options, 'disable_dashboard_access', '', true);
$dashboard_redirect_url   = ppress_var($abdc_options, 'dashboard_redirect_url', '', true);

$disable_admin_bar_roles        = ppress_var($abdc_options, 'disable_admin_bar_roles', [], true);
$disable_dashboard_access_roles = ppress_var($abdc_options, 'disable_dashboard_access_roles', [], true);

?>
<style>input[type='text'], textarea, select {
        width: 600px;
    }</style>
<form method="post">
    <div class="postbox">
        <div class="postbox-header">
            <h2 class="hndle is-non-sortable">
                <span><?php _e('Admin Bar Visibility Control', 'wp-user-avatar'); ?></span></h2>
        </div>

        <div class="inside">
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="disable-admin-bar"><?php _e('Disable Admin Bar', 'wp-user-avatar'); ?></label>
                    </th>
                    <td>
                        <input id="disable_admin_bar" type="checkbox" name="ppress_abdc_options[disable_admin_bar]" value="yes" <?php checked($disable_admin_bar, 'yes') ?>>
                        <p class="description">
                            <?php _e('Check to disable admin bar.', 'wp-user-avatar'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="disable_admin_bar_roles"><?php _e('Admin Bar Control', 'wp-user-avatar'); ?></label>
                    </th>
                    <td>
                        <?php foreach (ppress_get_editable_roles() as $role_key => $data) :
                            ?>
                            <label>
                                <input id="admin-bar-<?php echo $role_key; ?>" type="checkbox" name="ppress_abdc_options[disable_admin_bar_roles][]" value="<?php echo $role_key; ?>" <?php checked(in_array($role_key, $disable_admin_bar_roles)); ?>>
                                <?php echo $data['name']; ?></label><br/>
                        <?php endforeach; ?>
                        <p class="description">
                            <?php _e('Select the user roles that the admin bar will be disabled for. It will be disabled for everyone except admins if none is checked.', 'wp-user-avatar'); ?>
                        </p>
                    </td>
                </tr>
            </table>
            <p>
                <?php wp_nonce_field('ppress_abc_settings_nonce'); ?>
                <input class="button-primary" type="submit" name="settings_submit" value="<?php _e('Save Changes', 'wp-user-avatar'); ?>">
            </p>
        </div>
    </div>

    <div class="postbox">

        <div class="postbox-header">
            <h2 class="hndle is-non-sortable">
                <span><?php _e('Dashboard Access Control', 'wp-user-avatar'); ?></span></h2>
        </div>

        <div class="inside">
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="disable_dashboard_access"><?php _e('Disable Dashboard Access', 'wp-user-avatar'); ?></label>
                    </th>
                    <td>
                        <input id="disable_dashboard_access" type="checkbox" name="ppress_abdc_options[disable_dashboard_access]" value="yes" <?php checked($disable_dashboard_access, 'yes') ?>>
                        <p class="description">
                            <?php _e('Check to disable dashboard access for everyone.', 'wp-user-avatar'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="disable_dashboard_access_roles"><?php _e('Dashboard Access Control', 'wp-user-avatar'); ?></label>
                    </th>
                    <td>
                        <?php foreach (ppress_get_editable_roles() as $role_key => $data) :
                            ?>
                            <label>
                                <input id="dashboard-access-<?php echo $role_key; ?>" type="checkbox" name="ppress_abdc_options[disable_dashboard_access_roles][]" value="<?php echo $role_key; ?>" <?php checked(in_array($role_key, $disable_dashboard_access_roles)); ?>>
                                <?php echo $data['name']; ?></label><br/>
                        <?php endforeach; ?>
                        <p class="description">
                            <?php _e('Select the user roles that dashboard access will be disabled for. It will be disabled for everyone except admins if none is checked.', 'wp-user-avatar'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="dashboard_redirect_url"><?php _e('Dashboard Redirect URL', 'wp-user-avatar'); ?></label>
                    </th>
                    <td>
                        <input id="dashboard_redirect_url" type="text" name="ppress_abdc_options[dashboard_redirect_url]" value="<?php echo $dashboard_redirect_url; ?>">
                        <p class="description">
                            <?php _e('Enter URL to redirect users to without dashboard access. If empty, users will be redirected to website homepage.', 'wp-user-avatar'); ?>
                        </p>
                    </td>
                </tr>
            </table>
            <p>
                <?php wp_nonce_field('ppress_abc_settings_nonce'); ?>
                <input class="button-primary" type="submit" name="settings_submit" value="<?php _e('Save Changes', 'wp-user-avatar'); ?>">
            </p>
        </div>
    </div>
</form>