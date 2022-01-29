<div class="rmlms-promo-wrap rmagic">
    <div  class="rmcontent">
        <div class="rmheader"><?php _e( 'LeadMagic', 'custom-registration-form-builder-with-submission-manager' ); ?></div>  
<?php if(!$data->is_lm_activated): ?>
    <div class="rmrow rmlms-banner"><img src="<?php echo RM_EX_LMS()->base_url; ?>images/lm_sshot.jpg"/></div>

<div class="rmrow rmlms-prag"><strong><?php _e( 'LeadMagic', 'custom-registration-form-builder-with-submission-manager' ); ?></strong> 
    <?php _e( 'solves two problems that 
    many of our users face - Firstly,
    an ability to quickly create a working 
    splash page/ landing page on their site
    with a permalink and embedded form without 
    worrying about the technicalities. Secondly, 
    displaying registration form in a consistent design environment 
    where it fits in overall scheme of the page presenting a pleasant experience to the visitor.', 'custom-registration-form-builder-with-submission-manager' ); ?></div>

<div class="rmrow rmlms-action-call"><?php _e( 'Ready to <strong>extend</strong> power of your forms?', 'custom-registration-form-builder-with-submission-manager' ) ?></div>
    <?php if(!$data->is_lm_installed): ?>
    <div class="rmlms-button-wrap rmrow"><a class="button" href="<?php echo $data->lm_install_url; ?>" target="_self"><?php _e( 'Install Now', 'custom-registration-form-builder-with-submission-manager' ) ?></a></div>
    <?php else:?>
    <div class="rmlms-button-wrap rmrow"><a class="button" href="<?php echo $data->lm_activate_url; ?>" target="_self"><?php _e( 'Activate Now', 'custom-registration-form-builder-with-submission-manager' ) ?></a></div>
    <?php endif; ?>
<?php else: ?>
    <div class="rmrow">
    <?php printf(__( 'LeadMagic is already installed. <a href="%s">Click here</a> to create/manage landing pages.', 'custom-registration-form-builder-with-submission-manager' ),$data->lm_page_url); ?>    
    
</div>

<?php endif; ?>

    </div>
    </div>
