jQuery(function($) {

	//jQuery(document).ready(function($) {
	var project_hash = null;
	var project_status = null;
	var get_value = null;
	var tab_value = null;

	// make sure to only check the feed status on the woosea_manage_feed page
	url = new URL(window.location.href);
	if (url.searchParams.get('page')) {
		get_value = url.searchParams.get('page');
 	}
	if (url.searchParams.get('tab')) {
		tab_value = url.searchParams.get('tab');
	}    

  	if (get_value == 'woosea_manage_feed') {
		jQuery(function($) {
		//$(document).on('ready',function(){
                        // Check if feed is processing
                        
                        jQuery.ajax({
                                method: "POST",
                                url: ajaxurl,
                                data: { 'action': 'woosea_check_processing' }
                        })
                        .done(function( data ) {
				if(data.processing == "true"){
					myInterval = setInterval(woosea_check_perc,10000);
				} else {
					console.log("No refresh interval is needed, all feeds are ready");
				}                      
			})
                        .fail(function( data ) {
                                console.log('Failed AJAX Call :( /// Return Data: ' + data);
                        });
		});
	}

	//$(".dismiss-review-notification").click(function(){
	$(".dismiss-review-notification").on('click', function(){
	        jQuery.ajax({
                	method: "POST",
                        url: ajaxurl,
                        data: { 'action': 'woosea_review_notification' }
                })

		$(".review-notification").remove();	

	});

	//$(".get_elite").click(function(e){
	$(".get_elite").on('click', function(e){
		if(e.target.tagName === 'A') return; // clicking on links should not close the div notice

		$(".get_elite").remove();	
	
	        jQuery.ajax({
                	method: "POST",
                        url: ajaxurl,
                        data: { 'action': 'woosea_getelite_notification' }
                })
	});

	//$(".get_elite_activate").click(function(e){
	$(".get_elite_activate").on('click', function(e){
		if(e.target.tagName === 'A') return; // clicking on links should not close the div notice

		$(".get_elite_activate").remove();	
	
	        jQuery.ajax({
                	method: "POST",
                        url: ajaxurl,
                        data: { 'action': 'woosea_getelite_active_notification' }
                })
	});

//   	$("td[colspan=8]").find("div").parents("tr").hide();
   	$("td[id=manage_inline]").find("div").parents("tr").hide();

	//$('.checkbox-field').change(function(index, obj){
	$('.checkbox-field').on('change', function(index, obj){
                var csrfToken = $('#csrfToken').val();

		if(get_value == 'woosea_manage_settings' && tab_value == 'woosea_manage_attributes'){
			var attribute_value = $(this).val();
			var attribute_name = $(this).attr('name');
			var attribute_status = $(this).prop("checked");

	                jQuery.ajax({
 		               	method: "POST",
               	         	url: ajaxurl,
                        	data: { 
					'action': 'woosea_add_attributes', 
					'security': csfrToken,
					'attribute_name': attribute_name, 
					'attribute_value': attribute_value, 
					'active': attribute_status 
				}
                	})
		} else if (get_value == 'woosea_manage_feed') {
			project_hash = $(this).val();
			project_status = $(this).prop("checked");

	                jQuery.ajax({
 		               	method: "POST",
               	         	url: ajaxurl,
                        	data: { 
					'action': 'woosea_project_status', 
					'project_hash': project_hash, 
					'active': project_status 
				}
                	})

         		$("table tbody").find('input[name="manage_record"]').each(function(){
				var hash = this.value;
				if(hash == project_hash){
					if (project_status == false){
						$(this).parents("tr").addClass('strikethrough');
					} else {
						$(this).parents("tr").removeClass('strikethrough');
					}
                		}
            		});
		} else {
			// Do nothing, waste of resources
		}
	});

	// Check if user would like to enable product data manipulation support
	$('#add_manipulation_support').on('change', function(){ // on change of state
   		if(this.checked){

			// Checkbox is on
                	jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 'action': 'woosea_add_manipulation', 'status': "on" }
                	})
		} else {
			// Checkbox is off
                	jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 'action': 'woosea_add_manipulation', 'status': "off" }
                	})
		}
	})	

	// Check if user would like to enable WPML support
	$('#add_wpml_support').on('change', function(){ // on change of state
   		if(this.checked){

			// Checkbox is on
                	jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 'action': 'woosea_add_wpml', 'status': "on" }
                	})
		} else {
			// Checkbox is off
                	jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 'action': 'woosea_add_wpml', 'status': "off" }
                	})
		}
	})	

	// Check if user would like to enable Aelia Currency Switcher support
	$('#add_aelia_support').on('change', function(){ // on change of state
   		if(this.checked){

			// Checkbox is on
                	jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 'action': 'woosea_add_aelia', 'status': "on" }
                	})
		} else {
			// Checkbox is off
                	jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 'action': 'woosea_add_aelia', 'status': "off" }
                	})
		}
	})	

	// Check if user would like to use mother image for variations
	$('#add_mother_image').on('change', function(){ // on change of state
   		if(this.checked){

			// Checkbox is on
                	jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 'action': 'woosea_add_mother_image', 'status': "on" }
                	})
		} else {
			// Checkbox is off
                	jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 'action': 'woosea_add_mother_image', 'status': "off" }
                	})
		}
	})	

	// Check if user would like to add all country shipping costs
	$('#add_all_shipping').on('change', function(){ // on change of state
   		if(this.checked){

			// Checkbox is on
                	jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 'action': 'woosea_add_all_shipping', 'status': "on" }
                	})
		} else {
			// Checkbox is off
                	jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 'action': 'woosea_add_all_shipping', 'status': "off" }
                	})
		}
	})	

	// Check if user would like the plugin to respect free shipping class
	$('#free_shipping').on('change', function(){ // on change of state
   		if(this.checked){

			// Checkbox is on
                	jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 'action': 'woosea_free_shipping', 'status': "on" }
                	})
		} else {
			// Checkbox is off
                	jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 'action': 'woosea_free_shipping', 'status': "off" }
                	})
		}
	})	

	// Check if user would like the plugin to respect free shipping class
	$('#local_pickup_shipping').on('change', function(){ // on change of state
   		if(this.checked){

			// Checkbox is on
                	jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 'action': 'woosea_local_pickup_shipping', 'status': "on" }
                	})
		} else {
			// Checkbox is off
                	jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 'action': 'woosea_local_pickup_shipping', 'status': "off" }
                	})
		}
	})	

	// Check if user would like the plugin to remove the free shipping class
	$('#remove_free_shipping').on('change', function(){ // on change of state
   		if(this.checked){

			// Checkbox is on
                	jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 'action': 'woosea_remove_free_shipping', 'status': "on" }
                	})
		} else {
			// Checkbox is off
                	jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 'action': 'woosea_remove_free_shipping', 'status': "off" }
                	})
		}
	})	

	// Check if user would like to enable debug logging
	$('#add_woosea_logging').on('change', function(){ // on change of state
   		if(this.checked){

			// Checkbox is on
                	jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 'action': 'woosea_add_woosea_logging', 'status': "on" }
                	})
		} else {
			// Checkbox is off
                	jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 'action': 'woosea_add_woosea_logging', 'status': "off" }
                	})
		}
	})	

	// Check if user would like to enable addition of CDATA
	$('#add_woosea_cdata').on('change', function(){ // on change of state
   		if(this.checked){

			// Checkbox is on
                	jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 'action': 'woosea_add_woosea_cdata', 'status': "on" }
                	})
		} else {
			// Checkbox is off
                	jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 'action': 'woosea_add_woosea_cdata', 'status': "off" }
                	})
		}
	})	

	// Check if user would like to add a Facebook Pixel to their website
	$('#woosea_content_ids').on('change', function(){ // on change of state
		var content_ids = $('#woosea_content_ids').val();
		if(content_ids){
                	jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 'action': 'woosea_facebook_content_ids', 'content_ids': content_ids }
                	})
		}
	})

	// Check if user would like to add a Facebook Pixel to their website
	$('#add_facebook_pixel').on('change', function(){ // on change of state
		var nonce = $('#add_facebook_pixel').val();

   		if(this.checked){

			// Checkbox is on
                	jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 
					'action': 'woosea_add_facebook_pixel_setting', 
					'security': nonce,
					'status': "on" 
				}
                	})
			.done(function( data ) {
				$('#facebook_pixel').after('<tr id="facebook_pixel_id"><td colspan="2"><span>Insert Facebook pixel ID:</span>&nbsp;<input type=\"hidden\" name=\"nonce_facebook_pixel_id\" id=\"nonce_facebook_pixel_id\" value=\"'+ nonce +'\"><input type="text" class="input-field-medium" id="fb_pixel_id" name="fb_pixel_id">&nbsp;<input type="button" id="save_facebook_pixel_id" value="Save"></td></tr>');	
			})
                	.fail(function( data ) {
                        	console.log('Failed AJAX Call :( /// Return Data: ' + data);
                	});	
		} else {
			// Checkbox is off
                	jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 
					'action': 'woosea_add_facebook_pixel_setting', 
					'security': nonce,
					'status': "off" 
				}
                	})
			.done(function( data ) {
				$('#facebook_pixel_id').remove();	
			})
                	.fail(function( data ) {
                        	console.log('Failed AJAX Call :( /// Return Data: ' + data);
                	});	
		}
	})	

	// Check if user would like to enable the Facebook Conversion API
	$('#add_facebook_capi').on('change', function(){ // on change of state
		var nonce = $('#add_facebook_capi').val();

   		if(this.checked){

			// Checkbox is on
                	jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 
					'action': 'woosea_add_facebook_capi_setting', 
					'security': nonce,
					'status': "on" 
				}
                	})
			.done(function( data ) {
				$('#facebook_capi').after('<tr id="facebook_capi_token"><td colspan="2"><span>Insert your Facebook Conversion API token:</span><br/><br/><input type=\"hidden\" name=\"nonce_facebook_capi_id\" id=\"nonce_facebook_capi_id\" value=\"' + nonce +'\"><input type="textarea" class="textarea-field" id="fb_capi_token" name="fb_capi_token"><br/><br/><input type="button" id="save_facebook_capi_token" value="Save"></td></tr>');	
			})
                	.fail(function( data ) {
                        	console.log('Failed AJAX Call :( /// Return Data: ' + data);
                	});	
		} else {
			// Checkbox is off
                	jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 
					'action': 'woosea_add_facebook_capi_setting', 
					'security': nonce,
					'status': "off" 
				}
                	})
			.done(function( data ) {
				$('#facebook_capi_token').remove();	
			})
                	.fail(function( data ) {
                        	console.log('Failed AJAX Call :( /// Return Data: ' + data);
                	});	
		}
	})	


	// Check if user would like to change the batch size 
	$('#add_batch').on('change', function(){ // on change of state
   		if(this.checked){

                        var popup_dialog = confirm("Are you sure you want to change the batch size?\n\nChanging the batch size could seriously effect the performance of your website. We advise against changing the batch size if you are unsure about its effects!\n\nPlease reach out to support@adtribes.io when you would like to receive some help with this feature.");
                        if (popup_dialog == true){
				// Checkbox is on
        	        	jQuery.ajax({
                	        	method: "POST",
                        		url: ajaxurl,
                        		data: { 'action': 'woosea_add_batch', 'status': "on" }
                		})
				.done(function( data ) {
					$('#batch').after('<tr id="woosea_batch_size"><td colspan="2"><span>Insert batch size:</span>&nbsp;<input type=\"hidden\" name=\"nonce_batch\" id=\"nonce_batch\" value=\"'+ nonce +'\"><input type="text" class="input-field-medium" id="batch_size" name="batch_size">&nbsp;<input type="submit" id="save_batch_size" value="Save"></td></tr>');	
				})
                		.fail(function( data ) {
                        		console.log('Failed AJAX Call :( /// Return Data: ' + data);
                		});
			}	
		} else {
			// Checkbox is off
                	jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 'action': 'woosea_add_batch', 'status': "off" }
                	})
			.done(function( data ) {
				$('#woosea_batch_size').remove();	
			})
                	.fail(function( data ) {
                        	console.log('Failed AJAX Call :( /// Return Data: ' + data);
                	});	
		}
	})	

        // Save Batch Size
        jQuery("#save_batch_size").on('click',function(){
		var nonce = $('#nonce_batch').val();
		var batch_size = $('#batch_size').val();
	        var re = /^[0-9]*$/;
                
		var woosea_valid_batch_size=re.test(batch_size);
                // Check for allowed characters
                if (!woosea_valid_batch_size){
                        $('.notice').replaceWith("<div class='notice notice-error woosea-notice-conversion is-dismissible'><p>Sorry, only numbers are allowed for your batch size number.</p></div>");
                        // Disable submit button too
                        $('#save_batch_size').attr('disabled',true);
                } else {
                        $('.woosea-notice-conversion').remove();
                        $('#save_batch_size').attr('disabled',false);

			// Now we need to save the conversion ID so we can use it in the dynamic remarketing JS
                        jQuery.ajax({
                                method: "POST",
                                url: ajaxurl,
                                data: { 
					'action': 'woosea_save_batch_size', 
					'security': nonce,
					'batch_size': batch_size 
				}
                        })
                }	
	})

	// Check if user would like to enable Dynamic Remarketing
	$('#add_remarketing').on('change', function(){ // on change of state
                var nonce = $('#add_remarketing').val();

		if(this.checked){

			// Checkbox is on
                	jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 
					'action': 'woosea_add_remarketing', 
					'security': nonce,
					'status': "on" 
				}
                	})
			.done(function( data ) {
				$('#remarketing').after('<tr id="adwords_conversion_id"><td colspan="2"><span>Insert your Dynamic Remarketing Conversion tracking ID:</span>&nbsp;<input type=\"hidden\" name=\"nonce_adwords_conversion_id\" id=\"nonce_adwords_conversion_id\" value=\"'+ nonce +'\"><input type="text" class="input-field-medium" id="adwords_conv_id" name="adwords_conv_id">&nbsp;<input type="submit" id="save_conversion_id" value="Save"></td></tr>');	
			})
                	.fail(function( data ) {
                        	console.log('Failed AJAX Call :( /// Return Data: ' + data);
                	});	
		} else {
			// Checkbox is off
                	jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 
					'action': 'woosea_add_remarketing', 
					'security': nonce,
					'status': "off" 
				}
                	})
			.done(function( data ) {
				$('#adwords_conversion_id').remove();	
			})
                	.fail(function( data ) {
                        	console.log('Failed AJAX Call :( /// Return Data: ' + data);
                	});	
		}
	})	

        // Save Google Dynamic Remarketing pixel ID
        jQuery("#save_conversion_id").on('click',function(){
                var nonce = $('#nonce_adwords_conversion_id').val();
		var adwords_conversion_id = $('#adwords_conv_id').val();
	        var re = /^[0-9,-]*$/;
                
		var woosea_valid_conversion_id=re.test(adwords_conversion_id);
                // Check for allowed characters
                if (!woosea_valid_conversion_id){
                        $('.notice').replaceWith("<div class='notice notice-error woosea-notice-conversion is-dismissible'><p>Sorry, only numbers are allowed for your Dynamic Remarketing Conversion tracking ID.</p></div>");
                        // Disable submit button too
                        $('#save_conversion_id').attr('disabled',true);
                } else {
                        $('.woosea-notice-conversion').remove();
                        $('#save_conversion_id').attr('disabled',false);

			// Now we need to save the conversion ID so we can use it in the dynamic remarketing JS
                        jQuery.ajax({
                                method: "POST",
				url: ajaxurl,
                                data: { 
					'action': 'woosea_save_adwords_conversion_id', 
					'security': nonce,
					'adwords_conversion_id': adwords_conversion_id 
				}
                        })
                }	
	})

        // Save Facebook Pixel ID
        jQuery("#save_facebook_pixel_id").on('click',function(){
	        var nonce = $('#nonce_facebook_pixel_id').val();
		var facebook_pixel_id = $('#fb_pixel_id').val();
	        var re = /^[0-9]*$/;
		var woosea_valid_facebook_pixel_id=re.test(facebook_pixel_id);

                // Check for allowed characters
                if (!woosea_valid_facebook_pixel_id){
                        $('.notice').replaceWith("<div class='notice notice-error woosea-notice-conversion is-dismissible'><p>Sorry, only numbers are allowed for your Facebook Pixel ID.</p></div>");
                        // Disable submit button too
                        $('#save_facebook_pixel_id').attr('disabled',true);
                } else {
                        $('.woosea-notice-conversion').remove();
                        $('#save_facebook_pixel_id').attr('disabled',false);

			// Now we need to save the Facebook pixel ID so we can use it in the facebook pixel JS
                        jQuery.ajax({
                                method: "POST",
                                url: ajaxurl,
                                data: { 
					'action': 'woosea_save_facebook_pixel_id', 
					'security': nonce,
					'facebook_pixel_id': facebook_pixel_id 
				}
                        })
                }	
	})

        // Save Facebook Conversion API token
        jQuery("#save_facebook_capi_token").on('click',function(){
	        var nonce = $('#nonce_facebook_capi_id').val();
		var facebook_capi_token = $('#fb_capi_token').val();
	        var re = /^[0-9A-Za-z]*$/;
		var woosea_valid_facebook_capi_token=re.test(facebook_capi_token);

                // Check for allowed characters
                if (!woosea_valid_facebook_capi_token){
                        $('.notice').replaceWith("<div class='notice notice-error woosea-notice-conversion is-dismissible'><p>Sorry, this is not a valid Facebook Conversion API Token.</p></div>");
                        // Disable submit button too
                        $('#save_facebook_capi_token').attr('disabled',true);
                } else {
                        $('.woosea-notice-conversion').remove();
                        $('#save_facebook_capi_token').attr('disabled',false);

			// Now we need to save the Facebook Conversion API Token
                        jQuery.ajax({
                                method: "POST",
                                url: ajaxurl,
                                data: { 
					'action': 'woosea_save_facebook_capi_token', 
					'security': nonce,
					'facebook_capi_token': facebook_capi_token 
				}
                        })
                }	
	})

	// Check if user would like to add attributes
	$('#add_identifiers').on('change', function(){ // on change of state
   		if(this.checked){
			// Checkbox is on
                	jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 'action': 'woosea_add_identifiers', 'status': "on" }
                	})
		} else {
			// Checkbox is off
                	jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 'action': 'woosea_add_identifiers', 'status': "off" }
                	})
		}
	})	

	// Check if user would like to fix the WooCommerce structured data bug
	$('#fix_json_ld').on('change', function(){ // on change of state

   		if(this.checked){
			// Checkbox is on
                	jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 'action': 'woosea_enable_structured_data', 'status': "on" }
                	})
		} else {
			// Checkbox is off
                	jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 'action': 'woosea_enable_structured_data', 'status': "off" }
                	})
		}
	})	

	// Check if user wants to display structured data prices excluding VAT
	$('#no_structured_vat').on('change', function(){ // on change of state

   		if(this.checked){
			// Checkbox is on
                	jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 'action': 'woosea_structured_vat', 'status': "on" }
                	})
		} else {
			// Checkbox is off
                	jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 'action': 'woosea_structured_vat', 'status': "off" }
                	})
		}
	})	

