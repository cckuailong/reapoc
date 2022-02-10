<div <?php echo ewd_format_classes( $this->classes ); ?> id='<?php echo $this->get_id(); ?>' data-post_id='<?php echo $this->post->ID; ?>'>

	<?php $this->print_faq_title(); ?>

	<?php $this->maybe_print_faq_preview(); ?>

	<div class='ewd-ufaq-faq-body <?php echo ( ! $this->display_all_answers ? 'ewd-ufaq-hidden' : '' ); ?>' >

		<?php foreach ( $this->get_order_elements() as $element => $label ) { ?>

			<?php $this->maybe_print_element( $element ); ?>

		<?php } ?>

	</div>

</div>