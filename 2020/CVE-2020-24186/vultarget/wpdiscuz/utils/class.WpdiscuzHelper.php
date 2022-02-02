<?php
if (!defined("ABSPATH")) {
    exit();
}

class WpdiscuzHelper implements WpDiscuzConstants {

    private static $spoilerPattern = '@\[(\[?)(spoiler)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)@isu';
    private static $inlineFormPattern = '@\[(\[?)(wpdiscuz\-feedback)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)@isu';
    private static $inlineFormAttsPattern = '@([\w-]+)\s*=\s*\"([^\"]*)\"(?:\s|$)|([\w-]+)\s*=\s*\'([^\']*)\'(?:\s|$)|([\w-]+)\s*=\s*([^\s\'\"]+)(?:\s|$)|\"([^\"]*)\"(?:\s|$)|\'([^\']*)\'(?:\s|$)|(\S+)(?:\s|$)@isu';
    private $options;
    private $dbManager;
    private $wpdiscuzForm;
    private static $current_time;
    private $avatars;

    public function __construct($options, $dbManager, $wpdiscuzForm) {
        $this->options = $options;
        $this->dbManager = $dbManager;
        $this->wpdiscuzForm = $wpdiscuzForm;
        self::$current_time = current_time("timestamp");
        add_filter("the_champ_login_interface_filter", [&$this, "wpDiscuzSuperSocializerLogin"], 15, 2);
        add_filter("pre_comment_user_ip", [&$this, "fixLocalhostIp"], 10);
        if ($this->options->thread_layouts["defaultAvatarUrlForUser"]) {
            add_filter("pre_get_avatar", [$this, "preGetDefaultAvatarForUser"], 9, 3);
        }
        if ($this->options->thread_layouts["defaultAvatarUrlForGuest"]) {
            add_filter("pre_get_avatar", [$this, "preGetDefaultAvatarForGuest"], 9, 3);
        }
        if ($this->options->subscription["enableUserMentioning"]) {
            add_filter("comment_text", [&$this, "userMentioning"], 10, 3);
        }
        if ($this->options->content["enableShortcodes"]) {
            add_filter("comment_text", [&$this, "doShortcode"], 10, 3);
        }
        add_filter("comment_text", [&$this, "multipleBlockquotesToOne"], 100);
        add_filter("wp_update_comment_data", [&$this, "commentDataArr"], 10, 3);
        add_action("post_updated", [&$this, "checkFeedbackShortcodes"], 10, 3);
        add_filter("comment_row_actions", [&$this, "commentRowStickAction"], 10, 2);
        add_filter("admin_comment_types_dropdown", [&$this, "addCommentTypes"]);

        add_action("wp_ajax_wpdGetInfo", [&$this, "wpdGetInfo"]);
        add_action("wp_ajax_nopriv_wpdGetInfo", [&$this, "wpdGetInfo"]);
        if ($this->options->login["showActivityTab"]) {
            add_action("wp_ajax_wpdGetActivityPage", [&$this, "getActivityPage"]);
            add_action("wp_ajax_nopriv_wpdGetActivityPage", [&$this, "getActivityPage"]);
        }
        if ($this->options->login["showSubscriptionsTab"]) {
            add_action("wp_ajax_wpdGetSubscriptionsPage", [&$this, "getSubscriptionsPage"]);
            add_action("wp_ajax_nopriv_wpdGetSubscriptionsPage", [&$this, "getSubscriptionsPage"]);
        }
        if ($this->options->login["showFollowsTab"]) {
            add_action("wp_ajax_wpdGetFollowsPage", [&$this, "getFollowsPage"]);
            add_action("wp_ajax_nopriv_wpdGetFollowsPage", [&$this, "getFollowsPage"]);
        }
        add_action("admin_post_disableAddonsDemo", [&$this, "disableAddonsDemo"]);
        $requestUri = !empty($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : "";
        if (!get_option(self::OPTION_SLUG_DEACTIVATION) && (strpos($requestUri, "/plugins.php") !== false)) {
            add_action("admin_footer", [&$this, "wpdDeactivationReasonModal"]);
        }
        add_filter("wpdiscuz_comment_author", [$this, "umAuthorName"], 10, 2);
        add_action("add_meta_boxes", [&$this, "addRatingResetButton"], 10, 2);
    }

    public function filterKses() {
        $allowedtags = [];
        $allowedtags["br"] = [];
        $allowedtags["a"] = ["href" => true, "title" => true, "target" => true, "rel" => true, "download" => true, "hreflang" => true, "media" => true, "type" => true];
        $allowedtags["i"] = ["class" => true];
        $allowedtags["b"] = [];
        $allowedtags["u"] = [];
        $allowedtags["strong"] = [];
        $allowedtags["s"] = [];
        $allowedtags["p"] = [];
        $allowedtags["blockquote"] = ["cite" => true];
        $allowedtags["ul"] = [];
        $allowedtags["li"] = [];
        $allowedtags["ol"] = [];
        $allowedtags["code"] = [];
        $allowedtags["em"] = [];
        $allowedtags["abbr"] = ["title" => true];
        $allowedtags["q"] = ["cite" => true];
        $allowedtags["acronym"] = ["title" => true];
        $allowedtags["cite"] = [];
        $allowedtags["strike"] = [];
        $allowedtags["del"] = ["datetime" => true];
        $allowedtags["span"] = ["id" => true, "class" => true, "title" => true, "contenteditable" => true, "data-name" => true];
        $allowedtags["pre"] = ["class" => true, "spellcheck" => true];
        return apply_filters("wpdiscuz_allowedtags", $allowedtags);
    }

    public function filterCommentText($commentContent) {
        if (!current_user_can("unfiltered_html")) {
            kses_remove_filters();
            $commentContent = wp_kses($commentContent, $this->filterKses());
        }
        return $commentContent;
    }

    public function dateDiff($datetime) {
        $text = "";
        if ($datetime) {
            $search = ["[number]", "[time_unit]", "[adjective]"];
            $replace = [];
            $now = new DateTime();
            $ago = new DateTime($datetime);
            $diff = $now->diff($ago);
            if ($diff->y) {
                $replace[] = $diff->y;
                $replace[] = $diff->y > 1 ? esc_html($this->options->phrases["wc_year_text_plural"]) : esc_html($this->options->phrases["wc_year_text"]);
            } else if ($diff->m) {
                $replace[] = $diff->m;
                $replace[] = $diff->m > 1 ? esc_html($this->options->phrases["wc_month_text_plural"]) : esc_html($this->options->phrases["wc_month_text"]);
            } else if ($diff->d) {
                $replace[] = $diff->d;
                $replace[] = $diff->d > 1 ? esc_html($this->options->phrases["wc_day_text_plural"]) : esc_html($this->options->phrases["wc_day_text"]);
            } else if ($diff->h) {
                $replace[] = $diff->h;
                $replace[] = $diff->h > 1 ? esc_html($this->options->phrases["wc_hour_text_plural"]) : esc_html($this->options->phrases["wc_hour_text"]);
            } else if ($diff->i) {
                $replace[] = $diff->i;
                $replace[] = $diff->i > 1 ? esc_html($this->options->phrases["wc_minute_text_plural"]) : esc_html($this->options->phrases["wc_minute_text"]);
            } else if ($diff->s) {
                $replace[] = $diff->s;
                $replace[] = $diff->s > 1 ? esc_html($this->options->phrases["wc_second_text_plural"]) : esc_html($this->options->phrases["wc_second_text"]);
            }
            if ($replace) {
                $replace[] = esc_html($this->options->phrases["wc_ago_text"]);
                $text = str_replace($search, $replace, $this->options->general["dateDiffFormat"]);
            } else {
                $text = esc_html($this->options->phrases["wc_right_now_text"]);
            }
        }
        return $text;
    }

    public function makeClickable($ret) {
        $ret = " " . $ret;
        $hook = "?";
        if (is_ssl() && $this->options->general["commentLinkFilter"] == 1) {
            $hook = "";
        }
        $ret = preg_replace_callback("#[^\"|\'](https" . $hook . ":\/\/[^\s]+(\.jpe?g|\.png|\.gif|\.bmp))#i", [&$this, "replaceUrlToImg"], $ret);
        // this one is not in an array because we need it to run last, for cleanup of accidental links within links
        $ret = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $ret);
        $ret = trim($ret);
        return $ret;
    }

    public function replaceUrlToImg($matches) {
        $url = $matches[1];
        if (is_ssl() && $this->options->general["commentLinkFilter"] == 2 && strpos($matches[1], "https://") === false) {
            $url = str_replace("http://", "https://", $url);
        }
        $rel = "noreferrer ugc";
        if (strpos($url, get_site_url()) !== 0) {
            $rel .= " nofollow";
        }
        return apply_filters("wpdiscuz_source_to_image_conversion", "<a rel='$rel' target='_blank' href='" . esc_url_raw($url) . "'><img alt='comment image' src='" . esc_url_raw($url) . "' /></a>", $url);
    }

    /**
     * check if comment has been posted today or not
     * @param type $comment WP_Comment object or Datetime value
     * @return type
     */
    public static function isPostedToday($comment) {
        if (is_object($comment)) {
            return date("Ymd", strtotime(current_time("Ymd"))) <= date("Ymd", strtotime($comment->comment_date));
        } else {
            return date("Ymd", strtotime(current_time("Ymd"))) <= date("Ymd", strtotime($comment));
        }
    }

