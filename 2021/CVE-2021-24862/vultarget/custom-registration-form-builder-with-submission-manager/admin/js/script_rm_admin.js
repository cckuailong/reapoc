 jQuery(document).ready(function(){
     jQuery('.rm-upgrade-note-gold').mouseenter(function() {
         jQuery('.rm-banner-box').addClass('rm-hop');
         jQuery(this).siblings().addClass('rm-blur');
    });
     jQuery('.rm-upgrade-note-gold').mouseleave(function() {
         jQuery('.rm-banner-box').removeClass('rm-hop');
         jQuery(this).siblings().removeClass('rm-blur');
    });
    // Removing single : from labels on all RM pages
    jQuery(".rmcontent .rmrow .rmfield label").each(function(){
        var label_val= jQuery(this).html();
        var new_label = label_val.replace(':',' ');
        jQuery(this).html(new_label);
    });
    
    jQuery(".rmagic .mce-tinymce iframe ").contents().find("body").attr("style","background-color:#fff;font-size: 16px;");

    
 });

(function (RM_jQ) {
    'use strict';

    /*
     * This function is fired on ready event
     * Activates when document is loaded completely
     *
     * @returns {undefined}
     */



    RM_jQ(function () {

        var chart_obj = RM_jQ(".rm-box-graph");

        rm_setup_google_charts();




        //To implement sorting operation using drag and drop
        //Just have to put id 'sortable' on the element you want to scroll.
        //jQuery UI sortable is used



        var checked_el_ids = [];

        RM_jQ('.rm_sortable_elements').sortable({
            axis: 'y',
            opacity: 0.7,
            handle: '.rm_sortable_handle'
        });

        RM_jQ('.rm_sortable_form_fields').sortable({
            axis: 'y',
            opacity: 0.7,
            handle: '.rm_sortable_handle',
            update: function (event, ui) {
                var list_sortable = RM_jQ(this).sortable('toArray');

                var data = {
                    action: 'rm_sort_form_fields',
                    'rm_slug': 'rm_field_set_order',
                    data: list_sortable
                };

                RM_jQ.post(ajaxurl, data, function (response) {
                    void(0);
                });
            }
        });
        
        RM_jQ('.rm_sortable_login_fields').sortable({
            axis: 'y',
            opacity: 0.7,
            handle: '.rm_sortable_handle',
            update: function (event, ui) {
                var list_sortable = RM_jQ(this).sortable('toArray');
                var data = {
                    action: 'rm_sort_login_fields',
                    'rm_slug': 'rm_login_field_set_order',
                    data: list_sortable
                };

                RM_jQ.post(ajaxurl, data, function (response) {
                    void(0);
                });
            }
        });
        
        //tabbing operation
        RM_jQ('.rm_tabbing_container').tabs();

        //Attach date picker
        RM_jQ('.rm_custom_subfilter_dates').datepicker({dateFormat: 'yy-mm-dd'});
        
        //hide fields on add_field form
        var field_type = RM_jQ('#rm_field_type_select_dropdown').val();

        RM_jQ.field_add_form_manage(field_type);

        //Set appropriate help text.
        //var field_type_help_text = rm_get_help_text(field_type);
        //RM_jQ('#rm_field_type_select_dropdown').parent().next('.rmnote').html(field_type_help_text);

        RM_jQ('.rm_toggle_deactivate').click(function (e) {
            if (!RM_jQ('.rm_checkbox').is(':checked')) {
                e.preventDefault();
            }
        });

        RM_jQ('#id_paypal_field_type_dd').find('option').each(function(index) {
            if(jQuery(this).val() != 'fixed') {
                jQuery(this).prop('disabled',true);
            }
        });
        
        field_type = RM_jQ('#id_paypal_field_type_dd').val();

        if (field_type)
            RM_jQ.setup_pricing_fields_visibility(field_type);

        var field_type_help_text = rm_get_help_text_price_field(field_type);
        jQuery('#id_paypal_field_type_dd').parent().next('.rmnote').children('.rmnotecontent').html(field_type_help_text);

        var theme = RM_jQ('#theme_dropdown').val();

        if (theme)
            RM_jQ.setup_layouts_visibility(theme)

        RM_jQ('.rm_checkbox').click(function () {
            checked_el_ids.push(RM_jQ(this).parent('.card').attr('id'));
            RM_jQ('.rm_actions').prop('disabled', false);
        });




        RM_jQ('#rm_form_manager_operartionbar').submit(function () {
            var i = [];
            RM_jQ.map(RM_jQ("input[name='rm_selected_forms[]']"), function (value, index) {
                if (RM_jQ(value).is(":checked")) {
                    i.push(RM_jQ(value).val());
                }
            });
            RM_jQ("input[name='rm_selected']").val(JSON.stringify(i));
        });

        RM_jQ(document).ajaxStart(function () {
            RM_jQ("#rm_f_loading").show();
             RM_jQ("#rm_f_loading_mail").show();
            
        });
        RM_jQ(document).ajaxComplete(function () {
            RM_jQ("#rm_f_loading").hide();
            RM_jQ("#rm_f_loading_mail").hide();
        });



    });






    /**
     * function to delete a forms field
     *
     * @var  field_id   id of the field to delete
     */
    RM_jQ.delete_form_field = function (field_id) {

        var data = {
            action: 'rm_delete_form_field',
            data: field_id
        };

        RM_jQ.post(ajaxurl, data, function (response) {
            console.log(response);
        });
    };

    RM_jQ(document).ready(function () {



        RM_jQ(".rm_checkbox_group").change(function () {
            if (RM_jQ(this).attr('checked')) {
                RM_jQ(".rm_action_bar .rm_action_btn").prop('disabled', false);
            }

        });

        RM_jQ("#rm_editor_add_form").change(function () {
            tinymce.execCommand('mceFocus', false, 'content');
            if (RM_jQ(this).val() != 0) {
                if (RM_jQ(this).val() === '__0')
                    var shortcode = "[RM_Login]";
                else
                    var shortcode = "[RM_Form id='" + RM_jQ(this).val() + "']";

                if (typeof send_to_editor == 'function')
                    send_to_editor(shortcode);
                else
                    tinyMCE.get('content').execCommand('mceInsertContent', false, shortcode);

            }

        });


        RM_jQ("#rm_editor_add_email").change(function () {
            //tinymce.execCommand('mceFocus',false,'form_email_content');
            if (RM_jQ(this).val() != 0) {
                var shortcode = "{{" + RM_jQ(this).val() + "}}";

                if (typeof send_to_editor == 'function')
                    send_to_editor(shortcode);
                else
                    tinyMCE.get('form_email_content').execCommand('mceInsertContent', false, shortcode);

            }

        });

        RM_jQ("#mce_rm_mail_body").change(function () {
            tinymce.execCommand('mceFocus', false, 'rm_mail_body');
            if (RM_jQ(this).val() != 0) {
                var shortcode = "{{" + RM_jQ(this).val() + "}}";

                if (typeof send_to_editor == 'function')
                    send_to_editor(shortcode);
                else
                    tinyMCE.get('rm_mail_body').execCommand('mceInsertContent', false, shortcode);


            }

        });




    });


    RM_jQ.prevent_quick_add_form = function (event) {
        var f_name = RM_jQ('#rm_form_name').val().toString().trim();
        if (f_name === "" || !f_name) {

            RM_jQ('#rm_form_name').fadeIn(100).fadeOut(1000, function () {
                RM_jQ('#rm_form_name').css("border", "");
                RM_jQ('#rm_form_name').fadeIn(100);
                RM_jQ('#rm_form_name').val('');
            });
            RM_jQ('#rm_form_name').css("border", "1px solid #FF6C6C");
            event.preventDefault();
        }
    };

    RM_jQ.prevent_field_add = function (event, rm_msg) {
        RM_jQ('.rm_prevent_empty').each(function () {
            var f_name = RM_jQ(this).val().toString().trim();
            if (f_name === "" || !f_name) {

                RM_jQ(this).fadeIn(100).fadeOut(1000, function () {
                    RM_jQ(this).css("border", "");
                    RM_jQ(this).fadeIn(100);
                    RM_jQ(this).val('');
                });
                RM_jQ(this).css("border", "1px solid #FF6C6C");
                RM_jQ('#rm_jqnotice_text').html(rm_msg);
                RM_jQ('#rm_jqnotice_row').show();
                event.preventDefault();
            } else
                RM_jQ('#rm_jqnotice_text').html('');

        });

    };


    //Email listing
//    RM_jQ.remove_email = function (elem){
//        var id = RM_jQ(elem).attr('id');
//        var mailbox_id = id.substr(2);
//        RM_jQ(elem).closest("#"+mailbox_id).remove();
//    };
//
//    RM_jQ.add_email_field = function aef (initialcounter){
//        aef.counter = ++aef.counter || initialcounter;
//        var newemail = RM_jQ('#id_rm_add_email_tb').val();
//        var t = "<div id='id_test_"+aef.counter+"'><input class='rm_options_resp_email' type='email' name='resp_emails[]' value='"+newemail+"' readonly='true'></input><div class='x_remove_resp_email' id='x_id_test_"+aef.counter+"' onclick='jQuery.remove_email(this)'>X</div></div>";
//        var x = RM_jQ(document.createElement("div")).attr("id","xxxxxxx");
//        x.after().html(t);
//        x.appendTo('#id_rm_admin_emails_container');
//        RM_jQ('#id_rm_add_email_tb').val("");
//    };


    /**
     * function to hide field_add form fields according to field type
     *
     * @var  field_type   type of the field to be added
     */
    RM_jQ.field_add_form_manage = function (field_type) {
        if(!field_type)
            return;
        
        var all_elem = RM_jQ(".rm_static_field");
        RM_jQ(".rm_sub_heading").show();
        RM_jQ(".rm_check").hide();
        all_elem.prop('disabled', false);
        all_elem.parents(".rmrow").show();
        all_elem.removeClass("rm_prevent_empty");
        RM_jQ("#rm_field_value_paragraph, #rm_field_value_options_textarea, #rm_field_value_heading, #rm_field_value_options_sortable, #rm_field_value_file_types, #rm_field_value_pricing").attr('required', false);
        RM_jQ("#rm_jqnotice_row").hide();
        RM_jQ("#field_repeatable_line_type").hide();
        RM_jQ("#rm_no_api_notice").hide();
        RM_jQ("#time_Zone").hide();
        RM_jQ("#scroll").hide();
        RM_jQ("#date_range").hide();
        RM_jQ("#custom").hide();
        RM_jQ("#privacy_policy").hide();
        RM_jQ("#rm_tnc_cb_label_container").hide();
//        RM_jQ("#scroll").hide();
        RM_jQ("#rm_field_helptext_container").show();
        RM_jQ("#rm_icon_setting_container,#rm_icon_field_settings_header").show();
        RM_jQ("#rm_field_dateformat_container").hide();
        RM_jQ("#rm_field_dateformat").prop('disabled', true);
        
        switch (field_type) {
            case 'Textbox' :
            case 'WCBilling' :
            case 'WCShipping' :
            case 'Fname' :
            case 'Lname' :
            case 'Nickname' :
            case 'Phone' :
            case 'Mobile' :
            case 'Username' :
            case 'UserPassword':
            case 'Password' :
                var object = RM_jQ(".rm_field_value, .rm_textarea_type, .rm_options_type_fields, #rm_field_is_read_only-0");				
                break;
            
            case 'Privacy':
                var object = RM_jQ(".rm_field_value, .rm_textarea_type, .rm_options_type_fields, #rm_field_is_read_only-0, #rm_field_max_length, #rm_field_is_required-0, #rm_field_show_on-0, #rm_field_is_editable-0, #rm_sub_heading");
                RM_jQ('#rm_field_placeholder').parents(".rmrow").hide();
                RM_jQ('#rm_sub_heading').hide();
                RM_jQ("#privacy_policy").show();
                break;
                
            case 'WCBillingPhone' :
                var object = RM_jQ(".rm_field_value, .rm_textarea_type, .rm_options_type_fields, #rm_field_is_read_only-0, #rm_field_show_on-0, #rm_field_is_editable-0, #rm_field_max_length");
                break;
                
            case 'Hidden':
                var object = RM_jQ(".rm_field_value, .rm_textarea_type, .rm_options_type_fields, #rm_field_is_read_only-0, #rm_field_placeholder, #rm_field_helptext, #rm_field_class, #rm_field_max_length, #rm_field_is_required-0, #rm_field_show_on-0, #rm_field_is_editable-0").not("#rm_field_default_value");
                RM_jQ("#rm_icon_setting_container, #rm_sub_heading,#rm_icon_field_settings_header").hide();
                RM_jQ("#rm_field_default_value").show();
                break;    
                
            case 'Custom' :
                var object = RM_jQ(".rm_field_value, .rm_textarea_type, .rm_options_type_fields, #rm_field_is_read_only-0");
		RM_jQ("#custom").show();		
                break;
            case 'HTMLP' :
                var object = RM_jQ(".rm_input_type, .rm_field_value, #rm_field_show_on-0, #rm_field_is_editable-0").not("#rm_field_value_paragraph");
                var val_field = RM_jQ("#rm_field_value_paragraph");
                RM_jQ(".rm_sub_heading").hide();
                RM_jQ("#scroll").hide();
                RM_jQ("#rm_field_helptext_container").hide();
                RM_jQ("#rm_icon_setting_container,#rm_icon_field_settings_header").hide();
                break;
            case 'Shortcode' :
                var object = RM_jQ(".rm_input_type, .rm_field_value, #rm_field_show_on-0, #rm_field_is_editable-0").not("#rm_field_value_shortcode");
                var val_field = RM_jQ("#rm_field_value_shortcode");
                RM_jQ(".rm_sub_heading").hide();
                RM_jQ("#rm_icon_setting_container,#rm_icon_field_settings_header").hide();
                break;
            case 'HTMLH' :
                var object = RM_jQ(".rm_input_type, .rm_field_value, #rm_field_show_on-0, #rm_field_is_editable-0").not("#rm_field_value_heading");
                var val_field = RM_jQ("#rm_field_value_heading");
                RM_jQ(".rm_sub_heading").hide();
                RM_jQ("#scroll").hide();
                RM_jQ("#rm_field_helptext_container").hide();
                RM_jQ("#rm_icon_setting_container,#rm_icon_field_settings_header").hide();
                break;

            case 'Select' :
            case 'Multi-Dropdown' :
                var object = RM_jQ(".rm_text_type_field, .rm_field_value, .rm_textarea_type, #rm_field_default_value_sortable").not("#rm_field_value_options_textarea, #rm_field_helptext_container");
                var val_field = RM_jQ("#rm_field_value_options_textarea");
                break;

            case 'Radio' :
                var object = RM_jQ(".rm_text_type_field, .rm_field_value, .rm_textarea_type, #rm_field_default_value_sortable").not("#rm_field_value_options_sortable, #rm_field_helptext_container");
                var val_field = RM_jQ("#rm_field_value_options_sortable");
                break;

            case 'Textarea' :
            case 'BInfo' :
                var object = RM_jQ(".rm_field_value, .rm_options_type_fields, #rm_field_is_read_only-0");
                break;
            case 'Checkbox' :
                var object = RM_jQ(".rm_text_type_field, .rm_field_value, .rm_textarea_type, #rm_field_default_value").not("#rm_field_value_options_sortable, #rm_field_helptext_container");
                var val_field = RM_jQ("#rm_field_value_options_sortable");
                RM_jQ(".rm_check").show();
                break;
            case 'Bdate' :
            var object = RM_jQ(".rm_static_field").not(".rm_required, #rm_field_is_required-0, #rm_field_helptext_container, #rm_field_is_editable-0, #rm_field_placeholder, #existing_user_meta");
		RM_jQ("#date_range").show();
                RM_jQ("#rm_field_dateformat_container").show();
                RM_jQ("#rm_field_dateformat").prop('disabled', false);
                break;
                        
            case 'Country' :
            case 'Gender' :
            case 'Timezone' :
            case 'Language' :
            case 'ESign':
            case 'Image' :
            case 'Rating' :
            case 'Website' :
                var object = RM_jQ(".rm_static_field").not(".rm_required, #rm_field_is_required-0, #rm_field_helptext_container, #rm_field_is_editable-0");
                break;
            
            case 'jQueryUIDate' :
                var object = RM_jQ(".rm_static_field").not(".rm_required, #rm_field_is_required-0, #rm_field_helptext_container, #rm_field_is_editable-0, #rm_field_dateformat, #rm_field_placeholder, #existing_user_meta");
                RM_jQ("#rm_field_dateformat_container").show();
                RM_jQ("#rm_field_dateformat").prop('disabled', false);
                break;
                
            case 'Email' :
            case 'SecEmail' :
            case 'Number' :
                var object = RM_jQ(".rm_static_field").not(".rm_required, #rm_field_is_required-0, #rm_field_helptext_container, #rm_field_is_editable-0, #rm_field_placeholder, #existing_user_meta");
                break;
            case 'Facebook' :
            case 'Twitter' :
            case 'Google' :
            case 'Linked' :
            case 'Youtube' :
            case 'VKontacte' :
            case 'Instagram' :
            case 'Skype' :
            case 'SoundCloud' :
              var object = RM_jQ(".rm_static_field").not(".rm_required, #rm_field_is_editable-0, #rm_field_is_required-0,#rm_field_placeholder, #rm_field_helptext_container");
                break;
            case 'Divider' :
            case 'Spacing' :
                var object = RM_jQ(".rm_static_field").not(".rm_required, #rm_field_is_required-0");
                RM_jQ("#rm_icon_setting_container,#rm_icon_field_settings_header").hide();
                RM_jQ("#rm_field_helptext_container").hide();
                break;
            case 'Time' :
               var object = RM_jQ(".rm_static_field").not(".rm_required, #rm_field_is_editable-0, #rm_field_is_required-0, #rm_field_helptext_container");
               RM_jQ("#time_Zone").show();
               break;
            case 'Repeatable_M':   
            case 'Repeatable' :
                var object = RM_jQ(".rm_field_value, .rm_textarea_type, .rm_options_type_fields, #rm_field_placeholder, #rm_field_is_read_only-0, #rm_field_helptext_container");
                RM_jQ("#field_repeatable_line_type").show();
                break;
            case 'Terms' :
                var object = RM_jQ(".rm_static_field").not(".rm_required, #rm_field_is_editable-0, #rm_field_is_required-0, #rm_field_value_terms, #rm_field_helptext_container,#existing_user_meta");
                var val_field = RM_jQ("#rm_field_value_terms");
                RM_jQ("#scroll").show();
                RM_jQ("#rm_tnc_cb_label_container").show();
                break;
            case 'File' :
                var object = RM_jQ(".rm_static_field, #rm_field_default_value").not(".rm_required, #rm_field_is_editable-0, #rm_field_is_required-0, #rm_field_value_file_types, #rm_field_helptext_container");
                //var val_field = RM_jQ("#rm_field_value_file_types");
                break;

            case 'Price' :
                var object = RM_jQ(".rm_static_field").not(".rm_required, #rm_field_is_required-0, #rm_field_value_pricing, #rm_field_helptext_container");
                var val_field = RM_jQ("#rm_field_value_pricing");
                break;

            case 'Map' :
            case 'Address' : 
                var object = RM_jQ(".rm_static_field").not("#rm_field_type_select_dropdown, #rm_field_is_editable-0, #rm_field_label, #rm_field_is_required-0, #rm_field_show_on-0, #rm_field_helptext_container, #existing_user_meta");
                RM_jQ("#rm_no_api_notice").show();
                break;

            default :
                var object = RM_jQ(".rm_static_field").not("#rm_field_type_select_dropdown");
                RM_jQ("#rm_field_helptext_container").hide();
                RM_jQ("#rm_icon_setting_container,#rm_icon_field_settings_header").hide();
                RM_jQ(".rm_sub_heading").hide();


        }
        
        var unique_option_fields= ['Textbox','Phone','Mobile','Custom','Email','Number','SecEmail'];
        if(unique_option_fields.indexOf(field_type)>=0)
            jQuery("#rm_unique_div").show();
        else
            jQuery("#rm_unique_div").hide();
        object.parents(".rmrow").hide();
        object.prop('disabled', true);
 
        if (field_type === 'HTMLP' || field_type === 'HTMLH' || field_type === 'Terms' || field_type === 'Price' || field_type === 'Checkbox' || field_type === 'Radio' || field_type === 'Select'|| field_type === 'Multi-Dropdown'||  field_type === 'Shortcode') {
            val_field.attr('required', true);
            val_field.addClass("rm_prevent_empty");
        }
        if (field_type === 'Divider'|| field_type === 'Spacing')
        {
                 RM_jQ("#rm_field_show_on-0").attr('checked', false);
            RM_jQ("#rm_field_is_required-0").parents(".rmrow").hide();
            RM_jQ("#rm_field_show_on-0").parents(".rmrow").hide();
            RM_jQ("#rm_sub_heading").hide();
        }
         if (field_type === 'Shortcode')
        {
            RM_jQ("#rm_field_value_shortcode").parents(".rmrow").show();
            RM_jQ("#rm_field_value_paragraph").parents(".rmrow").hide();
        }
        if (field_type === 'Fname' || field_type === 'Lname' || field_type === 'BInfo'|| field_type === 'Nickname'|| field_type === 'SecEmail'|| field_type === 'Website') {
            RM_jQ("#rm_field_show_on-0").attr('checked', false);
            RM_jQ("#rm_field_show_on-0").attr('readonly', true);
            RM_jQ("#rm_field_show_on-0").parents(".rmrow").hide();
        }
 if (field_type === 'Number') {
          RM_jQ("#rm_field_max_length").parents(".rmrow").show();
          RM_jQ("#rm_field_max_length").prop('disabled', false);
        }
        var rm_other_box = RM_jQ("#rmaddotheroptiontextdiv");
        if (field_type === 'Checkbox' || field_type=="Radio") {
            rm_other_box.show();
            rm_other_box.siblings('#rm_action_field_container').addClass('rm_shrink_div');
        }
         
	else {
            rm_other_box.hide();
            rm_other_box.siblings('#rm_action_field_container').removeClass('rm_shrink_div');
        }

    };





    RM_jQ.setup_pricing_fields_visibility = function (field_type) {

        var all_elem = RM_jQ(".rm_static_field");
        all_elem.removeClass("rm_prevent_empty");

        switch (field_type) {

            case 'fixed':
                RM_jQ('#id_block_fields_for_dd_multisel').find('input').prop('required', false);
                RM_jQ('#id_paypal_field_value_no').prop('required', true);
                RM_jQ('#id_paypal_field_value_no').addClass('rm_prevent_empty');
                RM_jQ('#id_block_fields_for_dd_multisel').hide();
                RM_jQ('#id_block_fields_for_fixed, #id_allow_quantity_container').show();
                break;

            case 'multisel':
            case 'dropdown':
                RM_jQ('#id_block_fields_for_dd_multisel').find('input').prop('required', true);
                RM_jQ('#id_block_fields_for_dd_multisel').find('input').addClass("rm_prevent_empty");
                RM_jQ('#rm_append_option').removeClass("rm_prevent_empty"); //Remove class from "click to append" box
                RM_jQ('#id_paypal_field_value_no').prop('required', false);
                RM_jQ('#id_block_fields_for_dd_multisel, #id_allow_quantity_container').show();
                RM_jQ('#id_block_fields_for_fixed').hide();
                break;

            case 'userdef':
                RM_jQ('#id_block_fields_for_dd_multisel').find('input').prop('required', false);
                RM_jQ('#id_paypal_field_value_no').prop('required', false);
                RM_jQ('#id_block_fields_for_dd_multisel, #id_allow_quantity_container').hide();
                RM_jQ('#id_block_fields_for_fixed').hide();
                break;
        }

    };


    RM_jQ.setup_layouts_visibility = function (theme) {

        switch (theme) {

            case 'matchmytheme':
               // RM_jQ('#layout_two_columns_container').hide();
                break;

            case 'classic':
                //RM_jQ('#layout_two_columns_container').show();
                break;
        }

    };


    /**
     * Function to define some form actions by setting 'rm_slug'
     *
     * @param {string} form_id   id attribute of the form to be submitted.
     * @param {string} slug      value of rm_slug to be set
     */

    RM_jQ.rm_do_action = function (form_id, slug) {
        if(slug == 'rm_user_delete'){
            if(form_id == 'form_user_page_action') {
                var choice = confirm('Deleting this user will also delete the content assigned to the user. Do you want to continue?');
                if(choice) {
                    var form = RM_jQ("form#" + form_id);
                    form.children('input#rm_slug_input_field').val(slug);
                    form.submit();
                }
            } else {
                var selected = [];
                RM_jQ.each(RM_jQ("input[name='rm_users[]']:checked"), function(){            
                    selected.push({id: RM_jQ(this).val(),email: RM_jQ(this).data('email')});
                });
                var html='';
                for(var i=0;i<selected.length;i++){
                    html += 'ID #' + selected[i].id + ": " + selected[i].email + "<br>";
                    RM_jQ("#rm_reassign_user option[value='"+ selected[i].id + "']").remove();
                }
                var delete_pop_container=  RM_jQ("#rm_user_delete_popup");
                if(selected.length>1){
                   delete_pop_container.find(".user_msg1").html('You have specified these users for deletion:');
                   delete_pop_container.find(".user_msg2").html('What should be done with content owned by these users?');
                }
                else{
                   delete_pop_container.find(".user_msg1").html('You have specified this user for deletion:');
                   delete_pop_container.find(".user_msg2").html('What should be done with content owned by this user?');
                }
                delete_pop_container.find(".rm_user_datails").html(html);
                RM_jQ("#rm_user_delete_popup").show();
            }
        } else {
            var form = RM_jQ("form#" + form_id);
            form.children('input#rm_slug_input_field').val(slug);
            form.submit();
        }
    };

    RM_jQ.rm_user_deletion_confirmed = function(){
        var form = RM_jQ("#rm_user_manager_form");
        form.children('input#rm_slug_input_field').val('rm_user_delete');
        form.submit();
    }
    

    RM_jQ.rm_append_textbox_other = function (elem) {
        RM_jQ("#rmaddotheroptiontextboxdiv").show();
        RM_jQ("#rm_field_is_other_option").val(1);
    };

    RM_jQ.rm_delete_textbox_other = function (elem) {
        RM_jQ("#rmaddotheroptiontextboxdiv").hide();
        RM_jQ("#rm_field_is_other_option").val('');
    };

    /**
     * Function to define some form actions by setting 'rm_slug'.
     * But also provide an JS confirmation before proceeding.
     *
     * @param {string} alert   message to show as alert
     * @param {string} form_id   id attribute of the form to be submitted.
     * @param {string} slug      value of rm_slug to be set
     */

    RM_jQ.rm_do_action_with_alert = function (alert, form_id, slug) {

        if (confirm(alert)) {

            var form = RM_jQ("form#" + form_id);

            form.children('input#rm_slug_input_field').val(slug);

            form.submit();
        }

    };

    RM_jQ.rm_invertcolor = function (element) {
        var a = element.css("background-color");
        var b = element.css("color");

        element.css('color', a);
        element.css('background-color', b);
    };

    RM_jQ.rm_test_smtp_config = function () {

        var data = {
            'action': 'rm_test_smtp_config',
            'test_email': RM_jQ("#id_rm_test_email_tb").val(),
            'smtp_host': RM_jQ("#id_rm_smtp_host_tb").val(),
            'SMTPAuth': RM_jQ("#id_rm_smtp_auth_cb-0").val(),
            'Port': RM_jQ("#id_rm_smtp_port_num").val(),
            'Username': RM_jQ("#id_rm_smtp_username_tb").val(),
            'Password': RM_jQ("#id_rm_smtp_password_tb").val(),
            'SMTPSecure': RM_jQ("#id_rm_smtp_enctype_dd").val(),
            'From': RM_jQ("#id_rm_from_email_tb").val(),
            'FromName': RM_jQ("#id_rm_from_tb").val()
        };

        RM_jQ.post(ajaxurl, data, function (response) {
           
            if(response.indexOf("blank_email") >= 0){
              response = response.split('blank_email')[1];
             
              RM_jQ("#rm_smtp_test_response").html(response);
              RM_jQ("#rm_smtp_test_response").removeClass();
              RM_jQ("#rm_smtp_test_response").addClass('rm_response rm_failed');
          }
          else{
            RM_jQ("#rm_smtp_test_response").html(response);
            RM_jQ("#rm_smtp_test_response").removeClass();
            response = response.split("!")[0];
            if(response!=='Failed')
                RM_jQ("#rm_smtp_test_response").addClass('rm_response rm_success');
            else
                RM_jQ("#rm_smtp_test_response").addClass('rm_response rm_failed');
        }
        });
    };
    
    RM_jQ.rm_test_wordpress_default_mail = function () {
 
         var data = {
             'action': 'rm_test_wordpress_default_mail',
             'test_email': RM_jQ("#wordpress_default_email_to").val(),
             'message': RM_jQ("#wordpress_default_email_message").val(),
             'From': RM_jQ("#id_rm_from_email_tb").val()
         };
 
         RM_jQ.post(ajaxurl, data, function (response) {
             if(response.indexOf("blank_email") >= 0){
               response = response.split('blank_email')[1];
               
               RM_jQ("#rm_wordpress_default_mail_test_response").html(response);
               RM_jQ("#rm_wordpress_default_mail_test_response").removeClass();
               RM_jQ("#rm_wordpress_default_mail_test_response").addClass('rm_response rm_failed');
           }
           else{
             RM_jQ("#rm_wordpress_default_mail_test_response").html(response);
             RM_jQ("#rm_wordpress_default_mail_test_response").removeClass();
             response = response.split("!")[0];
             if(response!=='Failed')
                 RM_jQ("#rm_wordpress_default_mail_test_response").addClass('rm_response rm_success');
             else
                 RM_jQ("#rm_wordpress_default_mail_test_response").addClass('rm_response rm_failed');
         }
         });
     };

})(jQuery);







