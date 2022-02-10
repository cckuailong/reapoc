<?php
/*
 * The base plugin class.
 */


class NM_PersonalizedProduct {
	
	static $tbl_productmeta = 'nm_personalized';
	
	
	/**
	 * this holds all input objects
	 */
	var $inputs;
	
	/**
	 * the static object instace
	 */
	private static $ins = null;
	
	
	public static function get_instance()
	{
		// create a new object if it doesn't exist.
		is_null(self::$ins) && self::$ins = new self;
		return self::$ins;
	}
	
	
	function __construct() {
		
		
		// populating $inputs with NM_Inputs object
		$this -> inputs = self::get_all_inputs();
		
		
		/** ============ NEW Hooks ==================== */
		
		add_action( 'admin_bar_menu',   'ppom_admin_bar_menu', 1000 );
		
		// Rendering fields on product page
		if (ppom_is_legacy_mode()) {
			add_action ( 'woocommerce_before_add_to_cart_button', 'ppom_woocommerce_show_fields', 15);
		}else{
			add_action ( 'woocommerce_before_add_to_cart_button', 'ppom_woocommerce_inputs_template_base', 15);
		}
		
		
		// if( apply_filters('ppom_remove_duplicate_fields', true) ) {
		// 	add_action ( 'woocommerce_single_variation', 'ppom_woocommerce_show_fields', 15);
		// }
		
		add_filter('ppom_input_templates_path', 'ppom_hooks_check_theme_path', 10, 3);
		
		
		// Validating before add to cart
		add_filter ( 'woocommerce_add_to_cart_validation', 'ppom_woocommerce_validate_product', 10, 3 );
		
		// Adding meta to cart form product page
		add_filter ( 'woocommerce_add_cart_item_data', 'ppom_woocommerce_add_cart_item_data', 10, 2);
		
		// add_action( 'wp', array($this, 'wp_loaded') );
		
		
		/*
		 * 4- now loading all meta on cart/checkout page from session confirmed that it is loading for cart and checkout
		 */
		
		// Control price calculations on cart page
		if( ppom_get_price_mode() == 'legacy' ) {
		
			add_filter ( 'woocommerce_get_cart_item_from_session', 'ppom_woocommerce_update_cart_fees', 10, 2 );
			add_action( 'woocommerce_cart_calculate_fees', 'ppom_woocommerce_add_fixed_fee' );
		} else {
			
			add_filter ( 'woocommerce_get_cart_item_from_session', 'ppom_price_check_price_matrix', 8, 2 );
			
			/** since 21.3, woocommerce_get_cart_item_from_session replaced with following **/
			// add_action ( 'woocommerce_before_calculate_totals', 'ppom_before_calculate_totals', 999, 1 );
			
			// Above hook has issue so reverting back to old hook for prices
			add_filter ( 'woocommerce_get_cart_item_from_session', 'ppom_price_controller', 10, 2 );
			
			add_action( 'woocommerce_cart_calculate_fees', 'ppom_price_cart_fee' );
			// Calculating weights
			add_action( 'ppom_before_calculate_cart_total', 'ppom_hooks_update_cart_weight', 10, 3);
		}
		
		// Adding scripts
		// add_action( 'wp_enqueue_scripts', 'ppom_woocommerce_load_scripts' );
		
		
		
		add_action( 'woocommerce_cart_loaded_from_session', 'ppom_calculate_totals_from_session');
		
		// Mini/Cart Widget fixed fee
		add_action( 'woocommerce_widget_shopping_cart_before_buttons', 'ppom_woocommerce_mini_cart_fixed_fee');
		
		// Changing price displa on loop for price matrix
	    add_filter('woocommerce_get_price_html', 'ppom_woocommerce_alter_price', 10, 2);
	    // Hiding variation price if dynamic price is enable
	    // add_filter( 'woocommerce_show_variation_price', 'ppom_hide_variation_price_html', 99, 3);
	    
	    // Product default quantity
	    add_filter( 'woocommerce_quantity_input_args', 'ppom_woocommerce_product_default_quantity', 10, 2);
	    // Product Min quantity control for matrix
	    add_filter( 'woocommerce_quantity_input_min', 'ppom_woocommerce_set_min_quantity', 10, 2);
	    // Product Max quantity control for matrix
	    add_filter( 'woocommerce_quantity_input_max', 'ppom_woocommerce_set_max_quantity', 10, 2);
	    // Product Step quantity control for matrix
	    add_filter( 'woocommerce_quantity_input_step', 'ppom_woocommerce_set_quantity_step', 10, 2);
		
		// Show item meta data on cart/checkout pages.
		add_filter ( 'woocommerce_get_item_data', 'ppom_woocommerce_add_item_meta', 10, 2 );
		
		// Control quantity on cart when quantities used
		add_filter( 'woocommerce_add_to_cart_quantity', 'ppom_woocommerce_add_to_cart_quantity', 10, 2);
		
		// Control cart redirect, whed used shortcoe
		add_filter( 'woocommerce_add_to_cart_redirect', 'ppom_hooks_redirect_to_cart_if_shortcode', 10, 1);
		
		// Cart quantity control
		if( ppom_get_price_mode() == 'legacy' ) {
			add_filter( 'woocommerce_cart_item_quantity', 'ppom_woocommerce_control_cart_quantity_legacy', 10, 2);
		} else {
			add_filter( 'woocommerce_cart_item_quantity', 'ppom_woocommerce_control_cart_quantity', 99, 2);
		}
		
		
		// add_filter( 'woocommerce_cart_item_subtotal', 'ppom_woocommerce_item_subtotal', 10, 3);
		add_filter( 'woocommerce_checkout_cart_item_quantity', 'ppom_woocommerce_control_checkout_quantity', 10, 3);
		add_filter( 'woocommerce_order_item_quantity_html', 'ppom_woocommerce_control_oder_item_quantity', 10, 2);
		add_filter( 'woocommerce_email_order_item_quantity', 'ppom_woocommerce_control_email_item_quantity', 10, 2);
		
		/**
		 * it is disabled due to issue in order again WHEN VQ input used
		 * By Najeeb
		 * Date: July 30, 2021
		 * */
		// add_filter( 'woocommerce_order_item_get_quantity', 'ppom_woocommerce_control_order_item_quantity', 10, 2);
		
		// Cart update function
		add_filter( 'woocommerce_update_cart_validation', 'ppom_woocommerce_cart_update_validate', 10, 4);
		
		/**
		 * Adding item_meta to orders 2.0 it is in classes/class-wc-checkout function:
		** create_order() 
		** woocommerce_new_order_item is deprecated
		*/
		// add_action ( 'woocommerce_new_order_item', 'ppom_woocommerce_order_item_meta', 10, 3);
		add_action ( 'woocommerce_checkout_create_order_line_item', 'ppom_woocommerce_order_item_meta', 99, 4);
		
		// Changing display to label in orders
		add_filter( 'woocommerce_order_item_display_meta_key', 'ppom_woocommerce_order_key', 10, 3);
		// Few inputs like file/crop/image need to show meta value in tags
		add_filter( 'woocommerce_order_item_display_meta_value', 'ppom_woocommerce_order_value', 10, 3);
		// Hiding some additional field like ppom_has_quantities
		add_filter( 'woocommerce_order_item_get_formatted_meta_data', 'ppom_woocommerce_hide_order_meta', 10, 2);
		// see: https://github.com/woocommerce/woocommerce/issues/23294
		add_filter( 'woocommerce_is_attribute_in_product_name', '__return_false' );
		
		/*
		 * 7- movnig confirmed/paid orders into another directory
		 * dir_name: confirmed
		*/
		add_action ( 'woocommerce_checkout_order_processed', 'ppom_woocommerce_rename_files', 10, 3);
		
		/** ============ LOCAL Hooks ==================== */
		 add_filter('ppom_field_attributes', 'ppom_hooks_set_attributes', 10, 2);
		 // if jQuery datepicker is selected
		 add_filter('ppom_field_setting', 'ppom_hooks_input_args', 10, 3);
		 // Checkbox validation hook
		 add_filter('ppom_has_posted_field_value', 'ppom_hooks_checkbox_valided', 10, 3);
		 // Change color type to text for rendering
		 add_filter('nmform_attribute_value', 'ppom_hooks_color_to_text_type', 10, 3);
		 // Option show for Pricematrix
		 add_filter('ppom_show_option_price', 'ppom_hooks_show_option_price_pricematrix', 10, 2);
		 /**
		 ** 1- Translating meta settings being saved via admin
		 ** 2- Also saving ppom_id in each fiel
		 **/
		 add_filter('ppom_meta_data_saving', 'ppom_hooks_register_wpml', 10, 2);
		 
		 if( ppom_get_conditions_mode() === 'new' ) { 
		 	add_filter('ppom_input_wrapper_class', 'ppom_hooks_input_wrapper_class_new', 10, 2);
			add_filter('ppom_field_main_wrapper_class', 'ppom_hooks_input_main_wrapper_class', 10, 3);
		 } else {
		 	// add a wrapper class in each input e.g: ppom-input-{data_name}
			add_filter('ppom_input_wrapper_class', 'ppom_hooks_input_wrapper_class', 10, 2);
		 }
		 
		 // Saving cropped image
		 add_filter('ppom_add_cart_item_data', 'ppom_hooks_save_cropped_image', 10, 2);
		 // Formatting the order meta with options price and id
		 add_filter('ppom_order_display_value', 'ppom_hooks_format_order_value', 999, 3);
		 // setting -ve operator for option for negative numbers
		 add_filter('ppom_option_price_operator', 'ppom_hooks_set_option_operator', 99, 3);
		 
		 
		 //add_filter('ppom_cart_fixed_fee', 'ppom_hooks_convert_price_back');
		 
		 // Adding option keys into PPOM options (converted)
		 add_filter('ppom_option_meta', 'update_converted_option_keys', 9, 5);
		 
		 // Shortcode
		 add_shortcode('ppom', 'ppom_hooks_render_shortcode');
		 
		 /** ============ Ajax callbacks ==================== */
		 add_action('wp_ajax_nopriv_ppom_upload_file', 'ppom_upload_file');
		 add_action('wp_ajax_ppom_upload_file', 'ppom_upload_file');
		 add_action('wp_ajax_nopriv_ppom_delete_file', 'ppom_delete_file');
		 add_action('wp_ajax_ppom_delete_file', 'ppom_delete_file');
		 
		 add_action('wp_ajax_ppom_ajax_validation', 'ppom_woocommerce_ajax_validate');
		 add_action('wp_ajax_nopriv_ppom_ajax_validation', 'ppom_woocommerce_ajax_validate');
		 
		 
		 
		 /** ============ Admin hooks ===================== **/		/*
		 * adding a panel on product single page in admin
		 */
		 
		if( version_compare( ppom_get_pro_version(), 17.0, "<" ) && ppom_pro_is_installed() ) {
			
			add_action ( 'add_meta_boxes', 'ppom_admin_product_meta_metabox');
		}
		
		add_action( 'admin_notices', 'ppom_admin_show_notices' );
		
		// Saving settings and fields
		add_action('wp_ajax_ppom_save_form_meta', 'ppom_admin_save_form_meta');
		add_action('wp_ajax_ppom_update_form_meta', 'ppom_admin_update_form_meta');
		add_action('wp_ajax_ppom_delete_meta', 'ppom_admin_delete_meta');
		add_action('wp_ajax_ppom_delete_selected_meta', 'ppom_admin_delete_selected_meta');
		
		/*
		 * saving product meta in admin/product signel page
		 */
		add_action ( 'woocommerce_process_product_meta', 'ppom_admin_process_product_meta');
		
		
		/**
		 * change add to cart text on shop page
		 */
		 //add_filter('woocommerce_loop_add_to_cart_link', array($this, 'change_add_to_cart_text'), 10, 2);
		add_filter( 'woocommerce_product_add_to_cart_url', array($this, 'loop_add_to_cart_url'), 10, 2);
 		add_filter( 'woocommerce_product_add_to_cart_text', array($this, 'loop_add_to_cart_text'), 10, 2);
 		add_filter( 'woocommerce_product_supports', array($this, 'product_supports'), 10, 3);
 		add_action( 'woocommerce_product_duplicate', array($this, 'duplicate_product_meta'), 10, 2 );
		
		
		/*
		 * 8- cron job (shedualed hourly)
		 * to remove un-paid images
		 */
		add_action('do_action_remove_images', 'ppom_files_removed_unused_images');
		// adding scheduale weekly
		add_filter( 'cron_schedules', 'ppom_hooks_weekly_cron_schedule' );

		add_action('admin_footer-edit.php', array($this, 'nm_add_bulk_meta'));
		
		add_action('load-edit.php', array(&$this, 'nm_meta_bulk_action'));
		
		add_action('admin_notices', array(&$this, 'nm_add_meta_notices'));
		
		// Applying meta
		add_action( 'admin_post_ppom_attach', array($this, 'ppom_attach_meta') );
		add_action( 'template_redirect', array($this, 'show_wc_custom_message'));
		
		// Export - Update to Pro Notice
		add_action( 'admin_post_ppom_export_meta', array($this, 'ppom_export_meta') );
		
		/**
		 * adding extra column in products list for meta show
		 * @since 8.0
		 **/
		add_filter('manage_product_posts_columns' , 'ppom_admin_show_product_meta', 999);
		add_action( 'manage_product_posts_custom_column' , 'ppom_admin_product_meta_column', 999, 2 );
		
		/**
		 * re-calculate price matrix prices if 
		 * variation options included from settings of price matrix
		 * @since 8.5
		 **/
		add_filter('ppom_price_matrix_post', 'ppom_adjust_price_matrix_for_option_price', 10, 3);
		
		// WooCommece Advance Order Plugin
		add_filter("woe_fetch_order", 'ppom_hooks_convert_option_json_to_string', 10, 2);
		
		// Remmove form-control class for certain input types like
		// image
		add_filter('ppom_input_classes', array($this, 'input_classes'), 99, 2);
		
		
		// Yes here, Not in admin due to priority issues
		if( version_compare( ppom_get_pro_version(), 17.0, ">=" ) || !ppom_pro_is_installed() ) {
    		add_filter( 'woocommerce_product_data_tabs', array($this, 'add_ppom_meta_tabs') );
    		add_filter( 'woocommerce_product_data_panels', array($this, 'add_ppom_meta_panel') );
    	}
    	
    	// Generating DOM optin IDs (checkbox, radio, select, images, palattes etc)
    	add_filter( 'ppom_dom_option_id', 'ppom_hooks_dom_option_id', 99, 2);
    	
    	// NOTE: Debug only
    	// delete_option('ppom_demo_meta_installed');
    	
    	add_action('in_admin_header', 'ppom_hooks_remove_admin_notices', 99);
	}
	
