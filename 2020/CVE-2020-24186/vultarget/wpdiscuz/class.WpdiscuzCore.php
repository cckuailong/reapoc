<?php
/*
 * Plugin Name: wpDiscuz
 * Description: #1 WordPress Comment Plugin. Innovative, modern and feature-rich comment system to supercharge your website comment section.
 * Version: 7.0.4
 * Author: gVectors Team
 * Author URI: https://gvectors.com/
 * Plugin URI: https://wpdiscuz.com/
 * Text Domain: wpdiscuz
 * Domain Path: /languages/
 * wpDiscuz Update: manual
 */
if (!defined("ABSPATH")) {
    exit();
}

define("WPDISCUZ_DIR_PATH", dirname(__FILE__));
define("WPDISCUZ_DIR_NAME", basename(WPDISCUZ_DIR_PATH));

include_once WPDISCUZ_DIR_PATH . "/includes/interface.WpDiscuzConstants.php";
include_once WPDISCUZ_DIR_PATH . "/utils/functions.php";
include_once WPDISCUZ_DIR_PATH . "/options/class.WpdiscuzOptions.php";
include_once WPDISCUZ_DIR_PATH . "/utils/class.WpdiscuzHelper.php";
include_once WPDISCUZ_DIR_PATH . "/utils/class.WpdiscuzHelperEmail.php";
include_once WPDISCUZ_DIR_PATH . "/utils/class.WpdiscuzHelperOptimization.php";
include_once WPDISCUZ_DIR_PATH . "/utils/class.WpdiscuzHelperUpload.php";
include_once WPDISCUZ_DIR_PATH . "/includes/class.WpdiscuzDBManager.php";
include_once WPDISCUZ_DIR_PATH . "/forms/wpDiscuzForm.php";
include_once WPDISCUZ_DIR_PATH . "/utils/class.WpdiscuzCache.php";
include_once WPDISCUZ_DIR_PATH . "/utils/class.WpdiscuzHelperAjax.php";
include_once WPDISCUZ_DIR_PATH . "/includes/class.WpdiscuzRest.php";

class WpdiscuzCore implements WpDiscuzConstants {

    public $dbManager;
    public $helper;
    public $helperAjax;
    public $helperEmail;
    public $helperOptimization;
    public $helperUpload;
    public $wpdiscuzOptionsJs;
    public $options;
    public $commentsArgs;
    private $version;
    public $wpdiscuzForm;
    public $form;
    private $cache;
    public $subscriptionData;
    public $isWpdiscuzLoaded;
    private $requestUri;
    public static $CURRENT_BLOG_ID;
    private static $_instance = null;

    /**
     * @deprecated
     */
    public $optionsSerialized;

