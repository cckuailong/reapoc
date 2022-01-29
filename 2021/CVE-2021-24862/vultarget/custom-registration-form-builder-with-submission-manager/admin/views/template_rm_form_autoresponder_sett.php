<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_form_autoresponder_sett.php'); else {
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<div class="rmagic">

    <!--Dialogue Box Starts-->
    <div class="rmcontent">


        <?php
        $form = new RM_PFBC_Form("form_sett_autoresponder");
        $form->configure(array(
            "prevent" => array("bootstrap", "jQuery"),
            "action" => ""
        ));

        if (isset($data->model->form_id)) {
            $form->addElement(new Element_HTML('<div class="rmheader">' . $data->model->form_name . '</div>'));
            $form->addElement(new Element_HTML('<div class="rmsettingtitle">' . RM_UI_Strings::get('LABEL_F_AUTO_RESP_SETT') . '</div>'));
            $form->addElement(new Element_Hidden("form_id", $data->model->form_id));
        } else {
            $form->addElement(new Element_HTML('<div class="rmheader">' . RM_UI_Strings::get("TITLE_NEW_FORM_PAGE") . '</div>'));
        }

        $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_AUTO_REPLY') . "</b>", "form_should_send_email", array(1 => ""), array("id" => "rm_ss", "onclick" => "rm_toggle(    this, 'rm_auto_reply')", "class" => "rm_ss", "onclick" => "hide_show(this);", "value" => $data->model->form_should_send_email, "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_AUTO_RESPONDER'))));

        if ($data->model->form_should_send_email == '1')
            $form->addElement(new Element_HTML('<div class="childfieldsrow" id="rm_ss_childfieldsrow" >'));
        else
            $form->addElement(new Element_HTML('<div class="childfieldsrow" id="rm_ss_childfieldsrow" style="display:none">'));

        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_AR_EMAIL_SUBJECT') . "</b>", "form_email_subject", array("id" => "rm_form_name", "value" => $data->model->form_options->form_email_subject, "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_AUTO_RESP_SUB'))));

        $form->addElement(new Element_TinyMCEWP("<b>" . RM_UI_Strings::get('LABEL_AR_EMAIL_BODY') . "</b>(".__('Mail Merge and HTML Supported', 'custom-registration-form-builder-with-submission-manager')."):", $data->model->form_options->form_email_content, "form_email_content", array('editor_class' => 'rm_TinydMCE', 'editor_height' => '100px'), array("longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_AUTO_RESP_MSG'))));
        $form->addElement(new Element_HTML('</div>'));

        $form->addElement(new Element_HTMLL('&#8592; &nbsp; '.__('Cancel','custom-registration-form-builder-with-submission-manager'), '?page='.$data->next_page.'&rm_form_id='.$data->model->form_id, array('class' => 'cancel')));
        $form->addElement(new Element_Button(RM_UI_Strings::get('LABEL_SAVE'), "submit", array("id" => "rm_submit_btn", "class" => "rm_btn", "name" => "submit", "onClick" => "jQuery.prevent_field_add(event,'".__('This is a required field.','custom-registration-form-builder-with-submission-manager')."')")));
        $form->render();
        ?>
    </div>
    <?php 
    if(!defined('REGMAGIC_ADDON')) include RM_ADMIN_DIR.'views/template_rm_promo_banner_bottom.php';
    ?>
    
</div>

<?php } ?>