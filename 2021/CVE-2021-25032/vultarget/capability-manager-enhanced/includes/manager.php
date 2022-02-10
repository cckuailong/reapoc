<?php
/**
 * PublishPress Capabilities [Free]
 * 
 * Plugin to create and manage roles and capabilities.
 * 
 * This is the plugin's original controller module, which is due for some refactoring.
 * It registers and handles menus, loads javascript, and processes or routes update operations from the Capabilities screen.
 * 
 * Note: for lower overhead, this module is only loaded for Capabilities Pro URLs. 
 * For all other wp-admin URLs, menus are registered by a separate skeleton module.
 *
 * @author		Jordi Canals, Kevin Behrens
 * @copyright   Copyright (C) 2009, 2010 Jordi Canals, (C) 2020 PublishPress
 * @license		GNU General Public License version 2
 * @link		https://publishpress.com/
 *
 *
 *	Copyright 2009, 2010 Jordi Canals <devel@jcanals.cat>
 *
 *	Modifications Copyright 2020, PublishPress <help@publishpress.com>
 *
 *	This program is free software; you can redistribute it and/or
 *	modify it under the terms of the GNU General Public License
 *	version 2 as published by the Free Software Foundation.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

add_action( 'init', 'cme_update_pp_usage' );  // update early so resulting post type cap changes are applied for this request's UI construction

function cme_update_pp_usage() {
	if ( ! empty($_REQUEST['update_filtered_types']) || ! empty($_REQUEST['update_filtered_taxonomies']) || ! empty($_REQUEST['update_detailed_taxonomies']) || ! empty($_REQUEST['SaveRole']) ) {
		require_once( dirname(__FILE__).'/pp-handler.php' );
		return _cme_update_pp_usage();
	}
}

// Core WP roles to apply safeguard preventing accidental lockout from dashboard
function _cme_core_roles() {
	return apply_filters( 'pp_caps_core_roles', array( 'administrator', 'editor', 'revisor', 'author', 'contributor', 'subscriber' ) );
}

function _cme_core_caps() {
	$core_caps = array_fill_keys( array( 'switch_themes', 'edit_themes', 'activate_plugins', 'edit_plugins', 'edit_users', 'edit_files', 'manage_options', 'moderate_comments',
	'manage_links', 'upload_files', 'import', 'unfiltered_html', 'read', 'delete_users', 'create_users', 'unfiltered_upload', 'edit_dashboard',
	'update_plugins', 'delete_plugins', 'install_plugins', 'update_themes', 'install_themes',
	'update_core', 'list_users', 'remove_users', 'promote_users', 'edit_theme_options', 'delete_themes', 'export' ), true );

	// @todo (possibly) 
	/*
	if (is_multisite()) {
		$core_caps['manage_network_plugins'] = true;
	}
	*/

	ksort( $core_caps );
	return $core_caps;
}

