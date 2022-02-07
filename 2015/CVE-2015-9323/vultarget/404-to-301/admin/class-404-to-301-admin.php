<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die('Damn it.! Dude you are looking for what?');
}
/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and enqueue the dashboard-specific stylesheet, JavaScript
 * and all other admin side functions.
 *
 * @link       http://iscode.co/product/404-to-301/
 * @since      2.0.0
 * @package    I4T3
 * @subpackage I4T3/admin
 * @author     Joel James <me@joelsays.com>
 */
class _404_To_301_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The table name of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $table    The table name of this plugin in db.
	 */
	private $table;

	/**
	* The options from db.
	*
	* @since    2.0.0
	* @access   private
	* @var      string    $gnrl_options    Get the options saved in db.
	*/
	private $gnrl_options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.0.0
	 * @var      string    $plugin_name       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 * @var      string    $table    The name of the database table of this plugin.
	 */
	public function __construct( $plugin_name, $version, $table ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->table = $table;
	}
	

	/**
	 * Register the stylesheets for the Dashboard.
	 *
	 * This function is used to register all the required stylesheets for
	 * dashboard. Styles will be registered only for i4t3 pages for performance.
	 *
	 * @since 	2.0.0
	 * @uses 	wp_enqueue_style 	To register style
	 */
	public function enqueue_styles() {

		global $pagenow;
		
		if (( $pagenow == 'admin.php' ) && ( in_array($_GET['page'], array('i4t3-settings','i4t3-logs')))) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/min/admin.css', array(), $this->version, 'all' );
		}

	}


	/**
	 * Register the scripts for the Dashboard.
	 *
	 * This function is used to register all the required scripts for
	 * dashboard. Scripts will be registered only for i4t3 pages for performance.
	 *
	 * @since 	2.0.0
	 * @uses 	wp_enqueue_script 	To register script
	 */
	public function enqueue_scripts() {

		global $pagenow;
		
		if (( $pagenow == 'admin.php' ) && ( in_array($_GET['page'], array('i4t3-settings')))) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/admin.js', array( 'jquery' ), $this->version, false );
		}

	}
	
	
	/**
	 * Run upgrade functions
	 *
	 * If 404 to 301 is upgraded, we may need to perform few updations in db
	 * 
	 * @since	2.0.0
	 * @uses	get_option()	To get the activation redirect option from db.
	 * @return	void.
	 */
	public function i4t3_upgrade_if_new() {
	
		if ( !get_option('i4t3_version_no') || ( get_option('i4t3_version_no') < I4T3_VERSION  ) ) {
			if( class_exists( '_404_To_301_Activator' ) ) {
				_404_To_301_Activator::activate();
			}
			update_option('i4t3_version_no', I4T3_VERSION );
		}
	}


	/**
	* Creating admin menus for 404 to 301.
	*
	* @since    2.0.0
	* @author	Joel James
	* @uses 	action hook 	add_submenu_page 	Action hook to add new admin menu sub page.
	*/
	public function i4t3_create_404_to_301_menu(){
		
		// Error log menu
		add_menu_page( 
				__( '404 Error Logs', '404-to-301' ),
				__( '404 Error Logs', '404-to-301' ),
				I4T3_ADMIN_PERMISSION,
				'i4t3-logs', 
				array( $this,'i4t3_render_list_page' ),
				'dashicons-redo', 
				90
		);
			
		// 404 to 301 settings menu
		add_submenu_page(
			'i4t3-logs',
			__( '404 to 301 Settings', '404-to-301' ),
			'404 Settings', 
			I4T3_ADMIN_PERMISSION, 
			'i4t3-settings',
			array( $this, 'i4t3_admin_page' )
		);
	}



	/**
	* Creating log table page.
	*
	* @since    2.0.0
	* @author	Joel James
	* @uses 	class	_404_To_301_Logs	To initialize and load the log listing table.
	*/
	public function i4t3_render_list_page(){
		
		global $i4t3_errorlogtable;
		$i4t3_errorlogtable = new _404_To_301_Logs( $this->table );
		echo '<div class="wrap"><h2>'. __( '404 Error Logs', '404-to-301' ) .'</h2>';
		$i4t3_errorlogtable->prepare_items(); 
		echo '<form method="post">
				<input type="hidden" name="page" value="i4t3_logs_list">';
		$i4t3_errorlogtable->display(); 
		echo '</form></div>'; 
	}


	/**
	* Rename admin menu text to : 404 to 301.
	*
	* @since    2.0.0
	* @author	Joel James
	* @var 		global 		$menu 	menus registered in this site.
	*/
	public function i4t3_rename_plugin_menu() {  
	    global $menu;       
	    $menu[90][0] = __( '404 to 301', '404-to-301' ); // Change menu text
	} 


	/**
	* Admin options page display.
	*
	* Includes admin page contents to manage i4t3 settings.
	* All html parts will be included in this page.
	*
	* @since    2.0.0
	* @author 	Joel James
	*/
	public function i4t3_admin_page() {

		require plugin_dir_path( __FILE__ ) . 'partials/404-to-301-admin-display.php';
	}

	
	/**
	* Registering i4t3 options.
	* This function is used to register all settings options to the db using
	* WordPress settings API.
	* If we want to register another setting, we can include that here.
	*
	* @since 	2.0.0
	* @author 	Joel James
	* @action 	hooks 		register_setting       Hook to register i4t3 options in db.
	*/
	public function i4t3_options_register(){

		register_setting( 
			'i4t3_gnrl_options', 
			'i4t3_gnrl_options' 
		);

	}


	/**
	* Custom footer text for i4t3 pages.
	*
	* Function to alter the default footer text to show i4t3 credits only on i4t3 pages.
	*
	* @since    2.0.0
	* @author	Joel James
	*/
	function i4t3_dashboard_footer () {
		
		global $pagenow;
		if (( $pagenow == 'admin.php' ) && ( in_array ( $_GET['page'], array('i4t3-settings', 'i4t3-logs')))) {
			
			_e( 'Thank you for choosing 404 to 301 to improve your website', '404-to-301' );
			echo ' | Kindly give this plugin a <a href="https://wordpress.org/support/view/plugin-reviews/404-to-301?filter=5#postform">rating &#9733; &#9733;</a>';
		} else {
			return;
		}
	}


	/**
	* Custom Plugin Action Link.
	*
	* Function to add a quick link to i4t3, when being listed on your
	* plugins list view.
	*
	* @since    2.0.0
	* @return	$links		Links to display.
	* @author	Joel James
	*/
	public function i4t3_plugin_action_links( $links, $file ) {
		$plugin_file = basename('404-to-301.php');
		if (basename($file) == $plugin_file) {
			$settings_link = '<a href="admin.php?page=i4t3-settings">Settings</a>';
			$settings_link .= ' | <a href="admin.php?page=i4t3-logs">Logs</a>';
			array_unshift($links, $settings_link);
		}
		return $links;
	}


	/**
	* Get debug data.
	*
	* Function to output the debug data for the plugin. This will be useful
	* when asking for support. Just copy and paste these data to the email.
	*
	* @since    2.0.0
	* @var 		array 	$gnrl_options 	Array of plugin settings
	* @var 		array 	$active_plugins  Array of active plugins path
	* @return	$html		Html content to diplay.
	* @author	Joel James
	*/
	public function i4t3_get_debug_data() {

		$html = '';
		$gnrl_options = get_option('i4t3_gnrl_options');
		$active_plugins = get_option ( 'active_plugins', array () );
		$active_theme = wp_get_theme();
		
		// Dump the plugin settings data
		if( !empty( $gnrl_options ) ) {
			$html 	.=	'<h4>Settings Data</h4>
							<p><pre>';
			foreach ( $gnrl_options as $key => $option ) {
				$html 	.=	$key.' : '.$option.'<br/>';
			}
			$html 	.=	'</pre></p><hr/>';
		}
		// Output basic info about the site
		$html 	.=	'<h4>Basic Details</h4>
						<p>
							Version of Plugin : '. $this->version .'<br/>
							Home Page : '. home_url() .'<br/>
						</p><hr/>';

		if ( $active_theme->exists() ) {

			$html 	.=	'<h4>Active Theme Details</h4>
							<p>
								Name : '. $active_theme->get( 'Name' ) .'<br/>
								Version : '. $active_theme->get( 'Version' ) .'<br/>
								Theme URI : '. $active_theme->get( 'ThemeURI' ) .'<br/>
							</p><hr/>';
		}
		
		// Dump the active plugins data
		if( !empty( $active_plugins ) ) {
			$html 	.=	'<h4>Active Plugins</h4>
							<p>';
			foreach ( $active_plugins as $plugin ) {
				$html 	.=	$plugin.'<br/>';
			}
			$html 	.=	'</p>';
		}

		return $html;
	}

	
}