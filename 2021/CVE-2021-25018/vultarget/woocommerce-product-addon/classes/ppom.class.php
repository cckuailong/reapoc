<?php
/**
 * PPOM Meta Class
 * @since version 15.0
 * 
 * */
 
 
class PPOM_Meta {
    
        protected static $wc_product;
        private static $ins = null;
        public static $product_id;
        var $meta_id;
        
        // $product_id can be null if get instance to get data by meta_id
        function __construct( $product_id=null ) {
            
            self::$wc_product = wc_get_product( $product_id );
            $this->category_meta = [];
            $this->ppom_with_cat = $this->all_ppom_with_categories();
            $this->meta_id    = $this->get_meta_id($product_id);
            self::$product_id = $product_id;
            
            
            $this->ppom_settings = $this->settings();
            $this->fields        = $this->get_fields();
            
            //Now we are creating properties agains each methods in our Alpha class.
            $methods = get_class_methods( $this );
            $excluded_methods = array('__construct', 
                                        'get_settings_by_id',
                                        'get_fields_by_id',
                                        'settings',
                                        'all_ppom_with_categories',
                                        'ppom_has_category_meta',
                                        'get_meta_id',
                                        'get_fields',
                                        'has_unique_datanames',
                                        'get_instance');
                                        
            foreach ( $methods as $method ) {
                if ( ! in_array($method, $excluded_methods) ) {
                    $this->$method = $this->$method();
                }
            }
            
        }
        
        public static function get_instance($product_id) {
	    
	        // ALERT: This cause issues .. April 16, 2021: Najeeb
            // ( $product_id != self::$product_id || is_null(self::$ins) ) &&  self::$ins = new PPOM_Meta($product_id);
            
            is_null(self::$ins) && self::$ins = new PPOM_Meta($product_id);
            
            return self::$ins;
        }
        
        
        function get_meta_id($product_id) {
            
            $ppom_product_id = get_post_meta ( $product_id, PPOM_PRODUCT_META_KEY, true );
            
            if( $ppom_product_id == 0 || $ppom_product_id == 'None' ) {
        		$ppom_product_id = null;
        	}
        	
            // Checking if PPOM attached in category
            $ppom_in_category = $this->ppom_has_category_meta( $product_id );
            // if( $ppom_in_category && ! is_admin() ){
            if( $ppom_in_category ){
                
        		/**
        		 * checking product against categories
        		 * @since 6.4
        		 */
        		 
        		$ppom_overrides = apply_filters('ppom_meta_overrides', 'default');
        		
        		switch( $ppom_overrides ) {
        		    
        		    case 'category_override':
        		        $ppom_product_id = $ppom_in_category;
        		    break;
        		    case 'individual_override':
        		        // Check for a categorical assignment in the event an individual assignment does not exist for this product in the wp_options table
                         if ($ppom_product_id === null) {
                              $ppom_product_id = $ppom_in_category;
                         }
                    break;
        		    default:
        		        if(is_array($ppom_product_id) && !in_array($ppom_in_category, $ppom_product_id) ){
        		            
                    		$ppom_priority = apply_filters('ppom_meta_priority', 'category_first');

                    		if( $ppom_priority == 'category_first' ) {
                    		    
                		        $ppom_product_id = array_merge($ppom_in_category, $ppom_product_id);
                    		} else {
                    		    $ppom_product_id = array_merge($ppom_product_id, $ppom_in_category);
                    		}
        		            
                		}elseif( ! $ppom_product_id ) { // If no meta groups attached to products
                		
                		    $ppom_product_id = $ppom_in_category;
                		}
                	break;
        		        
        		}
        		
        	}
            
        	return apply_filters('ppom_product_meta_id', $ppom_product_id, $product_id);
        }
        
        // Properties functions
        function is_exists() {
         
            if( $this->meta_id == 0 || $this->meta_id == 'None' ) {
        		$this->meta_id = null;
        	}
        	
            
            return $this->meta_id == null ? false : true;
        }
        
        
        // since 15.0 multiple meta can be set against single product
        // so we have to set one active one meta for compatiblility isues
        function single_meta_id() {
            
            $single_meta = $this->meta_id;
            
            if( $this->has_multiple_meta() ) {
    		    $single_meta = $this->meta_id[0];
    		}
    		
    		if( $this->meta_id == 0 || $this->meta_id == 'None' ) 
    		    $single_meta = null;
    		    
    		return $single_meta;
        }
        
