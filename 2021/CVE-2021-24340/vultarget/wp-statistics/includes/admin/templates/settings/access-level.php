<?php
//Get List Roles Wordpress
global $wp_roles;
?>
    <table class="form-table">
        <tbody>
        <tr valign="top">
            <th scope="row" colspan="2"><h3><?php _e('User Roles', 'wp-statistics'); ?></h3></th>
        </tr>
        <?php

        //Get List Of Capability
        foreach ($wp_roles->roles as $role) {
            $cap_list = $role['capabilities'];
            foreach ($cap_list as $key => $cap) {

                //remove level_ from List
                if (substr($key, 0, 6) != 'level_') {
                    $all_caps[$key] = 1;
                }
            }
        }

        ksort($all_caps);
        $read_cap    = WP_STATISTICS\Option::get('read_capability', 'manage_options');
        $option_list = '';
        foreach ($all_caps as $key => $cap) {
            if ($key == $read_cap) {
                $selected = " SELECTED";
            } else {
                $selected = "";
            }
            $option_list .= "<option value='{$key}'{$selected}>{$key}</option>";
        }
        ?>
        <tr valign="top">
            <th scope="row">
                <label for="wps_read_capability"><?php _e('Required User Level to View WP Statistics:', 'wp-statistics') ?></label>
            </th>
            <td>
                <select dir="ltr" id="wps_read_capability" name="wps_read_capability"><?php echo $option_list; ?></select>
            </td>
        </tr>

        <?php
        $manage_cap = WP_STATISTICS\Option::get('manage_capability', 'manage_options');
        foreach ($all_caps as $key => $cap) {
            if ($key == $manage_cap) {
                $selected = " SELECTED";
            } else {
                $selected = "";
            }
            $option_list .= "<option value='{$key}'{$selected}>{$key}</option>";
        }
        ?>
        <tr valign="top">
            <th scope="row">
                <label for="wps_manage_capability"><?php _e('Required User Level to Manage WP Statistics:', 'wp-statistics') ?></label>
            </th>
            <td>
                <select dir="ltr" id="wps_manage_capability" name="wps_manage_capability"><?php echo $option_list; ?></select>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row" colspan="2">
                <p class="description"><?php echo sprintf(__('See the %sWordPress Roles and Capabilities%s page for details on capability levels.', 'wp-statistics'), '<a target=_blank href="http://codex.wordpress.org/Roles_and_Capabilities">', '</a>'); ?></p>
                <p class="description"><?php echo __('Hint: manage_network = Super Admin Network, manage_options = Administrator, edit_others_posts = Editor, publish_posts = Author, edit_posts = Contributor, read = Everyone.', 'wp-statistics'); ?></p>
                <p class="description"><?php echo __('Each of the above cascades the rights upwards in the default WordPress configuration. For example, selecting publish_posts grants the right to Authors, Editors, Admins, and Super Admins.', 'wp-statistics'); ?></p>
                <p class="description"><?php echo sprintf(__('If you need a more robust solution to delegate access, you might want to look at %s Capability Manager Enhanced %s in the WordPress plugin directory.', 'wp-statistics'), '<a href="http://wordpress.org/plugins/capability-manager-enhanced/" target=_blank>', '</a>'); ?></p>
            </th>
        </tr>

        </tbody>
    </table>
<?php submit_button(__('Update', 'wp-statistics'), 'primary', 'submit');