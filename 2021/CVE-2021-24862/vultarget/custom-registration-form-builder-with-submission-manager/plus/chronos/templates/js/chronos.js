jQuery(document).ready(function(){
   /* set up checkbox toggles*/
   jQuery(".rmc_control_toggle").each(function(){
       var this_jq = jQuery(this);
        if(!this_jq.prop('checked'))
            jQuery("#"+this_jq.attr('id')+"_ctrl_subpanel").hide();
        
        this_jq.on('change',function(){
            if(this_jq.prop('checked'))
                jQuery("#"+this_jq.attr('id')+"_ctrl_subpanel").slideDown();
            else
                jQuery("#"+this_jq.attr('id')+"_ctrl_subpanel").slideUp();
        }); 
   });

   jQuery("form#rmc_task_settings_form").fadeIn();
   
   jQuery("#rmc_field_selector_task_action_send_mail").change(function () {
        tinymce.execCommand('mceFocus', false, 'content');
        var field_placeholder = jQuery(this).val();
        if (field_placeholder != 0) {
            field_placeholder = "{{"+field_placeholder+"}}";
            if (typeof send_to_editor == 'function')
                send_to_editor(field_placeholder);
            else
                tinyMCE.get('content').execCommand('mceInsertContent', false, field_placeholder);
        }
    });
   
});

function rmc_validate_task_form(slide_identifier) {
    var validator = "rmc_validate_"+slide_identifier;
    if(typeof window[validator] === 'function') {
        if(!(window[validator]())) {
            event.preventDefault();
            return false;
        }
        else
            return true;
    }
}

function rmc_validate_basic_details() {
    var is_valid = true;
    var task_name_jq = jQuery("#rmc_task_name");
    if(task_name_jq.val().toString().trim() == "") {
        is_valid = false;
        task_name_jq.focus();
        rmc_flash_element(task_name_jq);
    }
    return is_valid;
}

function rmc_validate_rule_config() {
    var is_valid = true;
    var error_jq;
    var first_invalid_element_jq = null;
    var enable_user_state_rule_jq = jQuery("#rmc_enable_user_account_rule");
    if(enable_user_state_rule_jq.prop('checked')) {
        if(jQuery("#rmc_enable_user_account_rule_ctrl_subpanel input[type='radio']:checked").length == 0) {
            is_valid = false;
            jQuery("#rm_user_state_rule_error").show();
            first_invalid_element_jq = jQuery("#rmc_enable_user_account_rule_ctrl_subpanel input[type='radio']");
            rmc_flash_element(first_invalid_element_jq);
        }
    }
    
    var enable_sub_time_rule_jq = jQuery("#rmc_enable_sub_time_rule");
    if(enable_sub_time_rule_jq.prop('checked')) {
        var enabled_rules = jQuery("#rmc_enable_sub_time_rule_ctrl_subpanel input[type='checkbox']:checked");
        error_jq = jQuery("#rm_sub_time_rule_error");
        if(enabled_rules.length == 0) {
            is_valid = false;
            error_jq.html("&#9888; " + chronos_js_vars.one_rule_error).show();
            if(!first_invalid_element_jq)
                first_invalid_element_jq = jQuery("#rmc_enable_sub_time_rule_older_than");
            rmc_flash_element(jQuery("#rmc_enable_sub_time_rule_ctrl_subpanel input[type='checkbox']"));
        } else {  
            var older_than_age = null;
            var younger_than_age = null;
            if(jQuery("#rmc_enable_sub_time_rule_older_than").prop('checked')) {
                var age_jq = jQuery("#rmc_rule_sub_time_older_than_age");
                var age = age_jq.val().toString().trim();
                if(age == "") {
                    is_valid = false;
                    error_jq.html("&#9888; " + chronos_js_vars.age_error).show();
                    if(!first_invalid_element_jq)
                        first_invalid_element_jq = age_jq;
                    rmc_flash_element(age_jq);
                } else
                    older_than_age = parseInt(age);
            }
            if(jQuery("#rmc_enable_sub_time_rule_younger_than").prop('checked')) {
                var age_jq = jQuery("#rmc_rule_sub_time_younger_than_age");
                var age = age_jq.val().toString().trim();
                if(age == "") {
                    is_valid = false;
                    error_jq.html("&#9888; " + chronos_js_vars.age_error).show();
                    if(!first_invalid_element_jq)
                        first_invalid_element_jq = age_jq;
                    rmc_flash_element(age_jq);
                } else
                    younger_than_age = parseInt(age);
            }
            if(younger_than_age && older_than_age) {
                if(older_than_age >= younger_than_age) {
                    is_valid = false;
                    error_jq.html("&#9888; " + chronos_js_vars.invalid_age).show();
                    if(!first_invalid_element_jq)
                        first_invalid_element_jq = jQuery("#rmc_rule_sub_time_older_than_age");
                }
            }
        }
    }
    
    var enable_field_val_rule_jq = jQuery("#rmc_enable_field_value_rule");
    if(enable_field_val_rule_jq.prop('checked')) {
        error_jq = jQuery("#rm_field_value_rule_error");
        jQuery("#rmc_enable_field_value_rule_ctrl_subpanel .appendable_options").each(function(){
            var this_jq = jQuery(this);
            var f_id = this_jq.find("select").val();
            var f_val_jq = this_jq.find("input[type='text']");
            if(f_id && f_val_jq.val().toString().trim()=="") {
                is_valid = false;
                error_jq.html("&#9888; " + chronos_js_vars.empty_error).show();
                if(!first_invalid_element_jq)
                    first_invalid_element_jq = f_val_jq;
                rmc_flash_element(f_val_jq);
            }
        });
    }
    
    if(first_invalid_element_jq)
        first_invalid_element_jq.focus();
    return is_valid;
}

