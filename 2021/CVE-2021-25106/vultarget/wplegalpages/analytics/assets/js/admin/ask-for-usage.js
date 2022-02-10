(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	$(
		function() {
			$( document ).on(
				'click',
				'.ask-for-usage-notice .notice-dismiss, .ask-for-usage-notice .notice-buttons button.ask-for-usage-optout',
				function(){
					var parent = $( this ).parents( '.ask-for-usage-notice' );
					var slug   = parent.find( 'input' ).val();
					var data   = {
						action: 'ask-for-usage-dismiss',
						slug: slug,
						security: ask_for_usage.nonces.ask_for_usage,
					};
					$.ajax(
						{
							url: ask_for_usage.ajax_url,
							data: data,
							dataType:'json',
							type: 'POST',
							success: function (data) {
								location.reload();
							}
						}
					);
				}
			);
			$( document ).on(
				'click',
				'.ask-for-usage-notice .notice-buttons button.ask-for-usage-optin',
				function(){
					var parent = $( this ).parents( '.ask-for-usage-notice' );
					var slug   = parent.find( 'input' ).val();
					var data   = {
						action: 'ask-for-usage-optin',
						slug: slug,
						security: ask_for_usage.nonces.ask_for_usage,
					};
					$.ajax(
						{
							url: ask_for_usage.ajax_url,
							data: data,
							dataType:'json',
							type: 'POST',
							success: function (data) {
								location.reload();
							}
						}
					);
				}
			);
		}
	);
})( jQuery );
