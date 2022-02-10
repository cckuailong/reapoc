function SavedataByAjaxRequest(data, method) {
    return jQuery.ajax({
        url: frontendajax.ajaxurl,
        type: method,
        data: data,
        cache: false
    });
}

function lfbErrorCheck(){
    var termaccept = true;
    if(jQuery('.term_accept').length){
        var termaccept = false;
        var numItems = jQuery('.term_accept').length;
        jQuery('.term_accept').css("outline", "2px solid #f50808");

        jQuery("input:checkbox[class=term_accept]:checked").each(function () {
                --numItems;
            jQuery('#'+jQuery(this).attr("id")).css("outline", "none");
            if(numItems==false){
                termaccept = true;
            }
            });
    }

     return termaccept;
}



jQuery(document).ready(function(){
var dateToday = new Date();

    jQuery('.lf-jquery-datepicker').datepicker({
            dateFormat: "mm/dd/yy",
            showOtherMonths: true,
            selectOtherMonths: true,
            autoclose: true,
            changeMonth: true,
            changeYear: true,
            gotoCurrent: true,
            yearRange:  (dateToday.getFullYear()-200) +":" + (dateToday.getFullYear()),
        });
});

 var CaptchaCallback = function(){  
     var recaptcha = jQuery(".g-recaptcha").attr('data-sitekey'); 
      jQuery('.g-recaptcha').each(function(){
        grecaptcha.render(this,{
            'sitekey' : recaptcha,
            'callback' : correctCaptcha,
            });
      })
  };

 var correctCaptcha = function(response) {
 };
 function lfb_upload_button(newthis){
    $id = jQuery(newthis).attr('filetext');
    $var = jQuery(newthis).val();

    $newValue = $var.replace("C:\\fakepath\\", "");
    
     jQuery("."+$id).val($newValue);
   //jQuery("."+$id).val($var);
}
/*
 *Save form data from front-end
 */
 // inser form data
function lfbInserForm(element,form_id,uploaddata=''){
            var this_form_data = element.serialize();
            if(uploaddata!=''){
            this_form_data = this_form_data + '&' + uploaddata;
            } 

            form_data = this_form_data + "&action=Save_Form_Data";

        SavedataByAjaxRequest(form_data, 'POST').success(function(response) {    
            element.find('#loading_image').hide();;
            if (jQuery.trim(response) == 'invalidcaptcha') {

            element.find(".leadform-show-message-form-"+form_id).append("<div class='error'><p>Invalid Captcha</p></div>");
                grecaptcha.reset();

            } else if (jQuery.trim(response) == 'inserted') {
                var redirect = jQuery(".successmsg_"+form_id).attr('redirect');
                    element.siblings(".successmsg_"+form_id).css('display','block');
                    jQuery('#lfb-submit').trigger('click');
                    element.hide();
                    if (typeof grecaptcha === "function") { 
                        grecaptcha.reset();
                    }
                if(jQuery.trim(redirect)!=''){
                    window.location.href = redirect;
                }
            }
        });
    }

function lfbfileUpload(element,form_id){
    var fd = new FormData();
    var file = element.find('input[type="file"]');
        for (var i = 0, len = file.length; i < len; i++) {
            if(file[i].files[0]!=undefined){
                //console.log(file[i].name);
                //console.log(file[i].files[0].name);
                fd.append(file[i].name, file[i].files[0]);
            }
        }
    fd.append('action', 'fileupload');  
    fd.append('fid', form_id);  
    jQuery.ajax({
        type: 'POST',
        url: frontendajax.ajaxurl,
        data: fd,
        contentType: false,
        processData: false,
        success: function(response){
           var uploaddata  = jQuery.trim(response);
            lfbInserForm(element,form_id,uploaddata);
        }
    });
}

//captcha validation check
function lfbCaptchaCheck(element,form_id){
        var captcha_res = element.find(".g-recaptcha-response").val();
    form_data = "captcha_res="+captcha_res+"&action=verifyFormCaptcha";
    SavedataByAjaxRequest(form_data, 'POST').success(function(response) {
    element.find('#loading_image').hide();
        if (jQuery.trim(response) == 'Yes') {
        if(element.find('.upload-type').length){
         lfbfileUpload(element,form_id);
        }else{
         lfbInserForm(element,form_id);
        }
         } else {
          element.find(".leadform-show-message-form-"+form_id).append("<div class='error'><p>Invalid Captcha</p></div>");
          grecaptcha.reset();
        }
    });
}


// form submit
jQuery(document).on('submit', "form.lead-form-front", function(event) {
    
     if(!lfbErrorCheck()){
      return false;
    }
    event.preventDefault(); 
    var element = jQuery(this);
    element.find('input[type=submit]').prop('disabled', true);
    var form_id = element.find(".hidden_field").val();   
    var captcha_status = element.find(".this_form_captcha_status").val();
    
    element.find('#loading_image').show();  
    element.find(".leadform-show-message-form-"+form_id).empty();

    if(captcha_status=='disable'){
        if(element.find('.upload-type').length){
            lfbfileUpload(element,form_id);
        } else{
            lfbInserForm(element,form_id);
        }
     } else {
            lfbCaptchaCheck(element,form_id);
    }
 element.find('input[type=submit]').prop('disabled', false);
});

// required-field-function
jQuery(function(){
    var requiredCheckboxes = jQuery('.lead-form-front :checkbox[required]');
    requiredCheckboxes.change(function(){
        if(requiredCheckboxes.is(':checked')) {
            requiredCheckboxes.removeAttr('required');
        }
        else {
            requiredCheckboxes.attr('required', 'required');
        }
    });
});