function rmc_validate_action_config() {
    var is_valid = true;
    var first_invalid_element_jq = null;
    var enable_action_send_mail_jq = jQuery("#rmc_enable_send_mail_action");
    if(enable_action_send_mail_jq.prop('checked')) {
        var sub_jq = jQuery("#rmc_action_send_mail_sub");        
        if(sub_jq.val().toString().trim()=="") {
            is_valid = false;
            if(!first_invalid_element_jq)
                first_invalid_element_jq = sub_jq;
            rmc_flash_element(sub_jq);
        }
        
        /*Validate tinymce editot container, its little elaborated*/
        /*First check if visual or text tab is activated*/
        var body_jq;
        var is_visual_tab_active = (tinyMCE.activeEditor == null || tinyMCE.activeEditor.isHidden() != false) ? false : true;
        if(is_visual_tab_active) {
            body_jq = jQuery(tinyMCE.activeEditor.getContentAreaContainer());
            editor_content = tinyMCE.activeEditor.getContent();
        } else {
            body_jq = jQuery("textarea[name='rmc_action_send_mail_body']");
            editor_content = body_jq.val().toString();
        }
        if(editor_content.trim()=="") {
            is_valid = false;   
            if(!first_invalid_element_jq)
                first_invalid_element_jq = body_jq;
            rmc_flash_element(body_jq);
        }
    }
    if(first_invalid_element_jq)
        first_invalid_element_jq.focus();
    if(is_valid)
        jQuery( "form:first" ).submit();
    return is_valid;
}

function rmc_flash_element(x){
   x.each(function () {
        jQuery(this).css("border", "1px solid #FF6C6C");        
        jQuery(this).fadeIn(100).fadeOut(1000, function () {
            jQuery(this).css("border", "");
            jQuery(this).fadeIn(100);
            jQuery(this).val('');
        });
    });       
}

function rmc_run_task_now(task_id) {
    if(typeof task_id != 'undefined') {
        var data = {
                        action: 'rm_chronos_ajax',
                        rm_chronos_ajax_action: 'trigger_task',
                        task_id: task_id
                    };
        jQuery("#id_rmc_run_task_now_"+task_id).text('Running').addClass('rm_deactivated');            
        jQuery.post(ajaxurl, data,function(resp){
            resp = JSON.parse(resp);
            if(resp['result'] == 'error') {
                alert(resp['message']);
                jQuery("#id_rmc_run_task_now_"+task_id).removeClass('rm_deactivated');
            }
            else
                jQuery("#id_rmc_run_task_now_"+task_id).text('Task Finished');
        });
    }
}

function rmc_delete_tasks_batch() {
    var tasks_jq = jQuery("input[name='rm_selected[]']:checked");
    var task_ids = tasks_jq.map(function() {return this.value;}).get();
    var delete_link_jq = jQuery("#rm-delete-task a");
    if(task_ids.length > 0) {
        var data = {
                        action: 'rm_chronos_ajax',
                        rm_chronos_ajax_action: 'delete_tasks_batch',
                        task_ids: task_ids
                    };
        var orig_text = delete_link_jq.html();
        jQuery("#rm-delete-task").addClass("rm_deactivated"); 
        delete_link_jq.html("<i>" + chronos_js_vars.removing + "<i>");        
        jQuery.post(ajaxurl, data,function(resp){
            tasks_jq.closest("div.rm-slab").slideUp(400, function(){
                jQuery(this).remove();
            });
            delete_link_jq.html(orig_text);
        });
    }
}

function rmc_duplicate_tasks_batch() {
    var tasks_jq = jQuery("input[name='rm_selected[]']:checked");
    var task_ids = tasks_jq.map(function() {return this.value;}).get();
    if(task_ids.length > 0) {
        var data = {
                        action: 'rm_chronos_ajax',
                        rm_chronos_ajax_action: 'duplicate_tasks_batch',
                        task_ids: task_ids
                    };
        jQuery("#rm-duplicate-task").addClass("rm_deactivated"); 
        jQuery.post(ajaxurl, data,function(resp){
            window.location.reload();
        });
    }
}

function rmc_set_state_tasks_batch(state) {
    var tasks_jq = jQuery("input[name='rm_selected[]']:checked");
    var task_ids = tasks_jq.map(function() {return this.value;}).get();
    if(task_ids.length > 0) {
        var data = {
                        action: 'rm_chronos_ajax',
                        rm_chronos_ajax_action: 'set_state_tasks_batch',
                        task_ids: task_ids,
                        state: state
                    };
        jQuery("#rm-enable-task, #rm-disable-task").addClass("rm_deactivated"); 
        jQuery.post(ajaxurl, data,function(resp){
            window.location.reload();
        });
    }
}