//	$(".actions").delegate("span", "click", function() {
	$(".actions").on("click", "span", function() {
   		var id=$(this).attr('id');
		var idsplit = id.split('_');
		var project_hash = idsplit[1];
		var action = idsplit[0];		

		if (action == "gear"){
    			$("tr").not(':first').click(
				function(event) {
        				var $target = $(event.target);
        				$target.closest("tr").next().find("div").parents("tr").slideDown( "slow" );                
    				}
			);
		}

		if (action == "copy"){

			var popup_dialog = confirm("Are you sure you want to copy this feed?");
			if (popup_dialog == true){
       			jQuery.ajax({
                			method: "POST",
                       	 		url: ajaxurl,
                        		data: { 'action': 'woosea_project_copy', 'project_hash': project_hash }
                		})

                        	.done(function( data ) {
					data = JSON.parse( data );
					$('#woosea_main_table').append('<tr class><td>&nbsp;</td><td colspan="5"><span>The plugin is creating a new product feed now: <b><i>"' + data.projectname + '"</i></b>. Please refresh your browser to manage the copied product feed project.</span></span></td></tr>');
				})
            		}
		}


		if (action == "trash"){

			var popup_dialog = confirm("Are you sure you want to delete this feed?");
			if (popup_dialog == true){
        			jQuery.ajax({
                			method: "POST",
                       	 		url: ajaxurl,
                        		data: { 'action': 'woosea_project_delete', 'project_hash': project_hash }
                		})
	
            			$("table tbody").find('input[name="manage_record"]').each(function(){
					var hash = this.value;
					if(hash == project_hash){
                    				$(this).parents("tr").remove();
                			}
            			});
            		}
		}

		if(action == "cancel"){

			var popup_dialog = confirm("Are you sure you want to cancel processing the feed?");
			if (popup_dialog == true){
        			jQuery.ajax({
                			method: "POST",
                       	 		url: ajaxurl,
                        		data: { 'action': 'woosea_project_cancel', 'project_hash': project_hash }
                		})
	
				// Replace status of project to stop processing
			        $("table tbody").find('input[name="manage_record"]').each(function(){
					var hash = this.value;
					if(hash == project_hash){
						$(".woo-product-feed-pro-blink_"+hash).text(function () {
                                       			$(this).addClass('woo-product-feed-pro-blink_me');
    							return $(this).text().replace("ready", "stop processing"); 
						});	
					}
            			});
			}
		}

		if (action == "refresh"){
		
			var popup_dialog = confirm("Are you sure you want to refresh the product feed?");
			if (popup_dialog == true){
        			jQuery.ajax({
                			method: "POST",
                       	 		url: ajaxurl,
                        		data: { 'action': 'woosea_project_refresh', 'project_hash': project_hash }
                		})

				// Replace status of project to processing
			        $("table tbody").find('input[name="manage_record"]').each(function(){
					var hash = this.value;
					if(hash == project_hash){
						$(".woo-product-feed-pro-blink_off_"+hash).text(function () {
                                        		$(this).addClass('woo-product-feed-pro-blink_me');
							var status = $(".woo-product-feed-pro-blink_off_"+hash).text();
							myInterval = setInterval(woosea_check_perc,5000);
							if(status == "ready"){
								return $(this).text().replace("ready", "processing (0%)");
							} else if (status == "stopped"){
								return $(this).text().replace("stopped", "processing (0%)");
							} else if (status == "not run yet"){
								return $(this).text().replace("not run yet", "processing (0%)");
							} else {
								// it should not be coming here at all
								return $(this).text().replace("ready", "processing (0%)");
							}
						});	
					}
            			});
			}
		}
	});

	function woosea_check_perc(){
  		// Check if we need to UP the processing percentage

		$("table tbody").find('input[name="manage_record"]').each(function(){
       	        	var hash = this.value;
			jQuery.ajax({
				method: "POST",
                      	 	url: ajaxurl,
                       		data: { 'action': 'woosea_project_processing_status', 'project_hash': hash },
				success: function(data) {
	                        	data = JSON.parse( data );

					if(data.proc_perc < 100){
						if(data.running != "stopped"){
							$("#woosea_proc_"+hash).addClass('woo-product-feed-pro-blink_me');	
							return $("#woosea_proc_"+hash).text("processing ("+data.proc_perc+"%)");
						}
					} else if(data.proc_perc == 100){
					//	clearInterval(myInterval);
						$("#woosea_proc_"+hash).removeClass('woo-product-feed-pro-blink_me');	
						return $("#woosea_proc_"+hash).text("ready");
					} else if(data.proc_perc == 999){
						// Do not do anything
					} else {
					//	clearInterval(myInterval);
					}
				}
               		})

	             	// Check if we can kill the refresh interval
			// Kill interval when all feeds are done processing
        	     	jQuery.ajax({
                		method: "POST",
                       		url: ajaxurl,
                       		data: { 'action': 'woosea_check_processing' }
            	  	})
              		.done(function( data ) {
				data = JSON.parse( data );
                		if(data.processing == "false"){
					clearInterval(myInterval);
                       			console.log("Kill interval, all feeds are ready");
                    		}
             		})
             		.fail(function( data ) {
                		console.log('Failed AJAX Call :( /// Return Data: ' + data);
	             	});
		});
	}
});
