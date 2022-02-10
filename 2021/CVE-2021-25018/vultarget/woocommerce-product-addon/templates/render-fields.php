<?php
/**
 * Rendering all fields on product page
 * @since 10.0
 * 
 * */
if( ! defined("ABSPATH") ) die("Not Allowed");

// ppom_pa($args);
$ppom_id = is_array($ppom_id) ? implode(',', $ppom_id) : $ppom_id;

echo '<input type="hidden" id="ppom_product_price" value="'.esc_attr($product->get_price()).'">';	// it is setting price to be used for dymanic prices in script.js
echo '<input type="hidden" name="ppom[fields][id]" id="ppom_productmeta_id" value="'.esc_attr($ppom_id).'">';
echo '<input type="hidden" name="ppom_product_id" id="ppom_product_id" value="'.esc_attr(ppom_get_product_id($product)).'">';
echo '<input type="hidden" name="add-to-cart" value="'.esc_attr(ppom_get_product_id($product)).'" />';

// Manage conditional hidden fields to skip validation
echo '<input type="hidden" name="ppom[conditionally_hidden]" id="conditionally_hidden">';
// Option price hidden input: ppom-price.js
echo '<input type="hidden" name="ppom[ppom_option_price]" id="ppom_option_price">';
// Cart key if editing

$cart_key = isset($_GET['_cart_key']) ? sanitize_text_field($_GET['_cart_key']) : '';
echo '<input type="hidden" name="ppom_cart_key" value="'.esc_attr($cart_key).'">';


// Price placeholder, it will be cloned via js in ppom-price.js
echo '<div id="ppom-price-cloner-wrapper">';
echo '<span id="ppom-price-cloner">';
printf(__(get_woocommerce_price_format(), "ppom"), get_woocommerce_currency_symbol(), '<span class="ppom-price"></span>');
echo '</span>';
echo '</div>';


echo '<div class="form-row ppom-rendering-fields align-items-center ppom-section-collapse uk-grid">';

$posted_values = '';
if( apply_filters('ppom_retain_after_add_to_cart', true) ) {
	$posted_values = isset($_POST['ppom']['fields']) ? $_POST['ppom']['fields'] : '';
	$posted_values = apply_filters('ppom_default_values', $posted_values, $_POST, ppom_get_product_id($product), $args);
}

// ppom_pa($posted_values);

$section_started = false;
$ppom_field_counter = 0;
$ppom_collapse_counter = 0;
$allow_nextprev = ppom_get_option('ppom-collapse-nextprev');