	/*
	 * ============================================================== All about Admin -> Single Product page ==============================================================
	 */
	 
	 /**
  	 * add to cart button url change
  	 */
  	function loop_add_to_cart_url($url, $product){
  	
  	
  		if( ! $product -> is_in_stock() ) 
  		return $url;
  			
  		$product_id = ppom_get_product_id($product);
  		// $ppom		= new PPOM_Meta( $product_id );
  		$ppom		= new PPOM_Meta( $product_id );
	  	if( ! $ppom->is_exists ){
			return $url;
		}

  		if (!in_array($product->get_type(), array('variable', 'grouped', 'external'))) {
  			// only if can be purchased
  			if ($ppom->is_exists) {
  				return get_permalink($product_id);
  			}
  		}
  		return $url;
  	}
	  
  	/**
  	 * add to cart button text change
  	 */
  	function loop_add_to_cart_text($text, $product){
  		
  		if( ! $product -> is_in_stock() ) 
  			return $text;
  			
  		$product_id = ppom_get_product_id($product);
  		$ppom		= new PPOM_Meta( $product_id );
  		
	  	if( ! $ppom->is_exists ) {
			return $text;
		}
		

  		if (!in_array($product->get_type(), array('variable', 'grouped', 'external'))) {
  			// only if can be purchased
  			if ($ppom->is_exists) {
  				$text = apply_filters('ppom_select_option_text', __('Select options', 'woocommerce'), $text, $product);
  			}
  		}
  		return $text;
  	}
	
