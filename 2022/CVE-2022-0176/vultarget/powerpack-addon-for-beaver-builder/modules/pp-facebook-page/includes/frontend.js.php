;(function($) {
	$(document).ready(function() {
		new PPFacebookPage({
			id: '<?php echo $id; ?>',
			sdkUrl: '<?php echo pp_get_fb_sdk_url(); ?>'
		});
	});
})(jQuery);
