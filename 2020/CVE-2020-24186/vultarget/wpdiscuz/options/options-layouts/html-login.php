<?php
if (!defined("ABSPATH")) {
    exit();
}
?>
<!-- Option start -->
<div class="wpd-opt-row">
    <div class="wpd-opt-intro">
        <img class="wpd-opt-img" src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/" . $setting["icon"])); ?>" style="height: 80px; padding-top: 5px;"/>
        <?php echo sprintf(esc_html__("Here you can manage commenters authorization and data control related settings. wpDiscuz is focused on user engagement and community building ideas, therefore it's integrated with community builder plugins like wpForo Forum, BuddyPress, etc... With these plugins comment authors are linked to their profile pages. In case you want to display commenters profile and activity information directly on comment list, please be sure to check out the %s addon.", "wpdiscuz"), "<a href='https://gvectors.com/product/wpdiscuz-comment-author-info/'  target='_blank' style='color:#07B290;'>wpDiscuz Comemnt Author Info</a>"); ?>
    </div>
    <div class="wpd-opt-doc" style="padding-top: 10px;">
        <a href="https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/user-authorization-and-profile-data/" title="<?php esc_attr_e("Read the documentation", "wpdiscuz") ?>" target="_blank"><i class="far fa-question-circle"></i></a>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="showLoggedInUsername">
    <div class="wpd-opt-name">
        <label for="showLoggedInUsername"><?php echo esc_html($setting["options"]["showLoggedInUsername"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["showLoggedInUsername"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->login["showLoggedInUsername"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_LOGIN); ?>[showLoggedInUsername]" id="showLoggedInUsername">
            <label for="showLoggedInUsername"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["showLoggedInUsername"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="showLoginLinkForGuests">
    <div class="wpd-opt-name">
        <label for="showLoginLinkForGuests"><?php echo esc_html($setting["options"]["showLoginLinkForGuests"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["showLoginLinkForGuests"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->login["showLoginLinkForGuests"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_LOGIN); ?>[showLoginLinkForGuests]" id="showLoginLinkForGuests">
            <label for="showLoginLinkForGuests"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["showLoginLinkForGuests"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="loginUrl">
    <div class="wpd-opt-name">
        <label for="loginUrl"><?php echo esc_html($setting["options"]["loginUrl"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["loginUrl"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input type="url" value="<?php echo esc_attr($this->login["loginUrl"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_LOGIN); ?>[loginUrl]" id="loginUrl" placeholder="<?php echo esc_url_raw(home_url("/my-login-page/")) ?>" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["loginUrl"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="myContentSettings">
    <div class="wpd-opt-name">
        <?php echo esc_html($setting["options"]["myContentSettings"]["label"]) ?>
        <p class="wpd-desc"><?php echo $setting["options"]["myContentSettings"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-optset">
            <div class="wpd-switcher">
                <input type="checkbox" id="showUserSettingsButton">
                <label for="showUserSettingsButton"></label>
            </div>
            <div class="wpd-optset-label">
                <label for="showUserSettingsButton"><?php esc_html_e("Enable button", "wpdiscuz") ?></label>
            </div>
        </div>
        <div class="wpd-optset">
            <div class="wpd-switcher">
                <input type="checkbox" <?php checked($this->login["showActivityTab"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_LOGIN); ?>[showActivityTab]" id="showActivityTab">
                <label for="showActivityTab"></label>
            </div>
            <div class="wpd-optset-label">
                <label for="showActivityTab"><?php esc_html_e("Show Activity Tab", "wpdiscuz") ?></label>
            </div>
        </div>
        <div class="wpd-optset">
            <div class="wpd-switcher">
                <input type="checkbox" <?php checked($this->login["showSubscriptionsTab"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_LOGIN); ?>[showSubscriptionsTab]" id="showSubscriptionsTab">
                <label for="showSubscriptionsTab"></label>
            </div>
            <div class="wpd-optset-label">
                <label for="showSubscriptionsTab"><?php esc_html_e("Show Subscriptions Tab", "wpdiscuz") ?></label>
            </div>
        </div>
        <div class="wpd-optset">
            <div class="wpd-switcher">
                <input type="checkbox" <?php checked($this->login["showFollowsTab"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_LOGIN); ?>[showFollowsTab]" id="showFollowsTab">
                <label for="showFollowsTab"></label>
            </div>
            <div class="wpd-optset-label">
                <label for="showFollowsTab"><?php esc_html_e("Show Followers Tab", "wpdiscuz") ?></label>
            </div>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["myContentSettings"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="enableProfileURLs">
    <div class="wpd-opt-name">
        <label for="enableProfileURLs"><?php echo esc_html($setting["options"]["enableProfileURLs"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["enableProfileURLs"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->login["enableProfileURLs"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_LOGIN); ?>[enableProfileURLs]" id="enableProfileURLs">
            <label for="enableProfileURLs"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enableProfileURLs"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="websiteAsProfileUrl">
    <div class="wpd-opt-name">
        <label for="websiteAsProfileUrl"><?php echo esc_html($setting["options"]["websiteAsProfileUrl"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["websiteAsProfileUrl"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->login["websiteAsProfileUrl"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_LOGIN); ?>[websiteAsProfileUrl]" id="websiteAsProfileUrl">
            <label for="websiteAsProfileUrl"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["websiteAsProfileUrl"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="isUserByEmail">
    <div class="wpd-opt-name">
        <label for="isUserByEmail"><?php echo esc_html($setting["options"]["isUserByEmail"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["isUserByEmail"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->login["isUserByEmail"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_LOGIN); ?>[isUserByEmail]" id="isUserByEmail">
            <label for="isUserByEmail"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["isUserByEmail"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->