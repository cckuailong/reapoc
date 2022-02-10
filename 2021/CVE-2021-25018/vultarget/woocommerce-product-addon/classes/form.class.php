<?php
/**
 * PPOM Frontend Form Rendering Class
 *
 * It control inputs base templates
 *
 * @version  1.0
 */
 
/* 
**========== Block direct access =========== 
*/
if( ! defined('ABSPATH' ) ){ exit; }
 
class PPOM_Form {
	
	private static $ins = null;
    
    /**
	 * Return all ppom attach meta data
	 *
	 * @var array
	 */
	public static $ppom;
	
	/**
	 * Return the product ID
	 *
	 * @var number
	 */
// 	public static $product_id;
	
	/**
	 * Return wc product object
	 *
	 * @var object
	 */
// 	public static $product;
	
	/**
	 * Return templates args
	 *
	 * @var array
	 */
    public static $args;

	function __construct( $product, $args ){
	    

		$this->product    = $product;

		$this->product_id = ppom_get_product_id( $this->product );

		self::$ppom       = new PPOM_Meta( $this->product_id );
		
        self::$args       = $args;
	}

	public static function get_instance() {
	    
        // create a new object if it doesn't exist.
        is_null(self::$ins) && self::$ins = new self;
        
        return self::$ins;
    }
    
    /**
	 * PPOM main wrapper 2 classes
	 * 
	 * @return string
	 */
    function wrapper_inner_classes() {
        
        $classes = ['form-row','ppom-rendering-fields','align-items-center','ppom-section-collapse'];
                        
        $classes = apply_filters('ppom_wrapper2_classes', $classes, $this->product);
                            
        return implode(' ', $classes);
    }
    
