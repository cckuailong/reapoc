jQuery(document).ready(function(){
    jQuery(".rm-form-wizard-step-action").click(function(){
        var $this = jQuery(this);
        jQuery(".rm-form-wizard-step").removeClass("rm-wizard-activated");
        $this.closest(".rm-form-wizard-step").addClass("rm-wizard-activated");
        jQuery(".rm_formflow_page").hide().removeClass("rm-forflow-page-active");
        jQuery($this.data("page")).show().addClass("rm-forflow-page-active");
    });
    
    jQuery(".rm_formflow_page_next_btn").click(function(){
        var $this = jQuery(this);
        jQuery(".rm-form-wizard-step").removeClass("rm-wizard-activated");
        jQuery(".rm-form-wizard-step-action[data-page='"+$this.data("next_page")+"']").closest(".rm-form-wizard-step").addClass("rm-wizard-activated");
        jQuery(".rm_formflow_page").hide().removeClass("rm-forflow-page-active");
        jQuery($this.data("next_page")).show().addClass("rm-forflow-page-active");
    });
});

function rm_copy_content(target, click_ele) {

    var text_to_copy = jQuery(target).text();

    var tmp = jQuery("<input id='fd_form_shortcode_input' readonly>");
    var target_html = jQuery(target).html();
    jQuery(target).html('');
    jQuery(target).append(tmp);
    tmp.val(text_to_copy).select();
    var result = document.execCommand("copy");

    if (result != false) {
        jQuery(target).html(target_html);
        jQuery(click_ele).text(formflow_vars.copied);
        setTimeout(function(){
            jQuery(click_ele).text(formflow_vars.copy);
        },1000);
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

function rm_formflow_set_def_form(e) {
    var form_id = jQuery(e).attr('id').slice(8);
        if(typeof form_id != 'undefined' && !jQuery(e).hasClass('rm_def_form_star')){
        var data = {
			'action': 'set_default_form',
                        'rm_ajaxnonce': formflow_vars.ajaxnonce,
			'rm_def_form_id': form_id
		};

        jQuery.post(ajaxurl, data, function(response) {
                        var old_form = jQuery('.rm_def_form_star');
			old_form.removeClass('rm_def_form_star');
                        old_form.addClass('rm_not_def_form_star');
                        
                        var curr_form = jQuery('#rm-star_'+form_id);
                        curr_form.removeClass('rm_not_def_form_star');
                        curr_form.addClass('rm_def_form_star');
		});
            }
}


function rm_set_publish_popup(form_id, publish_type, callback) {
    /* hide all panels*/
    jQuery(".rm_publish_section").hide();    
    jQuery("[data-publish_code]").each(function(){
        var $this = jQuery(this);
        var code = $this.data("publish_code");
        $this.text(code.replace("%fid%",form_id));
    });
    
    var def_form_id = jQuery(".rm_form_star").attr("id","rm-star_"+form_id).data("def_form_id");
    
    if(def_form_id === form_id)
        jQuery(".rm_form_star").removeClass("rm_not_def_form_star").addClass("rm_def_form_star");
    else
        jQuery(".rm_form_star").removeClass("rm_def_form_star").addClass("rm_not_def_form_star");
    
    jQuery("#rm_publish_"+publish_type).show();
}
