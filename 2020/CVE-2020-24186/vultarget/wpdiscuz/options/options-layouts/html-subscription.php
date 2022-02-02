<?php
if (!defined("ABSPATH")) {
    exit();
}
?>
<!-- Option start -->
<div class="wpd-opt-row">
    <div class="wpd-opt-intro">
        <img class="wpd-opt-img" src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/" . $setting["icon"])); ?>" style="height: 64px; padding-top: 5px;"/>
        <?php echo sprintf(__("wpDiscuz allows users to get all kind of news from your website comment system, such as new comments, new replies, double opt-in subscription, user mentioning, user following and new comments by followed users. You can manage all those options here. All those options are based on email notifications. You can manage email templates in wpDiscuz > Phrases > Email Tab. <br>In wpDiscuz > Dashboard page, you can find a quick overview of user subscriptions. For an advanced subscriptions management tool, please checkout %s addon.", "wpdiscuz"), "<a href='https://gvectors.com/product/wpdiscuz-subscribe-manager/'  target='_blank' style='color:#07B290;'>wpDiscuz Subscription Manager</a>"); ?>
    </div>
    <div class="wpd-opt-doc" style="padding-top: 10px;">
        <a href="https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/subscription-and-user-following/" title="<?php esc_attr_e("Read the documentation", "wpdiscuz") ?>" target="_blank"><i class="far fa-question-circle"></i></a>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="enableUserMentioning">
    <div class="wpd-opt-name">
        <label for="enableUserMentioning"><?php echo esc_html($setting["options"]["enableUserMentioning"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["enableUserMentioning"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->subscription["enableUserMentioning"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SUBSCRIPTION); ?>[enableUserMentioning]" id="enableUserMentioning">
            <label for="enableUserMentioning"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enableUserMentioning"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="sendMailToMentionedUsers">
    <div class="wpd-opt-name">
        <label for="sendMailToMentionedUsers"><?php echo esc_html($setting["options"]["sendMailToMentionedUsers"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["sendMailToMentionedUsers"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->subscription["sendMailToMentionedUsers"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SUBSCRIPTION); ?>[sendMailToMentionedUsers]" id="sendMailToMentionedUsers">
            <label for="sendMailToMentionedUsers"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["sendMailToMentionedUsers"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="isNotifyOnCommentApprove">
    <div class="wpd-opt-name">
        <label for="isNotifyOnCommentApprove"><?php echo esc_html($setting["options"]["isNotifyOnCommentApprove"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["isNotifyOnCommentApprove"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->subscription["isNotifyOnCommentApprove"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SUBSCRIPTION); ?>[isNotifyOnCommentApprove]" id="isNotifyOnCommentApprove">
            <label for="isNotifyOnCommentApprove"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["isNotifyOnCommentApprove"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="enableMemberConfirm">
    <div class="wpd-opt-name">
        <label for="enableMemberConfirm"><?php echo esc_html($setting["options"]["enableMemberConfirm"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["enableMemberConfirm"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->subscription["enableMemberConfirm"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SUBSCRIPTION); ?>[enableMemberConfirm]" id="enableMemberConfirm">
            <label for="enableMemberConfirm"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enableMemberConfirm"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="enableGuestsConfirm">
    <div class="wpd-opt-name">
        <label for="enableGuestsConfirm"><?php echo esc_html($setting["options"]["enableGuestsConfirm"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["enableGuestsConfirm"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->subscription["enableGuestsConfirm"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SUBSCRIPTION); ?>[enableGuestsConfirm]" id="enableGuestsConfirm">
            <label for="enableGuestsConfirm"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enableGuestsConfirm"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="subscriptionType">
    <div class="wpd-opt-name">
        <label for="subscriptionType"><?php echo esc_html($setting["options"]["subscriptionType"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["subscriptionType"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-radio">
            <input type="radio" value="2" <?php checked(2 == $this->subscription["subscriptionType"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_SUBSCRIPTION); ?>[subscriptionType]" id="subscriptionTypePost" />
            <label for="subscriptionTypePost" class="wpd-radio-circle"></label>
            <label for="subscriptionTypePost"><?php esc_html_e("Subscribe to all comments of this post", "wpdiscuz") ?></label>
        </div>
        <div class="wpd-radio">
            <input type="radio" value="3" <?php checked(3 == $this->subscription["subscriptionType"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_SUBSCRIPTION); ?>[subscriptionType]" id="subscriptionTypeAllComments" />
            <label for="subscriptionTypeAllComments" class="wpd-radio-circle"></label>
            <label for="subscriptionTypeAllComments"><?php esc_html_e("Subscribe to all replies to my comments", "wpdiscuz") ?></label>
        </div>
        <div class="wpd-radio">
            <input type="radio" value="1" <?php checked(1 == $this->subscription["subscriptionType"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_SUBSCRIPTION); ?>[subscriptionType]" id="subscriptionTypeBoth" />
            <label for="subscriptionTypeBoth" class="wpd-radio-circle"></label>
            <label for="subscriptionTypeBoth"><?php esc_html_e("Both", "wpdiscuz") ?></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["subscriptionType"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="showReplyCheckbox">
    <div class="wpd-opt-name">
        <label for="showReplyCheckbox"><?php echo esc_html($setting["options"]["showReplyCheckbox"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["showReplyCheckbox"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->subscription["showReplyCheckbox"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SUBSCRIPTION); ?>[showReplyCheckbox]" id="showReplyCheckbox">
            <label for="showReplyCheckbox"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["showReplyCheckbox"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="isReplyDefaultChecked">
    <div class="wpd-opt-name">
        <label for="isReplyDefaultChecked"><?php echo esc_html($setting["options"]["isReplyDefaultChecked"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["isReplyDefaultChecked"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->subscription["isReplyDefaultChecked"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SUBSCRIPTION); ?>[isReplyDefaultChecked]" id="isReplyDefaultChecked">
            <label for="isReplyDefaultChecked"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["isReplyDefaultChecked"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<?php if (class_exists("Prompt_Comment_Form_Handling")) { ?>
    <!-- Option start -->
    <div class="wpd-opt-row" data-wpd-opt="usePostmaticForCommentNotification">
        <div class="wpd-opt-name">
            <label for="usePostmaticForCommentNotification"><?php echo esc_html($setting["options"]["usePostmaticForCommentNotification"]["label"]) ?></label>
            <p class="wpd-desc"><?php echo $setting["options"]["usePostmaticForCommentNotification"]["description"] ?></p>
        </div>
        <div class="wpd-opt-input">
            <div class="wpd-switcher">
                <input type="checkbox" <?php checked($this->subscription["usePostmaticForCommentNotification"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SUBSCRIPTION); ?>[usePostmaticForCommentNotification]" id="usePostmaticForCommentNotification" />
                <label for="usePostmaticForCommentNotification"></label>
            </div>
        </div>
        <div class="wpd-opt-doc">
            <?php $this->printDocLink($setting["options"]["usePostmaticForCommentNotification"]["docurl"]) ?>
        </div>
    </div>
    <!-- Option end -->
<?php } ?>

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="isFollowActive">
    <div class="wpd-opt-name">
        <label for="isFollowActive"><?php echo esc_html($setting["options"]["isFollowActive"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["isFollowActive"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->subscription["isFollowActive"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SUBSCRIPTION); ?>[isFollowActive]" id="isFollowActive">
            <label for="isFollowActive"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["isFollowActive"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="disableFollowConfirmForUsers">
    <div class="wpd-opt-name">
        <label for="disableFollowConfirmForUsers"><?php echo esc_html($setting["options"]["disableFollowConfirmForUsers"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["disableFollowConfirmForUsers"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->subscription["disableFollowConfirmForUsers"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_SUBSCRIPTION); ?>[disableFollowConfirmForUsers]" id="disableFollowConfirmForUsers">
            <label for="disableFollowConfirmForUsers"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["disableFollowConfirmForUsers"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->



