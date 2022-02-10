<div class='ewd-ufaq-faq-header'>

	<?php foreach ( $this->faqs as $faq_count => $faq ) { ?>

		<?php if ( $faq_count < $this->faqs_per_page * ( $this->faq_page - 1) or $faq_count >= $this->faqs_per_page * ( $this->faq_page ) ) { continue; } ?>

		<?php if ( ! in_array( $faq->post->ID, $this->displayed_faqs ) ) { continue; } ?>

		<div class='ewd-ufaq-faq-header-title'>

		<a class='ewd-ufaq-faq-header-link'  data-postid='<?php echo esc_attr( $faq->unique_id ); ?>'>
			<?php echo esc_html( $faq->question ); ?>
		</a>

		</div>


	<?php } ?>

</div>