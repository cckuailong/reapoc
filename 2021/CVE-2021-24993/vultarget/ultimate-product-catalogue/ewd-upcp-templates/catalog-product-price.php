<div class='ewd-upcp-catalog-product-price' data-item_price='<?php echo esc_attr( $this->product->current_price ); ?>'>

	<span>
		<?php echo esc_attr( $this->product->get_display_price( 'regular' ) ); ?>
	</span>

	<?php $this->maybe_print_sale_price(); ?>

</div>