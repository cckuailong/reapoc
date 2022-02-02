<?php
if (!defined("ABSPATH")) {
    exit();
}
?>
<!-- Option start -->
<div class="wpd-opt-row">
    <div class="wpd-opt-intro">
        <img class="wpd-opt-img" src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/" . $setting["icon"])); ?>" style="height: 65px; padding-top: 5px;"/>
        <?php echo sprintf(__("WordPress has already all necessary tools to moderate commends. You can approve, unapprove, mark as spam, delete and do other things with comments in WordPress Dashboard > Comments admin page. <br><br>On front-end you can only edit, close and stick comments. In case you want to have all moderation options on front-end, we recommend checkout %s addon. Besides the comment editing, closing and sticking options, here you can limit users commenting activity. You can set max number of comments per user per post or sitewide, allow them only comment or only reply.", "wpdiscuz"), "<a href='https://gvectors.com/product/wpdiscuz-frontend-moderation/'  target='_blank' style='color:#07B290;'>wpDiscuz Frontend Moderation</a>"); ?>
    </div>
    <div class="wpd-opt-doc" style="padding-top: 10px;">
        <a href="https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-moderation/" title="<?php esc_html_e("Read the documentation", "wpdiscuz") ?>" target="_blank"><i class="far fa-question-circle"></i></a>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="commentEditableTime">
    <div class="wpd-opt-name">
        <label for="commentEditableTime"><?php echo esc_html($setting["options"]["commentEditableTime"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["commentEditableTime"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <select id="commentEditableTime" name="<?php echo esc_attr(WpdiscuzCore::TAB_MODERATION); ?>[commentEditableTime]">
            <option value="0" <?php selected($this->moderation["commentEditableTime"], "0"); ?>><?php esc_html_e("Do not allow", "wpdiscuz"); ?></option>
            <option value="900" <?php selected($this->moderation["commentEditableTime"], "900"); ?>>15 <?php esc_html_e("Minutes", "wpdiscuz"); ?></option>
            <option value="1800" <?php selected($this->moderation["commentEditableTime"], "1800"); ?>>30 <?php esc_html_e("Minutes", "wpdiscuz"); ?></option>
            <option value="3600" <?php selected($this->moderation["commentEditableTime"], "3600"); ?>>1 <?php esc_html_e("Hour", "wpdiscuz"); ?></option>
            <option value="10800" <?php selected($this->moderation["commentEditableTime"], "10800"); ?>>3 <?php esc_html_e("Hours", "wpdiscuz"); ?></option>
            <option value="86400" <?php selected($this->moderation["commentEditableTime"], "86400"); ?>>24 <?php esc_html_e("Hours", "wpdiscuz"); ?></option>
            <option value="unlimit" <?php selected($this->moderation["commentEditableTime"], "unlimit"); ?>><?php esc_html_e("Unlimit", "wpdiscuz"); ?></option>
        </select>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["commentEditableTime"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="enableEditingWhenHaveReplies">
    <div class="wpd-opt-name">
        <label for="enableEditingWhenHaveReplies"><?php echo esc_html($setting["options"]["enableEditingWhenHaveReplies"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["enableEditingWhenHaveReplies"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->moderation["enableEditingWhenHaveReplies"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_MODERATION); ?>[enableEditingWhenHaveReplies]" id="enableEditingWhenHaveReplies">
            <label for="enableEditingWhenHaveReplies"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enableEditingWhenHaveReplies"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="displayEditingInfo">
    <div class="wpd-opt-name">
        <label for="displayEditingInfo"><?php echo esc_html($setting["options"]["displayEditingInfo"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["displayEditingInfo"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->moderation["displayEditingInfo"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_MODERATION); ?>[displayEditingInfo]" id="displayEditingInfo">
            <label for="displayEditingInfo"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["displayEditingInfo"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="enableStickButton">
    <div class="wpd-opt-name">
        <label for="enableStickButton"><?php echo esc_html($setting["options"]["enableStickButton"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["enableStickButton"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->moderation["enableStickButton"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_MODERATION); ?>[enableStickButton]" id="enableStickButton">
            <label for="enableStickButton"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enableStickButton"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="enableCloseButton">
    <div class="wpd-opt-name">
        <label for="enableCloseButton"><?php echo esc_html($setting["options"]["enableCloseButton"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["enableCloseButton"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->moderation["enableCloseButton"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_MODERATION); ?>[enableCloseButton]" id="enableCloseButton">
            <label for="enableCloseButton"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enableCloseButton"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="userCommentsLimit">
    <div class="wpd-opt-name">
        <label for="userCommentsLimit"><?php echo esc_html($setting["options"]["userCommentsLimit"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["userCommentsLimit"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switch-field">
            <input type="radio" value="disable" <?php checked($this->moderation["restrictCommentingPerUser"] === "disable"); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_MODERATION); ?>[restrictCommentingPerUser]" id="restrictCommentingPerUserDisable" class="restrictCommentingPerUser"/>
            <label for="restrictCommentingPerUserDisable"><?php esc_html_e("Disable", "wpdiscuz") ?></label>
            <input type="radio" value="post" <?php checked($this->moderation["restrictCommentingPerUser"] === "post"); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_MODERATION); ?>[restrictCommentingPerUser]" id="restrictCommentingPerUserPost" class="restrictCommentingPerUser" />
            <label for="restrictCommentingPerUserPost"><?php esc_html_e("Per Post", "wpdiscuz") ?></label>
            <input type="radio" value="blog" <?php checked($this->moderation["restrictCommentingPerUser"] === "blog"); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_MODERATION); ?>[restrictCommentingPerUser]" id="restrictCommentingPerUserBlog" class="restrictCommentingPerUser" />
            <label for="restrictCommentingPerUserBlog"><?php esc_html_e("Sitewide", "wpdiscuz") ?></label>
        </div>
        <div class="wpd-clear" style="height: 10px;"></div>
        <div class="wpd-switch-field">
            <input type="radio" value="both" <?php checked($this->moderation["commentRestrictionType"] === "both"); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_MODERATION); ?>[commentRestrictionType]" id="restrictCommentingPerUserBoth" class="restrictCommentingPerUser" />
            <label for="restrictCommentingPerUserBoth"><?php esc_html_e("Both", "wpdiscuz") ?></label>
            <input type="radio" value="parent" <?php checked($this->moderation["commentRestrictionType"] === "parent"); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_MODERATION); ?>[commentRestrictionType]" id="restrictCommentingPerUserParent" class="restrictCommentingPerUser" />
            <label for="restrictCommentingPerUserParent"><?php esc_html_e("Comment", "wpdiscuz") ?></label>
            <input type="radio" value="reply" <?php checked($this->moderation["commentRestrictionType"] === "reply"); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_MODERATION); ?>[commentRestrictionType]" id="restrictCommentingPerUserReply" class="restrictCommentingPerUser" />
            <label for="restrictCommentingPerUserReply"><?php esc_html_e("Reply", "wpdiscuz") ?></label>
        </div>
        <div class="wpd-clear" style="height: 10px;"></div>
        <div class="wpd-input">
            <span><input type="number" value="<?php echo esc_attr($this->moderation["userCommentsLimit"]); ?>" min="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_MODERATION); ?>[userCommentsLimit]" id="userCommentsLimit" style="width:70px;">&nbsp; <?php esc_html_e("Max number of comments", "wpdiscuz") ?></span>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["userCommentsLimit"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->
