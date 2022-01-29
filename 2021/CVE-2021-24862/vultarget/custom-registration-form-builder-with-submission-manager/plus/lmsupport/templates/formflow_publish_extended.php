<?php
?>
<div class="rm-directory-container dbfl">
    <div class="rm-publish-directory-col rm-difl">
        <div class="rm-section-publish-note"><?php _e('Display this form in a landing/squeeze page','custom-registration-form-builder-with-submission-manager'); ?> </div>
        <div class="rm-publish-text"><?php _e('RegistrationMagic has a dedicated extension for this purpose. It will allow you to display any form inside a landing or squeeze page, with full control over other visual elements of the page. The extension is free, and you can get started within few minutes. You can create as many pages as you like with individual slugs.','custom-registration-form-builder-with-submission-manager'); ?> </div>
        <?php if(!$data->is_lm_installed): ?>
            <a href="<?php echo $data->lm_install_url; ?>"><?php _e('Install Now','custom-registration-form-builder-with-submission-manager'); ?></a>
        <?php elseif(!$data->is_lm_activated): ?>
            <a href="<?php echo $data->lm_activate_url; ?>"><?php _e('Activate Now','custom-registration-form-builder-with-submission-manager'); ?></a>
        <?php else: ?>
            <a href="<?php echo $data->lm_page_url; ?>"><?php _e('Create/Manage Landing Pages','custom-registration-form-builder-with-submission-manager'); ?></a>
        <?php endif; ?>
    </div>
    <div class="rm-publish-directory-col rm-difl"><img src="<?php echo RM_EX_LMS()->base_url; ?>images/rm-splash.png"></div>
</div>
