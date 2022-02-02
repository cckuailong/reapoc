<?php
if (!defined("ABSPATH")) {
    exit();
}
?>
<div class="wpdtool-accordion-item">

    <div class="fas wpdtool-accordion-title" data-wpdtool-selector="wpdtool-<?php echo $tool["selector"]; ?>">
        <p><?php esc_html_e("Database Operations", "wpdiscuz"); ?></p>
    </div>


    <div class="wpdtool-accordion-content">

        <div class="wpdtool wpdtool-fix-database-tables">
            <p class="wpdtool-desc"><?php esc_html_e("Using this tool you can fix database tables.", "wpdiscuz"); ?></p>
            <form action="" method="post" class="wc-tools-settings-form wc-form">
                <?php wp_nonce_field("wc_tools_form", "wpd-fix-tables"); ?>
                <div class="wpdtool-block">
                    <button type="submit" class="button button-secondary fix-tables" title="<?php esc_attr_e("Fix Tables", "wpdiscuz"); ?>">
                        <?php esc_html_e("Fix Tables", "wpdiscuz"); ?>&nbsp;
                        <i class="fas wc-hidden"></i>
                    </button>
                    <span class="fix-tables-import-progress"></span>
                </div>
            </form>
        </div>

    </div>
</div>