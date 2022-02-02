<?php
if (!defined("ABSPATH")) {
    exit();
}
$rebuildRatingsCount = intval($this->dbManager->getRebuildRatingsCount());
$disabledratings = $rebuildRatingsCount ? "" : "disabled='disabled'";
?>
<div class="wpdtool-accordion-item">

    <div class="fas wpdtool-accordion-title" data-wpdtool-selector="wpdtool-<?php echo $tool["selector"]; ?>">
        <p><?php esc_html_e("Rebuild Ratings", "wpdiscuz"); ?></p>
    </div>

    <div class="wpdtool-accordion-content">

        <div class="wpdtool wpdtool-rebuild-ratings">
            <p class="wpdtool-desc"><?php esc_html_e("Using this tool you can rebuild ratings.", "wpdiscuz"); ?></p>
            <form action="" method="post" class="wc-tools-settings-form wc-form">
                <?php wp_nonce_field("wc_tools_form", "wpd-rebuild-ratings"); ?>
                <div class="wpdtool-block">
                    <button <?php echo $disabledratings; ?> type="submit" class="button button-secondary rebuild-ratings" title="<?php esc_attr_e("Start Rebuild", "wpdiscuz"); ?>">
                        <?php esc_html_e("Rebuild Ratings", "wpdiscuz"); ?>&nbsp;
                        <i class="fas wc-hidden"></i>
                    </button>
                    <span class="rebuild-ratings-import-progress">&nbsp;</span>
                    <input type="hidden" name="rebuild-ratings-start-id" value="0" class="rebuild-ratings-start-id"/>
                    <input type="hidden" name="rebuild-ratings-count" value="<?php echo esc_attr($rebuildRatingsCount); ?>" class="rebuild-ratings-count"/>
                    <input type="hidden" name="rebuild-ratings-step" value="0" class="rebuild-ratings-step"/>
                </div>
            </form>
        </div>

    </div>
</div>