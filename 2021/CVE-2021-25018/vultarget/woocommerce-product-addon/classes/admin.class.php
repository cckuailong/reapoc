<?php
/*
 * working behind the seen
 */

/* 
**========== Direct access not allowed =========== 
*/ 
if( ! defined('ABSPATH') ) die('Not Allowed');

class NM_PersonalizedProduct_Admin extends NM_PersonalizedProduct {

	var $menu_pages, $plugin_scripts_admin, $plugin_settings;

	function __construct() {
		
		// setting plugin meta saved in config.php
		$this->plugin_meta = ppom_get_plugin_meta();
		
		// getting saved settings
		$this->plugin_settings = get_option ( $this -> plugin_meta['shortname'] . '_settings' );
		
		// populating $inputs with NM_Inputs object
		// $this -> inputs = self::get_all_inputs ();
		
		/*
		 * [1] TODO: change this for plugin admin pages
		*/
		$this->menu_pages = array ( 
			array (
				'page_title' => __('PPOM', "ppom"),
				'menu_title' => __('PPOM', "ppom"),
				'cap' => 'manage_options',
				'slug' => "ppom",
				'callback' => 'product_meta',
				'parent_slug' => 'woocommerce' 
			),
		);
		
		
		add_action ( 'admin_menu', array (
				$this,
				'add_menu_pages' 
		) );
		
		
		// Get PRO version
		// add_action('ppom_after_ppom_field_admin', 'ppom_admin_pro_version_noticez', 10);
		add_action('ppom_after_ppom_field_admin', 'ppom_admin_rate_and_get', 10);
		
		// Getting products list
		add_action('wp_ajax_ppom_get_products', array($this, 'get_products'));
		add_action('wp_ajax_ppom_attach_ppoms', array($this, 'ppom_attach_ppoms'));
		
		// Adding setting tab in WooCommerce
		if( !ppom_settings_migrated() ) {
    		add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
    		// Display settings
    		add_action( 'woocommerce_settings_tabs_ppom_settings', array($this, 'settings_tab') );
    		// Save settings
    		add_action( 'woocommerce_update_options_ppom_settings', array($this, 'save_settings') );
		}
    	
    	// adding wpml support for PPOM Settings
    	add_filter('woocommerce_admin_settings_sanitize_option', array($this, 'ppom_setting_wpml'), 10, 3); 
    	
    	add_action( 'admin_head', array($this, 'ppom_tabs_custom_style') );
    	
    	add_action('ppom_pdf_setting_action', 'ppom_admin_update_pro_notice',10);
    	
    	add_action( 'woocommerce_admin_field_ppom_multi_select', array( $this, 'ppom_multi_select_role_setting' ),2,10 );
    	
	}
	

	
	/*
	 * creating menu page for this plugin
	*/
	function add_menu_pages() {
		
		if( !$this->menu_pages ) return '';
		
		foreach ( $this->menu_pages as $page ) {
			
			$cap = apply_filters('ppom_menu_capability', $page ['cap']);
			
			if ($page ['parent_slug'] == '') {
				
				$menu = add_options_page ( __ ( 'PPOM Fields', "ppom" ), __ ( 'PPOM Fields', "ppom" ), $cap, $page ['slug'], array (
						$this,
						$page ['callback'] 
				), $this->plugin_meta ['logo'], $this->plugin_meta ['menu_position'] );
			} else {
				
				$menu = add_submenu_page ( $page ['parent_slug'], __ ( $page ['page_title'], "ppom" ), __ ( 'PPOM Fields', "ppom" ), 
						$cap, $page ['slug'], array (
						$this,
						$page ['callback'] 
				) );
			}
			
			if( ! current_user_can('administrator') ) {
				$cap = 'ppom_options_page';
				// Menu page for roles set by PPOM Permission Settings
				add_menu_page ( __ ( $page ['page_title'], "ppom" ), __ ( 'PPOM Fields', "ppom" ), 
							$cap, $page ['slug'], array (
							$this,
							$page ['callback'] 
					) );
			}
			
			
		}
	}
	