    public static function getMicrotime() {
        list($pfx_usec, $pfx_sec) = explode(" ", microtime());
        return ((float) $pfx_usec + (float) $pfx_sec);
    }

    /**
     * check if comment is still editable or not
     * return boolean
     */
    public function isCommentEditable($comment) {
        $commentTimestamp = strtotime($comment->comment_date);
        $timeDiff = (self::$current_time - $commentTimestamp);
        $editableTimeLimit = ($this->options->moderation["commentEditableTime"] == "unlimit") ? abs($timeDiff) + 1 : intval($this->options->moderation["commentEditableTime"]);
        return $editableTimeLimit && ($timeDiff < $editableTimeLimit);
    }

    /**
     * checks if the current comment content is in min/max range defined in options
     */
    public function isContentInRange($commentContent) {
        $commentMinLength = intval($this->options->content["commentTextMinLength"]);
        $commentMaxLength = intval($this->options->content["commentTextMaxLength"]);
        $commentContent = trim(strip_tags($commentContent));
        $contentLength = function_exists("mb_strlen") ? mb_strlen($commentContent) : strlen($commentContent);
        return ($commentMinLength && $contentLength >= $commentMinLength) && ($commentMaxLength == 0 || $contentLength <= $commentMaxLength);
    }

    /**
     * return client real ip
     */
    public static function getRealIPAddr() {
        $ip = $_SERVER["REMOTE_ADDR"];

        $ip = apply_filters("pre_comment_user_ip", $ip);

        if ($ip == "::1") {
            $ip = "127.0.0.1";
        }
        return $ip;
    }

    public function getUIDData($uid) {
        $id_strings = explode("_", $uid);
        return $id_strings;
    }

    public function superSocializerFix() {
        $output = "";
        if (function_exists("the_champ_login_button")) {
            $output .= "<div id='comments' style='width: 0;height: 0;clear: both;margin: 0;padding: 0;'></div>";
            $output .= "<div id='respond' class='comments-area'>";
        } else {
            $output .= "<div id='comments' class='comments-area'>";
            $output .= "<div id='respond' style='width: 0;height: 0;clear: both;margin: 0;padding: 0;'></div>";
        }
        echo $output;
    }

    public static function getCommentExcerpt($commentContent, $uniqueId, $options) {
        $readMoreLink = "<span id='wpdiscuz-readmore-" . esc_attr($uniqueId) . "'><span class='wpdiscuz-hellip'>&hellip;&nbsp;</span><span class='wpdiscuz-readmore' title='" . esc_attr($options->phrases["wc_read_more"]) . "'>" . esc_html($options->phrases["wc_read_more"]) . "</span></span>";
        return "<p>" . wp_trim_words($commentContent, $options->content["commentReadMoreLimit"], $readMoreLink) . "</p>";
    }

    public static function strWordCount($content) {
        $words = preg_split("/[\n\r\t ]+/", $content, -1, PREG_SPLIT_NO_EMPTY);
        $words = array_filter($words, function ($w) {
            return $w !== "&nbsp;";
        });
        return count($words);
    }

    public function isLoadWpdiscuz($post) {
        if (!$post || !is_object($post) || (is_front_page() && !$this->options->general["isEnableOnHome"])) {
            return false;
        }
        $form = $this->wpdiscuzForm->getForm($post->ID);
        return apply_filters("is_load_wpdiscuz", $form->getFormID() && (comments_open($post) || $post->comment_count) && is_singular() && post_type_supports($post->post_type, "comments"), $post);
    }

    public function replaceCommentContentCode($content) {
        if (is_ssl()) {
            $content = preg_replace_callback("#<\s*?img[^>]*src*=*[\"\']?([^\"\']*)[^>]+>#is", [&$this, "replaceImageToURL"], $content);
        }
        return preg_replace_callback("#`(.*?)`#is", [&$this, "replaceCodeContent"], stripslashes($content));
    }

    private function replaceImageToURL($matches) {
        if (strpos($matches[1], "https://") === false && $this->options->general["commentLinkFilter"] == 1) {
            return "\r\n" . $matches[1] . "\r\n";
        } elseif (strpos($matches[1], "https://") === false && $this->options->general["commentLinkFilter"] == 2) {
            return str_replace("http://", "https://", $matches[0]);
        } else {
            return $matches[0];
        }
    }

    private function replaceCodeContent($matches) {
        $codeContent = trim($matches[1]);
        $codeContent = str_replace(["<", ">"], ["&lt;", "&gt;"], $codeContent);
        return "<code>" . $codeContent . "</code>";
    }

    public function spoiler($content) {
        return preg_replace_callback(self::$spoilerPattern, [$this, "_spoiler"], $content);
    }

    private function _spoiler($matches) {
        $html = "<div class='wpdiscuz-spoiler-wrap'>";
        $title = esc_html($this->options->phrases["wc_spoiler"]);
        $matches[3] = str_replace(["&#8221;", "&#8220;", "&#8243;", "&#8242;"], "\"", $matches[3]);
        if (preg_match("@title[^\S]*=[^\S]*\"*([^\"]+)\"@is", $matches[3], $titleMatch)) {
            $title = trim($titleMatch[1]) ? trim($titleMatch[1]) : esc_html($this->options->phrases["wc_spoiler"]);
        }

        $html .= "<div class='wpdiscuz-spoiler wpdiscuz-spoiler-closed'><i class='fas fa-plus' aria-hidden='true'></i>" . $title . "</div>";
        $html .= "<div class='wpdiscuz-spoiler-content'>" . $matches[5] . "</div>";
        $html .= "</div>";
        return $html;
    }

    public function getCurrentUserDisplayName($current_user) {
        $displayName = trim($current_user->display_name);
        if (!$displayName) {
            $user_nicename = trim($current_user->user_nicename);
            $displayName = $user_nicename ? $user_nicename : trim($current_user->user_login);
        }
        return $displayName;
    }

    public function enqueueWpDiscuzStyle($slug, $fileName, $version, $form) {
        $themes = $form->getThemes();
        $theme = $form->getTheme();
        $wpdiscuzStyleURL = "";
        if (file_exists(get_stylesheet_directory() . "/wpdiscuz/$fileName.css")) {
            $wpdiscuzStyleURL = get_stylesheet_directory_uri() . "/wpdiscuz/$fileName.css";
        } elseif (file_exists(get_template_directory() . "/wpdiscuz/$fileName.css")) {
            $wpdiscuzStyleURL = get_template_directory_uri() . "/wpdiscuz/$fileName.css";
        } else if (file_exists($theme . "/$fileName.css")) {
            $wpdiscuzStyleURL = $themes[$theme]["url"] . "/$fileName.css";
        }
        if ($wpdiscuzStyleURL) {
            wp_register_style($slug, $wpdiscuzStyleURL, null, $version);
            wp_enqueue_style($slug);
        }
    }

    public function wpDiscuzSuperSocializerLogin($html, $theChampLoginOptions) {
        global $wp_current_filter;
        if (in_array("comment_form_top", $wp_current_filter) && isset($theChampLoginOptions["providers"]) && is_array($theChampLoginOptions["providers"]) && count($theChampLoginOptions["providers"]) > 0) {
            $html = "<style type='text/css'>#wpcomm .wc_social_plugin_wrapper .wp-social-login-connect-with_by_the_champ{float:left;font-size:13px;padding:5px 7px 0 0;text-transform:uppercase}#wpcomm .wc_social_plugin_wrapper ul.wc_social_login_by_the_champ{list-style:none outside none!important;margin:0!important;padding-left:0!important}#wpcomm .wc_social_plugin_wrapper ul.wc_social_login_by_the_champ .theChampLogin{width:24px!important;height:24px!important}#wpcomm .wpd-secondary-forms-social-content ul.wc_social_login_by_the_champ{list-style:none outside none!important;margin:0!important;padding-left:0!important}#wpcomm .wpd-secondary-forms-social-content ul.wc_social_login_by_the_champ .theChampLogin{width:24px!important;height:24px!important}#wpcomm .wpd-secondary-forms-social-content ul.wc_social_login_by_the_champ li{float:right!important}#wpcomm .wc_social_plugin_wrapper .theChampFacebookButton{ display:block!important; }#wpcomm .theChampTwitterButton{background-position:-4px -68px!important}#wpcomm .theChampGoogleButton{background-position:-36px -2px!important}#wpcomm .theChampVkontakteButton{background-position:-35px -67px!important}#wpcomm .theChampLinkedinButton{background-position:-34px -34px!important;}.theChampCommentingTabs #wpcomm li{ margin:0px 1px 10px 0px!important; }</style>";
            $html .= "<div class='wp-social-login-widget'>";
            $html .= "<div class='wp-social-login-connect-with_by_the_champ'>" . esc_html($this->options->phrases["wc_connect_with"]) . ":</div>";
            $html .= "<div class='wp-social-login-provider-list'>";
            if (isset($theChampLoginOptions["gdpr_enable"])) {
                $html .= "<div class='heateor_ss_sl_optin_container'><label><input type='checkbox' class='heateor_ss_social_login_optin' value='1' />" . str_replace($theChampLoginOptions["ppu_placeholder"], "<a href='" . esc_url_raw($theChampLoginOptions["privacy_policy_url"]) . "' target='_blank'>" . $theChampLoginOptions["ppu_placeholder"] . "</a>", wp_strip_all_tags($theChampLoginOptions["privacy_policy_optin_text"])) . "</label></div>";
            }
            $html .= "<ul class='wc_social_login_by_the_champ'>";
            foreach ($theChampLoginOptions["providers"] as $k => $provider) {
                $html .= "<li><i ";
                if ($provider == "google") {
                    $html .= "id='theChamp" . esc_attr(ucfirst($provider)) . "Button' ";
                }
                $html .= "class='theChampLogin theChamp" . esc_attr(ucfirst($provider)) . "Background theChamp" . esc_attr(ucfirst($provider)) . "Login' ";
                $html .= "alt='Login with ";
                $html .= ucfirst($provider);
                $html .= "' title='Login with ";
                if ($provider == "live") {
                    $html .= "Windows Live";
                } else {
                    $html .= ucfirst($provider);
                }
                $html .= "' onclick='theChampCommentFormLogin = true; theChampInitiateLogin(this)' >";
                $html .= "<ss style='display:block' class='theChampLoginSvg theChamp" . esc_attr(ucfirst($provider)) . "LoginSvg'></ss></i></li>";
            }
            $html .= "</ul><div class='wpdiscuz_clear'></div></div></div>";
        }
        return $html;
    }