  	/**
  	 * Filter woocommerce_product_supports in order to remove support for ajax_add_to_cart for personalized product
  	 */
  	function product_supports($support, $feature, $product) {
  	
  		if ( $feature != "ajax_add_to_cart" )
  			return $support;
		
	  	$product_id = ppom_get_product_id($product);
  		$ppom		= new PPOM_Meta( $product_id );
  		if( ! $ppom->is_exists ) {
			return $support;
		}

  		if (!in_array($product->get_type(), array('variable', 'grouped', 'external'))) {
  			// only if can be purchased
  			if ($ppom->is_exists) {
  				return false;
  			}
  		}
  		return $support;
  	}
  	
  	
  	/** This function is called when a product is duplicated by an admin.
  	 * It checks it the product got nm-woocommerce-personalized-product meta, and if yes,
  	 * copy the meta to the duplicated product. */
  	function duplicate_product_meta($duplicate, $product) {
  		
  		$product_id = ppom_get_product_id($product);
  		$ppom		= new PPOM_Meta( $product_id );
  		
  		if( $ppom->is_exists ) {
  			update_post_meta($duplicate->get_id(), PPOM_PRODUCT_META_KEY, $ppom->meta_id);
  		}	
  	}
	  
	
	/*function wp_loaded() {
		
		// var_dump(DOING_AJAX);
		if( is_cart() || is_checkout() || (defined('DOING_AJAX') && DOING_AJAX && isset($_REQUEST['wc-ajax'])) ) {
			global $WOOCS;
			remove_filter('woocommerce_product_get_price', array($WOOCS, 'raw_woocommerce_price'), 9999, 2);
		}
	}*/
	