    private function __construct() {
        add_option(self::OPTION_SLUG_VERSION, "1.0.0");
        $this->version = get_option(self::OPTION_SLUG_VERSION, "1.0.0");
        wp_cookie_constants();
        $this->dbManager = new WpdiscuzDBManager();
        $this->optionsSerialized = $this->options = new WpdiscuzOptions($this->dbManager);
        $this->optionsSerialized->reCaptchaVersion = $this->options->recaptcha["version"];
        $this->wpdiscuzForm = new wpDiscuzForm($this->options, $this->version);
        $this->helper = new WpdiscuzHelper($this->options, $this->dbManager, $this->wpdiscuzForm);
        $this->helperEmail = new WpdiscuzHelperEmail($this->options, $this->dbManager);
        $this->helperOptimization = new WpdiscuzHelperOptimization($this->options, $this->dbManager, $this->helperEmail);
        $this->helperAjax = new WpdiscuzHelperAjax($this->options, $this->dbManager, $this->helper, $this->helperEmail, $this->wpdiscuzForm);
        $this->helperUpload = new WpdiscuzHelperUpload($this->options, $this->dbManager, $this->wpdiscuzForm, $this->helper);
        $this->cache = new WpdiscuzCache($this->options, $this->helper, $this->dbManager);
        $this->requestUri = !empty($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : "";

        if ($this->options->thread_display["isLoadOnlyParentComments"]) {
            add_action("wp_ajax_wpdShowReplies", [&$this, "showReplies"]);
            add_action("wp_ajax_nopriv_wpdShowReplies", [&$this, "showReplies"]);
        }

        self::$CURRENT_BLOG_ID = get_current_blog_id();
        register_activation_hook(__FILE__, [&$this, "pluginActivation"]);

        /* GRAVATARS CACHE */
        register_activation_hook(__FILE__, [&$this, "registerGravatarsJobs"]);
        register_deactivation_hook(__FILE__, [&$this, "deregisterGravatarsJobs"]);
        add_filter("cron_schedules", [&$this, "setGravatarsIntervals"]);
        /* GRAVATARS CACHE */
        add_action("wpmu_new_blog", [&$this, "addNewBlog"]);
        add_action("delete_blog", [&$this, "deleteBlog"]);
        add_action("wp_head", [&$this, "initCurrentPostType"]);

        add_action("init", [&$this, "wpdiscuzTextDomain"]);
        add_action("admin_init", [&$this, "pluginNewVersion"], 1);
        add_action("admin_enqueue_scripts", [&$this, "backendFiles"], 100);
        add_action("wp_enqueue_scripts", [&$this, "frontendFiles"]);
        add_action("admin_menu", [&$this, "addPluginOptionsPage"], 1);

        add_action("wp_ajax_wpdLoadMoreComments", [&$this, "loadMoreComments"]);
        add_action("wp_ajax_nopriv_wpdLoadMoreComments", [&$this, "loadMoreComments"]);
        add_action("wp_ajax_wpdSorting", [&$this, "sorting"]);
        add_action("wp_ajax_nopriv_wpdSorting", [&$this, "sorting"]);
        add_action("wp_ajax_wpdAddComment", [&$this, "addComment"]);
        add_action("wp_ajax_nopriv_wpdAddComment", [&$this, "addComment"]);
        add_action("wp_ajax_wpdGetSingleComment", [&$this, "getSingleComment"]);
        add_action("wp_ajax_nopriv_wpdGetSingleComment", [&$this, "getSingleComment"]);
        add_action("wp_ajax_wpdMostReactedComment", [&$this, "mostReactedComment"]);
        add_action("wp_ajax_nopriv_wpdMostReactedComment", [&$this, "mostReactedComment"]);
        add_action("wp_ajax_wpdHottestThread", [&$this, "hottestThread"]);
        add_action("wp_ajax_nopriv_wpdHottestThread", [&$this, "hottestThread"]);

        $plugin = plugin_basename(__FILE__);
        add_filter("plugin_action_links_$plugin", [&$this, "addPluginSettingsLink"]);
        add_filter("comments_clauses", [&$this, "commentsClauses"]);

        add_action("wp_ajax_wpdSaveEditedComment", [&$this, "saveEditedComment"]);
        add_action("wp_ajax_nopriv_wpdSaveEditedComment", [&$this, "saveEditedComment"]);

        if ($this->options->live["commentListUpdateType"]) {
            add_action("wp_ajax_wpdUpdateAutomatically", [&$this, "updateAutomatically"]);
            add_action("wp_ajax_nopriv_wpdUpdateAutomatically", [&$this, "updateAutomatically"]);
        }

        add_action("wp_loaded", [&$this, "addNewRoles"]);
        add_filter("comments_template_query_args", [&$this, "commentsTemplateQueryArgs"]);
        add_action("pre_get_comments", [&$this, "preGetComments"]);
        add_filter("found_comments_query", [&$this, "foundCommentsQuery"], 10, 2);

        if ($this->options->live["commentListUpdateType"] || ($this->options->live["enableBubble"] && $this->options->live["bubbleLiveUpdate"])) {
            add_action("rest_api_init", [&$this, "registerRestRoutes"], 10);
        }
        if ($this->options->live["enableBubble"] && $this->options->live["bubbleLiveUpdate"]) {
            add_action("wp_ajax_wpdBubbleUpdate", [&$this, "bubbleUpdate"]);
            add_action("wp_ajax_nopriv_wpdBubbleUpdate", [&$this, "bubbleUpdate"]);
        }

        add_action("admin_footer", [&$this, "feedbackDialog"]);
        add_filter("mce_buttons", [&$this, "mceButton"]);
        add_filter("mce_external_plugins", [&$this, "mceExternalPlugin"]);
        add_shortcode(self::WPDISCUZ_FEEDBACK_SHORTCODE, [&$this, "feedbackShortcode"]);
        add_action("wp_ajax_wpdAddInlineComment", [&$this, "addInlineComment"]);
        add_action("wp_ajax_nopriv_wpdAddInlineComment", [&$this, "addInlineComment"]);
        add_action("wp_footer", [&$this, "footerContents"]);
        add_action("enqueue_block_editor_assets", [&$this, "gutenbergButton"]);

        add_filter("extra_plugin_headers", [&$this, "extraPluginHeaders"]);
        add_filter("auto_update_plugin", [&$this, "shouldUpdate"], 10, 2);
    }

    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function extraPluginHeaders($headers) {
        $headers[] = "wpDiscuz Update";
        return $headers;
    }

    public function shouldUpdate($shouldUpdate, $plugin) {
        if (!isset($plugin->plugin, $plugin->new_version)) {
            return $shouldUpdate;
        }

        if ("wpdiscuz/class.WpdiscuzCore.php" !== $plugin->plugin) {
            return $shouldUpdate;
        }

        $pluginData = get_plugin_data(__FILE__);

        if (isset($pluginData["wpDiscuz Update"]) && $pluginData["wpDiscuz Update"] === "manual") {
            return false;
        }

        return $shouldUpdate;
    }

    public function pluginActivation($networkwide) {
        if (function_exists("is_multisite") && is_multisite() && $networkwide) {
            $oldBlogID = $this->dbManager->getBlogID();
            $oldSitePluginVersion = $this->version;
            $blogIDs = $this->dbManager->getBlogIDs();
            foreach ($blogIDs as $k => $blogID) {
                switch_to_blog($blogID);
                $this->version = get_option(self::OPTION_SLUG_VERSION, "1.0.0");
                $this->activateWpDiscuz();
            }
            switch_to_blog($oldBlogID);
            $this->version = $oldSitePluginVersion;
            return;
        }
        $this->activateWpDiscuz();
    }

    public function addNewBlog($blogID) {
        if (is_plugin_active_for_network("wpdiscuz/class.WpdiscuzCore.php")) {
            $oldBlogID = $this->dbManager->getBlogID();
            $oldSitePluginVersion = $this->version;
            switch_to_blog($blogID);
            $this->version = get_option(self::OPTION_SLUG_VERSION, "1.0.0");
            $this->activateWpDiscuz();
            switch_to_blog($oldBlogID);
            $this->version = $oldSitePluginVersion;
        }
    }

    public function deleteBlog($blogID) {
        if (is_plugin_active_for_network("wpdiscuz/class.WpdiscuzCore.php")) {
            $oldBlogID = $this->dbManager->getBlogID();
            switch_to_blog($blogID);
            $this->dbManager->dropTables();
            switch_to_blog($oldBlogID);
        }
    }

    private function activateWpDiscuz() {
        $this->dbManager->dbCreateTables();
        $this->pluginNewVersion();
    }

    public function wpdiscuzTextDomain() {
        load_plugin_textdomain("wpdiscuz", false, dirname(plugin_basename(__FILE__)) . "/languages/");
    }

    public function registerGravatarsJobs() {
        if (!wp_next_scheduled(self::GRAVATARS_CACHE_ADD_ACTION)) {
            wp_schedule_event(current_time("timestamp"), self::GRAVATARS_CACHE_ADD_KEY_RECURRENCE, self::GRAVATARS_CACHE_ADD_ACTION);
        }

        if (!wp_next_scheduled(self::GRAVATARS_CACHE_DELETE_ACTION)) {
            wp_schedule_event(current_time("timestamp"), self::GRAVATARS_CACHE_DELETE_KEY_RECURRENCE, self::GRAVATARS_CACHE_DELETE_ACTION);
        }
    }

    public function deregisterGravatarsJobs() {
        if (wp_next_scheduled(self::GRAVATARS_CACHE_ADD_ACTION)) {
            wp_clear_scheduled_hook(self::GRAVATARS_CACHE_ADD_ACTION);
        }

        if (wp_next_scheduled(self::GRAVATARS_CACHE_DELETE_ACTION)) {
            wp_clear_scheduled_hook(self::GRAVATARS_CACHE_DELETE_ACTION);
        }
    }

    public function setGravatarsIntervals($schedules) {
        $cacheAddInterval = [
            "interval" => self::GRAVATARS_CACHE_ADD_RECURRENCE * HOUR_IN_SECONDS,
            "display" => esc_html__("Every 3 hours", "wpdiscuz")
        ];
        $cacheDeleteInterval = [
            "interval" => self::GRAVATARS_CACHE_DELETE_RECURRENCE * HOUR_IN_SECONDS,
            "display" => esc_html__("Every 48 hours", "wpdiscuz")
        ];
        $schedules[self::GRAVATARS_CACHE_ADD_KEY_RECURRENCE] = $cacheAddInterval;
        $schedules[self::GRAVATARS_CACHE_DELETE_KEY_RECURRENCE] = $cacheDeleteInterval;
        return $schedules;
    }

    public function updateAutomatically() {
        $postId = isset($_POST["postId"]) ? intval($_POST["postId"]) : 0;
        $loadLastCommentId = isset($_POST["loadLastCommentId"]) ? intval($_POST["loadLastCommentId"]) : 0;
        if ($postId && $loadLastCommentId) {
            $this->isWpdiscuzLoaded = true;
            $visibleCommentIds = isset($_POST["visibleCommentIds"]) ? rtrim($_POST["visibleCommentIds"], ",") : "";
            $cArgs = $this->getDefaultCommentsArgs($postId);
            $lastCommentId = $this->dbManager->getLastCommentId($cArgs);
            if ($lastCommentId > $loadLastCommentId) {
                $response = [];
                $response["loadLastCommentId"] = $lastCommentId;
                $commentListArgs = $this->getCommentListArgs($postId);
                $commentListArgs["new_loaded_class"] = "wpd-new-loaded-comment";
                $sentEmail = isset($_COOKIE["comment_author_email_" . COOKIEHASH]) ? trim($_COOKIE["comment_author_email_" . COOKIEHASH]) : "";
                $email = !empty($commentListArgs["current_user"]->ID) ? $commentListArgs["current_user"]->user_email : $sentEmail;
                $newCommentIds = $this->dbManager->getNewCommentIds($cArgs, $loadLastCommentId, $email, $visibleCommentIds);
                $newCommentIds = apply_filters("wpdiscuz_live_update_new_comment_ids", $newCommentIds, $postId, $commentListArgs["current_user"]);
                $response["message"] = [];
                foreach ($newCommentIds as $k => $newCommentId) {
                    $comment = get_comment($newCommentId);
                    if (($comment->comment_parent && (in_array($comment->comment_parent, explode(",", $visibleCommentIds)) || in_array($comment->comment_parent, $newCommentIds))) || !$comment->comment_parent) {
                        $commentHtml = wp_list_comments($commentListArgs, [$comment]);
                        $commentObject = ["comment_parent" => $comment->comment_parent, "comment_html" => $commentHtml];
                        if ($comment->comment_parent) {
                            array_push($response["message"], $commentObject);
                        } else {
                            array_unshift($response["message"], $commentObject);
                        }
                    }
                }
                $response["wc_all_comments_count_new"] = esc_html(get_comments_number($postId));
                $response["wc_all_comments_count_new_html"] = "<span class='wpdtc'>" . esc_html($response["wc_all_comments_count_new"]) . "</span> " . esc_html(1 == $response["wc_all_comments_count_new"] ? $this->form->getHeaderTextSingle() : $this->form->getHeaderTextPlural());
                wp_send_json_success($response);
            }
        }
    }

    public function bubbleUpdate() {
        $postId = isset($_POST["postId"]) ? intval($_POST["postId"]) : 0;
        $newCommentIds = isset($_POST["newCommentIds"]) ? trim($_POST["newCommentIds"]) : "";
        if ($postId && $newCommentIds) {
            $this->isWpdiscuzLoaded = true;
            $newCommentIds = explode(",", trim($newCommentIds, ","));
            $postId = intval($postId);
            $commentListArgs = $this->getCommentListArgs($postId);
            $commentListArgs["new_loaded_class"] = "wpd-new-loaded-comment";
            $response = ["message" => []];
            foreach ($newCommentIds as $k => $newCommentId) {
                $comment = get_comment($newCommentId);
                $commentHtml = wp_list_comments($commentListArgs, [$comment]);
                $response["message"][] = ["comment_id" => $comment->comment_ID, "comment_parent" => $comment->comment_parent, "comment_html" => $commentHtml];
            }
            $response = apply_filters("wpdiscuz_ajax_callbacks", $response);
            wp_send_json_success($response);
        }
    }

    public function addComment() {
        $isAnonymous = false;
        $uniqueId = isset($_POST["wpdiscuz_unique_id"]) ? trim($_POST["wpdiscuz_unique_id"]) : "";
        $postId = isset($_POST["postId"]) ? intval($_POST["postId"]) : "";
        $comment_content = isset($_POST["wc_comment"]) ? trim($_POST["wc_comment"]) : "";

        if ($uniqueId && $postId && $comment_content) {
            $form = $this->wpdiscuzForm->getForm($postId);
            $form->initFormFields();

            do_action("wpdiscuz_before_comment_post");
            if (!comments_open($postId)) {
                wp_die(esc_html($this->options->phrases["wc_commenting_is_closed"]));
            }

            if (function_exists("zerospam_get_key") && isset($_POST["wpdiscuz_zs"]) && ($wpdiscuzZS = $_POST["wpdiscuz_zs"])) {
                $_POST["zerospam_key"] = $wpdiscuzZS == md5(zerospam_get_key()) ? zerospam_get_key() : "";
            }
            $commentDepth = isset($_POST["wpd_comment_depth"]) && intval($_POST["wpd_comment_depth"]) ? intval($_POST["wpd_comment_depth"]) : 1;
            $isInSameContainer = "1";
            $currentUser = WpdiscuzHelper::getCurrentUser();
            if (class_exists("WooCommerce") && get_post_type($postId) === "product") {
                $isInSameContainer = "0";
            } else if ($commentDepth > $this->options->wp["threadCommentsDepth"]) {
                $commentDepth = $this->options->wp["threadCommentsDepth"];
                $isInSameContainer = "0";
            } else if (!$this->options->wp["threadComments"]) {
                $isInSameContainer = "0";
            }
            $notificationType = isset($_POST["wpdiscuz_notification_type"]) ? $_POST["wpdiscuz_notification_type"] : "";

            $form->validateDefaultCaptcha($currentUser);
            $form->validateFields($currentUser);

            $website_url = "";
            if ($currentUser && $currentUser->ID) {
                $user_id = $currentUser->ID;
                $name = $this->helper->getCurrentUserDisplayName($currentUser);
                $email = $currentUser->user_email;
            } else {
                $user_id = 0;
                $name = urldecode($form->validateDefaultName($currentUser));
                $email = urldecode($form->validateDefaultEmail($currentUser, $isAnonymous));
                $website_url = $form->validateDefaultWebsite($currentUser);
            }

            $comment_content = ($this->options->form["richEditor"] === "both" || (!wp_is_mobile() && $this->options->form["richEditor"] === "desktop")) && !$this->options->showEditorToolbar() ? html_entity_decode($comment_content) : $comment_content;
            $comment_content = $this->helper->replaceCommentContentCode($comment_content);
            $comment_content = $this->helper->filterCommentText($comment_content);
            if (!$comment_content) {
                wp_send_json_error("wc_msg_required_fields");
            }
            $commentMinLength = intval($this->options->content["commentTextMinLength"]);
            $commentMaxLength = intval($this->options->content["commentTextMaxLength"]);
            $contentLength = function_exists("mb_strlen") ? mb_strlen(strip_tags($comment_content)) : strlen(strip_tags($comment_content));
            if ($commentMinLength > 0 && $contentLength < $commentMinLength) {
                wp_send_json_error("wc_msg_input_min_length");
            }

            if ($commentMaxLength > 0 && $contentLength > $commentMaxLength) {
                wp_send_json_error("wc_msg_input_max_length");
            }

            if ($name && $email && $comment_content) {
                $this->isWpdiscuzLoaded = true;
                $website_url = $website_url ? urldecode($website_url) : "";
                $stickyComment = isset($_POST["wc_sticky_comment"]) && ($sticky = intval($_POST["wc_sticky_comment"])) ? $sticky : "";
                $closedComment = isset($_POST["wc_closed_comment"]) && ($closed = absint($_POST["wc_closed_comment"])) ? $closed : "";
                $uid_data = $this->helper->getUIDData($uniqueId);
                $comment_parent = intval($uid_data[0]);
                $parentComment = $comment_parent ? get_comment($comment_parent) : null;
                $comment_parent = isset($parentComment->comment_ID) ? $parentComment->comment_ID : 0;
                if ($parentComment && intval(get_comment_meta($comment_parent, self::META_KEY_CLOSED, true))) {
                    wp_die(esc_html($this->options->phrases["wc_closed_comment_thread"]));
                }
                $this->helper->restrictCommentingPerUser($email, $comment_parent, $postId);
                $wc_user_agent = isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : "";
                $new_commentdata = [
                    "user_id" => $user_id,
                    "comment_post_ID" => $postId,
                    "comment_parent" => class_exists("WooCommerce") && get_post_type($postId) === "product" ? 0 : $comment_parent,
                    "comment_author" => $name,
                    "comment_author_email" => $email,
                    "comment_content" => $comment_content,
                    "comment_author_url" => $website_url,
                    "comment_agent" => $wc_user_agent,
                    "comment_type" => $stickyComment ? self::WPDISCUZ_STICKY_COMMENT : "",
                ];

                $new_comment_id = wp_new_comment(wp_slash($new_commentdata));
                if ($closedComment) {
                    add_comment_meta($new_comment_id, self::META_KEY_CLOSED, "1");
                }
                $form->saveCommentMeta($new_comment_id);
                $newComment = get_comment($new_comment_id);
                $held_moderate = 1;
                if ($newComment->comment_approved === "1") {
                    $held_moderate = 0;
                }
                if ($notificationType == WpdiscuzCore::SUBSCRIPTION_POST && class_exists("Prompt_Comment_Form_Handling") && $this->options->subscription["usePostmaticForCommentNotification"]) {
                    $_POST[Prompt_Comment_Form_Handling::SUBSCRIBE_CHECKBOX_NAME] = 1;
                    Prompt_Comment_Form_Handling::handle_form($new_comment_id, $newComment->comment_approved);
                } else if (!$isAnonymous && $notificationType) {
                    $subscriptionData = $this->dbManager->hasSubscription($postId, $email);
                    if (!$subscriptionData || ($subscriptionData && $subscriptionData["type"] === self::SUBSCRIPTION_COMMENT)) {
                        $noNeedMemberConfirm = ($currentUser->ID && !$this->options->subscription["enableMemberConfirm"]);
                        $noNeedGuestsConfirm = (!$currentUser->ID && !$this->options->subscription["enableGuestsConfirm"]);
                        if ($noNeedMemberConfirm || $noNeedGuestsConfirm) {
                            $this->dbManager->addEmailNotification($new_comment_id, $postId, $email, self::SUBSCRIPTION_COMMENT, 1);
                        } else {
                            $confirmData = $this->dbManager->addEmailNotification($new_comment_id, $postId, $email, self::SUBSCRIPTION_COMMENT);
                            if ($confirmData) {
                                $this->helperEmail->confirmEmailSender($confirmData["id"], $confirmData["activation_key"], $postId, $email);
                            }
                        }
                    }
                }
                $response = [];
                $response["redirect"] = $this->options->general["redirectPage"];
                $response["new_comment_id"] = $new_comment_id;
                $response["comment_author"] = $name;
                $response["comment_author_email"] = $email;
                $response["comment_author_url"] = $website_url;
                $response["is_main"] = class_exists("WooCommerce") && get_post_type($postId) === "product" ? 1 : ($comment_parent ? 0 : 1);
                $response["held_moderate"] = $held_moderate;
                $response["is_in_same_container"] = $isInSameContainer;
                $response["wc_all_comments_count_new"] = esc_html(get_comments_number($postId));
                $response["wc_all_comments_count_new_html"] = "<span class='wpdtc'>" . esc_html($response["wc_all_comments_count_new"]) . "</span> " . esc_html(1 == $response["wc_all_comments_count_new"] ? $form->getHeaderTextSingle() : $form->getHeaderTextPlural());

                $commentListArgs = $this->getCommentListArgs($postId);
                $commentListArgs["addComment"] = $commentDepth;
                $commentListArgs["comment_author_email"] = $email;
                if (apply_filters("wpdiscuz_enable_user_mentioning", true) && $this->options->subscription["enableUserMentioning"] && $this->options->subscription["sendMailToMentionedUsers"] && ($mentionedUsers = $this->helper->getMentionedUsers($newComment->comment_content))) {
                    $this->helperEmail->sendMailToMentionedUsers($mentionedUsers, $newComment);
                }
                $response["uniqueid"] = $uniqueId;
                $response["message"] = wp_list_comments($commentListArgs, [$newComment]);
                $response["message"] = wp_unslash($response["message"]);
                do_action("wpdiscuz_after_comment_post", $newComment, $currentUser);
                $response["callbackFunctions"] = [];
                $response = apply_filters("wpdiscuz_comment_post", $response);
                do_action("wpdiscuz_clean_post_cache", $postId, "comment_posted");
                wp_send_json_success($response);
            } else {
                wp_send_json_error("wc_invalid_field");
            }
        } else {
            wp_send_json_error("wc_msg_required_fields");
        }
    }

    /**
     * save edited comment via ajax
     */
    public function saveEditedComment() {
        $commentId = isset($_POST["commentId"]) ? intval($_POST["commentId"]) : 0;
        $trimmedContent = isset($_POST["wc_comment"]) ? trim($_POST["wc_comment"]) : "";
        if (!$trimmedContent || !strip_tags($trimmedContent)) {
            wp_send_json_error("wc_msg_required_fields");
        }
        $trimmedContent = ($this->options->form["richEditor"] === "both" || (!wp_is_mobile() && $this->options->form["richEditor"] === "desktop")) && !$this->options->showEditorToolbar() ? html_entity_decode($trimmedContent) : $trimmedContent;
        if ($commentId) {
            $this->isWpdiscuzLoaded = true;
            $comment = get_comment($commentId);
            $currentUser = WpdiscuzHelper::getCurrentUser();
            $uniqueId = $comment->comment_ID . "_" . $comment->comment_parent;
            $highLevelUser = current_user_can("moderate_comments");
            $isCurrentUserCanEdit = $this->helper->isCommentEditable($comment) && $this->helper->canUserEditComment($comment, $currentUser);
            if (!intval(get_comment_meta($comment->comment_ID, self::META_KEY_CLOSED, true)) && ($highLevelUser || $isCurrentUserCanEdit)) {
                $isInRange = $this->helper->isContentInRange($trimmedContent);

                if (!$isInRange && !$highLevelUser) {
                    $commentMinLength = intval($this->options->content["commentTextMinLength"]);
                    $commentMaxLength = intval($this->options->content["commentTextMaxLength"]);
                    $contentLength = function_exists("mb_strlen") ? mb_strlen(strip_tags($trimmedContent)) : strlen(strip_tags($trimmedContent));
                    if ($commentMinLength > 0 && $contentLength < $commentMinLength) {
                        wp_send_json_error("wc_msg_input_min_length");
                    }

                    if ($commentMaxLength > 0 && $contentLength > $commentMaxLength) {
                        wp_send_json_error("wc_msg_input_max_length");
                    }
                }

                if ($isInRange || $highLevelUser) {
                    $response = [];
                    $form = $this->wpdiscuzForm->getForm($comment->comment_post_ID);
                    $form->initFormFields();
                    $form->validateFields($currentUser);
                    if ($trimmedContent != $comment->comment_content) {
                        $trimmedContent = $this->helper->replaceCommentContentCode($trimmedContent);
                        $commentContent = $this->helper->filterCommentText($trimmedContent);
                        $userAgent = isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : "";
                        $commentarr = [
                            "comment_ID" => $commentId,
                            "comment_content" => $commentContent,
                            "comment_agent" => $userAgent,
                            "comment_approved" => $comment->comment_approved
                        ];
                        wp_update_comment(wp_slash($commentarr));
                        $lastEditedAt = current_time("mysql", 1);
                        update_comment_meta($commentId, self::META_KEY_LAST_EDITED_AT, $lastEditedAt);
                        update_comment_meta($commentId, self::META_KEY_LAST_EDITED_BY, !empty($currentUser->ID) ? $currentUser->ID : $comment->comment_author_email);
                        if ($this->options->moderation["displayEditingInfo"]) {
                            if (!empty($currentUser->ID)) {
                                $lastEditedBy = get_user_by("id", $currentUser->ID);
                            } else {
                                $lastEditedBy = get_user_by("email", $currentUser->comment_author_email);
                            }
                            $username = $lastEditedBy ? $lastEditedBy->display_name : $comment->comment_author;
                            $response["lastEdited"] = "<div class='wpd-comment-last-edited'><i class='far fa-edit'></i>" . esc_html(sprintf($this->options->phrases["wc_last_edited"], $this->helper->dateDiff($lastEditedAt), $username)) . "</div>";
                        }
                        do_action("wpdiscuz_clean_post_cache", $comment->comment_post_ID, "comment_edited");
                    }

                    $form->saveCommentMeta($comment->comment_ID);
                    $commentContent = isset($commentContent) ? $commentContent : $trimmedContent;
                    if ($this->options->content["enableImageConversion"]) {
                        $commentContent = $this->helper->makeClickable($commentContent);
                    }
                    $commentLink = get_comment_link($comment);
                    if ($this->options->social["enableTwitterShare"]) {
                        $response["twitterShareLink"] = esc_url_raw("https://twitter.com/intent/tweet?text=" . $this->helper->getTwitterShareContent($commentContent, $commentLink) . "&url=" . urlencode($commentLink));
                    }
                    if ($this->options->social["enableWhatsappShare"]) {
                        $response["whatsappShareLink"] = esc_url_raw((wp_is_mobile() ? "https://api.whatsapp.com" : "https://web.whatsapp.com") . "/send?text=" . $this->helper->getWhatsappShareContent($commentContent, $commentLink));
                    }
                    $commentContent = apply_filters("comment_text", $commentContent, $comment, ["is_wpdiscuz_comment" => true]);
                    $commentReadMoreLimit = $this->options->content["commentReadMoreLimit"];
                    if (stripos($commentContent, "[/spoiler]") !== false) {
                        $commentReadMoreLimit = 0;
                        $commentContent = $this->helper->spoiler($commentContent);
                    }
                    if ($commentReadMoreLimit && WpdiscuzHelper::strWordCount(wp_strip_all_tags($commentContent)) > $commentReadMoreLimit) {
                        $commentContent = WpdiscuzHelper::getCommentExcerpt($commentContent, $uniqueId, $this->options);
                    }
                    $commentContent = apply_filters("wpdiscuz_after_read_more", $commentContent, $comment, ["is_wpdiscuz_comment" => true]);

                    $components = $this->helper->getComponents($form->getTheme(), $form->getLayout());
                    $inlineContent = "";
                    if ($inlineFormID = intval(get_comment_meta($comment->comment_ID, self::META_KEY_FEEDBACK_FORM_ID, true))) {
                        $feedbackForm = $this->dbManager->getFeedbackForm($inlineFormID);
                        $inlineContent = "<div class='wpd-inline-feedback-wrapper'><span class='wpd-inline-feedback-info'>" . esc_html($this->options->phrases["wc_feedback_content_text"]) . "</span> <i class='fas fa-quote-left'></i>" . wp_trim_words($feedbackForm->content, apply_filters("wpdiscuz_feedback_content_words_count", 20)) . "&quot;  <a class='wpd-feedback-content-link' data-feedback-content-id='{$feedbackForm->id}' href='#wpd-inline-{$feedbackForm->id}'>" . esc_html($this->options->phrases["wc_read_more"]) . "</a></div>";
                    }
                    $response["message"] = str_replace(["{TEXT_WRAPPER_CLASSES}", "{TEXT}"], ["wpd-comment-text", $inlineContent . $commentContent], $components["text.html"]);
                    $response["callbackFunctions"] = [];
                    $response = apply_filters("wpdiscuz_comment_edit_save", $response);
                    wp_send_json_success($response);
                } else {
                    wp_send_json_error("wc_comment_edit_not_possible");
                }
            } else {
                wp_send_json_error("wc_comment_edit_not_possible");
            }
        }
    }

    /**
     * Gets single comment with its full thread and displays in comment list
     */
    public function getSingleComment() {
        $commentId = isset($_POST["commentId"]) ? intval($_POST["commentId"]) : 0;
        $comment = get_comment($commentId);
        $postId = isset($_POST["postId"]) ? intval($_POST["postId"]) : 0;
        if ($commentId && $postId && $comment && $comment->comment_post_ID == $postId) {
            $commentListArgs = $this->getCommentListArgs($postId);
            $this->commentsArgs = $this->getDefaultCommentsArgs($postId);
            $showUnapprovedComment = false;
            $includeUnapproved = null;
            if ($comment->comment_approved === "0") {
                if ($commentListArgs["high_level_user"]) {
                    $showUnapprovedComment = true;
                } else if (!empty($this->commentsArgs["include_unapproved"])) {
                    $includeUnapproved = $this->commentsArgs["include_unapproved"][0];
                    if (is_numeric($this->commentsArgs["include_unapproved"][0])) {
                        if ($comment->user_id == $this->commentsArgs["include_unapproved"][0]) {
                            $showUnapprovedComment = true;
                        }
                    } else if ($comment->comment_author_email == $this->commentsArgs["include_unapproved"][0]) {
                        $showUnapprovedComment = true;
                    }
                }
            }
            if ($comment->comment_approved === "1" || $showUnapprovedComment) {
                $this->isWpdiscuzLoaded = true;
                $commentStatusIn = ["1"];
                if ($this->commentsArgs["status"] === "all") {
                    $commentStatusIn[] = "0";
                }
                $parentComment = null;
                if (!$this->options->wp["threadComments"]) {
                    $comments = [$comment];
                    $parentComment = $comment;
                } else if ($parentComment = $this->helperOptimization->getCommentRoot($commentId, $commentStatusIn, $includeUnapproved)) {
                    $args = [
                        "format" => "flat",
                        "status" => $this->commentsArgs["status"],
                        "orderby" => $this->commentsArgs["orderby"],
                        "post_id" => $this->commentsArgs["post_id"],
                    ];
                    if (!empty($this->commentsArgs["include_unapproved"])) {
                        $args["include_unapproved"] = $this->commentsArgs["include_unapproved"];
                    }
                    $tree = $parentComment->get_children($args);
                    $comments = array_merge([$parentComment], $tree);
                }
                if ($parentComment) {
                    $commentListArgs["isSingle"] = true;
                    $commentListArgs["new_loaded_class"] = "wpd-new-loaded-comment";
                    if ($comments && $this->options->thread_layouts["highlightVotingButtons"]) {
                        if (!empty($commentListArgs['current_user']->ID)) {
                            $commentListArgs['user_votes'] = $this->dbManager->getUserVotes($comments, $commentListArgs['current_user']->ID);
                        } else {
                            $commentListArgs['user_votes'] = $this->dbManager->getUserVotes($comments, md5($this->helper->getRealIPAddr()));
                        }
                    }
                    $response = [];
                    $response["message"] = wp_list_comments($commentListArgs, $comments);
                    $response["parentCommentID"] = $parentComment->comment_ID;
                    $response["callbackFunctions"] = [];
                    $response = apply_filters("wpdiscuz_ajax_callbacks", $response);
                    wp_send_json_success($response);
                }
            }
        }
    }

    public function loadMoreComments() {
        $postId = isset($_POST["postId"]) ? intval($_POST["postId"]) : 0;
        $lastParentId = isset($_POST["lastParentId"]) ? intval($_POST["lastParentId"]) : 0;
        if ($lastParentId >= 0 && $postId) {
            $this->isWpdiscuzLoaded = true;
            $isFirstLoad = isset($_POST["isFirstLoad"]) ? intval($_POST["isFirstLoad"]) : 0;
            // max value of php int for limit
            $limit = ($isFirstLoad && $this->options->thread_display["commentListLoadType"] == 3) || (!$isFirstLoad && $this->options->thread_display["commentListLoadType"] == 1) ? PHP_INT_MAX - 1 : $this->options->wp["commentPerPage"];
            $args = ["number" => $limit];
            $args["wpdType"] = !empty($_POST["wpdType"]) ? trim($_POST["wpdType"]) : "";
            if ($isFirstLoad) {
                $args["first_load"] = true;
            }
            $sorting = isset($_POST["sorting"]) ? trim($_POST["sorting"]) : "";
            if ($sorting === "newest") {
                $args["orderby"] = $this->options->thread_display["orderCommentsBy"];
                $args["order"] = "desc";
            } else if ($sorting === "oldest") {
                $args["orderby"] = $this->options->thread_display["orderCommentsBy"];
                $args["order"] = "asc";
            } else if ($sorting === "by_vote") {
                $args["orderby"] = "by_vote";
                $args["order"] = $this->options->wp["commentOrder"];
            }
            if (isset($args["orderby"]) && $args["orderby"] === "by_vote" && !empty($_POST["offset"]) && ($offset = intval($_POST["offset"]))) {
                $args["offset"] = $offset * ($isFirstLoad && $this->options->thread_display["commentListLoadType"] == 3 ? 0 : $this->options->wp["commentPerPage"]);
            }
            $args["last_parent_id"] = $lastParentId;
            $args["post_id"] = $postId;
            $args = apply_filters("wpdiscuz_filter_args", $args);
            $commentData = $this->getWPComments($args);
            $commentData["loadLastCommentId"] = $this->dbManager->getLastCommentId($this->commentsArgs);
            $commentData["callbackFunctions"] = [];
            $commentData = apply_filters("wpdiscuz_ajax_callbacks", $commentData);
            wp_send_json_success($commentData);
        }
    }

    public function sorting() {
        $postId = isset($_POST["postId"]) ? intval($_POST["postId"]) : 0;
        $sorting = isset($_POST["sorting"]) ? trim($_POST["sorting"]) : "";
        if ($postId && $sorting) {
            $this->isWpdiscuzLoaded = true;
            $args = ["post_id" => $postId];
            if ($sorting === "newest") {
                $args["orderby"] = $this->options->thread_display["orderCommentsBy"];
                $args["order"] = "desc";
            } else if ($sorting === "oldest") {
                $args["orderby"] = $this->options->thread_display["orderCommentsBy"];
                $args["order"] = "asc";
            } else if ($sorting === "by_vote") {
                $args["orderby"] = "by_vote";
                $args["order"] = $this->options->wp["commentOrder"];
            }
            $args["first_load"] = 1;
            $args["wpdType"] = !empty($_POST["wpdType"]) ? trim($_POST["wpdType"]) : "";
            $args = apply_filters("wpdiscuz_filter_args", $args);
            $commentData = $this->getWPComments($args);
            $response = [
                "last_parent_id" => $commentData["last_parent_id"],
                "is_show_load_more" => $commentData["is_show_load_more"],
                "message" => $commentData["comment_list"],
                "callbackFunctions" => [],
            ];
            $response = apply_filters("wpdiscuz_ajax_callbacks", $response);
            wp_send_json_success($response);
        }
    }

    /**
     * get comments by comment type
     */
    public function getWPComments($args = []) {
        global $post;
        $postId = isset($args["post_id"]) ? $args["post_id"] : $post->ID;
        $defaults = $this->getDefaultCommentsArgs($postId);
        $this->commentsArgs = wp_parse_args($args, $defaults);
        $commentListArgs = $this->getCommentListArgs($postId);
        do_action("wpdiscuz_before_getcomments", $this->commentsArgs, $commentListArgs["current_user"], $args);
        $commentData = [];
        $commentList = $this->_getWPComments($commentListArgs, $commentData);
        $commentData["comment_list"] = wp_list_comments($commentListArgs, $commentList);
        $this->commentsArgs["caller"] = "";
        if ($this->cache->doGravatarsCache && $this->cache->gravatars) {
            $this->dbManager->addGravatars($this->cache->gravatars);
        }
        return $commentData;
    }

    public function _getWPComments(&$commentListArgs, &$commentData) {
        $commentList = [];
        if ($this->options->wp["isPaginate"]) {// PAGINATION
            $page = get_query_var("cpage");
            $this->commentsArgs["number"] = $this->options->wp["commentPerPage"];
            $this->commentsArgs["order"] = "asc";
            $this->commentsArgs["caller"] = "";
            if ($this->options->wp["threadComments"]) {
                $this->commentsArgs["parent"] = 0;
            }

            if ($page) {
                $this->commentsArgs["offset"] = ($page - 1) * $this->options->wp["commentPerPage"];
            } else if ($this->options->wp["defaultCommentsPage"] == "oldest") {
                $this->commentsArgs["offset"] = 0;
            }

            $commentListArgs["page"] = 0;
            $commentListArgs["per_page"] = 0;
            $commentListArgs["reverse_top_level"] = ($this->options->wp["commentOrder"] == "desc");

            $commentList = get_comments($this->commentsArgs);
            if ($this->options->wp["threadComments"] && $commentList) {
                $commentList = $this->getChildren($commentList, $commentListArgs);
            }
            $this->getStickyComments(true, $commentList, $commentListArgs);
        } else {
            $limitBefore = $this->commentsArgs["number"];
            if ($this->commentsArgs["number"]) {
                $this->commentsArgs["number"] += 1;
            }
            if ($this->commentsArgs["wpdType"] === "inline") {
                $this->commentsArgs["meta_query"] = [
                    [
                        "key" => self::META_KEY_FEEDBACK_FORM_ID,
                        "value" => 0,
                        "compare" => "!=",
                    ],
                ];
            }
            $commentList = get_comments($this->commentsArgs);
            $commentListCount = count($commentList);
            if ($limitBefore && $commentListCount > $limitBefore) {
                unset($commentList[$commentListCount - 1]);
                $commentListCount--;
                $commentData["is_show_load_more"] = true;
            } else {
                $commentData["is_show_load_more"] = false;
            }
            if ($commentList) {
                $commentData["last_parent_id"] = $commentList[$commentListCount - 1]->comment_ID;
                if ($this->options->wp["threadComments"]) {
                    $commentList = $this->getChildren($commentList, $commentListArgs);
                }
            } else {
                $commentData["last_parent_id"] = 0;
            }
            $this->getStickyComments(false, $commentList, $commentListArgs);
            $commentListArgs["page"] = 1;
            $commentListArgs["last_parent_id"] = $commentData["last_parent_id"];
        }
        if ($commentList && $this->options->thread_layouts["highlightVotingButtons"]) {
            if (!empty($commentListArgs['current_user']->ID)) {
                $commentListArgs['user_votes'] = $this->dbManager->getUserVotes($commentList, $commentListArgs['current_user']->ID);
            } else {
                $commentListArgs['user_votes'] = $this->dbManager->getUserVotes($commentList, md5($this->helper->getRealIPAddr()));
            }
        }
        return $commentList;
    }

    private function getChildren($commentList, &$commentListArgs) {
        $parentComments = [];
        $args = [
            "format" => "flat",
            "status" => $this->commentsArgs["status"],
            "orderby" => $this->commentsArgs["orderby"],
            "post_id" => $this->commentsArgs["post_id"],
        ];
        if (!empty($this->commentsArgs["include_unapproved"])) {
            $args["include_unapproved"] = $this->commentsArgs["include_unapproved"];
        }
        foreach ($commentList as $k => $parentComment) {
            $parentComments[] = $parentComment;
            $children = $parentComment->get_children($args);
            if ($this->options->thread_display["isLoadOnlyParentComments"]) {
                $commentListArgs["wpdiscuz_child_count_" . $parentComment->comment_ID] = count($children);
            } else {
                $parentComments = array_merge($parentComments, $children);
            }
        }
        return $parentComments;
    }

    public function commentsTemplateQueryArgs($args) {
        global $post;
        if ($this->isWpdiscuzLoaded) {
            if ($this->options->wp["isPaginate"]) {
                $args["caller"] = "wpdiscuz";
            } else {
                $args["post__not_in"] = $post->ID;
            }
        }
        return $args;
    }

    public function preGetComments($queryObj) {
        if (isset($this->commentsArgs["caller"]) && $this->commentsArgs["caller"] === "wpdiscuz-") {
            $vars = $queryObj->query_vars;
            $vars["comment__in"] = "";
            $queryObj->query_vars = $vars;
        }
    }

    public function foundCommentsQuery($q, $qObj) {
        if ($this->options->wp["isPaginate"] && isset($qObj->query_vars["caller"]) && $qObj->query_vars["caller"] === "wpdiscuz" && empty($this->commentsArgs["sticky"])) {
            global $wpdb, $post;
            $where = "WHERE";
            if (!empty($qObj->query_vars["include_unapproved"][0])) {
                if (is_numeric($qObj->query_vars["include_unapproved"][0])) {
                    $where .= " (comment_approved = '1' OR " . $wpdb->prepare("(user_id = %d AND comment_approved = '0')", $qObj->query_vars["include_unapproved"][0]) . ")";
                } else {
                    $where .= " (comment_approved = '1' OR " . $wpdb->prepare("(comment_author_email = %s AND comment_approved = '0')", $qObj->query_vars["include_unapproved"][0]) . ")";
                }
            } else {
                $where .= " comment_approved = '1'";
            }
            $where .= " AND comment_post_ID = {$post->ID}";
            if ($this->options->wp["threadComments"]) {
                $where .= " AND comment_parent = 0";
            }
            $typesNotIn = apply_filters("wpdiscuz_found_comments_query", [self::WPDISCUZ_STICKY_COMMENT]);
            foreach ($typesNotIn as $k => &$type) {
                $type = esc_sql($type);
            }
            $where .= " AND comment_type NOT IN ('" . implode("','", $typesNotIn) . "')";
            $q = "SELECT COUNT(*) FROM {$wpdb->comments} $where";
        }
        return $q;
    }

    /**
     * add comments clauses
     * add new orderby clause when sort type is vote and wordpress comments order is older (ASC)
     */
    public function commentsClauses($args) {
        global $wpdb;
        if (isset($this->commentsArgs["caller"]) && $this->commentsArgs["caller"] === "wpdiscuz" && !$this->options->wp["isPaginate"]) {
            $orderby = "";
            $args["caller"] = $this->commentsArgs["caller"] = "wpdiscuz-";
            if ($this->options->thread_layouts["showVotingButtons"] && $this->commentsArgs["orderby"] === "by_vote") {
                $args["join"] .= " LEFT JOIN " . $wpdb->commentmeta . " AS `cm` ON " . $wpdb->comments . ".comment_ID = `cm`.comment_id  AND (`cm`.meta_key = '" . self::META_KEY_VOTES . "')";
                $orderby = " IFNULL(`cm`.meta_value,0)+0 DESC, ";
            } else if ($this->commentsArgs["last_parent_id"] && empty($this->commentsArgs["sticky"])) {
                $args["where"] = $wpdb->comments . ".`comment_ID`" . ($this->commentsArgs["order"] === 'desc' ? " < " : " > ") . $this->commentsArgs["last_parent_id"] . ($args["where"] ? " AND " : "") . $args["where"];
            }
            $args["orderby"] = $orderby . $wpdb->comments . ".`{$this->options->thread_display["orderCommentsBy"]}` ";
            $args["orderby"] .= isset($args["order"]) ? "" : $this->commentsArgs["order"];
        }
        return $args;
    }

    public function getDefaultCommentsArgs($postId = 0) {
        global $user_ID;
        $commenter = wp_get_current_commenter();
        $args = [
            "caller" => "wpdiscuz",
            "post_id" => intval($postId),
            "last_parent_id" => 0,
            "orderby" => $this->options->thread_display["orderCommentsBy"],
            "order" => $this->options->wp["commentOrder"],
            // max value of php int for limit
            "number" => $this->options->thread_display["commentListLoadType"] == 3 ? PHP_INT_MAX - 1 : $this->options->wp["commentPerPage"],
            "status" => !$this->options->wp["isPaginate"] && current_user_can("moderate_comments") ? "all" : "approve",
            "update_comment_meta_cache" => false,
            "no_found_rows" => false,
            "type__not_in" => [self::WPDISCUZ_STICKY_COMMENT],
            "wpdType" => "",
        ];
        if ($this->options->wp["threadComments"]) {
            $args["parent"] = 0;
        }
        if (!current_user_can("moderate_comments")) {
            if ($user_ID) {
                $args["include_unapproved"] = [$user_ID];
            } elseif (!empty($commenter["comment_author_email"])) {
                $args["include_unapproved"] = [$commenter["comment_author_email"]];
            }
        }
        return apply_filters("wpdiscuz_comments_args", $args);
    }

    /**
     * register options page for plugin
     */
    public function addPluginOptionsPage() {
        add_menu_page("wpDiscuz", "wpDiscuz", "manage_options", self::PAGE_WPDISCUZ, "", "dashicons-admin-comments", 26);
        add_submenu_page(self::PAGE_WPDISCUZ, "&raquo; " . esc_html__("Dashboard", "wpdiscuz"), "&raquo; " . esc_html__("Dashboard", "wpdiscuz"), "manage_options", self::PAGE_WPDISCUZ, [&$this->options, "dashboard"]);
        add_submenu_page(self::PAGE_WPDISCUZ, "&raquo; " . esc_html__("Settings", "wpdiscuz"), "&raquo; " . esc_html__("Settings", "wpdiscuz"), "manage_options", self::PAGE_SETTINGS, [&$this->options, "mainOptionsForm"]);
        if (!$this->options->general["isUsePoMo"]) {
            add_submenu_page(self::PAGE_WPDISCUZ, "&raquo; " . esc_html__("Phrases", "wpdiscuz"), "&raquo; " . esc_html__("Phrases", "wpdiscuz"), "manage_options", self::PAGE_PHRASES, [&$this->options, "phrasesOptionsForm"]);
        }
        add_submenu_page(self::PAGE_WPDISCUZ, "&raquo; " . esc_html__("Tools", "wpdiscuz"), "&raquo; " . esc_html__("Tools", "wpdiscuz"), "manage_options", self::PAGE_TOOLS, [&$this->options, "tools"]);
        add_submenu_page(self::PAGE_WPDISCUZ, "&raquo; " . esc_html__("Addons", "wpdiscuz"), "&raquo; " . esc_html__("Addons", "wpdiscuz"), "manage_options", self::PAGE_ADDONS, [&$this->options, "addons"]);
    }

    /**
     * Scripts and styles registration on administration pages
     */
    public function backendFiles() {
        global $typenow, $pagenow;
        $wp_version = get_bloginfo("version");
        $wpdiscuzWpPages = apply_filters("wpdiscuz_wp_admin_pages", ["edit-comments.php", "admin.php", "comment.php"]);
        $wpdiscuzPages = apply_filters("wpdiscuz_admin_pages", [self::PAGE_WPDISCUZ, self::PAGE_SETTINGS, self::PAGE_PHRASES, self::PAGE_TOOLS, self::PAGE_ADDONS]);
        wp_register_style("wpdiscuz-font-awesome", plugins_url(WPDISCUZ_DIR_NAME . "/assets/third-party/font-awesome-5.13.0/css/fontawesome-all.min.css"), null, $this->version);

        if ((isset($_GET["page"]) && in_array($_GET["page"], $wpdiscuzPages) && in_array($pagenow, $wpdiscuzWpPages)) || ($typenow == "wpdiscuz_form") || ($pagenow == self::PAGE_COMMENTS)) {
            $args = [
                "msgConfirmResetOptions" => esc_html__("Do you really want to reset all options?", "wpdiscuz"),
                "msgConfirmResetTabOptions" => esc_html__("Do you really want to reset tab options?", "wpdiscuz"),
                "msgConfirmRemoveVotes" => esc_html__("Do you really want to remove voting data?", "wpdiscuz"),
                "msgConfirmResetPhrases" => esc_html__("Do you really want to reset phrases?", "wpdiscuz"),
                "wmuMsgConfirmAttachmentDelete" => esc_html__("Do you really want to delet this attachment?", "wpdiscuz"),
                "msgConfirmPurgeGravatarsCache" => esc_html__("Do you really want to delete gravatars cache?", "wpdiscuz"),
            ];
            // Media Upload Lightbox
            wp_register_style("wmu-colorbox-css", plugins_url(WPDISCUZ_DIR_NAME . "/assets/third-party/colorbox/colorbox.css"));
            wp_enqueue_style("wmu-colorbox-css");
            wp_register_script("wmu-colorbox-js", plugins_url(WPDISCUZ_DIR_NAME . "/assets/third-party/colorbox/jquery.colorbox.min.js"), ["jquery"]);
            wp_enqueue_script("wmu-colorbox-js");

            wp_register_style("wpdiscuz-mu-css", plugins_url(WPDISCUZ_DIR_NAME . "/assets/css/wpdiscuz-mu-backend.css"));
            wp_enqueue_style("wpdiscuz-mu-css");
            wp_register_script("wpdiscuz-mu-js", plugins_url(WPDISCUZ_DIR_NAME . "/assets/js/wpdiscuz-mu-backend.js"), ["jquery"], $this->version, true);
            wp_localize_script("wpdiscuz-mu-js", "wpdiscuzMUJsObj", $args);
            wp_enqueue_script("wpdiscuz-mu-js");

            wp_enqueue_style("wpdiscuz-font-awesome");
            wp_register_style("wpdiscuz-cp-index-css", plugins_url(WPDISCUZ_DIR_NAME . "/assets/third-party/colorpicker/css/index.css"), null, $this->version);
            wp_enqueue_style("wpdiscuz-cp-index-css");
            wp_register_style("wpdiscuz-cp-compatibility-css", plugins_url(WPDISCUZ_DIR_NAME . "/assets/third-party/colorpicker/css/compatibility.css"), null, $this->version);
            wp_enqueue_style("wpdiscuz-cp-compatibility-css");
            wp_register_script("wpdiscuz-cp-colors-js", plugins_url(WPDISCUZ_DIR_NAME . "/assets/third-party/colorpicker/js/colors.js"), ["jquery"], $this->version, false);
            wp_enqueue_script("wpdiscuz-cp-colors-js");
            wp_register_script("wpdiscuz-cp-colorpicker-js", plugins_url(WPDISCUZ_DIR_NAME . "/assets/third-party/colorpicker/js/jqColorPicker.min.js"), ["jquery"], $this->version, false);
            wp_enqueue_script("wpdiscuz-cp-colorpicker-js");
            wp_register_script("wpdiscuz-cp-index-js", plugins_url(WPDISCUZ_DIR_NAME . "/assets/third-party/colorpicker/js/index.js"), ["jquery"], $this->version, false);
            wp_enqueue_script("wpdiscuz-cp-index-js");
            wp_register_style("wpdiscuz-options-css", plugins_url(WPDISCUZ_DIR_NAME . "/assets/css/wpdiscuz-options.css"), null, $this->version);
            wp_enqueue_style("wpdiscuz-options-css");
            wp_register_script("wpdiscuz-options-js", plugins_url(WPDISCUZ_DIR_NAME . "/assets/js/wpdiscuz-options.js"), ["jquery"], $this->version);
            wp_enqueue_script("wpdiscuz-options-js");
            wp_localize_script("wpdiscuz-options-js", "wpdiscuzObj", $args);
            wp_enqueue_script("thickbox");
            wp_register_script("wpdiscuz-contenthover", plugins_url(WPDISCUZ_DIR_NAME . "/assets/third-party/contenthover/jquery.contenthover.min.js"), ["jquery"], $this->version, true);
            wp_enqueue_script("wpdiscuz-contenthover");

            if (isset($_GET["page"])) {
                wp_register_style("wpdiscuz-easy-responsive-tabs-css", plugins_url(WPDISCUZ_DIR_NAME . "/assets/third-party/easy-responsive-tabs/css/easy-responsive-tabs.min.css"), null, $this->version);
                wp_enqueue_style("wpdiscuz-easy-responsive-tabs-css");
                wp_register_script("wpdiscuz-easy-responsive-tabs-js", plugins_url(WPDISCUZ_DIR_NAME . "/assets/third-party/easy-responsive-tabs/js/easy-responsive-tabs.js"), ["jquery"], $this->version, true);
                wp_enqueue_script("wpdiscuz-easy-responsive-tabs-js");
                wp_register_script("wpdiscuz-jquery-cookie", plugins_url(WPDISCUZ_DIR_NAME . "/assets/third-party/wpdccjs/wpdcc.js"), ["jquery"], $this->version, true);
                wp_enqueue_script("wpdiscuz-jquery-cookie");
                wp_register_script("wpdiscuz-chart-js", plugins_url(WPDISCUZ_DIR_NAME . "/assets/third-party/chart/chart.min.js"), [], $this->version, true);
                wp_enqueue_script("wpdiscuz-chart-js");
            }
        } else if ($pagenow == "comment.php") {
            wp_register_style("wpdiscuz-options-css", plugins_url(WPDISCUZ_DIR_NAME . "/assets/css/wpdiscuz-options.css"), null, $this->version);
            wp_enqueue_style("wpdiscuz-options-css");
            wp_enqueue_style("wpdiscuz-font-awesome");
        }
        if (version_compare($wp_version, "4.2.0", ">=")) {
            wp_register_script("wpdiscuz-addon-notes", plugins_url(WPDISCUZ_DIR_NAME . "/assets/js/wpdiscuz-notes.js"), ["jquery"], $this->version, true);
            wp_enqueue_script("wpdiscuz-addon-notes");
        }

        if (!get_option(self::OPTION_SLUG_DEACTIVATION) && (strpos($this->requestUri, "/plugins.php") !== false)) {
            $reasonArgs = [
                "msgReasonRequired" => esc_html__("Please check one of reasons before sending feedback!", "wpdiscuz"),
                "msgReasonDescRequired" => esc_html__("Please provide more information", "wpdiscuz"),
                "adminUrl" => get_admin_url()
            ];
            wp_register_style("wpdiscuz-lity-css", plugins_url(WPDISCUZ_DIR_NAME . "/assets/third-party/lity/lity.css"), null, $this->version);
            wp_enqueue_style("wpdiscuz-lity-css");
            wp_register_script("wpdiscuz-lity-js", plugins_url(WPDISCUZ_DIR_NAME . "/assets/third-party/lity/lity.js"), ["jquery"], $this->version);
            wp_enqueue_script("wpdiscuz-lity-js");
            wp_register_style("wpdiscuz-deactivation-css", plugins_url(WPDISCUZ_DIR_NAME . "/assets/css/wpdiscuz-deactivation.css"));
            wp_enqueue_style("wpdiscuz-deactivation-css");
            wp_register_script("wpdiscuz-deactivation-js", plugins_url(WPDISCUZ_DIR_NAME . "/assets/js/wpdiscuz-deactivation.js"), ["jquery"], $this->version);
            wp_enqueue_script("wpdiscuz-deactivation-js");
            wp_localize_script("wpdiscuz-deactivation-js", "deactivationObj", $reasonArgs);
        }
        wp_register_script(self::WPDISCUZ_FEEDBACK_SHORTCODE . "-shortcode-js", null);
        wp_enqueue_script(self::WPDISCUZ_FEEDBACK_SHORTCODE . "-shortcode-js");
        wp_localize_script(self::WPDISCUZ_FEEDBACK_SHORTCODE . "-shortcode-js", "wpdObject", ["ajaxUrl" => admin_url("admin-ajax.php"), "shortcode" => self::WPDISCUZ_FEEDBACK_SHORTCODE, "image" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/shortcode.png"), "tooltip" => $this->options->phrases["wc_feedback_shortcode_tooltip"], "popup_title" => $this->options->phrases["wc_feedback_popup_title"], "leave_feebdack" => $this->options->phrases["wc_please_leave_feebdack"], "no_text_selected" => esc_html__("No text is selected. Please select a part of text from post content.", "wpdiscuz")]);
    }

    /**
     * Styles and scripts registration to use on front page
     */
    public function frontendFiles() {
        global $post;
        $this->isWpdiscuzLoaded = $this->helper->isLoadWpdiscuz($post);
        $suf = $this->options->general["loadMinVersion"] ? ".min" : "";
        wp_register_style("wpdiscuz-font-awesome", plugins_url(WPDISCUZ_DIR_NAME . "/assets/third-party/font-awesome-5.13.0/css/fontawesome-all.min.css"), null, $this->version);
        wp_register_style("wpdiscuz-ratings", plugins_url(WPDISCUZ_DIR_NAME . "/assets/css/wpdiscuz-ratings$suf.css"), null, $this->version);
        wp_register_style("wpdiscuz-ratings-rtl", plugins_url(WPDISCUZ_DIR_NAME . "/assets/css/wpdiscuz-ratings-rtl$suf.css"), null, $this->version);
        if (!$this->isWpdiscuzLoaded && $this->options->rating["ratingCssOnNoneSingular"]) {
            wp_enqueue_style("wpdiscuz-ratings");
            if (is_rtl()) {
                wp_enqueue_style("wpdiscuz-ratings-rtl");
            }
        }
        if ($this->isWpdiscuzLoaded) {
            $this->form = $this->wpdiscuzForm->getForm($post->ID);
            $this->form->initFormMeta();
            $this->wpdiscuzOptionsJs = $this->options->getOptionsForJs();
            $this->wpdiscuzOptionsJs["version"] = $this->version;
            $this->wpdiscuzOptionsJs["wc_post_id"] = $post->ID;
            $this->wpdiscuzOptionsJs["loadLastCommentId"] = 0;
            $this->wpdiscuzOptionsJs["isCookiesEnabled"] = has_action("set_comment_cookies");
            if ($this->options->live["commentListUpdateType"] || ($this->options->live["enableBubble"] && $this->options->live["bubbleLiveUpdate"])) {
                $cArgs = $this->getDefaultCommentsArgs($post->ID);
                $this->wpdiscuzOptionsJs["loadLastCommentId"] = $this->dbManager->getLastCommentId($cArgs);
            }
            if ($this->form->showRecaptcha() && apply_filters("wpdiscuz_recaptcha_site_key", $this->options->recaptcha["siteKey"]) && apply_filters("wpdiscuz_recaptcha_secret", $this->options->recaptcha["secretKey"])) {
                wp_register_script("wpdiscuz-google-recaptcha", apply_filters("wpdiscuz_recaptcha_url", $this->options->recaptcha["reCaptchaUrl"]), null, "1.0.0", true);
                wp_enqueue_script("wpdiscuz-google-recaptcha");
            }
            $this->wpdiscuzOptionsJs["dataFilterCallbacks"] = [];
            $this->wpdiscuzOptionsJs = apply_filters("wpdiscuz_js_options", $this->wpdiscuzOptionsJs, $this->options);
            $this->wpdiscuzOptionsJs["url"] = admin_url("admin-ajax.php");
            $this->wpdiscuzOptionsJs["customAjaxUrl"] = plugins_url(WPDISCUZ_DIR_NAME . "/utils/ajax/wpdiscuz-ajax.php");
            $this->wpdiscuzOptionsJs["bubbleUpdateUrl"] = rest_url("wpdiscuz/v1/update");
            $loadQuill = $this->options->form["richEditor"] === "both" || (!wp_is_mobile() && $this->options->form["richEditor"] === "desktop");
            $customCSSSlug = "wpdiscuz-frontend-custom-css";
            $customFileName = "style-custom";
            if (is_rtl()) {
                $customCSSSlug = "wpdiscuz-frontend-custom-rtl-css";
                $customFileName = "style-custom-rtl";
                if ($this->options->thread_styles["theme"] === "wpd-minimal") {
                    $cssSlug = "wpdiscuz-frontend-minimal-rtl-css";
                    $fileName = "style-minimal-rtl";
                } else {
                    $cssSlug = "wpdiscuz-frontend-rtl-css";
                    $fileName = "style-rtl";
                }
            } else if ($this->options->thread_styles["theme"] === "wpd-minimal") {
                $cssSlug = "wpdiscuz-frontend-minimal-css";
                $fileName = "style-minimal";
            } else {
                $cssSlug = "wpdiscuz-frontend-css";
                $fileName = "style";
            }
            $this->helper->enqueueWpDiscuzStyle($cssSlug, $fileName, $this->version, $this->form);
            $this->helper->enqueueWpDiscuzStyle($customCSSSlug, $customFileName, $this->version, $this->form);
            wp_add_inline_style($cssSlug, $this->helper->initCustomCss());
            $ucArgs = [
                "msgConfirmDeleteComment" => esc_html($this->options->phrases["wc_confirm_comment_delete"]),
                "msgConfirmCancelSubscription" => esc_html($this->options->phrases["wc_confirm_cancel_subscription"]),
                "msgConfirmCancelFollow" => esc_html($this->options->phrases["wc_confirm_cancel_follow"]),
                "additionalTab" => (int) apply_filters("wpdiscuz_enable_content_modal", false),
            ];
            if ($this->options->thread_styles["enableFontAwesome"]) {
                if ($this->form->hasIcon) {
                    wp_enqueue_style("wpdiscuz-font-awesome");
                } else {
                    wp_register_style("wpdiscuz-fa", plugins_url(WPDISCUZ_DIR_NAME . "/assets/third-party/font-awesome-5.13.0/css/fa.min.css"), null, $this->version);
                    wp_enqueue_style("wpdiscuz-fa");
                }
            }
            if ($this->options->general["loadComboVersion"]) {
                $combo_js = "";
                $combo_css = "";
                if (!$loadQuill) {
                    $combo_js .= "-no_quill";
                    $combo_css .= "-no_quill";
                }
                wp_register_style("wpdiscuz-combo-css", plugins_url(WPDISCUZ_DIR_NAME . "/assets/css/wpdiscuz-combo$combo_css.min.css"));
                wp_enqueue_style("wpdiscuz-combo-css");
                wp_register_script("wpdiscuz-combo-js", plugins_url(WPDISCUZ_DIR_NAME . "/assets/js/wpdiscuz-combo$combo_js.min.js"), ["jquery"], $this->version, true);
                wp_enqueue_script("wpdiscuz-combo-js");
                wp_localize_script("wpdiscuz-combo-js", "wpdiscuzAjaxObj", $this->wpdiscuzOptionsJs);
                wp_localize_script("wpdiscuz-combo-js", "wpdiscuzUCObj", $ucArgs);
                if ($loadQuill) {
                    wp_add_inline_script("wpdiscuz-combo$combo_js-js", $this->options->editorOptions(), "before");
                }
            } else {
                wp_register_script("wpdiscuz-cookie-js", plugins_url(WPDISCUZ_DIR_NAME . "/assets/third-party/wpdccjs/wpdcc.js"), ["jquery"], $this->version, true);
                wp_enqueue_script("wpdiscuz-cookie-js");
                //
                if ($loadQuill) {
                    wp_register_style("quill-css", plugins_url(WPDISCUZ_DIR_NAME . "/assets/third-party/quill/quill.snow$suf.css"), null, "1.3.6");
                    wp_enqueue_style("quill-css");
                    wp_register_script("quill", plugins_url("/assets/third-party/quill/quill$suf.js", __FILE__), ["wpdiscuz-cookie-js"], "1.3.6", true);
                    wp_enqueue_script("quill");
                    wp_add_inline_script("quill", $this->options->editorOptions(), "before");
                    wp_register_script("wpd-editor", plugins_url("/assets/js/wpd-editor$suf.js", __FILE__), ["quill"], "1.3.6", true);
                    wp_enqueue_script("wpd-editor");
                }
                wp_register_script("autogrowtextarea-js", plugins_url("/assets/third-party/autogrow/jquery.autogrowtextarea.min.js", __FILE__), ["jquery"], "1.3.6", true);
                wp_enqueue_script("autogrowtextarea-js");
                wp_register_script("wpdiscuz-ajax-js", plugins_url(WPDISCUZ_DIR_NAME . "/assets/js/wpdiscuz$suf.js"), [$loadQuill ? "wpd-editor" : "jquery"], $this->version, true);
                wp_enqueue_script("wpdiscuz-ajax-js");
                wp_localize_script("wpdiscuz-ajax-js", "wpdiscuzAjaxObj", $this->wpdiscuzOptionsJs);
                //
                if ($this->options->content["wmuIsEnabled"]) {
                    // Media Upload Lightbox
                    if ($this->options->content["wmuIsLightbox"]) {
                        wp_register_style("wmu-colorbox-css", plugins_url(WPDISCUZ_DIR_NAME . "/assets/third-party/colorbox/colorbox$suf.css"));
                        wp_enqueue_style("wmu-colorbox-css");
                        wp_register_script("wmu-colorbox-js", plugins_url(WPDISCUZ_DIR_NAME . "/assets/third-party/colorbox/jquery.colorbox$suf.js"), ["jquery"], $this->version, true);
                        wp_enqueue_script("wmu-colorbox-js");
                    }
                    wp_register_style("wpdiscuz-mu-css", plugins_url(WPDISCUZ_DIR_NAME . "/assets/css/wpdiscuz-mu-frontend$suf.css"));
                    wp_enqueue_style("wpdiscuz-mu-css");
                    wp_register_script("wpdiscuz-mu-js", plugins_url(WPDISCUZ_DIR_NAME . "/assets/js/wpdiscuz-mu-frontend$suf.js"), ["wpdiscuz-ajax-js"], $this->version, true);
                    wp_enqueue_script("wpdiscuz-mu-js");
                }
                if ($this->options->login["showActivityTab"] || $this->options->login["showSubscriptionsTab"] || $this->options->login["showFollowsTab"] || apply_filters("wpdiscuz_enable_content_modal", false)) {
                    wp_register_style("wpdiscuz-user-content-css", plugins_url(WPDISCUZ_DIR_NAME . "/assets/css/wpdiscuz-user-content$suf.css"), null, $this->version);
                    wp_enqueue_style("wpdiscuz-user-content-css");
                    wp_register_script("wpdiscuz-lity-js", plugins_url(WPDISCUZ_DIR_NAME . "/assets/third-party/lity/lity$suf.js"), ["jquery"], $this->version, true);
                    wp_enqueue_script("wpdiscuz-lity-js");
                    wp_register_script("wpdiscuz-user-content-js", plugins_url(WPDISCUZ_DIR_NAME . "/assets/js/wpdiscuz-user-content$suf.js"), ["wpdiscuz-lity-js"], $this->version, true);
                    wp_enqueue_script("wpdiscuz-user-content-js");
                    wp_localize_script("wpdiscuz-user-content-js", "wpdiscuzUCObj", $ucArgs);
                }
            }
            if (!$loadQuill && $this->options->form["enableQuickTags"]) {
                wp_enqueue_script("quicktags");
                wp_register_script("wpdiscuz-quicktags", plugins_url("/assets/third-party/quicktags/wpdiscuz-quictags.js", __FILE__), [$this->options->general["loadComboVersion"] ? "wpdiscuz-combo-js" : "wpdiscuz-ajax-js"], $this->version, true);
                wp_enqueue_script("wpdiscuz-quicktags");
            }
            do_action("wpdiscuz_front_scripts", $this->options);
        }
    }

    public function pluginNewVersion() {
        $pluginData = get_plugin_data(__FILE__);
        if (version_compare($pluginData["Version"], $this->version, ">")) {
            $this->dbManager->dbCreateTables();
            $this->wpdiscuzForm->createDefaultForm($this->version);
            $options = $this->changeOldOptions(get_option(self::OPTION_SLUG_OPTIONS), $pluginData);
            if ($this->version === "5.3.2") {
                $this->mergeOptions($options);
            } else {
                $this->addNewOptions($options);
            }
            $this->addNewPhrases();
            update_option(self::OPTION_SLUG_VERSION, $pluginData["Version"]);

            if (version_compare($this->version, "2.1.2", "<=") && version_compare($this->version, "1.0.0", "!=")) {
                $this->dbManager->alterPhrasesTable();
            }

            if (version_compare($this->version, "2.1.7", "<=") && version_compare($this->version, "1.0.0", "!=")) {
                $this->dbManager->alterVotingTable();
            }

            if (version_compare($this->version, "5.0.5", "<=") && version_compare($this->version, "1.0.0", "!=")) {
                $this->dbManager->alterNotificationTable();
            }

            if (version_compare($this->version, "5.1.2", "<=")) {
                $this->dbManager->deleteOldStatisticCaches();
            }

            if (version_compare($this->version, "7.0.0", "<")) {
                $this->deactivateAddons();
                if (version_compare($this->version, "1.0.0", "!=") && version_compare($this->version, "7.0.0-beta-1", "<")) {
                    $this->dbManager->alterVotingTableForDateAndPostId();
                    $this->options->replaceOldOptions($options);
                }
                $this->setRecaptchaOptions($options);
                $showVoteRegenerate = intval($this->dbManager->showVoteRegenerate());
                add_option(self::OPTION_SLUG_SHOW_VOTE_REG_MESSAGE, ($showVoteRegenerate ? "1" : "0"), "", "no");
                $showClosedRegenerate = intval($this->dbManager->showClosedRegenerate());
                add_option(self::OPTION_SLUG_SHOW_CLOSED_REG_MESSAGE, ($showClosedRegenerate ? "1" : "0"), "", "no");
                $showVoteDataRegenerate = intval($this->dbManager->showVoteDataRegenerate());
                add_option(self::OPTION_SLUG_SHOW_VOTE_DATA_REG_MESSAGE, ($showVoteDataRegenerate ? "1" : "0"), "", "no");
                add_option(self::OPTION_SLUG_SHOW_SYNC_COMMENTERS_MESSAGE, ($this->dbManager->usersHaveComments() ? "1" : "0"), "", "no");
                add_option(self::OPTION_SLUG_WIZARD_COMPLETED, "0", "", "no");
                add_option(self::OPTION_SLUG_WIZARD_AFTER_UPDATE, ($this->version === "1.0.0" ? "0" : "1"), "", "no");
                $this->removeOldFiles();
                $this->dbManager->deleteStatisticCaches();
                if (($advancedNocaptcha = get_option("anr_admin_options")) && !empty($advancedNocaptcha["enabled_forms"]) && ($key = array_search("comment", $advancedNocaptcha["enabled_forms"]))) {
                    unset($advancedNocaptcha["enabled_forms"][$key]);
                    update_option("anr_admin_options", $advancedNocaptcha);
                }
                if (($invisibleRecaptcha = get_option("ic-wordpress-settings")) && !empty($invisibleRecaptcha["CF"])) {
                    unset($invisibleRecaptcha["CF"]);
                    update_option("ic-wordpress-settings", $invisibleRecaptcha);
                }
            }
            if (version_compare($this->version, "7.0.3", "<") && version_compare($this->version, "1.0.0", "!=")) {
                $this->dbManager->alterSubscriptionTable();
            }
            do_action("wpdiscuz_clean_all_caches", $pluginData["Version"], $this->version);
        }
        do_action("wpdiscuz_check_version");
    }

    /**
     * remove old captcha files
     */
    private function removeOldFiles() {
        $wpUploadsDir = wp_upload_dir();
        $captchaDir = $wpUploadsDir["basedir"] . "/wpdiscuz/captcha/";
        if (file_exists($captchaDir)) {
            $files = function_exists("scandir") ? scandir($captchaDir) : false;
            if ($files && is_array($files)) {
                foreach ($files as $k => $file) {
                    if ($file != "." && $file != "..") {
                        $fileName = $captchaDir . $file;
                        if (is_file($fileName)) {
                            @unlink($fileName);
                        } else {
                            @rmdir($fileName);
                        }
                    }
                }
            }
            @rmdir($captchaDir);
        }
    }

    private function deactivateAddons() {
        $plugins = [];
        if (is_plugin_active("wpdiscuz-ads-manager/class-WpdiscuzAdsManager.php")) {
            $plugins[] = "wpdiscuz-ads-manager/class-WpdiscuzAdsManager.php";
        }
        if (is_plugin_active("wpdiscuz-advanced-likers/class.WpdiscuzVoters.php")) {
            $plugins[] = "wpdiscuz-advanced-likers/class.WpdiscuzVoters.php";
        }
        if (is_plugin_active("wpdiscuz-comment-author-info/wpdiscuz-comment-author-info.php")) {
            $plugins[] = "wpdiscuz-comment-author-info/wpdiscuz-comment-author-info.php";
        }
        if (is_plugin_active("wpdiscuz-comment-search/wpDiscuzCommentSearch.php")) {
            $plugins[] = "wpdiscuz-comment-search/wpDiscuzCommentSearch.php";
        }
        if (is_plugin_active("wpdiscuz-comment-translation/wpdiscuz-translate.php")) {
            $plugins[] = "wpdiscuz-comment-translation/wpdiscuz-translate.php";
        }
        if (is_plugin_active("wpdiscuz-emoticons/wpDiscuzSmile.php")) {
            $plugins[] = "wpdiscuz-emoticons/wpDiscuzSmile.php";
        }
        if (is_plugin_active("wpdiscuz-frontend-moderation/class.wpDiscuzFrontEndModeration.php")) {
            $plugins[] = "wpdiscuz-frontend-moderation/class.wpDiscuzFrontEndModeration.php";
        }
        if (is_plugin_active("wpdiscuz-media-uploader/class.WpdiscuzMediaUploader.php")) {
            $plugins[] = "wpdiscuz-media-uploader/class.WpdiscuzMediaUploader.php";
        }
        if (is_plugin_active("wpdiscuz-mycred/wpdiscuz-mc.php")) {
            $plugins[] = "wpdiscuz-mycred/wpdiscuz-mc.php";
        }
        if (is_plugin_active("wpdiscuz-online-users/wpdiscuz-ou.php")) {
            $plugins[] = "wpdiscuz-online-users/wpdiscuz-ou.php";
        }
        if (is_plugin_active("wpdiscuz-recaptcha/wpDiscuzReCaptcha.php")) {
            $plugins[] = "wpdiscuz-recaptcha/wpDiscuzReCaptcha.php";
        }
        if (is_plugin_active("wpdiscuz-report-flagging/wpDiscuzFlagComment.php")) {
            $plugins[] = "wpdiscuz-report-flagging/wpDiscuzFlagComment.php";
        }
        if (is_plugin_active("wpdiscuz-subscribe-manager/wpdSubscribeManager.php")) {
            $plugins[] = "wpdiscuz-subscribe-manager/wpdSubscribeManager.php";
        }
        if (is_plugin_active("wpdiscuz-user-comment-mentioning/WpdiscuzUCM.php")) {
            $plugins[] = "wpdiscuz-user-comment-mentioning/WpdiscuzUCM.php";
        }
        if (is_plugin_active("wpdiscuz-widgets/wpDiscuzWidgets.php")) {
            $plugins[] = "wpdiscuz-widgets/wpDiscuzWidgets.php";
        }
        if ($plugins) {
            deactivate_plugins($plugins);
            add_option(self::OPTION_SLUG_WIZARD_SHOW_ADDONS_MSG, "1", "", "no");
        }
    }

    /**
     * set recaptcha options
     */
    private function setRecaptchaOptions($options) {
        if (!$options[self::TAB_RECAPTCHA]["siteKey"] && ($recaptchaSettings = get_option("wpdiscuz_recaptcha"))) {
            $options[self::TAB_RECAPTCHA]["siteKey"] = $recaptchaSettings["site_key"];
            $options[self::TAB_RECAPTCHA]["secretKey"] = $recaptchaSettings["secret_key"];
            $options[self::TAB_RECAPTCHA]["theme"] = $recaptchaSettings["theme"];
            $options[self::TAB_RECAPTCHA]["lang"] = $recaptchaSettings["lang"];
            $options[self::TAB_RECAPTCHA]["showForGuests"] = 1;
            $options[self::TAB_RECAPTCHA]["requestMethod"] = $recaptchaSettings["request_method"];
            $options[self::TAB_RECAPTCHA]["isShowOnSubscribeForm"] = $recaptchaSettings["isShowOnSubscribeForm"] === "on" ? 1 : 0;
            $this->options->initOptions($options);
            $this->options->updateOptions();
        }
    }

    /**
     * merge old and new options
     */
    private function addNewOptions(&$options) {
        $options = array_merge($this->options->getDefaultOptions(), $options);
        $this->options->initOptions($options);
        $this->options->updateOptions();
    }

    /**
     * merge old and new options recursively
     */
    private function mergeOptions(&$options) {
        $newOptions = [];
        $defaults = $this->options->getDefaultOptions();
        foreach ($options as $key => $value) {
            $newOptions[$key] = array_merge($defaults[$key], $value);
        }
        $options = $newOptions;
        $this->options->initOptions($options);
        $this->options->updateOptions();
    }

    /**
     * merge old and new phrases
     */
    private function addNewPhrases() {
        if ($this->dbManager->isPhraseExists("wc_be_the_first_text")) {
            $wc_saved_phrases = $this->dbManager->getPhrases();
            $this->options->initPhrases();
            $wc_phrases = $this->options->phrases;
            $wc_new_phrases = array_merge($wc_phrases, $wc_saved_phrases);
            $this->dbManager->updatePhrases($wc_new_phrases);
        }
    }

    /**
     * change old options if needed
     */
    private function changeOldOptions($options, $pluginData) {
        $oldOptions = maybe_unserialize($options);
        if (isset($oldOptions["wc_comment_list_order"])) {
            update_option("comment_order", $oldOptions["wc_comment_list_order"]);
        }
        if (isset($oldOptions["wc_comment_count"])) {
            update_option("comments_per_page", $oldOptions["wc_comment_count"]);
        }
        if (isset($oldOptions["wc_load_all_comments"])) {
            $this->options->commentListLoadType = 1;
        }
        if (isset($this->options->disableFontAwesome) && $this->options->disableFontAwesome && $pluginData["Version"] == "5.0.4") {
            $this->options->disableFontAwesome = 0;
            $oldOptions["disableFontAwesome"] = 0;
        }

        if (version_compare($this->version, "5.2.1", "<=")) {
            $oldOptions["isNativeAjaxEnabled"] = 1;
        }
        if (version_compare($this->version, "7.0.0", ">=") && version_compare($this->version, "7.0.2", "<")) {
            $oldOptions[self::TAB_RATING]["enablePostRatingSchema"] = 0;
        }
        if (version_compare($this->version, "7.0.0", ">=") && version_compare($this->version, "7.0.4", "<")) {
            $newMimeTypes = [];
            foreach ($oldOptions[self::TAB_CONTENT]["wmuMimeTypes"] as $exts => $type) {
                foreach (explode('|', $exts) as $k => $ext) {
                    $newMimeTypes[$ext] = $type;
                }
            }
            $oldOptions[self::TAB_CONTENT]["wmuMimeTypes"] = $newMimeTypes;
        }
        return $oldOptions;
    }

    // Add settings link on plugin page
    public function addPluginSettingsLink($links) {
        $links[] = "<a href='" . esc_url_raw(admin_url("admin.php?page=" . self::PAGE_WPDISCUZ)) . "'>" . esc_html__("Dashboard", "wpdiscuz") . "</a>";
        $links[] = "<a href='" . esc_url_raw(admin_url("admin.php?page=" . self::PAGE_SETTINGS)) . "'>" . esc_html__("Settings", "wpdiscuz") . "</a>";
        if (!$this->options->general["isUsePoMo"]) {
            $links[] = "<a href='" . esc_url_raw(admin_url("admin.php?page=" . self::PAGE_PHRASES)) . "'>" . esc_html__("Phrases", "wpdiscuz") . "</a>";
        }
        return $links;
    }

    public function initCurrentPostType() {
        global $post;
        if ($this->isWpdiscuzLoaded) {
            $this->form = $this->wpdiscuzForm->getForm($post->ID);
            add_filter("comments_template", [&$this, "addCommentForm"], 9999999);
        }
    }

    public function addContentModal() {
        echo "<a id='wpdUserContentInfoAnchor' style='display:none;' rel='#wpdUserContentInfo' data-wpd-lity>wpDiscuz</a>";
        echo "<div id='wpdUserContentInfo' style='overflow:auto;background:#FDFDF6;padding:20px;width:600px;max-width:100%;border-radius:6px;' class='lity-hide'></div>";
    }

    public function saveLastVisit($post) {
        $currentUser = WpdiscuzHelper::getCurrentUser();
        if (!empty($currentUser->ID)) {
            $lastVisit = get_user_meta($currentUser->ID, self::USERMETA_LAST_VISIT, true);
            $lastVisit = is_array($lastVisit) ? $lastVisit : [];
            $lastVisit[$post->ID] = current_time("timestamp");
            update_user_meta($currentUser->ID, self::USERMETA_LAST_VISIT, $lastVisit);
        }
    }

    public function addCommentForm($file) {
        return $this->helper->getCommentFormPath($this->form->getTheme());
    }

    public function getCommentListArgs($postId) {
        $post = get_post($postId);
        $postsAuthors = $post->comment_count && $this->options->login["enableProfileURLs"] ? $this->dbManager->getPostsAuthors() : [];
        $voteSvgs = [
            "fa-plus|fa-minus" => [
                "<svg aria-hidden='true' focusable='false' data-prefix='fas' data-icon='plus' class='svg-inline--fa fa-plus fa-w-14' role='img' xmlns='https://www.w3.org/2000/svg' viewBox='0 0 448 512'><path d='M416 208H272V64c0-17.67-14.33-32-32-32h-32c-17.67 0-32 14.33-32 32v144H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h144v144c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32V304h144c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z'></path></svg>",
                "<svg aria-hidden='true' focusable='false' data-prefix='fas' data-icon='minus' class='svg-inline--fa fa-minus fa-w-14' role='img' xmlns='https://www.w3.org/2000/svg' viewBox='0 0 448 512'><path d='M416 208H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h384c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z'></path></svg>",
            ],
            "fa-chevron-up|fa-chevron-down" => [
                "<svg aria-hidden='true' focusable='false' data-prefix='fas' data-icon='chevron-up' class='svg-inline--fa fa-chevron-up fa-w-14' role='img' xmlns='https://www.w3.org/2000/svg' viewBox='0 0 448 512'><path d='M240.971 130.524l194.343 194.343c9.373 9.373 9.373 24.569 0 33.941l-22.667 22.667c-9.357 9.357-24.522 9.375-33.901.04L224 227.495 69.255 381.516c-9.379 9.335-24.544 9.317-33.901-.04l-22.667-22.667c-9.373-9.373-9.373-24.569 0-33.941L207.03 130.525c9.372-9.373 24.568-9.373 33.941-.001z'></path></svg>",
                "<svg aria-hidden='true' focusable='false' data-prefix='fas' data-icon='chevron-down' class='svg-inline--fa fa-chevron-down fa-w-14' role='img' xmlns='https://www.w3.org/2000/svg' viewBox='0 0 448 512'><path d='M207.029 381.476L12.686 187.132c-9.373-9.373-9.373-24.569 0-33.941l22.667-22.667c9.357-9.357 24.522-9.375 33.901-.04L224 284.505l154.745-154.021c9.379-9.335 24.544-9.317 33.901.04l22.667 22.667c9.373 9.373 9.373 24.569 0 33.941L240.971 381.476c-9.373 9.372-24.569 9.372-33.942 0z'></path></svg>",
            ],
            "fa-thumbs-up|fa-thumbs-down" => [
                "<svg xmlns='https://www.w3.org/2000/svg' viewBox='0 0 24 24'><path fill='none' d='M0 0h24v24H0V0z'/><path d='M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.59 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z'/></svg>",
                "<svg xmlns='https://www.w3.org/2000/svg' viewBox='0 0 24 24'><path fill='none' d='M0 0h24v24H0z'/><path d='M15 3H6c-.83 0-1.54.5-1.84 1.22l-3.02 7.05c-.09.23-.14.47-.14.73v2c0 1.1.9 2 2 2h6.31l-.95 4.57-.03.32c0 .41.17.79.44 1.06L9.83 23l6.59-6.59c.36-.36.58-.86.58-1.41V5c0-1.1-.9-2-2-2zm4 0v12h4V3h-4z'/></svg>",
            ],
            "fa-smile|fa-frown" => [
                "<svg aria-hidden='true' focusable='false' data-prefix='far' data-icon='smile' class='svg-inline--fa fa-smile fa-w-16' role='img' xmlns='https://www.w3.org/2000/svg' viewBox='0 0 496 512'><path d='M248 8C111 8 0 119 0 256s111 248 248 248 248-111 248-248S385 8 248 8zm0 448c-110.3 0-200-89.7-200-200S137.7 56 248 56s200 89.7 200 200-89.7 200-200 200zm-80-216c17.7 0 32-14.3 32-32s-14.3-32-32-32-32 14.3-32 32 14.3 32 32 32zm160 0c17.7 0 32-14.3 32-32s-14.3-32-32-32-32 14.3-32 32 14.3 32 32 32zm4 72.6c-20.8 25-51.5 39.4-84 39.4s-63.2-14.3-84-39.4c-8.5-10.2-23.7-11.5-33.8-3.1-10.2 8.5-11.5 23.6-3.1 33.8 30 36 74.1 56.6 120.9 56.6s90.9-20.6 120.9-56.6c8.5-10.2 7.1-25.3-3.1-33.8-10.1-8.4-25.3-7.1-33.8 3.1z'></path></svg>",
                "<svg aria-hidden='true' focusable='false' data-prefix='far' data-icon='frown' class='svg-inline--fa fa-frown fa-w-16' role='img' xmlns='https://www.w3.org/2000/svg' viewBox='0 0 496 512'><path d='M248 8C111 8 0 119 0 256s111 248 248 248 248-111 248-248S385 8 248 8zm0 448c-110.3 0-200-89.7-200-200S137.7 56 248 56s200 89.7 200 200-89.7 200-200 200zm-80-216c17.7 0 32-14.3 32-32s-14.3-32-32-32-32 14.3-32 32 14.3 32 32 32zm160-64c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm-80 128c-40.2 0-78 17.7-103.8 48.6-8.5 10.2-7.1 25.3 3.1 33.8 10.2 8.4 25.3 7.1 33.8-3.1 16.6-19.9 41-31.4 66.9-31.4s50.3 11.4 66.9 31.4c8.1 9.7 23.1 11.9 33.8 3.1 10.2-8.5 11.5-23.6 3.1-33.8C326 321.7 288.2 304 248 304z'></path></svg>",
            ],
        ];
        $currentUser = WpdiscuzHelper::getCurrentUser();
        $currentUserEmail = "";
        $isUserLoggedIn = false;
        if (!empty($currentUser->ID)) {
            $currentUserEmail = $currentUser->user_email;
            $isUserLoggedIn = true;
        } else if (!empty($_COOKIE["comment_author_email_" . COOKIEHASH])) {
            $currentUserEmail = urldecode(trim($_COOKIE["comment_author_email_" . COOKIEHASH]));
        }
        $this->form = $this->wpdiscuzForm->getForm($postId);
        $high_level_user = current_user_can("moderate_comments");
        $can_stick_or_close = $post->post_author == $currentUser->ID;
        $post_permalink = get_permalink($postId);
        $theme = $this->form->getTheme();
        $layout = $this->form->getLayout();
        include_once $this->helper->getWalkerPath($theme);
        $args = [
            "style" => "div",
            "echo" => false,
            "isSingle" => false,
            "reverse_top_level" => false,
            "post_id" => $postId,
            "reverse_children" => !$this->options->thread_display["reverseChildren"],
            "post_author" => $post->post_author,
            "posts_authors" => $postsAuthors,
            "voting_icons" => $voteSvgs[$this->options->thread_layouts["votingButtonsIcon"]],
            "high_level_user" => $high_level_user,
            "avatar_trackback" => apply_filters("wpdiscuz_avatar_trackback", plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/trackback.png")),
            "wpdiscuz_gravatar_size" => apply_filters("wpdiscuz_gravatar_size", 64),
            "can_stick_or_close" => $can_stick_or_close,
            "user_follows" => $this->dbManager->getUserFollows($currentUserEmail),
            "current_user" => $currentUser,
            "current_user_email" => $currentUserEmail,
            "is_share_enabled" => $this->options->isShareEnabled(),
            "post_permalink" => $post_permalink,
            "can_user_reply" => comments_open($post->ID) && $this->options->wp["threadComments"] && (($this->form ? $this->form->isUserCanComment($currentUser, $postId) : true) || $high_level_user) && !(class_exists("WooCommerce") && get_post_type($post) === "product"),
            "can_user_follow" => $this->options->subscription["isFollowActive"] && $isUserLoggedIn && !empty($currentUserEmail),
            "can_user_vote" => $currentUser->ID || $this->options->thread_layouts["isGuestCanVote"],
            "wpd_stick_btn" => $this->options->moderation["enableStickButton"] && ($high_level_user || $can_stick_or_close) ? "<span class='wpd_stick_btn wpd-cta-button'>%s</span>" : "",
            "wpd_close_btn" => $this->options->moderation["enableCloseButton"] && ($high_level_user || $can_stick_or_close) ? "<span class='wpd_close_btn wpd-cta-button'>%s</span>" : "",
            "is_wpdiscuz_comment" => true,
            "share_buttons" => "",
            "feedback_content_words_count" => apply_filters("wpdiscuz_feedback_content_words_count", 20),
            "user_votes" => [],
            "last_visit" => $this->options->thread_display["highlightUnreadComments"] && ($lastVisit = get_user_meta($currentUser->ID, self::USERMETA_LAST_VISIT, true)) && !empty($lastVisit[$postId]) ? $lastVisit[$postId] : "",
            "site_url" => get_site_url(),
            "layout" => $layout,
            "components" => $this->helper->getComponents($theme, $layout),
            "walker" => new WpdiscuzWalker($this->helper, $this->helperOptimization, $this->dbManager, $this->options),
        ];
        if ($this->options->social["enableFbShare"] && $this->options->social["fbAppID"]) {
            $args["share_buttons"] .= "<span class='wc_fb'><i class='fab fa-facebook-f wpf-cta' aria-hidden='true' title='" . esc_attr($this->options->phrases["wc_share_facebook"]) . "'></i></span>";
        }
        if ($this->options->social["enableVkShare"]) {
            $args["share_buttons"] .= "<a class='wc_vk' rel='noreferrer' target='_blank' href='https://vk.com/share.php?url=" . esc_url_raw($post_permalink) . "' title='" . esc_attr($this->options->phrases["wc_share_vk"]) . "'><i class='fab fa-vk wpf-cta' aria-hidden='true'></i></a>";
        }
        if ($this->options->social["enableOkShare"]) {
            $args["share_buttons"] .= "<a class='wc_ok' rel='noreferrer' target='_blank' href='https://connect.ok.ru/offer?url=" . esc_url_raw($post_permalink) . "' title='" . esc_attr($this->options->phrases["wc_share_ok"]) . "'><i class='fab fa-odnoklassniki wpf-cta' aria-hidden='true'></i></a>";
        }
        if ($this->options->social["enableWhatsappShare"]) {
            $args["whatsapp_url"] = wp_is_mobile() ? "https://api.whatsapp.com" : "https://web.whatsapp.com";
        }
        return apply_filters("wpdiscuz_comment_list_args", $args);
    }

    public function addNewRoles() {
        global $wp_roles;
        $roles = apply_filters("editable_roles", $wp_roles->roles);
        $newBlogRoles = [];
        $newBlogRoleLabels = [];
        $newRolePhrases = [];
        foreach ($roles as $roleName => $roleInfo) {
            $newBlogRoles[$roleName] = isset($this->options->labels["blogRoles"][$roleName]) ? $this->options->labels["blogRoles"][$roleName] : "#00B38F";
            if ($roleName === "administrator") {
                $newBlogRoleLabels[$roleName] = isset($this->options->labels["blogRoleLabels"][$roleName]) ? $this->options->labels["blogRoleLabels"][$roleName] : 1;
                $newRolePhrases["wc_blog_role_" . $roleName] = isset($this->options->phrases["wc_blog_role_" . $roleName]) ? $this->options->phrases["wc_blog_role_" . $roleName] : esc_html__("Admin", "wpdiscuz");
            } elseif ($roleName === "post_author") {
                $newBlogRoleLabels[$roleName] = isset($this->options->labels["blogRoleLabels"][$roleName]) ? $this->options->labels["blogRoleLabels"][$roleName] : 1;
                $newRolePhrases["wc_blog_role_" . $roleName] = isset($this->options->phrases["wc_blog_role_" . $roleName]) ? $this->options->phrases["wc_blog_role_" . $roleName] : esc_html__("Author", "wpdiscuz");
            } elseif ($roleName === "editor") {
                $newBlogRoleLabels[$roleName] = isset($this->options->labels["blogRoleLabels"][$roleName]) ? $this->options->labels["blogRoleLabels"][$roleName] : 1;
                $newRolePhrases["wc_blog_role_" . $roleName] = isset($this->options->phrases["wc_blog_role_" . $roleName]) ? $this->options->phrases["wc_blog_role_" . $roleName] : esc_html__("Editor", "wpdiscuz");
            } else {
                $newBlogRoleLabels[$roleName] = isset($this->options->labels["blogRoleLabels"][$roleName]) ? $this->options->labels["blogRoleLabels"][$roleName] : 0;
                $newRolePhrases["wc_blog_role_" . $roleName] = isset($this->options->phrases["wc_blog_role_" . $roleName]) ? $this->options->phrases["wc_blog_role_" . $roleName] : esc_html__("Member", "wpdiscuz");
            }
        }
        $newBlogRoles["post_author"] = isset($this->options->labels["blogRoles"]["post_author"]) ? $this->options->labels["blogRoles"]["post_author"] : "#00B38F";
        $newBlogRoleLabels["post_author"] = isset($this->options->labels["blogRoleLabels"]["post_author"]) ? $this->options->labels["blogRoleLabels"]["post_author"] : 1;
        $newBlogRoles["guest"] = isset($this->options->labels["blogRoles"]["guest"]) ? $this->options->labels["blogRoles"]["guest"] : "#00B38F";
        $newBlogRoleLabels["guest"] = isset($this->options->labels["blogRoleLabels"]["guest"]) ? $this->options->labels["blogRoleLabels"]["guest"] : 0;
        $newRolePhrases["wc_blog_role_post_author"] = isset($this->options->phrases["wc_blog_role_post_author"]) ? $this->options->phrases["wc_blog_role_post_author"] : esc_html__("Author", "wpdiscuz");
        $newRolePhrases["wc_blog_role_guest"] = isset($this->options->phrases["wc_blog_role_guest"]) ? $this->options->phrases["wc_blog_role_guest"] : esc_html__("Guest", "wpdiscuz");
        foreach ($this->options->phrases as $key => $value) {
            if (strpos("wc_blog_role_", $key) === 0) {
                unset($this->options->phrases[$key]);
            }
        }
        foreach ($newRolePhrases as $key => $value) {
            $this->options->phrases[$key] = $value;
        }
        $this->options->labels["blogRoles"] = $newBlogRoles;
        $this->options->labels["blogRoleLabels"] = $newBlogRoleLabels;
    }

    public function showReplies() {
        $postId = isset($_POST["postId"]) ? intval($_POST["postId"]) : 0;
        $commentId = isset($_POST["commentId"]) ? intval($_POST["commentId"]) : 0;
        if ($postId) {
            $this->isWpdiscuzLoaded = true;
            $commentListArgs = $this->getCommentListArgs($postId);
            $cArgs = $this->getDefaultCommentsArgs($postId);
            $cArgs["parent"] = $commentId;
            $cArgs["number"] = null;
            $comment = get_comment($commentId);
            $args = [
                "format" => "flat",
                "status" => $cArgs["status"],
                "orderby" => $cArgs["orderby"],
                "post_id" => $cArgs["post_id"],
            ];
            if (!empty($cArgs["include_unapproved"])) {
                $args["include_unapproved"] = $cArgs["include_unapproved"];
            }
            $children = $comment->get_children($args);
            $commentListArgs["wpdiscuz_child_count_" . $comment->comment_ID] = count($children);
            $comments = array_merge([$comment], $children);
            if ($comments) {
                $response = [];
                if ($this->options->thread_layouts["highlightVotingButtons"]) {
                    if (!empty($commentListArgs['current_user']->ID)) {
                        $commentListArgs["user_votes"] = $this->dbManager->getUserVotes($comments, $commentListArgs['current_user']->ID);
                    } else {
                        $commentListArgs["user_votes"] = $this->dbManager->getUserVotes($comments, md5($this->helper->getRealIPAddr()));
                    }
                }
                $response["comment_list"] = wp_list_comments($commentListArgs, $comments);
                $response["callbackFunctions"] = [];
                $response = apply_filters("wpdiscuz_ajax_callbacks", $response);
                wp_send_json_success($response);
            }
        }
    }

    public function mostReactedComment() {
        $postId = isset($_POST["postId"]) ? intval($_POST["postId"]) : 0;
        if ($postId) {
            $this->isWpdiscuzLoaded = true;
            $commentId = $this->dbManager->getMostReactedCommentId($postId);
            $comment = get_comment($commentId);
            if ($comment && $comment->comment_post_ID == $postId) {
                $this->commentsArgs = $this->getDefaultCommentsArgs($postId);
                $commentStatusIn = ["1"];
                if ($this->commentsArgs["status"] === "all") {
                    $commentStatusIn[] = "0";
                }
                $args = [
                    "format" => "flat",
                    "status" => $this->commentsArgs["status"],
                    "orderby" => $this->commentsArgs["orderby"],
                    "post_id" => $this->commentsArgs["post_id"],
                ];
                $includeUnapproved = null;
                if (!empty($this->commentsArgs["include_unapproved"])) {
                    $args["include_unapproved"] = $this->commentsArgs["include_unapproved"];
                    $includeUnapproved = $this->commentsArgs["include_unapproved"];
                }
                $parentComment = $this->helperOptimization->getCommentRoot($commentId, $commentStatusIn, $includeUnapproved);
                $tree = $parentComment->get_children($args);
                $comments = array_merge([$parentComment], $tree);
                $commentListArgs = $this->getCommentListArgs($postId);
                $commentListArgs["isSingle"] = true;
                $commentListArgs["new_loaded_class"] = "wpd-new-loaded-comment";
                $response = [];
                if ($comments && $this->options->thread_layouts["highlightVotingButtons"]) {
                    if (!empty($commentListArgs['current_user']->ID)) {
                        $commentListArgs["user_votes"] = $this->dbManager->getUserVotes($comments, $commentListArgs['current_user']->ID);
                    } else {
                        $commentListArgs["user_votes"] = $this->dbManager->getUserVotes($comments, md5($this->helper->getRealIPAddr()));
                    }
                }
                $response["message"] = wp_list_comments($commentListArgs, $comments);
                $response["commentId"] = $commentId;
                $response["parentCommentID"] = $parentComment->comment_ID;
                $response["callbackFunctions"] = [];
                $response = apply_filters("wpdiscuz_ajax_callbacks", $response);
                wp_send_json_success($response);
            }
        }
    }

    public function hottestThread() {
        $postId = isset($_POST["postId"]) ? intval($_POST["postId"]) : 0;
        if ($postId) {
            $this->isWpdiscuzLoaded = true;
            $parentCommentIds = $this->dbManager->getParentCommentsHavingReplies($postId);
            $childCount = 0;
            $hottestCommentId = 0;
            $hottestChildren = [];
            foreach ($parentCommentIds as $k => $parentCommentId) {
                $tree = [];
                $children = $this->dbManager->getHottestTree($parentCommentId);
                $tmpCount = count($children);
                if ($childCount < $tmpCount) {
                    $childCount = $tmpCount;
                    $hottestCommentId = $parentCommentId;
                    $hottestChildren = $children;
                }
            }

            if ($hottestCommentId && $hottestChildren) {
                $this->commentsArgs = $this->getDefaultCommentsArgs($postId);
                $commentStatusIn = ["1"];
                if ($this->commentsArgs["status"] === "all") {
                    $commentStatusIn[] = "0";
                }
                $args = [
                    "format" => "flat",
                    "status" => $this->commentsArgs["status"],
                    "orderby" => $this->commentsArgs["orderby"],
                    "post_id" => $this->commentsArgs["post_id"],
                ];
                $includeUnapproved = null;
                if (!empty($this->commentsArgs["include_unapproved"])) {
                    $args["include_unapproved"] = $this->commentsArgs["include_unapproved"];
                    $includeUnapproved = $this->commentsArgs["include_unapproved"];
                }
                $parentComment = $this->helperOptimization->getCommentRoot($hottestCommentId, $commentStatusIn, $includeUnapproved);
                $tree = $parentComment->get_children($args);
                $comments = array_merge([$parentComment], $tree);
                $commentListArgs = $this->getCommentListArgs($postId);
                $commentListArgs["isSingle"] = true;
                $commentListArgs["new_loaded_class"] = "wpd-new-loaded-comment";
                $response = [];
                if ($comments && $this->options->thread_layouts["highlightVotingButtons"]) {
                    if (!empty($commentListArgs['current_user']->ID)) {
                        $commentListArgs["user_votes"] = $this->dbManager->getUserVotes($comments, $commentListArgs['current_user']->ID);
                    } else {
                        $commentListArgs["user_votes"] = $this->dbManager->getUserVotes($comments, md5($this->helper->getRealIPAddr()));
                    }
                }
                $response["message"] = wp_list_comments($commentListArgs, $comments);
                $response["commentId"] = $hottestCommentId;
                $response["callbackFunctions"] = [];
                $response = apply_filters("wpdiscuz_ajax_callbacks", $response);
                wp_send_json_success($response);
            }
        }
    }

    private function getStickyComments($isPaginate, &$commentList, &$commentListArgs) {
        if (!empty($this->commentsArgs["first_load"])) {
            $this->commentsArgs["sticky"] = 1;
            $this->commentsArgs["number"] = null;
            if ($isPaginate) {
                $this->commentsArgs["number"] = "";
                $this->commentsArgs["offset"] = "";
                $this->commentsArgs["parent"] = "";
            }
            $this->commentsArgs["caller"] = "wpdiscuz";
            $this->commentsArgs["type__not_in"] = [];
            $this->commentsArgs["type__in"] = [self::WPDISCUZ_STICKY_COMMENT];
            $stickyComments = get_comments($this->commentsArgs);
            if ($stickyComments) {
                if ($this->options->wp["threadComments"]) {
                    $stickyComments = $this->getChildren($stickyComments, $commentListArgs);
                }
                $commentList = ($isPaginate && $this->options->wp["commentOrder"] == "desc") ? array_merge($commentList, $stickyComments) : array_merge($stickyComments, $commentList);
            }
        }
    }

    public function footerContents() {
        if ($this->isWpdiscuzLoaded) {
            global $post;
            if ($this->options->login["showActivityTab"] || $this->options->login["showSubscriptionsTab"] || $this->options->login["showFollowsTab"] || apply_filters("wpdiscuz_enable_content_modal", false)) {
                $this->addContentModal();
            }
            if ($this->options->live["enableBubble"]) {
                $this->addBubble($post);
            }
            if ($this->options->thread_display["highlightUnreadComments"]) {
                $this->saveLastVisit($post);
            }
            echo "<div id='wpd-editor-source-code-wrapper-bg'></div><div id='wpd-editor-source-code-wrapper'><textarea id='wpd-editor-source-code'></textarea><button id='wpd-insert-source-code'>Insert</button><input type='hidden' id='wpd-editor-uid' /></div>";
        }
    }

    public function addBubble($post) {
        if (comments_open($post->ID)) {
            echo "<div id='wpd-bubble-wrapper'>";
            $commentsNumber = get_comments_number($post->ID);
            echo "<span id='wpd-bubble-all-comments-count'" . ($commentsNumber ? "" : " style='display:none;'") . ">" . esc_html($commentsNumber) . "</span>";
            echo "<div id='wpd-bubble-count'>";
            echo "<svg xmlns='https://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'><path class='wpd-bubble-count-first' d='M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z'/><path class='wpd-bubble-count-second' d='M0 0h24v24H0z' /></svg>";
            echo "<span class='wpd-new-comments-count'>0</span>";
            echo "</div>";
            echo "<div id='wpd-bubble'>";
            echo "<svg xmlns='https://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'><path class='wpd-bubble-plus-first' d='M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z'/><path class='wpd-bubble-plus-second' d='M0 0h24v24H0z' /></svg>";
            echo "<div id='wpd-bubble-add-message'>" . esc_html($this->options->phrases["wc_bubble_invite_message"]) . "<span id='wpd-bubble-add-message-close'><a href='#'>x</a></span></div>";
            echo "</div>";
            echo "<div id='wpd-bubble-notification'><svg xmlns='https://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'><path class='wpd-bubble-notification-first' d='M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z'/><path class='wpd-bubble-notification-second' d='M0 0h24v24H0z' /></svg>";
            if ($this->options->live["bubbleShowNewCommentMessage"]) {
                echo "<div id='wpd-bubble-notification-message'>";
                echo "<div id='wpd-bubble-author'>";
                echo "<div>";
                echo "<span id='wpd-bubble-author-avatar'></span>";
                echo "<span id='wpd-bubble-author-name'></span>";
                echo "<span id='wpd-bubble-comment-date'>(<span class='wpd-bubble-spans'></span>)</span>";
                echo "</div>";
                echo "<span id='wpd-bubble-comment-close'><a href='#'>x</a></span>";
                echo "</div>";
                echo "<div id='wpd-bubble-comment'>";
                echo "<span id='wpd-bubble-comment-text'></span>";
                echo "<span id='wpd-bubble-comment-reply-link'>| <a href='#'>Reply</a></span>";
                echo "</div>";
                echo "</div>";
            }
            echo "</div>";
            echo "</div>";
        }
    }

    public function registerRestRoutes() {
        $controller = new WpdiscuzRest($this->dbManager, $this->options, $this->helper, $this->wpdiscuzForm);
        $controller->registerRoutes();
    }

    public function mceButton($buttons) {
        global $post;
        if (!empty($post->ID) && comments_open($post->ID) && ($form = $this->wpdiscuzForm->getForm($post->ID)) && $form->getFormID()) {
            array_push($buttons, "|", "wpDiscuz");
        }
        return $buttons;
    }

    public function mceExternalPlugin($plugin_array) {
        global $post;
        if (!empty($post->ID) && comments_open($post->ID) && ($form = $this->wpdiscuzForm->getForm($post->ID)) && $form->getFormID()) {
            $plugin_array["wpDiscuz"] = esc_url_raw(plugins_url("assets/js/wpdiscuz-shortcode-tinymce.js", __FILE__));
        }
        return $plugin_array;
    }

    public function gutenbergButton() {
        global $post;
        if (!empty($post->ID) && comments_open($post->ID) && ($form = $this->wpdiscuzForm->getForm($post->ID)) && $form->getFormID()) {
            wp_register_script(self::WPDISCUZ_FEEDBACK_SHORTCODE . "-shortcode-gutenberg-js", plugins_url(WPDISCUZ_DIR_NAME . "/assets/js/wpdiscuz-shortcode-gutenberg.js"), ["wp-blocks", "wp-editor", "wp-components"]);
            wp_enqueue_script(self::WPDISCUZ_FEEDBACK_SHORTCODE . "-shortcode-gutenberg-js");
            wp_localize_script(self::WPDISCUZ_FEEDBACK_SHORTCODE . "-shortcode-gutenberg-js", "wpdObject", ["shortcode" => self::WPDISCUZ_FEEDBACK_SHORTCODE, "image" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/shortcode.png"), "tooltip" => $this->options->phrases["wc_feedback_shortcode_tooltip"], "popup_title" => $this->options->phrases["wc_feedback_popup_title"], "leave_feebdack" => $this->options->phrases["wc_please_leave_feebdack"]]);
        }
    }

    public function feedbackDialog() {
        global $post;
        if (!empty($post->ID) && comments_open($post->ID) && ($form = $this->wpdiscuzForm->getForm($post->ID)) && $form->getFormID()) {
            ?>
            <style type="text/css">
                #TB_title{background:#E9E9E9!important; font-size:16px!important; font-weight:normal!important; line-height:20px!important; padding:5px 10px;}
                #TB_ajaxContent{width: 100% !important; box-sizing: border-box; height: 80vh !important; overflow-y: auto;}
                #TB_ajaxContent .wpd-put-shortcode-parent{padding-top: 20px; text-align: center;}
                #TB_ajaxContent p{padding: 0px; margin-top: 2px;}
                #TB_ajaxContent input[type="text"]{width: 98%; padding:7px 10px; box-sizing: border-box;}
                #TB_ajaxContent .wpd-shortcode-inline-form h3{font-size: 14px; color: #000000; padding-top: 0px; margin-bottom: 5px;}
                #TB_ajaxContent img.wpd-fbs-screen{width: 100%;}
                #TB_ajaxContent .wpd-inline-type{width: 100%;text-align: center;font-weight: 600;padding: 1px 0px 5px 0px;box-sizing: border-box;margin-top: 5px;line-height: 10px;}
                #wpd-inline-content{padding: 10px;background-color: #f5f5f5;margin: 10px 0px 0px 0px;max-height: 55px;overflow-y: auto;font-size: 12px;line-height: 15px;}
                #wpd-inline-content p{font-size: 12px;line-height: 15px;}
                #wpd-inline-content .wpd-text-error{color: #DD0000; font-size: 13px;}
            </style>
            <div id="wpdiscuz_feedback_dialog" style="display:none">
                <div class="wpd-shortcode-inline-form">
                    <h3><?php esc_html_e("Selected Text", "wpdiscuz") ?></h3>
                    <div id="wpd-inline-content">
                        <span class="wpd-text-error"><?php esc_html_e("No text is selected. Please select a part of text from post content.", "wpdiscuz"); ?></span>
                    </div>
                    <table>
                        <tr>
                            <td colspan="2">
                                <h3><?php esc_html_e("Your Question to Readers", "wpdiscuz"); ?></h3>
                                <p class="description" style="line-height: 17px; color: #777; font-size: 12px;"><?php esc_html_e("A simple question or a call to leave a feedback on the selected part of text. Something like &quot;By the way. Do you agree with this?&quot; or &quot;Would love your thoughts, please comment on this.&quot;", "wpdiscuz"); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="text" id="wpd-inline-question" name="wpd-inline-question" placeholder="<?php esc_attr_e("e.g: Any thoughts on this?", "wpdiscuz") ?>" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <h3><?php esc_html_e("Feedback Button Display Type", "wpdiscuz"); ?></h3>
                                <p class="description" style="line-height: 17px; color: #777; font-size: 12px;"><?php esc_html_e("By default the inline feedback form is closed and only the button is displayed. Once reader scrolled and reached to the selected text part, this button animates with comment button size and color changes attracting readers attention. Readers click on the button and open your question with feedback form. Using this option you can keep opened the feedback form. Readers can close it using [x] button.", "wpdiscuz"); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 50%;">
                                <div class="wpd-inline-type">
                                    <label>
                                        <input type="radio" name="wpd-inline-type" value="0" checked="checked" />
                                        <?php esc_html_e("CLOSED", "wpdiscuz") ?>
                                    </label>
                                </div>
                                <img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/inline-feedback-closed.png")) ?>" class="wpd-fbs-screen">
                            </td>
                            <td style="width: 50%">
                                <div class="wpd-inline-type">
                                    <label>
                                        <input type="radio" name="wpd-inline-type" value="1" />
                                        <?php esc_html_e("OPENED", "wpdiscuz") ?>
                                    </label>
                                </div>
                                <img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/inline-feedback-opened.png")) ?>" class="wpd-fbs-screen">
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="wpd-put-shortcode-parent">
                    <button type="button" class="button button-primary button-large" id="wpd-put-shortcode"><?php esc_html_e("Add Inline Feedback Button", "wpdiscuz"); ?></button>
                </div>
            </div>
            <?php
        }
    }

    public function feedbackShortcode($atts, $content = "") {
        global $post;
        if ($this->isWpdiscuzLoaded && comments_open($post->ID)) {
            $atts = shortcode_atts(["id" => "", "question" => "", "opened" => 0], $atts, self::WPDISCUZ_FEEDBACK_SHORTCODE);
            if ($atts["id"] && $atts["question"] && ($inline_form = $this->dbManager->getFeedbackFormByUid($post->ID, $atts["id"]))) {
                $content = "<div class='wpd-inline-shortcode wpd-inline-" . ($inline_form->opened ? "opened" : "closed") . "' id='wpd-inline-" . $inline_form->id . "'>" . html_entity_decode($content);
                $content .= "<div class='wpd-inline-icon-wrapper'>";
                $content .= "<svg class='wpd-inline-icon" . ($this->options->inline["inlineFeedbackAttractionType"] === "blink" ? " wpd-ignored" : "") . "' xmlns='https://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'><path class='wpd-inline-icon-first' d='M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z'/><path class='wpd-inline-icon-second' d='M0 0h24v24H0z' /></svg>";
                $args = [
                    "count" => true,
                    "meta_query" => [
                        [
                            "key" => self::META_KEY_FEEDBACK_FORM_ID,
                            "value" => $inline_form->id,
                            "compare" => "=",
                        ],
                    ],
                ];
                $count = get_comments($args);
                $content .= "<div class='wpd-inline-icon-count" . esc_attr($count ? " wpd-has-comments" : "") . "'>" . esc_html($count) . "</div>";
                $content .= "<div class='wpd-inline-form-wrapper'>";
                $content .= "<div class='wpd-inline-form-question'>" . esc_html($inline_form->question);
                $content .= "<span class='wpd-inline-form-close'><a href='#'>x</a></span>";
                $content .= "</div>";
                $content .= "</div>";
                $content .= "</div>";
                $content .= "</div>";
            }
        }
        return $content;
    }

    public function addInlineComment() {
        $inline_form_id = !empty($_POST["inline_form_id"]) ? intval($_POST["inline_form_id"]) : 0;
        if ($inline_form_id && ($inline_form = $this->dbManager->getFeedbackForm($inline_form_id))) {
            if (!empty($_POST["_wpd_inline_nonce"]) && wp_verify_nonce($_POST["_wpd_inline_nonce"], "wpd_inline_nonce_" . $inline_form->post_id)) {
                if (!comments_open($inline_form->post_id)) {
                    wp_die(esc_html($this->options->phrases["wc_commenting_is_closed"]));
                }
                $currentUser = WpdiscuzHelper::getCurrentUser();
                $isAnonymous = false;
                if (!empty($currentUser->ID)) {
                    $user_id = $currentUser->ID;
                    $name = $this->helper->getCurrentUserDisplayName($currentUser);
                    $email = $currentUser->user_email;
                } else {
                    $user_id = 0;
                    $name = !empty($_POST["wpd_inline_name"]) ? urldecode(trim($_POST["wpd_inline_name"])) : "";
                    if (!empty($_POST["wpd_inline_email"]) && ($email = trim($_POST["wpd_inline_email"]))) {
                        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                            wp_send_json_error("wc_error_email_text");
                        }
                    } else {
                        $email = uniqid() . "@example.com";
                        $isAnonymous = true;
                    }
                    $email = urldecode($email);
                }
                $comment_content = !empty($_POST["wpd_inline_comment"]) ? stripslashes(trim($_POST["wpd_inline_comment"])) : "";
                $comment_content = $this->helper->filterCommentText($comment_content);
                if (!$comment_content) {
                    wp_send_json_error("wc_msg_required_fields");
                }
                $commentMinLength = intval($this->options->content["commentTextMinLength"]);
                $commentMaxLength = intval($this->options->content["commentTextMaxLength"]);
                $contentLength = function_exists("mb_strlen") ? mb_strlen($comment_content) : strlen($comment_content);
                if ($commentMinLength > 0 && $contentLength < $commentMinLength) {
                    wp_send_json_error("wc_msg_input_min_length");
                }
                if ($commentMaxLength > 0 && $contentLength > $commentMaxLength) {
                    wp_send_json_error("wc_msg_input_max_length");
                }

                if ($name && $email && $comment_content) {
                    $this->isWpdiscuzLoaded = true;
                    $wc_user_agent = !empty($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : "";
                    $new_commentdata = [
                        "user_id" => $user_id,
                        "comment_post_ID" => $inline_form->post_id,
                        "comment_parent" => 0,
                        "comment_author" => $name,
                        "comment_author_email" => $email,
                        "comment_author_url" => "",
                        "comment_content" => $comment_content,
                        "comment_agent" => $wc_user_agent,
                        "comment_type" => "",
                    ];
                    $this->helper->restrictCommentingPerUser($email, 0, $inline_form->post_id);
                    $new_comment_id = wp_new_comment(wp_slash($new_commentdata));
                    add_comment_meta($new_comment_id, self::META_KEY_FEEDBACK_FORM_ID, $inline_form->id);
                    $args = [
                        "count" => true,
                        "meta_query" => [
                            [
                                "key" => self::META_KEY_FEEDBACK_FORM_ID,
                                "value" => $inline_form->id,
                                "compare" => "=",
                            ],
                        ],
                    ];
                    $newComment = get_comment($new_comment_id);
                    if (apply_filters("wpdiscuz_enable_user_mentioning", true) && $this->options->subscription["enableUserMentioning"] && $this->options->subscription["sendMailToMentionedUsers"] && ($mentionedUsers = $this->helper->getMentionedUsers($newComment->comment_content))) {
                        $this->helperEmail->sendMailToMentionedUsers($mentionedUsers, $newComment);
                    }
                    $notifiyMe = isset($_POST["wpd_inline_notify_me"]) ? absint($_POST["wpd_inline_notify_me"]) : 0;
                    if (!$isAnonymous && $notifiyMe) {
                        $noNeedMemberConfirm = ($currentUser->ID && !$this->options->subscription["enableMemberConfirm"]);
                        $noNeedGuestsConfirm = (!$currentUser->ID && !$this->options->subscription["enableGuestsConfirm"]);
                        if ($noNeedMemberConfirm || $noNeedGuestsConfirm) {
                            $this->dbManager->addEmailNotification($new_comment_id, $inline_form->post_id, $email, self::SUBSCRIPTION_COMMENT, 1);
                        } else {
                            $confirmData = $this->dbManager->addEmailNotification($new_comment_id, $inline_form->post_id, $email, self::SUBSCRIPTION_COMMENT);
                            if ($confirmData) {
                                $this->helperEmail->confirmEmailSender($confirmData["id"], $confirmData["activation_key"], $inline_form->post_id, $email);
                            }
                        }
                    }
                    if ($newComment->comment_approved === "spam" || $newComment->comment_approved === "trash") {
                        wp_send_json_error();
                    }
                    $form = $this->wpdiscuzForm->getForm($inline_form->post_id);
                    $response = [];
                    $commentListArgs = $this->getCommentListArgs($inline_form->post_id);
                    $response["message"] = wp_list_comments($commentListArgs, [$newComment]);
                    $response["newCount"] = esc_html(get_comments($args));
                    $response["new_comment_id"] = $new_comment_id;
                    $response["notification"] = esc_html($this->options->phrases["wc_feedback_comment_success"]);
                    $response["allCommentsCountNew"] = esc_html(get_comments_number($inline_form->post_id));
                    $response["allCommentsCountNewHtml"] = "<span class='wpdtc'>" . esc_html($response["allCommentsCountNew"]) . "</span> " . esc_html(1 == $response["allCommentsCountNew"] ? $form->getHeaderTextSingle() : $form->getHeaderTextPlural());
                    do_action("wpdiscuz_clean_post_cache", $inline_form->post_id, "inline_comment_posted");
                    wp_send_json_success($response);
                } else {
                    wp_send_json_error("wc_invalid_field");
                }
            }
        }
        wp_send_json_error("wc_msg_required_fields");
    }

}

$wpdiscuz = wpDiscuz();
