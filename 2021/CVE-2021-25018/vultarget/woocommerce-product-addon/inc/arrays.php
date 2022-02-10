<?php
/**
 * Arrays contining settings/meta detail
 **/
 
if( ! defined('ABSPATH') ) die('Not Allowed.');


/**
 * Get plugin meta
*/
function ppom_get_plugin_meta(){

	$ppom_meta = array('name'	=> 'PPOM',
				'dir_name'		=> '',
				'shortname'		=> 'nm_personalizedproduct',
				'path'			=> PPOM_PATH,
				'url'			=> PPOM_URL,
				'db_version'	=> 3.12,
				'logo'			=> PPOM_URL . '/images/logo.png',
				'menu_position'	=> 90,
				'ppom_bulkquantity'	=> PPOM_WP_PLUGIN_DIR . '/ppom-addon-bulkquantity/classes/input.bulkquantity.php',
				'ppom_eventcalendar'	=> PPOM_WP_PLUGIN_DIR . '/ppom-addon-eventcalendar/classes/input.eventcalendar.php',
				'ppom_fixedprice'	=> PPOM_WP_PLUGIN_DIR . '/ppom-addon-fixedprice/classes/input.fixedprice.php',
	);
	
	return apply_filters('ppom_plugin_meta', $ppom_meta);
}

/**
 * Return cols for inputs
*/
function ppom_get_input_cols() {
	
	$ppom_cols = array(
		2 => '2 Col',
		3 => '3 Col', 
		4 => '4 Col',
		5 => '5 Col',
		6 => '6 Col',
		7 => '7 Col',
		8 => '8 Col',
		9 => '9 Col',
		10 => '10 Col',
		11 => '11 Col',
		12 => '12 Col'
	);
	
	return apply_filters('ppom_field_cols', $ppom_cols);
}

/**
 * Return visibility options for inputs
*/
function ppom_field_visibility_options() {
	
	$visibility_options = array(
		'everyone'	=> __('Everyone'),
		'guests'	=> __('Only Guests'),
		'members'	=> __('Only Members'),
		'roles'		=> __('By Role')
	);
								
	return apply_filters('ppom_field_visibility_options', $visibility_options);
}

/**
 * Get timezone list
*/
function ppom_array_get_timezone_list($selected_regions, $show_time) {
	
	if( $selected_regions == 'All' ) {
	    $regions = array(
	        DateTimeZone::AFRICA,
	        DateTimeZone::AMERICA,
	        DateTimeZone::ANTARCTICA,
	        DateTimeZone::ASIA,
	        DateTimeZone::ATLANTIC,
	        DateTimeZone::AUSTRALIA,
	        DateTimeZone::EUROPE,
	        DateTimeZone::INDIAN,
	        DateTimeZone::PACIFIC,
	    );
	} else {
		$selected_regions = explode(",", $selected_regions);
		$tz_regions = array();
		
		foreach($selected_regions as $region) {
			
			switch($region) {
				case 'AFRICA':
					$tz_regions[] = DateTimeZone::AFRICA;
				break;
				case 'AMERICA':
					$tz_regions[] = DateTimeZone::AMERICA;
				break;
				case 'ANTARCTICA':
					$tz_regions[] = DateTimeZone::ANTARCTICA;
				break;
				case 'ASIA':
					$tz_regions[] = DateTimeZone::ASIA;
				break;
				case 'ATLANTIC':
					$tz_regions[] = DateTimeZone::ATLANTIC;
				break;
				case 'AUSTRALIA':
					$tz_regions[] = DateTimeZone::AUSTRALIA;
				break;
				case 'EUROPE':
					$tz_regions[] = DateTimeZone::EUROPE;
				break;
				case 'INDIAN':
					$tz_regions[] = DateTimeZone::INDIAN;
				break;
				case 'PACIFIC':
					$tz_regions[] = DateTimeZone::PACIFIC;
				break;
			}
		}
		$regions = $tz_regions;
	}
	
    $timezones = array();
    foreach( $regions as $region ) {
        $timezones = array_merge( $timezones, DateTimeZone::listIdentifiers( $region ) );
    }

    $timezone_offsets = array();
    foreach( $timezones as $timezone ) {
        $tz = new DateTimeZone($timezone);
        $timezone_offsets[$timezone] = $tz->getOffset(new DateTime);
    }

    // sort timezone by timezone name
    ksort($timezone_offsets);

    $timezone_list = array();
    foreach( $timezone_offsets as $timezone => $offset ) {
        $offset_prefix = $offset < 0 ? '-' : '+';
        $offset_formatted = gmdate( 'H:i', abs($offset) );

        $pretty_offset = "UTC${offset_prefix}${offset_formatted}";
        
        $t = new DateTimeZone($timezone);
        $c = new DateTime(null, $t);
        $current_time = $c->format('g:i A');

		if( $show_time == 'on' ) {
        	$timezone_list[$timezone] = "(${pretty_offset}) $timezone - $current_time";
		} else {
			$timezone_list[$timezone] = "(${pretty_offset}) $timezone";
		}
    }

    return $timezone_list;
}

