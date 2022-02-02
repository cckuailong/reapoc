<?php
if (!defined("ABSPATH")) {
    exit();
}

class WpdiscuzHelperAjax implements WpDiscuzConstants {

    private $options;
    private $dbManager;
    private $helper;
    private $helperEmail;
    private $wpdiscuzForm;

    public function __construct($options, $dbManager, $helper, $helperEmail, $wpdiscuzForm) {
        $this->options = $options;
        $this->dbManager = $dbManager;
        $this->helper = $helper;
        $this->helperEmail = $helperEmail;
        $this->wpdiscuzForm = $wpdiscuzForm;
        add_action("wp_ajax_wpdStickComment", [&$this, "stickComment"]);
        add_action("wp_ajax_wpdCloseThread", [&$this, "closeThread"]);
        add_action("wp_ajax_wpdDeactivate", [&$this, "deactivate"]);
        add_action("wp_ajax_wpdImportSTCR", [&$this, "importSTCR"]);
        add_action("wp_ajax_wpdImportLSTC", [&$this, "importLSTC"]);
        
        add_action("wp_ajax_wpdFollowUser", [&$this, "followUser"]);
        add_action("wp_ajax_wpdRegenerateVoteMetas", [&$this, "regenerateVoteMetas"]);
        add_action("wp_ajax_wpdRegenerateClosedComments", [&$this, "regenerateClosedComments"]);
        add_action("wp_ajax_wpdRegenerateVoteData", [&$this, "regenerateVoteData"]);
        add_action("wp_ajax_wpdSyncCommenterData", [&$this, "syncCommenterData"]);
        add_action("wp_ajax_wpdRebuildRatings", [&$this, "rebuildRatings"]);
        add_action("wp_ajax_wpdFixTables", [&$this, "fixTables"]);
        if ($this->options->login["showActivityTab"] || $this->options->login["showSubscriptionsTab"] || $this->options->login["showFollowsTab"]) {
            add_action("wp_ajax_wpdDeleteComment", [&$this, "deleteComment"]);
            add_action("wp_ajax_nopriv_wpdDeleteComment", [&$this, "deleteComment"]);
            add_action("wp_ajax_wpdCancelSubscription", [&$this, "deleteSubscription"]);
            add_action("wp_ajax_nopriv_wpdCancelSubscription", [&$this, "deleteSubscription"]);
            add_action("wp_ajax_wpdCancelFollow", [&$this, "deleteFollow"]);
            add_action("wp_ajax_nopriv_wpdCancelFollow", [&$this, "deleteFollow"]);
            add_action("wp_ajax_wpdEmailDeleteLinks", [&$this, "emailDeleteLinks"]);
            add_action("wp_ajax_nopriv_wpdGuestAction", [&$this, "guestAction"]);
        }
        if ($this->options->content["commentReadMoreLimit"]) {
            add_action("wp_ajax_wpdReadMore", [&$this, "readMore"]);
            add_action("wp_ajax_nopriv_wpdReadMore", [&$this, "readMore"]);
        }
        add_action("wp_ajax_wpdRedirect", [&$this, "redirect"]);
        add_action("wp_ajax_nopriv_wpdRedirect", [&$this, "redirect"]);
        if ($this->options->thread_layouts["showVotingButtons"]) {
            add_action("wp_ajax_wpdVoteOnComment", [&$this, "voteOnComment"]);
            add_action("wp_ajax_nopriv_wpdVoteOnComment", [&$this, "voteOnComment"]);
        }
        add_action("wp_ajax_wpdGetInlineCommentForm", [&$this, "getInlineCommentForm"]);
        add_action("wp_ajax_nopriv_wpdGetInlineCommentForm", [&$this, "getInlineCommentForm"]);
        add_action("wp_ajax_wpdGetLastInlineComments", [&$this, "getLastInlineComments"]);
        add_action("wp_ajax_nopriv_wpdGetLastInlineComments", [&$this, "getLastInlineComments"]);
        add_action("wp_ajax_wpdEditComment", [&$this, "editComment"]);
        add_action("wp_ajax_nopriv_wpdEditComment", [&$this, "editComment"]);
        add_action("wp_ajax_wpdUserRate", [&$this, "userRate"]);
        add_action("wp_ajax_nopriv_wpdUserRate", [&$this, "userRate"]);
        add_action("wp_ajax_wpdUnsubscribe", [&$this, "unsubscribe"]);
        add_action("wp_ajax_nopriv_wpdUnsubscribe", [&$this, "unsubscribe"]);
        add_action("wp_ajax_wpd_stat_brief", [&$this, "wpd_stat_brief"]);
        add_action("wp_ajax_wpd_stat_subs", [&$this, "wpd_stat_subs"]);
        add_action("wp_ajax_wpd_stat_graph", [&$this, "wpd_stat_graph"]);
        add_action("wp_ajax_wpd_stat_user", [&$this, "wpd_stat_user"]);
        add_action("wp_ajax_searchOption", [&$this, "searchOption"]);
        add_action("wp_ajax_wpdResetPostRating", [&$this, "resetPostRating"]);
        add_action("wp_ajax_wpdResetFieldsRatings", [&$this, "resetFieldsRatings"]);
    }

    public function stickComment() {
        $postId = isset($_POST["postId"]) ? intval($_POST["postId"]) : 0;
        $commentId = isset($_POST["commentId"]) ? intval($_POST["commentId"]) : 0;
        if ($postId && $commentId) {
            $comment = get_comment($commentId);
            $userCanStickComment = current_user_can("moderate_comments");
            if (!$userCanStickComment) {
                $post = get_post($postId);
                $currentUser = WpdiscuzHelper::getCurrentUser();
                $userCanStickComment = $post && isset($post->post_author) && $currentUser && isset($currentUser->ID) && $post->post_author == $currentUser->ID;
            }
            if ($userCanStickComment && $comment && isset($comment->comment_ID) && $comment->comment_ID && !$comment->comment_parent) {
                $commentarr = ["comment_ID" => $commentId];
                if ($comment->comment_type == self::WPDISCUZ_STICKY_COMMENT) {
                    $commentarr["comment_type"] = "";
                    $response = esc_html($this->options->phrases["wc_stick_comment"]);
                } else {
                    $commentarr["comment_type"] = self::WPDISCUZ_STICKY_COMMENT;
                    $response = esc_html($this->options->phrases["wc_unstick_comment"]);
                }
                $commentarr["wpdiscuz_comment_update"] = true;
                if (wp_update_comment(wp_slash($commentarr))) {
                    wp_send_json_success($response);
                }
            }
        }
    }

    public function closeThread() {
        $postId = isset($_POST["postId"]) ? intval($_POST["postId"]) : 0;
        $commentId = isset($_POST["commentId"]) ? intval($_POST["commentId"]) : 0;
        if ($postId && $commentId) {
            $comment = get_comment($commentId);
            $userCanCloseComment = current_user_can("moderate_comments");
            if (!$userCanCloseComment) {
                $post = get_post($postId);
                $currentUser = WpdiscuzHelper::getCurrentUser();
                $userCanCloseComment = !empty($post->post_author) && !empty($currentUser->ID) && $post->post_author == $currentUser->ID;
            }
            if ($userCanCloseComment && !empty($comment->comment_ID) && !$comment->comment_parent) {
                $children = $comment->get_children([
                    "format" => "flat",
                    "status" => "all",
                    "post_id" => $postId,
                ]);
                $response = [];
                $isClosed = intval(get_comment_meta($comment->comment_ID, self::META_KEY_CLOSED, true));
                if ($isClosed) {
                    $response["data"] = esc_html($this->options->phrases["wc_close_comment"]);
                    $response["icon"] = esc_attr("fa-unlock");
                } else {
                    $response["data"] = esc_html($this->options->phrases["wc_open_comment"]);
                    $response["icon"] = esc_attr("fa-lock");
                }
                update_comment_meta($comment->comment_ID, self::META_KEY_CLOSED, intval(!$isClosed));
                if ($children && is_array($children)) {
                    foreach ($children as $k => $child) {
                        update_comment_meta($child->comment_ID, self::META_KEY_CLOSED, intval(!$isClosed));
                    }
                }
                wp_send_json_success($response);
            }
        }
    }

