<?php

class WpdiscuzDBManager implements WpDiscuzConstants {

    private $db;
    private $usersVoted;
    private $phrases;
    private $emailNotification;
    private $avatarsCache;
    private $followUsers;
    private $feedbackForms;
    private $usersRated;

    function __construct() {
        $this->initDB();
    }

    private function initDB() {
        global $wpdb;
        $this->db = $wpdb;
        $this->usersVoted = $this->db->prefix . "wc_users_voted";
        $this->phrases = $this->db->prefix . "wc_phrases";
        $this->emailNotification = $this->db->prefix . "wc_comments_subscription";
        $this->avatarsCache = $this->db->prefix . "wc_avatars_cache";
        $this->followUsers = $this->db->prefix . "wc_follow_users";
        $this->feedbackForms = $this->db->prefix . "wc_feedback_forms";
        $this->usersRated = $this->db->prefix . "wc_users_rated";
    }

    /**
     * check if table exists in database
     * return true if exists false otherwise
     */
    public function isTableExists($tableName, $isFullname = true) {
        $sql = $isFullname ? "SHOW TABLES LIKE '$tableName'" : "SHOW TABLES LIKE '{$this->db->prefix}{$tableName}'";
        return $this->db->get_var($sql);
    }

    /**
     * create table in db on activation if not exists
     */
    public function dbCreateTables() {
        $this->initDB();
        require_once(ABSPATH . "wp-admin/includes/upgrade.php");
        $charset_collate = $this->db->get_charset_collate();
        $sql = "CREATE TABLE `{$this->usersVoted}`(`id` INT(11) NOT NULL AUTO_INCREMENT,`user_id` VARCHAR(32) NOT NULL, `comment_id` INT(11) NOT NULL, `vote_type` INT(11) DEFAULT NULL, `is_guest` TINYINT(1) DEFAULT 0, `post_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0, `date` INT(11) UNSIGNED NOT NULL DEFAULT 0, PRIMARY KEY (`id`), KEY `user_id` (`user_id`), KEY `comment_id` (`comment_id`),  KEY `vote_type` (`vote_type`), KEY `is_guest` (`is_guest`), KEY `post_id` (`post_id`)) {$charset_collate};";
        maybe_create_table($this->usersVoted, $sql);

        $sql = "CREATE TABLE `{$this->phrases}`(`id` INT(11) NOT NULL AUTO_INCREMENT, `phrase_key` VARCHAR(100) NOT NULL, `phrase_value` TEXT NOT NULL, PRIMARY KEY (`id`), KEY `phrase_key` (`phrase_key`)) {$charset_collate};";
        maybe_create_table($this->phrases, $sql);

        $sql = "CREATE TABLE `{$this->emailNotification}`(`id` INT(11) NOT NULL AUTO_INCREMENT, `email` VARCHAR(100) NOT NULL, `subscribtion_id` INT(11) NOT NULL, `post_id` INT(11) NOT NULL, `subscribtion_type` VARCHAR(20) NOT NULL, `activation_key` VARCHAR(32) NOT NULL, `confirm` TINYINT DEFAULT 0, `subscription_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP, `imported_from` VARCHAR(25) NOT NULL DEFAULT '', PRIMARY KEY (`id`), KEY `subscribtion_id` (`subscribtion_id`), KEY `post_id` (`post_id`), KEY `confirm`(`confirm`), UNIQUE KEY `subscribe_unique_index` (`subscribtion_id`,`email`,`post_id`)) {$charset_collate};";
        maybe_create_table($this->emailNotification, $sql);

        $sql = "CREATE TABLE `{$this->avatarsCache}`(`id` INT(11) NOT NULL AUTO_INCREMENT, `user_id` int(11) NOT NULL DEFAULT 0, `user_email` VARCHAR(100) NOT NULL, `url` VARCHAR(255) NOT NULL, `hash` VARCHAR(32) NOT NULL, `maketime` INT(11) NOT NULL DEFAULT 0, `cached` TINYINT(1) NOT NULL DEFAULT 0, PRIMARY KEY (`id`), KEY `user_id` (`user_id`), UNIQUE KEY `user_email` (`user_email`), KEY `maketime` (`maketime`), KEY `cached` (`cached`)) {$charset_collate};";
        maybe_create_table($this->avatarsCache, $sql);

        $sql = "CREATE TABLE `{$this->followUsers}` (`id` int(11) NOT NULL AUTO_INCREMENT, `post_id` int(11) NOT NULL DEFAULT '0', `user_id` int(11) NOT NULL DEFAULT '0', `user_email` varchar(100) NOT NULL, `user_name` varchar(255) NOT NULL, `follower_id` int(11) NOT NULL DEFAULT '0', `follower_email` varchar(100) NOT NULL, `follower_name` varchar(255) NOT NULL, `activation_key` varchar(32) NOT NULL, `confirm` tinyint(1) NOT NULL DEFAULT '0', `follow_timestamp` int(11) NOT NULL, `follow_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`), KEY `post_id` (`post_id`), KEY `user_id` (`user_id`), KEY `user_email` (`user_email`), KEY `follower_id` (`follower_id`), KEY `follower_email` (`follower_email`), KEY `confirm` (`confirm`), KEY `follow_timestamp` (`follow_timestamp`), UNIQUE KEY `follow_unique_key` (`user_email`, `follower_email`)) {$charset_collate};";
        maybe_create_table($this->followUsers, $sql);

        $sql = "CREATE TABLE `{$this->feedbackForms}` (`id` int(11) NOT NULL AUTO_INCREMENT, `post_id` int(11) NOT NULL DEFAULT 0, `unique_id` VARCHAR(15) NOT NULL, `question` varchar(255) NOT NULL, `opened` TINYINT(4) UNSIGNED NOT NULL DEFAULT 0, `content` LONGTEXT NOT NULL, PRIMARY KEY (`id`), UNIQUE KEY `unique_id` (`unique_id`), KEY `post_id` (`post_id`)) {$charset_collate};";
        maybe_create_table($this->feedbackForms, $sql);

        $sql = "CREATE TABLE `{$this->usersRated}` (`id` int(11) NOT NULL AUTO_INCREMENT, `post_id` int(11) NOT NULL DEFAULT 0, `user_id` int(11) NOT NULL DEFAULT 0, `user_ip` VARCHAR(32) NOT NULL DEFAULT '', `rating` int(11) NOT NULL, `date` INT(11) UNSIGNED NOT NULL DEFAULT 0, PRIMARY KEY (`id`), KEY `post_id` (`post_id`), KEY `user_id` (`user_id`)) {$charset_collate};";
        maybe_create_table($this->usersRated, $sql);
    }

    /**
     * add vote type
     */
    public function addVoteType($userId, $commentId, $voteType, $isUserLoggedIn, $postId, $date) {
        $sql = $this->db->prepare("INSERT INTO `{$this->usersVoted}`(`user_id`, `comment_id`, `vote_type`,`is_guest`,`post_id`,`date`)VALUES(%s,%d,%d,%d,%d,%d);", $userId, $commentId, $voteType, !$isUserLoggedIn, $postId, $date);
        return $this->db->query($sql);
    }

    /**
     * update vote type
     */
    public function updateVoteType($user_id, $comment_id, $vote_type, $date) {
        $sql = $this->db->prepare("UPDATE `{$this->usersVoted}` SET `vote_type` = %d, `date` = %d WHERE (`user_id` = %s OR MD5(`user_id`) = %s) AND `comment_id` = %d", $vote_type, $date, $user_id, $user_id, $comment_id);
        return $this->db->query($sql);
    }

    /**
     * check if the user is already voted on comment or not by user id and comment id
     */
    public function isUserVoted($user_id, $comment_id) {
        $sql = $this->db->prepare("SELECT `vote_type` FROM `{$this->usersVoted}` WHERE (`user_id` = %s OR MD5(`user_id`) = %s) AND `comment_id` = %d;", $user_id, $user_id, $comment_id);
        return $this->db->get_var($sql);
    }

    /**
     * update phrases
     */
    public function deletePhrases() {
        if ($this->isTableExists($this->phrases)) {
            return $this->db->query("TRUNCATE `{$this->phrases}`");
        }
    }

    /**
     * update phrases
     */
    public function updatePhrases($phrases) {
        if ($phrases) {
            foreach ($phrases as $key => $value) {
                $value = stripslashes($value);
                if ($this->isPhraseExists($key)) {
                    $sql = $this->db->prepare("UPDATE `{$this->phrases}` SET `phrase_value` = %s WHERE `phrase_key` = %s;", $value, $key);
                } else {
                    $sql = $this->db->prepare("INSERT INTO `{$this->phrases}`(`phrase_key`, `phrase_value`)VALUES(%s, %s);", $key, $value);
                }
                $this->db->query($sql);
            }
        }
    }