    public static function getCurrentUser() {
        global $user_ID;
        if ($user_ID) {
            $user = get_userdata($user_ID);
        } else {
            $user = wp_set_current_user(0);
        }
        return $user;
    }

    public function canUserEditComment($comment, $currentUser, $commentListArgs = []) {
        if (isset($commentListArgs["comment_author_email"])) {
            $storedCookieEmail = $commentListArgs["comment_author_email"];
        } else {
            $storedCookieEmail = isset($_COOKIE["comment_author_email_" . COOKIEHASH]) ? $_COOKIE["comment_author_email_" . COOKIEHASH] : "";
        }
        return !(!$this->options->moderation["enableEditingWhenHaveReplies"] && $comment->get_children(["post_id" => $comment->comment_post_ID])) && (($storedCookieEmail == $comment->comment_author_email && $_SERVER["REMOTE_ADDR"] == $comment->comment_author_IP) || ($currentUser && $currentUser->ID && $currentUser->ID == $comment->user_id));
    }

    public function addCommentTypes($args) {
        $args[self::WPDISCUZ_STICKY_COMMENT] = esc_html__("Sticky", "wpdiscuz");
        return $args;
    }

    public function commentRowStickAction($actions, $comment) {
        if (!$comment->comment_parent) {
            $stickText = $comment->comment_type == self::WPDISCUZ_STICKY_COMMENT ? $this->options->phrases["wc_unstick_comment"] : $this->options->phrases["wc_stick_comment"];
            if (intval(get_comment_meta($comment->comment_ID, self::META_KEY_CLOSED, true))) {
                $closeText = $this->options->phrases["wc_open_comment"];
                $closeIcon = "fa-lock";
            } else {
                $closeText = $this->options->phrases["wc_close_comment"];
                $closeIcon = "fa-unlock";
            }
            $actions["stick"] = "<a data-comment='" . $comment->comment_ID . "' data-post='" . $comment->comment_post_ID . "' class='wpd_stick_btn' href='#'> <i class='fas fa-thumbtack'></i> <span class='wpd_stick_text'>" . esc_html($stickText) . "</span></a>";
            $actions["close"] = "<a data-comment='" . $comment->comment_ID . "' data-post='" . $comment->comment_post_ID . "' class='wpd_close_btn' href='#'> <i class='fas " . $closeIcon . "'></i> <span class='wpd_close_text'>" . esc_html($closeText) . "</span></a>";
        }
        return $actions;
    }

    public function wpdDeactivationReasonModal() {
        include_once WPDISCUZ_DIR_PATH . "/utils/deactivation-reason-modal.php";
    }

    public function disableAddonsDemo() {
        if (current_user_can("manage_options") && isset($_GET["_wpnonce"]) && wp_verify_nonce($_GET["_wpnonce"], "disableAddonsDemo") && isset($_GET["show"])) {
            update_option(self::OPTION_SLUG_SHOW_DEMO, intval($_GET["show"]));
            wp_redirect(admin_url("admin.php?page=" . WpdiscuzCore::PAGE_SETTINGS));
        }
    }

    public function getCommentDate($comment) {
        if ($this->options->general["simpleCommentDate"]) {
            $dateFormat = $this->options->wp["dateFormat"];
            $timeFormat = $this->options->wp["timeFormat"];
            if (self::isPostedToday($comment)) {
                $postedDate = $this->options->phrases["wc_posted_today_text"] . " " . mysql2date($timeFormat, $comment->comment_date);
            } else {
                $postedDate = get_comment_date($dateFormat . " " . $timeFormat, $comment->comment_ID);
            }
        } else {
            $postedDate = $this->dateDiff($comment->comment_date_gmt);
        }
        return $postedDate;
    }

    public function getPostDate($post) {
        if ($this->options->general["simpleCommentDate"]) {
            $dateFormat = $this->options->wp["dateFormat"];
            $timeFormat = $this->options->wp["timeFormat"];
            if ($this->isPostPostedToday($post)) {
                $postedDate = $this->options->phrases["wc_posted_today_text"] . " " . mysql2date($timeFormat, $post->post_date);
            } else {
                $postedDate = get_the_date($dateFormat . " " . $timeFormat, $post);
            }
        } else {
            $postedDate = $this->dateDiff($post->post_date_gmt);
        }
        return $postedDate;
    }

    public function getDate($comment) {
        if ($this->options->general["simpleCommentDate"]) {
            $dateFormat = $this->options->wp["dateFormat"];
            $timeFormat = $this->options->wp["timeFormat"];
            if (self::isPostedToday($comment)) {
                $postedDate = $this->options->phrases["wc_posted_today_text"] . " " . mysql2date($timeFormat, $comment);
            } else {
                $postedDate = date($dateFormat . " " . $timeFormat, strtotime($comment));
            }
        } else {
            $postedDate = $this->dateDiff($comment);
        }
        return $postedDate;
    }

    private function isPostPostedToday($post) {
        return date("Ymd", strtotime(current_time("Ymd"))) <= date("Ymd", strtotime($post->post_date));
    }

    public function wpdGetInfo() {
        $response = "";
        $currentUser = self::getCurrentUser();
        if ($currentUser && $currentUser->ID) {
            $currentUserId = $currentUser->ID;
            $currentUserEmail = $currentUser->user_email;
        } else {
            $currentUserId = 0;
            $currentUserEmail = isset($_COOKIE["comment_author_email_" . COOKIEHASH]) ? $_COOKIE["comment_author_email_" . COOKIEHASH] : "";
        }

        if (is_user_logged_in()) {
            $response .= "<div class='wpd-wrapper'>";
            $response .= "<ul class='wpd-list'>";
            if ($this->options->login["showActivityTab"]) {
                $response .= $this->getActivityTitleHtml();
            }
            if ($this->options->login["showSubscriptionsTab"]) {
                $response .= $this->getSubscriptionsTitleHtml();
            }
            if ($this->options->login["showFollowsTab"]) {
                $response .= $this->getFollowsTitleHtml();
            }
            $isFirstTab = true;
            $response .= apply_filters("wpdiscuz_content_modal_title", "", $currentUser);
            $response .= "</ul>";
            $response .= "<div class='wpd-content'>";
            if ($this->options->login["showActivityTab"]) {
                $response .= $this->getActivityContentHtml($currentUserId, $currentUserEmail, $isFirstTab);
                $isFirstTab = false;
            }
            if ($this->options->login["showSubscriptionsTab"]) {
                $response .= $this->getSubscriptionsContentHtml($currentUserId, $currentUserEmail, $isFirstTab);
                $isFirstTab = false;
            }
            if ($this->options->login["showFollowsTab"]) {
                $response .= $this->getFollowsContentHtml($currentUserId, $currentUserEmail, $isFirstTab);
                $isFirstTab = false;
            }
            $response .= apply_filters("wpdiscuz_content_modal_content", "", $currentUser, $isFirstTab);
            $response .= "</div>";
            $response .= "<div class='wpd-user-email-delete-links-wrap'>";
            $response .= "<a href='#' class='wpd-user-email-delete-links wpd-not-clicked'>";
            $response .= esc_html($this->options->phrases["wc_user_settings_email_me_delete_links"]);
            $response .= "<span class='wpd-loading wpd-hide'><i class='fas fa-pulse fa-spinner'></i></span>";
            $response .= "</a>";
            $response .= "<div class='wpd-bulk-desc'>" . esc_html($this->options->phrases["wc_user_settings_email_me_delete_links_desc"]) . "</div>";
            $response .= "</div>";
            $response .= "</div>";
        } else if ($currentUserEmail) {
            $commentBtn = $this->getDeleteAllCommentsButton($currentUserEmail);
            $subscribeBtn = $this->getDeleteAllSubscriptionsButton($currentUserEmail);
            $cookieBtnClass = !$commentBtn && !$subscribeBtn ? "wpd-show" : "wpd-hide";
            $response .= "<div class='wpd-wrapper wpd-guest-settings'>";
            $response .= $commentBtn;
            $response .= $subscribeBtn;
            $response .= $this->deleteCookiesButton($currentUserEmail, $cookieBtnClass);
            $response .= "</div>";
        } else {
            $response .= "<div class='wpd-wrapper'>";
            $response .= esc_html($this->options->phrases["wc_user_settings_no_data"]);
            $response .= "</div>";
        }
        wp_die($response);
    }

