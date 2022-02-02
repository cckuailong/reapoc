<?php
if (!defined("ABSPATH")) {
    exit();
}
ob_start();
$action = "wpdGetActivityPage";
$perPage = apply_filters("wpdiscuz_content_per_page", 3);
$lrItemsCount = 3;
$args = ["number" => $perPage, "status" => "all", "user_id" => "", "author_email" => ""];
if ($currentUserId) {
    $args["user_id"] = $currentUserId;
} else if ($currentUserEmail) {
    $args["author_email"] = $currentUserEmail;
}

$items = get_comments($args);
if ($items && is_array($items)) {
    $args["number"] = null;
    $args["count"] = true;
    $itemsCount = get_comments($args);
    $pCount = intval($itemsCount / $perPage);
    $pageCount = ($itemsCount % $perPage == 0) ? $pCount : $pCount + 1;
    $page = 0;
    foreach ($items as $k => $item) {
        include WPDISCUZ_DIR_PATH . "/utils/layouts/activity/item.php";
    }
    include WPDISCUZ_DIR_PATH . "/utils/layouts/pagination.php";
    ?>
    <input type="hidden" class="wpd-page-number" value="0"/>
<?php } else { ?>
    <div class='wpd-item'><?php echo esc_html($this->options->phrases["wc_user_settings_no_data"]); ?></div>
    <?php
}
$html .= ob_get_clean();
