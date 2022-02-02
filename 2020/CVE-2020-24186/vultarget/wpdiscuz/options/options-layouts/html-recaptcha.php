<?php
if (!defined("ABSPATH")) {
    exit();
}
?>
<!-- Option start -->
<div class="wpd-opt-row">
    <div class="wpd-opt-intro">
        <img class="wpd-opt-img" src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/" . $setting["icon"])); ?>" style="height: 65px;"/>
        <?php echo sprintf(esc_html__("Please %s with Google to obtain the Site Key and Secret Key for %s. Then insert those keys in according fields below.", "wpdiscuz"), "<a href='https://www.google.com/recaptcha/admin' target='_blank'>" . esc_html__("register your domain", "wpdiscuz") . "</a>", "<span style='color:#FF0000'>" . esc_html__("reCAPTCHA Version 2", "wpdiscuz") . "</span>"); ?>
        <?php echo sprintf(esc_html__("If you want to use the latest Version 3 - Invisible Google reCAPTCHA with comment form, please checkout %s addon. This will make your commenters life easier, letting them pass through with ease.", "wpdiscuz"), "<a href='https://gvectors.com/product/wpdiscuz-recaptcha/' target='_blank' style='color:#07B290;'>" . "wpDiscuz reCAPTCHA" . "</a>"); ?>
    </div>
    <div class="wpd-opt-doc" style="padding-top: 10px;">
        <a href="https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/google-recaptcha/" title="<?php esc_attr_e("Read the documentation", "wpdiscuz") ?>" target="_blank"><i class="far fa-question-circle"></i></a>
    </div>
</div>
<!-- Option end -->

<div class="wpd-subtitle">
    <?php esc_html_e("General", "wpdiscuz"); ?>
</div>

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="requestMethod">
    <div class="wpd-opt-name">
        <label for="requestMethod"><?php echo esc_html($setting["options"]["requestMethod"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["requestMethod"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <select id="requestMethod" name="<?php echo esc_attr(WpdiscuzCore::TAB_RECAPTCHA); ?>[requestMethod]">
            <option value="auto" <?php selected($this->recaptcha["requestMethod"], "auto"); ?>><?php esc_html_e("Auto", "wpdiscuz"); ?></option>
            <option value="socket" <?php selected($this->recaptcha["requestMethod"], "socket"); ?>><?php esc_html_e("SocketPost", "wpdiscuz"); ?></option>
            <option value="curl" <?php selected($this->recaptcha["requestMethod"], "curl"); ?>><?php esc_html_e("CurlPost", "wpdiscuz"); ?></option>
            <option value="post" <?php selected($this->recaptcha["requestMethod"], "post"); ?>><?php esc_html_e("Post", "wpdiscuz"); ?></option>
        </select>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["requestMethod"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="showForGuests">
    <div class="wpd-opt-name">
        <label for="showForGuests"><?php echo esc_html($setting["options"]["showForGuests"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["showForGuests"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->recaptcha["showForGuests"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_RECAPTCHA); ?>[showForGuests]" id="showForGuests">
            <label for="showForGuests"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["showForGuests"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="showForUsers">
    <div class="wpd-opt-name">
        <label for="showForUsers"><?php echo esc_html($setting["options"]["showForUsers"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["showForUsers"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php echo checked($this->recaptcha["showForUsers"] == 1); ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_RECAPTCHA); ?>[showForUsers]" id="showForUsers">
            <label for="showForUsers"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["showForUsers"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="isShowOnSubscribeForm">
    <div class="wpd-opt-name">
        <label for="isShowOnSubscribeForm"><?php echo esc_html($setting["options"]["isShowOnSubscribeForm"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["isShowOnSubscribeForm"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php echo checked($this->recaptcha["isShowOnSubscribeForm"] == 1); ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_RECAPTCHA); ?>[isShowOnSubscribeForm]" id="isShowOnSubscribeForm">
            <label for="isShowOnSubscribeForm"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["isShowOnSubscribeForm"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<div class="wpd-subtitle">
    <?php esc_html_e("reCAPTCHA v2", "wpdiscuz"); ?>
</div>

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="siteKey">
    <div class="wpd-opt-name">
        <label for="siteKey"><?php echo esc_html($setting["options"]["siteKey"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["siteKey"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input id="siteKey" type="text" name="<?php echo esc_attr(WpdiscuzCore::TAB_RECAPTCHA); ?>[siteKey]" placeholder="<?php esc_html_e("reCAPTCHA V2 Site Key", "wpdiscuz"); ?>" value="<?php echo esc_attr($this->recaptcha["siteKey"]); ?>" style="margin:1px;padding:3px 5px; width:90%;"/>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["siteKey"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="secretKey">
    <div class="wpd-opt-name">
        <label for="secretKey"><?php echo esc_html($setting["options"]["secretKey"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["secretKey"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input id="secretKey" type="text" name="<?php echo esc_attr(WpdiscuzCore::TAB_RECAPTCHA); ?>[secretKey]" placeholder="<?php esc_html_e("reCAPTCHA V2 Secret Key", "wpdiscuz"); ?>" value="<?php echo esc_attr($this->recaptcha["secretKey"]); ?>" style="margin: 1px;padding:3px 5px; width:90%;"/>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["secretKey"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="theme">
    <div class="wpd-opt-name">
        <label for="theme"><?php echo esc_html($setting["options"]["theme"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["theme"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <select id="theme" name="<?php echo esc_attr(WpdiscuzCore::TAB_RECAPTCHA); ?>[theme]">
            <option value="light" <?php selected($this->recaptcha["theme"], "light"); ?>><?php esc_html_e("Light", "wpdiscuz"); ?></option>
            <option value="dark" <?php selected($this->recaptcha["theme"], "dark"); ?>><?php esc_html_e("Dark", "wpdiscuz"); ?></option>
        </select>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["theme"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="lang">
    <div class="wpd-opt-name">
        <label for="lang"><?php echo esc_html($setting["options"]["lang"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["lang"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input id="lang" type="text" name="<?php echo esc_attr(WpdiscuzCore::TAB_RECAPTCHA); ?>[lang]" value="<?php echo esc_attr($this->recaptcha["lang"]); ?>" placeholder="<?php esc_attr_e("Example en", "wpdiscuz"); ?>" style="margin: 1px;padding:3px 5px; width:120px;"/><br />
        <a target="blanck" style="font-size: 10px;" href="https://developers.google.com/recaptcha/docs/language"><?php esc_html_e("Language codes", "wpdiscuz"); ?></a>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["lang"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->