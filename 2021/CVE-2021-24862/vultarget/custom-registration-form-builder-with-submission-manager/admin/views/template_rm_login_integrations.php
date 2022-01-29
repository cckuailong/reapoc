<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_login_integrations.php'); else {
$type= $data->type; $options= $data->options; ?>
<div class="rmagic">

    <!--Dialogue Box Starts-->
    <div class="rmcontent">     
        <div class="rmheader"><?php echo _e('Social Integration', 'custom-registration-form-builder-with-submission-manager'); ?></div>
        <?php
        if(!RM_Utilities::is_ssl() && $type=='fb'){
            echo _e('<div class="rmrow"><div class="rmnotice">Warning: SSL not detected! You need to have SSL installed on your site to make Facebook login work properly.</div></div>', 'custom-registration-form-builder-with-submission-manager');
        }
        
        $form = new RM_PFBC_Form("login-integrations");

        $form->configure(array(
            "prevent" => array("bootstrap", "jQuery"),
            "action" => ""
        ));
        
        if($type=='fb'){
            if(RM_Utilities::is_ssl()){
                $form->addElement(new Element_Checkbox(RM_UI_Strings::get('LABEL_LOGIN_FACEBOOK_OPTION'), "enable_facebook", array("yes" => ''),array('id'=>'enable_facebook',"class" => "id_rm_enable_fb_cb" , "value" =>$options['enable_facebook'], "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_THIRDPARTY_FB_ENABLE'))));
                $form->addElement(new Element_HTML('<div class="childfieldsrow" style="display:none;" id="rm_div_enable_facebook-0">'));
                    $form->addElement(new Element_Textbox(RM_UI_Strings::get('LABEL_FACEBOOK_APP_ID'), "facebook_app_id", array("value" =>$options['facebook_app_id'], "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_THIRDPARTY_FB_APPID'))));
                $form->addElement(new Element_HTML("</div>"));
            }else{
                $form->addElement(new Element_Checkbox(RM_UI_Strings::get('LABEL_LOGIN_FACEBOOK_OPTION'), "enable_facebook", array("yes" => ''),array('id'=>'enable_facebook',"class" => "id_rm_enable_fb_cb" , "value" =>"","disabled"=>"disabled", "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_THIRDPARTY_FB_ENABLE'))));
                $form->addElement(new Element_Hidden("facebook_app_id", ""));
            }
        }
        
        $form->addElement(new Element_HTMLL('&#8592; &nbsp; '.__('Cancel','custom-registration-form-builder-with-submission-manager'), '?page=rm_login_sett_manage', array('class' => 'cancel')));
        $form->addElement(new Element_Button(RM_UI_Strings::get('LABEL_SAVE'), "submit", array("id" => "rm_submit_btn", "class" => "rm_btn", "name" => "submit")));
        $form->render();
        ?>
    </div>
</div>

<script>
    jQuery(document).ready(function(){
        jQuery("input[type=checkbox]").change(function(){ 
            var id= jQuery(this).prop('id');
            
            if(jQuery(this).is(':checked')){
                jQuery("#rm_div_" + id).slideDown();
                return;
            }
            jQuery("#rm_div_" + id).slideUp();
        });
        jQuery("input[type=checkbox]").trigger('change');
        
    });
</script>  
<?php } ?>