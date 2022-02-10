<?php if ( in_array( 'category', $this->get_option( 'extra-elements' ) ) ) { ?>
	
	<div class='ewd-upcp-single-product-categories'>

		<span class='ewd-upcp-single-product-extra-element-label'>
			<?php echo esc_html( $this->get_categories_label() ); ?>
		</span>

		<span class='ewd-upcp-single-product-extra-element-value'>
			<?php echo esc_html( $this->product->get_category_names() ); ?>
		</span>

	</div>

<?php } ?>

<?php if ( in_array( 'subcategory', $this->get_option( 'extra-elements' ) ) ) { ?>
	
	<div class='ewd-upcp-single-product-subcategories'>

		<span class='ewd-upcp-single-product-extra-element-label'>
			<?php echo esc_html( $this->get_subcategories_label() ); ?>
		</span>

		<span class='ewd-upcp-single-product-extra-element-value'>
			<?php echo esc_html( $this->product->get_subcategory_names() ); ?>
		</span>

	</div>

<?php } ?>

<?php if ( in_array( 'tags', $this->get_option( 'extra-elements' ) ) ) { ?>
	
	<div class='ewd-upcp-single-product-tags'>

		<span class='ewd-upcp-single-product-extra-element-label'>
			<?php echo esc_html( $this->get_tags_label() ); ?>
		</span>

		<span class='ewd-upcp-single-product-extra-element-value'>
			<?php echo esc_html( $this->product->get_tag_names() ); ?>
		</span>

	</div>

<?php } ?>

<?php if ( in_array( 'customfields', $this->get_option( 'extra-elements' ) ) ) { ?>
	
	<div class='ewd-upcp-single-product-custom-fields'>

		<?php echo $this->print_custom_fields(); ?>

	</div>

<?php } ?>