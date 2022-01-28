<?php
/*
 Plugin Name: Under Construction
 Plugin URI: https://wordpress.org/plugins/underconstruction/
 Description: Makes it so your site can only be accessed by users who log in. Useful for developing a site on a live server, without the world being able to see it
 Version: 1.18
 Author: Noah Kagan
 Author URI: https://appsumo.com/tools/wordpress/?utm_source=sumo&utm_medium=wp-widget&utm_campaign=underconstruction
 */

/*
 This file is part of underConstruction.
 underConstruction is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.
 underConstruction is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.
 You should have received a copy of the GNU General Public License
 along with underConstruction.  If not, see <http://www.gnu.org/licenses/>.
 */

?>
<?php

if (!defined('UNDERCONSTRUCTION_PLUGIN_DIR')) {
  define('UNDERCONSTRUCTION_PLUGIN_DIR', dirname(__FILE__));
}

class underConstruction
{
	var $installedFolder = "";
	var $mainOptionsPage = "under-construction";

	function __construct()
	{
		$this->installedFolder = basename(dirname(__FILE__));
	}

	function underConstruction()
	{
		$this->__construct();
	}

	function getMainOptionsPage()
	{
		return $this->mainOptionsPage;
	}

	function underConstructionAdminInit()
	{
		/* Register our script. */
		wp_register_script('underConstructionJS', WP_PLUGIN_URL.'/'.$this->installedFolder.'/underconstruction.min.js');
		$this->uc_handle_external_redirects();

		wp_enqueue_script('under-construction-admin',plugin_dir_url( __FILE__ ). 'scripts/underconstruction-scripts.js',array('jquery'));
		wp_enqueue_style('under-construction-admin-style',plugin_dir_url( __FILE__ ).'styles/underconstruction-style-common.css', array(), '3.1.1');
	}

	function uc_changeMessage()
	{
		require_once ('ucOptions.php');
	}

	function uc_adminMenu()
	{
		/* Register our plugin page */
		$page = add_options_page('Under Construction Settings', 'Under Construction', 'activate_plugins', $this->mainOptionsPage, array($this, 'uc_changeMessage'));

		/* Using registered $page handle to hook script load */
		add_action('admin_print_scripts-'.$page, array($this, 'underConstructionEnqueueScripts'));

	}

	function underConstructionEnqueueScripts()
	{
		/*
		 * It will be called only on your plugin admin page, enqueue our script here
		 */
		wp_enqueue_script('scriptaculous');
		wp_enqueue_script('underConstructionJS');
	}

	function uc_overrideWP()
	{
		if ($this->pluginIsActive())
		{
			if (!is_user_logged_in())
			{
				$array = get_option('underConstructionIPWhitelist');
				
				if(!is_array($array)){
					$array = array();
				}
				
				if(!in_array(inet_ntop(inet_pton($_SERVER['REMOTE_ADDR'])), $array)){

					//send a 503 if the setting requires it
					if (get_option('underConstructionHTTPStatus') == 503)
					{
						header('HTTP/1.1 503 Service Unavailable');
					}

					//send a 503 if the setting requires it
					if (get_option('underConstructionHTTPStatus') == 301)
					{
						header( "HTTP/1.1 301 Moved Permanently" );
						header( "Location: " . get_option('underConstructionRedirectURL') );
					}

					if ($this->displayStatusCodeIs(0)) //they want the default!
					{
						require_once ('defaultMessage.php');
						displayDefaultComingSoonPage();
						die();
					}

					if ($this->displayStatusCodeIs(1)) //they want the default with custom text!
					{
						require_once ('defaultMessage.php');
						displayComingSoonPage($this->getCustomPageTitle(), $this->getCustomHeaderText(), $this->getCustomBodyText());
						die();
					}

					if ($this->displayStatusCodeIs(2)) //they want custom HTML!
					{
						echo html_entity_decode($this->getCustomHTML(), ENT_QUOTES);
						die();
					}
					
					if($this->displayStatusCodeIs(3)){
						require_once(get_template_directory() . '/under-construction.php');
						die();
					}
				}
			}
		}
	}

