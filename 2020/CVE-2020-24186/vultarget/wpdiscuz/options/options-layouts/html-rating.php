<?php
if (!defined("ABSPATH")) {
    exit();
}
?>
<!-- Option start -->
<div class="wpd-opt-row">
    <div class="wpd-opt-intro">
        <img class="wpd-opt-img" src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/" . $setting["icon"])); ?>" style="height: 58px; padding-top: 5px;"/>
        <?php echo sprintf(esc_html__("You have two ways to add Post Rating for your blog posts and articles. The first, comment independent type is enabled by default and appears on top of comment section. It allows people rate your articles without leaving comments. ", "wpdiscuz") . "<br><br>" . esc_html__('The second type, is the old, comment depended way. You should create a "Rating" custom field in comment form allowing people to rate while they leave a comment. If you\'ve already configured the second type (Rating comment field) in comment form, the first type will be automatically disabled. Both types of ratings can be managed in %s', "wpdiscuz"), "<a href='" . admin_url("edit.php?post_type=wpdiscuz_form") . "'>" . esc_html__("Comment Form Manager &raquo;", "wpdiscuz") . "</a>"); ?>
    </div>
    <div class="wpd-opt-doc" style="padding-top: 10px;">
        <a href="https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/article-and-comment-rating/" title="<?php esc_attr_e("Read the documentation", "wpdiscuz") ?>" target="_blank"><i class="far fa-question-circle"></i></a>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="enablePostRatingSchema">
    <div class="wpd-opt-name">
        <label for="enablePostRatingSchema"><?php echo esc_html($setting["options"]["enablePostRatingSchema"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["enablePostRatingSchema"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->rating["enablePostRatingSchema"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_RATING); ?>[enablePostRatingSchema]" id="enablePostRatingSchema">
            <label for="enablePostRatingSchema"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enablePostRatingSchema"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="displayRatingOnPost">
    <div class="wpd-opt-name">
        <label for="displayRatingOnPost"><?php echo esc_html($setting["options"]["displayRatingOnPost"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["displayRatingOnPost"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div>
            <input type="checkbox" <?php checked(in_array("before_comment_form", $this->rating["displayRatingOnPost"])) ?> value="before_comment_form" name="<?php echo esc_attr(WpdiscuzCore::TAB_RATING); ?>[displayRatingOnPost][]" id="displayRatingOnPostBeforeCommentForm" style="margin:0px; vertical-align: middle;" />
            <label for="displayRatingOnPostBeforeCommentForm"><?php esc_html_e("Before Comment Form", "wpdiscuz"); ?></label>
        </div>
        <div>
            <input type="checkbox" <?php checked(in_array("before", $this->rating["displayRatingOnPost"])) ?> value="before" name="<?php echo esc_attr(WpdiscuzCore::TAB_RATING); ?>[displayRatingOnPost][]" id="displayRatingOnPostBefore" style="margin:0px; vertical-align: middle;" />
            <label for="displayRatingOnPostBefore"><?php esc_html_e("Before Content", "wpdiscuz"); ?></label>
        </div>
        <div>
            <input type="checkbox" <?php checked(in_array("after", $this->rating["displayRatingOnPost"])) ?> value="after" name="<?php echo esc_attr(WpdiscuzCore::TAB_RATING); ?>[displayRatingOnPost][]" id="displayRatingOnPostAfter" style="margin:0px; vertical-align: middle;" />
            <label for="displayRatingOnPostAfter"><?php esc_html_e("After Content", "wpdiscuz"); ?></label>
        </div>
        <div>
            <input type="checkbox" <?php checked($this->rating["ratingCssOnNoneSingular"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_RATING); ?>[ratingCssOnNoneSingular]" id="ratingCssOnNoneSingular" style="margin:0px; vertical-align: middle;" />
            <label for="ratingCssOnNoneSingular"><?php esc_html_e("Display ratings on non-singular pages", "wpdiscuz"); ?></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["displayRatingOnPost"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="ratingStarColors">
    <div class="wpd-opt-input" style="width: calc(100% - 40px);">
        <h2 style="margin-bottom: 0px;font-size: 15px; color: #555;"><?php echo esc_html($setting["options"]["ratingStarColors"]["label"]) ?></h2>
        <p class="wpd-desc"><?php echo $setting["options"]["ratingStarColors"]["description"] ?></p>
        <hr />
        <div class="wpd-color-wrap">
            <input type="text" class="wpdiscuz-color-picker regular-text" value="<?php echo esc_attr($this->rating["ratingHoverColor"]); ?>" id="ratingHovratingHoverColorerColor" name="<?php echo esc_attr(WpdiscuzCore::TAB_RATING); ?>[ratingHoverColor]" placeholder="<?php esc_attr_e("Example: #00FF00", "wpdiscuz"); ?>"/>
            <label><?php esc_html_e("Rating Stars Hover Color", "wpdiscuz"); ?></label>
        </div>
        <div class="wpd-color-wrap">
            <input type="text" class="wpdiscuz-color-picker regular-text" value="<?php echo esc_attr($this->rating["ratingInactiveColor"]); ?>" id="ratingInactiveColor" name="<?php echo esc_attr(WpdiscuzCore::TAB_RATING); ?>[ratingInactiveColor]" placeholder="<?php esc_attr_e("Example: #00FF00", "wpdiscuz"); ?>"/>
            <label><?php esc_html_e("Rating Stars Inactive Color", "wpdiscuz"); ?></label>
        </div>
        <div class="wpd-color-wrap">
            <input type="text" class="wpdiscuz-color-picker regular-text" value="<?php echo esc_attr($this->rating["ratingActiveColor"]); ?>" id="ratingActiveColor" name="<?php echo esc_attr(WpdiscuzCore::TAB_RATING); ?>[ratingActiveColor]" placeholder="<?php esc_attr_e("Example: #00FF00", "wpdiscuz"); ?>"/>
            <label><?php esc_html_e("Rating Stars Active Color", "wpdiscuz"); ?></label>
        </div>
        <div style="clear: both"></div>
    </div>
    <div class="wpd-opt-doc" style="padding-top: 36px;">
        <?php $this->printDocLink($setting["options"]["ratingStarColors"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->