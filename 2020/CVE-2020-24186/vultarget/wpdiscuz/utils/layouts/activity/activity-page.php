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
    $args = ["number" => $perPage, "status" => "all", "user_id" => "", "author_email" => "", "offset" => $offset];

    if ($currentUserId) {
        $args["user_id"] = $currentUserId;
    } else if ($currentUserEmail) {
        $args["author_email"] = $currentUserEmail;
    }
    $items = get_comments($args);
    if ($items && is_array($items)) {
        $args["number"] = null;
        $args["count"] = true;
        $allComments = get_comments($args);
        $pCount = intval($allComments / $perPage);
        $pageCount = ($allComments % $perPage == 0) ? $pCount : $pCount + 1;
        foreach ($items as $k => $item) {
            include WPDISCUZ_DIR_PATH . "/utils/layouts/activity/item.php";
        }
        include WPDISCUZ_DIR_PATH . "/utils/layouts/pagination.php";
        ?>
        <input type="hidden" class="wpd-page-number" value="<?php echo esc_attr($page); ?>"/>
    <?php } else { ?>
        <div class='wpd-item'><?php echo esc_html($this->options->phrases["wc_user_settings_no_data"]); ?></div>
        <?php
    }
}
