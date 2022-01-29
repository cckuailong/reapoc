<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_primary_field_add.php'); else {
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$form = new RM_PFBC_Form("add-field");

$form->configure(array(
    "prevent" => array("bootstrap", "jQuery"),
    "action" => ""
));

$form->addElement(new Element_HTML('<div class="rmheader">' . RM_UI_Strings::get("TITLE_EDIT_FIELD_PAGE") . '</div>'));

$form->addElement(new Element_Hidden("field_id", $data->model->field_id));

$form->addElement(new Element_Hidden("form_id", $data->form_id));
$form->addElement(new Element_Hidden("field_is_required", 1));

if(!empty($data->model->field_type) && strtolower($data->model->field_type)=='username'){
    $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_SELECT_TYPE') . "</b>", "primary_field_type", array("id" => "rm_field_type_select_dropdown", "disabled" => "1" , "value" => $data->model->field_type, "class" => "rm_static_field rm_required", /*"required" => "1",*/ "longDesc"=>__('This is Username field. Type of this field can not be changed.','custom-registration-form-builder-with-submission-manager'))));
}
else if(!empty($data->model->field_type) && strtolower($data->model->field_type)=='userpassword'){
    $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_SELECT_TYPE') . "</b>", "primary_field_type", array("id" => "rm_field_type_select_dropdown", "disabled" => "1" , "value" => $data->model->field_type, "class" => "rm_static_field rm_required", /*"required" => "1",*/ "longDesc"=>__('This is password field. Type of this field can not be changed.','custom-registration-form-builder-with-submission-manager'))));
}
else{
    $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_SELECT_TYPE') . "</b>", "primary_field_type", array("id" => "rm_field_type_select_dropdown", "disabled" => "1" , "value" => $data->model->field_type, "class" => "rm_static_field rm_required", /*"required" => "1",*/ "longDesc"=>RM_UI_Strings::get('HELP_ADD_PRIMARY_FIELD_EMAIL'))));
}

$form->addElement(new Element_Hidden('field_type',$data->model->field_type));
$form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_LABEL') . "</b>", "field_label", array("class" => "rm_static_field rm_required", "required" => "1", "value" => $data->model->field_label, "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_LABEL'))));
$form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_PLACEHOLDER_TEXT') . "</b>", "field_placeholder", array("id" => "rm_field_placeholder", "class" => "rm_static_field rm_text_type_field rm_input_type", "value" => $data->model->field_options->field_placeholder, "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_PLACEHOLDER'))));

$form->addElement(new Element_HTML('<div id="rm_field_helptext_container">'));
$form->addElement(new Element_Textarea("<b>" . RM_UI_Strings::get('LABEL_HELP_TEXT') . "</b>", "help_text", array("id" => "rm_field_helptext", "class" => "", "value" => $data->model->field_options->help_text, "longDesc" => RM_UI_Strings::get('HELP_ADD_FIELD_HELP_TEXT'))));
$form->addElement(new Element_HTML('</div>'));

/***Begin :Icon Settings******/
$form->addElement(new Element_HTML('<div class="rmrow rm_field_settings_group_header rm_icon_sett_collapsed" id="rm_icon_field_settings_header" onclick="rm_toggle_icon_settings()"><a>' . RM_UI_Strings::get('ICON_FIELD_SETTINGS') . '<span class="rm-toggle-settings"></span></a></div>'));
$form->addElement(new Element_HTML('<div id="rm_icon_field_settings_container" style="display:none">'));
$form->addElement(new Element_HTML('<div id="rm_icon_setting_container">'));
$form->addElement(new Element_HTML('<div class="rmrow" id="rm_jqnotice_row_date_type"><div class="rmfield" for="rm_field_value_options_textarea"><label>'.RM_UI_Strings::get('LABEL_FIELD_ICON').'</label></div><div class="rminput" id="rm_field_icon_chosen"><i class="material-icons"'.$icon_style.' id="id_show_selected_icon">'.$f_icon->codepoint.'</i><div class="rm-icon-action"><div onclick="show_icon_reservoir()"><a href="javascript:void(0)">'.RM_UI_Strings::get('LABEL_FIELD_ICON_CHANGE').'</a></div> <div onclick="rm_remove_icon()"><a href="javascript:void(0)">'.RM_UI_Strings::get('LABEL_REMOVE').'</a></div></div></div><div class="rmnote"><div class="rmprenote"></div><div class="rmnotecontent">'.RM_UI_Strings::get('HELP_FIELD_ICON').'</div></div></div>'));
$form->addElement(new Element_Hidden('input_selected_icon_codepoint', $f_icon->codepoint, array('id'=>'id_input_selected_icon')));
$form->addElement(new Element_Color(RM_UI_Strings::get('LABEL_FIELD_ICON_FG_COLOR'), "icon_fg_color", array("id" => "rm_", "value" => $f_icon->fg_color, "onchange" => "change_icon_fg_color(this)", "longDesc" => RM_UI_Strings::get('HELP_FIELD_ICON_FG_COLOR'))));

