(function($) {

	new PPInfoList( {
		id: '<?php echo $id; ?>',
		layout: {
			large: '<?php echo $settings->layouts; ?>',
			medium: '<?php echo isset( $settings->layouts_medium ) ? $settings->layouts_medium : ''; ?>',
			responsive: '<?php echo isset( $settings->layouts_responsive ) ? $settings->layouts_responsive : ''; ?>',
		},
		breakpoints: {
			medium: '<?php echo $global_settings->medium_breakpoint; ?>',
			responsive: '<?php echo $global_settings->responsive_breakpoint; ?>',
		},
	} );

})(jQuery);
