document.observe("dom:loaded", function() {
	Event.observe('displayOption0', 'click', displayOption0Checked);
	Event.observe('displayOption1', 'click', displayOption1Checked);
	Event.observe('displayOption2', 'click', displayOption2Checked);
	Event.observe('displayOption3', 'click', displayOption3Checked);
});

function displayOption0Checked() {
	document.getElementById('customText').style.display = 'none';
	document.getElementById('customHTML').style.display = 'none';
}

function displayOption1Checked() {
	document.getElementById('customText').style.display = 'block';
	document.getElementById('customHTML').style.display = 'none';
}

function displayOption2Checked() {
	document.getElementById('customText').style.display = 'none';
	document.getElementById('customHTML').style.display = 'block';
}

function displayOption3Checked() {
	document.getElementById('customText').style.display = 'none';
	document.getElementById('customHTML').style.display = 'none';
}

jQuery(function($) {
	$('#add_current_address_btn').click(function(e) {
		e.preventDefault();
		e.stopPropagation();
		
		$(this).hide();
		$('#loading_current_address').show();
		
		$.get(ajaxurl, {
			action : 'uc_get_ip_address'
		}, function(response) {

			$('#loading_current_address').hide();
			$('#add_current_address_btn').show();
			$('#ip_address').val(response);
		});
	});
	
	$('#301_status').click(function(){
		console.log("selected 301 redirect");
		jQuery('#redirect_panel').show();
	});
	
	$('#200_status, #503_status').click(function(){
		jQuery('#redirect_panel').hide();
	});
});