$form->addElement(new Element_Color(RM_UI_Strings::get('LABEL_FIELD_ICON_BG_COLOR'), "icon_bg_color", array("id" => "rm_", "value" => $f_icon->bg_color, "onchange" => "change_icon_bg_color(this)", "longDesc" => RM_UI_Strings::get('HELP_FIELD_ICON_BG_COLOR'))));

$form->addElement(new Element_Range(RM_UI_Strings::get('LABEL_FIELD_ICON_BG_ALPHA'), "icon_bg_alpha", array("id" => "rm_", "value" => $f_icon->bg_alpha, "step" => 0.1, "min" => 0, "max" => 1, "oninput" => "finechange_icon_bg_color()", "onchange" => "finechange_icon_bg_color()", "longDesc" => RM_UI_Strings::get('HELP_FIELD_ICON_BG_ALPHA'))));

$form->addElement(new Element_Select(RM_UI_Strings::get('LABEL_FIELD_ICON_SHAPE'), "icon_shape", $icon_shapes, array("id" => "rm_", "value" => $f_icon->shape, "onchange" => "change_icon_shape(this)", "longDesc" => RM_UI_Strings::get('HELP_FIELD_ICON_SHAPE'))));
$form->addElement(new Element_HTML('</div>'));
$form->addElement(new Element_HTML('</div>'));
/***END :Icon Settings******/

$form->addElement(new Element_HTML('<div style="display:none">'));
$form->addElement(new Element_jQueryUIDate("", '', array()));
$form->addElement(new Element_HTML('</div>'));

/**** Begin: Advanced Field Settings ****/
$form->addElement(new Element_HTML('<div class="rmrow rm_field_settings_group_header rm_adv_sett_collapsed" id="rm_advance_field_settings_header" onclick="rm_toggle_adv_settings()"><a>' . RM_UI_Strings::get('ADV_FIELD_SETTINGS') . '<span class="rm-toggle-settings"></span></a></div>'));
$form->addElement(new Element_HTML('<div id="rm_advance_field_settings_container" style="display:none">'));
$form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_CSS_CLASS') . "</b>", "field_css_class", array("id" => "rm_field_class", "class" => "rm_static_field rm_required", "value" => $data->model->field_options->field_css_class, "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_CSS_CLASS'))));

