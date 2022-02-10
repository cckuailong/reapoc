<?php
/**
 * Process when plugin is activated
 */
function pvc_install(){
	update_option( 'a3_pvc_version', A3_PVC_VERSION );

	// empty pvc_daily table for daily
	wp_schedule_event( time(), 'daily', 'pvc_empty_daily_table_daily_event_hook' );

	\A3Rev\PageViewsCount\A3_PVC::install_database();

	delete_metadata( 'user', 0, $GLOBALS[A3_PVC_PREFIX.'admin_init']->plugin_name . '-' . 'plugin_framework_global_box' . '-' . 'opened', '', true );

	update_option('pvc_just_installed', true);
}

/**
 * Process when plugin is deactivated
 */
function pvc_deactivation() {
	wp_clear_scheduled_hook( 'pvc_empty_daily_table_daily_event_hook' );
}

update_option('a3rev_pvc_plugin', 'a3_page_view_count');
update_option('a3rev_auth_pvc', '');

function a3_pvc_plugin_init() {

	if ( get_option( 'pvc_just_installed' ) ) {
		delete_option( 'pvc_just_installed' );

		// Set Settings Default from Admin Init
		$GLOBALS[A3_PVC_PREFIX.'admin_init']->set_default_settings();

		// Build sass
		$GLOBALS[A3_PVC_PREFIX.'less']->plugin_build_sass();
	}

	// Set up localisation
	a3_pvc_load_plugin_textdomain();
}

add_action( 'init', 'a3_pvc_plugin_init' );

add_action( 'widgets_init', function() {
	register_widget( '\A3Rev\PageViewsCount\Widget\PVC' );
} );

// Add custom style to dashboard
add_action( 'admin_enqueue_scripts', array( '\A3Rev\PageViewsCount\A3_PVC', 'a3_wp_admin' ) );

// Add extra link on left of Deactivate link on Plugin manager page
add_action('plugin_action_links_'.A3_PVC_PLUGIN_NAME, array('\A3Rev\PageViewsCount\A3_PVC', 'settings_plugin_links') );

// Add text on right of Visit the plugin on Plugin manager page
add_filter( 'plugin_row_meta', array('\A3Rev\PageViewsCount\A3_PVC', 'plugin_extra_links'), 10, 2 );


// Need to call Admin Init to show Admin UI
$GLOBALS[A3_PVC_PREFIX.'admin_init']->init();

// Add upgrade notice to Dashboard pages
add_filter( $GLOBALS[A3_PVC_PREFIX.'admin_init']->plugin_name . '_plugin_extension_boxes', array( '\A3Rev\PageViewsCount\A3_PVC', 'plugin_extension_box' ) );

/**
 * On the scheduled action hook, run the function.
 */
add_action( 'pvc_empty_daily_table_daily_event_hook', 'pvc_empty_daily_table_do_daily' );
function pvc_empty_daily_table_do_daily() {
	global $wpdb;
	$wpdb->query("DELETE FROM " . $wpdb->prefix . "pvc_daily WHERE time <= '".date('Y-m-d', strtotime('-2 days'))."'");
}

$pvc_settings = get_option( 'pvc_settings', array( 'position' => 'bottom' ) );
if ( isset( $pvc_settings['position'] ) && 'top' == $pvc_settings['position'] ) {
	add_action('genesis_before_post_content', array('\A3Rev\PageViewsCount\A3_PVC', 'genesis_pvc_stats_echo'));
} else {
	add_action('genesis_after_post_content', array('\A3Rev\PageViewsCount\A3_PVC', 'genesis_pvc_stats_echo'));
}
//add_action('loop_end', array('\A3Rev\PageViewsCount\A3_PVC', 'pvc_stats_echo'), 9);
add_filter('the_content', array('\A3Rev\PageViewsCount\A3_PVC','pvc_stats_show'), 8);
add_filter('the_excerpt', array('\A3Rev\PageViewsCount\A3_PVC','excerpt_pvc_stats_show'), 8);
//add_filter('get_the_excerpt', array('\A3Rev\PageViewsCount\A3_PVC','excerpt_pvc_stats_show'), 8);

// Add ajax script to load page view count stats into footer
add_action( 'wp_enqueue_scripts', array( '\A3Rev\PageViewsCount\A3_PVC', 'register_plugin_scripts' ) );

