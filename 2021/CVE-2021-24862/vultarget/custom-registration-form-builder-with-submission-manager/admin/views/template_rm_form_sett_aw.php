<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_form_sett_aw.php'); else {
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
        
        
        $form = new RM_PFBC_Form("form_sett_aweber");
        $form->configure(array(
            "prevent" => array("bootstrap", "jQuery"),
            "action" => ""
        ));
 if (isset($data->form_id)) {
            $form->addElement(new Element_HTML('<div class="rmheader">' . $data->form_name . '</div>'));
            $form->addElement(new Element_HTML('<div class="rmsettingtitle">' . RM_UI_Strings::get('LABEL_AWEBER_OPTION') . '</div>'));
            $form->addElement(new Element_Hidden("form_id", $data->form_id));
        } else {
            $form->addElement(new Element_HTML('<div class="rmheader">' . RM_UI_Strings::get("TITLE_NEW_FORM_PAGE") . '</div>'));
        }
        $form->addElement(new Element_Checkbox(RM_UI_Strings::get('LABEL_AWEBER_OPTION'), "enable_aweber", array(1 => ""),array("id" => "id_rm_enable_aw_cb", "class" => "id_rm_enable_aw_cb" ,"disabled" => "disabled",  "value" => "",   "longDesc" => RM_UI_Strings::get('MSG_BUY_PRO_BOTH_INLINE'))));
        //var_dump($data->model->form_options->enable_aweber);
      
        $form->addElement(new Element_HTML('<div class="childfieldsrow" id="id_rm_enable_aw_cb_childfieldsrow" >'));
        $form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LABEL_AW_LIST') . "</b>", "aw_list", array(0=>"Aweber list"), array("id" => "aw_list","disabled" => "disabled",  "value" => "", "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_AW_LIST'))));
        $form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LABEL_EMAIL') . "</b>", "email",array(0=>"Email"), array("id" => "email", "disabled" => "disabled",  "value" => "", "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_FIELD'))));
        $form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('FIRST_NAME') . "</b>", "first_name",array(0=>"First name"), array("id" => "first_name","disabled" => "disabled",  "value" => "" , "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_FIELD'))));
        $form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LAST_NAME') . "</b>", "last_name",array(0=>"Last name"), array("id" => "last_name", "disabled" => "disabled",  "value" => "", "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_FIELD'))));
        $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_OPT_IN_CB') . "</b>", "form_is_opt_in_checkbox_aw", array(1 => ""), array("id" => "rm_", "class" => "rm_op", "onclick" => "hide_show(this);","disabled" => "disabled",  "value" => "", "longDesc" => RM_UI_Strings::get('HELP_OPT_IN_CB_AW'))));
        $form->addElement(new Element_HTML('<div class="childfieldsrow" id="rm_op_childfieldsrow" >'));
        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_OPT_IN_CB_TEXT') . "</b>", "form_opt_in_text_aw", array("id" => "rm_form_name","disabled" => "disabled",  "value" => "", "longDesc" => RM_UI_Strings::get('HELP_OPT_IN_CB_TEXT'))));
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