    /**
     * checks if the phrase key exists in database
     */
    public function isPhraseExists($phrase_key) {
        $sql = $this->db->prepare("SELECT `phrase_key` FROM `{$this->phrases}` WHERE `phrase_key` LIKE %s", $phrase_key);
        return $this->db->get_var($sql);
    }

    /**
     * get phrases from db
     */
    public function getPhrases() {
        $sql = "SELECT `phrase_key`, `phrase_value` FROM `{$this->phrases}`;";
        $phrases = $this->db->get_results($sql, ARRAY_A);
        $tmp_phrases = [];
        foreach ($phrases as $k => $phrase) {
            $tmp_phrases[$phrase["phrase_key"]] = $phrase["phrase_value"];
        }
        return $tmp_phrases;
    }

    /**
     * get last comment id from database
     * current post last comment id if post id was passed
     */
    public function getLastCommentId($args) {
        $inlineType = "";
        if (!empty($args["wpdType"]) && $args["wpdType"] === "inline") {
            $inlineType = " INNER JOIN `{$this->db->commentmeta}` AS `cmi` ON `c`.`comment_ID` = `cmi`.`comment_id` AND `cmi`.`meta_key` = '" . self::META_KEY_FEEDBACK_FORM_ID . "'";
        }
        if ($args["post_id"]) {
            $approved = "";
            if ($args["status"] == "all") {
                $approved = " AND `c`.`comment_approved` IN('1','0')";
            } else {
                $approved = " AND `c`.`comment_approved` = '1'";
            }
            $sql = $this->db->prepare("SELECT MAX(`c`.`comment_ID`) FROM `{$this->db->comments}` AS `c`$inlineType WHERE `c`.`comment_post_ID` = %d" . $approved . ";", $args["post_id"]);
        } else {
            $sql = "SELECT MAX(`c`.`comment_ID`) FROM `{$this->db->comments}` AS `c`$inlineType;";
        }
        return intval($this->db->get_var($sql));
    }

    /**
     * retrives new comment ids for live update (UA - Update Automatically)
     */
    public function getNewCommentIds($args, $loadLastCommentId, $email, $visibleCommentIds) {
        $wpdiscuz = wpDiscuz();
        $approved = "";
        if ($args["status"] == "all") {
            $approved = " AND `comment_approved` IN('1','0')";
        } else {
            $approved = " AND `comment_approved` = '1'";
        }
        $visible = "";
        if ($visibleCommentIds) {
            $visible = " AND `comment_ID` NOT IN(" . rtrim($visibleCommentIds, ",") . ")";
        }
        $sqlCommentIds = $this->db->prepare("SELECT `comment_ID` FROM `{$this->db->comments}` WHERE `comment_post_ID` = %d AND `comment_ID` > %d AND `comment_author_email` != %s" . $approved . $visible . " ORDER BY `{$wpdiscuz->options->thread_display["orderCommentsBy"]}` ASC;", $args["post_id"], $loadLastCommentId, $email);
        return $this->db->get_col($sqlCommentIds);
    }

    /**
     * @param type $visibleCommentIds comment ids which is visible at the moment on front end
     * @param type $email the current user email
     * @return type array of author comment ids
     */
    public function getAuthorVisibleComments($args, $visibleCommentIds, $email) {
        $sql = $this->db->prepare("SELECT `comment_ID` FROM `{$this->db->comments}` WHERE `comment_approved` = '1' AND `comment_ID` IN($visibleCommentIds) AND `comment_author_email` = %s;", $email);
        return $this->db->get_col($sql);
    }

    public function getParentCommentsHavingReplies($postId) {
        $sql = $this->db->prepare("SELECT `c1`.`comment_ID` FROM `{$this->db->comments}` AS `c1` INNER JOIN  `{$this->db->comments}` AS `c2` ON `c1`.`comment_post_ID` = `c2`.`comment_post_ID` AND `c2`.`comment_parent` = `c1`.`comment_ID` WHERE `c1`.`comment_post_ID` = %d AND `c1`.`comment_parent` = 0 GROUP BY `c1`.`comment_ID` ORDER BY `c1`.`comment_ID` DESC;", $postId);
        $data = $this->db->get_col($sql);
        return $data;
    }

    /**
     * get first level comments by parent comment id
     */
    public function getCommentsByParentId($commentId) {
        $sql_comments = $this->db->prepare("SELECT `comment_ID` FROM `{$this->db->comments}` WHERE `comment_parent` = %d AND `comment_approved` = '1';", $commentId);
        return $this->db->get_col($sql_comments);
    }

    public function addEmailNotification($subsriptionId, $postId, $email, $subscriptionType, $confirm = 0) {
        if (strpos($email, "@example.com") !== false) {
            return false;
        }
        if ($subscriptionType != self::SUBSCRIPTION_COMMENT) {
            $this->deleteCommentNotifications($subsriptionId, $email);
        }
        $activationKey = md5($email . uniqid() . time());
        $sql = $this->db->prepare("INSERT INTO `{$this->emailNotification}` (`email`, `subscribtion_id`, `post_id`, `subscribtion_type`, `activation_key`,`confirm`) VALUES(%s, %d, %d, %s, %s, %d);", $email, $subsriptionId, $postId, $subscriptionType, $activationKey, $confirm);
        $this->db->query($sql);
        return $this->db->insert_id ? ["id" => $this->db->insert_id, "activation_key" => $activationKey] : false;
    }

    public function getPostNewCommentNotification($post_id, $email) {
        $sql = $this->db->prepare("SELECT `id`, `email`, `activation_key` FROM `{$this->emailNotification}` WHERE `subscribtion_type` = %s AND `confirm` = 1 AND `post_id` = %d  AND `email` != %s;", self::SUBSCRIPTION_POST, $post_id, $email);
        return $this->db->get_results($sql, ARRAY_A);
    }

    public function getAllNewCommentNotification($post_id, $email) {
        $sql = $this->db->prepare("SELECT `id`, `email`, `activation_key` FROM `{$this->emailNotification}` WHERE `subscribtion_type` = %s AND `confirm` = 1 AND `post_id` = %d  AND `email` != %s;", self::SUBSCRIPTION_ALL_COMMENT, $post_id, $email);
        return $this->db->get_results($sql, ARRAY_A);
    }

    public function getNewReplyNotification($comment_id, $email) {
        $sql = $this->db->prepare("SELECT `id`, `email`, `activation_key` FROM `{$this->emailNotification}` WHERE `subscribtion_type` = %s AND `confirm` = 1 AND `subscribtion_id` = %d  AND `email` != %s;", self::SUBSCRIPTION_COMMENT, $comment_id, $email);
        return $this->db->get_results($sql, ARRAY_A);
    }

    public function hasSubscription($postId, $email) {
        $sql = $this->db->prepare("SELECT `subscribtion_type` as `type`, `confirm`, `id`, `activation_key` FROM `{$this->emailNotification}` WHERE  `post_id` = %d AND `email` = %s;", $postId, $email);
        $result = $this->db->get_row($sql, ARRAY_A);
        return $result;
    }

    public function hasConfirmedSubscription($email) {
        $sql = "SELECT `subscribtion_type` as `type` FROM `{$this->emailNotification}` WHERE `email` = %s AND `confirm` = 1;";
        $sql = $this->db->prepare($sql, $email);
        return $this->db->get_var($sql);
    }

    public function hasConfirmedSubscriptionByID($subscribeID) {
        $sql = "SELECT `subscribtion_type` as `type` FROM `{$this->emailNotification}` WHERE `id` = %d AND `confirm` = 1;";
        $sql = $this->db->prepare($sql, $subscribeID);
        return $this->db->get_var($sql);
    }

    /**
     * delete comment thread subscriptions if new subscription type is post
     */
    public function deleteCommentNotifications($post_id, $email) {
        $sql = $this->db->prepare("DELETE FROM `{$this->emailNotification}` WHERE `subscribtion_type` != %s AND `post_id` = %d AND `email` LIKE %s;", self::SUBSCRIPTION_POST, $post_id, $email);
        $this->db->query($sql);
    }

