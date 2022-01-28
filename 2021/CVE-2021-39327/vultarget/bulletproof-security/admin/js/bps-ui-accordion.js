// BPS jQuery Accordion
jQuery(document).ready(function($){
    
	$( '#bps-accordion-1' ).addClass( "bps-accordion" );
	$( "#bps-accordion-1" ).accordion({
		collapsible: true,
		//active: 0, // do not use this option here so that inline jQuery code can be used per Form submission instead.
		animate: 500,
		autoHeight: true,
		clearStyle: true,
		heightStyle: "content"
    });
    
	// not displayed open by default - slower/smoother animation
	$( '#bps-accordion-2' ).addClass( "bps-accordion" );	
	$( "#bps-accordion-2" ).accordion({
		collapsible: true,
		active: false,
		animate: 1500,
		autoHeight: false,
		clearStyle: true,
		heightStyle: "content"
    });

    // displayed open by default - slower/smoother animation
	$( '#bps-accordion-3' ).addClass( "bps-accordion" );	
	$( "#bps-accordion-3" ).accordion({
		collapsible: true,
		active: 0,
		animate: 1400,
		autoHeight: false,
		clearStyle: true,
		heightStyle: "content"
    });
});