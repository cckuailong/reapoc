<div class='ewd-upcp-product-comparison-product'>
	
	<div class='ewd-upcp-product-comparison-title'>

		<?php $this->comparison_product->print_title(); ?>

	</div>

	<?php if ( in_array( 'image', $this->product_comparison_fields ) ) { ?>
		
		<div class='ewd-upcp-product-comparison-image'>

			<?php $this->comparison_product->print_image(); ?>
		
		</div>

	<?php } ?>

	<?php if ( in_array( 'price', $this->product_comparison_fields ) ) { ?>
		
		<div class='ewd-upcp-product-comparison-price'>

			<?php $this->comparison_product->print_product_price(); ?>
		
		</div>

	<?php } ?>

	<?php if ( in_array( 'categories', $this->product_comparison_fields ) ) { ?>
		
		<div class='ewd-upcp-product-comparison-categories'>

			<?php $this->comparison_product->print_categories(); ?>
		
		</div>

	<?php } ?>

	<?php if ( in_array( 'subcategories', $this->product_comparison_fields ) ) { ?>
		
		<div class='ewd-upcp-product-comparison-subcategories'>

			<?php $this->comparison_product->print_subcategories(); ?>
		
		</div>

	<?php } ?>

	<?php if ( in_array( 'tags', $this->product_comparison_fields ) ) { ?>
		
		<div class='ewd-upcp-product-comparison-tags'>

			<?php $this->comparison_product->print_tags(); ?>
		
		</div>

	<?php } ?>

	<?php foreach ( $this->get_product_comparison_custom_fields()  as $custom_field ) { ?>

		<div class='ewd-upcp-product-comparison-custom-field'>

			<?php $this->comparison_product->print_custom_field( $custom_field ); ?>

		</div>

	<?php } ?>

</div>