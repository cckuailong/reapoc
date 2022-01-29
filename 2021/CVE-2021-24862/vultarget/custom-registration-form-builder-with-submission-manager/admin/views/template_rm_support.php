<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_support.php'); else {
?>
<div class="rmagic rm-support-wrap">
    <!--------Operationsbar Ends-->

    <!-------Contentarea Starts-->

    <div class="rm-support-container">

        <div class="rm-support-block rm-difl">
            <div class="rm-support-block-head rm-dbfl">
                    <div class="rm-support-block-title rm-difl"><?php _e('Resources', 'custom-registration-form-builder-with-submission-manager'); ?></div> 
                <div class="rm-support-block-icon rm-difl"> <img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'images/rm-support-resources.png'; ?>"></div>
               
            </div>
            
            <div class="rm-support-col-wrap">
                <div class="rm-support-col">
                    
                    <div class="rm-support-col-row rm-support-col-head"><?php _e('Guides', 'custom-registration-form-builder-with-submission-manager'); ?></div>
                    <div class="rm-support-col-row ">
                        <div class="rm-support-col-subhead"><a href="https://registrationmagic.com/create-wordpress-registration-page-starter-guide/" target="_blank"><?php _e('Starter Guide', 'custom-registration-form-builder-with-submission-manager'); ?></a></div>
                        <div class="rm-support-text"><?php _e('Build your first WordPress registration page', 'custom-registration-form-builder-with-submission-manager'); ?></div>
                    </div>
                    
                    <div class="rm-support-col-row ">
                        <div class="rm-support-col-subhead"><a href="https://registrationmagic.com/setup-payments-on-registrationmagic-form-using-products/" target="_blank"><?php _e('Payments Guide', 'custom-registration-form-builder-with-submission-manager'); ?></a></div>
                        <div class="rm-support-text"><?php _e('Start accepting payments from your users', 'custom-registration-form-builder-with-submission-manager'); ?></div>
                    </div>
                    
                    <div class="rm-support-col-row ">
                        <div class="rm-support-col-subhead"><a href="https://registrationmagic.com/security-guide-for-wordpress-forms/" target="_blank"><?php _e('Security Guide', 'custom-registration-form-builder-with-submission-manager'); ?></a></div>
                        <div class="rm-support-text"><?php _e('Learn best security practices for registration forms', 'custom-registration-form-builder-with-submission-manager'); ?></div>
                    </div>
                    
                    <div class="rm-support-col-row ">
                        <div class="rm-support-col-subhead"><a href="https://registrationmagic.com/wordpress-user-registration-status-guide/" target="_blank"><?php _e('Custom Status Guide', 'custom-registration-form-builder-with-submission-manager'); ?></a></div>
                        <div class="rm-support-text"><?php _e('How to set up and use custom statuses and labels', 'custom-registration-form-builder-with-submission-manager'); ?></div>
                    </div>
                    
                    <div class="rm-support-col-row ">
                        <div class="rm-support-col-subhead"><a href="https://registrationmagic.com/wordpress-user-roles-permissions-role-editor-complete-guide/" target="_blank"><?php _e('Roles and Permissions Guide', 'custom-registration-form-builder-with-submission-manager'); ?></a></div>
                        <div class="rm-support-text"><?php _e('Extend or limit control of users on your site', 'custom-registration-form-builder-with-submission-manager'); ?></div>
                    </div>
                    
                    <div class="rm-support-col-row ">
                        <div class="rm-support-col-subhead"><a href="https://registrationmagic.com/complete-automation-guide-wordpress-forms/" target="_blank"><?php _e('Automation Guide', 'custom-registration-form-builder-with-submission-manager'); ?></a></div>
                        <div class="rm-support-text"><?php _e('Automate workflows to reduce manual stress', 'custom-registration-form-builder-with-submission-manager'); ?></div>
                    </div>
                    
                    <div class="rm-support-col-row ">
                        <div class="rm-support-col-subhead"><a href="https://registrationmagic.com/publish-registration-forms-and-display-registered-users/" target="_blank"><?php _e('Publishing Guide', 'custom-registration-form-builder-with-submission-manager'); ?></a></div>
                        <div class="rm-support-text"><?php _e('Display what you have built on the frontend', 'custom-registration-form-builder-with-submission-manager'); ?></div>
                    </div>
                    
                    <div class="rm-support-col-row ">
                        <div class="rm-support-col-subhead"><a href="https://registrationmagic.com/wordpress-user-login-plugin-guide/" target="_blank"><?php _e('Login Guide', 'custom-registration-form-builder-with-submission-manager'); ?></a></div>
                        <div class="rm-support-text"><?php _e('Make use of advance in-built login features', 'custom-registration-form-builder-with-submission-manager'); ?></div>
                    </div>
                    
                    <div class="rm-support-col-row ">
                        <div class="rm-support-col-subhead"><a href="https://registrationmagic.com/wordpress-registration-shortcodes-list/" target="_blank"><?php _e('Shortcodes Guide', 'custom-registration-form-builder-with-submission-manager'); ?></a></div>
                        <div class="rm-support-text"><?php _e('All the shortcodes you need in one place', 'custom-registration-form-builder-with-submission-manager'); ?></div>
                    </div>
                    
                    
                    
                </div>
                
                
                
                <div class="rm-support-col">
                    <div class="rm-support-col-row rm-support-col-head"><?php _e('Useful Links', 'custom-registration-form-builder-with-submission-manager'); ?></div>
                    <div class="rm-support-col-row">
                        <div class="rm-support-col-subhead"><a href="https://registrationmagic.com/tag/tutorials/" target="_blank"><?php _e('Tutorials', 'custom-registration-form-builder-with-submission-manager'); ?></a></div>
                        <div class="rm-support-text"><?php _e('Comprehensive tutorials covering all aspects of RegistrationMagic.', 'custom-registration-form-builder-with-submission-manager'); ?></div>
                    </div>
                    
                    <div class="rm-support-col-row">
                        <div class="rm-support-col-subhead"><a href="https://registrationmagic.com/tag/wordpress-ideas/" target="_blank"><?php _e('Breakthrough Ideas', 'custom-registration-form-builder-with-submission-manager'); ?></a></div>
                        <div class="rm-support-text"><?php _e('Innovative solutions to your everyday problems', 'custom-registration-form-builder-with-submission-manager'); ?></div>
                    </div>
                    
                    <div class="rm-support-col-row">
                        <div class="rm-support-col-subhead"><a href="https://registrationmagic.com/blog/" target="_blank"><?php _e('Blog', 'custom-registration-form-builder-with-submission-manager'); ?></a></div>
                        <div class="rm-support-text"><?php _e('Our regular blog with feature updates and more', 'custom-registration-form-builder-with-submission-manager'); ?></div>
                    </div>
                    
                    <div class="rm-support-col-row">
                        <div class="rm-support-col-subhead"><a href="https://wordpress.org/plugins/custom-registration-form-builder-with-submission-manager/#developers" target="_blank"><?php _e('Changelog', 'custom-registration-form-builder-with-submission-manager'); ?></a></div>
                        <div class="rm-support-text"><?php _e('Chronological list of changes and updates', 'custom-registration-form-builder-with-submission-manager'); ?></div>
                    </div>
                    
                   <div class="rm-support-col-row">
                        <div class="rm-support-col-subhead"><a href="https://wordpress.org/plugins/custom-registration-form-builder-with-submission-manager/advanced/#plugin-download-history-stats" target="_blank"><?php _e('Previous Version', 'custom-registration-form-builder-with-submission-manager'); ?></a></div>
                        <div class="rm-support-text"><?php _e('For rolling back to a previous Standard release', 'custom-registration-form-builder-with-submission-manager'); ?></div>
                    </div> 
                    
                    <!-- <div class="rm-support-col-row">
                        <div class="rm-support-col-subhead"><a href="https://registrationmagic.com/checkout/purchase-history/" target="_blank"><?php _e('Download', 'custom-registration-form-builder-with-submission-manager'); ?></a></div>
                        <div class="rm-support-text"><?php _e('Download the latest version of Premium', 'custom-registration-form-builder-with-submission-manager'); ?></div>
                    </div>-->
                    
                    <div class="rm-support-col-row rm-support-col-head"><?php _e('Contact Us', 'custom-registration-form-builder-with-submission-manager'); ?></div>
                    
                    <div class="rm-support-col-row">
                        <div class="rm-support-col-subhead"><a href="https://registrationmagic.com/help-support/" target="_blank"><?php _e('Feature Request', 'custom-registration-form-builder-with-submission-manager'); ?></a></div>
                        <div class="rm-support-text"><?php _e('Submit a feature request to our development team', 'custom-registration-form-builder-with-submission-manager'); ?></div>
                    </div>
                    
                    <div class="rm-support-col-row">
                        <div class="rm-support-col-subhead"><a href="https://registrationmagic.com/help-support/" target="_blank"><?php _e('Support Ticket', 'custom-registration-form-builder-with-submission-manager'); ?></a></div>
                        <div class="rm-support-text"><?php _e('Create a support ticket on our helpdesk', 'custom-registration-form-builder-with-submission-manager'); ?></div>
                    </div>
                
                
                </div> 

            </div>
        </div>

        <div class="rm-support-block rm-difl">
        <div class="rm-support-block-head rm-dbfl">
                              <div class="rm-support-block-title rm-difl"><?php _e('FAQs', 'custom-registration-form-builder-with-submission-manager'); ?></div>  
                <div class="rm-support-block-icon rm-difl"> <img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'images/rm-support-faq.png'; ?>"></div>

            </div>
            
         <div id="rm-support-faq">
             <div class="rm-support-faq-row rm-support-faq-head"><?php _e('Frequently Asked Questions', 'custom-registration-form-builder-with-submission-manager'); ?></div>
             
             <!--  <div class="rm-support-faq-row">
		<div class="rm-support-faq-toggle">
                    <a href="#" target="_blank"> <?php _e('How to send forms to your email after submission?', 'custom-registration-form-builder-with-submission-manager'); ?></a>
            </div> -->
                

            
             
            <div class="rm-support-faq-row">
		<div class="rm-support-faq-toggle">
                    <a href="https://registrationmagic.com/how-display-custom-success-message-in-wordpress-forms/" target="_blank"> <?php _e('How to display a message after form submission?', 'custom-registration-form-builder-with-submission-manager'); ?></a>
		</div>
            </div>
             
            <div class="rm-support-faq-row">
		<div class="rm-support-faq-toggle">
                      <a href="https://registrationmagic.com/autoresponder-email-message-after-form-submission/" target="_blank">  <?php _e('How to send a confirmation or thank you email to your users who have successfully submitted forms?', 'custom-registration-form-builder-with-submission-manager'); ?></a>
		</div>
            </div>
             
            <div class="rm-support-faq-row">
		<div class="rm-support-faq-toggle">
                      <a href="https://registrationmagic.com/redirect-user-to-specific-page-after-form-submission/" target="_blank"> <?php _e('How to redirect users to a different page or post after form submission?', 'custom-registration-form-builder-with-submission-manager'); ?></a>
		</div>
                
	
            </div>
             
         <!--  <div class="rm-support-faq-row">
		<div class="rm-support-faq-toggle">
                       <a href="#">  <?php _e('How to force login after form submission?', 'custom-registration-form-builder-with-submission-manager'); ?></a>
		</div>
            </div> -->
             
              <!--  <div class="rm-support-faq-row">
		<div class="rm-support-faq-toggle">
                       <a href="#">  <?php _e('How to send forms automatically to another email address?', 'custom-registration-form-builder-with-submission-manager'); ?></a>
		</div>
         
               </div>  -->
             
                <div class="rm-support-faq-row">
		  <div class="rm-support-faq-toggle">
                       <a href="https://registrationmagic.com/knowledgebase/form-style/" target="_blank">  <?php _e('How to change position of the labels on your forms?', 'custom-registration-form-builder-with-submission-manager'); ?></a>
		 </div>
                </div>
             
                <div class="rm-support-faq-row">
		  <div class="rm-support-faq-toggle">
                       <a href="https://registrationmagic.com/add-captcha-wordpress-login-registration-form/" target="_blank">  <?php _e('How to enable reCAPTCHA on your forms?', 'custom-registration-form-builder-with-submission-manager'); ?></a>
		 </div>
                </div>
             
                <div class="rm-support-faq-row">
		  <div class="rm-support-faq-toggle">
                       <a href="https://registrationmagic.com/manage-export-wordpress-registration-forms-data/" target="_blank">  <?php _e('How to export form submissions?', 'custom-registration-form-builder-with-submission-manager'); ?></a>
		 </div>
                </div>
             
                <!--  <div class="rm-support-faq-row">
		  <div class="rm-support-faq-toggle">
                       <a href="#">  <?php _e('How to approve user accounts without logging into the dashboard?', 'custom-registration-form-builder-with-submission-manager'); ?></a>
		 </div>
                </div> -->
             
                <div class="rm-support-faq-row">
		  <div class="rm-support-faq-toggle">
                       <a href="https://registrationmagic.com/wordpress-registrationmagic-form-payments-even-easier/" target="_blank">  <?php _e('How to accept payments through your forms?', 'custom-registration-form-builder-with-submission-manager'); ?></a>
		 </div>
                </div>
             
                <!--  <div class="rm-support-faq-row">
		  <div class="rm-support-faq-toggle">
                       <a href="#">  <?php _e('How to allow users to edit their forms after submission?', 'custom-registration-form-builder-with-submission-manager'); ?></a>
		 </div>
                </div> -->
             
                <div class="rm-support-faq-row">
		  <div class="rm-support-faq-toggle">
                       <a href="https://registrationmagic.com/wordpress-registration-form-required-fields/" target="_blank">  <?php _e('How to make a field mandatory in a form?', 'custom-registration-form-builder-with-submission-manager'); ?></a>
		 </div>
                </div>
             
                <div class="rm-support-faq-row">
		  <div class="rm-support-faq-toggle">
                       <a href="https://registrationmagic.com/add-configure-rich-text-in-your-wordpress-form/" target="_blank">  <?php _e('How to insert content or rich text within the form?', 'custom-registration-form-builder-with-submission-manager'); ?></a>
		 </div>
                </div>
             
                  <div class="rm-support-faq-row">
		  <div class="rm-support-faq-toggle">
                       <a href="https://registrationmagic.com/disable-wordpress-user-without-deleting-their-account/" target="_blank">  <?php _e('How to deactivate or suspend an already registered user?', 'custom-registration-form-builder-with-submission-manager'); ?></a>
		 </div>
                </div>
             
                <div class="rm-support-faq-row">
		  <div class="rm-support-faq-toggle">
                       <a href="https://registrationmagic.com/wordpress-user-login-plugin-guide/" target="_blank">  <?php _e('How to publish a login form or a button on your site?', 'custom-registration-form-builder-with-submission-manager'); ?></a>
		 </div>
                </div>
             
                <div class="rm-support-faq-row">
		  <div class="rm-support-faq-toggle">
                       <a href="https://registrationmagic.com/redirect-user-to-specific-page-after-form-submission/" target="_blank">  <?php _e('How to redirect users after login?', 'custom-registration-form-builder-with-submission-manager'); ?></a>
		 </div>
                </div>
             
                <div class="rm-support-faq-row">
		  <div class="rm-support-faq-toggle">
                       <a href="https://registrationmagic.com/wordpress-user-management-plugin-guide/" target="_blank">  <?php _e("How to check user's login records?", "custom-registration-form-builder-with-submission-manager"); ?></a>
		 </div>
                </div>

	</div>
 
        </div>

    </div>

</div>
<?php } ?>