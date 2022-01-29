<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_form_sett_override.php'); else {
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//var_dump($data);die;
?>

<div class="rmagic">

    <!--Dialogue Box Starts-->
    <div class="rmcontent">


        <?php
        $form = new RM_PFBC_Form("form_sett_general");
        $form->configure(array(
            "prevent" => array("bootstrap", "jQuery"),
            "action" => ""
        ));
        
        
        
        


        if (isset($data->model->form_id)) {
            $form->addElement(new Element_HTML('<div class="rmheader">' . $data->model->form_name . '</div>'));
            $form->addElement(new Element_HTML('<div class="rmsettingtitle">' . RM_UI_Strings::get('LABEL_F_GLOBAL_OVERRIDE_SETT') . '</div>'));
            $form->addElement(new Element_Hidden("form_id", $data->model->form_id));
        } else {
            $form->addElement(new Element_HTML('<div class="rmheader">' . RM_UI_Strings::get("TITLE_NEW_FORM_PAGE") . '</div>'));
        }
        $form->addElement(new Element_HTML('<div class="rmnotice rm-invite-field-row"><b>' . RM_UI_Strings::get("GLOBAL_OVERRIDES_NOTE") . '</b></div>     
'));
        $mails = explode(',',$data->model->form_options->admin_email);
        $form->addElement(new Element_Checkbox(RM_UI_Strings::get('LABEL_FORM_NOTIFS_TO'), "admin_notification", array("yes" => ''),array("id" => "id_rm_admin_notify_cb", "class" => "id_rm_admin_notify_cb" , "disabled" => 1,"value" =>$data->model->form_options->admin_notification,  "onclick" => "hide_show(this)" , "longDesc" => RM_UI_Strings::get('MSG_BUY_PRO_BOTH_INLINE'))));
        
        if ($data->model->form_options->admin_notification == 'yes')
            $form->addElement(new Element_HTML('<div class="childfieldsrow" id="id_rm_admin_notify_cb_childfieldsrow">'));
        else
            $form->addElement(new Element_HTML('<div class="childfieldsrow" id="id_rm_admin_notify_cb_childfieldsrow" style="display:none">'));
        $form->addElement(new Element_Emailsortable(RM_UI_Strings::get('LABEL_RECIPIENTS_OPTION') . ":", "resp_emails[]", array("id" => "rm_field_value_file_types", "class" => "rm-static-field rm_field_value rm_options_resp_email", "value" => $mails, "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_ARESP_RESPS'))));
        $form->addElement(new Element_HTML('</div>'));
        
        $form->addElement(new Element_Radio("<b>" . RM_UI_Strings::get('LABEL_SHOW_PROG_BAR') . "</b>", "display_progress_bar", array('yes'=>RM_UI_Strings::get('LABEL_YES'),'no'=>RM_UI_Strings::get('LABEL_NO'),'default'=>RM_UI_Strings::get('LABEL_DEFAULT')), array("id"=>"id_form_actrl_date_type","class"=>"rm_deactivated","readonly"=>"readonly","disabled"=>"disabled", "longDesc" => RM_UI_Strings::get('MSG_BUY_PRO_BOTH_INLINE'))));
        $form->addElement(new Element_Radio("<b>" . RM_UI_Strings::get('LABEL_ENABLE_CAPTCHA') . "</b>", "enable_captcha", array('no'=>RM_UI_Strings::get('LABEL_NO'),'default'=>RM_UI_Strings::get('LABEL_DEFAULT')), array("id"=>"id_rm_enable_captcha_cb","value" => "","class"=>"rm_deactivated","readonly"=>"readonly","disabled"=>"disabled", "longDesc" => RM_UI_Strings::get('MSG_BUY_PRO_BOTH_INLINE'))));
        $form->addElement(new Element_Number(RM_UI_Strings::get('LABEL_SUB_LIMIT_ANTISPAM'), "sub_limit_antispam", array("value" => "","class"=>"rm_deactivated","readonly"=>"readonly","disabled"=>"disabled", "step" => 1, "min" => 0, "longDesc" => RM_UI_Strings::get('MSG_BUY_PRO_BOTH_INLINE'))));
      
        if(!isset($data->model->form_id))
        $form->addElement(new Element_HTMLL('&#8592; &nbsp; '.__('Cancel','custom-registration-form-builder-with-submission-manager'), 'javascript:void(0)', array('class' => 'cancel', 'onClick' => 'window.history.back();')));
        else
            $form->addElement (new Element_HTMLL ('&#8592; &nbsp; '.__('Cancel','custom-registration-form-builder-with-submission-manager'), '?page='.$data->next_page.'&rm_form_id='.$data->model->form_id, array('class' => 'cancel')));
        $form->addElement(new Element_Button(RM_UI_Strings::get('LABEL_SAVE'), "submit", array("id" => "rm_submit_btn", "class" => "rm_btn", "disabled"=>1, "style"=>"opacity:0.25", "name" => "submit", "onClick" => "jQuery.prevent_field_add(event,'".__('This is a required field.', 'custom-registration-form-builder-with-submission-manager')."')")));
        $form->render();
        ?>
    </div>
</div>

<?php
}