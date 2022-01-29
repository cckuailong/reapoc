<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_frontend_primer.php'); else {
?>

<div class="rm-features-list">
    <div class="rm-101"><?php _e('RegistrationMagic 101', 'custom-registration-form-builder-with-submission-manager'); ?></div>
    <div class="rm-feature-banner">
        <?php _e('A quick lowdown on RegistrationMagic front end features and how to display them.', 'custom-registration-form-builder-with-submission-manager'); ?>
        <span class="rm-pro"><a target="_blank" href="http://registrationmagic.com/registrationmagic-silver-edition/"></a></span>

    </div>


    <div class="rm-features-table">
        <div class="rm-features-row">
            <div class="rm-feature-cell">
                <img src="<?php echo RM_IMG_URL; ?>help.png">
            </div>
            <div class="rm-feature-cell"><?php _e('Four simple ways to register users on your site.', 'custom-registration-form-builder-with-submission-manager'); ?></div>
            <div class="rm-feature-cell">
                <img src="<?php echo RM_IMG_URL; ?>next.png">
            </div>

        </div>
        <div class="rm-feature-content">
            <img src="<?php echo RM_IMG_URL; ?>content-arrow.png" class="content-arrow">
            <ol>
                <li><strong><?php _e('Using shortcode', 'custom-registration-form-builder-with-submission-manager'); ?></strong></li>
                <p><?php _e('Paste the form shortcode on the page or post where you want to display the form. RegistrationMagic form shortcodes are in this format', 'custom-registration-form-builder-with-submission-manager'); ?> <span class="rm-code">[RM_Form id='1']</span> <?php _e('and are found on form cards.', 'custom-registration-form-builder-with-submission-manager'); ?> <a target="_blank" href="https://registrationmagic.com/display-registration-forms-wordpress-site/"><?php _e('Read this tutorial', 'custom-registration-form-builder-with-submission-manager'); ?></a> <?php _e('for a step by step guide.', 'custom-registration-form-builder-with-submission-manager'); ?></p>
                
                <li><strong><?php _e('Using RegistrationMagic Widget', 'custom-registration-form-builder-with-submission-manager'); ?></strong></li>
                <p><?php _e("If you are using one of the many available page builder plugins, you can also use the built in RegistrationMagic widget to publish forms on your pages. Page builder plugins allow you to create custom layouts by dragging and dropping widgets in rows or columns. In Widgets section of your favorite page builder plugin, search for 'RegistrationMagic Form' widget. Once selected, it will ask you to select a form from dropdown in its settings. If you do not find RegistrationMagic form widget, you can always add an editor widget and paste RegistrationMagic form shortcode in it.", 'custom-registration-form-builder-with-submission-manager'); ?></p>

                <p><?php _e('By using same method, you can also publish RegistrationMagic forms on any widget area in your theme by going to Appearance -> Widgets section in your dashboard', 'custom-registration-form-builder-with-submission-manager'); ?></p>
                 <img src="<?php echo RM_IMG_URL; ?>widget-section.jpg" class="content-asset">
                <li><strong><?php _e('Using Front End sliding panel', 'custom-registration-form-builder-with-submission-manager'); ?></strong></li>
                <p><?php printf(__('Turn on the Magic button on your site by going to <a target="_blank" href="%s">Global Settings / General Settings</a>. Look for the toggle checkbox with label <span class="rm-code">Show Magic Pop-up Button, Menu and Panels</span> and turn it on.', 'custom-registration-form-builder-with-submission-manager'),'admin.php?page=rm_options_general'); ?></p>
                <p><?php _e('Make sure you have selected a form as your default registration form by clicking on the grey star over the form card. This form will display automatically inside the sliding panel.', 'custom-registration-form-builder-with-submission-manager'); ?></p>
                <img src="<?php echo RM_IMG_URL; ?>floating-menu.jpg" class="content-asset">
                <img src="<?php echo RM_IMG_URL; ?>floating-button.jpg" class="content-asset">
                <li><strong><?php _e('Embedding form on an external site', 'custom-registration-form-builder-with-submission-manager'); ?></strong></li>
                <p><?php _e('This method to display registration forms on WordPress site is for advanced users and therefore only available in Gold Bundle of RegistrationMagic. It works best when we want to show a RegistrationMagic form where there’s no option to paste the shortcode. For example – a site outside our WordPress site.', 'custom-registration-form-builder-with-submission-manager'); ?></p>
                <p><?php _e('Embed codes are located just below the short code.', 'custom-registration-form-builder-with-submission-manager'); ?></p>
                <img src="<?php echo RM_IMG_URL; ?>shortcode-location-1024x537.jpg" class="content-asset">
            </ol>
        </div>
        <div class="rm-features-row">
            <div class="rm-feature-cell">
                <img src="<?php echo RM_IMG_URL; ?>help.png">
            </div>
            <div class="rm-feature-cell"><?php _e('How to insert a form into a post, page or widget?', 'custom-registration-form-builder-with-submission-manager'); ?></div>
            <div class="rm-feature-cell">
                <img src="<?php echo RM_IMG_URL; ?>next.png">
            </div>

        </div>
        <div class="rm-feature-content">
            <img src="<?php echo RM_IMG_URL; ?>content-arrow.png" class="content-arrow">
            <?php _e('The easiest way to insert a form into content is to paste its shortcode where you want to display the form. Form shortcode is found on the form card. RegistrationMagic form shortcodes are in this format', 'custom-registration-form-builder-with-submission-manager'); ?> <span class="rm-code">[RM_Form id='1']</span>
            <img src="<?php echo RM_IMG_URL; ?>form-card-description.png" class="content-asset">
        </div>

        <div class="rm-features-row">
            <div class="rm-feature-cell">
                <img src="<?php echo RM_IMG_URL; ?>help.png">
            </div>
            <div class="rm-feature-cell"><?php _e('How to display a login box?', 'custom-registration-form-builder-with-submission-manager'); ?></div>
            <div class="rm-feature-cell">
                <img src="<?php echo RM_IMG_URL; ?>next.png">
            </div>

        </div>

        <div class="rm-feature-content">
            <img src="<?php echo RM_IMG_URL; ?>content-arrow.png" class="content-arrow">
            <?php _e('Login system is built into RegistrationMagic. To display a login box on any page, post or widget use this code', 'custom-registration-form-builder-with-submission-manager'); ?> <span class="rm-code">[RM_Login]</span>.
            <img class="content-asset" src="<?php echo RM_IMG_URL; ?>login-box.png">
        </div>

        <div class="rm-features-row">
            <div class="rm-feature-cell">
                <img src="<?php echo RM_IMG_URL; ?>help.png">
            </div>
            <div class="rm-feature-cell"><?php _e('How to allow users to check their submissions?', 'custom-registration-form-builder-with-submission-manager'); ?></div>
            <div class="rm-feature-cell">
                <img src="<?php echo RM_IMG_URL; ?>next.png">
            </div>

        </div>

        <div class="rm-feature-content">
            <img src="<?php echo RM_IMG_URL; ?>content-arrow.png" class="content-arrow">
            <?php _e('RegistrationMagic allows your website users to check the forms they have submitted by logging into the front end area. Shortcode for front-end submission viewing is <span class="rm-code">[RM_Front_Submissions]</span>. When the plugin is first installed a new page is automatically created with this shortcode pasted inside it.', 'custom-registration-form-builder-with-submission-manager'); ?>
                  
        </div>

        <div class="rm-features-row">
            <div class="rm-feature-cell">
                <img src="<?php echo RM_IMG_URL; ?>help.png">
            </div>
            <div class="rm-feature-cell"><?php _e('How to let users check transaction details and status.', 'custom-registration-form-builder-with-submission-manager'); ?></div>
            <div class="rm-feature-cell">
                <img src="<?php echo RM_IMG_URL; ?>next.png">
            </div>

        </div>

        <div class="rm-feature-content"><?php _e('Front-end submission area also contains tab for transaction details. If you have set up paid registration forms, then this area will display to users with their transaction details including payments status as complete or refunded.', 'custom-registration-form-builder-with-submission-manager'); ?></div>
    
        <div class="rm-features-row">
            <div class="rm-feature-cell">
                <img src="<?php echo RM_IMG_URL; ?>help.png">
            </div>
            <div class="rm-feature-cell"><?php _e('Easily display list of registered users', 'custom-registration-form-builder-with-submission-manager'); ?></div>
            <div class="rm-feature-cell">
                <img src="<?php echo RM_IMG_URL; ?>next.png">
            </div>

        </div>
        
        <div class="rm-feature-content">
            <img src="<?php echo RM_IMG_URL; ?>content-arrow.png" class="content-arrow">
            <?php _e('Now you can display registered users\' list on front end of your website! The shortcode format is <span class="rm-code">[RM_Users]</span>. This will display all the users (registered through RegistrationMagic) on a page or post where you paste the shortcode. If you want to display only specific users, you can pass two more parameters to the shortcode - filter the list by registration form and time of registration. The longer format is <span class="rm-code">[RM_Users form_id="id" timerange="year"]</span>. Where id should be replaced by ID for the RegistrationMagic form and timerange can be year, month, week or today. Of course, you can use any of the two depending on your needs. IDs of the form can be found in their respective form shortcodes.', 'custom-registration-form-builder-with-submission-manager'); ?>
            <img src="<?php echo RM_IMG_URL; ?>user-profiles.jpg" class="content-asset">
            <P><?php _e('Please note, this feature is exclusive to RegistrationMagic Premium.', 'custom-registration-form-builder-with-submission-manager'); ?></P>
        </div>
        
        <?php do_action('rm_frontend_primer_content'); ?>
    
    </div>



</div>
<style>
    .rm-new-primer-entry{
        font-size: 10px;
        color: #ff6c6c;
    }
    
</style>

<pre class='rm-pre-wrapper-for-script-tags'><script>
    jQuery(document).ready(function () {
        jQuery(".rm-features-row").click(function () {
            jQuery(this).next().filter(".rm-feature-content").slideToggle("fast");
        });
    });

</script></pre>

<?php } ?>