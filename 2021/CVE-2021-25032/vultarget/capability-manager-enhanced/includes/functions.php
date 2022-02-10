<?php
/*
 * PublishPress Capabilities [Free]
 * 
 * Functions available for any URL, which are not contained within a class
 * 
 * For performance and code separation, do not include functions that are only needed for wp-admin requests
 * 
 */

function pp_capabilities_is_editable_role($role_name, $args = []) {
    static $editable_roles;

    if (!function_exists('wp_roles')) {
        return false;
    }

    if (!isset($editable_roles) || !empty($args['force_refresh'])) {
        $all_roles = wp_roles()->roles;
        $editable_roles = apply_filters('editable_roles', $all_roles, $args);
    }

    return apply_filters('pp_capabilities_editable_role', isset($editable_roles[$role_name]), $role_name);
}

function _cme_act_pp_active()
{
    if (defined('PRESSPERMIT_VERSION') || (defined('PPC_VERSION') && function_exists('pp_init_cap_caster'))) {
        define('PRESSPERMIT_ACTIVE', true);
    } else {
        if (defined('SCOPER_VERSION') || (defined('PP_VERSION') && function_exists('pp_init_users_interceptor'))) {
            define('OLD_PRESSPERMIT_ACTIVE', true);
        }
    }
}

function _cme_cap_helper()
{
    global $cme_cap_helper;

    require_once(dirname(__FILE__) . '/cap-helper.php');
    $cme_cap_helper = new CME_Cap_Helper();

    add_action('registered_post_type', '_cme_post_type_late_reg', 5, 2);
    add_action('registered_taxonomy', '_cme_taxonomy_late_reg', 5, 2);
}

function _cme_post_type_late_reg($post_type, $type_obj)
{
    global $cme_cap_helper;

    if (!empty($type_obj->public) || !empty($type_obj->show_ui)) {
        $cme_cap_helper->refresh();
    }
}

function _cme_taxonomy_late_reg($taxonomy, $tx_obj)
{
    global $cme_cap_helper;

    if (!empty($tx_obj->public)) {
        $cme_cap_helper->refresh();
    }
}

