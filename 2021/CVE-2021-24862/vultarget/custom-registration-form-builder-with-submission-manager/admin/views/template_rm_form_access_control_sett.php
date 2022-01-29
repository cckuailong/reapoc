<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_form_access_control_sett.php'); else {
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$date_cb_value = 1;
$pass_cb_value = 1;
$role_cb_value = 1;
$date_question = RM_UI_Strings::get('LABEL_ACTRL_DATE_QUESTION_DEF');
$pass_question = RM_UI_Strings::get('LABEL_ACTRL_PASS_QUESTION_DEF');
$fail_msg = RM_UI_Strings::get('LABEL_ACTRL_FAIL_MSG_DEF');
$date_ll = '';
$date_ul = '';
$diff_ll = 18;
$diff_ul = 45;

$date_type = "diff";

$passphrase = null;

$roles = array('administrator', 'editor', 'author');
?>

<div class="rmagic">

    <!--Dialogue Box Starts-->
    <div class="rmcontent">


        <?php
        $form = new RM_PFBC_Form("form_sett_access_control");
        $form->configure(array(
            "prevent" => array("bootstrap", "jQuery"),
            "action" => ""
        ));
        
        if (isset($data->model->form_id)) {
            $form->addElement(new Element_HTML('<div class="rmheader">' . $data->model->form_name . '</div>'));
            $form->addElement(new Element_HTML('<div class="rmsettingtitle">' . RM_UI_Strings::get('LABEL_F_ACTRL_SETT') . '</div>'));
            $form->addElement(new Element_Hidden("form_id", $data->model->form_id));
        } else {
            $form->addElement(new Element_HTML('<div class="rmheader">' . RM_UI_Strings::get("TITLE_NEW_FORM_PAGE") . '</div>'));
        }
        
        //Date based restrictions.
        $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_ACTRL_DATE_CB') . "</b>", "form_actrl_date_cb", array(1=>''), array("id" => "id_form_actrl_date_cb", "value" => $date_cb_value, "onclick" => "actrl_date_click_handler()", "disabled"=>1,"longDesc" => RM_UI_Strings::get('HELP_FORM_ACTRL_DATE').RM_UI_Strings::get('MSG_BUY_PRO_BOTH_INLINE'))));
        
        
        //border is inlined to prevent jumpy animation.
        //Wonderful hack from: http://stackoverflow.com/questions/1335461/jquery-slide-is-jumpy
        if($date_cb_value)
        {
            $form->addElement(new Element_HTML("<div id='form_actrl_date_container' class='childfieldsrow' style='border: 1px solid transparent'>"));
        } 
        else
        {
            $form->addElement(new Element_HTML("<div id='form_actrl_date_container' class='childfieldsrow' style='display:none;border: 1px solid transparent;'>"));
        }
        
        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_ACTRL_DATE_QUESTION') . "</b>", "form_actrl_date_question", array("value" => $date_question, "disabled"=>1,"required"=>"required" ,"longDesc" => RM_UI_Strings::get('HELP_FORM_ACTRL_DATE_QSTN'))));
        
        $form->addElement(new Element_Radio("<b>" . RM_UI_Strings::get('LABEL_ACTRL_DATE_TYPE') . "</b>", "form_actrl_date_type", array('diff'=>RM_UI_Strings::get('LABEL_ACTRL_DATE_TYPE_DIFF'),'date'=>RM_UI_Strings::get('LABEL_ACTRL_DATE_TYPE_DATE')), array("id"=>"id_form_actrl_date_type","value" => $date_type, 'onclick' => 'handle_date_type_change(this)', "disabled"=>1, "longDesc" => RM_UI_Strings::get('HELP_FORM_ACTRL_DATE_TYPE'))));
        $form->addElement(new Element_HTML('<div class="rmrow" id="rm_jqnotice_row_date_type" style="display:none;padding: 0px 20px 0px 20px;min-height: 0px;"><div class="rmfield" for="rm_field_value_options_textarea"><label></label></div><div class="rminput" id="rm_jqnotice_text">'.RM_UI_Strings::get('MSG_INVALID_ACTRL_DATE_TYPE').'</div></div>'));
        if($date_type === 'date')
        {
            $form->addElement(new Element_HTML("<div id='form_actrl_date_type_1_container' style='border: 1px solid transparent'>"));
        } 
        else
        {
            $form->addElement(new Element_HTML("<div id='form_actrl_date_type_1_container' style='display:none;border: 1px solid transparent;'>"));
        }
        $form->addElement(new Element_jQueryUIDate("<b>" . RM_UI_Strings::get('LABEL_ACTRL_DATE_LLIMIT') . "</b>", "form_actrl_date_ll_date", array("value" => $date_ll, "disabled"=>1, "id" => "form_actrl_date_ll_date","longDesc" => '')));
        
        $form->addElement(new Element_jQueryUIDate("<b>" . RM_UI_Strings::get('LABEL_ACTRL_DATE_ULIMIT') . "</b>", "form_actrl_date_ul_date", array("value" => $date_ul, "disabled"=>1,"id" => "form_actrl_date_ul_date", "longDesc" => '')));
         $form->addElement(new Element_HTML("<div id='date_error' style='display:none' align='center'>"));
          $form->addElement(new Element_HTML("</div>"));
        
        
        $form->addElement(new Element_HTML("</div>"));
         if($date_type === 'diff')
        {
            $form->addElement(new Element_HTML("<div id='form_actrl_date_type_2_container' style='border: 1px solid transparent'>"));
        } 
        else
        {
            $form->addElement(new Element_HTML("<div id='form_actrl_date_type_2_container' style='display:none;border: 1px solid transparent;'>"));
        }
        $form->addElement(new Element_Number("<b>" . RM_UI_Strings::get('LABEL_ACTRL_DATE_LLIMIT') . "</b>", "form_actrl_date_ll_diff", array("value" => $diff_ll, "disabled"=>1, "id" => "form_actrl_date_ll_diff","longDesc" => '')));
        
        $form->addElement(new Element_Number("<b>" . RM_UI_Strings::get('LABEL_ACTRL_DATE_ULIMIT') . "</b>", "form_actrl_date_ul_diff", array("value" => $diff_ul, "disabled"=>1,  "id" => "form_actrl_date_ul_diff","longDesc" => '')));
        $form->addElement(new Element_HTML("</div>"));
        $form->addElement(new Element_HTML("<div id='date_limit_error' style='display:none' align='center'>"));
          $form->addElement(new Element_HTML("</div>"));
        $form->addElement(new Element_HTML('<div class="rmrow" id="rm_jqnotice_row_date_limit" style="display:none;padding: 0px 20px 0px 20px;min-height: 0px;"><div class="rmfield" for="rm_field_value_options_textarea"><label></label></div><div class="rminput" id="rm_jqnotice_text">'.RM_UI_Strings::get('MSG_INVALID_ACTRL_DATE_LIMIT').'</div></div>'));
        
        $form->addElement(new Element_HTML("</div>"));
        
        
        //Passphrase based restrictions
        $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_ACTRL_PASS_CB') . "</b>", "form_actrl_pass_cb", array(1=>''), array("id" => "id_form_actrl_pass_cb", "value" => $pass_cb_value, "onclick" => "actrl_pass_click_handler()", "disabled"=>1, "longDesc" => RM_UI_Strings::get('HELP_FORM_ACTRL_PASS').RM_UI_Strings::get('MSG_BUY_PRO_BOTH_INLINE'))));
        
        if($pass_cb_value)
        {
            $form->addElement(new Element_HTML("<div id='form_actrl_pass_container' class='childfieldsrow' style='border: 1px solid transparent;'>"));
        } 
        else
        {
            $form->addElement(new Element_HTML("<div id='form_actrl_pass_container' class='childfieldsrow' style='display:none;border: 1px solid transparent;'>"));
        }
        
        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_ACTRL_PASS_QUESTION') . "</b>", "form_actrl_pass_question", array("value" => $pass_question, "disabled"=>1, "longDesc" => RM_UI_Strings::get('HELP_FORM_ACTRL_PASS_QSTN'))));
        
        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_ACTRL_PASS_PASS') . "</b>", "form_actrl_pass_passphrase", array("value" => $passphrase, "disabled"=>1, "longDesc" => RM_UI_Strings::get('HELP_FORM_ACTRL_PASS_PASS'))));
        $form->addElement(new Element_HTML('<div class="rmrow" id="rm_jqnotice_row_pass_pass" style="display:none;padding: 0px 20px 0px 20px;min-height: 0px;"><div class="rmfield" for="rm_field_value_options_textarea"><label></label></div><div class="rminput" id="rm_jqnotice_text">'.RM_UI_Strings::get('MSG_INVALID_ACTRL_PASS_PASS').'</div></div>'));
        $form->addElement(new Element_HTML("</div>"));
        
        //User role based restrictions
        $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_ACTRL_ROLE_CB') . "</b>", "form_actrl_role_cb", array(1=>''), array("id" => "id_form_actrl_role_cb", "value" => $role_cb_value, "onclick" => "actrl_role_click_handler()", "disabled"=>1, "longDesc" => RM_UI_Strings::get('HELP_FORM_ACTRL_ROLE').RM_UI_Strings::get('MSG_BUY_PRO_BOTH_INLINE'))));
        
        if($role_cb_value)
        {
            $form->addElement(new Element_HTML("<div id='form_actrl_role_container' class='childfieldsrow' style='border: 1px solid transparent;'>"));
        } 
        else
        {
            $form->addElement(new Element_HTML("<div id='form_actrl_role_container' class='childfieldsrow' style='display:none;border: 1px solid transparent;'>"));
        }
        
        $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_ACTRL_ROLE_ROLES') . "</b>", "form_actrl_roles", $data->all_roles, array("id"=>"id_form_actrl_date_type", "disabled"=>1, "value" => $roles, "longDesc" => RM_UI_Strings::get('HELP_FORM_ACTRL_ROLE_ROLES'))));
        
        $form->addElement(new Element_HTML('<div class="rmrow" id="rm_jqnotice_row_roles" style="display:none;padding: 0px 20px 0px 20px;min-height: 0px;"><div class="rmfield" for="rm_field_value_options_textarea"><label></label></div><div class="rminput" id="rm_jqnotice_text">'.RM_UI_Strings::get('MSG_INVALID_ACTRL_ROLES').'</div></div>'));

        $form->addElement(new Element_HTML("</div>"));

        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_ACTRL_FAIL_MSG') . "</b>", "form_actrl_fail_msg", array("value" => $fail_msg, "disabled"=>1, "longDesc" => RM_UI_Strings::get('HELP_FORM_ACTRL_FAIL_MSG'))));
        
        $form->addElement(new Element_HTMLL('&#8592; &nbsp; '.__('Cancel','custom-registration-form-builder-with-submission-manager'), '?page='.$data->next_page.'&rm_form_id='.$data->form_id, array('class' => 'cancel')));
        $form->addElement(new Element_Button(RM_UI_Strings::get('LABEL_SAVE'), "submit", array("id" => "rm_submit_btn", "class" => "rm_btn", "disabled"=>1, "name" => "submit", "style"=>"opacity:0.25")));
        $form->render();
        ?>
    </div>
    
    <?php 
    $rm_promo_banner_title = __("Unlock powerful access control and more by upgrading",'custom-registration-form-builder-with-submission-manager');
    include RM_ADMIN_DIR.'views/template_rm_promo_banner_bottom.php';
    ?>

</div>

<pre class='rm-pre-wrapper-for-script-tags'><script>
    

</script></pre>

<?php }

