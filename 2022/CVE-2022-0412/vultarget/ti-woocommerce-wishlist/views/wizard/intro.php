<?php
/**
 * The Template for displaying wizard intro step.
 *
 * @since             1.0.0
 * @package           TInvWishlist\Wizard\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="tinwl-inner tinwl-intro">
	<h2 class="tinvwl-sub-title"><?php esc_html_e( 'Setup Wizard', 'ti-woocommerce-wishlist' ); ?></h2>
	<h1 class="tinvwl-title"><?php esc_html_e( 'Welcome!', 'ti-woocommerce-wishlist' ); ?></h1>
	<div class="tinvwl-thumb">
		<i class="wizard_setup"></i>
	</div>
	<div class="tinvwl-desc"><?php
		esc_html_e( 'Thank you for choosing the Wishlist plugin by TemplateInvaders to enhance your WooCommerce store!', 'ti-woocommerce-wishlist' );
		esc_html_e( 'This quick setup wizard will help you configure the basic settings.', 'ti-woocommerce-wishlist' );
		?></div>
	<?php echo TInvWL_Form::_button_submit( 'continue', '<i class="ftinvwl ftinvwl-magic"></i>' . __( 'let’s go', 'ti-woocommerce-wishlist' ), array( 'class' => 'tinvwl-btn red w-icon xl-icon round' ) ); // WPCS: xss ok. ?>
	<div class="tinv-wishlist-clear"></div>
	<a class="tinvwl-skip"
	   href="<?php echo esc_url( admin_url() ); ?>"><?php esc_html_e( 'Not Right Now', 'ti-woocommerce-wishlist' ); ?></a>
</div>