    /**
     * create unsubscribe link
     */
    public function unsubscribeLink($postID, $email) {
        global $wp_rewrite;
        $wc_unsubscribe = $this->getUnsubscribeLinkParams($postID, $email);
        $post_id = $wc_unsubscribe["post_id"];
        $wc_unsubscribe_link = !$wp_rewrite->using_permalinks() ? get_permalink($post_id) . "&" : get_permalink($post_id) . "?";
        $wc_unsubscribe_link .= "wpdiscuzUrlAnchor&wpdiscuzSubscribeID=" . $wc_unsubscribe["id"] . "&key=" . $wc_unsubscribe["activation_key"] . "&#wc_unsubscribe_message";
        return esc_url_raw($wc_unsubscribe_link);
    }

    public function getUnsubscribeLinkParams($postID, $email) {
        $sql_subscriber_data = $this->db->prepare("SELECT `id`, `post_id`, `activation_key` FROM `{$this->emailNotification}` WHERE  `post_id` = %d  AND `email` LIKE %s", $postID, $email);
        return $this->db->get_row($sql_subscriber_data, ARRAY_A);
    }

    /**
     * generate confirm link
     */
    public function confirmLink($id, $activationKey, $postID) {
        global $wp_rewrite;
        $wc_confirm_link = !$wp_rewrite->using_permalinks() ? get_permalink($postID) . "&" : get_permalink($postID) . "?";
        $wc_confirm_link .= "wpdiscuzUrlAnchor&wpdiscuzConfirmID=$id&wpdiscuzConfirmKey=$activationKey&wpDiscuzComfirm=yes#wc_unsubscribe_message";
        return esc_url_raw($wc_confirm_link);
    }

    /**
     * Confirm  post or comment subscription
     */
    public function notificationConfirm($subscribe_id, $key) {
        $sql_confirm = $this->db->prepare("UPDATE `{$this->emailNotification}` SET `confirm` = 1 WHERE `id` = %d AND `activation_key` LIKE %s;", $subscribe_id, $key);
        return $this->db->query($sql_confirm);
    }

    /**
     * delete subscription
     */
    public function unsubscribe($id, $activation_key) {
        $sql_unsubscribe = $this->db->prepare("DELETE FROM `{$this->emailNotification}` WHERE `id` = %d AND `activation_key` LIKE %s", $id, $activation_key);
        return $this->db->query($sql_unsubscribe);
    }

    public function alterPhrasesTable() {
        $sql_alter = "ALTER TABLE `{$this->phrases}` MODIFY `phrase_value` TEXT NOT NULL;";
        $this->db->query($sql_alter);
    }

    public function alterVotingTable() {
        $sql_alter = "ALTER TABLE `{$this->usersVoted}` MODIFY `user_id` VARCHAR(255) NOT NULL, ADD COLUMN `is_guest` TINYINT(1) DEFAULT 0, ADD INDEX `is_guest` (`is_guest`);";
        $this->db->query($sql_alter);
    }

    public function alterVotingTableForDateAndPostId() {
        $sql_alter = "ALTER TABLE `{$this->usersVoted}` ADD COLUMN `post_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0, ADD COLUMN `date` INT(11) UNSIGNED NOT NULL DEFAULT 0, ADD INDEX `post_id` (`post_id`);";
        $this->db->query($sql_alter);
    }

    public function alterNotificationTable() {
        $sql_alter = "ALTER TABLE `{$this->emailNotification}` DROP INDEX subscribe_unique_index, ADD UNIQUE KEY `subscribe_unique_index` (`subscribtion_id`,`email`, `post_id`);";
        $this->db->query($sql_alter);
    }

    public function alterSubscriptionTable() {
        $sql_alter = "ALTER TABLE `{$this->emailNotification}` ADD COLUMN `imported_from` VARCHAR(25) NOT NULL DEFAULT '';";
        $this->db->query($sql_alter);
    }

    /**
     * return users id who have published posts
     */
    public function getPostsAuthors() {
        if (($postsAuthors = get_transient(self::TRS_POSTS_AUTHORS)) === false) {
            $sql = "SELECT `post_author` FROM `{$this->db->posts}` WHERE `post_type` = 'post' AND `post_status` IN ('publish', 'private') GROUP BY `post_author`;";
            $postsAuthors = $this->db->get_col($sql);
            set_transient(self::TRS_POSTS_AUTHORS, $postsAuthors, 6 * HOUR_IN_SECONDS);
        }
        return $postsAuthors;
    }

    public function removeVotes() {
        $sqlTruncate = "TRUNCATE `{$this->usersVoted}`;";
        $sqlDelete = "DELETE FROM `{$this->db->commentmeta}` WHERE `meta_key` = '" . self::META_KEY_VOTES . "' OR `meta_key` = '" . self::META_KEY_VOTES_SEPARATE . "';";
        return $this->db->query($sqlTruncate) && $this->db->query($sqlDelete);
    }

    public function getVotes($commentId) {
        $sql = $this->db->prepare("SELECT COUNT(`id`) FROM `{$this->usersVoted}` WHERE `vote_type` = 1 AND `comment_id` = %d UNION ALL SELECT COUNT(`id`) FROM `{$this->usersVoted}` WHERE `vote_type` = -1 AND `comment_id` = %d", $commentId, $commentId);
        return $this->db->get_col($sql);
    }

    /* MULTI SITE */

    public function getBlogID() {
        return $this->db->blogid;
    }

    public function getBlogIDs() {
        return $this->db->get_col("SELECT blog_id FROM `{$this->db->blogs}`");
    }

    public function dropTables() {
        $this->initDB();
        $this->db->query("DROP TABLE IF EXISTS `{$this->emailNotification}`");
        $this->db->query("DROP TABLE IF EXISTS `{$this->phrases}`");
        $this->db->query("DROP TABLE IF EXISTS `{$this->usersVoted}`");
        $this->db->query("DROP TABLE IF EXISTS `{$this->avatarsCache}`");
        $this->db->query("DROP TABLE IF EXISTS `{$this->followUsers}`");
        $this->db->query("DROP TABLE IF EXISTS `{$this->feedbackForms}`");
    }

    public function deleteSubscriptions($commnetId) {
        if ($cId = intval($commnetId)) {
            $sql = $this->db->prepare("DELETE FROM `{$this->emailNotification}` WHERE `subscribtion_id` = %d;", $cId);
            $this->db->query($sql);
        }
    }

    public function deleteVotes($commnetId) {
        if ($cId = intval($commnetId)) {
            $sql = $this->db->prepare("DELETE FROM `{$this->usersVoted}` WHERE `comment_id` = %d;", $cId);
            $this->db->query($sql);
        }
    }

    public function deleteUserVotes($user_id) {
        $sql = $this->db->prepare("DELETE FROM `{$this->usersVoted}` WHERE `user_id` = %d;", $user_id);
        $this->db->query($sql);
    }

    /* === GRAVATARS CACHE === */

    public function addGravatars($gravatarsData) {
        if ($gravatarsData && is_array($gravatarsData)) {
            $sql = "INSERT INTO `{$this->avatarsCache}`(`user_id`, `user_email`, `url`, `hash`, `maketime`, `cached`) VALUES";
            $sqlValues = "";
            $makeTime = current_time("timestamp");
            foreach ($gravatarsData as $k => $gravatarData) {
                $userId = intval($gravatarData["user_id"]);
                $userEmail = str_rot13(trim($gravatarData["user_email"]));
                $url = trim($gravatarData["url"]);
                $hash = trim($gravatarData["hash"]);
                $cached = intval($gravatarData["cached"]);
                $sqlValues .= "($userId, '$userEmail', '$url', '$hash', '$makeTime', $cached),";
            }
            $sql .= rtrim($sqlValues, ",");
            $sql .= "ON DUPLICATE KEY UPDATE `user_id` = `user_id`, `user_email` = `user_email`, `url` = `url`, `hash` = `hash`, `maketime` = `maketime`, `cached` = `cached`;";
            $this->db->query($sql);
        }
    }

    public function getGravatars($limit = 10) {
        $data = [];
        $limit = apply_filters("wpdiscuz_gravatars_cache_limit", $limit);
        if ($l = intval($limit)) {
            $sql = $this->db->prepare("SELECT * FROM `{$this->avatarsCache}` WHERE `cached` = 0 LIMIT %d;", $l);
            $data = $this->db->get_results($sql, ARRAY_A);
        }
        return $data;
    }

    public function getExpiredGravatars($timeFrame) {
        $data = [];
        if ($timeFrame) {
            $currentTime = current_time("timestamp");
            $sql = $this->db->prepare("SELECT CONCAT(`hash`, '.gif') FROM `{$this->avatarsCache}` WHERE `maketime` + %d < %d", $timeFrame, $currentTime);
            $data = $this->db->get_col($sql);
        }
        return $data;
    }

