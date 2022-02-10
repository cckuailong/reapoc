var plans={trial:9,free:0,plus:1,pro:2,vip:3,ultra:4};
var likebtn_popup;
var likebtn_popup_timer;
var likebtn_preview;
var likebtn_preview_offset;
var likebtn_preview_position = 'static';
var likebtn_preview_refresh = 300;
var likebtn_preview_wait = false;
var likebtn_pin = false;
var likebtn_poll_cntr;
var likebtn_force_deact = false;

// replace all occurences of a string
String.prototype.replaceAll = function(search, replace){
    return this.split(search).join(replace);
};

// Set cookie
function likebtnSetCookie(name, value, props) {
    props = props || {}
    var exp = props.expires
    if (typeof exp == "number" && exp) {
        var d = new Date()
        d.setTime(d.getTime() + exp*1000)
        exp = props.expires = d
    }
    if (exp && exp.toUTCString) { props.expires = exp.toUTCString() }

    value = encodeURIComponent(value)
    var updatedCookie = name + "=" + value
    for (var propName in props) {
        updatedCookie += "; " + propName
        var propValue = props[propName]
        if(propValue !== true){ updatedCookie += "=" + propValue }
    }

    document.cookie = updatedCookie;
}

// Get cookie
function likebtnGetCookie(name) {
    var matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined
}

// Кemove cookie
function removeCookie(name) {
    // expiration date in the past
    var exp_date = new Date();
    exp_date.setSeconds(exp_date.getSeconds() - 3600);
    likebtnSetCookie(name, "", {expires:exp_date, path:"/"});
}

jQuery(document).ready(function(jQuery) {
    likebtnApplyTooltips();

    // Poll
    if (typeof(likebtn_msg_feedback_sent) != "undefined" && likebtn_msg_feedback_sent == 0) {
        var a = jQuery(".plugins .active[data-slug='likebtn-like-button'] .deactivate a:first");
        if (!a) {
            a = jQuery(".plugins #likebtn-like-button.active .deactivate a:first");
        }
        if (!a || "undefined" == typeof(a.dialog)) {
            return;
        }
        a.attr('onclick', 'deactivatePoll(event, "'+a.attr('href')+'")');
    }
});

function deactivatePoll(event, href)
{
    var dialog_exists = true;
    if (event) {
        event.preventDefault();
    }
    likebtn_poll_cntr = jQuery("#likebtn_poll");
    if (!likebtn_poll_cntr.size()) {
        dialog_exists = false;
        var poll_html = 
            '<div id="likebtn_poll">'+
                '<form id="likebtn_poll_form" onsubmit="return false">'+
                '<div class="likebtn_poll_offer" style="display:none">'+likebtn_msg_f_offer1+'<div class="likebtn-coupon">'+likebtn_msg_coupon+'</div><center>'+likebtn_msg_f_offer2+'</center></div>'+
                '<div class="likebtn_poll_intro">'+likebtn_msg_f_intro+'</div>'+
                '<div class="likebtn_poll_opt"><input type="radio" name="likebtn_reason" value="features" id="likebtn_reason_features" onclick="likebtnPollChoose(this)" required="required"/> <label for="likebtn_reason_features"><strong>'+likebtn_msg_f_features+'</strong></label></div>'+
                '<div class="likebtn_poll_reason lpr_features">'+
                    '<textarea rows="3" style="width:100%" placeholder="'+likebtn_msg_f_ph+'" id="likebtn_ta_features" name="likebtn_missing_features"></textarea><br/>'+
                    '<input type="checkbox" id="likebtn_reason_features_email_c" onclick="likebtnPollFeaturesEmail(this)"/> <label for="likebtn_reason_features_email_c">'+likebtn_msg_f_notify+'</label><br/><input type="email" name="likebtn_reason_email" value="" placeholder="'+likebtn_msg_ye+'" style="width:50%; display:none" id="likebtn_reason_features_email" />'+
                '</div>'+

                '<div class="likebtn_poll_opt"><input type="radio" name="likebtn_reason" value="pricing" id="likebtn_reason_pricing" onclick="likebtnPollChoose(this)" required="required"/>  <label for="likebtn_reason_pricing"><strong>'+likebtn_msg_f_pricing+'</strong></label></div>'+
                '<div class="likebtn_poll_reason lpr_pricing">'+
                    likebtn_msg_f_pricing_i+
                    //'<input type="email" name="likebtn_reason_pricing_email" value="" placeholder="Your email" style="width:50%"/>'+
                '</div>'+

                '<div class="likebtn_poll_opt"><input type="radio" name="likebtn_reason" value="integration" id="likebtn_reason_integration" onclick="likebtnPollChoose(this)" required="required"/>  <label for="likebtn_reason_integration"><strong>'+likebtn_msg_f_int+'</strong></label></div>'+
                '<div class="likebtn_poll_reason lpr_integration">'+
                    likebtn_msg_f_i1+' <a href="'+likebtn_msg_website+'developers" target="_blank">'+likebtn_msg_f_i2+'</a>'+
                '</div>'+

                '<div class="likebtn_poll_opt"><input type="radio" name="likebtn_reason" value="tmp" id="likebtn_reason_tmp" onclick="likebtnPollChoose(this)" required="required"/>  <label for="likebtn_reason_tmp"><strong>'+likebtn_msg_f_tmp+'</strong></label></div>'+

                '<div class="likebtn_poll_opt"><input type="radio" name="likebtn_reason" value="other" id="likebtn_reason_other" onclick="likebtnPollChoose(this)" required="required"/>  <label for="likebtn_reason_other"><strong>'+likebtn_msg_other+'</strong></label></div>'+
                '<div class="likebtn_poll_reason lpr_other" style="margin-bottom:0">'+
                    '<textarea rows="3" style="width:100%" placeholder="'+likebtn_msg_other_ph+'" id="likebtn_ta_other" name="likebtn_other_text"></textarea>'+
                '</div><br/>'+

                '<div class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix">'+
                    '<div style="display:none" id="likebtn_poll_loader"><img src="'+likebtn_msg_pub_url+'img/ajax_loader_hor.gif" /></div>'+
                    '<div class="ui-dialog-buttonset">'+
                        '<button type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only button-primary likebtn-button-submit" role="button"><span class="ui-button-text">'+likebtn_msg_f_submit2+'</span></button>&nbsp; '+
                        '<button type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only button-secondary likebtn-button-submit" role="button" data-deactivate="1"><span class="ui-button-text">'+likebtn_msg_f_submit1+'</span></button>&nbsp; '+
                        '<button type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only button-secondary likebtn-button-close" role="button"><span class="ui-button-text">'+likebtn_msg_f_cancel+'</span></button>'+
                    '</div>'+
                '</div>'+
                '<input type="submit" id="likebtn_poll_submit" style="display:none">'+
                '</form>'+
            '</div>';

        likebtn_poll_cntr = jQuery(poll_html).appendTo(jQuery("body"));
    }

    likebtn_poll_cntr.dialog({
        resizable: false,
        autoOpen: false,
        modal: true,
        width: '50%',
        title: likebtn_msg_d_title,
        draggable: false,
        show: 'fade',
        dialogClass: 'likebtn_dialog',
        close: function( event, ui ) {
            
        },
        open: function() {
            if (!dialog_exists) {
                jQuery('.ui-widget-overlay, #likebtn_poll .likebtn-button-close').bind('click', function() {
                    likebtn_poll_cntr.dialog('close');
                });
                jQuery('#likebtn_poll .likebtn-button-submit').bind('click', function() {
                    var form = jQuery("#likebtn_poll_form");
                    if (form[0] && !form[0].checkValidity()) {
                        jQuery("#likebtn_poll_submit").click();
                        return;
                    }

                    var data = jQuery("#likebtn_poll_form").serializeArray();
                    var decision = 'keep';
                    if (jQuery(this).attr("data-deactivate") == '1') {
                        decision = 'deactivate';

                        if (jQuery("#likebtn_reason_pricing").is(':checked') && !likebtn_force_deact) {
                            likebtn_force_deact = true;
                            jQuery(".likebtn_poll_offer").show();
                            jQuery(".likebtn_poll_intro").hide();
                            jQuery(".likebtn_poll_opt").hide();
                            jQuery(".likebtn_poll_reason").hide();
                            jQuery("#likebtn_poll_form .ui-dialog-buttonset .likebtn-button-submit:first").hide();
                            jQuery("#likebtn_poll_form .ui-dialog-buttonset .likebtn-button-submit:eq(1)").removeClass('button-secondary').addClass('button-primary').children('.ui-button-text:first').text(likebtn_msg_f_deact_anyway);
                            jQuery("#likebtn_poll_form .ui-dialog-buttonset .likebtn-button-close:first").text(likebtn_msg_f_close);
                            return;
                        }
                    }

                    // Show loader
                    jQuery("#likebtn_poll_loader").show();
                    jQuery("#likebtn_poll_form .ui-dialog-buttonset").hide();

                    var email = likebtn_msg_account_email;
                    if (!email) {
                        email = likebtn_msg_admin_email;
                    }

                    data.push({'name':'action', 'value':'likebtn_plugin_feedback'});
                    data.push({'name':'admin_email', 'value':likebtn_msg_admin_email});
                    data.push({'name':'account_email', 'value':likebtn_msg_account_email});
                    data.push({'name':'email', 'value':email});
                    data.push({'name':'site_id', 'value':likebtn_msg_account_site_id});
                    data.push({'name':'website', 'value':likebtn_msg_site_url});
                    data.push({'name':'plan', 'value':likebtn_msg_plan});
                    data.push({'name':'version', 'value':likebtn_msg_version});
                    data.push({'name':'locale', 'value':likebtn_msg_locale});
                    data.push({'name':'decision', 'value':decision});

                    jQuery.ajax({
                        type: 'POST',
                        dataType: "json",
                        url: ajaxurl,
                        data: data,
                        success: function(response) {
                            likebtn_poll_cntr.dialog('close');
                            if (decision == 'deactivate') {
                                window.location.href = href;
                            }
                            jQuery("#likebtn_poll_loader").hide();
                            jQuery("#likebtn_poll_form .ui-dialog-buttonset").show();
                        },
                        error: function(response) {
                            likebtn_poll_cntr.dialog('close');
                            if (decision == 'deactivate') {
                                window.location.href = href;
                            }
                            jQuery("#likebtn_poll_loader").hide();
                            jQuery("#likebtn_poll_form .ui-dialog-buttonset").show();
                        }
                    });
                });
            }
        },
        position: { 
            my: "center", 
            at: "center" 
        }
    });

    likebtn_poll_cntr.dialog('open');
}

