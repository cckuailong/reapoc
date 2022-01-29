<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_PUBLIC_DIR . 'widgets/html/otp.php'); else {
// Show if user is not logged in
if (!$data->rm_public->is_authorized() && !is_user_logged_in())
{

    if (!empty($data->instance['title']))
    {
        echo $data->args['before_title'] . apply_filters('widget_title', $data->instance['title']) . $data->args['after_title'];
    }
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
    <div class="rm_otp_widget_container" id="<?php echo $data->uid; ?>">
        <div id="rm_otp_login">
            <!--Block to display if email is not entered -->
            <div id="rm_otp_enter_email">
                <div class="dbfl rm_otpw_email rm_otpw_field_container">
                    <input type="text" placeholder="<?php _e('Email or User Name', 'custom-registration-form-builder-with-submission-manager'); ?>" value="" id="rm_otp_econtact" name="<?php echo wp_generate_password(5, false, false); ?>"
                           onkeypress="return rm_otpw_proceed(event, this)" maxlength="50" class="rm_otpw_input_box"/>
                    <input type="hidden" id="rm_username" value="">
                    <button class="rm_otpw_next_btn rm_hide_when_loader"  onclick="rm_otpw_proceed(event, this, 'submit')">&rarr;</button>
                    <button class="rm_otpw_next_btn rm_loader rm_otpw_rotate" style="display:none">&#8635;</button>
                </div>
            </div>

            <!-- Block to enter OTP Code-->
            <div id="rm_otp_enter_otp" style="display:none" class="rm_otp_after_email rm-otpw-animationRight">
                <span class="rm_otpw_login_back_btn">
                    &larr;&nbsp;<?php __('Back', 'custom-registration-form-builder-with-submission-manager'); ?>
                </span>
                <div class="dbfl rm_otpw_login_panel_fields rm_otpw_field_container ">

                    <input type="text" value="" placeholder="<?php _e('OTP', 'custom-registration-form-builder-with-submission-manager'); ?>" maxlength="50" name="<?php echo wp_generate_password(5, false, false); ?>" class="rm_otpw_input_box rm_otp_kcontact" onkeypress="return rm_otpw_proceed(event, this)"/>

                    <button class="rm_otpw_next_btn rm_hide_when_loader"  onclick="rm_otpw_proceed(event, this, 'submit')">&rarr;</button>
                    <button class="rm_otpw_next_btn rm_loader rm_otpw_rotate" style="display:none">&#8635;</button>
                </div>
            </div>

            <!-- Block to enter User Password -->
            <div id="rm_otp_enter_password" style="display:none" class="rm_otp_after_email rm-otpw-animationRight">
                <span class="rm_otpw_login_back_btn">
                    &larr;&nbsp;<?php __('Back', 'custom-registration-form-builder-with-submission-manager'); ?>
                </span>
                <div class="dbfl rm_otpw_login_panel_fields rm_otpw_field_container">

                    <input type="password" value="" placeholder="<?php _e('Password', 'custom-registration-form-builder-with-submission-manager'); ?>" maxlength="50" name="<?php echo wp_generate_password(5, false, false); ?>" class="rm_otpw_input_box rm_otp_kcontact" onkeypress="return rm_otpw_proceed(event, this)"/>

                    <button class="rm_otpw_next_btn rm_hide_when_loader" id="rm-panel-login" onclick="rm_otpw_proceed(event, this, 'submit')">&rarr;</button>
                    <button class="rm_otpw_next_btn rm_loader rm_otpw_rotate" style="display:none">&#8635;</button>

                 
                </div>
                
                      <div id="rm_otpw_rememberme_container" class="dbfl">
                        <div class="dbfl rm_cb">
                            <input style="width:auto" type="checkbox" id="rm_rememberme" value="yes"><?php echo RM_UI_Strings::get('LABEL_REMEMBER'); ?>
                        </div>
                        <div class="dbfl rm_link">
                            <a href="<?php echo $lostpassword_url; ?>" target="blank"><?php echo RM_UI_Strings::get('MSG_LOST_PASS'); ?></a>
                        </div>
                    </div> 
            </div>
            
            <input type="hidden" value="<?php echo wp_generate_password(8, false); ?>" name="security_key"/>
            <div class="rm_f_notifications">
                <span class="rm_f_error"></span>
                <span class="rm_f_success"></span> 
            </div>
            
        </div>        

    
</div>
    <pre class='rm-pre-wrapper-for-script-tags'><script>var ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>";</script></pre>
<?php
} else
{

    if (!empty($data->instance['title']))
    {
        echo $data->args['before_title'] . apply_filters('widget_title', $data->instance['title']) . $data->args['after_title'];
    }
    ?>
    <div id="rm_f_sub_page">
    <?php
    RM_Utilities::create_submission_page();
    ?>
        <a href="<?php echo get_permalink(get_option('rm_option_front_sub_page_id')); ?>"><?php _e('View Submissions', 'custom-registration-form-builder-with-submission-manager'); ?></a>
    </div>
<?php
} }