    /**
	 * PPOM fields rendering callback
	 * 
	 * @return fields html
	 */
    function ppom_fields_render(){

	    $posted_values = '';
        
        $section_started = false;
        $ppom_field_counter = 0;
        $ppom_collapse_counter = 0;
        $allow_nextprev = ppom_get_option('ppom-collapse-nextprev');

    	// posted value being
    	// used ppom-pro
        $posted_values = apply_filters('ppom_default_values', $posted_values, $_POST, $this->product_id, self::$args);
        
        foreach( self::$ppom->fields as $meta ) {
            
    		$type      = isset($meta['type']) ? $meta['type'] : '';
            $title     = isset($meta['title']) ? ppom_wpml_translate($meta['title'], 'PPOM') : '';
    		$data_name = isset($meta['data_name']) ? $meta['data_name'] : $title;
            $ppom_field_counter++;
            
    		// Set ID on meta against dataname
    		$meta['id'] = $data_name;
    		
            // Dataname senatize
	        $data_name = sanitize_key( $data_name );
	        
    		$fm = new PPOM_Legacy_InputManager($meta, $type);
            
            // add_action('ppom_rendering_inputs_'.$type, array($this, 'render_input_template'), 10, 2);

            $field_html    = '';
        
            // checking field visibility
			if( ! ppom_is_field_visible($meta) ) continue;
			
            if( empty($data_name) ) { printf(__("Please provide data name property for %s", "ppom"), $title); continue; }
            
    		$default_value = $this->get_field_default_value($posted_values, $data_name, $meta);
            
            $ppom_cond_data = ppom_get_conditional_data_attributes($meta);

            // Text|Email|Date|Number
            $ppom_field_attributes = apply_filters('ppom_field_attributes', $meta, $type);

            // Set inputs attr into meta
            $meta['attributes'] = $ppom_field_attributes;
            
            $field_wrapper_class = $this->field_main_wrapper_classes($meta);
            
            
            // Collapse Fields Section
    		if( $type == 'collapse' ) {
    			$collapse_type	= isset($meta['collapse_type']) ? $meta['collapse_type'] : '';
    
    			if( $section_started ) {
    				
    				echo '<div class="ppom-loop-fields" style="clear:both"></div>';
    				
    				if ($allow_nextprev == 'yes') {
    					echo '<div class="ppom-collapse-nextprev-btn" data-collapse-index="'.esc_attr($ppom_collapse_counter).'">';
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
            
			
			
            $field_wrapper_div = '<div data-data_name='.esc_attr($data_name).' '.$ppom_cond_data.' class="'.esc_attr($field_wrapper_class).'">';
            $field_html .= apply_filters('ppom_field_wrapper_div', $field_wrapper_div, $meta, $this->product);

                /**
                 * creating action space to render more addons
                 * 
                 * Legacy Hook: ppom_rendering_inputs
                 * 
                 * Template based load addons Hook: ppom_rendering_inputs_{$type}
                 * 
                 * Updated by Najeeb on May 24, 2021
                 * Now the CORE inputs will be rendered via function rather hooks
                **/
                ob_start();
                
                
                $all_inputs = ppom_array_all_inputs();
                $core_inputs = $all_inputs['core'];
                
                if( in_array($type, $core_inputs) ) {
                    $this->render_input_template($meta, $default_value);
                }
                
                do_action('ppom_rendering_inputs', $meta, $data_name, $fm->input_classes_array(), $fm->field_label(), $fm->options());
                do_action("ppom_rendering_inputs_{$type}", $meta, $default_value);
                
                
    			$field_html .= ob_get_clean();

            $field_html .= '</div>';
            
            if( count(self::$ppom->fields) == $ppom_field_counter && $section_started ) {
    			$field_html .= '</div>';
    		}
			
			// Filter: nmforms_input_htmls
            // @TODO need to change with relevant name
            echo apply_filters("nmforms_input_html", $field_html, $meta, $default_value);
            
    	}
    }
    
    function render_input_template($meta, $default_value){
        
        $type = isset($meta['type']) ? $meta['type'] : '';     
        
        $template_path  = "frontend/inputs/{$type}.php";
        $template_vars	= array( 
                            'field_meta'    => $meta, 
                            'default_value' => $default_value,
                            'product'       => $this->product
                        );
        
        $template_vars   = apply_filters('ppom_input_templates_vars', $template_vars, $this);                        
        
        ppom_load_input_templates( $template_path, $template_vars );
    }
    
    /**
	 * Field Main Wrapper Classes
	 * 
	 * @hook ppom_field_main_wrapper_class
	 */
    function field_main_wrapper_classes($meta) {
        
        $width    = $this->field_wrapper_width($meta);
        $dataname = isset($meta['data_name']) ?  sanitize_key($meta['data_name']): '';

        $classes   = array();
        $classes[] = 'ppom-field-wrapper';
        $classes[] = 'ppom-col';
        $classes[] = 'col-md-'. $width;
        $classes[] = $dataname;
        $classes[] = "ppom-wrapper_outer-{$dataname}";
        $classes[] = "ppom-id-{$meta['ppom_id']}";

        $wrapper_classes = implode(' ',$classes);
        
        $wrapper_classes =  apply_filters('ppom_field_main_wrapper_class', $wrapper_classes, $classes, $meta);
        
        return $wrapper_classes;
    }
    
    /**
	 * Field Colunm Width
	 *
	 * @hook ppom_{$field_type}_input_meta_width
	 */
    function field_wrapper_width($input_meta) {

        $field_column = isset($input_meta['width'] ) ? $input_meta['width']: 12;
    
        // Check width has old settings
        if( strpos( $field_column, '%' ) !== false ) {
            
            $field_column = 12;
        } elseif( intval($field_column) > 12 ) {
            $field_column = 12;
        }
        
        return apply_filters("ppom_input_meta_width", $field_column, $input_meta);
    }
    
    /**
	 * Rendering form extra contents
	 */
    function form_contents(){

        $template_vars = array(
                            'ppom_id'    => self::$ppom->meta_id, 
                            'product'    => $this->product,
                            'product_id' => $this->product_id
                        );
        
	    ob_start();
	    	ppom_load_input_templates( 'frontend/component/form-data.php', 
	    	apply_filters('ppom_form_extra_contents', $template_vars, $this)
	    	);
	    echo ob_get_clean();
    }

    /**
	 * Check If PPOM Fields Empty
	 */
	function has_ppom_fields(){
        
        $return = false;
		if( self::$ppom->fields ) {
            $return = true;
        }

        return apply_filters('has_ppom_fields', $return);
	}
	
	/**
	 * Rendering price table HTML
	 */
	function render_price_table_html() {
	    
	    $html = '<div id="ppom-price-container" class="ppom-price-container-'.esc_attr($this->product_id).'"></div>';
	    return apply_filters('ppom_price_tale_html', $html, $this); 
	}

	/**
	 * Get default input/posted values 
	 * 
	 * @return defual_value
	 */
	function get_field_default_value($posted_values, $data_name, $meta){

        $default_value = isset($meta['default_value'] ) ?  $meta['default_value']: '';
        $type          = isset($meta['type']) ? $meta['type'] : '';

        // current values from $_GET/$_POST
        if( isset($posted_values[$data_name]) ) {

            switch ($type) {
            
                case 'image':
                    $image_data  = $posted_values[$data_name];
                    unset($default_value);
                    foreach($image_data as $data){
                        $default_value[] = json_decode( stripslashes($data), true);
                    }
                    break;
                
                case 'audio':
                    $audio_data  = $posted_values[$data_name];
                    unset($default_value);
                    foreach($audio_data as $data){
                        $default_value[] = json_decode( stripslashes($data), true);
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
        }else if( isset($_POST['ppom']['fields'][$data_name]) && apply_filters('ppom_retain_after_add_to_cart', true) ) {
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

        return apply_filters("ppom_field_default_value", $default_value, $meta, $this->product);
    }
}