function likebtnPollChoose(radio)
{
    var chosen = jQuery(radio).val();
    if (chosen) {
        jQuery("#likebtn_poll .likebtn_poll_reason").hide();
        jQuery("#likebtn_poll .lpr_"+chosen).show();
        
        jQuery("#likebtn_poll textarea").removeAttr("required");

        if (jQuery("#likebtn_ta_"+chosen).is(":visible") && chosen != 'other') {
            jQuery("#likebtn_ta_"+chosen).attr("required", "required");
        } else {
            jQuery("#likebtn_ta_"+chosen).removeAttr("required");
        }
    }
}
function likebtnPollFeaturesEmail(el)
{
    if (jQuery(el).is(":checked")) {
        jQuery("#likebtn_reason_features_email").show();
    } else {
        jQuery("#likebtn_reason_features_email").hide().val('');
    }
}

// Show/hide entity options
function entityShowChange(el, entity_name)
{
    if (jQuery(el).is(':checked')) {
        jQuery("#entity_container_"+entity_name).show();
        jQuery("#likebtn_subpage_tab_wrapper .likebtn_tab_"+entity_name+" .likebtn_show_marker").removeClass('hidden');
    } else {
        jQuery("#entity_container_"+entity_name).hide();
        jQuery("#likebtn_subpage_tab_wrapper .likebtn_tab_"+entity_name+" .likebtn_show_marker").addClass('hidden');
    }
}

// Show/hide options on Use settings from select
function userSettingsFromChange(el, entity_name)
{
    if (jQuery(el).val()) {
        jQuery("#use_settings_from_container_"+entity_name).hide();
        jQuery("#likebtn_save_preview").hide();
        jQuery("#preview_fixer").hide();
    } else {
        jQuery("#use_settings_from_container_"+entity_name).show();
        jQuery("#likebtn_save_preview").show();
        jQuery("#preview_fixer").show();
    }
}

// Toggle collapable area
function toggleCollapsable(el)
{
    jQuery(el).parent().children('.inside').toggle();
}

// Toggle upgrade website instructions
function toggleToUpgrade()
{
    jQuery("#likebtn_to_upgrade").toggle();
}

// Toggle Post format container
function postFormatAllChange(el, entity_name)
{
    if (jQuery(el).is(':checked')) {
        jQuery("#post_format_container_"+entity_name).hide();
    } else {
        jQuery("#post_format_container_"+entity_name).show();
    }
}

// Account data change
function accountChange()
{
    var account_data_filled = true;
    jQuery("input.likebtn_account").each(function(index, element) {
        if (!jQuery(element).val()) {
            account_data_filled = false;
        }
    });
    
    jQuery(".likebtn_sync_cntr:first").removeClass('likebtn_sync_ena_flag').addClass('likebtn_sync_dis_flag');
    if (account_data_filled) {
        //jQuery(":input[name='likebtn_sync_inerval']").removeAttr('disabled');
    } else {
        jQuery(":input[name='likebtn_sync_inerval']").val('');/*.attr('disabled', 'disabled')*/;
    }
}

// test synchronization
function testSync(loader_src)
{
    if (jQuery(".likebtn_test_sync_container:first img").size()) {
        return;
    }
    
    jQuery(".likebtn_test_sync_container:first").html('<img src="' + loader_src + '" />');

    jQuery.ajax({
        type: 'POST',
        dataType: "json",
        url: ajaxurl,
        data: {
            action: 'likebtn_test_sync',
            likebtn_account_email: jQuery(":input[name='likebtn_account_email']:first").val(),
            likebtn_account_api_key: jQuery(":input[name='likebtn_account_api_key']:first").val(),
            likebtn_site_id: jQuery(":input[name='likebtn_site_id']:first").val()
        },
        success: function(response) {
            var result_text = '';
            if (typeof(response.result_text) != "undefined") {
                result_text = response.result_text;
            }
            if (typeof(response.result) == "undefined" || response.result != "success") {
                jQuery(".likebtn_test_sync_container:first").text(result_text);
                jQuery(".likebtn_test_sync_container:first").css('color', 'red');
                if (typeof(response.message) != "undefined") {
                    var text = jQuery(".likebtn_test_sync_container:first").html() + ': ' + response.message;
                    jQuery(".likebtn_test_sync_container:first").html(text);
                }
            } else {
                if (jQuery(".likebtn_sync_cntr:first").hasClass('likebtn_sync_dis_flag')) {
                    jQuery(".likebtn_test_sync_container:first").text('');
                } else {
                    jQuery(".likebtn_test_sync_container:first").text(result_text);
                    jQuery(".likebtn_test_sync_container:first").css('color', 'green');
                }

                jQuery(".likebtn_sync_cntr:first").removeClass('likebtn_sync_dis_flag').addClass('likebtn_sync_ena_flag');
            }

        },
        error: function(response) {
            jQuery(".likebtn_test_sync_container:first").html(likebtn_msg_error).css('color', 'red');
        }
    });
}

// check account data
function checkAccount(loader_src)
{
    if (jQuery(".likebtn_check_account_container:first img").size()) {
        return;
    }

    jQuery(".likebtn_check_account_container:first").html('<img src="' + loader_src + '" />');

    jQuery.ajax({
        type: 'POST',
        dataType: "json",
        url: ajaxurl,
        data: {
            action: 'likebtn_check_account',
            likebtn_account_email: jQuery(":input[name='likebtn_account_email']:first").val(),
            likebtn_account_api_key: jQuery(":input[name='likebtn_account_api_key']:first").val(),
            likebtn_site_id: jQuery(":input[name='likebtn_site_id']:first").val()
        },
        success: function(response) {
            var result_text = '';
            if (typeof(response.result_text) != "undefined") {
                result_text = response.result_text;
            }
            jQuery(".likebtn_check_account_container:first").text(result_text);
            if (typeof(response.result) == "undefined" || response.result != "success") {
                jQuery(".likebtn_check_account_container:first").css('color', 'red');
                if (typeof(response.message) != "undefined") {
                    var text = jQuery(".likebtn_check_account_container:first").html() + ': ' + response.message;
                    jQuery(".likebtn_check_account_container:first").html(text);
                }
            } else {
                jQuery(".likebtn_check_account_container:first").css('color', 'green');
            }

        },
        error: function(response) {
            jQuery(".likebtn_check_account_container:first").html(likebtn_msg_error).css('color', 'red');
        }
    });
}

// full synchronization
function manualSync(loader_src)
{
    jQuery(".likebtn_manual_sync_container:first").html('<img src="' + loader_src + '" />');

    jQuery.ajax({
        type: 'POST',
        dataType: "json",
        url: ajaxurl,
        data: {
            action: 'likebtn_manual_sync',
            likebtn_account_email: jQuery(":input[name='likebtn_account_email']:first").val(),
            likebtn_account_api_key: jQuery(":input[name='likebtn_account_api_key']:first").val()
        },
        success: function(response) {
            var result_text = '';
            if (typeof(response.result_text) != "undefined") {
                result_text = response.result_text;
            }
            jQuery(".likebtn_manual_sync_container:first").text(result_text);
            if (typeof(response.result) == "undefined" || response.result != "success") {
                jQuery(".likebtn_manual_sync_container:first").css('color', 'red');
                if (typeof(response.message) != "undefined") {
                    var text = jQuery(".likebtn_manual_sync_container:first").html() + ': ' + response.message;
                    jQuery(".likebtn_manual_sync_container:first").html(text);
                }
            } else {
                jQuery(".likebtn_manual_sync_container:first").css('color', 'green');
            }

        },
        error: function(response) {
            jQuery(".likebtn_manual_sync_container:first").html(likebtn_msg_error).css('color', 'red');
        }
    });
}

// System check
function systemCheck(loader_src)
{
    jQuery(".likebtn_sc_container:first").html('<img src="' + loader_src + '" />');

    jQuery.ajax({
        type: 'POST',
        dataType: "json",
        url: ajaxurl,
        data: {
            action: 'likebtn_system_check'
        },
        success: function(response) {
            var result_text = '';
            if (typeof(response.result_text) != "undefined") {
                result_text = response.result_text;
            }
            jQuery(".likebtn_sc_container:first").text(result_text);
            if (typeof(response.result) == "undefined" || response.result != "success") {
                jQuery(".likebtn_sc_container:first").css('color', 'red');
            } else {
                jQuery(".likebtn_sc_container:first").css('color', 'green');
            }

            if (typeof(response.result_html) != "undefined") {
                var sc_win = likebtnPopup("", result_text);
                sc_win.document.body.innerHTML = response.result_html;
            }
        },
        error: function(response) {
            jQuery(".likebtn_sc_container:first").html(likebtn_msg_error).css('color', 'red');
        }
    });
}

