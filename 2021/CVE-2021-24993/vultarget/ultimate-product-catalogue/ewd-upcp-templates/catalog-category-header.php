<div class='ewd-upcp-catalog-category ewd-upcp-catalog-category-<?php echo $this->get_option( 'styling-category-heading-style' ); ?>'>

	<div class='ewd-upcp-catalog-category-heading'>

		<?php $this->maybe_print_category_heading_image(); ?>
		
		<div class='ewd-upcp-catalog-category-label'>
			<?php echo esc_html( get_term( $this->category_id )->name ); ?>
		</div>

		<?php $this->maybe_print_category_heading_description(); ?>

	</div>