<?php
/**
 * Inputs rendering class
 **/
 

// constants/configs
define( 'ECHOABLE', false );

class NM_Form {
     
     
     /**
	 * the static object instace
	 */
	private static $ins = null;
	
	private $echoable;
	
	private $defaults;
	
	
     function __construct() {
         
        //  should control echo or return
        $this -> echoable = $this->get_property( 'echoable' );
        
        // control defaul settings
        $this -> defaults = $this->get_property( 'defaults' );
        
        // Local filters
        add_filter('nmform_attribute_value', array($this, 'adjust_attributes_values'), 10, 3);
     }
 
     public function Input( $args, $default_value = '' ) {
         
         $type       = $this -> get_attribute_value( 'type', $args);
         
         switch( $type ) {
             
            case 'text':
            case 'date':
            case 'daterange':
            case 'datetime-local':
            case 'email':
            case 'number':
            case 'color':
                
                $input_html = $this -> Regular( $args, $default_value );
                
            break;
            
            case 'measure':
                $input_html = $this -> Measure( $args, $default_value );
            break;
            
            case 'textarea':
                $input_html = $this -> Textarea( $args, $default_value );
            break;
            
            case 'select':
                $input_html = $this -> Select( $args, $default_value );
            break;
            
            case 'timezone':
                $input_html = $this -> Timezone( $args, $default_value );
            break;
            
            case 'checkbox':
                $input_html = $this -> Checkbox( $args, $default_value );
            break;
            
            case 'radio':
                $input_html = $this -> Radio( $args, $default_value );
            break;
            
            case 'palettes':
                $input_html = $this -> Palettes( $args, $default_value );
            break;
            
            case 'image':
                $input_html = $this -> Image( $args, $default_value );
            break;
            
            case 'pricematrix':
                $input_html = $this -> Pricematrix( $args, $default_value );
            break;
            
            case 'quantities':
                $input_html = $this -> Quantities( $args, $default_value );
            break;
            
            case 'section':
                $input_html = $this -> Section( $args, $default_value );
            break;
            
            case 'audio':
                $input_html = $this -> Audio_video( $args, $default_value );
            break;
            
            case 'file':
                $input_html = $this -> File( $args, $default_value );
            break;
            
            case 'cropper':
                $input_html = $this -> Cropper( $args, $default_value );
            break;
            
         }
         
        return $input_html;
     }
     
    /**
     * Regular Input Field
     * 1. Text
     * 2. Date
     * 3. Email
     * 4. Number
     * 5. color
     **/
     
    public function Regular( $args, $default_value = '' ) {
         
        $product    = isset($args['product_id']) ? wc_get_product($args['product_id']) : null;
        
        $type       = $this -> get_attribute_value( 'type', $args);
        
        $label      = $this -> get_attribute_value('label', $args);
        $classes    = $this -> get_attribute_value('classes', $args);
        $id         = $this -> get_attribute_value('id', $args);
        $name       = $this -> get_attribute_value('name', $args);
        $placeholder= $this -> get_attribute_value('placeholder', $args);
        $attributes = $this -> get_attribute_value('attributes', $args);
        
        $num_min    = $this -> get_attribute_value('min', $args);
        $num_max    = $this -> get_attribute_value('max', $args);
        $step       = $this -> get_attribute_value('step', $args);
        
        
        $onetime    = $this -> get_attribute_value('onetime', $args);
        $taxable    = $this -> get_attribute_value('taxable', $args);
        $price      = $this -> get_attribute_value('price', $args);
        $price_without_tax = '';
        
        // Only title without description for price calculation etc.
        $title      = $args['title'];
        
        if($onetime == 'on' && $taxable == 'on') {
			$price_without_tax = $price;
			$price = ppom_get_price_including_tax($price, $product);
		}
        
        $input_wrapper_class = $this->get_default_setting_value('global', 'input_wrapper_class', $id);
        $input_wrapper_class = apply_filters('ppom_input_wrapper_class', $input_wrapper_class, $args);
        $html       = '<div class="'.$input_wrapper_class.'">';
        if( $label ){
            $html   .= '<label class="'.$this->get_default_setting_value('global', 'label_class', $id).'" for="'.$id.'">';
            $html   .= sprintf(__("%s", "ppom"), $label) .'</label>';
        }
        
        if( $price !== '' ) $classes .= ' ppom-priced';
        // ppom_pa($args);
        
        $html       .= '<input type="'.esc_attr($type).'" ';
        $html       .= 'id="'.esc_attr($id).'" ';
        $html       .= 'name="'.esc_attr($name).'" ';
        $html       .= 'class="'.esc_attr($classes).'" ';
        $html       .= 'placeholder="'.esc_attr($placeholder).'" ';
        $html       .= 'autocomplete="off" ';
        $html       .= 'data-type="'.esc_attr($type).'" ';
        $html       .= 'data-data_name="'.esc_attr($id).'" ';
        $html       .= 'data-title="'.esc_attr($title).'" '; // Input main label/title
        $html       .= 'data-price="'.esc_attr($price).'" ';
        $html       .= 'data-onetime="'.esc_attr($onetime).'" ';
        $html       .= 'data-taxable="'.esc_attr($taxable).'" ';
        $html       .= 'data-without_tax="'.esc_attr($price_without_tax).'" ';
        
        // Adding min/max for number input
        if( $type == 'number' ) {
            $html       .= 'min="'.esc_attr($num_min).'" ';
            $html       .= 'max="'.esc_attr($num_max).'" ';
            $html       .= 'step="'.esc_attr($step).'" ';
        }
        
        // Regex-Input Mask (Since 18.0)
        if( $args['use_regex'] == 'on' && $args['input_mask'] != '' ) {
            $html       .= 'data-inputmask-regex="'.esc_attr($args['input_mask']).'" ';
        }        
        
        //Values
        if( $default_value != '')
        $html      .= 'value="'.esc_attr($default_value).'" ';
        
        // Attributes
        foreach($attributes as $attr => $value) {
            
            $html      .= esc_attr($attr) . '="'.esc_attr($value).'" ';
        }
        
        
        $html      .= '>';
        $html      .= '</div>';    //form-group
        
        // filter: nmforms_input_htmls
        return apply_filters("nmforms_input_html", $html, $args, $default_value);
    }
    
    
    public function Measure( $args, $default_value = '' ) {
         
        $product    = isset($args['product_id']) ? wc_get_product($args['product_id']) : null;
        
        $type       = $this -> get_attribute_value( 'type', $args);
        
        $label      = $this -> get_attribute_value('label', $args);
        $classes    = $this -> get_attribute_value('classes', $args);
        $id         = $this -> get_attribute_value('id', $args);
        $name       = $this -> get_attribute_value('name', $args);
        $placeholder= $this -> get_attribute_value('placeholder', $args);
        $attributes = $this -> get_attribute_value('attributes', $args);
        
        $num_min    = $this -> get_attribute_value('min', $args);
        $num_max    = $this -> get_attribute_value('max', $args);
        $step       = $this -> get_attribute_value('step', $args);
        
        $num_min    = $num_min == '' ? 0 : $num_min;
        
        $use_units  = $args['use_units'] == 'on' ? true : false;
       
        $input_wrapper_class = $this->get_default_setting_value('global', 'input_wrapper_class', $id);
        $input_wrapper_class = apply_filters('ppom_input_wrapper_class', $input_wrapper_class, $args);
        $html       = '<div class="'.esc_attr($input_wrapper_class).'">';
        if( $label ){
            $html   .= '<label class="'.$this->get_default_setting_value('global', 'label_class', $id).'" for="'.$id.'">';
            $html   .= sprintf(__("%s", "ppom"), $label) .'</label>';
        }
        
        $classes .= ' ppom-measure-input';
        
        // Options
        $options    = $this -> get_attribute_value('options', $args);
        
        // ppom_pa($options);
        
        $dom_type = 'number';
        
        $html .= '<div class="ppom-measure">';
          
          if( $use_units ) {
          $html .= '<div class="form-check form-check-inline">';
            // $html .= '<button class="btn btn-outline-secondary ppom-measure-btn dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.__('Select',"ppom").'</button>';
            // $html .= '<div class="dropdown-menu">';
            
                foreach($options as $option) {
                    
                    $data_label = $label;
                    // $measure_label = $option['label'].'/'.$
                    $option_id = $option['option_id'];
                    $unit    = $option['raw'];
                    $html .= '<input checked name="ppom[unit]['.$id.']" value="'.esc_attr($unit).'" class="form-check-input ppom-measure-unit" type="radio" id="'.esc_attr($option_id).'" data-apply="measure" ';
                    $html .= sprintf(__('data-use_units="'.esc_attr($use_units).'" data-price="%s" data-label="%s" data-data_name="%s" data-unit="%s" data-optionid="%s">',"ppom"), $option['price'], esc_attr($data_label), $id, $unit, $option_id);
                    $html .= '<label class="form-check-label" id="'.esc_attr($option_id).'">';
                    $html .= sprintf(__("%s","ppom"), $option['label']);
                    $html .= '</label>';
                }
                
            // $html .= '</div>';
          $html .= '</div>';
          
            
          } else {
              
                $option_id = "{$id}_unit";
                $html .= '<input style="display:none"  value="" checked name="ppom[unit]['.$id.']" class="form-check-input ppom-input ppom-measure-unit" type="radio" id="'.esc_attr($option_id).'" data-apply="measure" ';
                $html .= sprintf(__('data-use_units="no" data-price="%s" data-label="%s" data-data_name="%s" data-optionid="%s" data-qty="%s">',"ppom"), 
                                        ppom_get_product_price($product), 
                                        esc_attr($label), 
                                        $id, 
                                        $option_id, 
                                        esc_attr($default_value));
                    
          }// Units used closed
          
          
        $html       .= '<input type="'.esc_attr($dom_type).'" ';
        $html       .= 'id="'.esc_attr($id).'" ';
        $html       .= 'data-data_name="'.esc_attr($id).'" ';
        $html       .= 'name="'.esc_attr($name).'" ';
        $html       .= 'class="'.esc_attr($classes).'" ';
        $html       .= 'placeholder="'.esc_attr($placeholder).'" ';
        $html       .= 'autocomplete="false" ';
        $html       .= 'data-type="'.esc_attr($type).'" ';
        
        // Adding min/max for number input
        $html       .= 'min="'.esc_attr($num_min).'" ';
        $html       .= 'max="'.esc_attr($num_max).'" ';
        $html       .= 'step="'.esc_attr($step).'" ';
        $html       .= 'data-price="'.esc_attr(ppom_get_product_price($product)).'" ';
        $html       .= 'data-title="'.esc_attr($label).'" '; // Input main label/title
        $html       .= 'data-use_units="'.esc_attr($use_units).'" '; // Input main label/title
        //   $html .= '<input type="text" class="form-control" aria-label="Text input with dropdown button">';
        
        //Values
        if( $default_value != '')
        $html      .= 'value="'.esc_attr($default_value).'" ';
        
        // Attributes
        foreach($attributes as $attr => $value) {
            
            $html      .= esc_attr($attr) . '="'.esc_attr($value).'" ';
        }
        
        
            $html      .= '>'; //closing input
            $html .= '</div>'; //input-group
        $html .= '</div>';    //form-group
        
        // filter: nmforms_input_htmls
        return apply_filters("nmforms_input_html", $html, $args, $default_value);
    }
    
