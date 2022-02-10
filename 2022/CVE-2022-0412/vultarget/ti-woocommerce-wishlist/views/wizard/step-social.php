<?php
/**
 * The Template for displaying wizard social step.
 *
 * @since             1.0.0
 * @package           TInvWishlist\Wizard\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="tinwl-inner">
	<div class="row">
		<div>
			<div class="tinvwl-title-wrap">
				<h1><?php esc_html_e( 'Share', 'ti-woocommerce-wishlist' ); ?></h1>
				<div class="tinvwl-separator"></div>
			</div>

			<div class="tinvwl-desc"><?php esc_html_e( 'Allow people to share wishlists by adding social share buttons to Wishlist page.', 'ti-woocommerce-wishlist' ); ?></div>

			<div class="form-horizontal">
				<div class="form-group">
					<?php echo TInvWL_Form::_label( 'social_facebook', __( 'Show "Facebook" Button', 'ti-woocommerce-wishlist' ), array( 'class' => 'col-md-6 control-label' ) ); // WPCS: xss ok. ?>
					<div class="col-md-6">
						<?php echo TInvWL_Form::_checkboxonoff( 'social_facebook', $social_facebook_value ); // WPCS: xss ok. ?>
					</div>
				</div>
			</div>

			<div class="form-horizontal">
				<div class="form-group">
					<?php echo TInvWL_Form::_label( 'social_twitter', __( 'Show "Twitter" Button', 'ti-woocommerce-wishlist' ), array( 'class' => 'col-md-6 control-label' ) ); // WPCS: xss ok. ?>
					<div class="col-md-6">
						<?php echo TInvWL_Form::_checkboxonoff( 'social_twitter', $social_twitter_value ); // WPCS: xss ok. ?>
					</div>
				</div>
			</div>

			<div class="form-horizontal">
				<div class="form-group">
					<?php echo TInvWL_Form::_label( 'social_pinterest', __( 'Show "Pinterest" Button', 'ti-woocommerce-wishlist' ), array( 'class' => 'col-md-6 control-label' ) ); // WPCS: xss ok. ?>
					<div class="col-md-6">
						<?php echo TInvWL_Form::_checkboxonoff( 'social_pinterest', $social_pinterest_value ); // WPCS: xss ok. ?>
					</div>
				</div>
			</div>

			<div class="form-horizontal">
				<div class="form-group">
					<?php echo TInvWL_Form::_label( 'social_whatsapp', __( 'Show "WhatsApp" Button', 'ti-woocommerce-wishlist' ), array( 'class' => 'col-md-6 control-label' ) ); // WPCS: xss ok. ?>
					<div class="col-md-6">
						<?php echo TInvWL_Form::_checkboxonoff( 'social_whatsapp', $social_whatsapp_value ); // WPCS: xss ok. ?>
					</div>
				</div>
			</div>

			<div class="form-horizontal">
				<div class="form-group">
					<?php echo TInvWL_Form::_label( 'social_clipboard', __( 'Show "Clipboard" Button', 'ti-woocommerce-wishlist' ), array( 'class' => 'col-md-6 control-label' ) ); // WPCS: xss ok. ?>
					<div class="col-md-6">
						<?php echo TInvWL_Form::_checkboxonoff( 'social_clipboard', $social_clipboard_value ); // WPCS: xss ok. ?>
					</div>
				</div>
			</div>

			<div class="form-horizontal">
				<div class="form-group">
					<?php echo TInvWL_Form::_label( 'social_email', __( 'Show "Share by Email" Button', 'ti-woocommerce-wishlist' ), array( 'class' => 'col-md-6 control-label' ) ); // WPCS: xss ok. ?>
					<div class="col-md-6">
						<?php echo TInvWL_Form::_checkboxonoff( 'social_email', $social_email_value ); // WPCS: xss ok. ?>
					</div>
				</div>
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
