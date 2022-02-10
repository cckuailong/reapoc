<div class='ewd-ufaq-faq-header'>

	<?php $faq_count = 0; ?>

	<?php foreach ( $this->category_faqs as $term_id => $category_faqs ) { ?>

		<?php if ( $faq_count < $this->faqs_per_page * ( $this->faq_page - 1) or $faq_count >= $this->faqs_per_page * ( $this->faq_page ) ) { continue; } ?>

		<?php $category = get_term( $term_id, EWD_UFAQ_FAQ_CATEGORY_TAXONOMY ); ?>

		<div class='ewd-ufaq-faq-header-category'>

			<div class='ewd-ufaq-faq-header-category-title' data-categoryid='<?php echo esc_attr( $category->term_id ); ?>'>
				<<?php echo $this->get_option( 'styling-category-heading-type' ) ?>><?php echo esc_html( $category->name ); ?></<?php echo $this->get_option( 'styling-category-heading-type' ) ?>>
			</div>

			<?php foreach ( $category_faqs as $faq ) { ?>

				<?php if ( ! in_array( $faq->post->ID, $this->displayed_faqs ) ) { continue; } ?>

				<div class='ewd-ufaq-faq-header-title'>

					<a class='ewd-ufaq-faq-header-link'  data-postid='<?php echo esc_attr( $faq->unique_id ); ?>'>
						<?php echo esc_html( $faq->question ); ?>
					</a>

				</div>

				<?php $faq_count++; ?>

			<?php } ?>

		</div>

	<?php } ?>

</div>