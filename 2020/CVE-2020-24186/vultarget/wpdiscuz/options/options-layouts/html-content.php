<?php
if (!defined("ABSPATH")) {
    exit();
}
?>
<!-- Option start -->
<div class="wpd-opt-row">
    <div class="wpd-opt-intro">
        <img class="wpd-opt-img" src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/" . $setting["icon"])); ?>" style="height: 70px; padding-top: 5px;"/>
	    <?php echo sprintf(esc_html__("Here you can manage comment content related options, such as comment text length, comment content breaking, comment image attachment... By default wpDiscuz allows to attach one image with comment. For an advanced media uploading and file attachment options we recommend %s addon.", "wpdiscuz"), "<a href='https://gvectors.com/product/wpdiscuz-media-uploader/' target='_blank' style='color:#07B290;'>wpDiscuz Media Uploader</a>") ?>
    </div>
    <div class="wpd-opt-doc" style="padding-top: 10px;">
        <a href="https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-content-and-media/" title="<?php esc_attr_e("Read the documentation", "wpdiscuz") ?>" target="_blank"><i class="far fa-question-circle"></i></a>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="commentTextLength">
    <div class="wpd-opt-name">
        <label for="commentTextLength"><?php echo esc_html($setting["options"]["commentTextLength"]["label"]); ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["commentTextLength"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <span for="commentTextMinLength"><input type="number" value="<?php echo esc_attr($this->content["commentTextMinLength"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_CONTENT); ?>[commentTextMinLength]" id="commentTextMinLength" style="width:70px;"> <?php esc_html_e("Min", "wpdiscuz") ?> </span>
        <span for="commentTextMaxLength">&nbsp; <input type="number" placeholder="&infin;" value="<?php echo esc_attr($this->content["commentTextMaxLength"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_CONTENT); ?>[commentTextMaxLength]" id="commentTextMaxLength" style="width:70px;"> <?php esc_html_e("Max", "wpdiscuz") ?></span>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["commentTextLength"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="enableImageConversion">
    <div class="wpd-opt-name">
        <label for="enableImageConversion"><?php echo esc_html($setting["options"]["enableImageConversion"]["label"]); ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["enableImageConversion"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->content["enableImageConversion"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_CONTENT); ?>[enableImageConversion]" id="enableImageConversion">
            <label for="enableImageConversion"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enableImageConversion"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="enableShortcodes">
    <div class="wpd-opt-name">
        <label for="enableShortcodes"><?php echo esc_html($setting["options"]["enableShortcodes"]["label"]); ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["enableShortcodes"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->content["enableShortcodes"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_CONTENT); ?>[enableShortcodes]" id="enableShortcodes">
            <label for="enableShortcodes"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enableShortcodes"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="commentReadMoreLimit">
    <div class="wpd-opt-name">
        <label for="commentReadMoreLimit"><?php echo esc_html($setting["options"]["commentReadMoreLimit"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["commentReadMoreLimit"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input type="number" value="<?php echo esc_attr($this->content["commentReadMoreLimit"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_CONTENT); ?>[commentReadMoreLimit]" id="commentReadMoreLimit" style="width:100px;" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["commentReadMoreLimit"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<div class="wpd-subtitle">
    <span class="dashicons dashicons-paperclip"></span> <?php esc_html_e("File Attachment Settings", "wpdiscuz") ?>
</div>

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="wmuIsEnabled">
    <div class="wpd-opt-name">
        <label for="wmuIsEnabled"><?php echo esc_html($setting["options"]["wmuIsEnabled"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["wmuIsEnabled"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->content["wmuIsEnabled"] == 1); ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_CONTENT); ?>[wmuIsEnabled]" id="wmuIsEnabled">
            <label for="wmuIsEnabled"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["wmuIsEnabled"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="wmuIsGuestAllowed">
    <div class="wpd-opt-name">
        <label for="wmuIsGuestAllowed"><?php echo esc_html($setting["options"]["wmuIsGuestAllowed"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["wmuIsGuestAllowed"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->content["wmuIsGuestAllowed"] == 1); ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_CONTENT); ?>[wmuIsGuestAllowed]" id="wmuIsGuestAllowed">
            <label for="wmuIsGuestAllowed"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["wmuIsGuestAllowed"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="wmuIsLightbox">
    <div class="wpd-opt-name">
        <label for="wmuIsLightbox"><?php echo esc_html($setting["options"]["wmuIsLightbox"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["wmuIsLightbox"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->content["wmuIsLightbox"] == 1); ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_CONTENT); ?>[wmuIsLightbox]" id="wmuIsLightbox">
            <label for="wmuIsLightbox"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["wmuIsLightbox"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="wmuMimeTypes" style="display: block;">
    <div class="wpd-opt-name" style="width: 100%;">
        <div style="float: left;">
            <label for="wmuMimeTypes"><?php echo esc_html($setting["options"]["wmuMimeTypes"]["label"]) ?></label>
            <p class="wpd-desc"><?php echo $setting["options"]["wmuMimeTypes"]["description"] ?></p>
        </div>
        <div class="wpd-opt-doc" style="float: right;">
            <?php $this->printDocLink($setting["options"]["wmuMimeTypes"]["docurl"]) ?>
        </div>
        <div class="wpd-clear"></div>
    </div>
    <div class="wpd-opt-input" style="width: 100%; margin-top: 10px;">
        <?php
        $mimes = $this->getDefaultFileTypes();
        foreach ($mimes as $ext => $mime) {
            ?>
            <div class="wpd-mublock-inline wpd-mu-mimes" style="margin-right: 2px;">
                <input type="checkbox" <?php checked(isset($this->content["wmuMimeTypes"][$ext]) && $this->content["wmuMimeTypes"][$ext] == $mime); ?> value="<?php echo esc_attr($mime); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_CONTENT); ?>[wmuMimeTypes][<?php echo esc_attr($ext); ?>]" id="wmu-<?php echo esc_attr($ext); ?>" style="margin:0px; vertical-align: middle;" />
                <label for="wmu-<?php echo esc_attr($ext); ?>" title="<?php echo esc_attr($ext); ?>" style="white-space:nowrap; font-size:13px;"><?php echo esc_html($ext); ?></label>
            </div>
            <?php
        }
        ?>
        <div class="wpd-clear"></div>
        <div style="margin-top:10px;">
            <button id="wmuSelectMimes" type="button" class="button button-secondary"><?php esc_html_e("Select All", "wpdiscuz"); ?></button>
            <button id="wmuUnselectMimes" type="button" class="button button-secondary"><?php esc_html_e("Unselect All", "wpdiscuz"); ?></button>
            <button id="wmuInvertMimes" type="button" class="button button-secondary"><?php esc_html_e("Invert Selection", "wpdiscuz"); ?></button>
        </div>
    </div>
</div>
<!-- Option end -->

<?php do_action("wpdiscuz_mu_custom_mime_types", $setting, $this); ?>

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="wmuMaxFileSize">
    <div class="wpd-opt-name">
        <label for="wmuMaxFileSize"><?php echo esc_html($setting["options"]["wmuMaxFileSize"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["wmuMaxFileSize"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input step="any" type="number" value="<?php echo esc_attr($this->content["wmuMaxFileSize"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_CONTENT); ?>[wmuMaxFileSize]" id="wmuMaxFileSize" class="wmu-number" style="width: 80px;"/> <span style="vertical-align:middle;">MB</span>
        <p style="padding-top:0px;">
            <?php
            $uploadMaxFilesizeInMB = $this->wmuUploadMaxFileSize / (1024 * 1024);
            $postMaxSizeInMB = $this->wmuPostMaxSize / (1024 * 1024);
            $uploadMaxFilesizeStyle = $this->content["wmuMaxFileSize"] > $uploadMaxFilesizeInMB ? "style='color:#f00;'" : "";
            $postMaxSizeStyle = $uploadMaxFilesizeInMB > $postMaxSizeInMB || $this->content["wmuMaxFileSize"] > $postMaxSizeInMB ? "style='color:#f00;'" : "";
            ?>
            <span <?php echo $uploadMaxFilesizeStyle; ?>><?php echo esc_html__("Server 'upload_max_filesize' is ", "wpdiscuz") . $uploadMaxFilesizeInMB . "MB<br/>"; ?></span>
            <span <?php echo $postMaxSizeStyle; ?>><?php echo esc_html__("Server 'post_max_size' is ", "wpdiscuz") . $postMaxSizeInMB . "MB"; ?></span>
        </p>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["wmuMaxFileSize"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="wmuIsShowFilesDashboard">
    <div class="wpd-opt-name">
        <label for="wmuIsShowFilesDashboard"><?php echo esc_html($setting["options"]["wmuIsShowFilesDashboard"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["wmuIsShowFilesDashboard"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->content["wmuIsShowFilesDashboard"] == 1); ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_CONTENT); ?>[wmuIsShowFilesDashboard]" id="wmuIsShowFilesDashboard">
            <label for="wmuIsShowFilesDashboard"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["wmuIsShowFilesDashboard"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="wmuSingleImageSize">
    <div class="wpd-opt-name">
        <label for="wmuSingleImageSize"><?php echo esc_html($setting["options"]["wmuSingleImageSize"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["wmuSingleImageSize"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input type="text" value="<?php echo esc_attr($this->content["wmuSingleImageWidth"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_CONTENT); ?>[wmuSingleImageWidth]" id="wmuSingleImageWidth" class="wmu-number wmu-image-dimension wmu-image-width" style="width: 80px;" /><span> <?php esc_html_e("Width (px)", "wpdiscuz"); ?> </span><br>
        <input type="text" value="<?php echo esc_attr($this->content["wmuSingleImageHeight"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_CONTENT); ?>[wmuSingleImageHeight]" id="wmuSingleImageHeight" class="wmu-number wmu-image-dimension wmu-image-height" style="width: 80px;" /><span> <?php esc_html_e("Height (px)", "wpdiscuz"); ?> </span>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["wmuSingleImageSize"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="wmuImageSizes">
    <div class="wpd-opt-name">
        <label for="wmuImageSizes"><?php echo esc_html($setting["options"]["wmuImageSizes"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["wmuImageSizes"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <?php
        $allImageSizes = $this->getAllImageSizes();
        foreach ($allImageSizes as $imageSize) {
            $sizeWidth = intval(get_option("{$imageSize}_size_w"));
            $sizeHeight = intval(get_option("{$imageSize}_size_h"));
            $disabled = "";
            $checked = checked(in_array($imageSize, $this->content["wmuImageSizes"]), true, false);
            if (!$sizeWidth && !$sizeHeight) {
                $disabled = "disabled='disabled'";
            }
            ?>
            <div class="wpd-mublock">
                <input type="checkbox" <?php echo $checked; ?> <?php echo $disabled; ?> value="<?php echo esc_attr($imageSize); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_CONTENT); ?>[wmuImageSizes][]" id="wmu<?php echo esc_attr($imageSize); ?>" style="margin:0px; vertical-align: middle;" />
                <label for="wmu<?php echo esc_attr($imageSize); ?>"><?php echo esc_html($imageSize . " ( " . get_option("{$imageSize}_size_w") . " x " . get_option("{$imageSize}_size_h") . " )"); ?></label>
            </div>
            <?php
        }
        ?>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["wmuImageSizes"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->