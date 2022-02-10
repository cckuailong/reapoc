<div class='form-field'>

	<label id='ewd-ufaq-submit-faq-question' class='ewd-ufaq-submit-faq-label'>
		<?php echo esc_html( $this->get_label( 'label-question-title' ) ); ?>:
	</label>

	<input type='text' name='faq_question' id='faq_question' value='<?php echo ( ! empty( $_POST['faq_question'] ) ? esc_attr( $_POST['faq_question'] ) : '' ); ?>' />

	<div id='ewd-ufaq-faq-question' class='ewd-ufaq-field-explanation'>
		
		<label for='explanation'></label>
		
		<span>
			<?php echo esc_html( $this->get_label( 'label-question-title-explanation' ) ); ?>
		</span>

	</div>

</div>