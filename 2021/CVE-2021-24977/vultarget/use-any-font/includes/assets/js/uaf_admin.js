function uaf_lite_api_key_generate(){	
	jQuery.ajax({url: uaf_server_url+'/uaf_convertor/generate_lite_key.php',
	beforeSend : function(){
		jQuery('#uaf_api_key_generate').val('Generating...');
	},
	error: function(){
		jQuery('#uaf_api_key_generate').val(' Error ! ');		
	},
	success: function(result){
        var dataReturn 	= JSON.parse(result);
		key 			= dataReturn.key;
		jQuery('#uaf_api_key').val(key);
		jQuery('#uaf_api_key_generate').val('Click Verify to Complete');
    }});
}

function open_add_font(){
    jQuery('#font-upload').toggle('fast');
}

function multi_variation_holder_toggle(){
	jQuery('.multi_variation_holder').slideToggle('medium');	
}

function open_assign_font(){
	jQuery('#open_assign_font').toggle('fast');
    if (jQuery('#open_assign_font_button').val() == 'Assign Font'){
        jQuery('#open_assign_font_button').val('Close Assign Font');
    } else {
        jQuery('#open_assign_font_button').val('Assign Font');
    }
}

jQuery('document').ready(function(){
	/* JS UPLOADER */
	jQuery('#open_add_font_form_js').submit(function(e){
	    
	    e.preventDefault();

	    breakValidation = false;
	    jQuery('#open_add_font_form_js .uaf_required').each(function(){
	        if(!jQuery(this).val()){
				jQuery(this).next('.field_error').show();
				breakValidation = true;
				return false;
			}
		});

	    if(breakValidation){return false;}
	    
		jQuery.ajax( {
	      url: uaf_server_url+'/uaf_convertor/convert.php',
	      type: 'POST',
	      data: new FormData( this ),
	      processData: false,
	      contentType: false,
		  beforeSend : function(){
				 jQuery('#submit-uaf-font').attr('disabled',true);
				 jQuery('#font_upload_message').attr('class','ok');
				 jQuery('#font_upload_message').html('Uploading Font. It might take few mins based on your font file size.');
			  },
		  success: function(data, textStatus, jqXHR) 
	        {
	            var dataReturn = JSON.parse(data);
				status = dataReturn.global.status;
				msg	   = dataReturn.global.msg;
				
				if (status == 'error'){
					jQuery('#font_upload_message').attr('class',status);
					jQuery('#font_upload_message').html(msg);
				} else {
					woffStatus = dataReturn.woff.status;
					woff2Status = dataReturn.woff2.status;
					if (woffStatus == 'ok' && woff2Status == 'ok'){
						jQuery('#convert_response').val(data);
						jQuery('#font_upload_message').attr('class','ok');
						jQuery('#font_upload_message').html('Font Conversion Complete. Finalizing...');
						jQuery('#submit-uaf-font').attr('disabled',false);
						jQuery('#fontfile').remove();
						e.currentTarget.submit();
					} else {
						jQuery('#font_upload_message').attr('class','error');
						jQuery('#font_upload_message').html('Problem converting font to woff/woff2 formats. Please contact support.');
					}
				}			
	        },
		   error: function(jqXHR, textStatus, errorThrown) 
	        {
	            jQuery('#font_upload_message').attr('class','error');
				jQuery('#font_upload_message').html('Unexpected Error Occured. Please set Use PHP Uploader to Yes from Settings Tab and try again.');
				jQuery('#submit-uaf-font').attr('disabled',false);
	        }	
	    });
	});

	/* PHP UPLOADER */
	jQuery('#open_add_font_form_php').submit(function(){
	    breakValidation = false;
	    jQuery('#open_add_font_form_php .uaf_required').each(function(){
	        if(!jQuery(this).val()){
	            jQuery(this).next('.field_error').show();
	            breakValidation = true;
	            return false;
	        }
	    });    
	    if(breakValidation){return false;}
	});
});