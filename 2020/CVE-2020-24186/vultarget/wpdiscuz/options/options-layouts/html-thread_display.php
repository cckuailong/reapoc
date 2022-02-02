<?php
if (!defined("ABSPATH")) {
    exit();
}
?>
<!-- Option start -->
<div class="wpd-opt-row">
    <div class="wpd-opt-intro">
        <img class="wpd-opt-img" src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/" . $setting["icon"])); ?>" style="height: 70px; padding-top: 5px;"/>
        <?php esc_html_e('Here you can find all necessary options to control comment threads loading, displaying and sorting functions. Using "Comment List Loading Type", "Comments Pagination Type" and "Display only parent comments" options, you can get the highest page loading speed.  Also you can manage comment thread filtering buttons.', "wpdiscuz"); ?>
    </div>
    <div class="wpd-opt-doc" style="padding-top: 10px;">
        <a href="https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-thread-displaying/" title="<?php esc_attr_e("Read the documentation", "wpdiscuz") ?>" target="_blank"><i class="far fa-question-circle"></i></a>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="firstLoadWithAjax">
    <div class="wpd-opt-name">
        <label for="firstLoadWithAjax"><?php echo esc_html($setting["options"]["firstLoadWithAjax"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["firstLoadWithAjax"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-radio">
            <input type="radio" value="0" <?php checked(0 == $this->thread_display["firstLoadWithAjax"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_DISPLAY); ?>[firstLoadWithAjax]" id="disableFirstLoadWithAjax" class="firstLoadWithAjax"/>
            <label for="disableFirstLoadWithAjax" class="wpd-radio-circle"></label>
            <label for="disableFirstLoadWithAjax"><?php esc_html_e("Load with page", "wpdiscuz") ?></label>
        </div>
        <div class="wpd-radio">
            <input type="radio" value="1" <?php checked(1 == $this->thread_display["firstLoadWithAjax"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_DISPLAY); ?>[firstLoadWithAjax]" id="loadWithAjax" class="firstLoadWithAjax"/>
            <label for="loadWithAjax" class="wpd-radio-circle"></label>
            <label for="loadWithAjax" title="<?php esc_attr_e("Initiates AJAX loading once page loading is complete", "wpdiscuz") ?>"><?php esc_html_e("Initiate AJAX loading after page", "wpdiscuz") ?></label>
        </div>
        <div class="wpd-radio">
            <input type="radio" value="2" <?php checked(2 == $this->thread_display["firstLoadWithAjax"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_DISPLAY); ?>[firstLoadWithAjax]" id="firstLoadWithAjaxButton" class="firstLoadWithAjax"/>
            <label for="firstLoadWithAjaxButton" class="wpd-radio-circle"></label>
            <label for="firstLoadWithAjaxButton" title="<?php esc_attr_e("Display [View Comments] button to load comments manually", "wpdiscuz") ?>"><?php esc_html_e("Display [View Comments] button", "wpdiscuz") ?></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["firstLoadWithAjax"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="commentListLoadType">
    <div class="wpd-opt-name">
        <label for="commentListLoadType"><?php echo esc_html($setting["options"]["commentListLoadType"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["commentListLoadType"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <fieldset>
            <div class="wpd-radio">
                <input type="radio" value="0" <?php checked(0 == $this->thread_display["commentListLoadType"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_DISPLAY); ?>[commentListLoadType]" id="commentListLoadDefault" class="commentListLoadType"/>
                <label for="commentListLoadDefault" class="wpd-radio-circle"></label>
                <label for="commentListLoadDefault"><?php esc_html_e("[Load more] Button", "wpdiscuz") ?></label>
            </div>
            <div class="wpd-radio">
                <input type="radio" value="1" <?php checked(1 == $this->thread_display["commentListLoadType"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_DISPLAY); ?>[commentListLoadType]" id="commentListLoadRest" class="commentListLoadType"/>
                <label for="commentListLoadRest" class="wpd-radio-circle"></label>
                <label for="commentListLoadRest"><?php esc_html_e("[Load rest of all comments] Button", "wpdiscuz") ?></label>
            </div>
            <div class="wpd-radio">
                <input type="radio" value="3" <?php checked(3 == $this->thread_display["commentListLoadType"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_DISPLAY); ?>[commentListLoadType]" id="commentListLoadAll" class="commentListLoadType"/>
                <label for="commentListLoadAll" class="wpd-radio-circle"></label>
                <label for="commentListLoadAll"><?php esc_html_e("Load all comments", "wpdiscuz") ?></label>
            </div>
            <div class="wpd-radio">
                <input type="radio" value="2" <?php checked(2 == $this->thread_display["commentListLoadType"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_DISPLAY); ?>[commentListLoadType]" id="commentListLoadLazy" class="commentListLoadType"/>
                <label for="commentListLoadLazy" class="wpd-radio-circle"></label>
                <label for="commentListLoadLazy"><?php esc_html_e("Lazy load comments on scrolling", "wpdiscuz") ?></label>
            </div>
        </fieldset>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["commentListLoadType"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="isLoadOnlyParentComments">
    <div class="wpd-opt-name">
        <label for="isLoadOnlyParentComments"><?php echo $setting["options"]["isLoadOnlyParentComments"]["label"] ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["isLoadOnlyParentComments"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->thread_display["isLoadOnlyParentComments"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_DISPLAY); ?>[isLoadOnlyParentComments]" id="isLoadOnlyParentComments">
            <label for="isLoadOnlyParentComments"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["isLoadOnlyParentComments"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="showReactedFilterButton">
    <div class="wpd-opt-name">
        <label for="showReactedFilterButton"><?php echo esc_html($setting["options"]["showReactedFilterButton"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["showReactedFilterButton"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->thread_display["showReactedFilterButton"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_DISPLAY); ?>[showReactedFilterButton]" id="showReactedFilterButton">
            <label for="showReactedFilterButton"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["showReactedFilterButton"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="showHottestFilterButton">
    <div class="wpd-opt-name">
        <label for="showHottestFilterButton"><?php echo esc_html($setting["options"]["showHottestFilterButton"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["showHottestFilterButton"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->thread_display["showHottestFilterButton"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_DISPLAY); ?>[showHottestFilterButton]" id="showHottestFilterButton">
            <label for="showHottestFilterButton"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["showHottestFilterButton"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="showSortingButtons">
    <div class="wpd-opt-name">
        <label for="showSortingButtons"><?php echo esc_html($setting["options"]["showSortingButtons"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["showSortingButtons"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->thread_display["showSortingButtons"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_DISPLAY); ?>[showSortingButtons]" id="showSortingButtons">
            <label for="showSortingButtons"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["showSortingButtons"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="mostVotedByDefault">
    <div class="wpd-opt-name">
        <label for="mostVotedByDefault"><?php echo esc_html($setting["options"]["mostVotedByDefault"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["mostVotedByDefault"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->thread_display["mostVotedByDefault"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_DISPLAY); ?>[mostVotedByDefault]" id="mostVotedByDefault">
            <label for="mostVotedByDefault"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["mostVotedByDefault"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="reverseChildren">
    <div class="wpd-opt-name">
        <label for="reverseChildren"><?php echo esc_html($setting["options"]["reverseChildren"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["reverseChildren"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->thread_display["reverseChildren"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_DISPLAY); ?>[reverseChildren]" id="reverseChildren">
            <label for="reverseChildren"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["reverseChildren"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="highlightUnreadComments">
    <div class="wpd-opt-name">
        <label for="highlightUnreadComments"><?php echo esc_html($setting["options"]["highlightUnreadComments"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["highlightUnreadComments"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->thread_display["highlightUnreadComments"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_DISPLAY); ?>[highlightUnreadComments]" id="highlightUnreadComments">
            <label for="highlightUnreadComments"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["highlightUnreadComments"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="scrollToComment">
    <div class="wpd-opt-name">
        <label for="scrollToComment"><?php echo $setting["options"]["scrollToComment"]["label"] ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["scrollToComment"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switcher">
            <input type="checkbox" <?php checked($this->thread_display["scrollToComment"] == 1) ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_DISPLAY); ?>[scrollToComment]" id="scrollToComment">
            <label for="scrollToComment"></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["scrollToComment"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="orderCommentsBy">
    <div class="wpd-opt-name">
        <label for="orderCommentsBy"><?php echo esc_html($setting["options"]["orderCommentsBy"]["label"]) ?></label>
        <p class="wpd-desc"><?php echo $setting["options"]["orderCommentsBy"]["description"] ?></p>
    </div>
    <div class="wpd-opt-input">
        <div class="wpd-switch-field">
            <input type="radio" value="comment_ID" <?php checked("comment_ID" === $this->thread_display["orderCommentsBy"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_DISPLAY); ?>[orderCommentsBy]" id="orderCommentsById" />
            <label for="orderCommentsById" style="min-width:60px;"><?php esc_html_e("ID", "wpdiscuz"); ?></label>
            <input type="radio" value="comment_date_gmt" <?php checked("comment_date_gmt" === $this->thread_display["orderCommentsBy"]); ?> name="<?php echo esc_attr(WpdiscuzCore::TAB_THREAD_DISPLAY); ?>[orderCommentsBy]" id="orderCommentsByDate" />
            <label for="orderCommentsByDate" style="min-width:60px;"><?php esc_html_e("Date", "wpdiscuz"); ?></label>
        </div>
    </div>
    <div class="wpd-opt-doc">
        <?php $this->printDocLink($setting["options"]["orderCommentsBy"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->