// Send test vote notification
function sendTestVoteNotification(loader_src)
{
    jQuery(".likebtn_vn_message:first").html('<img src="' + loader_src + '" />');

    jQuery.ajax({
        type: 'POST',
        dataType: "json",
        url: ajaxurl,
        data: {
            action: 'likebtn_test_vote_notification',
            options: {
                'likebtn_notify_to': jQuery(":input[name='likebtn_notify_to']:first").val(),
                'likebtn_notify_from': jQuery(":input[name='likebtn_notify_from']:first").val(),
                'likebtn_notify_subject': jQuery(":input[name='likebtn_notify_subject']:first").val(),
                'likebtn_notify_text': jQuery(":input[name='likebtn_notify_text']:first").val()
            }
        },
        success: function(response) {
            var result_text = '';
            jQuery(".likebtn_vn_message:first").html('');
            if (typeof(response.result_text) != "undefined") {
                result_text = response.result_text;
            }
            jQuery(".likebtn_vn_container:first").text(result_text);
            if (typeof(response.result) == "undefined" || response.result != "success") {
                jQuery(".likebtn_vn_container:first").css('color', 'red');
            } else {
                jQuery(".likebtn_vn_container:first").css('color', 'green');
            }
        },
        error: function(response) {
            jQuery(".likebtn_vn_message:first").html('');
            jQuery(".likebtn_vn_container:first").html(likebtn_msg_error).css('color', 'red');
        }
    });
}

// enable/disable elements depending on the plan
function planChange(plan_id)
{
    // come through all plan dependent elements
    jQuery(".plan_dependent").each(function() {

        var classes;
        var class_name;
        var option_plan;
        var option_plan_id;
        var available = false;

        if (jQuery(this).attr('class')) {
            classes = jQuery(this).attr('class').split(/\s+/);

            for (var i = 0; i < classes.length; i++) {
                class_name  = classes[i];
                if (!class_name) {
                    continue;
                }
                option_plan = class_name.replace(/plan_/, '');

                if (!option_plan) {
                    continue;
                }
                if (typeof(plans[option_plan]) == "undefined") {
                    continue;
                }
                option_plan_id = plans[option_plan];

                if (plan_id >= option_plan_id) {
                    available = true;
                }
            }
        } else {
            available = true;
        }

        if (available) {
            jQuery(this).removeClass('likebtn_disabled').find(':input').removeAttr('readonly').removeClass('disabled');
            /*if (this.tagName.toUpperCase() === 'SELECT') {
                jQuery(this).removeAttr('onchange');
            }*/
        } else {
            jQuery(this).addClass('likebtn_disabled').find(':input').each(function(index, el) {
                jQuery(el).attr('readonly', 'readonly').addClass('disabled').attr('onclick', 'return false');
                /*if (this.tagName.toUpperCase() === 'SELECT') {
                    jQuery(this).attr('onchange', "this.defaultIndex=this.selectedIndex;");
                }*/
                // Disable AddThis select2
                if (el.id && el.id == 'settings_addthis_service_codes') {
                    jQuery(el)
                        .attr("disabled", "disabled")
                        .addClass('disabled')
                        .select2Sortable('destroy');
                }
                if (el.id && el.id == 'popup_donate_input') {
                    jQuery("#popup_donate_trigger").attr('href', 'javascript:void(0);');
                }
            });
        }
    });
}

// reset settings
function resetSettings(entity_name, parameters)
{
    var input;
    var default_value;

    if (!confirm(likebtn_msg_reset)) {
        return false;
    }

    for (option_name in parameters) {
        input = jQuery('#use_settings_from_container_'+entity_name+' :input[name^="likebtn_'+option_name+'_'+entity_name+'"]:first');

        default_value = parameters[option_name];

        if (input.attr('type') == 'checkbox') {
            jQuery('#use_settings_from_container_'+entity_name+' :input[name^="likebtn_'+option_name+'_'+entity_name+'"]').removeAttr('checked');
            jQuery('#use_settings_from_container_'+entity_name+' :input[name^="likebtn_'+option_name+'_'+entity_name+'"][value="'+default_value+'"]').attr('checked', 'checked');
        } else if(input.attr('type') == 'radio') {
            jQuery('#use_settings_from_container_'+entity_name+' :input[name^="likebtn_'+option_name+'_'+entity_name+'"][value="'+default_value+'"]').attr('checked', 'checked');
        } else {
            input.val(default_value);
        }
        input.change();
    }

    // Manual reset
    likebtnSetChecked(jQuery("#settings_form .theme_type_radio:first"));
    likebtnSetChecked(jQuery("#settings_form .icon_l_type_radio:first"));
    likebtnSetChecked(jQuery("#settings_form .icon_d_type_radio:first"));

    displayFields();
    likebtnRefreshPreview(true);
}

// select/unselect items
function statisticsItemsCheckbox(el)
{
    if (jQuery(el).is(':checked')) {
        jQuery("#statistics_container .item_checkbox").attr("checked", "checked");
    } else {
        jQuery("#statistics_container .item_checkbox").removeAttr("checked");
    }
}

// Edit statistics items
function statisticsEdit(entity_name, entity_id, type, cur_value, plan, text_enter, text_upgrade, text_error)
{
    if (entity_name === '' || entity_id === '' || type === '') {
        return false;
    }

    if (typeof(plan) != "undefined" && parseInt(plan) >= plans.ultra) {
        var value = prompt(text_enter, cur_value);
        if (value == null) {
            return false;
        }
    } else {
        alert(text_upgrade);
        return false;
    }

    if (type === 'like') {
        internal_type = 1;
    } else {
        internal_type = -1;
    }

    var target_el = jQuery("#item_"+entity_id+" .item_"+type+":first");
    var value_backup = target_el.text();

    // Show loader
    target_el.html('<img src="'+likebtn_spinner_src+'" />');

    jQuery.ajax({
        url: ajaxurl,
        method: "POST",
        data: {
            action: 'likebtn_edit_item',
            entity_name: entity_name,
            entity_id: entity_id,
            type: internal_type,
            value: value
        },
        dataType: "json",
        success: function(data) {
            if (data) {
                if (data.result == "success") {
                    if (typeof(data.value) !== "undefined") {
                        target_el.text(data.value);
                    }
                } else {
                    if (typeof(data.message) !== "undefined") {
                        alert(data.message);
                    } else {
                        alert(text_error);
                    }
                    target_el.text(value_backup);
                }
            } else {
                alert(text_error);
            }
        },
        error: function(data) {
            alert(text_error);
            target_el.text(value_backup);
        }
    });

    return true;
}

// Universal go to tab
function likebtnGotoTab(tab, content_wrapper, content_wrapper_id, wrapper_selector, tab_perfix) {

    if (tab == 'popup') {
        likebtnPreviewDonate();
    }

    if (typeof(tab_perfix) == 'undefined') {
        tab_perfix = 'likebtn_tab_';
    }

    // Content
    jQuery(content_wrapper).addClass('hidden');
    jQuery(content_wrapper_id+tab).removeClass('hidden');

    // Tab
    jQuery(wrapper_selector+" .nav-tab").removeClass('nav-tab-active');
    jQuery(wrapper_selector+" .nav-tab."+tab_perfix+tab).addClass('nav-tab-active');
}

// Show subpage
/*function likebtnGotoSubpage(subpage) {

    if (!jQuery("#likebtn_subpage_wrapper_"+subpage).size()) {
        // Show first tab
        var subpage_id = jQuery(".likebtn_subpage:first").attr('id');
        if (subpage_id) {
            subpage = subpage_id.replace('likebtn_subpage_wrapper_', '');
        } else {
            // Could not find first tab
            return false;
        }
    }

    // Content
    jQuery(".likebtn_subpage").addClass('hidden');
    jQuery("#likebtn_subpage_wrapper_"+subpage).removeClass('hidden');

    // Tab
    jQuery("#likebtn_subpage_tab_wrapper .nav-tab").removeClass('nav-tab-active');
    jQuery("#likebtn_subpage_tab_wrapper .nav-tab.likebtn_tab_"+subpage).addClass('nav-tab-active');

    jQuery("#likebtn_subpage").val(subpage);
}*/

// Detect if subpage is selected and goto it
/*function likebtnDetectSubpage()
{
    hash = window.location.hash;

    if (hash && hash.substr(0, 29) == '#likebtn_subpage_') {
        likebtnGotoSubpage(hash.substr(29));
    }
}*/

// Open popup window
function likebtnPopup(url, name, height, width)
{
    if (typeof(width) === "undefined" || !width) {
        width = 1000;
    }
    if (typeof(height) === "undefined" || !height) {
        height = 600;
    }
    if (width > jQuery(window).width()) {
        width = jQuery(window).width();
    }

    var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
    var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

    var w = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    var h = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

    var left = ((w / 2) - (width / 2)) + dualScreenLeft;
    var top = ((h / 2) - (height / 2)) + dualScreenTop;

    var p = window.open(url, name, 'height='+height+',width='+width+',top='+top+',left='+left+',toolbar=0,scrollbars=yes');
    p.focus();

    return p;
}

// On Save Buttons
function likebtnOnSaveButtons()
{
    // Select2
    jQuery('#settings_popup_content_order_input').val(likebtnGetMultipleSelect2Val('#settings_popup_content_order'));

    // Do not save default addthis
    var addthis_service_codes = likebtnGetMultipleSelect2Val("#settings_addthis_service_codes");
    var addthis_service_codes_default_value = jQuery("#settings_addthis_service_codes").attr('data-default');

    if (addthis_service_codes && addthis_service_codes === addthis_service_codes_default_value) {
        addthis_service_codes = '';
    }
    jQuery('#settings_addthis_service_codes_input').val(addthis_service_codes);
    
}

// Format image dropdown
function likebtnFormatSelect(state)
{
    // optgroup
    /*if (!state.id) {
        return state.text;
    }*/
    var image_name;

    if (state.id) {
        image_name = state.id.toLowerCase();
    } else {
        return state.text;
    }
    var select_id = jQuery(state.element).parents('select:first').attr('id');
    var option_html = '<img src="' + window["likebtn_path_"+select_id] + image_name + '.png" style="border-style:none;" alt="' + image_name + '" />';

    if (typeof(state.text) !== "undefined" && state.text) {
        option_html += ' &nbsp;<span class="image_dropdown_text">-&nbsp; ' + state.text + '</span>';
    }

    return option_html;
}