	/**
	 * Adds meta groups in admin dropdown to apply on products.
	 *
	 */
	function nm_add_bulk_meta() {
		global $post_type;
			
		if($post_type == 'product' and $all_meta = $this -> get_product_meta_all ()) {
			foreach ( $all_meta as $meta ) {
				?>
<script type="text/javascript">
						jQuery(document).ready(function() {
							jQuery('<option>').val('<?php printf(__("nm_action_%d", "ppom"), $meta->productmeta_id)?>', "ppom").text('<?php _e($meta->productmeta_name)?>').appendTo("select[name='action']");
							jQuery('<option>').val('<?php printf(__("nm_action_%d", "ppom"), $meta->productmeta_id)?>').text('<?php _e($meta->productmeta_name)?>').appendTo("select[name='action2']");
						});
					</script>
<?php
			}
			?>
<script type="text/javascript">
					jQuery(document).ready(function() {
						jQuery('<option>').val('nm_delete_meta').text('<?php _e('Remove Meta', "ppom")?>').appendTo("select[name='action']");
						jQuery('<option>').val('nm_delete_meta').text('<?php _e('Remove Meta', "ppom")?>').appendTo("select[name='action2']");
					});
				</script>
<?php
	    }
	    
	    $wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
	    
	}

	function nm_meta_bulk_action() {
		global $typenow;
		$post_type = $typenow;
			
		if($post_type == 'product') {
				
			// get the action
			$wp_list_table = _get_list_table('WP_Posts_List_Table');  // depending on your resource type this could be WP_Users_List_Table, WP_Comments_List_Table, etc
			$action = $wp_list_table->current_action();
			
			// make sure ids are submitted.  depending on the resource type, this may be 'media' or 'ids'
			if(isset($_REQUEST['post']) && is_array($_REQUEST['post'])){
				$post_ids = array_map('intval', $_REQUEST['post']);
			}
			
			if(empty($post_ids)) return;
			
			// this is based on wp-admin/edit.php
			$sendback = remove_query_arg( array('nm_updated', 'nm_removed', 'untrashed', 'deleted', 'ids'), wp_get_referer() );
			if ( ! $sendback )
				$sendback = admin_url( "edit.php?post_type=$post_type" );
				
			$pagenum = $wp_list_table->get_pagenum();
			$sendback = add_query_arg( 'paged', $pagenum, $sendback );
			
			
			$nm_do_action = ($action == 'nm_delete_meta') ? $action : substr($action, 0, 10);
				
			switch($nm_do_action) {
				case 'nm_action_':
				$nm_updated = 0;
				foreach( $post_ids as $post_id ) {
							
					$meta_id  = array( intval(substr($action, 10)) );
					update_post_meta ( $post_id, '_product_meta_id', $meta_id );
			
					$nm_updated++;
				}
				$sendback = add_query_arg( array('nm_updated' => $nm_updated, 'ids' => join(',', $post_ids)), $sendback );
				break;
				
				case 'nm_delete_meta':
				$nm_removed = 0;
				foreach( $post_ids as $post_id ) {
							
					delete_post_meta ( $post_id, '_product_meta_id' );
			
					$nm_removed++;
				}
				$sendback = add_query_arg( array('nm_removed' => $nm_removed, 'ids' => join(',', $post_ids)), $sendback );
				break;
				
				default: return;
			}
			
			wp_redirect($sendback);
			
			exit();
		}
	}
	/**
	 * display an admin notice on the Products page after updating meta
	 */
	function nm_add_meta_notices() {
		global $post_type, $pagenow;
			
		if($pagenow == 'edit.php' && $post_type == 'product' && isset($_REQUEST['nm_updated']) && (int) $_REQUEST['nm_updated']) {
			$message = sprintf( _n( 'Product meta updated.', '%s Products meta updated.', $_REQUEST['nm_updated'] ), number_format_i18n( $_REQUEST['nm_updated'] ) );
			echo "<div class=\"updated\"><p>{$message}</p></div>";
		}
		elseif($pagenow == 'edit.php' && $post_type == 'product' && isset($_REQUEST['nm_removed']) && (int) $_REQUEST['nm_removed']){
			$message = sprintf( _n( 'Product meta removed.', '%s Products meta removed.', $_REQUEST['nm_removed'] ), number_format_i18n( $_REQUEST['nm_removed'] ) );
			echo "<div class=\"updated\"><p>{$message}</p></div>";	
		}
	}	
	
