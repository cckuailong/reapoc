<?php
if (!defined("ABSPATH")) {
    exit();
}
?>
<div class="wpdtool-accordion-item">
    <div class="fas wpdtool-accordion-title" data-wpdtool-selector="wpdtool-<?php echo $tool["selector"]; ?>">
        <p><?php esc_html_e("Export / Import options", "wpdiscuz"); ?></p>                
    </div>

    <div class="wpdtool-accordion-content">

        <div class="wpdtool wpdtool-export-options">
            <p class="wpdtool-desc"><?php esc_html_e("Using this tool you can backup wpDiscuz options or migrate them from one WordPress to another.", "wpdiscuz"); ?></p>
            <form action="" method="post" class="wc-tools-settings-form wc-form">
                <?php wp_nonce_field("wc_tools_form", "wpd-options-export"); ?>
                <div class="wpdtool-block">
                    <?php if (file_exists($wpdiscuzOptionsDir . self::OPTIONS_FILENAME . ".txt")) { ?>
                        <div class="wpdtool-left">
                            <a href="<?php echo esc_url_raw($wpdiscuzOptionsUrl . self::OPTIONS_FILENAME . ".txt"); ?>" download="<?php echo esc_attr(self::OPTIONS_FILENAME . ".txt"); ?>" class="button button-secondary">
                                <?php esc_html_e("Download Options", "wpdiscuz"); ?>
                            </a>
                        </div>
                    <?php } ?>
                    <div class="wpdtool-right">
                        <input type="submit" name="wpdiscuz-export-submit" class="button button-primary" value="<?php esc_attr_e("Backup Options", "wpdiscuz"); ?>">
                    </div>
                    <div class="clearfix"></div>
                    <input type="hidden" name="tools-action" value="export-options" />
                </div>
            </form>
        </div>

        <div class="wpdtool wpdtool-import-options">
            <p class="wpdtool-desc"><?php esc_html_e("Here you can import and restore wpDiscuz options. You just need to choose backup file and click import options.", "wpdiscuz"); ?></p>
            <form action="" method="post" class="wc-tools-settings-form wc-form" enctype="multipart/form-data">
                <?php wp_nonce_field("wc_tools_form", "wpd-options-import"); ?>
                <div class="wpdtool-block wpdtool-alignleft111">
                    <div class="wpdtool-left">
                        <input type="file" name="wpdiscuz-options-file" class="" style="vertical-align: top;"/>
                    </div>
                    <div class="wpdtool-right">
                        <input type="submit" name="wpdiscuz-import-submit" class="button button-primary" value="<?php esc_attr_e("Import Options", "wpdiscuz"); ?>">
                    </div>
                    <div class="clearfix"></div>
                    <input type="hidden" name="tools-action" value="import-options" />
                </div>
            </form>
        </div>
    </div>
</div>