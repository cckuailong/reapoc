<div class='ewd-upcp-single-product-tab <?php echo ( ! $this->is_starting_tab( 'reviews' ) ? 'ewd-upcp-hidden' : '' ); ?>' data-tab='reviews'>

	<?php echo do_shortcode( "[ultimate-reviews product_name='" . esc_attr( $this->product->name ) . "' review_filtering='&lsqb;&rsqb;']" ); ?>
	
	<div class='ewd-upcp-tab-divider'></div>

	<h2>
		<?php _e( 'Leave a review', 'ultimate-product-catalogue' ); ?>
	</h2>

	<style>.ewd-urp-form-header {display:none;}</style>

	<?php echo do_shortcode( "[submit-review product_name='" . esc_attr( $this->product->name ) . "']" ); ?>

</div>