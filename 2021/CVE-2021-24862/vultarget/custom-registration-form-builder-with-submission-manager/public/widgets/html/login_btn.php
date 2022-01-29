<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_PUBLIC_DIR . 'widgets/html/login_btn.php'); else {
if(is_user_logged_in()): ?>
    <?php /*if($instance['display_card']==1){?> onmouseout="jQuery('#rm_login_widget_front').hide();" <?php }*/ ?>
    <div class="rm_widget_container" >
        <div id="<?php echo $this->get_field_name('logout_btn'); ?>">
            <div class="rm-logout-widget">
                <a class="rm-button" href="<?php echo wp_logout_url() ?>"><?php echo $instance['logout_label']; ?> </a>
                
                <?php if($instance['display_card']==1): ?>
                <div style="display: none;" id="rm_login_widget_front">
                    <span class="rm_login_widget_nub"></span>
                    <?php echo do_shortcode('[RM_Login]'); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
      
    </div>
<?php else: ?>
    <div class="rm_widget_container">
            <!-- Login pop up -->
            
            <?php if($instance['login_method']=='popup'): ?>
            <span class="rm-login-widget-wrap">
                <div id="<?php echo $this->get_field_name('login_btn'); ?>">
                     <a class="rm-button"  onclick="jQuery(this).closest('.rm_widget_container').find('.rm-login-popup').toggle();"><?php echo $instance['login_label']; ?></a>
                </div>
                <div class="rm-login-popup" style="display:none" id="rm-login-widget">
                    <div class="rm_login_widget-container">
                    <span class="rm_login_widget_nub"></span>
                    <div  class="rm_login_widget-close" onclick="jQuery(this).closest('.rm_widget_container').find('.rm-login-popup').toggle();"></div>
                    <div class="rm_login_widget-wrap"><?php echo do_shortcode('[RM_Login btn_widget="1"]'); ?></div>
                    </div></div>
            </span>
            <?php else: ?>
                <div>
                    <a class="rm-button" href="<?php echo get_permalink($instance['login_url']) ?>"><?php echo $instance['login_label']; ?></a>
                </div>
            <?php endif; ?>
    </div>
<?php endif; ?>

<?php if(isset($_REQUEST['login_popup_show']) && $_REQUEST['login_popup_show']==1): ?>
<script>
jQuery(document).ready(function () {
    jQuery('.widget_rm_login_btn_widget .rm_widget_container').find('.rm-login-popup').toggle();
});
</script>
<?php endif; } ?>