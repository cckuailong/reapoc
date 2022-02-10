<?php $high_contrast = cmplz_get_value('high_contrast', false, 'settings') ? 'cmplz-high-contrast' : '';?>
<div class="wrap <?php echo $high_contrast ?>" id="complianz">
	<?php //this header is a placeholder to ensure notices do not end up in the middle of our code ?>
	<h1 class="cmplz-notice-hook-element"></h1>
	<div id="cmplz-{page}">
		<div id="cmplz-header">
			<img alt="Complianz-GDPR/CCPA" src="<?php echo trailingslashit(cmplz_url)?>assets/images/cmplz-logo.svg">
            <div class="cmplz-header-right">
                <a href="https://complianz.io/docs/" class="link-black" target="_blank"><?php _e("Documentation", "complianz-gdpr")?></a>
                <a href="<?php echo apply_filters('cmplz_support_link', 'https://wordpress.org/support/plugin/complianz-gdpr/')?>" class="button button-black" target="_blank"><?php _e("Support", "complianz-gdpr") ?></a>
            </div>
		</div>
		<div id="cmplz-content-area">
			{content}
		</div>
	</div>
</div>
