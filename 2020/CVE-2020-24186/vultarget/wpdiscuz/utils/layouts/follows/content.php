<?php
if (!defined("ABSPATH")) {
    exit();
}
ob_start();
$action = "wpdGetFollowsPage";
$perPage = apply_filters("wpdiscuz_content_per_page", 3);
$items = $this->dbManager->getFollows($currentUserEmail, $perPage, 0);
if ($items && is_array($items)) {
    $page = 0;
    $lrItemsCount = 3;
    $itemsCount = $this->dbManager->getFollowsCount($currentUserEmail);
    $pCount = intval($itemsCount / $perPage);
    $pageCount = ($itemsCount % $perPage == 0) ? $pCount : $pCount + 1;
    foreach ($items as $k => $item) {
        $fId = $item->id;
        $fName = $item->user_name;
        $fPostId = $item->post_id;
        $fKey = $item->activation_key;
        $postedDate = $this->getDate($item->follow_date);
        include WPDISCUZ_DIR_PATH . "/utils/layouts/follows/item.php";
    }
    include WPDISCUZ_DIR_PATH . "/utils/layouts/pagination.php";
    ?>
    <input type="hidden" class="wpd-page-number" value="0"/>
<?php } else { ?>
    <div class='wpd-item'><?php echo esc_html($this->options->phrases["wc_user_settings_no_data"]); ?></div>
    <?php
}
$html .= ob_get_clean();