	/*
	 * CALLBACKS
	*/
	function product_meta() {
		
		echo '<div id="ppom-pre-loading"></div>';
		    
		echo '<div class="ppom-admin-wrap woocommerce ppom-wrapper" style="display:none">';
		
		$action  = (isset($_REQUEST ['action']) ? sanitize_text_field($_REQUEST ['action']) : '');
		$do_meta = (isset($_REQUEST ['do_meta']) ? sanitize_text_field($_REQUEST ['do_meta']) : '');
		$view    = (isset($_REQUEST ['view']) ? sanitize_text_field($_REQUEST ['view']) : '');

		if ($action != 'new' && $do_meta != 'edit' && $do_meta != 'clone' && $view != 'addons') {
			echo '<h1 class="ppom-heading-style">' . __ ( 'N-Media WooCommerce Personalized Product Option Manager', "ppom" ) . '</h1>';
			echo '<p>' . __ ( 'Create different meta groups for different products.', "ppom" ) . '</p>';
		}
		
		if ((isset ( $_REQUEST ['productmeta_id'] ) && $_REQUEST ['do_meta'] == 'edit') || $action == 'new') {
			ppom_load_template ( 'admin/ppom-fields.php' );
		} elseif ( isset($_REQUEST ['do_meta']) && $_REQUEST ['do_meta'] == 'clone') {
			
			$this -> clone_product_meta( intval($_REQUEST ['productmeta_id']) );
		}else if( isset ( $_REQUEST ['page'] ) && $_REQUEST ['page'] == 'ppom' && $view === 'addons'){
			ppom_load_template ( 'admin/addons-list.php' );
		}else{
			$url_add = add_query_arg(array('action' => 'new'));
			$video_url = 'https://najeebmedia.com/ppom/#howtovideo';
			$ppom_settings_url = admin_url( "admin.php?page=wc-settings&tab=ppom_settings" );
			$ppom_addons_url = add_query_arg(array('view' => 'addons'));
			
			echo '<div class="ppom-product-meta-block text-center ppom-meta-card-block">';
				echo '<h2>' . __ ( 'How it works?', "ppom" ) . '</h2>';
				printf(__('<p><a href="%s" target="_blank">Watch a Quick Video</a>', "ppom"), $video_url);
				printf(__(' - <a href="%s">PPOM Settings</a></p>', "ppom"), $ppom_settings_url);
				echo '<a style="margin-right: 3px;" class="btn btn-success" href="'.esc_url($url_add).'"><span class="dashicons dashicons-plus"></span> '. __ ( 'Add PPOM Meta Group', "ppom" ) . '</a>';
				echo '<a class="btn btn-success" href="'.esc_url($ppom_addons_url).'">'. __ ( 'PPOM Addons', "ppom" ) . '</a>';
			echo '</div>';
			echo '<br>';

			do_action('ppom_pdf_setting_action');
			do_action('ppom_enquiryform_setting_action');
			
			
		}
		
		// existing meta group tables show only ppom main page
		if ( $action != 'new' && $do_meta != 'edit' && $view != 'addons') {
			ppom_load_template ( 'admin/existing-meta.php' );
		}
		
		echo '</div>';
	}
	
	/*
	 * Get Products
	*/
	function get_products() {
		
		if(!ppom_security_role()){
			_e ("Sorry, you are not allowed to perform this action", 'ppom');
			die(0);
		}
		
		global $wpdb;
		
		$all_product_data = $wpdb->get_results("SELECT ID,post_title FROM `" . $wpdb->prefix . "posts` where post_type='product' and post_status = 'publish'");
		
		$ppom_id = intval($_GET['ppom_id']);
		
		ob_start();
		$vars = array('product_list'=>$all_product_data,'ppom_id'=>$ppom_id);
		ppom_load_template('admin/products-list.php', $vars);
		
		$list_html = ob_get_clean();
        
        echo ppom_esc_html($list_html);
        
        die(0);
	}
	

