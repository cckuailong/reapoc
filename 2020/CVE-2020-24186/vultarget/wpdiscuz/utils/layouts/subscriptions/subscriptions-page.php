<?php
if (!defined("ABSPATH")) {
    exit();
}
$action = isset($_POST["action"]) ? $_POST["action"] : "";
$currentUser = self::getCurrentUser();
if ($currentUser && $currentUser->ID) {
    $currentUserId = $currentUser->ID;
    $currentUserEmail = $currentUser->user_email;
} else {
    $currentUserId = 0;
    $currentUserEmail = isset($_COOKIE["comment_author_email_" . COOKIEHASH]) ? $_COOKIE["comment_author_email_" . COOKIEHASH] : "";
}

if ($action && $currentUserEmail) {
    $page = isset($_POST["page"]) ? intval($_POST["page"]) : 0;
    $lrItemsCount = 3;
    $perPage = apply_filters("wpdiscuz_content_per_page", 3);
    $offset = $page * $perPage;
    $items = $this->dbManager->getSubscriptions($currentUserEmail, $perPage, $offset);
    if ($items && is_array($items)) {
        $itemsCount = $this->dbManager->getSubscriptionsCount($currentUserEmail);
        $pCount = intval($itemsCount / $perPage);
        $pageCount = ($itemsCount % $perPage == 0) ? $pCount : $pCount + 1;

        foreach ($items as $k => $item) {
            $sId = $item->id;
            $sEmail = $item->email;
            $scId = $item->subscribtion_id;
            $sPostId = $item->post_id;
            $sType = $item->subscribtion_type;
            $sKey = $item->activation_key;

            if ($sType == "reply") {
                $object = get_comment($scId);
                $link = get_comment_link($scId);
                $author = $object->comment_author;
                $postedDate = $this->getCommentDate($object);
                $content = wp_trim_words($object->comment_content, 20, "&hellip;");
                $sTypeInfo = $this->options->phrases["wc_user_settings_subscribed_to_replies"];
            } else {
                $object = get_post($sPostId);
                $link = get_permalink($sPostId);
                $postAuthor = get_user_by("ID", $object->post_author);
                $author = $postAuthor->display_name ? $postAuthor->display_name : $postAuthor->user_login;
                $postedDate = $this->getPostDate($object);
                $content = $object->post_title;
                $sTypeInfo = ($sType == "all_comment") ? $this->options->phrases["wc_user_settings_subscribed_to_replies_own"] : $this->options->phrases["wc_user_settings_subscribed_to_all_comments"];
            }

            if ($object && !is_wp_error($object)) {
                include WPDISCUZ_DIR_PATH . "/utils/layouts/subscriptions/item.php";
            }
        }
        include WPDISCUZ_DIR_PATH . "/utils/layouts/pagination.php";
        ?>
        <input type="hidden" class="wpd-page-number" value="<?php echo esc_attr($page); ?>"/>
    <?php } else { ?>
        <div class='wpd-item'><?php echo esc_html($this->options->phrases["wc_user_settings_no_data"]); ?></div>
        <?php
    }
}