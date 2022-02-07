<?php if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed'); ?>

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
				?>
			</p>
			<p>
				<?php
					echo $text2;
				?>
			</p>
			<p>
				<?php
					echo $text3;
				?>
			</p>
		</div>
	</div>
	<div class="clear"></div>
</div>
