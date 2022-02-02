<?php
if (!defined("ABSPATH")) {
    exit();
}
?>
<!-- Option start -->
<div class="wpd-opt-row">
    <div class="wpd-opt-intro">
        <img class="wpd-opt-img" src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/" . $setting["icon"])); ?>" style="height: 90px; padding-top: 5px;"/>
        <?php esc_html_e("Here you can manage comment thread styles, custom colors and add custom CSS. By default wpDiscuz comes with &laquo;Light&raquo; style. If your theme style is dark, we recommend choose the &laquo;Dark&raquo; option for comments too. In case you want to totally customize comment style or create it from scratch, we recommend choose the &laquo;Off&raquo; option to stop loading wpDiscuz core CSS. In this case only basic CSS code will be loaded allowing you add your custom style easier.", "wpdiscuz"); ?>
    </div>
    <div class="wpd-opt-doc" style="padding-top: 10px;">
        <a href="https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/styles-and-colors/" title="<?php esc_attr_e("Read the documentation", "wpdiscuz") ?>" target="_blank"><i class="far fa-question-circle"></i></a>
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
        <div class="wpd-switch-field">
            <input <?php checked($this->thread_styles["theme"] == "wpd-minimal"); ?> value="wpd-minimal" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_STYLES); ?>[theme]" id="themeMinimal" type="radio"><label for="themeMinimal"><?php esc_html_e("Off", "wpdiscuz"); ?></label>
            <input <?php checked($this->thread_styles["theme"] == "wpd-default"); ?> value="wpd-default" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_STYLES); ?>[theme]" id="themeDefault" type="radio"><label for="themeDefault"><?php esc_html_e("Default", "wpdiscuz"); ?></label>
            <input <?php checked($this->thread_styles["theme"] == "wpd-dark"); ?> value="wpd-dark" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_STYLES); ?>[theme]" id="themeDark" type="radio"><label for="themeDark"><?php esc_html_e("Dark", "wpdiscuz"); ?></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["theme"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="styleSpecificColors">
    <div class="wpd-opt-input" style="width: calc(100% - 40px);">
        <h2 style="margin-bottom: 0px;font-size: 15px; color: #555;"><?php echo esc_html($setting["options"]["styleSpecificColors"]["label"]) ?></h2>
        <p class="wpd-desc"><?php echo $setting["options"]["styleSpecificColors"]["description"] ?></p>
        <div class="wpd-default-style-colors" style="float: left; width: 48%;">
            <h4 style="font-size: 14px; color: #0c8d71;"><?php _e("Default Style","wpdiscuz"); ?></h4>
            <div class="wpd-scol-wrap">
                <input type="text" class="wpdiscuz-color-picker regular-text" value="<?php echo esc_attr($this->thread_styles["defaultCommentAreaBG"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_STYLES); ?>[defaultCommentAreaBG]" placeholder="<?php esc_attr_e("default", "wpdiscuz"); ?>"/>
                <label><?php esc_html_e("Comment Area Background", "wpdiscuz"); ?></label>
            </div>
            <div class="wpd-scol-wrap">
                <input type="text" class="wpdiscuz-color-picker regular-text" value="<?php echo esc_attr($this->thread_styles["defaultCommentTextColor"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_STYLES); ?>[defaultCommentTextColor]" placeholder="<?php esc_attr_e("default", "wpdiscuz"); ?>"/>
                <label><?php esc_html_e("Comment Text", "wpdiscuz"); ?></label>
            </div>
            <div class="wpd-scol-wrap">
                <input type="text" class="wpdiscuz-color-picker regular-text" value="<?php echo esc_attr($this->thread_styles["defaultCommentFieldsBG"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_STYLES); ?>[defaultCommentFieldsBG]" placeholder="<?php esc_attr_e("default", "wpdiscuz"); ?>"/>
                <label><?php esc_html_e("Comment Fields Background", "wpdiscuz"); ?></label>
            </div>
            <div class="wpd-scol-wrap">
                <input type="text" class="wpdiscuz-color-picker regular-text" value="<?php echo esc_attr($this->thread_styles["defaultCommentFieldsBorderColor"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_STYLES); ?>[defaultCommentFieldsBorderColor]" placeholder="<?php esc_attr_e("default", "wpdiscuz"); ?>"/>
                <label><?php esc_html_e("Comment Fields Border", "wpdiscuz"); ?></label>
            </div>
            <div  class="wpd-scol-wrap">
                <input type="text" class="wpdiscuz-color-picker regular-text" value="<?php echo esc_attr($this->thread_styles["defaultCommentFieldsTextColor"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_STYLES); ?>[defaultCommentFieldsTextColor]" placeholder="<?php esc_attr_e("default", "wpdiscuz"); ?>"/>
                <label><?php esc_html_e("Comment Fields Text", "wpdiscuz"); ?></label>
            </div>
            <div class="wpd-scol-wrap">
                <input type="text" class="wpdiscuz-color-picker regular-text" value="<?php echo esc_attr($this->thread_styles["defaultCommentFieldsPlaceholderColor"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_STYLES); ?>[defaultCommentFieldsPlaceholderColor]" placeholder="<?php esc_attr_e("default", "wpdiscuz"); ?>"/>
                <label><?php esc_html_e("Comment Fields Placeholder", "wpdiscuz"); ?></label>
            </div>
        </div>
        <div class="wpd-dark-style-colors" style="float: right; width: 48%;">
            <h4 style="font-size: 14px; color: #222;"><?php _e("Dark Style", "wpdiscuz"); ?></h4>
            <div class="wpd-scol-wrap" style="background: #F5F5F5;">
                <input type="text" class="wpdiscuz-color-picker regular-text" value="<?php echo esc_attr($this->thread_styles["darkCommentAreaBG"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_STYLES); ?>[darkCommentAreaBG]" placeholder="<?php esc_attr_e("default", "wpdiscuz"); ?>"/>
                <label><?php esc_html_e("Comment Area Background", "wpdiscuz"); ?></label>
            </div>
            <div class="wpd-scol-wrap" style="background: #F5F5F5;">
                <input type="text" class="wpdiscuz-color-picker regular-text" value="<?php echo esc_attr($this->thread_styles["darkCommentTextColor"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_STYLES); ?>[darkCommentTextColor]" placeholder="<?php esc_attr_e("default", "wpdiscuz"); ?>"/>
                <label><?php esc_html_e("Comment Text", "wpdiscuz"); ?></label>
            </div>
            <div class="wpd-scol-wrap" style="background: #F5F5F5;">
                <input type="text" class="wpdiscuz-color-picker regular-text" value="<?php echo esc_attr($this->thread_styles["darkCommentFieldsBG"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_STYLES); ?>[darkCommentFieldsBG]" placeholder="<?php esc_attr_e("default", "wpdiscuz"); ?>"/>
                <label><?php esc_html_e("Comment Fields Background", "wpdiscuz"); ?></label>
            </div>
            <div class="wpd-scol-wrap" style="background: #F5F5F5;">
                <input type="text" class="wpdiscuz-color-picker regular-text" value="<?php echo esc_attr($this->thread_styles["darkCommentFieldsBorderColor"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_STYLES); ?>[darkCommentFieldsBorderColor]" placeholder="<?php esc_attr_e("default", "wpdiscuz"); ?>"/>
                <label><?php esc_html_e("Comment Fields Border", "wpdiscuz"); ?></label>
            </div>
            <div class="wpd-scol-wrap" style="background: #F5F5F5;">
                <input type="text" class="wpdiscuz-color-picker regular-text" value="<?php echo esc_attr($this->thread_styles["darkCommentFieldsTextColor"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_STYLES); ?>[darkCommentFieldsTextColor]" placeholder="<?php esc_attr_e("default", "wpdiscuz"); ?>"/>
                <label><?php esc_html_e("Comment Fields Text", "wpdiscuz"); ?></label>
            </div>
            <div class="wpd-scol-wrap" style="background: #F5F5F5;">
                <input type="text" class="wpdiscuz-color-picker regular-text" value="<?php echo esc_attr($this->thread_styles["darkCommentFieldsPlaceholderColor"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_STYLES); ?>[darkCommentFieldsPlaceholderColor]" placeholder="<?php esc_attr_e("default", "wpdiscuz"); ?>"/>
                <label><?php esc_html_e("Comment Fields Placeholder", "wpdiscuz"); ?></label>
            </div>
        </div>
        <div style="clear: both"></div>
    </div>
    <div class="wpd-opt-doc" style="padding-top: 36px;">
        <?php $this->printDocLink($setting["options"]["styleSpecificColors"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="colors">
    <div class="wpd-opt-input" style="width: calc(100% - 40px);">
        <h2 style="margin-bottom: 0px;font-size: 15px; color: #555;"><?php echo esc_html($setting["options"]["colors"]["label"]) ?></h2>
        <p class="wpd-desc"><?php echo $setting["options"]["colors"]["description"] ?></p>
        <hr />
        <div class="wpd-color-wrap">
            <input type="text" class="wpdiscuz-color-picker regular-text" value="<?php echo esc_attr($this->thread_styles["primaryColor"]); ?>" id="primaryColor" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_STYLES); ?>[primaryColor]" placeholder="<?php esc_attr_e("Example: #00FF00", "wpdiscuz"); ?>"/>
            <label><?php esc_html_e("Primary Color", "wpdiscuz"); ?></label>
        </div>
        <div class="wpd-color-wrap">
            <input type="text" class="wpdiscuz-color-picker regular-text" value="<?php echo esc_attr($this->thread_styles["newLoadedCommentBGColor"]); ?>" id="newLoadedCommentBGColor" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_STYLES); ?>[newLoadedCommentBGColor]" placeholder="<?php esc_attr_e("Example: #00FF00", "wpdiscuz"); ?>"/>
            <label><?php esc_html_e("Unread comments background", "wpdiscuz"); ?></label>
        </div>
        <div class="wpd-color-wrap">
            <input type="text" class="wpdiscuz-color-picker regular-text" value="<?php echo esc_attr($this->thread_styles["primaryButtonColor"]); ?>" id="primaryButtonColor" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_STYLES); ?>[primaryButtonColor]" placeholder="<?php esc_attr_e("Text Color", "wpdiscuz"); ?>"/>
            <label><?php esc_html_e("Primary buttons text", "wpdiscuz"); ?></label>
        </div>
        <div class="wpd-color-wrap">
            <input type="text" class="wpdiscuz-color-picker regular-text" value="<?php echo esc_attr($this->thread_styles["primaryButtonBG"]); ?>" id="primaryButtonBG" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_STYLES); ?>[primaryButtonBG]" placeholder="<?php esc_attr_e("Background Color", "wpdiscuz"); ?>"/>
            <label><?php esc_html_e("Primary buttons background", "wpdiscuz"); ?></label>
        </div>
        <div class="wpd-color-wrap">
            <input type="text" class="wpdiscuz-color-picker regular-text" value="<?php echo esc_attr($this->thread_styles["bubbleColors"]); ?>" id="bubbleColors" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_STYLES); ?>[bubbleColors]" placeholder="<?php esc_attr_e("Example: #00FF00", "wpdiscuz"); ?>"/>
            <label><?php esc_html_e("Comment Bubble Colors", "wpdiscuz"); ?></label>
        </div>
        <div class="wpd-color-wrap">
            <input type="text" class="wpdiscuz-color-picker regular-text" value="<?php echo esc_attr($this->thread_styles["inlineFeedbackColors"]); ?>" id="inlineFeedbackColors" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_STYLES); ?>[inlineFeedbackColors]" placeholder="<?php esc_attr_e("Example: #00FF00", "wpdiscuz"); ?>"/>
            <label><?php esc_html_e("Inline Feedback Icon Colors", "wpdiscuz"); ?></label>
        </div>
        <div style="clear: both"></div>
    </div>
    <div class="wpd-opt-doc" style="padding-top: 36px;">
        <?php $this->printDocLink($setting["options"]["colors"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<div style="padding: 20px 0 30px 0; font-size: 14px;">
    <a href="<?php echo admin_url("admin.php?page=" . WpdiscuzCore::PAGE_SETTINGS . "&wpd_tab=rating") ?>"><?php _e("Comment Rating Colors","wpdiscuz"); ?></a> and <a href="<?php echo admin_url("admin.php?page=" . WpdiscuzCore::PAGE_SETTINGS . "&wpd_tab=labels") ?>"><?php _e("Comment Author Label Colors","wpdiscuz"); ?></a>
</div>

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="enableFontAwesome">
    <div class="wpd-opt-name">
        <label for="enableFontAwesome"><?php echo esc_html($setting["options"]["enableFontAwesome"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["enableFontAwesome"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->thread_styles["enableFontAwesome"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_STYLES); ?>[enableFontAwesome]" id="enableFontAwesome">
            <label for="enableFontAwesome"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enableFontAwesome"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="customCss">
    <div class="wpd-opt-name" style="width: 28%;">
        <label for="customCss"><?php echo esc_html($setting["options"]["customCss"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["customCss"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input" style="width: 69%; text-align: right;">
        <textarea class="regular-text" id="customCss" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_STYLES); ?>[customCss]" placeholder="" style="width: 90%; height: 100px; color: #333333; font-family: 'Courier New', Courier, monospace; background: #f5f5f5;direction:ltr;text-align:left;"><?php echo stripslashes($this->thread_styles["customCss"]); ?></textarea>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["customCss"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->
