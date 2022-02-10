( function( window ) {

'use strict';

// class helper functions from bonzo https://github.com/ded/bonzo

function classReg( className ) {
  return new RegExp("(^|\\s+)" + className + "(\\s+|$)");
}

// classList support for class management
// altho to be fair, the api sucks because it won't accept multiple classes at once
var hasClass, addClass, removeClass;

if ( 'classList' in document.documentElement ) {
  hasClass = function( elem, c ) {
    return elem.classList.contains( c );
  };
  addClass = function( elem, c ) {
    elem.classList.add( c );
  };
  removeClass = function( elem, c ) {
    elem.classList.remove( c );
  };
}
else {
  hasClass = function( elem, c ) {
    return classReg( c ).test( elem.className );
  };
  addClass = function( elem, c ) {
    if ( !hasClass( elem, c ) ) {
      elem.className = elem.className + ' ' + c;
    }
  };
  removeClass = function( elem, c ) {
    elem.className = elem.className.replace( classReg( c ), ' ' );
  };
}

function toggleClass( elem, c ) {
  var fn = hasClass( elem, c ) ? removeClass : addClass;
  fn( elem, c );
}

window.classie = {
  // full names
  hasClass: hasClass,
  addClass: addClass,
  removeClass: removeClass,
  toggleClass: toggleClass,
  // short names
  has: hasClass,
  add: addClass,
  remove: removeClass,
  toggle: toggleClass
};

})( window );

