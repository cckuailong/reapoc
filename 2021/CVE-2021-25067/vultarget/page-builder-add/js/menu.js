(function($){


	var elToAppendTo = jQuery('#menu-posts-ulpb_post').children('.wp-submenu-wrap');

	$(elToAppendTo).children('li:contains("Blank Page")').css('display','none');
	$(elToAppendTo).children('li:contains("Go Pro")').children().css({'color':'#fff', 'background':'#8BC34A' } );

	if (ulpb_oldf_site_url.premActive == 'true') {
		$(elToAppendTo).children('li:contains("Go Pro")').css('display','none');
	}

})(jQuery);