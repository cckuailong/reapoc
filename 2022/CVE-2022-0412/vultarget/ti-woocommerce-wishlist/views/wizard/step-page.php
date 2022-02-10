<?php
/**
 * The Template for displaying wizard page step.
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
		<h1><?php esc_html_e( 'Page Setup', 'ti-woocommerce-wishlist' ); ?></h1>
		<div class="tinvwl-desc">
			<?php esc_html_e( 'The following page needs to be applied so the “Wishlist” knows where it is. ', 'ti-woocommerce-wishlist' ); ?>
			<br/>
			<?php esc_html_e( 'Choose from existing pages or leave this field empty and the Wishlist page will be created automatically:', 'ti-woocommerce-wishlist' ); ?>
		</div>
	</div>

	<div class="tinvwl-separator"></div>

	<div class="form-horizontal">
		<div class="form-group">
			<?php echo TInvWL_Form::_label( 'general_default_title', __( 'Default Wishlist Name', 'ti-woocommerce-wishlist' ), array( 'class' => 'col-md-6 control-label' ) ); // WPCS: xss ok. ?>
			<div class="col-md-6">
				<?php echo TInvWL_Form::_text( 'general_default_title', $general_default_title_value, array(
						'required' => 'required',
						'class'    => 'form-control'
				) ); // WPCS: xss ok. ?>
			</div>
		</div>
	</div>

	<?php
	foreach (
			array(
					'wishlist' => __( 'My Wishlist', 'ti-woocommerce-wishlist' ),
			) as $key => $label
	) {
		TInvWL_View::view( 'step-page-field', array(
				'key'        => $key,
				'label'      => $label,
				'page_field' => $page_pages[ $key ],
		), 'wizard' );
	} ?>

	<div class="tinvwl-separator"></div>

	<div class="tinvwl-desc">
		<?php esc_html_e( 'Once created, this page can be managed from WordPress dashboard.', 'ti-woocommerce-wishlist' ); ?>
		<br/>
	</div>

	<div class="tinvwl-nav tinv-wishlist-clearfix">
		<div class="tinvwl-next">
			<a class="tinvwl-skip"
			   href="<?php echo esc_url( add_query_arg( 'step', absint( filter_input( INPUT_GET, 'step' ) ) + 1, set_url_scheme( ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ) ) ); // @codingStandardsIgnoreLine WordPress.VIP.SuperGlobalInputUsage.AccessDetected ?>"><?php esc_html_e( 'Skip this step', 'ti-woocommerce-wishlist' ); ?></a>
			<?php echo TInvWL_Form::_button_submit( 'nextstep', __( 'continue', 'ti-woocommerce-wishlist' ), array( 'class' => 'tinvwl-btn red w-icon round' ) ); // WPCS: xss ok. ?>
		</div>
	</div>
</div>
