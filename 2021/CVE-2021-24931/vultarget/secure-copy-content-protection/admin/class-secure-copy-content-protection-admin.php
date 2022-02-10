<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Secure_Copy_Content_Protection
 * @subpackage Secure_Copy_Content_Protection/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Secure_Copy_Content_Protection
 * @subpackage Secure_Copy_Content_Protection/admin
 * @author     Security Team <info@ays-pro.com>
 */
class Secure_Copy_Content_Protection_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;
	private $results_obj;
	private $settings_obj;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		add_filter('set-screen-option', array(__CLASS__, 'set_screen'), 10, 3);

        $per_page_array = array(
            'sccp_results_per_page'
        );
        foreach($per_page_array as $option_name){
            add_filter('set_screen_option_'.$option_name, array(__CLASS__, 'set_screen'), 10, 3);
        }

	}

	/**
	 * Register the styles for the admin menu area.
	 *
	 * @since    1.5.0
	 */
	public function admin_menu_styles() {
		echo "
        <style>
        	.ays_menu_badge_new{
                padding: 2px 2px !important;
            }

        	.ays_menu_badge{
                color: #fff;
                display: inline-block;
                font-size: 10px;
                line-height: 14px;
                text-align: center;
                background: #ca4a1f;
                margin-left: 5px;
                border-radius: 20px;
                padding: 2px 5px;
            }            

            #adminmenu a.toplevel_page_secure-copy-content-protection div.wp-menu-image img {
                padding: 0;
                opacity: .6;
                width: 32px;
                transition: all .3s ease-in;
            }

            #adminmenu a.toplevel_page_secure-copy-content-protection + ul.wp-submenu.wp-submenu-wrap li:last-child a {
                color: #68A615;
            }
        </style>
        ";
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles( $hook_suffix ) {

		wp_enqueue_style($this->plugin_name . '-admin', plugin_dir_url(__FILE__) . 'css/admin.css', array(), $this->version, 'all');
		wp_enqueue_style('sweetalert-css', '//cdn.jsdelivr.net/npm/sweetalert2@7.26.29/dist/sweetalert2.min.css', array(), $this->version, 'all');

		if (false === strpos($hook_suffix, $this->plugin_name)) {
			return;
		}
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Secure_Copy_Content_Protection_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Secure_Copy_Content_Protection_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// You need styling for the datepicker. For simplicity I've linked to the jQuery UI CSS on a CDN.
        wp_register_style( 'jquery-ui', 'https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css' );
        wp_enqueue_style( 'jquery-ui' );

		wp_enqueue_style('wp-color-picker');
		wp_enqueue_style('ays-sccp-select2', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css', array(), $this->version, 'all');
		wp_enqueue_style('ays_code_mirror', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.48.4/codemirror.css', array(), $this->version, 'all');
		wp_enqueue_style('copy_content_protection_bootstrap', plugin_dir_url(__FILE__) . 'css/bootstrap.min.css', array(), $this->version, 'all');
		wp_enqueue_style($this->plugin_name.'-jquery-datetimepicker', plugin_dir_url(__FILE__) . 'css/jquery-ui-timepicker-addon.css', array(), $this->version, 'all');
		//wp_enqueue_style('copy_content_protection_datatable', '//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css', array(), $this->version, 'all');
		wp_enqueue_style('copy_content_protection_datatable_bootstrap', '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css', array(), $this->version, 'all');
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/secure-copy-content-protection-admin.css', array(), $this->version, 'all');
		wp_enqueue_style('ays_sccp_font_awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', array(), $this->version, 'all');
		wp_enqueue_style('animate.css', plugin_dir_url(__FILE__) . 'css/animate.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts( $hook_suffix ) {
		global $wp_version;

        $version1 = $wp_version;
        $operator = '>=';
        $version2 = '5.5';
        $versionCompare = $this->versionCompare($version1, $operator, $version2);
        if ($versionCompare) {	
            wp_enqueue_script( $this->plugin_name.'-wp-load-scripts', plugin_dir_url(__FILE__) . 'js/ays-wp-load-scripts.js', array(), $this->version, true);
        }		

		if (false !== strpos($hook_suffix, "plugins.php")){
			wp_enqueue_script('sweetalert-js', '//cdn.jsdelivr.net/npm/sweetalert2@7.26.29/dist/sweetalert2.all.min.js', array('jquery'), $this->version, true);
			wp_enqueue_script($this->plugin_name . '-admin', plugin_dir_url(__FILE__) . 'js/admin.js', array('jquery'), $this->version, true);
			wp_localize_script($this->plugin_name . '-admin', 'sccp_admin_ajax', array('ajax_url' => admin_url('admin-ajax.php')));
		}

		if (false === strpos($hook_suffix, $this->plugin_name)) {
			return;
		}
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Secure_Copy_Content_Protection_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Secure_Copy_Content_Protection_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		global $wp_roles;
		$ays_users_roles = $wp_roles->roles;

		// wp_enqueue_script('wp-color-picker-alpha', plugin_dir_url(__FILE__) . 'js/wp-color-picker-alpha.min.js', array('wp-color-picker'), '2.1.3', true);
		wp_enqueue_script( $this->plugin_name.'-wp-color-picker-alpha', plugin_dir_url(__FILE__) . 'js/wp-color-picker-alpha.min.js', array('wp-color-picker'), $this->version, true);
        // wp_enqueue_editor();
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_media();

		/* 
        ========================================== 
           File exporters
           * xlsx
        ========================================== 
        */
		
		wp_enqueue_script( $this->plugin_name."-xlsx.core.min.js", plugin_dir_url( __FILE__ ) . 'js/xlsx.core.min.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( $this->plugin_name."-fileSaver.js", plugin_dir_url( __FILE__ ) . 'js/FileSaver.js', array( 'jquery' ), $this->version, true );	
		wp_enqueue_script( $this->plugin_name."-jhxlsx.js", plugin_dir_url( __FILE__ ) . 'js/jhxlsx.js', array( 'jquery' ), $this->version, true );

		wp_enqueue_script('ays_code_mirror', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.48.4/codemirror.min.js', array('jquery'), $this->version, true);
		wp_enqueue_script('select2js', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js', array('jquery'), $this->version, true);
		wp_enqueue_script('cpy_content_protection_datatable', '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js', array('jquery'), $this->version, true);
		wp_enqueue_script('cpy_content_protection_datatable_bootstrap', '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js', array('jquery'), $this->version, true);
		wp_enqueue_script('cpy_content_protection_popper', plugin_dir_url(__FILE__) . 'js/popper.min.js', array('jquery'), $this->version, true);
		wp_enqueue_script('cpy_content_protection_bootstrap', plugin_dir_url(__FILE__) . 'js/bootstrap.min.js', array('jquery'), $this->version, true);
		wp_enqueue_script( $this->plugin_name."-jquery.datetimepicker.js", plugin_dir_url( __FILE__ ) . 'js/jquery-ui-timepicker-addon.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/secure-copy-content-protection-admin.js', array('jquery', 'wp-color-picker'), $this->version, true);
		wp_localize_script($this->plugin_name, 'sccp', array(
			'ajax'           	=> admin_url('admin-ajax.php'),
			'loader_message' 	=> __('Just a moment...', $this->plugin_name),
			'loader_url'     	=> SCCP_ADMIN_URL . '/images/rocket.svg',
			'bc_user_role'    	=> $ays_users_roles,
		));

		$color_picker_strings = array(
			'clear'            => __( 'Clear', $this->plugin_name ),
			'clearAriaLabel'   => __( 'Clear color', $this->plugin_name ),
			'defaultString'    => __( 'Default', $this->plugin_name ),
			'defaultAriaLabel' => __( 'Select default color', $this->plugin_name ),
			'pick'             => __( 'Select Color', $this->plugin_name ),
			'defaultLabel'     => __( 'Color value', $this->plugin_name ),
		);
		wp_localize_script( $this->plugin_name.'-wp-color-picker-alpha', 'wpColorPickerL10n', $color_picker_strings );

		wp_localize_script($this->plugin_name, 'sccpLangObj', array(
            'nameField'       => __( 'Name field', $this->plugin_name ),
            'title'           => __( 'Tick the checkbox to show the Name field', $this->plugin_name ),

        ) );

	}

	function codemirror_enqueue_scripts($hook) {
		if (strpos($hook, $this->plugin_name) !== false) {
			if(function_exists('wp_enqueue_code_editor')){
	            $cm_settings['codeEditor'] = wp_enqueue_code_editor(array(
	                'type' => 'text/css',
	                'codemirror' => array(
	                    'inputStyle' => 'contenteditable',
	                    'theme' => 'cobalt',
	                )
	            ));
	        
		        wp_localize_script('wp-theme-plugin-editor', 'cm_settings', $cm_settings);
		       
		        wp_enqueue_script('wp-theme-plugin-editor');
	            wp_enqueue_style('wp-codemirror');
	            
	        }
		}
        
	}

	function versionCompare($version1, $operator, $version2) {
   
        $_fv = intval ( trim ( str_replace ( '.', '', $version1 ) ) );
        $_sv = intval ( trim ( str_replace ( '.', '', $version2 ) ) );
       
        if (strlen ( $_fv ) > strlen ( $_sv )) {
            $_sv = str_pad ( $_sv, strlen ( $_fv ), 0 );
        }
       
        if (strlen ( $_fv ) < strlen ( $_sv )) {
            $_fv = str_pad ( $_fv, strlen ( $_sv ), 0 );
        }
       
        return version_compare ( ( string ) $_fv, ( string ) $_sv, $operator );
    }

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {
		$hook_sccp = add_menu_page(
			'Copy Protection', 
			'Copy Protection', 
			'manage_options', $this->plugin_name, 
			array(
			$this,
			'display_plugin_setup_page'
		), SCCP_ADMIN_URL . '/images/sccp.png', 6);
		add_action( "load-$hook_sccp", array( $this, 'add_tabs' ));

		$hook_subscribe_to_view = add_submenu_page( $this->plugin_name,
            __('Subscribe to view', $this->plugin_name),
            __('Subscribe to view', $this->plugin_name),
            'manage_options',
            $this->plugin_name . '-subscribe-to-view',
            array($this, 'display_plugin_sccp_subscribe_to_view_page') 
        );
		add_action( "load-$hook_subscribe_to_view", array( $this, 'add_tabs' ));

        global $wpdb;
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}ays_sccp_reports WHERE `unread` = 1";
        $unread_results_count = $wpdb->get_var($sql);
        $results_text = __('Results', $this->plugin_name);
        $menu_item = ($unread_results_count == 0) ? $results_text : $results_text . '<span class="ays_menu_badge ays_results_bage">' . $unread_results_count . '</span>';
		$hook_results = add_submenu_page( $this->plugin_name,
			$results_text,
            $menu_item,
            'manage_options',
            $this->plugin_name . '-results-to-view',
            array($this, 'display_plugin_sccp_results_to_view_page') 
        );
        add_action("load-$hook_results", array($this, 'screen_option_results'));
		add_action( "load-$hook_results", array( $this, 'add_tabs' ));

		$hook_settings = add_submenu_page( $this->plugin_name,
            __('General Settings', $this->plugin_name),
            __('General Settings', $this->plugin_name),
            'manage_options',
            $this->plugin_name . '-settings',
            array($this, 'display_plugin_sccp_settings_page') 
        );
        add_action("load-$hook_settings", array($this, 'screen_option_settings'));
		add_action( "load-$hook_settings", array( $this, 'add_tabs' ));

		$hook_featured_plugins = add_submenu_page( $this->plugin_name,
            __('Our Products', $this->plugin_name),
            __('Our Products', $this->plugin_name),
            'manage_options',
            $this->plugin_name . '-featured-plugins',
            array($this, 'display_plugin_sccp_featured_plugins_page') 
        );
		add_action( "load-$hook_featured_plugins", array( $this, 'add_tabs' ));

		$hook_pro_features = add_submenu_page(
			$this->plugin_name,
			__('PRO Features', $this->plugin_name),
			__('PRO Features', $this->plugin_name),
			'manage_options',
			$this->plugin_name . '-pro-features',
			array($this, 'display_plugin_sccp_pro_features_page')
		);
		add_action( "load-$hook_pro_features", array( $this, 'add_tabs' ));
	}

	public function add_tabs() {
		$screen = get_current_screen();
	
		if ( ! $screen) {
			return;
		}
	
		$screen->add_help_tab(
			array(
				'id'      => 'sccp_help_tab',
				'title'   => __( 'General Information:
					'),
				'content' =>
					'<h2>' . __( 'SCCP Information', $this->plugin_name) . '</h2>' .
					'<p>' .
						__( 'Copy Content Protection is a must-have WordPress plugin which prevents the risk of plagiarism on your website. After the activation of the plugin the Copy and Paste, right-click option, inspect elements (F12 key), content-selection, copy the image, save image as features will be automatically disabled. In addition, the user has an option to enable or disable the features via the checkbox.',  $this->plugin_name ).'</p>'
			)
		);
	
		$screen->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', $this->plugin_name) . '</strong></p>' .
			'<p>
				<a href="https://www.youtube.com/watch?v=whYBGV703SM" target="_blank">' . __( 'Youtube video tutorials' , $this->plugin_name ) . '</a>
			</p>' .
			'<p>
				<a href="https://ays-pro.com/wordpress-copy-content-protection-user-manual" target="_blank">' . __( 'Documentation', $this->plugin_name ) . '</a>
			</p>' .
			'<p>
				<a href="https://ays-pro.com/wordpress/secure-copy-content-protection" target="_blank">' . __( 'Copy Protection plugin Premium version', $this->plugin_name ) . '</a>
			</p>'
		);
	}

	public function ays_sccp_results_export_xlsx($results){
        
		global $wpdb;
		error_reporting(0);        

        $results_array = array();
		$results_headers = array(
            array( 'text' => "Shortcode ID" ),
            array( 'text' => "User Email" ),
            array( 'text' => "User IP" ),
            array( 'text' => "Date" ),
            array( 'text' => "WP User" ),
            array( 'text' => "City, Country" )
		);

        $results_array[] = $results_headers;
        foreach ($results as $key => $result){

            $result['user_id'] = $result['user_id'] > 0 ? get_user_by('ID', $result['user_id'])->display_name : "Guest";
            
            $res_array = array(
                array( 'text' => $result['subscribe_id'] ),
                array( 'text' => $result['subscribe_email'] ),
                array( 'text' => $result['user_ip'] ),
                array( 'text' => $result['vote_date'] ),
                array( 'text' => $result['user_id'] ),
                array( 'text' => $result['user_address'] )
            );            
      
            $results_array[] = $res_array;
        }
        
		$response = array(
			'status' => true,
			'data'   => $results_array,
			"type"   => 'xlsx'
		);
		return $response;
    }

    public function ays_sccp_results_export_csv($results){
    	global $wpdb;
		error_reporting(0);

		$url = plugin_dir_url(__FILE__) . "partials/results/";
    	$path = plugin_dir_path(__FILE__) . "partials/results/";

		$file_url          	= $url . 'exported_sccp/exported_sccp.csv';
		$file_path          = $path . 'exported_sccp/exported_sccp.csv';
		$export_file        = fopen($file_path, 'wa');

		if (!$export_file) {
			echo json_encode(array(
				'status' => false
			));
			wp_die();
		}

		$export_file_fields = array('Shortcode ID', 'User Email', 'User IP', 'Date', 'WP User', 'City, Country');
		fputcsv($export_file, $export_file_fields);

		$results_array_csv = array();
		
		foreach ($results as $f_value) {
			 $f_value['user_id'] = $f_value['user_id'] > 0 ? get_user_by('ID', $f_value['user_id'])->display_name : "Guest";
			 $results_array_csv = $f_value;
			
			fputcsv($export_file, $results_array_csv);
		}
		
		fclose($export_file);

		$response = array(
			'status' => true,
			'file' 	 => $file_url,
			"type"   => 'csv'
		);

		return $response;
    }

    public function ays_sccp_results_export_json($results){
        
		global $wpdb;
		error_reporting(0);
        $results_array = array();
        foreach ($results as $key => $result){
            
            $user_id = $result['user_id'] > 0 ? get_user_by('ID', $result['user_id'])->display_name : "Guest";
            $res_array = array(
                'subscribe_id'	  => $result['subscribe_id'],
                'subscribe_email' => $result['subscribe_email'],
                'user_ip' 		  => $result['user_ip'],
                'vote_date' 	  => $result['vote_date'],
                'user_id' 		  => $user_id,
                'user_address'    => $result['user_address']            
            );

            $results_array[] = $res_array;
        }
        
		$response = array(
			'status' => true,
			'data'   => $results_array,
			"type"   => 'json'
		);
		return $response;
    } 

	public function ays_sccp_results_export_file(){
    	global $wpdb;
		error_reporting(0);

		$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';

		$shortcode_ids = "SELECT DISTINCT subscribe_id FROM {$wpdb->prefix}ays_sccp_reports";			
   		$short_id = (isset($_REQUEST['sccp_id']) && $_REQUEST['sccp_id'] != null) ? implode(',', $_REQUEST['sccp_id']) : $shortcode_ids;

		$date_from = isset($_REQUEST['date_from']) && $_REQUEST['date_from'] != '' ? $_REQUEST['date_from'] : '2000-01-01';
    	$date_to = isset($_REQUEST['date_to']) && $_REQUEST['date_to'] != '' ? $_REQUEST['date_to'] : current_time('Y-m-d');

		$sql = "SELECT subscribe_id, subscribe_email, user_ip, vote_date, user_id, user_address 
				FROM {$wpdb->prefix}ays_sccp_reports 
				WHERE subscribe_id IN ($short_id) 
             	AND vote_date BETWEEN '$date_from' AND '$date_to 23:59:59'";

		$formfields = $wpdb->get_results($sql, 'ARRAY_A');

		switch($type){
            case 'csv':
                $export_data = $this->ays_sccp_results_export_csv($formfields);
            break;
            case 'xlsx':
                $export_data = $this->ays_sccp_results_export_xlsx($formfields);
            break;
            case 'json':
                $export_data = $this->ays_sccp_results_export_json($formfields);
            break;
        }

        ob_end_clean();
        $ob_get_clean = ob_get_clean();
        echo json_encode($export_data);
        wp_die();

    }

    // EXPORT FILTERS AV
    public function ays_sccp_show_filters(){
        error_reporting(0);
        global $wpdb;

        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'ays_sccp_show_filters') {            

			$shortcode_ids = $wpdb->get_results("SELECT DISTINCT subscribe_id FROM {$wpdb->prefix}ays_sccp_reports", "ARRAY_A");

            $sql = "SELECT COUNT(subscribe_id) FROM {$wpdb->prefix}ays_sccp_reports ORDER BY subscribe_id DESC";
            $short_count = $wpdb->get_var($sql);

            echo json_encode(array(
                "shortcode" => $shortcode_ids,
                "count" => $short_count
            ));
            wp_die();
        }
    }

    public function ays_sccp_results_export_filter(){
        global $wpdb;
        error_reporting(0);
       	if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'ays_sccp_results_export_filter') {
			$shortcode_ids = "SELECT DISTINCT subscribe_id FROM {$wpdb->prefix}ays_sccp_reports";			
       		$short_id = (isset($_REQUEST['sccp_id']) && $_REQUEST['sccp_id'] != null) ? implode(',', $_REQUEST['sccp_id']) : $shortcode_ids;
       		$all_ids = $wpdb->get_results($shortcode_ids);
       		$date_from = isset($_REQUEST['date_from']) && $_REQUEST['date_from'] != '' ? $_REQUEST['date_from'] : '2000-01-01';
        	$date_to = isset($_REQUEST['date_to']) && $_REQUEST['date_to'] != '' ? $_REQUEST['date_to'] : current_time('Y-m-d');

            $sql = "SELECT COUNT(subscribe_id) AS qanak FROM {$wpdb->prefix}ays_sccp_reports
             WHERE subscribe_id IN ($short_id)
             AND vote_date BETWEEN '$date_from' AND '$date_to 23:59:59'";
            $results = $wpdb->get_var($sql);
	        ob_end_clean();
	        $ob_get_clean = ob_get_clean();
            $res = array(
            	'results' => $results
            );
	        echo json_encode($res);
	        wp_die();
        }
    }

    public static function set_screen($status, $option, $value){
        return $value;
    }
	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */

	public function add_action_links( $links ) {
		/*
		*  Documentation : https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
		*/
		$settings_link = array(
			'<a href="' . admin_url('options-general.php?page=' . $this->plugin_name) . '">' . __('Settings', $this->plugin_name) . '</a>',
			'<a href="https://ays-demo.com/secure-copy-content-protection-free-demo/" target="_blank">' . __('Demo', $this->plugin_name) . '</a>',
            '<a href="https://ays-pro.com/wordpress/secure-copy-content-protection" target="_blank" style="color:red; font-weight:bold;">' . __('Buy Now', $this->plugin_name) . '</a>',
		);

		return array_merge($settings_link, $links);

	}

 	public function add_plugin_row_meta($meta, $file) {
		if ($file == SCCP_BASENAME) {
			$meta[] = '<a href="https://wordpress.org/support/plugin/secure-copy-content-protection/" target="_blank">' . esc_html__( 'Free Support', $this->plugin_name ) . '</a>';
		}

		return $meta;
	}


	public function display_plugin_setup_page() {
		$this->settings_obj = new Sccp_Settings_Actions($this->plugin_name);
		require_once('partials/secure-copy-content-protection-admin-display.php');
	}

	public function screen_option_settings() {
        $this->settings_obj = new Sccp_Settings_Actions($this->plugin_name);
    }

	public function display_plugin_sccp_settings_page(){
        include_once('partials/settings/secure-copy-content-protection-settings.php');
    }		

	public function display_plugin_sccp_featured_plugins_page(){
        include_once('partials/features/secure-copy-content-protection-featured-display.php');
    }

    public function display_plugin_sccp_pro_features_page() {
		include_once('partials/features/secure-copy-content-protection-pro-features.php');
	}

	public function display_plugin_sccp_subscribe_to_view_page() {
		include_once('partials/subscribe/secure-copy-content-protection-subscribe-display.php');
    }

	public function display_plugin_sccp_results_to_view_page() {
		include_once('partials/results/secure-copy-content-protection-results-display.php');
    }

	public function deactivate_sccp_option() {
		$request_value  = $_REQUEST['upgrade_plugin'];
		$upgrade_option = get_option('sccp_upgrade_plugin', '');
		if ($upgrade_option === '') {
			add_option('sccp_upgrade_plugin', $request_value);
		} else {
			update_option('sccp_upgrade_plugin', $request_value);
		}
		echo json_encode(array('option' => get_option('sccp_upgrade_plugin', '')));
		wp_die();
	}

	public function screen_option_results() {
		$option = 'per_page';
		$args   = array(
			'label'   => __('Results', $this->plugin_name),
			'default' => 7,
			'option'  => 'sccp_results_per_page',
		);

		add_screen_option($option, $args);
		$this->results_obj = new Sccp_Results_List_Table($this->plugin_name);
		$this->settings_obj = new Sccp_Settings_Actions($this->plugin_name);

	}

	// Mailchimp - Get mailchimp lists
    public function ays_get_mailchimp_lists($username, $api_key){
        error_reporting(0);
        if($username == ""){
            return array(
                'total_items' => 0
            );
        }
        if($api_key == ""){
            return array(
                'total_items' => 0
            );
        }
        
        $api_prefix = explode("-",$api_key);
        $api_prefix = isset($api_prefix[1]) && $api_prefix[1] != "" ? $api_prefix[1] : '';
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://".$api_prefix.".api.mailchimp.com/3.0/lists/?count=100",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_USERPWD => "$username:$api_key",
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "cache-control: no-cache"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
      		//echo "cURL Error #:" . $err;
        } else {
            return json_decode($response, true);
        }
    }
    public function sccp_admin_footer($a){
        if(isset($_REQUEST['page'])){
            if(false !== strpos($_REQUEST['page'], $this->plugin_name)){
                ?>
                <p style="font-size:13px;text-align:center;font-style:italic;">
                    <span style="margin-left:0px;margin-right:10px;" class="ays_heart_beat"><i class="ays_fa ays_fa_heart animated"></i></span>
                    <span><?php echo __( "If you love our plugin, please do big favor and rate us on", $this->plugin_name); ?></span> 
                    <a target="_blank" href='https://wordpress.org/support/plugin/secure-copy-content-protection/reviews/?rate=5#new-post'>WordPress.org</a>
                    <span class="ays_heart_beat"><i class="ays_fa ays_fa_heart animated"></i></span>
                </p>
            <?php
            }
        }
    }

	// Mailchimp update list
	public static function ays_add_mailchimp_update_list($username, $api_key, $list_id, $args){
		if($username == "" || $api_key == ""){
			return false;
		}

		if( $list_id == '' ){
			return false;
		}

		if( ! isset( $args['double_optin'] ) || ! array_key_exists( 'double_optin', $args ) ){
			return false;
		}

		$list_data = self::ays_get_mailchimp_list( $username, $api_key, $list_id );

		if( empty( $list_data ) ){
			return false;
		}

		$double_optin = isset( $args['double_optin'] ) && $args['double_optin'] == 'on' ? true : false;

		$fields = array(
			"name" => $list_data['name'],
			"contact" => $list_data['contact'],
			"permission_reminder" => $list_data['permission_reminder'],
			"use_archive_bar" => $list_data['use_archive_bar'],
			"campaign_defaults" => $list_data['campaign_defaults'],
			"email_type_option" => $list_data['email_type_option'],
			"double_optin" => $double_optin,
		);

		$api_prefix = explode("-",$api_key)[1];

		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://".$api_prefix.".api.mailchimp.com/3.0/lists/".$list_id."/",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_USERPWD => "$username:$api_key",
			CURLOPT_CUSTOMREQUEST => "PATCH",
			CURLOPT_POSTFIELDS => json_encode($fields),
			CURLOPT_HTTPHEADER => array(
				"Content-Type: application/json",
				"cache-control: no-cache"
			),
		));

		$response = curl_exec($curl);

		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			return "cURL Error #: " . $err;
		} else {
			return json_decode( $response, true );
		}
	}

	// Mailchimp - Get mailchimp list
	public static function ays_get_mailchimp_list($username, $api_key, $list_id){
		error_reporting(0);
		if($username == ""){
			return array();
		}
		if($api_key == ""){
			return array();
		}
		if($list_id == ""){
			return array();
		}

		$api_prefix = explode("-",$api_key)[1];

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://".$api_prefix.".api.mailchimp.com/3.0/lists/".$list_id,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_USERPWD => "$username:$api_key",
			CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
			CURLOPT_HTTPHEADER => array(
				"Content-Type: application/json",
				"cache-control: no-cache"
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
//            echo "cURL Error #:" . $err;
		} else {
			return json_decode($response, true);
		}
	}

    public function ays_sccp_sale_baner(){
		if(isset($_POST['ays_sccp_sale_btn'])){
            update_option('ays_sccp_sale_notification', 1); 
            update_option('ays_sccp_sale_date', current_time( 'mysql' ));
        }
        $ays_sccp_sale_date = get_option('ays_sccp_sale_date');
        $current_date = current_time( 'mysql' );
        $date_diff = strtotime($current_date) -  intval(strtotime($ays_sccp_sale_date)) ;
        $val = 60*60*24*5;
        $days_diff = $date_diff / $val;

        if(intval($days_diff) > 0 ){
            update_option('ays_sccp_sale_notification', 0); 
        }

        $ays_sccp_ishmar = intval(get_option('ays_sccp_sale_notification'));
        
        if($ays_sccp_ishmar == 0 ){
            $this->ays_sccp_sale_message($ays_sccp_ishmar);
        }
	}

	public function ays_sccp_sale_message($ishmar){
        if($ishmar == 0 ){
			?>
			<div id="ays-sccp-dicount-month-main" class="notice notice-success is-dismissible ays_sccp_dicount_info">
				<div id="ays-sccp-dicount-month" class="ays_sccp_dicount_month" style="display: flex;align-items: center;justify-content: space-between;">
					<div>
						<a href="https://bit.ly/34EPlCF" target="_blank" class="ays-poll-sale-banner-link"><img src="<?php echo SCCP_ADMIN_URL . '/images/great_bundle_logo.png';?> " style="width: 60px;"></a>
					</div>
					<div style="font-size:14px; padding:12px; width: 100%;">
						<strong style="font-weight: bold;">
							<?php echo __( "Limited Time <span style='color:red;'>50%</span> SALE on <a href='https://bit.ly/34EPlCF' target='_blank'> 5 Powerful Plugins</a>
							",SCCP_NAME); ?>
						</strong>
						<br>
						<strong style="font-size: 12px;font-weight: bold;">
							
								<?php echo __( "Great bundle offer for you! It consists of 5 different powerful plugins, each one allowing you to make your WordPress experience the best that could be.  Hurry! Ends on June 30. <a href='https://bit.ly/34EPlCF' target='_blank'>Check it out!</a>",SCCP_NAME);?>
						
						</strong>
						
						<form action="" method="POST">
									<button class="btn btn-link ays-button" name="ays_sccp_sale_btn" style="height: 32px; margin-left: 0;padding-left: 0">Dismiss ad</button>
								</form>
							
					</div>
					<a href="https://bit.ly/34EPlCF" class="button button-primary ays-button" id="ays-button-top-buy-now" target="_blank" style="height: 32px; display: flex; align-items: center; font-weight: 500; " ><?php echo __('Learn more',SCCP_NAME);?></a>
				
				</div>
				
			</div>
			<?php
		}
	}

}