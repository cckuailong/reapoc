<?php
/**
 * The Template for displaying admin premium features this plugin.
 *
 * @since             1.0.0
 * @package           TInvWishlist\Admin\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<section class="tinvwl-panel w-shadow w-bg">
	<div class="container-fluid">
		<div class="row">
			<div style="text-align: center; padding:10px 25px;">
				<a href="https://templateinvaders.com/product/ti-woocommerce-wishlist-wordpress-plugin/?utm_source=<?php echo TINVWL_UTM_SOURCE; // WPCS: xss ok. ?>&utm_campaign=<?php echo TINVWL_UTM_CAMPAIGN; // WPCS: xss ok. ?>&utm_medium=<?php echo TINVWL_UTM_MEDIUM; // WPCS: xss ok. ?>&utm_content=upgrade&partner=<?php echo TINVWL_UTM_SOURCE; // WPCS: xss ok. ?>"><img
							src="<?php echo esc_attr( TINVWL_URL . 'assets/img/upgrade_to_pro.jpg' ); ?>"></a>
			</div>
		</div>
	</div>
</section>
