<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_login_analytics.php'); else {
//echo '<pre>';print_r($data->login_logs);echo '</pre>';
?>
<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="rmagic">
    <div class="operationsbar">
        <div class="rmtitle"><?php echo _e('Login Timeline', 'custom-registration-form-builder-with-submission-manager'); ?></div>
        <div class="nav">
            <ul>
                <li onclick="window.history.back()"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("LABEL_BACK"); ?></a></li>
                <li><a href="admin.php?page=rm_login_sett_manage&action=rm_login_log_reset"><?php echo RM_UI_Strings::get('LABEL_RESET_STATS'); ?></a></li>
                <li id="rm_form_toggle" class="rm-form-toggle">
                    <?php echo RM_UI_Strings::get('LABEL_TOGGLE_FORM'); ?>
                    <select id="rm_form_dropdown" onchange="rm_refresh_stats()">
                        <?php
                        echo "<option selected value='rm_login_form'>".__('Login Form','custom-registration-form-builder-with-submission-manager')."</option>";
                        foreach ($data->all_forms as $form_id => $form_name):
                            echo "<option value='$form_id'>$form_name</option>";
                        endforeach;
                        ?>
                    </select>
                </li>
            </ul>
        </div>            
    </div>
    
    <!-- Login-timeline Start -->
         
    <div class="rm-login-timeline rm-dbfl">
        
        <div  class="rm-advanced-timeline rm-dbfl"><a href="?page=rm_login_advanced"><?php _e('Advanced View', 'custom-registration-form-builder-with-submission-manager'); ?></a></div>
        <table class="rm-login-analytics">
            <tbody>
                <?php
                if(isset($data->login_logs)){
                    if (!empty($data->login_logs) && (is_array($data->login_logs) || is_object($data->login_logs))){
                        $gopt=new RM_Options;
                        $blocked_ips=array();
                        $blocked_ips=$gopt->get_value_of('banned_ip');
                        
                        foreach ($data->login_logs as $login_log){
                            ?>
                            <tr class="rm-login-result <?php echo ($login_log->status==1)?'rm-login-success':'rm-login-failed'; ?>">
                                <td><div class="rm-login-user-time-log"><?php echo date('j M Y, h:i a', strtotime($login_log->time)); ?></div></td>
                                <td>
                                    <div class="rm-login-form-user">
                                        <a href="#">
                                            <?php echo get_avatar($login_log->email)?get_avatar($login_log->email):'<img src="'.RM_IMG_URL.'default_person.png">'; ?>
                                        </a>
                                        <?php $user = get_user_by( 'email', $login_log->email ); ?>
                                        <?php if(!empty($user)): ?>
                                            <span class="rm-login-user-status <?php echo (RM_Utilities::is_user_online($user->ID))?'rm-login-user-online':'' ?>"><i class="fa fa-circle"></i></span>
                                        <?php else: ?>
                                            <span class="rm-login-user-status"><i class="fa fa-circle"></i></span>
                                        <?php endif; ?>
                                            <span class="rm-login-form-user-name" title="<?php echo ($user)?$user->display_name:($login_log->social_type=='instagram'?$login_log->username_used:$login_log->email); ?>"><?php echo ($user)?$user->display_name:($login_log->social_type=='instagram'?$login_log->username_used:$login_log->email); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="rm-login-user-browser">
                                        <img src="<?php echo RM_IMG_URL. str_replace(' ', '_', strtolower($login_log->browser)).'.png'; ?>">
                                    </div>
                                </td>
                                <td><div class="rm-login-user-ip <?php echo ($login_log->ban==1)?'rm-login-boolean-result rm-login-false':''; ?>" <?php echo ($login_log->ban==1)?'style="color:#c21106;"':''; ?>><?php echo $login_log->ip == '::1' ? 'localhost' : $login_log->ip ?><?php echo ($login_log->ban==1)?'<i class="fa fa-times"></i>':''; ?></div></td>
                                <td><div class="rm-login-method rm-login-<?php echo strtolower($login_log->type) ?>"><?php echo $login_log->type ?></div></td>
                                <?php
                                if($login_log->status==1){
                                    $login_icon = '<i class="fa fa-unlock-alt"></i>';
                                    if(strtolower($login_log->type)=='otp'){
                                        $login_icon = '<i class="fa fa-unlock-alt"></i>';
                                    }else if(strtolower($login_log->type)=='2fa' || strtolower($login_log->type)=='fa'){
                                        $login_icon = '<i class="fa fa-unlock-alt"></i><i class="fa fa-unlock-alt"></i>';
                                    }else if(strtolower($login_log->type)=='social'){
                                        $login_icon = '<i class="fa fa-'.$login_log->social_type.'"></i>';
                                    }
                                }else{
                                    $login_icon = '<i class="fa fa-lock"></i>';
                                    if(strtolower($login_log->type)=='otp'){
                                        $login_icon = '<i class="fa fa-lock"></i>';
                                    }else if(strtolower($login_log->type)=='2fa' || strtolower($login_log->type)=='fa'){
                                        $login_icon = '<i class="fa fa-lock"></i><i class="fa fa-lock"></i>';
                                    }else if(strtolower($login_log->type)=='social'){
                                        $login_icon = '<i class="fa fa-'.$login_log->social_type.'"></i>';
                                    }
                                }
                                ?>
                                <td> <div class="rm-login-result-icon"><?php echo $login_icon; ?></div></td>
                                <td><div class="rm-login-boolean-result <?php echo ($login_log->status==1)?'rm-login-true':'rm-login-false'; ?>"><i class="fa fa-<?php echo ($login_log->status==1)?'check':'times'; ?>"></i></div></td>
                                <td> <div class="rm-login-user-sep"><i class="fa fa-circle"></i></div></td>
                                <td class="rm-login-setting"> 
                                    <div class="rm-login-setting-dropdown" onclick="pg_toggle_dropdown_menu(this)">
                                        <div class="rm-login-dropdown-icon"> 
                                            <i class="fa fa-cog" aria-hidden="true"></i> 
                                        </div>

                                        <ul class="rm-login-dropdown-menu" style="display: none;">
                                            <?php if($user): ?>
                                            <?php 
                                            $user_status = get_user_meta($user->ID,'rm_user_status',TRUE);
                                            ?>
                                            <li class="rm_deactive_link_<?php echo $user->ID; ?>" <?php echo ($user_status)?'style="display: none;"':'' ?>><a onclick="rm_login_suspend_user('<?php echo $user->ID; ?>','<?php echo $user->user_login; ?>')"><?php _e('Suspend User', 'custom-registration-form-builder-with-submission-manager'); ?></a></li>
                                            <li class="rm_active_link_<?php echo $user->ID; ?>" <?php echo ($user_status)?'':'style="display: none;"' ?>><a onclick="rm_login_activate_user('<?php echo $user->ID; ?>','<?php echo $user->user_login; ?>')"><?php _e('Activate User', 'custom-registration-form-builder-with-submission-manager'); ?></a></li>
                                            <li><a onclick="rm_login_reset_password('<?php echo $user->ID; ?>','<?php echo $login_log->email; ?>','<?php echo $user->user_login; ?>')"><?php _e('Reset Password', 'custom-registration-form-builder-with-submission-manager'); ?></a></li>
                                            <?php endif; ?>
                                            
                                            <?php
                                            $ip_as_arr = explode('.', $login_log->ip);
                                            if(count($ip_as_arr)!=4){
                                                $sanitized_user_ip = $login_log->ip;
                                            }else{
                                                $sanitized_user_ip = sprintf("%'03s.%'03s.%'03s.%'03s", $ip_as_arr[0], $ip_as_arr[1], $ip_as_arr[2], $ip_as_arr[3]);
                                            }
                                            
                                            ?>
                                            <li><a href="#rm-send-email-<?php echo $login_log->id; ?>" onclick="CallModalBoxEmail(this,'<?php echo $login_log->email; ?>',<?php echo $login_log->id; ?>)"><?php _e('Send Email', 'custom-registration-form-builder-with-submission-manager'); ?></a></li>
                                            <li><a href="#rm-login-user-details-<?php echo $login_log->id; ?>" onclick="CallModalBox(this)"><?php _e('Details', 'custom-registration-form-builder-with-submission-manager'); ?></a></li>
                                        </ul>
                                    </div>

                                </td>

                            </tr>
                            
                            <div id="rm-login-user-details-<?php echo $login_log->id; ?>" class="rm-login-user-details" style="display:none">
                                <div class="rm-modal-overlay"></div>
                                <div class="rm-modal-wrap ">
                                    <div  class="rm-modal-close">&times;</div>

                                    <div class="rm-modal-container">
                                        <div class="rm-login-user-details-dialog">
                                            <div class="rm-login-details-lf">
                                                <div class="rm-login-user-profile">
                                                    <div class="rm-login-user-profile-avatar">
                                                        <a href="#">
                                                            <?php echo get_avatar($login_log->email)?get_avatar($login_log->email):'<img src="'.RM_IMG_URL.'default_person.png">'; ?>
                                                        </a>
                                                    </div> 

                                                    <div class="rm-login-user-name"><?php echo ($user)?$user->display_name:$login_log->email; ?></div>
                                                    
                                                    <?php if($user): ?>
                                                    <div class="rm-register-details">
                                                        <?php _e('Registered User Since:', 'custom-registration-form-builder-with-submission-manager'); ?>
                                                        <span class="rm-dbfl rm-registered-date"> <?php echo date('j M Y', strtotime($user->user_registered)); ?> </span>
                                                    </div>
                                                    <div class="rm-view-register-user"><a href="?page=rm_user_view&user_id=<?php echo $user->ID; ?>"><?php _e('View', 'custom-registration-form-builder-with-submission-manager'); ?></a></div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="rm-login-details-rf">
                                                <div class="rm-login-user-info-wrap">
                                                <div class="rm-login-user-info"><?php _e('Username Used', 'custom-registration-form-builder-with-submission-manager'); ?><span><?php echo ($login_log->username_used!='')?$login_log->username_used:$login_log->email; ?></span></div>
                                                <div class="rm-login-user-info"><?php _e('Login Attempted', 'custom-registration-form-builder-with-submission-manager'); ?><span><?php echo date('h:i A j F Y', strtotime($login_log->time)); ?></span></div>
                                                <div class="rm-login-user-info"><?php _e('Login From', 'custom-registration-form-builder-with-submission-manager'); ?><span><?php echo $login_log->login_url; ?></span></div>
                                                <?php
                                                if($login_log->type=='2fa'){
                                                    $login_type = '2FA';
                                                }else if($login_log->type=='otp'){
                                                    $login_type = 'OTP';
                                                }else{
                                                    $login_type = ucfirst($login_log->type);
                                                }
                                                ?>
                                                <div class="rm-login-user-info"><?php _e('Login Method', 'custom-registration-form-builder-with-submission-manager'); ?><span><?php echo $login_type ?></span></div>
                                                <?php
                                                if($login_log->status==1){
                                                    $login_result = __('Success','custom-registration-form-builder-with-submission-manager');
                                                }else if($login_log->failure_reason=='incorrect_reCAPCTCHA'){
                                                    $login_result = __('Incorrect reCAPCTHA','custom-registration-form-builder-with-submission-manager');
                                                }else if($login_log->failure_reason=='incorrect_otp'){
                                                    $login_result = __('Incorrect OTP','custom-registration-form-builder-with-submission-manager');
                                                }else if($login_log->failure_reason=='expired_otp'){
                                                    $login_result = __('Expired OTP','custom-registration-form-builder-with-submission-manager');
                                                }else{
                                                    $login_result = ucwords(str_replace('_', ' ', $login_log->failure_reason));
                                                }
                                                ?>
                                                <div class="rm-login-user-info"><?php _e('Login Result', 'custom-registration-form-builder-with-submission-manager'); ?><span><?php echo $login_result; ?></span></div>
                                                <div class="rm-login-user-info"><?php _e('Final Result', 'custom-registration-form-builder-with-submission-manager'); ?><span><?php echo ($login_log->status==1)?'Login Successful':'Login Denied' ?></span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>            
            
                            <div id="rm-send-email-<?php echo $login_log->id; ?>" class="rm-login-user-details rm-send-email" style="display:none">
                                <div class="rm-modal-overlay"></div>
                                <div class="rm-modal-wrap">
                                    <div class="rm-modal-title rm-dbfl"><?php _e("Send Email", 'custom-registration-form-builder-with-submission-manager'); ?><div class="rm-modal-close">&times;</div></div>
                                    
                                    <div class="rm-modal-container rm-dbfl">
                                        <div class="rm-dbfl rm-email-box-row"><span class="rm-field-lable rm-difl"><?php _e("To", 'custom-registration-form-builder-with-submission-manager'); ?></span><span class="rm-field-input rm-difl" ><span class="rm-message-username"><?php _e('@', 'custom-registration-form-builder-with-submission-manager'); ?><span><?php echo $login_log->email; ?></span></span></span></div>
                                        <div class="rm-dbfl rm-email-box-row"><span class="rm-field-lable rm-difl"><?php _e("Subject", 'custom-registration-form-builder-with-submission-manager'); ?>:</span><span class="rm-field-input rm-difl"><input name="rm-user-subject" value="RM LOGIN EMAIL" class="rm-user-subject-<?php echo $login_log->id; ?>" placeholder="<?php _e('Type your subject here','custom-registration-form-builder-with-submission-manager') ?>"/></span></div>
                                        <div class="rm-dbfl rm-email-box-row"><span class="rm-field-lable rm-difl"><?php _e("Message", 'custom-registration-form-builder-with-submission-manager'); ?>:</span><span class="rm-field-input rm-difl"><textarea name="rm-user-message" class="rm-user-message-<?php echo $login_log->id; ?>" placeholder="<?php _e('Type your message here','custom-registration-form-builder-with-submission-manager') ?>"></textarea></span></div>
                                    </div>

                                    <div class="rm-send-email-footer rm-dbfl">
                                        <div class="rm-send-email- rm-difl"><a href="javascript:void(0)" onclick="rm_login_send_email('<?php echo $login_log->email; ?>',<?php echo $login_log->id; ?>)"><?php _e("Send", 'custom-registration-form-builder-with-submission-manager'); ?></a></div>
                                        <div class="rm-difl"><a href="javascript:void(0)" class="rm-model-cancel"><?php _e("Cancel", 'custom-registration-form-builder-with-submission-manager'); ?></a></div>
                                    </div>
                                </div>
                            </div>
                            
                            <?php
                        }
                    }else{
                        echo '<div class="rmnotice">'.sprintf(__('Not enough data. Come back later to check login activity. <a target="_blank" href="%s">More Info</a>', 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/wordpress-user-login-plugin-guide/#login-analytics').'</div>';
                    }
                }
                ?>
            </tbody>
        </table>
        
        <div id="rm_status_update_model" class="rm-send-email">
     <div class="rm-notification-overlay"></div>
            <div class="rm-modal-wrap-toast">
             

                <div class="rm-modal-container rm-dbfl">
                    <div class="rm-dbfl rm-email-box-row rm_status_update_body"></div>
                </div>
            </div>
        </div>
        <div id="rm_status_failed_model" class="rm-send-email" style="display:none">
       <div class="rm-notification-overlay"></div>
            <div class="rm-modal-wrap-toast">
           

                <div class="rm-modal-container rm-dbfl">
                    <div class="rm-dbfl rm-email-box-row"><?php _e('Unable to process your request. Please try again.', 'custom-registration-form-builder-with-submission-manager'); ?></div>
                </div>
            </div>
        </div>
        
        <?php
        /* * ********** Pagination Logic ************** */
        $max_pages_without_abb = 10;
        $max_visible_pages_near_current_page = 3; //This many pages will be shown on both sides of current page number.

        if ($data->total_pages > 1):
            ?>
            <ul class="rmpagination">
                <?php
                if ($data->curr_page > 1):
                    ?>
                    <li><a href="?page=<?php echo $data->rm_slug ?>&rm_reqpage=1"><?php echo RM_UI_Strings::get('LABEL_FIRST'); ?></a></li>
                    <li><a href="?page=<?php echo $data->rm_slug ?>&rm_reqpage=<?php echo $data->curr_page - 1; ?>"><?php echo RM_UI_Strings::get('LABEL_PREVIOUS'); ?></a></li>
                    <?php
                endif;
                if ($data->total_pages > $max_pages_without_abb):
                    if ($data->curr_page > $max_visible_pages_near_current_page + 1):
                        ?>
                        <li><a> ... </a></li>
                        <?php
                        $first_visible_page = $data->curr_page - $max_visible_pages_near_current_page;
                    else:
                        $first_visible_page = 1;
                    endif;

                    if ($data->curr_page < $data->total_pages - $max_visible_pages_near_current_page):
                        $last_visible_page = $data->curr_page + $max_visible_pages_near_current_page;
                    else:
                        $last_visible_page = $data->total_pages;
                    endif;
                else:
                    $first_visible_page = 1;
                    $last_visible_page = $data->total_pages;
                endif;
                for ($i = $first_visible_page; $i <= $last_visible_page; $i++):
                    if ($i != $data->curr_page):
                        ?>
                        <li><a href="?page=<?php echo $data->rm_slug ?>&rm_reqpage=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                    <?php else:
                        ?>
                        <li><a class="active" href="?page=<?php echo $data->rm_slug ?>&rm_reqpage=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                    <?php
                    endif;
                endfor;
                if ($data->total_pages > $max_pages_without_abb):
                    if ($data->curr_page < $data->total_pages - $max_visible_pages_near_current_page):
                        ?>
                        <li><a> ... </a></li>
                        <?php
                    endif;
                endif;
                ?>
                <?php
                if ($data->curr_page < $data->total_pages):
                    ?>
                    <li><a href="?page=<?php echo $data->rm_slug ?>&rm_reqpage=<?php echo $data->curr_page + 1; ?>"><?php echo RM_UI_Strings::get('LABEL_NEXT'); ?></a></li>
                    <li><a href="?page=<?php echo $data->rm_slug ?>&rm_reqpage=<?php echo $data->total_pages; ?>"><?php echo RM_UI_Strings::get('LABEL_LAST'); ?></a></li>
                    <?php
                endif;
                ?>
            </ul>
        <?php endif; ?>



    </div>
    <!-- Login-timeline End -->
    
    
    
    <link rel="stylesheet" type="text/css" href="<?php echo RM_BASE_URL . 'admin/css/'; ?>style_rm_form_dashboard.css">
    <?php if(defined('REGMAGIC_ADDON')) { ?>
    <link rel="stylesheet" type="text/css" href="<?php echo RM_ADDON_BASE_URL . 'admin/css/'; ?>style_rm_form_dashboard.css">
    <?php } ?>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <pre class="rm-pre-wrapper-for-script-tags"><script src="<?php echo RM_BASE_URL . 'admin/js/'; ?>script_rm_form_dashboard.js"></script></pre>
    <pre class='rm-pre-wrapper-for-script-tags'><script>
        //Takes value of various status variables (form_id, timeline_range) and reloads page with those parameteres updated.
        function rm_refresh_stats_graph(){
        var trange = jQuery('#rm_stat_timerange').val();
        if(typeof trange == 'undefined')
            trange = <?php echo $data->timerange; ?>;
        window.location = '?page=rm_login_analytics&rm_tr='+trange;
    }
    </script></pre>
    <div class="rm-grid-section dbfl" id="rm_tour_timewise_stats">
        <div class="rm-grid-section-title dbfl rm-box-title"><?php _e('Login Failures vs. Login Success Over Time (Line Graph)', 'custom-registration-form-builder-with-submission-manager'); ?></div>
        <div class="rm-timerange-toggle rm-fd-form-toggle rm-timerange-dashboard">
            <?php echo RM_UI_Strings::get('LABEL_SELECT_TIMERANGE'); ?>
            <select id="rm_stat_timerange" onchange="rm_refresh_stats_graph()">
                <?php
                $trs = array(7,30,60,90);
                foreach($trs as $tr){
                    echo "<option value=$tr";
                    if($data->timerange == $tr)
                        echo " selected";
                    printf(">".RM_UI_Strings::get("STAT_TIME_RANGES")."</option>",$tr);
                }
                ?>
            </select>
        </div>
        <div class="rm-box-graph" id="rm_subs_over_time_chart_div">
        </div>
    </div>
</div>









<script>

function pg_toggle_dropdown_menu(a)
{
    jQuery(a).find('.rm-login-dropdown-menu').slideToggle('fast');
    jQuery('.rm-login-setting-dropdown').not(a).children(".rm-login-dropdown-menu").slideUp('fast');
    
}

function CallModalBoxEmail(ele) {
    jQuery(jQuery(ele).attr('href')).toggle();
}

function CallModalBox(ele) {
    jQuery(jQuery(ele).attr('href')).toggle();
}
jQuery(document).ready(function () {
    jQuery('.rm-modal-close, .rm-modal-overlay, .rm-model-cancel').click(function () {
        jQuery(this).parents('.rm-login-user-details, .rm-send-email').hide();
    });
});

function rm_login_suspend_user(user_id,username){
    var data = {
        'action': 'rm_deactivate_rm_user',
        'user_id': user_id
    };

    jQuery.post(ajaxurl, data, function(response) {
        if(response=='success'){
            jQuery('.rm_deactive_link_'+user_id).hide();
            jQuery('.rm_active_link_'+user_id).show();
            
            jQuery('.rm_status_update_body').html('<?php _e("User account for ",'custom-registration-form-builder-with-submission-manager') ?>'+username+' <?php _e("suspended successfully.",'custom-registration-form-builder-with-submission-manager') ?>');
            jQuery('#rm_status_update_model').addClass('rm-modal-show');
             setTimeout(function(){
             jQuery('#rm_status_update_model').removeClass('rm-modal-show');
            }, 3000);
        }else{
            jQuery('#rm_status_failed_model').addClass('rm-modal-show');
        }
    });
}

function rm_login_activate_user(user_id,username){
    var data = {
        'action': 'rm_activate_rm_user',
        'user_id': user_id
    };

    jQuery.post(ajaxurl, data, function(response) {
        if(response=='success'){
            jQuery('.rm_active_link_'+user_id).hide();
            jQuery('.rm_deactive_link_'+user_id).show();
            
            jQuery('.rm_status_update_body').html('<?php _e("User account for ",'custom-registration-form-builder-with-submission-manager') ?>'+username+' <?php _e("activated successfully.",'custom-registration-form-builder-with-submission-manager') ?>');
            jQuery('#rm_status_update_model').addClass('rm-modal-show');
             setTimeout(function(){
             jQuery('#rm_status_update_model').removeClass('rm-modal-show');
            }, 3000);
            
        }else{
            jQuery('#rm_status_failed_model').addClass('rm-modal-show');
       
           
        }
    });
}

function rm_login_reset_password(user_id,user_email,username){
    var data = {
        'action': 'rm_reset_password',
        'user_id': user_id,
        'user_email': user_email
    };

    jQuery.post(ajaxurl, data, function(response) {
        if(response=='success'){
            jQuery('.rm_status_update_body').html('<?php _e("A new password was sent to ",'custom-registration-form-builder-with-submission-manager') ?>'+username+' <?php _e(" successfully.",'custom-registration-form-builder-with-submission-manager') ?>');
             jQuery('#rm_status_update_model').addClass('rm-modal-show');
             setTimeout(function(){
             jQuery('#rm_status_update_model').removeClass('rm-modal-show');
            }, 3000);
            
        }else{
            jQuery('#rm_status_failed_model').addClass('rm-modal-show');
        }
    });
}

function rm_login_send_email(user_email,log_id){
    var user_message = jQuery('.rm-user-message-'+log_id).val();
    var user_subject = jQuery('.rm-user-subject-'+log_id).val();
    
    var data = {
        'action': 'rm_send_email',
        'user_email': user_email,
        'user_message': user_message,
        'user_subject': user_subject
    };
    
    jQuery.post(ajaxurl, data, function(response) {
        jQuery('#rm-send-email-'+log_id).toggle();
        if(response=='success'){
            jQuery('.rm_status_update_body').html("<?php _e('Mail sent successfully.','custom-registration-form-builder-with-submission-manager') ?>")
            jQuery('#rm_status_update_model').addClass('rm-modal-show');
            setTimeout(function(){
                  jQuery('#rm_status_update_model').removeClass('rm-modal-show');
                  location.reload(true);
            }, 2000);
            
        }else{
            jQuery('#rm_status_failed_model').addClass('rm-modal-show');
        }
    });
}

</script>

<?php
/* * ****************************************************************
 * ******   *******     Chart drawing - Line Chart        **************
 * **************************************************************** */
$data_string = '';
foreach ($data->day_wise_stat as $date => $per_day) {
    $formatted_name = $date;
    $data_string .= ", ['$formatted_name', " . $per_day->success . ", $per_day->fail]";
}
$data_string = substr($data_string, 2);
?>

<pre class='rm-pre-wrapper-for-script-tags'><script>
    function drawTimewiseStat()
    {
        var data = google.visualization.arrayToDataTable([
            ['<?php echo RM_UI_Strings::get('LABEL_DATE'); ?>',
             'Login Success',
             'Login Failures'],
            <?php echo $data_string; ?>
        ]);

        var options = {
            chartArea: {width: '90%'},
            height: 500,
            fontName: 'Titillium Web',
            hAxis: {
                title: '',
                minValue: 0,
                slantedText: false,
                maxAlternation: 1,
                maxTextLines: 1
            },
            vAxis: {
                title: '',
                viewWindow: {min: 0},
                minValue: 4,
            },
            legend: {position: 'top', maxLines: 3},
            colors: ['#485566', '#00A9DE'],
            
        };
        
        var chart = new google.visualization.LineChart(document.getElementById('rm_subs_over_time_chart_div'));
        chart.draw(data, options);
    }
    
    function rm_refresh_stats(){
        var form_id = jQuery('#rm_form_dropdown').val();
        var trange = jQuery('#rm_stat_timerange').val();
        if(typeof trange == 'undefined')
            trange = <?php echo $data->timerange; ?>;
        window.location = '?page=rm_analytics_show_form&rm_form_id=' + form_id + '&rm_tr='+trange;
    }
</script></pre>
<?php } ?>