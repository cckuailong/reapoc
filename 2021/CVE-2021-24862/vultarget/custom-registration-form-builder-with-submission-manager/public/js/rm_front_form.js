/*Functions for displaying helptext tooltip*/
function rmHelpTextIn2(a) {
    var helpTextNode = jQuery(a).siblings(".rmnote");
    var fieldWidth = jQuery(a).children().innerWidth();
    var fieldHeight = jQuery(a).parent().outerHeight();
    var topPos = fieldHeight;
    //var id = setInterval(frame, 1);
    jQuery(helpTextNode).css("width", fieldWidth + "px");
    jQuery(helpTextNode).css('top', topPos + "px");
    helpTextNode.fadeIn(500);
    /*function frame() {
        if (topPos === fieldHeight) {
            clearInterval(id);
        } else {
            topPos++;
            helpTextNode.css('top', topPos + "px");
            }
        }*/
    } 

function rmHelpTextOut2(a) {
    jQuery(a).siblings(".rmnote").fadeOut('fast');
}

function rmHexToRgb(hex) {
    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}

function rmFontColor(rmColor) {
        jQuery(".rmnote").css("background-color", rmColor);
        jQuery(".rmprenote").css("border-bottom-color", rmColor);
        if(rmColor.indexOf('#')>=0){ // Convert hex code into RGB
            rgbColor= rmHexToRgb(rmColor);
            rmColor= 'rgb(' + rgbColor.r + ',' + rgbColor.g + ',' + rgbColor.b + ')';
        }
        if(rmColor==null || rmColor===undefined){
            return;
        }
        var rmRgb = rmColor.substr(3);
        rmRgb = rmRgb.split(',');
        rmRgb[0] = parseFloat((rmRgb[0].substr(1)) / 255);
        rmRgb[1] = parseFloat(rmRgb[1] / 255);
        rmRgb[2] = parseFloat((rmRgb[2].substring(0, rmRgb[2].length-1)) / 255);
        rmRgb.sort(function(a, b){return a-b});
        rmLum = Math.ceil(((rmRgb[2] + rmRgb[1]) * 100) / 2);
        if (rmLum > 80) {jQuery(".rmnote").css("color", "black");}
}

/* Functions for automatically styling certain uncommon fields*/
function rmAddStyle(rmElement) {
    
    var rmHasText = jQuery(".rminput").children("input[type='text']").length;
        if (jQuery.isNumeric(rmHasText) && rmHasText > 0) {
            var rmInputClass = jQuery(".rminput").children("input[type='text']").attr('class');
            if (rmInputClass != undefined) {
                jQuery(".rm-input-control").addClass(rmInputClass);
            }
        } 
    //var rmInputHeight = jQuery(".rm-input-control").outerHeight();
    //var rmInputBorder = jQuery(".rm-input-control").css("border");
    //if (rmInputBorder.search("inset") != -1) {rmInputBorder = "1px solid rgba(150,150,150,0.4)";}
    //var rmInputRadius = jQuery(".rm-input-control").css("border-radius");
    //* var rmInputBg = jQuery(".rm-input-control").css("background"); */
    //if (rmInputHeight < 36) {rmInputHeight = 36}; 
    //jQuery(rmElement).css({"height": rmInputHeight + "px", /*--"border": rmInputBorder, --*/"border-radius": rmInputRadius /*, "background": rmInputBg */});
}


function load_js_data(){
    var data = {
        'action': 'rm_js_data'
    };

    jQuery.post(rm_ajax_url, data, function (response) {
       rm_js_data= JSON.parse(response);
       initialize_validation_strings();
    });

}

function initialize_validation_strings(){
    if(typeof jQuery.validator != 'undefined'){
        rm_js_data.validations.maxlength = jQuery.validator.format(rm_js_data.validations.maxlength);
        rm_js_data.validations.minlength = jQuery.validator.format(rm_js_data.validations.minlength);
        rm_js_data.validations.max = jQuery.validator.format(rm_js_data.validations.max);
        rm_js_data.validations.min = jQuery.validator.format(rm_js_data.validations.min);
        jQuery.extend(jQuery.validator.messages,rm_js_data.validations); 
    }
}