// Check upgrade functions
add_action('plugins_loaded', 'pvc_lite_upgrade_plugin');
function pvc_lite_upgrade_plugin () {

	if(version_compare(get_option('a3_pvc_version'), '1.2') === -1){
		update_option('a3_pvc_version', '1.2');
		\A3Rev\PageViewsCount\A3_PVC::upgrade_version_1_2();
	}

	if(version_compare(get_option('a3_pvc_version'), '1.3.5') === -1){
		update_option('a3_pvc_version', '1.3.5');

		wp_schedule_event( strtotime( date('Y-m-d'). ' 00:00:00' ), 'daily', 'pvc_empty_daily_table_daily_event_hook' );
		global $wpdb;
		$sql = "ALTER TABLE ". $wpdb->prefix . "pvc_daily  CHANGE `id` `id` BIGINT NOT NULL AUTO_INCREMENT";
		$wpdb->query($sql);
		$sql = "ALTER TABLE ". $wpdb->prefix . "pvc_total  CHANGE `id` `id` BIGINT NOT NULL AUTO_INCREMENT";
		$wpdb->query($sql);
	}
	if(version_compare(get_option('a3_pvc_version'), '1.3.6') === -1){
		update_option('a3_pvc_version', '1.3.6');

		$pvc_settings = get_option( 'pvc_settings' );
		if ( isset( $pvc_settings['post_types'] ) && is_array( $pvc_settings['post_types'] ) && count( $pvc_settings['post_types'] ) > 0 ) {
			$post_types_new = array();
			foreach ( $pvc_settings['post_types'] as $post_type ) {
				$post_types_new[$post_type] = $post_type;
			}
			$pvc_settings['post_types'] = $post_types_new;
			update_option( 'pvc_settings', $pvc_settings );
		}
	}

	if ( version_compare( get_option('a3_pvc_version'), '1.4.0' ) === -1 ) {
		update_option('a3_pvc_version', '1.4.0');

		// Set Settings Default from Admin Init
		$pvc_settings = get_option( 'pvc_settings' );
		$pvc_settings['show_on_excerpt_content'] = 'yes';

		update_option( 'pvc_settings', $pvc_settings );
	}

	if ( version_compare( get_option('a3_pvc_version'), '2.0.0' ) === -1 ) {
		update_option('a3_pvc_version', '2.0.0');

		// Build sass
		$GLOBALS[A3_PVC_PREFIX.'less']->plugin_build_sass();
	}

	update_option( 'a3_pvc_version', A3_PVC_VERSION );

}

if ( 'responsi' === get_template() ) {
   remove_filter('the_content', array(
       '\A3Rev\PageViewsCount\A3_PVC',
       'pvc_stats_show'
   ), 8);
   remove_filter('the_excerpt', array(
       '\A3Rev\PageViewsCount\A3_PVC',
       'excerpt_pvc_stats_show'
   ), 8);

   if (!function_exists( 'add_view_count')) {
       function add_view_count()
       {
           $postid = get_the_ID();
           $html   = '';
           $class  = '';
           if (!is_single() && !is_page() && !is_404()) {
               $class = ' custom_box';
           }
           if (function_exists('pvc_check_exclude') && pvc_check_exclude())
               return '';

           if (function_exists('pvc_stats_update'))
               $html .= '<div class="add_view_count' . $class . '">' . pvc_stats_update($postid, 0) . '</div>';
           echo $html;
       }
   }

   if (!function_exists( 'add_view_count_for_theme')) {
       function add_view_count_for_theme()
       {
           remove_filter('the_content', array( '\A3Rev\PageViewsCount\A3_PVC', 'pvc_stats_show'), 8);
           remove_filter('the_excerpt', array( '\A3Rev\PageViewsCount\A3_PVC', 'excerpt_pvc_stats_show'), 8);
           if ( !is_admin() && !is_home() ) {
           		global $pvc_settings;
           		if ( 'top' == $pvc_settings['position'] ) {
					add_action('responsi_loop_before', 'add_view_count', 30);
				} else {
					add_action('responsi_loop_after', 'add_view_count', 30);
				}
           }
       }
   }

   remove_action('wp_head', 'add_view_count_for_theme');
   add_action('wp_head', 'add_view_count_for_theme');
}

function pvc_ict_t_e( $name, $string ) {
	global $pvc_wpml;
	$string = ( function_exists('icl_t') ? icl_t( $pvc_wpml->plugin_wpml_name, $name, $string ) : $string );
	
	echo $string;
}

function pvc_ict_t__( $name, $string ) {
	global $pvc_wpml;
	$string = ( function_exists('icl_t') ? icl_t( $pvc_wpml->plugin_wpml_name, $name, $string ) : $string );
	
	return $string;
}
