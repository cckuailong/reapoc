<?php
/**
 * Deprecated actions plugin class
 *
 * @since             1.13.0
 * @package           TInvWishlist
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Deprecated actions plugin class
 */
class TInvWL_Deprecated_Actions extends TInvWL_Deprecated {

	/**
	 * Array of deprecated hooks we need to handle.
	 * Format of 'new' => 'old'.
	 *
	 * @var array
	 */
	protected $deprecated_hooks = array(
		'tinvwl_wishlist_addtowishlist_button'    => 'tinv_wishlist_addtowishlist_button',
		'tinvwl_wishlist_addtowishlist_dialogbox' => 'tinv_wishlist_addtowishlist_dialogbox',
	);

	/**
	 * Array of versions on each hook has been deprecated.
	 *
	 * @var array
	 */
	protected $deprecated_version = array(
		'tinv_wishlist_addtowishlist_button'    => '1.13.0',
		'tinv_wishlist_addtowishlist_dialogbox' => '1.13.0',
	);

	/**
	 * Hook into the new hook so we can handle deprecated hooks once fired.
	 *
	 * @param string $hook_name Hook name.
	 */
	public function hook_in( $hook_name ) {
		add_action( $hook_name, array( $this, 'maybe_handle_deprecated_hook' ), - 1000, 8 );
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
		if ( has_action( $old_hook ) ) {
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
		switch ( $old_hook ) {
			default:
				do_action_ref_array( $old_hook, $new_callback_args );
				break;
		}
	}
}
