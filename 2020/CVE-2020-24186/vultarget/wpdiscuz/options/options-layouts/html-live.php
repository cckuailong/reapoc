<?php
if (!defined("ABSPATH")) {
    exit();
}
?>
<!-- Option start -->
<div class="wpd-opt-row">
    <div class="wpd-opt-intro">
        <img class="wpd-opt-img" src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/" . $setting["icon"])); ?>" style="height: 70px; padding-top: 5px;"/>
        <?php esc_html_e("In wpDiscuz 7 the real-time commenting becomes more live and attractive. It's based on REST API and doesn't overload your server. A specific sticky comment icon on your web pages, called &laquo;Bubble&raquo; keeps article readers and commenters up to date. It displays new comments as pop-up information or as a number in an orange circle.", "wpdiscuz"); ?>
        <?php esc_html_e("This Bubble is also designed to invite article readers to comment. It displays invite message allowing them fast and easy jump to comment area.", "wpdiscuz"); ?>
	    <?php esc_html_e("You can enabled comments live update without the Bubble too. There is a separate live update option to keep comment list real-time updating.", "wpdiscuz"); ?>
    </div>
    <div class="wpd-opt-doc" style="padding-top: 10px;">
        <a href="https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/live-commenting-and-notifications/" title="<?php esc_attr_e("Read the documentation", "wpdiscuz") ?>" target="_blank"><i class="far fa-question-circle"></i></a>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="bubble">
    <div class="wpd-opt-name">
        <label><?php echo esc_html($setting["options"]["bubble"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["bubble"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switch-field">
            <input type="radio" value="1" <?php checked(1 == $this->live["enableBubble"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_LIVE); ?>[enableBubble]" id="enableBubble" />
            <label for="enableBubble" style="min-width:60px;"><?php esc_html_e("Enable", "wpdiscuz"); ?></label>
            <input type="radio" value="0" <?php checked(0 == $this->live["enableBubble"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_LIVE); ?>[enableBubble]" id="disableBubble" />
            <label for="disableBubble" style="min-width:60px;"><?php esc_html_e("Disable", "wpdiscuz"); ?></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["bubble"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="bubbleLocation">
    <div class="wpd-opt-name">
        <label for="bubbleLocation"><?php echo esc_html($setting["options"]["bubbleLocation"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["bubbleLocation"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switch-field">
            <input type="radio" <?php checked($this->live["bubbleLocation"] == "content_left") ?> value="content_left" name="<?php echo esc_attr(WpdiscuzCore::TAB_LIVE); ?>[bubbleLocation]" id="content_left" class="content_left" style="vertical-align: bottom;"/>
            <label for="content_left" style="min-width:60px;"><?php esc_html_e("Content Left", "wpdiscuz"); ?></label>
            <input type="radio" <?php checked($this->live["bubbleLocation"] == "left_corner") ?> value="left_corner" name="<?php echo esc_attr(WpdiscuzCore::TAB_LIVE); ?>[bubbleLocation]" id="left_corner" class="left_corner" style="vertical-align: bottom;"/>
            <label for="left_corner" style="min-width:60px;"><?php esc_html_e("Left Corner", "wpdiscuz"); ?></label>
            <input type="radio" <?php checked($this->live["bubbleLocation"] == "right_corner") ?> value="right_corner" name="<?php echo esc_attr(WpdiscuzCore::TAB_LIVE); ?>[bubbleLocation]" id="right_corner" class="right_corner" style="vertical-align: bottom;"/>
            <label for="right_corner" style="min-width:60px;"><?php esc_html_e("Right Corner", "wpdiscuz"); ?></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["bubbleLocation"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="bubbleLiveUpdate">
    <div class="wpd-opt-name">
        <label><?php echo esc_html($setting["options"]["bubbleLiveUpdate"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["bubbleLiveUpdate"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switch-field">
            <input type="radio" value="1" <?php checked(1 == $this->live["bubbleLiveUpdate"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_LIVE); ?>[bubbleLiveUpdate]" id="enableBubbleLiveUpdate" />
            <label for="enableBubbleLiveUpdate" style="min-width:60px;"><?php esc_html_e("Enable", "wpdiscuz"); ?></label>
            <input type="radio" value="0" <?php checked(0 == $this->live["bubbleLiveUpdate"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_LIVE); ?>[bubbleLiveUpdate]" id="disableBubbleLiveUpdate" />
            <label for="disableBubbleLiveUpdate" style="min-width:60px;"><?php esc_html_e("Disable", "wpdiscuz"); ?></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["bubbleLiveUpdate"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="bubbleShowNewCommentMessage">
    <div class="wpd-opt-name">
        <label><?php echo esc_html($setting["options"]["bubbleShowNewCommentMessage"]["label"]) ?></label>
        <div style="width: 76%; margin-top: 5px;"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/bubble-new-comment-info.png")); ?>" style="width: 100%;"/></div>
        <p class="wpd-desc"><?php echo $setting["options"]["bubbleShowNewCommentMessage"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switch-field">
            <input type="radio" value="1" <?php checked(1 == $this->live["bubbleShowNewCommentMessage"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_LIVE); ?>[bubbleShowNewCommentMessage]" id="bubbleShowNewCommentMessage" />
            <label for="bubbleShowNewCommentMessage" style="min-width:60px;"><?php esc_html_e("Enable", "wpdiscuz"); ?></label>
            <input type="radio" value="0" <?php checked(0 == $this->live["bubbleShowNewCommentMessage"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_LIVE); ?>[bubbleShowNewCommentMessage]" id="bubbleHideNewCommentMessage" />
            <label for="bubbleHideNewCommentMessage" style="min-width:60px;"><?php esc_html_e("Disable", "wpdiscuz"); ?></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["bubbleShowNewCommentMessage"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="bubbleHintTimeout">
    <div class="wpd-opt-name">
        <label for="bubbleHintTimeout"><?php echo esc_html($setting["options"]["bubbleHintTimeout"]["label"]) ?></label>
        <div style="width: 77%; margin-top: 5px;"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/bubble-invite-to-comment.png")); ?>" style="width: 100%"/></div>
        <p class="wpd-desc"><?php echo $setting["options"]["bubbleHintTimeout"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <span><input type="number" value="<?php echo esc_attr($this->live["bubbleHintTimeout"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_LIVE); ?>[bubbleHintTimeout]" id="bubbleHintTimeout" style="width:70px;"> <?php esc_html_e("seconds", "wpdiscuz") ?></span>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["bubbleHintTimeout"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="bubbleHintHideTimeout">
    <div class="wpd-opt-name">
        <label for="bubbleHintHideTimeout"><?php echo esc_html($setting["options"]["bubbleHintHideTimeout"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["bubbleHintHideTimeout"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <span><input type="number" value="<?php echo esc_attr($this->live["bubbleHintHideTimeout"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_LIVE); ?>[bubbleHintHideTimeout]" id="bubbleHintHideTimeout" style="width:70px;"> <?php esc_html_e("seconds", "wpdiscuz") ?></span>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["bubbleHintHideTimeout"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="commentListUpdateType">
    <div class="wpd-opt-name">
        <label for="commentListUpdateType"><?php echo esc_html($setting["options"]["commentListUpdateType"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["commentListUpdateType"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switch-field">
            <input type="radio" value="1" <?php checked(1 == $this->live["commentListUpdateType"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_LIVE); ?>[commentListUpdateType]" id="wc_comment_list_update_always" />
            <label for="wc_comment_list_update_always" style="min-width:60px;"><?php esc_html_e("Enable", "wpdiscuz"); ?></label>
            <input type="radio" value="0" <?php checked(0 == $this->live["commentListUpdateType"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_LIVE); ?>[commentListUpdateType]" id="wc_comment_list_update_never" />
            <label for="wc_comment_list_update_never" style="min-width:60px;"><?php esc_html_e("Disable", "wpdiscuz"); ?></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["commentListUpdateType"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="liveUpdateGuests">
    <div class="wpd-opt-name">
        <label for="liveUpdateGuests"><?php echo esc_html($setting["options"]["liveUpdateGuests"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["liveUpdateGuests"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->live["liveUpdateGuests"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_LIVE); ?>[liveUpdateGuests]" id="liveUpdateGuests">
            <label for="liveUpdateGuests"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["liveUpdateGuests"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="commentListUpdateTimer">
    <div class="wpd-opt-name">
        <label for="commentListUpdateTimer"><?php echo esc_html($setting["options"]["commentListUpdateTimer"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["commentListUpdateTimer"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <select id="commentListUpdateTimer" name="<?php echo esc_attr(WpdiscuzCore::TAB_LIVE); ?>[commentListUpdateTimer]">
            <option value="10" <?php selected($this->live["commentListUpdateTimer"], 10); ?>>10 <?php esc_html_e("Seconds", "wpdiscuz"); ?></option>
            <option value="20" <?php selected($this->live["commentListUpdateTimer"], 20); ?>>20 <?php esc_html_e("Seconds", "wpdiscuz"); ?></option>
            <option value="30" <?php selected($this->live["commentListUpdateTimer"], 30); ?>>30 <?php esc_html_e("Seconds", "wpdiscuz"); ?></option>
            <option value="60" <?php selected($this->live["commentListUpdateTimer"], 60); ?>>1 <?php esc_html_e("Minute", "wpdiscuz"); ?></option>
            <option value="180" <?php selected($this->live["commentListUpdateTimer"], 180); ?>>3 <?php esc_html_e("Minutes", "wpdiscuz"); ?></option>
            <option value="300" <?php selected($this->live["commentListUpdateTimer"], 300); ?>>5 <?php esc_html_e("Minutes", "wpdiscuz"); ?></option>
            <option value="600" <?php selected($this->live["commentListUpdateTimer"], 600); ?>>10 <?php esc_html_e("Minutes", "wpdiscuz"); ?></option>
        </select>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["commentListUpdateTimer"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->