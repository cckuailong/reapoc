<?php
/*
 * PublishPress Capabilities [Free]
 * 
 * Process update operations from the Capabilities screen
 * 
 */

class CapsmanHandler
{
	var $cm;

	function __construct($manager_obj = false) {
		if ($manager_obj) {
			$this->cm = $manager_obj;
		} else {
			global $capsman;
			$this->cm = $capsman;
		}

		require_once (dirname(CME_FILE) . '/includes/roles/roles-functions.php');
	}
	
	function processAdminGeneral( $post ) {
		global $wp_roles;
		
		if ('pp-capabilities-settings' == $_REQUEST['page']) {
			do_action('publishpress-caps_process_update');
			return;
		}

		// Create a new role.
		if ( ! empty($post['CreateRole']) ) {
			if ( $newrole = $this->createRole($post['create-name']) ) {
				ak_admin_notify(__('New role created.', 'capsman-enhanced'));
				$this->cm->set_current_role($newrole);
			} else {
				if ( empty($post['create-name']) && in_array(get_locale(), ['en_EN', 'en_US']) )
					ak_admin_error( 'Error: No role name specified.', 'capsman-enhanced' );
				else
					ak_admin_error(__('Error: Failed creating the new role.', 'capsman-enhanced'));
			}

		// rename role
		} elseif (!empty($post['RenameRole']) && !empty($post['rename-name'])) {
			$current = get_role($post['current']);
			$new_title = sanitize_text_field($post['rename-name']);

			if ($current && isset($wp_roles->roles[$current->name]) && $new_title) {
				$old_title = $wp_roles->roles[$current->name]['name'];
				$wp_roles->roles[$current->name]['name'] = $new_title;
				update_option($wp_roles->role_key, $wp_roles->roles);

				ak_admin_notify(sprintf(__('Role "%s" (id %s) renamed to "%s"', 'capsman-enhanced'), $old_title, strtolower($current->name), $new_title));
				$this->cm->set_current_role($current->name);
			}
		// Copy current role to a new one.
		} elseif ( ! empty($post['CopyRole']) ) {
			$current = get_role($post['current']);
			if ( $newrole = $this->createRole($post['copy-name'], $current->capabilities) ) {
				ak_admin_notify(__('New role created.', 'capsman-enhanced'));
				$this->cm->set_current_role($newrole);
			} else {
				if ( empty($post['copy-name']) && in_array(get_locale(), ['en_EN', 'en_US']) )
					ak_admin_error( 'Error: No role name specified.', 'capsman-enhanced' );
				else
					ak_admin_error(__('Error: Failed creating the new role.', 'capsman-enhanced'));
			}

		// Save role changes. Already saved at start with self::saveRoleCapabilities().
		} elseif ( ! empty($post['SaveRole']) ) {
			if ( MULTISITE ) {
				global $wp_roles;
				( method_exists( $wp_roles, 'for_site' ) ) ? $wp_roles->for_site() : $wp_roles->reinit();
			}
			
			if (!pp_capabilities_is_editable_role($post['current'])) {
				ak_admin_error( 'The selected role is not editable.', 'capsman-enhanced' );
				return;
			}

			$this->saveRoleCapabilities($post['current'], $post['caps'], $post['level']);
			
			if ( defined( 'PRESSPERMIT_ACTIVE' ) ) {  // log customized role caps for subsequent restoration
				// for bbPress < 2.2, need to log customization of roles following bbPress activation
				$plugins = ( function_exists( 'bbp_get_version' ) && version_compare( bbp_get_version(), '2.2', '<' ) ) ? array( 'bbpress.php' ) : array();	// back compat

				if ( ! $customized_roles = get_option( 'pp_customized_roles' ) )
					$customized_roles = array();
				
				$customized_roles[$post['role']] = (object) array( 'caps' => array_map( 'boolval', $post['caps'] ), 'plugins' => $plugins );
				update_option( 'pp_customized_roles', $customized_roles );
				
				global $wpdb;
				$wpdb->query( "UPDATE $wpdb->options SET autoload = 'no' WHERE option_name = 'pp_customized_roles'" );
			}
		// Create New Capability and adds it to current role.
		} elseif ( ! empty($post['AddCap']) ) {
			if ( MULTISITE ) {
				global $wp_roles;
				( method_exists( $wp_roles, 'for_site' ) ) ? $wp_roles->for_site() : $wp_roles->reinit();
			}

			if (!pp_capabilities_is_editable_role($post['current'])) {
				ak_admin_error( 'The selected role is not editable.', 'capsman-enhanced' );
				return;
			}

			$role = get_role($post['current']);
			$role->name = $post['current'];		// bbPress workaround

			$newname = $this->createNewName($post['capability-name']);

			if (empty($newname['error'])) {
				$role->add_cap($newname['name']);

				// for bbPress < 2.2, need to log customization of roles following bbPress activation
				$plugins = ( function_exists( 'bbp_get_version' ) && version_compare( bbp_get_version(), '2.2', '<' ) ) ? array( 'bbpress.php' ) : array();	// back compat
				
				if ( ! $customized_roles = get_option( 'pp_customized_roles' ) )
					$customized_roles = array();

				$customized_roles[$post['role']] = (object) array( 'caps' => array_merge( $role->capabilities, array( $newname['name'] => 1 ) ), 'plugins' => $plugins );
				update_option( 'pp_customized_roles', $customized_roles );
				
				global $wpdb;
				$wpdb->query( "UPDATE $wpdb->options SET autoload = 'no' WHERE option_name = 'pp_customized_roles'" );

				$url = admin_url('admin.php?page=pp-capabilities&role=' . $post['role'] . '&added=1');
				wp_redirect($url);
				exit;
			} else {
				ak_admin_notify(__('Incorrect capability name.'));
			}
			
		} elseif ( ! empty($post['update_filtered_types']) || ! empty($post['update_filtered_taxonomies']) || ! empty($post['update_detailed_taxonomies']) ) {
			//if ( /*  settings saved successfully on plugins_loaded action  */ ) {
				ak_admin_notify(__('Type / Taxonomy settings saved.', 'capsman-enhanced'));
			//} else {
			//	ak_admin_error(__('Error saving capability settings.', 'capsman-enhanced'));
			//}
		} else {
			if (!apply_filters('publishpress-caps_submission_ok', false)) {
				ak_admin_error(__('Bad form received.', 'capsman-enhanced'));
			}
		}

		if ( ! empty($newrole) && defined('PRESSPERMIT_ACTIVE') ) {
			if ( ( ! empty($post['CreateRole']) && ! empty( $_REQUEST['new_role_pp_only'] ) ) || ( ! empty($post['CopyRole']) && ! empty( $_REQUEST['copy_role_pp_only'] ) ) ) {
				$pp_only = (array) pp_capabilities_get_permissions_option( 'supplemental_role_defs' );
				$pp_only[]= $newrole;

				pp_capabilities_update_permissions_option('supplemental_role_defs', $pp_only);
				
				_cme_pp_default_pattern_role( $newrole );
				pp_refresh_options();
			}
		}
	}

	
	/**
	 * Creates a new role/capability name from user input name.
	 * Name rules are:
	 * 		- 2-40 charachers lenght.
	 * 		- Only letters, digits, spaces and underscores.
	 * 		- Must to start with a letter.
	 *
	 * @param string $name	Name from user input.
	 * @return array|false An array with the name and display_name, or false if not valid $name.
	 */
	public function createNewName( $name ) {
		// Allow max 40 characters, letters, digits and spaces
		$name = trim(substr($name, 0, 40));
		$pattern = '/^[a-zA-Z][a-zA-Z0-9 _]+$/';

		if ( preg_match($pattern, $name) ) {
			$roles = ak_get_roles();

			$name = str_replace(' ', '_', $name);
			if ( in_array($name, $roles) || array_key_exists($name, $this->cm->capabilities) ) {
				return ['error' => 'role_exists', 'name' => $name];		// Already a role or capability with this name.
			}

			$display = explode('_', $name);
			$name = strtolower($name);

			// Apply ucfirst proper caps unless capitalization already provided
			foreach($display as $i => $word) {
				if ($word === strtolower($word)) {
					$display[$i] = ucfirst($word);
				}
			}

			$display = implode(' ', $display);

			return compact('name', 'display');
		} else {
			return ['error' => 'invalid_name', 'name' => $name];
		}
	}

