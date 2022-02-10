<div class='ewd-upcp-related-products'>
	
	<div class='ewd-upcp-related-products-title'>
		<?php echo esc_html( $this->get_label( 'label-related-products' ) ); ?>
	</div>

	<?php foreach ( $this->product->related_products as $related_product ) { ?>

		<?php echo $this->render_minimal_product( $related_product ); ?>

	<?php } ?>

</div>