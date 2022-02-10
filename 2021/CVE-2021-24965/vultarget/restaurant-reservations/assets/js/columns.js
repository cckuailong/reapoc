jQuery(document).ready(function($){
	$(function(){
		$(window).resize(function(){
			$('.rtb-booking-form form button').each(function(){
				var thisButton = $(this);
				var buttonHalfWidth = ( thisButton.outerWidth() / 2 );
				thisButton.css('margin-left', 'calc(50% - '+buttonHalfWidth+'px');
			});
		}).resize();
	}); 
});