        function has_multiple_meta() {
            
            $multiple_meta = false;
            if( is_array($this->meta_id) ) {
                $multiple_meta = true;
            }
            
            return $multiple_meta;
        }
        
        // getting settings
        function settings() {
            
           $meta_id = $this->single_meta_id();
           
           if( !$meta_id || $meta_id == "None" ) return null;
    			
    		global $wpdb;
    		$qry = "SELECT * FROM " . $wpdb->prefix . PPOM_TABLE_META . " WHERE productmeta_id = {$meta_id}";
    		$meta_settings = $wpdb->get_row ( $qry );
    		
    		$meta_settings = empty($meta_settings) ? null : $meta_settings;
    		
    		return apply_filters('ppom_meta_settings', $meta_settings, $this);
        }
        
        // getting fields
        function get_fields() {
            
            if( ! $this->is_exists() )
    			return null;
    			
    		// Meta created without any fields
            if( ! $this->ppom_settings ) return null;
            
            $meta_fields = array();
            global $wpdb;
            if( $this->has_multiple_meta() ) {
                
                foreach( $this->meta_id as $meta_id ) {
                    
        		    $qry = "SELECT the_meta FROM " . $wpdb->prefix . PPOM_TABLE_META . " WHERE productmeta_id = {$meta_id}";
        		    $fields = $wpdb->get_var ( $qry );
                    $fields = json_decode ( $fields, true );
        		    
        		    if( is_array($fields) ) {
        		        $meta_fields = array_merge($meta_fields, $fields);
        		    }
                }
            } else {
                $meta_id = $this->meta_id;
                $qry = "SELECT the_meta FROM " . $wpdb->prefix . PPOM_TABLE_META . " WHERE productmeta_id = {$meta_id}";
    		    $fields = $wpdb->get_var ( $qry );
                $meta_fields = json_decode ( $fields, true );
            }
            
            // Filter fields which are active only
            $meta_fields = array_filter($meta_fields, function($field){
                return !isset($field['status']) || $field['status'] == 'on';
            });
            
            // ppom_pa($meta_fields);
            
            return apply_filters('ppom_meta_fields', $meta_fields, $this);
        }
        
        // Getting fields by meta id
        function get_fields_by_id( $ppom_id ) {
            
            $meta_fields = array();
            global $wpdb;
            
            $ppom_ids = explode(",", $ppom_id);
            foreach( $ppom_ids as $meta_id ) {
                    
    		    $qry = "SELECT the_meta FROM " . $wpdb->prefix . PPOM_TABLE_META . " WHERE productmeta_id = {$meta_id}";
    		    $fields = $wpdb->get_var ( $qry );
                $fields = json_decode ( $fields, true );
                if( is_array($fields) )
    		        $meta_fields = array_merge($meta_fields, $fields);
            }
            
            // Filter fields which are active only
            $meta_fields = array_filter($meta_fields, function($field){
                return !isset($field['status']) || $field['status'] == 'on';
            });
            
            $meta_fields = array_filter($meta_fields, function($field){
                return !isset($field['status']) || $field['status'] == 'on';
            });
            
            // if( empty($meta_fields) ) return null;
            
            return apply_filters('ppom_meta_fields_by_id', $meta_fields, $ppom_ids, $this);
        }
        
        function ppom_has_category_meta( $product_id ) {
		
        	$p_categories = get_the_terms($product_id, 'product_cat');
        	
        // 	ppom_pa($p_categories);
        // 	ppom_pa($this->ppom_with_cat);
        	
        	$meta_found = array();
        	if($p_categories){
        	 	
        	 	if( $this->ppom_with_cat ) {
            		foreach($this->ppom_with_cat as $meta_cats){
            			
            // 			if( $meta_found )	//if we found any meta so dont need to loop again
            // 				continue;
            			
            			if( $meta_cats->productmeta_categories == 'All' ) {
            				$meta_found[] = $meta_cats->productmeta_id;
            			}else{
            				//making array of meta cats
            				$meta_cat_array = explode("\r\n", $meta_cats->productmeta_categories);
            				//Now iterating the p_categories to check it's slug in meta cats
            				foreach($p_categories as $cat) {
            					if( in_array($cat->slug, $meta_cat_array) ) {
            					    $meta_found[] = $meta_cats->productmeta_id;
            					}
            				}
            			}
            
            		}
        	 	}
        	 }
        	 
            $this->category_meta = $meta_found;
        	return $meta_found;
        }
        
