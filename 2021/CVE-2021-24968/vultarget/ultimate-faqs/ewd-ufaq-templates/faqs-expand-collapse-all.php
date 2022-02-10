<div class='ewd-ufaq-expand-collapse-div'>

	<span class='ewd-ufaq-expand-all <?php echo ( $this->display_all_answers ? 'ewd-ufaq-hidden' : '' ); ?>'>
		<span class='ewd-ufaq-toggle-all-symbol'>c</span> 
		<?php echo esc_html( $this->get_label( 'label-expand-all' ) ); ?>
	</span>

	<span class='ewd-ufaq-collapse-all <?php echo ( ! $this->display_all_answers ? 'ewd-ufaq-hidden' : '' ); ?>'>
		<span class='ewd-ufaq-toggle-all-symbol'>C</span>
		<?php echo esc_html( $this->get_label( 'label-collapse-all' ) ); ?>
	</span>

</div>