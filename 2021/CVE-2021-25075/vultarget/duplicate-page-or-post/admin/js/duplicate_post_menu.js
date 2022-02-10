
function wpdevart_duplicate_post_save_admin_menu_parametrs(){
	jQuery('#wpdevart_save_in_databese').addClass('padding_loading');
	jQuery('#wpdevart_save_in_databese').prop('disabled', true);		
	jQuery('#wpdevart_save_in_databese .saving_in_progress').css('display','inline-block');
	jQuery('#wpdevart_save_in_databese').attr('data-clickabel','no');
	var clickabel
	var wpdevart_data={};
	jQuery("#wpdevart_parametrs_table > tbody > tr input").each(function(){
		wpdevart_data[jQuery(this).attr("id")]=jQuery(this).val();
	});
	jQuery("#wpdevart_parametrs_table > tbody > tr select").each(function(){
		wpdevart_data[jQuery(this).attr("id")]=jQuery(this).val();
	});	
	
	jQuery.ajax({
		type:'POST',
		url: wpdevart_js_object.ajax_url+'?action=wpdevart_duplicate_post_parametrs_save_in_db',
		data: wpdevart_data,
	}).done(function(date) {
		jQuery('#wpdevart_save_in_databese .saving_in_progress').css('display','none');
		if(date=="1"){						
			jQuery('#wpdevart_save_in_databese .sucsses_save').css('display','inline-block');
			setTimeout(function(){jQuery('#wpdevart_save_in_databese').attr('data-clickabel','yes');jQuery('#wpdevart_save_in_databese .sucsses_save').hide('fast');jQuery('#wpdevart_save_in_databese').removeClass('padding_loading');jQuery('#wpdevart_save_in_databese').prop('disabled', false);	},1800);
			jQuery('#wpdevart_save_in_databese').attr('data-clickabel','no');
		}
		else{
			jQuery('#wpdevart_save_in_databese .error_in_saving').css('display','inline-block');
			jQuery('#wpdevart_save_in_databese').parent().find('.error_massage').eq(0).html(date);
		}
	});
}
jQuery(document).ready(function(){
		jQuery("#wpdevart_save_in_databese").click(function(){
			if(jQuery('#wpdevart_save_in_databese').attr('data-clickabel')=="yes"){
				wpdevart_duplicate_post_save_admin_menu_parametrs();
			}
		})
	})