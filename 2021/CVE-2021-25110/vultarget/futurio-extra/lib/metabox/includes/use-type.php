<?php 
/*
|| --------------------------------------------------------------------------------------------
|| Metabox Use-type Use Type
|| --------------------------------------------------------------------------------------------
||
|| @package		Dilaz Metabox
|| @subpackage	Use Type
|| @since		Dilaz Metabox 2.0
|| @author		WebDilaz Team, http://webdilaz.com
|| @copyright	Copyright (C) 2017, WebDilaz LTD
|| @link		http://webdilaz.com/metaboxes
|| @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
|| 
*/

defined('ABSPATH') || exit;

$use_type_parameters = array();
$parameters['use_type_error'] = false;

if (isset($parameters['use_type']) && $parameters['use_type'] == 'theme') {
	
	# check if its plugin when in theme use type
	if (strpos(dirname(__FILE__), '\plugins\\') !== false) {
		
		add_action('admin_notices', function() {
			echo '<div id="message" class="error"><p><strong>'. sprintf( __( 'Wrong use-type for metabox options. Please set "<em>use_type</em>" parameter to "<em>plugin</em>" in "<em>%s</em>".', 'dilaz-metabox' ), 'wp-content'. wp_normalize_path(explode('wp-content', dirname(__DIR__))[1]) .'/config.php' ) .'</strong></p></div>';
		});
		
		$parameters['use_type_error'] = true;
		
	} else {
		
		$theme_object  = wp_get_theme();
		$theme_name    = is_child_theme() ? $theme_object['Template'] : $theme_object['Name'];
		$theme_name_lc = strtolower($theme_name);
		$theme_version = $theme_object['Version'];
		$theme_uri     = get_template_directory_uri();
		$theme_folder  = basename($theme_uri);
		
		/* 
		 * If the theme folder name string appears multiple times,
		 * lets split the string as shown below and focus only 
		 * on the last theme folder name string
		 */
		$split_1      = explode('includes', dirname(__FILE__));
		$split_2      = explode($theme_folder, $split_1[0]);
		$split_2_last = array_pop($split_2);
		
		$use_type_parameters = array(
			'item_name'    => $theme_name,
			'item_version' => $theme_version,
			'dir_url'      => trailingslashit($theme_uri . wp_normalize_path($split_2_last)),
		);
		
		update_option('dilaz_'.$parameters['use_type'].'_'.$theme_name.'_prefix', $prefix);
	}

} else if (isset($parameters['use_type']) && $parameters['use_type'] == 'plugin') {
	
	# check if its theme when in plugin use type
	if (strpos(dirname(__FILE__), 'themes')) {
		
		add_action('admin_notices', function() {
			echo '<div id="message" class="error"><p><strong>'. sprintf( __( 'Wrong use-type for metabox options. Please set "<em>use_type</em>" parameter to "<em>theme</em>" in "<em>%s</em>".', 'dilaz-metabox' ), 'wp-content'. wp_normalize_path(explode('wp-content', dirname(__DIR__))[1]) .'/config.php' ) .'</strong></p></div>';
		});
		
		$parameters['use_type_error'] = true;
		
	} else {
		
		if (!function_exists('get_plugin_data')) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		
		$plugin_data = [];
		
		$plugins_dir     = ABSPATH . 'wp-content/plugins/'; 
		$plugin_basename = plugin_basename(__FILE__);
		$plugin_folder   = strtok($plugin_basename, '/');
		
		# use global to check plugin data from all PHP files within plugin main folder
		foreach (glob(trailingslashit($plugins_dir . $plugin_folder) . '*.php') as $file) {
			$plugin_data = get_plugin_data($file);
			
			# lets ensure we don't return empty plugin data
			if (empty($plugin_data['Name'])) continue; else break;
		}
		
		$plugin_name    = $plugin_data['Name'];
		$plugin_name_lc = strtolower($plugin_name);
		$plugin_version = $plugin_data['Version'];
		
		/* 
		 * If the theme name string multiple times, lets
		 * split the string as show below and focus only 
		 * on the last theme name string
		 */
		$split_1      = explode('includes', plugin_dir_url(__FILE__));
		$split_2      = explode($plugin_folder, $split_1[0]);
		$split_2_last = array_pop($split_2);
		$split_3      = array($split_2_last, implode($plugin_folder, $split_2));
		
		$use_type_parameters = array(
			'item_name'    => $plugin_name,
			'item_version' => $plugin_version,
			'dir_url'      => trailingslashit(wp_normalize_path($split_3[1].$plugin_folder.$split_3[0])),
		);
		
		update_option('dilaz_'.$parameters['use_type'].'_'.$plugin_name.'_prefix', $prefix);
	}
}

$parameters = wp_parse_args($use_type_parameters, $parameters);

return $parameters;