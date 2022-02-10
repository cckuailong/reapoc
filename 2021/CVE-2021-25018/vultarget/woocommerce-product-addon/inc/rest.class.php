<?php
/**
 * PPOM Rest API
 * Basic operations
 * -- Add/update text,radio,select,checkbox,date,email
 * -- Adding field to product
 * --- Endpoint: site_url/wp-json/ppom/v1/add-field/product/{product_id}
 * --- Method: Post
 * --- Params: data_name, title, type, required
 * --- Example []
 **/
 
class PPOM_Rest {
    
    function __construct() {

        if( ppom_is_api_enable() ) {
            add_action( 'rest_api_init', array($this, 'init_api') );
		}
        
    }
    
    
    
    function init_api() {
        
        // getting ppom fields against product
        register_rest_route( 'ppom/v1', '/get/product/', array(
        'methods' => 'GET',
        'callback' => array($this, 'get_ppom_meta_info_product'),
        'permission_callback' => '__return_true',
        ));
        
        // setting meta fields about meta against product
        register_rest_route( 'ppom/v1', '/set/product/', array(
        'methods' => 'POST',
        'callback' => array($this, 'ppom_save_meta_product'),
        'permission_callback' => '__return_true',
        ));
        
        // delete meta fields about meta against product
        register_rest_route( 'ppom/v1', '/delete/product/', array(
        'methods' => 'POST',
        'callback' => array($this, 'delete_ppom_fields_product'),
        'permission_callback' => '__return_true',
        ));
        
        
        // Orders
        // getting ppom fields against product
        register_rest_route( 'ppom/v1', '/get/order/', array(
        'methods' => 'GET',
        'callback' => array($this, 'get_ppom_meta_info_order'),
        'permission_callback' => '__return_true',
        ));
        
        // setting meta fields about meta against product
        register_rest_route( 'ppom/v1', '/set/order/', array(
        'methods' => 'POST',
        'callback' => array($this, 'ppom_update_meta_order'),
        'permission_callback' => '__return_true',
        ));
        
        // delete meta fields about meta against product
        register_rest_route( 'ppom/v1', '/delete/order/', array(
        'methods' => 'POST',
        'callback' => array($this, 'delete_ppom_fields_order'),
        'permission_callback' => '__return_true',
        ));
    }
    
    
    // Getting ppom meta info
    function get_ppom_meta_info_product( WP_REST_Request $request ) {
        
        $this->set_headers();
        
        // getting request params:
        $product_id = $request->get_param( 'product_id' );
        
        $response_info = array();
        if( $product_id == '' ) {
            $response_info = array('status'=>'no_product',
                                    'message' => __('No Product Found', "ppom")
                                    );
            return new WP_REST_Response( $response_info );
        }
            
        $product_id = intval( $product_id );
	    $ppom		= new PPOM_Meta( $product_id );
        if( ! $ppom->is_exists ) {
            
            $response_info = array('status'=>'no_meta',
                                    'message' => __('No Meta Found', "ppom")
                                    );
            return new WP_REST_Response( $response_info );
        }
        
        $meta_id = $ppom->single_meta_id;
        $ppom_fields = $ppom->fields;
        
        
        $ppom_fields = $this->filter_required_keys_only($ppom_fields);
        
        $response_info = array('status'=>'success',
                            'message' => __("Meta found {$meta_id}", "ppom"),
                            'meta_id'   => intval($meta_id),
                            'product_id'=> $product_id,
                            'ppom_fields' => $ppom_fields,
                            );
        
        
        // Create the response object
        $response = new WP_REST_Response( $response_info );
        
        return $response;
    }
    
