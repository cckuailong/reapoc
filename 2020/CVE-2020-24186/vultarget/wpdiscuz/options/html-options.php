<?php
if (!defined("ABSPATH")) {
    exit();
}
$tab = isset($_GET["wpd_tab"]) ? sanitize_key($_GET["wpd_tab"]) : false;
$wizard = isset($_GET["wpd_wizard"]) ? absint($_GET["wpd_wizard"]) : 0;
if (!$wizard && !intval(get_option(self::OPTION_SLUG_WIZARD_COMPLETED))) {
    $wizard = 1;
}
$optionsObject = $this;
$settings = $this->settingsArray();
?>

<div id="wpd-setbox" class="wrap wpd-dash">
    <!-- wpd-setbox-head start -->
    <div class="wpd-setbox-head">
        <div class="wpd-head-logo">
            <img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/wpdiscuz-7-logo.png")); ?>" />
        </div>
        <div class="wpd-head-title">
            <?php esc_html_e("wpDiscuz", "wpdiscuz") ?>
        </div>
        <div class="wpd-head-info">
            <span><a href="https://wpdiscuz.com/docs/wpdiscuz-7/" target="_blank"><?php esc_html_e("Documentation", "wpdiscuz"); ?></a></span>
            <span><a href="https://wpdiscuz.com/support/" target="_blank"><?php esc_html_e("Support", "wpdiscuz"); ?></a></span>
            <span><a href="https://gvectors.com/product-category/wpdiscuz/" target="_blank"><?php esc_html_e("Addons", "wpdiscuz"); ?></a></span>
        </div>
    </div>
    <h1 style="width:0;height:0;margin:0;padding:0;"></h1>
    <?php do_action("wpdiscuz_option_page"); ?>
    <?php settings_errors("wpdiscuz"); ?>
    <!-- wpd-setbox-head end -->
    <?php
    if ($wizard) {
        ?>
        <style>
            .wpd-wizard{margin: 50px 60px;background: #FFF; box-shadow: 0px 0px 2px #bbb; padding: 20px 20px 30px 20px; border-radius: 10px;}
            .wpd-wizard-title{font-weight: normal; color: #555; font-size: 30px; text-align: center; line-height: 30px; margin: 15px auto 40px auto;}
            .wpd-wizard-bar{width: 600px; margin: 20px auto;}
            .wpd-wizard-steps{position: relative; z-index: 1; display: flex; justify-content: center; flex-direction: row; align-items: end;}
            .wpd-wizard-step{margin-bottom: -10px; width: 33%;}
            .wpd-wizard-progress{height: 5px; background-color: #07B290; border-radius: 5px;}
            .wpd-wizard-step-title{font-size: 18px; font-weight: normal; color: #0e8e75; text-align: center;}
            .wpd-wizard-step-point{width: 15px; height: 15px; border-radius: 10px; background-color: #07B290; margin: 10px auto 0px auto; display: flex; justify-content: center; align-items: center;}
            .wpd-wiz-active .wpd-wizard-current{width: 7px; height: 7px; border-radius: 5px; background-color: #fff;}
            .wpd-wizard-body{width:96%; margin: 0px auto 10px auto; box-sizing: border-box; padding: 20px 40px 1px 40px;}
            .wpd-wizard-step-name{font-size: 18px; font-weight: normal; margin: 0px 0px 20px 0px; color: #0e8e75;background: #f5f5f5; padding: 12px 20px;}
            .wpd-wizard-opt .wpd-switch-field input{display: none;}
            .wpd-wizard-opt .wpd-switch-field label{float: left; display: inline-block; line-height: 20px; min-width:60px; font-weight: normal; background-color: #e4e4e4;color:#333;font-size: 13px;text-align: center; text-shadow: none;padding: 2px 10px; border: 1px solid rgba(0, 0, 0, 0.2);-webkit-box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.3), 0 1px rgba(255, 255, 255, 0.1);box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.3), 0 1px rgba(255, 255, 255, 0.1);-webkit-transition: all 0.1s ease-in-out;-moz-transition:    all 0.1s ease-in-out; -ms-transition: all 0.1s ease-in-out; -o-transition: all 0.1s ease-in-out;transition: all 0.1s ease-in-out;}
            .wpd-wizard-opt .wpd-switch-field label:hover {cursor: pointer;}
            .wpd-wizard-opt .wpd-switch-field input:checked + label{background-color: #07B290; -webkit-box-shadow: none; box-shadow: none; color:#fff;} /* #66DD8D */
            .wpd-wizard-opt .wpd-switch-field label:first-of-type{border-radius: 4px 0 0 4px;}
            .wpd-wizard-opt .wpd-switch-field label:last-of-type{border-radius: 0 4px 4px 0;}
            .wpd-wizard-opt{ border-bottom: 2px solid #eee; display: flex; flex-direction: row; justify-content: space-between; align-items: center; padding: 10px 0px; margin-top: 10px;}
            .wpd-wizard-opt .wpd-wizard-opt-label{width: 50%; font-size: 15px; font-weight: 600; text-align: right; border-right: 2px solid #DDD; padding: 0px 20px;}
            .wpd-wizard-opt .wpd-wizard-opt-option{width: 50%; display: flex; justify-content: flex-start; flex-direction: row; padding: 0px 20px;}
            .wpd-wizard-buttons{text-align: center;}
            .wpd-wizard-button{box-shadow: 1px 1px 13px #bbb; background: #07B290; cursor: pointer; color: #fff; font-size: 20px; font-weight: normal; border: none; padding: 10px 40px; border-radius: 3px;}
            .wpd-wizard-button:hover{box-shadow: 1px 1px 20px #07B290;background: #0e8e75;}
            a.wpd-wizard-button:hover{box-shadow: 1px 1px 20px #07B290;background: #0e8e75;color: #fff;text-decoration: none!important;}
            .wpd-wizard-note{display: block; margin-bottom: 20px; background-color: #f9ffc6; border: 1px dashed #ff6e2d; padding: 15px 30px 20px 30px;}
            .wpd-wizard-note h3{margin: 5px 0 0 0; text-align: center; font-weight: normal; font-size: 18px; color: #ee0004;}
            .wpd-wizard-note h3 img{height: 20px; vertical-align: bottom;}
            .wpd-wizard-note p{margin: 10px 0 0 0; text-align: justify; font-weight: normal; font-size: 14px; font-family: 'Lucida Bright', 'DejaVu Serif', Georgia, serif;}
            .wpd-wizard-help{background-color: #fafafa; padding: 0; border: 1px dashed #ccc; display: flex; flex-direction: row; justify-content: space-between; align-items: stretch; margin-bottom: 40px;}
            .wpd-wizard-help h3{margin: 5px 0 0 0; text-align: center; font-weight: normal; font-size: 18px;}
            .wpd-wizard-help h3 img{height: 20px; vertical-align: bottom;}
            .wpd-wizard-help p{margin: 10px 0 0 0; text-align: justify; font-weight: normal; font-size: 14px; font-family: 'Lucida Bright', 'DejaVu Serif', Georgia, serif;}
            .wpd-wizard-section{margin-top: 0px; padding-bottom: 20px; font-size: 14px; line-height: 1.5; font-family: 'Lucida Bright', 'DejaVu Serif', Georgia, serif;}
            .wpd-wizard-section-title{margin: 10px 0px; padding: 0px 10px 7px 10px; border-bottom: 1px dashed #ccc; font-size: 18px;line-height: 1.6; font-family: 'Lucida Bright', 'DejaVu Serif', Georgia, serif;}
        </style>

        <div class="wpd-wizard">
            <?php
            if (in_array($wizard, [1, 2, 3])) {
                ?>
                <form method="post" action="<?php echo admin_url("admin.php?page=" . WpdiscuzCore::PAGE_SETTINGS . "&wpd_wizard=" . ($wizard + 1)); ?>">
                    <p class="wpd-wizard-title">
                        <?php intval(get_option(self::OPTION_SLUG_WIZARD_AFTER_UPDATE)) ? esc_html_e("Just 3 Steps to Complete Update!", "wpdiscuz") : esc_html_e("Just 3 Steps to Complete Installation!", "wpdiscuz"); ?>
                    </p>
                    <div class="wpd-wizard-bar">
                        <div class="wpd-wizard-steps">
                            <div class="wpd-wizard-step <?php echo $wizard === 1 ? "wpd-wiz-active" : ""; ?>">
                                <div class="wpd-wizard-step-title">
                                    <?php esc_html_e("Step 1", "wpdiscuz") ?>
                                </div>
                                <div class="wpd-wizard-step-point">
                                    <div class="wpd-wizard-current"></div>
                                </div>
                            </div>
                            <div class="wpd-wizard-step <?php echo $wizard === 2 ? "wpd-wiz-active" : ""; ?>">
                                <div class="wpd-wizard-step-title">
                                    <?php esc_html_e("Step 2", "wpdiscuz") ?>
                                </div>
                                <div class="wpd-wizard-step-point">
                                    <div class="wpd-wizard-current"></div>
                                </div>
                            </div>
                            <div class="wpd-wizard-step <?php echo $wizard === 3 ? "wpd-wiz-active" : ""; ?>">
                                <div class="wpd-wizard-step-title">
                                    <?php esc_html_e("Step 3", "wpdiscuz") ?>
                                </div>
                                <div class="wpd-wizard-step-point">
                                    <div class="wpd-wizard-current"></div>
                                </div>
                            </div>
                        </div>
                        <div class="wpd-wizard-progress"></div>
                    </div>
                    <div class="wpd-wizard-body">
                        <?php
                        if ($wizard === 1) {
                            $notice = false;
                            $customizedDir = "";
                            $wpd_addon_fix = ( isset($_GET['wpd_addon_fix']) && $_GET['wpd_addon_fix'] ) ? 1 : 0;
                            if (is_dir(get_stylesheet_directory() . "/wpdiscuz")) {
                                $customizedDir = get_stylesheet_directory() . "/wpdiscuz";
                            } else if (is_dir(get_template_directory() . "/wpdiscuz")) {
                                $customizedDir = get_template_directory() . "/wpdiscuz";
                            }
                            if ($customizedDir && count(scandir($customizedDir)) > 2) {
                                $notice = true;
                                ?>
                                <!-- Wizard Note - start -->
                                <div class="wpd-wizard-note">
                                    <h3><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/warning.png")); ?>"/> <?php esc_html_e("Custom wpDiscuz template files are detected!", "wpdiscuz"); ?></h3>
                                    <p>
                                        <span class="dashicons dashicons-welcome-comments"></span> <strong><?php esc_html_e("Information: ", "wpdiscuz") ?></strong>
                                        <?php esc_html_e("Your customized wpDiscuz template files are no longer compatible with wpDiscuz 7.  This is a doable major version update (from 5.x to 7.x) with totally redesigned comment system and template files. You can do the same customization on the new wpDiscuz 7 template files with the same upgrade-safe way in case the new comment layouts don't fit your needs. Please find those below.", "wpdiscuz"); ?>
                                    </p>
                                    <p>
                                        <span class="dashicons dashicons-yes-alt"></span> <strong><?php esc_html_e("Fix the problem: ", "wpdiscuz") ?></strong>
                                        <span style="color: #ee0004"><?php esc_html_e("Please remove wpDiscuz template files from your active theme's /wpdiscuz/ folder.", "wpdiscuz") ?></span>
                                        <?php esc_html_e("Use FTP client or hosting service cPanel > File Manager tool. WordPress theme folders are located in /wp-content/themes/ directory. The active theme folder can be detected by name.", "wpdiscuz"); ?>
                                    </p>
                                    <p style="text-align: right; margin-top: -10px;"><a href="<?php echo esc_url_raw(admin_url("admin.php?page=" . WpdiscuzCore::PAGE_SETTINGS)) . "&wpd_wizard=1&wpd_addon_fix=" . $wpd_addon_fix ?>" class="button" style="text-decoration: none;"><?php _e('Mark as solved', 'wpdiscuz') ?></a></p>
                                </div>
                                <!-- Wizard Note - end -->
                                <?php
                            }
                            if (intval(get_option(self::OPTION_SLUG_WIZARD_SHOW_ADDONS_MSG)) && !isset($_GET['wpd_addon_fix'])) {
                                $notice = true;
                                ?>
                                <!-- Wizard Note - start -->
                                <div class="wpd-wizard-note">
                                    <h3><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/warning.png")); ?>"/> <?php esc_html_e("All wpDiscuz Addons are Deactivated!", "wpdiscuz"); ?></h3>
                                    <p>
                                        <span class="dashicons dashicons-welcome-comments"></span> <strong><?php esc_html_e("Information: ", "wpdiscuz") ?></strong>
                                        <?php esc_html_e("All old versions of addons are not compatible with wpDiscuz 7, because of new comment layouts and template functions. In order to avoid errors wpDiscuz deactivates those during the update process.", "wpdiscuz") ?>
                                    </p>
                                    <p>
                                        <span class="dashicons dashicons-yes-alt"></span> <strong><?php esc_html_e("Fix the problem: ", "wpdiscuz") ?></strong>
                                        <span style="color: #ee0004"><?php esc_html_e("Please update wpDiscuz addons in Dashboard > Plugins admin page, then activate those back.", "wpdiscuz"); ?></span>
                                        <?php esc_html_e("Prior to the wpDiscuz 7 release, we've released new versions of all wpDiscuz addons. If you've already using the latest versions, just activate those back. If your license key is expired and you cannot update, please renew those addons at gVectors Store with 30% discount applied automatically at checkout page. Just make sure you're logged-in in the store with your customer account.", "wpdiscuz"); ?>
                                    </p>
                                    <p style="text-align: right; margin-top: -10px;"><a href="<?php echo esc_url_raw(admin_url("admin.php?page=" . WpdiscuzCore::PAGE_SETTINGS)) . "&wpd_wizard=1&wpd_addon_fix=1" ?>" class="button" style="text-decoration: none;"><?php _e('Ok, I understood', 'wpdiscuz') ?></a></p>
                                </div>
                                <!-- Wizard Note - end -->
                                <?php
                            }
                            if (class_exists("Jetpack") && Jetpack::is_module_active("comments")) {
                                $notice = true;
                                ?>
                                <!-- Wizard Note - start -->
                                <div class="wpd-wizard-note">
                                    <h3><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/warning.png")); ?>"/> <?php esc_html_e("Jetpack comments is active, please deactivate!", "wpdiscuz"); ?></h3>
                                    <p>
                                        <span class="dashicons dashicons-welcome-comments"></span> <strong><?php esc_html_e("Information: ", "wpdiscuz") ?></strong> <?php esc_html_e("Jetpack Comments doesn't allow wpDiscuz comment form to be loaded on your posts and pages. It overwrites wpDiscuz Comment form.", "wpdiscuz"); ?>
                                    </p>
                                    <p>
                                        <span class="dashicons dashicons-yes-alt"></span> <strong><?php esc_html_e("Fix the problem: ", "wpdiscuz") ?></strong>
                                        <span style="color: #ee0004"><?php esc_html_e("Please disable Jetpack Comments.", "wpdiscuz") ?></span>
                                        <?php esc_html_e('Use the "Deactivate" button located next to the "Learn More" button. Just click on this button and Jetpack Comments will be deactivated. Once it\'s disabled, please delete all caches.', "wpdiscuz") ?>
                                    </p>
                                    <p style="text-align: right; margin-top: -10px;"><a href="<?php echo esc_url_raw(admin_url("admin.php?page=" . WpdiscuzCore::PAGE_SETTINGS)) . "&wpd_wizard=&wpd_addon_fix=" . $wpd_addon_fix ?>" class="button" style="text-decoration: none;"><?php _e('Mark as solved', 'wpdiscuz') ?></a></p>
                                </div>
                                <!-- Wizard Note - end -->
                                <?php
                            }
                            if ($notice) {
                                ?>
                                <!-- Wizard Help - start -->
                                <div class="wpd-wizard-help">
                                    <div style="width: 50%; padding: 20px; box-sizing: border-box;">
                                        <h3><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/solved.png")); ?>"/> <?php esc_html_e("I can't resolve the problems!", "wpdiscuz"); ?></h3>
                                        <p><?php esc_html_e("All problems displayed above have fixing tips in the same boxes. If, for some reason you can't solve those problems, we're ready to help you and fix those as soon as possible. Please open a support topic at gVectors support forum or contact as via support@gvectors.com email address. Our support team works from 6am till 6pm in GMT+0 timezone. Please be patient when you open a new support topic or when contacting us via email. We'll get back to you within 3-12 hours.", "wpdiscuz") ?></p>
                                    </div>
                                    <div style="width: 50%; padding: 20px; box-sizing: border-box;">
                                        <h3><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/cancel.png")); ?>"/> <?php esc_html_e("I want to downgrade to previous 5.x version!", "wpdiscuz"); ?></h3>
                                        <p><?php esc_html_e("To downgrade wpDiscuz, you should deactivate and delete wpDiscuz 7 plugin. Then download the previous 5.3.2 version ZIP file from wpDiscuz plugin page (use dropdown menu under statistic graphics). And install the ZIP file in Dashboard > Plugins > Add Plugin admin page. The downgrading is only recommended to gain a time to do template customizations or wait for help from our support team. Once the issues are resolved, you should update to latest wpDiscuz version.", "wpdiscuz") ?></p>
                                    </div>
                                </div>
                                <!-- Wizard Help - end -->
                                <?php
                            }
                            ?>
                            <!-- Step 1 - Comments Style & Layout - START -->
                            <p class="wpd-wizard-step-name"><?php esc_html_e("Comments Style & Layout", "wpdiscuz") ?></p>
                            <?php
                            if (!intval(get_option(self::OPTION_SLUG_WIZARD_AFTER_UPDATE))) {
                                ?>
                                <div class="wpd-wizard-opt" style="margin-top: 0px; padding-bottom: 20px;">
                                    <div class="wpd-wizard-opt-label">
                                        <?php esc_html_e("Comments Style", "wpdiscuz") ?>
                                    </div>
                                    <div class="wpd-wizard-opt-option">
                                        <div class="wpd-opt-input">
                                            <div class="wpd-switch-field">
                                                <input value="wpd-default" checked="checked" name="theme" id="themeDefault" type="radio"><label for="themeDefault"><?php esc_html_e("Light", "wpdiscuz"); ?></label>
                                                <input value="wpd-dark" name="theme" id="themeDark" type="radio"><label for="themeDark"><?php esc_html_e("Dark", "wpdiscuz"); ?></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                            <div class="wpd-wizard-opt" style="border-bottom: none;">
                                <div class="wpd-wizard-opt-label">
                                    <?php esc_html_e("Choose Your Comments Layout", "wpdiscuz") ?>
                                </div>
                                <div class="wpd-wizard-opt-option">
                                    <div class="wpd-opt-input">
                                        <div class="wpd-switch-field">
                                            <input value="1" checked="checked" name="layout" id="wpdLayout_1" type="radio"><label for="wpdLayout_1"><?php esc_html_e("Layout #1", "wpdiscuz"); ?></label>
                                            <input value="2" name="layout" id="wpdLayout_2" type="radio"><label for="wpdLayout_2"><?php esc_html_e("Layout #2", "wpdiscuz"); ?></label>
                                            <input value="3" name="layout" id="wpdLayout_3" type="radio"><label for="wpdLayout_3"><?php esc_html_e("Layout #3", "wpdiscuz"); ?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="wpd_comment_layouts" style="width: 80%; margin: 15px auto 0 auto">
                                <div class="wpd-box-layout">
                                    <a href="#img1"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/layout-1s.png")); ?>" class="wpd-com-layout-1"/></a>
                                    <a href="#_" class="wpd-lightbox" id="img1"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/layout-1.png")); ?>"/></a>
                                    <h4><?php esc_html_e("Comments Layout #1", "wpdiscuz") ?><br><hr style="width: 30%; margin-top: 10px; border-bottom: 1px dashed #07B290;"></h4>
                                </div>
                                <div class="wpd-box-layout">
                                    <a href="#img2"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/layout-2s.png")); ?>" class="wpd-com-layout-2"/></a>
                                    <a href="#_" class="wpd-lightbox" id="img2"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/layout-2.png")); ?>"/></a>
                                    <h4><?php esc_html_e("Comments Layout #2", "wpdiscuz") ?><br><hr style="width: 30%; margin-top: 10px; border-bottom: 1px dashed #07B290;"></h4>
                                </div>
                                <div class="wpd-box-layout">
                                    <a href="#img3"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/layout-3s.png")); ?>" class="wpd-com-layout-3"/></a>
                                    <a href="#_" class="wpd-lightbox" id="img3"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/layout-3.png")); ?>"/></a>
                                    <h4><?php esc_html_e("Comments Layout #3", "wpdiscuz") ?><br><hr style="width: 30%; margin-top: 10px; border-bottom: 1px dashed #07B290;"></h4>
                                </div>
                            </div>
                            <!-- Step 1 - Comments Style & Layout - END -->
                            <?php
                        } else if ($wizard === 2) {
                            ?>
                            <!-- Step 2 - Bubble - START -->
                            <p class="wpd-wizard-step-name"><?php esc_html_e("Comment Bubble", "wpdiscuz") ?></p>

                            <div class="wpd-wizard-opt wpd-wizard-section">
                                <img class="wpd-opt-img" src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/box-bubble.png")); ?>" style="height: 70px; padding: 0px 30px 5px 10px;"/>
                                <?php esc_html_e("Comment Bubble is a sticky comment icon on your web pages. It keeps article readers and commenters up to date. It displays new comments as pop-up information or as a number in an orange circle.", "wpdiscuz"); ?>
                                <?php esc_html_e("The Bubble is also designed to invite article readers to comment. It displays invite message allowing them fast and easy jump to comment area.", "wpdiscuz"); ?>
                            </div>
                            <div class="wpd-wizard-opt" style="padding-bottom: 20px;">
                                <div class="wpd-wizard-opt-label">
                                    <?php esc_html_e("Comment Bubble", "wpdiscuz") ?>
                                </div>
                                <div class="wpd-wizard-opt-option">
                                    <div class="wpd-switch-field">
                                        <input type="radio" value="1" checked name="enableBubble" id="enableBubble" />
                                        <label for="enableBubble" style="min-width:60px;"><?php esc_html_e("Enable", "wpdiscuz"); ?></label>
                                        <input type="radio" value="0" name="enableBubble" id="disableBubble" />
                                        <label for="disableBubble" style="min-width:60px;"><?php esc_html_e("Disable", "wpdiscuz"); ?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="wpd-wizard-opt" style="padding-bottom: 20px;">
                                <div class="wpd-wizard-opt-label">
                                    <?php esc_html_e("Bubble Live Update", "wpdiscuz") ?>
                                </div>
                                <div class="wpd-wizard-opt-option">
                                    <div class="wpd-switch-field">
                                        <input type="radio" value="1" name="bubbleLiveUpdate" id="enableBubbleLiveUpdate" />
                                        <label for="enableBubbleLiveUpdate" style="min-width:60px;"><?php esc_html_e("Enable", "wpdiscuz"); ?></label>
                                        <input type="radio" value="0" checked name="bubbleLiveUpdate" id="disableBubbleLiveUpdate" />
                                        <label for="disableBubbleLiveUpdate" style="min-width:60px;"><?php esc_html_e("Disable", "wpdiscuz"); ?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="wpd-wizard-opt" style="border-bottom: none;">
                                <div class="wpd-wizard-opt-label">
                                    <?php esc_html_e("Comment Bubble Location", "wpdiscuz") ?>
                                </div>
                                <div class="wpd-wizard-opt-option">
                                    <div class="wpd-switch-field">
                                        <input type="radio" checked value="content_left" name="bubbleLocation" id="content_left" class="content_left" style="vertical-align: bottom;"/>
                                        <label for="content_left" style="min-width:60px;"><?php esc_html_e("Content Left", "wpdiscuz"); ?></label>
                                        <input type="radio" value="left_corner" name="bubbleLocation" id="left_corner" class="left_corner" style="vertical-align: bottom;"/>
                                        <label for="left_corner" style="min-width:60px;"><?php esc_html_e("Left Corner", "wpdiscuz"); ?></label>
                                        <input type="radio" value="right_corner" name="bubbleLocation" id="right_corner" class="right_corner" style="vertical-align: bottom;"/>
                                        <label for="right_corner" style="min-width:60px;"><?php esc_html_e("Right Corner", "wpdiscuz"); ?></label>
                                    </div>
                                </div>
                            </div>
                            <div id="wpd_comment_layouts" style="width: 60%; margin: 15px auto 0 auto;">
                                <div class="wpd-box-layout">
                                    <a href="#img11"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/bubble-location-side.png")); ?>" class="wpd-com-layout-1"/></a>
                                    <a href="#_" class="wpd-lightbox" id="img11"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/bubble-location-side.png")); ?>"/></a>
                                    <h4><?php esc_html_e("Content Left", "wpdiscuz") ?><br><hr style="width: 30%; margin-top: 10px; border-bottom: 1px dashed #07B290;"></h4>
                                </div>
                                <div class="wpd-box-layout">
                                    <a href="#img22"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/bubble-location-left.png")); ?>" class="wpd-com-layout-2"/></a>
                                    <a href="#_" class="wpd-lightbox" id="img22"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/bubble-location-left.png")); ?>"/></a>
                                    <h4><?php esc_html_e("Left Corner", "wpdiscuz") ?><br><hr style="width: 30%; margin-top: 10px; border-bottom: 1px dashed #07B290;"></h4>
                                </div>
                                <div class="wpd-box-layout">
                                    <a href="#img33"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/bubble-location-right.png")); ?>" class="wpd-com-layout-3"/></a>
                                    <a href="#_" class="wpd-lightbox" id="img33"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/bubble-location-right.png")); ?>"/></a>
                                    <h4><?php esc_html_e("Right Corner", "wpdiscuz") ?><br><hr style="width: 30%; margin-top: 10px; border-bottom: 1px dashed #07B290;"></h4>
                                </div>
                            </div>
                            <!-- Step 2 - Bubble - END -->
                            <?php
                        } else if ($wizard === 3) {
                            ?>
                            <!-- Step 3 - Display Comments - START -->
                            <p class="wpd-wizard-step-name"><?php esc_html_e("More News", "wpdiscuz") ?></p>

                            <div class="wpd-wizard-section" style="border-bottom: 2px solid #eee;">
                                <p class="wpd-wizard-section-title">
                                    <?php esc_html_e("Increase Your Comments!", "wpdiscuz"); ?><br />
                                    <?php esc_html_e("Add Inline Commenting forms in article!", "wpdiscuz"); ?><br />
                                    <?php esc_html_e("Ask questions to readers and discuss directly on article content!", "wpdiscuz"); ?>
                                </p>
                                <img class="wpd-opt-img" src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/box-feedback.png")); ?>" style="height: 60px;  padding: 0px 30px 5px 10px; float: left; "/>
                                <?php esc_html_e("Great News! Now you can add questions for readers on certain part of article content and ask for feedback while visitors read it.", "wpdiscuz"); ?>
                                <?php esc_html_e("Select a part of text, add inline commenting form in post content using the green &laquo;Comment&raquo; button on post editor toolbar. Once it's added in article editor (backend), on article (front-end) readers will see a small comment icon next to the text part you've selected.", "wpdiscuz"); ?>
                                <div class="wpd-zoom-image" style="width: 60%; margin: 0px auto;">
                                    <a href="#img_inline"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/inline-feedback-toolbar-button.png")); ?>" style="margin-top: 15px; margin-left: 5px; width: 100%;"/></a>
                                    <a href="#_" class="wpd-lightbox" id="img_inline"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/inline-feedback-toolbar-button-vertical.png")); ?>"/></a>
                                </div>
                            </div>

                            <p class="wpd-wizard-section-title">
                                <?php esc_html_e("Article Rating vs Comment Rating", "wpdiscuz") ?>
                            </p>
                            <div class="wpd-wizard-opt" style="border-bottom: none;">
                                <div class="wpd-wizard-opt-label">
                                    <?php esc_html_e("Enable Article Rating", "wpdiscuz") ?>
                                </div>
                                <div class="wpd-wizard-opt-option">
                                    <div class="wpd-switch-field">
                                        <input checked="checked" type="radio" value="1" name="enable_post_rating" id="enableArticleRating" />
                                        <label for="enableArticleRating" style="min-width:60px;"><?php esc_html_e("Enable", "wpdiscuz"); ?></label>
                                        <input type="radio" value="0" name="enable_post_rating" id="disableArticleRating" />
                                        <label for="disableArticleRating" style="min-width:60px;"><?php esc_html_e("Disable", "wpdiscuz"); ?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="wpd-wizard-section" style="border-bottom: 2px solid #eee;">
                                <img class="wpd-news-icon" src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/rating.png")); ?>" style="height: 50px;  padding: 5px 30px 15px 10px; float: left; ">
                                <?php esc_html_e("Before, you had to create a Rating field in comment form to allow users rate article while they post a comment, there was no way to rate without commenting. Now you can allow users rate your articles without leaving a comment. wpDiscuz 7 has a built-in Article Rating system which is not based on comment form custom fields and appears on top of comment section, under the article content.", "wpdiscuz") ?>
                                <div class="wpd-zoom-image" style="width: 70%; margin: 10px auto;">
                                    <a href="#img55"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/news/rating-vs.png")); ?>" style="width: 100%;"/></a>
                                    <a href="#_" class="wpd-lightbox" id="img55"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/news/rating-vs-v.png")); ?>"/></a>
                                </div>
                            </div>
                            <!-- Step 3 - Display Comments - END -->
                            <?php
                        }
                        ?>
                    </div>
                    <div class="wpd-wizard-foot">
                        <div class="wpd-wizard-buttons">
                            <input class="wpd-wizard-button" type="submit" name="" value="<?php $wizard === 3 ? (intval(get_option(self::OPTION_SLUG_WIZARD_AFTER_UPDATE)) ? esc_html_e("Finish Update", "wpdiscuz") : esc_html_e("Finish Installation", "wpdiscuz")) : esc_html_e("Next Step &raquo;", "wpdiscuz"); ?>" />
                        </div>
                    </div>
                    <?php
                    wp_nonce_field("wpd_wizard_form");
                    ?>
                </form>
                <?php
            } else {
                update_option(self::OPTION_SLUG_WIZARD_COMPLETED, "1");
                ?>
                <p class="wpd-wizard-title"><?php esc_html_e("Thank You!", "wpdiscuz"); ?></p>
                <div class="wpd-wizard-foot">
                    <div class="wpd-wizard-buttons">
                        <a class="wpd-wizard-button" href="<?php echo esc_url_raw(admin_url("admin.php?page=" . WpdiscuzCore::PAGE_SETTINGS)); ?>"><?php esc_html_e("Start Using wpDiscuz", "wpdiscuz"); ?></a>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
        <?php
    } else {
        ?>
        <!-- wpd-setbox-body start -->
        <div class="wpd-setbox-body">
            <div class="wpd-section">
                <h3><?php esc_html_e("Settings", "wpdiscuz") ?></h3>
                <div class="wpd-opt-search">
                    <input id="wpd-opt-search-field" type="text" name="" value="" placeholder="<?php esc_attr_e("Find an option...", "wpdiscuz") ?>" />
                    <span class="dashicons dashicons-search"></span>
                    <div id="wpd-opt-search-results"></div>
                </div>
            </div>

            <?php
            if (!$tab || ($tab && !isset($settings["core"][$tab]) && !isset($settings["addons"][$tab]))) {
                ?>
                <!-- wpd-box-wrap start -->
                <div class="wpd-box-wrap wpd-settings-home">
                    <?php
                    foreach ($settings["core"] as $tab_key => $setting) {
                        ?>
                        <!-- Settings start -->
                        <div class="wpd-box">
                            <div class="wpd-box-info wpd-<?php echo $setting["status"] ?>">
                                <img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/" . $setting["status"] . ".png")); ?>"/>
                            </div>
                            <div class="wpd-box-head">
                                <a href="<?php echo esc_url_raw(admin_url("admin.php?page=" . WpdiscuzCore::PAGE_SETTINGS . "&wpd_tab=" . $tab_key)); ?>" title="<?php esc_attr_e("Open Settings", "wpdiscuz") ?>">
                                    <img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/" . $setting["icon"])); ?>" style="height: <?php echo $setting["icon-height"] ?>;" />
                                </a>
                            </div>
                            <div class="wpd-box-foot">
                                <div class="wpd-box-title">
                                    <a href="<?php echo esc_url_raw(admin_url("admin.php?page=" . WpdiscuzCore::PAGE_SETTINGS . "&wpd_tab=" . $tab_key)); ?>" title="<?php esc_attr_e("Open Settings", "wpdiscuz") ?>"><?php echo $setting["title"] ?></a>
                                </div>
                                <div class="wpd-box-arrow">
                                    <a href="<?php echo esc_url_raw(admin_url("admin.php?page=" . WpdiscuzCore::PAGE_SETTINGS . "&&wpd_tab=" . $tab_key)); ?>" title="<?php esc_attr_e("Open Settings", "wpdiscuz") ?>"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/arrow-right.png")); ?>" /></a>
                                </div>
                            </div>
                        </div>
                        <!-- Settings end -->
                        <?php
                    }
                    ?>
                    <div class="wpd-clear"></div>
                </div>
                <!-- wpd-box-wrap end -->
                <?php
                if ($settings["addons"]) {
                    ?>
                    <div class="wpd-section">
                        <h3><?php esc_html_e("Addons Settings", "wpdiscuz") ?></h3>
                    </div>
                    <!-- wpd-box-wrap start -->
                    <div class="wpd-box-wrap wpd-box-addons wpd-settings-home">
                        <?php foreach ($settings["addons"] as $addon_key => $addon) { ?>
                            <!-- Addon Settings start -->
                            <div class="wpd-box">
                                <div class="wpd-body">
                                    <div class="wpd-icon">
                                        <img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/addon.png")); ?>" style="height: 20px; padding: 0px;" />
                                    </div>
                                    <div class="wpd-title">
                                        <a href="<?php echo esc_url_raw(admin_url("admin.php?page=" . WpdiscuzCore::PAGE_SETTINGS . "&wpd_tab=" . $addon_key)); ?>"><?php echo esc_html($addon["title"]); ?></a>
                                    </div>
                                    <div class="wpd-more">
                                        <img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/dots.png")); ?>" style="height: 18px; padding-top: 2px;" />
                                    </div>
                                </div>
                            </div>
                            <!-- Addon Settings end -->
                        <?php } ?>
                    </div>
                    <!-- wpd-box-wrap end -->
                    <div class="wpd-clear"></div>
                    <?php
                }
            } else {
                ?>
                <div id="wpd-settings-tab" class="wpd-box-wrap wpd-settings-tab_<?php echo esc_attr($tab); ?>" style="align-items:flex-start;">
                    <!-- Settings Content start -->
                    <div class="wpd-box wpd-setcon">
                        <?php
                        $setting = isset($settings["core"][$tab]) ? $settings["core"][$tab] : $settings["addons"][$tab];
                        $filePath = $setting["file_path"];
                        ?>
                        <div class="wpd-setcon-head">
                            <?php echo esc_html($setting["title"]); ?>
                            <a class="wpd-back" href="<?php echo esc_url_raw(admin_url("admin.php?page=" . WpdiscuzCore::PAGE_SETTINGS)); ?>"><span class="dashicons dashicons-arrow-left-alt2"></span><?php esc_html_e("Back", "wpdiscuz") ?></a>
                        </div>
                        <div class="wpd-setcon-body">
                            <form action="<?php echo esc_url_raw(admin_url("admin.php?page=" . WpdiscuzCore::PAGE_SETTINGS . "&wpd_tab=" . $tab)); ?>" method="post" name="<?php echo esc_attr($tab); ?>" class="wc-main-settings-form wc-form" enctype="multipart/form-data">
                                <?php
                                wp_nonce_field("wc_options_form-" . $tab);
                                include_once $filePath;
                                do_action("wpdiscuz_settings_tab_after", $tab, $setting);
                                $resetOptionsUrl = admin_url("admin.php?page=" . WpdiscuzCore::PAGE_SETTINGS . "&wpd_tab=" . $tab . "&wpdiscuz_reset_options=1");
                                $resetOptionsUrl = wp_nonce_url($resetOptionsUrl, "wpdiscuz_reset_options_nonce-" . $tab);
                                $resetAllTabs = admin_url("admin.php?page=" . WpdiscuzCore::PAGE_SETTINGS . "&wpd_tab=all&wpdiscuz_reset_options=1");
                                $resetAllTabs = wp_nonce_url($resetAllTabs, "wpdiscuz_reset_options_nonce-all");
                                ?>
                                <div class="wpd-opt-row" data-wpd-opt="commentTextLength">
                                    <input type="hidden" name="wpd_tab" value="<?php echo esc_attr($tab); ?>" />                                
                                    <div>
                                        <a id="wpdiscuz-reset-options" style="text-decoration:none;" class="button button-secondary" href="<?php echo esc_url_raw($resetOptionsUrl); ?>"><?php esc_html_e("Reset Tab Options", "wpdiscuz"); ?></a> 
                                        <a id="wpdiscuz-reset-all-options" style="text-decoration:none;" class="button button-secondary" href="<?php echo esc_url_raw($resetAllTabs); ?>"><?php esc_html_e("Reset All Options", "wpdiscuz"); ?></a> 
                                    </div>
                                    <input style="float: right;" type="submit" class="button button-primary" name="wc_submit_options" value="<?php esc_attr_e("Save Changes", "wpdiscuz"); ?>" />                                
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Settings Content end -->
                    <!-- Settings Sidebar start -->
                    <div class="wpd-setbar">
                        <ul class="wpd-box wpd-menu-group">
                            <li class="wpd-menu-head"><?php esc_html_e("Settings", "wpdiscuz") ?> <span class="dashicons dashicons-arrow-up"></span></li>
                            <?php
                            foreach ($settings["core"] as $tab_key => $setting) {
                                ?>
                                <li<?php if ($tab === $tab_key) echo " class='wpd-active'"; ?>><a href="<?php echo esc_url_raw(admin_url("admin.php?page=" . WpdiscuzCore::PAGE_SETTINGS . "&wpd_tab=" . $tab_key)); ?>"><span class="dashicons dashicons-arrow-left-alt2"></span> <?php echo esc_html($setting["title"]); ?></a></li>
                                <?php
                            }
                            ?>
                        </ul>
                        <?php
                        if ($settings["addons"]) {
                            ?>
                            <ul class="wpd-box wpd-menu-group">
                                <li class="wpd-menu-head"><?php esc_html_e("Addons Settings", "wpdiscuz") ?> <span class="dashicons dashicons-arrow-up"></span></li>
                                <?php
                                foreach ($settings["addons"] as $addon_key => $addon) {
                                    ?>
                                    <li<?php if ($tab === $addon_key) echo " class='wpd-active'"; ?>><a href="<?php echo esc_url_raw(admin_url("admin.php?page=" . WpdiscuzCore::PAGE_SETTINGS . "&wpd_tab=" . $addon_key)); ?>"><span class="dashicons dashicons-arrow-left-alt2"></span> <?php echo esc_html($addon["title"]); ?></a></li>
                                    <?php
                                }
                                ?>
                            </ul>
                            <?php
                        }
                        ?>
                    </div>
                    <!-- Settings Sidebar end -->
                    <div class="wpd-clear"></div>
                </div>
                <?php
            }
            ?>
        </div>
        <!-- wpd-setbox-body end -->
        <?php
    }
    ?>
</div>