<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_PUBLIC_DIR . 'widgets/html/login.php'); else {
$login_service= new RM_Login_Service;
$recovery_options= $login_service->get_recovery_options();
$lostpassword_url= wp_lostpassword_url();
if(!empty($recovery_options['en_pwd_recovery'])){
    $page_id= $recovery_options['recovery_page'];
    if(!empty($page_id)){
        $lostpassword_url= get_permalink($page_id);
    }
}
?>
<div id="rm_otp_login">

    <div class="dbfl rm-magic-popup-login">

        <!--Block to display if email is not entered -->
        <div id="rm_otp_enter_email">
            <div class="rm-login-panel-user-image dbfl">
                <img class="dbfl rm-placeholder-user-image" src="<?php echo RM_IMG_URL; ?>placeholder-pic.png">
            </div>
            <div class="rm_f_notifications">
                <span class="rm_f_error"></span>
                <span class="rm_f_success"></span> 
            </div>
            <div class="dbfl rm-login-panel-fields">
                <input type="text" placeholder="<?php _e('Username or Email', 'custom-registration-form-builder-with-submission-manager'); ?>" value="" id="rm_otp_econtact" name="<?php echo wp_generate_password(5, false, false); ?>"
                       onkeypress="return rm_call_otp(event, '.rm-floating-page-login')" maxlength="50" class="difl rm-rounded-corners rm-grey-box"/>
                <input type="hidden" id="rm_username" value="">
                <button class="difl rm-accent-bg rm-button" id="rm-panel-login" onclick="rm_call_otp(event, '.rm-floating-page-login', 'submit')"><i class="material-icons">&#xE5C8;</i></button>
            </div>
        </div>
        
        <!-- Block to enter OTP Code-->
        <div id="rm_otp_enter_otp" style="display:none" class="rm_otp_after_email rm-mp-animationRight">
            <div class="rm-login-goback_img dbfl">
                <img onclick="rm_otp_go_back()" class="" src="<?php echo RM_IMG_URL; ?>left-arrow.png">
            </div>
            <div class="rm-login-panel-user-image dbfl">
                <img class="dbfl rm-placeholder-user-image" src="<?php echo RM_IMG_URL; ?>user-icon-blue.jpg">
            </div>
            <div class="rm_f_notifications">
                <span class="rm_f_error"></span>
                <span class="rm_f_success"></span> 
            </div>
            <div class="dbfl rm-login-panel-fields">

                <input type="text" value="" placeholder="<?php _e('OTP', 'custom-registration-form-builder-with-submission-manager'); ?>" maxlength="50" name="<?php echo wp_generate_password(5, false, false); ?>" class="difl rm-rounded-corners rm_otp_kcontact" onkeypress="return rm_call_otp(event, '.rm-floating-page-login')"/>

                <button class="difl rm-accent-bg rm-button" id="rm-panel-login" onclick="rm_call_otp(event, '.rm-floating-page-login', 'submit')"><i class="material-icons">&#xE5C8;</i></button>
            </div>
        </div>
        
        <!-- Block to enter User Password -->
        <div id="rm_otp_enter_password" style="display:none" class="rm_otp_after_email rm-mp-animationRight">
            <div class="rm-login-goback_img dbfl">
                <img onclick="rm_otp_go_back()" class="" src="<?php echo RM_IMG_URL; ?>left-arrow.png">
            </div>
            <div class="rm-login-panel-user-image dbfl">
                <img class="dbfl rm-placeholder-user-image" src="<?php echo RM_IMG_URL; ?>user-icon-blue.jpg">
            </div>
            <div class="rm_f_notifications">
                <span class="rm_f_error"></span>
                <span class="rm_f_success"></span> 
            </div>
            <div class="dbfl rm-login-panel-fields">

                <input type="password" value="" placeholder="<?php _e('Password', 'custom-registration-form-builder-with-submission-manager'); ?>" maxlength="50" name="<?php echo wp_generate_password(5, false, false); ?>" class="difl rm-rounded-corners rm_otp_kcontact" onkeypress="return rm_call_otp(event, '.rm-floating-page-login')"/>
                            
                <button class="difl rm-accent-bg rm-button" id="rm-panel-login" onclick="rm_call_otp(event, '.rm-floating-page-login', 'submit')"><i class="material-icons">&#xE5C8;</i></button>
                
                <div id="rm_rememberme_cb" class="dbfl"><div class="difl rm_cb"><input style="width:auto" type="checkbox" id="rm_rememberme" value="yes"><?php echo RM_UI_Strings::get('LABEL_REMEMBER'); ?></div><div class="difl rm_link"><a href="<?php echo $lostpassword_url; ?>" target="blank"><?php echo RM_UI_Strings::get('MSG_LOST_PASS'); ?></a></div></div>    
            </div>
        </div>
    </div>
    
    <input type="hidden" value="<?php echo wp_generate_password(8, false); ?>" name="security_key"/>
    
</div>
<pre class="rm-pre-wrapper-for-script-tags"><script type="text/javascript">
    function rm_otp_go_back(){
        jQuery("." + "rm-floating-page-login" + " #rm_otp_login " + "#rm_otp_enter_email").addClass("rm-mp-animationLeft").show();
        jQuery("." + "rm-floating-page-login" + " #rm_otp_login " + ".rm_otp_after_email").hide();
    }
</script></pre>

<?php } ?>