    private function getDeleteAllCommentsButton($email) {
        $html = "";
        if (!is_email($email)) {
            return $html;
        }
        $commentCount = get_comments(["author_email" => $email, "count" => true]);
        if ($commentCount) {
            $html .= "<div class='wpd-user-settings-button-wrap'>";
            $html .= "<div class='wpd-user-settings-button wpd-delete-all-comments wpd-not-clicked' data-wpd-delete-action='deleteComments'>";
            $html .= esc_html($this->options->phrases["wc_user_settings_request_deleting_comments"]);
            $html .= "<span class='wpd-loading wpd-hide'><i class='fas fa-spinner fa-pulse'></i></span>";
            $html .= "</div>";
            $html .= "</div>";
        }
        return $html;
    }

    private function getDeleteAllSubscriptionsButton($email) {
        $html = "";
        if (!is_email($email)) {
            return $html;
        }
        $subscriptions = $this->dbManager->getSubscriptions($email, 1, 0);
        if ($subscriptions) {
            $html .= "<div class='wpd-user-settings-button-wrap'>";
            $html .= "<div class='wpd-user-settings-button wpd-delete-all-subscriptions wpd-not-clicked' data-wpd-delete-action='deleteSubscriptions'>";
            $html .= esc_html($this->options->phrases["wc_user_settings_cancel_subscriptions"]);
            $html .= "<span class='wpd-loading wpd-hide'><i class='fas fa-spinner fa-pulse'></i></span>";
            $html .= "</div>";
            $html .= "</div>";
        }
        return $html;
    }

    private function deleteCookiesButton($email, $cookieBtnClass) {
        $html = "";
        if (!is_email($email)) {
            return $html;
        }
        $html .= "<div class='wpd-user-settings-button-wrap " . $cookieBtnClass . "'>";
        $html .= "<div class='wpd-user-settings-button wpd-delete-all-cookies wpd-not-clicked' data-wpd-delete-action='deleteCookies'>";
        $html .= esc_html($this->options->phrases["wc_user_settings_clear_cookie"]);
        $html .= "<span class='wpd-loading wpd-hide'><i class='fas fa-spinner fa-pulse'></i></span>";
        $html .= "</div>";
        $html .= "</div>";
        return $html;
    }

    private function getActivityTitleHtml() {
        ob_start();
        include_once WPDISCUZ_DIR_PATH . "/utils/layouts/activity/title.php";
        return ob_get_clean();
    }

    private function getActivityContentHtml($currentUserId, $currentUserEmail, $isFirstTab) {
        $html = "<div id='wpd-content-item-1' class='wpd-content-item'>";
        if ($isFirstTab) {
            include_once WPDISCUZ_DIR_PATH . "/utils/layouts/activity/content.php";
        } else {
            $html .= "<img alt='wpdiscuz-loading' src='" . plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/loading.gif") . "' />";
        }
        $html .= "</div>";
        return $html;
    }

    public function getActivityPage() {
        ob_start();
        include_once WPDISCUZ_DIR_PATH . "/utils/layouts/activity/activity-page.php";
        $html = ob_get_clean();
        wp_die($html);
    }

    private function getSubscriptionsTitleHtml() {
        ob_start();
        include_once WPDISCUZ_DIR_PATH . "/utils/layouts/subscriptions/title.php";
        return ob_get_clean();
    }

    private function getSubscriptionsContentHtml($currentUserId, $currentUserEmail, $isFirstTab) {
        $html = "<div id='wpd-content-item-2' class='wpd-content-item'>";
        if ($isFirstTab) {
            include_once WPDISCUZ_DIR_PATH . "/utils/layouts/subscriptions/content.php";
        } else {
            $html .= "<img alt='wpdiscuz-loading' src='" . plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/loading.gif") . "' />";
        }
        $html .= "</div>";
        return $html;
    }

    public function getSubscriptionsPage() {
        ob_start();
        include_once WPDISCUZ_DIR_PATH . "/utils/layouts/subscriptions/subscriptions-page.php";
        $html = ob_get_clean();
        wp_die($html);
    }

    private function getFollowsTitleHtml() {
        ob_start();
        include_once WPDISCUZ_DIR_PATH . "/utils/layouts/follows/title.php";
        return ob_get_clean();
    }

    private function getFollowsContentHtml($currentUserId, $currentUserEmail, $isFirstTab) {
        $html = "<div id='wpd-content-item-3' class='wpd-content-item'>";
        if ($isFirstTab) {
            include_once WPDISCUZ_DIR_PATH . "/utils/layouts/follows/content.php";
        } else {
            $html .= "<img alt='wpdiscuz-loading' src='" . plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/loading.gif") . "' />";
        }
        $html .= "</div>";
        return $html;
    }

    public function getFollowsPage() {
        ob_start();
        include_once WPDISCUZ_DIR_PATH . "/utils/layouts/follows/follows-page.php";
        $html = ob_get_clean();
        wp_die($html);
    }

    public static function fixEmailFrom($domain) {
        $domain = strtolower($domain);
        if (substr($domain, 0, 4) == "www.") {
            $domain = substr($domain, 4);
        }
        return $domain;
    }

    public function fixLocalhostIp($ip) {
        if (trim($ip) == "::1") {
            $ip = "127.0.0.1";
        }
        return $ip;
    }

    public function commentDataArr($data, $comment, $commentarr) {
        if (!empty($data["wpdiscuz_comment_update"])) {
            $data["comment_date"] = $comment["comment_date"];
            $data["comment_date_gmt"] = $comment["comment_date_gmt"];
        }
        return $data;
    }

    public function getTwitterShareContent($comment_content, $commentLink) {
        $commentLinkLength = strlen($commentLink);
        $twitt_content = "";
        if ($commentLinkLength < 110) {
            $twitt_content = esc_attr(strip_tags($comment_content));
            $length = strlen($twitt_content);
            $twitt_content = function_exists("mb_substr") ? mb_substr($twitt_content, 0, 135 - $commentLinkLength) : substr($twitt_content, 0, 135 - $commentLinkLength);
            if (strlen($twitt_content) < $length) {
                $twitt_content .= "... ";
            }
        }
        return $twitt_content;
    }

    public function getWhatsappShareContent($comment_content, $commentLink) {
        $whatsapp_content = esc_attr(strip_tags($comment_content));
        $length = strlen($whatsapp_content);
        $whatsapp_content = function_exists("mb_substr") ? mb_substr($whatsapp_content, 0, 100) : substr($whatsapp_content, 0, 100);
        if (strlen($whatsapp_content) < $length) {
            $whatsapp_content .= "... ";
        }
        $whatsapp_content = urlencode($whatsapp_content) . ' URL: ' . urlencode($commentLink);
        return $whatsapp_content;
    }

    public function preGetDefaultAvatarForUser($avatar, $idOrEmail, $args) {
        if ($this->options->thread_layouts["changeAvatarsEverywhere"] || isset($args["wpdiscuz_gravatar_user_email"])) {
            $nameAndEmail = $this->getUserNameAndEmail($idOrEmail);
            if ($nameAndEmail["isUser"]) {
                $valid = true;
                if (isset($this->avatars[$nameAndEmail["email"]])) {
                    $valid = $this->avatars[$nameAndEmail["email"]];
                } else if ($this->isValidAvatar($nameAndEmail["email"])) {
                    $this->avatars[$nameAndEmail["email"]] = true;
                } else {
                    $valid = false;
                    $this->avatars[$nameAndEmail["email"]] = false;
                }
                if (!$valid) {
                    $class = ["avatar", "avatar-" . (int) $args["size"], "photo"];
                    if ($args["class"]) {
                        if (is_array($args["class"])) {
                            $class = array_merge($class, $args["class"]);
                        } else {
                            $class[] = $args["class"];
                        }
                    }
                    $url = $this->options->thread_layouts["defaultAvatarUrlForUser"];
                    $url2x = $url;
                    $avatar = sprintf("<img alt='%s' src='%s' srcset='%s' class='%s' height='%d' width='%d' %s/>", esc_attr($args["alt"]), esc_url_raw($url), esc_attr("$url2x 2x"), esc_attr(implode(" ", $class)), esc_attr((int) $args["height"]), esc_attr((int) $args["width"]), $args["extra_attr"]);
                }
            }
        }
        return $avatar;
    }

    public function preGetDefaultAvatarForGuest($avatar, $idOrEmail, $args) {
        if ($this->options->thread_layouts["changeAvatarsEverywhere"] || isset($args["wpdiscuz_gravatar_user_email"])) {
            $nameAndEmail = $this->getUserNameAndEmail($idOrEmail);
            if (!$nameAndEmail["isUser"]) {
                $valid = true;
                if (isset($this->avatars[$nameAndEmail["email"]])) {
                    $valid = $this->avatars[$nameAndEmail["email"]];
                } else if ($this->isValidAvatar($nameAndEmail["email"])) {
                    $this->avatars[$nameAndEmail["email"]] = true;
                } else {
                    $valid = false;
                    $this->avatars[$nameAndEmail["email"]] = false;
                }
                if (!$valid) {
                    $class = ["avatar", "avatar-" . (int) $args["size"], "photo"];
                    if ($args["class"]) {
                        if (is_array($args["class"])) {
                            $class = array_merge($class, $args["class"]);
                        } else {
                            $class[] = $args["class"];
                        }
                    }
                    $url = $this->options->thread_layouts["defaultAvatarUrlForGuest"];
                    $url2x = $url;
                    $avatar = sprintf("<img alt='%s' src='%s' srcset='%s' class='%s' height='%d' width='%d' %s/>", esc_attr($args["alt"]), esc_url_raw($url), esc_attr("$url2x 2x"), esc_attr(implode(" ", $class)), esc_attr((int) $args["height"]), esc_attr((int) $args["width"]), $args["extra_attr"]);
                }
            }
        }
        return $avatar;
    }

