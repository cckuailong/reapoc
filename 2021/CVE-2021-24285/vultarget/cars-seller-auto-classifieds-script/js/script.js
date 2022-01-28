/*  Table of Contents 
01. MENU ACTIVATION
02. GALLERY JAVASCRIPT
03. FITVIDES RESPONSIVE VIDEOS
04. MOBILE SELECT MENU
05. IE PLACEHOLDER TEXT
06. jQUERY TABS & TOGLE
*/
/*
=============================================== 01. MENU ACTIVATION  ===============================================
*/
jQuery(document).ready(function($) {
	jQuery("ul.sf-menu").supersubs({ 
	        minWidth:    4,   // minimum width of sub-menus in em units 
	        maxWidth:    25,   // maximum width of sub-menus in em units 
	        extraWidth:  1     // extra width can ensure lines don't sometimes turn over 
	                           // due to slight rounding differences and font-family 
	    }).superfish({ 
			animation:     {height:'show'},   // slide-down effect without fade-in 
			animationOut:  {opacity:'hide'},
			speed:         250,           // speed of the opening animation. Equivalent to second parameter of jQuery’s .animate() method
			speedOut:      'fast',
			autoArrows:    false,               // if true, arrow mark-up generated automatically = cleaner source code at expense of initialisation performance 
			dropShadows:   false,               // completely disable drop shadows by setting this to false 
			delay:     400               // 1.2 second delay on mouseout 
		});
});


/*
=============================================== 02. GALLERY JAVASCRIPT  ===============================================
*/
jQuery(document).ready(function($) {
    $('.gallery-progression').flexslider({
		animation: "fade",      
		slideDirection: "horizontal", 
		slideshow: false,         
		slideshowSpeed: 7000,  
		animationDuration: 200,        
		directionNav: true,             
		controlNav: true               
    });
});


jQuery(document).ready(function($) {
    $(window).load(function(){
      $('#carousel-vehicle').flexslider({
		  animation: "slide",
		  controlNav: false,
		  animationLoop: false,  
		  slideshow: false,
		  itemWidth: 100,
		  itemMargin: 6,
		  asNavFor: '#vehicle_slider'
      });

      $('#vehicle_slider').flexslider({
		animation: "fade",
		controlNav: false,
		directionNav: false, 
		animationLoop: false,
		slideshow: false, 
		sync: "#carousel"
      });
    });
});


/*
=============================================== 03. FITVIDES RESPONSIVE VIDEOS  ===============================================
*/
jQuery(document).ready(function($) {  
$("#main").fitVids();
$(".flexslider").fitVids();
});


/*
=============================================== 04. MOBILE SELECT MENU  ===============================================
*/
jQuery(document).ready(function($) {
$('.sf-menu').mobileMenu({
    defaultText: 'Navigate to...',
    className: 'select-menu',
    subMenuDash: '&ndash;&ndash;'
});

$('.filter-children').mobileMenu({
    defaultText: 'Navigate to...',
    className: 'select-menu',
    subMenuDash: '&ndash;&ndash;'
});
});




/*
=============================================== 05. IE PLACEHOLDER TEXT  ===============================================
*/
jQuery(document).ready(function($) {
$('input, textarea').placeholder();
 });



/*
 =============================================== 06. jQUERY TABS & TOGLE  ===============================================
*/
 
 /* -------------------- jQuery Tabs -------------------- */
 jQuery(document).ready(function($) {
   $('#progression-tab-container').easytabs();
   
   $('#button-select-progression').click( function() {
     $('#progression-tab-container').easytabs('select', '#progression_contact');
   });
   
 });
 
jQuery(document).ready(function($) {
	$('.sidebar-button-price').click(function(){
	        $('html, body').animate({scrollTop:$('#progression-tab-container').position().top}, 'fast');
	        return false;
	    });	 
});


/* -------------------- jQuery Toggle -------------------- */
jQuery(document).ready(function($) {  
	$("ul.progression-toggle li").click(function(){
		$(this).toggleClass("progression_active");
		$(this).next(".div_progression_toggle").stop(true, true).slideToggle("normal");
	});
});




