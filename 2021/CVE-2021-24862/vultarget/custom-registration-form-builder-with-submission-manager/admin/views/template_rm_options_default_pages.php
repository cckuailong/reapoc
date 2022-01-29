<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_options_default_pages.php'); else {
?>
<div class="rmagic">

    <!--Dialogue Box Starts-->
    <div class="rmcontent">


        <?php
        $wp_pages = RM_Utilities::wp_pages_dropdown();
        $form = new RM_PFBC_Form("rm_default_pages");
        $form->configure(array(
            "prevent" => array("bootstrap", "jQuery"),
            "action" => ""
        ));
        $form->addElement(new Element_HTML('<div class="rmheader">'.__('Default Pages', 'custom-registration-form-builder-with-submission-manager').'</div>'));
        $selected = ($data['default_registration_url'] !== null) ? $data['default_registration_url'] : 0;
        $form->addElement(new Element_Select(RM_UI_Strings::get('LABEL_DEFAULT_REGISTER_URL'), "default_registration_url", $wp_pages, array("value" => $selected, "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_GEN_REG_URL'))));
        
        $options= new RM_Options();
        $front_sub_page= $options->get_value_of('front_sub_page_id');
        $form->addElement(new Element_Select(__('Default User Account Page', 'custom-registration-form-builder-with-submission-manager'), "default_user_acc_page", $wp_pages, array("value" => $front_sub_page,"longDesc" => __("Select the page on which you have pasted shortcode [RM_Front_Submissions].",'custom-registration-form-builder-with-submission-manager'))));

        $form->addElement(new Element_HTMLL('&#8592; &nbsp; '.__('Cancel','custom-registration-form-builder-with-submission-manager'), '?page=rm_options_manage', array('class' => 'cancel')));
        $form->addElement(new Element_Button(RM_UI_Strings::get('LABEL_SAVE'), "submit", array("id" => "rm_submit_btn", "class" => "rm_btn", "name" => "submit", "onClick" => "jQuery.prevent_field_add(event,'".__('This is a required field.','custom-registration-form-builder-with-submission-manager') ."')")));
        $form->render();
        ?>
    </div>
</div>

<?php
}
