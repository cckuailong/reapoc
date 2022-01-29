var rm_admin_js_data;
function delete_role(el,role){
    jQuery("#"+role).attr('checked','checked');
    jQuery.rm_do_action('rm_user_role_mananger_form','rm_user_role_delete');
}
function save_default_form(element,role)
{
    var data = {
			'action': 'rm_add_default_form',
			'form': element.value,
			'role':role
		};
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(response) {
                        jQuery("#select_"+role).hide();
                        jQuery("#label_"+role).html(response);
                        jQuery("#label_"+role).show();
                        if(response == '')
                        jQuery("#add_"+role).show();
                        else
                        jQuery("#change_"+role).show();
                 
		});
   
}

/*
 Utitlity function to show hide elements on checkbox onchange events.
 */

function checkbox_toggle_elements(el,elIds,mode){
    var ids= elIds.split(',');
    if(mode === undefined)
        mode= 0;
    if(el.checked){
        for(i=0;i<ids.length;i++){
            if(mode==1)
                jQuery("#"+ids[i]).hide(800);
            else
                jQuery("#"+ids[i]).show(800);
        }
    }else{
        for(i=0;i<ids.length;i++){
            if(mode==1)
                jQuery("#"+ ids[i]).show(800);
            else
                jQuery("#"+ ids[i]).hide(800);
        }
    }
}

function checkbox_disable_elements(el,elIds,mode,callback,args){
	hide_show(el);
    var ids= elIds.split(',');
    var is_checked = jQuery(el).is(':checked');
    if(mode === 0){
        is_checked = !is_checked;
    }
    jQuery.map(ids, function(id){
        if(is_checked){
            jQuery('#' + id).attr('disabled', true);
        }else{
            jQuery('#' + id).attr('disabled', false);
        }
        if(callback !== undefined && typeof callback === 'function'){
            if(args !== undefined && typeof args === 'array'){
                args.unshift(is_checked,id,mode);
                callback.apply(this, args);
            }else
                callback(is_checked,id,mode);
        }
    });
	
}

function hide_show_radio(element)
{
  var  rates = jQuery(element).val();
  var classname =jQuery(element).attr('class');
var childclass=classname+'_childfieldsrow';
  if(rates=='page')
  {
   jQuery('#'+childclass).slideDown();
    jQuery('.rm_form_page').slideDown();   
     jQuery('.rm_form_url').slideUp();  
  }
  else if(rates=='url')
  {
  jQuery('#'+childclass).slideDown(); 
     jQuery('.rm_form_page').slideUp();   
    jQuery('.rm_form_url').slideDown();  
  }
  else
  {
      jQuery('#'+childclass).slideUp();
  }
   console.log(rates);
}
function hide_show(element)
{
  
  var classname =jQuery(element).attr('class');
var childclass=classname+'_childfieldsrow';


 
if(jQuery(element).is(':checked'))
jQuery('#'+childclass).slideDown();
else
  jQuery('#'+childclass).slideUp();  


}

function hide_show_handlers(element){

     element_val = jQuery(element).val();
    // alert(element_val);
     if(element_val=="yes"){
         jQuery('#id_rm_wordpress_default_cb_childfieldsrow').slideUp();
         jQuery('#id_rm_smtp_enable_cb_childfieldsrow').slideDown();
     }
     else{
          jQuery('#id_rm_smtp_enable_cb_childfieldsrow').slideUp();
         jQuery('#id_rm_wordpress_default_cb_childfieldsrow').slideDown();
     }
 }




function rm_add_class(is_checked,el_id,mode,class_name){
    if(class_name === undefined){
        class_name = 'rm_prevent_empty';
    }
    if(mode === 0){
        is_checked = !is_checked;
    }
    
    if(is_checked)
        jQuery('#' + el_id).addClass(class_name);
    else
        jQuery('#' + el_id).removeClass(class_name);
}

