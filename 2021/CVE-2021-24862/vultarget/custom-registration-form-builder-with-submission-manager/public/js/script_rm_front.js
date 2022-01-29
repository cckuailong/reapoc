/**
 * FILE for all the javascript functionality for the front end of the plugin
 */
/* For front end OTP widget */
var rm_ajax_url = rm_ajax.url;
var rm_validation_attr = ['data-rm-valid-username','data-rm-valid-email'];
var rm_ajax_security = rm_ajax.security;
var rm_js_data;

function rmInitGoogleApi() {
    var rm_init_map_containers = setInterval(function(){
            if (typeof rmInitMap === 'function') {
             var rm_all_maps = jQuery(".rm-map-controls-uninitialized");
             var i;
             var curr_id = '';
             if(rm_all_maps.length>0) clearInterval(rm_init_map_containers);
             for (i = 0; i < rm_all_maps.length; i++) { 
                if(jQuery(rm_all_maps[i]).is(':visible')){
                curr_id = rm_all_maps[i].getAttribute("id");
                jQuery(rm_all_maps[i]).removeClass("rm-map-controls-uninitialized");
                rmInitMap(curr_id);
             }
            }
        }
        }, 100);
}

// This is a dummy request to exchange cookies after auto login
function rm_send_dummy_ajax_request(url){
    var data = {'action' : 'rm_dummy_refresh'};
    jQuery.post (rm_ajax_url, data, function(){
        if(url)
           window.location=url;
        else{
             //jQuery("#rm_ajax_login").hide();
             //jQuery("#rm_ajax_after_login").show();
        }
    });            
}

function scroll_down_end(element) {

    if (element.scrollTop + element.offsetHeight >= element.scrollHeight)
    {
        var div = jQuery(element).parent().siblings();
        jQuery(div).children().removeAttr('disabled');

    }
    else
    {
    var text_height = jQuery(element).css('font-size').replace('px', '');
        text_height = Math.ceil(parseInt(text_height));
        var field_height = Math.floor(jQuery(element).height());
        var line_per_field = Math.floor(jQuery(element).height() / text_height);
        var text = jQuery(element).val();
        var lines = text.split(/\r|\r\n|\n/);
        var count = text.length;
        
        var count = count / field_height;
        
        var count = Math.floor(count);
        
        lines = lines.length;
       count =count *line_per_field;
        if (lines > count)
            count = lines;
     
        if (count <= line_per_field)
        {
            count = 1;
        }
        
        if ((count * field_height) <= field_height) {

            var div = jQuery(element).parent().siblings();

               jQuery(div).children().removeAttr('disabled');

        }
    }
   
}

var rm_call_otp = function (event,elem,opType) {
   
    if (event.keyCode == 13 || opType=="submit") {

        var otp_key_status = jQuery(elem + " #rm_otp_login #rm_otp_enter_otp .rm_otp_kcontact").is(":visible");
        var user_key_status = jQuery(elem + " #rm_otp_login #rm_otp_enter_password .rm_otp_kcontact").is(":visible");
        
        var data = {
            'action': 'rm_set_otp',
            'rm_otp_email': jQuery(elem + " #rm_otp_econtact").val(),
            'rm_slug': 'rm_front_set_otp'
        };
        if (otp_key_status)
        {
            data.rm_otp_key = jQuery(elem + " #rm_otp_enter_otp .rm_otp_kcontact").val();
        }else
            if(user_key_status){
                if(jQuery(elem + " #rm_rememberme").is(':checked'))
                    data.rm_remember = 'yes';
                data.rm_username = jQuery(elem + " #rm_username").val();
                data.rm_user_key = jQuery(elem + " #rm_otp_enter_password .rm_otp_kcontact").val();
            }
            
        jQuery(elem + " .rm_hide_when_loader").hide();
        jQuery(elem + " .rm_loader").show();
        jQuery.post(rm_ajax_url, data, function (response) {
            jQuery(elem + " .rm_loader").hide();
            jQuery(elem + " .rm_hide_when_loader").show();
            var responseObj = jQuery.parseJSON(response);
            if (responseObj.error == true) {
                jQuery(elem + " #rm_otp_login .rm_f_notifications .rm_f_error").hide().html(responseObj.msg).slideDown('slow');
                jQuery(elem + " #rm_otp_login .rm_f_notifications .rm_f_success").hide();
                /*jQuery("#rm_otp_login " + responseObj.hide).hide('slow');*/
            } else {
                jQuery(elem + " #rm_otp_login .rm_f_notifications .rm_f_error").hide();
                jQuery(elem + " #rm_otp_login .rm_f_notifications .rm_f_success").hide().html(responseObj.msg).slideDown('slow');
                jQuery(elem + " #rm_otp_login " + responseObj.show).show();
                jQuery(elem + " #rm_otp_login " + responseObj.hide).hide();

                if(responseObj.username){
                    jQuery(elem + " #rm_username").val(responseObj.username);
                }else
                    jQuery(elem + " #rm_username").val('');
                
                if (responseObj.reload) {
                    location.reload();
                }
                
                if (responseObj.redirect) {
                    window.location = responseObj.redirect;
                }
                
            }
        });
    }
};