    public function deactivate() {
        $response = ["code" => 0];
        $json = filter_input(INPUT_POST, "deactivateData");
        if ($json) {
            parse_str($json, $data);
            if (isset($data["never_show"]) && ($v = intval($data["never_show"]))) {
                update_option(self::OPTION_SLUG_DEACTIVATION, $v);
                $response["code"] = "dismiss_and_deactivate";
            } else if (isset($data["deactivation_reason"]) && ($reason = trim($data["deactivation_reason"]))) {
                $pluginData = get_plugin_data(WPDISCUZ_DIR_PATH . "/class.WpdiscuzCore.php");
                $blogTitle = get_option("blogname");
                $to = "feedback@wpdiscuz.com";
                $subject = "[wpDiscuz Feedback - " . $pluginData["Version"] . "] - " . $reason;
                $headers = [];
                $contentType = "text/html";
                $fromName = html_entity_decode($blogTitle, ENT_QUOTES);
                $siteUrl = get_site_url();
                $parsedUrl = parse_url($siteUrl);
                $domain = isset($parsedUrl["host"]) ? WpdiscuzHelper::fixEmailFrom($parsedUrl["host"]) : "";
                $fromEmail = "no-reply@" . $domain;
                $headers[] = "Content-Type:  $contentType; charset=UTF-8";
                $headers[] = "From: " . $fromName . " <" . $fromEmail . "> \r\n";
                $message = "<strong>Deactivation subject:</strong> " . $reason . "\r\n" . "<br/>";
                if (isset($data["deactivation_reason_desc"]) && ($reasonDesc = trim($data["deactivation_reason_desc"]))) {
                    $message .= "<strong>Deactivation reason:</strong> " . $reasonDesc . "\r\n" . "<br/>";
                }
                if (isset($data["deactivation_feedback_email"]) && ($feedback_email = trim($data["deactivation_feedback_email"]))) {
                    if (filter_var($feedback_email, FILTER_VALIDATE_EMAIL) === false) {
                        $response["code"] = "send_and_deactivate";
                        wp_die(json_encode($response));
                    }
                    $to = "support@wpdiscuz.com";
                    $message .= "<strong>Feedback Email:</strong> " . $feedback_email . "\r\n" . "<br/>";
                }
                $subject = html_entity_decode($subject, ENT_QUOTES);
                $message = html_entity_decode($message, ENT_QUOTES);
                $sent = wp_mail($to, $subject, do_shortcode($message), $headers);
                $response["code"] = "send_and_deactivate";
            }
        }
        wp_die(json_encode($response));
    }

    /**
     * Import subscriptions from "Subscribe To Comments Reloaded" plugin
     */
    public function importSTCR() {
        $response = ["progress" => 0];
        $stcrData = isset($_POST["stcrData"]) ? $_POST["stcrData"] : "";
        if ($stcrData) {
            parse_str($stcrData, $data);
            $limit = 50;
            $step = isset($data["stcr-step"]) ? intval($data["stcr-step"]) : 0;
            $stcrSubscriptionsCount = isset($data["stcr-subscriptions-count"]) ? intval($data["stcr-subscriptions-count"]) : 0;
            $nonce = isset($data["wpd-stcr-subscriptions"]) ? trim($data["wpd-stcr-subscriptions"]) : "";
            if (wp_verify_nonce($nonce, "wc_tools_form") && $stcrSubscriptionsCount) {
                $offset = $limit * $step;
                if ($limit && $offset >= 0) {
                    $subscriptions = $this->dbManager->getStcrSubscriptions($limit, $offset);
                    if ($subscriptions) {
                        $this->dbManager->addStcrSubscriptions($subscriptions);
                        ++$step;
                        $response["step"] = $step;
                        $progress = $offset ? $offset * 100 / $stcrSubscriptionsCount : $limit * 100 / $stcrSubscriptionsCount;
                        $response["progress"] = ($prg = intval($progress)) > 100 ? 100 : $prg;
                    } else {
                        $response["progress"] = 100;
                    }
                }
            }
        }
        wp_die(json_encode($response));
    }

    /**
     * Import subscriptions from "Lightweight Subscribe To Comments" plugin
     */
    public function importLSTC() {
        $response = ["progress" => 0];
        $lstcData = isset($_POST["lstcData"]) ? $_POST["lstcData"] : "";
        if ($lstcData) {
            parse_str($lstcData, $data);
            $limit = 50;
            $step = isset($data["lstc-step"]) ? intval($data["lstc-step"]) : 0;
            $lstcSubscriptionsCount = isset($data["lstc-subscriptions-count"]) ? intval($data["lstc-subscriptions-count"]) : 0;
            $nonce = isset($data["wpd-lstc-subscriptions"]) ? trim($data["wpd-lstc-subscriptions"]) : "";
            if (wp_verify_nonce($nonce, "wc_tools_form") && $lstcSubscriptionsCount) {
                $offset = $limit * $step;
                if ($limit && $offset >= 0) {
                    $subscriptions = $this->dbManager->getLstcSubscriptions($limit, $offset);
                    if ($subscriptions) {
                        $this->dbManager->addLstcSubscriptions($subscriptions);
                        ++$step;
                        $response["step"] = $step;
                        $progress = $offset ? $offset * 100 / $lstcSubscriptionsCount : $limit * 100 / $lstcSubscriptionsCount;
                        $response["progress"] = ($prg = intval($progress)) > 100 ? 100 : $prg;
                    } else {
                        $response["progress"] = 100;
                    }
                }
            }
        }
        wp_die(json_encode($response));
    }

    public function deleteComment() {
        $commentId = isset($_POST["id"]) ? intval($_POST["id"]) : 0;
        wp_delete_comment($commentId, true);
        $this->helper->getActivityPage();
    }

    public function deleteSubscription() {
        $subscriptionId = isset($_POST["id"]) ? intval($_POST["id"]) : 0;
        $this->dbManager->unsubscribeById($subscriptionId);
        $this->helper->getSubscriptionsPage();
    }

    public function deleteFollow() {
        $followId = isset($_POST["id"]) ? intval($_POST["id"]) : 0;
        $this->dbManager->unfollowById($followId);
        $this->helper->getFollowsPage();
    }