// Get ordered select2 val
function likebtnGetMultipleSelect2Val(selector)
{
    var vals = [];
    var objects = jQuery(selector).select2('data');
    for (var i in objects) {
        vals.push(objects[i].id);
    }

    return vals.join();
}

// Set select2 data (keeps ordering)
function likebtnSetMultipleSelect2Val(selector, val, with_text)
{
    var vals = val.split(',');
    var data = [];
    var text;

    for (var i in vals) {
        text = '';
        val = vals[i].trim();

        if (with_text) {
            // Find text in select
            text = jQuery(selector+" option[value='"+val+"']").text();
        }
        if (!text) {
            text = val;
        }

        data.push({
            id: val,
            text: text
        });
    }

    jQuery(selector).select2('data', data);
}

// Format image dropdown
function likebtnAddthisSelectResult(state)
{
    var option_html = '<i class="likebtn_at16_' + state.text.toLowerCase() + '"></i> ' + state.text;
    return option_html;
}
// Format image dropdown
function likebtnAddthisSelectSelection(state)
{
    if (typeof(state.text) === "undefined" || !state.text) {
        return '';
    }
    var option_html = '<i class="likebtn_at16_' + state.text.toLowerCase() + '" title="' + state.text + '"></i>';
    return option_html;
}

// Display field values from settings
function displayFields()
{
    var lang = jQuery("#settings_lang").val();

    // AddThis
    if (jQuery("#settings_addthis_service_codes_input").val()) {
        likebtnSetMultipleSelect2Val('#settings_addthis_service_codes', jQuery('#settings_addthis_service_codes_input').val());
    } else {
        likebtnSetMultipleSelect2Val('#settings_addthis_service_codes', likebtnGetDefaultAddthis(lang));
    }

    // Popup content order
    likebtnSetMultipleSelect2Val('#settings_popup_content_order', jQuery('#settings_popup_content_order_input').val(), true);

    likebtnOptionChange();

    // Display donate buttons
    likebtnPreviewDonate();

    // Must come before displayTranslationsOnLoad
    displayAddthis();
    displayTranslationsOnLoad();
}

// Preview donate buttons
function likebtnPreviewDonate()
{
    if (typeof(likebtnDGGetPreview) === "undefined") {
        return false;
    }
    jQuery("#donate_pveview").html(likebtnDGGetPreview('#popup_donate_input'));
}

// Get default AddThis value
function likebtnGetDefaultAddthis(lang)
{
    if (typeof(lang) === undefined) {
        lang = jQuery("#settings_lang").val();
    }
    var default_value = likebtn_default_settings.addthis_service_codes.default_values;

    if (typeof(lang) !== "undefined" && typeof(default_value[lang]) !== "undefined") {
        return default_value[lang];
    } else {
        return default_value.all;
    }
}

// Display AddThis
function displayAddthis()
{
    var default_value;

    var lang = jQuery("#settings_lang").val();

    // On load
    if (!likebtn_prev_lang) {
        likebtn_prev_lang = lang;
    }

    default_value = likebtnGetDefaultAddthis(lang);
    prev_default_value = likebtnGetDefaultAddthis(likebtn_prev_lang);

    if (likebtnGetMultipleSelect2Val("#settings_addthis_service_codes") == prev_default_value) {
        likebtnSetMultipleSelect2Val('#settings_addthis_service_codes', default_value);
    }
    // Remember default value
    jQuery("#settings_addthis_service_codes").attr('data-default', default_value);
}

// Display translations on settings load
function displayTranslationsOnLoad(settings)
{
    var lang = jQuery("#settings_lang").val();
    // Remember lang
    likebtn_prev_lang = lang;
}

// Display translations on lang change
function displayTranslations()
{
    var lang = jQuery("#settings_lang").val();

    // Remember lang
    likebtn_prev_lang = lang;
}

// Refresh like button preview
function likebtnRefreshPreview(ignore_timeout)
{
    var wrapper = jQuery(".preview_container:first .likebtn-wrapper:first");
    var properties = [];
    var property_name;
    var entity_regexp;
    var entity_name;

    if (typeof(ignore_timeout) === "undefined" || !ignore_timeout) {
        if (!likebtn_preview_wait) {
            likebtn_preview_wait = true;
            setTimeout(function(){ likebtnRefreshPreview(true); }, likebtn_preview_refresh);
        }
        return;
    } else {
        likebtn_preview_wait = false;
    }

    entity_name = jQuery("#likebtn_entity_name_field").val();
    if (!wrapper || !entity_name) {
        return false;
    }

    // Prepare field names
    jQuery("#settings_form :input").each(function(index, element) {
        var field = jQuery(element);
        var name = field.attr('name');
        var value = field.val();
        if (!name || name.indexOf('[') != -1) {
            return;
        }
        // Cut entity name out
        entity_regexp = new RegExp("_"+likebtnEscapeRegExp(entity_name)+"$");
        property_name = name.replace(entity_regexp, '');
        if ((name.indexOf('likebtn_settings_') == -1 && typeof(likebtn_sci[property_name]) == "undefined") || field.hasClass('disabled') ) {
            return;
        }

        // Format value
        if (field.attr('type') == 'checkbox' && !field.is(':checked')) {
            value = '0';
        }
        // Find selected radio
        if (field.attr('type') == 'radio' && !field.is(':checked')) {
            return;
        }

        property_name = property_name.replace(/^likebtn_settings_/, '');
        property_name = property_name.replace(/^likebtn_/, '');

        // Fetch dynamic fields
        if (property_name == 'addthis_service_codes') {
            value = likebtnGetMultipleSelect2Val("#settings_addthis_service_codes");
        }
        if (property_name == 'popup_content_order') {
            value = likebtnGetMultipleSelect2Val('#settings_popup_content_order');
        }
        var user_logged_in = jQuery("#settings_form .user_logged_in_radio:checked").val();
        if (property_name == 'user_logged_in_alert' && user_logged_in != 'alert' && user_logged_in != 'alert_btn' && user_logged_in != 'modal') {
            return;
        }

        // check default
        if (typeof(reset_settings[property_name]) !== "undefined") {
            if (value == reset_settings[property_name]) {
                value = '';
            }
        }

        properties[property_name] = value;
    });

    if (typeof(LikeBtn) !== "undefined") {
        wrapper[0].style.lineHeight = '';
        LikeBtn.apply(wrapper[0], properties, ['identifier', 'site_id']);
    }

    // Show shortcode
    likebtnShowShortcode('likebtn_sc', properties);
}

// Escape string against regular expression
function likebtnEscapeRegExp(str) {
  return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
}

// Fix preview container
function likebtnFixPreview(no_event)
{
    likebtn_preview = jQuery("#preview_fixer");
    if (!likebtn_preview.size()) {
        return;
    }
    likebtn_preview_offset = likebtn_preview.offset().top - 40;

    if (typeof(no_event) == "undefined") {
        jQuery(window).scroll(function() {
            likebtnScrollPreview();
        });
        jQuery(window).resize(function() {
            likebtnFixPreview(true);
        });
    }
}

function likebtnScrollPreview()
{
    var scroll_top = jQuery(window).scrollTop();
    if (!likebtn_preview.is(':visible') || (likebtn_preview_position == 'fixed' && (scroll_top < likebtn_preview_offset || likebtn_pin))) {
        likebtn_preview
            .addClass('likebtn_preview_static')
            .removeClass('likebtn_preview_fixed')
            .width('auto');
        
        jQuery('.likebtn_subpage:first').css({paddingTop: '0'});

        likebtn_preview_position = 'static';

    } else if (likebtn_preview_position == 'static' && scroll_top >= likebtn_preview_offset && !likebtn_pin) {
        likebtn_preview
            .addClass('likebtn_preview_fixed')
            .removeClass('likebtn_preview_static')
            .width(jQuery("#settings_container").width());

        jQuery('.likebtn_subpage:first').css({
            paddingTop: likebtn_preview.height() + parseInt(likebtn_preview.css('marginBottom')) + 'px'
        });

        likebtn_preview_position = 'fixed';
    }
}

// Refresh plan
function refreshPlan(msg_error, msg_success)
{
    jQuery('#likebtn_refresh_trgr').hide();
    jQuery('#likebtn_refresh_ldr').show();
    jQuery("#likebtn_refresh_msg_wr").hide();
    jQuery("#likebtn_refresh_error").hide();
    jQuery("#likebtn_refresh_success").hide();

    jQuery.ajax({
        type: 'POST',
        dataType: "json",
        url: ajaxurl,
        data: {
            action: 'likebtn_refresh_plan'
        },
        success: function(response) {
            if (typeof(response.reload) != "undefined" && response.reload) {
                location.reload(false);
                return;
            } else if (typeof(response.html) != "undefined" && response.html) {
                jQuery("#likebtn_plan_wr").html(response.html);
                jQuery("#likebtn_refresh_success").html(msg_success).show();
                jQuery("#likebtn_refresh_msg_wr").show();
            } else if (typeof(response.message) != "undefined" && response.message) {
                jQuery("#likebtn_refresh_error").html(response.message).show();
                jQuery("#likebtn_refresh_msg_wr").show();
            } else {
                jQuery("#likebtn_refresh_error").html(msg_error).show();
                jQuery("#likebtn_refresh_msg_wr").show();
            }
            jQuery('#likebtn_refresh_trgr').show();
            jQuery('#likebtn_refresh_ldr').hide();

            likebtnApplyTooltips();
        },
        error: function(response) {
            jQuery("#likebtn_refresh_error").html(msg_error).show();
            jQuery("#likebtn_refresh_msg_wr").show();
            jQuery('#likebtn_refresh_trgr').show();
            jQuery('#likebtn_refresh_ldr').hide();
        }
    });
}

