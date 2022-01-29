<?php

?>
<div class="rm-features-row">
    <div class="rm-feature-cell">
        <img src="<?php echo RM_IMG_URL; ?>help.png">
    </div>
    <div class="rm-feature-cell"><?php _e('How can I display a form in a landing/ squeeze page?','custom-registration-form-builder-with-submission-manager'); ?> <sup class="rm-new-primer-entry"><?php _e('New','custom-registration-form-builder-with-submission-manager'); ?></sup></div>
    <div class="rm-feature-cell">
        <img src="<?php echo RM_IMG_URL; ?>next.png">
    </div>

</div>
<div class="rm-feature-content">
    <img src="<?php echo RM_IMG_URL; ?>content-arrow.png" class="content-arrow">
    <?php _e('RegistrationMagic has a dedicated extension for this purpose. It will allow you to display any form inside a landing or squeeze page, with full control over other visual elements of the page. The extension is free, and you can get started within few minutes. You can create as many pages as you like with individual slugs.','custom-registration-form-builder-with-submission-manager'); ?>
<?php if(!$data->is_lm_installed): ?>
    <a href="<?php echo $data->lm_install_url; ?>"> <?php _e('Install Now','custom-registration-form-builder-with-submission-manager'); ?></a>.
<?php elseif(!$data->is_lm_activated): ?>
    <a href="<?php echo $data->lm_activate_url; ?>"><?php _e('Activate Now','custom-registration-form-builder-with-submission-manager'); ?></a>.
<?php else: ?>
    <a href="<?php echo $data->lm_page_url; ?>"><?php _e('Create/Manage Landing Pages','custom-registration-form-builder-with-submission-manager'); ?></a>.
<?php endif; ?>
    <img src="<?php echo RM_EX_LMS()->base_url; ?>images/lm_sshot_fe_primer.jpg" class="content-asset">
</div>