    private function isValidAvatar($email) {
        $url = "http://www.gravatar.com/avatar/" . md5($email) . "?d=404";
        $headers = wp_remote_head($url);
        return !is_wp_error($headers) && 200 === $headers["response"]["code"];
    }

    private function getUserNameAndEmail($idOrEmail) {
        $nameAndEmail = ["name" => "guest", "email" => "unknown@example.com", "isUser" => 0];
        if (is_object($idOrEmail)) {
            if (!empty($idOrEmail->comment_author_email)) {
                $nameAndEmail = ["name" => $idOrEmail->comment_author, "email" => $idOrEmail->comment_author_email, "isUser" => 1];
            }
        } else if (is_numeric($idOrEmail)) {
            $user = get_user_by("id", $idOrEmail);
            if ($user) {
                $nameAndEmail = ["name" => $user->display_name, "email" => $user->user_email, "isUser" => 1];
            }
        } else {
            $user = get_user_by("email", $idOrEmail);
            if ($user) {
                $nameAndEmail = ["name" => $user->display_name, "email" => $user->user_email, "isUser" => 1];
            } else {
                $nameAndEmail["email"] = $idOrEmail;
            }
        }
        return $nameAndEmail;
    }

    public function userMentioning($content, $comment, $args = []) {
        if (apply_filters("wpdiscuz_enable_user_mentioning", true) && !empty($args["is_wpdiscuz_comment"]) && ($users = $this->getMentionedUsers($content))) {
            foreach ($users as $k => $user) {
                $user_link = "";
                if (class_exists("BuddyPress")) {
                    $user_link = bp_core_get_user_domain($user["u_id"]);
                }
                if (class_exists("UM_API")) {
                    um_fetch_user($user["u_id"]);
                    $user_link = um_user_profile_url();
                }
                if (!$user_link) {
                    $user_link = get_author_posts_url($user["u_id"]);
                }
                if ($user_link) {
                    $replacement = "<a href='" . $user_link . "' rel='author'>@" . $user["name"] . "</a>";
                } else {
                    $replacement = "<span>@" . $user["name"] . "</span>";
                }
                $replacement .= "$2";
                $content = preg_replace("/(" . $user["replace"] . ")([\s\n\r\t\@\,\.\!\?\#\$\%\-\:\;\'\"\`\~\)\(\}\{\|\\\[\]]?)/", $replacement, $content);
            }
        }
        return $content;
    }

    public function doShortcode($content, $comment, $args = []) {
        if (!empty($args["is_wpdiscuz_comment"])) {
            return do_shortcode($content);
        }
        return $content;
    }

    public function getMentionedUsers($content) {
        $users = [];
        if (preg_match_all("/(@[^\s\,\@\.\!\?\#\$\%\:\;\'\"\`\~\)\(\}\{\|\\\[\]]*)/is", $content, $nicenames)) {
            $nicenames = array_map("strip_tags", $nicenames[0]);
            $nicenames = array_unique($nicenames);
            foreach ($nicenames as $k => $nicename) {
                if ($user = get_user_by("slug", ltrim($nicename, "@"))) {
                    $users[] = ["replace" => $nicename, "u_id" => $user->ID, "name" => $user->display_name, "email" => $user->user_email];
                }
            }
        }
        return $users;
    }

    public function checkFeedbackShortcodes($post_ID, $post_after, $post_before) {
        if (comments_open($post_ID) && ($form = $this->wpdiscuzForm->getForm($post_ID)) && $form->getFormID()) {
            preg_match_all(self::$inlineFormPattern, $post_before->post_content, $matchesBefore, PREG_SET_ORDER);
            preg_match_all(self::$inlineFormPattern, $post_after->post_content, $matchesAfter, PREG_SET_ORDER);
            if ($matchesAfter || $matchesBefore) {
                $inlineFormsBefore = [];
                $defaultAtts = ["id" => "", "question" => "", "opened" => 0, "content" => ""];
                foreach ($matchesBefore as $k => $matchBefore) {
                    if (isset($matchBefore[3]) && preg_match_all(self::$inlineFormAttsPattern, $matchBefore[3], $attsBefore, PREG_SET_ORDER)) {
                        $atts = [];
                        foreach ($attsBefore as $k1 => $attrBefore) {
                            $atts[$attrBefore[1]] = $attrBefore[2];
                        }
                        $atts = array_merge($defaultAtts, $atts);
                        if (($atts["id"] = trim($atts["id"])) && ($atts["question"] = strip_tags($atts["question"]))) {
                            $inlineFormsBefore[$atts["id"]] = ["question" => $atts["question"], "opened" => $atts["opened"]];
                        }
                    }
                }
                foreach ($matchesAfter as $k => $matchAfter) {
                    if (isset($matchAfter[3])) {
                        if (function_exists("use_block_editor_for_post") && use_block_editor_for_post($post_ID)) {
                            $matchAfter[3] = json_decode('"' . $matchAfter[3] . '"');
                        }
                        if (preg_match_all(self::$inlineFormAttsPattern, $matchAfter[3], $attsAfter, PREG_SET_ORDER)) {
                            $atts = [];
                            foreach ($attsAfter as $k1 => $attrAfter) {
                                $atts[$attrAfter[1]] = $attrAfter[2];
                            }
                            $atts["content"] = $matchAfter[5];
                            $atts = array_merge($defaultAtts, $atts);
                            if (($atts["id"] = trim($atts["id"])) && ($atts["question"] = strip_tags($atts["question"]))) {
                                if (isset($inlineFormsBefore[$atts["id"]])) {
                                    if ($atts["question"] !== $inlineFormsBefore[$atts["id"]]["question"] || $atts["opened"] !== $inlineFormsBefore[$atts["id"]]["opened"]) {
                                        $this->dbManager->updateFeedbackForm($post_ID, $atts["id"], $atts["question"], $atts["opened"]);
                                    }
                                    unset($inlineFormsBefore[$atts["id"]]);
                                } else {
                                    $this->dbManager->addFeedbackForm($post_ID, $atts["id"], $atts["question"], $atts["opened"], $atts["content"]);
                                }
                            }
                        }
                    }
                }
                foreach ($inlineFormsBefore as $uid => $inlineFormBefore) {
                    $this->dbManager->deleteFeedbackForm($post_ID, $uid);
                }
            }
        }
    }

    public function getCommentFormPath($theme) {
        if (file_exists(get_stylesheet_directory() . "/wpdiscuz/comment-form.php")) {
            return get_stylesheet_directory() . "/wpdiscuz/comment-form.php";
        } elseif (file_exists(get_template_directory() . "/comment-form.php")) {
            return get_template_directory() . "/wpdiscuz/comment-form.php";
        } else {
            return apply_filters("wpdiscuz_comment_form_include", $theme . "/comment-form.php");
        }
    }

    public function getWalkerPath($theme) {
        if (file_exists(get_stylesheet_directory() . "/wpdiscuz/class.WpdiscuzWalker.php")) {
            return get_stylesheet_directory() . "/wpdiscuz/class.WpdiscuzWalker.php";
        } elseif (file_exists(get_template_directory() . "/wpdiscuz/class.WpdiscuzWalker.php")) {
            return get_template_directory() . "/wpdiscuz/class.WpdiscuzWalker.php";
        } else {
            return apply_filters("wpdiscuz_walker_include", $theme . "/class.WpdiscuzWalker.php");
        }
    }

    public function scanDir($path) {
        $scannedComponents = scandir($path);
        unset($scannedComponents[0]);
        unset($scannedComponents[1]);
        $components = [];
        foreach ($scannedComponents as $k => $component) {
            if ("index.html" != $component) {
                $components[$component] = $path . $component;
            }
        }
        return $components;
    }

    public function getComponents($theme, $layout) {
        $wpdPath = $theme . "/layouts/{$layout}/";
        $wpdComponents = $this->scanDir($wpdPath);
        $scannedComponents = [];
        if (is_dir(get_stylesheet_directory() . "/wpdiscuz/layouts/" . $layout)) {
            $scannedComponents = $this->scanDir(get_stylesheet_directory() . "/wpdiscuz/layouts/" . $layout . "/");
        } else if (is_dir(get_template_directory() . "/wpdiscuz/layouts/" . $layout)) {
            $scannedComponents = $this->scanDir(get_template_directory() . "/wpdiscuz/layouts/" . $layout . "/");
        }
        $components = array_merge($wpdComponents, $scannedComponents);
        foreach ($components as $key => $component) {
            $components[$key] = file_get_contents($component);
        }
        return $components;
    }

