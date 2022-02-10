<div id='ewd-upcp-single-product-<?php echo $this->product->id; ?>' class='ewd-upcp-custom-mobile-product-page ewd-upcp-product-page ewd-upcp-hidden'>

	<?php foreach ( $this->get_custom_product_page_elements() as $page_element ) { ?>

		<?php $this->print_custom_product_page_element( $page_element ); ?>

	<?php } ?>

</div>