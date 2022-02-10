<?php
/**
 * PPOM Legacy Inputs Meta Data
 *
 * It control the inputs meta data. It show all the legacy inputs settings.
 *
 * @version  21.2
 */

/* 
**========== Block direct access =========== 
*/
if( ! defined('ABSPATH' ) ){ exit; }

class PPOM_Legacy_InputManager {
    
    /**
	 * Return all ppom inputs meta data
	 *
	 * @var array
	 */
    public static $input_meta;
    
    /**
	 * Return input type
	 *
	 * @var string
	 */
    public $input_type;
    
    /* ======= Class Construct ======== */
    function __construct( $input_meta, $input_type ) {
        
        self::$input_meta  = $input_meta;
        
        $this->input_type = $input_type;
    }
    
    /**
	 * Field Title
	 */
    function title() {

        $title = isset( self::$input_meta['title'] ) ?  stripslashes( self::$input_meta['title'] ): '';
        
        return ppom_wpml_translate($title, 'PPOM');
    }


    /**
	 * Field dataname (Field Unique ID)
	 */
    function data_name() {

        $data_name = isset( self::$input_meta['data_name'] ) ?  sanitize_key( self::$input_meta['data_name'] ): $this->title();
        
        return $data_name;
    }
    
    
    /**
	 * Field Desciption
	 */
    function desc() {

        $desc = isset( self::$input_meta['description'] ) ?  stripslashes( self::$input_meta['description'] ): '';
        
        $desc = ppom_wpml_translate($desc, 'PPOM');
        
        // old Filter
        return apply_filters( 'ppom_description_content', $desc, self::$input_meta );
    }

    
    /**
	 * Field Required
	 */
    function required() {

        return isset( self::$input_meta['required'] ) ?  self::$input_meta['required']: '';
    }

    
    /**
	 * Field Colunm Width
	 */
    function width() {

        $field_column = isset( self::$input_meta['width'] ) ?  self::$input_meta['width']: 12;
    
        // Check width has old settings
        if( strpos( $field_column, '%' ) !== false ) {
            
            $field_column = 12;
        } elseif( intval($field_column) > 12 ) {
            $field_column = 12;
        }
        
        return apply_filters('ppom_field_col', $field_column, self::$input_meta);
    }


    /**
	 * Field Label
	 * 
	 * Show Asterisk If Require On
	 * 
	 * Show Description If Not Null
	 */
    function field_label(){

        $asterisk    = ( !empty($this->required()) && $this->title() != '' ) ? '<span class="show_required"> *</span>' : '';

        $show_desc   = ( !empty( $this->desc() ) ) ? '<span class="show_description">'. $this->desc() .'</span>' : '';
        
        $show_desc   = apply_filters('ppom_field_description', $show_desc, self::$input_meta);

        return $this->title() . $asterisk . $show_desc;
    }
    
    
    /**
	 * Field Multiple Options
	 * 
	 * Checkbox|Radio|Select|Image|Pallete
	 */
    function options() {

        $options = isset( self::$input_meta['options'] ) ?  self::$input_meta['options']: array();

        if(is_array($options)){
            $options = array_map("ppom_translation_options", $options);
        }

        return $options;
    }
    
    
    /**
	 * Field Classes Array
	 */
    function input_classes_array() {

        $classes   = isset( self::$input_meta['class'] ) ? explode(',',self::$input_meta['class']): array();

        if( !empty( $classes ) ) {
            $classes[] = 'form-control';
        } else {
            $classes = array('form-control');
        }
        
        // TODO: re-check again
        if ($this->input_type != 'image') {
            $classes[] = 'ppom-input';
        }
        
        if (($this->input_type == 'radio' && ($key = array_search('form-control', $classes)) !== false) || 
            $this->input_type == 'checkbox' && ($key = array_search('form-control', $classes)) !== false ) {
			unset($classes[$key]);
            $classes[] = 'ppom-check-input';
		}
        
        return $classes;
    }
}