    public function deleteExpiredGravatars($timeFrame) {
        if ($timeFrame) {
            $currentTime = current_time("timestamp");
            $sql = $this->db->prepare("DELETE FROM `{$this->avatarsCache}` WHERE `maketime` + %d < %d;", $timeFrame, $currentTime);
            $this->db->query($sql);
        }
    }

    public function deleteGravatars() {
        $this->db->query("TRUNCATE `{$this->avatarsCache}`;");
    }

    public function updateGravatarsStatus($cachedIds) {
        if ($cachedIds) {
            $makeTime = current_time("timestamp");
            $ids = implode(",", $cachedIds);
            $sql = "UPDATE `{$this->avatarsCache}` SET `maketime` = $makeTime, `cached` = 1 WHERE `id` IN ($ids);";
            $this->db->query($sql);
        }
    }

    /* === GRAVATARS CACHE === */

    /* === STCR SUBSCRIPTIONS - Subscribe To Comments Reloaded === */

    public function getStcrAllSubscriptions() {
        $sql = "SELECT COUNT(*) FROM `{$this->db->postmeta}` WHERE meta_key LIKE '%_stcr@%' AND SUBSTRING(meta_value, 21) IN ('Y', 'R');";
        return $this->db->get_var($sql);
    }

    public function getStcrSubscriptions($limit, $offset) {
        $data = [];
        if (intval($limit) && intval($offset) >= 0) {
            $sql = "SELECT `post_id`, SUBSTRING(`meta_key`, 8) AS `email`, SUBSTRING(meta_value, 1, 19) AS `date`, LOWER(SUBSTRING(meta_value, 21)) AS `subscription_type`, 1 AS `status` FROM `{$this->db->postmeta}` WHERE meta_key LIKE '%_stcr@%' AND SUBSTRING(meta_value, 21) IN ('Y', 'R') ORDER BY SUBSTRING(meta_value, 1, 19) ASC LIMIT $offset, $limit;";
            $data = $this->db->get_results($sql, ARRAY_A);
        }
        return $data;
    }

    public function addStcrSubscriptions($subscriptions = []) {
        foreach ($subscriptions as $k => $subscription) {
            $email = $subscription["email"];
            $subscriptionId = $subscription["post_id"];
            $postId = $subscription["post_id"];
            $subscriptionType = $subscription["subscription_type"] == "y" ? self::SUBSCRIPTION_POST : self::SUBSCRIPTION_ALL_COMMENT;
            $activationKey = md5($email . uniqid() . time());
            $subscriptionDate = $subscription["date"];
            $confirm = $subscription["status"];
            $userSubscription = $this->getUserSubscription($email, $postId);
            $importedFrom = "subscribe-to-comments-reloaded"; // this is a slug in wp repo

            if ($userSubscription) {
                if ($userSubscription["type"] == self::SUBSCRIPTION_POST) {
                    continue;
                } else {
                    $sql = "UPDATE `{$this->emailNotification}` SET `subscribtion_id` = %d, `post_id` = %d, `subscribtion_type` = %s, `imported_from` = %s WHERE `id` = %d;";
                    $sql = $this->db->prepare($sql, $subscriptionId, $postId, $subscriptionType, $importedFrom, $userSubscription["id"]);
                    $this->db->query($sql);
                }
            } else {
                $sql = "INSERT INTO `{$this->emailNotification}` (`email`, `subscribtion_id`, `post_id`, `subscribtion_type`, `activation_key`, `confirm`, `subscription_date`, `imported_from`) VALUES (%s, %d, %d, %s, %s, %d, %s, %s);";
                $sql = $this->db->prepare($sql, $email, $postId, $postId, $subscriptionType, $activationKey, $confirm, $subscriptionDate, $importedFrom);
                $this->db->query($sql);
            }
        }
    }

    public function getUserSubscription($email, $postId) {
        $sql = "SELECT `id`, `subscribtion_type` as `type` FROM `{$this->emailNotification}` WHERE `email` = %s AND `post_id` = %d AND `confirm` = 1;";
        $sql = $this->db->prepare($sql, $email, $postId);
        return $this->db->get_row($sql, ARRAY_A);
    }

    /* === STCR SUBSCRIPTIONS - Subscribe To Comments Reloaded === */

    /* === LSTC SUBSCRIPTIONS - Lightweight Subscribe To Comments === */

    // TODO       

    public function getLstcAllSubscriptions() {
        $sql = "SELECT COUNT(*) FROM `{$this->db->prefix}comment_notifier`;";
        return $this->db->get_var($sql);
    }

    public function getLstcSubscriptions($limit, $offset) {
        $data = [];
        if (intval($limit) && intval($offset) >= 0) {
            $sql = "SELECT `post_id`, `email`, NOW() AS `date`, 'post' AS `subscription_type`, 1 AS `status` FROM `{$this->db->prefix}comment_notifier` ORDER BY `id` ASC LIMIT $offset, $limit;";
            $data = $this->db->get_results($sql, ARRAY_A);
        }
        return $data;
    }

    public function addLstcSubscriptions($subscriptions = []) {
        foreach ($subscriptions as $k => $subscription) {
            $email = $subscription["email"];
            $subscriptionId = $subscription["post_id"];
            $postId = $subscription["post_id"];
            $subscriptionType = self::SUBSCRIPTION_POST;
            $activationKey = md5($email . uniqid() . time());
            $subscriptionDate = $subscription["date"];
            $confirm = $subscription["status"];
            $userSubscription = $this->getUserSubscription($email, $postId);
            $importedFrom = "comment-notifier-no-spammers"; // this is a slug in wp repo

            if ($userSubscription) {
                if ($userSubscription["type"] == self::SUBSCRIPTION_POST) {
                    continue;
                } else {
                    $sql = "UPDATE `{$this->emailNotification}` SET `subscribtion_id` = %d, `post_id` = %d, `subscribtion_type` = %s, `imported_from` = %s WHERE `id` = %d;";
                    $sql = $this->db->prepare($sql, $subscriptionId, $postId, $subscriptionType, $importedFrom, $userSubscription["id"]);
                    $this->db->query($sql);
                }
            } else {
                $sql = "INSERT INTO `{$this->emailNotification}` (`email`, `subscribtion_id`, `post_id`, `subscribtion_type`, `activation_key`, `confirm`, `subscription_date`, `imported_from`) VALUES (%s, %d, %d, %s, %s, %d, %s, %s);";
                $sql = $this->db->prepare($sql, $email, $postId, $postId, $subscriptionType, $activationKey, $confirm, $subscriptionDate, $importedFrom);
                $this->db->query($sql);
            }
        }
    }

    /* === LSTC SUBSCRIPTIONS - Lightweight Subscribe To Comments === */

    /* === STATISTICS === */

    public function getCommentsCount() {
        $sql = "SELECT COUNT(*) FROM `{$this->db->comments}` WHERE `comment_approved` = '1';";
        return number_format(intval($this->db->get_var($sql)));
    }

    public function getInlineCommentsCount() {
        $sql = "SELECT COUNT(*) FROM `{$this->db->comments}` as `c` INNER JOIN `{$this->db->commentmeta}` as `cm` ON `cm`.`comment_id` = `c`.`comment_ID` AND `cm`.`meta_key` = '" . self::META_KEY_FEEDBACK_FORM_ID . "' WHERE `comment_approved` = '1';";
        return number_format(intval($this->db->get_var($sql)));
    }

    public function getThreadsCount() {
        $sql = "SELECT COUNT(*) FROM `{$this->db->comments}` WHERE `comment_approved` = '1' AND `comment_parent` = 0;";
        return number_format(intval($this->db->get_var($sql)));
    }

    public function getRepliesCount() {
        $sql = "SELECT COUNT(*) FROM `{$this->db->comments}` WHERE `comment_approved` = '1' AND `comment_parent` != 0;";
        return number_format(intval($this->db->get_var($sql)));
    }

    public function getUserCommentersCount() {
        $sql = "SELECT COUNT(*) FROM `{$this->db->comments}` WHERE `user_id` != 0 AND `comment_approved` = '1';";
        return number_format(intval($this->db->get_var($sql)));
    }

    public function getGuestCommentersCount() {
        $sql = "SELECT COUNT(*) FROM `{$this->db->comments}` WHERE `user_id` = 0 AND `comment_approved` = '1';";
        return number_format(intval($this->db->get_var($sql)));
    }

    public function getAllSubscribersCount() {
        $sql = "SELECT COUNT(DISTINCT `email`) FROM `{$this->emailNotification}` WHERE `confirm` = 1;";
        return number_format(intval($this->db->get_var($sql)));
    }