	function input_classes( $classes, $meta ) {
		
		$type 			= ( isset($meta['type']) ? $meta ['type'] : '');
		
		$no_form_control = array('image');
	
		// removing form-control class	
		if( ! in_array($type, $no_form_control) ) return $classes;
		
		if (($key = array_search('form-control', $classes)) !== false) {
			unset($classes[$key]);
		}
		
		return $classes;
	}
	
	
	function get_product_meta_all() {
		
		global $wpdb;
		
		$qry = "SELECT * FROM " . $wpdb->prefix . PPOM_TABLE_META;
		$res = $wpdb->get_results ( $qry );
		
		return $res;
	}
	
	function get_product_meta($meta_id) {
		
		if( !$meta_id )
			return ;
			
		if ($meta_id == 'None')
			return;
			
		global $wpdb;
		
		$qry = "SELECT * FROM " . $wpdb->prefix . PPOM_TABLE_META . " WHERE productmeta_id = $meta_id";
		$res = $wpdb->get_row ( $qry );
		
		return $res;
	}
	

	public static function activate_plugin() {
		global $wpdb;
	
		/*
		 * meta_for: this is to make this table to contact more then one metas for NM plugins in future in this plugin it will be populated with: forms
		 */
		$forms_table_name = $wpdb->prefix . PPOM_TABLE_META;
		
		$charset_collate = $wpdb->get_charset_collate();
		
		$sql = "CREATE TABLE $forms_table_name (
		productmeta_id INT(5) NOT NULL AUTO_INCREMENT,
		productmeta_name VARCHAR(50) NOT NULL,
		productmeta_validation VARCHAR(3),
        dynamic_price_display VARCHAR(10),
        send_file_attachment VARCHAR(3) NOT NULL,
        show_cart_thumb VARCHAR(3),
		aviary_api_key VARCHAR(40),
		productmeta_style MEDIUMTEXT,
		productmeta_js MEDIUMTEXT,
		productmeta_categories MEDIUMTEXT,
		the_meta MEDIUMTEXT NOT NULL,
		productmeta_created DATETIME NOT NULL,
		PRIMARY KEY  (productmeta_id)
		) $charset_collate;";
		
