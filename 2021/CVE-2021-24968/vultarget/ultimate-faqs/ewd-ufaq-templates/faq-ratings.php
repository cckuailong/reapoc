<div class='ewd-ufaq-ratings'>
	
	<div class='ewd-ufaq-ratings-label'>
		<?php echo esc_html( $this->get_label( 'label-find-faq-helpful' ) ); ?>
	</div>
	
	<div class='ewd-ufaq-rating-button ewd-ufaq-up-vote <?php echo ( ( $ewd_ufaq_controller->settings->get_setting( 'thumbs-up-image' ) and $ewd_ufaq_controller->settings->get_setting( 'thumbs-up-image' ) != 'http://' ) ? 'ewd-ufaq-ratings-custom-image' : 'ewd-ufaq-ratings-default-image' ); ?>' data-faq_id='<?php echo $this->post->ID; ?>'>
		<?php echo $this->get_thumbs_up_image(); ?>
		<span><?php echo $this->get_up_votes(); ?></span>
	</div>
	
	<div class='ewd-ufaq-rating-button ewd-ufaq-down-vote <?php echo ( ( $ewd_ufaq_controller->settings->get_setting( 'thumbs-down-image' ) and $ewd_ufaq_controller->settings->get_setting( 'thumbs-down-image' ) != 'http://' ) ? 'ewd-ufaq-ratings-custom-image' : 'ewd-ufaq-ratings-default-image' ); ?>' data-faq_id='<?php echo $this->post->ID; ?>'>
		<?php echo $this->get_thumbs_down_image(); ?>
		<span><?php echo $this->get_down_votes(); ?></span>
	</div>
</div>

<div class='ewd-ufaq-clear'></div>