    public function emailDeleteLinks() {
        global $wp_rewrite;
        $postId = isset($_POST["postId"]) ? intval($_POST["postId"]) : 0;
        $post = get_post($postId);
        $currentUser = WpdiscuzHelper::getCurrentUser();
        if ($post && $currentUser->exists()) {
            $currentUserEmail = $currentUser->user_email;

            if ($currentUserEmail) {
                $siteUrl = get_site_url();
                $blogTitle = html_entity_decode(get_option("blogname"), ENT_QUOTES);
                $hashValue = $this->generateUserActionHash($currentUserEmail);
                $mainUrl = !$wp_rewrite->using_permalinks() ? get_permalink($post) . "&" : get_permalink($post) . "?";
                $deleteCommentsUrl = $mainUrl . "wpdiscuzUrlAnchor&deleteComments=$hashValue";
                $unsubscribeUrl = $mainUrl . "wpdiscuzUrlAnchor&deleteSubscriptions=$hashValue";
                $unfollowUrl = $mainUrl . "wpdiscuzUrlAnchor&deleteFollows=$hashValue";

                $subject = $this->options->phrases["wc_user_settings_delete_links"];

                $message = str_replace(["[SITE_URL]", "[BLOG_TITLE]", "[DELETE_COMMENTS_URL]"], [$siteUrl, $blogTitle, $deleteCommentsUrl], $this->options->phrases["wc_user_settings_delete_all_comments_message"]);

                $message .= $this->options->phrases["wc_user_settings_delete_all_subscriptions_message"];

                if (strpos($message, "[DELETE_SUBSCRIPTIONS_URL]") !== false) {
                    $message = str_replace("[DELETE_SUBSCRIPTIONS_URL]", $unsubscribeUrl, $message);
                }

                $message .= $this->options->phrases["wc_user_settings_delete_all_follows_message"];

                if (strpos($message, "[DELETE_FOLLOWS_URL]") !== false) {
                    $message = str_replace("[DELETE_FOLLOWS_URL]", $unfollowUrl, $message);
                }

                $this->userActionMail($currentUserEmail, $subject, $message);
            }
        }
        wp_die();
    }

    public function guestAction() {
        global $wp_rewrite;
        $guestEmail = isset($_COOKIE["comment_author_email_" . COOKIEHASH]) ? $_COOKIE["comment_author_email_" . COOKIEHASH] : "";
        $guestAction = filter_input(INPUT_POST, "guestAction", FILTER_SANITIZE_STRING);
        $postId = filter_input(INPUT_POST, "postId", FILTER_SANITIZE_NUMBER_INT);
        $post = get_post($postId);
        $response = [
            "code" => 0,
            "message" => "<div class='wpd-guest-action-message wpd-guest-action-error'>" . esc_html($this->options->phrases["wc_user_settings_email_error"]) . "</div>"
        ];
        if ($post && $guestEmail) {
            $hashValue = $this->generateUserActionHash($guestEmail);
            $mainUrl = !$wp_rewrite->using_permalinks() ? get_permalink($post) . "&" : get_permalink($post) . "?";
            $link = "";
            $message = "";
            $siteUrl = get_site_url();
            $blogTitle = html_entity_decode(get_option("blogname"), ENT_QUOTES);
            if ($guestAction == "deleteComments") {
                $link = $mainUrl . "wpdiscuzUrlAnchor&deleteComments=$hashValue";
                $subject = $this->options->phrases["wc_user_settings_delete_all_comments"];
                $message = $this->options->phrases["wc_user_settings_delete_all_comments_message"];
                if (strpos($message, "[DELETE_COMMENTS_URL]") !== false) {
                    $message = str_replace("[DELETE_COMMENTS_URL]", $link, $message);
                }
            } elseif ($guestAction == "deleteSubscriptions") {
                $subject = $this->options->phrases["wc_user_settings_delete_all_subscriptions"];
                $link = $mainUrl . "wpdiscuzUrlAnchor&deleteSubscriptions=$hashValue";
                $message = $this->options->phrases["wc_user_settings_delete_all_subscriptions_message"];
                if (strpos($message, "[DELETE_SUBSCRIPTIONS_URL]") !== false) {
                    $message = str_replace("[DELETE_SUBSCRIPTIONS_URL]", $link, $message);
                }
            }

            $subject = str_replace(["[SITE_URL]", "[BLOG_TITLE]"], [$siteUrl, $blogTitle], $subject);
            $message = str_replace(["[SITE_URL]", "[BLOG_TITLE]"], [$siteUrl, $blogTitle], $message);

            if ($this->userActionMail($guestEmail, $subject, $message)) {
                $response["code"] = 1;
                $parts = explode("@", $guestEmail);
                $guestEmail = substr($parts[0], 0, min(1, strlen($parts[0]) - 1)) . str_repeat("*", max(1, strlen($parts[0]) - 1)) . "@" . $parts[1];
                $response["message"] = "<div class='wpd-guest-action-message wpd-guest-action-success'>" . esc_html($this->options->phrases["wc_user_settings_check_email"]) . " ($guestEmail)" . "</div>";
            }
        }
        wp_die(json_encode($response));
    }

    private function generateUserActionHash($email) {
        $hashedEmail = hash_hmac("sha256", $email, get_option(self::OPTION_SLUG_HASH_KEY));
        $hashKey = self::TRS_USER_HASH . $hashedEmail;
        $hashExpire = apply_filters("wpdiscuz_delete_all_content", 3 * DAY_IN_SECONDS);
        set_transient($hashKey, $email, $hashExpire);
        return $hashedEmail;
    }

    private function userActionMail($email, $subject, $message) {
        $siteUrl = get_site_url();
        $blogTitle = get_option("blogname");
        $fromName = html_entity_decode($blogTitle, ENT_QUOTES);
        $parsedUrl = parse_url($siteUrl);
        $domain = isset($parsedUrl["host"]) ? WpdiscuzHelper::fixEmailFrom($parsedUrl["host"]) : "";
        $fromEmail = "no-reply@" . $domain;
        $headers[] = "Content-Type: text/html; charset=UTF-8";
        $headers[] = "From: " . $fromName . " <" . $fromEmail . "> \r\n";
        $subject = html_entity_decode($subject, ENT_QUOTES);
        $message = html_entity_decode($message, ENT_QUOTES);
        return wp_mail($email, $subject, do_shortcode($message), $headers);
    }

    public function followUser() {
        $postId = isset($_POST["postId"]) ? intval($_POST["postId"]) : 0;
        $commentId = isset($_POST["commentId"]) ? intval($_POST["commentId"]) : 0;
        if ($postId && $commentId) {
            $comment = get_comment($commentId);
            if ($comment && $comment->comment_author_email) {
                $currentUser = WpdiscuzHelper::getCurrentUser();
                if ($currentUser && $currentUser->ID) {
                    $args = [
                        "post_id" => $comment->comment_post_ID,
                        "user_id" => $comment->user_id,
                        "user_email" => $comment->comment_author_email,
                        "user_name" => $comment->comment_author,
                        "follower_id" => $currentUser->ID,
                        "follower_email" => $currentUser->user_email,
                        "follower_name" => $currentUser->display_name,
                        "confirm" => $this->options->subscription["disableFollowConfirmForUsers"],
                    ];
                    $followExists = $this->dbManager->isFollowExists($comment->comment_author_email, $currentUser->user_email);
                    if ($followExists) {
                        $response = [];
                        if (intval($followExists["confirm"])) { // confirmed follow already exists
                            $response["code"] = "wc_follow_canceled";
                            $this->dbManager->cancelFollow($followExists["id"], $followExists["activation_key"]);
                            $response["followTip"] = esc_attr($this->options->phrases["wc_follow_user"]);
                        } else { // follow exists but not confirmed yet, send confirm email again if neccessary
                            if ($this->options->subscription["disableFollowConfirmForUsers"]) {
                                $this->dbManager->confirmFollow($followExists["id"], $followExists["activation_key"]);
                                $response["code"] = "wc_follow_success";
                                $response["followClass"] = "wpd-follow-active";
                                $response["followTip"] = esc_attr($this->options->phrases["wc_unfollow_user"]);
                            } else {
                                $this->followConfirmAction($comment->comment_post_ID, $followExists["id"], $followExists["activation_key"], $args["follower_email"]);
                            }
                        }
                        wp_send_json_success($response);
                    } else {
                        $followData = $this->dbManager->addNewFollow($args);
                        if ($followData) {
                            if ($this->options->subscription["disableFollowConfirmForUsers"]) {
                                $response = [];
                                $response["code"] = "wc_follow_success";
                                $response["followClass"] = "wpd-follow-active";
                                $response["followTip"] = esc_attr($this->options->phrases["wc_unfollow_user"]);
                                wp_send_json_success($response);
                            } else {
                                $this->followConfirmAction($comment->comment_post_ID, $followData["id"], $followData["activation_key"], $args["follower_email"]);
                            }
                        } else {
                            wp_send_json_error("wc_follow_not_added");
                        }
                    }
                } else {
                    wp_send_json_error("wc_follow_login_to_follow");
                }
            } else {
                wp_send_json_error("wc_follow_impossible");
            }
        }
    }

