<?php
/*
 * PublishPress Capabilities [Free]
 * 
 * Process updates to Type-Specific Types / Taxonomies, Detailed Taxonomies
 * 
 */

function _cme_update_pp_usage() {
	static $updated;
	if ( ! empty($updated) ) { return true; }
	
	if ( ! current_user_can( 'manage_capabilities' ) ) {
		return false;
	}
	
	if ( ! empty( $_REQUEST['update_filtered_types']) || ! empty( $_REQUEST['update_filtered_taxonomies']) || ! empty($_REQUEST['update_detailed_taxonomies']) || ! empty( $_REQUEST['SaveRole']) ) {
		// update Press Permit "Filtered Post Types".  This determines whether type-specific capability definitions are forced
		$options = array( 'enabled_post_types', 'enabled_taxonomies', 'detailed_taxonomies' );
		
		$posted = $_POST;
		
		$pp_prefix = (defined('PPC_VERSION') && !defined('PRESSPERMIT_VERSION')) ? 'pp' : 'presspermit';

		foreach( $options as $option_basename ) {
			if ( ! isset( $posted["{$option_basename}-options"] ) )
				continue;
		
			$unselected = array();
			$value = array();
		
			foreach( $posted["{$option_basename}-options"] as $key ) {
				if ( ( 'enabled_taxonomies' == $option_basename ) && ! empty( $posted["detailed_taxonomies-{$key}"] ) && ! empty( $posted['update_detailed_taxonomies']) ) {
					// if Detailed is selected, also select Type-Specific
					$posted["enabled_taxonomies-{$key}"] = true;
					$value[$key] = true;
				} elseif ( ( 'detailed_taxonomies' == $option_basename ) && empty( $posted["enabled_taxonomies-{$key}"] ) && ! empty( $posted['update_filtered_taxonomies']) ) {
					// if Enabled is deselected, also deselect Type-Specific
					$unselected[$key] = true;
				} elseif ( empty( $posted["{$option_basename}-$key"] ) ) {
					$unselected[$key] = true;
				} else {
					$value[$key] = true;
				}
			}

			$option_name = ( 'detailed_taxonomies' == $option_basename ) ?  'cme_' . $option_basename : $pp_prefix . '_' . $option_basename;
			
			if ( $current = get_option( $option_name ) ) {
				if ( $current = array_diff_key( $current, $unselected ) )
					$value = array_merge( $current, $value );	// retain setting for any types which were previously enabled for filtering but are currently not registered
			}
			
			$value = stripslashes_deep($value);
			
			update_option( $option_name, $value );
			
			if (in_array($option_name, ['presspermit_enabled_post_types', 'pp_enabled_post_types'])) {
				// ensure smooth transition if Press Permit Core is deactivated
				update_option( 'cme_enabled_post_types', $value );
			}

			if (defined('PRESSPERMIT_ACTIVE') && in_array($option_basename, ['enabled_post_types', 'enabled_taxonomies'])) {
				pp_capabilities_update_permissions_option($option_basename, $value);
			}
			
			$updated = true;
		}
		
		if ( ! empty( $_REQUEST['update_filtered_types']) ) {
			update_option( $pp_prefix . '_define_create_posts_cap', ! empty($_REQUEST['pp_define_create_posts_cap']) );
		}
	}
	
	if ( defined( 'PRESSPERMIT_ACTIVE' ) ) {
		if ( ! empty( $_REQUEST['SaveRole']) ) {
			if ( ! empty( $_REQUEST['role'] ) ) {
				$pp_only = (array) pp_capabilities_get_permissions_option( 'supplemental_role_defs' );
				
				if ( empty($_REQUEST['pp_only_role']) )
					$pp_only = array_diff( $pp_only, array($_REQUEST['role']) );
				else
					$pp_only[]= $_REQUEST['role'];

				pp_capabilities_update_permissions_option('supplemental_role_defs', array_unique($pp_only));

				_cme_pp_default_pattern_role( $_REQUEST['role'] );
			}
		}
		
		if ( $updated ) {
			pp_refresh_options();
		}
	}
	
	return $updated;
}