	/**
	 * Creates a new role.
	 *
	 * @param string $name	Role name to create.
	 * @param array $caps	Role capabilities.
	 * @return string|false	Returns the name of the new role created or false if failed.
	 */
	public function createRole( $name, $caps = [], $args = [] ) {
		if ( ! is_array($caps) )
			$caps = array();

		$role = $this->createNewName($name);
		if (!empty($role['error'])) {
			return false;
		}

		$new_role = add_role($role['name'], $role['display'], $caps);
		if ( is_object($new_role) ) {
			return $role['name'];
		} else {
			return false;
		}
	}

	 /**
	  * Saves capability changes to roles.
	  *
	  * @param string $role_name Role name to change its capabilities
	  * @param array $caps New capabilities for the role.
	  * @return void
	  */
	private function saveRoleCapabilities( $role_name, $caps, $level ) {
		$this->cm->generateNames();
		$role = get_role($role_name);

		// workaround to ensure db storage of customizations to bbp dynamic roles
		$role->name = $role_name;
		
		$stored_role_caps = ( ! empty($role->capabilities) && is_array($role->capabilities) ) ? array_intersect( $role->capabilities, array(true, 1) ) : array();
		$stored_negative_role_caps = ( ! empty($role->capabilities) && is_array($role->capabilities) ) ? array_intersect( $role->capabilities, array(false) ) : array();
		
		$old_caps = array_intersect_key( $stored_role_caps, $this->cm->capabilities);
		$new_caps = ( is_array($caps) ) ? array_map('boolval', $caps) : array();
		$new_caps = array_merge($new_caps, ak_level2caps($level));

		// Find caps to add and remove
		$add_caps = array_diff_key($new_caps, $old_caps);
		$del_caps = array_diff_key(array_merge($old_caps, $stored_negative_role_caps), $new_caps);

		$changed_caps = array();
		foreach( array_intersect_key( $new_caps, $old_caps ) as $cap_name => $cap_val ) {
			if ( $new_caps[$cap_name] != $old_caps[$cap_name] )
				$changed_caps[$cap_name] = $cap_val;
		}
		
		$add_caps = array_merge( $add_caps, $changed_caps );
		
		if ( ! $is_administrator = current_user_can('administrator') ) {
			unset($add_caps['manage_capabilities']);
			unset($del_caps['manage_capabilities']);
		}

		if ( 'administrator' == $role_name && isset($del_caps['manage_capabilities']) ) {
			unset($del_caps['manage_capabilities']);
			ak_admin_error(__('You cannot remove Manage Capabilities from Administrators', 'capsman-enhanced'));
		}
		
		// additional safeguard against removal of read capability
		if ( isset( $del_caps['read'] ) && _cme_is_read_removal_blocked( $role_name ) ) {
			unset( $del_caps['read'] );
		}
		
		// Add new capabilities to role
		foreach ( $add_caps as $cap => $grant ) {
			if ( $is_administrator || current_user_can($cap) )
				$role->add_cap( $cap, $grant );
		}

		// Remove capabilities from role
		foreach ( $del_caps as $cap => $grant) {
			if ( $is_administrator || current_user_can($cap) )
				$role->remove_cap($cap);
		}
		
		$this->cm->log_db_roles();
		
		if (is_multisite() && is_super_admin() && is_main_site()) {
			if ( ! $autocreate_roles = get_site_option( 'cme_autocreate_roles' ) )
				$autocreate_roles = array();
			
			$this_role_autocreate = ! empty($_REQUEST['cme_autocreate_role']);
			
			if ( $this_role_autocreate && ! in_array( $role_name, $autocreate_roles ) ) {
				$autocreate_roles []= $role_name;
				update_site_option( 'cme_autocreate_roles', $autocreate_roles );
			}
			
			if ( ! $this_role_autocreate && in_array( $role_name, $autocreate_roles ) ) {
				$autocreate_roles = array_diff( $autocreate_roles, array( $role_name ) );
				update_site_option( 'cme_autocreate_roles', $autocreate_roles );
			}
			
			$do_role_sync = !empty($_REQUEST['cme_net_sync_role']);
			$do_option_sync = !empty($_REQUEST['cme_net_sync_options']);

			if ($do_role_sync || $do_option_sync) {
				// loop through all sites on network, creating or updating role def
		
				global $wpdb, $wp_roles, $blog_id;
				$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs ORDER BY blog_id" );
				$orig_blog_id = $blog_id;	
		
				if ($do_role_sync) {
					$role_caption = $wp_roles->role_names[$role_name];
					
					$new_caps = ( is_array($caps) ) ? array_map('boolval', $caps) : array();
					$new_caps = array_merge($new_caps, ak_level2caps($level) );
					
					$admin_role = $wp_roles->get_role('administrator');
					$main_admin_caps = array_merge( $admin_role->capabilities, ak_level2caps(10) );
				}

				$sync_options = [];

				if ($do_option_sync) {
					// capability-related options
					$pp_prefix = (defined('PPC_VERSION') && !defined('PRESSPERMIT_VERSION')) ? 'pp' : 'presspermit';

					foreach(['define_create_posts_cap', 'enabled_post_types', 'enabled_taxonomies'] as $option_name) {
						$sync_options["{$pp_prefix}_$option_name"] = get_option("{$pp_prefix}_$option_name");
					}

					$sync_options['cme_detailed_taxonomies'] = get_option('cme_detailed_taxonomies');
					$sync_options['cme_enabled_post_types'] = get_option('cme_enabled_post_types');
					$sync_options['presspermit_supplemental_role_defs'] = get_option('presspermit_supplemental_role_defs');
				}

				foreach ( $blog_ids as $id ) {				
					if ( is_main_site($id) )
						continue;
					
					switch_to_blog( $id );

					if ($do_role_sync) {
						( method_exists( $wp_roles, 'for_site' ) ) ? $wp_roles->for_site() : $wp_roles->reinit();
						
						if ( $blog_role = $wp_roles->get_role( $role_name ) ) {
							$stored_role_caps = ( ! empty($blog_role->capabilities) && is_array($blog_role->capabilities) ) ? array_intersect( $blog_role->capabilities, array(true, 1) ) : array();
							
							$old_caps = array_intersect_key( $stored_role_caps, $this->cm->capabilities);

							// Find caps to add and remove
							$add_caps = array_diff_key($new_caps, $old_caps);
							$del_caps = array_intersect_key( array_diff_key($old_caps, $new_caps), $main_admin_caps );	// don't mess with caps that are totally unused on main site
							
							// Add new capabilities to role
							foreach ( $add_caps as $cap => $grant ) {
								$blog_role->add_cap( $cap, $grant );
							}

							// Remove capabilities from role
							foreach ( $del_caps as $cap => $grant) {
								$blog_role->remove_cap($cap);
							}
						} else {
							$wp_roles->add_role( $role_name, $role_caption, $new_caps );
						}
					}

					foreach($sync_options as $option_name => $option_val) {
						update_option($option_name, $option_val);
					}
					
					restore_current_blog();
				}
				
				( method_exists( $wp_roles, 'for_site' ) ) ? $wp_roles->for_site() : $wp_roles->reinit();
			}
		} // endif multisite installation with super admin editing a main site role

		pp_capabilities_autobackup();
	}
	
	/**
	 * Deletes a role.
	 * The role comes from the $_GET['role'] var and the nonce has already been checked.
	 * Default WordPress role cannot be deleted and if trying to do it, throws an error.
	 * Users with the deleted role, are moved to the WordPress default role.
	 *
	 * @return void
	 */
	function adminDeleteRole ()
	{
		$role_name = $_GET['role'];
		check_admin_referer('delete-role_' . $role_name);
		
		$this->cm->current = $role_name;

		if (!pp_capabilities_is_editable_role($role_name)) {
			ak_admin_error( 'The selected role is not editable.', 'capsman-enhanced' );
		}

		if (false !== pp_capabilities_roles()->actions->delete_role($role_name, ['allow_system_role_deletion' => true, 'nonce_check' => false])) {
			unset($this->cm->roles[$role_name]);
			$this->cm->current = get_option('default_role');
		}
	}
}

if ( ! function_exists('boolval') ) {
	function boolval( $val ) {
		return (bool) $val;
	}
}
