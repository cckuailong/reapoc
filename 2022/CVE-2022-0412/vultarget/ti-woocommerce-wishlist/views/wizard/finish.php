<?php
/**
 * The Template for displaying wizard finish step.
 *
 * @version             1.6.1
 * @package           TInvWishlist\Wizard\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="tinwl-inner tinwl-finish">
	<h2 class="tinvwl-sub-title"><?php esc_html_e( 'Congratulations', 'ti-woocommerce-wishlist' ); ?></h2>
	<h1 class="tinvwl-title"><?php esc_html_e( 'Your Wishlist is ready!', 'ti-woocommerce-wishlist' ); ?></h1>
	<div class="tinvwl-desc">
		<?php printf( esc_html__( 'You have set basic Wishlist settings. If you want to make more in-depth plugin setup you can make it in plugin settings page %s.', 'ti-woocommerce-wishlist' ), sprintf( '<a target="_blank" href="%s">%s</a>', esc_url( self::admin_url( '' ) ), esc_html__( 'WooCommerce Wishlist Plugin Settings', 'ti-woocommerce-wishlist' ) ) ); // WPCS: xss ok. ?>
		<br/>
		<?php printf( esc_html__( 'Details about WooCommerce Wishlist Plugin options can be found in our %s.', 'ti-woocommerce-wishlist' ), sprintf( '<a target="_blank" href="%s">%s</a>', 'https://templateinvaders.com/documentation/ti-woocommerce-wishlist-free/?utm_source=' . TINVWL_UTM_SOURCE . '&utm_campaign=' . TINVWL_UTM_CAMPAIGN . '&utm_medium=' . TINVWL_UTM_MEDIUM . '&utm_content=wizard_documentation&partner=' . TINVWL_UTM_SOURCE, esc_html__( 'Online Documentation', 'ti-woocommerce-wishlist' ) ) ); // WPCS: xss ok.
		?>
	</div>
	<a class="tinvwl-btn grey w-icon xl-icon round"
	   href="<?php echo 'https://templateinvaders.com/documentation/ti-woocommerce-wishlist-free/?utm_source=' . TINVWL_UTM_SOURCE . '&utm_campaign=' . TINVWL_UTM_CAMPAIGN . '&utm_medium=' . TINVWL_UTM_MEDIUM . '&utm_content=wizard_documentation&partner=' . TINVWL_UTM_SOURCE; // WPCS: xss ok.
	   ?>"><i class="ftinvwl ftinvwl-graduation-cap"></i><?php esc_html_e( 'Documentation', 'ti-woocommerce-wishlist' ); ?>
	</a>
	<a class="tinvwl-btn grey w-icon xl-icon round" href="<?php echo esc_url( self::admin_url( '' ) ); ?>"><i
				class="ftinvwl ftinvwl-wrench"></i><?php esc_html_e( 'Wishlist Settings', 'ti-woocommerce-wishlist' ); ?>
	</a>
	<div class="tinv-wishlist-clear" style="padding: 20px 0"></div>
	<a class="tinvwl-btn red w-icon smaller-txt"
	   href="https://templateinvaders.com/?utm_source=<?php echo TINVWL_UTM_SOURCE; // WPCS: xss ok. ?>&utm_campaign=<?php echo TINVWL_UTM_CAMPAIGN; // WPCS: xss ok. ?>&utm_medium=<?php echo TINVWL_UTM_MEDIUM; // WPCS: xss ok. ?>&utm_content=wizard_upgrade&partner=<?php echo TINVWL_UTM_SOURCE; // WPCS: xss ok. ?>"><i
				class="ftinvwl ftinvwl-star"></i><?php esc_html_e( 'Upgrade to Premium', 'ti-woocommerce-wishlist' ); ?>
	</a>
</div>
