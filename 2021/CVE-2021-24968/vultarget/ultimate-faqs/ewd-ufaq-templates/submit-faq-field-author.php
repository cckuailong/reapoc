<div class='form-field'>

	<label id='ewd-ufaq-faq-author' class='ewd-ufaq-faq-label'>
		<?php echo esc_html( $this->get_label( 'label-question-author' ) ); ?>:
	</label>

	<input type='text' name='post_author' id='post_author' value='<?php echo ( ! empty( $_POST['post_author'] ) ? esc_attr( $_POST['post_author'] ) : ''); ?>' />
		
	<div id='ewd-ufaq-author-explanation' class='ewd-ufaq-field-explanation'>
		
		<label for='explanation'></label>
		
		<span>
			<?php echo esc_html( $this->get_label( 'label-question-author-explanation' ) ); ?>
		</span>

	</div>

</div>