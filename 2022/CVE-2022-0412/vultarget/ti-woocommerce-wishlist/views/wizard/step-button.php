<?php
/**
 * The Template for displaying wizard button step.
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
		<h1><?php esc_html_e( 'Button options', 'ti-woocommerce-wishlist' ); ?></h1>
		<div class="tinvwl-separator"></div>
	</div>

	<div class="tinvwl-desc">
		<?php esc_html_e( 'Choose where to place “Add to Wishlist” button on the product page: before or after “Add to Cart” button.', 'ti-woocommerce-wishlist' ); ?>
		<br/>
		<?php
		$links = array(
				sprintf( '<a target="_blank" href="%s">%s</a>', esc_url( self::admin_url( 'style-settings' ) ), __( 'TI  Wishlists > Style Options', 'ti-woocommerce-wishlist' ) ),
		);
		printf( __( 'And set button text. You can add an icon, change button appearance and other settings in %s.', 'ti-woocommerce-wishlist' ), implode( __( ' and ', 'ti-woocommerce-wishlist' ), $links ) ); // WPCS: xss ok.
		?>
	</div>

	<div class="form-horizontal">
		<div class="form-group">
			<?php echo TInvWL_Form::_label( 'add_to_wishlist_position', __( 'Button position', 'ti-woocommerce-wishlist' ), array( 'class' => 'col-md-6 control-label' ) ); // WPCS: xss ok. ?>
			<div class="col-md-6">
				<?php echo TInvWL_Form::_select( 'add_to_wishlist_position', $add_to_wishlist_position_value, array( 'class' => 'form-control' ), $add_to_wishlist_position_options ); // WPCS: xss ok. ?>
			</div>
		</div>
	</div>

	<div class="form-horizontal">
		<div class="form-group">
			<?php echo TInvWL_Form::_label( 'add_to_wishlist_text', __( '"Add to Wishlist" Text', 'ti-woocommerce-wishlist' ), array( 'class' => 'col-md-6 control-label' ) ); // WPCS: xss ok. ?>
			<div class="col-md-6">
				<?php echo TInvWL_Form::_text( 'add_to_wishlist_text', $add_to_wishlist_text_value, array( 'class' => 'form-control' ) ); // WPCS: xss ok. ?>
			</div>
		</div>
	</div>

	<div class="tinvwl-separator"></div>

	<div class="tinvwl-desc">
		<?php printf( __( 'You can also show “Add to Wishlist” button in Product listing. More options in %s.', 'ti-woocommerce-wishlist' ), $links[0] ); // WPCS: xss ok. ?>
	</div>

	<div class="form-horizontal">
		<div class="form-group">
			<?php echo TInvWL_Form::_label( 'add_to_wishlist_catalog_show_in_loop', __( 'Show in Product listing', 'ti-woocommerce-wishlist' ), array( 'class' => 'col-md-6 control-label' ) ); // WPCS: xss ok. ?>
			<div class="col-md-6">
				<?php echo TInvWL_Form::_checkboxonoff( 'add_to_wishlist_catalog_show_in_loop', $add_to_wishlist_catalog_show_in_loop_value ); // WPCS: xss ok. ?>
			</div>
		</div>
	</div>

	<div class="form-horizontal">
		<div class="form-group">
			<?php echo TInvWL_Form::_label( 'add_to_wishlist_catalog_text', __( '"Add to Wishlist" Text in Product listing', 'ti-woocommerce-wishlist' ), array( 'class' => 'col-md-6 control-label' ) ); // WPCS: xss ok. ?>
			<div class="col-md-6">
				<?php echo TInvWL_Form::_text( 'add_to_wishlist_catalog_text', $add_to_wishlist_catalog_text_value, array( 'class' => 'form-control' ) ); // WPCS: xss ok. ?>
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