function _cme_is_read_removal_blocked( $role_name ) {
	$role = get_role($role_name);
	$rcaps = $role->capabilities;

	$core_caps = array_diff_key( _cme_core_caps(), array_fill_keys( array( 'unfiltered_html', 'unfiltered_upload', 'upload_files', 'edit_files', 'read' ), true ) );

	if ( empty( $rcaps['dashboard_lockout_ok'] ) ) {
		$edit_caps = array();
		foreach ( get_post_types( array( 'public' => true ), 'object' ) as $type_obj ) {
			$edit_caps = array_merge( $edit_caps, array_values( array_diff_key( (array) $type_obj->cap, array( 'read_private_posts' => true ) ) ) );
		}

		$edit_caps = array_fill_keys( $edit_caps, true );
		unset( $edit_caps['read'] );
		unset( $edit_caps['upload_files'] );
		unset( $edit_caps['edit_files'] );

		if ( $role_has_admin_caps = in_array( $role_name, _cme_core_roles() ) && ( array_intersect_key( $rcaps, array_diff_key( $core_caps, array( 'read' => true ) ) ) || array_intersect_key( $rcaps, $edit_caps ) ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Class CapabilityManager.
 * Sets the main environment for all Capability Manager components.
 *
 * @author		Jordi Canals, Kevin Behrens
 * @link		https://publishpress.com/
 */
class CapabilityManager
{
	/**
	 * Array with all capabilities to be managed. (Depends on user caps).
	 * The array keys are the capability, the value is its screen name.
	 * @var array
	 */
	var $capabilities = array();

	/**
	 * Array with roles that can be managed. (Depends on user roles).
	 * The array keys are the role name, the value is its translated name.
	 * @var array
	 */
	var $roles = array();

	/**
	 * Current role we are managing
	 * @var string
	 */
	var $current;

	/**
	 * Maximum level current manager can assign to a user.
	 * @var int
	 */
	private $max_level;

	private $log_db_role_objects = array();

	var $message;

	/**
	 * Module ID. Is the module internal short name.
	 *
	 * @var string
	 */
	public $ID;

	public function __construct()
	{
		$this->ID = 'capsman';
		$this->mod_url = plugins_url( '', CME_FILE );

		if (is_admin() && !empty($_REQUEST['page']) && ('pp-capabilities-settings' == $_REQUEST['page']) && (!empty($_POST['all_options']) || !empty($_POST['all_options_pro']))) {
			require_once (dirname(CME_FILE) . '/includes/settings-handler.php');
		}

		$this->moduleLoad();

		add_action('admin_menu', array($this, 'adminMenus'), 5);  // execute prior to PP, to use menu hook

		// Load styles
		add_action('admin_print_styles', array($this, 'adminStyles'));

		if ( isset($_REQUEST['page']) && ( 'pp-capabilities' == $_REQUEST['page'] ) ) {
			add_action('admin_enqueue_scripts', array($this, 'adminScriptsPP'));
		}

		add_action('init', [$this, 'initRolesAdmin']);

		add_action('wp_ajax_pp-roles-add-role', [$this, 'handleRolesAjax']);
		add_action('wp_ajax_pp-roles-delete-role', [$this, 'handleRolesAjax']);

		if (defined('PRESSPERMIT_VERSION')) {
			add_action('wp_ajax_pp-roles-hide-role', [$this, 'handleRolesAjax']);
			add_action('wp_ajax_pp-roles-unhide-role', [$this, 'handleRolesAjax']);
		}
	}

    /**
     * Enqueues administration styles.
     *
     * @hook action 'admin_print_styles'
	 *
     * @return void
     */
    function adminStyles()
    {
		if (empty($_REQUEST['page']) 
		|| !in_array( 
			$_REQUEST['page'], 
			['pp-capabilities', 'pp-capabilities-roles', 'pp-capabilities-admin-menus', 'pp-capabilities-nav-menus', 'pp-capabilities-editor-features', 'pp-capabilities-backup', 'pp-capabilities-settings', 'pp-capabilities-admin-features']
			)
		) {
			return;
		}

		wp_enqueue_style('cme-admin-common', $this->mod_url . '/common/css/pressshack-admin.css', [], PUBLISHPRESS_CAPS_VERSION);

		wp_register_style( $this->ID . 'framework_admin', $this->mod_url . '/framework/styles/admin.css', false, PUBLISHPRESS_CAPS_VERSION);
		wp_enqueue_style( $this->ID . 'framework_admin');

		wp_register_style( $this->ID . '_admin', $this->mod_url . '/common/css/admin.css', false, PUBLISHPRESS_CAPS_VERSION);
		wp_enqueue_style( $this->ID . '_admin');

		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '.dev' : '';
		$url = $this->mod_url . "/common/js/admin{$suffix}.js";
		wp_enqueue_script( 'cme_admin', $url, array('jquery'), PUBLISHPRESS_CAPS_VERSION, true );
		wp_localize_script( 'cme_admin', 'cmeAdmin', [
			'negationCaption' => __( 'Explicity negate this capability by storing as disabled', 'capsman-enhanced' ),
			'typeCapsNegationCaption' => __( 'Explicitly negate these capabilities by storing as disabled', 'capsman-enhanced' ),
			'typeCapUnregistered' => __( 'Post type registration does not define this capability distinctly', 'capsman-enhanced' ),
			'capNegated' => __( 'This capability is explicitly negated. Click to add/remove normally.', 'capsman-enhanced' ),
			'chkCaption' => __( 'Add or remove this capability from the WordPress role', 'capsman-enhanced' ),
			'switchableCaption' => __( 'Add or remove capability from the role normally', 'capsman-enhanced' ) ,
			'deleteWarning' => __( 'Are you sure you want to delete this item ?', 'capsman-enhanced' ),
			'saveWarning'   => __( 'Add or clear custom item entry before saving changes.', 'capsman-enhanced' )
			]
		);
    }

	function adminScriptsPP() {
		wp_enqueue_style( 'plugin-install' );
		wp_enqueue_script( 'plugin-install' );
		add_thickbox();
	}

	/**
	 * Creates some filters at module load time.
	 *
	 * @return void
	 */
    protected function moduleLoad ()
    {
		$old_version = get_option($this->ID . '_version');
		if ( version_compare( $old_version, PUBLISHPRESS_CAPS_VERSION, 'ne') ) {
			update_option($this->ID . '_version', PUBLISHPRESS_CAPS_VERSION);
			$this->pluginUpdate();
		}

        // Only roles that a user can administer can be assigned to others.
        add_filter('editable_roles', array($this, 'filterEditRoles'));

        // Users with roles that cannot be managed, are not allowed to be edited.
        add_filter('map_meta_cap', array(&$this, 'filterUserEdit'), 10, 4);

		// ensure storage, retrieval of db-stored customizations to dynamic roles
		if ( isset($_REQUEST['page']) && in_array( $_REQUEST['page'], array( 'pp-capabilities', 'pp-capabilities-backup' ) ) ) {
			global $wpdb;
			$role_key = $wpdb->prefix . 'user_roles';
			$this->log_db_roles();
			add_filter( 'option_' . $role_key, array( &$this, 'reinstate_db_roles' ), PHP_INT_MAX );
		}

		add_filter( 'plugins_loaded', array( &$this, 'processRoleUpdate' ) );
    }

	public function set_current_role($role_name) {
		global $current_user;

		if ($role_name && !empty($current_user) && !empty($current_user->ID)) {
			update_option("capsman_last_role_{$current_user->ID}", $role_name);
		}
	}

	public function get_last_role() {
		global $current_user;
	
		$role_name = get_option("capsman_last_role_{$current_user->ID}");
	
		if (!$role_name || !get_role($role_name)) {
			$role_name = get_option('default_role');
		}
	
		return $role_name;
	}

	// Direct query of stored role definitions
	function log_db_roles( $legacy_arg = '' ) {
		global $wpdb;

		$results = (array) maybe_unserialize( $wpdb->get_var("SELECT option_value FROM $wpdb->options WHERE option_name = '{$wpdb->prefix}user_roles' LIMIT 1") );
		foreach( $results as $_role_name => $_role ) {
			$this->log_db_role_objects[$_role_name] = (object) $_role;
		}

		return $legacy_arg;
	}

	// note: this is only applied when accessing the cme role edit form
	function reinstate_db_roles( $passthru_roles = array() ) {
		global $wp_roles;

		if ( isset($wp_roles) && $this->log_db_role_objects ) {
			$intersect = array_intersect_key( $wp_roles->role_objects, $this->log_db_role_objects );
			foreach( array_keys( $intersect ) as $key ) {
				if ( ! empty( $this->log_db_role_objects[$key]->capabilities ) )
					$wp_roles->role_objects[$key]->capabilities = $this->log_db_role_objects[$key]->capabilities;
			}
		}

		return $passthru_roles;
	}

	/**
	 * Updates Capability Manager to a new version
	 *
	 * @return void
	 */
	protected function pluginUpdate ()
	{
		global $wpdb;

		$backup = get_option($this->ID . '_backup');
		if ( false === $backup ) {		// No previous backup found. Save it!
			global $wpdb;
			$roles = get_option($wpdb->prefix . 'user_roles');
			update_option( $this->ID . '_backup', $roles, false );
			update_option( $this->ID . '_backup_datestamp', current_time( 'timestamp' ), false );
		}

		if (!$wpdb->get_var("SELECT COUNT(option_id) FROM $wpdb->options WHERE option_name LIKE 'cme_backup_auto_%'")) {
			pp_capabilities_autobackup();
		}
	}

	/**
	 * Adds admin panel menus. (At plugins loading time. This is before plugins_loaded).
	 * User needs to have 'manage_capabilities' to access this menus.
	 * This is set as an action in the parent class constructor.
	 *
	 * @hook action admin_menu
	 * @return void
	 */
	public function adminMenus ()
	{
		// First we check if user is administrator and can 'manage_capabilities'.
		if ( current_user_can('administrator') && ! current_user_can('manage_capabilities') ) {
			$this->setAdminCapability();
		}

		add_action( 'admin_menu', array( &$this, 'cme_menu' ), 18 );
	}

	public function cme_menu() {
		$cap_name = (is_multisite() && is_super_admin()) ? 'read' : 'manage_capabilities';

		$permissions_title = __('Capabilities', 'capsman-enhanced');

		$menu_order = 72;

		if (defined('PUBLISHPRESS_PERMISSIONS_MENU_GROUPING')) {
			foreach ((array)get_option('active_plugins') as $plugin_file) {
				if ( false !== strpos($plugin_file, 'publishpress.php') ) {
					$menu_order = 27;
				}
			}
		}

		add_menu_page(
			$permissions_title,
			$permissions_title,
			$cap_name,
			'pp-capabilities',
			array($this, 'generalManager'),
			'dashicons-admin-network',
			$menu_order
		);

        $hook = add_submenu_page('pp-capabilities',  __('Roles', 'capsman-enhanced'), __('Roles', 'capsman-enhanced'), $cap_name, 'pp-capabilities-roles', [$this, 'ManageRoles']);
        
        if (!empty($hook)) {
            add_action( 
                "load-$hook", 
                function() {
                    require_once (dirname(CME_FILE) . '/includes/roles/roles-functions.php');
                    admin_roles_page_load();
                }
            );
		}

		add_submenu_page('pp-capabilities',  __('Editor Features', 'capsman-enhanced'), __('Editor Features', 'capsman-enhanced'), $cap_name, 'pp-capabilities-editor-features', [$this, 'ManageEditorFeatures']);

		add_submenu_page('pp-capabilities',  __('Admin Features', 'capsman-enhanced'), __('Admin Features', 'capsman-enhanced'), $cap_name, 'pp-capabilities-admin-features', [$this, 'ManageAdminFeatures']);

		do_action('pp-capabilities-admin-submenus');

		add_submenu_page('pp-capabilities',  __('Backup', 'capsman-enhanced'), __('Backup', 'capsman-enhanced'), $cap_name, 'pp-capabilities-backup', array($this, 'backupTool'));

		if (defined('PUBLISHPRESS_CAPS_PRO_VERSION')) {
			add_submenu_page('pp-capabilities',  __('Settings', 'capsman-enhanced'), __('Settings', 'capsman-enhanced'), $cap_name, 'pp-capabilities-settings', array($this, 'settingsPage'));
		}

		if (!defined('PUBLISHPRESS_CAPS_PRO_VERSION')) {
			add_submenu_page(
	            'pp-capabilities',
	            __('Upgrade to Pro', 'capsman-enhanced'),
	            __('Upgrade to Pro', 'capsman-enhanced'),
	            'manage_capabilities',
	            'capsman-enhanced',
	            array($this, 'generalManager')
	        );
		}
	}

    function initRolesAdmin() {
        // @todo: solve order of execution issue so this column headers definition is not duplicated
        if (!empty($_REQUEST['page']) && ('pp-capabilities-roles' == $_REQUEST['page'])) {
            add_filter(
                "manage_capabilities_page_pp-capabilities-roles_columns", 

                function($arr) {
                    return [
                        'cb' => '<input type="checkbox"/>',
                        'name' => __('Name', 'capsman-enhanced'),
                        'role' => __('Role', 'capsman-enhanced'),
                        'count' => __('Users', 'capsman-enhanced'),
                    ];
                }
            );
        }
    }

	function handleRolesAjax() {
        require_once (dirname(CME_FILE) . '/includes/roles/roles-functions.php');

        if (!class_exists('PP_Capabilities_Roles')) {
            require_once (dirname(CME_FILE) . '/includes/roles/class/class-pp-roles.php');
        }

        $roles = pp_capabilities_roles()->run();
    }

	/**
	 * Manages roles
	 *
	 * @hook add_management_page
	 * @return void
	 */
	public function ManageRoles ()
	{
		if ((!is_multisite() || !is_super_admin()) && !current_user_can('administrator') && !current_user_can('manage_capabilities')) {
            // TODO: Implement exceptions.
		    wp_die('<strong>' .__('You do not have permission to manage roles.', 'capsman-enhanced') . '</strong>');
		}

        require_once (dirname(CME_FILE) . '/includes/roles/roles-functions.php');

        if (!class_exists('PP_Capabilities_Roles')) {
            require_once (dirname(CME_FILE) . '/includes/roles/class/class-pp-roles.php');
        }

        $roles = pp_capabilities_roles()->run();

        require_once ( dirname(CME_FILE) . '/includes/roles/roles.php' );
	}


	/**
	 * Manages Editor Features
	 *
	 * @return void
	 */
	public function ManageEditorFeatures() {
		if ((!is_multisite() || !is_super_admin()) && !current_user_can('administrator') && !current_user_can('manage_capabilities')) {
            // TODO: Implement exceptions.
		    wp_die('<strong>' .__('You do not have permission to manage editor features.', 'capabilities-pro') . '</strong>');
		}

		$this->generateNames();
		$roles = array_keys($this->roles);

		if (!isset($this->current)) {
			if (empty($_POST) && !empty($_REQUEST['role'])) {
				$this->set_current_role($_REQUEST['role']);
			}
		}

		if (!isset($this->current) || !get_role($this->current)) {
			$this->current = get_option('default_role');
		}

		if (!in_array($this->current, $roles)) {
			$this->current = array_shift($roles);
		}

		if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['ppc-editor-features-role'])) {
            $this->set_current_role($_POST['ppc-editor-features-role']);

            $classic_editor = pp_capabilities_is_classic_editor_available();

            //$def_post_types = apply_filters('pp_capabilities_feature_post_types', get_post_types(['public' => true]));
            $def_post_types = apply_filters('pp_capabilities_feature_post_types', ['post', 'page']);

            foreach ($def_post_types as $post_type) {
                if ($classic_editor) {
                    $posted_settings = (isset($_POST["capsman_feature_restrict_classic_{$post_type}"])) ? $_POST["capsman_feature_restrict_classic_{$post_type}"] : [];
                    $post_features_option = get_option("capsman_feature_restrict_classic_{$post_type}", []);
                    $post_features_option[$_POST['ppc-editor-features-role']] = $posted_settings;
                    update_option("capsman_feature_restrict_classic_{$post_type}", $post_features_option, false);
                }

                $posted_settings = (isset($_POST["capsman_feature_restrict_{$post_type}"])) ? $_POST["capsman_feature_restrict_{$post_type}"] : [];
                $post_features_option = get_option("capsman_feature_restrict_{$post_type}", []);
                $post_features_option[$_POST['ppc-editor-features-role']] = $posted_settings;
                update_option("capsman_feature_restrict_{$post_type}", $post_features_option, false);
            }

            ak_admin_notify(__('Settings updated.', 'capabilities-pro'));
		}

		do_action('pp_capabilities_editor_features');
        include(dirname(CME_FILE) . '/includes/features/editor-features.php');
    }
	
	/**
	 * Manages Admin Features
	 *
	 * @return void
	 */
	public function ManageAdminFeatures() {
		if ((!is_multisite() || !is_super_admin()) && !current_user_can('administrator') && !current_user_can('manage_capabilities')) {
            // TODO: Implement exceptions.
		    wp_die('<strong>' .__('You do not have permission to manage admin features.', 'capabilities-pro') . '</strong>');
		}

		$this->generateNames();
		$roles = array_keys($this->roles);

		if (!isset($this->current)) {
			if (empty($_POST) && !empty($_REQUEST['role'])) {
				$this->set_current_role($_REQUEST['role']);
			}
		}

		if (!isset($this->current) || !get_role($this->current)) {
			$this->current = get_option('default_role');
		}

		if (!in_array($this->current, $roles)) {
			$this->current = array_shift($roles);
		}

		if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['ppc-admin-features-role'])) {
            $this->set_current_role($_POST['ppc-admin-features-role']);

			$disabled_admin_items = !empty(get_option('capsman_disabled_admin_features')) ? (array)get_option('capsman_disabled_admin_features') : [];
			$disabled_admin_items[$_POST['ppc-admin-features-role']] = isset($_POST['capsman_disabled_admin_features']) ? $_POST['capsman_disabled_admin_features'] : '';

			update_option('capsman_disabled_admin_features', $disabled_admin_items, false);

			//set reload option for instant reflection if user is updating own role
			if(in_array($_POST['ppc-admin-features-role'], wp_get_current_user()->roles)){
				$ppc_page_reload = '1';
			}
			
            ak_admin_notify(__('Settings updated.', 'capabilities-pro'));
		}

        include(dirname(CME_FILE) . '/includes/features/admin-features.php');
    }

	/**
	 * Sets the 'manage_capabilities' cap to the administrator role.
	 *
	 * @return void
	 */
	public function setAdminCapability ()
	{
		if ($admin = get_role('administrator')) {
			$admin->add_cap('manage_capabilities');
		}
	}

	/**
	 * Filters roles that can be shown in roles list.
	 * This is mainly used to prevent an user admin to create other users with
	 * higher capabilities.
	 *
	 * @hook 'editable_roles' filter.
	 *
	 * @param $roles List of roles to check.
	 * @return array Restircted roles list
	 */
	function filterEditRoles ( $roles )
	{
		global $current_user;

		if (function_exists('wp_get_current_user') || defined('PP_CAPABILITIES_ROLES_FILTER_EARLY_EXECUTION')) {  // Avoid downstream fatal error from premature current_user_can() call if get_editable_roles() is called too early
			$this->generateNames();
			$valid = array_keys($this->roles);

			foreach ( $roles as $role => $caps ) {
				if ( ! in_array($role, $valid) ) {
					unset($roles[$role]);
				}
			}
		}

        return $roles;
	}

	/**
	 * Checks if a user can be edited or not by current administrator.
	 * Returns array('do_not_allow') if user cannot be edited.
	 *
	 * @hook 'map_meta_cap' filter
	 *
	 * @param array $caps Current user capabilities
	 * @param string $cap Capability to check
	 * @param int $user_id Current user ID
	 * @param array $args For our purpose, we receive edited user id at $args[0]
	 * @return array Allowed capabilities.
	 */
	function filterUserEdit ( $caps, $cap, $user_id, $args )
	{
	    if ( ! in_array( $cap, array( 'edit_user', 'delete_user', 'promote_user', 'remove_user' ) ) || ( ! isset($args[0]) ) || $user_id == (int) $args[0] ) {
	        return $caps;
	    }

		$user = new WP_User( (int) $args[0] );

		$this->generateNames();

		if ( defined( 'CME_LEGACY_USER_EDIT_FILTER' ) && CME_LEGACY_USER_EDIT_FILTER ) {
			$valid = array_keys($this->roles);

			foreach ( $user->roles as $role ) {
				if ( ! in_array($role, $valid) ) {
					$caps = array('do_not_allow');
					break;
				}
			}
		} else {
			global $wp_roles;

			foreach ( $user->roles as $role ) {
				$r = get_role( $role );
    			$level = ak_caps2level($r->capabilities);

				if ( ( ! $level ) && ( 'administrator' == $role ) )
					$level = 10;

	    		if ( $level > $this->max_level ) {
		    		$caps = array('do_not_allow');
					break;
			    }
    		}

		}

		return $caps;
	}

	function processRoleUpdate() {
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ( ! empty($_REQUEST['SaveRole']) || ! empty($_REQUEST['AddCap']) ) ) {
			if ((!is_multisite() || !is_super_admin()) && !current_user_can('administrator') && !current_user_can('manage_capabilities')) {
				// TODO: Implement exceptions.
				wp_die('<strong>' .__('You do not have permission to manage capabilities.', 'capsman-enhanced') . '</strong>');
			}

			if ( ! empty($_REQUEST['current']) ) { // don't process role update unless form variable is received
				check_admin_referer('capsman-general-manager');

				$role = get_role($_REQUEST['current']);
				$current_level = ($role) ? ak_caps2level($role->capabilities) : 0;

				$this->processAdminGeneral();

				$set_level = (isset($_POST['level'])) ? $_POST['level'] : 0;

				if ($set_level != $current_level) {
					global $wp_roles, $wp_version;

					if ( version_compare($wp_version, '4.9', '>=') ) {
						$wp_roles->for_site();
					} else {
						$wp_roles->reinit();
					}

					foreach( get_users(array('role' => $_REQUEST['current'], 'fields' => 'ID')) as $ID ) {
						$user = new WP_User($ID);
						$user->get_role_caps();
						$user->update_user_level_from_caps();
					}
				}
			}
		}
	}

	/**
	 * Manages global settings admin.
	 *
	 * @hook add_submenu_page
	 * @return void
	 */
	function generalManager () {
		if ((!is_multisite() || !is_super_admin()) && !current_user_can('administrator') && !current_user_can('manage_capabilities')) {
            // TODO: Implement exceptions.
		    wp_die('<strong>' .__('You do not have permission to manage capabilities.', 'capsman-enhanced') . '</strong>');
		}

		if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
			if ( empty($_REQUEST['SaveRole']) && empty($_REQUEST['AddCap']) ) {
				check_admin_referer('capsman-general-manager');
				$this->processAdminGeneral();
			} elseif ( ! empty($_REQUEST['SaveRole']) ) {
				ak_admin_notify( $this->message );  // moved update operation to earlier action to avoid UI refresh issues.  But outputting notification there breaks styling.
			} elseif ( ! empty($_REQUEST['AddCap']) ) {
				ak_admin_notify( $this->message );
			}
		} else {
			if (!empty($_REQUEST['added'])) {
				ak_admin_notify(__('New capability added to role.'));
			}
		}

		$this->generateNames();
		$roles = array_keys($this->roles);

		if ( isset($_GET['action']) && 'delete' == $_GET['action']) {
			require_once( dirname(__FILE__).'/handler.php' );
			$capsman_modify = new CapsmanHandler( $this );
			$capsman_modify->adminDeleteRole();
		}

		if ( ! isset($this->current) ) { // By default, we manage the default role
			if (empty($_POST) && !empty($_REQUEST['role'])) {
				$role = $_REQUEST['role'];

				if (!pp_capabilities_is_editable_role($role)) {
					wp_die(__('The selected role is not editable.', 'capsman-enhanced'));
				}

				$this->set_current_role($role);
			}
		}

		if (!isset($this->current) || !get_role($this->current)) {
			$this->current = $this->get_last_role();
		}

		if ( ! in_array($this->current, $roles) ) {    // Current role has been deleted.
			$this->current = array_shift($roles);
		}

		include ( dirname(CME_FILE) . '/includes/admin.php' );
	}

	/**
	 * Processes and saves the changes in the general capabilities form.
	 *
	 * @return void
	 */
	private function processAdminGeneral ()
	{
		if (! isset($_POST['action']) || 'update' != $_POST['action'] ) {
		    // TODO: Implement exceptions. This must be a fatal error.
			ak_admin_error(__('Bad form Received', 'capsman-enhanced'));
			return;
		}

		$post = stripslashes_deep($_POST);
		if ( empty ($post['caps']) ) {
		    $post['caps'] = array();
		}

		// Select a new role.
		if ( ! empty($post['LoadRole']) ) {
			$this->set_current_role($post['role']);
		} else {
			$this->set_current_role($post['current']);

			require_once( dirname(__FILE__).'/handler.php' );
			$capsman_modify = new CapsmanHandler( $this );
			$capsman_modify->processAdminGeneral( $post );
		}
	}

	/**
	 * Callback function to create names.
	 * Replaces underscores by spaces and uppercases the first letter.
	 *
	 * @access private
	 * @param string $cap Capability name.
	 * @return string	The generated name.
	 */
	function _capNamesCB ( $cap )
	{
		$cap = str_replace('_', ' ', $cap);
		//$cap = ucfirst($cap);

		return $cap;
	}

	/**
	 * Generates an array with the system capability names.
	 * The key is the capability and the value the created screen name.
	 *
	 * @uses self::_capNamesCB()
	 * @return void
	 */
	function generateSysNames ()
	{
		$this->max_level = 10;
		$this->roles = ak_get_roles(true);
		$caps = array();

		foreach ( array_keys($this->roles) as $role ) {
			$role_caps = get_role($role);
			$caps = array_merge( $caps, (array) $role_caps->capabilities );  // user reported PHP 5.3.3 error without array cast
		}

		$keys = array_keys($caps);
		$names = array_map(array($this, '_capNamesCB'), $keys);
		$this->capabilities = array_combine($keys, $names);

		asort($this->capabilities);
	}

	/**
	 * Generates an array with the user capability names.
	 * If user has 'administrator' role, system roles are generated.
	 * The key is the capability and the value the created screen name.
	 * A user cannot manage more capabilities that has himself (Except for administrators).
	 *
	 * @uses self::_capNamesCB()
	 * @return void
	 */
	function generateNames ()
	{
		if ( current_user_can('administrator') || ( is_multisite() && is_super_admin() ) ) {
			$this->generateSysNames();
		} else {
		    global $user_ID;
		    $user = new WP_User($user_ID);
		    $this->max_level = ak_caps2level($user->allcaps);

		    $keys = array_keys($user->allcaps);
    		$names = array_map(array($this, '_capNamesCB'), $keys);

	    	$this->capabilities = ( $keys ) ? array_combine($keys, $names) : array();

		    $roles = ak_get_roles(true);
    		unset($roles['administrator']);

			if ( ( defined( 'CME_LEGACY_USER_EDIT_FILTER' ) && CME_LEGACY_USER_EDIT_FILTER ) || ( ! empty( $_REQUEST['page'] ) && 'pp-capabilities' == $_REQUEST['page'] ) ) {
				foreach ( $user->roles as $role ) {			// Unset the roles from capability list.
					unset ( $this->capabilities[$role] );
					unset ( $roles[$role]);					// User cannot manage his roles.
				}
			}

	    	asort($this->capabilities);

		    foreach ( array_keys($roles) as $role ) {
			    $r = get_role($role);
    			$level = ak_caps2level($r->capabilities);

	    		if ( $level > $this->max_level ) {
		    		unset($roles[$role]);
			    }
    		}

	    	$this->roles = $roles;
		}
	}

	/**
	 * Manages backup, restore and resset roles and capabilities
	 *
	 * @hook add_management_page
	 * @return void
	 */
	function backupTool ()
	{
		if ((!is_multisite() || !is_super_admin()) && !current_user_can('administrator') && !current_user_can('restore_roles')) {
		    // TODO: Implement exceptions.
			wp_die('<strong>' .__('You do not have permission to restore roles.', 'capsman-enhanced') . '</strong>');
		}

		if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
			require_once( dirname(__FILE__).'/backup-handler.php' );
			$cme_backup_handler = new Capsman_BackupHandler( $this );
			$cme_backup_handler->processBackupTool();
		}

		if ( isset($_GET['action']) && 'reset-defaults' == $_GET['action']) {
			require_once( dirname(__FILE__).'/backup-handler.php' );
			$cme_backup_handler = new Capsman_BackupHandler( $this );
			$cme_backup_handler->backupToolReset();
		}

		include ( dirname(CME_FILE) . '/includes/backup.php' );
	}

	function settingsPage() {
		include ( dirname(CME_FILE) . '/includes/settings.php' );
	}
}

