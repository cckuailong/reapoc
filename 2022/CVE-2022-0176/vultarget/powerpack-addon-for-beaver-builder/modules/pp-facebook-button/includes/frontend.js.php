;(function($) {
	$(document).ready(function() {
		new PPFacebookButton({
			id: '<?php echo $id; ?>',
			sdkUrl: '<?php echo pp_get_fb_sdk_url(); ?>',
			currentUrl: '<?php echo get_permalink(); ?>'
		});
	});
})(jQuery);