	function uc_admin_override_WP(){

		if(!$this->pluginIsActive()){
			return;
		}

		if(get_option('underConstructionRequiredRole') && is_user_logged_in()){
			
			global $wp_roles;
			$all_roles = $wp_roles->roles;
					
			$editable_roles = apply_filters('editable_roles', $all_roles);

			$required_role = $editable_roles[get_option('underConstructionRequiredRole')];

			$new_privs = array();


			foreach($required_role['capabilities'] as $key => $value){
				if($value == true){
					$new_privs[] = $key;
				}
			}

			if(!current_user_can($new_privs[0])){
				wp_logout();
				wp_redirect(get_bloginfo('url'));
			}
		}
	}

	function getCustomHTML()
	{
		return stripslashes(get_option('underConstructionHTML'));
	}


	function uc_activate()
	{
		if (get_option('underConstructionArchive'))
		{
			//get all the options back from the archive
			$options = get_option('underConstructionArchive');

			//put them back where they belong
			update_option('underConstructionHTML', $options['underConstructionHTML']);
			update_option('underConstructionActivationStatus', $options['underConstructionActivationStatus']);
			update_option('underConstructionCustomText', $options['underConstructionCustomText']);
			update_option('underConstructionDisplayOption', $options['underConstructionDisplayOption']);
			update_option('underConstructionHTTPStatus', $options['underConstructionHTTPStatus']);

			delete_option('underConstructionArchive');
		}
	}

	function uc_deactivate()
	{
		//get all the options. store them in an array
		$options = array();
		$options['underConstructionHTML'] = get_option('underConstructionHTML');
		$options['underConstructionActivationStatus'] = get_option('underConstructionActivationStatus');
		$options['underConstructionCustomText'] = get_option('underConstructionCustomText');
		$options['underConstructionDisplayOption'] = get_option('underConstructionDisplayOption');
		$options['underConstructionHTTPStatus'] = get_option('underConstructionHTTPStatus');

		//store the options all in one record, in case we ever reactivate the plugin
		update_option('underConstructionArchive', $options);

		//delete the separate ones
		delete_option('underConstructionHTML');
		delete_option('underConstructionActivationStatus');
		delete_option('underConstructionCustomText');
		delete_option('underConstructionDisplayOption');
		delete_option('underConstructionHTTPStatus');
	}