foreach( $ppom_fields_meta as $meta ) {
    
    $type 			= ( isset($meta['type']) ? $meta ['type'] : '');
	$title			= ( isset($meta['title']) ? stripslashes($meta ['title']) : '');
	$data_name		= ( isset($meta['data_name']) ? $meta ['data_name'] : $title);
	$col			= ppom_get_field_colum($meta);
	$required		= ( isset($meta['required'] ) ? $meta['required'] : '' );
	$description	= ( isset($meta['description'] ) ? stripslashes($meta['description']) : '' );
	$logic			= ( isset($meta['logic'] ) ? $meta['logic'] : '' );
	$condition		= ( isset($meta['conditions'] ) ? $meta['conditions'] : '' );
	$options		= ( isset($meta['options'] ) ? $meta['options'] : array());
	$default_value  = ( isset($meta['default_value'] ) ? $meta['default_value'] : '');
	$classes        = ( isset($meta['class'] ) ? $meta['class'] : '');
	
	$ppom_field_counter++;
	
	// @since: 12.4
	// checking field visibility
	if( ! ppom_is_field_visible($meta) ) continue;
	
	if( empty($data_name) ) {
	    printf(__("Please provide data name property for %s", "ppom"), $title);
	    continue;
	}
	// Dataname senatize
	$data_name = sanitize_key( $data_name );
	
	if( !empty( $classes ) ) {
	    $classes = explode(",", $classes);
	    $classes[] = 'form-control';
	} else {
	    $classes = array('form-control');
	}
	
	$classes = apply_filters('ppom_input_classes', $classes, $meta);
	
	
	// current values from $_GET/$_POST
	if( isset($posted_values[$data_name]) ) {
		// ppom_pa($posted_values);
		
		switch ($type) {
		
			case 'image':
				$image_data  = $posted_values[$data_name];
					unset($default_value);
				foreach($image_data as $data){
					$default_value[] = json_decode( stripslashes($data), true );
				}
				break;
				
			case 'cropper':
				$default_value = isset($meta['selected']) ? $meta['selected'] : '';
				break;
				
			default:
				$default_value  = $posted_values[$data_name];
				break;
			}
			
	} else if( isset($_GET[$data_name]) ) {
		// When Cart Edit addon used
		$default_value  = sanitize_text_field($_GET[$data_name]);
	}else if( isset($_POST['ppom']['fields'][$data_name]) ) {
		$default_value  = sanitize_text_field($_POST['ppom']['fields'][$data_name]);
	} else {
		
		// Default values in settings
		switch ($type) {
			
			case 'textarea':
				
				if( is_numeric($default_value) ) {
					$content_post = get_post( intval($default_value) );
					$content = !empty($content_post) ? $content_post->post_content : '';
					$content = apply_filters('the_content', $content);
					$default_value = str_replace(']]>', ']]&gt;', $content);
				}
				break;
				
			case 'checkbox':
				$default_value = isset($meta['checked']) ? explode("\r\n", $meta['checked']) : '';
				break;
				
			case 'select':
			case 'radio':
			case 'timezone':
			case 'palettes':
			case 'image':
			case 'cropper':
				$default_value = isset($meta['selected']) ? $meta['selected'] : '';
				break;
				
		}
		
	}
	
	// Stripslashes: default values
	$default_value = ! is_array($default_value) ? stripslashes($default_value) : $default_value;
	$default_value = apply_filters("ppom_field_default_value", $default_value, $meta, $product);
	
	
	//WPML
	$title			= ppom_wpml_translate($title, 'PPOM');
	$description	= ppom_wpml_translate($description, 'PPOM');
	$description    = apply_filters( 'ppom_description_content', $description, $meta );

	
	// Generating field label
	$show_asterisk		= ( !empty($required) && $title != '' ) ? '<span class="show_required"> *</span>' : '';
	$show_description	= ( !empty($description) ) ? '<span class="show_description">'.$description.'</span>' : '';
	$show_description	= apply_filters('ppom_field_description', $show_description, $meta);
	
	$field_label = $title . $show_asterisk . $show_description;
	
	$placeholder 				= isset( $meta['placeholder'] ) ? stripslashes($meta['placeholder']) : '';
	
	if(is_array($options)){
		$options		= array_map("ppom_translation_options", $options);
	}
	// Form row

		$input_wrapper_class = $data_name;

		// Collapse Fields Section
		if( $type == 'collapse' ) {
			$collapse_type	= isset($meta['collapse_type']) ? $meta['collapse_type'] : '';

			if( $section_started ) {
				
				echo '<div class="ppom-loop-fields" style="clear:both"></div>';
				
				if ($allow_nextprev == 'yes') {
					echo '<div class="ppom-collapse-nextprev-btn" data-collapse-index="'.$ppom_collapse_counter.'">';
		    			echo '<button class="ppom-collapse-prev">'.sprintf(__("Prev", "ppom")).'</button>';
		    			echo '<button class="ppom-collapse-next">'.sprintf(__("Next", "ppom")).'</button>';
	    			echo '</div>';
				}
				echo '</div>';
			}

			if ($collapse_type == 'end') {
				echo '<div class="ppom-collapsed-child-end">';
			}

			if ($collapse_type != 'end' ) {
    			echo '<h4 data-collapse-id="'.esc_attr($data_name).'" class="ppom-collapsed-title">'.$title.'</h4>';
    			echo '<div class="collapsed-child">';
			}
				
			$section_started = true;
			$ppom_collapse_counter++;
		}

		// skip collapse div
		if ($type == 'collapse') continue;
		
		$ppom_cond_data = ppom_get_conditional_data_attributes($meta);
		$field_main_wrapper = 'ppom-field-wrapper ppom-col col-md-'.esc_attr($col).' '.esc_attr($input_wrapper_class);
		
		$ppom_field_wrapper_div = '<div data-data_name='.esc_attr($data_name).' '.$ppom_cond_data.' class="'.apply_filters('ppom_field_main_wapper_class', $field_main_wrapper, $meta).'">';
        echo apply_filters('ppom_field_wrapper_div', $ppom_field_wrapper_div, $meta, $product);
            
        // Text|Email|Date|Number
        $ppom_field_attributes = apply_filters('ppom_field_attributes', $meta, $type);
        
            switch( $type ) {
                
                case 'text':
                case 'email':
                case 'date':
            	case 'daterange':
                case 'number':
                case 'color':
                	
                	$min	= isset( $meta['min'] ) ? $meta['min'] : '';
                	$max	= isset( $meta['max'] ) ? $meta['max'] : '';
                	$step	= isset( $meta['step'] ) ? $meta['step'] : '';
                	
                	// Masking
                	$mask	= isset( $meta['input_mask'] ) ? $meta['input_mask'] : '';
                	$regex	= isset( $meta['use_regex'] ) ? $meta['use_regex'] : '';
                	
                	$taxable	= (isset( $meta['onetime_taxable'] ) ? $meta['onetime_taxable'] : '' );
					$onetime	= isset($meta['onetime']) ? $meta['onetime'] : '';
					$price		= isset($meta['price']) ? $meta['price'] : '';
                	
                	$default_value = strip_tags($default_value);
                	
                    $ppom_field_setting = array(  
                    				'id'        => $data_name,
                                    'type'      => $type,
                                    'name'      => "ppom[fields][{$data_name}]",
                                    'classes'   => $classes,
                                    'label'     => $field_label,
                                    'title'		=> $title,
                                    'attributes'=> $ppom_field_attributes,
                                    'min'		=> $min,
                                    'max'		=> $max,
                                    'step'		=> $step,
                                    'placeholder'	=> $placeholder,
                                    'autocomplete' => "false",
                                    'use_regex'	=> $regex,
                                    'input_mask'=> $mask,
                                    'onetime'	=> $onetime,
					            	'taxable'	=> $taxable,
					            	'price'		=> $price,
                                    );
                                    
                    
                    
                    $ppom_field_setting = apply_filters('ppom_field_setting', $ppom_field_setting, $meta, $product);
                    echo apply_filters("ppom_field_html_{$type}", NMForm()->Input($ppom_field_setting, $default_value), $type, $meta);
                	break;
                	
                case 'measure':
                	
                	$min	= isset( $meta['min'] ) ? $meta['min'] : '';
                	$max	= isset( $meta['max'] ) ? $meta['max'] : '';
                	$step	= isset( $meta['step'] ) ? $meta['step'] : '';
                	$use_units = isset( $meta['use_units'] ) ? $meta['use_units'] : '';
                	$options = ppom_convert_options_to_key_val($options, $meta, $product);
                	$default_value = strip_tags($default_value);
            
                    $ppom_field_setting = array(  
                    				'id'        => $data_name,
                                    'type'      => $type,
                                    'name'      => "ppom[fields][{$data_name}]",
                                    'classes'   => $classes,
                                    'label'     => $field_label,
                                    'title'		=> $title,
                                    'attributes'=> $ppom_field_attributes,
                                    'min'		=> $min,
                                    'max'		=> $max,
                                    'step'		=> $step,
                                    'options'	=> $options,
                                    'use_units'=> $use_units,
                                    );
                                    
                    
                    $ppom_field_setting = apply_filters('ppom_field_setting', $ppom_field_setting, $meta, $product);
                    echo NMForm() -> Input($ppom_field_setting, $default_value);
                	break;
                	
                case 'textarea':
                	
                	if( !empty($default_value) ) {
                	
                		$default_value = str_replace(']]>', ']]&gt;', $default_value);
                	}
					
					// Cols & Rows
					$cols	= ( isset($meta['cols']) ? $meta ['cols'] : 10);
					$rows	= ( isset($meta['rows']) ? $meta ['rows'] : 3);
					$editor	= ( isset($meta['rich_editor']) ? $meta ['rich_editor'] : '');
					
					$ppom_field_setting = array(  
		                				'id'        => $data_name,
		                                'type'      => $type,
		                                'name'      => "ppom[fields][{$data_name}]",
		                                'classes'   => $classes,
		                                'label'     => $field_label,
		                                'title'		=> $title,
		                                'attributes'=> $ppom_field_attributes,
		                                'cols'		=> $cols,
		                                'rows'		=> $rows,
		                                'rich_editor' => $editor,
		                                'placeholder'	=> $placeholder,
		                                );
		                
		            $ppom_field_setting = apply_filters('ppom_field_setting', $ppom_field_setting, $meta, $product);
                    echo apply_filters("ppom_field_html_{$type}", NMForm()->Input($ppom_field_setting, $default_value), $type, $meta);
                	break;
                
                case 'checkbox':
                	
                	$options = ppom_convert_options_to_key_val($options, $meta, $product);
					$taxable		= (isset( $meta['onetime_taxable'] ) ? $meta['onetime_taxable'] : '' );
					
					$onetime = isset($meta['onetime']) ? $meta['onetime'] : '';
					$ppom_field_setting = array(  
								  'id'      	=> $data_name,
					              'type'    	=> 'checkbox',
					              'name'    	=> "ppom[fields][{$data_name}]",
					              //'classes'   => $classes, // apply default class: form-check-input
                                  'label'   	=> $field_label,
                                  'title'		=> $title,
                                  'attributes'	=> $ppom_field_attributes,
					              'options' 	=> $options,
					              'onetime'		=> $onetime,
					              'taxable'		=> $taxable,
					              );
					
					$ppom_field_setting = apply_filters('ppom_field_setting', $ppom_field_setting, $meta, $product);
					echo apply_filters("ppom_field_html_{$type}", NMForm()->Input($ppom_field_setting, $default_value), $type, $meta);
					break;
					
				case 'select':
                	
                	$option_process = array();
                	$option_process = ppom_convert_options_to_key_val($options, $meta, $product);
                		
					// ppom_pa($option_process);
                	$onetime = isset($meta['onetime']) ? $meta['onetime'] : '';
                	$taxable = (isset( $meta['onetime_taxable'] ) ? $meta['onetime_taxable'] : '' );
                	
                	$ppom_field_setting = array(  
								  'id'        => $data_name,
					              'type'      => 'select',
					              'name'      => "ppom[fields][{$data_name}]",
					              'classes'   => $classes,
                                  'label'     => $field_label,
                                  'title'		=> $title,
                                  'attributes'=> $ppom_field_attributes,
					              'options'   => $option_process,
					              'onetime'		=> $onetime,
					              'taxable'		=> $taxable,
					              );
					
					$ppom_field_setting = apply_filters('ppom_field_setting', $ppom_field_setting, $meta, $product);
					echo apply_filters("ppom_field_html_{$type}", NMForm()->Input($ppom_field_setting, $default_value), $type, $meta);
					break;
					
				case 'radio':
                	
                	$options = ppom_convert_options_to_key_val($options, $meta, $product);
                	$onetime = isset($meta['onetime']) ? $meta['onetime'] : '';
                	$taxable		= (isset( $meta['onetime_taxable'] ) ? $meta['onetime_taxable'] : '' );
                	
					$ppom_field_setting = array(  
								  'id'        => $data_name,
					              'type'      => 'radio',
					              'name'      => "ppom[fields][{$data_name}]",
					              //'classes'   => $classes, // apply default class: form-check-input
                                  'label'     => $field_label,
                                  'title'		=> $title,
                                  'attributes'=> $ppom_field_attributes,
					              'options'   => $options,
					              'onetime'		=> $onetime,
					              'taxable'		=> $taxable,
					              );
					
					$ppom_field_setting = apply_filters('ppom_field_setting', $ppom_field_setting, $meta, $product);
					echo apply_filters("ppom_field_html_{$type}", NMForm()->Input($ppom_field_setting, $default_value), $type, $meta);
					break;
					
				case 'timezone':
                	
                	$regions		= isset($meta['regions']) ? $meta['regions'] : 'All';
                	$show_time		= isset($meta['show_time']) ? $meta['show_time'] : '';
                	$first_option	= isset($meta['first_option']) ? $meta['first_option'] : '';
					
                	$options = ppom_array_get_timezone_list($regions, $show_time);
                	if( !empty($first_option) ) {
                		$options[''] = sprintf(__("%s","ppom"), $first_option);
                	}
                	
                	// ppom_pa($options);
                	
					$ppom_field_setting = array(  
								  'id'        => $data_name,
					              'type'      => 'timezone',
					              'name'      => "ppom[fields][{$data_name}]",
					              'classes'   => $classes,
                                  'label'     => $field_label,
                                  'title'	  => $title,
                                  'attributes'=> $ppom_field_attributes,
					              'options'   => $options,
					              );
					
					$ppom_field_setting = apply_filters('ppom_field_setting', $ppom_field_setting, $meta, $product);
					echo NMForm() -> Input($ppom_field_setting, $default_value);
					break;
					
				case 'palettes':
					// ppom_pa($meta);
					$options = ppom_convert_options_to_key_val($options, $meta, $product);
					$color_width = !empty($meta['color_width']) ? intval($meta['color_width']) : 50;
    				$color_height = !empty($meta['color_height']) ? intval($meta['color_height']) : 50;
    				$onetime = isset($meta['onetime']) ? $meta['onetime'] : '';
                	$taxable		= (isset( $meta['onetime_taxable'] ) ? $meta['onetime_taxable'] : '' );
                	$display_circle	= (isset($meta['circle']) && $meta['circle'] == 'on') ? true : false;
                	$multiple_allowed	= isset($meta['multiple_allowed']) ? $meta['multiple_allowed'] : '';
                	$max_selected	= isset($meta['max_selected']) ? $meta['max_selected'] : '';
                	
                	if (isset($meta['selected_palette_bcolor']) && $meta['selected_palette_bcolor'] != '') {
						$selected_palette_bcolor = $meta['selected_palette_bcolor'];
					}else{
						$selected_palette_bcolor = '#000';
					}
                	
					$ppom_field_setting = array(  
                    				'id'        => $data_name,
                                    'type'      => $type,
                                    'name'      => "ppom[fields][{$data_name}]",
                                    'classes'   => $classes,
                                    'label'     => $field_label,
                                    'title'		=> $title,
                                    'color_height'=> $color_height,
                                    'color_width'=> $color_width,
                                    'options'   => $options,
                                    'onetime'		=> $onetime,
					            	'taxable'		=> $taxable,
					            	'display_circle'	=> $display_circle,
					            	'multiple_allowed' => $multiple_allowed,
					            	'selected_palette_bcolor' => $selected_palette_bcolor,
					            	'max_selected'	=> $max_selected,
                                    );
                    
                    $ppom_field_setting = apply_filters('ppom_field_setting', $ppom_field_setting, $meta, $product);
                    echo NMForm() -> Input($ppom_field_setting, $default_value);
                	break;
                	
            	case 'image':
					$images	= isset($meta['images']) ? $meta['images'] : array();
					// ppom_pa($meta);
					$show_popup	= isset($meta['show_popup']) ? $meta['show_popup'] : '';

					// image selected border color
					if (isset($meta['selected_img_bordercolor']) && $meta['selected_img_bordercolor'] != '') {
						$selected_img_bordercolor = $meta['selected_img_bordercolor'];
					}else{
						$selected_img_bordercolor = '#f00';
					}

					// image width
					if (isset($meta['image_width']) && $meta['image_width'] != '') {
						$image_width = $meta['image_width'];
					}else{
						$image_width = '75px';
					}

					// image height
					if (isset($meta['image_height']) && $meta['image_height'] != '') {
						$image_height = $meta['image_height'];
					}else{
						$image_height = 'auto';
					}

					$multiple_allowed	= isset($meta['multiple_allowed']) ? $meta['multiple_allowed'] : '';
					
					$saved_images = isset($meta['images']) ? $meta['images'] : array();
					$images = ppom_convert_options_to_key_val($saved_images, $meta, $product);
					
					$ppom_field_setting = array(  
                    				'id'        => $data_name,
                                    'type'      => $type,
                                    'name'      => "ppom[fields][{$data_name}]",
                                    'classes'   => $classes,
                                    'label'     => $field_label,
                                    'selected_img_bordercolor'     => $selected_img_bordercolor,
                                    'title'		=> $title,
                                    'legacy_view'	=> (isset($meta['legacy_view'])) ? $meta['legacy_view'] : '',
									'multiple_allowed' => $multiple_allowed,
									'images'	=> $images,
                                    'show_popup'=> $show_popup,
                                    'image_width' => $image_width,
                                    'image_height'=> $image_height,
                                    );
                    
                    $ppom_field_setting = apply_filters('ppom_field_setting', $ppom_field_setting, $meta, $product);
                    echo NMForm() -> Input($ppom_field_setting, $default_value);
                	break;
                	
                	case 'pricematrix':
                	
                	$options		= ppom_convert_options_to_key_val($options, $meta, $product);
                	$discount		= isset($meta['discount']) ? $meta['discount'] : '';
                	$show_slider	= isset($meta['show_slider']) ? $meta['show_slider'] : '';
                	$qty_step		= isset($meta['qty_step']) ? $meta['qty_step'] : 1;
                	$hide_matrix	= isset($meta['hide_matrix_table']) ? $meta['hide_matrix_table'] : '';
                	$show_price_per_unit		= isset($meta['show_price_per_unit']) ? $meta['show_price_per_unit'] : '';
                	
                	$ppom_field_setting = array(  
								  'id'        => $data_name,
					              'type'      => $type,
					              'name'      => "ppom[fields][{$data_name}]",
					              'label'	  => $field_label,
                                  'ranges'    => $options,
                                  'discount'  => $discount,
                                  'show_slider'	=> $show_slider,
                                  'qty_step'	=> $qty_step,
                                  'hide_matrix'	=> $hide_matrix,
                                  'show_price_per_unit' => $show_price_per_unit,
					              );
					
					$ppom_field_setting = apply_filters('ppom_field_setting', $ppom_field_setting, $meta, $product);
					echo NMForm() -> Input($ppom_field_setting);
					break;
                   
                   case 'quantities':
                	
                	$view_control = (isset( $meta['view_control'] ) ? $meta['view_control'] : '' );
                	$horizontal_layout = (isset( $meta['horizontal'] ) ? $meta['horizontal'] : '' );
                	$include_productprice = isset($meta['use_productprice']) ? $meta['use_productprice'] : '';
                	$min_qty = (isset( $meta['min_qty'] ) ? $meta['min_qty'] : '' );
                	$max_qty = (isset( $meta['max_qty'] ) ? $meta['max_qty'] : '' );
                	$default_price = isset($meta['default_price']) ? $meta['default_price'] : '';
                	
                	
                	if( !empty($_GET[$data_name]) ) {
                	
                		$default_value = array_map('sanitize_text_field', $_GET[$data_name]);
                	} 
					// ppom_pa($options);
					$ppom_field_setting = array(  
								  'id'        => $data_name,
					              'type'      => $type,
					              'name'      => "ppom[fields][{$data_name}]",
					              'label'	  => $field_label,
					              'required'		=> $required,
                                  'horizontal_layout' => $horizontal_layout,
                                  'min_qty' => $min_qty,
                                  'max_qty' => $max_qty,
                                  'view_control'		=> $view_control,
                                  'options'		=> $options,
                                  'include_productprice' => $include_productprice,
                                  'default_price'	=> $default_price,
					              );
					
					$ppom_field_setting = apply_filters('ppom_field_setting', $ppom_field_setting, $meta, $product);
					echo NMForm() -> Input($ppom_field_setting, $default_value);
					break;
					
					// Section or HTML
					case 'section':
                	
                	$field_html	= isset($meta['html']) ? ppom_wpml_translate($meta['html'], 'PPOM') : '';
                	
					$ppom_field_setting = array(  
								  'id'        => $data_name,
					              'type'      => $type,
					              'label'     => $field_label,
					              'name'      => "ppom[fields][{$data_name}]",
					              'html'		=> $field_html,
					              );
					
					$ppom_field_setting = apply_filters('ppom_field_setting', $ppom_field_setting, $meta, $product);
					echo apply_filters("ppom_field_html_{$type}", NMForm()->Input($ppom_field_setting, $default_value), $type, $meta);
					break;
					
				// Audio/videos
				case 'audio':
					
					$audios	= isset($meta['audio']) ? $meta['audio'] : array();
					$multiple_allowed	= isset($meta['multiple_allowed']) ? $meta['multiple_allowed'] : '';
					// $audios = ppom_convert_options_to_key_val($audios, $meta, $product);
				
					$ppom_field_setting = array(  
                    				'id'        => $data_name,
                                    'type'      => $type,
                                    'name'      => "ppom[fields][{$data_name}]",
                                    'classes'   => $classes,
                                    'label'     => $field_label,
                                    'title'		=> $title,
                                    /*'legacy_view'	=> (isset($meta['legacy_view'])) ? $meta['legacy_view'] : '',
									'popup_width'	=> $popup_width,
									'popup_height'	=> $popup_height,*/
									'multiple_allowed' => $multiple_allowed,
									'audios'		=> $audios,
                                    
                                    );
                    
                    $ppom_field_setting = apply_filters('ppom_field_setting', $ppom_field_setting, $meta, $product);
                    echo NMForm() -> Input($ppom_field_setting, $default_value);
                	break;
                	
            	// File upload
				case 'file':
					
					$label_select = ($meta['button_label_select'] == '' ? __('Select files', "ppom") : $meta['button_label_select']);
					$files_allowed = ($meta['files_allowed'] == '' ? 1 : $meta['files_allowed']);
					$file_types = ($meta['file_types'] == '' ? 'jpg,png,gif' : $meta['file_types']);
					$file_size = ($meta['file_size'] == '' ? '10mb' : $meta['file_size']);
					$chunk_size = apply_filters('ppom_file_upload_chunk_size', '1mb');
					
					$drag_drop		= (isset( $meta ['dragdrop'] ) ? $meta ['dragdrop'] : '' );
					$button_class	= (isset( $meta ['button_class'] ) ? $meta ['button_class'] : '' );
					$photo_editing	= (isset( $meta ['photo_editing'] ) ? $meta ['photo_editing'] : '' );
					$editing_tools	= (isset( $meta ['editing_tools'] ) ? $meta ['editing_tools'] : '' );
					$popup_width	= (isset( $meta ['popup_width'] ) ? $meta ['popup_width'] : '500' );
					$popup_height	= (isset( $meta ['popup_height'] ) ? $meta ['popup_height'] : '400' );
					$file_cost		= (isset( $meta ['file_cost'] ) ? $meta ['file_cost'] : '' );
					$taxable		= (isset( $meta['onetime_taxable'] ) ? $meta['onetime_taxable'] : '' );
					$language		= (isset( $meta['language_opt'] ) ? $meta['language_opt'] : '' );
					
					$field_label = ($file_cost == '') ? $field_label : $field_label . ' - ' . wc_price($file_cost);
					
					$ppom_field_setting = array(
									'name'					=> "ppom[fields][{$data_name}]",
									'id'					=> $data_name,
									'type'					=> $type,
									'label'     			=> $field_label,
									'dragdrop'				=> $drag_drop,
									'button_label'			=> $label_select,
									'files_allowed'			=> $files_allowed,
									'file_types'			=> $file_types,
									'file_size'				=> $file_size,
									'chunk_size'			=> $chunk_size,
									'button_class'			=> $button_class,
									'photo_editing'			=> $photo_editing,
									'editing_tools'			=> $editing_tools,
									'aviary_apikey'			=> $ppom_settings -> aviary_api_key,
									/*'popupwidth'			=> $popup_width,
									'popup-height'			=> $popup_height,*/
									'file_cost'				=> $file_cost,
									'taxable'				=> $taxable,
									'language'				=> $language,
									);
									
					
                    
                    $ppom_field_setting = apply_filters('ppom_field_setting', $ppom_field_setting, $meta, $product);
                    echo apply_filters("ppom_field_html_{$type}", NMForm()->Input($ppom_field_setting, $default_value), $type, $meta);
                	break;
                	
            	// Cropper
				case 'cropper':
					
					$label_select	= ($meta['button_label_select'] == '' ? __('Select files', "ppom") : $meta['button_label_select']);
					$files_allowed	= ($meta['files_allowed'] == '' ? 1 : $meta['files_allowed']);
					$file_types 	= 'jpg,png,gif';
					$file_size		= ($meta['file_size'] == '' ? '10mb' : $meta['file_size']);
					$chunk_size 	= apply_filters('ppom_file_upload_chunk_size', '1mb');
					
					$drag_drop		= (isset( $meta ['dragdrop'] ) ? $meta ['dragdrop'] : '' );
					$button_class	= (isset( $meta ['button_class'] ) ? $meta ['button_class'] : '' );
					$taxable		= (isset( $meta['onetime_taxable'] ) ? $meta['onetime_taxable'] : '' );
					$language		= (isset( $meta['language_opt'] ) ? $meta['language_opt'] : '' );
					$file_cost		= (isset( $meta ['file_cost'] ) ? $meta ['file_cost'] : '' );
					$field_label	= ($file_cost == '') ? $field_label : $field_label . ' - ' . wc_price($file_cost);
					$first_option	= isset($meta['first_option']) ? $meta['first_option'] : '';
					$options		= ppom_convert_options_to_key_val($options, $meta, $product);
					// ppom_pa($options);
					
					// Croppie options
					$croppie_options	= ppom_get_croppie_options($meta);
					
					$ppom_field_setting = array(
									'name'					=> "ppom[fields][{$data_name}]",
									'id'					=> $data_name,
									'type'					=> $type,
									'label'     			=> $field_label,
									'dragdrop'				=> $drag_drop,
									'button_label'			=> $label_select,
									'files_allowed'			=> $files_allowed,
									'file_types'			=> $file_types,
									'file_size'				=> $file_size,
									'chunk_size'			=> $chunk_size,
									'button_class'			=> $button_class,
									'file_cost'				=> $file_cost,
									'taxable'				=> $taxable,
									'language'				=> $language,
									'croppie_options'		=> $croppie_options,
									'first_option'			=> $first_option,
									'options'				=> $options,
									);
									
					
                    
                    $ppom_field_setting = apply_filters('ppom_field_setting', $ppom_field_setting, $meta, $product);
                    echo NMForm() -> Input($ppom_field_setting, $default_value);
                	break;
					
				// Fixed Price Addon
				// DEPRECATED: UPDATE TO FIXEDPRICE VERSION 18.0
            	case 'fixedprice_DEPRECATED':
							
					break;
					
					case 'hidden';
					
						$field_name = "ppom[fields][{$data_name}]";
						$hidden_val = isset( $meta['field_value'] ) ? $meta['field_value'] : '';
						
						echo '<input type="hidden" id="'.esc_attr($data_name).'" name="'.esc_attr($field_name).'" value="'.esc_attr($hidden_val).'">';
					break;
            }
            
            
        	/**
        	 * creating action space to render more addons
        	 **/
        	 do_action('ppom_rendering_inputs', $meta, $data_name, $classes, $field_label, $options);
        
        echo '</div>';  //col-lg-*
        
        if( count($ppom_fields_meta) == $ppom_field_counter && $section_started ) {
			echo '</div>';
		}
	
}

echo '</div>'; // Ends form-row