    private function followConfirmAction($postId, $id, $key, $email) {
        $send = $this->helperEmail->followConfirmEmail($postId, $id, $key, $email);
        if ($send) {
            wp_send_json_success(["code" => "wc_follow_email_confirm"]);
        } else {
            $this->dbManager->cancelFollow($id, $key);
            wp_send_json_error("wc_follow_email_confirm_fail");
        }
    }

    public function regenerateVoteMetas() {
        $response = ["progress" => 0];
        $voteRegenerateData = isset($_POST["voteRegenerateData"]) ? $_POST["voteRegenerateData"] : "";
        if ($voteRegenerateData) {
            parse_str($voteRegenerateData, $data);
            $limit = !empty($data["vote-regenerate-limit"]) ? intval($data["vote-regenerate-limit"]) : 500;
            $step = !empty($data["vote-regenerate-step"]) ? intval($data["vote-regenerate-step"]) : 0;
            $voteRegenerateCount = !empty($data["vote-regenerate-count"]) ? intval($data["vote-regenerate-count"]) : 0;
            $voteRegenerateStartId = !empty($data["vote-regenerate-start-id"]) ? intval($data["vote-regenerate-start-id"]) : 0;
            $nonce = !empty($data["wpd-vote-regenerate"]) ? trim($data["wpd-vote-regenerate"]) : "";
            if (wp_verify_nonce($nonce, "wc_tools_form") && $voteRegenerateCount && $voteRegenerateStartId >= 0 && $limit) {
                $voteRegenerateVoteData = $this->dbManager->getVoteRegenerateData($voteRegenerateStartId, $limit);
                if ($voteRegenerateVoteData) {
                    $this->dbManager->regenerateVoteMetas($voteRegenerateVoteData);
                    ++$step;
                    $progress = $step * $limit * 100 / $voteRegenerateCount;
                    $response["progress"] = ($p = intval($progress)) > 100 ? 100 : $p;
                    $response["startId"] = $voteRegenerateVoteData[count($voteRegenerateVoteData) - 1];
                    if ($response["progress"] == 100) {
                        update_option(self::OPTION_SLUG_SHOW_VOTE_REG_MESSAGE, "0");
                    }
                } else {
                    $response["progress"] = 100;
                    $response["startId"] = 0;
                    update_option(self::OPTION_SLUG_SHOW_VOTE_REG_MESSAGE, "0");
                }
                $response["step"] = $step;
            }
        }
        wp_die(json_encode($response));
    }

    public function regenerateClosedComments() {
        $response = ["progress" => 0];
        $closedRegenerateData = isset($_POST["closedRegenerateData"]) ? $_POST["closedRegenerateData"] : "";
        if ($closedRegenerateData) {
            parse_str($closedRegenerateData, $data);
            $limit = !empty($data["closed-regenerate-limit"]) ? intval($data["closed-regenerate-limit"]) : 500;
            $step = isset($data["closed-regenerate-step"]) ? intval($data["closed-regenerate-step"]) : 0;
            $closedRegenerateCount = isset($data["closed-regenerate-count"]) ? intval($data["closed-regenerate-count"]) : 0;
            $closedRegenerateStartId = isset($data["closed-regenerate-start-id"]) ? intval($data["closed-regenerate-start-id"]) : 0;
            $nonce = isset($data["wpd-closed-regenerate"]) ? trim($data["wpd-closed-regenerate"]) : "";
            if (wp_verify_nonce($nonce, "wc_tools_form") && $closedRegenerateCount && $closedRegenerateStartId >= 0 && $limit) {
                $closedRegenerateClosedData = $this->dbManager->getClosedRegenerateData($closedRegenerateStartId, $limit);
                if ($closedRegenerateClosedData) {
                    $this->dbManager->regenerateClosedComments($closedRegenerateClosedData);
                    ++$step;
                    $progress = $step * $limit * 100 / $closedRegenerateCount;
                    $response["progress"] = ($p = intval($progress)) > 100 ? 100 : $p;
                    $response["startId"] = $closedRegenerateClosedData[count($closedRegenerateClosedData) - 1];
                    if ($response["progress"] == 100) {
                        update_option(self::OPTION_SLUG_SHOW_CLOSED_REG_MESSAGE, "0");
                    }
                } else {
                    $response["progress"] = 100;
                    $response["startId"] = 0;
                    update_option(self::OPTION_SLUG_SHOW_CLOSED_REG_MESSAGE, "0");
                }
                $response["step"] = $step;
            }
        }
        wp_die(json_encode($response));
    }

    public function regenerateVoteData() {
        $response = ["progress" => 0];
        $regenerateVoteData = isset($_POST["regenerateVoteData"]) ? $_POST["regenerateVoteData"] : "";
        if ($regenerateVoteData) {
            parse_str($regenerateVoteData, $data);
            $limit = !empty($data["regenerate-vote-data-limit"]) ? intval($data["regenerate-vote-data-limit"]) : 500;
            $step = isset($data["regenerate-vote-data-step"]) ? intval($data["regenerate-vote-data-step"]) : 0;
            $regenerateVoteDataCount = isset($data["regenerate-vote-data-count"]) ? intval($data["regenerate-vote-data-count"]) : 0;
            $regenerateVoteDataStartId = isset($data["regenerate-vote-data-start-id"]) ? intval($data["regenerate-vote-data-start-id"]) : 0;
            $nonce = isset($data["wpd-regenerate-vote-data"]) ? trim($data["wpd-regenerate-vote-data"]) : "";
            if (wp_verify_nonce($nonce, "wc_tools_form") && $regenerateVoteDataCount && $regenerateVoteDataStartId >= 0 && $limit) {
                $voteDataRegenerateData = $this->dbManager->getVoteDataRegenerateData($regenerateVoteDataStartId, $limit);
                if ($voteDataRegenerateData) {
                    $this->dbManager->regenerateVoteData($voteDataRegenerateData);
                    ++$step;
                    $progress = $step * $limit * 100 / $regenerateVoteDataCount;
                    $response["progress"] = ($p = intval($progress)) > 100 ? 100 : $p;
                    $response["startId"] = $voteDataRegenerateData[count($voteDataRegenerateData) - 1];
                    if ($response["progress"] == 100) {
                        update_option(self::OPTION_SLUG_SHOW_VOTE_DATA_REG_MESSAGE, "0");
                    }
                } else {
                    $response["progress"] = 100;
                    $response["startId"] = 0;
                    update_option(self::OPTION_SLUG_SHOW_VOTE_DATA_REG_MESSAGE, "0");
                }
                $response["step"] = $step;
            }
        }
        wp_die(json_encode($response));
    }

