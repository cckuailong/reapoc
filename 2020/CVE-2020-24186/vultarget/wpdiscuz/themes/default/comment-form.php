<?php
if (!defined("ABSPATH")) {
    exit();
}

$loadStartTime = WpdiscuzHelper::getMicrotime();
global $post;
$wpdiscuz = wpDiscuz();
if (!function_exists("wpdiscuz_close_divs")) {

    function wpdiscuz_close_divs($html) {
        global $wpdiscuz;
        @preg_match_all("|<div|is", $html, $wc_div_open, PREG_SET_ORDER);
        @preg_match_all("|</div|is", $html, $wc_div_close, PREG_SET_ORDER);
        $wc_div_open = count((array) $wc_div_open);
        $wc_div_close = count((array) $wc_div_close);
        $wc_div_delta = $wc_div_open - $wc_div_close;
        if ($wc_div_delta) {
            $wc_div_end_html = str_repeat("</div>", $wc_div_delta);
            $html = $html . $wc_div_end_html;
        }
        return $html;
    }

}

$currentUser = $wpdiscuz->helper->getCurrentUser();
do_action("wpdiscuz_before_load", $post, $currentUser);
if (!post_password_required($post->ID)) {
    $commentsCount = get_comments_number();
    $wpCommClasses = [];
    $wpCommClasses[] = $currentUser && $currentUser->ID ? "wpdiscuz_auth" : "wpdiscuz_unauth";
    $wpCommClasses[] = $wpdiscuz->options->thread_styles["theme"];

    if (!$wpdiscuz->options->thread_layouts["showAvatars"] || !$wpdiscuz->options->wp["showAvatars"]) {
        $wpCommClasses[] = "wpdiscuz_no_avatar";
    }

    $ob_stat = ini_get("output_buffering");
    if ($ob_stat || $ob_stat === "" || $ob_stat == "0") {
        $wc_ob_allowed = true;
        ob_start();
        do_action("comment_form_top");
        do_action("wpdiscuz_comment_form_top", $post, $currentUser, $commentsCount);
        $wc_comment_form_top_content = ob_get_clean();
        $wc_comment_form_top_content = wpdiscuz_close_divs($wc_comment_form_top_content);
    } else {
        $wc_ob_allowed = false;
    }

    if ((isset($_GET["deleteComments"]) && $_GET["deleteComments"])) {
        $decodedEmail = get_transient(WpDiscuzConstants::TRS_USER_HASH . trim($_GET["deleteComments"]));
        if ($decodedEmail) {
            $comments = get_comments(["author_email" => $decodedEmail, "status" => "all", "fields" => "ids"]);
            if ($comments) {
                foreach ($comments as $k => $cid) {
                    wp_delete_comment($cid, true);
                }
                ?>
                <script>
                    jQuery(document).ready(function ($) {
                        wpdMessagesOnInit('<?php echo esc_html($wpdiscuz->options->phrases["wc_comments_are_deleted"]); ?>', 'success');
                    });
                </script>
                <?php
            }
        }
    } else if (isset($_GET["deleteSubscriptions"]) && $_GET["deleteSubscriptions"]) {
        $decodedEmail = get_transient(WpDiscuzConstants::TRS_USER_HASH . trim($_GET["deleteSubscriptions"]));
        if ($decodedEmail) {
            $wpdiscuz->dbManager->unsubscribeByEmail($decodedEmail);
            ?>
            <script>
                jQuery(document).ready(function ($) {
                    wpdMessagesOnInit('<?php echo esc_html($wpdiscuz->options->phrases["wc_cancel_subs_success"]); ?>', 'success');
                });
            </script>
            <?php
        }
    } else if (isset($_GET["deleteFollows"]) && $_GET["deleteFollows"]) {
        $decodedEmail = get_transient(WpDiscuzConstants::TRS_USER_HASH . trim($_GET["deleteFollows"]));
        if (get_transient(WpDiscuzConstants::TRS_USER_HASH . md5($decodedEmail)) !== false) {
            $wpdiscuz->dbManager->unfollowByEmail($decodedEmail);
            ?>
            <script>
                jQuery(document).ready(function ($) {
                    wpdMessagesOnInit('<?php echo esc_html($wpdiscuz->options->phrases["wc_cancel_follows_success"]); ?>', 'success');
                });
            </script>
            <?php
        }
    } else if (isset($_GET["wpdiscuzFollowID"]) && isset($_GET["wpdiscuzFollowKey"]) && isset($_GET["wpDiscuzComfirm"])) {
        if ($_GET["wpDiscuzComfirm"]) {
            if ($wpdiscuz->dbManager->confirmFollow($_GET["wpdiscuzFollowID"], $_GET["wpdiscuzFollowKey"])) {
                ?>
                <script>
                    jQuery(document).ready(function ($) {
                        wpdMessagesOnInit('<?php echo esc_html($wpdiscuz->options->phrases["wc_follow_confirm_success"]); ?>', 'success');
                    });
                </script>
                <?php
            }
        } else {
            if ($wpdiscuz->dbManager->cancelFollow($_GET["wpdiscuzFollowID"], $_GET["wpdiscuzFollowKey"])) {
                ?>
                <script>
                    jQuery(document).ready(function ($) {
                        wpdMessagesOnInit('<?php echo esc_html($wpdiscuz->options->phrases["wc_follow_cancel_success"]); ?>', 'success');
                    });
                </script>
                <?php
            }
        }
    }

    if (isset($_GET["wpdiscuzSubscribeID"]) && isset($_GET["key"])) {
        $wpdiscuz->dbManager->unsubscribe($_GET["wpdiscuzSubscribeID"], $_GET["key"]);
        ?>
        <script>
            jQuery(document).ready(function ($) {
                wpdMessagesOnInit('<?php echo esc_html($wpdiscuz->options->phrases["wc_unsubscribe_message"]); ?>', 'success');
            });
        </script>
        <?php
    }

    if (isset($_GET["wpdiscuzConfirmID"]) && isset($_GET["wpdiscuzConfirmKey"]) && isset($_GET["wpDiscuzComfirm"])) {
        $wpdiscuz->dbManager->notificationConfirm($_GET["wpdiscuzConfirmID"], $_GET["wpdiscuzConfirmKey"]);
        ?>
        <script>
            jQuery(document).ready(function ($) {
                wpdMessagesOnInit('<?php echo esc_html($wpdiscuz->options->phrases["wc_comfirm_success_message"]); ?>', 'success');
            });
        </script>
        <?php
    }
    ?>
    <div class="wpdiscuz_top_clearing"></div>
    <?php
    $form = $wpdiscuz->wpdiscuzForm->getForm($post->ID);

    $wpCommClasses[] = "wpd-layout-" . $form->getLayout();
    $commentsOpen = comments_open($post);
    $wpCommClasses[] = $commentsOpen ? "wpd-comments-open" : "wpd-comments-closed";
    $wpCommClasses = apply_filters("wpdiscuz_container_classes", $wpCommClasses);
    
    $wpCommClasses = implode(" ", $wpCommClasses);

    $isShowSubscribeBar = $form->isShowSubscriptionBar();
    $isPostmaticActive = !class_exists("Prompt_Comment_Form_Handling") || (class_exists("Prompt_Comment_Form_Handling") && !$wpdiscuz->options->subscription["usePostmaticForCommentNotification"]);

    $currentUserId = 0;
    $currentUserEmail = isset($_COOKIE["comment_author_email_" . COOKIEHASH]) ? $_COOKIE["comment_author_email_" . COOKIEHASH] : "";
    if ($currentUser && $currentUser->ID) {
        $currentUserId = $currentUser->ID;
        $currentUserEmail = $currentUser->user_email;
    }

    $wpdiscuz->helper->superSocializerFix();
    if ($commentsOpen) {
        if ($formCustomCss = $form->getCustomCSS()) {
            echo "<style type='text/css'>" . $formCustomCss . "</style>";
        }
    } else {
        do_action("comment_form_closed");
        do_action("wpdiscuz_comment_form_closed", $post, $currentUser, $commentsCount);
    }
    do_action("wpdiscuz_comment_form_before");
    ?>
    <div id="wpdcom" class="<?php echo esc_attr($wpCommClasses); ?>">
        <?php
        if ($commentsOpen) {
            do_action("comment_form_before");
            ?>
            <div class="wc_social_plugin_wrapper">
                <?php
                if ($wc_ob_allowed) {
                    echo $wc_comment_form_top_content;
                } else {
                    do_action("comment_form_top");
                    do_action("wpdiscuz_comment_form_top", $post, $currentUser, $commentsCount);
                }
                ?>
            </div>
            <div class="wpd-form-wrap">
                <div class="wpd-form-head">
                    <?php
                    if ($isShowSubscribeBar && $isPostmaticActive && $commentsOpen) {
                        ?>
                        <div class="wpd-sbs-toggle">
                            <i class="far fa-envelope"></i> <span class="wpd-sbs-title"><?php echo esc_html($wpdiscuz->options->phrases["wc_subscribe_anchor"]); ?></span> <i class="fas fa-caret-down"></i>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="wpd-auth">
                        <?php do_action("comment_main_form_bar_top"); ?>
                        <div class="wpd-login">
                            <?php
                            if ($currentUser && $currentUser->ID) {
                                if ($wpdiscuz->options->login["showLoggedInUsername"]) {
                                    $user_url = get_author_posts_url($currentUser->ID);
                                    $user_url = $wpdiscuz->helper->getProfileUrl($user_url, $currentUser);
                                    $logout = wp_loginout(get_permalink(), false);
                                    $logout = preg_replace("!>([^<]+)!is", ">" . esc_html($wpdiscuz->options->phrases["wc_log_out"]), $logout);
                                    if ($user_url) {
                                        $logout_text = esc_html($wpdiscuz->options->phrases["wc_logged_in_as"]) . " <a href='" . esc_url_raw($user_url) . "'>" . esc_html($wpdiscuz->helper->getCurrentUserDisplayName($currentUser)) . "</a> | " . $logout;
                                    } else {
                                        $logout_text = esc_html($wpdiscuz->options->phrases["wc_logged_in_as"]) . " " . esc_html($wpdiscuz->helper->getCurrentUserDisplayName($currentUser)) . " | " . $logout;
                                    }
                                    echo apply_filters("wpdiscuz_user_info_and_logout_link", $logout_text);
                                }
                            } else if ($wpdiscuz->options->login["showLoginLinkForGuests"]) {
                                if ($wpdiscuz->options->login["loginUrl"]) {
                                    $login = "<a href='" . esc_url_raw($wpdiscuz->options->login["loginUrl"]) . "'><i class='fas fa-sign-in-alt'></i> " . esc_html($wpdiscuz->options->phrases["wc_log_in"]) . "</a>";
                                } else {
                                    $login = $wpdiscuz->options->login["loginUrl"] ? "<a href='" . esc_url_raw($wpdiscuz->options->login["loginUrl"]) . "'></a>" : wp_loginout(get_permalink(), false);
                                    $login = preg_replace("!>([^<]+)!is", "><i class='fas fa-sign-in-alt'></i> " . esc_html($wpdiscuz->options->phrases["wc_log_in"]), $login);
                                }
                                if ($wpdiscuz->options->isShowLoginButtons()) {
                                    echo "<div class='wpd-sep'></div>";
                                }
                                echo apply_filters("wpdiscuz_login_link", $login);
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php do_action("comment_main_form_after_head"); ?>
                <?php
                if ($isShowSubscribeBar && $isPostmaticActive) {
                    $wpdiscuz->subscriptionData = $wpdiscuz->dbManager->hasSubscription($post->ID, $currentUser->user_email);
                    $subscriptionType = null;
                    if ($wpdiscuz->subscriptionData) {
                        $isConfirmed = $wpdiscuz->subscriptionData["confirm"];
                        $subscriptionType = $wpdiscuz->subscriptionData["type"];
                        if ($subscriptionType == WpdiscuzCore::SUBSCRIPTION_POST || $subscriptionType == WpdiscuzCore::SUBSCRIPTION_ALL_COMMENT) {
                            $unsubscribeLinkParams = $wpdiscuz->dbManager->getUnsubscribeLinkParams($post->ID, $currentUser->user_email);
                        }
                    }
                    ?>
                    <div class="wpdiscuz-subscribe-bar wpdiscuz-hidden">
                        <?php
                        if ($subscriptionType != WpdiscuzCore::SUBSCRIPTION_POST) {
                            ?>
                            <form action="<?php echo esc_url_raw(admin_url("admin-ajax.php") . "?action=wpdAddSubscription"); ?>" method="post" id="wpdiscuz-subscribe-form">
                                <div class="wpdiscuz-subscribe-form-intro"><?php echo esc_html($wpdiscuz->options->phrases["wc_notify_of"]); ?> </div>
                                <div class="wpdiscuz-subscribe-form-option" style="width:<?php echo!$currentUser->ID ? "40%" : "65%"; ?>;">
                                    <select class="wpdiscuz_select" name="wpdiscuzSubscriptionType" >
                                        <?php
                                        if ($wpdiscuz->options->subscription["subscriptionType"] != 3) {
                                            ?>
                                            <option value="<?php echo esc_attr(WpdiscuzCore::SUBSCRIPTION_POST); ?>"><?php echo esc_html($wpdiscuz->options->phrases["wc_notify_on_new_comment"]); ?></option>
                                            <?php
                                        }
                                        ?>
                                        <?php
                                        if ($wpdiscuz->options->subscription["subscriptionType"] != 2) {
                                            ?>
                                            <option value="<?php echo esc_attr(WpdiscuzCore::SUBSCRIPTION_ALL_COMMENT); ?>" <?php echo isset($unsubscribeLinkParams) || !$wpdiscuz->options->wp["threadComments"] ? "disabled" : ""; ?>><?php echo esc_html($wpdiscuz->options->phrases["wc_notify_on_all_new_reply"]); ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <?php
                                if (!$currentUser->ID) {
                                    ?>
                                    <div class="wpdiscuz-item wpdiscuz-subscribe-form-email">
                                        <input  class="email" type="email" name="wpdiscuzSubscriptionEmail" required="required" value="" placeholder="<?php echo esc_attr($wpdiscuz->options->phrases["wc_email_text"]); ?>"/>
                                    </div>
                                    <?php
                                }
                                ?>
                                <div class="wpdiscuz-subscribe-form-button">
                                    <input id="wpdiscuz_subscription_button" class="wpd-prim-button" type="submit" value="<?php echo esc_attr($wpdiscuz->options->phrases["wc_form_subscription_submit"]); ?>" name="wpdiscuz_subscription_button" />
                                </div> 
                                <?php
                                if (!$currentUser->ID && $form->isShowSubscriptionBarAgreement()) {
                                    ?>
                                    <div class="wpdiscuz-subscribe-agreement">
                                        <input id="show_subscription_agreement" type="checkbox" required="required" name="show_subscription_agreement" value="1">
                                        <label for="show_subscription_agreement"><?php echo $form->subscriptionBarAgreementLabel(); ?></label>
                                    </div>
                                    <?php
                                }
                                wp_nonce_field("wpdiscuz_subscribe_form_nonce_action", "wpdiscuz_subscribe_form_nonce");
                                do_action("wpdiscuz_after_subscription_form");
                                $c = $form->getCaptchaFied();
                                $c->subscribtionRecaptchaHtml($wpdiscuz->options);
                                ?>
                            </form>
                            <?php
                        }
                        if (isset($unsubscribeLinkParams)) {
                            $subscribeMessage = $isConfirmed ? $wpdiscuz->options->phrases["wc_unsubscribe"] : $wpdiscuz->options->phrases["wc_ignore_subscription"];
                            if ($subscriptionType == "all_comment") {
                                $introText = $wpdiscuz->options->phrases["wc_subscribed_to"] . " " . $wpdiscuz->options->phrases["wc_notify_on_all_new_reply"];
                            } elseif ($subscriptionType == "post") {
                                $introText = $wpdiscuz->options->phrases["wc_subscribed_to"] . " " . $wpdiscuz->options->phrases["wc_notify_on_new_comment"];
                            }
                            echo "<div class='wpdiscuz_subscribe_status'>" . esc_html($introText) . " | <a class='wpd-unsubscribe' data-sid='" . esc_attr($unsubscribeLinkParams["id"]) . "' data-skey='" . esc_attr($unsubscribeLinkParams["activation_key"]) . "' href='#'>" . esc_html($subscribeMessage) . "</a></div>";
                        }
                        ?>
                    </div>
                    <?php
                }
                ob_start();
                $wpdiscuz->wpdiscuzForm->renderFrontForm($commentsCount, $currentUser);
                $output = ob_get_clean();
                echo apply_filters("wpdiscuz_form_render", $output, $commentsCount, $currentUser);
                if (empty($currentUser->ID) && !$form->isUserCanComment($currentUser, $post->ID)) {
                    ?>
                    <div class="wpd-login-to-comment"><?php echo esc_html($wpdiscuz->options->phrases["wc_login_to_comment"]); ?></div>
                    <?php
                }
                ?>
            </div>
            <?php
            do_action("comment_form_after");
            do_action("wpdiscuz_comment_form_after", $post, $currentUser, $commentsCount);
        } else {
            
        }
        do_action("wpdiscuz_before_comments", $post, $currentUser, $commentsCount);
        $wooExists = class_exists("WooCommerce") && get_post_type($post->ID) == "product";
        ?>                   
        <div id="wpd-threads" class="wpd-thread-wrapper">
            <div class="wpd-thread-head">
                <div class="wpd-thread-info <?php echo $wooExists ? "wpd-reviews-tab" : ""; ?>">
                    <?php
                    if (!$wooExists) {
                        echo "<span class='wpdtc'>" . esc_html($commentsCount) . "</span> " . esc_html(1 == $commentsCount ? $form->getHeaderTextSingle() : $form->getHeaderTextPlural());
                    } else {
                        echo "<svg id='Capa_1' enable-background='new 0 0 512 512' height='24' viewBox='0 0 512 512' width='24' xmlns='https://www.w3.org/2000/svg'><path d='m144.789 277.138-8.789-17.593-8.789 17.593c-2.183 4.365-6.357 7.397-11.177 8.13l-19.468 2.93 14.019 13.799c3.472 3.413 5.068 8.32 4.263 13.14l-3.223 19.409 17.461-9.067c2.168-1.128 4.541-1.685 6.914-1.685s4.746.557 6.914 1.685l17.461 9.067-3.223-19.409c-.806-4.819.791-9.727 4.263-13.14l14.019-13.799-19.468-2.93c-4.82-.733-8.994-3.765-11.177-8.13z'/><path d='m384.789 277.138-8.789-17.593-8.789 17.593c-2.183 4.365-6.357 7.397-11.177 8.13l-19.468 2.93 14.019 13.799c3.472 3.413 5.068 8.32 4.263 13.14l-3.223 19.409 17.461-9.067c2.168-1.128 4.541-1.685 6.914-1.685s4.746.557 6.914 1.685l17.461 9.067-3.223-19.409c-.806-4.819.791-9.727 4.263-13.14l14.019-13.799-19.468-2.93c-4.82-.733-8.994-3.765-11.177-8.13z'/><path d='m466 121h-125.387l3.864 23.291c2.827 16.904-4.043 33.691-17.944 43.784-14.046 10.247-32.347 11.21-47.139 3.56l-23.394-12.144-23.364 12.129c-14.788 7.63-33.115 6.727-47.227-3.589-13.843-10.049-20.713-26.836-17.886-43.77l3.864-23.261h-125.387c-24.814 0-46 20.186-46 45v240c0 24.814 21.186 45 46 45h164.18l33.105 53.954c2.739 4.38 7.544 7.046 12.715 7.046s9.976-2.666 12.715-7.046l33.105-53.954h164.18c24.814 0 46-20.186 46-45v-240c0-24.814-21.186-45-46-45zm-248.145 167.52-29.839 29.37 6.87 41.323c.938 5.61-1.377 11.25-5.977 14.59-4.492 3.281-10.576 3.851-15.732 1.187l-37.177-19.293-37.178 19.292c-5.054 2.607-11.177 2.153-15.732-1.187-4.6-3.34-6.914-8.979-5.977-14.59l6.87-41.323-29.839-29.37c-4.043-3.999-5.493-9.932-3.735-15.322 1.758-5.405 6.416-9.36 12.026-10.21l41.426-6.226 18.721-37.471c5.068-10.166 21.768-10.166 26.836 0l18.721 37.471 41.426 6.226c5.61.85 10.269 4.805 12.026 10.21 1.758 5.391.307 11.324-3.736 15.323zm240 0-29.839 29.37 6.87 41.323c.938 5.61-1.377 11.25-5.977 14.59-4.492 3.281-10.576 3.851-15.732 1.187l-37.177-19.293-37.178 19.292c-5.054 2.607-11.177 2.153-15.732-1.187-4.6-3.34-6.914-8.979-5.977-14.59l6.87-41.323-29.839-29.37c-4.043-3.999-5.493-9.932-3.735-15.322 1.758-5.405 6.416-9.36 12.026-10.21l41.426-6.226 18.721-37.471c5.068-10.166 21.768-10.166 26.836 0l18.721 37.471 41.426 6.226c5.61.85 10.269 4.805 12.026 10.21 1.758 5.391.307 11.324-3.736 15.323z'/><path d='m341.576 63.183c-1.758-5.391-6.416-9.346-12.026-10.195l-41.411-6.226-18.721-39.137c-5.098-10.166-21.738-10.166-26.836 0l-18.721 39.137-41.411 6.226c-5.61.85-10.269 4.805-12.026 10.195-1.758 5.405-.308 11.338 3.735 15.322l29.824 29.385-6.87 41.323c-.938 5.61 1.377 11.25 5.977 14.59 4.556 3.325 10.679 3.794 15.732 1.187l37.178-19.293 37.178 19.292c5.156 2.664 11.241 2.095 15.732-1.187 4.6-3.34 6.914-8.979 5.977-14.59l-6.87-41.323 29.824-29.385c4.043-3.983 5.493-9.916 3.735-15.321z'/></svg>";
                    }
                    ?>
                </div>
                <?php
                if (($wpdiscuz->options->login["showActivityTab"] || $wpdiscuz->options->login["showSubscriptionsTab"] || $wpdiscuz->options->login["showFollowsTab"] || apply_filters("wpdiscuz_enable_content_modal", false)) && $currentUserEmail) {
                    ?>
                    <div class="wpdiscuz-user-settings wpd-info wpd-not-clicked" wpd-tooltip="<?php echo esc_attr($wpdiscuz->options->phrases["wc_content_and_settings"]); ?>"  wpd-tooltip-position="right">
                        <i class="fas fa-user-cog"></i>
                    </div>
                    <?php
                }
                ?>
                <div class="wpd-space"></div>
                <div class="wpd-thread-filter">
                    <?php
                    do_action("wpdiscuz_filtering_buttons", $currentUser, $wpdiscuz->options);
                    if (!$wpdiscuz->options->wp["isPaginate"] && $wpdiscuz->options->inline["showInlineFilterButton"] && $wpdiscuz->dbManager->postHasFeedbackForms($post->ID)) {
                        ?>
                        <div class="wpd-filter wpdf-inline wpd_not_clicked" data-filter-type="inline" wpd-tooltip="<?php echo esc_attr($wpdiscuz->options->phrases["wc_inline_comments"]); ?>"><i class="fas fa-quote-left"></i></div>
                        <?php
                    }
                    if ($wpdiscuz->options->thread_display["showReactedFilterButton"]) {
                        ?>
                        <div class="wpd-filter wpdf-reacted wpd_not_clicked" wpd-tooltip="<?php echo esc_attr($wpdiscuz->options->phrases["wc_most_reacted_comment"]); ?>"><i class="fas fa-bolt"></i></div>
                        <?php
                    }
                    if ($wpdiscuz->options->thread_display["showHottestFilterButton"]) {
                        ?>
                        <div class="wpd-filter wpdf-hottest wpd_not_clicked" wpd-tooltip="<?php echo esc_attr($wpdiscuz->options->phrases["wc_hottest_comment_thread"]); ?>"><i class="fas fa-fire"></i></div>
                        <?php
                    }
                    $wpdiscuzCommentsOrder = $wpdiscuz->options->wp["commentOrder"];
                    if (!$wpdiscuz->options->wp["isPaginate"] && $wpdiscuz->options->thread_display["showSortingButtons"] && $wpdiscuz->options->thread_display["mostVotedByDefault"]) {
                        $wpdiscuzCommentsOrderBy = "by_vote";
                    } else {
                        $wpdiscuzCommentsOrderBy = $wpdiscuz->options->thread_display["orderCommentsBy"];
                    }
                    $wpdiscuzCommentsOrderBy = apply_filters("wpdiscuz_comments_order_by", $wpdiscuzCommentsOrderBy);
                    $wpdiscuzCommentsOrder = apply_filters("wpdiscuz_comments_order", $wpdiscuzCommentsOrder);
                    if ($commentsCount && $wpdiscuz->options->thread_display["showSortingButtons"] && !$wpdiscuz->options->wp["isPaginate"]) {
                        $sortingButtons = [
                            [
                                "orderBy" => $wpdiscuz->options->thread_display["orderCommentsBy"],
                                "order" => "desc",
                                "class" => "wpdiscuz-date-sort-desc",
                                "text" => $wpdiscuz->options->phrases["wc_newest"],
                                "type" => "newest",
                            ],
                            [
                                "orderBy" => $wpdiscuz->options->thread_display["orderCommentsBy"],
                                "order" => "asc",
                                "class" => "wpdiscuz-date-sort-asc",
                                "text" => $wpdiscuz->options->phrases["wc_oldest"],
                                "type" => "oldest",
                            ],
                        ];
                        if ($wpdiscuz->options->thread_layouts["showVotingButtons"]) {
                            $sortingButtons[] = [
                                "orderBy" => "by_vote",
                                "order" => $wpdiscuz->options->wp["commentOrder"],
                                "class" => "wpdiscuz-vote-sort-up",
                                "text" => $wpdiscuz->options->phrases["wc_most_voted"],
                                "type" => "by_vote",
                            ];
                        }
                        $sortingButtons = apply_filters("wpdiscuz_sorting_buttons_array", $sortingButtons);
                        ?> 
                        <div class="wpd-filter wpdf-sorting">
                            <?php
                            foreach ($sortingButtons as $key => $value) {
                                if ($wpdiscuzCommentsOrderBy === $value["orderBy"] && $wpdiscuzCommentsOrder === $value["order"]) {
                                    ?>
                                    <span class="wpdiscuz-sort-button <?php echo esc_attr($value["class"]); ?> wpdiscuz-sort-button-active" data-sorting="<?php echo esc_attr($value["type"]); ?>"><?php echo esc_html($value["text"]); ?></span>
                                    <?php
                                    unset($sortingButtons[$key]);
                                    break;
                                }
                            }
                            ?>
                            <i class="fas fa-sort-down"></i>
                            <div class="wpdiscuz-sort-buttons">
                                <?php
                                foreach ($sortingButtons as $key => $value) {
                                    ?>
                                    <span class="wpdiscuz-sort-button <?php echo esc_attr($value["class"]); ?>" data-sorting="<?php echo esc_attr($value["type"]); ?>"><?php echo esc_html($value["text"]); ?></span>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <div class="wpd-comment-info-bar">
                <div class="wpd-current-view"><i class="fas fa-quote-left"></i> <?php echo esc_html($wpdiscuz->options->phrases["wc_inline_feedbacks"]); ?></div>
                <div class="wpd-filter-view-all"><?php echo esc_html($wpdiscuz->options->phrases["wc_inline_comments_view_all"]); ?></div>
            </div>
            <?php do_action("wpdiscuz_before_thread_list", $post, $currentUser, $commentsCount); ?>
            <div class="wpd-thread-list">
                <?php
                if ($wpdiscuz->options->wp["isPaginate"] || !$wpdiscuz->options->thread_display["firstLoadWithAjax"]) {
                    $args = ["first_load" => 1, "orderby" => $wpdiscuzCommentsOrderBy, "order" => $wpdiscuzCommentsOrder];
                    $commentData = $wpdiscuz->getWPComments($args);
                    echo $commentData["comment_list"];
                } else if ($wpdiscuz->options->thread_display["firstLoadWithAjax"] == 2 && $commentsCount) {
                    ?>
                    <div class="wpd-load-more-submit-wrap">
                        <button name="submit" class="wpd-load-comments wpd-prim-button">
                            <?php echo esc_html($wpdiscuz->options->phrases["wc_view_comments"]); ?>
                        </button>
                    </div>
                    <?php
                }
                ?>                
                <div class="wpdiscuz-comment-pagination"<?php echo!$wpdiscuz->options->wp["isPaginate"] && $wpdiscuz->options->thread_display["firstLoadWithAjax"] ? " style='display:none;'" : ""; ?>>
                    <?php
                    if ($wpdiscuz->options->wp["isPaginate"]) {
                        paginate_comments_links();
                    } else if ($wpdiscuz->options->thread_display["firstLoadWithAjax"]) {
                        $loadMoreButtonText = $wpdiscuz->options->thread_display["commentListLoadType"] == 1 ? $wpdiscuz->options->phrases["wc_load_rest_comments_submit_text"] : $wpdiscuz->options->phrases["wc_load_more_submit_text"];
                        ?>
                        <div class="wpd-load-more-submit-wrap">
                            <button name="submit" data-lastparentid="0" class="wpd-load-more-submit wpd-loaded wpd-prim-button">
                                <?php echo esc_html($loadMoreButtonText); ?>
                            </button>
                        </div>
                        <input id="wpdiscuzHasMoreComments" type="hidden" value="0" />
                        <?php
                    } else if ($commentData["is_show_load_more"]) {
                        $loadMoreButtonText = $wpdiscuz->options->thread_display["commentListLoadType"] == 1 ? $wpdiscuz->options->phrases["wc_load_rest_comments_submit_text"] : $wpdiscuz->options->phrases["wc_load_more_submit_text"];
                        ?>
                        <div class="wpd-load-more-submit-wrap">
                            <button name="submit" data-lastparentid="<?php echo esc_attr($commentData["last_parent_id"]); ?>" class="wpd-load-more-submit wpd-loaded wpd-prim-button">
                                <?php echo esc_html($loadMoreButtonText); ?>
                            </button>
                        </div>
                        <input id="wpdiscuzHasMoreComments" type="hidden" value="<?php echo esc_attr($commentData["is_show_load_more"]); ?>" />
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
        do_action("wpdiscuz_after_comments", $post, $currentUser, $commentsCount);
        if ($commentsCount) {
            if ($wpdiscuz->options->general["showPluginPoweredByLink"]) {
                ?>
                <div class="by-wpdiscuz">
                    <span id="awpdiscuz" onclick='javascript:document.getElementById("bywpdiscuz").style.display = "inline";
                            document.getElementById("awpdiscuz").style.display = "none";'>
                        <img alt="wpdiscuz" src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/plugin-icon/icon_info.png")); ?>"  align="absmiddle" class="wpdimg"/>
                    </span>&nbsp;
                    <a href="https://wpdiscuz.com/" target="_blank" rel='noreferrer' id="bywpdiscuz" title="wpDiscuz v<?php echo esc_attr(get_option(WpdiscuzCore::OPTION_SLUG_VERSION)); ?> - Supercharged native comments">wpDiscuz</a>
                </div>
                <?php
            }
        }
        ?>
    </div>
    <?php
    $loadEndTime = WpdiscuzHelper::getMicrotime();
    if (isset($_GET["wpdLoadTime"])) {
        ?>
        <div><?php echo esc_html($loadEndTime - $loadStartTime); ?></div>
        <?php
    }
    ?>
    </div>
    <div id="wpdiscuz-loading-bar" class="<?php echo esc_html(!empty($currentUser->ID) ? "wpdiscuz-loading-bar-auth" : "wpdiscuz-loading-bar-unauth"); ?>"></div>
    <div id="wpdiscuz-comment-message" class="<?php echo esc_html(!empty($currentUser->ID) ? "wpdiscuz-comment-message-auth" : "wpdiscuz-comment-message-unauth"); ?>"></div>
    <?php
}        