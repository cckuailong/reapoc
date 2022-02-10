<?php
/**
 * The Template for displaying not found wishlist.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/ti-wishlist-null.php.
 *
 * @version             1.25.5
 * @package           TInvWishlist\Template
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

?>
<p class="cart-empty  woocommerce-info">
	<?php esc_html_e('Wishlist is not found!', 'ti-woocommerce-wishlist'); ?>
</p>

<?php do_action('tinvwl_wishlist_is_null'); ?>

<p class="return-to-shop">
	<a class="button wc-backward"
	   href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))); ?>"><?php echo esc_html(apply_filters('woocommerce_return_to_shop_text', __('Return To Shop', 'ti-woocommerce-wishlist'))); ?></a>
</p>