// Switch to FREE plan
function goFree(msg_text, msg_error, msg_success)
{
    var value = prompt(msg_text);
    if (!value || value.toLowerCase() != 'free') {
        return;
    }

    jQuery('#likebtn_refresh_trgr').hide();
    jQuery('#likebtn_refresh_ldr').show();
    jQuery("#likebtn_refresh_msg_wr").hide();
    jQuery("#likebtn_refresh_error").hide();
    jQuery("#likebtn_refresh_success").hide();

    jQuery.ajax({
        type: 'POST',
        dataType: "json",
        url: ajaxurl,
        data: {
            action: 'likebtn_go_free'
        },
        success: function(response) {
            if (typeof(response.reload) != "undefined" && response.reload) {
                location.reload(false);
                return;
            } else if (typeof(response.html) != "undefined" && response.html) {
                jQuery("#likebtn_plan_wr").html(response.html);
                jQuery("#likebtn_refresh_success").html(msg_success).show();
                jQuery("#likebtn_refresh_msg_wr").show();
            } else if (typeof(response.message) != "undefined" && response.message) {
                jQuery("#likebtn_refresh_error").html(response.message).show();
                jQuery("#likebtn_refresh_msg_wr").show();
            } else {
                jQuery("#likebtn_refresh_error").html(msg_error).show();
                jQuery("#likebtn_refresh_msg_wr").show();
            }
            jQuery('#likebtn_refresh_trgr').show();
            jQuery('#likebtn_refresh_ldr').hide();

            likebtnApplyTooltips();
        },
        error: function(response) {
            jQuery("#likebtn_refresh_error").html(msg_error).show();
            jQuery("#likebtn_refresh_msg_wr").show();
            jQuery('#likebtn_refresh_trgr').show();
            jQuery('#likebtn_refresh_ldr').hide();
        }
    });
}

// Apply tipsy tooltips
function likebtnApplyTooltips()
{
    jQuery('#likebtn .likebtn_help, #likebtn .premium_feature, #likebtn .likebtn_ttip, #likebtn .likebtn_help_simple').each(function(index, el) {
        var gravity = jQuery(el).attr('data-likebtn_ttip_gr');
        if (gravity) {
            jQuery(el).tipsy({gravity: gravity});
        } else {
            jQuery(el).tipsy({gravity: 's'});
        }
    });
}

// Get LikeBtn account data
function likebtnGetAccountData(url)
{
    // Add domain
    url += '?add_website='+window.location.hostname;

    likebtn_popup = likebtnPopup(url, 'get_account_data');
    likebtn_popup_timer = setInterval(likebtnOnGetAccountDataClose, 500);
}

// Track popup close
function likebtnOnGetAccountDataClose()
{
    if (likebtn_popup.closed) {
        clearInterval(likebtn_popup_timer);

        jQuery.ajax({
            type: 'get',
            dataType: 'jsonp',
            url: 'https://likebtn.com/en/customer.php/api',
            data: {
                action: 'account_data',
                domain: window.location.hostname
            },
            success: function(data) {
                if (data.result && data.result == 'success' && data.response) {
                    if (data.response.email) {
                        jQuery("#likebtn_account_email_input").val(data.response.email);
                    }
                    if (data.response.api_key) {
                        jQuery("#likebtn_account_api_key_input").val(data.response.api_key);
                    }
                    if (data.response.site_id) {
                        jQuery("#likebtn_site_id_input").val(data.response.site_id);
                    }
                    accountChange();
                    /*if (!jQuery("#likebtn_sync_inerval_input").val()) {
                        jQuery("#likebtn_sync_inerval_input").val('5');
                    }*/
                }
            },
            error: function(data) {
                
            }
        });
    }
}

// Statistics bulk actions
function likebtnStatsBulkAction(action, plan, msg_confirm)
{
    if (typeof(plan) != "undefined" && parseInt(plan) >= plans.vip) {

        if (jQuery("#statistics_container .item_checkbox:checked").size()) {
            if (confirm(msg_confirm)) {
                jQuery("#stats_bulk_action").val(action);
                jQuery("#stats_actions_form").submit();
            } else {
                return false;
            }
        } else {
            alert(likebtn_msg_select_items);
            return false;
        }
    } else {
        alert(likebtn_msg_upgrade_vip);
        return false;
    }
}

// Statistics bulk actions
function likebtnFullReset(msg_confirm)
{
    var value = prompt(msg_confirm);
    if (value && value.toLowerCase() == 'reset') {
        jQuery("#likebtn_fr_form input[name='likebtn_full_reset']:first").val('tZFWPdFC');
        jQuery("#likebtn_fr_form").submit();
    }
}

// Export
function likebtnStatsExport(plan)
{
    if (typeof(plan) != "undefined" && parseInt(plan) >= plans.pro) {
        var likebtn_export = jQuery("#likebtn_export").clone();
        likebtn_export.removeClass('hidden');
        likebtn_export.removeAttr('id');

        likebtn_export.dialog({
            resizable: false,
            autoOpen: false,
            modal: true,
            width: 430,
            title: likebtn_msg_export,
            draggable: false,
            show: 'fade',
            dialogClass: 'likebtn_dlg',
            open: function() {
                jQuery('.ui-widget-overlay, .likebtn_export .likebtn-button-close').bind('click', function() {
                    likebtn_export.dialog('close');
                });
            },
            position: { 
                my: "center", 
                at: "center" 
            }
        });

        likebtn_export.dialog('open');
    } else {
        alert(likebtn_msg_upgrade_pro);
        return false;
    }
}

// Export votes
function likebtnVotesExport(msg_export)
{
    var likebtn_export = jQuery("#likebtn_export").clone();
    likebtn_export.removeClass('hidden');
    likebtn_export.removeAttr('id');

    likebtn_export.dialog({
        resizable: false,
        autoOpen: false,
        modal: true,
        width: 430,
        title: msg_export,
        draggable: false,
        show: 'fade',
        dialogClass: 'likebtn_dlg',
        open: function() {
            jQuery('.ui-widget-overlay, .likebtn_export .likebtn-button-close').bind('click', function() {
                likebtn_export.dialog('close');
            });
        },
        position: { 
            my: "center", 
            at: "center" 
        }
    });

    likebtn_export.dialog('open');
}

// Toggle shortcode container
function likebtnToggleShortcode(id)
{
    likebtnRefreshPreview();
    jQuery('#'+id).toggle();
}

// Show shortcode
function likebtnShowShortcode(id, properties)
{
    var shortcode = '[likebtn';
    var value = '';
    var identifier_type = jQuery("#likebtn_sc_wr [name='likebtn_identifier_type']:checked:first").val();

    if (identifier_type != 'post_id') {
        var likebtn_identifier = jQuery("#likebtn_sc_identifier").val();
        shortcode += ' identifier="'+likebtn_identifier+'"';
    }

    for (var name in properties) {
        if (properties[name] === null) {
            continue;
        }
        value = properties[name];
        // Miss default parameters
        if ((typeof(reset_settings[name]) !== undefined && reset_settings[name] === value) ||
            (typeof(reset_settings['settings_'+name]) !== undefined && reset_settings['settings_'+name] === value) ||
            (name == 'addthis_service_codes' && value == likebtnGetDefaultAddthis())
        ) {
            continue;
        }
        value = value.replaceAll('"', '&quot;');
        // Escape brackets
        value = value.replaceAll('[', '&#91;');
        value = value.replaceAll(']', '&#93;');
        if (value !== '') {
            shortcode += ' '+name+'="'+value+'"';
        }
    }
    shortcode += ']';

    jQuery('#'+id).val(shortcode);

    // Hint
    if (identifier_type == 'post_id') {
        jQuery(".likebtn_sc_identifier_custom").addClass('hidden');
    } else {
        jQuery(".likebtn_sc_identifier_custom").removeClass('hidden');
    }
}

// Show widget shortcode
function likebtnWidgetShortcode(mnemonic, sc_name, msg_save, no_toggle)
{
    var properties = [];
    var entity_name = [];

    if (!jQuery("#likebtn_widget_"+mnemonic).is(':visible')) {
        alert(msg_save);
    }

    jQuery("#likebtn_widget_"+mnemonic+" :input").each(function(index, element) {
        var field = jQuery(element);
        var name = field.attr('data-property');
        var value = field.val();

        if (!name || field.hasClass('disabled')) {
            return;
        }

        if (name == 'entity_name' || name == 'include_categories' || name == 'exclude_categories') {
            return;
        }

        // Format value
        if (field.attr('type') == 'checkbox' && !field.is(':checked')) {
            value = '0';
        }
        // Find selected radio
        if (field.attr('type') == 'radio' && !field.is(':checked')) {
            return;
        }

        properties[name] = value;
    });

    // Items to show
    jQuery("#likebtn_widget_"+mnemonic+" :input[data-property='entity_name']:checked").each(function(index, element) {
        entity_name.push(jQuery(element).val());
    });
    properties['entity_name'] = entity_name.join(',');

    properties['include_categories'] = likebtnGetMultipleSelect2Val("#likebtn_widget_"+mnemonic+" :input[data-property='include_categories']:first");
    properties['exclude_categories'] = likebtnGetMultipleSelect2Val("#likebtn_widget_"+mnemonic+" :input[data-property='exclude_categories']:first");

    var shortcode = '['+sc_name;

    for (var name in properties) {
        value = properties[name];
        value = value.replaceAll('"', '&quot;');
        // Escape brackets
        value = value.replaceAll('[', '&#91;');
        value = value.replaceAll(']', '&#93;');
        shortcode += ' '+name+'="'+value+'"';
    }
    shortcode += ']';

    jQuery('#likebtn_sc_'+mnemonic).val(shortcode);

    if (typeof(no_toggle) === "undefined" || !no_toggle) {
        jQuery('#likebtn_sc_wr_'+mnemonic).toggle();
    }
}