		require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta ( $sql );
		
		update_option ( "personalizedproduct_db_version", PPOM_DB_VERSION );
		
		// this is to remove un-confirmed files daily
		
		$delete_frequency = ppom_get_option('ppom_remove_unused_images_schedule');
		if ( ! wp_next_scheduled( 'do_action_remove_images' ) ) {
			wp_schedule_event( time(), $delete_frequency, 'do_action_remove_images');
		}
		
		if ( ! wp_next_scheduled( 'setup_styles_and_scripts_wooproduct' ) ) {
			wp_schedule_event( time(), 'daily', 'setup_styles_and_scripts_wooproduct');
		}
		
		// Installing Demo Meta
		self::ppom_install_demo_meta();
		
		
		self::set_ppom_menu_permission();
		
	}
	
	public static function deactivate_plugin() {
		
		// do nothing so far.
		wp_clear_scheduled_hook( 'do_action_remove_images' );
		
		wp_clear_scheduled_hook( 'setup_styles_and_scripts_wooproduct' );
		
		
		self::remove_ppom_menu_permission();
	}
	
	
	/**
	 * Set PPOM Menu Access via Settings
	 * 
	 * */
	public static function set_ppom_menu_permission(){
		
		$ppom_roles = ppom_get_option('ppom_permission_mfields', array());
		foreach($ppom_roles as $r){
			
			// if( $r == 'administrator' ) continue;
			
			$wp_role = get_role($r);
			if($wp_role)
				$wp_role->add_cap('ppom_options_page');
		}
	}
	
	/**
	 * Remove PPOM Menu Access via Settings
	 * 
	 * */
	public static function remove_ppom_menu_permission(){
		
		global $wp_roles;

    	$all_roles = $wp_roles->roles;	
    	$editable_roles = apply_filters('editable_roles', $all_roles);
		foreach($editable_roles as $role => $data){
			
			// if( $r == 'administrator' ) continue;
			
			$wp_role = get_role($role);
			$wp_role->remove_cap('ppom_options_page');
		}
	}
	
	
	/*
	 * cloning product meta for admin
	 * being called from: templates/admin/create-form.php
	 */
	function clone_product_meta($meta_id){
		
		// if(!ppom_security_role()){
		// 	_e ("Sorry, you are not allowed to perform this action", 'ppom');
		// 	die(0);
		// }

		global $wpdb;
		
		$forms_table_name = $wpdb->prefix . PPOM_TABLE_META;
		
		$sql = "INSERT INTO $forms_table_name
		(productmeta_name, aviary_api_key, productmeta_style,productmeta_categories, the_meta, productmeta_created) 
		SELECT productmeta_name, aviary_api_key, productmeta_style,productmeta_categories, the_meta, productmeta_created 
		FROM $forms_table_name 
		WHERE productmeta_id = %d;";
		
		$result = $wpdb -> query($wpdb -> prepare($sql, array($meta_id)));
		
		/* var_dump($result);
		
		$wpdb->show_errors();
		$wpdb->print_error(); */
		
	}
	
	/*
	 * returning NM_Inputs object
	*/
	function get_all_inputs() {
	
		$nm_inputs = PPOM_Inputs();
		// webcontact_pa($this->plugin_meta);
	
		// registering all inputs here
	
		$all_inputs = array (
				
				'text' 		=> $nm_inputs->get_input ( 'text' ),
				'textarea' 	=> $nm_inputs->get_input ( 'textarea' ),
				'select' 	=> $nm_inputs->get_input ( 'select' ),
				'radio' 	=> $nm_inputs->get_input ( 'radio' ),
				'checkbox' 	=> $nm_inputs->get_input ( 'checkbox' ),
				'email' 	=> $nm_inputs->get_input ( 'email' ),
				'date' 		=> $nm_inputs->get_input ( 'date' ),
				'number' 	=> $nm_inputs->get_input ( 'number' ),
				'hidden' 	=> $nm_inputs->get_input ( 'hidden' ),
				// 'masked' 	=> $nm_inputs->get_input ( 'masked' ),
		);
		
		return apply_filters('ppom_all_inputs', $all_inputs, $nm_inputs);
	}


	public static function ppom_decode_entities($arr){
		// asort($arr);
		$ReturnArray = array();
		foreach ($arr as $k => $v)
			// ppom_pa($v);
	        $ReturnArray[$k] = (is_array($v) || is_object($v)) ? self::ppom_decode_entities($v) : html_entity_decode($v);
	    return $ReturnArray;
	}
	
	
	// Since Version 17.0
	// Adding demo
	public static function ppom_install_demo_meta(){
		
		if( get_option('ppom_demo_meta_installed') ) return;
		
		global $wpdb;
		//get the csv file
		// ppom_pa($_FILES);
	    $demo_file = PPOM_PATH.'/assets/ppom-basic-meta.json';
	    if( ! file_exists($demo_file) ) return;
	    
	    $handle = fopen($demo_file,"r");
	    
	    $ppom_meta = '';
		if ($handle) {
		    while (!feof($handle)) {
		      $ppom_meta .= fgets($handle, 50000);
		    }
		
		    fclose($handle);
		}
		
		$ppom_meta = json_decode($ppom_meta);
		$ppom_meta = self::ppom_decode_entities($ppom_meta);
		// ppom_pa( $ppom_meta ); exit;
	    
	    $meta_count = 0;
	    foreach($ppom_meta as $meta) {
	    	
	    	$table = $wpdb->prefix . PPOM_TABLE_META;
	    	$qry = "INSERT INTO {$table} SET ";
	    	$meta_count++;
	    	
	    		foreach($meta as $key => $val) {
	    			
	    			if( $key == 'productmeta_id' ) continue;
	    			
	    			if( $key == 'productmeta_name' ) {
	    				$val = 'PPOM Demo Field';
	    			}
	    			
	    			$qry .= "{$key}='{$val}',";
	    		}
	    		
	    		$qry = substr($qry, 0, -1);
	    		// print $qry; exit;
	    		$res = $wpdb->query( $qry );
	    
			    /*$wpdb->show_errors();
			    $wpdb->print_error();
			    exit;*/
	    }
	    
	    update_option('ppom_demo_meta_installed', 1);
	}
	
	
	function ppom_attach_meta() {
		
		$product_id 	= isset($_GET['productid']) ? intval($_GET['productid']) : '';
		$meta_id		= isset($_GET['metaid']) ? intval($_GET['metaid']) : '';
		$meta_title 	= isset($_GET['metatitle']) ? sanitize_title($_GET['metatitle']) : '';
		
		ppom_attach_fields_to_product($meta_id, $product_id);
		
		$product_url = add_query_arg('ppom_title', $meta_title, get_permalink($product_id));
		wp_redirect( $product_url );
   		exit;
	}
	
	function show_wc_custom_message() {
		
	    if ( is_product() && isset($_GET['ppom_title'])) {
	    	
	    	$meta_title = sanitize_text_field($_GET['ppom_title']);
	    	wc_add_notice( sprintf(__("PPOM Meta Successfully Changed to - %s", "ppom"), $meta_title));
	    }
	}
	
	// Update to PRO Notice
	function ppom_export_meta() {
		
		// if( ppom_pro_is_installed() ) return '';
		$buy_pro = 'https://najeebmedia.com/ppom';
		$args = array("link_url" => $buy_pro, "link_text"=>'Buy $30.00', 'back_link'=>true);
		wp_die("Update to PRO Version for Export/Import","Update to PRO", $args);
	}
	
	function add_ppom_meta_tabs( $default_tabs ) {
    	
    	$default_tabs['ppom_tab'] = array(
		      'label'   =>  __( 'PPOM Fields', 'ppom' ),
		      'target'  =>  'ppom_meta_data_tab',
		      'priority' => 60,
		      'class'   => array('show_if_simple','show_if_variable')
		  );
		  
		return $default_tabs;
    }
    
    
    /**
	 * Contents of the gift card options product tab.
	 */
	function add_ppom_meta_panel() {
	
		global $post;
		
		echo '<div id="ppom_meta_data_tab" class="panel woocommerce_options_panel"	>';
		ppom_meta_list( $post );
		echo '</div>';

	}
	
	
}