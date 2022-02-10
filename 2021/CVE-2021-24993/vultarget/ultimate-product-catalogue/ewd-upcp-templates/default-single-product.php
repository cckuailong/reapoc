<div id='ewd-upcp-single-product-<?php echo $this->product->id; ?>' class='ewd-upcp-standard-product-page ewd-upcp-product-page'>

	<?php $this->print_product_breadcrumbs(); ?>

	<div class='ewd-upcp-single-product-details'>

		<div class='ewd-upcp-single-product-details-title-and-price'>

			<?php $this->print_title(); ?>
		
			<?php $this->maybe_print_price(); ?>
		
		</div>

		<div class='ewd-upcp-clear'></div>
			
		<div class='ewd-upcp-single-product-images-div'>

			<?php $this->print_additional_images(); ?>

			<?php $this->print_main_image(); ?>

		</div>


		<div class='ewd-upcp-single-product-details-description'>

			<?php $this->print_social_media_buttons(); ?>

			<?php $this->print_product_description(); ?>

			<?php $this->print_extra_description_elements(); ?>
				
		</div>

	</div>

	<div class='ewd-upcp-single-product-right-column'>

		<?php $this->maybe_print_related_products(); ?>

		<?php $this->maybe_print_next_previous_products(); ?>

		<?php $this->maybe_print_videos(); ?>

	</div> 

	<?php $this->maybe_print_inquiry_form(); ?>

</div>