// Get data by type and id
function couchDbView(id, view, callback, one, type, attempt) {
    //id = '138c209c-3b28-f831-de29-4d05fcb328ed:hz2mc57m';
    var key;
    if (type) {
        key = JSON.stringify([type, "id", id+""]);
    } else {
        key = '"'+id+'"';
    }
    var url = likebtn_couch_db_url+'/'+view+'?key='+key;

    jQuery.ajax({
        url: url,
        type: 'get',
        dataType: 'jsonp',
        //timeout: likebtn_couch_db_timeout,
        success: function(response) {
            var data = null;
            if (typeof(response.rows) !== "undefined" && 
                typeof(response.rows[0]) !== "undefined" && 
                typeof(response.rows[0].value) !== "undefined")
            {
                if (typeof(one) !== "undefined" && one) {
                    data = response.rows[0].value;
                } else {
                    data = [];
                    for (var i in response.rows) {
                        if (typeof(response.rows[i].value) !== "undefined") {
                            data.push(response.rows[i].value);
                        } else {
                            data.push(response.rows[i]);
                        }
                    }
                }
            }
            callback("success", data);
        },
        error: function(status) {
            if (typeof(attempt) === "undefined") {
                attempt = 1;
            }
            if (attempt < likebtn_couch_db_retry) {
                // Retry
                attempt++;
                couchDbView(id, view, callback, one, type, attempt);
            } else {
                callback("error", status);
            }
        }
    });
}

// Load website reports
function loadReports()
{
    // Hide errors
    jQuery(".reports-error:first").hide();

    // Check if report is already loaded
    if (jQuery("#likebtn_reports").hasClass("reports-loaded")) {
        return;
    }

    if (!likebtn_reports_id) {
        jQuery("#likebtn_reports .reports-total:first").html(0);
        jQuery("#likebtn_reports .reports-like:first").html(0);
        jQuery("#likebtn_reports .reports-dislike:first").html(0);
    }

    // Load stats from storage
    couchDbView(likebtn_reports_id, likebtn_couch_db_view_main,
        function(result, response) {

            if (result !== 'success') {
                jQuery("#likebtn_reports .reports-error:first").show();
                return;
            }
            jQuery("#likebtn_reports").addClass("reports-loaded");

            if (!response) {
                response = {};
            }

            var like = parseInt(response.like);
            if (isNaN(like)) {
                like = '0'
            }
            var dislike = parseInt(response.dislike);
            if (isNaN(dislike)) {
                dislike = '0'
            }
            var total = parseInt(like+dislike);
            if (isNaN(total)) {
                total = '0'
            }
            jQuery("#likebtn_reports .reports-total:first").html(total);
            jQuery("#likebtn_reports .reports-like:first").html(like);
            jQuery("#likebtn_reports .reports-dislike:first").html(dislike);

            var stats = {};
            if (response.stats) {
                try {
                    stats = JSON.parse(response.stats);
                } catch (e) {}
            }
            
            // Graphs
            Graph.setOptions({
                lang: global_graph_lang
            });
            
            var chart_options = {
                series: reportsGetSeries(stats.d),
                chart: {
                    renderTo: jQuery("#likebtn_reports .reports-graph-d:first")[0]
                },
                title : {
                    text : ''
                },
                plotOptions: {
                    line: {
                        cursor: 'pointer'
                    }
                },
                rangeSelector : {
                    inputEnabled: false
                },
                rangeSelector: {
                    buttons: [],
                    inputDateFormat: '%d.%m.%Y',
                    inputEditDateFormat: '%d.%m.%Y',
                    inputBoxBorderColor: 'white'
                },
                navigator: {
                    enabled: false
                },
                scrollbar: {
                    enabled: false
                }
            };
            
            var reports_chart = new Graph.StockChart(chart_options);

            // Year
            chart_options.series = reportsGetSeries(stats.m, 'm');
            chart_options.chart.renderTo = jQuery("#likebtn_reports .reports-graph-m:first")[0];
            chart_options.rangeSelector.inputDateFormat = '%m.%Y';
            chart_options.rangeSelector.inputEditDateFormat = '%m.%Y';
            chart_options.title.text = '';
            reports_chart = new Graph.StockChart(chart_options);
        },
        true,
        likebtn_couch_db_type
    );
}

// Show heat map
/*function showMap()
{
    if (!jQuery(".reports-map:first").size() || !likebtn_reports_loc.length) {
        return;
    }
    var map = new google.maps.Map(jQuery(".reports-map:first")[0], {
        zoom: 1,
        center: {lat: 37.775, lng: -122.434},
        mapTypeId: google.maps.MapTypeId.SATELLITE
    });

    var points = [];
    for (i in likebtn_reports_loc) {
        points.push(new google.maps.LatLng(likebtn_reports_loc[i][0], likebtn_reports_loc[i][1]));
    }

    heatmap = new google.maps.visualization.HeatmapLayer({
        data: points,
        map: map
    });
}*/

// Get series from data
function reportsGetSeries(data, mode)
{
    if (typeof(mode) == "undefined") {
        mode = 'd';
    }
    // Build series
    var series = [
        {
            name: likebtn_msg_votes, 
            data: [], 
            color: "#337ab7",
            marker: {
                enabled: true,
                radius: 4,
                symbol: "circle"
            }
        },
        {
            name: likebtn_msg_likes,
            data: [],
            color: "#5cb85c",
            marker: {
                enabled: true,
                radius: 4,
                symbol: "circle"
            }
        },
        {
            name: likebtn_msg_dislikes,
            data: [],
            color: "#f0ad4e",
            marker: {
                enabled: true,
                radius: 4,
                symbol: "circle"
            }
        }
    ];

    if (!data) {
        return series;
    }

    var i = 0;
    var last_ts = 0;
    for (date_str in data) {
        var date = reportsStrToDate(date_str);
        if (!date) {
            continue;
        }
        last_ts = date.getTime();

        // Broken date
        if (!last_ts) {
            continue;
        }

        var votes = data[date_str];
        var like = parseInt(votes[0]) || 0;
        var dislike = parseInt(votes[1]) || 0;
        var total = like+dislike;

        series[0].data[i] = [last_ts, total];
        series[1].data[i] = [last_ts, like];
        series[2].data[i] = [last_ts, dislike];
        i++;
    }

    // No data
    if (series[0].data.length == 0) {
        return series;
    }

    // Add zero values
    var ts = series[0].data[0][0];
    var i = 0;
    while (ts < last_ts) {
        if (mode == 'd') {
            ts = ts + 86400000;
        } else if (mode == 'm') {
            var date = new Date();
            date.setTime(ts);
            date = addMonths(date, 1);
            ts = date.getTime();
        }
        if (series[0].data[i+1] && series[0].data[i+1][0] !== ts) {
            arrayInsertAfter(series[0].data, i, [ts, 0]);
            arrayInsertAfter(series[1].data, i, [ts, 0]);
            arrayInsertAfter(series[2].data, i, [ts, 0]);
        }
        i++;
    }

    // Prepend empty values
    var ts = series[0].data[0][0];
    var diff = 0;
 
    if (mode == 'd') {
        diff = likebtn_report_store_days - series[0].data.length;
    } else if (mode == 'm') {
        diff = 12 - series[0].data.length;
    }

    for (i=0; i<diff; i++) {
        if (mode == 'd') {
            ts = ts - 86400000;
        } else if (mode == 'm') {
            var date = new Date();
            date.setTime(ts);
            date = addMonths(date, -1);
            ts = date.getTime();
        }
        
        arrayInsertBefore(series[0].data, 0, [ts, 0]);
        arrayInsertBefore(series[1].data, 0, [ts, 0]);
        arrayInsertBefore(series[2].data, 0, [ts, 0]);
    }

    return series;
}

// Convet str to date
function reportsStrToDate(str)
{
    if (str.length == 8) {
        return new Date(str.replace(/(\d{4})(\d{2})(\d{2})/,'$1-$2-$3T00:00:00Z'));
    } else if (str.length == 6) {
        return new Date(str.replace(/(\d{4})(\d{2})/,'$1-$2-01T00:00:00Z'));
    }

    return null;
}

// Add/diff month
function isLeapYearByValue(year) { 
    return (((year % 4 === 0) && (year % 100 !== 0)) || (year % 400 === 0)); 
};

