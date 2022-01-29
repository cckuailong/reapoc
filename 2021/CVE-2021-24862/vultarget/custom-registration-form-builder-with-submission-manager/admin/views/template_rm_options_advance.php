<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_options_advance.php'); else {
?>
<div class="rmagic">

    <!--Dialogue Box Starts-->
    <div class="rmcontent">


        <?php
//PFBC form
        $form = new RM_PFBC_Form("options_advance");
        $form->configure(array(
            "prevent" => array("bootstrap", "jQuery"),
            "action" => ""
        ));
        $form->addElement(new Element_HTML('<div class="rmheader">'.__('Advance Options', 'custom-registration-form-builder-with-submission-manager').'</div>'));
        $form->addElement(new Element_Select("<b>".__('Save Session at', 'custom-registration-form-builder-with-submission-manager')."</b>", "session_policy", array('db'=>'Database','file'=>'File'), array("value" =>$data['session_policy'], "class" => "rm_static_field rm_required", "longDesc"=>__("Define at which level sessions are saved.",'custom-registration-form-builder-with-submission-manager'))));
        $form->addElement(new Element_HTMLL('&#8592; &nbsp; '.__("Cancel",'custom-registration-form-builder-with-submission-manager'), '?page=rm_options_manage', array('class' => 'cancel')));
        $form->addElement(new Element_Button(RM_UI_Strings::get('LABEL_SAVE')));

        $form->render();
        ?>

    </div>
</div>
<?php } ?>