	/*
	 * PPOM Attachments
	*/
	function ppom_attach_ppoms() {
		
		if(!ppom_security_role()){
			_e ("Sorry, you are not allowed to perform this action", 'ppom');
			die(0);
		}
		
		// wp_send_json($_POST);
		$response = array();
		
		$ppom_id = intval($_POST['ppom_id']);
		
		$ppom_udpated = 0;
		// Reset
		if( isset($_POST['ppom_removed']) ) {
			
			foreach($_POST['ppom_removed'] as $product_id) {
			
				delete_post_meta( intval($product_id), '_product_meta_id');
				$ppom_udpated++;
			}
		}
		
		if( isset($_POST['ppom_attached']) ) {
			
			foreach($_POST['ppom_attached'] as $product_id) {
			
				update_post_meta(intval($product_id), '_product_meta_id', $ppom_id);
				$ppom_udpated++;
			}
		}
		
		$response = array("message"=>"PPOM updated for {$ppom_udpated} Products", 'status'=>'success');
		
		wp_send_json( $response );
	}


	/*
	 * Plugin Validation
	*/
	function validate_plugin(){
		
		echo '<div class="wrap">';
		echo '<h2>' . __ ( 'Provide API key below:', "ppom" ) . '</h2>';
		echo '<p>' . __ ( 'If you don\'t know your API key, please login into your: <a target="_blank" href="http://wordpresspoets.com/member-area">Member area</a>', "ppom" ) . '</p>';
		
		echo '<form onsubmit="return validate_api_wooproduct(this)">';
			echo '<p><label id="plugin_api_key">'.__('Entery API key', "ppom").':</label><br /><input type="text" name="plugin_api_key" id="plugin_api_key" /></p>';
			wp_nonce_field();
			echo '<p><input type="submit" class="button-primary button" name="plugin_api_key" /></p>';
			echo '<p id="nm-sending-api"></p>';
		echo '</form>';
		
		echo '</div>';
	}
	
	public static function add_settings_tab( $settings_tabs ) {
    	
        $settings_tabs['ppom_settings'] = __( 'PPOM Settings', 'ppom' );
        return $settings_tabs;
    }
    
    function settings_tab() {
    	
    	woocommerce_admin_fields( ppom_array_settings() );
        
    }
    
    function save_settings() {
    	
    	woocommerce_update_options( ppom_array_settings() );
    }
    
    function ppom_setting_wpml($value, $option, $raw_value) {
    	
    	if( isset($option['type']) && isset($option['type']) == 'text' ) {
    		$value = ppom_wpml_translate($value, 'PPOM');
    	}
    	
    	return $value;
    }

    function ppom_tabs_custom_style() {
		?>
		<style>
			#woocommerce-product-data .ppom_extra_options_panel label{ margin: 0 !important; }
			
			/* PPOM Meta in column */
			th.column-ppom_meta{ width: 10%!important;}
		</style><?php
	}
	
	function ppom_multi_select_role_setting($value){
		$selections =  get_option( $value['id'])? get_option( $value['id']) : 'administrator' ;
		
		
		if ( ! empty( $value['options'] ) ) {
				$selected_roles = $value['options'];
		} else {
			$selected_roles = array('administrator' => 'Administrator');
		}

		asort( $selected_roles );
		
		
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <span class="woocommerce-help-tip" data-tip="<?php echo $value['desc']; ?>"></span></label>
			</th>
			<td class="forminp">
				<select multiple="multiple" name="<?php echo esc_attr( $value['id'] ); ?>[]" style="width:350px" data-placeholder="<?php esc_attr_e( 'Choose Roles', 'ppom' ); ?>" aria-label="<?php esc_attr_e( 'Roles', 'ppom' ); ?>" class="wc-enhanced-select">
					<?php
					if ( ! empty( $selected_roles ) ) {
						foreach ( $selected_roles as $key => $val ) {
							echo '<option value="' . esc_attr( $key ) . '"' . wc_selected( $key, $selections ) . '>' . esc_html( $val ) . '</option>'; // WPCS: XSS ok.
						}
					}
					?>
				</select>  <br /><a class="select_all button" href="#"><?php esc_html_e( 'Select all', 'ppom' ); ?></a> <a class="select_none button" href="#"><?php esc_html_e( 'Select none', 'ppom' ); ?></a>
			</td>
		</tr>
		<?php 
		
	}
	
	
    
    
}