	function pluginIsActive()
	{

		if (!get_option('underConstructionActivationStatus')) //if it's not set yet
		{
			return false;
		}

		if (get_option('underConstructionActivationStatus') == 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function httpStatusCodeIs($status)
	{
		if (!get_option('underConstructionHTTPStatus')) //if it's not set yet
		{
			update_option('underConstructionHTTPStatus', 200); //set it
		}

		if (get_option('underConstructionHTTPStatus') == $status)
		{
			return true;
		}
		else
		{
			return false;
		}

	}

	function displayStatusCodeIs($status)
	{
		if (!get_option('underConstructionDisplayOption')) //if it's not set yet
		{
			update_option('underConstructionDisplayOption', 0); //set it
		}

		if (get_option('underConstructionDisplayOption') == $status)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function getCustomPageTitle()
	{
		if (get_option('underConstructionCustomText') != false)
		{
			$fields = get_option('underConstructionCustomText');
			return stripslashes($fields['pageTitle']);
		}
		else
		{
			return '';
		}
	}

	function getCustomHeaderText()
	{
		if (get_option('underConstructionCustomText') != false)
		{
			$fields = get_option('underConstructionCustomText');
			return stripslashes($fields['headerText']);
		}
		else
		{
			return '';
		}
	}

	function getCustomBodyText()
	{
		if (get_option('underConstructionCustomText') != false)
		{
			$fields = get_option('underConstructionCustomText');
			return stripslashes($fields['bodyText']);
		}
		else
		{
			return '';
		}
	}


	function global_notice() {
		if (!is_plugin_active('sumome/sumome.php') && in_array(substr(basename($_SERVER['REQUEST_URI']), 0, 11), array('plugins.php', 'index.php')) && get_option('underconstruction_global_notification') == 1) {
			?>
				<style type="text/css">
					#underconstruction_global_notification a.button:active {vertical-align:baseline;}
				</style>
				<div class="updated" id="underconstruction_global_notification" style="border:3px solid #317A96;position:relative;background:##3c9cc2;background-color:#3c9cc2;color:#ffffff;height:70px;">
					<a class="notice-dismiss" href="<?php echo admin_url('options-general.php?page=underConstructionMainOptions&underconstruction_global_notification=0'); ?>" style="right:165px;top:0;"></a>
					<a href="<?php echo admin_url('options-general.php?page=underConstructionMainOptions&wp_google_fonts_global_notification=0'); ?>" style="position:absolute;top:9px;right:15px;color:#ffffff;">Dismiss and go to settings</a>
					<p style="font-size:16px;line-height:50px;">
						<?php _e('Looking for more sharing tools?'); ?> &nbsp;<a style="background-color: #6267BE;border-color: #3C3F76;" href="<?php echo admin_url('plugin-install.php?tab=plugin-information&plugin=sumome&TB_iframe=true&width=743&height=500'); ?>" class="thickbox button button-primary">Get SumoMe WordPress Plugin</a>
					</p>
		        </div>
			<?php
		}
	}

	function plugin_deactivate() {
		delete_option('underconstruction_global_notification');
	}

	function admin_menu_link() {
	  add_menu_page( 'Under Construction', 'Under Construction', 'manage_options', 'under-construction', array(& $this, 'uc_changeMessage'), 'dashicons-hammer');

	  add_submenu_page( 'under-construction', 'Other Tools', 'Other Tools', 'manage_options', 'under-construction-plugin-other-tools', array($this, 'uc_other_tools_page'));

	  add_submenu_page(
	      'under-construction',
	      'Appsumo',
	      '<span class="under-construction-sidebar-appsumo-link"><span class="dashicons dashicons-star-filled" style="font-size: 17px"></span> AppSumo</span>',
	      'manage_options',
	      'uc_go_appsumo_pro',
	      array($this, 'uc_handle_external_redirects')
	    );

	  //add_filter( 'plugin_action_links_' . plugin_basename(__FILE__),array($this, 'uc_filter_plugin_actions'), 10, 2 );
	}

	function uc_filter_plugin_actions($links, $file) {
	  $settings_link = '<a href="admin.php?page=underConstruction">' . __('Settings') . '</a>';
	  array_unshift( $links, $settings_link );

	  return $links;
	}

	function uc_other_tools_page() {
	  include(UNDERCONSTRUCTION_PLUGIN_DIR.'/other_tools.php');
	}

	function uc_handle_external_redirects() {
	  if ( empty( $_GET['page'] ) ) {
	    return;
	  }

	  if ( 'uc_go_appsumo_pro' === $_GET['page'] ) {
	    wp_redirect( ( 'https://appsumo.com/tools/wordpress/?utm_source=sumo&utm_medium=wp-widget&utm_campaign=underconstruction' ) );
	    die;
	  }
	}
}

$underConstructionPlugin = new underConstruction();

add_action('template_redirect', array($underConstructionPlugin, 'uc_overrideWP'));
add_action('admin_init', array($underConstructionPlugin, 'uc_admin_override_WP'));
add_action('wp_login', array($underConstructionPlugin, 'uc_admin_override_WP'));


add_action('plugins_loaded', 'underConstructionInitTranslation');

add_action('admin_init', array($underConstructionPlugin, 'underConstructionAdminInit'));
//add_action('admin_menu', array($underConstructionPlugin, 'uc_adminMenu'));

register_activation_hook(__FILE__, array($underConstructionPlugin, 'uc_activate'));
register_deactivation_hook(__FILE__, array($underConstructionPlugin, 'uc_deactivate'));
register_uninstall_hook(__FILE__, 'underConstructionPlugin_delete');


add_action("admin_menu", array($underConstructionPlugin, 'admin_menu_link'));



function underConstructionPlugin_delete()
{
	delete_option('underConstructionArchive');
}

function uc_get_ip_address(){
	echo $_SERVER['REMOTE_ADDR'];
	die();
}


function underConstructionPluginLinks($links, $file)
{
	global $underConstructionPlugin;
	if ($file == basename(dirname(__FILE__)).'/'.basename(__FILE__) && function_exists("admin_url"))
	{
		//add settings page
		$manage_link = '<a href="'.admin_url('?page='.$underConstructionPlugin->getMainOptionsPage()).'">'.__('Settings').'</a>';
		array_unshift($links, $manage_link);


	}
	return $links;
}

function underConstructionInitTranslation() {
  load_plugin_textdomain( 'underconstruction', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}


add_filter('plugin_action_links', 'underConstructionPluginLinks', 10, 2);


//ajax

add_action('wp_ajax_uc_get_ip_address', 'uc_get_ip_address');

add_option('underconstruction_global_notification', 1);
//add_action( 'admin_notices', array($underConstructionPlugin, 'global_notice') );
register_deactivation_hook( __FILE__, array($underConstructionPlugin, 'plugin_deactivate') );