function cme_publishpressFooter() {
	?>
	<footer>

	<div class="pp-rating">
	<a href="https://wordpress.org/support/plugin/capability-manager-enhanced/reviews/#new-post" target="_blank" rel="noopener noreferrer">
	<?php printf(
		__('If you like %s, please leave us a %s rating. Thank you!', 'capsman-enhanced'),
		'<strong>PublishPress Capabilities</strong>',
		'<span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span>'
		);
	?>
	</a>
	</div>

	<hr>
	<nav>
	<ul>
	<li><a href="https://publishpress.com/capability-manager/" target="_blank" rel="noopener noreferrer" title="<?php _e('About PublishPress Capabilities', 'capsman-enhanced');?>"><?php _e('About', 'capsman-enhanced');?>
	</a></li>
	<li><a href="https://publishpress.com/knowledge-base/how-to-use-capability-manager/" target="_blank" rel="noopener noreferrer" title="<?php _e('Capabilites Documentation', 'capsman-enhanced');?>"><?php _e('Documentation', 'capsman-enhanced');?>
	</a></li>
	<li><a href="https://publishpress.com/contact" target="_blank" rel="noopener noreferrer" title="<?php _e('Contact the PublishPress team', 'capsman-enhanced');?>"><?php _e('Contact', 'capsman-enhanced');?>
	</a></li>
	<li><a href="https://twitter.com/publishpresscom" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-twitter"></span>
	</a></li>
	<li><a href="https://facebook.com/publishpress" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-facebook"></span>
	</a></li>
	</ul>
	</nav>

	<div class="pp-pressshack-logo">
	<a href="https://publishpress.com" target="_blank" rel="noopener noreferrer">

	<img src="<?php echo plugins_url('', CME_FILE) . '/common/img/publishpress-logo.png';?>" />
	</a>
	</div>

	</footer>
	<?php
}