    /**
     * Textarea field only
     * 
     * filter: nmforms_input_htmls
     * filter: 
     * */
    function Textarea($args, $default_value = '') {
        
        $label      = $this -> get_attribute_value('label', $args);
        $classes    = $this -> get_attribute_value('classes', $args);
        $id         = $this -> get_attribute_value('id', $args);
        $name       = $this -> get_attribute_value('name', $args);
        $placeholder= $this -> get_attribute_value('placeholder', $args);
        $attributes = $this -> get_attribute_value('attributes', $args);
        $rich_editor= $this -> get_attribute_value('rich_editor', $args);
        
        // cols & rows
        $cols       = $this -> get_attribute_value( 'cols', $args );
        $rows       = $this -> get_attribute_value( 'rows', $args );
        
        $input_wrapper_class = $this->get_default_setting_value('global', 'input_wrapper_class', $id);
        $input_wrapper_class = apply_filters('ppom_input_wrapper_class', $input_wrapper_class, $args);
        $html       = '<div class="'.$input_wrapper_class.'">';
        
        if( $label ){
            $html   .= '<label class="'.$this->get_default_setting_value('global', 'label_class', $id).'" for="'.$id.'">';
            $html   .= sprintf(__("%s", "ppom"), $label) .'</label>';
        }
        
        if( $rich_editor == 'on' ) {
						
			$wp_editor_setting = array('media_buttons'=> false,
									'textarea_rows'=> $rows,
									'editor_class' => $classes,
									'teeny'			=> true,
									'textarea_name'	=> $name	);
									
			ob_start();
            wp_editor($default_value, $id, $wp_editor_setting);
            $html .= ob_get_clean();
			
        } else {
        
            $html       .= '<textarea ';
            $html       .= 'id="'.esc_attr($id).'" ';
            $html       .= 'name="'.esc_attr($name).'" ';
            $html       .= 'data-data_name="'.esc_attr($id).'" ';
            $html       .= 'class="'.esc_attr($classes).'" ';
            $html       .= 'placeholder="'.esc_attr($placeholder).'" ';
            // $html       .= 'cols="'.esc_attr($cols).'" ';
            $html       .= 'rows="'.esc_attr($rows).'" ';
            
            // Attributes
            foreach($attributes as $attr => $value) {
                
                $html      .= esc_attr($attr) . '="'.esc_attr($value).'" ';
            }
            
            $html      .= '>';  // Closing textarea
            
            //Values
            if( $default_value != '') {
                $default_value  = str_replace('<br />',"\n",$default_value );
                $html      .= esc_html($default_value);
            }
            
            $html      .= '</textarea>';
        }
        
        $html      .= '</div>';    //form-group
        
        // filter: nmforms_input_htmls
        return apply_filters("nmforms_input_html", $html, $args, $default_value);
        
    }
    
    /**
     * Select options
     * 
     * $options: array($key => $value)
     **/
    public function Select( $args, $selected_value = '' ) {
        
        $product    = isset($args['product_id']) ? wc_get_product($args['product_id']) : null;
        
        $type       = $this -> get_attribute_value('type', $args);
        $label      = $this -> get_attribute_value('label', $args);
        $classes    = $this -> get_attribute_value('classes', $args);
        $id         = $this -> get_attribute_value('id', $args);
        $name       = $this -> get_attribute_value('name', $args);
        $multiple   = $this -> get_attribute_value('multiple', $args);
        $attributes = $this -> get_attribute_value('attributes', $args);
        
        // Only title without description for price calculation etc.
        $title      = $args['title'];
        // One time fee
        $onetime    = $args['onetime'];
        $taxable	= $args['taxable'];
        
        // Options
        $options    = $this -> get_attribute_value('options', $args);
        // ppom_pa($args);
       
        if ( ! $options ) return;

        $input_wrapper_class = $this->get_default_setting_value('global', 'input_wrapper_class', $id);
        $input_wrapper_class = apply_filters('ppom_input_wrapper_class', $input_wrapper_class, $args);
        $html       = '<div class="'.$input_wrapper_class.'">';
        
        if( $label ){
            $html   .= '<label class="'.$this->get_default_setting_value('global', 'label_class', $id).'" for="'.$id.'">';
            $html   .= sprintf(__("%s", "ppom"), $label) .'</label>';
        }
        
        $html       .= '<select ';
        $html       .= 'id="'.esc_attr($id).'" ';
        $html       .= 'name="'.esc_attr($name).'" ';
        $html       .= 'class="'.esc_attr($classes).'" ';
        $html       .= 'data-data_name="'.esc_attr($id).'" ';
        $html       .= ($multiple) ? 'multiple' : '';
        
        // Attributes
        foreach($attributes as $attr => $value) {
            
            $html      .= esc_attr($attr) . '="'.esc_attr($value).'" ';
        }
        
        $html       .= '>';  // Closing select
        
        $product_type = $product->get_type();
        
        foreach($options as $key => $value) {
                
                // for multiple selected
                // ppom_pa($value);
                
                $option_label   = $value['label'];
                $option_price   = $value['price'];
                $option_id      = isset($value['id']) ? $value['id'] : '';
                $raw_label      = $value['raw'];
                $without_tax    = $value['without_tax'];
                $opt_percent    = isset($value['percent']) ? $value['percent']: '';
                
                $ppom_has_percent = $opt_percent !== '' ? 'ppom-option-has-percent' : '';
                $option_class   = array("ppom-option-{$option_id}",
                                        "ppom-{$product_type}-option",
                                        $ppom_has_percent,
                                        );
                $option_class = apply_filters('ppom_option_classes', implode(" ", $option_class), $args);
                
                // if option has weight and price is not set, then set it zero for calculation
                if( empty($option_price) && !empty($value['option_weight']) ) {
                    $option_price = 0;
                }
                
                // var_dump($option_price);
                if( is_array($selected_value) ){
                
                    foreach($selected_value as $s){
                        $html   .= '<option '.selected( $s, $key, false ).' value="'.esc_attr($key).'" ';
                        $html   .= 'class="'.esc_attr($option_class).'" ';
                        $html   .= 'data-price="'.esc_attr($option_price).'" ';
                        $html   .= 'data-label="'.esc_attr($option_label).'" ';
                        $html   .= 'data-onetime="'.esc_attr($onetime).'" ';
                        $html   .= '>'.$option_label.'</option>';
                    }
                } else {
                    $html   .= '<option '.selected( $selected_value, $key, false ).' ';
                    $html   .= 'value="'.esc_attr($key).'" ';
                    $html   .= 'class="'.esc_attr($option_class).'" ';
                    $html   .= 'data-price="'.esc_attr($option_price).'" ';
                    $html   .= 'data-optionid="'.esc_attr($option_id).'" ';
                    $html   .= 'data-percent="'.esc_attr($opt_percent).'" ';
                    $html   .= 'data-label="'.esc_attr($raw_label).'" ';
                    $html   .= 'data-title="'.esc_attr($title).'" '; // Input main label/title
                    $html   .= 'data-onetime="'.esc_attr($onetime).'" ';
                    $html   .= 'data-taxable="'.esc_attr($taxable).'" ';
                    $html   .= 'data-without_tax="'.esc_attr($without_tax).'" ';
                    $html   .= 'data-data_name="'.esc_attr($id).'" ';
                    $html   .= '>'.$option_label.'</option>';
                }
            }
        
        $html .= '</select>';
        $html      .= '</div>';    //form-group
        
        
        // filter: nmforms_input_htmls
        return apply_filters("nmforms_input_html", $html, $args, $selected_value);
    }
    
