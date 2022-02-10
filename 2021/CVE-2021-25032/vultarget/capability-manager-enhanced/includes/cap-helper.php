<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
 * PublishPress Capabilities [Free]
 * 
 * For post types and taxonomies with "Type-Specific Capabilities" enabled, modify defined capabilities to be unique
 * 
 */

// @todo: port improvements back to PP Core

class CME_Cap_Helper {
	var $all_taxonomy_caps = array();	// $all_taxonomy_caps = array of capability names
	var $all_type_caps = array();		// $all_type_caps = array of capability names
	var $processed_types = array();
	var $processed_taxonomies = array();
	
	function __construct() {
		$this->refresh();
	}
	
	function refresh() {
		$this->force_distinct_post_caps();
		
		// Work around bug in More Taxonomies (and possibly other plugins) where category taxonomy is overriden without setting it public
		foreach( array( 'category', 'post_tag' ) as $taxonomy ) {
			global $wp_taxonomies;
			if ( isset( $wp_taxonomies[$taxonomy] ) )
				$wp_taxonomies[$taxonomy]->public = true;
		}
		
		$this->force_distinct_taxonomy_caps();
	}

	function force_distinct_post_caps() {  // for selected post types (as stored in option array presspermit_enabled_post_types)
		global $wp_post_types, $wp_roles;
		
		$core_meta_caps = array_fill_keys( array( 'read_post', 'edit_post', 'delete_post' ), true );
		
		$append_caps = array( 'edit_published_posts' => 'edit_posts', 'edit_private_posts' => 'edit_posts', 'delete_posts' => 'edit_posts', 'delete_others_posts' => 'delete_posts', 'delete_published_posts' => 'delete_posts', 'delete_private_posts' => 'delete_posts', 'read' => 'read' );
		
		$pp_prefix = (defined('PPC_VERSION') && !defined('PRESSPERMIT_VERSION')) ? 'pp' : 'presspermit';

		if ( get_option("{$pp_prefix}_define_create_posts_cap") ) {
			foreach( array( 'post', 'page' ) as $post_type ) {
				if ( $wp_post_types[$post_type]->cap->create_posts == $wp_post_types[$post_type]->cap->edit_posts ) {
					$wp_post_types[$post_type]->cap->create_posts = "create_{$post_type}s";
				}
			}
			
			foreach( cme_get_assisted_post_types() as $post_type ) {
				if ( ! in_array( $post_type, array( 'post', 'page' ) ) ) {
					if ( $wp_post_types[$post_type]->cap->create_posts == $wp_post_types[$post_type]->cap->edit_posts ) {
						$wp_post_types[$post_type]->cap->create_posts = str_replace( 'edit_', 'create_', $wp_post_types[$post_type]->cap->edit_posts );
					}
				}
			}
			
			$append_caps['create_posts'] = 'create_posts';
		}
		
		// count the number of post types that use each capability
		foreach( $wp_post_types as $post_type => $type_obj ) {
			foreach( array_unique( (array) $type_obj->cap ) as $cap_name ) {
				if ( ! isset( $this->all_type_caps[$cap_name] ) ) {
					$this->all_type_caps[$cap_name] = 1;
				} else {
					$this->all_type_caps[$cap_name]++;
				}
			}
		}
		
		$post_caps = (array) $wp_post_types['post']->cap;
		$page_caps = ( isset( $wp_post_types['page'] ) ) ? (array) $wp_post_types['page']->cap : array();
		
		$enabled_types = array_diff( cme_get_assisted_post_types(), $this->processed_types );
		
		// post types which are enabled for PP filtering must have distinct type-related cap definitions
		foreach( $enabled_types as $post_type ) {
			// append missing capability definitions
			foreach( $append_caps as $prop => $default ) {
				if ( ! isset( $wp_post_types[$post_type]->cap->$prop ) ) {
					$wp_post_types[$post_type]->cap->$prop = ( 'read' == $prop ) ? 'read' : $wp_post_types[$post_type]->cap->$default;
				}
			}

			$wp_post_types[$post_type]->map_meta_cap = true;
			
			$type_caps = array_diff_key( (array) $wp_post_types[$post_type]->cap, $core_meta_caps );
			
			$cap_base = ( 'attachment' == $post_type ) ? 'file' : $post_type;
			
			$cap_properties = array_keys( $type_caps );
			
			if ( 'attachment' == $post_type ) {  
				$cap_properties = array_diff( $cap_properties, array( 'publish_posts', 'edit_published_posts', 'delete_published_posts', 'edit_private_posts', 'delete_private_posts', 'read_private_posts' ) );
			}
			
			// 'read' is not converted to a type-specific equivalent, so disregard it for perf. 
			$cap_properties = array_diff( $cap_properties, array( 'read' ) );
			
			foreach( $cap_properties as $k => $cap_property ) {
				// If a cap property is set to one of the generic post type's caps, we will replace it
				if ( ( 'post' != $post_type ) && in_array( $type_caps[$cap_property], $post_caps ) ) {
					continue;
				}
				
				if ( ( 'page' != $post_type ) && in_array( $type_caps[$cap_property], $page_caps ) ) {
					continue;
				}
				
				// If a cap property is non-generic and not used by any other post types, keep it as is
				if ( $this->all_type_caps[ $type_caps[$cap_property] ] <= 1 ) {
					unset( $cap_properties[$k] );
			
				// If a cap property is used by any other post types, still keep it if it is the standard type-specific capability form for this post type
				} elseif ( ( $type_caps[$cap_property] == str_replace( "_posts", "_{$post_type}s", $cap_property ) )
						|| ( $type_caps[$cap_property] == str_replace( "_pages", "_{$post_type}s", $cap_property ) ) ) {
					
					unset( $cap_properties[$k] );
				
				// If a cap property is used by any other post types, still keep it if it is the custom pluralized type-specific capability form for this post type
				} else {
					$plural_type = _cme_get_plural( $post_type, $wp_post_types[$post_type] );
					if ( ( $type_caps[$cap_property] == str_replace( "_posts", "_{$plural_type}", $cap_property ) )
						|| ( $type_caps[$cap_property] == str_replace( "_pages", "_{$plural_type}", $cap_property ) ) ) {

						unset( $cap_properties[$k] );
					}
				}
			}

			if ( ! $cap_properties ) { 
				// This post type has no defaulted cap properties that need to be made type-specific.
				continue;
			}

			// Default plural slug
			//$plural_type = "{$cap_base}s";
			
			$plural_type = _cme_get_plural( $post_type, $wp_post_types[$post_type] );
			
			if ( "{$cap_base}s" != $plural_type ) {
				// If any role already has capabilities based on simple plural form, keep using that instead
				foreach ( $wp_roles as $role ) {
					foreach( array_keys( $type_caps ) as $cap_property ) {
						$generic_type = ( strpos( $cap_property, '_pages' ) ) ? 'page' : 'post';
						
						$simple_plural = str_replace( "_{$generic_type}s", "_{$cap_base}s", $cap_property );
						
						if ( isset( $role->capabilities[$simple_plural] ) ) {
							// A simple plural capability was manually stored to a role, so stick with that form
							$plural_type = "{$cap_base}s";
							break 2;
						}
					}
				}
			}
			
			// Replace "edit_posts" and other post type caps with an equivalent for this post type, using pluralization determined above.
			// If a this is a problem, register the post type with an array capability_type arg including the desired plural form.
			// It is also possible to modify existing $wp_post_types[$post_type]->cap values by hooking to the init action at priority 40.
			foreach( $cap_properties as $cap_property ) {
				// create_posts capability may be defaulted to "edit_posts" / "edit_pages"
				$generic_type = ( strpos( $cap_property, '_pages' ) ) ? 'page' : 'post';

				$target_cap_property = ( 'create_posts' == $cap_property ) ? $wp_post_types[$generic_type]->cap->$cap_property : $cap_property;
			
				if ( $plural_type != "{$cap_base}s" ) {
					// Since plural form is not simple, first replace plurals ('edit_posts' > 'edit_doohickies')
					$wp_post_types[$post_type]->cap->$cap_property = str_replace( "_{$generic_type}s", "_{$plural_type}", $target_cap_property );
				} else {
					// Replace based on simple plural ('edit_posts' > 'edit_doohickys')
					$wp_post_types[$post_type]->cap->$cap_property = str_replace( "_{$generic_type}", "_{$cap_base}", $target_cap_property );
				}
			}

			// Force distinct capability_type. This may be an array with plural form in second element (but not useful here if set as default 'post' / 'posts' ).
			// Some caution here against changing the variable data type. Although array is supported, other plugin code may assume string.
			if ( is_array( $wp_post_types[$post_type]->capability_type ) ) {
				$wp_post_types[$post_type]->capability_type = array( $post_type, $plural_type );

			} elseif ( in_array( $wp_post_types[$post_type]->capability_type, array('post','page') ) ) {
				$wp_post_types[$post_type]->capability_type = $post_type;
			}
			
			$type_caps = array_diff_key( (array) $wp_post_types[$post_type]->cap, $core_meta_caps );

			$wp_post_types[$post_type]->cap = (object) array_merge( (array) $wp_post_types[$post_type]->cap, $type_caps );
			
			//$this->all_type_caps = array_merge( $this->all_type_caps, array_fill_keys( $type_caps, true ) );
			
			foreach( array_unique( (array) $wp_post_types[$post_type]->cap ) as $cap_name ) {
				if ( ! isset( $this->all_type_caps[$cap_name] ) ) {
					$this->all_type_caps[$cap_name] = 1;
				} else {
					$this->all_type_caps[$cap_name]++;
				}
			}
			
		} // end foreach post type
		
		$this->processed_types = array_merge( $this->processed_types, $enabled_types );
		
		// need this for casting to other types even if "post" type is not enabled for PP filtering
		$wp_post_types['post']->cap->set_posts_status = 'set_posts_status';
		
		if ((is_multisite() && is_super_admin()) || current_user_can('administrator') || current_user_can('pp_administer_content')) {  // @ todo: support restricted administrator
			global $current_user;
			$current_user->allcaps = array_merge( $current_user->allcaps, array_fill_keys( array_keys( $this->all_type_caps ), true ) );
			
			global $pp_current_user;
			if ( ! empty( $pp_current_user ) ) {
				$pp_current_user->allcaps = array_merge( $pp_current_user->allcaps, array_fill_keys( array_keys( $this->all_type_caps ), true ) );
			}
		}
		
		do_action( 'cme_distinct_post_capabilities', $enabled_types );
	}
	