jQuery( document ).ready( function( $ ) {
	var	Mgwprm = document.getElementById( 'mg-wprm-wrap' );
	var	wprm_menuDir = document.getElementById( 'wprMenu' );
	body = document.body;

	//Body slide from one side ( left, right or top )
	if( ! $('.wprmenu_bar').hasClass('normalslide') )
		$('body').addClass('cbp-spmenu-push');

	$('.wprmenu_bar').click( function(event) {
		if( $(event.target).hasClass('bar_logo') )
			return;
		classie.toggle( this, 'active' );
		$(this).find('div.hamburger').toggleClass('is-active');

		if( $(this).hasClass('active') ) {
			$('html').addClass('wprmenu-body-fixed');
			if( wprmenu.enable_overlay == '1' ) {
				$('div.wprm-wrapper').find('.wprm-overlay').addClass('active');
			}
			
		}
		else {
			$('html').removeClass('wprmenu-body-fixed');
			if( wprmenu.enable_overlay == '1' ) {
				$('div.wprm-wrapper').find('.wprm-overlay').removeClass('active');
			}
		}

		// For the right side body push
		if (!$(this).hasClass('normalslide') && $(this).hasClass('left')) {
			doc_width = $(document).width()*(wprmenu.menu_width/100);
			push_width = (wprmenu.push_width != '' && wprmenu.push_width < doc_width) ? wprmenu.push_width : doc_width;
			classie.toggle(body, 'cbp-spmenu-push-toright');
			if( $('body').hasClass('cbp-spmenu-push-toright') )
				$('body').css('left',push_width+'px');
			else
				$('body').css('left','0px');
		}
		// For the left side body push
		if (!$(this).hasClass('normalslide') &&  $(this).hasClass('right')) {
			doc_width = $(document).width()*(wprmenu.menu_width/100);
			push_width = (wprmenu.push_width != '' && wprmenu.push_width < doc_width) ? wprmenu.push_width : doc_width;
			classie.toggle(body, 'cbp-spmenu-push-toleft');
			if( $('body').hasClass('cbp-spmenu-push-toleft') )
				$('body').css('left','-'+push_width+'px');
			else
				$('body').css('left','0px');
		}
		classie.toggle(Mgwprm, 'cbp-spmenu-open');

		close_sub_uls();

	});

	//fix the scaling issue by adding/replacing viewport metatag
	var mt = $('meta[name=viewport]');
	mt = mt.length ? mt : $('<meta name="viewport" />').appendTo('head');
	if(wprmenu.zooming == 'no') {
		mt.attr('content', 'user-scalable=no, width=device-width, maximum-scale=1, minimum-scale=1');
	} else {
		mt.attr('content', 'user-scalable=yes, width=device-width, initial-scale=1.0, minimum-scale=1');
	}


	// Click on body remove the menu
	$('body').click( function( event ) {	
		if ( $( '#wprmenu_bar' ).hasClass( 'active' ) ) {
			$('#wprmenu_bar .wprmenu_icon').addClass('open');
		}	
		else {
			$('#wprmenu_bar .wprmenu_icon').removeClass('open');
		}
	});

	menu = $('#mg-wprm-wrap');
	menu_ul = $('#wprmenu_menu_ul'), //the menu ul

	$(document).mouseup(function (e) {
		if ( ($(e.target).hasClass('wprmenu_bar') || $(e.target).parents('.wprmenu_bar').length == 0) && 
			($(e.target).hasClass('cbp-spmenu') || $(e.target).parents('.cbp-spmenu').length == 0)) {
    		if(menu.is(':visible') ) {
				$('.hamburger.is-active').trigger('click');
			}
		}
	});

	//add arrow element to the parent li items and chide its child uls
	menu.find('ul.sub-menu').each(function() {
		var sub_ul = $(this),
		parent_a = sub_ul.prev('a'),
		parent_li = parent_a.parent('li').first();

		parent_a.addClass('wprmenu_parent_item');
		parent_li.addClass('wprmenu_parent_item_li');

		var expand = parent_a.before('<span class="wprmenu_icon wprmenu_icon_par icon_default"></span> ').find('.wprmenu_icon_par');
		sub_ul.hide();
	});


	//expand / collapse action (SUBLEVELS)
	$('.wprmenu_icon_par').on('click',function() {
		var t = $(this),
		child_ul = t.parent('li').find('ul.sub-menu').first();
		child_ul.slideToggle('300');
		t.toggleClass('wprmenu_par_opened');
		t.parent('li').first().toggleClass('wprmenu_no_border_bottom');
	});

	//helper - close all submenus when menu is hiding
	function close_sub_uls() {
		menu.find('ul.sub-menu').each(function() {
			var ul = $(this),
			icon = ul.parent('li').find('.wprmenu_icon_par'),
			li = ul.parent('li');

			if(ul.is(':visible')) ul.slideUp(300);
			icon.removeClass('wprmenu_par_opened');
			li.removeClass('wprmenu_no_border_bottom');
		});
	}

	//submenu opened
	function open_sub_uls() {
		menu.find('ul.sub-menu').each(function() {
			var ul = $(this),
			icon = ul.parent('li').find('.wprmenu_icon_par'),
			li = ul.parent('li');

			ul.slideDown(300);
			icon.removeClass('wprmenu_par_opened');
			icon.addClass('wprmenu_par_opened');
		});
	}

	if( menu.hasClass('cbp-spmenu-top') && $('body').hasClass('cbp-spmenu-push') ){
		$('body').prepend(menu);
		//show / hide the menu
		$('#wprmenu_bar,#custom_menu_icon').on('click', function(e) {
			if( $(e.target).hasClass('bar_logo') )
				return;
			//scroll window top
			$("html, body").animate({ scrollTop: 0 }, 300);

			close_sub_uls();
			menu.stop(true, false).slideToggle(300);
		});
	}
	if( wprmenu.parent_click == 'yes' ) {
		$('a.wprmenu_parent_item').on('click', function(e){
			e.preventDefault();
			$(this).prev('.wprmenu_icon_par').trigger('click');
		});
	}
	$('#wprmenu_menu_ul a').click(function(){
		if( wprmenu.parent_click !='yes' || (wprmenu.parent_click == 'yes' && !$(this).hasClass('wprmenu_parent_item')) )
			$('.hamburger.is-active').trigger('click');
	});
	if( wprmenu.swipe == 'yes' ) {
		$('body').swipe({
			excludedElements: "button, input, select, textarea, .noSwipe",
			threshold: 200,
			swipe:function(event, direction, distance, duration, fingerCount, fingerData) {
				menu_el = $('.wprmenu_bar .hamburger, .wprmenu_bar .wpr-custom-menu');
				if( direction =='left' && menu_el.hasClass('is-active') )
					menu_el.trigger('click');
				
				if( direction =='right' && !menu_el.hasClass('is-active') )
					menu_el.trigger('click');
    		}
		});
	}

});