function rm_init_total_pricing() {
    
    var ele_rm_forms = jQuery("form[name='rm_form']");
    if(ele_rm_forms.length > 0) {
        ele_rm_forms.each(function(i) {
            var el_form = jQuery(this);
            var form_id = el_form.attr('id');     
            var price_elems = el_form.find('[data-rmfieldtype="price"]');
            if(price_elems.length > 0) {
                
                rm_calc_total_pricing(form_id);
                
                price_elems.change(function(e){       
                    rm_calc_total_pricing(form_id);
                });            
                                
                /*Get userdef price fields*/
                var ud_price_elems = price_elems.find('input[type="number"]');
                if(ud_price_elems.length > 0) {
                    ud_price_elems.keyup(function(e){       
                        rm_calc_total_pricing(form_id);
                    });
                }
                
                /*Get quantity fields*/
                var qty_elems = el_form.find('.rm_price_field_quantity');
                if(qty_elems.length > 0) {
                    qty_elems.keyup(function(e){       
                        rm_calc_total_pricing(form_id);
                    });
                    qty_elems.change(function(e){       
                        rm_calc_total_pricing(form_id);
                    });
                }
                
                /*Get role selector field if any*/
                var roles_elems = el_form.find('input[name="role_as"]');
                if(roles_elems.length > 0) {
                    roles_elems.change(function(e){       
                        rm_calc_total_pricing(form_id);
                    });
                }
            }
        });
    }    
}

function rm_calc_total_pricing(form_id){
    var ele_form = jQuery('#'+form_id);
    var price_elems = ele_form.find('[data-rmfieldtype="price"]');
    if(price_elems.length > 0) {
        var tot_price = 0;
        price_elems.each(function(i){
           var el = jQuery(this);
           var qty = 1;
           if(el.prop("tagName") == "INPUT") {
                var el_type = el.attr('type');
                var el_name = el.attr('name');
                switch(el_type){
                    case 'text':     
                        var ele_qty = ele_form.find(':input[name="'+el_name+'_qty"]');
                         
                         if(ele_qty.length > 0) {
                             qty = ele_qty.val();
                         }
                         /* Let it fall through for price calc */
                    case 'hidden':
                        ele_price = el.data("rmfieldprice");
                        if(!ele_price)
                            ele_price = 0;
                        break;

                    case 'number':
                         ele_price = el.val();
                         if(!ele_price)
                             ele_price = 0;
                         var ele_qty = ele_form.find(':input[name="'+el_name+'_qty"]');
                         
                         if(ele_qty.length > 0) {
                             qty = ele_qty.val();
                         }
                        break;

                    case 'checkbox':
                        if(el.prop("checked")){
                         ele_val = el.val();
                         price_val = el.data("rmfieldprice");
                         ele_price = price_val[ele_val];
                         if(!ele_price)
                             ele_price = 0;
                         el_name = el_name.slice(0,-2); /* remove [] */
                         var ele_qty = ele_form.find(':input[name="'+el_name+'_qty['+ele_val+']"]');                         
                            if(ele_qty.length > 0) {
                                qty = ele_qty.val();
                            }
                         }
                         else
                             ele_price = 0;  
                         
                         
                         
                        break;
                        
                    default:
                        ele_price = 0;
                        break;
                }
            } else if(el.prop("tagName") == "SELECT") {
                ele_val = el.val();
                var el_name = el.attr('name');
                if(!ele_val){
                    ele_price = 0;                      
                } else {
                    price_val = el.data("rmfieldprice");
                    ele_price = price_val[ele_val];
                    if(!ele_price)
                        ele_price = 0;  
                    
                    var ele_qty = ele_form.find(':input[name="'+el_name+'_qty"]');
                         
                    if(ele_qty.length > 0) {
                        qty = ele_qty.val();
                    }
                }
            } else {
                ele_price = 0;
            }   
            qty = parseInt(qty);
            if(isNaN(qty))
                qty = 1;
           tot_price += parseFloat(ele_price)*qty;
        });     
        
        /*Add cost of paid role*/
        var role_cost = 0;
        var ele_paidrole = jQuery("#paid_role"+form_id.substr(4));
        if(ele_paidrole.length > 0) {
            var role_data = ele_paidrole.data("rmcustomroles");
            var user_role = ele_paidrole.data("rmdefrole");
            if(!user_role) {
                var roles_elems = ele_form.find('input[name="role_as"]');
                if(roles_elems.length > 0) {
                    user_role = jQuery('input[name="role_as"]:checked', '#'+form_id).val();
                    if(typeof user_role == 'undefined')
                        user_role = '';
                }
            }
            
            if(user_role) {
                if(typeof role_data[user_role] != 'undefined' && role_data[user_role].is_paid)
                    role_cost = parseInt(role_data[user_role].amount);
                if(isNaN(role_cost))
                    role_cost = 0;
            }
        }
        tot_price += role_cost;
        var tot_price_ele = jQuery('#'+form_id).find(".rm_total_price,.rm-total-price-widget");
        if(tot_price_ele.length > 0) {
            var price_formatting = tot_price_ele.data("rmpriceformat");
            var f_tot_price = '';
            if(price_formatting.pos == 'after')
                f_tot_price = tot_price.toFixed(2) + price_formatting.symbol;
            else
                f_tot_price = price_formatting.symbol + tot_price.toFixed(2);

            tot_price_ele.html(price_formatting.loc_total_text.replace("%s",f_tot_price));
        }
    }
}