    // Save meta against product
    // Getting ppom meta info
    function ppom_save_meta_product( WP_REST_Request $request ) {
        
        $this->set_headers();
        
        // getting request params:
        $product_id = $request->get_param( 'product_id' );
        $secretkey  = $request->get_param( 'secret_key' );
        
        $all_data = $request->get_params();
        
        if( empty($all_data['fields']) ) {
            $response_info = array('status'=>'no_fields',
                                    'message' => __('No fields to save', "ppom")
                                    );
            return new WP_REST_Response( $response_info );
        }
        
        if( empty($secretkey) || ! $this->is_secret_key_valid($secretkey) ) {
            $response_info = array('status'=>'key_not_valid',
                                    'message' => __('Secret key is not valid', "ppom")
                                    );
            return new WP_REST_Response( $response_info );
        }
        
        $response_info = array();
        if( $product_id == '' ) {
            $response_info = array('status'=>'no_product',
                                    'message' => __('No Product Found', "ppom")
                                    );
            return new WP_REST_Response( $response_info );
        }
            
        
        $product_id = intval( $product_id );
	    $ppom		= new PPOM_Meta( $product_id );
        $ppom_settings = $ppom->ppom_settings;
        
        $ppom_fields = json_decode( stripslashes($all_data['fields']), true );
        
        $meta_response = array();
        if( empty($ppom_settings) ) {
            $meta_response = $this->save_new_meta_data($product_id, $ppom_fields);
        } else {
            $meta_response = $this->update_meta_data($ppom_settings, $ppom_fields, $product_id);
        }
        
        
        // ppom_pa($ppom_fields);
        
        return new WP_REST_Response( $meta_response );
        
    }
    
    
    /**
     * Delete fields against product
     * params: 
     * product_id: integer
     * secret_key: string
     * fields   : array()
     **/
    function delete_ppom_fields_product( WP_REST_Request $request ) {
        
        $this->set_headers();
        
        // getting request params:
        $product_id = $request->get_param( 'product_id' );
        $secretkey  = $request->get_param( 'secret_key' );
        
        $all_data = $request->get_params();
        
        if( empty($all_data['fields']) ) {
            $response_info = array('status'=>'no_fields',
                                    'message' => __('No fields to save', "ppom")
                                    );
            return new WP_REST_Response( $response_info );
        }
        
        if( empty($secretkey) || ! $this->is_secret_key_valid($secretkey) ) {
            $response_info = array('status'=>'key_not_valid',
                                    'message' => __('Secret key is not valid', "ppom")
                                    );
            return new WP_REST_Response( $response_info );
        }
        
        $response_info = array();
        if( $product_id == '' ) {
            $response_info = array('status'=>'no_product',
                                    'message' => __('No Product Found', "ppom")
                                    );
            return new WP_REST_Response( $response_info );
        }
            
        $product_id = intval( $product_id );
	    $ppom		= new PPOM_Meta( $product_id );
        $ppom_settings = $ppom->ppom_settings;
        
        $delete_fields = json_decode( stripslashes($all_data['fields']) );
        
        $meta_response = array();
        $meta_response = $this -> delete_meta_data( $ppom_settings, $delete_fields, $product_id );
        
        // ppom_pa($ppom_fields);
        
        return new WP_REST_Response( $meta_response );
        
    }
    
    
    
    // Check if secret key is set and matched
    function is_secret_key_valid( $secretkey ) {
        
        $api_key    = ppom_get_option( 'ppom_rest_secret_key', true );
        
        $key_valide = false;
        
        if( trim($api_key == $secretkey) )
            $key_valide = true;
            
        return $key_valide;
    }
    
    // build new meta entry
    function save_new_meta_data( $product_id, $ppom_fields ) {
        
        $product = new WC_Product( $product_id );
        
        $productmeta_name       = $product->get_title();
        $productmeta_validation = 'no';
        $dynamic_price_hide     = 'no';
        $send_file_attachment   = '';
        $show_cart_thumb        = 'no';
        $aviary_api_key         = '';
        $productmeta_style      = '';
        $productmeta_categories = '';
        
        $dt = array (
    			'productmeta_name'          => $productmeta_name,
    			'productmeta_validation'	=> $productmeta_validation,
                'dynamic_price_display'     => $dynamic_price_hide,
                'send_file_attachment'		=> $send_file_attachment,
                'show_cart_thumb'			=> $show_cart_thumb,
    			'aviary_api_key'            => trim ( $aviary_api_key ),
    			'productmeta_style'         => $productmeta_style,
    			'productmeta_categories'    => $productmeta_categories,
    			'the_meta'                  => json_encode ( $ppom_fields ),
    			'productmeta_created'       => current_time ( 'mysql' )
    	);
    	
    	
    	$format = array (
    			'%s',
    			'%s',
    			'%s',
                '%s',
    			'%s',
    			'%s',
    			'%s' 
    	);
    	
    	global $wpdb;
    	$ppom_table = $wpdb->prefix.PPOM_TABLE_META;
    	$wpdb->insert($ppom_table, $dt, $format);
    	$res_id = $wpdb->insert_id;
    	
    	$ppom_fields = apply_filters('ppom_meta_data_saving', $ppom_fields, $res_id);
    	// Updating PPOM Meta with ppom_id in each meta array
	    ppom_admin_update_ppom_meta_only( $res_id, $ppom_fields );
    	
    	$resp = array ();
    	if ($res_id) {
    		
    		$resp = array (
    				'status'    => 'success',
    				'meta_id'   => $res_id,
    				'product_id'=> $product_id,
    				'fields'    => $ppom_fields,
    		);
    		
    		// Also setting ppom meta to porduct
    		update_post_meta( $product_id, '_product_meta_id', $res_id );
    	} else {
    		
    		$resp = array (
    				'message' => __ ( 'No changes found.', 'ppom' ),
    				'status' => 'error',
    				'meta_id'   => '',
    				'product_id'=> $product_id,
    		);
    	}
    	
    	return $resp;
    }
    
