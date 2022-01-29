<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_login_sett_redirections.php'); else {
$params= $data->params;?>
<div class="rmagic">
    <!--Dialogue Box Starts-->
    <div class="rmcontent">
        
              <div class="rmheader"><?php echo _e('Redirections', 'custom-registration-form-builder-with-submission-manager'); ?></div>
        <div class="rmrow">
            <div class="rmnotice">
                <?php printf(__('Note: In rare few cases, server side caching may interfere with Redirections. Try disabling the server cache for your login page. This can be done by submitting a request to your server support team. You can also contact our support team <a target="_blank" href="%s">here</a>.', 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/help-support/'); ?>
            </div>
        </div> 
        <?php
        $form = new RM_PFBC_Form("add-login-redirection");

        $form->configure(array(
            "prevent" => array("bootstrap", "jQuery"),
            "action" => ""
        ));
  
        
        $form->addElement(new Element_Radio(__('Type of redirection', 'custom-registration-form-builder-with-submission-manager'), "redirection_type", array('common'=>__('Common', 'custom-registration-form-builder-with-submission-manager'),'role_based'=>__('Role Based', 'custom-registration-form-builder-with-submission-manager')), array("value" => $params['redirection_type'], "longDesc"=>__('Define if you wish to have same redirections for all user roles or every role will have seperate redirections.', 'custom-registration-form-builder-with-submission-manager'))));
            $form->addElement(new Element_HTML('<div id="rm_common_redirection" class="childfieldsrow">'));
                $form->addElement(new Element_Select("<b>" .__('After Login Redirect User to', 'custom-registration-form-builder-with-submission-manager') . "</b>", "redirection_link",RM_Utilities::wp_pages_dropdown(), array("value" => $params['redirection_link'], "class" => "rm_static_field rm_required", "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_POST_SUB_REDIR'))));
                $form->addElement(new Element_Checkbox("<b>" . __('Always redirect admin users to dashboard', 'custom-registration-form-builder-with-submission-manager') . "</b>", "admin_redirection_link", array(1 => ""), array("class" => "rm-static-field rm_input_type", "value" => isset($params['admin_redirection_link']) ? $params['admin_redirection_link'] : 0, "longdesc" => RM_UI_Strings::get('HELP_OPTIONS_GEN_REDIRECT_ADMIN_TO_DASH') )));
                $form->addElement(new Element_Select("<b>" . __('After Logout Redirect User to', 'custom-registration-form-builder-with-submission-manager') . "</b>", "logout_redirection",RM_Utilities::wp_pages_dropdown(), array("value" => $params['logout_redirection'], "class" => "rm_static_field rm_required", "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_POST_LOGOUT_REDIR'))));
            $form->addElement(new Element_HTML('</div>'));
            
            // Role Based
            $form->addElement(new Element_HTML('<div id="rm_role_redirection" class="childfieldsrow">'));
                foreach($data->roles as $role=>$role_name){
                    $role= strtolower(str_replace(' ', '', $role));
                    $form->addElement(new Element_Checkbox("<b>" . ucwords($role_name) . "</b>", "role_based_login_redirection[]", array($role => ""), array("class" => "rm-role-based rm-static-field rm_input_type", "value" => $params['role_based_login_redirection'], "longdesc" =>__('Turn on role specific redirection.','custom-registration-form-builder-with-submission-manager') )));
                        $form->addElement(new Element_HTML('<div id="rm_'.$role.'_redirection" class="childfieldsrow">'));
                            $form->addElement(new Element_Select("<b>" . __('After Login Redirect User to', 'custom-registration-form-builder-with-submission-manager') . "</b>", $role."_login_redirection",RM_Utilities::wp_pages_dropdown(), array("value" => isset($params[$role.'_login_redirection']) ? $params[$role.'_login_redirection'] : '', "class" => "rm_static_field rm_required", "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_POST_SUB_REDIR'))));
                            $form->addElement(new Element_Select("<b>" . __('After Logout Redirect User to', 'custom-registration-form-builder-with-submission-manager') . "</b>", $role."_logout_redirection",RM_Utilities::wp_pages_dropdown(), array("value" => isset($params[$role.'_logout_redirection']) ? $params[$role.'_logout_redirection'] : '', "class" => "rm_static_field rm_required", "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_POST_LOGOUT_REDIR'))));
                        $form->addElement(new Element_HTML('</div>'));             
                }
            $form->addElement(new Element_HTML('</div>'));
            
            $form->addElement(new Element_HTMLL('&#8592; &nbsp; '.__('Cancel','custom-registration-form-builder-with-submission-manager'), '?page=rm_login_sett_manage', array('class' => 'cancel')));
            $form->addElement(new Element_Button(RM_UI_Strings::get('LABEL_SAVE'), "submit", array("id" => "rm_submit_btn", "class" => "rm_btn", "name" => "submit")));
        $form->render();
        ?>
    </div>
</div>

<script>
    jQuery(document).ready(function(){
        jQuery("input[name=redirection_type]").change(function(){
            var redirection_type= jQuery(this).val();
            if(redirection_type=='common' && jQuery(this).is(':checked')){
                jQuery("#rm_common_redirection").slideDown();
                jQuery("#rm_role_redirection").slideUp();
            }
            else if(redirection_type=='role_based' && jQuery(this).is(':checked')){
                
                 jQuery("#rm_common_redirection").slideUp();
                jQuery("#rm_role_redirection").slideDown();
            }
        });
        
        jQuery(".rm-role-based").change(function(){
            jQuery('.rm-role-based').each(function(){
                var current= jQuery(this).val();
                if(jQuery(this).is(':checked')){
                    jQuery('#rm_' + current + '_redirection').slideDown();
                }
                else{
                    jQuery('#rm_' + current + '_redirection').slideUp();
                }
            })
        });
        jQuery("input[name=redirection_type]").trigger('change');
        jQuery(".rm-role-based").change();
        
    });
    
    
    
</script>    
<?php } ?>