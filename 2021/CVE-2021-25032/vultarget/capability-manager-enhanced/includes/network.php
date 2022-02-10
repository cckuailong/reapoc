<?php
/*
 * PublishPress Capabilities [Free]
 * 
 * Multisite-related functions / filter handlers
 * 
 */

add_action( 'wpmu_new_blog', '_cme_new_blog' );
function _cme_new_blog( $new_blog_id ) {
	if ( $autocreate_roles = get_site_option( 'cme_autocreate_roles' ) ) {
		global $wp_roles, $blog_id;
		
		$restore_blog_id = $blog_id;
		
		$main_site_id = (function_exists('get_main_site_id')) ? get_main_site_id() : 1;

		switch_to_blog($main_site_id);
		( method_exists( $wp_roles, 'for_site' ) ) ? $wp_roles->for_site() : $wp_roles->reinit();
		
		$main_site_caps = array();
		$role_captions = array();
		
		$admin_role = $wp_roles->get_role('administrator');
		$main_admin_caps = $admin_role->capabilities;
		
		if ( defined('PRESSPERMIT_ACTIVE') )
			$main_pp_only = (array) pp_capabilities_get_permissions_option( 'supplemental_role_defs' );
			//$pp_only[]= $newrole;
	
		foreach( $autocreate_roles as $role_name ) {
			if ( $role = get_role( $role_name ) ) {
				$main_site_caps[$role_name] = $role->capabilities;
				$role_captions[$role_name] = $wp_roles->role_names[$role_name];
			}
		}
		
		switch_to_blog($new_blog_id);
		( method_exists( $wp_roles, 'for_site' ) ) ? $wp_roles->for_site() : $wp_roles->reinit();
		
		if ( defined('PRESSPERMIT_ACTIVE') ) {
			pp_refresh_options();
			$blog_pp_only = (array) pp_capabilities_get_permissions_option( 'supplemental_role_defs' );
		}
			
		foreach( $main_site_caps as $role_name => $caps ) {
			if ( $blog_role = $wp_roles->get_role( $role_name ) ) {
				$stored_role_caps = ( ! empty($blog_role->capabilities) && is_array($blog_role->capabilities) ) ? array_intersect( $blog_role->capabilities, array(true, 1) ) : array();

				// Find caps to add and remove
				$add_caps = array_diff_key($caps, $stored_role_caps);
				$del_caps = array_intersect_key( array_diff_key($stored_role_caps, $caps), $main_admin_caps );	// don't mess with caps that are totally unused on main site
				
				// Add new capabilities to role
				foreach ( $add_caps as $cap => $grant )
					$blog_role->add_cap($cap);

				// Remove capabilities from role
				foreach ( $del_caps as $cap => $grant)
					$blog_role->remove_cap($cap);
			} else {
				$wp_roles->add_role( $role_name, $role_captions[$role_name], $caps );
			}
			
			if ( defined('PRESSPERMIT_ACTIVE') ) {
				if ( in_array( $role_name, $main_pp_only ) ) {
					_cme_pp_default_pattern_role( $role_name );
					$blog_pp_only []= $role_name;
				} else
					array_diff( $blog_pp_only, array( $role_name ) );
			}
		}
		
		if ( defined('PRESSPERMIT_ACTIVE') ) {
			pp_capabilities_update_permissions_option('supplemental_role_defs', $blog_pp_only);
		}

		restore_current_blog();
		( method_exists( $wp_roles, 'for_site' ) ) ? $wp_roles->for_site() : $wp_roles->reinit();
		
		if ( defined('PRESSPERMIT_ACTIVE') )
			pp_refresh_options();
	}
}

