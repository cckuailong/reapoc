<?php
if (!defined("ABSPATH")) {
    exit();
}
?>
<!-- Option start -->
<div class="wpd-opt-row">
    <div class="wpd-opt-intro">
        <img class="wpd-opt-img" src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/" . $setting["icon"])); ?>" style="height: 90px; padding-top: 5px;"/>
        <?php esc_html_e("wpDiscuz comes with built-in social login and share buttons. It includes Facebook, Twitter, Google, Disqus, WordPress.com, VK and OK Social Networks. Here you can configure App IDs and Keys to enable those. Once IDs and Keys are configured you'll see social login buttons on top of the main comment form. Social Login buttons are only available for guests, so make sure you're logged-out before checking those.", "wpdiscuz"); ?>
    </div>
    <div class="wpd-opt-doc" style="padding-top: 10px;">
        <a href="https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/social-login-and-share/facebook-app-configuration/" title="<?php esc_attr_e("Read the documentation", "wpdiscuz") ?>" target="_blank"><i class="far fa-question-circle"></i></a>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="socialLoginAgreementCheckbox">
    <div class="wpd-opt-name">
        <label for="socialLoginAgreementCheckbox"><?php echo esc_html($setting["options"]["socialLoginAgreementCheckbox"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["socialLoginAgreementCheckbox"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->social["socialLoginAgreementCheckbox"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[socialLoginAgreementCheckbox]" id="socialLoginAgreementCheckbox">
            <label for="socialLoginAgreementCheckbox"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["socialLoginAgreementCheckbox"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="socialLoginInSecondaryForm">
    <div class="wpd-opt-name">
        <label for="socialLoginInSecondaryForm"><?php echo esc_html($setting["options"]["socialLoginInSecondaryForm"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["socialLoginInSecondaryForm"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->social["socialLoginInSecondaryForm"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[socialLoginInSecondaryForm]" id="socialLoginInSecondaryForm">
            <label for="socialLoginInSecondaryForm"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["socialLoginInSecondaryForm"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="displayIconOnAvatar">
    <div class="wpd-opt-name">
        <label for="displayIconOnAvatar"><?php echo esc_html($setting["options"]["displayIconOnAvatar"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["displayIconOnAvatar"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->social["displayIconOnAvatar"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[displayIconOnAvatar]" id="displayIconOnAvatar">
            <label for="displayIconOnAvatar"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["displayIconOnAvatar"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<div class="wpd-social-label wpd-facebook" style="padding: 25px 0px 10px 0px;">
    <img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/social-icons/fb-m.png")); ?>" style="vertical-align:bottom; height: 24px; margin-bottom: -2px; position: relative; border-radius: 50%;">&nbsp; Facebook
</div>
<p style="font-size: 14px; font-style: italic; margin-top: 5px;">
    <?php esc_html_e("To start using Facebook Login and Share Buttons you should get Facebook Application Key and Secret for your website. Please follow to this", "wpdiscuz"); ?> <a href="https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/social-login-and-share/facebook-app-configuration/" target="_blank" style="font-weight: 600;"><?php esc_html_e("instruction &raquo;", "wpdiscuz"); ?></a> &nbsp;
    <?php echo esc_html__("Valid OAuth Redirect URI", "wpdiscuz") . " : <code>" . esc_url_raw(admin_url("admin-ajax.php?action=wpd_login_callback&provider=facebook")) . "</code>"; ?>
</p>

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="enableFbLogin">
    <div class="wpd-opt-name">
        <label for="enableFbLogin"><?php echo esc_html($setting["options"]["enableFbLogin"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["enableFbLogin"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->social["enableFbLogin"] == 1); ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[enableFbLogin]" id="enableFbLogin">
            <label for="enableFbLogin"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enableFbLogin"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<?php if (is_ssl()) { ?>
    <!-- Option start -->
    <div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="fbUseOAuth2">
        <div class="wpd-opt-name">
            <label for="fbUseOAuth2"><?php echo esc_html($setting["options"]["fbUseOAuth2"]["label"]) ?></label>
            <p class="wpd-desc"><?php echo $setting["options"]["fbUseOAuth2"]["description"] ?></p>
        </div>
        <div class="wpd-opt-input">
            <div class="wpd-switcher">
                <input type="checkbox" <?php checked($this->social["fbUseOAuth2"] == 1); ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[fbUseOAuth2]" id="fbUseOAuth2">
                <label for="fbUseOAuth2"></label>
            </div>
        </div>
        <div class="wpd-opt-doc">
            <?php $this->printDocLink($setting["options"]["fbUseOAuth2"]["docurl"]) ?>
        </div>
    </div>
    <!-- Option end -->
<?php } ?>

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="enableFbShare">
    <div class="wpd-opt-name">
        <label for="enableFbShare"><?php echo esc_html($setting["options"]["enableFbShare"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["enableFbShare"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->social["enableFbShare"] == 1); ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[enableFbShare]" id="enableFbShare">
            <label for="enableFbShare"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enableFbShare"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="fbAppID">
    <div class="wpd-opt-name">
        <label for="fbAppID"><?php echo esc_html($setting["options"]["fbAppID"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["fbAppID"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input placeholder="<?php esc_attr_e("Application ID", "wpdiscuz"); ?>" type="text" value="<?php echo esc_attr($this->social["fbAppID"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[fbAppID]" id="wpd-fb-app-id" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["fbAppID"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="fbAppSecret">
    <div class="wpd-opt-name">
        <label for="fbAppSecret"><?php echo esc_html($setting["options"]["fbAppSecret"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["fbAppSecret"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input placeholder="<?php esc_attr_e("Application Secret", "wpdiscuz"); ?>" type="text" value="<?php echo esc_attr($this->social["fbAppSecret"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[fbAppSecret]" id="wpd-fb-app-secret" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["fbAppSecret"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->


<div class="wpd-social-label wpd-twitter" style="padding: 25px 0px 10px 0px;">
    <img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/social-icons/tw-m.png")); ?>" style="vertical-align:bottom; height: 24px; margin-bottom: -2px; position: relative; border-radius: 50%;">&nbsp; Twitter
</div>
<p style="font-size: 14px; font-style: italic; margin-top: 5px;">
    <?php esc_html_e("To start using Twitter Login Button you should get Consumer Key and Secret for your website. Please follow to this", "wpdiscuz"); ?> <a href="https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/social-login-and-share/twitter-app-configuration/" target="_blank" style="font-weight: 600;"><?php esc_html_e("instruction &raquo;", "wpdiscuz"); ?></a> &nbsp;
    <?php echo esc_html__("Twitter Callback URL", "wpdiscuz") . " : <code>" . esc_url_raw(admin_url("admin-ajax.php")) . "</code>"; ?>
</p>


<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="enableTwitterLogin">
    <div class="wpd-opt-name">
        <label for="enableTwitterLogin"><?php echo esc_html($setting["options"]["enableTwitterLogin"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["enableTwitterLogin"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->social["enableTwitterLogin"] == 1); ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[enableTwitterLogin]" id="enableTwitterLogin">
            <label for="enableTwitterLogin"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enableTwitterLogin"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="enableTwitterShare">
    <div class="wpd-opt-name">
        <label for="enableTwitterShare"><?php echo esc_html($setting["options"]["enableTwitterShare"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["enableTwitterShare"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->social["enableTwitterShare"] == 1); ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[enableTwitterShare]" id="enableTwitterShare">
            <label for="enableTwitterShare"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enableTwitterShare"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="twitterAppID">
    <div class="wpd-opt-name">
        <label for="twitterAppID"><?php echo esc_html($setting["options"]["twitterAppID"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["twitterAppID"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input placeholder="<?php esc_attr_e("Consumer Key (API Key)", "wpdiscuz"); ?>" type="text" value="<?php echo esc_attr($this->social["twitterAppID"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[twitterAppID]" id="wpd-twitter-app-id" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["twitterAppID"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="twitterAppSecret">
    <div class="wpd-opt-name">
        <label for="twitterAppSecret"><?php echo esc_html($setting["options"]["twitterAppSecret"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["twitterAppSecret"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input placeholder="<?php esc_attr_e("Consumer Secret (API Secret)", "wpdiscuz"); ?>" type="text" value="<?php echo esc_attr($this->social["twitterAppSecret"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[twitterAppSecret]" id="wpd-twitter-app-secret" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["twitterAppSecret"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->


<div class="wpd-social-label wpd-google" style="padding: 25px 0px 10px 0px;">
    <img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/social-icons/gg-m.png")); ?>" style="vertical-align:bottom; height: 24px; margin-bottom: -2px; position: relative; border-radius: 50%;">&nbsp; Google
</div>
<p style="font-size: 14px; font-style: italic; margin-top: 5px;">
    <?php esc_html_e("To start using Google Login Button you should get Client ID and  Client Secret for your website. Please follow to this", "wpdiscuz"); ?> <a href="https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/social-login-and-share/google-app-configuration/" target="_blank" style="font-weight: 600;"><?php esc_html_e("instruction &raquo;", "wpdiscuz"); ?></a> &nbsp;
    <?php echo esc_html__("Permitted URI redirects", "wpdiscuz") . " : <code>" . esc_url_raw(admin_url("admin-ajax.php")) . "?action=wpd_login_callback&provider=google</code>"; ?>
</p>

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="enableGoogleLogin">
    <div class="wpd-opt-name">
        <label for="enableGoogleLogin"><?php echo esc_html($setting["options"]["enableGoogleLogin"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["enableGoogleLogin"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox"<?php checked($this->social["enableGoogleLogin"] == 1); ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[enableGoogleLogin]" id="enableGoogleLogin">
            <label for="enableGoogleLogin"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enableGoogleLogin"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="googleClientID">
    <div class="wpd-opt-name">
        <label for="googleClientID"><?php echo esc_html($setting["options"]["googleClientID"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["googleClientID"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input placeholder="<?php esc_attr_e("Client ID", "wpdiscuz"); ?>" type="text" value="<?php echo esc_attr($this->social["googleClientID"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[googleClientID]" id="wpd-google-client-id" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["googleClientID"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="googleClientSecret">
    <div class="wpd-opt-name">
        <label for="googleClientSecret"><?php echo esc_html($setting["options"]["googleClientSecret"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["googleClientSecret"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input placeholder="<?php esc_attr_e("Client Secret", "wpdiscuz"); ?>" type="text" value="<?php echo esc_attr($this->social["googleClientSecret"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[googleClientSecret]" id="wpd-google-client-secret" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["googleClientSecret"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->



<div class="wpd-social-label wpd-disqus" style="padding: 25px 0px 10px 0px;">
    <img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/social-icons/dq-m.png")); ?>" style="vertical-align:bottom; height: 24px; margin-bottom: -2px; position: relative; border-radius: 50%;">&nbsp; Disqus
</div>
<p style="font-size: 14px; font-style: italic; margin-top: 5px;">
    <?php esc_html_e("To start using Disqus Login Button you should get Public Key and Secret Key. Please follow to this ", "wpdiscuz"); ?> <a href="https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/social-login-and-share/disqus-app-configuration/" target="_blank" style="font-weight: 600;"><?php esc_html_e("instruction &raquo;", "wpdiscuz"); ?></a> &nbsp;
    <?php echo esc_html__("Redirect URI", "wpdiscuz") . " : <code>" . esc_url_raw(admin_url("admin-ajax.php")) . "?action=wpd_login_callback&provider=disqus</code>"; ?>
</p>

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="enableDisqusLogin">
    <div class="wpd-opt-name">
        <label for="enableDisqusLogin"><?php echo esc_html($setting["options"]["enableDisqusLogin"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["enableDisqusLogin"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->social["enableDisqusLogin"] == 1); ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[enableDisqusLogin]" id="enableDisqusLogin">
            <label for="enableDisqusLogin"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enableDisqusLogin"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="disqusPublicKey">
    <div class="wpd-opt-name">
        <label for="disqusPublicKey"><?php echo esc_html($setting["options"]["disqusPublicKey"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["disqusPublicKey"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input placeholder="<?php esc_attr_e("Public Key", "wpdiscuz"); ?>" type="text" value="<?php echo esc_attr($this->social["disqusPublicKey"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[disqusPublicKey]" id="wpd-disqus-public-key" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["disqusPublicKey"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="disqusSecretKey">
    <div class="wpd-opt-name">
        <label for="disqusSecretKey"><?php echo esc_html($setting["options"]["disqusSecretKey"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["disqusSecretKey"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input placeholder="<?php esc_attr_e("Secure Key", "wpdiscuz"); ?>" type="text" value="<?php echo esc_attr($this->social["disqusSecretKey"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[disqusSecretKey]" id="wpd-disqus-secret-key" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["disqusSecretKey"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->


<div class="wpd-social-label wpd-wordpress" style="padding: 25px 0px 10px 0px;">
    <img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/social-icons/wp-m.png")); ?>" style="vertical-align:bottom; height: 24px; margin-bottom: -2px; position: relative; border-radius: 50%;">&nbsp; WordPress.com
</div>
<p style="font-size: 14px; font-style: italic; margin-top: 5px;">
    <?php esc_html_e("To start using Wordpress.com Login Button you should get Client ID and Client Secret. Please follow to this ", "wpdiscuz"); ?> <a href="https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/social-login-and-share/wordpress-com-app-configuration/" target="_blank" style="font-weight: 600;"><?php esc_html_e("instruction &raquo;", "wpdiscuz"); ?></a> &nbsp;
    <?php echo esc_html__("Redirect URI", "wpdiscuz") . " : <code>" . esc_url_raw(home_url("/")) . "</code>"; ?>
</p>

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="enableWordpressLogin">
    <div class="wpd-opt-name">
        <label for="enableWordpressLogin"><?php echo esc_html($setting["options"]["enableWordpressLogin"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["enableWordpressLogin"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->social["enableWordpressLogin"] == 1); ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[enableWordpressLogin]" id="enableWordpressLogin">
            <label for="enableWordpressLogin"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enableWordpressLogin"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="wordpressClientID">
    <div class="wpd-opt-name">
        <label for="wordpressClientID"><?php echo esc_html($setting["options"]["wordpressClientID"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["wordpressClientID"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input placeholder="<?php esc_attr_e("Client ID", "wpdiscuz"); ?>" type="text" value="<?php echo esc_attr($this->social["wordpressClientID"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[wordpressClientID]" id="wpd-wordpress-client-id" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["wordpressClientID"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="wordpressClientSecret">
    <div class="wpd-opt-name">
        <label for="wordpressClientSecret"><?php echo esc_html($setting["options"]["wordpressClientSecret"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["wordpressClientSecret"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input placeholder="<?php esc_attr_e("Client Secret", "wpdiscuz"); ?>" type="text" value="<?php echo esc_attr($this->social["wordpressClientSecret"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[wordpressClientSecret]" id="wpd-wordpress-client-secret" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["wordpressClientSecret"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->
<div class="wpd-social-label wpd-instagram" style="padding: 25px 0px 10px 0px;">
    <img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/social-icons/ig-m.png")); ?>" style="vertical-align:bottom; height: 24px; margin-bottom: -2px; position: relative; border-radius: 50%;">&nbsp; Instagram
</div>
<p style="font-size: 14px; font-style: italic; margin-top: 5px;">
    <?php esc_html_e("To start using Instagram Login Button you should get Client ID and Client Secret. Please follow to this ", "wpdiscuz"); ?> <a href="https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/social-login-and-share/instagram-app-configuration/" target="_blank" style="font-weight: 600;"><?php esc_html_e("instruction &raquo;", "wpdiscuz"); ?></a> &nbsp;
    <?php echo esc_html__("Redirect URI", "wpdiscuz") . " : <code>" . esc_url_raw(site_url('/wpdiscuz_auth/instagram/')) . "</code>"; ?>
</p>

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="enableInstagramLogin">
    <div class="wpd-opt-name">
        <label for="enableInstagramLogin"><?php echo esc_html($setting["options"]["enableInstagramLogin"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["enableInstagramLogin"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox"<?php checked($this->social["enableInstagramLogin"] == 1); ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[enableInstagramLogin]" id="enableInstagramLogin">
            <label for="enableInstagramLogin"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enableInstagramLogin"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="instagramAppID">
    <div class="wpd-opt-name">
        <label for="instagramAppID"><?php echo esc_html($setting["options"]["instagramAppID"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["instagramAppID"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input placeholder="<?php esc_attr_e("App ID", "wpdiscuz"); ?>" type="text" value="<?php echo esc_attr($this->social["instagramAppID"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[instagramAppID]" id="wpd-google-client-id" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["instagramAppID"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="instagramAppSecret">
    <div class="wpd-opt-name">
        <label for="instagramAppSecret"><?php echo esc_html($setting["options"]["instagramAppSecret"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["instagramAppSecret"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input placeholder="<?php esc_attr_e("App Secret", "wpdiscuz"); ?>" type="text" value="<?php echo esc_attr($this->social["instagramAppSecret"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[instagramAppSecret]" id="wpd-google-client-secret" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["instagramAppSecret"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<div class="wpd-social-label wpd-linkedin" style="padding: 25px 0px 10px 0px;">
    <img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/social-icons/in-m.png")); ?>" style="vertical-align:bottom; height: 24px; margin-bottom: -2px; position: relative; border-radius: 50%;">&nbsp; LinkedIn
</div>
<p style="font-size: 14px; font-style: italic; margin-top: 5px;">
    <?php esc_html_e("To start using Linkedin Login Button you should get Client ID and Client Secret. Please follow to this ", "wpdiscuz"); ?> <a href="https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/social-login-and-share/linkedin-app-configuration/" target="_blank" style="font-weight: 600;"><?php esc_html_e("instruction &raquo;", "wpdiscuz"); ?></a> &nbsp;
    <?php echo esc_html__("Redirect URI", "wpdiscuz") . " : <code>" . esc_url_raw(site_url('/wpdiscuz_auth/linkedin/')) . "</code>"; ?>
</p>

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="enableLinkedinLogin">
    <div class="wpd-opt-name">
        <label for="enableLinkedinLogin"><?php echo esc_html($setting["options"]["enableLinkedinLogin"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["enableLinkedinLogin"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox"<?php checked($this->social["enableLinkedinLogin"] == 1); ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[enableLinkedinLogin]" id="enableLinkedinLogin">
            <label for="enableLinkedinLogin"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enableLinkedinLogin"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="linkedinClientID">
    <div class="wpd-opt-name">
        <label for="linkedinClientID"><?php echo esc_html($setting["options"]["linkedinClientID"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["linkedinClientID"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input placeholder="<?php esc_attr_e("Client ID", "wpdiscuz"); ?>" type="text" value="<?php echo esc_attr($this->social["linkedinClientID"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[linkedinClientID]" id="wpd-google-client-id" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["linkedinClientID"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="linkedinClientSecret">
    <div class="wpd-opt-name">
        <label for="linkedinClientSecret"><?php echo esc_html($setting["options"]["linkedinClientSecret"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["linkedinClientSecret"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input placeholder="<?php esc_attr_e("Client Secret", "wpdiscuz"); ?>" type="text" value="<?php echo esc_attr($this->social["linkedinClientSecret"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[linkedinClientSecret]" id="wpd-google-client-secret" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["linkedinClientSecret"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<div class="wpd-social-label wpd-wapp" style="padding: 25px 0px 10px 0px;">
    <img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/social-icons/wapp-m.png")); ?>" style="vertical-align:bottom; height: 24px; margin-bottom: -2px; position: relative; border-radius: 50%;">&nbsp; WhatsApp
</div>

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="enableWhatsappShare">
    <div class="wpd-opt-name">
        <label for="enableWhatsappShare"><?php echo esc_html($setting["options"]["enableWhatsappShare"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["enableWhatsappShare"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox"<?php checked($this->social["enableWhatsappShare"] == 1); ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[enableWhatsappShare]" id="enableWhatsappShare">
            <label for="enableWhatsappShare"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enableWhatsappShare"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<div class="wpd-social-label wpd-yandex" style="padding: 25px 0px 10px 0px;">
    <img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/social-icons/ya-m.png")); ?>" style="vertical-align:bottom; height: 24px; margin-bottom: -2px; position: relative; border-radius: 50%;">&nbsp; Yandex
</div>
<p style="font-size: 14px; font-style: italic; margin-top: 5px;">
    <?php esc_html_e("To start using Yandex Login Button you should get Client ID and Client Secret. Please follow to this ", "wpdiscuz"); ?> <a href="https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/social-login-and-share/yandex-app-configuration/" target="_blank" style="font-weight: 600;"><?php esc_html_e("instruction &raquo;", "wpdiscuz"); ?></a> &nbsp;
    <?php echo esc_html__("Redirect URI", "wpdiscuz") . " : <code>" . esc_url_raw(admin_url("admin-ajax.php?action=wpd_login_callback&provider=yandex")) . "</code>"; ?>
</p>

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="enableYandexLogin">
    <div class="wpd-opt-name">
        <label for="enableYandexLogin"><?php echo esc_html($setting["options"]["enableYandexLogin"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["enableYandexLogin"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox"<?php checked($this->social["enableYandexLogin"] == 1); ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[enableYandexLogin]" id="enableYandexLogin">
            <label for="enableYandexLogin"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enableYandexLogin"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="yandexID">
    <div class="wpd-opt-name">
        <label for="yandexID"><?php echo esc_html($setting["options"]["yandexID"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["yandexID"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input placeholder="<?php esc_attr_e("ID", "wpdiscuz"); ?>" type="text" value="<?php echo esc_attr($this->social["yandexID"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[yandexID]" id="wpd-google-client-id" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["yandexID"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="yandexPassword">
    <div class="wpd-opt-name">
        <label for="yandexPassword"><?php echo esc_html($setting["options"]["yandexPassword"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["yandexPassword"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input placeholder="<?php esc_attr_e("Password", "wpdiscuz"); ?>" type="text" value="<?php echo esc_attr($this->social["yandexPassword"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[yandexPassword]" id="wpd-google-client-secret" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["yandexPassword"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<div class="wpd-social-label wpd-vk" style="padding: 25px 0px 10px 0px;">
    <img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/social-icons/vk-m.png")); ?>" style="vertical-align:bottom; height: 24px; margin-bottom: -2px; position: relative; border-radius: 50%;">&nbsp; VK
</div>
<p style="font-size: 14px; font-style: italic; margin-top: 5px;">
    <?php esc_html_e("To start using VK Login Button you should get Application ID and Secure Key. Please follow to this ", "wpdiscuz"); ?> <a href="https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/social-login-and-share/vk-app-configuration/" target="_blank" style="font-weight: 600;"><?php esc_html_e("instruction &raquo;", "wpdiscuz"); ?></a> &nbsp;
    <?php echo esc_html__("Redirect URI", "wpdiscuz") . " : <code>" . esc_url_raw(admin_url("admin-ajax.php")) . "</code>"; ?>
</p>


<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="enableVkLogin">
    <div class="wpd-opt-name">
        <label for="enableVkLogin"><?php echo esc_html($setting["options"]["enableVkLogin"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["enableVkLogin"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->social["enableVkLogin"] == 1); ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[enableVkLogin]" id="enableVkLogin">
            <label for="enableVkLogin"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enableVkLogin"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="enableVkShare">
    <div class="wpd-opt-name">
        <label for="enableVkShare"><?php echo esc_html($setting["options"]["enableVkShare"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["enableVkShare"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->social["enableVkShare"] == 1); ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[enableVkShare]" id="enableVkShare">
            <label for="enableVkShare"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enableVkShare"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="vkAppID">
    <div class="wpd-opt-name">
        <label for="vkAppID"><?php echo esc_html($setting["options"]["vkAppID"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["vkAppID"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input placeholder="<?php esc_attr_e("Application ID", "wpdiscuz"); ?>" type="text" value="<?php echo esc_attr($this->social["vkAppID"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[vkAppID]" id="wpd-vk-app-id" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["vkAppID"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="vkAppSecret">
    <div class="wpd-opt-name">
        <label for="vkAppSecret"><?php echo esc_html($setting["options"]["vkAppSecret"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["vkAppSecret"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input placeholder="<?php esc_attr_e("Secure Key", "wpdiscuz"); ?>" type="text" value="<?php echo esc_attr($this->social["vkAppSecret"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[vkAppSecret]" id="wpd-vk-app-secret" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["vkAppSecret"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<div class="wpd-social-label wpd-mailru" style="padding: 25px 0px 10px 0px;">
    <img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/social-icons/mr-m.png")); ?>" style="vertical-align:bottom; height: 24px; margin-bottom: -2px; position: relative; border-radius: 50%;">&nbsp; Mail.ru
</div>
<p style="font-size: 14px; font-style: italic; margin-top: 5px;">
    <?php esc_html_e("To start using Mail.ru Login Button you should get Client ID and Client Secret. Please follow to this ", "wpdiscuz"); ?> <a href="https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/social-login-and-share/mail-ru-app-configuration/" target="_blank" style="font-weight: 600;"><?php esc_html_e("instruction &raquo;", "wpdiscuz"); ?></a> &nbsp;
    <?php echo esc_html__("Redirect URI", "wpdiscuz") . " : <code>" . esc_url_raw(admin_url("admin-ajax.php?action=wpd_login_callback&provider=mailru")) . "</code>"; ?>
</p>

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="enableMailruLogin">
    <div class="wpd-opt-name">
        <label for="enableMailruLogin"><?php echo esc_html($setting["options"]["enableMailruLogin"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["enableMailruLogin"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox"<?php checked($this->social["enableMailruLogin"] == 1); ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[enableMailruLogin]" id="enableMailruLogin">
            <label for="enableMailruLogin"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enableMailruLogin"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="mailruClientID">
    <div class="wpd-opt-name">
        <label for="mailruClientID"><?php echo esc_html($setting["options"]["mailruClientID"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["mailruClientID"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input placeholder="<?php esc_attr_e("ID", "wpdiscuz"); ?>" type="text" value="<?php echo esc_attr($this->social["mailruClientID"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[mailruClientID]" id="wpd-google-client-id" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["mailruClientID"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="mailruClientSecret">
    <div class="wpd-opt-name">
        <label for="mailruClientSecret"><?php echo esc_html($setting["options"]["mailruClientSecret"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["mailruClientSecret"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input placeholder="<?php esc_attr_e("Secret Key", "wpdiscuz"); ?>" type="text" value="<?php echo esc_attr($this->social["mailruClientSecret"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[mailruClientSecret]" id="wpd-google-client-secret" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["mailruClientSecret"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<div class="wpd-social-label wpd-ok" style="padding: 25px 0px 10px 0px;">
    <img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/social-icons/ok-m.png")); ?>" style="vertical-align:bottom; height: 24px; margin-bottom: -2px; position: relative; border-radius: 50%;">&nbsp; OK
</div>
<p style="font-size: 14px; font-style: italic; margin-top: 5px;">
    <?php esc_html_e("Getting started with", "wpdiscuz"); ?> <a href="https://apiok.ru/en/ext/oauth/">OK API</a><br>
    <?php esc_html_e("To get the Application ID, Key and Secret, you should create an app using one of the supported types (external, Android, iOS), use this", "wpdiscuz"); ?> <a href="https://apiok.ru/en/dev/app/create" target="_blank" style="font-weight: 600;"><?php esc_html_e("instruction &raquo;", "wpdiscuz"); ?></a> &nbsp;
    <?php echo esc_html__("Redirect URI", "wpdiscuz") . " : <code>" . esc_url_raw(admin_url("admin-ajax.php")) . "</code>"; ?>
</p>


<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="enableOkLogin">
    <div class="wpd-opt-name">
        <label for="enableOkLogin"><?php echo esc_html($setting["options"]["enableOkLogin"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["enableOkLogin"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->social["enableOkLogin"] == 1); ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[enableOkLogin]" id="enableOkLogin">
            <label for="enableOkLogin"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enableOkLogin"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="enableOkShare">
    <div class="wpd-opt-name">
        <label for="enableOkShare"><?php echo esc_html($setting["options"]["enableOkShare"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["enableOkShare"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->social["enableOkShare"] == 1); ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[enableOkShare]" id="enableOkShare">
            <label for="enableOkShare"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enableOkShare"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="okAppID">
    <div class="wpd-opt-name">
        <label for="okAppID"><?php echo esc_html($setting["options"]["okAppID"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["okAppID"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input placeholder="<?php esc_attr_e("Application ID", "wpdiscuz"); ?>" type="text" value="<?php echo esc_attr($this->social["okAppID"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[okAppID]" id="wpd-ok-app-id" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["okAppID"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="okAppKey">
    <div class="wpd-opt-name">
        <label for="okAppKey"><?php echo esc_html($setting["options"]["okAppKey"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["okAppKey"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input placeholder="<?php esc_attr_e("Application Key", "wpdiscuz"); ?>" type="text" value="<?php echo esc_attr($this->social["okAppKey"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[okAppKey]" id="wpd-ok-app-key" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["okAppKey"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="okAppSecret">
    <div class="wpd-opt-name">
        <label for="okAppSecret"><?php echo esc_html($setting["options"]["okAppSecret"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["okAppSecret"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input placeholder="<?php esc_attr_e("Application Secret", "wpdiscuz"); ?>" type="text" value="<?php echo esc_attr($this->social["okAppSecret"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[okAppSecret]" id="wpd-ok-app-secret" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["okAppSecret"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->
<div class="wpd-social-label wpd-wechat" style="padding: 25px 0px 10px 0px;">
    <img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/social-icons/wechat.png")); ?>" style="vertical-align:bottom; height: 24px; margin-bottom: -2px; position: relative; border-radius: 50%;">&nbsp; WeChat
</div>
<p style="font-size: 14px; font-style: italic; margin-top: 5px;">
    <?php esc_html_e("To start using WeChat Login Button you should get AppID and Secret. Please follow to this ", "wpdiscuz"); ?> <a href="https://learn.akamai.com/en-us/webhelp/identity-cloud/technical-library/GUID-E1F062CD-BC57-45C3-9F0E-4B84470D1B57.html" target="_blank" style="font-weight: 600;"><?php esc_html_e("instruction &raquo;", "wpdiscuz"); ?></a> &nbsp;
    <?php echo esc_html__("Redirect URI", "wpdiscuz") . " : <code>" . esc_url_raw(admin_url("admin-ajax.php?action=wpd_login_callback&provider=wechat")) . "</code>"; ?>
</p>

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="enableWechatLogin">
    <div class="wpd-opt-name">
        <label for="enableWechatLogin"><?php echo esc_html($setting["options"]["enableWechatLogin"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["enableWechatLogin"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox"<?php checked($this->social["enableWechatLogin"] == 1); ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[enableWechatLogin]" id="enableWechatLogin">
            <label for="enableWechatLogin"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enableWechatLogin"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="wechatAppID">
    <div class="wpd-opt-name">
        <label for="wechatAppID"><?php echo esc_html($setting["options"]["wechatAppID"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["wechatAppID"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input placeholder="<?php esc_attr_e("App ID", "wpdiscuz"); ?>" type="text" value="<?php echo esc_attr($this->social["wechatAppID"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[wechatAppID]" id="wpd-google-client-id" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["wechatAppID"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="wechatSecret">
    <div class="wpd-opt-name">
        <label for="wechatSecret"><?php echo esc_html($setting["options"]["wechatSecret"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["wechatSecret"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input placeholder="<?php esc_attr_e("Secret", "wpdiscuz"); ?>" type="text" value="<?php echo esc_attr($this->social["wechatSecret"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[wechatSecret]" id="wpd-google-client-secret" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["wechatSecret"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<div class="wpd-social-label wpd-weibo" style="padding: 25px 0px 10px 0px;">
    <img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/social-icons/weibo.png")); ?>" style="vertical-align:bottom; height: 24px; margin-bottom: -2px; position: relative; border-radius: 50%;">&nbsp; Sina Weibo
</div>
<p style="font-size: 14px; font-style: italic; margin-top: 5px;">
    <?php esc_html_e("To start using Weibo Login Button you should get App Key and App Secret. Please follow to this ", "wpdiscuz"); ?> <a href="https://learn.akamai.com/en-us/webhelp/identity-cloud/technical-library/GUID-8B0552FD-A5AE-49D2-9888-C4652FECF33D.html" target="_blank" style="font-weight: 600;"><?php esc_html_e("instruction &raquo;", "wpdiscuz"); ?></a> &nbsp;
    <?php echo esc_html__("Redirect URI", "wpdiscuz") . " : <code>" . esc_url_raw(admin_url("admin-ajax.php?action=wpd_login_callback&provider=weibo")) . "</code>"; ?>
</p>

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="enableWeiboLogin">
    <div class="wpd-opt-name">
        <label for="enableWeiboLogin"><?php echo esc_html($setting["options"]["enableWeiboLogin"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["enableWeiboLogin"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox"<?php checked($this->social["enableWeiboLogin"] == 1); ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[enableWeiboLogin]" id="enableWeiboLogin">
            <label for="enableWeiboLogin"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enableWeiboLogin"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="weiboKey">
    <div class="wpd-opt-name">
        <label for="weiboKey"><?php echo esc_html($setting["options"]["weiboKey"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["weiboKey"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input placeholder="<?php esc_attr_e("Key", "wpdiscuz"); ?>" type="text" value="<?php echo esc_attr($this->social["weiboKey"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[weiboKey]" id="wpd-google-client-id" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["weiboKey"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="weiboSecret">
    <div class="wpd-opt-name">
        <label for="weiboSecret"><?php echo esc_html($setting["options"]["weiboSecret"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["weiboSecret"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input placeholder="<?php esc_attr_e("Secret", "wpdiscuz"); ?>" type="text" value="<?php echo esc_attr($this->social["weiboSecret"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[weiboSecret]" id="wpd-google-client-secret" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["weiboSecret"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<div class="wpd-social-label wpd-qq" style="padding: 25px 0px 10px 0px;">
    <img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/social-icons/qq.png")); ?>" style="vertical-align:bottom; height: 24px; margin-bottom: -2px; position: relative; border-radius: 50%;">&nbsp; Tencent QQ
</div>
<p style="font-size: 14px; font-style: italic; margin-top: 5px;">
    <?php esc_html_e("To start using Tencent QQ Login Button you should get AppID and AppKey. Please follow to this ", "wpdiscuz"); ?> <a href="https://learn.akamai.com/en-us/webhelp/identity-cloud/technical-library/GUID-2B5EC7CA-3EEE-47DC-BC21-D200AED25E22.html" target="_blank" style="font-weight: 600;"><?php esc_html_e("instruction &raquo;", "wpdiscuz"); ?></a> &nbsp;
    <?php echo esc_html__("Redirect URI", "wpdiscuz") . " : <code>" . esc_url_raw(admin_url("admin-ajax.php?action=wpd_login_callback&provider=qq")) . "</code>"; ?>
</p>

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="enableQQLogin">
    <div class="wpd-opt-name">
        <label for="enableQQLogin"><?php echo esc_html($setting["options"]["enableQQLogin"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["enableQQLogin"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox"<?php checked($this->social["enableQQLogin"] == 1); ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[enableQQLogin]" id="enableQQLogin">
            <label for="enableQQLogin"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enableQQLogin"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="qqAppID">
    <div class="wpd-opt-name">
        <label for="qqAppID"><?php echo esc_html($setting["options"]["qqAppID"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["qqAppID"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input placeholder="<?php esc_attr_e("App ID", "wpdiscuz"); ?>" type="text" value="<?php echo esc_attr($this->social["qqAppID"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[qqAppID]" id="wpd-google-client-id" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["qqAppID"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="qqSecret">
    <div class="wpd-opt-name">
        <label for="qqSecret"><?php echo esc_html($setting["options"]["qqSecret"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["qqSecret"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input placeholder="<?php esc_attr_e("Secret", "wpdiscuz"); ?>" type="text" value="<?php echo esc_attr($this->social["qqSecret"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[qqSecret]" id="wpd-google-client-secret" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["qqSecret"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<div class="wpd-social-label wpd-baidu" style="padding: 25px 0px 10px 0px;">
    <img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/social-icons/baidu.png")); ?>" style="vertical-align:bottom; height: 24px; margin-bottom: -2px; position: relative; border-radius: 50%;">&nbsp; Baidu
</div>
<p style="font-size: 14px; font-style: italic; margin-top: 5px;">
    <?php esc_html_e("To start using Baidu Login Button you should get Client Id and Client Secret. Please follow to this ", "wpdiscuz"); ?> <a href="https://auth0.com/docs/connections/social/baidu" target="_blank" style="font-weight: 600;"><?php esc_html_e("instruction &raquo;", "wpdiscuz"); ?></a> &nbsp;
    <?php echo esc_html__("Redirect URI", "wpdiscuz") . " : <code>" . esc_url_raw(admin_url("admin-ajax.php?action=wpd_login_callback&provider=baidu")) . "</code>"; ?>
</p>

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="enableBaiduLogin">
    <div class="wpd-opt-name">
        <label for="enableBaiduLogin"><?php echo esc_html($setting["options"]["enableBaiduLogin"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["enableBaiduLogin"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox"<?php checked($this->social["enableBaiduLogin"] == 1); ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[enableBaiduLogin]" id="enableBaiduLogin">
            <label for="enableBaiduLogin"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enableBaiduLogin"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="baiduAppID">
    <div class="wpd-opt-name">
        <label for="baiduAppID"><?php echo esc_html($setting["options"]["baiduAppID"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["baiduAppID"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input placeholder="<?php esc_attr_e("App ID", "wpdiscuz"); ?>" type="text" value="<?php echo esc_attr($this->social["baiduAppID"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[baiduAppID]" id="wpd-google-client-id" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["baiduAppID"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row wpd-opt-row-tight" data-wpd-opt="baiduSecret">
    <div class="wpd-opt-name">
        <label for="baiduSecret"><?php echo esc_html($setting["options"]["baiduSecret"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["baiduSecret"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input placeholder="<?php esc_attr_e("Secret", "wpdiscuz"); ?>" type="text" value="<?php echo esc_attr($this->social["baiduSecret"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_SOCIAL); ?>[baiduSecret]" id="wpd-google-client-secret" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["baiduSecret"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->
