<div class='ewd-ufaq-meta-field'>

	<label for='faq_answer'>
		<?php echo esc_html( $this->get_label( 'label-proposed-answer' ) ); ?>:
	</label>

	<textarea name='faq_answer' class='ewd-ufaq-faq-textarea' required>
		<?php echo ( ! empty( $_POST['faq_answer'] ) ? esc_html( $_POST['faq_answer'] ) : '' ); ?>
	</textarea>

	<div id='ewd-ufaq-answer-explanation' class='ewd-ufaq-field-explanation'>
		
		<label for='explanation'></label>
		
		<span>
			<?php echo esc_html( $this->get_label( 'label-proposed-answer-explanation' ) ); ?>
		</span>

	</div>

</div>