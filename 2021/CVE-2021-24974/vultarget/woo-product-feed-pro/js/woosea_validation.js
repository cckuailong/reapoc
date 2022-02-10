jQuery(document).ready(function($) {

	// Disable submit button, will only enable if all fields validate
	$('#goforit').attr('disabled',true);

	// Validate project name
	$( "#projectname" ).on('blur', function() {
		var input=$(this);
		var re = /^[a-zA-Z0-9-_.àèìòùÀÈÌÒÙáéíóúýÁÉÍÓÚÝâêîôûÂÊÎÔÛãñõÃÑÕäëïöüÿÄËÏÖÜŸçÇßØøÅåÆæœ ]*$/;
		var minLength = 3;
		var maxLength = 30;
		var is_projectname=re.test(input.val());
		// Check for allowed characters
		if (!is_projectname){
			$('.notice').replaceWith("<div class='notice notice-error is-dismissible'><p>Sorry, only letters, numbers, whitespaces, -, . and _ are allowed for the projectname</p></div>");
			// Disable submit button too
			$('#goforit').attr('disabled',true);
		} else {
			// Check for length of projectname
			var value = $(this).val();
			if (value.length < minLength){
				$('.notice').replaceWith("<div class='notice notice-error is-dismissible'><p>Sorry, your project name needs to be at least 3 characters long.</p></div>");
				// Disable submit button too
			    	$('#goforit').attr('disabled',true);
			} else if (value.length > maxLength){
				// Disable submit button too
			    	$('#goforit').attr('disabled',true);
				$('.notice').replaceWith("<div class='notice notice-error is-dismissible'><p>Sorry, your project name cannot be over 30 characters long.</p></div>");
			} else {
				$('.notice').replaceWith("<div class='notice notice-info is-dismissible'><p>Please select the country and channel for which you would like to create a new product feed. The channel drop-down will populate with relevant country channels once you selected a country. Filling in a project name is mandatory.</p></div>");
				//$('.notice').remove();
			    	// Enable submit button
				$('#goforit').attr('disabled',false);
			}
		}
	});

	// Validate ruling values
        $( "#rulevalue" ).on('blur', function(){
        //$( "#rulevalue" ).blur("input", function(){
		var input=$(this);
		var minLength = 1;
		var maxLength = 200;
		var value = $(this).val();
		
		if (value.length < minLength){
			$('#rulevalueerror').append("<div id='woo-product-feed-pro-errormessage'>Sorry, minimum length is 1 charachter</div>");
			// Disable submit button too
			$('#goforit').attr('disabled',true);
		} else if (value.length > maxLength){
			// Disable submit button too
			$('#goforit').attr('disabled',true);
			$('#rulevalueerror').append("<div id='woo-product-feed-pro-errormessage'>Sorry, this value cannot be over 200 characters long.</div>");
		} else {
			$('#errormessage').remove();
			// Enable submit button
			$('#goforit').attr('disabled',false);
		}
	});
});