function rm_append_field(tag, element) {
    jQuery(element).parents('#rm_action_container_id').prev().append("<" + tag + " class='appendable_options rm-deletable-options'>" + jQuery(element).parents('#rm_action_container_id').prev().children(tag + ".appendable_options").html() + "</" + tag + ">").children().children("input").last().val('');
}

function rm_load_page(element, page, get_var){
    get_var = typeof get_var !== 'undefined' ? get_var : 'form_id';
    var selected_value= jQuery(element).val();
    
    if(selected_value=='rm_login_form' && page=='field_manage'){
        window.location = utilities_vars.admin_url + '/admin.php?page=rm_login_field_manage';
    }
    else
        window.location = '?page=rm_' + page + '&rm_' + get_var + '=' + selected_value;
}

function rm_load_page_multiple_vars(element, page, get_var, get_var_json){
    get_vars = get_var_json;

    var loc = '?page=rm_' + page;

    for (var key in get_vars) {
        if (get_vars.hasOwnProperty(key)) {
            loc += '&rm_'+key+'='+get_vars[key];
            //alert(key + " -> " + get_vars[key]);
        }
    }
    
    var eleval = jQuery(element).val();
    
    if(eleval == 'custom' || jQuery(element).hasClass('rm_custom_subfilter_dates')){
        var fromdate = jQuery('#rm_id_custom_subfilter_date_from').val();
        var uptodate = jQuery('#rm_id_custom_subfilter_date_upto').val();
        jQuery("#date_box").show();
        loc += '&rm_interval=custom&rm_fromdate=' + fromdate + '&rm_dateupto=' + uptodate;
    }   
    else{
        loc += '&rm_' + get_var + '=' + jQuery(element).val();
    }

    //alert(loc);
    window.location = loc;
}

function rm_toggle_field_add_form_fields(element){
    var field_type = jQuery(element).val();

    var field_type_help_text = rm_get_help_text(field_type);
    jQuery('#rm_field_type_select_dropdown').parent().next('.rmnote').children('.rmnotecontent').html(field_type_help_text);
        jQuery.field_add_form_manage(field_type);
}

function rm_get_help_text_price_field(ftype){

    switch(ftype)
    {
        case 'fixed':return utilities_vars.price_fixed; break;
        case 'multisel':return utilities_vars.price_multisel; break;
        case 'dropdown':return utilities_vars.dropdown; break;
        case 'userdef':return utilities_vars.userdef; break;
        default: return utilities_vars.price_default;
    }
}

function rm_sort_forms(element,req_page){
    var val = jQuery(element).val();
    if(val === 'form_name')
        window.location = '?page=rm_form_manage&rm_sortby=' + val + '&rm_descending=false&rm_reqpage='+req_page;
    else
        window.location = '?page=rm_form_manage&rm_sortby=' + val + '&rm_reqpage='+req_page;


}

function rm_toggle_visiblity(element) {
    console.log(jQuery(element).val());
}

function rm_toggle_visiblity_pricing_fields(element) {
    field_type = jQuery(element).val();
    var field_type_help_text = rm_get_help_text_price_field(field_type);
    jQuery('#id_paypal_field_type_dd').parent().next('.rmnote').children('.rmnotecontent').html(field_type_help_text);

    jQuery.setup_pricing_fields_visibility(field_type);


}

function rm_toggle_visiblity_layouts(element) {
    jQuery.setup_layouts_visibility(jQuery(element).val());
}

function rm_delete_appended_field(element, element_id) {
    if (jQuery(element).parents("#".element_id).children(".appendable_options").length > 1)
        jQuery(element).parent(".appendable_options").remove();
}

function handle_review_banner_click(action,info){
    var flag='';
    if(action=='skip')
        flag='wordpress'
    else
        flag=action;
    var data = {
        'action':'review_banner_handler',
        'type':flag,
        'info':info
    };
    jQuery.post(ajaxurl, data, function(response){
        
    });
    if(action=='feedback')
        window.open('https://metagauss.kayako.com/Default/Tickets/Submit', 'my window','');
    if(action=='wordpress')
        window.open('https://wordpress.org/support/plugin/custom-registration-form-builder-with-submission-manager/reviews/', 'my window','');
   }

