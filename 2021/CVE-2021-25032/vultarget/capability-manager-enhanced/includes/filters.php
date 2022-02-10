<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/*
 * PublishPress Capabilities [Free]
 * 
 * Load general purpose filters which need to execute for any URL, even front end
 * 
 */

/**
 * class CME_Extensions
 * 
 * Load filters and actions for integration with third party plugins
 */
class CME_Extensions {
	var $extensions = array();
	
	function add( $object ) {
		if ( ! is_object( $object ) ) return;
		
		$this->extensions[ get_class( $object ) ] = $object;
	}
}

global $cme_extensions;
$cme_extensions = new CME_Extensions();

add_filter( 'map_meta_cap', '_cme_remap_term_meta_cap', 5, 4 );

add_action( 'admin_head', '_cme_publishpress_roles_js');

if ( defined( 'WC_PLUGIN_FILE' ) ) {
	require_once ( dirname(__FILE__) . '/filters-woocommerce.php' );
	$cme_extensions->add( new CME_WooCommerce() );
}

if (!defined('CME_DISABLE_WP_EDIT_PUBLISHED_WORKAROUND')) {
	global $wp_version;
	if (version_compare($wp_version, '4.9.7', '>=')) { // avoid any issues with old REST API implementations
		require_once (dirname(__FILE__) . '/filters-wp_rest_workarounds.php');
		new PublishPress\Capabilities\WP_REST_Workarounds();
	}
}

if ( is_admin() ) {
	global $pagenow;
	if ( 'edit.php' == $pagenow ) {
		require_once ( dirname(__FILE__) . '/filters-admin.php' );
		new CME_AdminMenuNoPrivWorkaround();
	}
}

add_filter('plugin_action_links_' . plugin_basename(CME_FILE), '_cme_fltPluginActionLinks', 10, 2);

add_action('plugins_loaded', '_cme_migrate_pp_options');

add_filter('cme_filterable_post_types', '_cme_filterable_post_types');

function _cme_filterable_post_types($post_type_objects) {
	if ($advgb_profiles = get_post_type_object('advgb_profiles')) {
		$post_type_objects['advgb_profiles'] = $advgb_profiles;
	}

	return $post_type_objects;
}

function _cme_publishpress_roles_js() {
	if (defined('PUBLISHPRESS_VERSION') && ((strpos($_SERVER['REQUEST_URI'], 'page=pp-manage-roles')))) {
		require_once(dirname(__FILE__) . '/publishpress-roles.php');
		CME_PublishPressRoles::scripts();  // @todo: .js
	}
}

// Capabilities previously stored, retrieved settings from 'pp_' option names. Now using 'presspermit_' option names unless PressPermit 2.6.x or older is activated, but need to migrate previous settings
function _cme_migrate_pp_options() {
	if (!get_option('cme_pp_options_migrated') && get_option('cme_enabled_post_types')) {
		foreach(['enabled_post_types', 'enabled_taxonomies', 'define_create_posts_cap'] as $option_basename) {
			$presspermit_options = get_option("presspermit_{$option_basename}");
			
			if (!$presspermit_options) {
				$prefix = ('enabled_post_types' == $option_basename) ? 'cme_' : 'pp_';
				
				if ($option_val = get_option("{$prefix}_{$option_basename}")) {
					update_option("presspermit_{$option_basename}", $option_val);
				}
			}
		}

		update_option('cme_pp_options_migrated', true);
	}
}


// allow edit_terms, delete_terms, assign_terms capabilities to function separately from manage_terms
function _cme_remap_term_meta_cap ( $caps, $cap, $user_id, $args ) {
	global $current_user, $cme_cap_helper;
	
	if ( ! empty( $cme_cap_helper ) ) {
		$cap_helper = $cme_cap_helper;
	} else {
		global $ppce_cap_helper;
		if ( ! empty( $ppce_cap_helper ) ) {
			$cap_helper = $ppce_cap_helper;
		}
	}
	
	if ( empty($cap_helper) || empty( $cap_helper->all_taxonomy_caps[$cap] ) ) {
		return $caps;
	}
	
	if ( ! $enabled_taxonomies = array_intersect( cme_get_assisted_taxonomies(), cme_get_detailed_taxonomies() ) ) {
		return $caps;
	}
	
	// meta caps
	switch ( $cap ) {
		// If detailed taxonomy capabilities are enabled for categories or tags, don't also require default capability for term assignment / deletion
		case 'assign_categories':
			if (in_array('category', $enabled_taxonomies)) {
				$caps = array_diff($caps, ['edit_posts']);
			}
			break;

		case 'assign_post_tags':
			if (in_array('post_tag', $enabled_taxonomies)) {
				$caps = array_diff($caps, ['edit_posts']);
			}
			break;

		case 'delete_categories':
			if (in_array('category', $enabled_taxonomies)) {
				$caps = array_diff($caps, ['manage_categories']);
			}
			break;

		case 'delete_post_tags':
			if (in_array('post_tag', $enabled_taxonomies)) {
				$caps = array_diff($caps, ['manage_categories']);
			}
			break;

		case 'edit_term':
		case 'delete_term':
		case 'assign_term':
			$tx_cap = $cap . 's';
		
			if ( ! is_array($args) || empty($args[0]) ) {
				return $caps;
			}
			
			if ( $term = get_term( $args[0] ) ) {
				if ( in_array( $term->taxonomy, $enabled_taxonomies ) ) {
					if ( $tx_obj = get_taxonomy( $term->taxonomy ) ) {
						
						// If this taxonomy is set for distinct capabilities, we don't want manage_terms capability to be implicitly assigned.
						if ( empty( $current_user->allcaps[$tx_obj->cap->manage_terms] ) ) {
							$caps = array_diff( $caps, (array) $tx_obj->cap->manage_terms );
						}
						$caps[]= $tx_obj->cap->$tx_cap;
					}
				}
			}
			break;
		default:
	}
	
	// primitive caps
	foreach( $enabled_taxonomies as $taxonomy ) {
		if ( ! $tx_obj = get_taxonomy( $taxonomy ) ) {
			continue;
		}
		
		foreach( array( 'edit_terms', 'delete_terms', 'assign_terms' ) as $cap_prop ) {
			if ( $cap == $tx_obj->cap->$cap_prop ) {
				
				// If this taxonomy is set for distinct capabilities, we don't want manage_terms capability to be implicitly assigned.
				if ( empty( $current_user->allcaps[$tx_obj->cap->manage_terms] ) ) {
					$caps = array_diff( $caps, (array) $tx_obj->cap->manage_terms );
				}
				
				$caps[]= $tx_obj->cap->$cap_prop;
				return $caps;
			}
		}
	}
	
	return $caps;
}