    public function getPostSubscribersCount() {
        $sql = "SELECT COUNT(DISTINCT `email`) FROM `{$this->emailNotification}` WHERE `confirm` = 1 AND `subscribtion_type` = '" . self::SUBSCRIPTION_POST . "';";
        return number_format(intval($this->db->get_var($sql)));
    }

    public function getAllCommentSubscribersCount() {
        $sql = "SELECT COUNT(DISTINCT `email`) FROM `{$this->emailNotification}` WHERE `confirm` = 1 AND `subscribtion_type` = '" . self::SUBSCRIPTION_ALL_COMMENT . "';";
        return number_format(intval($this->db->get_var($sql)));
    }

    public function getCommentSubscribersCount() {
        $sql = "SELECT COUNT(DISTINCT `email`) FROM `{$this->emailNotification}` WHERE `confirm` = 1 AND `subscribtion_type` = '" . self::SUBSCRIPTION_COMMENT . "';";
        return number_format(intval($this->db->get_var($sql)));
    }

    public function getFollowersCount() {
        $sql = "SELECT COUNT(DISTINCT `follower_id`) FROM `{$this->followUsers}` WHERE `confirm` = 1;";
        return number_format(intval($this->db->get_var($sql)));
    }

    public function getFollowingCount() {
        $sql = "SELECT COUNT(DISTINCT `user_id`) FROM `{$this->followUsers}` WHERE `confirm` = 1;";
        return number_format(intval($this->db->get_var($sql)));
    }

    public function getGraphAllComments($interval) {
        $date = new DateTime();
        if ($interval === 'week') {
            $date->modify('-7 days');
        } else if ($interval === 'month') {
            $date->modify('-1 month');
        } else if ($interval === '6months') {
            $date->modify('-6 months');
        } else if ($interval === 'year') {
            $date->modify('-1 year');
        }
        $sql = "SELECT COUNT(`comment_ID`) AS `count`, SUBSTR(`comment_date_gmt`, 1, 10) AS `date` FROM `{$this->db->comments}` WHERE `comment_approved` = '1'" . ($interval === "all" ? "" : " AND `comment_date_gmt` > '{$date->format('Y-m-d')}'") . " GROUP BY `date`;";
        $results = $this->db->get_results($sql, ARRAY_A);
        $data = [];
        foreach ($results as $k => $val) {
            $data[esc_html(strtotime($val["date"]))] = esc_html(intval($val["count"]));
        }
        return $data;
    }

    public function getGraphInlineComments($interval) {
        $date = new DateTime();
        if ($interval === 'week') {
            $date->modify('-7 days');
        } else if ($interval === 'month') {
            $date->modify('-1 month');
        } else if ($interval === '6months') {
            $date->modify('-6 months');
        } else if ($interval === 'year') {
            $date->modify('-1 year');
        }
        $sql = "SELECT COUNT(`c`.`comment_ID`) AS `count`, SUBSTR(`c`.`comment_date_gmt`, 1, 10) AS `date` FROM `{$this->db->comments}` AS `c` INNER JOIN `{$this->db->commentmeta}` AS `cm` ON `cm`.`comment_id` = `c`.`comment_ID` AND `cm`.`meta_key` = '" . self::META_KEY_FEEDBACK_FORM_ID . "' WHERE `c`.`comment_approved` = '1'" . ($interval === "all" ? "" : " AND `c`.`comment_date_gmt` > '{$date->format('Y-m-d')}'") . " GROUP BY `date`;";
        $results = $this->db->get_results($sql, ARRAY_A);
        $data = [];
        foreach ($results as $k => $val) {
            $data[esc_html(strtotime($val["date"]))] = esc_html(intval($val["count"]));
        }
        return $data;
    }

    public function getActiveUsers($orderby, $order, $page) {
        if ($order === "asc") {
            $order = "ASC";
        } else {
            $order = "DESC";
        }
        $ordering = "`count` $order";
        if ($orderby === "subscriptions") {
            $ordering = "`scount` $order, `count` DESC";
        } else if ($orderby === "following") {
            $ordering = "`ficount` $order, `count` DESC";
        } else if ($orderby === "followers") {
            $ordering = "`fwcount` $order, `count` DESC";
        } else if ($orderby === "last_activity") {
            $ordering = "`last_date` $order, `count` DESC";
        }
        $limit = 6;
        $offset = $page > 0 ? ($page - 1) * $limit : 0;
        $limit++;
        $sql = "SELECT `c`.`comment_author_email`, `c`.`comment_author`, COUNT(`c`.`comment_ID`) AS `count`, IFNULL(`s`.`count`, 0) AS `scount`, IFNULL(`fi`.`count`, 0) AS `ficount`, IFNULL(`fw`.`count`, 0) AS `fwcount`, MAX(`c`.`comment_date_gmt`) AS `last_date` FROM `{$this->db->comments}` AS `c` LEFT JOIN (SELECT `email`, COUNT(`email`) AS `count` FROM `{$this->emailNotification}` WHERE `confirm` = 1 GROUP BY `email`) AS `s` ON `s`.`email` LIKE `c`.`comment_author_email` LEFT JOIN (SELECT `follower_email`, COUNT(`follower_email`) AS `count` FROM `{$this->followUsers}` WHERE `confirm` = 1 GROUP BY `follower_email`) AS `fi` ON `fi`.`follower_email` LIKE `c`.`comment_author_email` LEFT JOIN (SELECT `user_email`, COUNT(`user_email`) AS `count` FROM `{$this->followUsers}` WHERE `confirm` = 1 GROUP BY `user_email`) AS `fw` ON `fw`.`user_email` LIKE `c`.`comment_author_email` WHERE `c`.`comment_approved` = '1' GROUP BY `c`.`comment_author_email`, `c`.`comment_author` ORDER BY $ordering LIMIT $limit OFFSET $offset;";
        return $this->db->get_results($sql, ARRAY_A);
    }

    public function getMostReactedCommentId($postId, $cache = true) {
        if ($cache) {
            $stat = get_post_meta($postId, self::POSTMETA_STATISTICS, true);
            if (!is_array($stat))
                $stat = [];
            if ($stat && isset($stat[self::POSTMETA_REACTED])) {
                $reacted = intval($stat[self::POSTMETA_REACTED]);
            } else {
                $sql = $this->db->prepare("SELECT v.`comment_id` FROM `{$this->usersVoted}` AS `v` INNER JOIN `{$this->db->comments}` AS `c` ON `v`.`comment_id` = `c`.`comment_ID` WHERE `c`.`comment_post_ID`  = %d AND `c`.`comment_approved` = '1' GROUP BY `v`.`comment_id` ORDER BY COUNT(`v`.`comment_id`) DESC, `c`.`comment_ID` DESC LIMIT 1;", $postId);
                $reacted = intval($this->db->get_var($sql));
                $stat[self::POSTMETA_REACTED] = $reacted;
                update_post_meta($postId, self::POSTMETA_STATISTICS, $stat);
            }
        } else {
            $sql = $this->db->prepare("SELECT v.`comment_id` FROM `{$this->usersVoted}` AS `v` INNER JOIN `{$this->db->comments}` AS `c` ON `v`.`comment_id` = `c`.`comment_ID` WHERE `c`.`comment_post_ID`  = %d AND `c`.`comment_approved` = '1' GROUP BY `v`.`comment_id` ORDER BY COUNT(`v`.`comment_id`) DESC, `c`.`comment_ID` DESC LIMIT 1;", $postId);
            $reacted = intval($this->db->get_var($sql));
        }
        return $reacted;
    }

    public function getHottestTree($commentId) {
        $sql = $this->db->prepare("SELECT * FROM (SELECT * FROM `{$this->db->comments}`) `c`,(SELECT @pv := %d) AS `init` WHERE FIND_IN_SET(`c`.`comment_parent`, @pv) AND LENGTH(@pv := CONCAT(@pv, ',', `c`.`comment_ID`))", $commentId);
        $data = $this->db->get_results($sql, ARRAY_A);
        return $data;
    }

    public function deleteStatisticCaches() {
        $sql = "DELETE FROM `{$this->db->postmeta}` WHERE `meta_key` = '" . self::POSTMETA_STATISTICS . "';";
        $this->db->query($sql);
    }

    public function deleteOldStatisticCaches() {
        $sql = "DELETE FROM `{$this->db->options}` WHERE `option_name` LIKE '%wpdiscuz_threads_count_%' OR `option_name` LIKE '%wpdiscuz_replies_count_%' OR `option_name` LIKE '%wpdiscuz_followers_count_%' OR `option_name` LIKE '%wpdiscuz_most_reacted_%' OR `option_name` LIKE '%wpdiscuz_hottest_%' OR `option_name` LIKE '%wpdiscuz_authors_count_%' OR `option_name` LIKE '%wpdiscuz_recent_authors_%';";
        $this->db->query($sql);
    }

