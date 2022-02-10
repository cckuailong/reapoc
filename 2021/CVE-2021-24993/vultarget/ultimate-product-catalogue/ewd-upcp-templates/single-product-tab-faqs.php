<div class='ewd-upcp-single-product-tab <?php echo ( ! $this->is_starting_tab( 'faqs' ) ? 'ewd-upcp-hidden' : '' ); ?>' data-tab='faqs'>

	<?php $ufaq_product_category = get_term_by( 'name', $this->product->name, 'ufaq-category' ); ?>

	<?php echo do_shortcode( "[ultimate-faqs include_category='". ( ! empty( $ufaq_product_category ) ? $ufaq_product_category->slug : '' )  . ',' . $this->get_product_category_slugs() . "']" ); ?>

</div>