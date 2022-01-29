jQuery(document).ready(function () {
    jQuery('.rm-query-ask').click(function () {
        jQuery(this).siblings('.rm-query-answer').show();
    });
    
    jQuery('.rm-collapsible').click(function () {
        jQuery(this).parent().siblings().toggle();
        jQuery(this).toggleClass('rm-collapsible rm-collapsed');
    });
});

//hide a container when clicked outside
jQuery(document).mouseup(function (e) {
        var container = jQuery(".rm-query-answer");
        if (!container.is(e.target) // if the target of the click isn't the container... 
                && container.has(e.target).length === 0) // ... nor a descendant of the container 
        {
            container.hide();
        }
    });
/**
 * function to copy a element's content to clipboard. 
 * use limited to only non input type elements
 * 
 * @param {DOM Element} target  Element which has the content to be copied to clipboard
 */
function rm_copy_to_clipboard(target) {

    var text_to_copy = jQuery(target).text();

    var tmp = jQuery("<input id='fd_form_shortcode_input' readonly>");
    var target_html = jQuery(target).html();
    jQuery(target).html('');
    jQuery(target).append(tmp);
    tmp.val(text_to_copy).select();
    var result = document.execCommand("copy");

    if (result != false) {
        jQuery(target).html(target_html);
        jQuery("#rm_msg_copied_to_clipboard").fadeIn('slow');
        jQuery("#rm_msg_copied_to_clipboard").fadeOut('slow');
    } else {
        jQuery(document).mouseup(function (e) {
            var container = jQuery("#fd_form_shortcode_input");
            if (!container.is(e.target) // if the target of the click isn't the container... 
                    && container.has(e.target).length === 0) // ... nor a descendant of the container 
            {
                jQuery(target).html(target_html);
            }
        });
    }
}

function rm_fd_switch_form(form_id, timerange){
   if(form_id)
   {
       if(form_id=='rm_login_form'){
           location.href = '?page=rm_login_sett_manage';
       }
       else if(typeof timerange != 'undefined')
           location.href = '?page=rm_form_sett_manage&rm_form_id='+form_id+'&rm_tr='+timerange;
       else
           location.href = '?page=rm_form_sett_manage&rm_form_id='+form_id;
   }
}

function rm_fd_quick_toggle(elem, form_id) {
    var option_name = jQuery(elem).attr('name');
    var option_val;
    if (jQuery(elem).is(':checked'))
        option_val = true;
    else
        option_val = false;

    var data = {
        'action': 'rm_toggle_form_option',
        'rm_slug': 'rm_form_sett_qck_toggle',
        'form_id': form_id,
        'name': option_name,
        'value': option_val
    }

    jQuery.post(ajaxurl, data, function (response) {
        console.log(response);
    });

}

/* For dashboard promo tabs */
(function($){ 
    
    $(document).ready(function(){
       $(".rm_fd_promo_subsect_tabs").each(function(){
            var $this = $(this);
            $this.find("li").each(function(curr_index){
                $(this).hover(function(){
                    $this.find(".rm-fd-promo-content").hide().eq(curr_index).show();
                    $this.find(".rm-fd-promo-nub").hide().eq(curr_index).show();
                });
            });
            
            $this.find(".rm-fd-promo-content").hide().eq(0).show();
            $this.find(".rm-fd-promo-nub").hide().eq(0).show();
        });        
        
    });   
    
})(jQuery);