    public function syncCommenterData() {
        $syncCommenterData = !empty($_POST["syncCommenterData"]) ? $_POST["syncCommenterData"] : "";
        if ($syncCommenterData) {
            parse_str($syncCommenterData, $data);
            $nonce = !empty($data["wpd-sync-commenters"]) ? trim($data["wpd-sync-commenters"]) : "";
            if (wp_verify_nonce($nonce, "wc_tools_form")) {
                $this->dbManager->updateCommentersData();
                update_option(self::OPTION_SLUG_SHOW_SYNC_COMMENTERS_MESSAGE, "0");
                wp_send_json_success();
            }
        }
        wp_send_json_error();
    }

    public function rebuildRatings() {
        $response = ["progress" => 0];
        $rebuildRatings = isset($_POST["rebuildRatings"]) ? $_POST["rebuildRatings"] : "";
        if ($rebuildRatings) {
            parse_str($rebuildRatings, $data);
            $step = isset($data["rebuild-ratings-step"]) ? intval($data["rebuild-ratings-step"]) : 0;
            $rebuildRatingsCount = isset($data["rebuild-ratings-count"]) ? intval($data["rebuild-ratings-count"]) : 0;
            $rebuildRatingsStartId = isset($data["rebuild-ratings-start-id"]) ? intval($data["rebuild-ratings-start-id"]) : 0;
            $nonce = isset($data["wpd-rebuild-ratings"]) ? trim($data["wpd-rebuild-ratings"]) : "";
            if (wp_verify_nonce($nonce, "wc_tools_form") && $rebuildRatingsCount && $rebuildRatingsStartId >= 0) {
                $limit = 1;
                $rebuildRatingsData = $this->dbManager->getRebuildRatingsData($rebuildRatingsStartId, $limit);
                if ($rebuildRatingsData) {
                    $this->dbManager->rebuildRatings($rebuildRatingsData);
                    ++$step;
                    $progress = $step * $limit * 100 / $rebuildRatingsCount;
                    $response["progress"] = ($p = intval($progress)) > 100 ? 100 : $p;
                    $response["startId"] = $rebuildRatingsData[count($rebuildRatingsData) - 1]["meta_id"];
                } else {
                    $response["progress"] = 100;
                    $response["startId"] = 0;
                }
                $response["step"] = $step;
            }
        }
        wp_die(json_encode($response));
    }

    public function fixTables() {
        $fixTables = isset($_POST["fixTables"]) ? $_POST["fixTables"] : "";
        if ($fixTables) {
            parse_str($fixTables, $data);
            $nonce = !empty($data["wpd-fix-tables"]) ? trim($data["wpd-fix-tables"]) : "";
            if (wp_verify_nonce($nonce, "wc_tools_form")) {
                $this->dbManager->fixTables();
                wp_send_json_success();
            }
        }
        wp_send_json_error();
    }

    /**
     * loads the comment content on click via ajax
     */
    public function readMore() {
        $commentId = isset($_POST["commentId"]) ? intval($_POST["commentId"]) : 0;
        if ($commentId) {
            $comment = get_comment($commentId);
            $commentContent = $this->helper->filterCommentText($comment->comment_content);
            if ($this->options->content["enableImageConversion"]) {
                $commentContent = $this->helper->makeClickable($commentContent);
            }
            $commentContent = apply_filters("comment_text", $commentContent, $comment, ["is_wpdiscuz_comment" => true]);
            $commentContent = apply_filters("wpdiscuz_after_read_more", $commentContent, $comment, ["is_wpdiscuz_comment" => true]);
            $inlineContent = "";
            if ($inlineFormID = intval(get_comment_meta($comment->comment_ID, self::META_KEY_FEEDBACK_FORM_ID, true))) {
                $feedbackForm = $this->dbManager->getFeedbackForm($inlineFormID);
                $inlineContent = "<div class='wpd-inline-feedback-wrapper'><span class='wpd-inline-feedback-info'>" . esc_html($this->options->phrases["wc_feedback_content_text"]) . "</span> <i class=\"fas fa-quote-left\"></i>" . wp_trim_words($feedbackForm->content, apply_filters("wpdiscuz_feedback_content_words_count", 20)) . "&quot;  <a class='wpd-feedback-content-link' data-feedback-content-id='{$feedbackForm->id}' href='#wpd-inline-{$feedbackForm->id}'>" . esc_html($this->options->phrases["wc_read_more"]) . "</a></div>";
            }
            $form = $this->wpdiscuzForm->getForm($comment->comment_post_ID);
            $components = $this->helper->getComponents($form->getTheme(), $form->getLayout());
            $response = [
                "message" => str_replace(["{TEXT_WRAPPER_CLASSES}", "{TEXT}"], ["wpd-comment-text", $inlineContent . $commentContent], $components["text.html"]),
                "callbackFunctions" => [],
            ];
            $response = apply_filters("wpdiscuz_ajax_callbacks", $response);
            wp_send_json_success($response);
        } else {
            wp_send_json_error("error");
        }
    }

    /**
     * redirect first commenter to the selected page from options
     */
    public function redirect() {
        $commentId = !empty($_POST["commentId"]) ? intval($_POST["commentId"]) : 0;
        if ($this->options->general["redirectPage"] && $commentId) {
            $comment = get_comment($commentId);
            if ($comment->comment_ID) {
                $userCommentCount = get_comments(["author_email" => $comment->comment_author_email, "count" => true]);
                if ($userCommentCount == 1) {
                    wp_send_json_success(get_permalink($this->options->general["redirectPage"]));
                }
            }
        }
    }