/**
 * PPOM WC settings array
*/
function ppom_array_settings() {
	
	$v18_info_url = 'https://najeebmedia.com/blog/ppom-version-18-0-better-price-manipulation-currency-switcher/';
	$more_price_details = '<a target="_blank" href="'.esc_attr($v18_info_url).'">More Details<a>';
	$ppom_fields = $ppom_settings_url = admin_url( 'admin.php?page=ppom');

	$ppom_admin_url = admin_url('admin-post.php');
	$ppom_migrate_url   = add_query_arg(array('action'=>'ppom_migrate_settings_panel'), $ppom_admin_url);

	$ppom_settings = array(
    	
    	array(
			'title' => 'Settings Panel Migration',
			'type'  => 'title',
			'desc'	=> __('Please migrate settings to new settings panel framework. <a href="'.esc_url($ppom_migrate_url).'" class="page-title-action">Start Migration</a>','ppom'),
			'id'    => 'ppom_settings_migration',
		),
		
		array(
			'title' => 'PPOM Labels',
			'type'  => 'title',
			'desc'	=> __('Following settings help you the control and customize plugin as per your need. <a href="'.esc_url($ppom_fields).'">PPOM Fields Manager</a>','ppom'),
			'id'    => 'ppom_labels_settings',
		),
		
		array(
            'title'		=> __( 'Option Total', 'ppom' ),
            'type'		=> 'text',
            'desc'		=> __( 'Label For Price Table', 'ppom' ),
            'default'	=> __('Option Total', 'ppom'),
            'id'		=> 'ppom_label_option_total',
            'css'   	=> 'min-width:300px;',
			'desc_tip'	=> true,
        ),
        
        array(
            'title'		=> __( 'Option Total Suffix', 'ppom' ),
            'type'		=> 'text',
            'desc'		=> __( 'E.g for Tax/Va info like. Vat included', 'ppom' ),
            'default'	=> __('', 'ppom'),
            'id'		=> 'ppom_label_option_total_suffex',
            'css'   	=> 'min-width:300px;',
			'desc_tip'	=> true,
        ),
        array(
            'title'		=> __( 'Product Price', 'ppom' ),
            'type'		=> 'text',
            'desc'		=> __( 'Label For Price Table', 'ppom' ),
            'default'	=> __('Product Price', 'ppom'),
            'id'		=> 'ppom_label_product_price',
            'css'   	=> 'min-width:300px;',
			'desc_tip'	=> true,
        ),
        array(
            'title'		=> __( 'Total', 'ppom' ),
            'type'		=> 'text',
            'desc'		=> __( 'Label For Price Table', 'ppom' ),
            'default'	=> __('Total', 'ppom'),
            'id'		=> 'ppom_label_total',
            'css'   	=> 'min-width:300px;',
			'desc_tip'	=> true,
        ),
        array(
            'title'		=> __( 'Fixed Fee', 'ppom' ),
            'type'		=> 'text',
            'desc'		=> __( 'Label For Price Table', 'ppom' ),
            'default'	=> __('Fixed Fee', 'ppom'),
            'id'		=> 'ppom_label_fixed_fee',
            'css'   	=> 'min-width:300px;',
			'desc_tip'	=> true,
        ),
        array(
            'title'		=> __( 'Discount Price', 'ppom' ),
            'type'		=> 'text',
            'desc'		=> __( 'Label For Price Table', 'ppom' ),
            'default'	=> __('Discount Price', 'ppom'),
            'id'		=> 'ppom_label_discount_price',
            'css'   	=> 'min-width:300px;',
			'desc_tip'	=> true,
        ),
        array(
            'title'		=> __( 'Total Discount', 'ppom' ),
            'type'		=> 'text',
            'desc'		=> __( 'Label For Price Table', 'ppom' ),
            'default'	=> __('Total Discount', 'ppom'),
            'id'		=> 'ppom_label_total_discount',
            'css'   	=> 'min-width:300px;',
			'desc_tip'	=> true,
        ),
        
        array(
				'title'          => __( 'Disable Bootstrap', 'ppom' ),
				'type'          => 'checkbox',
				'label'         => __( 'Yes', 'ppom' ),
				'default'       => 'no',
				'id'            => 'ppom_disable_bootstrap',
				'desc'          => __( 'Bootstrap JS is being loaded from CDN, it will disable if your site already loading it.', 'ppom' ),
			),
			
		array(
				'title'          => __( 'Enable Legacy Inputs Rendering', 'ppom' ),
				'type'          => 'checkbox',
				'label'         => __( 'Yes', 'ppom' ),
				'default'       => 'no',
				'id'            => 'ppom_enable_legacy_inputs_rendering',
				'desc'			=> __("PPOM Version 22.0 is major update, if some issues occur you can revert back to old version by this.",'ppom')
			),
			
		array(
				'title'          => __( 'Disable FontAwesome', 'ppom' ),
				'type'          => 'checkbox',
				'label'         => __( 'Yes', 'ppom' ),
				'default'       => 'no',
				'id'            => 'ppom_disable_fontawesome',
				'desc'          => __( 'FontAwesome are being loaded from CDN, it will disable if your site already loading it.', 'ppom' ),
			),
		array(
				'title'          => __( 'Enable Legacy Price Calculations', 'ppom' ),
				'type'          => 'checkbox',
				'label'         => __( 'Yes', 'ppom' ),
				'default'       => 'no',
				'id'            => 'ppom_legacy_price',
				'desc'          => __( 'Yes. '.$more_price_details, 'ppom' ),
			),
		array(
				'title'         => __('PPOM Permissions', 'ppom' ),
				'type'          => 'ppom_multi_select',
				'label'         => __('Button', 'ppom' ),
				'default'       => 'administrator',
				'placeholder'   =>'choose role',
				'options'		=> ppom_get_all_editable_roles(),
				'id'            => 'ppom_permission_mfields',
				'desc'          => __( 'You can set permissions here so PPOM fields can be managed by different roles', 'ppom' ),
				'desc_tip'      => true,
			),
        
	    array(
			'type' => 'sectionend',
			'id'   => 'ppom_labels_settings',
		),
			
		array(
            'name'     => __( 'Advance Features (PRO)', 'ppom' ),
            'type'     => 'title',
            'desc'     => __('These options will work when PRO version is installed', 'ppom'),
            'id'       => 'ppom_pro_features'
        ),
        
        array(
				'title'          => __( 'Hide Product Price?', 'ppom' ),
				'type'          => 'checkbox',
				'label'         => __( 'Yes', 'ppom' ),
				'default'       => 'no',
				'id'            => 'ppom_hide_product_price',
				'desc'          => __( 'Hides Product core price under price Title (When PPOM Fields attached)', 'ppom' ),
				
			),
		
		array(
				'title'          => __( 'Hide Variable Product Price?', 'ppom' ),
				'type'          => 'checkbox',
				'label'         => __( 'Yes', 'ppom' ),
				'default'       => 'no',
				'id'            => 'ppom_hide_variable_product_price',
				'desc'          => __( 'Hides Variable Product core price under price Title (When PPOM Fields attached)', 'ppom' ),
				
			),
			
			
		array(
				'title'          => __( 'Hide Options Price?', 'ppom' ),
				'type'          => 'checkbox',
				'label'         => __( 'Yes', 'ppom' ),
				'default'       => 'no',
				'id'            => 'ppom_hide_option_price',
				'desc'          => __( 'Hides options price in Selec/Radio/Checkbox/Image display prices with label', 'ppom' ),
				
			),
			
		array(
				'title'          => __( 'Taxable Options Price?', 'ppom' ),
				'type'          => 'checkbox',
				'label'         => __( 'Yes', 'ppom' ),
				'default'       => 'no',
				'id'            => 'ppom_taxable_option_price',
				'desc'          => __( 'Apply tax settings on option prices from WooCommerce->Tax', 'ppom' ),
				
			),
			
		array(
				'title'          => __( 'Clear Fields after Add to Cart?', 'ppom' ),
				'type'          => 'checkbox',
				'label'         => __( 'Yes', 'ppom' ),
				'default'       => 'no',
				'id'            => 'ppom_hide_clear_fields',
				'desc'          => __( 'Empty all fields on Product page after to cart.', 'ppom' ),
				
			),
			
		array(
				'title'          => __( 'Enable PPOM REST API?', 'ppom' ),
				'type'          => 'checkbox',
				'label'         => __( 'Yes', 'ppom' ),
				'default'       => 'no',
				'id'            => 'ppom_api_enable',
				'desc'          => __( 'Check this option to enable PPOM REST API', 'ppom' ),
			),
			
		array(
			'title'          => __( 'Use optimized Price Table Caculation (BETA)', 'ppom' ),
			'type'          => 'checkbox',
			'label'         => __( 'Yes', 'ppom' ),
			'default'       => 'no',
			'id'            => 'ppom_price_table_v2',
			'desc'          => __( 'A Fast and Optimized script to caculate price on product page in Table.', 'ppom' ),
		),
		
		array(
			'title'          => __( 'Enable New Conditional Logic Script', 'ppom' ),
			'type'          => 'checkbox',
			'label'         => __( 'Yes', 'ppom' ),
			'default'       => 'no',
			'id'            => 'ppom_new_conditions',
			'desc'          => __( 'A faster approach to load conditional fields. Beta version, please report bug in new conditional script.', 'ppom' ),
		),
		
		array(
			'title'          => __( 'Do not send Product Meta to PayPal Invoice', 'ppom' ),
			'type'          => 'checkbox',
			'label'         => __( 'Yes', 'ppom' ),
			'default'       => 'no',
			'id'            => 'ppom_disable_meta_paypal_invoice',
			'desc'          => __( 'Product meta will not be sent to PayPal invoice, only the Item name will be sent to invoice', 'ppom' ),
		),
		
		array(
            'title'		=> __( 'Select Option Label', 'ppom' ),
            'type'		=> 'text',
            'desc'		=> __( 'Label For Price Table', 'ppom' ),
            'default'	=> __('Select Options', 'ppom'),
            'id'		=> 'ppom_label_select_option',
            'css'   	=> 'min-width:300px;',
			'desc_tip'	=> true,
        ),
			
			
		array(
            'title' => __( 'PPOM API Secret Key', 'ppom' ),
            'type' => 'text',
            'desc' => __( 'Enter any characters to create a secret key. This key must be set while requesting to API', 'ppom' ),
            'id'   => 'ppom_rest_secret_key',
            'css'   		=> 'min-width:300px;',
			'desc_tip'		=> true,
        ),
        
        array(
                'title'             => __( 'Delete Un-used images', 'ppom' ),
                'type'              => 'select',
                'label'             => __( 'Button', 'ppom' ),
                'default'           => 'daily',
                'options' => array( 'daily'=>__('Daily','ppom'),
                                    'weekly'=> __('Weekly','ppom'),
                                    'monthly'=> __('Monthly','ppom'),
                                ),
                'id'       => 'ppom_remove_unused_images_schedule',
                'desc'       => __( 'Set duration to uploaded images of abandoned cart. Re-activate plugin to when update this option', 'ppom' ),
                'desc_tip'      => true,
            ),
            
        array(
                'title'             => __( 'Meta Group Overrides', 'ppom' ),
                'type'              => 'select',
                'label'             => __( 'Button', 'ppom' ),
                'default'           => 'default',
                'options' => array( 'default'=>__('Default','ppom'),
                                    'category_override'=> __('Category Overrides Individual Assignment','ppom'),
                                    'individual_override'=> __('Individual Overrides Category Assignment','ppom'),
                                ),
                'id'       => 'ppom_meta_overrides',
                'desc'       => __( 'Leave if default if not sure.', 'ppom' ),
                'desc_tip'      => true,
        ),
        
        array(
                'title'             => __( 'Meta Group Priority', 'ppom' ),
                'type'              => 'select',
                'label'             => __( 'Button', 'ppom' ),
                'default'           => 'default',
                'options' => array( 'category_first'=>__('Category First','ppom'),
                                    'individual_first'=> __('Individual First','ppom'),
                                ),
                'id'       => 'ppom_meta_priority',
                'desc'       => __( 'Leave if default if not sure.', 'ppom' ),
                'desc_tip'      => true,
            ),
            
    	array(
                'title'             => __( 'Price Table Position', 'ppom' ),
                'type'              => 'select',
                'label'             => __( 'Button', 'ppom' ),
                'default'           => 'after',
                'options' => array( 'after'=>__('After PPOM Fields','ppom'),
                                    'before'=> __('Before  PPOM Fields','ppom'),
                                ),
                'id'       => 'ppom_price_table_location',
                'desc'       => __( 'Set the location to render Price Table on Front-end', 'ppom' ),
                'desc_tip'      => true,
            ),
        
        array(
			'type' => 'sectionend',
			'id'   => 'ppom_pro_features',
		),
        
		);
		
	return apply_filters('ppom_settings_data', $ppom_settings);
}