    /**
     * Timezone
     * 
     * $options: array($key => $value)
     **/
    public function Timezone( $args, $selected_value = '' ) {
        
        $label      = $this -> get_attribute_value('label', $args);
        $classes    = $this -> get_attribute_value('classes', $args);
        $id         = $this -> get_attribute_value('id', $args);
        $name       = $this -> get_attribute_value('name', $args);
        $multiple   = $this -> get_attribute_value('multiple', $args);
        $attributes = $this -> get_attribute_value('attributes', $args);
        
        // Only title withou description for price calculation etc.
        $title      = $args['title'];
        
        
        // Options
        $options    = $this -> get_attribute_value('options', $args);


        if ( ! $options ) return;

        $input_wrapper_class = $this->get_default_setting_value('global', 'input_wrapper_class', $id);
        $input_wrapper_class = apply_filters('ppom_input_wrapper_class', $input_wrapper_class, $args);
        $html       = '<div class="'.$input_wrapper_class.'">';
        
        if( $label ){
            $html   .= '<label class="'.$this->get_default_setting_value('global', 'label_class', $id).'" for="'.$id.'">';
            $html   .= sprintf(__("%s", "ppom"), $label) .'</label>';
        }
        
        $html       .= '<select ';
        $html       .= 'id="'.esc_attr($id).'" ';
        $html       .= 'name="'.esc_attr($name).'" ';
        $html       .= 'class="'.esc_attr($classes).'" ';
        $html       .= 'data-data_name="'.esc_attr($id).'" ';
        $html       .= ($multiple) ? 'multiple' : '';
        
        // Attributes
        foreach($attributes as $attr => $value) {
            
            $html      .= esc_attr($attr) . '="'.esc_attr($value).'" ';
        }
        
        $html       .= '>';  // Closing select
        
        // ppom_pa($options);
        foreach($options as $key => $option_label) {
            
            
            if( is_array($selected_value) ){
            
                foreach($selected_value as $s){
                    $html   .= '<option '.selected( $s, $key, false ).' value="'.esc_attr($key).'" ';
                    $html   .= 'data-price="'.esc_attr($option_price).'" ';
                    $html   .= 'data-label="'.esc_attr($option_label).'" ';
                    $html   .= 'data-onetime="'.esc_attr($onetime).'" ';
                    $html   .= '>'.$option_label.'</option>';
                }
            } else {
                $html   .= '<option '.selected( $selected_value, $key, false ).' ';
                $html   .= 'value="'.esc_attr($key).'" ';
                $html   .= 'data-title="'.esc_attr($title).'" '; // Input main label/title
                $html   .= '>'.$option_label.'</option>';
            }
        }
        
        $html .= '</select>';
        $html .= '</div>';    //form-group
        
        
        // filter: nmforms_input_htmls
        return apply_filters("nmforms_input_html", $html, $args, $selected_value);
    }
    
    
    // Checkbox
    public function Checkbox( $args, $checked_value = array() ) {
        
        $type       = $this -> get_attribute_value( 'type', $args);
        
        $label      = $this -> get_attribute_value('label', $args);
        $classes    = $this -> get_attribute_value('classes', $args);
        $id         = $this -> get_attribute_value('id', $args);
        $name       = $this -> get_attribute_value('name', $args);
        
        // Only title withou description for price calculation etc.
        $title      = $args['title'];
        // One time fee
        $onetime    = $args['onetime'];
        $taxable	= $args['taxable'];
        
        // Options
        $options    = $this -> get_attribute_value('options', $args);
        
        // Checkbox label class
        $check_wrapper_class = apply_filters('ppom_checkbox_wrapper_class','form-check-inline');
        $check_label_class = $this -> get_attribute_value('check_label_class', $args);

        if ( ! $options ) return;
        
        
        $input_wrapper_class = $this->get_default_setting_value('global', 'input_wrapper_class', $id);
        $input_wrapper_class = apply_filters('ppom_input_wrapper_class', $input_wrapper_class, $args);
        $html       = '<div class="'.$input_wrapper_class.'">';
        
        if( $label ){
            $html   .= '<label class="'.$this->get_default_setting_value('global', 'label_class', $id).'" for="'.$id.'">';
            $html   .= sprintf(__("%s", "ppom"), $label) .'</label>';
        }
       
        if( is_array($checked_value) )
            $checked_value = array_map('trim', $checked_value);
        
        foreach($options as $key => $value) {
            
            $option_label = $value['label'];
            $option_price = $value['price'];
            $raw_label      = $value['raw'];
            $without_tax    = $value['without_tax'];
            $option_id      = $value['option_id'];
            $dom_id         = apply_filters('ppom_dom_option_id', $option_id, $args);
            
            $checked_option = '';
            if( count($checked_value) > 0 && in_array($key, $checked_value) && !empty($key)){
            
                $checked_option = checked( $key, $key, false );
            }
            
            // $option_id = sanitize_key( $id."-".$key );
            
            $html       .= '<div class="'.esc_attr($check_wrapper_class).'">';
                $html       .= '<label class="'.esc_attr($check_label_class).'" for="'.esc_attr($dom_id).'">';
                    $html       .= '<input type="'.esc_attr($type).'" ';
                    $html       .= 'id="'.esc_attr($dom_id).'" ';
                    $html       .= 'name="'.esc_attr($name).'[]" ';
                    $html       .= 'class="'.esc_attr($classes).'" ';
                    $html       .= 'value="'.esc_attr($key).'" ';
                    $html       .= 'data-optionid="'.esc_attr($option_id).'" ';
                    $html       .= 'data-price="'.esc_attr($option_price).'" ';
                    $html       .= 'data-label="'.esc_attr($raw_label).'" ';
                    $html       .= 'data-title="'.esc_attr($title).'" '; // Input main label/title
                    $html       .= 'data-onetime="'.esc_attr($onetime).'" ';
                    $html       .= 'data-taxable="'.esc_attr($taxable).'" ';
                    $html       .= 'data-without_tax="'.esc_attr($without_tax).'" ';
                    $html       .= 'data-data_name="'.esc_attr($id).'" ';
                    $html       .= $checked_option;
                    $html       .= '> ';  // Closing checkbox
                    $html       .= '<span class="ppom-label-checkbox">'.$option_label.'</span>';
                $html       .= '</label>';    // closing form-check
            $html       .= '</div>';    // closing form-check
        }
        
        $html      .= '</div>';    //form-group
        
        
        // filter: nmforms_input_htmls
        return apply_filters("nmforms_input_html", $html, $args, $checked_value);
    }
    
    
    // Radio
    public function Radio( $args, $checked_value = '' ) {
        
        $type       = $this -> get_attribute_value( 'type', $args);
        
        $label      = $this -> get_attribute_value('label', $args);
        $classes    = $this -> get_attribute_value('classes', $args);
        $id         = $this -> get_attribute_value('id', $args);
        $name       = $this -> get_attribute_value('name', $args);
        
        // Only title withou description for price calculation etc.
        $title      = $args['title'];
        // One time fee
        $onetime    = $args['onetime'];
        $taxable	= $args['taxable'];
        
        // Options
        $options    = $this -> get_attribute_value('options', $args);
        if ( ! $options ) return;
        
        // Radio label class
        $radio_wrapper_class = apply_filters('ppom_radio_wrapper_class','form-check');
        $radio_label_class = $this -> get_attribute_value('radio_label_class', $args);

        
        
        $input_wrapper_class = $this->get_default_setting_value('global', 'input_wrapper_class', $id);
        $input_wrapper_class = apply_filters('ppom_input_wrapper_class', $input_wrapper_class, $args);
        $html       = '<div class="'.$input_wrapper_class.'">';
        
        if( $label ){
            $html   .= '<label class="'.$this->get_default_setting_value('global', 'label_class', $id).'" for="'.$id.'">';
            $html   .= sprintf(__("%s", "ppom"), $label) .'</label>';
        }
        
        foreach($options as $key => $value) {
            
            $option_label   = $value['label'];
            $option_price   = $value['price'];
            $raw_label      = $value['raw'];
            $without_tax    = $value['without_tax'];
            $option_id      = $value['option_id'];
            $dom_id         = apply_filters('ppom_dom_option_id', $option_id, $args);
            
            $checked_option = '';
            if( ! empty($checked_value) ){
            
                $checked_value = stripcslashes($checked_value);
                $checked_option = checked( $checked_value, $key, false );
            }
            
            
            $html       .= '<div class="'.esc_attr($radio_wrapper_class).'">';
                $html       .= '<label class="'.esc_attr($radio_label_class).'" for="'.esc_attr($dom_id).'">';
                    $html       .= '<input type="'.esc_attr($type).'" ';
                    $html       .= 'id="'.esc_attr($dom_id).'" ';
                    $html       .= 'name="'.esc_attr($name).'" ';
                    $html       .= 'class="'.esc_attr($classes).'" ';
                    $html       .= 'value="'.esc_attr($key).'" ';
                    $html       .= 'data-price="'.esc_attr($option_price).'" ';
                    $html       .= 'data-optionid="'.esc_attr($option_id).'" ';
                    $html       .= 'data-label="'.esc_attr($raw_label).'" ';
                    $html       .= 'data-title="'.esc_attr($title).'" '; // Input main label/title
                    $html       .= 'data-onetime="'.esc_attr($onetime).'" ';
                    $html       .= 'data-taxable="'.esc_attr($taxable).'" ';
                    $html       .= 'data-without_tax="'.esc_attr($without_tax).'" ';
                    $html       .= 'data-data_name="'.esc_attr($id).'" ';
                    $html       .= $checked_option;
                    $html       .= '> ';  // Closing radio
                    $html       .= '<span class="ppom-label-radio">'.$option_label.'</span>';
                $html       .= '</label>';    // closing form-check
            $html       .= '</div>';    // closing form-check
        }
        
        $html      .= '</div>';    //form-group
        
        
        // filter: nmforms_input_htmls
        return apply_filters("nmforms_input_html", $html, $args, $checked_value);
    }
    
