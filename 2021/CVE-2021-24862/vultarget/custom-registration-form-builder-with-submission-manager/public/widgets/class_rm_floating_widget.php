<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">  
<?php

    /**
     * Adds OTP widget.
     */
    class RM_Floating_Widget {

        /**
         * Register widget with WordPress.
         */
        public $widget_helper;
        public $param;
        public $user_level;
        public $user;
        public $user_role;

        function __construct($param) {
            $this->widget_helper = new RM_Widget_Helper();
            $this->param = $param;
            $this->user_level = $this->widget_helper->get_user_level();
            $this->user = $this->widget_helper->get_user();
        }

        public function show_widget() {
            if(defined('REGMAGIC_ADDON')) {
                $addon_widget = new RM_Floating_Widget_Addon();
                return $addon_widget->show_widget($this);
            }
            $options = new RM_Options();
            $this->include_scripts();
            global $rm_form_diary;
            wp_enqueue_script('rm_front');
            if(current_user_can('manage_options') && $options->get_value_of('hide_magic_panel_styler') != 'yes'){
            ?>
            <!----Color Switcher---->
            <!--noptimize-->
            <div class="rm-white-box difl rm-color-switcher rm-rounded-corners rm-shadow-10" id="rm-color-switcher">
                <div id="rm-theme-box-toggle" style="cursor: pointer; text-align: right; color: grey" onclick="close_theme_box()">x</div>
                <div class="rm-color-switcher-note rm-grey-box dbfl rm-pad-10"><?php _e("Welcome! This sticky panel is only visible to you as site admin. You can style RegistrationMagic's front-end panel on the right side, using the options below.",'custom-registration-form-builder-with-submission-manager') ?></div>
                <div class="rm-color-switch-title dbfl rm-accent-bg rm-pad-10"><?php _e("Magic Panel Styler!",'custom-registration-form-builder-with-submission-manager') ?></div>
                <input type="text" class="dbfl rm-grey-box jscolor" placeholder="<?php _e("Panel Accent Color",'custom-registration-form-builder-with-submission-manager') ?>" id="rm-panel-accent">
                <select class="dbfl" id="rm-panel-theme">
                    <option value="Light"><?php echo RM_UI_Strings::get('LABEL_LIGHT'); ?></option>
                    <option value="Dark"><?php echo RM_UI_Strings::get('LABEL_DARK'); ?></option>
                </select>
                <button class="difl" id="rm-color-switch"><?php echo RM_UI_Strings::get('LABEL_SWITCH'); ?></button>
            </div>
            <?php
            }
            ?>

            <div class="rm-magic-popup">
                <div class="rm-popup-menu rm-border rm-white-box dbfl" id="rm-menu" style="display:none">
                    <?php
                    if($this->user_level === 0x4){
                    ?>
                    <div class="rm-popup-item rm-popup-welcome-box dbfl rm-border" id="rm-account-open">
                        
                        <?php 
                            $av = get_avatar_data($this->user->ID); 
                            $profile_image_url = apply_filters('rm_profile_image',$av['url'],$this->user->ID);
                        ?>
                        <img class="rm-menu-userimage difl" src="<?php echo $profile_image_url; ?>">
                        <div class="rm-menu-user-details difl">
                           
                            <?php if(!empty($this->user->first_name) &&  !empty($this->user->last_name)): ?>
                                    <div class="dbfl"><?php echo $this->user->first_name.' '.$this->user->last_name; ?></div>
                                <?php elseif(!empty($this->user->first_name) &&  empty($this->user->last_name)): ?>
                                    <div class="dbfl"><?php echo $this->user->first_name; ?></div>
                                <?php else: ?>
                                    <div class="dbfl"> </span> <?php echo $this->user->display_name; ?></div>
                                 <?php endif; ?>
                        </div>
                    </div>
                     <?php
                    }
                        if ($this->user_level === 0x1) {
                            ?>
                    <div class="rm-popup-item dbfl" id="rm-login-open"><?php echo RM_UI_Strings::get('LABEL_LOGIN'); ?></div>
                    
                    <?php 
                    if(!isset($rm_form_diary[$this->param->default_form])){
                    ?>
                    <div class="rm-popup-item dbfl" id="rm-register-open-big"><?php echo RM_UI_Strings::get('LABEL_REGISTER'); ?></div>
                    <?php }
                    else {
                    ?>
                    <a href="#form_<?php echo $this->param->default_form;?>_1" id="rm_fab_register_redirect_link"><div class="rm-popup-item dbfl" id="rm-register-open-big"><?php echo RM_UI_Strings::get('LABEL_REGISTER'); ?></div></a>
                     <?php    
                    }
                   
                        }
                        ?>
                    <div class="rm-popup-item dbfl" id="rm-submissions-open"><?php echo RM_UI_Strings::get('LABEL_MY_SUBS'); ?></div>
                    <div class="rm-popup-item dbfl" id="rm-transactions-open"><?php echo RM_UI_Strings::get('LABEL_PAYMENTS'); ?></div>
                    <div class="rm-popup-item dbfl" id="rm-account-open"><?php echo RM_UI_Strings::get('LABEL_MY_DETAILS'); ?></div>
                    <?php if($this->user_level !== 0x1 && !is_user_logged_in()){ ?>
                    <div class="rm-popup-item dbfl rm-popup-item-log-off" id="rm_log_off" onclick="document.getElementById('rm_floating_btn_nav_form').submit()"><?php echo RM_UI_Strings::get('LABEL_LOG_OFF'); ?></div>
                   
                    <form method="post" id="rm_floating_btn_nav_form">
                       <input type="hidden" name="rm_slug" value="rm_front_log_off">
                       <input type="hidden" name="rm_do_not_redirect" value="true">
                   </form>
                    <?php } 
                    elseif(is_user_logged_in()){
                        ?>
                    <div class="rm-popup-item dbfl rm-popup-item-log-off" id="rm_log_off"><a href="<?php echo wp_logout_url(); ?>"><?php echo RM_UI_Strings::get('LABEL_LOG_OFF'); ?></a></div>
                    <?php
                    }
?>
                     <span class="rm-magic-popup-nub"></span>
                </div>
                <div class="rm-magic-popup-button rm-accent-bg rm-shadow-10 dbfl" id="rm-popup-button">
                    <img src="<?php echo $this->widget_helper->get_fab_icon(); ?>">
                    <span class="rm-magic-popup-close-button" style="display: none;"><i class="material-icons">&#xE5CD;</i></span>
                </div>
            </div>

            <div class="rm-floating-page rm-shadow-10 dbfl" id="rm-panel-page">
                <div class="rm-floating-page-top rm-border rm-white-box dbfl"><?php echo RM_UI_Strings::get('LABEL_MY_SUBS'); ?></div>
                <div class="rm-floating-page-content dbfl">

                    <!----Login Panel---->
                    <div class="rm-floating-page-login dbfl" id="rm-login-panel">
                        <?php $this->widget_helper->getLoginForm(); ?>
                    </div>
                    <!--Registration Form-->
                    <div class="dbfl" id="rm-register-panel-big">
                        <?php if ($this->param->default_form > 0 && !is_user_logged_in()) {                           
                                
                            echo do_shortcode("[RM_Form force_enable_multiform='true' id='".$this->param->default_form."']");       
                        } else {
                            echo "<div class='rm-no-default-from-notification'>". RM_UI_Strings::get('NO_DEFAULT_FORM')."</div>";
                        }
                        ?>
                    </div>

                    <!----Panel page submissions area---->
                    <div class="dbfl" id="rm-submissions-panel">
                        <?php
                        if ($this->user_level !== 0x1)
                            $this->widget_helper->getSubmissions();
                        else
                            echo "<div class='rm-no-default-from-notification'>".RM_UI_Strings::get('MSG_PLEASE_LOGIN_FIRST')."</div>";
                        ?>
                    </div>

                    <!--------User Transaction Panel------->
                    <div class="dbfl" id="rm-transactions-panel">
                        <?php
                        if ($this->user_level !== 0x1)
                            $this->widget_helper->getPayments();
                        else
                            echo "<div class='rm-no-default-from-notification'>".RM_UI_Strings::get('MSG_PLEASE_LOGIN_FIRST')."</div>";
                        ?>

                    </div>

                    <!----User Account Page---->
                    <div class="dbfl" id="rm-account-panel">
                        <?php
                        if ($this->user_level !== 0x1)
                            $this->widget_helper->get_account();
                        else
                            echo "<div class='rm-no-default-from-notification'>".RM_UI_Strings::get('MSG_PLEASE_LOGIN_FIRST')."</div>";
                        ?>
                        
                    </div>
                    
                </div>

                <div class="rm-floating-page-bottom rm-border rm-white-box dbfl">
                    <button class="rm-rounded-corners rm-button" id="rm-panel-close"><?php echo RM_UI_Strings::get('LABEL_FIELD_ICON_CLOSE'); ?></button>
                </div>

            </div>
            <!--/noptimize-->
            <?php
        }

        public function include_scripts() {
            $options = new RM_Options();
            $fab_color = $options->get_value_of('fab_color');
            $fab_theme = $options->get_value_of('fab_theme');
            ?>
            <!--noptimize-->
            <link rel="stylesheet" href="<?php echo RM_BASE_URL; ?>public/css/floating-button.css" type="text/css">
            <?php if(defined('REGMAGIC_ADDON'))
                echo '<link rel="stylesheet" href="' . RM_ADDON_BASE_URL . 'public/css/floating-button.css" type="text/css">';
            ?>
            <pre class="rm-pre-wrapper-for-script-tags"><script type="text/javascript">
                var rm_fab_theme = '<?php echo $fab_theme; ?>';
                var rm_fab_color = '<?php echo $fab_color; ?>';
                var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
                var floating_js_vars= {greetings: {morning: '<?php _e("Good Morning",'custom-registration-form-builder-with-submission-manager') ?>',evening:'<?php _e("Good Evening",'custom-registration-form-builder-with-submission-manager') ?>',afternoon: '<?php _e("Good Afternoon",'custom-registration-form-builder-with-submission-manager') ?>'}};
            </script></pre>
            <pre class="rm-pre-wrapper-for-script-tags"><script src="<?php echo RM_BASE_URL; ?>public/js/modernizr-custom.min.js" type="text/javascript"></script></pre>
            <pre class="rm-pre-wrapper-for-script-tags"><script src="<?php echo RM_BASE_URL; ?>admin/js/jscolor.min.js" type="text/javascript"></script></pre>
            <pre class="rm-pre-wrapper-for-script-tags"><script src="<?php echo defined('REGMAGIC_ADDON') ? RM_ADDON_BASE_URL : RM_BASE_URL; ?>public/js/floating-button.js" type="text/javascript"></script></pre>
            <!--/noptimize-->
            <?php
        }

    }

    // class Foo_Widget
    ?>