<?php
/**
 * Deprecated filters plugin class
 *
 * @since             1.13.0
 * @package           TInvWishlist
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Deprecated filters plugin class
 */
class TInvWL_Deprecated_Filters extends TInvWL_Deprecated {

	/**
	 * Array of deprecated hooks we need to handle.
	 * Format of 'new' => 'old'.
	 *
	 * @var array
	 */
	protected $deprecated_hooks = array(
		'tinvwl_load_frontend'                       => 'tinvwl-load_frontend',
		'tinvwl_default_wishlist_title'              => 'tinvwl-general-default_title',
		'tinvwl_removed_from_wishlist_text'          => 'tinvwl-general-text_removed_from',
		'tinvwl_added_to_wishlist_text'              => 'tinvwl-general-text_added_to',
		'tinvwl_added_to_wishlist_text_loop'         => 'tinvwl-add_to_wishlist_catalog-text',
		'tinvwl_view_wishlist_text'                  => 'tinvwl-general-text_browse',
		'tinvwl_already_in_wishlist_text'            => 'tinvwl-general-text_already_in',
		'tinvwl_allow_add_parent_variable_product'   => 'tinvwl-allow_parent_variable',
		'tinvwl_wishlist_products_counter_text'      => 'tinvwl-topline-text',
		'tinvwl_add_selected_to_cart_text'           => 'tinvwl-table-text_add_select_to_cart',
		'tinvwl_add_to_cart_text'                    => 'tinvwl-product_table-text_add_to_cart',
		'tinvwl_share_on_text'                       => 'tinvwl-social-share_on',
		'tinvwl_wishlist_products_counter_menu_html' => 'tinvwl-menu-item-title',
		'tinvwl_wc_cart_fragments_enabled'           => 'tinvwl-wc-cart-fragments',
		'tinvwl_add_all_to_cart_text'                => 'tinvwl-table-text_add_all_to_cart',
		'tinvwl_remove_from_wishlist_text_loop'      => 'tinvwl-add_to_wishlist_catalog-text_remove',
		'tinvwl_wishlist_get_item_data'              => 'tinv_wishlist_get_item_data',
	);

	/**
	 * Array of versions on each hook has been deprecated.
	 *
	 * @var array
	 */
	protected $deprecated_version = array(
		'tinvwl-load_frontend'                       => '1.13.0',
		'tinvwl-general-default_title'               => '1.13.0',
		'tinvwl-general-text_removed_from'           => '1.13.0',
		'tinvwl-general-text_added_to'               => '1.13.0',
		'tinvwl-add_to_wishlist_catalog-text'        => '1.13.0',
		'tinvwl-general-text_browse'                 => '1.13.0',
		'tinvwl-general-text_already_in'             => '1.13.0',
		'tinvwl-allow_parent_variable'               => '1.13.0',
		'tinvwl-topline-text'                        => '1.13.0',
		'tinvwl-table-text_add_select_to_cart'       => '1.13.0',
		'tinvwl-product_table-text_add_to_cart'      => '1.13.0',
		'tinvwl-social-share_on'                     => '1.13.0',
		'tinvwl-menu-item-title'                     => '1.13.0',
		'tinvwl-wc-cart-fragments'                   => '1.13.0',
		'tinvwl-table-text_add_all_to_cart'          => '1.13.0',
		'tinvwl-add_to_wishlist_catalog-text_remove' => '1.13.0',
		'tinv_wishlist_get_item_data'                => '1.13.0',
	);

	/**
	 * Hook into the new hook so we can handle deprecated hooks once fired.
	 *
	 * @param string $hook_name Hook name.
	 */
	public function hook_in( $hook_name ) {
		add_filter( $hook_name, array( $this, 'maybe_handle_deprecated_hook' ), - 1000, 8 );
	}

	/**
	 * If the old hook is in-use, trigger it.
	 *
	 * @param string $new_hook New hook name.
	 * @param string $old_hook Old hook name.
	 * @param array $new_callback_args New callback args.
	 * @param mixed $return_value Returned value.
	 *
	 * @return mixed
	 */
	public function handle_deprecated_hook( $new_hook, $old_hook, $new_callback_args, $return_value ) {
		if ( has_filter( $old_hook ) ) {
			$this->display_notice( $old_hook, $new_hook );
			$return_value = $this->trigger_hook( $old_hook, $new_callback_args );
		}

		return $return_value;
	}

	/**
	 * Fire off a legacy hook with it's args.
	 *
	 * @param string $old_hook Old hook name.
	 * @param array $new_callback_args New callback args.
	 *
	 * @return mixed
	 */
	protected function trigger_hook( $old_hook, $new_callback_args ) {
		return apply_filters_ref_array( $old_hook, $new_callback_args );
	}
}