function handle_newsletter_subscription_click(msg){
    var data = {
        'action':'newsletter_sub_handler'
    };
    jQuery.post(ajaxurl, data, function(){
        var el = jQuery('#rm_newsletter_sub');
        el.html(msg);
        el.fadeOut(1500);
    });
}

function rm_setup_google_charts(){

    if(typeof google != 'undefined'){
        // Load the Visualization API and the corechart package.
        google.charts.load('current', {'packages': ['corechart', 'bar']});

        // Set a callback to run when the Google Visualization API is loaded.

        //Since callback functions are defined in the templates, always
        //check for the existance of function beforehand so that javascript does not fail for other pages.
        if (typeof drawConversionChart == 'function')
            google.charts.setOnLoadCallback(drawConversionChart);

        if (typeof drawBrowserUsageChart == 'function')
            google.charts.setOnLoadCallback(drawBrowserUsageChart);

        if (typeof drawConversionByBrowserChart == 'function')
            google.charts.setOnLoadCallback(drawConversionByBrowserChart);
        
        if (typeof drawTimewiseStat == 'function')
            google.charts.setOnLoadCallback(drawTimewiseStat);

        if (typeof drawMultipleFieldCharts == 'function')
            google.charts.setOnLoadCallback(drawMultipleFieldCharts);
    }
}

function getUrlParams() {
        var result = {};
        var params = (window.location.search.split('?')[1] || '').split('&');
        for(var param in params) {
            if (params.hasOwnProperty(param)) {
                var paramParts = params[param].split('=');
                if(paramParts.length==1)
                    paramParts.push('');
                result[paramParts[0]] = encodeURIComponent(paramParts[1]).replace(/'/g,"%27").replace(/"/g,"%22") || "";
           
            }
        }
        return result;
}
    
function getPathFromUrl(url) {
    return url.split("?")[0];
  }
      
/*
   * Tockenized Autocomplete 
   */
function initialize_submission_filters(){
    jQuery(".rm-auto-tag").each(function () {
          //Submission Filter settings
          if (jQuery(this).hasClass('rm-submission-tag')) {
              var urlParams= getUrlParams();

              var tags= decodeURIComponent(urlParams.filter_tags);
              if(tags=='undefined')
                  tags= "";

              jQuery(this).tokens({
                    source : rm_admin_js_data.submission_filter_tags,
                    initValue : decodeURIComponent(tags).replace(/\+/g,  " ").split(","),
                    showSuggestionOnFocus : true,
                    onSelect : function(suggestions){
                        var urlParams= getUrlParams();
                        urlParams.filter_tags= suggestions.toString();
                        var queryString= jQuery.param(urlParams);
                        var url= getPathFromUrl(window.location.href);
                        window.location= url + "?" + queryString + "&rm_search_initiated"; 
                    }
              });
          }
      });
}

function update_current_url_with_param(key, value)
{
    key = encodeURI(key); value = encodeURI(value);

    var kvp = document.location.search.substr(1).split('&');

    var i=kvp.length; var x; while(i--) 
    {
        x = kvp[i].split('=');

        if (x[0]==key)
        {
            x[1] = value;
            kvp[i] = x.join('=');
            break;
        }
    }

    if(i<0) {kvp[kvp.length] = [key,value].join('=');}

    //this will reload the page, it's likely better to store this until finished
    return kvp.join('&'); 
}

function load_js_data(){
        var data = {
            'action': 'rm_admin_js_data'
        };
        
        jQuery.post(ajaxurl, data, function (response) {
           rm_admin_js_data= JSON.parse(response);
           initialize_submission_filters();
        });
        
    }
    
    // Intializing the necessary scripts
jQuery(document).ready(function(){
    load_js_data();
});

