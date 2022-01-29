<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_profilegrid.php'); else {
if(!empty($_REQUEST['gs'])): ?>
    <?php if(!class_exists('Profile_Magic')): ?>
        <div class="rmlms-promo-wrap rmagic">
            <div  class="rmcontent">
                <div class="rmheader"><?php _e( 'ProfileGrid', 'custom-registration-form-builder-with-submission-manager' ); ?></div>  

                <div class="rmrow rmlms-banner"><img src="<?php echo RM_IMG_URL; ?>profilegrid_hero.jpg"/></div>

                <div class="rmrow rmlms-prag">
                    <?php echo sprintf(__('Turn front-end user area into powerful user profile hub. Allow users to edit their profiles including photos. Offer interactive features like groups memberships, real-time messaging and friend lists. Restrict content selectively. ProfileGrid is free and you can get started within minutes.', 'custom-registration-form-builder-with-submission-manager'),'https://profilegrid.co/'); ?>
                </div>

                <div class="rmlms-button-wrap rmrow"><a class="button" href="<?php echo $data->pg_install_url; ?>" target="_self"><?php _e( 'Install Now', 'custom-registration-form-builder-with-submission-manager' ) ?></a></div>
            </div>
        </div>
    <?php else: ?>
        <div class="rmlms-promo-wrap rmagic">
            <div  class="rmcontent">
                <div class="rmheader"><?php _e( 'ProfileGrid is installed and active.', 'custom-registration-form-builder-with-submission-manager' ); ?></div>  
                <div class="rmrow"><a href="<?php echo admin_url('admin.php?page=pm_manage_groups'); ?>" target="_blank"><?php _e('Click here to open ProfileGrid','custom-registration-form-builder-with-submission-manager'); ?></a></div>
                <div class="rmrow"><a href="https://profilegrid.co/profilegrid-starter-guide/" target="_blank"><?php _e('ProfileGrid Starter Guide','custom-registration-form-builder-with-submission-manager'); ?></a></div>
                <div class="rmrow"><a target="_blank" href="https://registrationmagic.com/registrationmagic-profilegrid-integration"><?php _e('Learn what you can do with ProfileGrid integration','custom-registration-form-builder-with-submission-manager'); ?></a></div>
                <div class="buttonarea"><a href="javascript:void(0);" onclick="window.history.back()">&larr; &nbsp;<?php _e('Back','custom-registration-form-builder-with-submission-manager') ?></a></div>
            </div>
        </div>
    <?php endif; ?>
<?php else: ?>
    <div class="rmlms-promo-wrap rmagic">
        <div  class="rmcontent">
            <div class="rmheader"><?php _e( 'ProfileGrid is installed and active.', 'custom-registration-form-builder-with-submission-manager' ); ?></div>  
            <div class="rmrow"><a href="<?php echo admin_url('admin.php?page=pm_manage_groups'); ?>" target="_blank"><?php _e('Click here to open ProfileGrid','custom-registration-form-builder-with-submission-manager'); ?></a></div>
            <div class="rmrow"><a href="https://profilegrid.co/profilegrid-starter-guide/" target="_blank"><?php _e('ProfileGrid Starter Guide','custom-registration-form-builder-with-submission-manager'); ?></a></div>
            <div class="rmrow"><a target="_blank" href="https://registrationmagic.com/registrationmagic-profilegrid-integration"><?php _e('Learn what you can do with ProfileGrid integration','custom-registration-form-builder-with-submission-manager'); ?></a></div>
           <div class="buttonarea"><a href="javascript:void(0);" onclick="window.history.back()">&larr; &nbsp;<?php _e('Back','custom-registration-form-builder-with-submission-manager') ?></a></div>
        </div>
    </div>
<?php endif;
}