        function all_ppom_with_categories() {
            
            global $wpdb;
    		$ppom_table = $wpdb->prefix . PPOM_TABLE_META;
    		
    		$qry = "SELECT productmeta_id,  productmeta_categories FROM {$ppom_table}";
    		$qry .= " WHERE productmeta_categories != ''";
    		$meta_with_cats = $wpdb->get_results ( $qry );
    		return $meta_with_cats;
        }
        
        // check meta settings: ajax validation
        function ajax_validation_enabled() {
            
            $validation_enabled = false;
            
            if( ! $this->is_exists() )
    			return null;
    			
    		// Meta created without any fields
            if( ! $this->ppom_settings ) return null;
            
            return apply_filters('ppom_ajax_validation_enabled', $validation_enabled, $this);
        }
        
        // check meta settings: styels
        function inline_css() {
            
            $inline_css = '';
            
            if( ! $this->is_exists() )
    			return null;
    			
    		// Meta created without any fields
            if( ! $this->ppom_settings ) return null;
            
            if( $this->ppom_settings->productmeta_style != '' ) {
                $inline_css = stripslashes(strip_tags( $this->ppom_settings->productmeta_style ));
            }
    			
            return apply_filters('ppom_inline_css', $inline_css, $this);
        }
        
        // check meta settings: styels
        function inline_js() {
            
            $inline_js = '';
            
            if( ! $this->is_exists() )
    			return null;
    			
    		// Meta created without any fields
            if( ! $this->ppom_settings ) return null;
            
            if( isset($this->ppom_settings->productmeta_js) && $this->ppom_settings->productmeta_js != '' ) {
                $inline_js = stripslashes(strip_tags( $this->ppom_settings->productmeta_js ));
            }
    			
            return apply_filters('ppom_inline_js', $inline_js, $this);
        }
        
        // check meta settings: styels
        function price_display() {
            
            $price_display = '';
            
            if( ! $this->is_exists() )
    			return null;
    			
    		// Meta created without any fields
            if( ! $this->ppom_settings ) return null;
            
            $price_display = $this->ppom_settings->dynamic_price_display;
    			
            return apply_filters('ppom_price_display', $price_display, $this);
        }
        
        // check meta settings: styels
        function meta_title() {
            
            $meta_title = '';
            
            if( ! $this->is_exists() )
    			return null;
    			
    		// Meta created without any fields
            if( ! $this->ppom_settings ) return null;
            
            $meta_title = stripslashes($this->ppom_settings->productmeta_name);
    			
            return apply_filters('ppom_meta_title', $meta_title, $this);
        }
        
        // Since 15.1: checking if all meta has unique datanames
        function has_unique_datanames() {
            
            if( ! $this->fields ) return false;
            
            $has_unique = true;
            $datanames_array = array();
            
            // ppom_pa($this->fields);
            
            foreach( $this->fields as $field ) {
                
                $type = isset($field['type']) ? $field['type'] : '';
                
                // pricematrix does not have dataname
                if( $type == 'pricematrix' ) continue;
                // ignore collapased fields
                if( $type == 'collapse' ) continue;
                
                if( !isset($field['data_name']) ) {
                    $has_unique = false;
                    break;
                }
                
                if( in_array($field['data_name'], $datanames_array) ) {
                    
                    $has_unique = false;
                    break;
                }
                
                $datanames_array[] = $field['data_name'];
                
            }
            
            // ppom_pa($datanames_array);
            return apply_filters('ppom_has_unique_fields', $has_unique, $this);
        }
        
        /* ============== Get settings by metaid  ================= */
        function get_settings_by_id( $meta_id ) {
            
            global $wpdb;
            
    		$qry = "SELECT * FROM " . $wpdb->prefix . PPOM_TABLE_META . " WHERE productmeta_id = {$meta_id}";
    		$meta_settings = $wpdb->get_row ( $qry );
    		
    		$meta_settings = empty($meta_settings) ? null : $meta_settings;
    		
    		return apply_filters('ppom_get_settings_by_id', $meta_settings, $meta_id, $this);
        }
        
}