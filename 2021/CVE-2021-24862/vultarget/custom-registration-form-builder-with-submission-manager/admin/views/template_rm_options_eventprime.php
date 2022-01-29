<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_options_eventprime.php'); else {
if(!empty($_REQUEST['gs'])): ?>
    <?php if(!class_exists('Event_Magic')): ?>
        <div class="rmlms-promo-wrap rmagic">
            <div  class="rmcontent">
                <div class="rmheader"><?php _e( 'EventPrime', 'custom-registration-form-builder-with-submission-manager' ); ?></div>  

                <div class="rmrow rmlms-banner"><img src="<?php echo RM_IMG_URL; ?>eventprime-hero.jpg"/></div>

                <div class="rmrow rmlms-prag">
                    <div class="rm-ep-promo-content">
                        <?php echo sprintf(__('Planning to publish events calendar on your site? Use EventPrime to create simple or complex events and manage bookings. EventPrime is free and you can get started within minutes. ', 'custom-registration-form-builder-with-submission-manager'),'https://eventprime.net/'); ?>
                    </div>
                </div>
                
                <div class="rmrow rm-ep-promo-features">
                    
                    <ul>
                        <li>Extend power of your RegistrationMagic forms by connecting them to a complete Event Management toolkit.</li>
                        <li>Create/ edit, drag and drop Events directly on the Events Calendar.</li>
                        <li>Manage Event Sites/ Venues (Optional)</li>
                        <li>Manage Event Performers, Keynote Speakers, Hosts etc. (Optional)</li>
                        <li>Powerful widgets to showcase Events and Calendar on your website.</li>
                        <li>Dedicated area for your users to manage bookings.</li>
                        <li>Works with any theme.</li>
                    </ul>
                
                </div>

                <div class="rmlms-button-wrap rmrow"><a class="button" href="<?php echo $data->ep_install_url; ?>" target="_self"><?php _e( 'Install Now', 'custom-registration-form-builder-with-submission-manager' ) ?></a></div>
                <div class="rmrow rm-mg-icon"><img src="<?php echo RM_IMG_URL; ?>mg-icon.png"/></div>
            </div>
        </div>
    <?php else: ?>
        <div class="rmlms-promo-wrap rmagic">
            <div  class="rmcontent">
                <div class="rmheader"><?php _e( 'EventPrime is installed and active.', 'custom-registration-form-builder-with-submission-manager' ); ?></div> 
                <div class="rmrow"><a href="<?php echo admin_url('admin.php?page=event_magic'); ?>" target="_blank"><?php _e('Click here to open EventPrime','custom-registration-form-builder-with-submission-manager'); ?></a></div>
                <div class="rmrow"><a href="https://eventprime.net/starter-guide/" target="_blank"><?php _e('EventPrime Starter Guide','custom-registration-form-builder-with-submission-manager'); ?></a></div>
                <div class="rmrow"><a href="https://registrationmagic.com/boost-registrationmagic-event-froms-eventprime-integration/" target="_blank"><?php _e('Learn what you can do with EventPrime integration','custom-registration-form-builder-with-submission-manager'); ?></a></div>
                <div class="buttonarea"><a href="javascript:void(0);" onclick="window.history.back()">&larr; &nbsp;<?php _e('Back','custom-registration-form-builder-with-submission-manager') ?></a></div>
            </div>
        </div>
    <?php endif; ?>
<?php else: ?>
    <div class="rmlms-promo-wrap rmagic">
        <div  class="rmcontent">
            <div class="rmheader"><?php _e( 'EventPrime is installed and active.', 'custom-registration-form-builder-with-submission-manager' ); ?></div>  
              <div class="rmrow"><a href="<?php echo admin_url('admin.php?page=event_magic'); ?>" target="_blank"><?php _e('Click here to open EventPrime','custom-registration-form-builder-with-submission-manager'); ?></a></div>
                <div class="rmrow"><a href="https://eventprime.net/starter-guide/" target="_blank"><?php _e('EventPrime Starter Guide','custom-registration-form-builder-with-submission-manager'); ?></a></div>
                <div class="rmrow"><a href="https://registrationmagic.com/boost-registrationmagic-event-froms-eventprime-integration/" target="_blank"><?php _e('Learn what you can do with EventPrime integration','custom-registration-form-builder-with-submission-manager'); ?></a></div>
            <div class="buttonarea"><a href="javascript:void(0);" onclick="window.history.back()">&larr; &nbsp;<?php _e('Back','custom-registration-form-builder-with-submission-manager') ?></a></div>
        </div>
    </div>
<?php endif;
}