<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_options_user_privacy.php'); else {
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
        $form = new RM_PFBC_Form("options_user_privacy");
        $form->configure(array(
            "prevent" => array("bootstrap", "jQuery"),
            "action" => ""
        ));

        $form->addElement(new Element_HTML('<div class="rmheader">' . __('User Privacy', 'custom-registration-form-builder-with-submission-manager') . '</div>'));
        
        $form->addElement(new Element_HTML('<div class="rmrow"><div class="rmfield"><label>'.__('Delete All Plugin Data', 'custom-registration-form-builder-with-submission-manager').'</label></div><div class="rminput"><ul><li><a href="#rm-confirm-box" class="rm_btn button"  onclick="CallModalBoxEmail(this)">'.__('Delete', 'custom-registration-form-builder-with-submission-manager').'</a></li></ul></div><div class="rmnote"><div class="rmnotecontent">'.__('This will empty all RegistrationMagic tables in your WordPress database returning it to initial default installation state. Frontend published shortcode will remain working as long as RegistrationMagic is active. But you will have to reassign them at appropriate places. Do not close or refresh the page while this process is being executed. After execution, a success message will popup.', 'custom-registration-form-builder-with-submission-manager').'</div></div></div>'));
        ?>
        <div id="rm-confirm-box" class="rm-login-user-details rm-send-email" style="display:none">
            <div class="rm-modal-overlay"></div>
            <div class="rm-modal-wrap">
                <div class="rm-modal-title rm-dbfl">Please Confirm <div class="rm-modal-close">&times;</div></div>
                <div class="rm-modal-container rm-confirm-box-container rm-dbfl">
                    <div class="rm-dbfl rm-confirm-box-info"><span class="rm-dbfl" ><?php _e('You are going to delete all RegistrationMagic data from your site. This action cannot be undone. Click OK to proceed.', 'custom-registration-form-builder-with-submission-manager') ?></span></div>
                </div>

                <div class="rm-send-email-footer rm-dbfl">
                    <div class="rm-difl rm-data-cancel-bt"><a href="javascript:void(0)" class="rm-model-cancel">← &nbsp;<?php _e("Cancel", 'custom-registration-form-builder-with-submission-manager'); ?></a></div>
                    <div class="rm-difl rm-data-delete-bt"><a href="javascript:void(0)" onclick="rm_delete_data()">OK</a></div>
                </div>
            </div>
        </div>
        
        <div class="rm_status_update_model" class="rm-send-email">
            <div class="rm-notification-overlay"></div>
            <div class="rm-modal-wrap-toast">
                <div class="rm-modal-container rm-dbfl">
                    <div class="rm-status-close">×</div>
                    <div class="rm-dbfl rm-email-box-row rm_status_update_body">
                     
                    </div>
                </div>
            </div>
        </div>
        
        <div class="rm_status_failed_model" class="rm-send-email">
            <div class="rm-notification-overlay"></div>
            <div class="rm-modal-wrap-toast">
                <div class="rm-modal-container rm-dbfl">
                    <div class="rm-status-close">×</div>  
                    <div class="rm-dbfl rm-email-box-row"><?php _e('There was a problem and the process did not complete. Please try again later.', 'custom-registration-form-builder-with-submission-manager'); ?></div>
                </div>
            </div>
        </div>
        <?php
        $form->addElement(new Element_HTMLL('&#8592; &nbsp; '.__('Cancel','custom-registration-form-builder-with-submission-manager'), '?page=rm_options_manage', array('class' => 'cancel')));
        //$form->addElement(new Element_Button(RM_UI_Strings::get('LABEL_SAVE')));

        $form->render();
        ?>
    </div>
</div>
<script>
function CallModalBoxEmail(ele) {
    jQuery(jQuery(ele).attr('href')).toggle();
}

jQuery(document).ready(function () {
    jQuery('.rm-modal-close, .rm-modal-overlay, .rm-model-cancel').click(function () {
        jQuery(this).parents('#rm-confirm-box').hide();
    });
    
    jQuery('.rm-status-close').click(function(){
        jQuery('.rm_status_update_model').hide();
        jQuery('.rm_status_failed_model').hide();
        location.reload(true);
    });
});

function rm_delete_data(){
    var data = {
        'action': 'rm_delete_data'
    };
    jQuery('.rm-send-email-footer').html('Please wait.....')
    jQuery.post(ajaxurl, data, function(response) {
        jQuery('#rm-confirm-box').toggle();
        
        var result = response.split('-');
        if(result[0]=='success'){
            jQuery('.rm_status_update_body').html('All RegistrationMagic data was deleted from its table successfully! A total of '+result[1]+' database records were removed.')
            jQuery('.rm_status_update_model').addClass('rm-modal-show');
         
        }else{
            jQuery('.rm_status_failed_model').addClass('rm-modal-show');
        }
        
        
        setTimeout(function(){
           location.reload(true);
        }, 15000);
    });
}
</script>
<?php   
}