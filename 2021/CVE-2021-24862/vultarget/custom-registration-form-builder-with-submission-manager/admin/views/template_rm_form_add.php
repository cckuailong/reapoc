<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_form_add.php'); else {
//
/**
 * View template file of the plugin
 *
 * @internal Add form page view.
 */
global $rm_env_requirements;
?>

<div class="rmagic">

    <!--Dialogue Box Starts-->
    <div class="rmcontent">


        <?php
        $form = new RM_PFBC_Form("rm_form_add");
        $form->configure(array(
            "prevent" => array("bootstrap", "jQuery"),
            "action" => ""
        ));


        if (isset($data->model->form_id)) {
            $form->addElement(new Element_HTML('<div class="rmheader">' . $data->model->form_name . '</div>'));
            $form->addElement(new Element_Hidden("form_id", $data->model->form_id));
        } else {
            $form->addElement(new Element_HTML('<div class="rmheader">' . RM_UI_Strings::get("TITLE_NEW_FORM_PAGE") . '</div>'));
            $form->addElement(new Element_HTML('<div class="rmsubheader">' . RM_UI_Strings::get("SUBTITLE_NEW_FORM_PAGE") . '</div>'));
        }

        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_FORM_TITLE') . "</b>", "form_name", array("id" => "rm_form_name", "required" => "1", /*"value" => $data->model->form_name,*/ "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_TITLE'))));
        $form->addElement(new Element_Textarea("<b>" . RM_UI_Strings::get('LABEL_FORM_DESC') . "</b>", "form_description", array("id" => "rm_form_description", /*"value" => $data->model->form_options->form_description,*/ "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_DESC'))));
        $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_CREATE_WP_ACCOUNT') . "?</b>(" . RM_UI_Strings::get('LABEL_CREATE_WP_ACCOUNT_DESC') . "):", "form_type", array(1 => ""), array("id" => "rm_user_create", "class" => "rm_user_create", "onclick" => "hide_show(this);", /*"value" => $data->model->form_type,*/ "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_CREATE_WP_USER'))));
        $form->addElement(new Element_Hidden('form_pages', json_encode($data->model->form_options->form_pages)));
        if ($data->model->form_type == 1)
            $form->addElement(new Element_HTML('<div class="childfieldsrow" id="rm_user_create_childfieldsrow">'));
        else
            $form->addElement(new Element_HTML('<div class="childfieldsrow" id="rm_user_create_childfieldsrow" style="display:none">'));


        $form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LABEL_DO_ASGN_WP_USER_ROLE') . "</b>", "default_form_user_role", $data->roles, array("id" => "rm_user_role", /*"value" => $data->model->get_default_form_user_role(),*/ "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_WP_USER_ROLE_AUTO'))));

        $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_LET_USER_PICK') . "</b>", "form_should_user_pick", array(1 => ""), array("id" => "rm_form_should_user_pick", "class" => "rm_form_should_user_pick", "onclick" => "hide_show(this);", /*"value" => $data->model->form_options->form_should_user_pick,*/ "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_WP_USER_ROLE_PICK'))));
        
        if (count($data->model->form_options->form_should_user_pick) === 1)
            $form->addElement(new Element_HTML('<div class="childfieldsrow" id="rm_form_should_user_pick_childfieldsrow">'));
        else
            $form->addElement(new Element_HTML('<div class="childfieldsrow" id="rm_form_should_user_pick_childfieldsrow" style="display:none">'));

        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_USER_ROLE_FIELD') . "</b>", "form_user_field_label", array("id" => "rm_role_label", /*"value" => $data->model->form_options->form_user_field_label,*/ "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_ROLE_SELECTION_LABEL'))));
        $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_ALLOW_WP_ROLE') . "</b>", "form_user_role", array_slice($data->roles, 1), array("class" => "rm_allowed_roles", "id" => "rm_", /*"value" => $data->model->get_form_user_role(),*/ "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_ALLOWED_USER_ROLE'))));
        $form->addElement(new Element_HTML('</div>'));

        $form->addElement(new Element_HTML('</div>'));


        $form->addElement(new Element_TinyMCEWP("<b>" . RM_UI_Strings::get('LABEL_CONTENT_ABOVE') . "</b>", $data->model->form_options->form_custom_text, "form_custom_text", array('editor_class' => 'rm_TinyMCE', 'editor_height' => '100px'), array("longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_CONTENT_ABOVE_FORM'))));
        $form->addElement(new Element_TinyMCEWP("<b>" . RM_UI_Strings::get('LABEL_SUCC_MSG') . "</b>", $data->model->form_options->form_success_message, "form_success_message", array('editor_class' => 'rm_TinydMCE', 'editor_height' => '100px'), array("longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_SUCCESS_MSG'))));
        $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_UNIQUE_TOKEN') . "</b>", "form_is_unique_token", array(1 => ""), array("id" => "rm_", /*"value" => $data->model->form_options->form_is_unique_token,*/ "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_UNIQUE_TOKEN'))));
        $form->addElement(new Element_Radio("<b>" . RM_UI_Strings::get('LABEL_USER_REDIRECT') . "</b>", "form_redirect", array('none' => 'None', 'page' => 'Page', 'url' => 'URL'), array("id" => "rm_", "class" => "rm_", "onclick" => "hide_show_radio(this);", /*"value" => $data->model->form_redirect? : 'none',*/ "required" => "1", "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_REDIRECT_AFTER_SUB'))));

        if ($data->model->form_redirect == 'none')
            $form->addElement(new Element_HTML('<div class="childfieldsrow" id="rm__childfieldsrow" style="display:none" >'));
        else
            $form->addElement(new Element_HTML('<div class="childfieldsrow" id="rm__childfieldsrow" >'));
        if ($data->model->form_redirect == 'page')
            $form->addElement(new Element_HTML('<div class="rm_form_page">'));
        else
            $form->addElement(new Element_HTML('<div class="rm_form_page" style="display:none" >'));
        $form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LABEL_PAGE') . "</b>", "form_redirect_to_page", $data->wp_pages, array("id" => "rm_form_type", /*"value" => intval($data->model->get_form_redirect_to_page()) ? $data->model->get_form_redirect_to_page() : null,*/ "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_REDIRECT_PAGE'))));
        $form->addElement(new Element_HTML('</div>'));
        if ($data->model->form_redirect == 'url')
            $form->addElement(new Element_HTML('<div class="rm_form_url"> '));
        else
            $form->addElement(new Element_HTML('<div class="rm_form_url"  style="display:none">  '));
        $form->addElement(new Element_Url("<b>" . RM_UI_Strings::get('LABEL_URL') . "</b>", "form_redirect_to_url", array("id" => "rm_form_name", /*"value" => !intval($data->model->get_form_redirect_to_url()) ? $data->model->get_form_redirect_to_url() : null,*/ "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_REDIRECT_URL'))));
        $form->addElement(new Element_HTML('</div>'));

        $form->addElement(new Element_HTML('</div>'));

        $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_AUTO_REPLY') . "</b>", "form_should_send_email", array(1 => ""), array("id" => "rm_ss", "onclick" => "rm_toggle(    this, 'rm_auto_reply')", "class" => "rm_ss", "onclick" => "hide_show(this);", /*"value" => $data->model->form_should_send_email,*/ "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_AUTO_RESPONDER'))));

        if ($data->model->form_should_send_email == '1')
            $form->addElement(new Element_HTML('<div class="childfieldsrow" id="rm_ss_childfieldsrow" >'));
        else
            $form->addElement(new Element_HTML('<div class="childfieldsrow" id="rm_ss_childfieldsrow" style="display:none">'));

        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_AR_EMAIL_SUBJECT') . "</b>", "form_email_subject", array("id" => "rm_form_name", /*"value" => $data->model->form_options->form_email_subject,*/ "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_AUTO_RESP_SUB'))));

        $form->addElement(new Element_TinyMCEWP("<b>" . RM_UI_Strings::get('LABEL_AR_EMAIL_BODY') . "</b>".__('(Mail Merge and HTML Supported)', 'custom-registration-form-builder-with-submission-manager').":", $data->model->form_options->form_email_content, "form_email_content", array('editor_class' => 'rm_TinydMCE', 'editor_height' => '100px'), array("longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_AUTO_RESP_MSG'))));
        $form->addElement(new Element_HTML('</div>'));

        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_SUBMIT_BTN') . "</b>", "form_submit_btn_label", array("id" => "rm_form_name", /*"value" => $data->model->form_options->form_submit_btn_label,*/ "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_SUB_BTN_LABEL'))));
        $form->addElement(new Element_Color("<b>" . RM_UI_Strings::get('LABEL_SUBMIT_BTN_COLOR') . "</b>(".__('Does not works with Classic form style', 'custom-registration-form-builder-with-submission-manager')."):", "form_submit_btn_color", array("id" => "rm_", /*"value" => $data->model->form_options->form_submit_btn_color,*/ "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_SUB_BTN_FG_COLOR'))));
        $form->addElement(new Element_Color("<b>" . RM_UI_Strings::get('LABEL_SUBMIT_BTN_COLOR_BCK') . "</b>(" . RM_UI_Strings::get('LABEL_SUBMIT_BTN_COLOR_BCK_DSC') . "):", "form_submit_btn_bck_color", array("id" => "rm_", /*"value" => $data->model->form_options->form_submit_btn_bck_color,*/ "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_SUB_BTN_BG_COLOR'))));

        $mailchimp_key = get_option('rm_option_mailchimp_key');
        if (get_option('rm_option_enable_mailchimp') == "yes" && !empty($mailchimp_key)) {
            $form->addElement(new Element_HTML('<div id="rm_mailchimp_options">'));
        } else {
            $form->addElement(new Element_HTML('<div id="rm_mailchimp_options" class="hidden_element">'));
        }

        $form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LABEL_MAILCHIMP_LIST') . "</b>", "mailchimp_list", $data->mailchimp_list, array("id" => "mailchimp_list", /*"value" => $data->model->form_options->mailchimp_list,*/ "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_MC_LIST'))));
        $form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LABEL_MAILCHIMP_MAP_EMAIL') . "</b>", "mailchimp_mapped_email", $data->email_fields, array("id" => "mailchimp_mail", /*"value" => $data->model->get_mailchimp_mapped_email(),*/ "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_MC_EMAIL'))));
        $form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LABEL_MAILCHIMP_MAP_FIRST_NAME') . "</b>", "mailchimp_mapped_first_name", $data->all_fields, array("id" => "mailchimp_fname", /*"value" => $data->model->get_mailchimp_mapped_first_name(),*/ "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_MC_FNAME'))));
        $form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LABEL_MAILCHIMP_MAP_LAST_NAME') . "</b>", "mailchimp_mapped_last_name", $data->all_fields, array("id" => "mailchimp_lname", /*"value" => $data->model->get_mailchimp_mapped_last_name(),*/ "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_MC_LNAME'))));
        $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_OPT_IN_CB') . "</b>", "form_is_opt_in_checkbox", array(1 => ""), array("id" => "rm_", "class" => "rm_op", "onclick" => "hide_show(this);", /*"value" => $data->model->form_options->form_is_opt_in_checkbox,*/ "longDesc" => RM_UI_Strings::get('HELP_OPT_IN_CB'))));

        if ($data->model->form_options->form_is_opt_in_checkbox == '1')
            $form->addElement(new Element_HTML('<div class="childfieldsrow" id="rm_op_childfieldsrow" >'));
        else
            $form->addElement(new Element_HTML('<div class="childfieldsrow" id="rm_op_childfieldsrow" style="display:none">'));



        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_OPT_IN_CB_TEXT') . "</b>", "form_opt_in_text", array("id" => "rm_form_name", /*"value" => $data->model->form_options->form_opt_in_text,*/ "longDesc" => RM_UI_Strings::get('HELP_OPT_IN_CB_TEXT'))));
        $form->addElement(new Element_HTML('</div>'));
        $form->addElement(new Element_HTML('</div>'));

        $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_AUTO_EXPIRE') . "</b>", "form_should_auto_expire", array(1 => ""), array("id" => "rm_", "class" => "rm_a", /*"value" => $data->model->form_should_auto_expire,*/ "onclick" => "hide_show(this);", "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_AUTO_EXPIRE'))));
        if ($data->model->form_should_auto_expire == '1')
            $form->addElement(new Element_HTML('<div class="childfieldsrow" id="rm_a_childfieldsrow" >'));
        else
            $form->addElement(new Element_HTML('<div class="childfieldsrow" id="rm_a_childfieldsrow" style="display:none">'));

        $form->addElement(new Element_Radio("<b>" . RM_UI_Strings::get('LABEL_EXPIRY') . "</b>", "form_expired_by", array('submissions' => __('By Submissions','custom-registration-form-builder-with-submission-manager'), 'date' => __('By Date','custom-registration-form-builder-with-submission-manager'), 'both' => __('Set both (Whichever is earlier)','custom-registration-form-builder-with-submission-manager')), array("id" => "rm_", /*"value" => $data->model->form_options->form_expired_by,*/ "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_EXPIRE_BY'))));
        $form->addElement(new Element_Number("<b>" . RM_UI_Strings::get('LABEL_SUB_LIMIT') . "</b>", "form_submissions_limit", array("id" => "rm_form_name", /*"value" => $data->model->form_options->form_submissions_limit,*/ "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_AUTO_EXP_SUB_LIMIT'))));
        $form->addElement(new Element_jQueryUIDate("<b>" . RM_UI_Strings::get('LABEL_EXPIRY_DATE') . "</b>", 'form_expiry_date', array('class' => 'rm_dateelement', /*"value" => $data->model->form_options->form_expiry_date,*/ "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_AUTO_EXP_TIME_LIMIT'))));
        $form->addElement(new Element_HTML('</div>'));

        $form->addElement(new Element_Textarea("<b>" . RM_UI_Strings::get('LABEL_EXPIRY_MSG') . "</b>", "form_message_after_expiry", array("class" => "rm_form_description", /*"value" => $data->model->form_options->form_message_after_expiry,*/ "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_AUTO_EXP_MSG'))));

        if ($rm_env_requirements & RM_REQ_EXT_CURL) {
            //options for export submissions to url
            $options_send_to_url_field = array("id" => "rm_export_sub_url", /*"value" => $data->model->form_options->export_submissions_to_url,*/ "longDesc" => RM_UI_Strings::get('HELP_SEND_SUB_TO_URL'));
            if ($data->model->form_options->should_export_submissions != 1)
                $options_send_to_url_field['disabled'] = true;
            else
                $options_send_to_url_field['class'] = 'rm_prevent_empty';

            $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_EXPORT_TO_URL_CB') . "</b>", "should_export_submissions", array(1 => ""), array("id" => "rm_export_sub_cb", "class" => "rm_export_sub_cb", "onclick" => "checkbox_disable_elements(this, 'rm_export_sub_url', 0, rm_add_class);", /*"value" => $data->model->form_options->should_export_submissions,*/ "longDesc" => RM_UI_Strings::get('HELP_SEND_SUB_TO_URL_CB'))));
            if ($data->model->form_options->should_export_submissions == null)
                $form->addElement(new Element_HTML('<div class="childfieldsrow" id="rm_export_sub_cb_childfieldsrow" style="display:none" >'));
            else
                $form->addElement(new Element_HTML('<div class="childfieldsrow" id="rm_export_sub_cb_childfieldsrow" >'));

            $form->addElement(new Element_Url("<b>" . RM_UI_Strings::get('LABEL_EXPORT_URL') . "</b>", "export_submissions_to_url", $options_send_to_url_field));

            $form->addElement(new Element_HTML('</div>'));
        }
        else {
            $options_send_to_url_field = array("id" => "rm_export_sub_url", /*"value" => $data->model->form_options->export_submissions_to_url,*/ "longDesc" => RM_UI_Strings::get('HELP_SEND_SUB_TO_URL'), "disabled" => true);
            $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_EXPORT_TO_URL_CB') . "</b>", "should_export_submissions", array(1 => RM_UI_Strings::get('ERROR_NA_SEND_TO_URL_FEAT')), array("id" => "rm_export_sub_cb", "class" => "rm_export_sub_cb", "onclick" => "hise_show(this);", "disabled" => true, /*"value" => $data->model->form_options->should_export_submissions,*/ "longDesc" => RM_UI_Strings::get('HELP_SEND_SUB_TO_URL_CB'))));
            if ($data->model->form_options->should_export_submissions == null)
                $form->addElement(new Element_HTML('<div class="childfieldsrow" id="rm_export_sub_cb_childfieldsrow" style="display:none" >'));
            else
                $form->addElement(new Element_Url("<b>" . RM_UI_Strings::get('LABEL_EXPORT_URL') . "</b>", "export_submissions_to_url", $options_send_to_url_field));
            $form->addElement(new Element_HTML('</div>'));
        }


//$form->addElement(new Element_Button(RM_UI_Strings::get('LABEL_CANCEL'), "button", array("id" => "rm_submit_btn", "class" => "rm_btn", "name" => "cancel", "href" => "?page=rm_form_manage")));
        $form->addElement(new Element_HTMLL('&#8592; &nbsp; '.__('Cancel','custom-registration-form-builder-with-submission-manager'), '?page=rm_form_manage', array('class' => 'cancel')));
        $form->addElement(new Element_Button(RM_UI_Strings::get('LABEL_SAVE'), "submit", array("id" => "rm_submit_btn", "class" => "rm_btn", "name" => "submit", "onClick" => "jQuery.prevent_field_add(event,'".__('This is a required field','custom-registration-form-builder-with-submission-manager')."')")));
        $form->render();
        ?>
    </div>
</div>

<?php
}