    public function restrictCommentingPerUser($email, $comment_parent, $postId) {
        if ($this->options->moderation["restrictCommentingPerUser"] !== "disable") {
            $args = ["count" => true, "author_email" => $email];
            if ($this->options->moderation["restrictCommentingPerUser"] === "post") {
                $args["post_id"] = $postId;
            }
            if ($this->options->moderation["commentRestrictionType"] === "both") {
                $count = get_comments($args);
                if ($count >= $this->options->moderation["userCommentsLimit"]) {
                    wp_die(esc_html(sprintf($this->options->phrases["wc_not_allowed_to_comment_more_than"], $count)));
                }
            } else if ($this->options->moderation["commentRestrictionType"] === "parent" && !$comment_parent) {
                $args["parent"] = 0;
                $count = get_comments($args);
                if ($count >= $this->options->moderation["userCommentsLimit"]) {
                    wp_die(esc_html(sprintf($this->options->phrases["wc_not_allowed_to_create_comment_thread_more_than"], $count)));
                }
            } else if ($this->options->moderation["commentRestrictionType"] === "reply" && $comment_parent) {
                $args["parent__not_in"] = [0];
                $count = get_comments($args);
                if ($count >= $this->options->moderation["userCommentsLimit"]) {
                    wp_die(esc_html(sprintf($this->options->phrases["wc_not_allowed_to_reply_more_than"], $count)));
                }
            }
        }
    }

    public function getProfileUrl($profile_url, $user) {
        if ($this->options->login["enableProfileURLs"] && $user) {
            if (class_exists("BuddyPress")) {
                $profile_url = bp_core_get_user_domain($user->ID);
            } else if (class_exists("UM_API") || class_exists("UM")) {
                um_fetch_user($user->ID);
                $profile_url = um_user_profile_url();
            } else if (function_exists("WPF")) {
                $profile_url = wpforo_member($user->ID, "profile_url");
            }
        }
        return apply_filters("wpdiscuz_profile_url", $profile_url, $user);
    }

    public function umAuthorName($author_name, $comment) {
        if ($comment->user_id) {
            if (class_exists("UM_API") || class_exists("UM")) {
                um_fetch_user($comment->user_id);
                $author_name = um_user("display_name");
                um_reset_user();
            }
        }
        return $author_name;
    }

    public function multipleBlockquotesToOne($content) {
        $content = preg_replace('~<\/blockquote>\s?<blockquote>~is', '</p><p>', $content);
        $content = preg_replace('~<\/code>\s?<code>~is', '</p><p>', $content);
        return $content;
    }

    public function addRatingResetButton($postType, $post) {
        $form = $this->wpdiscuzForm->getForm($post->ID);
        if ($form->getFormID() && ($form->getEnableRateOnPost() || $form->getRatingsExists())) {
            add_meta_box("wpd_reset_ratings", __("Reset Ratings", "wpdiscuz"), [&$this, "resetRatingsButtons"], $postType, "side", "low");
        }
    }

    public function resetRatingsButtons($post) {
        $form = $this->wpdiscuzForm->getForm($post->ID);
        if ($form->getFormID()) {
            if ($form->getEnableRateOnPost()) {
                ?>
                <script>
                    jQuery(document).ready(function ($) {
                        $('#wpd_reset_post_rating').click(function () {
                            if (confirm('<?php _e("Are you sure you want to reset post rating?") ?>')) {
                                var $this = $(this);
                                $this.attr('disabled', true);
                                $this.next('.wpd_reset_rating_working').show();
                                $.ajax({
                                    url: wpdObject.ajaxUrl,
                                    type: "POST",
                                    data: {
                                        action: 'wpdResetPostRating',
                                        postId: <?php echo $post->ID; ?>
                                    }
                                }).done(function (r) {
                                    $this.next('.wpd_reset_rating_working').hide();
                                    if (r.success) {
                                        var sibling = $this.siblings('.wpd_reset_rating_done');
                                        sibling.show();
                                        setTimeout(function () {
                                            sibling.remove();
                                        }, 3000);
                                    }
                                }).fail(function (jqXHR, textStatus, errorThrown) {
                                    console.log(errorThrown);
                                });
                            }
                        });
                    });
                </script>
                <p id="wpd_reset_post_ratings_wrapper">
                    <button type="button" class="button" id="wpd_reset_post_rating" name="wpd_reset_post_rating"><?php _e("Reset Post Rating", "wpdiscuz"); ?></button>
                    <span class="wpd_reset_rating_working" style="display:none;"><?php _e("Working...", "wpdiscuz"); ?></span>
                    <span class="wpd_reset_rating_done" style="display:none;color:#10b493;"><?php _e("Done", "wpdiscuz"); ?></span>
                </p>
                <?php
            }
            if ($form->getRatingsExists()) {
                ?>
                <script>
                    jQuery(document).ready(function ($) {
                        $('#wpd_reset_fields_ratings').click(function () {
                            if (confirm('<?php _e("Are you sure you want to reset fields ratings?") ?>')) {
                                var $this = $(this);
                                $this.attr('disabled', true);
                                $this.next('.wpd_reset_rating_working').show();
                                $.ajax({
                                    url: wpdObject.ajaxUrl,
                                    type: "POST",
                                    data: {
                                        action: 'wpdResetFieldsRatings',
                                        postId: <?php echo $post->ID; ?>
                                    }
                                }).done(function (r) {
                                    $this.next('.wpd_reset_rating_working').hide();
                                    if (r.success) {
                                        var sibling = $this.siblings('.wpd_reset_rating_done');
                                        sibling.show();
                                        setTimeout(function () {
                                            sibling.remove();
                                        }, 3000);
                                    }
                                }).fail(function (jqXHR, textStatus, errorThrown) {
                                    console.log(errorThrown);
                                });
                            }
                        });
                    });
                </script>
                <p id="wpd_reset_fields_ratings_wrapper">
                    <button type="button" class="button" id="wpd_reset_fields_ratings" name="wpd_reset_fields_ratings"><?php _e("Reset Fields Ratings", "wpdiscuz"); ?></button>
                    <span class="wpd_reset_rating_working" style="display:none;"><?php _e("Working...", "wpdiscuz"); ?></span>
                    <span class="wpd_reset_rating_done" style="display:none;color:#10b493;"><?php _e("Done", "wpdiscuz"); ?></span>
                </p>
                <?php
            }
        }
    }

