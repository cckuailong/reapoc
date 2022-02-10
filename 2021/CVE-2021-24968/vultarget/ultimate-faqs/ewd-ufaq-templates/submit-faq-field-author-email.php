<div class='form-field'>

	<label id='ewd-ufaq-faq-author-email' class='ewd-ufaq-faq-label'>
		<?php echo esc_html( $this->get_label( 'label-question-author-email' ) ); ?>:
	</label>

	<input type='text' name='post_author_email' id='post_author_email' value='<?php echo ( ! empty( $_POST['post_author_email'] ) ? esc_attr( $_POST['post_author_email'] ) : ''); ?>' />
		
	<div id='ewd-ufaq-author-email-explanation' class='ewd-ufaq-field-explanation'>
		
		<label for='explanation'></label>
		
		<span>
			<?php echo esc_html( $this->get_label( 'label-question-author-email-explanation' ) ); ?>
		</span>

	</div>

</div>