/*All the functions to be hooked on the front end at document ready*/
jQuery(document).ready(function () {
    if(jQuery('#id_rm_tp_timezone').length > 0)
       jQuery('#id_rm_tp_timezone').val(-new Date().getTimezoneOffset()/60);
    var tab_container= jQuery('.rm_tabbing_container');
    if(tab_container.length>0){
        tab_container.tabs();
    }
    jQuery('.rm_terms_textarea').each(function () {
        var a = jQuery(this).children('textarea');
          if (a.length > 0)
                scroll_down_end(a);
       });
       
    jQuery(".rm_floating_action").click(function(){
       jQuery(".rm_floating_box").toggle('medium');
   });
    
    if(jQuery("#rm_f_mail_notification").length>0){
        jQuery("#rm_f_mail_notification").show('fast', function () {
            jQuery("#rm_f_mail_notification").fadeOut(3000);
        });     
    }

//Code for async login
/*
    jQuery( document ).on( "click", ".rm_login_btn", function(event) {
        var btnLbl = jQuery(this).val();
        var formvalidid  = '';
        if(jQuery("#rm_login_form_1").length){
            formvalidid = 'rm_login_form_1';
        } else if(jQuery("#rm_otp_form_1").length){
            formvalidid = 'rm_otp_form_1';
        }
        if(validate_empty_required_fields(formvalidid)){
            jQuery('.rm_login_btn').prop('disabled', true);
            jQuery('.rm_login_btn').prop('value', '');
            jQuery('.rm_login_btn').toggleClass('rm-login-btn-loader');
            event.preventDefault();
            var form = jQuery("#"+formvalidid);
            jQuery.post( form.attr('action'), form.serialize()).done(function( data ) {
                if(data.includes("alert-error")){
                    // remove previous errors
                    jQuery('.alert-error').remove();
                    // add errors
                    var elements = jQuery(data);
                    var found = jQuery('.alert-error', elements);
                    jQuery('input[name="rm_slug"]').after(found);
                    jQuery('.rm_login_btn').prop('disabled', false);
                    jQuery('.rm_login_btn').prop('value', btnLbl);
                    jQuery('.rm_login_btn').toggleClass('rm-login-btn-loader');
                } else if(data.includes("rm_otp_form_1")) {
                    document.write(data);
                } else{
                    // request for redirection url
                    var ndata= {
                        'action': 'rm_get_after_login_redirect',
                    };
                    jQuery.post(rm_ajax_url, ndata, function(response) {
                        resp = JSON.parse(response);
                        if(resp['redirect'] != '') {
                            window.location.href = resp['redirect'];
                        } else
                            alert("Error occured during redirection");
                    });
                }
            });
        }
    });
*/
});

function setup_payment_method_visibility(payment_method_type,form_id,form_no) {

    switch (payment_method_type)
    {
        case 'paypal':
            jQuery('#rm_stripe_fields_container_'+form_id+'_'+form_no).slideUp();
            break;
        case 'stripe':
            jQuery('#rm_stripe_fields_container_'+form_id+'_'+form_no).slideDown();
            break;
    }
}


function rm_toggle_tel_error(valid,el,msg){
    jQuery("." + el.prop('id') + "-error").remove();
    var inputValue= el.val();
    var form_con=el.closest('form');
    setTimeout(function(){ form_con.find('[type=submit]').prop('disabled',false);},500);
    if(inputValue.length==0 && el.prop('required')){
        return false;
    }
    
    if(!el.is(':visible'))
    {
        return false;
    }
    
    if(el.length>0 && inputValue.length>0){
         if(!valid){
             if(el.closest(".rminput").length>0){
                setTimeout(function(){el.closest(".rminput").append('<div><label class="'+ el.prop('id') +'-error rm-form-field-invalid-msg">' + msg + '</label></div'); },500);
                         return true;
             }
             else{
                setTimeout(function(){el.closest(".intl-tel-input").append('<div><label class="'+ el.prop('id') +'-error rm-form-field-invalid-msg">' + msg + '</label></div'); },500); 
                         return false;
             }  
         }
     }
     return false;
}

