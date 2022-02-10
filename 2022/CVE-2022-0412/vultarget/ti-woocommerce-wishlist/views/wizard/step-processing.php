<?php
/**
 * The Template for displaying wizard processing step.
 *
 * @since             1.0.0
 * @package           TInvWishlist\Wizard\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="tinwl-inner">
	<div class="tinvwl-title-wrap">
		<h1><?php esc_html_e( 'Processing Options', 'ti-woocommerce-wishlist' ); ?></h1>
		<div class="tinvwl-separator"></div>
	</div>

	<div class="tinvwl-desc"><?php esc_html_e( 'Following option allows you to set products to be automatically removed from a wishlist when added to cart.', 'ti-woocommerce-wishlist' ); ?></div>

	<div class="form-horizontal">
		<div class="form-group">
			<?php echo TInvWL_Form::_label( 'processing_autoremove', __( 'How products should be removed from Wishlist?', 'ti-woocommerce-wishlist' ), array( 'class' => 'col-md-6 control-label' ) ); // WPCS: xss ok. ?>
			<div class="col-md-6">
				<?php echo TInvWL_Form::_select( 'processing_autoremove', $processing_autoremove_value, array( 'class'      => 'form-control',
																											   'tiwl-show'  => '.tinvwl-processing-autoremove-status',
																											   'tiwl-value' => 'auto'
				), $processing_autoremove_options ); // WPCS: xss ok. ?>
			</div>
		</div>
	</div>

	<div class="tinvwl-separator"></div>

	<div class="tinvwl-nav tinv-wishlist-clearfix">
		<div class="tinvwl-next">
			<a class="tinvwl-skip"
			   href="<?php echo esc_url( add_query_arg( 'step', absint( filter_input( INPUT_GET, 'step' ) ) + 1, set_url_scheme( ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ) ) ); // @codingStandardsIgnoreLine WordPress.VIP.SuperGlobalInputUsage.AccessDetected ?>"><?php esc_html_e( 'Skip this step', 'ti-woocommerce-wishlist' ); ?></a>
			<?php echo TInvWL_Form::_button_submit( 'nextstep', __( 'continue', 'ti-woocommerce-wishlist' ), array( 'class' => 'tinvwl-btn red w-icon round' ) ); // WPCS: xss ok. ?>
		</div>
	</div>
</div>
