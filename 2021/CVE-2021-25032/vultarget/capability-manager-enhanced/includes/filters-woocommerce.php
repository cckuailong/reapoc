<?php
/**
 * class CME_WooCommerce
 * 
 * Uses WordPress or Woo API to adjust WooCommerce permissions
 */
class CME_WooCommerce {
	function __construct() {
		// Implement duplicate_product capability automatically if current user has it in role.
		global $current_user;
		if ( ! empty( $current_user->allcaps['duplicate_products'] ) ) {
			add_filter( 'woocommerce_duplicate_product_capability', array( &$this, 'implement_duplicate_product_cap' ) );
		}
		
		// Ensure orders can be edited or added based the edit_orders / create_orders capability
		add_action( '_admin_menu', array( &$this, 'support_order_caps' ), 1 );
	}
	
	function implement_duplicate_product_cap( $cap ) {
		return 'duplicate_products';
	}
	
	function support_order_caps() {
		global $submenu;

		if ( $type_obj = get_post_type_object( 'shop_order' ) ) {
			$key = 'edit.php?post_type=shop_order';
			if ( ! isset( $submenu[$key] ) ) {
				$submenu[$key] = array();
			}
			
			$submenu[$key][5] = array( 0 => sprintf( __( 'All %s' ), $type_obj->labels->name ), 1 => $type_obj->cap->edit_posts, 2 => 'edit.php?post_type=shop_order' );
			$submenu[$key][10] = array( __('Add New'), 1 => $type_obj->cap->create_posts, 2 => 'post-new.php?post_type=shop_order' );
		}
	}
}