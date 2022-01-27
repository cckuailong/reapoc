// BPS jQuery Tabs Menus with Toggle/Opacity
jQuery(document).ready(function($){
	
	$( '#bps-tabs' ).addClass( "bps-tab-page" ); 	
	$( '#bps-tabs' ).tabs({ 
		show: { 
			opacity: "toggle", 
			duration: 400 
		} 
	});
	
	// toggle causes undesirable effects/results for inpage tabs
	$( '#bps-edittabs' ).addClass( "bps-edittabs-class" );
	$( '#bps-edittabs' ).tabs();
	
	// Wizard no opacity toggle
	$( '#bps-tabs-wizard' ).addClass( "bps-tab-page" );
	$( '#bps-tabs-wizard' ).tabs();
});