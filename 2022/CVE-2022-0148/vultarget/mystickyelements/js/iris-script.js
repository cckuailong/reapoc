jQuery(document).ready(function($){
    $('.my-color-field').wpColorPicker();
	
	$('#mystickyelement-update').on( 'click', function() {
		
		var confirm_tab = confirm("All your current tabs will not be available. Are you ready to move to the current version?");
		if (confirm_tab == true) {
			return true;
		} else {
			return false;
		}		
	});
	
});