function rm_toggle_tel_wc_error(valid,el,msg){
    if(el.closest('.rmwc-input').length==0)
        return true;
    
    
    jQuery("." + el.prop('id') + "-error").remove();
    var inputValue= el.val();
    var form_con=el.closest('form');
    setTimeout(function(){ form_con.find('[type=submit]').prop('disabled',false);},500);
    if(inputValue.length==0 && el.prop('required')){
        return true;
    }
    
    if(!el.is(':visible'))
    {
        return true;
    }
    
    if(el.length>0 && inputValue.length>0){
         if(!valid){
             if(el.closest(".rminput").length>0){
                setTimeout(function(){el.closest(".rminput").append('<div><label class="'+ el.prop('id') +'-error rm-form-field-invalid-msg">' + msg + '</label></div'); },500);
                         return false;
             }
             else{
                setTimeout(function(){el.closest(".intl-tel-input").append('<div><label class="'+ el.prop('id') +'-error rm-form-field-invalid-msg">' + msg + '</label></div'); },500); 
                         return true;
             }  
         }
     }
     return true;
}

/*
function validate_empty_required_fields(formId) {
    let allAreFilled = true;
    document.getElementById(formId).querySelectorAll("[required]").forEach(function(i) {
        if (!allAreFilled) allAreFilled = false;
        if (!i.value) allAreFilled = false;
        if (i.type === "radio") {
            let radioValueCheck = false;
            document.getElementById("myForm").querySelectorAll(`[name=${i.name}]`).forEach(function(r) {
                if (r.checked) radioValueCheck = true;
            })
            allAreFilled = radioValueCheck;
        }
    })
    if (!allAreFilled) {
        allAreFilled = false;
    }

    return allAreFilled;
}
*/

/*launches all the functions assigned to an element on click event*/

function performClick(elemId, s_id, f_id) {
    var elem = document.getElementById(elemId);
    if (elem && document.createEvent) {
        var evt = document.createEvent("MouseEvents");
        evt.initEvent("click", true, false);
        elem.dispatchEvent(evt);
    }
}


function rm_append_field(tag, element_id) {
    jQuery('#' + element_id).append("<" + tag + " class='appendable_options'>" + jQuery('#' + element_id).children(tag + ".appendable_options").html() + "</" + tag + ">");
}

function rm_delete_appended_field(element, element_id) {
    if (jQuery(element).parents("#".element_id).children(".appendable_options").length > 1)
        jQuery(element).parent(".appendable_options").remove();
}


function rm_get_country_code_by_name(country_list,selected_country){
    var regex = new RegExp(selected_country + "\[[A-Z{{2}}\]",'i');
    if(selected_country.toLowerCase()=='india'){
        return 'in';
    }
    else if(selected_country.toLowerCase()=='' || selected_country.toLowerCase()=='us' || selected_country.toLowerCase()=='united_states'){
        return 'us';
    }
    else if(selected_country.toLowerCase()=='canada'){
        return 'ca';
    }
   
    var country_code='';
    for(country in country_list)
    {
        var found= country.search(regex);
        if(found>=0){
            var index= country.search(/\[[A-Z]{2}\]/i);
      
            if(index>=0) 
            { 
                country_code= country.substr(index+1,2).toLowerCase();
                return country_code;
            }
        }
    }
    return country_code;
}

var rm_toggleFloatingScreens= function(screen_name){
   jQuery("#" + screen_name).animate({width:'toggle'},300,"linear");
   /*jQuery("#" + screen_name).slideToggle('medium');*/
   jQuery('.rm_floating_screens .rm_hidden').not("#" + screen_name).hide();
}

var rm_closeFloatingScreens= function(screen_name){
   jQuery("#" + screen_name).animate({width:'toggle'},300,"linear",function(){
        jQuery(this).hide();
   });
   /*jQuery('.rm_floating_screens .rm_hidden').hide('medium');*/
}

var rm_empty_tp_entry = function(tpid){
    jQuery("#" + tpid).val('');
}
var rm_user_exists= function(el,url,data){
    var valid;
    jQuery.post(url, data, function(response) {
        elementId= jQuery(el).attr('id');
        jQuery("." + elementId + "-error").remove();
        response= JSON.parse(response);
        if(response.status){
           /* if(!jQuery("#" + elementId + "-error").length)*/
            jQuery(el).parent(".rminput").append('<label class="'+ elementId +'-error rm-form-field-invalid-msg">' + response.msg + '</label>');  
            jQuery(el).attr(data.attr,0);
            if (jQuery('#rm-menu').length > 0) {
             jQuery("#rm-menu").css('transform', 'translateY(0px)');
             }
            
        }
        else{    
             jQuery("." + elementId + "-error").remove();    
             jQuery(el).attr(data.attr,1); 
            }
     });
}