    public function voteOnComment() {
        $isUserLoggedIn = is_user_logged_in();
        if (!$this->options->thread_layouts["isGuestCanVote"] && !$isUserLoggedIn) {
            wp_send_json_error("wc_login_to_vote");
        }

        $commentId = isset($_POST["commentId"]) ? intval($_POST["commentId"]) : 0;
        $voteType = isset($_POST["voteType"]) ? intval($_POST["voteType"]) : 0;

        if ($commentId && $voteType && ($voteType != -1 || ($voteType == -1 && $this->options->thread_layouts["enableDislikeButton"]))) {
            if ($isUserLoggedIn) {
                $userIdOrIp = get_current_user_id();
            } else {
                $userIdOrIp = md5($this->helper->getRealIPAddr());
            }
            $isUserVoted = $this->dbManager->isUserVoted($userIdOrIp, $commentId);
            $comment = get_comment($commentId);
            if (!$isUserLoggedIn && md5($comment->comment_author_IP) == $userIdOrIp) {
                wp_send_json_error("wc_deny_voting_from_same_ip");
            }
            if ($comment->user_id == $userIdOrIp) {
                wp_send_json_error("wc_self_vote");
            }
            $response = [];
            if ($isUserVoted != "") {
                $isUserVotedInt = intval($isUserVoted);
                $vote = $isUserVotedInt + $voteType;
                if (($vote >= -1 && $vote <= 1) || ($vote == 2 && !$this->options->thread_layouts["enableDislikeButton"])) {
                    if ($vote == 2) {
                        $vote = 0;
                        $voteType = -1;
                    }
                    $this->dbManager->updateVoteType($userIdOrIp, $commentId, $vote, current_time("timestamp"));
                    $voteCount = intval(get_comment_meta($commentId, self::META_KEY_VOTES, true)) + $voteType;
                    update_comment_meta($commentId, self::META_KEY_VOTES, "" . $voteCount);
                    $votesSeparate = get_comment_meta($commentId, self::META_KEY_VOTES_SEPARATE, true);
                    $votesSeparate = is_array($votesSeparate) ? $votesSeparate : ["like" => 0, "dislike" => 0];
                    if ($vote == 0) {
                        if ($isUserVotedInt == 1) {
                            $votesSeparate["like"] -= 1;
                        } else if ($isUserVotedInt == -1) {
                            $votesSeparate["dislike"] -= 1;
                        }
                    } else {
                        if ($voteType == 1) {
                            $votesSeparate["like"] += 1;
                        } else if ($voteType == -1) {
                            $votesSeparate["dislike"] += 1;
                        }
                    }
                    update_comment_meta($commentId, self::META_KEY_VOTES_SEPARATE, $votesSeparate);
                    do_action("wpdiscuz_update_vote", $voteType, $isUserVoted, $comment);
                    if ($this->options->thread_layouts["votingButtonsStyle"]) {
                        $response["buttonsStyle"] = "separate";
                        $response["likeCount"] = esc_html($votesSeparate["like"]);
                        $response["dislikeCount"] = esc_html(-$votesSeparate["dislike"]);
                    } else {
                        $response["buttonsStyle"] = "total";
                        $response["votes"] = esc_html($voteCount);
                    }
                    $response["curUserReaction"] = $vote;
                } else {
                    wp_send_json_error("wc_vote_only_one_time");
                }
            } else {
                $this->dbManager->addVoteType($userIdOrIp, $commentId, $voteType, intval($isUserLoggedIn), $comment->comment_post_ID, current_time("timestamp"));
                $voteCount = intval(get_comment_meta($commentId, self::META_KEY_VOTES, true)) + $voteType;
                update_comment_meta($commentId, self::META_KEY_VOTES, "" . $voteCount);
                $votesSeparate = get_comment_meta($commentId, self::META_KEY_VOTES_SEPARATE, true);
                $votesSeparate = is_array($votesSeparate) ? $votesSeparate : ["like" => 0, "dislike" => 0];
                if ($voteType == 1) {
                    $votesSeparate["like"] += 1;
                } else if ($voteType == -1) {
                    $votesSeparate["dislike"] += 1;
                }
                update_comment_meta($commentId, self::META_KEY_VOTES_SEPARATE, $votesSeparate);
                do_action("wpdiscuz_add_vote", $voteType, $comment);
                if ($this->options->thread_layouts["votingButtonsStyle"]) {
                    $response["buttonsStyle"] = "separate";
                    $response["likeCount"] = esc_html($votesSeparate["like"]);
                    $response["dislikeCount"] = esc_html(-$votesSeparate["dislike"]);
                } else {
                    $response["buttonsStyle"] = "total";
                    $response["votes"] = esc_html($voteCount);
                }
                $response["curUserReaction"] = $voteType;
            }
            $response["callbackFunctions"] = [];
            $response = apply_filters("wpdiscuz_comment_vote", $response);
            do_action("wpdiscuz_clean_post_cache", $comment->comment_post_ID, "comment_voted");
            wp_send_json_success($response);
        } else {
            wp_send_json_error("wc_voting_error");
        }
    }

    public function getInlineCommentForm() {
        $post_id = !empty($_POST["postId"]) ? intval($_POST["postId"]) : 0;
        if ($post_id && $this->dbManager->postHasFeedbackForms($post_id)) {
            $response = "<div class='wpd-inline-form'>";
            $response .= "<form method='post' class='wpd_inline_comm_form' autocomplete='off'>";
            $response .= "<textarea name='wpd_inline_comment' class='wpd-inline-comment-content' placeholder='" . esc_attr($this->options->phrases["wc_inline_form_comment"]) . "' required='required'></textarea>";
            $response .= "<label class='wpd-inline-notification'><input name='wpd_inline_notify_me' class='wpd-inline-notify-me' type='checkbox' value='1' />&nbsp;" . esc_html($this->options->phrases["wc_inline_form_notify"]) . '</label>';
            $response .= "<div class='wpd-inline-form-second-row'>";
            $currentUser = WpdiscuzHelper::getCurrentUser();
            if (empty($currentUser->ID)) {
                $response .= "<input name='wpd_inline_name' class='wpd-inline-name-input' placeholder='" . esc_html($this->options->phrases["wc_inline_form_name"]) . "' required='required' />";
                $response .= "<input name='wpd_inline_email' class='wpd-inline-name-input' placeholder='" . esc_html($this->options->phrases["wc_inline_form_email"]) . "' />";
            }
            $response .= "<button class='wpd-inline-submit wpd_not_clicked' type='submit' name='wpd_inline_submit'><span>" . esc_html($this->options->phrases["wc_inline_form_comment_button"]) . "</span><svg xmlns='https://www.w3.org/2000/svg' class='wpd-inline-submit-icon' width='24' height='24' viewBox='0 0 24 24'><path class='wpd-inline-submit-icon-first' d='M2.01 21L23 12 2.01 3 2 10l15 2-15 2z'/><path class='wpd-inline-submit-icon-second' d='M0 0h24v24H0z'/></svg></button>";
            $response .= "</div>";
            $response .= wp_nonce_field("wpd_inline_nonce_" . $post_id, "_wpd_inline_nonce", false, false);
            $response .= "</form>";
            $response .= "</div>";
            wp_send_json_success($response);
        }
        wp_send_json_error("wc_msg_required_fields");
    }

    public function getLastInlineComments() {
        $inline_form_id = !empty($_POST["inline_form_id"]) ? intval($_POST["inline_form_id"]) : 0;
        if ($inline_form_id && ($inline_form = $this->dbManager->getFeedbackForm($inline_form_id))) {
            $args = [
                "orderby" => $this->options->thread_display["orderCommentsBy"],
                "order" => "DESC",
                "number" => 3,
                "meta_query" => [
                    [
                        "key" => self::META_KEY_FEEDBACK_FORM_ID,
                        "value" => $inline_form->id,
                        "compare" => "=",
                    ],
                ],
            ];
            $comments = get_comments($args);
            $content = "";
            if ($comments) {
                $content .= "<div class='wpd-last-inline-comments-wrapper'>";
                $content .= "<div class='wpd-last-inline-comments'>";
                foreach ($comments as $k => $comment) {
                    $content .= "<div class='wpd-last-inline-comment' data-inline-comment-id='" . esc_attr($comment->comment_ID) . "'>";
                    $content .= "<div>";
                    $content .= "<span class='wpd-last-inline-comment-author-avatar'>" . get_avatar($comment->comment_author_email, 16) . "</span>";
                    $content .= "<span class='wpd-last-inline-comment-author-name'>" . esc_html($comment->comment_author) . "</span>";
                    $content .= "<span class='wpd-last-inline-comment-date'>" . esc_html($this->helper->dateDiff($comment->comment_date_gmt)) . "</span>";
                    $content .= "</div>";
                    $commentContent = function_exists("mb_substr") ? mb_substr($comment->comment_content, 0, 85) : substr($comment->comment_content, 0, 85);
                    if (strlen($comment->comment_content) > strlen($commentContent)) {
                        $commentContent .= "&nbsp;<a href='" . get_comment_link($comment) . "' class='wpd-load-inline-comment' title='" . esc_html__("Read More", "wpdiscuz") . "'>[...]</a>";
                    }
                    $content .= "<span class='wpd-last-inline-comment-text'>" . wp_unslash($commentContent) . "</span>";
                    $content .= "</div>";
                }
                $content .= "</div>";
                if (!$this->options->wp["isPaginate"]) {
                    $content .= "<a href='' class='wpd-view-all-inline-comments'>" . esc_html($this->options->phrases["wc_inline_comments_view_all"]) . "</a>";
                }
                $content .= "</div>";
            }
            wp_send_json_success($content);
        } else {
            wp_send_json_error("wc_msg_required_fields");
        }
    }