    function update_meta_data( $ppom_meta, $ppom_fields, $product_id ) {
        
        $existing_fields = json_decode($ppom_meta->the_meta, true);
        // var_dump($ppom_meta); exit;
        
        $saved_fields = array();
        $merger_array = array();
        
        // First saving new fields
        foreach($ppom_fields as $new_field) {
            
            $merger_array[] = $new_field;
            $saved_fields[] = $new_field['data_name'];
        }
        
        // Now checking old fields
        foreach($existing_fields as $old_field){
            
            if( ! in_array($old_field['data_name'], $saved_fields) ) {
                
                $merger_array[] = $old_field;
            }
        }
        
        $merger_array = apply_filters('ppom_meta_data_saving', $merger_array, $ppom_meta->productmeta_id);
        
        $data = array('the_meta' => json_encode($merger_array) );
        $where = array (
    			'productmeta_id' => $ppom_meta->productmeta_id 
    	);
    	
        $format = array('%s');
        $where_format = array("%d");
        
        global $wpdb;
    	$ppom_table = $wpdb->prefix.PPOM_TABLE_META;
    	$rows_effected = $wpdb->update($ppom_table, $data, $where, $format, $where_format);
    	
    	$resp = array (
    				'status'    => 'success',
    				'meta_id'   => $ppom_meta->productmeta_id,
    				'product_id'=> $product_id,
    				'fields'    => $merger_array,
    		);
        
        return $resp;
    }
    
    function delete_meta_data( $ppom_meta, $delete_fields, $product_id) {
        
        $existing_fields = json_decode($ppom_meta->the_meta);
        
        global $wpdb;
        $merger_array = array();
        
        // Check if all feilds request exist
        if( in_array('__all_keys', $delete_fields) ) {
            
            // unset product meta key
            delete_post_meta( $product_id, '_product_meta_id' );
            
            // Deleting all fields
            $ppom_table = $wpdb->prefix . PPOM_TABLE_META;
            $res = $wpdb->query ( "DELETE FROM {$ppom_table} WHERE productmeta_id = " . $ppom_meta->productmeta_id );
            $delete_fields_resp = array('ppom_id'=>$ppom_meta->productmeta_id);
            
            $resp = array (
    				'status'    => 'success',
    				'meta_id'   => $ppom_meta->productmeta_id,
    				'product_id'=> $product_id,
    				'fields'    => '',
    		);
    		
            return $resp;
        }
        
        
        // Only adding those fields which are not deleted
        foreach($existing_fields as $field) {
            
            if( ! isset($field->data_name) ) continue;
            
            if( ! in_array($field->data_name, $delete_fields) ) {
                
                $merger_array[] = $field;
            }
        }
        
        
        $data = array('the_meta' => json_encode($merger_array) );
        $where = array (
    			'productmeta_id' => $ppom_meta->productmeta_id 
    	);
    	
        $format = array('%s');
        $where_format = array("%d");
        
    	$ppom_table = $wpdb->prefix.PPOM_TABLE_META;
    	$rows_effected = $wpdb->update($ppom_table, $data, $where, $format, $where_format);
    	
    	$resp = array (
    				'status'    => 'success',
    				'meta_id'   => $ppom_meta->productmeta_id,
    				'product_id'=> $product_id,
    				'fields'    => $merger_array,
    		);
        
        return $resp;
    }
    
    /**
     * ====================================================================
     * ========================== ORDERS ==================================
     * ====================================================================
     * */
     
