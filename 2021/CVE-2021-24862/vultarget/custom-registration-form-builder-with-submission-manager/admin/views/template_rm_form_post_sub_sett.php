<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_form_post_sub_sett.php'); else {
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
global $rm_env_requirements;
?>

<div class="rmagic">

    <!--Dialogue Box Starts-->
    <div class="rmcontent">


        <?php
        $form = new RM_PFBC_Form("form_sett_post_sub");
        $form->configure(array(
            "prevent" => array("bootstrap", "jQuery", "focus"),
            "action" => ""
        ));

        if (isset($data->model->form_id)) {
            $form->addElement(new Element_HTML('<div class="rmheader">' . $data->model->form_name . '</div>'));
            $form->addElement(new Element_HTML('<div class="rmsettingtitle">' . RM_UI_Strings::get('LABEL_F_PST_SUB_SETT') . '</div>'));
            $form->addElement(new Element_Hidden("form_id", $data->model->form_id));
        } else {
            $form->addElement(new Element_HTML('<div class="rmheader">' . RM_UI_Strings::get("TITLE_NEW_FORM_PAGE") . '</div>'));
        }

        $form->addElement(new Element_TinyMCEWP("<b>" . RM_UI_Strings::get('LABEL_SUCC_MSG') . "</b>", $data->model->form_options->form_success_message, "form_success_message", array('editor_class' => 'rm_TinydMCE', 'editor_height' => '100px'), array("longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_SUCCESS_MSG'))));
         $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_UNIQUE_TOKEN') . "</b>", "get_pro_3", array(1 => ''), array("id" => "rm_", "disabled" => 1, "value" => 'no', "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_UNIQUE_TOKEN') . "<br><br>" . RM_UI_Strings::get('MSG_BUY_PRO_INLINE'))));
        $form->addElement(new Element_Radio("<b>" . RM_UI_Strings::get('LABEL_USER_REDIRECT') . "</b>", "form_redirect", array('none' => 'None', 'page' => 'Page', 'url' => 'URL'), array("id" => "rm_", "class" => "rm_", "onclick" => "hide_show_radio(this);", "value" => $data->model->form_redirect? : 'none', "required" => "1", "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_REDIRECT_AFTER_SUB'))));

        if ($data->model->form_redirect !== 'page' && $data->model->form_redirect !== 'url' )
            $form->addElement(new Element_HTML('<div class="childfieldsrow" id="rm__childfieldsrow" style="display:none" >'));
        else
            $form->addElement(new Element_HTML('<div class="childfieldsrow" id="rm__childfieldsrow" >'));
        if ($data->model->form_redirect == 'page')
            $form->addElement(new Element_HTML('<div class="rm_form_page">'));
        else
            $form->addElement(new Element_HTML('<div class="rm_form_page" style="display:none" >'));
        $form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LABEL_PAGE') . "</b>", "form_redirect_to_page", $data->wp_pages, array("id" => "rm_form_type", "value" => intval($data->model->get_form_redirect_to_page()) ? $data->model->get_form_redirect_to_page() : null, "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_REDIRECT_PAGE'))));
        $form->addElement(new Element_HTML('</div>'));
        if ($data->model->form_redirect == 'url')
            $form->addElement(new Element_HTML('<div class="rm_form_url"> '));
        else
            $form->addElement(new Element_HTML('<div class="rm_form_url"  style="display:none">  '));
        $form->addElement(new Element_Url("<b>" . RM_UI_Strings::get('LABEL_URL') . "</b>", "form_redirect_to_url", array("id" => "rm_form_name",  "value" => !intval($data->model->get_form_redirect_to_url()) ? $data->model->get_form_redirect_to_url() : null, "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_REDIRECT_URL'))));
        $form->addElement(new Element_HTML('</div>'));

        $form->addElement(new Element_HTML('</div>'));

        if ($rm_env_requirements & RM_REQ_EXT_CURL) {
            //options for export submissions to url
            $options_send_to_url_field = array("id" => "rm_export_sub_url", "value" => $data->model->form_options->export_submissions_to_url, "longDesc" => RM_UI_Strings::get('HELP_SEND_SUB_TO_URL'));
            if ($data->model->form_options->should_export_submissions != 1)
                $options_send_to_url_field['disabled'] = true;
            else
                $options_send_to_url_field['class'] = 'rm_prevent_empty';

            $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_EXPORT_TO_URL_CB') . "</b>", "", array(1 => ''), array("disabled" => true, "id" => "rm_export_sub_cb", "onclick" => "checkbox_disable_elements(this, 'rm_export_sub_url', 0, rm_add_class);",  "longDesc" => RM_UI_Strings::get('HELP_SEND_SUB_TO_URL_CB') . "<br><br>" . RM_UI_Strings::get('MSG_BUY_PRO_INLINE'))));
            
                $form->addElement(new Element_HTML('<div class="childfieldsrow" id="rm_export_sub_cb_childfieldsrow" style="display:none" >'));
            

            $form->addElement(new Element_Url("<b>" . RM_UI_Strings::get('LABEL_EXPORT_URL') . "</b>", "export_submissions_to_url", $options_send_to_url_field));

            $form->addElement(new Element_HTML('</div>'));
        }
        else {
            $options_send_to_url_field = array("id" => "rm_export_sub_url", "value" => $data->model->form_options->export_submissions_to_url, "longDesc" => RM_UI_Strings::get('HELP_SEND_SUB_TO_URL'), "disabled" => true);
            $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_EXPORT_TO_URL_CB') . "</b>", "should_export_submissions", array(1 => RM_UI_Strings::get('ERROR_NA_SEND_TO_URL_FEAT')), array("id" => "rm_export_sub_cb", "class" => "rm_export_sub_cb", "onclick" => "hise_show(this);", "disabled" => true, "value" => $data->model->form_options->should_export_submissions, "longDesc" => RM_UI_Strings::get('HELP_SEND_SUB_TO_URL_CB'))));
            if ($data->model->form_options->should_export_submissions == null)
                $form->addElement(new Element_HTML('<div class="childfieldsrow" id="rm_export_sub_cb_childfieldsrow" style="display:none" >'));
            else
                $form->addElement(new Element_Url("<b>" . RM_UI_Strings::get('LABEL_EXPORT_URL') . "</b>", "export_submissions_to_url", $options_send_to_url_field));
            $form->addElement(new Element_HTML('</div>'));
        }

        $form->addElement(new Element_HTMLL('&#8592; &nbsp; '.__('Cancel','custom-registration-form-builder-with-submission-manager'), '?page='.$data->next_page.'&rm_form_id='.$data->model->form_id, array('class' => 'cancel')));
        $form->addElement(new Element_Button(RM_UI_Strings::get('LABEL_SAVE'), "submit", array("id" => "rm_submit_btn", "class" => "rm_btn", "name" => "submit", "onClick" => "jQuery.prevent_field_add(event,'".__('This is a required field.','custom-registration-form-builder-with-submission-manager')."')")));
        $form->render();
        ?>
    </div>
    
    <?php 
    include RM_ADMIN_DIR.'views/template_rm_promo_banner_bottom.php';
    ?>
</div>

<?php
}

