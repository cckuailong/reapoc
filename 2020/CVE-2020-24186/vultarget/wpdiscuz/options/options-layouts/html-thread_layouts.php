<?php
if (!defined("ABSPATH")) {
    exit();
}
?>
<!-- Option start -->
<div class="wpd-opt-row" style="border-bottom: none;">
    <div class="wpd-opt-intro">
        <img class="wpd-opt-img" src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/" . $setting["icon"])); ?>" style="height: 65px; padding-top: 5px;"/>
        <?php esc_html_e("Here you can manage comment layout components. You can display/hide certain button or information on comment threads, as well as commenters' avatars and comment voting options.", "wpdiscuz"); ?><br>
        <?php echo sprintf(esc_html__("wpDiscuz 7 comes with three modern and totally different comment thread layouts. They are called Layout #1, Layout #2 and Layout #3.", "wpdiscuz"), ""); ?>
        <div id="wpd_comment_layouts">
            <div class="wpd-box-layout">
                <a href="#img1"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/layout-1s.png")); ?>" class="wpd-com-layout-1"/></a>
                <a href="#_" class="wpd-lightbox" id="img1"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/layout-1.png")); ?>"/></a>
                <h4><?php esc_html_e("Comment Thread Layout #1", "wpdiscuz") ?><br><hr style="width: 30%; margin-top: 10px; border-bottom: 1px dashed #07B290;"></h4>
            </div>
            <div class="wpd-box-layout">
                <a href="#img2"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/layout-2s.png")); ?>" class="wpd-com-layout-2"/></a>
                <a href="#_" class="wpd-lightbox" id="img2"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/layout-2.png")); ?>"/></a>
                <h4><?php esc_html_e("Comment Thread Layout #2", "wpdiscuz") ?><br><hr style="width: 30%; margin-top: 10px; border-bottom: 1px dashed #07B290;"></h4>
            </div>
            <div class="wpd-box-layout">
                <a href="#img3"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/layout-3s.png")); ?>" class="wpd-com-layout-3"/></a>
                <a href="#_" class="wpd-lightbox" id="img3"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/layout-3.png")); ?>"/></a>
                <h4><?php esc_html_e("Comment Thread Layout #3", "wpdiscuz") ?><br><hr style="width: 30%; margin-top: 10px; border-bottom: 1px dashed #07B290;"></h4>
            </div>
        </div>
        <?php echo sprintf(esc_html__("You can select different comment thread layout for different comment forms in %s", "wpdiscuz"), "<a href='" . esc_url_raw(admin_url("edit.php?post_type=wpdiscuz_form")) . "'>" . esc_html__("Comment Form Manager &raquo;", "wpdiscuz") . "</a>"); ?>
    </div>
    <div class="wpd-opt-doc" style="padding-top: 10px;">
        <a href="https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-thread-features/" title="<?php esc_attr_e("Read the documentation", "wpdiscuz") ?>" target="_blank"><i class="far fa-question-circle"></i></a>
    </div>
</div>
<!-- Option end -->


<div class="wpd-subtitle" style="padding-top: 10px;">
    <span class="dashicons dashicons-id"></span> <?php esc_html_e("Avatar Settings", "wpdiscuz") ?>
