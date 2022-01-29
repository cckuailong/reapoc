<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_PUBLIC_DIR . 'views/template_rm_logged_in_view.php'); else {

        $user= wp_get_current_user();
        if(empty($user))
            return;
        $description= get_user_meta($user->ID, 'description', true);
        $service= new RM_Login_Service();
        $gopt= new RM_Options();
        $my_account= $gopt->get_value_of('front_sub_page_id');
        $view_options= $service->get_login_view_options();
       // print_r($view_options);
?>
<div class="rm-logged-in-view">
    <div class="rm-logged-in-view-wrap">
        <!-- User avatar -->
        <?php /* ?>
        <div class="rm-logged-in-lf" <?php echo (!empty($view_options['display_user_avatar']))?'':'id="rm-hide-user_avatar"' ?>>
            <?php echo get_avatar($user->user_email); ?>
        </div>
        <?php */ ?>
        <?php
        if(empty($view_options['display_user_avatar']) && empty($view_options['display_user_name']) && empty($view_options['display_greetings']) && empty($view_options['display_custom_msg']) && empty($view_options['display_account_link'])): ?>
        <?php if(!empty($view_options['display_logout_link'])): ?>
        <div class="rm_display_only_logout"><a href="<?php echo wp_logout_url(); ?>"><?php echo empty($view_options['logout_text']) ? RM_UI_Strings::get('LABEL_LOG_OFF') : $view_options['logout_text']; ?></a></div>
        <?php endif; ?> 
        <?php else: ?>
        <?php if(!empty($view_options['display_user_avatar'])): ?>
        <div class="rm-logged-in-lf">
            <?php echo !empty(get_avatar($user->user_email)) ? get_avatar($user->user_email) : '<img src="'.RM_IMG_URL.'default_person.png">'; ?>
        </div>
        <?php endif; ?>
                
        <div class="rm-logged-in-rf">
        
            <div class="rm-logged-welcome">
                <!-- User greeting message -->
                <?php if(!empty($view_options['display_greetings'])): ?>
                <span class="rm-greetings-text"><?php echo empty($view_options['greetings_text']) ? __('Welcome','custom-registration-form-builder-with-submission-manager') : $view_options['greetings_text']; ?></span>
                <?php endif; ?>

                <!-- Show user name -->
                <?php if(!empty($view_options['display_user_name'])): ?>
                    <?php if(!empty($user->user_firstname) && !empty($user->user_lastname)): ?>
                             <span class="rm-user-last-name"><?php echo $user->user_firstname.' '.$user->user_lastname; ?></span>
                     <?php elseif(!empty($user->user_firstname) && empty($user->user_lastname)): ?>  
                       <span class="rm-user-first-name"><?php echo $user->user_firstname; ?></span>
                     <?php elseif(!empty($user->user_lastname) && empty($user->user_firstname)) : ?>  
                       <span class="rm-user-last-name"><?php echo $user->user_lastname; ?></span>
                     <?php else: ?>
                        <span class="rm-user-last-name"><?php echo $user->display_name; ?></span>
                     <?php endif; ?> 
                <?php endif; ?>
            </div>
   
    
            <!-- User bio description -->
            <?php /* if(!empty($description)) : ?>
                <div><?php echo $description; ?></div>
            <?php endif; */?>
       
            <!-- Login custom message -->
            <?php if(!empty($view_options['display_custom_msg']) && !empty($view_options['custom_msg'])): ?>
            <div class="rm_display_custom_msg"><?php echo $view_options['custom_msg']; ?></div>
            <?php endif; ?>   
        
        </div> 
    
        
        
        <div class="rm-logged-in-account-links" style="border-color: #<?php echo $view_options['separator_bar_color']; ?>">
            <!-- My Account -->
            <?php if(!empty($view_options['display_account_link'])): ?>
                    <div class="rm_display_account"><a href="<?php echo get_permalink($my_account); ?>"><?php echo empty($view_options['account_link_text']) ? __('My Account', 'custom-registration-form-builder-with-submission-manager')  : $view_options['account_link_text']; ?></a></div>
            <?php endif; ?>

            <!-- Logout -->  
            <?php if(!empty($view_options['display_logout_link'])): ?>
            <div class="rm_display_logout"><a href="<?php echo wp_logout_url(); ?>"><?php echo empty($view_options['logout_text']) ? RM_UI_Strings::get('LABEL_LOG_OFF') : $view_options['logout_text']; ?></a></div>
            <?php endif; ?>    
        </div>
        <?php endif; ?>
    </div>  
</div>

<?php } ?>