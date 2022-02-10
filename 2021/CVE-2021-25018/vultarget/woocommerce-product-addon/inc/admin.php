<?php
/**
 * admin related functions/hooks
 * 
 * @since 10.0
 **/
 
if( ! defined('ABSPATH') ) die('Not Allowed.');
 
 // adding column in product list
function ppom_admin_show_product_meta( $columns ){
    
    unset($columns['date']);
    unset($columns['product_tag']);
    $columns['ppom_meta'] = __( 'PPOM', "ppom");
    $columns['date'] = __( 'Date');
    return $columns;
    
    return array_merge( $columns, 
              array('ppom_meta' => __( 'PPOM', "ppom") )
              );
}

function ppom_admin_product_meta_column( $column, $post_id ) {
    
    switch ( $column ) {

      case 'ppom_meta' :
          
        	$product_meta = '';
        	$ppom		= new PPOM_Meta( $post_id );
			
            $ppom_settings_url = admin_url( 'admin.php?page=ppom');
            
            if( $ppom->has_multiple_meta() ) {
            	foreach($ppom->meta_id as $meta_id) {
            		$ppom_setting = $ppom->get_settings_by_id($meta_id);
            		if( $ppom_setting ) {
	            		$meta_title		= stripslashes($ppom_setting->productmeta_name);
	                	$url_edit = add_query_arg(array('productmeta_id'=> $ppom_setting->productmeta_id, 'do_meta'=>'edit'), $ppom_settings_url);
	                	echo sprintf(__('<a href="%s">%s</a>', "ppom"), $url_edit, $meta_title);
	                	echo ', ';
            		}else{
                		echo sprintf(__('<a class="btn button" href="%s">%s</a>', "ppom"), $ppom_settings_url, "Add Fields");
            		}
            	}
            } else if ( $ppom->ppom_settings ){
                $url_edit = add_query_arg(array('productmeta_id'=> $ppom->meta_id, 'do_meta'=>'edit'), $ppom_settings_url);
                echo sprintf(__('<a href="%s">%s</a>', "ppom"), $url_edit, $ppom->meta_title);
            }else{
                echo sprintf(__('<a class="btn button" href="%s">%s</a>', "ppom"), $ppom_settings_url, "Add Fields");
            }
            
            break;

    }
}

function ppom_admin_product_meta_metabox() {
	
	add_meta_box ( 'ppom-select-meta', __ ( 'Select PPOM Meta', 'ppom' ), 'ppom_meta_list', 'product', 'side', 'default' );
}

function ppom_meta_list( $post ) {
    
  	$ppom		= new PPOM_Meta( $post->ID );
	$all_meta	= PPOM() -> get_product_meta_all ();
	$ppom_setting = admin_url('admin.php?page=ppom');
	
	$html = '<div class="options_group">';
	$html .= '<p>'.__('Select Meta to Show Fields on this product', 'ppom');
	// $html .= __(' Or <a target="_blank" class="button" href="'.esc_url($ppom_setting).'">Create New Meta</a>', 'ppom');
	$html .= '</p>';

	$html .= '<p>';
	$html .= '<select name="ppom_product_meta" id="ppom_product_meta" class="select">';
	$html .= '<option selected="selected"> ' . __('None', "ppom"). '</option>';
	
	foreach ( $all_meta as $meta ) {
			
		$html .= '<option value="'.esc_attr($meta->productmeta_id) . '" ';
		$html .= selected($ppom->single_meta_id, $meta->productmeta_id, false);
		$html .= 'id="select_meta_group-' . $meta->productmeta_id . '">';
		$html .= stripslashes($meta->productmeta_name);
		$html .= '</option>';
	}
	$html .= '</select>';
	
	if( $ppom->single_meta_id != 'None' ) {
		
		$ppom_add_args = array('productmeta_id'	=> $ppom->single_meta_id, 
								'do_meta'		=>'edit',
								'product_id'	=> $post->ID);
								
		$url_edit = add_query_arg($ppom_add_args, $ppom_setting);
		$html .= ' <a class="button" href="'.esc_url($url_edit).'" title="Edit"><span class="dashicons dashicons-edit"></span></a>';
	}
	
	// $html .= '<hr>';
	// $html .= ' <a class="button button-primary" href="'.esc_url($ppom_setting).'">Create New Meta</a>';
	
	$html .= '</p>';
	$html .= '</div>';
	
	$ppom_add_args = array('action'=>'new','product_id'	=> $post->ID);
	$ppom_setting_url = add_query_arg($ppom_add_args, $ppom_setting);
	
	$video_url = 'https://najeebmedia.com/ppom/#howtovideo';
	$html .= sprintf(__('<p><a href="%s" target="_blank">How to use?</a>', "ppom"), $video_url);
	$html .= sprintf(__(' - <a href="%s" target="_blank">Create New Meta</a></p>', "ppom"), $ppom_setting_url);
	
	echo apply_filters('ppom_select_meta_in_product', $html, $ppom, $all_meta);
	
	echo '<div class="ppom_extra_options_panel">';
	do_action('ppom_meta_box_after_list', $post);
	echo '</div>';
}