    /**
     * get comment text from db
     */
    public function editComment() {
        $commentId = !empty($_POST["commentId"]) ? intval($_POST["commentId"]) : 0;
        if ($commentId) {
            $comment = get_comment($commentId);
            $postID = $comment->comment_post_ID;
            $form = $this->wpdiscuzForm->getForm($postID);
            $form->initFormFields();
            $currentUser = WpdiscuzHelper::getCurrentUser();
            $highLevelUser = current_user_can("moderate_comments");
            $isCurrentUserCanEdit = $this->helper->isCommentEditable($comment) && $this->helper->canUserEditComment($comment, $currentUser);
            if (!intval(get_comment_meta($comment->comment_ID, self::META_KEY_CLOSED, true)) && ($highLevelUser || $isCurrentUserCanEdit)) {
                wp_send_json_success($form->renderEditFrontCommentForm($comment));
            } else {
                wp_send_json_error("wc_comment_edit_not_possible");
            }
        } else {
            wp_send_json_error("wc_comment_edit_not_possible");
        }
    }

    public function userRate() {
        $rating = !empty($_POST["rating"]) ? intval($_POST["rating"]) : 0;
        $post_id = !empty($_POST["postId"]) ? intval($_POST["postId"]) : 0;
        if ($rating && $post_id && ($form = $this->wpdiscuzForm->getForm($post_id))) {
            $currentUser = $this->helper->getCurrentUser();
            if (!empty($currentUser->ID)) {
                if (!$this->dbManager->isUserRated($currentUser->ID, "", $post_id)) {
                    $this->dbManager->addRate($post_id, $currentUser->ID, "", $rating, current_time("timestamp"));
                    $data = $this->dbManager->getPostRatingData($post_id);
                    $votes = 0;
                    foreach ($data as $value) {
                        $votes += $value;
                    }
                    $count = count($data);
                    update_post_meta($post_id, self::POSTMETA_POST_RATING, round($votes / $count, 1));
                    update_post_meta($post_id, self::POSTMETA_POST_RATING_COUNT, $count);
                    do_action("wpdiscuz_clean_post_cache", $post_id, "user_rated");
                    wp_send_json_success();
                } else {
                    wp_send_json_error("wc_cannot_rate_again");
                }
            } else if ($form->getUserCanRateOnPost()) {
                $userIp = md5($this->helper->getRealIPAddr());
                if (!$this->dbManager->isUserRated(0, $userIp, $post_id)) {
                    $this->dbManager->addRate($post_id, 0, $userIp, $rating, current_time("timestamp"));
                    $data = $this->dbManager->getPostRatingData($post_id);
                    $votes = 0;
                    foreach ($data as $value) {
                        $votes += $value;
                    }
                    $count = count($data);
                    update_post_meta($post_id, self::POSTMETA_POST_RATING, round($votes / $count, 1));
                    update_post_meta($post_id, self::POSTMETA_POST_RATING_COUNT, $count);
                    do_action("wpdiscuz_clean_post_cache", $post_id, "user_rated");
                    wp_send_json_success();
                } else {
                    wp_send_json_error("wc_cannot_rate_again");
                }
            } else {
                wp_send_json_error("wc_not_allowed_to_rate");
            }
        } else {
            wp_send_json_error("wc_msg_required_fields");
        }
    }

    public function unsubscribe() {
        $sid = !empty($_POST["sid"]) ? absint($_POST["sid"]) : 0;
        $skey = !empty($_POST["skey"]) ? trim($_POST["skey"]) : "";
        if ($sid && $skey) {
            $this->dbManager->unsubscribe($sid, $skey);
            wp_send_json_success(esc_html($this->options->phrases["wc_unsubscribe_message"]));
        }
        wp_send_json_error("Something is wrong");
    }

    public function wpd_stat_brief() {
        wp_send_json_success(["all" => esc_html($this->dbManager->getCommentsCount()), "inline" => esc_html($this->dbManager->getInlineCommentsCount()), "threads" => esc_html($this->dbManager->getThreadsCount()), "replies" => esc_html($this->dbManager->getRepliesCount()), "users" => esc_html($this->dbManager->getUserCommentersCount()), "guests" => esc_html($this->dbManager->getGuestCommentersCount())]);
    }

    public function wpd_stat_subs() {
        ob_start();
        ?>
        <ul class="wpd-box-list">
            <li><div class="wpd-list-label"><?php esc_html_e("Subscribers", "wpdiscuz") ?></div><div class="wpd-list-val"><?php echo esc_html($this->dbManager->getAllSubscribersCount()); ?></div></li>
            <li><div class="wpd-list-label"><?php esc_html_e("Subscription - posts", "wpdiscuz") ?></div><div class="wpd-list-val"><?php echo esc_html($this->dbManager->getPostSubscribersCount()); ?></div></li>
            <li><div class="wpd-list-label"><?php esc_html_e("Subscription - all comments", "wpdiscuz") ?></div><div class="wpd-list-val"><?php echo esc_html($this->dbManager->getAllCommentSubscribersCount()); ?></div></li>
            <li><div class="wpd-list-label"><?php esc_html_e("Subscription - comment", "wpdiscuz") ?></div><div class="wpd-list-val"><?php echo esc_html($this->dbManager->getCommentSubscribersCount()); ?></div></li>
            <li><div class="wpd-list-label"><?php esc_html_e("Followers", "wpdiscuz") ?></div><div class="wpd-list-val"><?php echo esc_html($this->dbManager->getFollowersCount()); ?></div></li>
            <li><div class="wpd-list-label"><?php esc_html_e("Following", "wpdiscuz") ?></div><div class="wpd-list-val"><?php echo esc_html($this->dbManager->getFollowingCount()); ?></div></li>
        </ul>
        <?php
        wp_die(ob_get_clean());
    }

    public function wpd_stat_graph() {
        $interval = !empty($_POST["interval"]) ? trim($_POST["interval"]) : "";
        if ($interval) {
            $all = $this->dbManager->getGraphAllComments($interval);
            $inline = $this->dbManager->getGraphInlineComments($interval);
            $diffInline = array_diff(array_keys($all), array_keys($inline));
            $diffAll = array_diff(array_keys($inline), array_keys($all));
            $combInline = array_combine($diffInline, array_pad([], count($diffInline), 0));
            $combAll = array_combine($diffAll, array_pad([], count($diffAll), 0));
            foreach ($combAll as $key => $val) {
                $all[$key] = $val;
            }
            foreach ($combInline as $key => $val) {
                $inline[$key] = $val;
            }
            ksort($all);
            ksort($inline);
            $data = [
                "el" => "<canvas id='wpdChart'></canvas>",
                "all" => array_values($all),
                "inline" => array_values($inline),
                "labels" => array_map(function ($v) {
                            return esc_html(date("d M", $v));
                        }, array_keys($all)),
            ];
            wp_send_json_success($data);
        }
        wp_send_json_error();
    }

