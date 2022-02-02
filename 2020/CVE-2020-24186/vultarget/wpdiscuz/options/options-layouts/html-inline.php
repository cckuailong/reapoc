<?php
if (!defined("ABSPATH")) {
    exit();
}
?>
<!-- Option start -->
<div class="wpd-opt-row">
    <div class="wpd-opt-intro">
        <img class="wpd-opt-img" src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/" . $setting["icon"])); ?>" style="height: 70px; padding-top: 5px;"/>
	    <?php esc_html_e("Article Inline Feedback feature is an interactive article reading option with author's questions and readers feedback (comments). Now article authors can add some questions for readers on certain part of article content and ask for feedback while visitors read it.", "wpdiscuz"); ?>
	    <?php esc_html_e("You can add Inline Feedback button in post content using &laquo;Comment&raquo; button on post editor toolbar.", "wpdiscuz"); ?>
        <div class="wpd-zoom-image" style="width: 100%;">
            <a href="#img111"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/inline-feedback-toolbar-button.png")); ?>" style="margin-top: 15px; margin-left: 5px; width: 100%;"/></a>
            <a href="#_" class="wpd-lightbox" id="img111"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/inline-feedback-toolbar-button-vertical.png")); ?>"/></a>
        </div>
	    <div style="padding: 10px 5px;">
		    <?php esc_html_e("Once a question is added in article editor (backend), on article (front-end) readers will see a small comment icon next to the text part you've selected. This feature engages post readers inviting them comment and leave a feedback while reading without scrolling down and using the standard comment form.", "wpdiscuz"); ?>
        </div>
    </div>
    <div class="wpd-opt-doc" style="padding-top: 10px;">
        <a href="https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/inline-commenting/" title="<?php esc_attr_e("Read the documentation", "wpdiscuz") ?>" target="_blank"><i class="far fa-question-circle"></i></a>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="showInlineFilterButton">
    <div class="wpd-opt-name">
        <label for="showInlineFilterButton"><?php echo esc_html($setting["options"]["showInlineFilterButton"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["showInlineFilterButton"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->inline["showInlineFilterButton"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_INLINE); ?>[showInlineFilterButton]" id="showInlineFilterButton">
            <label for="showInlineFilterButton"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["showInlineFilterButton"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->


<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="inlineFeedbackAttractionType">
    <div class="wpd-opt-name">
        <label><?php echo esc_html($setting["options"]["inlineFeedbackAttractionType"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["inlineFeedbackAttractionType"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-radio">
            <input type="radio" value="disable" <?php checked("disable" == $this->inline["inlineFeedbackAttractionType"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_INLINE); ?>[inlineFeedbackAttractionType]" id="inlineFeedbackAttractionTypeDisable" class="inlineFeedbackAttractionType"/>
            <label for="inlineFeedbackAttractionTypeDisable" class="wpd-radio-circle"></label>
            <label for="inlineFeedbackAttractionTypeDisable"><?php esc_html_e("Disable", "wpdiscuz") ?></label>
        </div>
        <div class="wpd-radio">
            <input type="radio" value="blink" <?php checked("blink" == $this->inline["inlineFeedbackAttractionType"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_INLINE); ?>[inlineFeedbackAttractionType]" id="inlineFeedbackAttractionTypeBlink" class="inlineFeedbackAttractionType"/>
            <label for="inlineFeedbackAttractionTypeBlink" class="wpd-radio-circle"></label>
            <label for="inlineFeedbackAttractionTypeBlink"><?php esc_html_e("Animate (blink and wiggle)", "wpdiscuz") ?></label>
        </div>
        <div class="wpd-radio">
            <input type="radio" value="scroll_open" <?php checked("scroll_open" == $this->inline["inlineFeedbackAttractionType"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_INLINE); ?>[inlineFeedbackAttractionType]" id="inlineFeedbackAttractionTypeScrollOpen" class="inlineFeedbackAttractionType"/>
            <label for="inlineFeedbackAttractionTypeScrollOpen" class="wpd-radio-circle"></label>
            <label for="inlineFeedbackAttractionTypeScrollOpen"><?php esc_html_e("Open the Feedback Form on scroll", "wpdiscuz") ?></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["inlineFeedbackAttractionType"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