/*
 * saving meta data against product
 */
function ppom_admin_process_product_meta( $post_id ) {
	
     
    $ppom_meta_selected = isset($_POST ['ppom_product_meta']) ? $_POST ['ppom_product_meta'] : '';
    
    // sanitization
    if( is_array($ppom_meta_selected) ) {
    	$ppom_meta_selected = array_map('intval', $ppom_meta_selected);
    }else{
    	$ppom_meta_selected = intval($ppom_meta_selected);
    }
    
    // ppom_pa($ppom_meta_selected); exit; 
    update_post_meta ( $post_id, '_product_meta_id', $ppom_meta_selected );
    
    do_action('ppom_proccess_meta', $post_id);
}

// Show notices
function ppom_admin_show_notices() {
    
    if ( $resp_notices = get_transient( "ppom_meta_imported" ) ) {
		?>
		<div id="message" class="<?php echo est_attr($resp_notices['class']); ?> updated notice is-dismissible">
			<p><?php echo wc_kses_notice($resp_notices['message']); ?></p>
			<button type="button" class="notice-dismiss">
				<span class="screen-reader-text"><?php _e( 'Dismiss this notice', 'ppom' ); ?></span>
			</button>
		</div>
	<?php
	
	    delete_transient("ppom_meta_imported");
	}
}

function ppom_admin_pro_version_notice() {
		
	$ppom_pro = 'https://najeebmedia.com/wordpress-plugin/woocommerce-personalized-product-option/';
	echo '<p class="center"><a href="'.esc_url($ppom_pro).'" class="btn btn-primary">Get PRO - Unlock All 20 Fields</a></p>';
	
	// Get PRO discount
	$ppom_buy = 'https://www.2checkout.com/checkout/purchase?sid=1686663&quantity=1&product_id=15';
	echo '<p>COUPON Code: PPOM25-2018</p>';
	echo '<p><a href="'.esc_url($ppom_buy).'" class="btn btn-primary">Get 25% Discoun</a></p>';
}

function ppom_admin_rate_and_get() {
		
	if( !ppom_pro_is_installed() ) return '';
	
	
	$ppom_pro = 'https://najeebmedia.com/get-quote/';
	echo '<p class="center"><a href="'.esc_url($ppom_pro).'" class="btn btn-primary">Get One Addon Free - Contact</a></p>';
}


/*
 * saving form meta in admin call
 */
