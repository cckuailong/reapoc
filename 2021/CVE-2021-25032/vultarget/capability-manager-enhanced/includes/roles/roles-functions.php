<?php

/**
 * Helper method to get the main role instance
 *
 * @return PP_Capabilities_Roles
 * @access   global
 */
function pp_capabilities_roles()
{
    if (!class_exists('PP_Capabilities_Roles')) {
        require_once (dirname(__FILE__) . '/class/class-pp-roles.php');
        $roles = pp_capabilities_roles()->run();
    }

    return PP_Capabilities_Roles::instance();
}

/**
 * Roles page load
 */
function admin_roles_page_load()
{
    $plugin_name = 'capsman';
    //enqueue styles
    wp_enqueue_style($plugin_name, plugin_dir_url(CME_FILE) . 'includes/roles/css/pp-roles-admin.css', [], PUBLISHPRESS_CAPS_VERSION, 'all');

    //enqueue scripts
    wp_enqueue_script($plugin_name . '_table_edit', plugin_dir_url(CME_FILE) . 'includes/roles/js/pp-roles-admin.js', ['jquery'], PUBLISHPRESS_CAPS_VERSION, false);

    //Localize
    wp_localize_script($plugin_name . '_table_edit', 'pp_roles_i18n', ['confirm_delete' => __('Are you sure you want to delete this role?', 'capsman-enhanced')]);

    //initialize table here to be able to register default WP_List_Table screen options
    pp_capabilities_roles()->admin->get_roles_list_table();

    //Handle actions
    pp_capabilities_roles()->admin->handle_actions();

    //Add screen options
    add_screen_option('per_page', ['default' => 999]);
}


/**
 * Conditional tag to check whether the currently logged-in user has a specific role.
 *
 * @access public
 * @param string|array $roles
 * @return bool
 */
function pp_roles_current_user_has_role($roles)
{

    return is_user_logged_in() ? pp_roles_user_has_role(get_current_user_id(), $roles) : false;
}


/**
 * Conditional tag to check whether a user has a specific role.
 *
 * @access public
 * @param int $user_id
 * @param string|array $roles
 * @return bool
 */
function pp_roles_user_has_role($user_id, $roles)
{

    $user = new WP_User($user_id);

    foreach ((array)$roles as $role) {

        if (in_array($role, (array)$user->roles))
            return true;
    }

    return false;
}


?>