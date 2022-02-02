<?php
if (!defined("ABSPATH")) {
    exit();
}
?>

<div id="wpd-dash" class="wrap wpd-dash">
    <h1 style="width:0;height:0;margin:0;padding:0;"></h1>

    <div class="wpd-dash-head">
        <div class="wpd-dash-head-left">
            <h1><?php esc_html_e("Welcome to wpDiscuz 7", "wpdiscuz"); ?></h1>
            <div class="wpd-head-subtitle"><?php esc_html_e("Built to Engage", "wpdiscuz") ?></div>
            <div class="wpd-head-welcome">
                <?php esc_html_e("Thank you for installing wpDiscuz!", "wpdiscuz"); ?><br>
                <?php esc_html_e("wpDiscuz 7 is a revolution in WordPress commenting experience.", "wpdiscuz"); ?><br>
                <?php esc_html_e("This version is mostly focused on website visitors engagement,", "wpdiscuz"); ?><br>
                <?php esc_html_e("It's totally improved with brand new innovative features bringing live to your website and discussions.", "wpdiscuz"); ?>
            </div>
        </div>
        <div class="wpd-dash-head-right">
            <img src="<?php echo plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/wpdiscuz-7-logo.png"); ?>" />
            <span class="wpd-version"><?php echo "7.0.0" ?></span>
        </div>
    </div>
    <?php do_action("wpdiscuz_option_page"); ?>
    <?php settings_errors("wpdiscuz"); ?>

    <div class="wpd-section" style="width: 98%;">
        <h3><?php esc_html_e("What's New", "wpdiscuz") ?></h3>
        <?php
        $showNews = isset($_COOKIE["wpd_show_news"]) ? intval($_COOKIE["wpd_show_news"]) : 1;
        ?>
        <div class="wpd-close wpd-toggle-news"><span class="dashicons dashicons-arrow-<?php echo $showNews ? "up" : "down" ?>"></span> <?php esc_html_e("Close", "wpdiscuz") ?></div>
    </div>

    <div id="wpdiscuz-news" class="wpdiscuz-news">
        <ul class="resp-tabs-list wpdiscuz-news-options">
            <li><?php esc_html_e("Inline Feedback", "wpdiscuz") ?></li>
            <li><?php esc_html_e("Live Notification, Bubble!", "wpdiscuz") ?></li>
            <li><?php esc_html_e("Comment Layouts", "wpdiscuz") ?></li>
            <li><?php esc_html_e("Social Login and Commenting", "wpdiscuz") ?></li>
            <li><?php esc_html_e("Article Rating vs Rating Field", "wpdiscuz") ?></li>
        </ul>
        <div class="resp-tabs-container wpdiscuz-news-options">
            <div class="wpd-tab-container">
                <img class="wpd-news-icon" src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/news/icon-feedback.png")); ?>">
                <h1><?php esc_html_e("Article Inline Feedback", "wpdiscuz"); ?></h1>
                <p><?php esc_html_e("First time in blog commenting experience we introduce, the Inline Feedback feature. This is an interactive article reading option with questions and feedback. Now article authors can add some questions for readers on certain part of article content and ask for feedback while visitors read it.", "wpdiscuz") ?></p>
                <img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/news/feedback-form.png")); ?>" style="float: right;width: 400px; padding: 0px 14px;">
                <br />
                <p style="margin-top: -10px;"><?php esc_html_e("Once a question is added in article editor (backend), on article (front-end), readers will see a small comment icon next to the text part you've selected. This feature engages post readers inviting them comment and leave a feedback without scrolling down and using the standard comment form. Thus they leave reply and react to post content or questions during the reading process.", "wpdiscuz") ?></p>
                <p style="padding-top: 5px;"><?php esc_html_e("The number of already left feedbacks will be displayed next to the feedback icon allowing people click and read other's feedbacks in the same place. Also, those feedbacks will be displayed with other standard comments in article comment section below.", "wpdiscuz") ?></p>
            </div>
            <div class="wpd-tab-container">
                <img class="wpd-news-icon" src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/news/icon-bubble.png")); ?>" style="width: 90px;">
                <h1><?php esc_html_e("Live Notification / Bubble", "wpdiscuz"); ?></h1>
                <p><?php esc_html_e(" In wpDiscuz 7 the real-time commenting becomes more live and attractive. It's based on REST API and doesn't overload your server. A specific sticky comment icon on your web pages, called &laquo;Bubble&raquo; keeps article readers and commenters up to date. It can display new comments as pop-up notification or as number in an orange circle.", "wpdiscuz") ?></p>
                <img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/news/bubble-new-comment-info.png")); ?>" style="float: right;width:45%; max-width: 420px; padding: 0px 14px;">
                <br />
                <p><?php esc_html_e("Once new comment is posted, Bubble shows pop-up message with the new comment excerpt as it's shown on screenshot. The small &laquo;Reply&raquo; button allows to reply that comment immediately or readers can click on the pop-up notification and jump to that comment thread below the article. Just make sure the Bubble Live Update is enabled in wpDiscuz > Settings > Live Commenting and Notifications options.", "wpdiscuz") ?></p>
                <img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/news/bubble-invite-to-comment.png")); ?>" style="float: left;width:45%; max-width: 420px; padding: 0px 14px;">
                <p><?php esc_html_e("If there is no new comments while visitor read the article, in most cases they don't even think about leaving some comment. The Bubble helps here too, it calls article readers to join to the discussion displaying them invite message. This message attracting readers attention and allows them fast and easy jump to comment area. Once page is loaded and visitor has read some content, it reminds about comments and calls to leave a reply.", "wpdiscuz") ?></p>
            </div>
            <div class="wpd-tab-container">
                <img class="wpd-news-icon" src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/news/icon-layouts.png")); ?>" style="width: auto; margin: 10px 5px; ">
                <h1><?php esc_html_e("Comment Layouts", "wpdiscuz"); ?></h1>
                <p><?php esc_html_e("wpDiscuz comment system design is totally changed. It comes with three nice layouts. You can even choose different layout for different pages. Three attractive, modern and clean layouts are ready to use. You can choose your proffered layout in wpDiscuz > Forms > Edit Comment Forms screen. Once the layout is changed, don't forget to delete all caches. Comment layouts are called simply &laquo;Layout #1&raquo;, &laquo;Layout #2&raquo;, &laquo;Layout #3&raquo;.", "wpdiscuz") ?></p>
                <div id="wpd_comment_layouts">
                    <div class="wpd-box-layout">
                        <a href="#img1"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/layout-1s.png")); ?>" class="wpd-com-layout-1"/></a>
                        <a href="#_" class="wpd-lightbox" id="img1"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/layout-1.png")); ?>"/></a>
                        <h4><?php esc_html_e("Comment Layout #1", "wpdiscuz") ?><br><hr style="width: 30%; margin-top: 10px; border-bottom: 1px dashed #07B290;"></h4>
                    </div>
                    <div class="wpd-box-layout">
                        <a href="#img2"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/layout-2s.png")); ?>" class="wpd-com-layout-2"/></a>
                        <a href="#_" class="wpd-lightbox" id="img2"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/layout-2.png")); ?>"/></a>
                        <h4><?php esc_html_e("Comment Layout #2", "wpdiscuz") ?><br><hr style="width: 30%; margin-top: 10px; border-bottom: 1px dashed #07B290;"></h4>
                    </div>
                    <div class="wpd-box-layout">
                        <a href="#img3"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/layout-3s.png")); ?>" class="wpd-com-layout-3"/></a>
                        <a href="#_" class="wpd-lightbox" id="img3"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/layout-3.png")); ?>"/></a>
                        <h4><?php esc_html_e("Comment Layout #3", "wpdiscuz") ?><br><hr style="width: 30%; margin-top: 10px; border-bottom: 1px dashed #07B290;"></h4>
                    </div>
                </div>
                <p><?php esc_html_e("The &laquo;Layout #1&raquo; is the simplest and cleanest layout. The &laquo;Layout #2&raquo; is designed for narrow comment sections. It displays comment content in wider area. The &laquo;Layout #3&raquo; layout is designed to accent comment thread hierarchy by colored vertical lines and indents.", "wpdiscuz") ?></p>
            </div>
            <div class="wpd-tab-container">
                <img class="wpd-news-icon" src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/news/icon-social.png")); ?>" style="width: 95px; margin: 5px 15px;">
                <h1><?php esc_html_e("Social Login and Commenting", "wpdiscuz"); ?></h1>
                <p><?php esc_html_e("wpDiscuz comes with built-in social login and share buttons. It includes Facebook, Twitter, Google, Disqus, WordPress.org, VK and OK Social Networks. You can enable those by managing API Keys in wpDiscuz > Settings > Social Login and Share options.", "wpdiscuz") ?></p>
                <img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/news/social-networks-shadow.png")); ?>" style="float: right; width: 65%; max-width: 520px; padding: 0px 14px; margin-bottom: -20px; position: relative; z-index: 10;">
                <div class="wpd-clear"></div>
                <img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/news/wpDiscuz-Social-Network-icons-on-user-avatars.png")); ?>" style="float: left;width:75%; max-width: 580px; padding: 0px 14px;">
                <div class="wpd-clear"></div>
            </div>
            <div class="wpd-tab-container">
                <img class="wpd-news-icon" src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/news/icon-rating.png")); ?>" style="width: auto; margin: 5px;">
                <h1><?php esc_html_e("Article Rating vs Rating Field", "wpdiscuz"); ?></h1>
                <p><?php esc_html_e("Before, you had to create a Rating field in comment form to allow users rate article while they post a comment, there was no way to rate without commenting. Now you can allow users rate your articles without leavening a comment. wpDiscuz 7 has a built-in Post Rating system which is not based on comment form custom fields. You can see that on top of comment section like the left one on the screenshot below:", "wpdiscuz") ?></p>
                <div class="wpd-zoom-image" style="width: 98%; margin: 10px auto;">
                    <a href="#img5"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/news/rating-vs.png")); ?>" style="width: 100%;"/></a>
                    <a href="#_" class="wpd-lightbox" id="img5"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/news/rating-vs-v.png")); ?>"/></a>
                </div>
            </div>
        </div>
    </div>

    <div style="clear: both;"></div>

    <div class="wpd-section wpd-sec-stat" style="margin-top:50px;">
        <h3><?php esc_html_e("Overview & Comments Statistic", "wpdiscuz") ?></h3>
        <div class="wpd-close"></div>
    </div>

    <!-- overview start -->
    <div class="wpd-overview">
        <!-- stat-brief start -->
        <div class="wpd-stat-brief">
            <div class="wpd-stat-box wpd-box wpd-stat-brief-top">
                <div class="wpd-stat-cell">
                    <div class="wpd-cell-icon"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/icon-comment.png")); ?>" width="30"></div>
                    <div class="wpd-cell-text"><span class="wpd-cell-num wpd-stat-brief-all"><strong>0</strong></span><span class="wpd-cell-label"><?php esc_html_e("All Comments", "wpdiscuz") ?></span></div>
                </div>
                <div class="wpd-stat-cell">
                    <div class="wpd-cell-icon"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/icon-comment-inlinepng.png")); ?>" width="36"></div>
                    <div class="wpd-cell-text"><span class="wpd-cell-num wpd-stat-brief-inline"><strong>0</strong></span><span class="wpd-cell-label"><?php esc_html_e("Inline Feedbacks", "wpdiscuz") ?></span></div>
                </div>
                <div class="wpd-stat-cell">
                    <div class="wpd-cell-icon"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/icon-threads.png")); ?>"></div>
                    <div class="wpd-cell-text"><span class="wpd-cell-num wpd-stat-brief-threads"><strong>0</strong></span><span class="wpd-cell-label"><?php esc_html_e("Comment Threads", "wpdiscuz") ?></span></div>
                </div>
                <div class="wpd-stat-cell">
                    <div class="wpd-cell-icon"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/icon-replies.png")); ?>" width="30"></div>
                    <div class="wpd-cell-text"><span class="wpd-cell-num wpd-stat-brief-replies"><strong>0</strong></span><span class="wpd-cell-label"><?php esc_html_e("Thread Replies", "wpdiscuz") ?></span></div>
                </div>
            </div>
            <div class="wpd-stat-box wpd-box wpd-stat-brief-bottom">
                <div class="wpd-stat-cell">
                    <div class="wpd-cell-icon"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/icon-users.png")); ?>" width="24"></div>
                    <div class="wpd-cell-text"><span class="wpd-cell-num wpd-stat-brief-users"><strong>0</strong></span><span class="wpd-cell-label"><?php esc_html_e("User Commenters", "wpdiscuz") ?></span></div>
                </div>
                <div class="wpd-stat-cell">
                    <div class="wpd-cell-icon"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/icon-guests.png")); ?>" width="24"></div>
                    <div class="wpd-cell-text"><span class="wpd-cell-num wpd-stat-brief-guests"><strong>0</strong></span><span class="wpd-cell-label"><?php esc_html_e("Guest Commenters", "wpdiscuz") ?></span></div>
                </div>
            </div>
        </div>
        <!-- stat-brief end -->
        <!-- stat-graph start -->
        <div class="wpd-stat-graph wpd-box" data-box="wpd_stat_graph">
            <?php
            $showGraph = isset($_COOKIE["wpd_stat_graph"]) ? intval($_COOKIE["wpd_stat_graph"]) : 1;
            ?>
            <div class="wpd-box-head">
                <h3><?php esc_html_e("Comment Statistic", "wpdiscuz") ?></h3>
                <div class="wpd-box-info"<?php echo $showGraph ? "" : " style='display: none;'" ?>>
                    <span><span class="dashicons dashicons-marker" style="color: #46C08F"></span> <?php esc_html_e("All comments", "wpdiscuz") ?></span>
                    &nbsp;&nbsp;
                    <span><span class="dashicons dashicons-marker" style="color: #0498F9"></span> <?php esc_html_e("Inline feedbacks", "wpdiscuz") ?></span>
                </div>
                <div class="wpd-box-toggle">
                    <span class="dashicons dashicons-admin-generic"<?php echo $showGraph ? "" : " style='display: none;'" ?>></span>
                    <span class="dashicons dashicons-arrow-<?php echo $showGraph ? "up" : "down" ?> wpd_not_clicked"></span>
                    <div class="wpd-graph-tools">
                        <span class="wpd_not_clicked" data-interval="today"><?php esc_html_e("Today", "wpdiscuz") ?></span>
                        <span class="wpd_not_clicked" data-interval="week"><?php esc_html_e("Last Week", "wpdiscuz") ?></span>
                        <span class="wpd_not_clicked" data-interval="month"><?php esc_html_e("Last Month", "wpdiscuz") ?></span>
                        <span class="wpd_not_clicked" data-interval="6months"><?php esc_html_e("Last 6 Months", "wpdiscuz") ?></span>
                        <span class="wpd_not_clicked" data-interval="year"><?php esc_html_e("Last Year", "wpdiscuz") ?></span>
                        <span class="wpd_not_clicked" data-interval="all"><?php esc_html_e("All Time", "wpdiscuz") ?></span>
                    </div>
                </div>
            </div>
            <div class="wpd-box-body"<?php echo $showGraph ? "" : " style='display: none;'"; ?>></div>
        </div>
        <!-- stat-graph end -->
        <!-- stat-user start -->
        <div class="wpd-stat-user wpd-box" data-box="wpd_stat_user">
            <?php
            $showUser = isset($_COOKIE["wpd_stat_user"]) ? intval($_COOKIE["wpd_stat_user"]) : 1;
            ?>
            <div class="wpd-box-head">
                <h3><?php esc_html_e("Active Users and Guests", "wpdiscuz") ?></h3>
                <div class="wpd-box-toggle">
                    <span class="dashicons dashicons-arrow-left" style="visibility: hidden;<?php echo $showUser ? "" : "display: none;"; ?>"></span>
                    <span class="dashicons dashicons-arrow-right"<?php echo $showUser ? "" : " style='display: none;'"; ?>></span>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <span class="dashicons dashicons-arrow-<?php echo $showUser ? "up" : "down" ?> wpd_not_clicked"></span>
                </div>
            </div>
            <div class="wpd-box-body"<?php echo $showUser ? "" : " style='display: none;'"; ?>></div>
        </div>
        <!-- stat-user end -->
        <!-- stat-subs start -->
        <div class="wpd-stat-subs wpd-box" data-box="wpd_stat_subs">
            <?php
            $showSubs = isset($_COOKIE["wpd_stat_subs"]) ? intval($_COOKIE["wpd_stat_subs"]) : 1;
            ?>
            <div class="wpd-box-head">
                <h3><?php esc_html_e("Subscriptions", "wpdiscuz"); ?></h3>
                <div class="wpd-box-toggle">
                    <span class="dashicons dashicons-arrow-<?php echo $showSubs ? "up" : "down"; ?> wpd_not_clicked"></span>
                </div>
            </div>
            <div class="wpd-box-body"<?php echo $showSubs ? "" : " style='display: none;'"; ?>></div>
        </div>
        <!-- stat-subs end -->
    </div>
    <!-- overview end -->

    <div class="wpd-section wpd-sec-stat" style="margin-top:60px;">
        <h3><?php esc_html_e("Credits", "wpdiscuz") ?></h3>
        <div class="wpd-close"></div>
    </div>

    <div class="wpd-credits wpd-box">
        <div class="wpd-widget">
            <div class="wpd-widget-head"><?php esc_html_e("Documentation", "wpdiscuz") ?></div>
            <div class="wpd-widget-body">
                <ul>
                    <li><a href="https://wpdiscuz.com/docs/wpdiscuz-7/getting-started/comment-forms/" target="_blank"><span class="dashicons dashicons-editor-help"></span> <?php esc_html_e("Getting Started", "wpdiscuz") ?></a></li>
                    <li><a href="https://wpdiscuz.com/docs/wpdiscuz-7/getting-started/manage-comment-forms/" target="_blank"><span class="dashicons dashicons-editor-help"></span> <?php esc_html_e("Manage Comment Forms", "wpdiscuz") ?></a></li>
                    <li><a href="https://wpdiscuz.com/docs/wpdiscuz-7/customization/comment-layouts/" target="_blank"><span class="dashicons dashicons-editor-help"></span> <?php esc_html_e("Manage Comment Layout", "wpdiscuz") ?></a></li>
                    <li><a href="https://wpdiscuz.com/docs/wpdiscuz-7/customization/custom-template-and-style/" target="_blank"><span class="dashicons dashicons-editor-help"></span> <?php esc_html_e("Plugin Customization", "wpdiscuz") ?></a></li>
                    <li><a href="https://wpdiscuz.com/docs/wpdiscuz-7/translation/translation-methods/" target="_blank"><span class="dashicons dashicons-editor-help"></span> <?php esc_html_e("Translation", "wpdiscuz") ?></a></li>
                    <li><a href="https://wpdiscuz.com/docs/wpdiscuz-7/privacy-and-gdpr/" target="_blank"><span class="dashicons dashicons-editor-help"></span> <?php esc_html_e("Privacy and GDPR", "wpdiscuz") ?></a></li>
                </ul>
            </div>
        </div>
        <div class="wpd-widget">
            <div class="wpd-widget-head"><?php esc_html_e("Support & Community", "wpdiscuz") ?></div>
            <div class="wpd-widget-body">
                <ul>
                    <li><a href="https://wpdiscuz.com/demo/" target="_blank"><span class="dashicons dashicons-admin-links"></span> <?php esc_html_e("wpDiscuz Demo", "wpdiscuz") ?></a></li>
                    <li><a href="https://wpdiscuz.com/support/" target="_blank"><span class="dashicons dashicons-admin-links"></span> <?php esc_html_e("wpDiscuz Support", "wpdiscuz") ?></a></li>
                    <li><a href="https://gvectors.com/forum/official-wpdiscuz-add-ons/" target="_blank"><span class="dashicons dashicons-admin-links"></span> <?php esc_html_e("wpDiscuz Addons Support", "wpdiscuz") ?></a></li>
                    <li><a href="https://wordpress.org/plugins/wpdiscuz/" target="_blank"><span class="dashicons dashicons-admin-links"></span> <?php esc_html_e("wpDiscuz Plugin Page", "wpdiscuz") ?></a></li>
                </ul>
            </div>
        </div>
        <div class="wpd-widget">
            <div class="wpd-widget-head"><?php esc_html_e("wpDiscuz Addons", "wpdiscuz") ?></div>
            <div class="wpd-widget-body">
                <p><a href="https://gvectors.com/product-category/wpdiscuz/" target="_blank"><span class="dashicons dashicons-admin-plugins"></span> <?php esc_html_e("wpDiscuz Addons", "wpdiscuz") ?></a></p>
                <p><?php esc_html_e("Get all wpDiscuz premium addons with unlimited site license and save 90% with", "wpdiscuz"); ?> <a href="https://gvectors.com/product/wpdiscuz-addons-bundle/" target="_blank">wpDiscuz Addons Bundle</a></p>
            </div>
        </div>
        <div class="wpd-widget">
            <div class="wpd-widget-head"><?php esc_html_e("Contribute", "wpdiscuz") ?></div>
            <div class="wpd-widget-body">
                <h4 style="margin: 12px 0px 0px; color:#DD0000"><span class="dashicons dashicons-translation"></span> <?php esc_html_e("Help to Translate wpDiscuz", "wpdiscuz") ?></h4>
                <p style="margin-top:0px;"><?php esc_html_e("We'd really appreciate if you could help translating wpDiscuz to your language.", "wpdiscuz") ?> <a href="https://translate.wordpress.org/projects/wp-plugins/wpdiscuz/" target="_blank"><?php esc_html_e("Just log in to the translation platform with your WordPress.org account, and suggest translations.", "wpdiscuz") ?></a></p>
                <h4 style="margin: 10px 0px 0px;">
                    <?php esc_html_e("Leave a Good Review", "wpdiscuz") ?> &nbsp;
                    <a href="https://wordpress.org/support/plugin/wpdiscuz/reviews/?filter=5" target="_blank" title="View wpDiscuz Reviews"><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span></a>
                </h4>
                <p style="margin-top:0px;"><?php esc_html_e("We love your reviews. This is the best way to say thank you to developers and support team.", "wpdiscuz") ?></p>
            </div>
        </div>
    </div>

</div>
<script>
    jQuery(document).ready(function ($) {
        $('#wpdiscuz-news').wpdiscuzEasyResponsiveTabs({type: 'vertical', width: 'auto', fit: true, tabidentify: 'wpdiscuz-news-options'});
    });
</script>