function ppom_admin_save_form_meta() {
	
	$db_version = floatval(get_option('personalizedproduct_db_version'));
	
	if( $db_version < 22.1 ) {
		$resp = array (
				'message' => __ ( 'Since version 22.0, Database has some changes. Please Deactivate & then activate the PPOM plugin.', 'ppom' ),
				'status' => 'error',
		);
		
		wp_send_json($resp);
	}
	
	// print_r($_REQUEST); exit;
	
	if(!ppom_security_role()){
		_e ("Sorry, you are not allowed to perform this action", 'ppom');
		die(0);
	}
	
	global $wpdb;
	
	extract ( $_REQUEST );
	
	$send_file_attachment 	= "NA";
	$aviary_api_key			= "NA";
	$show_cart_thumb		= "NA";
	$enable_ajax_validation		= "";
	
	$ppom_meta = isset($_REQUEST['ppom_meta']) ? $_REQUEST['ppom_meta'] : $_REQUEST['ppom'];
	$product_meta = apply_filters('ppom_meta_data_saving', $ppom_meta, $productmeta_id);
	$product_meta = ppom_sanitize_array_data( $product_meta );
	$product_meta = json_encode($product_meta);
	
	if( strlen($productmeta_name) > 50 ) {
		$resp = array (
				'message' => __ ( 'PPOM title is too long to save, please make it less than 50 characters.', 'ppom' ),
				'status' => 'error',
		);
		
		wp_send_json($resp);
	}

	$dt = apply_filters('ppom_settings_meta_data_new', array (
			'productmeta_name'          => $productmeta_name,
			'productmeta_validation'	=> $enable_ajax_validation,
            'dynamic_price_display'     => $dynamic_price_hide,
            'send_file_attachment'		=> $send_file_attachment,
            'show_cart_thumb'			=> $show_cart_thumb,
			'aviary_api_key'            => trim ( $aviary_api_key ),
			'productmeta_style'         => $productmeta_style,
			'productmeta_js'            => $productmeta_js,
			'productmeta_categories'    => $productmeta_categories,
			'the_meta'                  => $product_meta,
			'productmeta_created'       => current_time ( 'mysql' )
	));
	
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
	
	
	$ppom_id = $wpdb->insert_id;

	$product_meta = apply_filters('ppom_meta_data_saving', $ppom, $ppom_id);
	// Updating PPOM Meta with ppom_id in each meta array
	ppom_admin_update_ppom_meta_only( $ppom_id, $product_meta );
	
	$redirect_to = '';
	
	if( $ppom_id ) {
		$ppom_args = array('page'=>'ppom','productmeta_id'=>$ppom_id,'do_meta'=>'edit');
		$redirect_to = add_query_arg($ppom_args, admin_url('admin.php'));
	}
	
	
	if( isset($_REQUEST['product_id']) && $_REQUEST['product_id'] != '') {
		ppom_attach_fields_to_product($ppom_id, intval($_REQUEST['product_id']));
		$redirect_to = get_permalink($product_id);
	}
	
	$resp = array ();
	if ($ppom_id) {
		
		$resp = array (
				'message'		=> __ ( 'Form added successfully', 'ppom' ),
				'status'		=> 'success',
				'productmeta_id'=> $ppom_id,
				'redirect_to'	=> $redirect_to,
		);
	} else {
		
		$resp = array (
				'message' => __ ( 'No changes found.', 'ppom' ),
				'status' => 'success',
				'productmeta_id' => ''
		);
	}
	
	wp_send_json($resp);
}
	
/*
 * updating form meta in admin call
 */