</div>

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="showAvatars">
    <div class="wpd-opt-name">
        <label for="showAvatars"><?php echo esc_html($setting["options"]["showAvatars"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["showAvatars"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->thread_layouts["showAvatars"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_LAYOUTS); ?>[showAvatars]" id="showAvatars">
            <label for="showAvatars"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["showAvatars"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="defaultAvatarUrlForUser">
    <div class="wpd-opt-name">
        <label for="defaultAvatarUrlForUser"><?php echo esc_html($setting["options"]["defaultAvatarUrlForUser"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["defaultAvatarUrlForUser"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input type="url" value="<?php echo esc_attr($this->thread_layouts["defaultAvatarUrlForUser"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_LAYOUTS); ?>[defaultAvatarUrlForUser]" id="defaultAvatarUrlForUser" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["defaultAvatarUrlForUser"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="defaultAvatarUrlForGuest">
    <div class="wpd-opt-name">
        <label for="defaultAvatarUrlForGuest"><?php echo esc_html($setting["options"]["defaultAvatarUrlForGuest"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["defaultAvatarUrlForGuest"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <input type="url" value="<?php echo esc_attr($this->thread_layouts["defaultAvatarUrlForGuest"]); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_LAYOUTS); ?>[defaultAvatarUrlForGuest]" id="defaultAvatarUrlForGuest" />
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["defaultAvatarUrlForGuest"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="changeAvatarsEverywhere">
    <div class="wpd-opt-name">
        <label for="changeAvatarsEverywhere"><?php echo esc_html($setting["options"]["changeAvatarsEverywhere"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["changeAvatarsEverywhere"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->thread_layouts["changeAvatarsEverywhere"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_LAYOUTS); ?>[changeAvatarsEverywhere]" id="changeAvatarsEverywhere">
            <label for="changeAvatarsEverywhere"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["changeAvatarsEverywhere"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<div class="wpd-subtitle">
    <span class="dashicons dashicons-thumbs-up"></span> <?php esc_html_e("Comment Voting Buttons", "wpdiscuz") ?>
</div>

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="showVotingButtons">
    <div class="wpd-opt-name">
        <label for="showVotingButtons"><?php echo esc_html($setting["options"]["showVotingButtons"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["showVotingButtons"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->thread_layouts["showVotingButtons"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_LAYOUTS); ?>[showVotingButtons]" id="showVotingButtons">
            <label for="showVotingButtons"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["showVotingButtons"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="votingButtonsIcon">
    <div class="wpd-opt-name">
        <label for="votingButtonsIcon"><?php echo esc_html($setting["options"]["votingButtonsIcon"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["votingButtonsIcon"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switch-field" style="min-width: 220px;">
            <input type="radio" <?php checked($this->thread_layouts["votingButtonsIcon"] == "fa-plus|fa-minus") ?> value="fa-plus|fa-minus" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_LAYOUTS); ?>[votingButtonsIcon]" id="votingButtonsIconPlusMinus" class="votingButtonsIconPlusMinus" style="vertical-align: bottom;"/>
            <label for="votingButtonsIconPlusMinus" style="min-width:50px;"><i class="fas fa-plus"></i> <i class="fas fa-minus"></i></label>
            <input type="radio" <?php checked($this->thread_layouts["votingButtonsIcon"] == "fa-chevron-up|fa-chevron-down") ?> value="fa-chevron-up|fa-chevron-down" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_LAYOUTS); ?>[votingButtonsIcon]" id="votingButtonsIconChevronUpDown" class="votingButtonsIconChevronUpDown" style="vertical-align: bottom;"/>
            <label for="votingButtonsIconChevronUpDown" style="min-width:50px;"><i class="fas fa-chevron-up"></i> <i class="fas fa-chevron-down"></i></label>
            <input type="radio" <?php checked($this->thread_layouts["votingButtonsIcon"] == "fa-thumbs-up|fa-thumbs-down") ?> value="fa-thumbs-up|fa-thumbs-down" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_LAYOUTS); ?>[votingButtonsIcon]" id="votingButtonsIconThumbsUpDown" class="votingButtonsIconThumbsUpDown" style="vertical-align: bottom;"/>
            <label for="votingButtonsIconThumbsUpDown" style="min-width:50px;"><i class="fas fa-thumbs-up"></i> <i class="fas fa-thumbs-down"></i></label>
            <input type="radio" <?php checked($this->thread_layouts["votingButtonsIcon"] == "fa-smile|fa-frown") ?> value="fa-smile|fa-frown" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_LAYOUTS); ?>[votingButtonsIcon]" id="votingButtonsIconSmileFrown" class="votingButtonsIconSmileFrown" style="vertical-align: bottom;"/>
            <label for="votingButtonsIconSmileFrown" style="min-width:50px;"><i class="far fa-smile"></i> <i class="far fa-frown"></i></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["votingButtonsIcon"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="votingButtonsStyle">
    <div class="wpd-opt-name">
        <label for="votingButtonsStyle"><?php echo esc_html($setting["options"]["votingButtonsStyle"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["votingButtonsStyle"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switch-field">
            <input type="radio" <?php checked($this->thread_layouts["votingButtonsStyle"] == 0) ?> value="0" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_LAYOUTS); ?>[votingButtonsStyle]" id="votingButtonsStyleTotal" class="votingButtonsStyle"/><label for="votingButtonsStyleTotal"><?php esc_html_e("total count", "wpdiscuz"); ?></label> &nbsp;
            <input type="radio" <?php checked($this->thread_layouts["votingButtonsStyle"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_LAYOUTS); ?>[votingButtonsStyle]" id="votingButtonsStyleSeparate" class="votingButtonsStyle"/><label for="votingButtonsStyleSeparate"><?php esc_html_e("separate count", "wpdiscuz"); ?></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["votingButtonsStyle"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="enableDislikeButton">
    <div class="wpd-opt-name">
        <label for="enableDislikeButton"><?php echo esc_html($setting["options"]["enableDislikeButton"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["enableDislikeButton"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->thread_layouts["enableDislikeButton"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_LAYOUTS); ?>[enableDislikeButton]" id="enableDislikeButton">
            <label for="enableDislikeButton"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["enableDislikeButton"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="isGuestCanVote">
    <div class="wpd-opt-name">
        <label for="isGuestCanVote"><?php echo esc_html($setting["options"]["isGuestCanVote"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["isGuestCanVote"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->thread_layouts["isGuestCanVote"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_LAYOUTS); ?>[isGuestCanVote]" id="isGuestCanVote">
            <label for="isGuestCanVote"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["isGuestCanVote"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="highlightVotingButtons">
    <div class="wpd-opt-name">
        <label for="highlightVotingButtons"><?php echo esc_html($setting["options"]["highlightVotingButtons"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["highlightVotingButtons"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->thread_layouts["highlightVotingButtons"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_LAYOUTS); ?>[highlightVotingButtons]" id="highlightVotingButtons">
            <label for="highlightVotingButtons"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["highlightVotingButtons"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->


<div class="wpd-subtitle">
    <span class="dashicons dashicons-excerpt-view"></span> <?php esc_html_e("Layout Components", "wpdiscuz") ?>
</div>

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="showCommentLink">
    <div class="wpd-opt-name">
        <label for="showCommentLink"><?php echo esc_html($setting["options"]["showCommentLink"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["showCommentLink"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->thread_layouts["showCommentLink"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_LAYOUTS); ?>[showCommentLink]" id="showCommentLink">
            <label for="showCommentLink"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["showCommentLink"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="showCommentDate">
    <div class="wpd-opt-name">
        <label for="showCommentDate"><?php echo esc_html($setting["options"]["showCommentDate"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["showCommentDate"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->thread_layouts["showCommentDate"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_LAYOUTS); ?>[showCommentDate]" id="showCommentDate">
            <label for="showCommentDate"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["showCommentDate"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->