    // A custom input will be just some option html
    public function Palettes( $args, $default_value = '' ) {
        
        
        $product    = isset($args['product_id']) ? wc_get_product($args['product_id']) : null;
         
        $type       = $this -> get_attribute_value( 'type', $args);
        $id         = $this -> get_attribute_value( 'id', $args);
        $name       = $this -> get_attribute_value('name', $args);
        $label      = $this -> get_attribute_value( 'label', $args);
        $classes    = isset($args['classes']) ? $args['classes'] : '';
	    
	    // Only title withou description for price calculation etc.
        $title      = $args['title'];
        
        // One time fee
        $onetime    = $args['onetime'];
        $taxable	= $args['taxable'];
        
        // ppom_pa($args);
        
	    // Options
		$options    = isset($args['options']) ? $args['options'] : '';
		if ( ! $options ) return '';
		
        $html = '';
        
        if( !is_array($default_value) ) {
            
            $default_value = explode(',', $default_value);
        }
        
        $checked_value = array_map('trim', $default_value);

        // apply for selected palette border color
        $selected_palette_bcolor    = isset($args['selected_palette_bcolor']) ? $args['selected_palette_bcolor'] : '';
        $html .= '<style>';
                $html .=  '.ppom-palettes label > input:checked + .ppom-single-palette {
                        border: 2px solid '.esc_attr($selected_palette_bcolor).' !important;
                    }';
        $html .= '</style>';

        $input_wrapper_class = $this->get_default_setting_value('global', 'input_wrapper_class', $id);
        $input_wrapper_class = apply_filters('ppom_input_wrapper_class', $input_wrapper_class, $args);
        $html       .= '<div class="'.$input_wrapper_class.'">';
        if( $label ){
            $html   .= '<label class="'.$this->get_default_setting_value('global', 'label_class', $id).'" for="'.$id.'">';
            $html   .= sprintf(__("%s", "ppom"), $label) .'</label>';
        }
        // ppom_pa($options);
        $html .= '<div class="ppom-palettes ppom-palettes-'.esc_attr($id).'">';
     	foreach($options as $key => $value)
		{
			// First Separate color code and label
			$color_label_arr = explode('-', $key);
			$color_code = trim($color_label_arr[0]);
			$color_label = '';
			if(isset($color_label_arr[1])){
				$color_label = trim($color_label_arr[1]);
			}
			
			$color_label   = $value['label'];
			$option_label   = $value['label'];
        	$option_price   = $value['price'];
        	$raw_label      = $value['raw'];
        	$without_tax    = $value['without_tax'];

			$option_id      = $value['option_id'];
			$dom_id         = apply_filters('ppom_dom_option_id', $option_id, $args);
			
			$checked_option = '';
            if( count($checked_value) > 0 && in_array($key, $checked_value) && !empty($key)){
            
                $checked_option = checked( $key, $key, false );
            }
            
			
			$html .= '<label for="'.esc_attr($dom_id).'"> ';
			
			if ($args['multiple_allowed'] == 'on') {
    			$html .= '<input id="'.esc_attr($dom_id).'" ';
    			$html .= 'class="ppom-input" ';
    			$html .= 'data-price="'.esc_attr($option_price).'" ';
    			$html .= 'data-label="'.esc_attr($color_label).'" ';
    			$html   .= 'data-title="'.esc_attr($title).'" '; // Input main label/title
    			$html .= 'type="checkbox" ';
    			$html .= 'name="'.esc_attr($name).'[]" ';
    			$html .= 'value="'.esc_attr($raw_label).'" ';
    			$html .= 'data-onetime="'.esc_attr($onetime).'" ';
                $html .= 'data-taxable="'.esc_attr($taxable).'" ';
                $html .= 'data-without_tax="'.esc_attr($without_tax).'" ';
                $html .= 'data-optionid="'.esc_attr($option_id).'" ';
                $html .= 'data-data_name="'.esc_attr($id).'" ';
    			$html .= $checked_option;
    			$html .= '>';
    		}else{
    			
    			$html .= '<input id="'.esc_attr($dom_id).'" ';
    			$html .= 'class="ppom-input" ';
    			$html .= 'data-price="'.esc_attr($option_price).'" ';
    			$html .= 'data-label="'.esc_attr($color_label).'" ';
    			$html   .= 'data-title="'.esc_attr($title).'" '; // Input main label/title
    			$html .= 'type="radio" ';
    			$html .= 'name="'.esc_attr($name).'[]" ';
    			$html .= 'value="'.esc_attr($raw_label).'" ';
    			$html .= 'data-onetime="'.esc_attr($onetime).'" ';
                $html .= 'data-taxable="'.esc_attr($taxable).'" ';
                $html .= 'data-without_tax="'.esc_attr($without_tax).'" ';
                $html .= 'data-optionid="'.esc_attr($option_id).'" ';
                $html .= 'data-data_name="'.esc_attr($id).'" ';
    			$html .= $checked_option;
    			$html .= '>';
    		}
		
			$html .= '<span class="ppom-single-palette" ';
			$html	.= 'title="'.esc_attr($option_label).'" data-ppom-tooltip="ppom_tooltip" ';
			$html	.= 'style="background-color:'.esc_attr($color_code).'; ';
			$html	.= 'width:'.esc_attr($args['color_width']).'px; ';
			$html	.= 'height:'.esc_attr($args['color_height']).'px; ';
			if( $args['display_circle'] ) {
			    $html	.= 'border-radius: 50%; ';
			}
			$html	.= '">';    // Note '" ' is to close style inline attribute
			$html	.= '';
			$html	.= '</span>';
		
			$html .= '</label>';
		}
		$html .= '</div>'; //.ppom-palettes
        
