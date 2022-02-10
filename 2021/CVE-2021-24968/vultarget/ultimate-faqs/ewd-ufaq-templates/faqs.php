<div <?php echo ewd_format_classes( $this->classes ); ?> id='ewd-ufaq-faq-list'>

	<?php $this->print_shortcode_args(); ?>

	<?php $this->maybe_print_expand_collapse_all(); ?>

	<?php $this->maybe_print_header(); ?>

	<div class='ewd-ufaq-faqs'>

		<?php $this->print_faqs(); ?>

	</div>

	<?php $this->maybe_print_pagination(); ?>

</div>