<?php
if (!defined("ABSPATH")) {
    exit();
}
?>
<!-- Option start -->
<div class="wpd-opt-row">
    <div  class="wpd-opt-intro">
        <img class="wpd-opt-img" src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/" . $setting["icon"])); ?>" style="height: 56px; padding-top: 5px;"/>
        <?php echo sprintf(esc_html__("wpDiscuz allows you to customize comment form layout and fields. You can create as much comment forms as you want and attach those to certain post type or page. Please navigate to %s page to manage comment form specific settings.", "wpdiscuz"), "<a href='" . esc_url_raw(admin_url("edit.php?post_type=wpdiscuz_form")) . "'>" . esc_html__("Comment Form Manager", "wpdiscuz") . "</a>"); ?>
    </div>
    <div class="wpd-opt-doc" style="padding-top: 10px;">
        <a href="https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-form/" title="<?php esc_attr_e("Read the documentation", "wpdiscuz") ?>" target="_blank"><i class="far fa-question-circle"></i></a>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="commentFormView">
    <div class="wpd-opt-name">
        <label for="commentFormView"><?php echo esc_html($setting["options"]["commentFormView"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["commentFormView"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switch-field">
            <input type="radio" value="collapsed" <?php checked("collapsed" === $this->form["commentFormView"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_FORM); ?>[commentFormView]" id="collapsedForm" />
            <label for="collapsedForm" style="min-width:60px;"><?php esc_html_e("collapsed", "wpdiscuz"); ?></label>
            <input type="radio" value="expanded" <?php checked("expanded" === $this->form["commentFormView"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_FORM); ?>[commentFormView]" id="expandedForm" />
            <label for="expandedForm" style="min-width:60px;"><?php esc_html_e("expanded", "wpdiscuz"); ?></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["commentFormView"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="enableDropAnimation">
    <div class="wpd-opt-name">
        <label for="enableDropAnimation"><?php echo esc_html($setting["options"]["enableDropAnimation"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["enableDropAnimation"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->form["enableDropAnimation"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_FORM); ?>[enableDropAnimation]" id="enableDropAnimation">
            <label for="enableDropAnimation"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enableDropAnimation"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="richEditor">
    <div class="wpd-opt-name">
        <label for="richEditor"><?php echo esc_html($setting["options"]["richEditor"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["richEditor"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-radio">
            <input type="radio" value="both" <?php checked("both" === $this->form["richEditor"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_FORM); ?>[richEditor]" id="richEditorBoth" class="richEditor"/>
            <label for="richEditorBoth" class="wpd-radio-circle"></label>
            <label for="richEditorBoth"><?php esc_html_e("Mobile and Desktop", "wpdiscuz") ?></label>
        </div>
        <div class="wpd-radio">
            <input type="radio" value="desktop" <?php checked("desktop" === $this->form["richEditor"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_FORM); ?>[richEditor]" id="richEditorDesktop" class="richEditor"/>
            <label for="richEditorDesktop" class="wpd-radio-circle"></label>
            <label for="richEditorDesktop"><?php esc_html_e("Only Desktop", "wpdiscuz") ?></label>
        </div>
        <div class="wpd-radio">
            <input type="radio" value="none" <?php checked("none" === $this->form["richEditor"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_FORM); ?>[richEditor]" id="richEditorNone" class="richEditor"/>
            <label for="richEditorNone" class="wpd-radio-circle"></label>
            <label for="richEditorNone"><?php esc_html_e("None", "wpdiscuz") ?></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["richEditor"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="editorToolbar">
    <div class="wpd-opt-name" style="width: 100%;">
        <label for="editorToolbar"><?php echo esc_html($setting["options"]["editorToolbar"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["editorToolbar"]["description"] ?></p>
        <div class="wpd-editor-toolbar">
            <div id="wpdeb_b" title="<?php esc_attr_e("Bold", "wpdiscuz"); ?>" class="<?php echo $this->form["boldButton"] ? "wpd-enabled" : "wpd-disabled"; ?> wpd-editor-button" style="background-position: 1px 0;"></div>
            <input type="hidden" id="wpdeb_b-button" value="<?php echo esc_attr($this->form["boldButton"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_FORM); ?>[boldButton]" />
            <div id="wpdeb_i" title="<?php esc_attr_e("Italic", "wpdiscuz"); ?>" class="<?php echo $this->form["italicButton"] ? "wpd-enabled" : "wpd-disabled"; ?> wpd-editor-button" style="background-position: -32px 0;"></div>
            <input type="hidden" id="wpdeb_i-button" value="<?php echo esc_attr($this->form["italicButton"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_FORM); ?>[italicButton]" />
            <div id="wpdeb_u" title="<?php esc_attr_e("Underline", "wpdiscuz"); ?>" class="<?php echo $this->form["underlineButton"] ? "wpd-enabled" : "wpd-disabled"; ?> wpd-editor-button" style="background-position: -66px 0;"></div>
            <input type="hidden" id="wpdeb_u-button" value="<?php echo esc_attr($this->form["underlineButton"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_FORM); ?>[underlineButton]" />
            <div id="wpdeb_s" title="<?php esc_attr_e("Strike", "wpdiscuz"); ?>" class="<?php echo $this->form["strikeButton"] ? "wpd-enabled" : "wpd-disabled"; ?> wpd-editor-button" style="background-position: -100px 0;"></div>
            <input type="hidden" id="wpdeb_s-button" value="<?php echo esc_attr($this->form["strikeButton"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_FORM); ?>[strikeButton]" />
            <div id="wpdeb_ol" title="<?php esc_attr_e("Ordered List", "wpdiscuz"); ?>" class="<?php echo $this->form["olButton"] ? "wpd-enabled" : "wpd-disabled"; ?> wpd-editor-button" style="background-position: -132px 0;"></div>
            <input type="hidden" id="wpdeb_ol-button" value="<?php echo esc_attr($this->form["olButton"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_FORM); ?>[olButton]" />
            <div id="wpdeb_ul" title="<?php esc_attr_e("Unordered List", "wpdiscuz"); ?>" class="<?php echo $this->form["ulButton"] ? "wpd-enabled" : "wpd-disabled"; ?> wpd-editor-button" style="background-position: -167px 0;"></div>
            <input type="hidden" id="wpdeb_ul-button" value="<?php echo esc_attr($this->form["ulButton"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_FORM); ?>[ulButton]" />
            <div id="wpdeb_q" title="<?php esc_attr_e("Blockquote", "wpdiscuz"); ?>" class="<?php echo $this->form["blockquoteButton"] ? "wpd-enabled" : "wpd-disabled"; ?> wpd-editor-button" style="background-position: -200px 0;"></div>
            <input type="hidden" id="wpdeb_q-button" value="<?php echo esc_attr($this->form["blockquoteButton"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_FORM); ?>[blockquoteButton]" />
            <div id="wpdeb_c" title="<?php esc_attr_e("Code Block", "wpdiscuz"); ?>" class="<?php echo $this->form["codeblockButton"] ? "wpd-enabled" : "wpd-disabled"; ?> wpd-editor-button" style="background-position: -234px 0;"></div>
            <input type="hidden" id="wpdeb_c-button" value="<?php echo esc_attr($this->form["codeblockButton"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_FORM); ?>[codeblockButton]" />
            <div id="wpdeb_l" title="<?php esc_attr_e("Link", "wpdiscuz"); ?>" class="<?php echo $this->form["linkButton"] ? "wpd-enabled" : "wpd-disabled"; ?> wpd-editor-button" style="background-position: -268px 0;"></div>
            <input type="hidden" id="wpdeb_l-button" value="<?php echo esc_attr($this->form["linkButton"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_FORM); ?>[linkButton]" />
            <div id="wpdeb_sr" title="<?php esc_attr_e("Source Code", "wpdiscuz"); ?>" class="<?php echo $this->form["sourcecodeButton"] ? "wpd-enabled" : "wpd-disabled"; ?> wpd-editor-button" style="background-position: -300px 0;"></div>
            <input type="hidden" id="wpdeb_sr-button" value="<?php echo esc_attr($this->form["sourcecodeButton"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_FORM); ?>[sourcecodeButton]" />
            <div id="wpdeb_sp" title="<?php esc_attr_e("Spoiler", "wpdiscuz"); ?>" class="<?php echo $this->form["spoilerButton"] ? "wpd-enabled" : "wpd-disabled"; ?> wpd-editor-button" style="background-position: -330px 0; width: 33px;"></div>
            <input type="hidden" id="wpdeb_sp-button" value="<?php echo esc_attr($this->form["spoilerButton"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_FORM); ?>[spoilerButton]" />

            <div id="wpdeb_disable" class="wpd-editor-disable" style="color: #eb5454;display:<?php echo $this->showEditorToolbar() ? "block" : "none"; ?>;"><?php esc_html_e("Disable formatting toolbar", "wpdiscuz"); ?></div>
            <div id="wpdeb_enable" class="wpd-editor-disable" style="color: #41ABAB;display:<?php echo $this->showEditorToolbar() ? "none" : "block"; ?>;"><?php esc_html_e("Enable formatting toolbar", "wpdiscuz"); ?></div>

            <div style="flex-basis: 2%;">&nbsp;</div>
            <a href="<?php echo esc_url_raw(admin_url("admin.php?page=wpdiscuz_options_page&wpd_tab=content#wmuIsEnabled")); ?>" title="<?php echo esc_html__('Go to "Comment Content and Media" admin page to manage image attachment settings', 'wpdiscuz') ?>"><span class="wpd-editor-attachment"></span></a>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["editorToolbar"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="enableQuickTags">
    <div class="wpd-opt-name">
        <label for="enableQuickTags"><?php echo esc_html($setting["options"]["enableQuickTags"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["enableQuickTags"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->form["enableQuickTags"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_FORM); ?>[enableQuickTags]" id="enableQuickTags">
            <label for="enableQuickTags"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enableQuickTags"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="commenterNameLength">
    <div class="wpd-opt-name">
        <label for="commenterNameLength"><?php echo esc_html($setting["options"]["commenterNameLength"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["commenterNameLength"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <span><input type="number" value="<?php echo esc_attr($this->form["commenterNameMinLength"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_FORM); ?>[commenterNameMinLength]" id="commenterNameMinLength" style="width:70px;"> <?php esc_html_e("Min", "wpdiscuz") ?> </span>
        <span>&nbsp; <input type="number" value="<?php echo esc_attr($this->form["commenterNameMaxLength"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_FORM); ?>[commenterNameMaxLength]" id="commenterNameMaxLength" style="width:70px;"> <?php esc_html_e("Max", "wpdiscuz") ?></span>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["commenterNameLength"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="storeCommenterData">
    <div class="wpd-opt-name">
        <label for="storeCommenterData"><?php echo esc_html($setting["options"]["storeCommenterData"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["storeCommenterData"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input type="number" value="<?php echo esc_attr($this->form["storeCommenterData"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_FORM); ?>[storeCommenterData]" id="storeCommenterData" style="width:100px;">
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["storeCommenterData"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->