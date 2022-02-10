<div class='ewd-ufaq-captcha-div'>

	<label for='captcha_image'></label>
	<img src='data:image/png;base64,<?php echo $this->create_captcha_image(); ?>' alt='captcha' />
	<input type='hidden' name='ewd_ufaq_modified_captcha' value='<?php echo esc_attr( $this->captcha_form_code ); ?>' />

</div>

<div class='ewd-ufaq-captcha-response'><label for='captcha_text'><?php echo $this->get_label( 'label-captcha-image-number' ); ?>: </label>
	<input type='text' name='ewd_ufaq_captcha' value='' />
</div>