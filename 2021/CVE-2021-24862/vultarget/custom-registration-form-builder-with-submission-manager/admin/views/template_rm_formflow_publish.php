<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_formflow_publish.php'); else {
$settings = new RM_Options;

?>

<link rel="stylesheet" type="text/css" href="<?php echo RM_BASE_URL . 'admin/css/'; ?>style_rm_formflow.css">
<?php if(defined('REGMAGIC_ADDON')) { ?>
<link rel="stylesheet" type="text/css" href="<?php echo RM_ADDON_BASE_URL . 'admin/css/'; ?>style_rm_formflow.css">
<?php } ?>
<script type="text/javascript" src="<?php echo RM_BASE_URL . 'admin/js/'; ?>script_rm_formflow.js"></script>

    <div  class="rm-grid rm-dbfl">

        <div class="rm-grid-section rm-dbfl rm_publish_section" id="rm_publish_shortcode"> 
            
            <div class="rm-directory-container dbfl">
                <div class="rm-publish-directory-col rm-difl">
                    <div class="rm-section-publish-note"><?php _e('Publish inside a page or post','custom-registration-form-builder-with-submission-manager'); ?></div>
                    <div class="rm-publish-shortcode-row">
                        <div class="rm-publish-head"><?php _e('<b>For Classic Editor</b>','custom-registration-form-builder-with-submission-manager'); ?></div>
                        <div class="rm-publish-text"><?php _e('Paste this code snippet inside your content where you wish to publish this form.','custom-registration-form-builder-with-submission-manager'); ?></div>
                    </div>
                    <div class="rm-publish-shortcode-row">
                        <div class="rm-publish-head"><?php _e('<b>For Block Editor</b>','custom-registration-form-builder-with-submission-manager'); ?></div>
                        <div class="rm-publish-text"><?php _e('Create a new Shortcode Block and paste this code snippet inside the block\'s shortcode field.','custom-registration-form-builder-with-submission-manager'); ?></div>
                    </div>
                    <div class="rm-section-shortcode">
                     <span id="rmformshortcode" data-publish_code="[RM_Form id='%fid%']"><?php echo "[RM_Form id='{$form_id_to_publish}']"; ?></span>            
                     <div class="rm-click-to-copy-button" onclick="rm_copy_content(document.getElementById('rmformshortcode'), this)"><?php _e('Copy','custom-registration-form-builder-with-submission-manager'); ?></div>
                    </div>
                </div> 
                  <div class="rm-publish-directory-col rm-difl"><img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . "images/wp-block.png"; ?>"></div>
            </div>
        </div>
        
        <div class="rm-grid-section rm-dbfl rm_publish_section" id="rm_publish_widget">
            <div class="rm-section-publish-note">
                <span id="rmformshortcode"><?php _e('Publish inside a Page Builder or Widget area','custom-registration-form-builder-with-submission-manager'); ?></span>
                
            </div>
            <div class="rm-publish-text"><?php _e('Find RegistrationMagic Form widget in your <b>Appearance/Widgets</b> section. Drag it to the widget area where you wish to display the form. Select the form to display in Widget settings and save.','custom-registration-form-builder-with-submission-manager'); ?></div>
            <div class="rm-section-youtube-video"><img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . "images/rm-widget.png"; ?>"></div>
        
        </div> 
        <div class="rm-grid-section rm-dbfl rm_publish_section" id="rm_publish_embed">  

            <div class="rm-section-publish-note"> <?php _e('Publish using embed code','custom-registration-form-builder-with-submission-manager'); ?> </div>
                  
        
                    <span id="rmformembedcode" class="rm-premium-feature"><?php echo RM_UI_Strings::get("MSG_BUY_PRO_INLINE"); ?></span>
               
        

        </div> 
        <div class="rm-grid-section rm-dbfl rm_publish_section" id="rm_publish_userdir">

            <div class="rm-section-publish-note"> <?php _e('Publish a Directory of users who submitted this form','custom-registration-form-builder-with-submission-manager'); ?></div>
            <div class="rm-publish-text"><?php _e('Displays a directory of all the users who have submitted this form. Only users with WordPress account will be displayed.','custom-registration-form-builder-with-submission-manager'); ?></div>

            <div class="rm-directory-container rm-dbfl">
                <div class="rm-publish-directory-col rm-difl"><img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . "images/rm-user-directory.png"; ?>"></div>
                <div class="rm-publish-directory-col rm-difl">  
                    <div class="rm-section-shortcode"> 
                        <span id="rmformuserdircode" data-publish_code="[RM_Users form_id='%fid%']"><?php echo "[RM_Users form_id='{$form_id_to_publish}']"; ?></span>
                        <div class="rm-premium-feature"><?php echo RM_UI_Strings::get("MSG_BUY_PRO_INLINE"); ?></div>                       
                    </div>                    
                </div>
            </div>
        </div>  
        <div class="rm-grid-section rm-dbfl rm_publish_section" id="rm_publish_subs">
            <div class="rm-directory-container rm-dbfl">

                <div class="rm-publish-directory-col rm-difl">  
                    <div class="rm-section-publish-note"><?php _e('Create a user area on your site','custom-registration-form-builder-with-submission-manager'); ?></div>
                    <div class="rm-publish-text"><?php _e('This shortcode renders a comprehensive user specific area on your site.','custom-registration-form-builder-with-submission-manager'); ?></div>
                    <div class="rm-section-shortcode"> 
                        <span id="rmsubmissionscode"><?php echo "[RM_Front_Submissions]"; ?></span>
                        <div class="rm-click-to-copy-button" onclick="rm_copy_content(document.getElementById('rmsubmissionscode'), this)"><?php _e('Copy','custom-registration-form-builder-with-submission-manager'); ?></div>
                    </div>
                </div>
                <div class="rm-publish-directory-col rm-difl"><img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . "images/rm-submissions.gif"; ?>"></div>
                


            </div>
        </div>
        <div class="rm-grid-section rm-dbfl rm_publish_section" id="rm_publish_magicpopup">
            <div class="rm-directory-container rm-dbfl">
                <div class="rm-publish-directory-col rm-difl"><img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . "images/rm-magic-popup.png"; ?>"></div>
                <div class="rm-publish-directory-col rm-difl">  
                    <div class="rm-section-publish-note"><?php _e('Display the form in a sliding popup','custom-registration-form-builder-with-submission-manager'); ?></div>
                    <div class="rm-publish-text"><?php _e('Click the star to activate the form in Magic Popup. To remove the form, select another form.','custom-registration-form-builder-with-submission-manager'); ?></div>
                    <div class="rm-section-shortcode rm-formflow-embedcode"> 
                        <span id="rmdefformstar">
                            <i class="material-icons rm_not_def_form_star rm_form_star" onclick="rm_formflow_set_def_form(this)" id="rm-star_<?php echo $form_id_to_publish; ?>" data-def_form_id="<?php echo empty($data->def_form_id) ? '' : $data->def_form_id;?>">&#xe838</i>
                                <span>
                                   <?php _e(' Click above star to set this form as default registration form. The default registration form appears in the Magic Popup on front-end.','custom-registration-form-builder-with-submission-manager'); ?>
                                </span> 
                        </span>                        
                    </div>

                </div>
                <?php if($settings->get_value_of('display_floating_action_btn') !== 'yes'): ?>
                <div class="rm-magic-popup-notice rm-dbfl"> <?php printf(__('Magic pop-up is currently disabled. Please go to <a href="%s" target="_blank">Global Settings >> Magic Popup Button</a> to enable it.','custom-registration-form-builder-with-submission-manager'),'?page=rm_options_fab'); ?> </div>
                      <?php endif; ?>
            </div>
        </div>
        <div class="rm-grid-section rm-dbfl rm_publish_section" id="rm_publish_landingpage">
            <?php do_action("rm_formflow_publish_page", $form_id_to_publish); ?>
        </div>
        
        <div class="rm-grid-section dbfl rm_publish_section" id="rm_publish_otp">
            <div class="rm-directory-container dbfl">
                <?php _e("When you use forms which do not create WordPress user accounts, like a contact or an enquiry form, users still have option to login on your site's frontend and check their submissions. RegistrationMagic handles it using an ingenious OTP (One Time Password) system. When logging in, RegistrationMagic checks if the email address entered was used in a form submission in past. If it was, and there's no user account for the user, it will create and send a provisional password to user's email address. This password can only be used once and allows normal access to RegistrationMagic's user account area.", 'custom-registration-form-builder-with-submission-manager'); ?><br/><br/>
                <?php printf(__('OTP works seamlessly through Login Widget in <a href="%s" target="_blank">Appearance --> Widgets</a> and Login link in Magic PopUp Menu, which can be turned on by going to <a href="%s">Global Settings --> Magic Popup Button</a>', 'custom-registration-form-builder-with-submission-manager'),admin_url("widgets.php"),'?page=rm_options_fab'); ?>         

            </div>
        </div>
        
        <div class="rm-grid-section rm-dbfl rm_publish_section" id="rm_publish_login">
            <?php if($_GET['page']!='rm_login_sett_manage') : ?>
                <div class="rmrow">
                    <div class="rmnotice">
                        You can set properties of Login Form from its <a target="_blank" href="<?php echo admin_url('admin.php?page=rm_login_sett_manage'); ?>">settings</a> area.            
                    </div>
                </div>
            <?php endif; ?>
            <div class="rm-section-publish-note"> <?php _e("Publish a login form",'custom-registration-form-builder-with-submission-manager'); ?> </div>
            <div class="rm-publish-text"><?php _e("Login system is built into RegistrationMagic. To display a login box on any page, post or widget use this code.",'custom-registration-form-builder-with-submission-manager'); ?></div>
                        
            <div class="rm-section-shortcode">
                <span id="rmloginformshortcode"><?php echo "[RM_Login]"; ?></span>            
                <div class="rm-click-to-copy-button" onclick="rm_copy_content(document.getElementById('rmloginformshortcode'), this)"><?php _e("Copy",'custom-registration-form-builder-with-submission-manager'); ?></div>
            </div>
        </div>
        
        <div class="rm-grid-section rm-dbfl rm_publish_section" id="rm_publish_loginbtn">
            <div class="rm-directory-container rm-dbfl">
                <div class="rm-publish-directory-col rm-difl"><img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . "images/loginbtn-popup.png"; ?>"></div>
                <div class="rm-publish-directory-col rm-difl">  
                    <div class="rm-section-publish-note"><?php _e('Display Login Button anywhere on your site','custom-registration-form-builder-with-submission-manager'); ?></div>
                    <div class="rm-publish-text"><?php _e('<strong>RegistrationMagic Login Button Widget</strong> now allows you to publish Login/ Logout button anywhere on your site. It provides you option to define your own labels and login/ logout behaviour.','custom-registration-form-builder-with-submission-manager'); ?></div>
                    <div class="rm-publish-text"><?php printf(__('<a href="%s">Go To Widgets Now</a>', 'custom-registration-form-builder-with-submission-manager'),admin_url("widgets.php")); ?></div>
                </div>
            </div>
        </div>

    </div>
<?php } ?>