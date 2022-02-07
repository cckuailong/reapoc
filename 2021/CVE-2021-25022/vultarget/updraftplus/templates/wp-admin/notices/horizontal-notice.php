<?php if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed'); ?>

<?php if (!empty($button_meta) && 'review' == $button_meta) : ?>

	<div class="updraft-ad-container updated">
	<div class="updraft_notice_container updraft_review_notice_container">
		<div class="updraft_advert_content_left_extra">
			<img src="<?php echo UPDRAFTPLUS_URL.'/images/'.$image;?>" width="100" alt="<?php _e('notice image', 'updraftplus');?>" />
		</div>
		<div class="updraft_advert_content_right">
			<p>
				<?php echo $text; ?>
			</p>
					
			<?php if (!empty($button_link)) { ?>
				<div class="updraft_advert_button_container">
					<a class="button button-primary" href="<?php esc_attr_e(apply_filters('updraftplus_com_link', $button_link));?>" target="_blank" onclick="jQuery('.updraft-ad-container').slideUp(); jQuery.post(ajaxurl, {action: 'updraft_ajax', subaction: '<?php echo $dismiss_time;?>', nonce: '<?php echo wp_create_nonce('updraftplus-credentialtest-nonce');?>', dismiss_forever: '1' });">
						<?php _e('Ok, you deserve it', 'updraftplus'); ?>
					</a>
					<div class="dashicons dashicons-calendar"></div>
					<a class="updraft_notice_link" href="#" onclick="jQuery('.updraft-ad-container').slideUp(); jQuery.post(ajaxurl, {action: 'updraft_ajax', subaction: '<?php echo $dismiss_time;?>', nonce: '<?php echo wp_create_nonce('updraftplus-credentialtest-nonce');?>', dismiss_forever: '0' });">
						<?php _e('Maybe later', 'updraftplus'); ?>
					</a>
					<div class="dashicons dashicons-no-alt"></div>
					<a class="updraft_notice_link" href="#" onclick="jQuery('.updraft-ad-container').slideUp(); jQuery.post(ajaxurl, {action: 'updraft_ajax', subaction: '<?php echo $dismiss_time;?>', nonce: '<?php echo wp_create_nonce('updraftplus-credentialtest-nonce');?>', dismiss_forever: '1' });"><?php _e('Never', 'updraftplus'); ?></a>
				</div>
			<?php } ?>
		</div>
	</div>
	<div class="clear"></div>
</div>

<?php else : ?>

<div class="updraft-ad-container updated">
	<div class="updraft_notice_container">
		<div class="updraft_advert_content_left">
			<img src="<?php echo UPDRAFTPLUS_URL.'/images/'.$image;?>" width="60" height="60" alt="<?php _e('notice image', 'updraftplus');?>" />
		</div>
		<div class="updraft_advert_content_right">
			<h3 class="updraft_advert_heading">
				<?php
					if (!empty($prefix)) echo $prefix.' ';
					echo $title;
				?>
				<div class="updraft-advert-dismiss">
				<?php if (!empty($dismiss_time)) { ?>
					<a href="#" onclick="jQuery('.updraft-ad-container').slideUp(); jQuery.post(ajaxurl, {action: 'updraft_ajax', subaction: '<?php echo $dismiss_time;?>', nonce: '<?php echo wp_create_nonce('updraftplus-credentialtest-nonce');?>' });"><?php _e('Dismiss', 'updraftplus'); ?></a>
				<?php } else { ?>
					<a href="#" onclick="jQuery('.updraft-ad-container').slideUp();"><?php _e('Dismiss', 'updraftplus'); ?></a>
				<?php } ?>
				</div>
			</h3>
			<p>
				<?php
					echo $text;

					if (isset($discount_code)) echo ' <b>' . $discount_code . '</b>';
					
					if (!empty($button_link) && !empty($button_meta)) {
				?>
				<a class="updraft_notice_link" href="<?php esc_attr_e(apply_filters('updraftplus_com_link', $button_link));?>"><?php
						if ('updraftcentral' == $button_meta) {
							_e('Get UpdraftCentral', 'updraftplus');
						} elseif ('updraftplus' == $button_meta) {
							_e('Get Premium', 'updraftplus');
						} elseif ('signup' == $button_meta) {
							_e('Sign up', 'updraftplus');
						} elseif ('go_there' == $button_meta) {
							_e('Go there', 'updraftplus');
						} else {
							_e('Read more', 'updraftplus');
						}
					?></a>
				<?php
					}
				?>
			</p>
		</div>
	</div>
	<div class="clear"></div>
</div>

<?php

endif;