<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_formflow_config.php'); else {
$rdrto = urlencode("rm_field_manage&astep=config");

?>

<div class="rmagic">

    <!-----Operationsbar Starts----->
    <div class="operationsbar">
        <div class="rmtitle"><?php echo RM_UI_Strings::get("TITLE_FORMFLOW_CONFIG_PAGE"); ?></div>
        
        <div class="nav">
            <ul>  
                <!-- <li onclick="window.history.back()"><a href="javascript:void(0)"><?php // echo RM_UI_Strings::get("LABEL_BACK"); ?></a></li> -->

                <li><a class="thickbox rm_form_preview_btn" id="rm_form_preview_action_2" href="<?php echo esc_url(add_query_arg(array('form_prev' => '1','form_id' => $data->form_id),  get_permalink($data->prev_page))); ?>&TB_iframe=true&width=900&height=600"><?php _e('Preview','custom-registration-form-builder-with-submission-manager'); ?></a></li>                
                
            </ul>
        </div>
    </div>
    <!-- Operationsbar Ends -->
    <div class="rm-grid difl"> 
        <div class="rm-grid-section dbfl" id="rm-section-icons">  
            
            <div class="rm-grid-icon difl">
                <a href="?page=rm_form_sett_general&rm_form_id=<?php echo $data->form_id; ?>&rdrto=<?php echo $rdrto; ?>" class="rm_fd_link">    
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>form-settings.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('LABEL_F_GEN_SETT'); ?></div>
                </a>
            </div>
            
            <div class="rm-grid-icon difl" id="rm-accounts-icon">
                <a href="?page=rm_form_sett_accounts&rm_form_id=<?php echo $data->form_id; ?>&rdrto=<?php echo $rdrto; ?>" class="rm_fd_link">    
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>form-accounts.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('LABEL_F_ACC_SETT'); ?></div>

                </a>
            </div> 

            <div class="rm-grid-icon difl" id="rm-postsubmit-icon">
                <a href="?page=rm_form_sett_post_sub&rm_form_id=<?php echo $data->form_id; ?>&rdrto=<?php echo $rdrto; ?>" class="rm_fd_link">    
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>post-submission.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('LABEL_F_PST_SUB_SETT'); ?></div>
                </a>
            </div>
            
            <div class="rm-grid-icon difl" id="rm-autoresponder-icon">
                <a href="?page=rm_form_sett_autoresponder&rm_form_id=<?php echo $data->form_id; ?>&rdrto=<?php echo $rdrto; ?>" class="rm_fd_link">    
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>auto-responder.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('LABEL_F_AUTO_RESP_SETT'); ?></div>
                </a>
            </div>       

            <div class="rm-grid-icon difl" id="rm-limits-icon">
                <a href="?page=rm_form_sett_limits&rm_form_id=<?php echo $data->form_id; ?>&rdrto=<?php echo $rdrto; ?>" class="rm_fd_link">    
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>form-limits.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('LABEL_F_LIM_SETT'); ?></div>
                </a>
            </div>
            
            <div class="rm-grid-icon difl" id="rm-access-icon"> 
                <a href="?page=rm_form_sett_access_control&rm_form_id=<?php echo $data->form_id; ?>&rdrto=<?php echo $rdrto; ?>" class="rm_fd_link">   
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>form-access.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('LABEL_F_ACTRL_SETT'); ?></div>
                </a>
            </div>
            
            <div class="rm-grid-icon difl" id="rm-emtemplates-icon">
                <a href="?page=rm_form_sett_email_templates&rm_form_id=<?php echo $data->form_id; ?>&rdrto=<?php echo $rdrto; ?>" class="rm_fd_link">    
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>email_templates.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('LABEL_F_EMAIL_TEMPLATES_SETT'); ?></div>
                </a>
            </div>

            <div class="rm-grid-icon difl" id="rm-overrides-icon">
                <a href="?page=rm_form_sett_override&rm_form_id=<?php echo $data->form_id; ?>&rdrto=<?php echo $rdrto; ?>" class="rm_fd_link"> 
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>form-overrides.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('LABEL_F_OVERRIDES_SETT'); ?></div>
                </a>
            </div>
        </div>
        <div class="rm-grid-section dbfl" id="rm-section-icons"> 
            <div class="rm-grid-section-title dbfl" id="rm-thirdparty-section">
                <?php echo RM_UI_Strings::get('FD_ADD_APPS_TO_FORM'); ?>
            </div>
            <!-- icons for external integrations -->
            <div class="rm-grid-icon difl">  
                <a href="?page=rm_form_sett_mailchimp&rm_form_id=<?php echo $data->form_id; ?>&rdrto=<?php echo $rdrto; ?>" class="rm_fd_link">  
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>mailchimp.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('LABEL_F_MC_SETT'); ?></div>

                </a>
            </div> 

            <div class="rm-grid-icon difl"> 
                <a href="?page=rm_form_sett_aweber&rm_form_id=<?php echo $data->form_id; ?>&rdrto=<?php echo $rdrto; ?>" class="rm_fd_link">   
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>logo-aweber.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('LABEL_AWEBER_OPTION'); ?></div>

                </a>
            </div> 

             
            
            <?php $rdrto = "rm_field_manage"; ?>
            <?php do_action('rm_extended_apps', $rdrto); ?>

        </div>
        
        <div class="rm-grid-section dbfl" id="rm-section-icons">           
           <!-- Next button -->
           <div class="rm-field-next-step rm_formflow_page_next_btn" data-next_page="#rm_formflow_publish">
               <a href="javascript:void(0)" class="rm-form-setting-btn"><?php _e('Next','custom-registration-form-builder-with-submission-manager'); ?> &nbsp;&nbsp;></a>
           </div>
        </div>
        <!-- End Next step -->
    </div> <!-- rm-grid wrap ends -->
</div> <!-- rmagic wrap ends -->
<?php } ?>