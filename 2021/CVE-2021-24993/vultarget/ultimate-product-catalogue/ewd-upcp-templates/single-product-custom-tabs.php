<?php foreach ( $this->get_product_page_tabs() as $key => $tab ) { ?>
	
	<div class='ewd-upcp-single-product-tab <?php echo ( ! $this->is_starting_tab( sanitize_title( $tab->name ) ) ? 'ewd-upcp-hidden' : '' ); ?>' data-tab='<?php echo esc_attr( sanitize_title( $tab->name ) ); ?>'>

		<?php echo do_shortcode( $this->product->convert_custom_fields( $tab->content ) ); ?>

	</div>

<?php } ?>