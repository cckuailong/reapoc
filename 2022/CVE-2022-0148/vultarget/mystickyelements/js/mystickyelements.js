/***
File Name: mystickyelements.js
Version: 1.2
Author: m.r.d.a
License: GPLv2 or later
***/
(function($) {
	
	

var myfixed_disable_small = parseInt(mysticky_element.mysticky_disable_at_width_string);
var myfixed_click = mysticky_element.myfixed_click_string;
var mybodyWidth = parseInt(document.body.clientWidth);
if (mybodyWidth >= myfixed_disable_small) {
	if (myfixed_click == 'hover') {
		
	/* mysticky blocks animation */
		$('.mysticky-block-left .mysticky-block-icon').mouseover(function(){
			$(this).addClass("mse-open");
			$(this).parent().stop().animate({'left' : '0px'});
		});
		$('.mysticky-block-left .mysticky-block-icon').on( 'click', function(){
			$(this).toggleClass("mse-open");
				if ($(this).hasClass("mse-open")) {
					$(this).parent().stop().animate({'left' : '0px'});
				} else {
               		var x = $(this).parent().width();
					$(this).parent().stop().animate({'left' : 0 - x});
				} 
		});
		$('.mysticky-block-left .mysticky-block-content').mouseleave(function(){
			var x = $(this).parent().width();
			$(this).parent().stop().animate({'left': 0 - x});
		});

		$('.mysticky-block-right .mysticky-block-icon').mouseover(function(){
			$(this).addClass("mse-open");
		    $(this).parent().stop().animate({'right' : '0px'});
			
		});
		$('.mysticky-block-right .mysticky-block-icon').on( 'click', function(){
			$(this).toggleClass("mse-open");
				if ($(this).hasClass("mse-open")) {
					$(this).parent().stop().animate({'right' : '0px'});
				} else {
                	var y = $(this).parent().width();
					$(this).parent().stop().animate({'right' : 0 - y});
				} 
		});
		$('.mysticky-block-right .mysticky-block-content').mouseleave(function(e){
			var y = $(this).parent().width();
			$(this).parent().stop().animate({'right' : 0 - y});
		});
	};
		
	if (myfixed_click == 'click') {
	
	/* mysticky blocks animation */
		$('.mysticky-block-left .mysticky-block-icon').on( 'click', function(){
			$(this).toggleClass("mse-open");
			if ($(this).hasClass("mse-open")) {
		    	$(this).parent().stop().animate({'left' : '0px'});
			} else {
            	var x = $(this).parent().width();
				$(this).parent().stop().animate({'left': 0 - x});
			} 			
		});
		$('.mysticky-block-left .mysticky-block-content').mouseleave(function(){
			var x = $(this).parent().width();
			$('.mysticky-block-left .mysticky-block-icon').removeClass("mse-open");
			$(this).parent().stop().animate({'left': 0 - x});
		});
		$('.mysticky-block-right .mysticky-block-icon').on( 'click', function(){
			$(this).toggleClass("mse-open");
				if ($(this).hasClass("mse-open")) {
			$(this).parent().stop().animate({'right' : '0px'});
				} else {
                var y = $(this).parent().width();
				$(this).parent().stop().animate({'right' : 0 - y});
				} 
		});
		$('.mysticky-block-right .mysticky-block-content').mouseleave(function(e){
			var y = $(this).parent().width();
			$('.mysticky-block-right .mysticky-block-icon').removeClass("mse-open");
			$(this).parent().stop().animate({'right' : 0 - y});
		});
	};	  
};
})(jQuery);