	function force_distinct_taxonomy_caps() {
		global $wp_taxonomies, $wp_roles;
	
		$use_taxonomies = array_diff( cme_get_assisted_taxonomies(), $this->processed_taxonomies );
		$detailed_taxonomies = cme_get_detailed_taxonomies();
		
		$tx_specific_caps = array( 'manage_terms' => 'manage_terms', 'edit_terms' => 'manage_terms', 'delete_terms' => 'manage_terms' );
		$tx_detail_caps = array( 'edit_terms' => 'edit_terms', 'delete_terms' => 'delete_terms', 'assign_terms' => 'assign_terms' );
		
		$core_tx_caps = array();
		$this->all_taxonomy_caps = array();
		
		// currently, disallow category and post_tag cap use by selected custom taxonomies, but don't require category and post_tag to have different caps
		$core_taxonomies = array( 'category' );
		foreach( $core_taxonomies as $taxonomy ) {
			foreach( array_keys($tx_specific_caps) as $cap_property ) {
				$core_tx_caps[ $wp_taxonomies[$taxonomy]->cap->$cap_property ] = true;
			}
		}
		
		// count the number of taxonomies that use each capability
		foreach( $wp_taxonomies as $taxonomy => $tx_obj ) {
			//$this_tx_caps = array_unique( (array) $tx_obj->cap );
			$this_tx_caps = (array) $tx_obj->cap;

			foreach( $this_tx_caps as $cap_name ) {
				if ( ! isset( $this->all_taxonomy_caps[$cap_name] ) ) {
					$this->all_taxonomy_caps[$cap_name] = 1;
				} else {
					$this->all_taxonomy_caps[$cap_name]++;
				}
			}
		}
		
		foreach( array_keys($wp_taxonomies) as $taxonomy ) {
			if ( 'yes' == $wp_taxonomies[$taxonomy]->public ) {	// clean up a GD Taxonomies quirk (otherwise wp_get_taxonomy_object will fail when filtering for public => true)
				$wp_taxonomies[$taxonomy]->public = true;
			
			} elseif ( ( '' === $wp_taxonomies[$taxonomy]->public ) && ( ! empty( $wp_taxonomies[$taxonomy]->query_var_bool ) ) ) { // clean up a More Taxonomies quirk (otherwise wp_get_taxonomy_object will fail when filtering for public => true)
				$wp_taxonomies[$taxonomy]->public = true;
			}
			
			$tx_caps = (array) $wp_taxonomies[$taxonomy]->cap;
			
			if ( ( ! in_array($taxonomy, $use_taxonomies) || empty( $wp_taxonomies[$taxonomy]->public ) ) && ( 'nav_menu' != $taxonomy ) )
				continue;

			if ( ! in_array( $taxonomy, $core_taxonomies ) ) {
				// Default plural slug
				//$plural_type = "{$taxonomy}s";
				
				$plural_type = _cme_get_plural( $taxonomy, $wp_taxonomies[$taxonomy] );

				if ( "{$taxonomy}s" != $plural_type ) {
					// ... unless any role already has capabilities based on simple plural form
					foreach ( $wp_roles as $role ) {
						foreach( array_keys( $tx_caps ) as $cap_property ) {
							$simple_plural = str_replace( "_terms", "_{$taxonomy}s", $cap_property );
							
							if ( isset( $role->capabilities[$simple_plural] ) ) {
								// A simple plural capability was manually stored to a role, so stick with that form
								$plural_type = "{$taxonomy}s";
								break 2;
							}
						}
					}
				}
				
				// First, force taxonomy-specific capabilities.
				// (Don't allow any capability defined for this taxonomy to match any capability defined for category or post tag (unless this IS category or post tag)
				foreach( $tx_specific_caps as $cap_property => $replacement_cap_format ) {
					// If this capability is also defined as another taxonomy cap, replace it
					if ( ! empty($tx_caps[$cap_property]) && ( $this->all_taxonomy_caps[ $tx_caps[$cap_property] ] > 1 ) ) { // note: greater than check is on array value, not count
						
						// ... but leave it alone if it is a standard taxonomy-specific cap for this taxonomy
						if ( ( $tx_caps[$cap_property] != str_replace( '_terms', "_{$plural_type}", $cap_property ) )
						&& ( $tx_caps[$cap_property] != str_replace( '_terms', "_{$taxonomy}s", $cap_property ) ) ) {
							
							$wp_taxonomies[$taxonomy]->cap->$cap_property = str_replace( '_terms', "_{$plural_type}", $replacement_cap_format );
						}
					}
				}
				$tx_caps = (array) $wp_taxonomies[$taxonomy]->cap;


				// Optionally, also force edit_terms and delete_terms to be distinct from manage_terms, and force a distinct assign_terms capability
				if ( in_array( $taxonomy, $detailed_taxonomies ) ) {
					foreach( $tx_detail_caps as $cap_property => $replacement_cap_format ) {
						$tx_cap_usage = array_count_values($tx_caps);

						// If a unique edit/delete capability is already defined, don't change the definition
						if (!empty($tx_caps[$cap_property]) 
						&& (empty($this->all_taxonomy_caps[$tx_caps[$cap_property]]) || $this->all_taxonomy_caps[$tx_caps[$cap_property]] == 1) 
						&& ($tx_cap_usage[$tx_caps[$cap_property]] == 1)
						&& !defined('CAPSMAN_LEGACY_DETAILED_TAX_CAPS')
						) {
							// If roles were already configured with generated capability name, migrate to custom predefined capability name
							$custom_detailed_taxonomy_caps = true;
							$generated_cap_name = str_replace('_terms', "_{$plural_type}", $replacement_cap_format);

							if (!get_option("cme_migrated_taxonomy_caps")) {
								foreach ($wp_roles->roles as $role_name => $role) {
									if (!empty($role['capabilities'][$generated_cap_name])) {
										$_role = get_role($role_name);
										$_role->add_cap($tx_caps[$cap_property]);
										$_role->remove_cap($generated_cap_name);
									}
								}
							}

							continue;
						}

						if ( ! empty( $this->all_taxonomy_caps[ $tx_caps[$cap_property] ] ) ) {
							// assign_terms is otherwise not forced taxonomy-distinct 
							$wp_taxonomies[$taxonomy]->cap->$cap_property = str_replace( '_terms', "_{$plural_type}", $replacement_cap_format );
							break;
						}
						
						foreach( $tx_caps as $other_cap_property => $other_cap ) {
							if ( $other_cap_property == $cap_property ) {
								continue;
							}
							
							if ( $other_cap == $tx_caps[$cap_property] ) {
								$wp_taxonomies[$taxonomy]->cap->$cap_property = str_replace( '_terms', "_{$plural_type}", $replacement_cap_format );
								break;
							}
						}
					}

					if (!empty($custom_detailed_taxonomy_caps)) {
						update_option("cme_migrated_taxonomy_caps", true);
					}
				}
				
				$tx_caps = (array) $wp_taxonomies[$taxonomy]->cap;
			}
			
			foreach( array_unique( $tx_caps ) as $cap_name ) {
				if ( ! isset( $this->all_taxonomy_caps[$cap_name] ) ) {
					$this->all_taxonomy_caps[$cap_name] = 1;
				} else {
					$this->all_taxonomy_caps[$cap_name]++;
				}
			}
		}
		
		$this->all_taxonomy_caps = array_merge( $this->all_taxonomy_caps, array( 'assign_term' => true ) );
		
		if ((is_multisite() && is_super_admin()) || current_user_can('administrator') || current_user_can('pp_administer_content')) {  // @ todo: support restricted administrator
			global $current_user;
			$current_user->allcaps = array_merge( $current_user->allcaps, array_fill_keys( array_keys( $this->all_taxonomy_caps ), true ) );
			
			global $pp_current_user;
			if ( ! empty( $pp_current_user ) ) {
				$pp_current_user->allcaps = array_merge( $pp_current_user->allcaps, array_fill_keys( array_keys( $this->all_taxonomy_caps ), true ) );
			}
		}
		
		// make sure Nav Menu Managers can also add menu items
		global $wp_taxonomies;
		$wp_taxonomies['nav_menu']->cap->assign_terms = 'manage_nav_menus';
		
		$this->processed_taxonomies = array_merge( $this->processed_taxonomies, $use_taxonomies );
	}
} // end class CME_Cap_Helper