     // Getting ppom meta info
    function get_ppom_meta_info_order( WP_REST_Request $request ) {
        
        $this->set_headers();
        
        // getting request params:
        $order_id = $request->get_param( 'order_id' );
        
        $order = wc_get_order( $order_id );
        // return new WP_REST_Response( ['items'=>$order] );
        
        $response_info = array();
        if( !$order) {
            $response_info = array('status'=>'no_order',
                                    'message' => __('No Order Found', "ppom")
                                    );
            return new WP_REST_Response( $response_info );
        }
            
        
        $item_product_meta = $this->get_order_item_meta($order_id);
        
        $response_info = array('status'=>'success',
                            'message' => __("Order found {$order_id}", "ppom"),
                            'order_items_meta' => $item_product_meta,
                            );
        
        
        // Create the response object
        $response = new WP_REST_Response( $response_info );
        
        return $response;
    }
    
    // update meta against order
    function ppom_update_meta_order( WP_REST_Request $request ) {
        
        $this->set_headers();
        
        // getting request params:
        $order_id   = $request->get_param( 'order_id' );
        $secretkey  = $request->get_param( 'secret_key' );
        
        $all_data = $request->get_params();
        

        
        $order = wc_get_order( $order_id );
        
        $response_info = array();
        if( !$order) {
            $response_info = array('status'=>'no_order',
                                    'message' => __('No Order Found', "ppom")
                                    );
            return new WP_REST_Response( $response_info );
        }
        
        if( empty($all_data['fields']) ) {
            $response_info = array( 'status'=>'no_fields',
                                    'message' => __('No meta found to save', "ppom")
                                    );
            return new WP_REST_Response( $response_info );
        }
        
        if( empty($secretkey) || ! $this->is_secret_key_valid($secretkey) ) {
            $response_info = array('status'=>'key_not_valid',
                                    'message' => __('Secret key is not valid', "ppom")
                                    );
            return new WP_REST_Response( $response_info );
        }
        
            
        $item_product_meta = array();
        $order_item_meta = json_decode( stripslashes($all_data['fields']) );
        
        // return new WP_REST_Response( $order_item_meta );
                
        if( empty($order_item_meta) ) {
            $response_info = array('status'=>'fields_not_valid',
                                    'message' => __('Submitted fields are in valid format.', "ppom")
                                    );
            return new WP_REST_Response( $response_info );
        }
        
        
        
        foreach( $order->get_items() as $item_id => $item_product ){
            
            // Get the special meta data in an array:
            $product_id     = $item_product->get_product_id();
            
            foreach($item_product->get_meta_data() as $item_meta_data) {
                
                foreach($order_item_meta as $order_product_id => $item_meta) {
                    
                    // check if product id exists in requested fields
                    $order_product_id = intval($order_product_id);
                    
                    if( $order_product_id == $product_id ) {
                        
                        foreach($item_meta as $meta_key => $meta_val) {
                            
                            
                            $scalar_value = $meta_val;
                            
                            if( is_array($meta_val) ) {
                                $scalar_value = json_encode($meta_val);
                            }
                            
                            $meta_update_res = wc_update_order_item_meta($item_id, $meta_key, $scalar_value);
                        } 
                    }
                }
            }
        }
        
        
        $item_product_meta = $this->get_order_item_meta( $order_id );
        
        $response_info = array('status'=>'success',
                            'message' => __("Order updated {$order_id}", "ppom"),
                            'order_items_meta' => $item_product_meta,
                            );
        
        return new WP_REST_Response( $response_info );
    }
    