/**
 * PPOM Scripts Vars
 * It only used for addon via this function "ppom_hooks_load_input_scripts"
 * cart-edit-addon
 * svg-addon
*/
function ppom_array_get_js_input_vars( $product, $args = null ) {
	
	// ppom_pa($args);
	$defaults = array (
 		'wc_no_decimal' 		=> wc_get_price_decimals(),
 		'show_price_per_unit'	=> false,
	);
	
	// Parse incoming $args into an array and merge it with $defaults
	$args					= wp_parse_args( $args, $defaults );
	$decimal_palces 		= $args['wc_no_decimal'];
	$show_price_per_unit	= $args['show_price_per_unit'];
	
	$product_id 		= ppom_get_product_id($product);
	$ppom				= new PPOM_Meta( $product_id );
	$ppom_meta_settings = $ppom->ppom_settings;
    $ppom_meta_fields	= $ppom->fields;
	
	if( !empty($ppom_id) ) {
		$ppom_meta_fields	= $ppom->get_fields_by_id($ppom_id);
		$ppom_meta_settings	= $ppom->get_settings_by_id($ppom_id);
	}
	
	$ppom_meta_fields_updated = array();
	foreach ($ppom_meta_fields as $index => $fields_meta) {
		
		$type			= isset($fields_meta['type']) ? $fields_meta['type'] : '';
		$title			= ( isset($fields_meta['title']) ? $fields_meta ['title'] : '');
		$data_name		= ( isset($fields_meta['data_name']) ? $fields_meta ['data_name'] : $title);
		
		$fields_meta['data_name']		= sanitize_key( $data_name );
		$fields_meta['title']			= stripslashes($title);
		
		$fields_meta['field_type'] = apply_filters('ppom_js_fields', $type, $fields_meta);

		// Some field specific settings
		switch( $type ) {
		
			case 'daterange':
				// Check if value is in GET 
				if( !empty($_GET[$data_name]) ) {
					
					$value = sanitize_text_field($_GET[$data_name]);
					$to_dates = explode(' - ', $value);
					$fields_meta['start_date'] = $to_dates[0];
					$fields_meta['end_date'] = $to_dates[0];
				}
	        break;
	        
	        case 'color':
	        	// Check if value is in GET 
				if( !empty($_GET[$data_name]) ) {
					
					$fields_meta['default_color'] = sanitize_text_field($_GET[$data_name]);
				}
			break;
			
			case 'bulkquantity':
					
				$fields_meta['options'] = stripslashes($fields_meta['options']);
				
				// To make bulkquantity option WOOCS ready
				$bulkquantities_options = json_decode($fields_meta['options'], true);
				$bulkquantities_new_options = array();
				foreach($bulkquantities_options as $bq_opt) {
					$bq_array = array();
					foreach($bq_opt as $key => $value) {
						
						if( $key != 'Quantity Range' ) {
							$bq_array[$key] = apply_filters('ppom_option_price', $value);
						} else {
							$bq_array[$key] = $value;
						}
					}
					$bulkquantities_new_options[] = $bq_array;
				}
				
				$fields_meta['options'] = json_encode($bulkquantities_new_options);
			break;
		}
		
		$ppom_meta_fields_updated[] = $fields_meta;
		 
	}
	
	
	$js_vars = array();
	$js_vars['ajaxurl'] 		= admin_url( 'admin-ajax.php', (is_ssl() ? 'https' : 'http') );
	$js_vars['ppom_inputs'] 	= $ppom_meta_fields_updated;
	$js_vars['field_meta'] 		= $ppom_meta_fields_updated;
	$js_vars['ppom_validate_nonce'] = wp_create_nonce( 'ppom_validating_action' );
	$js_vars['wc_thousand_sep']	= wc_get_price_thousand_separator();
	$js_vars['wc_currency_pos']	= get_option( 'woocommerce_currency_pos' );
	$js_vars['wc_decimal_sep']	= get_option('woocommerce_price_decimal_sep');
	$js_vars['wc_no_decimal']	= $decimal_palces;
	$variation_id = '';
	$context		= 'product';
	$js_vars['wc_product_price']= ppom_get_product_price($product, $variation_id, $context);
	$js_vars['wc_product_regular_price']= ppom_get_product_regular_price($product);
	$ppom_label_discount_price = ppom_get_option('ppom_label_discount_price', __( 'Discount Price', 'ppom' ));
	$ppom_label_product_price = ppom_get_option('ppom_label_product_price', __( 'Product Price', 'ppom' ));
	$ppom_label_option_total = ppom_get_option('ppom_label_option_total', __( 'Option Total', 'ppom' ));
	$ppom_label_fixed_fee = ppom_get_option('ppom_label_fixed_fee', __( 'Fixed Fee', 'ppom' ));
	$ppom_label_total_discount = ppom_get_option('ppom_label_total_discount', __( 'Total Discount', 'ppom' ));
	$ppom_label_total = ppom_get_option('ppom_label_total', __( 'Total', 'ppom' ));
	$js_vars['total_discount_label']	= sprintf(__("%s", 'ppom'), $ppom_label_total_discount);
	$js_vars['price_matrix_heading']	= sprintf(__("%s", 'ppom'), $ppom_label_discount_price);
	$js_vars['product_base_label']	= sprintf(__("%s", 'ppom'), $ppom_label_product_price);
	$js_vars['option_total_label']	= sprintf(__("%s", 'ppom'), $ppom_label_option_total);
	$js_vars['fixed_fee_heading']	= sprintf(__("%s", 'ppom'), $ppom_label_fixed_fee);
	$js_vars['total_without_fixed_label']	= sprintf(__("%s", 'ppom'), $ppom_label_total);
	$js_vars['product_quantity_label'] = __("Product Quantity", "ppom");
	$js_vars['product_title'] = sprintf(__("%s", "ppom"), $product->get_title());
	$js_vars['per_unit_label'] = __("unit", "ppom");
	$js_vars['show_price_per_unit'] = $show_price_per_unit;
	$js_vars['text_quantity'] = __("Quantity","ppom");
	$js_vars['show_option_price'] =  $ppom->price_display;
	$js_vars['is_shortcode'] = 'no';
	$js_vars['plugin_url'] = PPOM_URL;
	$js_vars['is_mobile'] = ppom_is_mobile();
	$js_vars['product_id'] = $product_id;
	$js_vars['tax_prefix'] = ppom_tax_label_display();
	
	return apply_filters('ppom_input_vars', $js_vars, $product);
}

