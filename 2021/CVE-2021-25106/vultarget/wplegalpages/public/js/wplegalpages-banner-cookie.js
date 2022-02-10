/**
 * Banner cookies JavaScript.
 *
 * @package    Wplegalpages
 * @subpackage Wplegalpages/public
 * @author     wpeka <https://club.wpeka.com>
 */

 (function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
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
		for(var i=0; i<cookies.length; i++) {
			var name = cookies[i]['cookie_name']
			var end = new Date();
			end.setTime(end.getTime() + (cookies[i]['cookie_expire'] * 1000))
			$.cookie( name, name, {expires: end } )
		}
		location.reload()
})( jQuery );