function ppom_admin_update_form_meta() {
	
	
	$return_page = isset($_REQUEST['ppom_meta']) ? 'ppom-energy' : 'ppom';
	extract ( $_REQUEST );

	$ppom_args = array('page'=>$return_page,'productmeta_id'=>$productmeta_id,'do_meta'=>'edit');
	$redirect_to = add_query_arg($ppom_args, admin_url('admin.php'));
	
	$db_version = floatval(get_option('personalizedproduct_db_version'));
	
	if( $db_version < 22.1 ) {
		$resp = array (
				'message' => __ ( 'Since version 22.0, Database has some changes. Please Deactivate & then activate the PPOM plugin.', 'ppom' ),
				'status' => 'error',
				'productmeta_id' => $productmeta_id,
				'redirect_to'	=> $redirect_to,
		);
		
		wp_send_json($resp);
	}
	
	// print_r($_REQUEST); exit;
	

	if(!ppom_security_role()){
		_e ("Sorry, you are not allowed to perform this action", 'ppom');
		die(0);
	}
	
	
	global $wpdb;
	
	$ppom_meta = isset($_REQUEST['ppom_meta']) ? $_REQUEST['ppom_meta'] : $_REQUEST['ppom'];
	$product_meta = apply_filters('ppom_meta_data_saving', $ppom_meta, $productmeta_id);
	$product_meta = ppom_sanitize_array_data( $product_meta );
	$product_meta = json_encode($product_meta);
	// ppom_pa($product_meta); exit;
	
	$productmeta_name = isset($_REQUEST['productmeta_name']) ? sanitize_text_field($_REQUEST['productmeta_name']) : '';
	$productmeta_validation = isset($_REQUEST['enable_ajax_validation']) ? sanitize_text_field($_REQUEST['enable_ajax_validation']) : '';
	$dynamic_price_hide = isset($_REQUEST['dynamic_price_hide']) ? sanitize_text_field($_REQUEST['dynamic_price_hide']) : '';
	$send_file_attachment = isset($_REQUEST['send_file_attachment']) ? sanitize_text_field($_REQUEST['send_file_attachment']) : '';
	$show_cart_thumb = isset($_REQUEST['show_cart_thumb']) ? sanitize_text_field($_REQUEST['show_cart_thumb']) : '';
	$aviary_api_key = isset($_REQUEST['aviary_api_key']) ? sanitize_text_field($_REQUEST['aviary_api_key']) : '';
	$productmeta_style = isset($_REQUEST['productmeta_style']) ? sanitize_text_field($_REQUEST['productmeta_style']) : '';
	$productmeta_js = isset($_REQUEST['productmeta_js']) ? sanitize_text_field($_REQUEST['productmeta_js']) : '';
	$productmeta_categories = isset($_REQUEST['productmeta_categories']) ? $_REQUEST['productmeta_categories'] : '';
	
	if( strlen($productmeta_name) > 50 ) {
		$resp = array (
				'message' => __ ( 'PPOM title is too long to save, please make it less than 50 characters.', 'ppom' ),
				'status' => 'error',
		);
		
		wp_send_json($resp);
	}
	
	
	$dt = $dt = apply_filters('ppom_settings_meta_data_update', array (
			'productmeta_name'          => $productmeta_name,
			'productmeta_validation'    => $productmeta_validation,
            'dynamic_price_display'     => $dynamic_price_hide,
            'send_file_attachment'		=> $send_file_attachment,
            'show_cart_thumb'			=> $show_cart_thumb,
			'aviary_api_key'            => trim ( $aviary_api_key ),
			'productmeta_style'         => $productmeta_style,
			'productmeta_js'            => $productmeta_js,
			'productmeta_categories'    => $productmeta_categories,
			'the_meta'                  => $product_meta,
	), $productmeta_id);
	
	// ppom_pa($dt); exit;
	
	$where = array (
			'productmeta_id' => $productmeta_id 
	);
	
	$format = array (
			'%s',
			'%s',
            '%s',
            '%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
	);
	$where_format = array (
			'%d' 
	);
	
	global $wpdb;
	$ppom_table = $wpdb->prefix.PPOM_TABLE_META;
	$rows_effected = $wpdb->update($ppom_table, $dt, $where, $format, $where_format);
	
	// $wpdb->show_errors(); $wpdb->print_error();
	
	$return_page = isset($_REQUEST['ppom_meta']) ? 'ppom-energy' : 'ppom';

	$ppom_args = array('page'=>$return_page,'productmeta_id'=>$productmeta_id,'do_meta'=>'edit');
	$redirect_to = add_query_arg($ppom_args, admin_url('admin.php'));
	
	$resp = array ();
	if ($rows_effected) {
		
		$resp = array (
				'message' => __ ( 'Form updated successfully', 'ppom' ),
				'status' => 'success',
				'productmeta_id' => $productmeta_id,
				'redirect_to'	=> $redirect_to, 
		);
	} else {
		
		$resp = array (
				'message' => __ ( 'Form updated successfully.', 'ppom' ),
				'status' => 'success',
				'productmeta_id' => $productmeta_id,
				'redirect_to'	=> $redirect_to,
		);
	}
	
	wp_send_json($resp);
}

// Update PPOM Fields Only
function ppom_admin_update_ppom_meta_only($ppom_id, $ppom_meta) {
	
	// print_r($_REQUEST); exit;
	global $wpdb;

	$dt = array (
			'the_meta'	=> json_encode ( $ppom_meta )
	);
	
	// ppom_pa($dt); exit;
	
	$where = array (
			'productmeta_id' => $ppom_id 
	);
	
	$format = array (
			'%s',
	);
	$where_format = array (
			'%d' 
	);
	
	global $wpdb;
	$ppom_table = $wpdb->prefix.PPOM_TABLE_META;
	$rows_effected = $wpdb->update($ppom_table, $dt, $where, $format, $where_format);
	
	// $wpdb->show_errors(); $wpdb->print_error();
	
	if ($rows_effected) {
		
		return true;
	} else {
		return false;
	}
	
}

/*
 * delete meta
 */
function ppom_admin_delete_meta() {
	
	if(!ppom_security_role()){
		_e ("Sorry, you are not allowed to perform this action", 'ppom');
		die(0);
	}
	
	global $wpdb;
	
	extract ( $_REQUEST );
	
	$tbl_name	= $wpdb->prefix . PPOM_TABLE_META;
	$ppom_id	= intval($productmeta_id);
	$res = $wpdb->query ( $wpdb->prepare("DELETE FROM {$tbl_name} WHERE productmeta_id = %d", $productmeta_id ));

	
	if ($res) {
		
		_e ( 'Meta deleted successfully', 'ppom' );
	} else {
		$wpdb->show_errors ();
		$wpdb->print_error ();
	}
	
	die ( 0 );
}

/*
 * delete meta
 */