var rm_get_state= function(el,url,data){ 
    jQuery.post(url, data, function(response) {
        elementId= jQuery(el).attr('id');
        console.log(response);
        //console.log(data.state_field_id);
        var name = jQuery('#'+data.state_field_id+'_attrs').attr('data-name');
        var placeholder = jQuery('#'+data.state_field_id+'_attrs').attr('data-placeholder');
        var class_val = jQuery('#'+data.state_field_id+'_attrs').attr('data-class');
        var style = jQuery('#'+data.state_field_id+'_attrs').attr('data-style');
        console.log('--'+style);
        var required = jQuery('#'+data.state_field_id+'_attrs').attr('data-required');
        var required_attr = '';
        if(required!=''){
            required_attr = 'required="required"';
        }
        var value = jQuery('#'+data.state_field_id+'_attrs').attr('data-value');
        if(response!='' && response!=0){
            jQuery('#'+data.state_field_id).html('<select name="'+name+'" style="'+style+'" class="'+class_val+'" placeholder="'+placeholder+'" '+required_attr+'>'+response+'</select>');
        }else{
            jQuery('#'+data.state_field_id).html('<input type="text" name="'+name+'" style="'+style+'" placeholder="'+placeholder+'" class="'+class_val+'" value="'+value+'" '+required_attr+'>');
        }
    });
}

/* Intializing the necessary scripts*/
jQuery(document).ready(function(){
    if(jQuery(".data-conditional").length>0)
    jQuery(".data-conditional").conditionize({});
});


jQuery(document).ready(function () {
    jQuery(".rm_mapv_container").each(function() {
    if (jQuery(this).width() < 600) {
        jQuery(this).addClass("rm_mapvsm");


    } else {
        jQuery(this).addClass("rm_mapvlg");

    }
    });
});


/*  Login Widget Popup */

   jQuery(document).ready(function(){
   
    var rmColor = jQuery(".rm_widget_container").find("a").css('color');
      jQuery(".widget_rm_login_btn_widget .rm_widget_container div a.rm-button").css("border-color", rmColor);
      
    var pgWidget_ParentWidth =  jQuery('.widget_rm_login_btn_widget').width();
        if (pgWidget_ParentWidth < 280) {
        jQuery('.widget_rm_login_btn_widget').addClass('rm-narrow-widget');
   
    }
     
   
     
   });
   
   var resizeboxes  = function() {
    if (jQuery("#rm_front_sub_tabs").width() < 800)
    {
        jQuery("#rm_front_sub_tabs").addClass("rmNarrow");
        jQuery("#rm_front_sub_tabs").removeClass("rmWide");
    }
    else
    {
        jQuery("#rm_front_sub_tabs").removeClass("rmNarrow");
        jQuery("#rm_front_sub_tabs").addClass("rmWide");
    }
};

resizeboxes();

jQuery(window).load(function(){
    resizeboxes();   
});
   
   
function handle_data(email,first_name,type) {
	var data = {
			'action': 'rm_login_social_user',
			'email': email,
            'type': type,
            'security': rm_ajax_security
		};
    
		/* since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php*/
		jQuery.post(rm_ajax_url, data, function(response) {
                    resp = JSON.parse(response);
                    if(resp['code'] == 'allowed') {
                        if(resp['msg'].length)
                            location = resp['msg'];  
                        else
                            location.reload();
                    } else
                        alert(resp['msg']);
		});
	}
 
   jQuery(document).ready(function(){
       
    jQuery(".rm_form_field_type_richtext").parent() .addClass("rm-richtext-fw");   
       
    }); 

// Making sure action is empty on login form
jQuery(document).ready(function () {
    jQuery("form#rm_login_form_1").submit(function(event) {
        jQuery(this).attr("action","");
    });
});

//User Area Block Resizer

   var resize_submission_boxes  = function() {
       
  console.log(jQuery(".rm-user-details-card").width());
  
    if (jQuery(".rm-user-details-card").width() < 450){
            
        jQuery(".rm-user-details-card").addClass("rm-narrow-card");
        jQuery(".rm-user-details-card").removeClass("rm-wide-card");
    }
    
    else{
         
        jQuery(".rm-user-details-card").removeClass("rm-narrow-card");
        jQuery(".rm-user-details-card").addClass("rm-wide-card");
       
    }
};

jQuery(window).on('resize', resize_submission_boxes);

jQuery(document).ready(function () {
    setTimeout(function () {
        resize_submission_boxes();
    }, 500);

});

//User Area Block Resizer End