<?php
/**
 * The Template for displaying wizard field for page step.
 *
 * @since             1.0.0
 * @package           TInvWishlist\Wizard\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="form-horizontal">
	<div class="form-group">
		<?php echo TInvWL_Form::_label( sprintf( 'page_%s', $key ), $label, array( 'class' => 'col-md-6 control-label' ) ); // WPCS: xss ok. ?>
		<div class="tinvwl-page-select <?php echo $page_field['error'] ? esc_attr( 'tinvwl-error ' ) : '' ?>col-md-6">
			<?php if ( $page_field['error'] ) : ?><span class="tinvwl-error-icon"><i
						class="ftinvwl ftinvwl-exclamation-triangle"></i></span><?php endif; ?>
			<?php
			$select_extra = array(
					'class'      => 'form-control',
					'tiwl-show'  => sprintf( '.tinvwl-page-%s-new', $key ),
					'tiwl-value' => - 100,
			);
			if ( $page_field['error'] ) {
				$select_extra['required'] = 'required';
			}
			echo TInvWL_Form::_select( sprintf( 'page_%s', $key ), $page_field['value'], $select_extra, $page_field['options'] ); // WPCS: xss ok.
			echo TInvWL_Form::_text( array( // WPCS: xss ok.
					'name' => sprintf( 'page_%s_auto', $key ),
					'type' => 'hidden',
			), $page_field['error'] ? 0 : 1 );
			?>
			<?php if ( $page_field['error'] ) : ?>
				<div class="tinvwl-error-desc"><?php printf( __( 'Page with name “%s” aready exist! Please choose another page or create a new one', 'ti-woocommerce-wishlist' ), esc_html( $page_field['new_value'] ) ); // WPCS: xss ok. ?></div><?php endif; ?>
		</div>
	</div>
</div>
<div class="tinvwl-page-<?php echo esc_attr( $key ); ?>-new form-horizontal">
	<div class="form-group">
		<div class="col-md-6">&nbsp;</div>
		<div class="col-md-6">
			<?php echo TInvWL_Form::_text( sprintf( 'page_%s_new', $key ), $page_field['new_value'], array( 'class' => 'form-control' ) ); // WPCS: xss ok. ?>
		</div>
	</div>
</div>