/**
 * Showing Tax prefix
 * @since 20.5
*/
function ppom_tax_label_display() {
	/*if ( wc_tax_enabled() && 'excl' === get_option( 'woocommerce_tax_display_shop' ) &&  get_option( 'woocommerce_price_display_suffix' ) !== '' ) {
		return sprintf(__("%s", 'ppom'), get_option( 'woocommerce_price_display_suffix' ));
	}*/
	
	$suffex = ppom_get_option( 'ppom_label_option_total_suffex' );
	if ( wc_tax_enabled() && $suffex !== '' ) {
		return sprintf(__("%s", 'ppom'), $suffex);
	}
}

/**
 * Get PPOM inputs
*/
function ppom_array_all_inputs(){
	
	$all_inputs = array(
		'core' => array(
			'text',
			'textarea',
			'select',
			'radio',
			'checkbox',
			'email',
			'date',
			'number',
			'hidden',
			'daterange',
			'color',
			'file',
			'image',
			'timezone',
			'quantities',
			'cropper',
			'pricematrix',
			'section',
			'palettes',
			'audio',
			'measure',
			'divider'
		)
	);
		
	return apply_filters('ppom_all_inputs_array', $all_inputs);
}

/**
 * Get all PPOM addons details
*/
function ppom_array_get_addons_details(){
	
	$ppom_site_url = 'https://najeebmedia.com/wordpress-plugin/woocommerce-personalized-product-option/';
	
	$addons = array(
		array(
			'title'   => __( 'Texter', 'ppom' ),
			'desc'    => __( 'PPOM Texter Addon is the best and simple solution for web2print business using WooCommerce. Now define a fixed position and area for Text in your Templates like on Mug, T-shirt or Visiting Cards with preset font family, size. The client will fill the text with his all of its attributes and send to cart. It’s like a smart Product Designer. Multiple templates can also be attached to one product.', 'ppom' ),
			'actions' => array(
				array(
					'title' => __( 'Demo', 'ppom' ),
					'link'  => 'https://ppom.nmediahosting.com/product/design-shirt-with-ppom-texter/'
				),
				array(
					'title' => __( 'Quick Video', 'ppom' ),
					'link'  => ''
				),
				array(
					'title' => __( 'More Info', 'ppom' ),
					'link'  => $ppom_site_url.'#ppom-texter'
				),
			)
		),
		array(
			'title'   => __( 'WooCommerce Package Price', 'ppom' ),
			'desc'    => __( 'Sometimes prices are very complex like for a printing company, they are selling their visiting cards in Packages.So Package Price Add-on allows admin to set prices against package. It’s usage is very simple, just add quantity (package) and it’s price. There is also option to set unit like you are selling visiting cards then unit may called as “cards”.', 'ppom' ),
			'actions' => array(
				array(
					'title' => __( 'Demo', 'ppom' ),
					'link'  => 'https://ppom.nmediahosting.com/product/patient-ninja/'
				),
				array(
					'title' => __( 'Quick Video', 'ppom' ),
					'link'  => ''
				),
				array(
					'title' => __( 'More Info', 'ppom' ),
					'link'  => $ppom_site_url.'#woocommercepackageprice'
				),
			)
		),
		array(
			'title'   => __( 'Fields PopUp', 'ppom' ),
			'desc'    => __( 'PPOM Fields PopUp wrap all PPOM fields inside a popup. A product with large number of fields can now has simple button with customized label. To enable this PopUp just one click required in product edit page. For more details please visit Demo or watch video.', 'ppom' ),
			'actions' => array(
				array(
					'title' => __( 'Demo', 'ppom' ),
					'link'  => 'https://ppom.nmediahosting.com/product/ppom-fields-popup/'
				),
				array(
					'title' => __( 'Quick Video', 'ppom' ),
					'link'  => 'https://vimeo.com/309118167'
				),
				array(
					'title' => __( 'More Info', 'ppom' ),
					'link'  =>  $ppom_site_url.'#fieldspopup'
				),
			)
		),
		array(
			'title'   => __( 'Google Font Map Picker', 'ppom' ),
			'desc'    => __( 'Google Font and Map Picker has two input fields. Font selector loads fonts from Google and client can pick font and can see live preview of font effect. Admin can also filter font families and set Custom Fonts. Google Map fetch coordinate based on address and show map.', 'ppom' ),
			'actions' => array(
				array(
					'title' => __( 'Demo', 'ppom' ),
					'link'  => 'https://ppom.nmediahosting.com/product/google-font-picker-ppom/'
				),
				array(
					'title' => __( 'Quick Video', 'ppom' ),
					'link'  => 'https://vimeo.com/377580122'
				),
				array(
					'title' => __( 'More Info', 'ppom' ),
					'link'  => $ppom_site_url.'#googlefontandmappicker'
				),
			)
		),
		array(
			'title'   => __( 'Image DropDown', 'ppom' ),
			'desc'    => __( 'PPOM Image DropDown Addon show images inside a select box. The title, description, and prices can be added along with all images. It’s best when you have a long list of images and don’t want to use Image Type input.', 'ppom' ),
			'actions' => array(
				array(
					'title' => __( 'Demo', 'ppom' ),
					'link'  => 'https://ppom.nmediahosting.com/product/image-dropdown/'
				),
				array(
					'title' => __( 'Quick Video', 'ppom' ),
					'link'  => 'https://najeebmedia.com/addon-image-drop-down/'
				),
				array(
					'title' => __( 'More Info', 'ppom' ),
					'link'  => $ppom_site_url.'#imagedropdown'
				),
			)
		),
		array(
			'title'   => __( 'Bulk Quantity Options', 'ppom' ),
			'desc'    => __( 'Bulk Quantity for Options Addon allow store admin to set discount prices for each options. This Addon is best tool for companies like Printin, designing and who looking to sale products with options with different prices.', 'ppom' ),
			'actions' => array(
				array(
					'title' => __( 'Demo', 'ppom' ),
					'link'  => 'https://ppom.nmediahosting.com/product/woo-logo-3/'
				),
				array(
					'title' => __( 'Quick Video', 'ppom' ),
					'link'  => ''
				),
				array(
					'title' => __( 'More Info', 'ppom' ),
					'link'  => $ppom_site_url.'#bulkquantity'
				),
			)
		),
		array(
			'title'   => __( 'PriceTable', 'ppom' ),
			'desc'    => __( 'PPOM Price Table Add-on a completely new way to sell Packages, Plans, or products with a list of features. More than ten beautiful designs for the price tables available to sell plans in WooCommerce Store.', 'ppom' ),
			'actions' => array(
				array(
					'title' => __( 'Demo', 'ppom' ),
					'link'  => 'https://ppom.nmediahosting.com/product/price-table-add-on/'
				),
				array(
					'title' => __( 'Quick Video', 'ppom' ),
					'link'  => ''
				),
				array(
					'title' => __( 'More Info', 'ppom' ),
					'link'  => $ppom_site_url.'#pricetable'
				),
			)
		),
		array(
			'title'   => __( 'Cart Edit', 'ppom' ),
			'desc'    => __( 'PPOM Cart Edit Addon allow clients to edit fields once these are added to cart on cart page. It has also option to show all meta fields in different column on cart page. Extra column can be disable from Settings -> PPOM Cart tab.', 'ppom' ),
			'actions' => array(
				array(
					'title' => __( 'Demo', 'ppom' ),
					'link'  => 'https://ppom.nmediahosting.com/product/happy-ninja/'
				),
				array(
					'title' => __( 'Quick Video', 'ppom' ),
					'link'  => 'https://vimeo.com/358618266'
				),
				array(
					'title' => __( 'More Info', 'ppom' ),
					'link'  => $ppom_site_url.'#cartedit'
				),
			)
		),
		array(
			'title'   => __( 'SVG ADDON', 'ppom' ),
			'desc'    => __( 'PPOM SVG Add-on is a new product designer plugin. Now you can create and upload your SVG template for complex designs with Google Fonts. The customer can select the design part to make the product as per his need.', 'ppom' ),
			'actions' => array(
				array(
					'title' => __( 'Demo', 'ppom' ),
					'link'  => ''
				),
				array(
					'title' => __( 'Quick Video', 'ppom' ),
					'link'  => ''
				),
				array(
					'title' => __( 'More Info', 'ppom' ),
					'link'  => $ppom_site_url.'#svg'
				),
			)
		),
		array(
			'title'   => __( 'UploadCare', 'ppom' ),
			'desc'    => __( 'PPOM UploadCare now can be used for image editing on WooCommerce Product pages for any web2print business. It has tons of options that we integrated with our PPOM Add-on.','ppom' ),
			'actions' => array(
				array(
					'title' => __( 'Demo', 'ppom' ),
					'link'  => ''
				),
				array(
					'title' => __( 'Quick Video', 'ppom' ),
					'link'  => ''
				),
				array(
					'title' => __( 'More Info', 'ppom' ),
					'link'  => $ppom_site_url.'#uploadcare'
				),
			)
		),
		array(
			'title'   => __( 'Domain Checker', 'ppom' ),
			'desc'    => __( 'Domain Checker Addon will check any domain’s availability. Adds domain to cart if it’s not already registered. A simple solution to sell domains with WooCommerce PPOM. Customized messages for domain availability/not-availability. Ajax base script to check domain and show result.', 'ppom' ),
			'actions' => array(
				array(
					'title' => __( 'Demo', 'ppom' ),
					'link'  => 'https://ppom.nmediahosting.com/product/ppom-domain-checker-addon/'
				),
				array(
					'title' => __( 'Quick Video', 'ppom' ),
					'link'  => ''
				),
				array(
					'title' => __( 'More Info', 'ppom' ),
					'link'  => $ppom_site_url.'#domainchecker'
				),
			)
		),
		array(
			'title'   => __( 'Quantities Pack', 'ppom' ),
			'desc'    => __( 'PPOM Quantity Pack Add-on is a special input type which is very similar to Variation Quantities input but with a good difference. Like if you are want to sell some products with different options in specific quantities, not sure? please see Demo and Details.', 'ppom' ),
			'actions' => array(
				array(
					'title' => __( 'Demo', 'ppom' ),
					'link'  => 'https://najeebmedia.com/blog/ppom-quantity-pack-addon/'
				),
				array(
					'title' => __( 'Quick Video', 'ppom' ),
					'link'  => ''
				),
				array(
					'title' => __( 'More Info', 'ppom' ),
					'link'  => $ppom_site_url.'#quantitypack'
				),
			)
		),
		array(
			'title'   => __( 'Fancy Cropper', 'ppom' ),
			'desc'    => __( 'PPOM Fancy Cropper allows customers to upload images and crop before sending to cart. Admin can set different sizes and options for cropper. All cropped images are added to the cart and received in order meta for admin to download and process. For further order processing, WooConvo Revision Addon can be used.', 'ppom' ),
			'actions' => array(
				array(
					'title' => __( 'Demo', 'ppom' ),
					'link'  => 'https://ppom.nmediahosting.com/product/fancy-cropper-demo/'
				),
				array(
					'title' => __( 'Quick Video', 'ppom' ),
					'link'  => 'https://vimeo.com/416623997'
				),
				array(
					'title' => __( 'More Info', 'ppom' ),
					'link'  => $ppom_site_url.'#fancycropper'
				),
			)
		),
		array(
			'title'   => __( 'Super List', 'ppom' ),
			'desc'    => __( 'PPOM Super List addon contains many pre-defined lists to render as Select field on product page. It includes Countries, Currencies, Months etc. For more details for all available lists please visit demo.', 'ppom' ),
			'actions' => array(
				array(
					'title' => __( 'Demo', 'ppom' ),
					'link'  => 'https://ppom.nmediahosting.com/product/super-list-demo-copy/'
				),
				array(
					'title' => __( 'Quick Video', 'ppom' ),
					'link'  => ''
				),
				array(
					'title' => __( 'More Info', 'ppom' ),
					'link'  => $ppom_site_url.'#superlist'
				),
			)
		),
		array(
			'title'   => __( 'PDF Export', 'ppom' ),
			'desc'    => __( '
PPOM PDF Export Add-on will generate a PDF against each order including all PPOM Fields in a beautiful format. Header and Footer can also be set for PDF. This is an awesome feature when you need to print order details. For more detail watch video.', 'ppom' ),
			'actions' => array(
				array(
					'title' => __( 'Demo', 'ppom' ),
					'link'  => 'https://ppom.nmediahosting.com/product/simple-t-shirt-design/'
				),
				array(
					'title' => __( 'Quick Video', 'ppom' ),
					'link'  => 'https://vimeo.com/326834403'
				),
				array(
					'title' => __( 'More Info', 'ppom' ),
					'link'  => $ppom_site_url.'#pdfexport'
				),
			)
		),
		array(
			'title'   => __( 'Event Calendar', 'ppom' ),
			'desc'    => __( 'WooCommerce Event Calendar enables clients to purchase tickets against different events from Product Page and checkout. This Event Calendar Add-on is very simple to use, manage Ticket Price, Stocks.', 'ppom' ),
			'actions' => array(
				array(
					'title' => __( 'Demo', 'ppom' ),
					'link'  => 'https://ppom.nmediahosting.com/product/multiple-event-calendar/'
				),
				array(
					'title' => __( 'Quick Video', 'ppom' ),
					'link'  => 'https://vimeo.com/349698112'
				),
				array(
					'title' => __( 'More Info', 'ppom' ),
					'link'  => $ppom_site_url.'#eventcalendar'
				),
			)
		),
		array(
			'title'   => __( 'Field Collapsed', 'ppom' ),
			'desc'    => __( 'PPOM Field Collapsed Add-on is front-end design which groups field inside beautiful section. Like steps, and if your product has a large number of fields then it is the best add-on to a short length of your product page.', 'ppom' ),
			'actions' => array(
				array(
					'title' => __( 'Demo', 'ppom' ),
					'link'  => 'https://ppom.nmediahosting.com/product/ppom-collapsed-fields/'
				),
				array(
					'title' => __( 'Quick Video', 'ppom' ),
					'link'  => 'https://vimeo.com/284667121'
				),
				array(
					'title' => __( 'More Info', 'ppom' ),
					'link'  => $ppom_site_url.'#fieldcollapes'
				),
			)
		),
		array(
			'title'   => __( 'Text Counter', 'ppom' ),
			'desc'    => __( 'PPOM Text Counter Add-on is special type text input field. It can restrict a total number of words or character with nice info panel below. Each word or character can be changed and the price will be added to cart. See video for more details', 'ppom' ),
			'actions' => array(
				array(
					'title' => __( 'Demo', 'ppom' ),
					'link'  => 'https://ppom.nmediahosting.com/product/ppom-text-counter-add-on/'
				),
				array(
					'title' => __( 'Quick Video', 'ppom' ),
					'link'  => 'https://vimeo.com/314283537'
				),
				array(
					'title' => __( 'More Info', 'ppom' ),
					'link'  => $ppom_site_url.'#textcounter'
				),
			)
		),
		array(
			'title'   => __( 'Enquiry Form', 'ppom' ),
			'desc'    => __( 'PPOM Enquiry Form Add-on adds button on product page. A customer can ask the admin about any product with PPOM Field in email. All PPOM Meta Fields are sent with message typed by the customer. Multiple email recipients can be added for product enquiry.', 'ppom' ),
			'actions' => array(
				array(
					'title' => __( 'Demo', 'ppom' ),
					'link'  => ''
				),
				array(
					'title' => __( 'Quick Video', 'ppom' ),
					'link'  => 'https://vimeo.com/325028677'
				),
				array(
					'title' => __( 'More Info', 'ppom' ),
					'link'  => $ppom_site_url.'#enquiryform'
				),
			)
		),
		array(
			'title'   => __( 'AutoComplete/MultiSelect', 'ppom' ),
			'desc'    => __( 'PPOM AutoComplete/MultiSelect Add-on contain two special inputs. AutoComplete allows users to choose an option from the options list provided in settings. It’s like a Select input but values are filtered by keywords. It’s best to use when you have a long list of options', 'ppom' ),
			'actions' => array(
				array(
					'title' => __( 'Demo', 'ppom' ),
					'link'  => 'https://ppom.nmediahosting.com/product/ppom-auto-complete-demo/'
				),
				array(
					'title' => __( 'Quick Video', 'ppom' ),
					'link'  => ''
				),
				array(
					'title' => __( 'More Info', 'ppom' ),
					'link'  => $ppom_site_url.'#autocompletemultiselect'
				),
			)
		),
		array(
			'title'   => __( 'Variation Quantity Matrix', 'ppom' ),
			'desc'    => __( 'PPOM Variation Quantity Matrix Add-on is an advanced form of Variation Quantity Field. Variation Quantity Matrix is a super simple form when quantities need to be collected against multiple options. Like if you selling T-Shirt and need to collect Quantities against each Color of each Size, this Add-on can be used to render a Tabular/Grid. Please check our demo or video for more details.', 'ppom' ),
			'actions' => array(
				array(
					'title' => __( 'Demo', 'ppom' ),
					'link'  => 'https://ppom.nmediahosting.com/product/t-shirt-variations/'
				),
				array(
					'title' => __( 'Quick Video', 'ppom' ),
					'link'  => 'https://vimeo.com/391981516'
				),
				array(
					'title' => __( 'More Info', 'ppom' ),
					'link'  => $ppom_site_url.'#variationquantitymatrix'
				),
			)
		),
		array(
			'title'   => __( 'WooCommerce Variation Quantity', 'ppom' ),
			'desc'    => __( 'PPOM WooCommerce Variation Quantity Add-on turn default WC variations into variations quantities and each variations can be ordered with different quantities. In other words client can bulk order with WooCommerce Variations. It is very similar to PPOM Variation Quantity input but the only difference is that it use WooCommerce core variations.', 'ppom' ),
			'actions' => array(
				array(
					'title' => __( 'Demo', 'ppom' ),
					'link'  => 'https://ppom.nmediahosting.com/product/woocommerce-variation-quantity/'
				),
				array(
					'title' => __( 'Quick Video', 'ppom' ),
					'link'  => 'https://vimeo.com/402089812'
				),
				array(
					'title' => __( 'More Info', 'ppom' ),
					'link'  => $ppom_site_url.'#woocemmrcevariationquantity'
				),
			)
		),
		array(
			'title'   => __( 'Option Quantity', 'ppom' ),
			'desc'    => __( 'PPOM Option Quantity Add-on is simple ‘Number’ input type which can be used to accept option quantity BUT Price can also be set. Like for a Pizza Product, customer have a option to order more then one drink and each extra drink as price. Here this Option Quantity will do the trick. For more details please see demo. This Add-on can be used as Name your Price.', 'ppom' ),
			'actions' => array(
				array(
					'title' => __( 'Demo', 'ppom' ),
					'link'  => 'https://najeebmedia.com/blog/option-quantity-ppom-add-on-released/'
				),
				array(
					'title' => __( 'Quick Video', 'ppom' ),
					'link'  => ''
				),
				array(
					'title' => __( 'More Info', 'ppom' ),
					'link'  => $ppom_site_url.'#optionquantity'
				),
			)
		),
	);
	
	return $addons;
}