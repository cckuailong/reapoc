<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function wpedon_plugin_buttons() {

	if (!isset($_GET['action']) || $_GET['action'] == "delete" || !empty($_GET['action2']) == "delete") {
		
		// create a table
		
		if(!class_exists('WP_List_Table')) {
			require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
		}
		
		class wpedon_buttons_table extends WP_List_Table {
			
			
			function get_data() {
				global $wp_query;
				
				$args = array('post_type' => 'wpplugin_don_button','posts_per_page' => -1);
				
				$posts = get_posts($args);
				
				$count = "0";
				foreach ($posts as $post) {
					
					$id = $posts[$count]->ID;
					$post_title = $posts[$count]->post_title;
					
					if ($post_title == "" || $post_title == " " || empty($post_title)) {
						$post_title = "(No Item Name)";
					}
					
					$shortcode = 	'<input type="text" value="[wpedon id='.$id.']">';
					$price = 		esc_attr(get_post_meta($id,'wpedon_button_price',true));
					$sku = 			esc_attr(get_post_meta($id,'wpedon_button_id',true));
					
					if (empty($price)) {
						$price = "Customer enters amount";
					}
					
					
					$product = $post_title;
					
					$data[] = array(
						'ID' 		=> $id,
						'product' 	=> $product,
						'shortcode' => $shortcode,
						'price' 	=> $price,
						'sku'		=> $sku
					);
					
					$count++;
				}
				
				if (empty($data)) {
					$data = array();
				}
				
				return $data;
			}
				
				
				
			function __construct() {
				global $status, $page;
				
				parent::__construct( array(
					'singular'  => 'product',
					'plural'    => 'products',
					'ajax'      => false
				) );
			}
				
				
			 function column_default($item, $column_name){
					switch($column_name){
						case 'product':
						case 'shortcode':
						case 'price':
						case 'sku':
							return $item[$column_name];
						default:
							return print_r($item,true);
					}
			}

			function column_product($item){
			
				// edit
				$edit_bare = '?page=wpedon_buttons&action=edit&product='.$item['ID'];
				$edit_url = wp_nonce_url($edit_bare, 'edit_'.$item['ID']);
				
				// delete
				$delete_bare = '?page=wpedon_buttons&action=delete&inline=true&product='.$item['ID'];
				$delete_url = wp_nonce_url($delete_bare, 'bulk-'.$this->_args['plural']);
				
				$actions = array(
					'edit'      => '<a href="' . esc_url($edit_url) . '">Edit</a>',
					'delete'    => '<a href="' . esc_url($delete_url) . '">Delete</a>'
				);
				
				return sprintf('%1$s %2$s',
					esc_attr($item['product']),
					$this->row_actions($actions)
				);
			}
			
			
			
			function column_cb($item) {
				return sprintf(
					'<input type="checkbox" name="%1$s[]" value="%2$s" />',
					esc_attr($this->_args['singular']),
					esc_attr($item['ID'])
				);
			}
			
			
			function get_columns() {
				$columns = array(
					'cb'			=> '<input type="checkbox" />',
					'product'     	=> 'Name',
					'shortcode'     => 'Shortcode',
					'price'    		=> 'Amount',
					'sku'			=> 'ID'
				);
				return $columns;
			}
			
			
			function no_items() {
				_e( 'No buttons found.' );
			}
			
			
			function get_bulk_actions() {
					$actions = array(
						'delete'    => 'Delete'
					);
					return $actions;
			}
			
			public function process_bulk_action() {
				if ( isset( $_GET['_wpnonce'] ) && ! empty( $_GET['_wpnonce'] ) ) {
					$nonce  = $_GET['_wpnonce'];
					$action = 'bulk-' . $this->_args['plural'];
					
					if ( ! wp_verify_nonce( $nonce, $action ) ) {
						wp_die('Security check fail'); 
					}
				}
			}
			
			
			function prepare_items() {
				global $wpdb;
				
				$per_page = 5;
				
				$columns = $this->get_columns();
				$hidden = array();
				$sortable = $this->get_sortable_columns();
				
				$this->_column_headers = array($columns, $hidden, $sortable);
				
				$this->process_bulk_action();
				
				$data = $this->get_data();

				function usort_reorder($a,$b) {
					$orderby = (!empty($_REQUEST['orderby'])) ? sanitize_text_field($_REQUEST['orderby']) : 'product';
					$order = (!empty($_REQUEST['order'])) ? sanitize_text_field($_REQUEST['order']) : 'asc';
					$result = strcmp($a[$orderby], $b[$orderby]);
					return ($order==='asc') ? $result : -$result;
				}
				usort($data, 'usort_reorder');
				

				$current_page = $this->get_pagenum();
				
				

				$total_items = count($data);

				$data = array_slice($data,(($current_page-1)*$per_page),$per_page);

				$this->items = $data;

				$this->set_pagination_args( array(
					'total_items' => $total_items,
					'per_page'    => $per_page,
					'total_pages' => ceil($total_items/$per_page)
				) );
				
			}
		}
	

		function wpedon_tt_render_list_page() {

			$testListTable = new wpedon_buttons_table();
			$testListTable->prepare_items();

			?>
			
			<style>
			.check-column {
				width: 2% !important;
			}
			.column-product {
				width: 25%;
			}
			.column-shortcode {
				width: 35%;
			}
			.column-price {
				width: 25%;
			}
			.column-sku {
				width: 13%;
			}
			</style>
			
			<div style="width:98%">
			
				<table width="100%"><tr><td>
				<br />
				<span style="font-size:20pt;">PayPal Donation Buttons</span>
				</td><td valign="bottom">
				<a href="?page=wpedon_buttons&action=new" name='btn2' class='button-primary' style='font-size: 14px;height: 30px;float: right;'>New PayPal Donation Button</a>
				</td></tr></table>
				
				<?php
                if (isset($_GET['message'])) {
                    switch ($_GET['message']) {
                        case 'created':
                            echo "<div class='updated'><p>Button created.</p></div>";
                            break;
                        case 'deleted':
                            echo "<div class='updated'><p>Button(s) deleted.</p></div>";
                            break;
                        case 'nothing':
                            echo "<div class='updated'><p>No action selected.</p></div>";
                            break;
                        case 'nothing_deleted':
                            echo "<div class='updated'><p>Nothing selected to delete.</p></div>";
                            break;
                        case 'error':
                            echo "<div class='updated'><p>An error occured while processing the query. Please try again.</p></div>";
                    }
                }
				?>
				
				<form id="products-filter" method="get">
					<input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']); ?>" />
					<?php $testListTable->display() ?>
				</form>
			
			</div>

			<?php
		}
		
		wpedon_tt_render_list_page();
	}
	
	
	// admin products page new product
	if (isset($_GET['action']) && $_GET['action'] == "new") {
		include_once ('private_buttons_new.php');
	}
	// end admin products page new product
	
	// admin products page edit product
	if (isset($_GET['action']) && $_GET['action'] == "edit") {
		$post_id = $_GET['product'];
		check_admin_referer('edit_'.$post_id);
		include_once ('private_buttons_edit.php');
	}
	// end admin products page edit product
	
	// admin products page delete product
	if (isset($_GET['action']) && $_GET['action'] == "delete" || isset($_GET['action2']) && $_GET['action2'] == "delete") {	

			// check nonce for security
			$nonce = $_REQUEST['_wpnonce'];
			$action = 'bulk-products';
			
			if ( ! wp_verify_nonce( $nonce, $action ) ) {
				wp_die('Security check fail'); 
			}
		
		if (!empty($_GET['inline']) == "true") {
			$post_id = array(intval($_GET['product']));
		} else {
            $post_id = array_map('intval', $_GET['product']);
		}
		
		if (empty($post_id)) {
			echo'<script>window.location="?page=wpedon_buttons&message=nothing_deleted"; </script>';
			exit;
		}
		
		foreach ($post_id as $to_delete) {
			$to_delete = intval($to_delete);
			
			if (get_post_type($to_delete) != 'wpplugin_don_button') {
				$to_delete = false;
			}

			if (!$to_delete) {
				echo'<script>window.location="?page=wpedon_buttons&message=error"; </script>';
				exit;
			}
			
			wp_delete_post($to_delete,1);
			delete_post_meta($to_delete,'wpedon_button_price');
			delete_post_meta($to_delete,'wpedon_button_id');
			delete_post_meta($to_delete,'wpedon_button_enable_name');
			delete_post_meta($to_delete,'wpedon_button_enable_price');
			delete_post_meta($to_delete,'wpedon_button_enable_currency');
			delete_post_meta($to_delete,'wpedon_button_currency');
			delete_post_meta($to_delete,'wpedon_button_language');
			delete_post_meta($to_delete,'wpedon_button_account');
			delete_post_meta($to_delete,'wpedon_button_return');
			delete_post_meta($to_delete,'wpedon_button_buttonsize');
			delete_post_meta($to_delete,'wpedon_button_scpriceprice');
			delete_post_meta($to_delete,'wpedon_button_scpriceaname');
			delete_post_meta($to_delete,'wpedon_button_scpricebname');
			delete_post_meta($to_delete,'wpedon_button_scpricecname');
			delete_post_meta($to_delete,'wpedon_button_scpricedname');
			delete_post_meta($to_delete,'wpedon_button_scpriceename');
			delete_post_meta($to_delete,'wpedon_button_scpricefname');
			delete_post_meta($to_delete,'wpedon_button_scpricegname');
			delete_post_meta($to_delete,'wpedon_button_scpricehname');
			delete_post_meta($to_delete,'wpedon_button_scpriceiname');
			delete_post_meta($to_delete,'wpedon_button_scpricejname');
			delete_post_meta($to_delete,'wpedon_button_scpricea');
			delete_post_meta($to_delete,'wpedon_button_scpriceb');
			delete_post_meta($to_delete,'wpedon_button_scpricec');
			delete_post_meta($to_delete,'wpedon_button_scpriced');
			delete_post_meta($to_delete,'wpedon_button_scpricee');
			delete_post_meta($to_delete,'wpedon_button_scpricef');
			delete_post_meta($to_delete,'wpedon_button_scpriceg');
			delete_post_meta($to_delete,'wpedon_button_scpriceh');
			delete_post_meta($to_delete,'wpedon_button_scpricei');
			delete_post_meta($to_delete,'wpedon_button_scpricej');
			
		}
		
		echo'<script>window.location="?page=wpedon_buttons&message=deleted"; </script>';
		exit;
		
	}
	// end admin products page delete product
	
	// admin orders page no action taken
	if (isset($_GET['action']) && $_GET['action'] == "-1") {
		echo'<script>window.location="?page=wpedon_buttons&message=nothing"; </script>';
	}
	// end admin orders page no action taken

}