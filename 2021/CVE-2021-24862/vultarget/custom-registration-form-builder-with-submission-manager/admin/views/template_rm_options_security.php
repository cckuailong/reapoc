<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_options_security.php'); else {
?>
<div class="rmagic">

    <!--Dialogue Box Starts-->
    <div class="rmcontent">


        <?php
        $form = new RM_PFBC_Form("options_security");
        $form->configure(array(
            "prevent" => array("bootstrap", "jQuery"),
            "action" => ""
        ));

        $options_pb_key = array("id" => "rm_captcha_public_key", "value" => $data['public_key'], "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_ASPM_SITE_KEY'));
        $options_pr_key = array("id" => "rm_captcha_private_key", "value" => $data['private_key'], "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_ASPM_SECRET_KEY'));
        $options_pb_key1 = array("id" => "rm_captcha_public_key3", "value" => $data['public_key3'], "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_ASPM_SITE_KEY'));
        $options_pr_key1 = array("id" => "rm_captcha_private_key3", "value" => $data['private_key3'], "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_ASPM_SECRET_KEY'));


        $form->addElement(new Element_HTML('<div class="rmheader">' . RM_UI_Strings::get('LABEL_ANTI_SPAM') . '</div>'));
       $form->addElement(new Element_Checkbox(RM_UI_Strings::get('LABEL_ENABLE_CAPTCHA'), "enable_captcha", array("yes" => ''),array("id" => "id_rm_enable_captcha_cb", "class" => "id_rm_enable_captcha_cb" , "onclick" => "hide_show(this)","value"=>$data['enable_captcha'], "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_ASPM_ENABLE_CAPTCHA')) ));
        if ($data['enable_captcha'] == 'yes')
            $form->addElement(new Element_HTML('<div class="childfieldsrow" id="id_rm_enable_captcha_cb_childfieldsrow">'));
        else
            $form->addElement(new Element_HTML('<div class="childfieldsrow" id="id_rm_enable_captcha_cb_childfieldsrow" style="display:none">'));

        $form->addElement(new Element_Select(__('Version','custom-registration-form-builder-with-submission-manager'), "recaptcha_v",array('v2'=>__('reCaptcha v2','custom-registration-form-builder-with-submission-manager'),'v3'=>__('reCaptcha v3','custom-registration-form-builder-with-submission-manager')), array('id'=>"recaptcha_v","value" =>$data['recaptcha_v'], "longDesc" =>__('Select reCaptcha version you want to use with your forms.','custom-registration-form-builder-with-submission-manager'))));
        $form->addElement(new Element_HTML("<div class='childfieldsrow'>"));
            $form->addElement(new Element_HTML('<div id="recaptcha_v2">'));
                $form->addElement(new Element_Textbox(RM_UI_Strings::get('LABEL_SITE_KEY'), "public_key", $options_pb_key));
                $form->addElement(new Element_Textbox(RM_UI_Strings::get('LABEL_CAPTCHA_KEY'), "private_key", $options_pr_key));
            $form->addElement(new Element_HTML('</div>'));

            $form->addElement(new Element_HTML('<div id="recaptcha_v3">'));
                $form->addElement(new Element_Textbox(RM_UI_Strings::get('LABEL_SITE_KEY'), "public_key3", $options_pb_key1));
                $form->addElement(new Element_Textbox(RM_UI_Strings::get('LABEL_CAPTCHA_KEY'), "private_key3", $options_pr_key1));
            $form->addElement(new Element_HTML('</div>'));
        $form->addElement(new Element_HTML('</div>'));
        
        $form->addElement(new Element_HTML("</div>"));

        $form->addElement(new Element_Number(RM_UI_Strings::get('LABEL_SUB_LIMIT_ANTISPAM'), "sub_limit_antispam", array("value" => $data['sub_limit_antispam'], "step" => 1, "min" => 0, "longDesc" => RM_UI_Strings::get('LABEL_SUB_LIMIT_ANTISPAM_HELP'))));
        $form->addElement(new Element_Checkbox(RM_UI_Strings::get('LABEL_ENABLE_PW_RESTRICTIONS'), "enable_custom_pw_rests", array("yes" => ''),array("id" => "id_custom_pw_rests","value" => "","disabled"=>"disabled", "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_CUSTOM_PW_RESTS').RM_UI_Strings::get('MSG_BUY_PRO_BOTH_INLINE'))));
    
         $form->addElement(new Element_Textarea(RM_UI_Strings::get('LABEL_BAN_IP'), "rm_pro", array("value" => is_array($data['banned_ip'])?implode("\n",$data['banned_ip']):null, 'disabled' => 1, "pattern" =>"[0-9\.\?\s].*",  "title"=>  RM_UI_Strings::get('VALIDATION_ERROR_IP_ADDRESS'), "longDesc" => RM_UI_Strings::get('LABEL_BAN_IP_HELP').'<br><br>'.RM_UI_Strings::get('MSG_BUY_PRO_BOTH_INLINE'))));
        
        $form->addElement(new Element_Textarea(RM_UI_Strings::get('LABEL_BAN_EMAIL'), "rm_pro", array("value" => is_array($data['banned_email'])?implode("\n",$data['banned_email']):null, 'disabled' => 1, "longDesc" => RM_UI_Strings::get('LABEL_BAN_EMAIL_HELP').'<br><br>'.RM_UI_Strings::get('MSG_BUY_PRO_BOTH_INLINE'))));
        
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
       $= jQuery;
       $("#recaptcha_v").change(function(){
           var selected_v= $(this).val();
           if(selected_v=='v2'){
               $("#recaptcha_v2").show();
               $("#recaptcha_v3").hide();
               return;
           }
           $("#recaptcha_v2").hide();
           $("#recaptcha_v3").show();
       })
        $("#recaptcha_v").trigger('change');
    });
</script>
<?php } ?>