// Note: this intentionally shares "presspermit_enabled_post_types" option with PublishPress Permissions 
function cme_get_assisted_post_types() {
	$type_args = array( 'public' => true, 'show_ui' => true );
	
	$post_types = get_post_types( $type_args, 'names', 'or' );
	
	$omit_types = apply_filters('presspermit_unfiltered_post_types', ['forum', 'topic', 'reply', 'wp_block', 'customize_changeset']);
	$omit_types = (defined('PP_CAPABILITIES_NO_LEGACY_FILTERS')) ? $omit_types : apply_filters('pp_unfiltered_post_types', $omit_types);

	if ($omit_types) {
		$post_types = array_diff_key( $post_types, array_fill_keys( (array) $omit_types, true ) );
	}
	
	$option_name = (defined('PPC_VERSION') && !defined('PRESSPERMIT_VERSION')) ? 'pp_enabled_post_types' : 'presspermit_enabled_post_types';
	$enabled = (array) get_option( $option_name, array( 'post' => true, 'page' => true ) );

	$post_types = array_intersect( $post_types, array_keys( array_filter( $enabled ) ) );
	
	return apply_filters( 'cme_assisted_post_types', $post_types, $type_args );
}

// Note: this intentionally does NOT share Press Permit' option name, for back compat reasons
// Enabling filtered taxonomies in PP previously did not cause the edit_terms, delete_terms, assign_terms capabilities to be enforced
function cme_get_assisted_taxonomies() {
	$tx_args = ['public' => true, 'show_ui' => true];
	$taxonomies = apply_filters('cme_filterable_taxonomies', get_taxonomies($tx_args, 'object', 'or'));
	$taxonomies = array_combine(array_keys($taxonomies), array_keys($taxonomies));

	if ($omit_taxonomies = apply_filters('pp_unfiltered_taxonomies', [])) {
		$taxonomies = array_diff($taxonomies, (array) $omit_taxonomies);
	}
	
	$option_name = (defined('PPC_VERSION') && !defined('PRESSPERMIT_VERSION')) ? 'pp_enabled_taxonomies' : 'presspermit_enabled_taxonomies';
	$enabled = (array) get_option( $option_name, []);
	$taxonomies = array_intersect( $taxonomies, array_keys( array_filter( $enabled ) ) );
	
	return apply_filters( 'cme_assisted_taxonomies', $taxonomies, $tx_args );
}

function cme_get_detailed_taxonomies() {
	$tx_args = ['public' => true, 'show_ui' => true];
	$taxonomies = apply_filters('cme_filterable_taxonomies', get_taxonomies($tx_args, 'object', 'or'));
	$taxonomies = array_combine(array_keys($taxonomies), array_keys($taxonomies));

	if ($omit_taxonomies = apply_filters('pp_unfiltered_taxonomies', [])) {
		$taxonomies = array_diff($taxonomies, (array) $omit_taxonomies);
	}
	
	$enabled = (array) get_option('cme_detailed_taxonomies', []);
	$taxonomies = array_intersect( $taxonomies, array_keys( array_filter( $enabled ) ) );
	
	return apply_filters( 'cme_detailed_taxonomies', $taxonomies, $tx_args );
}

function _cme_get_plural( $slug, $type_obj = false ) {
	if ( $type_obj && ! empty( $type_obj->rest_base ) && ( $type_obj->rest_base != $slug ) && ( $type_obj->rest_base != "{$slug}s" ) ) {
		// Use plural form from rest_base
		if ( $pos = strpos( $type_obj->rest_base, '/' ) ) {
			return sanitize_key( substr( $type_obj->rest_base, 0, $pos + 1 ) );
		} else {
			return sanitize_key( $type_obj->rest_base );
		}
	} else {
		require_once ( dirname(__FILE__) . '/inflect-cme.php' );
		return sanitize_key( CME_Inflect::pluralize( $slug ) );	
	}
}

function _cme_fltPluginActionLinks($links, $file)
{
	if ($file == plugin_basename(CME_FILE)) {
		if (!is_network_admin()) {
			$links[] = "<a href='" . admin_url("admin.php?page=pp-capabilities") . "'>" . __('Edit Roles', 'capsman-enhanced') . "</a>";
		}
	}

	return $links;
}