function rm_register_stat_ids() {
    var form_ids = [];
    
    jQuery("form[name='rm_form']").each(function(){
        $this = jQuery(this);
        if($this.find("input[name='stat_id']").length > 0)
            form_ids.push($this.attr('id'));
    })
    
    var data = {
                    'action': 'rm_register_stat_ids',
                    'form_ids': form_ids
               };
    
    jQuery.post(rm_ajax_url,
                data,
                function(resp){
                    resp = JSON.parse(resp);
                    if(typeof resp === 'object') {
                        var stat_id = null, stat_field;
                        for(var key in resp) {
                            if(resp.hasOwnProperty(key)) {
                                stat_id = resp[key];                                
                                stat_field = jQuery("form#"+key+" input[name='stat_id']");
                                if(stat_field.length > 0) {
                                    stat_field.val(stat_id);
                                }
                            }
                        }
                    }                    
                });
}

var rmColor;
jQuery(document).ready(function(){
    jQuery(".rminput").on ({
        click: function () {rmHelpTextIn2(this);},
        focusin: function () {rmHelpTextIn2(this);},
        mouseleave: function () {rmHelpTextOut2(this);},
        focusout: function () {rmHelpTextOut2(this);}
    });
    
    jQuery("input, select, textarea").blur(function (){
        jQuery(this).parents(".rminput").siblings(".rmnote").fadeOut('fast');
    });
    
    rm_register_stat_ids();
    
    load_js_data();
    
    /*Initialize "Total" price display functionality*/
    rm_init_total_pricing();
    
     /*Group fields in two column view*/
  
    jQuery('.rmagic.rm_layout_two_columns .rmfieldset').each(function(){
        var a = jQuery(this).children(".rmrow").not(".rm_captcha_fieldrow");

        for( var i = 0; i < a.length; i+=2 ) {
             a.slice(i, i+2).wrapAll('<div class="rm-two-columns-wrap"></div>');
         }

    });    
    
    var $rmagic = jQuery(".rmagic");
    if($rmagic.length > 0) {
        /* Commands for automatically styling certain uncommon fields*/
        $rmagic.append("<input type='text' class='rm-input-control'><a class='rm-anchor-control'>.</a>");

        var rmHasSelect = jQuery(".rminput").children("select[multiple != multiple]").length;
        if (jQuery.isNumeric(rmHasSelect) && rmHasSelect > 0) {
            rmAddStyle("select[multiple != multiple]");
            jQuery(".rminput").children("select[multiple != multiple]").css("background", "");}

        var rmHasNumber = jQuery(".rminput").children("input[type = 'number']").length;
        if (jQuery.isNumeric(rmHasNumber) && rmHasNumber > 0) {
            rmAddStyle("input[type = 'number']");}
        
        var rmHasUrl = jQuery(".rminput").children("input[type = 'url']").length;
        if (jQuery.isNumeric(rmHasUrl) && rmHasUrl > 0) {
            rmAddStyle("input[type = 'url']");}
        
        var rmHasPass = jQuery(".rminput").children("input[type = 'password']").length;
        if (jQuery.isNumeric(rmHasPass) && rmHasPass > 0) {
            rmAddStyle("input[type = 'password']");}
        
        var rmHasAddress = jQuery(".rminput").children(".rm_address_type_ca").length;
        if (jQuery.isNumeric(rmHasAddress) && rmHasAddress > 0) {
            rmAddStyle(".select2-selection.select2-selection--single");
            var rmLineHeight = jQuery(".select2-selection.select2-selection--single").outerHeight();
            jQuery(".select2-selection__rendered").css("line-height", rmLineHeight + "px");
        }

        /* For automatically styling helptext tooltip*/
        rmColor = jQuery(".rm-anchor-control").css("color");
        rmFontColor(rmColor);

        jQuery(".rm-input-control").remove();
        jQuery(".rm-anchor-control").remove();
    }
    // Remove RMCB param
    // Remove RMCB param
    var url_hash = location.hash;
    var newURL = rmRemoveURLParameter(location.href,'rmcb') + url_hash;
    window.history.pushState('', '', newURL);
    
    $=  jQuery;
    $('.rm_privacy_cb').each(function(){
        var form= $(this).closest('.rmagic-form');
        if(form.length==0)
            return;
        $(this).change(function(){ 
            if($(this).is(':visible')){
               if($(this).prop('checked')){
                $(form).find(':submit').removeAttr('disabled');
               } 
               else
               {
                   $(form).find(':submit').attr('disabled','disabled');
               }
            }
        });
        $(this).trigger('change');
    });
    
     $('[name=rm_sb_btn]').click(function(){
        var parent_form= $(this).closest('.rmagic-form');
        if(parent_form.length>0){
            parent_form.find('.rmmap_container .map').each(function(){
                var map_id= $(this).prop('id');
                var map_container= $(this);
                if(map_id){
                    map_id= map_id.substr(3);
                    setTimeout(function(){ if(map_container.is(':visible')){rmInitMap(map_id);}}, 1000);
                }
            });
        }
    });
    // refresh form on back button
    if($(".rmagic-form").length>0 && performance){
        var perfEntries = performance.getEntriesByType("navigation");
        if(perfEntries){
            for (var i=0; i < perfEntries.length; i++) {
                var p = perfEntries[i];
                if(p.type && p.type=='back_forward'){
                    location.reload(true);
                }
            }
        }
        
    }
    
    // ESignature Field
    $('.rmagic-form .rm_esign_field').change(function(event){
        if(event.target.files.length==0)
           return;
        var container= $(this).closest('.rminput');
        container.find('.rm_esign_preview').remove();
        var reader = new FileReader();
        var image= $('<img/>', {
            class: 'rm_esign_preview'
        });
        container.append(image);
        reader.onload = function()
        { 
         image.attr('src',reader.result);
        }
        reader.readAsDataURL(event.target.files[0]);
   });
    
    var rm_forms = $('form.rmagic-form');
    if(rm_forms.length !== 0) {
        $.each(rm_forms, function( key, value ) {
            var pg_custom_tab = $(this).closest('div.pg_custom_tab_content');
            if(pg_custom_tab.length !== 0) {
                $(this).attr('action', '#' + pg_custom_tab.attr('id'));
            }
        });
    }
});


function rmRemoveURLParameter(url, parameter) {
    //prefer to use l.search if you have a location/link object
    var urlparts= url.split('?');   
    if (urlparts.length>=2) {

        var prefix= encodeURIComponent(parameter)+'=';
        var pars= urlparts[1].split(/[&;]/g);

        //reverse iteration as may be destructive
        for (var i= pars.length; i-- > 0;) {    
            //idiom for string.startsWith
            if (pars[i].lastIndexOf(prefix, 0) !== -1) {  
                pars.splice(i, 1);
            }
        }

        url= urlparts[0] + (pars.length > 0 ? '?' + pars.join('&') : "");
        return url;
    } else {
        return url;
    }
}

jQuery(document).ready(function(){
    jQuery(".rmagic .rmrow .rminput select").parent(".rminput").addClass("rminput-note");
});