<?php
if (!defined("ABSPATH")) {
    exit();
}

$tools = [
    ["selector" => "options", "file" => WPDISCUZ_DIR_PATH . "/options/tools-layouts/tool-options.php"],
    ["selector" => "phrases", "file" => WPDISCUZ_DIR_PATH . "/options/tools-layouts/tool-phrases.php"],
    ["selector" => "images", "file" => WPDISCUZ_DIR_PATH . "/options/tools-layouts/tool-images.php"],
    ["selector" => "regenerate", "file" => WPDISCUZ_DIR_PATH . "/options/tools-layouts/tool-regenerate.php"],
    ["selector" => "subscriptions", "file" => WPDISCUZ_DIR_PATH . "/options/tools-layouts/tool-subscriptions.php"],
    ["selector" => "ratings", "file" => WPDISCUZ_DIR_PATH . "/options/tools-layouts/tool-ratings.php"],
    ["selector" => "database", "file" => WPDISCUZ_DIR_PATH . "/options/tools-layouts/tool-database.php"],
];
$tools = apply_filters("wpdiscuz_dashboard_tools", $tools);
?>
<div class="wrap wpdiscuz_tools_page">
    <div style="float:left; width:50px; height:55px; margin:10px 10px 10px 0px;">
        <img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/wpdiscuz-7-logo.png")); ?>" style="height: 48px;"/>
    </div>
    <h1 style="padding-bottom:20px; padding-top:15px;"><?php esc_html_e("wpDiscuz Tools", "wpdiscuz"); ?></h1>
    <br style="clear:both" />
    <?php settings_errors("wpdiscuz"); ?>
    <div id="toolsTab">
        <?php
        if ($tools && is_array($tools))
            foreach ($tools as $tool) {
                if (!empty($tool["selector"]) && !empty($tool["file"]) && file_exists($tool["file"])) {
                    include_once $tool["file"];
                }
            }
        ?>
    </div>
</div>