        $html   .= '</div>';    //form-group
        
        // filter: nmforms_input_htmls
        return apply_filters("nmforms_input_html", $html, $args, $default_value);
    }
    
    // Image type
    public function Image( $args, $default_value = '' ) {
        
        $product    = isset($args['product_id']) ? wc_get_product($args['product_id']) : null;
         
        $type       = $this -> get_attribute_value( 'type', $args);
        $id         = $this -> get_attribute_value( 'id', $args);
        $name       = $this -> get_attribute_value('name', $args);
        $label      = $this -> get_attribute_value( 'label', $args);
        $classes    = $this -> get_attribute_value('classes', $args);
        
        // Only title withou description for price calculation etc.
        $title      = $args['title'];
        
	    // Options
	    $images    = isset($args['images']) ? $args['images'] : '';
        if ( ! $images ) return __("Images not selected", "ppom");

        $input_wrapper_class = $this->get_default_setting_value('global', 'input_wrapper_class', $id);
        $input_wrapper_class = apply_filters('ppom_input_wrapper_class', $input_wrapper_class, $args);
        $html       = '<div class="'.$input_wrapper_class.'">';
        if( $label ){
            $html   .= '<label class="'.$this->get_default_setting_value('global', 'label_class', $id).'" for="'.$id.'">';
            $html   .= sprintf(__("%s", "ppom"), $label) .'</label>';
        }
        
        // apply for selected images border color
		$selected_img_bordercolor    = isset($args['selected_img_bordercolor']) ? $args['selected_img_bordercolor'] : '';
        $html .= '<style>';
               $html .='.'.$id.' .nm-boxes-outer input:checked + img {
                       border: 2px solid '.esc_attr($selected_img_bordercolor).' !important;
                   }
                    .'.$id.' .pre_upload_image img{
                       height: '.esc_attr($args['image_height']).' !important;
                       width : '.esc_attr($args['image_width']).' !important;
                   }';
       $html .= '</style>';
       
    //   ppom_pa($images);
        
        if (isset($args['legacy_view']) && $args['legacy_view'] == 'on') {
	        $html .= '<div class="ppom_upload_image_box">';
			foreach ($images as $image){
						
				$image_full     = isset($image['link']) ? $image['link'] : 0;
				$image_id       = isset($image['image_id']) ? $image['image_id'] : 0;
				$image_title    = isset($image['raw']) ? stripslashes($image['raw']) : '';
				$image_label    = isset($image['label']) ? stripslashes($image['label']) : '';
				$image_price    = isset($image['price']) ? $image['price'] : 0;
				$option_id      = $id.'-'.$image_id;
				
				// If price set in %
				if(strpos($image['price'],'%') !== false){
					$image_price = ppom_get_amount_after_percentage($product->get_price(), $image['price']);
				}

	            // Actually image URL is link
				// $image_price    = apply_filters('ppom_option_price', $image_price);
				$image_link         = isset($image['url']) ? $image['url'] : '';
				$image_url          = apply_filters('ppom_image_input_url', wp_get_attachment_thumb_url( $image_id ), $image, $args);
	
				
				// Currency Switcher
				
				$checked_option = '';
				if( ! empty($default_value) ){
				    
	               if( !is_array($default_value) )
	                $checked_option = checked( $default_value, $image['raw'], false );
	            }
				
				$html .= '<div class="pre_upload_image '.esc_attr($classes).'">';
				
				if( !empty($image_link) ) {
				    $html .= '<a href="'.esc_url($image_link).'"><img class="img-thumbnail" src="'.esc_url($image_url).'" /></a>';
				} else {
				    $html .= '<img class="img-thumbnail"  data-model-id="modalImage'.esc_attr($image_id).'" src="'.esc_url($image_url).'" />';
				}
				
				// Loading Modals
				$modal_vars = array('image_id' => $image_id, 'image_full'=>$image_full, 'image_title'=>$image_label);
				ppom_load_template('v10/image-modals.php', $modal_vars);
				?>
				
				<?php
					
				$html	.= '<div class="input_image">';
				if ($args['multiple_allowed'] == 'on') {
					$html	.= '<input type="checkbox" ';
					$html   .= 'id="'.esc_attr($option_id).'" ';
					$html   .= 'data-price="'.esc_attr($image_price).'" ';
					$html   .= 'class="ppom-input" ';
					$html   .= 'data-label="'.esc_attr($image_title).'" ';
					$html   .= 'data-title="'.esc_attr($title).'" '; // Input main label/title
					$html   .= 'data-optionid="'.esc_attr($option_id).'" ';
                    $html   .= 'data-data_name="'.esc_attr($id).'" ';
					$html   .= 'name="'.$args['name'].'[]" ';
					$html   .= 'value="'.esc_attr(json_encode($image)).'" />';
				}else{
					
					//default selected
				// 	$checked = ($image['raw'] == $default_value ? 'checked = "checked"' : '' );
					$html	.= '<input type="radio" ';
					$html   .= 'id="'.esc_attr($option_id).'" ';
					$html   .= 'data-price="'.esc_attr($image_price).'" ';
					$html   .= 'class="ppom-input" ';
					$html   .= 'data-label="'.esc_attr($image_title).'" ';
					$html   .= 'data-title="'.esc_attr($title).'" '; // Input main label/title
					$html   .= 'data-type="'.esc_attr($type).'" name="'.$args['name'].'[]" ';
					$html   .= 'data-optionid="'.esc_attr($option_id).'" ';
                    $html   .= 'data-data_name="'.esc_attr($id).'" ';
					$html   .= 'value="'.esc_attr(json_encode($image)).'" '.$checked_option.' />';
				}
					
			    $html	.= '<div class="p_u_i_name">'.$image_label.'</div>';
				$html	.= '</div>';	//input_image
					
					
				$html .= '</div>';  // pre_upload_image
			}
			
			$html .= '</div>'; //.ppom_upload_image_box
        	
        } else {
			$html .= '<div class="nm-boxes-outer">';
				
			$img_index = 0;
			
			if ($images) {
			    
				
				
				foreach ($images as $image){

                    // ppom_pa($image);
					$image_full   = isset($image['link']) ? $image['link'] : 0;
					$image_id   = isset($image['image_id']) ? $image['image_id'] : 0;
					$image_title= isset($image['raw']) ? stripslashes($image['raw']) : '';
					$image_label= isset($image['label']) ? stripslashes($image['label']) : '';
					$image_price= isset($image['price']) ? $image['price'] : 0;
					$option_id  = $id.'-'.$image_id;

                    // If price set in %
    				if(strpos($image['price'],'%') !== false){
    					$image_price = ppom_get_amount_after_percentage($product->get_price(), $image['price']);
    				}
    				
    				// Actually image URL is link
					$image_link = isset($image['url']) ? $image['url'] : '';
					$image_url          = apply_filters('ppom_image_input_url', wp_get_attachment_thumb_url( $image_id ), $image, $args);
					
					$checked_option = '';
					
					if( ! empty($default_value) ){
					    
					    if( is_array($default_value) ) {
					        foreach($default_value as $img_data) {
					            
					            if( $image['image_id'] == $img_data['image_id'] ) {
					                $checked_option = 'checked="checked"';
					            }
					        }
					    } else {
					        
					        $checked_option = ($image['raw'] == $default_value ? 'checked=checked' : '' );
		                  
					    }
					    
		            }
					
					$html .= '<label>';
					$html .= '<div class="pre_upload_image '.esc_attr($classes).'" ';
					$html .= 'title="'.esc_attr($image_label).'" data-ppom-tooltip="ppom_tooltip">';
						if ($args['multiple_allowed'] == 'on') {
							$html	.= '<input type="checkbox" ';
							$html   .= 'id="'.esc_attr($option_id).'" ';
							$html   .= 'data-price="'.esc_attr($image_price).'" ';
							$html   .= 'data-label="'.esc_attr($image_title).'" ';
							$html   .= 'class="ppom-input" ';
							$html   .= 'data-title="'.esc_attr($title).'" '; // Input main label/title
							$html   .= 'data-optionid="'.esc_attr($option_id).'" ';
                            $html   .= 'data-data_name="'.esc_attr($id).'" ';
							$html   .= 'name="'.$args['name'].'[]" ';
							$html   .= 'value="'.esc_attr(json_encode($image)).'" '.esc_attr($checked_option).' />';
						}else{
							
							//default selected
				// 			$checked = ($image['label'] == $default_value ? 'checked = "checked"' : '' );
							$html	.= '<input type="radio" ';
							$html   .= 'id="'.esc_attr($option_id).'" ';
							$html   .= 'class="ppom-input" ';
							$html   .= 'data-price="'.esc_attr($image_price).'" ';
							$html   .= 'data-label="'.esc_attr($image_title).'" ';
							$html   .= 'data-title="'.esc_attr($title).'" '; // Input main label/title
							$html   .= 'data-optionid="'.esc_attr($option_id).'" ';
                            $html   .= 'data-data_name="'.esc_attr($id).'" ';
							$html   .= 'data-type="'.esc_attr($type).'" name="'.$args['name'].'[]" ';
							$html   .= 'value="'.esc_attr(json_encode($image)).'" '.esc_attr($checked_option).' />';
						}
						
					if($image['image_id'] != ''){
						if( isset($image['url']) && $image['url'] != '' ) {
							$html .= '<a href="'.$image['url'].'"><img src="'.$image_url.'" /></a>';
						} else {
						    
						    $image_url = wp_get_attachment_thumb_url( $image['image_id'] );
							$html .= '<img data-image-tooltip="'.wp_get_attachment_url($image['image_id']).'" class="img-thumbnail ppom-zoom-'.esc_attr($id).'" src="'.esc_url($image_url).'" />';
						}
						
					}else{
						if( isset($image['url']) && $image['url'] != '' )
							$html .= '<a href="'.$image['url'].'"><img width="150" height="150" src="'.esc_url($image['link']).'" /></a>';
						else {
							$html .= '<img class="img-thumbnail ppom-zoom-'.esc_attr($id).'" data-image-tooltip="'.esc_url($image['link']).'" src="'.esc_url($image['link']).'" />';
						}
					}
					
					$html .= '</div></label>';
						
					$img_index++;
				}
			}
			
			$html .= '<div style="clear:both"></div>';	
				
			$html .= '</div>';		//nm-boxes-outer
        }
        $html   .= '</div>';    //form-group
        
        // filter: nmforms_input_htmls
        return apply_filters("nmforms_input_html", $html, $args, $default_value);
        // return 'asd';
    }
    
    // A custom input will be just some option html
    public function Pricematrix( $args, $default_value = '' ) {
         
        $id         = $this -> get_attribute_value( 'id', $args);
        $type       = $this -> get_attribute_value( 'type', $args);
        $label      = $this -> get_attribute_value( 'label', $args);
        $ranges     = $args['ranges'];
        $discount   = $args['discount'];
        $is_hidden  = ($args['hide_matrix'] == 'on') ? true : false;
        
        // ppom_pa($ranges);
        
        $input_wrapper_class = $this->get_default_setting_value('global', 'input_wrapper_class', $id);
        $input_wrapper_class = apply_filters('ppom_input_wrapper_class', $input_wrapper_class, $args);
        $html       = '<div class="'.$input_wrapper_class.'">';
        if( $label ){
            $html   .= '<label class="'.$this->get_default_setting_value('global', 'label_class', $id).'" for="'.$id.'">';
            $html   .= sprintf(__("%s", "ppom"), $label) .'</label>';
        }
        
        // Check if price matrix table is not hidden by settings
        if( ! $is_hidden ) {
            foreach($ranges as $opt)
    		{
    			$price = isset( $opt['raw_price'] ) ? trim($opt['raw_price']) : 0;
    			$label = isset( $opt['label'] ) ? $opt['label'] : $opt['raw'];
    			
    			if( !empty($opt['percent']) ){
    				
    				$percent = $opt['percent'];
    				if( $discount == 'on' ) {
    				    $price = "-{$percent}";
    				} else {
    				    $price = "{$percent} (".wc_price( $price ).")";
    				}
    				
    			}else {
    				$price = wc_price( $price );	
    			}
    			
    			
    			$range_id      = isset($opt['option_id']) ? $opt['option_id'] : '';
    			
    			$html .= '<div class="ppom-pricematrix-range ppom-range-'.esc_attr($range_id).'">';
    			$html .= '<span class="pm-range">'.apply_filters('ppom_matrix_item_label', stripslashes(trim($label)), $opt).'</span>';
    			$html .= '<span class="pm-price" style="float:right">'.apply_filters('ppom_matrix_item_price', $price, $opt).'</span>';
    			$html .= '</div>';
    		}
        }
		
		// Showing Range Slider
		if( isset($args['show_slider']) && $args['show_slider'] == 'on' ) {
		    
		    $first_range = reset($ranges);
			$qty_ranges = explode('-', $first_range['raw']);
			$min_quantity	= $qty_ranges[0]-1;
		    
		    $last_range = end($ranges);
			$qty_ranges = explode('-', $last_range['raw']);
			$max_quantity	= $qty_ranges[1];
			$qty_step   = !empty($args['qty_step']) ? $args['qty_step'] : 1;
			
		    $html   .= '<div class="ppom-slider-container">';
		    
		    if( apply_filters('ppom_range_slider_legacy', false, $args) ) {
		    
    		    $html   .= '<input class="ppom-range-slide" data-slider-id="ppomSlider" ';
    		    $html   .= 'type="text" data-slider-min="'.esc_attr($min_quantity).'"
    		                            data-slider-max="'.esc_attr($max_quantity).'" 
    		                            data-slider-step="'.esc_attr($qty_step).'" 
    		                            data-slider-value="0"/>';
		    } else {
		        $html .= '<input type="range" class="form-control-range ppom-range-bs-slider" 
		                                id="'.esc_attr($id).'"
		                                min="'.esc_attr($min_quantity).'"
    		                            max="'.esc_attr($max_quantity).'" 
    		                            step="'.esc_attr($qty_step).'" >';
		    }
		    $html   .= '</div>';
		    
		}
        
        $html   .= '</div>';    //form-group
        
        $html   .= '<input name="ppom[ppom_pricematrix]" data-dataname="'.esc_attr($id).'" data-discount="'.esc_attr($discount).'" class="active ppom_pricematrix ppom-input" type="hidden" value="'.esc_attr( json_encode($ranges)).'" />';
        
        // filter: nmforms_input_htmls
        return apply_filters("nmforms_input_html", $html, $args, $default_value);
    }
    
    // Variation Quantities
    public function Quantities( $args, $default_value = '' ) {
         
        $product    = isset($args['product_id']) ? wc_get_product($args['product_id']) : null;
        // IMPORTANT
        // if price matrix is used and quantities has price set or default price
        // it will conflict. So to use with price matrix prices should not be set
        $product_id     = ppom_get_product_id($product);
        $matrix_found   = ppom_has_field_by_type( $product_id, 'pricematrix' );
        if( !empty($matrix_found) && ppom_is_field_has_price($args) ) {
            $error_msg = __('Quantities cannot be used with Price Matrix, Remove prices from quantities input.', 'ppom');
            return sprintf(__('<div class="woocommerce-error">%s</div>', 'ppom'), $error_msg);
        }
        
        $type       = $this -> get_attribute_value( 'type', $args);
        $id         = $this -> get_attribute_value( 'id', $args);
        $label      = $this -> get_attribute_value( 'label', $args);
        // ppom_pa($args);
        $input_wrapper_class = $this->get_default_setting_value('global', 'input_wrapper_class', $id);
        $input_wrapper_class = apply_filters('ppom_input_wrapper_class', $input_wrapper_class, $args);
        $html       = '<div class="ppom-input-quantities '.$input_wrapper_class.' table-responsive">';
        if( $label ){
            $html   .= '<label class="'.$this->get_default_setting_value('global', 'label_class', $id).'" for="'.$id.'">';
            $html   .= sprintf(__("%s", "ppom"), $label) .'</label>';
        }
        
        
        $template_vars = array('args' => $args, 'default_value'=>$default_value);
        ob_start();
        ppom_load_template( 'input/quantities.php', $template_vars);
        $html .= ob_get_clean();
        
        $html   .= '</div>';    //form-group
        
        // filter: nmforms_input_htmls
        return apply_filters("nmforms_input_html", $html, $args, $default_value);
    }
    
    // HTML or Text (section)
    public function Section( $args, $default_value = '' ) {
         
        $type       = $this -> get_attribute_value( 'type', $args);
        $name       = $this -> get_attribute_value('name', $args);
        $id         = $this -> get_attribute_value( 'id', $args);
        $label      = $this -> get_attribute_value('label', $args);
        $field_html = $this -> get_attribute_value( 'html', $args);
        
        // var_dump($field_html);
        $input_wrapper_class = $this->get_default_setting_value('global', 'input_wrapper_class', $id);
        $input_wrapper_class = apply_filters('ppom_input_wrapper_class', $input_wrapper_class, $args);
        $html       = '<div class="'.$input_wrapper_class.'">';
       
        if( $label ) {
            
            $field_html = $field_html . $label;
        }
        
        $html_content = apply_filters( 'ppom_section_content', $field_html );
        
        $html   .= stripslashes( $html_content );
        
        $html .= '<div style="clear: both"></div>';
        
        $html .= '<input type="hidden" id="'.esc_attr($id).'" name="'.esc_attr($name).'" value="'.esc_attr($field_html).'">';
        
        $html   .= '</div>';    //form-group
        
        // filter: nmforms_input_htmls
        return apply_filters("nmforms_input_html", $html, $args, $default_value);
    }
    
    // Audio/video
    public function Audio_video( $args, $default_value = '' ) {
         
        $type       = $this -> get_attribute_value( 'type', $args);
        $id         = $this -> get_attribute_value( 'id', $args);
        $name       = $this -> get_attribute_value('name', $args);
        $label      = $this -> get_attribute_value( 'label', $args);
        $classes    = isset($args['classes']) ? $args['classes'] : '';
	    
	    // Only title withou description for price calculation etc.
        $title      = $args['title'];
        
	    // Options
		$audios    = isset($args['audios']) ? $args['audios'] : '';
		if ( ! $audios ) return __("audios not selected", "ppom");

        $input_wrapper_class = $this->get_default_setting_value('global', 'input_wrapper_class', $id);
        $input_wrapper_class = apply_filters('ppom_input_wrapper_class', $input_wrapper_class, $args);
        $html       = '<div class="'.$input_wrapper_class.'">';
        if( $label ){
            $html   .= '<label class="'.$this->get_default_setting_value('global', 'label_class', $id).'" for="'.$id.'">';
            $html   .= sprintf(__("%s", "ppom"), $label) .'</label>';
        }
        
        // ppom_pa($audios);
        $html .= '<div class="ppom_audio_box">';
		foreach ($audios as $audio){
					
			
			$audio_link = isset($audio['link']) ? $audio['link'] : 0;
			$audio_id   = isset($audio['id']) ? $audio['id'] : 0;
			$audio_title= isset($audio['title']) ? stripslashes($audio['title']) : 0;
			$audio_price= isset($audio['price']) ? $audio['price'] : 0;

            // Actually image URL is link
			$audio_url  = wp_get_attachment_url( $audio_id );
			$audio_title_price = $audio_title . ' ' . ($audio_price > 0 ? ppom_price($audio_price) : '');
			
			$checked_option = '';
			if( ! empty($default_value) ){
        
                $checked_option = checked( $default_value, $key, false );
            }
			
			$html .= '<div class="ppom_audio">';
			
			if( !empty($audio_url) ) {
			    $html .= apply_filters( 'the_content', $audio_url );
			}
			
			?>
			
			<?php
				
			$html	.= '<div class="input_image">';
			if ($args['multiple_allowed'] == 'on') {
				$html	.= '<input type="checkbox" ';
				$html   .= 'data-price="'.esc_attr($audio_price).'" ';
				$html   .= 'class="ppom-input" ';
				$html   .= 'data-data_name="'.esc_attr($id).'" ';
				$html   .= 'data-label="'.esc_attr($audio_title).'" ';
				$html   .= 'data-title="'.esc_attr($title).'" '; // Input main label/title
				$html   .= 'name="'.$args['name'].'[]" ';
				$html   .= 'value="'.esc_attr(json_encode($audio)).'" />';
			}else{
				
				$html	.= '<input type="radio" ';
				$html   .= 'data-price="'.esc_attr($audio_price).'" ';
				$html   .= 'data-label="'.esc_attr($audio_title).'" ';
				$html   .= 'class="ppom-input" ';
				$html   .= 'data-data_name="'.esc_attr($id).'" ';
				$html   .= 'data-title="'.esc_attr($title).'" '; // Input main label/title
				$html   .= 'data-type="'.esc_attr($type).'" name="'.$args['name'].'[]" ';
				$html   .= 'value="'.esc_attr(json_encode($audio)).'" '.$checked_option.' />';
			}
				
		    $html	.= '<div class="p_u_i_name">'.$audio_title_price.'</div>';
			$html	.= '</div>';	//input_image
				
				
			$html .= '</div>';  // pre_upload_image
		}
		
		$html .= '</div>'; //.ppom_upload_image_box
        $html   .= '</div>';    //form-group
        
        // filter: nmforms_input_htmls
        return apply_filters("nmforms_input_html", $html, $args, $default_value);
    }
    
    // File Upload
    public function File( $args, $default_files = '' ) {
         
        $type       = $this -> get_attribute_value( 'type', $args);
        $id         = $this -> get_attribute_value( 'id', $args);
        $label      = $this -> get_attribute_value( 'label', $args);
        
       
        $input_wrapper_class = $this->get_default_setting_value('global', 'input_wrapper_class', $id);
        $input_wrapper_class = apply_filters('ppom_input_wrapper_class', $input_wrapper_class, $args);
        
        $html       = '<div id="ppom-file-container-'.esc_attr($args['id']).'" class="'.$input_wrapper_class.'">';
        if( $label ){
            $html   .= '<label class="'.$this->get_default_setting_value('global', 'label_class', $id).'" for="'.$id.'">';
            $html   .= sprintf(__("%s", "ppom"), $label) .'</label>';
        }
        

        $container_height = isset($args['dragdrop']) ? 'auto' : '30px' ;
        $html .= '<div class="ppom-file-container text-center" ';
        $html .= 'style="height: '.esc_attr($container_height).' ;">';
			$html .= '<a id="selectfiles-'.esc_attr($args['id']).'" ';
			$html .= 'href="javascript:;" ';
			$html .= 'class="btn btn-primary '.esc_attr($args['button_class']).'">';
			$html .= $args['button_label'] . '</a>';
			$html .= '<span class="ppom-dragdrop-text">';
			$html .= __("Drag File Here", "ppom");
			$html .= '</span>';
		$html .= '</div>';		//ppom-file-container

		if($args['dragdrop']){
			
			$html .= '<div class="ppom-droptext">';
				$html .= __('Drag file/directory here', "ppom");
			$html .= '</div>';
		}
    	
    	$html .= '<div id="filelist-'.esc_attr($args['id']).'" class="filelist">';
    	
    	
    	// Editing existing file
    	if( !empty( $default_files ) ) {
    	    
    	   // var_dump($default_files);
    	    
    	    foreach($default_files as $key => $file ) {
    	        
    	        $file_preview = ppom_uploaded_file_preview($file['org'], $args);
    	        if( !isset($file['org']) || $file_preview == '') continue;
    	        
    	        $html .= '<div class="u_i_c_box" id="u_i_c_'.esc_attr($key).'" data-fileid="'.esc_attr($key).'">';
    	        
    	        $html .= $file_preview;
    	        
    	        if( $html != '' ) 
    	        
    	        $file_name = $file['org'];
    	        $data_name = 'ppom[fields]['.$args['id'].']['.$key.'][org]';
    	        $file_class = 'ppom-file-cb ppom-file-cb-'.$args['id'];
    	        
    	        // Adding CB for data handling
    	        $html .= '<input checked="checked" name="'.esc_attr($data_name).'" ';
    	        $html .= 'data-price="'.esc_attr($args['file_cost']).'" ';
    	        $html .= 'data-label="'.esc_attr($file_name).'" ';
    	        $html .= 'data-title="'.esc_attr($label).'" ';
    	        $html .= 'value="'.esc_attr($file_name).'" ';
    	        $html .= 'class="'.esc_attr($file_class).'" ';
    	        $html .= 'type="checkbox"/>';
    	        
    	        $html .= '</div>'; //u_i_c_box
    	        
    	    }
    	}
    	
    	$html .= '</div>';  // filelist
        
        $html   .= '</div>';    //form-group
        
        // filter: nmforms_input_htmls
        return apply_filters("nmforms_input_html", $html, $args, $default_files);
    }
    
    // Cropper
    public function Cropper( $args, $selected_value = '' ) {
         
        $type       = $this -> get_attribute_value( 'type', $args);
        $id         = $this -> get_attribute_value( 'id', $args);
        $name       = $this -> get_attribute_value('name', $args);
        $label      = $this -> get_attribute_value( 'label', $args);
        $title      = $this -> get_attribute_value( 'title', $args);
        $classes    = $this -> get_attribute_value('classes', $args);
        
        $input_wrapper_class = $this->get_default_setting_value('global', 'input_wrapper_class', $id);
        $input_wrapper_class = apply_filters('ppom_input_wrapper_class', $input_wrapper_class, $args);
        
        $html       = '<div id="ppom-file-container-'.esc_attr($args['id']).'" class="'.$input_wrapper_class.'">';
        if( $label ){
            $html   .= '<label class="'.$this->get_default_setting_value('global', 'label_class', $id).'" for="'.$id.'">';
            $html   .= sprintf(__("%s", "ppom"), $label) .'</label>';
        }
        
        $container_height = isset($args['dragdrop']) ? 'auto' : '30px' ;
        $html .= '<div class="ppom-file-container text-center" ';
        $html .= 'style="height: '.esc_attr($container_height).' ;">';
			$html .= '<a id="selectfiles-'.esc_attr($args['id']).'" ';
			$html .= 'href="javascript:;" ';
			$html .= 'class="btn btn-primary '.esc_attr($args['button_class']).'">';
			$html .= $args['button_label'] . '</a>';
			$html .= '<span class="ppom-dragdrop-text">'.__('Drag file/directory here', "ppom").'</span>';
		$html .= '</div>';		//ppom-file-container

		if($args['dragdrop']){
			
			$html .= '<div class="ppom-droptext">';
				$html .= __('Drag file/directory here', "ppom");
			$html .= '</div>';
		}
    	
    	$html .= '<div id="filelist-'.esc_attr($args['id']).'" class="filelist"></div>';
    	
    	$html   .= '<div class="ppom-croppie-wrapper-'.esc_attr($args['id']).' text-center">';
    	$html   .= '<div class="ppom-croppie-preview">';
        // 	ppom_pa($args);
    
    	// @since: 12.8
    	// Showing size option if more than one found.
    	if (isset($args['options']) && count($args['options']) > 0){
    	    
    	   $cropping_sizes = $args['options'];
    	    
    	   $select_css = 'width:'.$args['croppie_options']['boundary']['width'].'px;';
    	   $select_css .= 'margin:5px auto;display:none;';
    	    
    	    $html   .= '<select style="'.esc_attr($select_css).'" ';
    	        $html .= 'class="'.esc_attr($classes).'" ';
    	        $html .= 'name="'.esc_attr($name).'[ratio]" ';
    	        $html .= 'data-field_name="'.esc_attr($args['id']).'" ';
    	        $html .= 'data-data_name="'.esc_attr($args['id']).'" ';
    	        $html .= 'id="crop-size-'.esc_attr($args['id']).'">';
    	        
    	        if( $args['first_option'] ) {
    	            
    	            $html .= sprintf(__('<option value="">%s</option>','ppom'),$args['first_option']);
    	        }
    	        
    	        foreach($cropping_sizes as $key => $size) {
    	            
    	            $option_label   = $size['label'];
                    $option_price   = $size['price'];
                    $raw_label      = $size['raw'];
                    $without_tax    = $size['without_tax'];
                    $option_id      = $size['option_id'];
                    
                    if( $option_id == "__first_option__" ) continue;
    	            
    	            $html   .= '<option '.selected( $selected_value, $key, false ).' ';
                    $html   .= 'value="'.esc_attr($option_id).'" ';
                    $html   .= 'data-price="'.esc_attr($option_price).'" ';
                    $html   .= 'data-label="'.esc_attr($raw_label).'" ';
                    $html   .= 'data-title="'.esc_attr($title).'" '; // Input main label/title
                    // $html   .= 'data-onetime="'.esc_attr($onetime).'" ';
                    // $html   .= 'data-taxable="'.esc_attr($taxable).'" ';
                    $html   .= 'data-without_tax="'.esc_attr($without_tax).'" ';
                    $html   .= 'data-width="'.esc_attr($size['width']).'" data-height="'.esc_attr($size['height']).'" ';
                    $html   .= '>'.$option_label.'</option>';
    	        }
    	        
    	   $html    .= '</select>';
    	   
    	}
    	
    	$html   .= '</div>';    // ppom-croppie-preview
    	$html   .= '</div>'; //ppom-croppie-wrapper
    
        $html   .= '</div>';    //form-group
        
        // filter: nmforms_input_htmls
        return apply_filters("nmforms_input_html", $html, $args, $selected_value);
    }
    
    // A custom input will be just some option html
    public function Custom( $args, $default_value = '' ) {
         
        $type       = $this -> get_attribute_value( 'type', $args);
        $id         = $this -> get_attribute_value( 'id', $args);
        $label      = $this -> get_attribute_value( 'label', $args);
        
        $input_wrapper_class = $this->get_default_setting_value('global', 'input_wrapper_class', $id);
        $input_wrapper_class = apply_filters('ppom_input_wrapper_class', $input_wrapper_class, $args);
        $html       = '<div class="'.$input_wrapper_class.'">';
        if( $label ){
            $html   .= '<label class="'.$this->get_default_setting_value('global', 'label_class', $id).'" for="'.$id.'">';
            $html   .= sprintf(__("%s", "ppom"), $label) .'</label>';
        }
        
        $html   .= apply_filters('nmform_custom_input', $html, $args, $default_value);
        
        $html   .= '</div>';    //form-group
        
        // filter: nmforms_input_htmls
        return apply_filters("nmforms_input_html", $html, $args, $default_value);
    }
    
    

    /**
     * this function return current or/else default attribute values
     * 
     * filter: nmform_attribute_value
     * 
     * */
    private function get_attribute_value( $attr, $args ) {
        
        $attr_value = '';
        $type       = isset($args['type']) ? $args['type'] : $this->get_default_setting_value('global', 'type');
        
        // if( $attr == 'type' ) return $type;
        
        if( isset($args[$attr]) ){
            
            $attr_value = $args[$attr];
        } else {
            
            $attr_value = $this->get_default_setting_value( $type, $attr );
        }
        
        return apply_filters('nmform_attribute_value', $attr_value, $attr, $args);
    }
    
    
    /**
     * this function return default value
     * defined in class/config
     * 
     * @params: $setting_type
     * @params: $key
     * filter: default_setting_value
     * */
    function get_default_setting_value( $setting_type, $key, $field_id = '' ){
        
        $defaults = $this -> get_property( 'defaults' );
        
        $default_value = isset( $defaults[$setting_type][$key] ) ? $defaults[$setting_type][$key] : '';
        
        return apply_filters('default_setting_value', $default_value, $setting_type, $key, $field_id);
    }
    
    
    /**
     * function return class property values/settings
     * 
     * filter: nmform_property-{$property}
     * */
    private function get_property( $property ) {
        
        $value = '';
        switch( $property ) {
            
            case 'echoable':
                    $value = ECHOABLE;
            break;
            
            case 'defaults':
                
                    $value =  array(
                                    'global'   => array('type' => 'text',
                                                        'input_wrapper_class'=>'form-group',
                                                        'label_class'   => 'form-control-label',),
                                    'text'      => array('placeholder' => "", 'attributes' => array()),
                                    'date'      => array(),
                                    'email'     => array(),
                                    'number'    => array(),
                                    'cropper'    => array('classes' => array('ppom-cropping-size','form-control')),
                                    'textarea'  => array('cols' => 6, 'rows' => 3, 'placeholder'=>''),
                                    'select'    => array('multiple' => false),
                                    'checkbox'  => array('label_class' => 'form-control-label',
                                                        'check_wrapper_class' => 'form-check',
                                                        'check_label_class' => 'form-check-label',
                                                        'classes' => array('ppom-check-input')),
                                    'radio'     => array('label_class' => 'form-control-label',
                                                        'radio_wrapper_class' => 'form-check',
                                                        'radio_label_class' => 'form-check-label',
                                                        'classes' => array('ppom-check-input')),
                    );
            break;
        }
        
        return apply_filters("nmform_property-{$property}", $value);
        
    }
    
    
    
    /**
     * ====================== FILTERS =====================================
     * 
     * */
     
    public function adjust_attributes_values( $attr_value, $attr, $args ) {
        
        switch( $attr ) {
            
            // converting classes to string
            case 'classes':
                $type   =  $this -> get_attribute_value( 'type', $args);
                
                if ($type != 'image') {
                    
                    $attr_value[] = 'ppom-input';
                }
                // adding ppom-input class to all inputs
                // {type} also added as class
                $attr_value[] = $type;
                $attr_value = implode(" ", $attr_value);
            break;
            
            /**
             * converting name to array for multiple:select
             * */
            case 'name':
                
                $type   =  $this -> get_attribute_value( 'type', $args);
                $multiple   = $this -> get_attribute_value('multiple', $args);
                if( $type == 'select' && $multiple ){
                    
                    $attr_value .= '[]';
                }
            break;
        }
        
        return $attr_value;
    }
    
    /**
     * ====================== ENDs FILTERS =====================================
     * 
     * */
    
    public static function get_instance()
	{
		// create a new object if it doesn't exist.
		is_null(self::$ins) && self::$ins = new self;
		return self::$ins;
	}
}

function NMForm(){
	return NM_Form::get_instance();
}