if(strtolower($data->model->field_type)=="username" || strtolower($data->selected_field)=='username'){
    $form->addElement(new Element_Textarea("<b>".__('Username exists error','custom-registration-form-builder-with-submission-manager')."</b>", "user_exists_error", array("class" => "", "value" =>$data->model->field_options->user_exists_error , "longDesc" => sprintf(__('Contents of the error message user sees when trying to register with existing or blacklisted/ reserved username. You can manage blacklisted/ reserved usernames in Global Settings → Security. <a target="_blank" class="rm-more" href="%s">More</a>','custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/username-field-settings/#htexisterror'))));
    $form->addElement(new Element_Checkbox("<b>".__('Allowed Characters','custom-registration-form-builder-with-submission-manager')."</b>", "username_characters", array('alphabets' => __('Alphabets','custom-registration-form-builder-with-submission-manager'),'numbers'=>__('Numbers','custom-registration-form-builder-with-submission-manager'),'underscores'=>__('Underscores','custom-registration-form-builder-with-submission-manager'),'periods'=>__('Periods','custom-registration-form-builder-with-submission-manager')), array("class" => "rm_field_multiline rm_input_type", "value" => $data->model->field_options->username_characters, "longDesc"=>sprintf(__('Select the type of characters accepted for usernames. If none selected, All the characters would be allowed. <a target="_blank" class="rm-more" href="%s">More</a>','custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/username-field-settings/#htallowedchars'))));
    $form->addElement(new Element_Textarea("<b>".__('Invalid username format error','custom-registration-form-builder-with-submission-manager')."</b>", "invalid_username_format", array("class" => "", "value" => $data->model->field_options->invalid_username_format, "longDesc" => sprintf(__('Contents of the error message user sees when trying to enter username with characters not allowed. Use code {{allowed_characters}} to insert allowed characters dynamically. <a target="_blank" class="rm-more" href="%s">More</a>','custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/username-field-settings/#htinvalidformat'))));
}

if(strtolower($data->model->field_type)=="userpassword" || strtolower($data->selected_field)=='userpassword'){
    $form->addElement(new Element_Checkbox("<b>".__('Display password confirmation field','custom-registration-form-builder-with-submission-manager')."</b>", "en_confirm_pwd", array('1'=>''), array("class" => "rm_field_multiline rm_input_type en_confirm_pwd", "value" => $data->model->field_options->en_confirm_pwd, "longDesc"=>sprintf(__('Display a password confirmation field below the password field. <a target="_blank" class="rm-more" href="%s">More</a>','custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/password-field-settings/#htdisplayconfirmation'))));
        $form->addElement(new Element_HTML('<div class="childfieldsrow" id="confirm_pwd_options">'));
             $form->addElement(new Element_Textarea("<b>".__('Password match error','custom-registration-form-builder-with-submission-manager')."</b>", "pass_mismatch_err", array("class" => "", "value" =>$data->model->field_options->pass_mismatch_err , "longDesc" => sprintf(__('Contents of the error message user sees when the password entered in password confirmation field does not matches. <a target="_blank" class="rm-more" href="%s">More</a>','custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/password-field-settings/#htmatcherror'))));
        $form->addElement(new Element_HTML('</div>'));
    $gopts= new RM_Options();
 

      
    $form->addElement(new Element_Checkbox("<b>".__('Display password strength meter','custom-registration-form-builder-with-submission-manager')."</b>", "en_pass_strength", array('1'=>''), array("class" => "rm_field_multiline rm_input_type en_pass_strength ", "value" => $data->model->field_options->en_pass_strength, "longDesc"=>sprintf(__('Displays a live password strength meter to provide feedback to the user about the strength of the password they are entering. For advance password rules, checkout Global Settings → Security. <a target="_blank" class="rm-more" href="%s">More</a>','custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/password-field-settings/#htstrengthmeter'))));   
    $form->addElement(new Element_HTML("<div class='childfieldsrow' id='pwd_strength_options'>"));
        $form->addElement(new Element_Textbox("<b>".__('Short','custom-registration-form-builder-with-submission-manager')."</b>", "pwd_short_msg", array("class" => "rm_static_field", "value" => $data->model->field_options->pwd_short_msg, "longDesc"=>'')));
        $form->addElement(new Element_Textbox("<b>".__('Weak','custom-registration-form-builder-with-submission-manager')."</b>", "pwd_weak_msg", array("class" => "rm_static_field", "value" => $data->model->field_options->pwd_weak_msg, "longDesc"=>'')));
        $form->addElement(new Element_Textbox("<b>".__('Medium','custom-registration-form-builder-with-submission-manager')."</b>", "pwd_medium_msg", array("class" => "rm_static_field", "value" => $data->model->field_options->pwd_medium_msg, "longDesc"=>'')));
        $form->addElement(new Element_Textbox("<b>".__('Strong','custom-registration-form-builder-with-submission-manager')."</b>", "pwd_strong_msg", array("class" => "rm_static_field", "value" => $data->model->field_options->pwd_strong_msg, "longDesc"=>'')));
    $form->addElement(new Element_HTML('</div>'));
}
    
if(strtolower($data->model->field_type)=="email" && $data->model->is_field_primary == 1) {
    $form->addElement(new Element_Checkbox("<b>".__('Display email confirmation field','custom-registration-form-builder-with-submission-manager')."</b>", "en_confirm_email", array('1'=>''), array("class" => "rm_field_multiline rm_input_type en_confirm_email", "value" => $data->model->field_options->en_confirm_email, "longDesc"=>__('Display an email confirmation field below the email field.','custom-registration-form-builder-with-submission-manager'))));
    $form->addElement(new Element_HTML('<div class="childfieldsrow" id="confirm_pwd_options">'));
    $form->addElement(new Element_Textarea("<b>".__('Email mismatch error message','custom-registration-form-builder-with-submission-manager')."</b>", "email_mismatch_err", array("class" => "", "value" =>$data->model->field_options->email_mismatch_err , "longDesc" => __('Contents of the error message user sees when the email entered in email confirmation field does not match.','custom-registration-form-builder-with-submission-manager'))));
    $form->addElement(new Element_HTML('</div>'));
}

$form->addElement(new Element_HTML('</div>'));
/**** End: Advanced Field Settings */

//Button Area
$form->addElement(new Element_HTMLL('&#8592; &nbsp; '.__('Cancel','custom-registration-form-builder-with-submission-manager'), '?page=rm_field_manage&rm_form_id='.$data->form_id, array('class' => 'cancel')));
$form->addElement(new Element_Button(RM_UI_Strings::get('LABEL_SAVE'), "submit", array("id" => "rm_submit_btn", "class" => "rm_btn", "name" => "submit")));

?>

<?php 

$form->render();
}