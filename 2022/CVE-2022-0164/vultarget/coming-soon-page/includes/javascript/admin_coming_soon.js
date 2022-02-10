/*for pro feature*/
jQuery(document).ready(function(e) {
    jQuery('.pro_select,.pro_input,.disabled_picker').click(function(){alert("If you want to use this feature upgrade to Coming soon Pro")});
	  jQuery('li.ui-state-default').mousedown(function(){alert("If you want to use this feature upgrade to Coming soon Pro")})
});
 ///////////////					///////////////
 ///////////////	MANY INPUTS		///////////////
 ///////////////					///////////////
 var many_inputs={
		main_element_for_inserting_element:'no_blocked_ips',
		element_name_and_id:'coming_soon_page_showed_ips',
		value_jsone_encoded:'',
		jsone_no_any_problem:1,
		placeholder:'Type Ip Here',
		
		// create all elements
		creates_elements:function(){
			var local_this=this;
			
			try {
				var object_value=JSON.parse(this.value_jsone_encoded);
			}
			catch (err) {
			  this.jsone_no_any_problem=0;
			}
			
			if(this.jsone_no_any_problem){
				for(key in object_value){
					var element=this.creat_single_element(object_value[key]);
					jQuery('#'+this.main_element_for_inserting_element).append(element);
				}
			}

			var element=this.creat_single_element();
			jQuery('#'+this.main_element_for_inserting_element).append(element);
			
			var hidden_element_for_values= '<input type="hidden" value="" id="'+this.element_name_and_id+'" name="'+this.element_name_and_id+'" />'
			jQuery('#'+this.main_element_for_inserting_element).prepend(hidden_element_for_values);
			local_this.insert_value_on_hidden_element();
			
		},

		// function for creating element
		creat_single_element:function(element_value){
			var local_this=this;
			element_value = typeof element_value !== 'undefined' ? element_value : '';
			jQuery('#'+this.main_element_for_inserting_element).append(element=' <div class="emelent_'+this.element_name_and_id+'"> <input type="text" placeholder="'+this.placeholder+'" value="'+element_value+'" /><span class="remove_element remove_element_'+this.element_name_and_id+'"></span>  </div>');
			jQuery(this.get_last_element()).children('span').click(function(){
				local_this.remove_single_element(jQuery(this));
			});
			var next_element_focus=false
			jQuery(this.get_last_element()).children('input').keydown(function(){
					if(event.which == 13)
						next_element_focus=true;
					else
						next_element_focus=false;
			});
			jQuery(this.get_last_element()).children('input').change(function(){
					if(jQuery(jQuery('.emelent_'+local_this.element_name_and_id)).index(jQuery(this).parent())==jQuery('.emelent_'+local_this.element_name_and_id).length-1){
						jQuery('#'+local_this.main_element_for_inserting_element).append(local_this.creat_single_element())
						if(next_element_focus)
							jQuery('.emelent_'+local_this.element_name_and_id).eq(jQuery('.emelent_'+local_this.element_name_and_id).length-1).children('input').focus();
						next_element_focus=false;
					}
					local_this.insert_value_on_hidden_element();
						
			});

		},				
		// function for remove element
		remove_single_element:function(element){
			if(jQuery('.emelent_'+this.element_name_and_id).length>1)
				jQuery(element).parent().remove();
			this.insert_value_on_hidden_element();
		},
		
		// set input json encoded value of all inputs
		insert_value_on_hidden_element:function(){
			var input_value={}
			var z=0;
		
			jQuery('.emelent_'+this.element_name_and_id).each(function(index, element) {
				input_value[z]=jQuery(this).children('input').val();
				z++;
			});
			z--;
			if( input_value[z]=='')
				delete input_value[z];
			jQuery('#'+this.element_name_and_id).val(JSON.stringify(input_value));
		},
		get_last_element:function(){
			return jQuery('#'+this.main_element_for_inserting_element+' .emelent_'+this.element_name_and_id).eq(jQuery('#'+this.main_element_for_inserting_element+' .emelent_'+this.element_name_and_id).length-1);
		}
	}
	
 ///////////////					///////////////
 ///////////////	MANY INPUTS	END	///////////////
 ///////////////					///////////////