function _cme_init()
{
    require_once(dirname(__FILE__) . '/filters.php');

    load_plugin_textdomain('capsman-enhanced', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

function cme_is_plugin_active($check_plugin_file)
{
    if (!$check_plugin_file)
        return false;

    $plugins = (array)get_option('active_plugins');

    foreach ($plugins as $plugin_file) {
        if (false !== strpos($plugin_file, $check_plugin_file))
            return $plugin_file;
    }
}

// if a role is marked as hidden, also default it for use by Press Permit as a Pattern Role (when PP Collaborative Editing is activated and Advanced Settings enabled)
function _cme_pp_default_pattern_role($role)
{
    if (!$pp_role_usage = get_option('pp_role_usage'))
        $pp_role_usage = array();

    if (empty($pp_role_usage[$role])) {
        $pp_role_usage[$role] = 'pattern';
        update_option('pp_role_usage', $pp_role_usage);
    }
}

// deprecated
function capsman_get_pp_option($option_basename)
{
    return pp_capabilities_get_permissions_option($option_basename);
}

function pp_capabilities_autobackup()
{
    global $wpdb;

    $roles = get_option($wpdb->prefix . 'user_roles');
    update_option('cme_backup_auto_' . current_time('Y-m-d_g-i-s_a'), $roles, false);

    $max_auto_backups = (defined('CME_AUTOBACKUPS')) ? CME_AUTOBACKUPS : 20;

    $keep_ids = $wpdb->get_col("SELECT option_id FROM $wpdb->options WHERE option_name LIKE 'cme_backup_auto_%' ORDER BY option_id DESC LIMIT $max_auto_backups");

    if (count($keep_ids) == $max_auto_backups) {
        $id_csv = implode("','", $keep_ids);

        $wpdb->query(
            "DELETE FROM $wpdb->options WHERE option_name LIKE 'cme_backup_auto_%' AND option_id NOT IN ('$id_csv')"
        );
    }
}

function pp_capabilities_get_permissions_option($option_basename)
{
    return (function_exists('presspermit')) ? presspermit()->getOption($option_basename) : pp_get_option($option_basename);
}

function pp_capabilities_update_permissions_option($option_basename, $option_val)
{
    function_exists('presspermit') ? presspermit()->updateOption($option_basename, $option_val) : pp_update_option($option_basename, $option_val);
}

/**
 * Get post type.
 *
 * @return null|string String of the post type.
 */
function pp_capabilities_get_post_type()
{
    global $post, $typenow, $current_screen;

    // We have a post so we can just get the post type from that.
    if ($post && $post->post_type) {
        return $post->post_type;
    }

    // Check the global $typenow - set in admin.php
    if ($typenow) {
        return $typenow;
    }

    // Check the global $current_screen object - set in screen.php
    if ($current_screen && $current_screen->post_type) {
        return $current_screen->post_type;
    }

    if (isset($_GET['post']) && !is_array($_GET['post'])) {
        $post_id = (int) esc_attr($_GET['post']);

    } elseif (isset($_POST['post_ID'])) {
        $post_id = (int) esc_attr($_POST['post_ID']);
    }

    if (!empty($post_id)) {
        return get_post_type($post_id);
    }

    // lastly check the post_type querystring
    if (isset($_REQUEST['post_type'])) {
        return sanitize_key($_REQUEST['post_type']);
    }

    return 'post';
}

/**
 * Check if Classic Editor plugin is available.
 *
 * @return bool
 */
function pp_capabilities_is_classic_editor_available()
{
    global $wp_version;

    return class_exists('Classic_Editor')
        || function_exists( 'the_gutenberg_project' )
        || class_exists('Gutenberg_Ramp')
        || version_compare($wp_version, '5.0', '<')
        || (function_exists('et_get_option') && 'on' === et_get_option('et_enable_classic_editor', 'off'));
}

    
/**
 * Get admin bar node and set as global for our usage.
 * Due to admin toolbar, this function need to run in frontend as well
 *
 * @return array||object $wp_admin_bar nodes.
 */
function ppc_features_get_admin_bar_nodes($wp_admin_bar){

    $adminBarNode = is_object($wp_admin_bar) ? $wp_admin_bar->get_nodes() : '';
    $ppcAdminBar = [];

    if (is_array($adminBarNode) || is_object($adminBarNode)) {
        foreach ($adminBarNode as $adminBarnode) {
            $id = $adminBarnode->id;
            $title = $adminBarnode->title;
            $parent = $adminBarnode->parent;
            $ppcAdminBar[$id] = array('id' => $id, 'title' => $title, 'parent' => $parent);
        }
    }

    $GLOBALS['ppcAdminBar'] = $ppcAdminBar;
}
add_action('admin_bar_menu', 'ppc_features_get_admin_bar_nodes', 999);

/**
 * Implement admin features restriction.
 * Due to admin toolbar, this function need to run in frontend as well
 *
 */
function ppc_admin_feature_restrictions() {
    require_once ( dirname(CME_FILE) . '/includes/features/restrict-admin-features.php' );    
    PP_Capabilities_Admin_Features::adminFeaturedRestriction();
}
add_action('plugins_loaded', 'ppc_admin_feature_restrictions');

/**
 * List of capabilities admin pages
 *
 */
function pp_capabilities_admin_pages(){

    $pp_capabilities_pages = [
        'pp-capabilities', 
        'pp-capabilities-roles', 
        'pp-capabilities-admin-menus', 
        'pp-capabilities-nav-menus', 
        'pp-capabilities-editor-features', 
        'pp-capabilities-backup', 
        'pp-capabilities-settings', 
        'pp-capabilities-admin-features'
    ];

   return apply_filters('pp_capabilities_admin_pages', $pp_capabilities_pages);
}

/**
 * Check if user is in capabilities admin page
 *
 */
function is_pp_capabilities_admin_page(){
    
    $pp_capabilities_pages = pp_capabilities_admin_pages();

    $is_pp_capabilities_page = false;
	if ( isset( $_GET['page'] ) && in_array( $_GET['page'], $pp_capabilities_pages )) {
        $is_pp_capabilities_page = true;
    }

    return apply_filters('is_pp_capabilities_admin_page', $is_pp_capabilities_page);
}