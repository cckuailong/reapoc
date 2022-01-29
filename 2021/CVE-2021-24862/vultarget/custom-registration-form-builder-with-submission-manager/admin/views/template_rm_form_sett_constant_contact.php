<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_form_sett_constant_contact.php'); else {
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

global $rm_env_requirements;

?>

<?php if (!($rm_env_requirements & RM_REQ_EXT_CURL)){ ?>
 <div class="shortcode_notification ext_na_error_notice"><p class="rm-notice-para"><?php echo RM_UI_Strings::get('RM_ERROR_EXTENSION_CURL_CC');?></p></div>
 <?php }
 $installed_php_version = phpversion();
//var_dump(version_compare('5.4', $installed_php_version, '<') && RM_REQ_EXT_CURL);
 if (!version_compare('5.4', $installed_php_version, '<'))
      {
 ?>
 <div class="shortcode_notification ext_na_error_notice"><p class="rm-notice-para"><?php echo RM_UI_Strings::get('RM_ERROR_PHP_4.5');?></p></div>
 <?php 
      }

 ?>
<div class="rmagic">

    <!--Dialogue Box Starts-->
    <div class="rmcontent">


        <?php
        $form = new RM_PFBC_Form("form_sett_cc");
        $form->configure(array(
            "prevent" => array("bootstrap", "jQuery"),
            "action" => ""
        ));
        if (isset($data->form_id)) {
            $form->addElement(new Element_HTML('<div class="rmheader">' . $data->form_name . '</div>'));
            $form->addElement(new Element_HTML('<div class="rmsettingtitle">' . RM_UI_Strings::get('LABEL_CONSTANT_CONTACT_OPTION') . '</div>'));
            $form->addElement(new Element_Hidden("form_id", $data->form_id));
        } else {
            $form->addElement(new Element_HTML('<div class="rmheader">' . RM_UI_Strings::get("TITLE_NEW_FORM_PAGE") . '</div>'));
        }
        $form->addElement(new Element_Checkbox(RM_UI_Strings::get('LABEL_CONSTANT_CONTACT_OPTION'), "enable_ccontact", array(1 => ""),array("id" => "id_rm_enable_cc_cb", "class" => "id_rm_enable_cc_cb" ,"disabled" => "disabled",  "value" => "", "longDesc" => RM_UI_Strings::get('MSG_BUY_PRO_BOTH_INLINE'))));
       
           $form->addElement(new Element_HTML('<div class="childfieldsrow" id="id_rm_enable_cc_cb_childfieldsrow">'));
       
        $form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LABEL_CC_LIST') . "</b>", "cc_list", array(0=>"cc_list"), array("id" => "cc_list",  "disabled" => "disabled",  "value" => "", "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_CC_LIST'))));
        $form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LABEL_EMAIL') . "</b>", "email",array(0=>"Email"), array("id" => "email",  "disabled" => "disabled",  "value" => ""  , "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_CC'))));
        $form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('FIRST_NAME') . "</b>", "first_name",array(0=>"first name"), array("id" => "first_name", "disabled" => "disabled",  "value" => "", "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_CC'))));
        $form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LAST_NAME') . "</b>", "last_name",array(0=>"last name"), array("id" => "last_name", "disabled" => "disabled",  "value" => "" , "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_CC'))));
        $form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LABEL_MNAME') . "</b>", "middle_name",array(0=>"middle name"), array("id" => "middle_name", "disabled" => "disabled",  "value" => "", "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_CC'))));
        $form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LABEL_COMPANY') . "</b>", "company_name",array(0=>"company name"), array("id" => "company_name", "disabled" => "disabled",  "value" => "" , "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_CC'))));
        $form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LABEL_JOB_TILE') . "</b>", "job_title", array(0=>"job title"),array("id" => "job_title", "disabled" => "disabled",  "value" => "", "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_CC'))));
        $form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LABEL_WORK_PHONE') . "</b>", "work_phone",array(0=>"work phone"), array("id" => "work_phone",  "disabled" => "disabled",  "value" => "" , "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_CC'))));
        $form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LABEL_CELL_PHONE') . "</b>", "cell_phone",array(0=>"cell phone"), array("id" => "cell_phone", "disabled" => "disabled",  "value" => "" , "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_CC'))));
        $form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LABEL_HOME_PHONE') . "</b>", "home_phone",array(0=>"home phone"), array("id" => "home_phone",  "disabled" => "disabled",  "value" => "", "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_CC'))));
        //$form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LABEL_FAX') . "</b>", "fax",$data->field_array['phone'], array("id" => "fax", "value" =>isset($data->model->form_options->cc_relations->fax) ?$data->model->form_options->cc_relations->fax : "" , "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_CC'))));
        //$form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LABEL_ADDRESS') . "</b>", "address",$data->field_array['address'], array("id" => "address", "value" =>isset($data->model->form_options->cc_relations->address) ?$data->model->form_options->cc_relations->address : "" , "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_CC'))));
        //$form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LABEL_CREATED_DATE') . "</b>", "created_date",$data->field_array['date'], array("id" => "created_date", "value" =>isset($data->model->form_options->cc_relations->created_date) ?$data->model->form_options->cc_relations->created_date : "" , "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_CC'))));
        $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_OPT_IN_CB') . "</b>", "form_is_opt_in_checkbox_cc", array(1 => ""), array("id" => "rm_", "class" => "rm_op",  "disabled" => "disabled",  "value" => "", "longDesc" => RM_UI_Strings::get('HELP_OPT_IN_CB_CC'))));

        $form->addElement(new Element_HTML('<div class="childfieldsrow" id="rm_op_childfieldsrow" >'));
        
        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_OPT_IN_CB_TEXT') . "</b>", "form_opt_in_text_cc", array("id" => "rm_form_name",  "disabled" => "disabled",  "value" => "", "longDesc" => RM_UI_Strings::get('HELP_OPT_IN_CB_TEXT'))));
        $form->addElement(new Element_Radio("<b>" . RM_UI_Strings::get('LABEL_DEFAULT_STATE') . "</b>", "Default State", array('Checked'=>RM_UI_Strings::get('LABEL_CHECKED'),'Unchecked'=>RM_UI_Strings::get('LABEL_UNCHECKED')), array("id"=>"id_rm_default_state","disabled" => "disabled",  "value" => "", "longDesc" => RM_UI_Strings::get('MSG_OPT_IN_DEFAULT_STATE'))));
       
        $form->addElement(new Element_HTML('</div>'));
        $form->addElement(new Element_HTML('</div>'));
        $form->addElement (new Element_HTMLL ('&#8592; &nbsp; '.__('Cancel','custom-registration-form-builder-with-submission-manager'), '?page='.$data->next_page.'&rm_form_id='.$data->form_id, array('class' => 'cancel')));
 
        $form->render();
        ?>
    </div>
    <?php 
    include RM_ADMIN_DIR.'views/template_rm_promo_banner_bottom.php';
    ?>
</div>

<?php
}