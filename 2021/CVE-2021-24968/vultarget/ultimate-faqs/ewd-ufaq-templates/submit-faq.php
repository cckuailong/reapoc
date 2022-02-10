<div class='ewd-ufaq-question-form'>

	<?php $this->maybe_print_submitted_faq_message(); ?>

	<form id='question_form' method='post' action='#'>

		<?php $this->print_nonce_field(); ?>

		<?php $this->print_referer_field(); ?>

		<?php $this->print_question_title_field(); ?>

		<?php $this->maybe_print_answer_field(); ?>

		<?php $this->maybe_print_custom_fields(); ?>

		<?php $this->print_author_field(); ?>

		<?php $this->maybe_print_author_email_field(); ?>

		<?php $this->maybe_print_captcha_field(); ?>

		<p class='submit'>

			<input type='submit' name='submit_question' id='submit' class='button-primary' value='<?php echo esc_attr( $this->submit_text ); ?>'  />

		</p>

	</form>

</div>