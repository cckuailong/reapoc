<div class='ewd-upcp-minimal-product'>

	<a class='ewd-upcp-product-details-link' href='<?php echo esc_attr( $this->details_link ); ?>' <?php echo $this->get_product_link_target(); ?>>

		<div class='ewd-upcp-minimal-product-image'>
			<?php echo $this->product->get_image(); ?>
		</div>

		<div class='ewd-upcp-minimal-product-title'>
			<?php echo esc_html( $this->product->name ); ?>
		</div>

		<div class='ewd-upcp-minimal-product-price'>
			<?php echo esc_attr( $this->product->get_display_price() ); ?>
		</div>

	</a>

</div>