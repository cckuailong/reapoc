<?php
if (!defined("ABSPATH")) {
    exit();
}
?>
<div class="wpdtool-accordion-item">
    <div class="fas wpdtool-accordion-title" data-wpdtool-selector="wpdtool-<?php echo $tool["selector"]; ?>">
        <p><?php esc_html_e("Export / Import phrases", "wpdiscuz"); ?></p>                
    </div>


    <div class="wpdtool-accordion-content">

        <div class="wpdtool wpdtool-export-phrases">
            <p class="wpdtool-desc"><?php esc_html_e("Using this tool you can backup wpDiscuz phrases or migrate them from one WordPress to another.", "wpdiscuz"); ?></p>
            <form action="" method="post" class="wc-tools-settings-form wc-form">
                <?php wp_nonce_field("wc_tools_form", "wpd-phrases-export"); ?>
                <div class="wpdtool-block">
                    <?php if (file_exists($wpdiscuzOptionsDir . self::PHRASES_FILENAME . ".txt")) { ?>
                        <div class="wpdtool-left">
                            <a href="<?php echo esc_url_raw($wpdiscuzOptionsUrl . self::PHRASES_FILENAME . ".txt"); ?>" download="<?php echo esc_attr(self::PHRASES_FILENAME . ".txt"); ?>" class="button button-secondary">
                                <?php esc_html_e("Download Phrases", "wpdiscuz"); ?>
                            </a>
                        </div>
                    <?php } ?>
                    <div class="wpdtool-right">
                        <input type="submit" name="wpdiscuz-export-submit" class="button button-primary" value="<?php esc_attr_e("Backup Phrases", "wpdiscuz"); ?>">
                    </div>
                    <div class="clearfix"></div>
                    <input type="hidden" name="tools-action" value="export-phrases" />
                </div>
            </form>
        </div>

        <div class="wpdtool wpdtool-import-phrases">            
            <p class="wpdtool-desc"><?php esc_html_e("Here you can import and restore wpDiscuz phrases. You just need to choose backup file and click import phrases.", "wpdiscuz"); ?></p>
            <form action="" method="post" class="wc-tools-settings-form wc-form" enctype="multipart/form-data">
                <?php wp_nonce_field("wc_tools_form", "wpd-phrases-import"); ?>
                <div class="wpdtool-block">
                    <div class="wpdtool-left">
                        <input type="file" name="wpdiscuz-phrases-file" class=""/>
                    </div>
                    <div class="wpdtool-right">
                        <input type="submit" name="wpdiscuz-import-submit" class="button button-primary" value="<?php esc_attr_e("Import Phrases", "wpdiscuz"); ?>">
                    </div>
                    <div class="clearfix"></div>
                    <input type="hidden" name="tools-action" value="import-phrases" />
                </div>
            </form>
        </div>

    </div>
</div>