    /**
     * Delete fields against order
     * params: 
     * order_id: integer
     * secret_key: string
     * fields   : array()
     **/
    function delete_ppom_fields_order( WP_REST_Request $request ) {
        
        $this->set_headers();
        
        // getting request params:
        $order_id = $request->get_param( 'order_id' );
        $secretkey  = $request->get_param( 'secret_key' );
        
        $all_data = $request->get_params();
        
        $order = wc_get_order( $order_id );
        
        $response_info = array();
        if( !$order) {
            $response_info = array('status'=>'no_order',
                                    'message' => __('No Order Found', "ppom")
                                    );
            return new WP_REST_Response( $response_info );
        }
        
        if( empty($all_data['fields']) ) {
            $response_info = array('status'=>'no_fields',
                                    'message' => __('No fields to delete', "ppom")
                                    );
            return new WP_REST_Response( $response_info );
        }
        
        if( empty($secretkey) || ! $this->is_secret_key_valid($secretkey) ) {
            $response_info = array('status'=>'key_not_valid',
                                    'message' => __('Secret key is not valid', "ppom")
                                    );
            return new WP_REST_Response( $response_info );
        }
        
        $item_product_meta = array();
        $order_item_meta = json_decode( stripslashes($all_data['fields']) );
        
        foreach( $order->get_items() as $item_id => $item_product ){
            
            // Get the special meta data in an array:
            $product_id     = $item_product->get_product_id();
            
            foreach($item_product->get_meta_data() as $item_meta_data) {
                
            
                foreach($order_item_meta as $order_product_id => $delete_meta) {
                    
                    // check if product id exists in requested fields
                    if( $order_product_id == $product_id ) {
                    
                        foreach($delete_meta as $meta_key ) {
                            
                            wc_delete_order_item_meta($item_id, $meta_key);
                        }
                    }
                }
            }
        }
        
        
        $item_product_meta = $this->get_order_item_meta( $order_id );
        
        $response_info = array('status'=>'success',
                            'message' => __("Order updated {$order_id}", "ppom"),
                            'order_items_meta' => $item_product_meta,
                            );
        
        return new WP_REST_Response( $response_info );
        
    }
    
    
    // Return all order items' meta
    function get_order_item_meta( $order_id ) {
        
        $order = wc_get_order( $order_id );
        
        $order_item_meta_data = array();
        
        foreach( $order->get_items() as $item_id => $item_product ){
            
            // Get the special meta data in an array:
            $product_id     = $item_product->get_product_id();
            $ppom_meta_data = $item_product->get_meta('_ppom_fields');
            $context = 'api';
            $ppom_meta_ids		= null;
	        $ppom_meta			= ppom_generate_cart_meta($ppom_meta_data['fields'], $product_id, $ppom_meta_ids, $context);
	        
            // getting checkbox/radio/select price detail
            $meta_info = array();
            foreach($item_product->get_meta_data() as $meta_data) {
                
                $formatted_data = array();
                $fields_info = ppom_get_field_meta_by_dataname($product_id, $meta_data->key);
                if( ! $fields_info ) continue;
                
                $formatted_data['id'] = $meta_data->id;
                $formatted_data['key'] = $meta_data->key;
                $formatted_data['value'] = $meta_data->value;
                
                if( isset($ppom_meta[$meta_data->key]) ) {
                    $formatted_data['display'] = $ppom_meta[$meta_data->key]['display'];
                    $formatted_data['value'] = $ppom_meta[$meta_data->key]['value'];
                }
                
                $meta_info[] = $formatted_data;
            }
            
            
            $order_item_meta_data[] = array('product_id' => $product_id,
                                        'product_meta_data' => $meta_info,
                                        );
        }
        
        return $order_item_meta_data;
    }
    
    
    function filter_required_keys_only( $ppom_fields ) {
        
        $required_keys = array('title','type','data_name','description','required');
        
        $new_ppom_fields = array();
        if( $ppom_fields ) {
            
            foreach( $ppom_fields as $field ) {
                
                $title  = isset($field['title']) ? $field['title'] : '';
                $type   = isset($field['type']) ? $field['type'] : '';
                $data_name = isset($field['data_name']) ? $field['data_name'] : '';
                $description = isset($field['description']) ? $field['description'] : '';
                $required = isset($field['required']) ? $field['required'] : '';
                
                if($type == 'imageselect' || $type == 'image') {
                    $options = isset($field['images']) ? $field['images'] : '';
                }else{
                    $options = isset($field['options']) ? $field['options'] : '';
                }
                
                $new_ppom_fields[] = array('title'      => $title,
                                            'type'      => $type,
                                            'data_name' => $data_name,
                                            'description'=> $description,
                                            'required'  => $required,
                                            'options'   => $options);
            }
        }
        
        return $new_ppom_fields;
    }
    
    // settings headers
     public function set_headers() {
     	
     	if (isset($_SERVER['HTTP_ORIGIN'])) {
	        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
	        header('Access-Control-Allow-Credentials: true');
	        header('Access-Control-Max-Age: 86400');    // cache for 1 day
	    }
	
	    // Access-Control headers are received during OPTIONS requests
	    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
	
	        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
	            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         
	
	        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
	            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
	
	        exit(0);
	    }
	    
     }
}

new PPOM_Rest;