function getDaysInMonthByValue(year, month) {
    return [31, (isLeapYearByValue(year) ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][month];
};

function isLeapYear(date) { 
    return isLeapYearByValue(date.getFullYear()); 
};

function getDaysInMonth(date) { 
    return getDaysInMonthByValue(date.getFullYear(), date.getMonth());
};

function addMonths(date, value) {
    var n = date.getDate();
    date.setDate(1);
    date.setMonth(date.getMonth() + value);
    date.setDate(Math.min(n, getDaysInMonth(date)));
    return date;
};

// Insert element after index
function arrayInsertAfter(arr, index, item) {
    arr.splice(index+1, 0, item);
};
function arrayInsertBefore(arr, index, item) {
    arr.splice(index, 0, item);
};

// Icons selector
function likebtnIconPick() {
    jQuery("#settings_container .likebtn-i-pick").click(function (e) {
        e.preventDefault();
        var target = jQuery(e.target);
        if (!target.attr('data-likebtn-wp-title')) {
            target = target.parents('a:first');
        }
        var likebtn_wp_media = wp.media({
            title: target.attr('data-likebtn-wp-title'),
            button: { text: likebtn_msg_set_img },
            multiple: false
        });
        likebtn_wp_media.on('select', function () {
            var attachment = likebtn_wp_media.state().get('selection').first().toJSON();
            var block = target.parents('.likebtn-is-block:first');
            target.removeClass('button button-large');
            block.find('.likebtn-is-inp:first').val(attachment.url+'#'+attachment.id).change();
            block.children().find('.likebtn-is-cap:first').addClass('hidden');
            block.children().find('img:first').attr('src', '').attr('src', attachment.url).removeClass('hidden');
            block.find('.likebtn-is-remove:first').removeClass('hidden');
        });
        likebtn_wp_media.open();
    });
}

// Remove selected icon
function likebtnIconRemove(el)
{
    var block = jQuery(el).parents('.likebtn-is-block:first');
    block.find('.likebtn-i-pick:first').addClass('button button-large');
    block.find('.likebtn-is-inp:first').val('').change();
    block.children().find('.likebtn-is-cap:first').removeClass('hidden');
    block.children().find('img:first').addClass('hidden');
    block.find('.likebtn-is-remove:first').addClass('hidden');

    likebtnRefreshPreview();
}

// Script on Buttons page
function likebtnScriptButtons(subpage, plan)
{
    jQuery(document).ready(function() {
        planChange(plan);

        // Image dropdown
        jQuery("select.image_dropdown").select2({
            formatResult: likebtnFormatSelect,
            formatSelection: likebtnFormatSelect,
            escapeMarkup: function(m) { return m; },
            minimumResultsForSearch: -1
        });

        jQuery("#settings_popup_content_order").select2({
            /*formatResult: likebtnPcoSelectResult,
            formatSelection: likebtnPcoSelectSelection,*/
            escapeMarkup: function(m) { return m; },
            dropdownCssClass: "likebtn_pco_container"
        }).select2Sortable();

        jQuery("#settings_addthis_service_codes").select2({
            formatResult: likebtnAddthisSelectResult,
            formatSelection: likebtnAddthisSelectSelection,
            escapeMarkup: function(m) { return m; },
            maximumSelectionSize: 8,
            dropdownCssClass: "likebtn_at16_conatiner"
        }).select2Sortable();

        jQuery("select.icon_dropdown").select2({
            dropdownCssClass: 'select2-celled',
            minimumResultsForSearch: -1,
            formatResult: likebtnIconFormatSelect,
            formatSelection: likebtnIconFormatSelect,
            escapeMarkup: function(m) { return m; }
        });

        jQuery("#likebtn_allow_forums").select2();

        var datetime_opts = {
            format: 'Y/m/d H:i'
        };
        if (!jQuery("#likebtn_voting_date").val()) {
           datetime_opts.startDate = likebtn_datetime;
        }
        jQuery("#likebtn_voting_date").datetimepicker(datetime_opts);

        var voting_created = parseInt(jQuery("#likebtn_voting_created").val());
        if (isNaN(voting_created)) {
            voting_created = 0;
        }
        jQuery("#likebtn_voting_created_cntr").durationPicker({"seconds":voting_created}).on("keyup change", function() {
            var seconds = jQuery(this).durationPicker("seconds");
            jQuery("#likebtn_voting_created").val(seconds);
        });
       
        // Radio images
        //jQuery('.image_toggle').buttonset();

        displayFields();

        // Color picker        
        // Change callback is called before changing input value
        jQuery('#settings_form .likebtn_cp').wpColorPicker({
            change: function(event, ui){
                likebtnRefreshPreview();
            }
        });

        // Events
        jQuery("#settings_lang").change(function() {
            // Must come before displayTranslations
            displayAddthis();
            displayTranslations();
        });

        // Refresh preview
        jQuery("#settings_container :input").on("keyup change", function(event) {
            likebtnOptionChange(event);
            likebtnRefreshPreview();
        });

        /// Sticky preview
        likebtnInitPin();
        // Fix preview
        likebtnFixPreview();
        likebtnScrollPreview();
        // Icons selectors
        likebtnIconPick();

        /*if (likebtnGetCookie('likebtn_pin')) {
            likebtnSetPin(true);
        }*/
        // Refresh preview
        jQuery("#likebtn_pin").on("keyup change", function(event) {
            likebtnPinChange(this);
        });
    });
}

function likebtnIconFormatSelect(state)
{
    return '<i class="lb-fi lb-fi-'+state.id+'"></i>';
}

// Display initial options
// Display options on change
function likebtnOptionChange(event)
{
    var target = null;
    if (event) {
        target = jQuery(event.target);
    }

    if (!target || target.hasClass('theme_type_radio')) {
        if (jQuery('#settings_form .theme_type_radio:checked').val() == 'custom') {
            jQuery("#settings_theme_custom").removeAttr('disabled').removeClass('disabled');
            
            jQuery("#settings_form .likebtn_custom").removeClass('hidden');
            jQuery("#settings_form .likebtn_custom :input[disabled!='disabled']").removeClass('disabled');
        } else {
            jQuery("#settings_theme_custom").attr('disabled', 'disabled').addClass('disabled');

            jQuery("#settings_form .likebtn_custom").addClass('hidden');
            jQuery("#settings_form .likebtn_custom :input[disabled!='disabled']").addClass('disabled');
        }
    }
    if (!target || target.hasClass('icon_l_type_radio')) {
        if (jQuery('#settings_form .icon_l_type_radio:checked').val() == 'url') {
            jQuery("#settings_icon_l").attr('disabled', 'disabled').addClass('disabled');
            jQuery("#settings_icon_l_url").removeAttr('disabled').removeClass('disabled');
            jQuery("#settings_icon_l_url_v").removeAttr('disabled').removeClass('disabled');

            if (jQuery("#settings_form .icon_l_type_radio:checked").val() == 'url') {
                jQuery("#settings_form .param_icon").addClass('hidden');
                jQuery("#settings_form .param_icon_l").addClass('hidden');
            }
        } else {
            jQuery("#settings_icon_l").removeAttr('disabled').removeClass('disabled');
            jQuery("#settings_icon_l_url").attr('disabled', 'disabled').addClass('disabled');
            jQuery("#settings_icon_l_url_v").attr('disabled', 'disabled').addClass('disabled');

            if (jQuery('.theme_type_radio:checked').val() == 'custom') {
                jQuery("#settings_form .param_icon").removeClass('hidden');
                jQuery("#settings_form .param_icon_l").removeClass('hidden');
            }
        }
    }
    if (!target || target.hasClass('icon_d_type_radio')) {
        if (jQuery('#settings_form .icon_d_type_radio:checked').val() == 'url') {
            jQuery("#settings_icon_d").attr('disabled', 'disabled').addClass('disabled');
            jQuery("#settings_icon_d_url").removeAttr('disabled').removeClass('disabled');
            jQuery("#settings_icon_d_url_v").removeAttr('disabled').removeClass('disabled');

            if (jQuery("#settings_form .icon_d_type_radio:checked").val() == 'url') {
                jQuery("#settings_form .param_icon").addClass('hidden');
                jQuery("#settings_form .param_icon_d").addClass('hidden');
            }
        } else {
            jQuery("#settings_icon_d").removeAttr('disabled').removeClass('disabled');
            jQuery("#settings_icon_d_url").attr('disabled', 'disabled').addClass('disabled');
            jQuery("#settings_icon_d_url_v").attr('disabled', 'disabled').addClass('disabled');

            if (jQuery('.theme_type_radio:checked').val() == 'custom') {
                jQuery("#settings_form .param_icon").removeClass('hidden');
                jQuery("#settings_form .param_icon_d").removeClass('hidden');
            }
        }
    }
    if (!target || target.hasClass('bp_activity')) {
        if (jQuery("#settings_form .bp_activity:first").is(':checked')) {
            jQuery("#settings_form .param_bp_hide_sitewide").removeClass('hidden');
            jQuery("#settings_form .param_bp_image").removeClass('hidden');
        } else {
            jQuery("#settings_form .param_bp_hide_sitewide").addClass('hidden');
            jQuery("#settings_form .param_bp_image").addClass('hidden');
        }
    }
    if (!target || target.hasClass('like_box_radio')) {
        if (jQuery('#settings_form .like_box_radio:checked').val() == '') {
            jQuery("#settings_form .param_like_box").addClass('hidden');
        } else {
            jQuery("#settings_form .param_like_box").removeClass('hidden');
        }
    }
    if (!target || target.hasClass('radio_voter_by')) {
        var voter_by = jQuery('#settings_form .radio_voter_by:checked').val();

        if (voter_by == 'user') {
            var who_can_1 = jQuery("#settings_form .user_logged_in_radio[value='']");
            var who_can_2 = jQuery("#settings_form .user_logged_in_radio[value='0']");
            if (who_can_1.is(':checked') || who_can_2.is(':checked')) {
                jQuery("#settings_form .user_logged_in_radio[value='1']").attr('checked', 'checked');
            }
            who_can_1.attr('disabled', 'disabled');
            who_can_2.attr('disabled', 'disabled');

            jQuery("#settings_form .param_voter_by_alert:first").removeClass('hidden');
        } else {
            jQuery("#settings_form .user_logged_in_radio[value='']").removeAttr('disabled');
            jQuery("#settings_form .user_logged_in_radio[value='0']").removeAttr('disabled');

            jQuery("#settings_form .param_voter_by_alert:first").addClass('hidden');
        }
    }
    if (!target || target.hasClass('user_logged_in_radio')) {
        var user_logged_in = jQuery('#settings_form .user_logged_in_radio:checked').val();

        if (user_logged_in == 'alert' || user_logged_in == 'alert_btn' || user_logged_in == 'modal') {
            jQuery("#settings_form .param_user_logged_in_alert:first").removeClass('hidden');
        } else {
            jQuery("#settings_form .param_user_logged_in_alert:first").addClass('hidden');
        }
        if (user_logged_in != '' && user_logged_in != '0') {
            jQuery("#settings_form .param_user_logged_in_notice:first").hide();
        } else {
            jQuery("#settings_form .param_user_logged_in_notice:first").show();
        }
    }
    if (!target || target.hasClass('voting_period')) {
        var val = jQuery('#likebtn_voting_period').val();
        jQuery("#settings_form .param_voting_period").addClass('hidden');
        jQuery("#settings_form .param_voting_period :input").addClass('disabled');
        if (val == 'date') {
            jQuery("#settings_form .param_vp_date:first").removeClass('hidden');
            jQuery("#settings_form .param_vp_date:first :input").removeClass('disabled');
        }
        if (val == 'created') {
            jQuery("#settings_form .param_vp_created:first").removeClass('hidden');
            jQuery("#settings_form .param_vp_created:first :input").removeClass('disabled');
        }
    }
    if (!target || target.hasClass('likebtn_wrap')) {
        if (jQuery('#settings_form .likebtn_wrap').is(":checked")) {
            jQuery("#settings_form .param_wrap:first").hide();
        } else {
            jQuery("#settings_form .param_wrap:first").show();
        }
    }
}

function likebtnInitPin()
{
    if (!jQuery("#likebtn_pin:checked").size()) {
        likebtn_pin = true;
    }
}

function likebtnPinChange(el)
{
    if (jQuery(el).is(':checked')) {
        likebtn_pin = false;
        removeCookie('likebtn_pin');
    } else {
        likebtn_pin = true;
        var pin_date = new Date();
        pin_date.setSeconds(pin_date.getSeconds() + 31536000);
        likebtnSetCookie('likebtn_pin', "1", {expires:pin_date, path:"/"});
        likebtnScrollPreview();
    }
}

function likebtnSetChecked(el)
{
    el.prop("checked", true).attr("checked", "checked").change();
}

function likebtnIpInfo(ip)
{
    var likebtn_ip_info = jQuery("#likebtn_ip_info").clone();
    likebtn_ip_info.removeClass('hidden');
    likebtn_ip_info.removeAttr('id');

    likebtn_ip_info.dialog({
        resizable: false,
        autoOpen: false,
        modal: true,
        width: 430,
        title: likebtn_msg_ip_info,
        draggable: false,
        show: 'fade',
        dialogClass: 'likebtn_dlg',
        open: function() {
            jQuery('.ui-widget-overlay, .likebtn_ip_info .likebtn-button-close').bind('click', function() {
                likebtn_ip_info.dialog('close');
            });
        },
        position: { 
            my: "center", 
            at: "center" 
        }
    });

    likebtn_ip_info.dialog('open');

    jQuery.get("//ipinfo.io/"+ip+"/", function(response) {
        if (!response || typeof(response.ip) == "undefined") {
            return;
        }
        var loc = response.loc.split(',');
        /*var map_opts = {
            zoom: 2,
            mapTypeId: google.maps.MapTypeId.HYBRID
        }

        if (loc.length == 2) {
             map_opts.center = {lat: loc[0]*1, lng: loc[1]*1};
        }

        var map = new google.maps.Map(jQuery(".likebtn_ip_info .likebtn_ip_info_map:visible")[0], map_opts);

        if (map_opts.center) {
            var marker = new google.maps.Marker({
                position: map_opts.center,
                map: map,
                title: ip
            });
        }*/
        if (loc.length == 2) {
            var lat = loc[0]*1;
            var lon = loc[1]*1;

            var map_html = '<iframe width="100%" height="200" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.openstreetmap.org/export/embed.html?bbox='+Math.abs(lon-1)+'%2C'+Math.abs(lat-1)+'%2C'+Math.abs(lon+1)+'%2C'+Math.abs(lat+1)+'&amp;layer=mapnik&amp;marker='+lat+'%2C'+lon+'"></iframe>';
            jQuery(".likebtn_ip_info .likebtn_ip_info_map:visible").html(map_html);
        }

        var ip = '';
        if (typeof(response.ip) !== "undefined") {
            ip = response.ip;
        }
        var country = '';
        if (typeof(response.country) !== "undefined") {
            country = response.country;
        }
        var region = '';
        if (typeof(response.region) !== "undefined") {
            region = response.region;
        }
        var loc = '';
        if (typeof(response.loc) !== "undefined") {
            loc = response.loc;
        }
        var postal = '';
        if (typeof(response.postal) !== "undefined") {
            postal = response.postal;
        }
        var org = '';
        if (typeof(response.org) !== "undefined") {
            org = response.org;
        }
        var hostname = '';
        if (typeof(response.hostname) !== "undefined") {
            hostname = response.hostname;
        }

        jQuery(".likebtn_ip_info .likebtn-ii-ip:visible").html(ip);
        jQuery(".likebtn_ip_info .likebtn-ii-country:visible").html(country);
        jQuery(".likebtn_ip_info .likebtn-ii-city:visible").html(region);
        jQuery(".likebtn_ip_info .likebtn-ii-latlon:visible").html(loc);
        jQuery(".likebtn_ip_info .likebtn-ii-postal:visible").html(postal);
        jQuery(".likebtn_ip_info .likebtn-ii-network:visible").html(org);
        jQuery(".likebtn_ip_info .likebtn-ii-hostname:visible").html(hostname);
    }, "jsonp");
}

function likebtnContactUs()
{
    var url = likebtn_msg_website+'customer.php/contact/full/?platform=WordPress&host_name='+likebtn_msg_site_url+'&email='+likebtn_msg_account_email+'&likebtn_short_version=1';

    likebtnPopup(url, 'contact_us', 600, 600);
}

function ipviChange(loader_src)
{
    if (jQuery(".likebtn_ipvi_change_container:first img").size()) {
        return;
    }
    jQuery(".likebtn_ipvi_change_container:first").html('<img src="' + loader_src + '" />');

    jQuery.ajax({
        type: 'POST',
        dataType: "json",
        url: ajaxurl,
        data: {
            action: 'likebtn_ipvi_get',
            likebtn_account_email: jQuery(":input[name='likebtn_account_email']:first").val(),
            likebtn_account_api_key: jQuery(":input[name='likebtn_account_api_key']:first").val(),
            likebtn_site_id: jQuery(":input[name='likebtn_site_id']:first").val()
        },
        success: function(response) {
            if (typeof(response.result) !== "undefined" && response.result == "success") {
                // Show current interval
                if (typeof(response.value) !== "undefined" && parseInt(response.value) != NaN && parseInt(response.value) > 0) {
                    var value = parseInt(response.value);
                    if (value <= 0 || value >= 31557600) {
                        jQuery("#ipvi_select").val(value);
                    } else {
                        jQuery("#ipvi_select").val('-1');
                    }
                    jQuery("#ipvi_secs").val(value).change();
                }
                jQuery("#likebtn_ipvi_change").hide();
                jQuery("#ip_vote_interval").find(":input").removeAttr('disabled').removeAttr('readonly').removeClass('disabled');
            } else {
                if (typeof(response.message) !== "undefined" && response.message) {
                    jQuery(".likebtn_ipvi_change_container:first").html(response.message).css('color', 'red');
                } else {
                    jQuery("#likebtn_ipvi_change").hide();
                    jQuery("#ip_vote_interval").find(":input").removeAttr('disabled').removeAttr('readonly').removeClass('disabled');
                }
            }
        },
        error: function(response) {
            jQuery("#likebtn_ipvi_change").hide();
            jQuery("#ip_vote_interval").find(":input").removeAttr('disabled').removeAttr('readonly').removeClass('disabled');
        }
    });
}

function ipviSelect(el)
{
    var val = jQuery(el).val();
    if (val == '-1') {
        jQuery(".ipvi_custom").removeClass('hidden');
    } else {
        //jQuery("#ipvi_secs").val(val);
        jQuery("#ipvi_secs_hidden").val(val);
        jQuery(".ipvi_custom").addClass('hidden');
    }
}

function scriptSettings()
{
    jQuery(document).ready(function(jQuery) {
        var cntr = jQuery("#ipvi_duration");
        var seconds = parseInt(jQuery("#ipvi_secs").val());
        var secs_cntr = jQuery("#ipvi_secs");
        var secs_cntr_hidden = jQuery("#ipvi_secs_hidden");

        cntr.durationPicker({
            "seconds":seconds
            /*"trans": {
                "seconds": "{% trans %}seconds{% endtrans %}",
                "minutes": "{% trans %}minutes{% endtrans %}",
                "hours": "{% trans %}hours{% endtrans %}",
                "days": "{{ 'days '|trans() }}",
                "months": "{% trans %}months{% endtrans %}",
                "years": "{% trans %}years{% endtrans %}"
            }*/
        }).on("keyup change", function() {
            var seconds = cntr.durationPicker('seconds');
            if (seconds >= 31557600) {
                seconds = 31557599;
                cntr.durationPicker('seconds', seconds);
            }
            if (seconds <= 0) {
                seconds = 1;
                cntr.durationPicker('seconds', seconds);
            }
            secs_cntr.val(seconds);
            secs_cntr_hidden.val(seconds);
        });
        secs_cntr.on("keyup change", function() {
            var value = jQuery(this).val();
            secs_cntr_hidden.val(value);
            cntr.durationPicker('seconds', value);
            cntr.find(".durationPickerGroup input").change();
        });
        cntr.find(".durationPickerGroup input").on('keyup change', function() {
            var val = parseInt(jQuery(this).val());
            if (!isNaN(val) && val > 0) {
                jQuery(this).addClass('has-value');
            } else {
                jQuery(this).removeClass('has-value');
            }
        }).change();
        cntr.find(".durationPickerGroup input").attr('disabled', 'disabled');
    });
}

function importSubmit(msg_text)
{
    var value = prompt(msg_text);
    if (!value || value.toLowerCase() != 'import') {
        return false;
    }
    return true;
}

// Clone object
function cloneObject(object) {
    if (object == null || typeof(object) != 'object') {
        return object;
    }
    var temp = object.constructor(); // changed
    for(var key in object) {
        temp[key] = cloneObject(object[key]);
    }
    return temp;
}