    /* === STATISTICS === */

    /* === MODAL === */

    public function getSubscriptionsCount($userEmail) {
        $sql = $this->db->prepare("SELECT COUNT(*) FROM `{$this->emailNotification}` WHERE `email` = %s;", trim($userEmail));
        $result = $this->db->get_var($sql);
        return $result;
    }

    public function getSubscriptions($userEmail, $limit, $offset) {
        $limitCondition = ($l = intval($limit)) > 0 ? "LIMIT $l OFFSET $offset" : "";
        $sql = $this->db->prepare("SELECT * FROM `{$this->emailNotification}` WHERE `email` = %s $limitCondition;", trim($userEmail));
        $result = $this->db->get_results($sql);
        return $result;
    }

    public function unsubscribeById($sId) {
        $sql = $this->db->prepare("DELETE FROM `{$this->emailNotification}` WHERE `id` = %d;", intval($sId));
        $this->db->query($sql);
    }

    public function unsubscribeByEmail($email) {
        $sql = $this->db->prepare("DELETE FROM `{$this->emailNotification}` WHERE `email` = %s;", trim($email));
        $this->db->query($sql);
    }

    // FOLLOWS
    public function getFollowsCount($userEmail) {
        $sql = $this->db->prepare("SELECT COUNT(*) FROM `{$this->followUsers}` WHERE `follower_email` = %s;", trim($userEmail));
        $result = $this->db->get_var($sql);
        return $result;
    }

    public function getFollows($userEmail, $limit, $offset) {
        $limitCondition = ($l = intval($limit)) > 0 ? "LIMIT $l OFFSET $offset" : "";
        $sql = $this->db->prepare("SELECT * FROM `{$this->followUsers}` WHERE `follower_email` = %s $limitCondition;", trim($userEmail));
        $result = $this->db->get_results($sql);
        return $result;
    }

    public function unfollowById($fId) {
        $sql = $this->db->prepare("DELETE FROM `{$this->followUsers}` WHERE `id` = %d;", intval($fId));
        $this->db->query($sql);
    }

    public function unfollowByEmail($email) {
        $sql = $this->db->prepare("DELETE FROM `{$this->followUsers}` WHERE `follower_email` = %s;", trim($email));
        $this->db->query($sql);
    }

    /**
     * remove user related follows
     * @param type $email the user email who other users following
     */
    public function deleteFollowsByEmail($email) {
        $sql = $this->db->prepare("DELETE FROM `{$this->followUsers}` WHERE `user_email` = %s;", trim($email));
        $this->db->query($sql);
    }

    /* === MODAL === */

    /* === FOLLOW USER === */

    public function getUserFollows($followerEmail) {
        $follows = [];
        if ($followerEmail) {
            $sql = $this->db->prepare("SELECT `user_email` FROM `{$this->followUsers}` WHERE `confirm` = 1 AND `follower_email` = %s;", $followerEmail);
            $follows = $this->db->get_col($sql);
        }
        return $follows;
    }

    public function getUserFollowers($userEmail) {
        $followers = [];
        if ($userEmail) {
            $sql = $this->db->prepare("SELECT * FROM `{$this->followUsers}` WHERE `confirm` = 1 AND `user_email` = %s;", $userEmail);
            $followers = $this->db->get_results($sql, ARRAY_A);
        }
        return $followers;
    }

    public function isFollowExists($userEmail, $followerEmail) {
        $exists = false;
        if ($userEmail && $followerEmail) {
            $sql = $this->db->prepare("SELECT `id`, `activation_key`, `confirm` FROM `{$this->followUsers}` WHERE `user_email` = %s AND `follower_email` = %s LIMIT 1;", $userEmail, $followerEmail);
            $exists = $this->db->get_row($sql, ARRAY_A);
        }
        return $exists;
    }

    public function addNewFollow($args) {
        $data = false;
        $postId = isset($args["post_id"]) ? intval($args["post_id"]) : 0;
        $userId = isset($args["user_id"]) ? intval($args["user_id"]) : 0;
        $userEmail = isset($args["user_email"]) ? trim($args["user_email"]) : "";
        $userName = isset($args["user_name"]) ? trim($args["user_name"]) : "";
        $followerId = isset($args["follower_id"]) ? intval($args["follower_id"]) : 0;
        $followerEmail = isset($args["follower_email"]) ? trim($args["follower_email"]) : "";
        $followerName = isset($args["follower_name"]) ? trim($args["follower_name"]) : "";
        $confirm = isset($args["confirm"]) ? intval($args["confirm"]) : 0;

        if (strpos($followerEmail, "@example.com") !== false) {
            return false;
        }

        if ($userEmail && $followerId && $followerEmail) {
            $currentDate = current_time("mysql");
            $currentTimestamp = strtotime($currentDate);
            $activationKey = md5($userEmail . $followerEmail . $currentTimestamp);
            $sql = $this->db->prepare("INSERT INTO `{$this->followUsers}` VALUES (NULL, %d, %d, %s, %s, %d, %s, %s, %s, %d, %d, %s);", $postId, $userId, $userEmail, $userName, $followerId, $followerEmail, $followerName, $activationKey, $confirm, $currentTimestamp, $currentDate);
            $this->db->query($sql);
            if ($this->db->insert_id) {
                $data = ["id" => $this->db->insert_id, "activation_key" => $activationKey];
            }
        }
        return $data;
    }

    public function followConfirmLink($postId, $id, $key) {
        global $wp_rewrite;
        $confirmLink = !$wp_rewrite->using_permalinks() ? get_permalink($postId) . "&" : get_permalink($postId) . "?";
        $confirmLink .= "wpdiscuzUrlAnchor&wpdiscuzFollowID=$id&wpdiscuzFollowKey=$key&wpDiscuzComfirm=1&#wc_follow_message";
        return esc_url_raw($confirmLink);
    }

    public function followCancelLink($postId, $id, $key) {
        global $wp_rewrite;
        $cancelLink = !$wp_rewrite->using_permalinks() ? get_permalink($postId) . "&" : get_permalink($postId) . "?";
        $cancelLink .= "wpdiscuzUrlAnchor&wpdiscuzFollowID=$id&wpdiscuzFollowKey=$key&wpDiscuzComfirm=0#wc_follow_message";
        return esc_url_raw($cancelLink);
    }

    public function confirmFollow($id, $key) {
        $sql = $this->db->prepare("UPDATE `{$this->followUsers}` SET `confirm` = 1 WHERE `id` = %d AND `activation_key` = %s;", intval($id), trim($key));
        return $this->db->query($sql);
    }

    public function cancelFollow($id, $key) {
        $sql = $this->db->prepare("DELETE FROM `{$this->followUsers}` WHERE `id` = %d AND `activation_key` = %s", intval($id), trim($key));
        return $this->db->query($sql);
    }

    public function updateUserInfo($user, $oldUser) {
        $userNewEmail = trim($user->user_email);
        $userOldEmail = trim($oldUser->user_email);
        $userNewName = trim($user->display_name);
        $userOldName = trim($oldUser->display_name);
        if ($userNewEmail != $userOldEmail) {
            $sql = $this->db->prepare("UPDATE `{$this->followUsers}` SET `user_email` = %s WHERE `user_email` = %s AND `follower_email` != %s;", $userNewEmail, $userOldEmail, $userNewEmail);
            $this->db->query($sql);
            $sql = $this->db->prepare("UPDATE `{$this->followUsers}` SET `follower_email` = %s WHERE `follower_email` = %s AND `user_email` != %s;", $userNewEmail, $userOldEmail, $userNewEmail);
            $this->db->query($sql);
            $sql = $this->db->prepare("UPDATE `{$this->emailNotification}` SET `email` = %s WHERE `email` = %s;", $userNewEmail, $userOldEmail);
            $this->db->query($sql);
        }

        if ($userNewName != $userOldName) {
            $sql = $this->db->prepare("UPDATE `{$this->followUsers}` SET `user_name` = %s WHERE `user_name` = %s;", $userNewName, $userOldName);
            $this->db->query($sql);
            $sql = $this->db->prepare("UPDATE `{$this->followUsers}` SET `follower_name` = %s WHERE `follower_name` = %s;", $userNewName, $userOldName);
            $this->db->query($sql);
        }
    }

    /* === Regenerate Vote Metas === */

    public function showVoteRegenerate() {
        $sql = "SELECT `comment_id` FROM `{$this->usersVoted}` LIMIT 1";
        return $this->db->get_var($sql);
    }