/*ADMIN CUSTOMIZE SETTINGS OPEN OR HIDE*/
function get_array_of_opened_elements(){
	var kk=0;
	var array_of_activ_elements=new Array();
	jQuery('#coming_soon_page .main_parametrs_group_div').each(function(index, element) {		
        if(!jQuery(this).hasClass('closed_params')){			
			array_of_activ_elements[kk]=jQuery('#coming_soon_page .main_parametrs_group_div').index(this);
			kk++;
		}
    });
	return array_of_activ_elements;
}
/*countdown*/
function refresh_countdown(){
	var countdown={}
	countdown['days']=jQuery('#coming_soon_page_countdownday').val();
	countdown['hours']=jQuery('#coming_soon_page_countdownhour').val();
	countdown['start_day']=jQuery('#coming_soon_page_countdownstart_day').val();

	jQuery('#coming_soon_page_countdown').val(JSON.stringify(countdown))
}

jQuery(document).ready(function(e) {
	/*countdown*/
	var currentTime = new Date();
	var month = currentTime.getMonth();
	var day = currentTime.getDate();
	var year = currentTime.getFullYear();
	jQuery("#coming_soon_page_countdownstart_day").datepicker({
		inline: true,
		nextText: '→',
		prevText: '←',
		showOtherMonths: true,
		dateFormat: 'dd/mm/yy',
		dayNamesMin: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
		maxDate: new Date(year,month,day)
	});
	/* SECTION OPEN HIDE AND SEAVE*/
    if (typeof(localStorage) != 'undefined' ) {
			active_coming_sections = localStorage.getItem("coming_soon_array_of_activ_section");
			active_coming_sections=JSON.parse(active_coming_sections)
			if(active_coming_sections!=null)
			for(ii=0; ii<active_coming_sections.length;ii++){
				jQuery(jQuery('#coming_soon_page .main_parametrs_group_div').eq(active_coming_sections[ii])).removeClass('closed_params');
			}
	}	
	jQuery('.main_parametrs_group_div > .head_panel_div').click(function(){
		
		if(jQuery(this).parent().hasClass('closed_params')){
			jQuery(this).parent().find('.inside_information_div').slideDown( "normal" )
			jQuery(this).parent().removeClass('closed_params');
			localStorage.setItem("coming_soon_array_of_activ_section", JSON.stringify(get_array_of_opened_elements()));
		}
		else{
			jQuery(this).parent().find('.inside_information_div').slideUp( "normal",function(){jQuery(this).parent().addClass('closed_params'); localStorage.setItem("coming_soon_array_of_activ_section", JSON.stringify(get_array_of_opened_elements()));} )
		}
		
	})
	/*SET CLOR PICKERS*/
	jQuery('.color_option').wpColorPicker()
	
	/*radio Enable Disable*/
	coming_soon_clickable=1;
	jQuery(".cb-enable").click(function(){
		if(!coming_soon_clickable || jQuery(this).hasClass('selected'))
		return;
		coming_soon_clickable=0;
		jQuery('#coming_soon_enable .saving_in_progress').css('display','inline-block');
		jQuery.ajax({
					type:'POST',
					url: coming_soon_ajaxurl+'?action=coming_soon_page_save',
					data: {curent_page:'general_save_parametr',coming_soon_options_nonce:jQuery('#coming_soon_options_nonce').val(),coming_soon_page_mode:'on'},
				}).done(function(date) {
					jQuery('#coming_soon_enable .saving_in_progress').css('display','none');
					if(date==comin_soon_parametrs_sucsses_saved){							
						jQuery('#coming_soon_enable .sucsses_save').css('display','inline-block');
						setTimeout(function(){coming_soon_clickable=1;jQuery('#coming_soon_enable .sucsses_save').hide('fast');jQuery('#save_button').removeClass('padding_loading');jQuery("#save_button").prop('disabled', false);},500);
						
					}
					else{
						jQuery('#coming_soon_enable .error_in_saving').css('display','inline-block');
						jQuery('#coming_soon_enable .error_massage').html(date);							
						
					}
		});
		var parent = jQuery(this).parents('.switch');
		jQuery('.cb-disable',parent).removeClass('selected');
		jQuery(this).addClass('selected');		
	});
	jQuery(".cb-disable").click(function(){
		if(!coming_soon_clickable || jQuery(this).hasClass('selected'))
		return;
		coming_soon_clickable=0;
		jQuery('#coming_soon_enable .saving_in_progress').css('display','inline-block');
		jQuery.ajax({
					type:'POST',
					url: coming_soon_ajaxurl+'?action=coming_soon_page_save',
					data: {curent_page:'general_save_parametr',coming_soon_options_nonce:jQuery('#coming_soon_options_nonce').val(),coming_soon_page_mode:'off'},
				}).done(function(date) {
					jQuery('#coming_soon_enable .saving_in_progress').css('display','none');
					if(date==comin_soon_parametrs_sucsses_saved){							
						jQuery('#coming_soon_enable .sucsses_save').css('display','inline-block');
						setTimeout(function(){coming_soon_clickable=1;jQuery('#coming_soon_enable .sucsses_save').hide('fast');jQuery('#save_button').removeClass('padding_loading');jQuery("#save_button").prop('disabled', false);},500);
						
					}
					else{
						jQuery('#coming_soon_enable .error_in_saving').css('display','inline-block');
						jQuery('#coming_soon_enable .error_massage').html(date);
						
					}
		});
		var parent = jQuery(this).parents('.switch');
		jQuery('.cb-enable',parent).removeClass('selected');
		jQuery(this).addClass('selected');
					
	});
	
	
	
	
	
	
	
	
	
	jQuery(document).ready(function($){
    $('#upload-btn').click(function(e) {
        e.preventDefault();
        var image = wp.media({ 
            title: 'Upload Image',
            // mutiple: true if you want to upload multiple files at once
            multiple: false
        }).open()
        .on('select', function(e){
            // This will return the selected image from the Media Uploader, the result is an object
            var uploaded_image = image.state().get('selection').first();
            // We convert uploaded_image to a JSON object to make accessing it easier
            // Output to the console uploaded_image
            console.log(uploaded_image);
            var image_url = uploaded_image.toJSON().url;
            // Let's assign the url value to the input field
            $('#image_url').val(image_url);
        });
    });
});
	
	jQuery('.upload-button').click(function (e) {
		var self=this;
        e.preventDefault();
        var image = wp.media({ 
            title: 'Upload Image',
            // mutiple: true if you want to upload multiple files at once
            multiple: false
        }).open()
        .on('select', function(e){
            // This will return the selected image from the Media Uploader, the result is an object
            var uploaded_image = image.state().get('selection').first();
            // We convert uploaded_image to a JSON object to make accessing it easier
            // Output to the console uploaded_image
            console.log(uploaded_image);
            var image_url = uploaded_image.toJSON().url;
            // Let's assign the url value to the input field
            jQuery(self).parent().find('.upload').val(image_url);
        });
    });
	
	
	
	
	
	
	
	/* MANY UPLOADS FOR BACKGROUND*/
	jQuery('.add_upload_image_button').click(function(){
		 
		jQuery('.slider_images_div').eq(jQuery('.slider_images_div').length-1).after(jQuery('<div class="slider_images_div"><input type="text" class="upload_many_images" value=""/><input class="upload-button  button" type="button" value="Upload"/><img src="'+comig_soon_plugin_url+'images/remove_element.png" title="remove" class="remove_upload_image"/></div>'))
			initial_last_element_functions(this);
		})
		jQuery('.remove_upload_image').click(function(){
			if(jQuery('.remove_upload_image').length>1)
				jQuery(this).parent().remove()	
	})
 	function initial_last_element_functions(element_of_add){
		jQuery('.remove_upload_image').eq(jQuery('.remove_upload_image').length-1).click(function(){
			if(jQuery('.remove_upload_image').length>1)
				jQuery(this).parent().remove()	
		})
		jQuery(element_of_add).parent().find('.upload-button').eq(jQuery(element_of_add).parent().find('.upload-button').length-1).click(function () {
				window.parent.uploadID = jQuery(this).prev('input');
				/*grab the specific input*/
				formfield = jQuery('.upload').attr('name');
				tb_show('', 'media-upload.php?type=image&height=640&width=1000&TB_iframe=true');
				
				return false;
			});
	}
	function generete_slider_images(){
			var slider_images_url={};
			var i=0;
			jQuery('.upload_many_images').each(function() {
				slider_images_url[i]=jQuery( this ).val();
				i++;
			});
			jQuery('#coming_soon_page_background_imgs').val(JSON.stringify(slider_images_url))
	}
	
	/*SELECT BACKGROUND OTHER TRS HIDDEN*/
	jQuery('.coming_set_hiddens').change(function(){
		jQuery(this).find('option').each(function(index, element) {
            jQuery('.tr_'+jQuery(this).val()).hide();
        });
		 jQuery('.tr_'+jQuery(this).val()).show();
	})
	jQuery('.coming_set_hiddens option').each(function(index, element) {
            jQuery('.tr_'+jQuery(this).val()).hide();
        });
		jQuery('.coming_set_hiddens').each(function(index, element) {
            jQuery('.tr_'+jQuery(this).val()).show();
        });
	
	
	/*slider options*/
	
	jQuery('.coming_number_slider').each(function(index, element) { 
		var loc_this=this;
		var curent_value=jQuery(this).val(); 
		var min_value=jQuery(this).attr('data-min-val');		
		var max_value=jQuery(this).attr('data-max-val');		
		jQuery( jQuery(this).parent().find('.slider_div') ).slider({
			orientation: "horizontal",
			range: "min",
			value: curent_value,
			min: parseInt(min_value),
			max: parseInt(max_value),
			slide: function( event, ui ) {
				if(jQuery(loc_this).hasClass('pro_input')){
					alert("If you want to use this feature upgrade to Coming soon Pro");
					jQuery(this).mouseup();
					return false;
				}
				jQuery( loc_this ).val( ui.value );
			}
		});
	});
	function set_ordering_to_input(ul_element){
		var set_input_value={};
		var i=0;
		jQuery(ul_element).find('li').each(function() {
			set_input_value[i]=jQuery( this ).attr('date-value');
			i++;
		});
		jQuery(ul_element).parent().find('input').val(JSON.stringify(set_input_value))
		
	}
	
	/*sortable content*/
	var askofen=0;
	jQuery(".save_all_section_parametrs").click(function(){
		jQuery(".save_section_parametrs").each(function(index, element) {
			jQuery(this).trigger('click');	
		});
		jQuery('.save_all_section_parametrs').addClass('padding_loading');
		jQuery('.save_all_section_parametrs').prop('disabled', true);		
		jQuery('.save_all_section_parametrs .saving_in_progress').css('display','inline-block');
		setTimeout(check_all_saved(),500);
	})
	function check_all_saved(){
		if(askofen==0){
			jQuery('.save_all_section_parametrs .saving_in_progress').css('display','none');
			jQuery('.save_all_section_parametrs .sucsses_save').css('display','inline-block');
			setTimeout(function(){jQuery('.save_all_section_parametrs .sucsses_save').hide('fast');jQuery('.save_all_section_parametrs').removeClass('padding_loading');jQuery('.save_all_section_parametrs').prop('disabled', false);},1800);
			
		}
		else{
		
			setTimeout(check_all_saved,500);
		}
	}
	/*############ Other section Save click ################*/
	jQuery(".save_section_parametrs").click(function(){
		generete_slider_images()
		if(tinymce.get( 'coming_soon_page_page_message')!=null)
			tinymce.get( 'coming_soon_page_page_message').save()
		if(tinymce.get( 'coming_soon_page_page_message_footer')!=null)
			tinymce.get( 'coming_soon_page_page_message_footer').save()
			
		var coming_soon_curent_section=jQuery(this).attr('id');
		jQuery.each( comin_soon_all_parametrs[coming_soon_curent_section], function( key, value ) {
		   comin_soon_all_parametrs[coming_soon_curent_section][key] =jQuery('#'+key).val() 
		});
		var coming_soon_date_for_post=comin_soon_all_parametrs;
		comin_soon_all_parametrs[coming_soon_curent_section]['curent_page']=coming_soon_curent_section;
		comin_soon_all_parametrs[coming_soon_curent_section]['coming_soon_options_nonce']=jQuery('#coming_soon_options_nonce').val();
		
		
		jQuery('#'+coming_soon_curent_section).addClass('padding_loading');
		jQuery('#'+coming_soon_curent_section).prop('disabled', true);		
		jQuery('#'+coming_soon_curent_section+' .saving_in_progress').css('display','inline-block');
		
		askofen++;
		jQuery.ajax({
					type:'POST',
					url: coming_soon_ajaxurl+'?action=coming_soon_page_save',
					data: comin_soon_all_parametrs[coming_soon_curent_section],
				}).done(function(date) {
					jQuery('#'+coming_soon_curent_section+' .saving_in_progress').css('display','none');
					if(date==comin_soon_parametrs_sucsses_saved){							
						jQuery('#'+coming_soon_curent_section+' .sucsses_save').css('display','inline-block');
						setTimeout(function(){coming_soon_clickable=1;jQuery('#'+coming_soon_curent_section+' .sucsses_save').hide('fast');jQuery('#'+coming_soon_curent_section+'.save_section_parametrs').removeClass('padding_loading');jQuery('#'+coming_soon_curent_section).prop('disabled', false);},1800);
						askofen--;
					}
					else{
						jQuery('#'+coming_soon_curent_section+' .error_in_saving').css('display','inline-block');
						jQuery('#'+coming_soon_curent_section).parent().find('.error_massage').eq(0).html(date);
						
					}
		});
	});

});