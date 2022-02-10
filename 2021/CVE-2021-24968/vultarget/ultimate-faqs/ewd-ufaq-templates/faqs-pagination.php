<div class='ewd-ufaq-bottom ewd-ufaq-pagination-<?php echo esc_attr( $this->get_option( 'page-type' ) ); ?>' data-current_page='<?php echo esc_attr( $this->faq_page ); ?>' data-max_page='<?php echo esc_attr( $this->max_page ); ?>'>

	<?php if ( $this->get_option( 'page-type' ) == 'distinct' ) { ?>

		<div class='ewd-ufaq-previous-faqs <?php echo ( $this->faq_page <= 1 ? 'ewd-ufaq-hidden' : '' ); ?>'>
			<h4><?php echo esc_html( $this->get_label( 'label-previous' ) ); ?></h4>
		</div>

		<div class='ewd-ufaq-next-faqs <?php echo ( $this->faq_page >= $this->max_page ? 'ewd-ufaq-hidden' : '' ); ?>'>
			<h4><?php echo esc_html( $this->get_label( 'label-next' ) ); ?></h4>
		</div>
	<?php } ?>

	<?php if ( $this->get_option( 'page-type' ) == 'load_more' ) { ?>

		<div class='ewd-ufaq-load-more <?php echo ( $this->faq_page >= $this->max_page ? 'ewd-ufaq-hidden' : '' ); ?>'>
			<h4><?php echo esc_html( $this->get_label( 'label-load-more' ) ); ?></h4>
		</div>
	<?php } ?>

</div>