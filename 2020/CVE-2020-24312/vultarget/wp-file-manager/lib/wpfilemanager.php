<?php if (!defined('ABSPATH')) {
        exit;
    } 
     $current_user = wp_get_current_user(); 
     $wp_fm_lang = get_transient('wp_fm_lang');
     $wp_fm_theme = get_transient('wp_fm_theme');
     $opt = get_option('wp_file_manager_settings');
    ?>
    <script>
    var vle_nonce = "<?php echo wp_create_nonce('verify-filemanager-email');?>";
    </script>
    <div class="wrap wp-filemanager-wrap">
    <?php
    $this->load_custom_assets(); 
    $this->load_help_desk();
    ?>

        <link href="https://fonts.googleapis.com/css?family=Raleway:400,700,900" rel="stylesheet">

        <div class="wp_fm_lang" style="float:left">
            <h3 class="fm_heading"><span class="fm_head_icon"><img src="<?php echo plugins_url('images/wp_file_manager-color.png', dirname(__FILE__)); ?>"></span>
                <span class="fm_head_txt">
                    <?php _e('WP File Manager', 'wp-file-manager'); ?> </span> <a href="https://filemanagerpro.io/product/file-manager"
                    class="button button-primary fm_pro_btn" target="_blank" title="Click to Buy PRO">
                    <?php _e('Buy PRO', 'wp-file-manager'); ?></a></h3>
        </div>

        <div class="wp_fm_lang" style="float:right">
            <h3 class="fm-topoption">

                <span class="switch_txt_theme">Change Theme Here:</span>

                <select name="theme" id="fm_theme">
                    <option value="default" <?php echo (isset($_GET['theme']) && $_GET['theme'] == 'default') ? 'selected="selected"' : (($wp_fm_theme !== false) && $wp_fm_theme == 'default' ? 'selected="selected"' : ''); ?>>
                        <?php _e('Default', 'wp-file-manager'); ?>
                    </option>
                    <option value="dark" <?php echo (isset($_GET['theme']) && $_GET['theme'] == 'dark') ?
                        'selected="selected"' : (($wp_fm_theme !== false) && $wp_fm_theme == 'dark' ? 'selected="selected"' : ''); ?>>
                        <?php _e('Dark', 'wp-file-manager'); ?>
                    </option>
                    <option value="light" <?php echo (isset($_GET['theme']) && $_GET['theme'] == 'light') ?
                        'selected="selected"' : (($wp_fm_theme !== false) && $wp_fm_theme == 'light' ? 'selected="selected"' : ''); ?>>
                        <?php _e('Light', 'wp-file-manager'); ?>
                    </option>
                    <option value="gray" <?php echo (isset($_GET['theme']) && $_GET['theme'] == 'gray') ?
                        'selected="selected"' : (($wp_fm_theme !== false) && $wp_fm_theme == 'gray' ? 'selected="selected"' : ''); ?>>
                        <?php _e('Gray', 'wp-file-manager'); ?>
                    </option>
                    <option value="windows - 10" <?php echo (isset($_GET['theme']) && $_GET['theme'] == 'windows - 10') ?
                        'selected="selected"' : (($wp_fm_theme !== false) && $wp_fm_theme == 'windows - 10' ?
                        'selected="selected"' : ''); ?>>
                        <?php _e('Windows - 10', 'wp-file-manager'); ?>
                    </option>
                </select>
                <select name="lang" id="fm_lang">
                    <?php foreach ($this->fm_languages() as $name => $lang) {
                            ?>
                    <option value="<?php echo $lang; ?>" <?php echo (isset($_GET['lang']) && $_GET['lang'] == $lang) ?
                        'selected="selected"' : (($wp_fm_lang !== false) && $wp_fm_lang == $lang ? 'selected="selected"' : ''); ?>>
                        <?php echo $name; ?>
                    </option>
                    <?php
                        }?>
                </select></h3>
        </div>
        <div style="clear:both"></div>
        <div id="wp_file_manager">
            <center><img src="<?php echo plugins_url('images/loading.gif', dirname(__FILE__)); ?>" class="wp_fm_loader" /></center>
        </div>


        <?php ///***** Verify Lokhal Popup Start *****///
          //delete_transient( 'filemanager_cancel_lk_popup_'.$current_user->ID );
        if (false === get_option('filemanager_email_verified_'.$current_user->ID) && (false === (get_transient('filemanager_cancel_lk_popup_'.$current_user->ID)))) {
        ?>
        <div id="lokhal_verify_email_popup" class="lokhal_verify_email_popup">
            <div class="lokhal_verify_email_popup_overlay"></div>
            <div class="lokhal_verify_email_popup_tbl">
                <div class="lokhal_verify_email_popup_cel">
                    <div class="lokhal_verify_email_popup_content">
                        <a href="javascript:void(0)" class="lokhal_cancel"> <img src="<?php echo plugins_url('lib/img/fm_close_icon.png', dirname(__FILE__)); ?>"
                                class="wp_fm_loader" /></a>
                        <div class="popup_inner_lokhal">
                            <h3>
                                <?php _e('Welcome to File Manager', 'wp-file-manager'); ?>
                            </h3>
                            <p class="lokhal_desc">
                                <?php _e('We love making new friends! Subscribe below and we promise to
    keep you up-to-date with our latest new plugins, updates,
    awesome deals and a few special offers.', 'wp-file-manager'); ?>
                            </p>
                            <form>
                                <div class="form_grp">
                                    <div class="form_twocol">
                                        <input name="verify_lokhal_fname" id="verify_lokhal_fname" class="regular-text"
                                            type="text" value="<?php echo (null == get_option('verify_filemanager_fname_'.$current_user->ID)) ? $current_user->user_firstname : get_option('verify_filemanager_fname_'.$current_user->ID); ?>"
                                            placeholder="First Name" />
                                        <span id="fname_error" class="error_msg">
                                            <?php _e('Please Enter First Name.', 'wp-file-manager'); ?></span>
                                    </div>
                                    <div class="form_twocol">
                                        <input name="verify_lokhal_lname" id="verify_lokhal_lname" class="regular-text"
                                            type="text" value="<?php echo (null ==
            get_option('verify_filemanager_lname_'.$current_user->ID)) ? $current_user->user_lastname : get_option('verify_filemanager_lname_'.$current_user->ID); ?>"
                                            placeholder="Last Name" />
                                        <span id="lname_error" class="error_msg">
                                            <?php _e('Please Enter Last Name.', 'wp-file-manager'); ?></span>
                                    </div>
                                </div>
                                <div class="form_grp">
                                    <div class="form_onecol">
                                        <input name="verify_lokhal_email" id="verify_lokhal_email" class="regular-text"
                                            type="text" value="<?php echo (null == get_option('filemanager_email_address_'.$current_user->ID)) ? $current_user->user_email : get_option('filemanager_email_address_'.$current_user->ID); ?>"
                                            placeholder="Email Address" />
                                        <span id="email_error" class="error_msg">
                                            <?php _e('Please Enter Email Address.', 'wp-file-manager'); ?></span>
                                    </div>
                                </div>
                                <div class="btn_dv">
                                    <button class="verify verify_local_email button button-primary "><span class="btn-text"><?php _e('Verify', 'wp-file-manager'); ?>
                                        </span>
                                        <span class="btn-text-icon">
                                            <img src="<?php echo plugins_url('images/btn-arrow-icon.png', dirname(__FILE__)); ?>" />
                                        </span></button>
                                    <button class="lokhal_cancel button">
                                        <?php _e('No Thanks', 'wp-file-manager'); ?></button>
                                </div>
                            </form>
                        </div>
                        <div class="fm_bot_links">
                            <a href="http://ikon.digital/terms.html" target="_blank">
                                <?php _e('Terms of Service', 'wp-file-manager'); ?></a> <a href="http://ikon.digital/privacy.html"
                                target="_blank">
                                <?php _e('Privacy Policy', 'wp-file-manager'); ?></a>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <?php
   } ///***** Verify Lokhal Popup End *****///?>


    </div>

    <div class="fm_msg_popup">
        <div class="fm_msg_popup_tbl">
            <div class="fm_msg_popup_cell">
                <div class="fm_msg_popup_inner">
                    <div class="fm_msg_text">
                        <?php _e('Saving...', 'wp-file-manager'); ?>
                    </div>
                    <div class="fm_msg_btn_dv"><a href="javascript:void(0)" class="fm_close_msg button button-primary"><?php _e('OK', 'wp-file-manager'); ?></a></div>
                </div>
            </div>
        </div>
    </div>