    public function getVoteRegenerateCount() {
        $sql = "SELECT COUNT(DISTINCT `comment_id`) FROM `{$this->usersVoted}`";
        return $this->db->get_var($sql);
    }

    public function getVoteRegenerateData($startId, $limit) {
        $sql = $this->db->prepare("SELECT DISTINCT `comment_id` FROM `{$this->usersVoted}` WHERE `comment_id` > %d ORDER BY `comment_id` ASC LIMIT %d;", $startId, $limit);
        return $this->db->get_col($sql);
    }

    public function regenerateVoteMetas($ids) {
        foreach ($ids as $k => $id) {
            $votes = $this->getVotes($id);
            $like = (int) $votes[0];
            $dislike = (int) $votes[1];
            update_comment_meta($id, self::META_KEY_VOTES_SEPARATE, ["like" => $like, "dislike" => $dislike]);
        }
    }

    /* === /Regenerate Vote Metas === */
    /* === Regenerate Vote Data === */

    public function showVoteDataRegenerate() {
        $sql = "SELECT `comment_id` FROM `{$this->usersVoted}` WHERE `post_id` = 0 LIMIT 1";
        return $this->db->get_var($sql);
    }

    public function getVoteDataRegenerateCount() {
        $sql = "SELECT COUNT(DISTINCT `comment_id`) FROM `{$this->usersVoted}` WHERE `post_id` = 0";
        return $this->db->get_var($sql);
    }

    public function getVoteDataRegenerateData($startId, $limit) {
        $sql = $this->db->prepare("SELECT DISTINCT `comment_id` FROM `{$this->usersVoted}` WHERE `comment_id` > %d AND `post_id` = 0 ORDER BY `comment_id` ASC LIMIT %d;", $startId, $limit);
        return $this->db->get_col($sql);
    }

    public function regenerateVoteData($ids) {
        foreach ($ids as $k => $id) {
            $comment = get_comment($id);
            $sql = $this->db->prepare("UPDATE {$this->usersVoted} SET `post_id` = %d, `date` = %d WHERE `comment_id` = %d", $comment->comment_post_ID, strtotime($comment->comment_date_gmt), $id);
            $this->db->query($sql);
        }
    }

    /* === /Regenerate Vote Data === */
    /* === Regenerate Closed Comments === */

    public function showClosedRegenerate() {
        $sql = "SELECT `comment_ID` FROM `{$this->db->comments}` WHERE `comment_karma` = 1 LIMIT 1;";
        return $this->db->get_var($sql);
    }

    public function getClosedRegenerateCount() {
        $sql = "SELECT COUNT(*) FROM `{$this->db->comments}` WHERE `comment_karma` = 1;";
        return $this->db->get_var($sql);
    }

    public function getClosedRegenerateData($startId, $limit) {
        $sql = $this->db->prepare("SELECT `comment_ID` FROM `{$this->db->comments}` WHERE `comment_ID` > %d AND `comment_karma` = 1 ORDER BY `comment_ID` ASC LIMIT %d;", $startId, $limit);
        return $this->db->get_col($sql);
    }

    public function regenerateClosedComments($ids) {
        foreach ($ids as $k => $id) {
            update_comment_meta($id, self::META_KEY_CLOSED, "1");
            wp_update_comment(["comment_ID" => $id, "comment_karma" => 0, "wpdiscuz_comment_update" => true]);
        }
    }

    /* === /Regenerate Closed Comments === */
    /* === Synchronize Commenter Data === */

    public function usersHaveComments() {
        $sql = "SELECT COUNT(*) FROM `{$this->db->comments}` WHERE `user_id` <> 0;";
        return intval($this->db->get_var($sql));
    }

    public function userHasComments($user_id) {
        $sql = $this->db->prepare("SELECT COUNT(*) FROM `{$this->db->comments}` WHERE `user_id` = %d;", $user_id);
        return intval($this->db->get_var($sql));
    }

    public function updateCommentersData() {
        $sql = "UPDATE `{$this->db->comments}` AS `c` INNER JOIN `{$this->db->users}` AS `u` ON `c`.`user_id` = `u`.`ID` SET `c`.`comment_author_email` = `u`.`user_email`, `c`.`comment_author` = `u`.`display_name`, `c`.`comment_author_url` = `u`.`user_url`;";
        $this->db->query($sql);
    }

    public function updateCommenterData($email, $name, $url, $id) {
        $sql = $this->db->prepare("UPDATE `{$this->db->comments}` SET `comment_author_email` = %s, `comment_author` = %s, `comment_author_url` = %s WHERE `user_id` = %d;", $email, $name, $url, $id);
        $this->db->query($sql);
    }

    /* === /Synchronize Commenter Data === */
    /* === Rebuild Ratings === */

    public function getRebuildRatingsCount() {
        $sql = $this->db->prepare("SELECT COUNT(*) FROM `{$this->db->postmeta}` WHERE `meta_key` = %s", self::POSTMETA_RATING_COUNT);
        return intval($this->db->get_var($sql));
    }

    public function getRebuildRatingsData($startId, $limit) {
        $sql = $this->db->prepare("SELECT * FROM `{$this->db->postmeta}` WHERE `meta_key` = %s AND `meta_id` > %d LIMIT %d", self::POSTMETA_RATING_COUNT, $startId, $limit);
        return $this->db->get_results($sql, ARRAY_A);
    }

    public function rebuildRatings($data) {
        foreach ($data as $key => $value) {
            $val = unserialize($value["meta_value"]);
            if ($val) {
                $newValues = [];
                foreach ($val as $k => $v) {
                    $sql = $this->db->prepare("SELECT COUNT(`cm`.`meta_id`) AS `count`, `cm`.`meta_value` FROM `{$this->db->commentmeta}` AS `cm` INNER JOIN `{$this->db->comments}` AS `c` ON `cm`.`comment_id` = `c`.`comment_ID` WHERE `c`.`comment_post_ID` = %d AND `cm`.`meta_key` = '%s' AND `cm`.`meta_value` IS NOT NULL AND `cm`.`meta_value` != 0 GROUP BY `cm`.`meta_value`", $value["post_id"], $k);
                    $values = $this->db->get_results($sql, ARRAY_A);
                    foreach ($values as $newData) {
                        $newValues[$k][$newData["meta_value"]] = $newData["count"];
                    }
                }
                update_post_meta($value["post_id"], self::POSTMETA_RATING_COUNT, $newValues, $val);
            }
        }
    }

    /* === /Rebuild Ratings === */
    /* === Feedback Comments === */

    public function addFeedbackForm($post_id, $uid, $question, $opened, $content) {
        $sql = $this->db->prepare("INSERT INTO `{$this->feedbackForms}` VALUES (NULL,%d,%s,%s,%d,%s);", $post_id, $uid, $question, $opened, $content);
        return $this->db->get_var($sql);
    }

    public function getFeedbackForm($id) {
        $sql = $this->db->prepare("SELECT * FROM `{$this->feedbackForms}` WHERE `id` = %s;", $id);
        return $this->db->get_row($sql);
    }

    public function getFeedbackFormByUid($post_id, $uid) {
        $sql = $this->db->prepare("SELECT * FROM `{$this->feedbackForms}` WHERE `post_id` = %d AND `unique_id` = %s;", $post_id, $uid);
        return $this->db->get_row($sql);
    }

    public function updateFeedbackForm($post_id, $uid, $question, $opened) {
        $sql = $this->db->prepare("UPDATE `{$this->feedbackForms}` SET `question` = %s, `opened` = %d WHERE `post_id` = %d AND `unique_id` = %s;", $question, $opened, $post_id, $uid);
        return $this->db->get_var($sql);
    }

    public function deleteFeedbackForm($post_id, $uid) {
        if ($form = $this->getFeedbackFormByUid($post_id, $uid)) {
            $sql = $this->db->prepare("DELETE FROM `{$this->feedbackForms}` WHERE `post_id` = %d AND `unique_id` = %s;", $post_id, $uid);
            $this->db->query($sql);
            $this->deleteCommentMetaByKeyAndValue(self::META_KEY_FEEDBACK_FORM_ID, $form->id);
        }
    }

    public function postHasFeedbackForms($post_id) {
        $sql = $this->db->prepare("SELECT `id` FROM `{$this->feedbackForms}` WHERE `post_id` = %d LIMIT 1;", $post_id);
        return $this->db->get_var($sql);
    }

    public function getOpenedFeedbackForms($post_id) {
        $sql = $this->db->prepare("SELECT `id` FROM `{$this->feedbackForms}` WHERE `post_id` = %d AND `opened` = 1;", $post_id);
        return $this->db->get_col($sql);
    }