function ppom_admin_delete_selected_meta() {
	
	if(!ppom_security_role()){
		_e ("Sorry, you are not allowed to perform this action", 'ppom');
		die(0);
	}
	
	global $wpdb;
	
	extract( $_REQUEST );
	$productmeta_ids = implode(', ', $productmeta_ids);
	$tbl_name	= $wpdb->prefix . PPOM_TABLE_META;
	
	$res = $wpdb->query ( $wpdb->prepare("DELETE FROM {$tbl_name} WHERE productmeta_id IN ({$productmeta_ids})", $productmeta_ids ));
	
	if ($res) {
		
		_e ( 'Meta deleted successfully', 'ppom' );
	} else {
		$wpdb->show_errors ();
		$wpdb->print_error ();
	}
	
	die ( 0 );
}


/*
 * simplifying meta for admin view in existing-meta.php
 */
function ppom_admin_simplify_meta($meta) {
	//echo $meta;
	$metas = json_decode ( $meta );
	
	$html = '';
	if ($metas) {
		$html .= '<ul>';
		foreach ( $metas as $meta => $data ) {
			
			//ppom_pa($data);
			$req = (isset( $data -> required ) && $data -> required == 'on') ? 'yes' : 'no';
			$title = (isset( $data -> title )  ? $data -> title : '');
			$type = (isset( $data -> type )  ? $data -> type : '');
			$options = (isset( $data -> options )  ? $data -> options : '');
			
			$html .= '<li>';
			$html .= '<strong>label:</strong> ' . esc_html($title);
			$html .= ' | <strong>type:</strong> ' . esc_html($type);
			
			if (! is_object ( $options) && is_array ( $options )){
				$html .= ' | <strong>options:</strong> ';
				foreach($options as $option){
					
					$display_info = '';
					if( isset($option->option) ) {
						$display_info = $option->option;
					} elseif(isset($option->width)) {
						$display_info = $option->width.'x'.$option->height;
					}
					
					if( empty($option->price) ) { 
						$html .= esc_html($display_info) .', ';
					} else{
						$html .= esc_html($display_info) . ' (' .$option -> price .'), ';
					}
				}
			}
			
				
			$html .= ' | <strong>required:</strong> ' . esc_html($req);
			$html .= '</li>';
		}
		
		$html .= '</ul>';
	}
	
	return $html;
}

// Showing PPOM Edit on Product Page
function ppom_admin_bar_menu() {

	if( ! is_product() ) return;
	
	global $wp_admin_bar, $product;
	
	$product_id = ppom_get_product_id( $product ); 
	$ppom		= new PPOM_Meta( $product_id );
	
	if( ! $ppom->is_exists ) return;

	$ppom_setting_url = admin_url( 'admin.php');
	$ppom_setting_url = add_query_arg(array('page'=>'ppom',
									'productmeta_id'=>$ppom->single_meta_id,
									'do_meta'	=> 'edit'),
									$ppom_setting_url
									);
	
	$bar_title = "Edit PPOM ({$ppom->meta_title})";
	$wp_admin_bar->add_node( array(
		'id'     => 'ppom-setting-bar',
		'title'  => sprintf(__( "%s", "ppom"), $bar_title ),
		'href'  => $ppom_setting_url,
	) );
	
	$all_meta	= PPOM() -> get_product_meta_all ();
	foreach ( $all_meta as $meta ) {
			
			$apply_link = admin_url('admin-post.php');
			$apply_arg	= array('productid'=>$product_id,
								'metaid'=>$meta->productmeta_id,
								'metatitle'=>$meta->productmeta_name,
								'action'=>'ppom_attach');
			$apply_link = add_query_arg($apply_arg, $apply_link);
			$bar_title = "Apply {$meta->productmeta_name}";
			$wp_admin_bar->add_node( array(
			'id'    	=> "ppom-setting-bar-{$meta->productmeta_id}",
			'title' 	=> sprintf(__( "%s", "ppom"), $bar_title ),
			'href'  	=> $apply_link,
			'parent'	=> 'ppom-setting-bar',
		) );
	}
}

function ppom_admin_update_pro_notice() {
	
	$ppom_url = 'https://najeebmedia.com/ppom';
	$buy_paddle = 'https://pay.paddle.com/checkout/536711';
	
	echo '<div class="ppom-more-plugins-block">';
		echo '<a class="btn btn-primary ppom-nm-plugins" href="'.esc_url($buy_paddle).'">'.__('Buy PPOM PRO').'</a>';
		echo '<a class="btn btn-yellow ppom-nm-plugins" href="'.esc_url($ppom_url).'">'.__('PPOM PRO Features & Demo').'</a>';
	echo '</div>';
}