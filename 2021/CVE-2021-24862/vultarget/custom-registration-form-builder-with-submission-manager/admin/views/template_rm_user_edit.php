<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_user_edit.php'); else {
//echo '<pre>'; var_dump($data->user->roles[0]);die;
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

        $form = new RM_PFBC_Form("rm_edit_user");
        $form->configure(array(
            "prevent" => array("bootstrap", "jQuery"),
            "action" => ""
        ));

        $form->addElement(new Element_HTML('<div class="rmheader">' . RM_UI_Strings::get('TITLE_USER_EDIT_PAGE') . '</div>'));
        $form->addElement(new Element_Hidden('rm_slug' , 'rm_user_edit'));
        $form->addElement(new Element_HTML(wp_nonce_field('edit_rm_user')));
        $form->addElement(new Element_Hidden('user_id' , $data->user->ID));
        $form->addElement(new Element_Hidden('rm_submitted' , 'true'));
        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_USERNAME') . "</b>", "user_name", array("class" => "rm-static-field rm_required", "required" => "1", "readonly" => "1", "value" => $data->user->user_login)));
        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_USEREMAIL') . "</b>", "user_email", array("class" => "rm-static-field rm_required", "required" => "1", "readonly" => "1", "value" => $data->user->user_email)));
        $form->addElement(new Element_Password("<b>" . RM_UI_Strings::get('LABEL_PASSWORD') . "</b>", "user_password", array("class" => "rm-static-field rm_required", "value" => '')));
        $form->addElement(new Element_Password("<b>" . RM_UI_Strings::get('LABEL_CONFIRM_PASSWORD') . "</b>", "user_password_conf", array("class" => "rm-static-field rm_required", "value" => '')));
        $form->addElement(new Element_Select(RM_UI_Strings::get('LABEL_ROLE'), "user_role", $data->roles, array("value" => $data->user->roles[0])));
        $form->addElement(new Element_HTMLL('&#8592; &nbsp; '.__('Cancel','custom-registration-form-builder-with-submission-manager'), '?page=rm_user_view&user_id='.$data->user->ID, array('class' => 'cancel')));
        $form->addElement(new Element_Button(RM_UI_Strings::get('LABEL_SAVE'), "submit", array("id" => "rm_submit_btn", "class" => "rm_btn", "name" => "submit")));
        $form->render();
        ?> 

    </div>
    <?php     
    //$rm_promo_banner_title = "Unlock export submissions and more by upgrading";
    include RM_ADMIN_DIR.'views/template_rm_promo_banner_bottom.php';
    ?>
</div>
<?php } ?>