    /**
     * init wpdiscuz styles
     */
    public function initCustomCss() {
        ob_start();
        $left = ( is_rtl() ) ? 'right' : 'left';
        $right = ( is_rtl() ) ? 'left' : 'right';
        $dark = $this->options->thread_styles["theme"] === "wpd-dark";
        $darkCommentAreaBG = $this->options->thread_styles["darkCommentAreaBG"] ? "background:" . $this->options->thread_styles["darkCommentAreaBG"] . ";" : "";
        $darkCommentTextColor = $this->options->thread_styles["darkCommentTextColor"] ? "color:" . $this->options->thread_styles["darkCommentTextColor"] . ";" : "";
        $darkCommentFieldsBG = $this->options->thread_styles["darkCommentFieldsBG"] ? "background:" . $this->options->thread_styles["darkCommentFieldsBG"] . ";" : "";
        $darkCommentFieldsBorderColor = $this->options->thread_styles["darkCommentFieldsBorderColor"] ? "border: 1px solid " . $this->options->thread_styles["darkCommentFieldsBorderColor"] . ";" : "";
        $darkCommentFieldsTextColor = $this->options->thread_styles["darkCommentFieldsTextColor"] ? "color:" . $this->options->thread_styles["darkCommentFieldsTextColor"] . ";" : "";
        $darkCommentFieldsPlaceholderColor = $this->options->thread_styles["darkCommentFieldsPlaceholderColor"] ? "opacity:1;color:" . $this->options->thread_styles["darkCommentFieldsPlaceholderColor"] . ";" : "";
        $defaultCommentAreaBG = $this->options->thread_styles["defaultCommentAreaBG"] ? "background:" . $this->options->thread_styles["defaultCommentAreaBG"] . ";" : "";
        $defaultCommentTextColor = $this->options->thread_styles["defaultCommentTextColor"] ? "color:" . $this->options->thread_styles["defaultCommentTextColor"] . ";" : "";
        $defaultCommentFieldsBG = $this->options->thread_styles["defaultCommentFieldsBG"] ? "background:" . $this->options->thread_styles["defaultCommentFieldsBorderColor"] . ";" : "";
        $defaultCommentFieldsBorderColor = $this->options->thread_styles["defaultCommentFieldsBorderColor"] ? "border: 1px solid " . $this->options->thread_styles["defaultCommentFieldsBorderColor"] . ";" : "";
        $defaultCommentFieldsTextColor = $this->options->thread_styles["defaultCommentFieldsTextColor"] ? "color:" . $this->options->thread_styles["defaultCommentFieldsTextColor"] . ";" : "";
        $defaultCommentFieldsPlaceholderColor = $this->options->thread_styles["defaultCommentFieldsPlaceholderColor"] ? "opacity:1;color:" . $this->options->thread_styles["defaultCommentFieldsPlaceholderColor"] . ";" : "";
        if ($this->options->thread_styles["theme"] !== "wpd-minimal") {
            $blogRoles = $this->options->labels["blogRoles"];
            if (!$blogRoles) {
                echo ".wc-comment-author a{color:#00B38F;} .wc-comment-label{background:#00B38F;}";
            }
            foreach ($blogRoles as $role => $color) {
                echo "\r\n";
                echo "#wpdcom .wpd-blog-" . $role . " .wpd-comment-label{color: #ffffff; background-color: " . $color . "; border: none;}\r\n";
                echo "#wpdcom .wpd-blog-" . $role . " .wpd-comment-author, #wpdcom .wpd-blog-" . $role . " .wpd-comment-author a{color: " . $color . ";}\r\n";
                if ($role == 'post_author')
                    echo "#wpdcom .wpd-blog-post_author .wpd-avatar img{border-color: " . $color . ";}";
                if ($role != 'subscriber' && $role != 'guest')
                    echo "#wpdcom.wpd-layout-1 .wpd-comment .wpd-blog-" . $role . " .wpd-avatar img{border-color: " . $color . ";}\r\n";
                if ($role == 'administrator' || $role == 'editor' || $role == 'post_author')
                    echo "#wpdcom.wpd-layout-2 .wpd-comment.wpd-reply .wpd-comment-wrap.wpd-blog-" . $role . "{border-" . $left . ": 3px solid " . $color . ";}\r\n";
                if ($role != 'guest')
                    echo "#wpdcom.wpd-layout-2 .wpd-comment .wpd-blog-" . $role . " .wpd-avatar img{border-bottom-color: " . $color . ";}\r\n";
                echo "#wpdcom.wpd-layout-3 .wpd-blog-" . $role . " .wpd-comment-subheader{border-top: 1px dashed " . $color . ";}\r\n";
                if ($role != 'subscriber' && $role != 'guest')
                    echo "#wpdcom.wpd-layout-3 .wpd-reply .wpd-blog-" . $role . " .wpd-comment-right{border-" . $left . ": 1px solid " . $color . ";}\r\n";
            }
            ?>
            <?php echo ( $this->options->thread_styles["commentTextSize"] != '14px') ? "#wpdcom .wpd-comment-text p{font-size:" . $this->options->thread_styles["commentTextSize"] . ";}\r\n" : ""; ?>
            <?php if ($dark) { ?>
                #comments, #respond, .comments-area, #wpdcom.wpd-dark{<?php echo $darkCommentAreaBG . $darkCommentTextColor ?>}
                #wpdcom .ql-editor > *{<?php echo $darkCommentFieldsTextColor ?>}
                #wpdcom .ql-editor::before{<?php echo $darkCommentFieldsPlaceholderColor ?>}
                #wpdcom .ql-toolbar{<?php echo $darkCommentFieldsBorderColor ?>border-top:none;}
                #wpdcom .ql-container{<?php echo $darkCommentFieldsBG . $darkCommentFieldsBorderColor ?>border-bottom:none;}
                #wpdcom .wpd-form-row .wpdiscuz-item input[type="text"], #wpdcom .wpd-form-row .wpdiscuz-item input[type="email"], #wpdcom .wpd-form-row .wpdiscuz-item input[type="url"], #wpdcom .wpd-form-row .wpdiscuz-item input[type="color"], #wpdcom .wpd-form-row .wpdiscuz-item input[type="date"], #wpdcom .wpd-form-row .wpdiscuz-item input[type="datetime"], #wpdcom .wpd-form-row .wpdiscuz-item input[type="datetime-local"], #wpdcom .wpd-form-row .wpdiscuz-item input[type="month"], #wpdcom .wpd-form-row .wpdiscuz-item input[type="number"], #wpdcom .wpd-form-row .wpdiscuz-item input[type="time"], #wpdcom textarea, #wpdcom select{<?php echo $darkCommentFieldsBG . $darkCommentFieldsBorderColor . $darkCommentFieldsTextColor ?>}
                #wpdcom.wpd-dark .wpdiscuz-item.wpd-field-select select.wpdiscuz_select, #wpdcom.wpd-dark select{<?php echo str_replace(';', '!important;', $darkCommentFieldsBG . $darkCommentFieldsBorderColor . $darkCommentFieldsTextColor) ?>}
                #wpdcom .wpd-form-row .wpdiscuz-item textarea{<?php echo $darkCommentFieldsBorderColor ?>}
                #wpdcom input::placeholder, #wpdcom textarea::placeholder, #wpdcom input::-moz-placeholder, #wpdcom textarea::-webkit-input-placeholder{<?php echo $darkCommentFieldsPlaceholderColor ?>}
                #wpdcom .wpd-comment-text{<?php echo $darkCommentTextColor ?>}
                .lity-wrap .wpd-item a{color: #666;} .lity-wrap .wpd-item a:hover{color: #222;} .wpd-inline-shortcode.wpd-active{background-color: #666;}
            <?php } else { ?>
                #comments, #respond, .comments-area, #wpdcom{<?php echo $defaultCommentAreaBG ?>}
                #wpdcom .ql-editor > *{<?php echo $defaultCommentFieldsTextColor ?>}
                #wpdcom .ql-editor::before{<?php echo $defaultCommentFieldsPlaceholderColor ?>}
                #wpdcom .ql-toolbar{<?php echo $defaultCommentFieldsBorderColor ?>border-top:none;}
                #wpdcom .ql-container{<?php echo $defaultCommentFieldsBG . $defaultCommentFieldsBorderColor ?>border-bottom:none;}
                #wpdcom .wpd-form-row .wpdiscuz-item input[type="text"], #wpdcom .wpd-form-row .wpdiscuz-item input[type="email"], #wpdcom .wpd-form-row .wpdiscuz-item input[type="url"], #wpdcom .wpd-form-row .wpdiscuz-item input[type="color"], #wpdcom .wpd-form-row .wpdiscuz-item input[type="date"], #wpdcom .wpd-form-row .wpdiscuz-item input[type="datetime"], #wpdcom .wpd-form-row .wpdiscuz-item input[type="datetime-local"], #wpdcom .wpd-form-row .wpdiscuz-item input[type="month"], #wpdcom .wpd-form-row .wpdiscuz-item input[type="number"], #wpdcom .wpd-form-row .wpdiscuz-item input[type="time"], #wpdcom textarea, #wpdcom select{<?php echo $defaultCommentFieldsBG . $defaultCommentFieldsBorderColor . $defaultCommentTextColor ?>}
                #wpdcom .wpd-form-row .wpdiscuz-item textarea{<?php echo $defaultCommentFieldsBorderColor ?>}
                #wpdcom input::placeholder, #wpdcom textarea::placeholder, #wpdcom input::-moz-placeholder, #wpdcom textarea::-webkit-input-placeholder{<?php echo $defaultCommentFieldsPlaceholderColor ?>}
                #wpdcom .wpd-comment-text{<?php echo $defaultCommentTextColor ?>}
            <?php } ?>
            #wpdcom .wpd-thread-head .wpd-thread-info{ border-bottom:2px solid <?php echo $this->options->thread_styles["primaryColor"]; ?>;}
            #wpdcom .wpd-thread-head .wpd-thread-info.wpd-reviews-tab svg{fill: <?php echo $this->options->thread_styles["primaryColor"]; ?>;}
            #wpdcom .wpd-thread-head .wpdiscuz-user-settings{border-bottom: 2px solid <?php echo $this->options->thread_styles["primaryColor"]; ?>;}
            #wpdcom .wpd-thread-head .wpdiscuz-user-settings:hover{color: <?php echo $this->options->thread_styles["primaryColor"]; ?>;}
            #wpdcom .wpd-comment .wpd-follow-link:hover{color: <?php echo $this->options->thread_styles["primaryColor"]; ?>;}
            #wpdcom .wpd-comment-status .wpd-sticky{color: <?php echo $this->options->thread_styles["primaryColor"]; ?>;}
            #wpdcom .wpd-thread-filter .wpdf-active{color: <?php echo $this->options->thread_styles["primaryColor"]; ?>; border-bottom-color:<?php echo $this->options->thread_styles["primaryColor"]; ?>;}
            #wpdcom .wpd-comment-info-bar {border: 1px dashed <?php echo $this->colorBrightness($this->options->thread_styles["primaryColor"], '0.2'); ?>; background: <?php echo $this->colorBrightness($this->options->thread_styles["primaryColor"], '0.9'); ?>; }
            #wpdcom .wpd-comment-info-bar .wpd-current-view i{color: <?php echo $this->options->thread_styles["primaryColor"]; ?>;}
            #wpdcom .wpd-filter-view-all:hover{background: <?php echo $this->options->thread_styles["primaryColor"]; ?>;}
            #wpdcom .wpdiscuz-item .wpdiscuz-rating > label {color: <?php echo $this->options->rating["ratingInactiveColor"]; ?>;}
            #wpdcom .wpdiscuz-item .wpdiscuz-rating:not(:checked) > label:hover,
            .wpdiscuz-rating:not(:checked) > label:hover ~ label {}
            #wpdcom .wpdiscuz-item .wpdiscuz-rating > input ~ label:hover,
            #wpdcom .wpdiscuz-item .wpdiscuz-rating > input:not(:checked) ~ label:hover ~ label,
            #wpdcom .wpdiscuz-item .wpdiscuz-rating > input:not(:checked) ~ label:hover ~ label{color: <?php echo $this->options->rating["ratingHoverColor"]; ?>;}
            #wpdcom .wpdiscuz-item .wpdiscuz-rating > input:checked ~ label:hover,
            #wpdcom .wpdiscuz-item .wpdiscuz-rating > input:checked ~ label:hover,
            #wpdcom .wpdiscuz-item .wpdiscuz-rating > label:hover ~ input:checked ~ label,
            #wpdcom .wpdiscuz-item .wpdiscuz-rating > input:checked + label:hover ~ label,
            #wpdcom .wpdiscuz-item .wpdiscuz-rating > input:checked ~ label:hover ~ label, .wpd-custom-field .wcf-active-star,
            #wpdcom .wpdiscuz-item .wpdiscuz-rating > input:checked ~ label{ color:<?php echo $this->options->rating["ratingActiveColor"]; ?>;}
            #wpd-post-rating .wpd-rating-wrap .wpd-rating-stars svg .wpd-star{fill: <?php echo $this->options->rating["ratingInactiveColor"]; ?>;}
            #wpd-post-rating .wpd-rating-wrap .wpd-rating-stars svg .wpd-active{fill:<?php echo $this->options->rating["ratingActiveColor"]; ?>;}
            #wpd-post-rating .wpd-rating-wrap .wpd-rate-starts svg .wpd-star{fill:<?php echo $this->options->rating["ratingInactiveColor"]; ?>;}
            #wpd-post-rating .wpd-rating-wrap .wpd-rate-starts:hover svg .wpd-star{fill:<?php echo $this->options->rating["ratingHoverColor"]; ?>;}
            #wpd-post-rating.wpd-not-rated .wpd-rating-wrap .wpd-rate-starts svg:hover ~ svg .wpd-star{ fill:<?php echo $this->options->rating["ratingInactiveColor"]; ?>;}
            .wpdiscuz-post-rating-wrap .wpd-rating .wpd-rating-wrap .wpd-rating-stars svg .wpd-star{fill:<?php echo $this->options->rating["ratingInactiveColor"]; ?>;}
            .wpdiscuz-post-rating-wrap .wpd-rating .wpd-rating-wrap .wpd-rating-stars svg .wpd-active{fill:<?php echo $this->options->rating["ratingActiveColor"]; ?>;}
            #wpdcom .wpd-comment .wpd-follow-active{color:#ff7a00;}
            #wpdcom .page-numbers{color:#555;border:#555 1px solid;}
            #wpdcom span.current{background:#555;}
            #wpdcom.wpd-layout-1 .wpd-new-loaded-comment > .wpd-comment-wrap > .wpd-comment-right{background:<?php echo $this->options->thread_styles["newLoadedCommentBGColor"]; ?>;}
            #wpdcom.wpd-layout-2 .wpd-new-loaded-comment.wpd-comment > .wpd-comment-wrap > .wpd-comment-right{background:<?php echo $this->options->thread_styles["newLoadedCommentBGColor"]; ?>;}
            #wpdcom.wpd-layout-2 .wpd-new-loaded-comment.wpd-comment.wpd-reply > .wpd-comment-wrap > .wpd-comment-right{background:transparent;}
            #wpdcom.wpd-layout-2 .wpd-new-loaded-comment.wpd-comment.wpd-reply > .wpd-comment-wrap {background:<?php echo $this->options->thread_styles["newLoadedCommentBGColor"]; ?>;}
            #wpdcom.wpd-layout-3 .wpd-new-loaded-comment.wpd-comment > .wpd-comment-wrap > .wpd-comment-right{background:<?php echo $this->options->thread_styles["newLoadedCommentBGColor"]; ?>;}
            #wpdcom .wpd-follow:hover i, #wpdcom .wpd-unfollow:hover i, #wpdcom .wpd-comment .wpd-follow-active:hover i{color:<?php echo $this->options->thread_styles["primaryColor"]; ?>;}
            #wpdcom .wpdiscuz-readmore{cursor:pointer;color:<?php echo $this->options->thread_styles["primaryColor"]; ?>;}
            .wpd-custom-field .wcf-pasiv-star, #wpcomm .wpdiscuz-item .wpdiscuz-rating > label {color: <?php echo $this->options->rating["ratingInactiveColor"]; ?>;}
            .wpd-wrapper .wpd-list-item.wpd-active{border-top: 3px solid <?php echo $this->options->thread_styles["primaryColor"]; ?>;}
            #wpdcom.wpd-layout-2 .wpd-comment.wpd-reply.wpd-unapproved-comment .wpd-comment-wrap{border-<?php echo $left ?>: 3px solid <?php echo $this->options->thread_styles["newLoadedCommentBGColor"]; ?>;}
            #wpdcom.wpd-layout-3 .wpd-comment.wpd-reply.wpd-unapproved-comment .wpd-comment-right{border-<?php echo $left ?>: 1px solid <?php echo $this->options->thread_styles["newLoadedCommentBGColor"]; ?>;}
            #wpdcom .wpd-prim-button{background-color: <?php echo $this->options->thread_styles["primaryButtonBG"]; ?>; color: <?php echo $this->options->thread_styles["primaryButtonColor"]; ?>;}
            #wpdcom .wpd_label__check i.wpdicon-on{color: <?php echo $this->options->thread_styles["primaryButtonBG"]; ?>; border: 1px solid <?php echo $this->colorBrightness($this->options->thread_styles["primaryButtonBG"], 0.5); ?>;}
            #wpd-bubble-wrapper #wpd-bubble-all-comments-count{color:<?php echo $this->options->thread_styles["bubbleColors"]; ?>;}
            #wpd-bubble-wrapper > div{background-color:<?php echo $this->options->thread_styles["bubbleColors"]; ?>;}
            #wpd-bubble-wrapper > #wpd-bubble #wpd-bubble-add-message{background-color:<?php echo $this->options->thread_styles["bubbleColors"]; ?>;}
            #wpd-bubble-wrapper > #wpd-bubble #wpd-bubble-add-message::before{border-left-color:<?php echo $this->options->thread_styles["bubbleColors"]; ?>; border-right-color:<?php echo $this->options->thread_styles["bubbleColors"]; ?>;}
            #wpd-bubble-wrapper.wpd-right-corner > #wpd-bubble #wpd-bubble-add-message::before{border-left-color:<?php echo $this->options->thread_styles["bubbleColors"]; ?>; border-right-color:<?php echo $this->options->thread_styles["bubbleColors"]; ?>;}
            .wpd-inline-icon-wrapper path.wpd-inline-icon-first{fill:<?php echo $this->options->thread_styles["inlineFeedbackColors"]; ?>;}
            .wpd-inline-icon-count{background-color:<?php echo $this->options->thread_styles["inlineFeedbackColors"]; ?>;}
            .wpd-inline-icon-count::before{border-<?php echo $right ?>-color:<?php echo $this->options->thread_styles["inlineFeedbackColors"]; ?>;}
            .wpd-inline-form-wrapper::before{border-bottom-color:<?php echo $this->options->thread_styles["inlineFeedbackColors"]; ?>;}
            .wpd-inline-form-question{background-color:<?php echo $this->options->thread_styles["inlineFeedbackColors"]; ?>;}
            .wpd-inline-form{background-color:<?php echo $this->options->thread_styles["inlineFeedbackColors"]; ?>;}
            .wpd-last-inline-comments-wrapper{border-color:<?php echo $this->options->thread_styles["inlineFeedbackColors"]; ?>;}
            .wpd-last-inline-comments-wrapper::before{border-bottom-color:<?php echo $this->options->thread_styles["inlineFeedbackColors"]; ?>;}
            .wpd-last-inline-comments-wrapper .wpd-view-all-inline-comments{background:<?php echo $this->options->thread_styles["inlineFeedbackColors"]; ?>;}
            .wpd-last-inline-comments-wrapper .wpd-view-all-inline-comments:hover,.wpd-last-inline-comments-wrapper .wpd-view-all-inline-comments:active,.wpd-last-inline-comments-wrapper .wpd-view-all-inline-comments:focus{background-color:<?php echo $this->options->thread_styles["inlineFeedbackColors"]; ?>;}
            <?php
        }
        ?>
        #wpdcom .ql-snow .ql-tooltip[data-mode="link"]::before{content:"<?php _e("Enter link:", "wpdiscuz"); ?>";}
        #wpdcom .ql-snow .ql-tooltip.ql-editing a.ql-action::after{content:"<?php _e("Save", "wpdiscuz"); ?>";}
        <?php
        do_action("wpdiscuz_dynamic_css", $this->options);
        if ($this->options->thread_styles["theme"] !== "wpd-minimal") {
            echo stripslashes($this->options->thread_styles["customCss"]);
        }
        $css = ob_get_clean();
        /* xMinfy Star ********************************************************* */
        if (apply_filters("wpdiscuz_minify_inline_css", true)) {
            $css = preg_replace('/\/\*((?!\*\/).)*\*\//', "", $css);
            $css = preg_replace('/\s{2,}/', " ", $css);
            $css = preg_replace('/\s*([:;{}])\s*/', "$1", $css);
            $css = preg_replace('/;}/', "}", $css);
        }
        /* xMinify End ********************************************************* */
        return $css;
    }

    /**
     * Increases or decreases the brightness of a color by a percentage of the current brightness.
     *
     * @param   string  $hexCode        Supported formats: `#FFF`, `#FFFFFF`, `FFF`, `FFFFFF`
     * @param   float   $adjustPercent  A number between -1 and 1. E.g. 0.3 = 30% lighter; -0.4 = 40% darker.
     *
     * @return  string
     */
    public function colorBrightness($hexCode, $adjustPercent = 1) {
        if (!$hexCode)
            return '#000';
        $hexCode = ltrim($hexCode, '#');
        if (strlen($hexCode) == 3) {
            $hexCode = $hexCode[0] . $hexCode[0] . $hexCode[1] . $hexCode[1] . $hexCode[2] . $hexCode[2];
        }
        $hexCode = array_map('hexdec', str_split($hexCode, 2));
        foreach ($hexCode as & $color) {
            $adjustableLimit = $adjustPercent < 0 ? $color : 255 - $color;
            $adjustAmount = ceil($adjustableLimit * $adjustPercent);

            $color = str_pad(dechex($color + $adjustAmount), 2, '0', STR_PAD_LEFT);
        }
        return '#' . implode($hexCode);
    }

}