    public function wpd_stat_user() {
        $orderby = !empty($_POST["orderby"]) ? trim($_POST["orderby"]) : "";
        $order = !empty($_POST["order"]) ? trim($_POST["order"]) : "";
        $page = !empty($_POST["page"]) ? intval($_POST["page"]) : "";
        if ($orderby && $order && $page) {
            ob_start();
            ?>
            <table class="wpd-user-table" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <th>
                        <?php esc_html_e("Comment Author", "wpdiscuz") ?>
                    </th>
                    <th class="wpd-sort-field<?php echo esc_attr("comments" === $orderby ? " wpd-active" : ""); ?>" data-orderby="comments">
                        <?php esc_html_e("Comments", "wpdiscuz") ?>
                        <span<?php echo "comments" !== $orderby ? " style='display:none;'" : ""; ?> class="dashicons <?php echo esc_attr("comments" === $orderby && "desc" === $order ? "dashicons-arrow-down-alt2" : "dashicons-arrow-up-alt2"); ?>"></span>
                    </th>
                    <th class="wpd-sort-field<?php echo esc_attr("subscriptions" === $orderby ? " wpd-active" : ""); ?>" data-orderby="subscriptions">
                        <?php esc_html_e("Subscriptions", "wpdiscuz") ?>
                        <span<?php echo "subscriptions" !== $orderby ? " style='display:none;'" : ""; ?> class="dashicons <?php echo esc_attr("subscriptions" === $orderby && "desc" === $order ? "dashicons-arrow-down-alt2" : "dashicons-arrow-up-alt2"); ?>"></span>
                    </th>
                    <th class="wpd-sort-field<?php echo esc_attr("following" === $orderby ? " wpd-active" : ""); ?>" data-orderby="following">
                        <?php esc_html_e("Following", "wpdiscuz") ?>
                        <span<?php echo "following" !== $orderby ? " style='display:none;'" : ""; ?> class="dashicons <?php echo esc_attr("following" === $orderby && "desc" === $order ? "dashicons-arrow-down-alt2" : "dashicons-arrow-up-alt2"); ?>"></span>
                    </th>
                    <th class="wpd-sort-field<?php echo esc_attr("followers" === $orderby ? " wpd-active" : ""); ?>" data-orderby="followers">
                        <?php esc_html_e("Followers", "wpdiscuz") ?>
                        <span<?php echo "followers" !== $orderby ? " style='display:none;'" : ""; ?> class="dashicons <?php echo esc_attr("followers" === $orderby && "desc" === $order ? "dashicons-arrow-down-alt2" : "dashicons-arrow-up-alt2"); ?>"></span>
                    </th>
                    <th class="wpd-sort-field<?php echo esc_attr("last_activity" === $orderby ? " wpd-active" : ""); ?>" data-orderby="last_activity">
                        <?php esc_html_e("Last Activity", "wpdiscuz") ?>
                        <span<?php echo "last_activity" !== $orderby ? " style='display:none;'" : ""; ?> class="dashicons <?php echo esc_attr("last_activity" === $orderby && "desc" === $order ? "dashicons-arrow-down-alt2" : "dashicons-arrow-up-alt2"); ?>"></span>
                    </th>
                </tr>
                <?php
                $data = [];
                $activeUsers = $this->dbManager->getActiveUsers($orderby, $order, $page);
                $more = false;
                if (count($activeUsers) > 6) {
                    $more = true;
                    array_pop($activeUsers);
                }
                $data["more"] = $more;
                foreach ($activeUsers as $k => $val) {
                    ?>
                    <tr>
                        <td>
                            <?php echo get_avatar($val["comment_author_email"], 24); ?>
                            <span class="wpd-name"><?php echo esc_html($val["comment_author"]); ?></span>
                        </td>
                        <td<?php echo "comments" === $orderby ? " class='wpd-active'" : ""; ?>><?php echo esc_html(number_format($val["count"])); ?></td>
                        <td<?php echo "subscriptions" === $orderby ? " class='wpd-active'" : ""; ?>><?php echo esc_html(number_format($val["scount"])); ?></td>
                        <td<?php echo "following" === $orderby ? " class='wpd-active'" : ""; ?>><?php echo esc_html(number_format($val["ficount"])); ?></td>
                        <td<?php echo "followers" === $orderby ? " class='wpd-active'" : ""; ?>><?php echo esc_html(number_format($val["fwcount"])); ?></td>
                        <td<?php echo "last_activity" === $orderby ? " class='wpd-active'" : ""; ?>><?php echo esc_html(date("d M, y", strtotime($val["last_date"]))); ?></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <?php
            $data["body"] = ob_get_clean();
            wp_send_json_success($data);
        }
        wp_send_json_error(esc_html__("Something is wrong"));
    }

    public function searchOption() {
        $search = !empty($_POST["s"]) ? trim($_POST["s"]) : "";
        if ($search) {
            $optionsObject = $this->options;
            $settings = $this->options->settingsArray();
            $result = [];
            foreach ($settings as $type) {
                foreach ($type as $key => $tab) {
                    foreach ($tab["options"] as $k => $val) {
                        if (stripos($tab["title"], $search) !== false || stripos($tab["title_original"], $search) !== false) {
                            $result[] = "<a href='" . esc_url_raw(admin_url("admin.php?page=" . self::PAGE_SETTINGS . "&wpd_tab=" . $key)) . "' tabindex='" . esc_attr($tab["title_original"]) . "'>" . $tab["title"] . "</a>";
                        }
                        if (stripos($val["label"], $search) !== false || stripos($val["description"], $search) !== false || stripos($val["label_original"], $search) !== false || stripos($val["description_original"], $search) !== false || stripos($k, $search)) {
                            $result[] = "<a href='" . esc_url_raw(admin_url("admin.php?page=" . self::PAGE_SETTINGS . "&wpd_tab=" . $key . "#wpdOpt-" . $k)) . "' tabindex='" . esc_attr($key . "-" . $k) . "'>" . $val["label"] . "</a>";
                        }
                    }
                }
            }
            $result = array_unique($result);
            wp_die(implode("<br>", $result));
        }
    }

    public function resetPostRating() {
        if (current_user_can("edit_posts")) {
            $postId = !empty($_POST["postId"]) ? intval($_POST["postId"]) : 0;
            if ($postId) {
                delete_post_meta($postId, self::POSTMETA_POST_RATING);
                delete_post_meta($postId, self::POSTMETA_POST_RATING_COUNT);
                $this->dbManager->removeRatings($postId);
                do_action("wpdiscuz_clean_post_cache", $postId, "ratings_reset");
                wp_send_json_success();
            }
        }
        wp_send_json_error();
    }

    public function resetFieldsRatings() {
        if (current_user_can("edit_posts")) {
            $postId = !empty($_POST["postId"]) ? intval($_POST["postId"]) : 0;
            if ($postId) {
                $postMeta = get_post_meta($postId, self::POSTMETA_RATING_COUNT, true);
                if ($postMeta) {
                    foreach ($postMeta as $key => $value) {
                        $this->dbManager->deleteCommentMeta($key);
                    }
                    update_post_meta($postId, self::POSTMETA_RATING_COUNT, []);
                }
                wp_send_json_success();
            }
        }
        wp_send_json_error();
    }

}