    /* === /Feedback Comments === */
    /* === User Votes === */

    public function getUserVotes($commentList, $user_id) {
        $comment_ids = [];
        $votes = [];
        foreach ($commentList as $comment) {
            $comment_ids[] = $comment->comment_ID;
        }
        if ($comment_ids) {
            $sql = $this->db->prepare("SELECT `comment_id`, `vote_type` FROM `{$this->usersVoted}` WHERE (`user_id` = %s OR MD5(`user_id`) = %s) AND `comment_id` IN(" . implode(",", $comment_ids) . ") AND `vote_type` != 0;", $user_id, $user_id);
            foreach ($this->db->get_results($sql) as $vote) {
                $votes[$vote->comment_id] = $vote->vote_type;
            }
        }
        return $votes;
    }

    /* === /User Votes === */
    /* === User Rated === */

    public function isUserRated($user_id, $user_ip, $post_id) {
        if ($user_id) {
            $condition = "`user_id` = $user_id";
        } else {
            $condition = "`user_ip` = '$user_ip'";
        }
        $sql = $this->db->prepare("SELECT `id` FROM `{$this->usersRated}` WHERE $condition AND `post_id` = %d;", $post_id);
        return $this->db->get_var($sql);
    }

    public function addRate($post_id, $user_id, $user_ip, $rating, $date) {
        $sql = $this->db->prepare("INSERT INTO `{$this->usersRated}` (`post_id`, `user_id`, `user_ip`, `rating`, `date`) VALUES (%d,%d,%s,%d,%d);", $post_id, $user_id, $user_ip, $rating, $date);
        return $this->db->query($sql);
    }

    public function getPostRatingData($post_id) {
        $sql = $this->db->prepare("SELECT `rating` FROM `{$this->usersRated}` WHERE `post_id` = %d;", $post_id);
        return $this->db->get_col($sql);
    }

    public function removeRatings($post_id) {
        $sql = $this->db->prepare("DELETE FROM `{$this->usersRated}` WHERE `post_id` = %d;", $post_id);
        $this->db->query($sql);
    }

    /* === /User Rated === */
    /* === Remove Comment Meta === */

    public function deleteCommentMeta($metaKey) {
        $sql = $this->db->prepare("DELETE FROM `{$this->db->commentmeta}` WHERE `meta_key` = %s;", $metaKey);
        $this->db->query($sql);
    }

    public function deleteCommentMetaByKeyAndValue($metaKey, $metaValue) {
        $sql = $this->db->prepare("DELETE FROM `{$this->db->commentmeta}` WHERE `meta_key` = %s AND `meta_value` = %s;", $metaKey, $metaValue);
        $this->db->query($sql);
    }

    /* === /Remove Comment Meta === */
    /* === Fix Tables === */

    public function fixTables() {
        $sql = "SHOW INDEX FROM `{$this->avatarsCache}` WHERE Key_name = 'url'";
        if ($this->db->get_results($sql)) {
            $sql = "ALTER TABLE `{$this->avatarsCache}` DROP INDEX `url`;";
            $this->db->query($sql);
        }
        $sql = "SHOW INDEX FROM `{$this->avatarsCache}` WHERE Key_name = 'hash'";
        if ($this->db->get_results($sql)) {
            $sql = "ALTER TABLE `{$this->avatarsCache}` DROP INDEX `hash`;";
            $this->db->query($sql);
        }
        $sql_alter = "ALTER TABLE `{$this->avatarsCache}` DROP INDEX `user_email`, MODIFY `user_email` VARCHAR(100) NOT NULL, MODIFY `hash` VARCHAR(32) NOT NULL, ADD UNIQUE KEY `user_email` (`user_email`);";
        $this->db->query($sql_alter);
        $sql_alter = "ALTER TABLE `{$this->emailNotification}` DROP INDEX `subscribe_unique_index`, MODIFY `email` VARCHAR(100) NOT NULL, MODIFY `subscribtion_type` VARCHAR(20) NOT NULL, MODIFY `activation_key` VARCHAR(32) NOT NULL, ADD UNIQUE KEY `subscribe_unique_index` (`subscribtion_id`,`email`,`post_id`);";
        $this->db->query($sql_alter);
        $sql_alter = "ALTER TABLE `{$this->followUsers}` DROP INDEX `follow_unique_key`, DROP INDEX `user_email`, DROP INDEX `follower_email`, MODIFY `user_email` VARCHAR(100) NOT NULL, MODIFY `follower_email` VARCHAR(100) NOT NULL, ADD KEY `user_email` (`user_email`), ADD KEY `follower_email` (`follower_email`), ADD UNIQUE KEY `follow_unique_key` (`user_email`,`follower_email`);";
        $this->db->query($sql_alter);
        $sql_alter = "ALTER TABLE `{$this->phrases}` DROP INDEX `phrase_key`, MODIFY `phrase_key` VARCHAR(100) NOT NULL, ADD KEY `phrase_key` (`phrase_key`);";
        $this->db->query($sql_alter);
        $sql_alter = "ALTER TABLE `{$this->usersVoted}` DROP INDEX `user_id`, MODIFY `user_id` VARCHAR(32) NOT NULL, ADD KEY `user_id` (`user_id`);";
        $this->db->query($sql_alter);
        $sql_alter = "ALTER TABLE `{$this->usersRated}` MODIFY `user_ip` VARCHAR(32) NOT NULL;";
        $this->db->query($sql_alter);
        if (!empty($this->db->charset)) {
            $sql_alter = "ALTER TABLE `{$this->feedbackForms}` CONVERT TO CHARACTER SET {$this->db->charset}" . ($this->db->collate ? " COLLATE {$this->db->collate}" : "") . ";";
            $this->db->query($sql_alter);
            $sql_alter = "ALTER TABLE `{$this->avatarsCache}` CONVERT TO CHARACTER SET {$this->db->charset}" . ($this->db->collate ? " COLLATE {$this->db->collate}" : "") . ";";
            $this->db->query($sql_alter);
            $sql_alter = "ALTER TABLE `{$this->emailNotification}` CONVERT TO CHARACTER SET {$this->db->charset}" . ($this->db->collate ? " COLLATE {$this->db->collate}" : "") . ";";
            $this->db->query($sql_alter);
            $sql_alter = "ALTER TABLE `{$this->followUsers}` CONVERT TO CHARACTER SET {$this->db->charset}" . ($this->db->collate ? " COLLATE {$this->db->collate}" : "") . ";";
            $this->db->query($sql_alter);
            $sql_alter = "ALTER TABLE `{$this->phrases}` CONVERT TO CHARACTER SET {$this->db->charset}" . ($this->db->collate ? " COLLATE {$this->db->collate}" : "") . ";";
            $this->db->query($sql_alter);
            $sql_alter = "ALTER TABLE `{$this->usersVoted}` CONVERT TO CHARACTER SET {$this->db->charset}" . ($this->db->collate ? " COLLATE {$this->db->collate}" : "") . ";";
            $this->db->query($sql_alter);
            $sql_alter = "ALTER TABLE `{$this->usersRated}` CONVERT TO CHARACTER SET {$this->db->charset}" . ($this->db->collate ? " COLLATE {$this->db->collate}" : "") . ";";
            $this->db->query($sql_alter);
            if (is_plugin_active("wpdiscuz-report-flagging/wpDiscuzFlagComment.php")) {
                $sql_alter = "ALTER TABLE `{$this->db->prefix}wc_flagged` CONVERT TO CHARACTER SET {$this->db->charset}" . ($this->db->collate ? " COLLATE {$this->db->collate}" : "") . ";";
                $this->db->query($sql_alter);
            }
        }
        if (is_plugin_active("wpdiscuz-online-users/wpdiscuz-ou.php")) {
            $sql_alter = "ALTER TABLE `{$this->db->prefix}wc_online_users` DROP INDEX `unique_online_user`, DROP INDEX `user_ip`, MODIFY `user_email` VARCHAR (100) NOT NULL, MODIFY `user_ip` VARCHAR (32) NOT NULL, ADD UNIQUE KEY `unique_online_user` (`blog_id`,`user_id`,`user_email`), ADD KEY `user_ip` (`user_ip`);";
            $this->db->query($sql_alter);
            if (!empty($this->db->charset)) {
                $sql_alter = "ALTER TABLE `{$this->db->prefix}wc_online_users` CONVERT TO CHARACTER SET {$this->db->charset}" . ($this->db->collate ? " COLLATE {$this->db->collate}" : "") . ";";
                $this->db->query($sql_alter);
            }
        }
    }

    /* === /Fix Tables === */
}
