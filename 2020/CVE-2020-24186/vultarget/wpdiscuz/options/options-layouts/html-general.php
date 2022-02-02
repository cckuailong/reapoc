<?php
if (!defined("ABSPATH")) {
    exit();
}
?>

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="isEnableOnHome">
    <div class="wpd-opt-name">
        <label for="isEnableOnHome"><?php echo esc_html($setting["options"]["isEnableOnHome"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["isEnableOnHome"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->general["isEnableOnHome"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_GENERAL); ?>[isEnableOnHome]" id="isEnableOnHome">
            <label for="isEnableOnHome"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["isEnableOnHome"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="isNativeAjaxEnabled">
    <div class="wpd-opt-name">
        <label for="isNativeAjaxEnabled"><?php echo esc_html($setting["options"]["isNativeAjaxEnabled"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["isNativeAjaxEnabled"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->general["isNativeAjaxEnabled"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_GENERAL); ?>[isNativeAjaxEnabled]" id="isNativeAjaxEnabled">
            <label for="isNativeAjaxEnabled"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["isNativeAjaxEnabled"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->
<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="loadComboVersion">
    <div class="wpd-opt-name">
        <label for="loadComboVersion"><?php echo esc_html($setting["options"]["loadComboVersion"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["loadComboVersion"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->general["loadComboVersion"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_GENERAL); ?>[loadComboVersion]" id="loadComboVersion">
            <label for="loadComboVersion"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["loadComboVersion"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->
<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="loadMinVersion">
    <div class="wpd-opt-name">
        <label for="loadMinVersion"><?php echo esc_html($setting["options"]["loadMinVersion"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["loadMinVersion"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->general["loadMinVersion"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_GENERAL); ?>[loadMinVersion]" id="loadMinVersion">
            <label for="loadMinVersion"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["loadMinVersion"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->
<?php if (is_ssl()) { ?>
    <!-- Option start -->
    <div class="wpd-opt-row" data-wpd-opt="commentLinkFilter">
        <div class="wpd-opt-name">
            <label><?php echo esc_html($setting["options"]["commentLinkFilter"]["label"]) ?></label>
            <p class="wpd-desc"><?php echo $setting["options"]["commentLinkFilter"]["description"] ?></p>
        </div>
        <div class="wpd-opt-input">
            <div class="wpd-radio">
                <input type="radio" value="1" <?php checked(1 == $this->general["commentLinkFilter"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_GENERAL); ?>[commentLinkFilter]" id="http-to-link"/>
                <label for="http-to-link" class="wpd-radio-circle"></label>
                <label for="http-to-link"><?php esc_html_e("Replace non-https content to simple link URLs", "wpdiscuz") ?></label>
            </div>
            <div class="wpd-radio">
                <input type="radio" value="2" <?php checked(2 == $this->general["commentLinkFilter"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_GENERAL); ?>[commentLinkFilter]" id="http-to-https"/>
                <label for="http-to-https" class="wpd-radio-circle"></label>
                <label for="http-to-https"><?php esc_html_e("Just replace http protocols to https (https may not be supported by content provider)", "wpdiscuz") ?></label>
            </div>
            <div class="wpd-radio">
                <input type="radio" value="3" <?php checked(3 == $this->general["commentLinkFilter"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_GENERAL); ?>[commentLinkFilter]" id="ignore-https"/>
                <label for="ignore-https" class="wpd-radio-circle"></label>
                <label for="ignore-https"><?php esc_html_e("Ignore non-https content", "wpdiscuz") ?></label>
            </div>
        </div>
        <div class="wpd-opt-doc">
            <?php $this->printDocLink($setting["options"]["commentLinkFilter"]["docurl"]) ?>
        </div>
    </div>
    <!-- Option end -->
<?php } ?>
<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="redirectPage">
    <div class="wpd-opt-name">
        <label for="redirectPage"><?php echo esc_html($setting["options"]["redirectPage"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["redirectPage"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <?php
        wp_dropdown_pages([
            "name" => WpdiscuzCore::TAB_GENERAL . "[redirectPage]",
            "selected" => $this->general["redirectPage"],
            "show_option_none" => esc_html__("Do not redirect", "wpdiscuz"),
            "option_none_value" => 0
        ]);
        ?>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["redirectPage"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="simpleCommentDate">
    <div class="wpd-opt-name">
        <label for="simpleCommentDate"><?php echo esc_html($setting["options"]["simpleCommentDate"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["simpleCommentDate"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher" style="margin-bottom: 5px;">
            <input type="checkbox" <?php checked($this->general["simpleCommentDate"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_GENERAL); ?>[simpleCommentDate]" id="simpleCommentDate">
            <label for="simpleCommentDate"></label>
        </div>
        <span style="font-size:13px; color:#999999; padding-left:0px; margin-left:0px; line-height:15px;">
            <?php echo esc_html(date(get_option("date_format"))); ?> / <?php echo esc_html(date(get_option("time_format"))); ?><br />
            <?php esc_html_e("Current Wordpress date/time format", "wpdiscuz"); ?>
        </span>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["simpleCommentDate"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="dateDiffFormat">
    <div class="wpd-opt-name">
        <label for="dateDiffFormat"><?php echo esc_html($setting["options"]["dateDiffFormat"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["dateDiffFormat"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input type="text" value="<?php echo esc_attr($this->general["dateDiffFormat"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_GENERAL); ?>[dateDiffFormat]" id="dateDiffFormat" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["dateDiffFormat"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="isUsePoMo">
    <div class="wpd-opt-name">
        <label for="isUsePoMo"><?php echo esc_html($setting["options"]["isUsePoMo"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["isUsePoMo"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->general["isUsePoMo"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_GENERAL); ?>[isUsePoMo]" id="isUsePoMo">
            <label for="isUsePoMo"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["isUsePoMo"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="showPluginPoweredByLink" style="border-bottom: none;">
    <div class="wpd-opt-name">
        <label for="showPluginPoweredByLink" style="padding-right: 20px;"><?php echo esc_html($setting["options"]["showPluginPoweredByLink"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["showPluginPoweredByLink"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <label for="showPluginPoweredByLink">
            <input type="checkbox" <?php checked($this->general["showPluginPoweredByLink"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_GENERAL); ?>[showPluginPoweredByLink]" id="showPluginPoweredByLink" />
            <span id="wpdiscuz_thank_you" style="color:#006600; font-size:13px;"> &nbsp;<?php esc_attr_e("Thank you!", "wpdiscuz"); ?></span>
        </label>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["showPluginPoweredByLink"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<div class="wpd-subtitle">
    <span class="dashicons dashicons-admin-generic"></span> <?php esc_html_e("Gravatar Cache", "wpdiscuz") ?>
</div>

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="isGravatarCacheEnabled">
    <div class="wpd-opt-name">
        <label for="isGravatarCacheEnabled"><?php echo esc_html($setting["options"]["isGravatarCacheEnabled"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["isGravatarCacheEnabled"]["description"] ?></p>
        <?php if (!$this->isFileFunctionsExists) { ?>
            <p class="desc"><?php esc_html_e("It seems file_get_contents() and file_put_contents() PHP functions don't exist.<br/> Please enable these functions in your server settings to use gravatar caching feature.", "wpdiscuz"); ?></p>
        <?php } ?>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->general["isGravatarCacheEnabled"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_GENERAL); ?>[isGravatarCacheEnabled]" id="isGravatarCacheEnabled">
            <label for="isGravatarCacheEnabled"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["isGravatarCacheEnabled"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="gravatarCacheMethod">
    <div class="wpd-opt-name">
        <label for="gravatarCacheMethod"><?php echo esc_html($setting["options"]["gravatarCacheMethod"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["gravatarCacheMethod"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switch-field">
            <input type="radio" <?php checked($this->general["gravatarCacheMethod"] == "runtime") ?> value="runtime" name="<?php echo esc_attr(WpdiscuzCore::TAB_GENERAL); ?>[gravatarCacheMethod]" id="gravatarCacheMethodRuntime" /><label for="gravatarCacheMethodRuntime"><?php esc_html_e("Runtime", "wpdiscuz"); ?></label>
            <input type="radio" <?php checked($this->general["gravatarCacheMethod"] == "cronjob") ?> value="cronjob" name="<?php echo esc_attr(WpdiscuzCore::TAB_GENERAL); ?>[gravatarCacheMethod]" id="gravatarCacheMethodCronjob" /><label for="gravatarCacheMethodCronjob"><?php esc_html_e("Cron job", "wpdiscuz"); ?></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["gravatarCacheMethod"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="gravatarCacheTimeout" style="border-bottom: none;">
    <div class="wpd-opt-name">
        <label for="gravatarCacheTimeout"><?php echo esc_html($setting["options"]["gravatarCacheTimeout"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["gravatarCacheTimeout"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <?php $gravatarCacheTimeout = isset($this->general["gravatarCacheTimeout"]) && ($days = absint($this->general["gravatarCacheTimeout"])) ? $days : 10; ?>
        <input type="number" id="gravatarCacheTimeout" name="<?php echo esc_attr(WpdiscuzCore::TAB_GENERAL); ?>[gravatarCacheTimeout]" value="<?php echo esc_attr($gravatarCacheTimeout); ?>" style="width: 80px;"/>&nbsp; <?php esc_html_e("days", "wpdiscuz") ?>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["gravatarCacheTimeout"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<div class="wpd-subtitle">
    <span class="dashicons dashicons-admin-tools"></span> <?php esc_html_e("Maintenance", "wpdiscuz") ?>
</div>

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="removeVoteData">
    <div class="wpd-opt-name">
        <label for="removeVoteData"><?php echo esc_html($setting["options"]["removeVoteData"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["removeVoteData"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <?php $voteUrl = admin_url("admin-post.php?action=removeVoteData"); ?>
        <a id="wpdiscuz-remove-votes" href="<?php echo esc_url_raw(wp_nonce_url($voteUrl, "removeVoteData")); ?>" class="button button-secondary" style="text-decoration: none;"><?php esc_html_e("Remove vote data", "wpdiscuz"); ?></a>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["removeVoteData"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="purgeAvatarCache">
    <div class="wpd-opt-name">
        <label for="purgeAvatarCache"><?php echo esc_html($setting["options"]["purgeAvatarCache"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["purgeAvatarCache"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <?php $expiredCacheUrl = admin_url("admin-post.php?action=purgeExpiredGravatarsCaches"); ?>
        <a id="wpdiscuz-purge-expired-gravatars-cache" href="<?php echo esc_url_raw(wp_nonce_url($expiredCacheUrl, "purgeExpiredGravatarsCaches")); ?>" class="button button-secondary" style="text-decoration: none;"><?php esc_html_e("Purge expired caches", "wpdiscuz"); ?></a>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["purgeAvatarCache"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="purgeAllCaches">
    <div class="wpd-opt-name">
        <label for="purgeAllCaches"><?php echo esc_html($setting["options"]["purgeAllCaches"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["purgeAllCaches"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <?php $allCacheUrl = admin_url("admin-post.php?action=purgeGravatarsCaches"); ?>
        <a id="wpdiscuz-purge-gravatars-cache" href="<?php echo esc_url_raw(wp_nonce_url($allCacheUrl, "purgeGravatarsCaches")); ?>" class="button button-secondary" style="text-decoration: none;"><?php esc_html_e("Purge all caches", "wpdiscuz"); ?></a>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["purgeAllCaches"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->