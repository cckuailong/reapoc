<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_options_user.php'); else {
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
        $form = new RM_PFBC_Form("options_users");
        $form->configure(array(
            "prevent" => array("bootstrap", "jQuery"),
            "action" => ""
        ));
        
        $acc_activation_methods = array('yes' => RM_UI_Strings::get('LABEL_ACC_ACT_AUTO'),
                                    '' => RM_UI_Strings::get('LABEL_ACC_ACT_MANUALLY'),//RM_UI_Strings::get('RATING_STAR_FACE_HEART'),
                                    'verify' => RM_UI_Strings::get('LABEL_ACC_ACT_BY_VERIFICATION')
                                    );
        
        $options_sp = array("id" => "id_rm_send_pass_cb", "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_USER_SEND_PASS'));
        
        if($data['auto_generated_password'] === 'yes')
            $options_sp['disabled'] = true;
        
        if( $data['send_password'] === 'yes')
            $options_sp['value'] = 'yes';

        $form->addElement(new Element_HTML('<div class="rmheader">' . RM_UI_Strings::get('GLOBAL_SETTINGS_USER') . '</div>'));
        $form->addElement(new Element_Radio(RM_UI_Strings::get('LABEL_ACC_ACT_METHOD'), "user_auto_approval", $acc_activation_methods, array("disabled"=>"disabled","value" =>$data['user_auto_approval'],'onchange'=>'show_verification_options(this)', "longDesc"=>RM_UI_Strings::get('HELP_ACC_ACT_METHOD').RM_UI_Strings::get('MSG_BUY_PRO_BOTH_INLINE'))));    

        //$form->addElement(new Element_Checkbox(RM_UI_Strings::get('LABEL_AUTO_PASSWORD'), "auto_generated_password", array("yes" => ''), $data['auto_generated_password'] === 'yes' ? array("id" => "id_rm_autogen_pass_cb", "value" => "yes", "onchange" => "checkbox_disable_elements(this, 'id_rm_send_pass_cb-0', 1)", "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_USER_AUTOGEN')) : array("id" => "id_rm_autogen_pass_cb", "onchange" => "checkbox_disable_elements(this, 'id_rm_send_pass_cb-0', 1)", "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_USER_AUTOGEN'))));

        $form->addElement(new Element_Checkbox(RM_UI_Strings::get('LABEL_SEND_PASS_EMAIL'), "send_password", array("yes" => ''), $options_sp));

       // $form->addElement(new Element_Checkbox(RM_UI_Strings::get('LABEL_REGISTER_APPROVAL'), "buy_pro", array("yes" => ''), array("value" => "yes", 'disabled' => 1, "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_USER_AUTOAPPROVAL') . "<br><br>" . RM_UI_Strings::get('MSG_BUY_PRO_INLINE'))));

        $form->addElement(new Element_HTMLL('&#8592; &nbsp; '.__("Cancel",'custom-registration-form-builder-with-submission-manager'), '?page=rm_options_manage', array('class' => 'cancel')));
        $form->addElement(new Element_Button(RM_UI_Strings::get('LABEL_SAVE')));

        $form->render();
        ?>
    </div>
    <?php 
    include RM_ADMIN_DIR.'views/template_rm_promo_banner_bottom.php';
    ?>
</div>
<script>
jQuery(document).ready(function(){
        jQuery( "#options_users-element-1-0" ).next('label').after( '<span class="rm-option-subtext"><?php _e("Once user successfully submits a registration form, his/her account is created and activated automatically.",'custom-registration-form-builder-with-submission-manager') ?></span>' );
        jQuery( "#options_users-element-1-1" ).next('label').after( '<span class="rm-option-subtext"><?php _e("Once user successfully submits a registration form, his/her account is created but in deactivated state. Useful for manually approving accounts via User Manager or admin notification email link. You can also activate accounts based on form properties using Automation.",'custom-registration-form-builder-with-submission-manager') ?></span>' );
        jQuery( "#options_users-element-1-2" ).next('label').after( '<span class="rm-option-subtext"><?php _e("Once user successfully submits a registration form, he/she will receive an email with account verification link. Clicking the link will activate his/her account. Admin can also manually approve unverified accounts.<br>Please note, if you are using paid registration, the user will be auto activated upon successful payment.",'custom-registration-form-builder-with-submission-manager') ?></span>' );
    });
</script>
<?php }