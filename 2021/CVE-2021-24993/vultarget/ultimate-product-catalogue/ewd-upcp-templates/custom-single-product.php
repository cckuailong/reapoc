<div id='ewd-upcp-single-product-<?php echo $this->product->id; ?>' class='ewd-upcp-custom-product-page ewd-upcp-product-page'>

	<?php $this->print_product_breadcrumbs(); ?>

	<div class='ewd-upcp-product-page-large-screen gridster gridster-large'>
	
		<ul>
	
			<?php foreach ( $this->get_custom_product_page_elements() as $page_element ) { ?>
	
				<?php $this->print_custom_product_page_element( $page_element ); ?>
	
			<?php } ?>
	
		</ul>

	</div>

	<div class='ewd-upcp-product-page-mobile-screen gridster gridster-mobile'>
	
		<ul>
	
			<?php foreach ( $this->get_mobile_custom_product_page_elements() as $page_element ) { ?>
	
				<?php $this->print_custom_product_page_element( $page_element ); ?>
	
			<?php } ?>
	
		</ul>

	</div>

</div>