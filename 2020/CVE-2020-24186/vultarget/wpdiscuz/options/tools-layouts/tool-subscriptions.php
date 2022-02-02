<?php
if (!defined("ABSPATH")) {
    exit();
}
$stcrSubscriptionsCount = intval($this->dbManager->getStcrAllSubscriptions());
$stcrDisabled = $stcrSubscriptionsCount ? "" : "disabled='disabled'";

$isLstcExists = $this->dbManager->isTableExists("comment_notifier", false);
$lstcSubscriptionsCount = $isLstcExists ? intval($this->dbManager->getLstcAllSubscriptions()) : false;
$lstcDisabled = $lstcSubscriptionsCount ? "" : "disabled='disabled'";
?>
<div class="wpdtool-accordion-item">

    <div class="fas wpdtool-accordion-title" data-wpdtool-selector="wpdtool-<?php echo $tool["selector"]; ?>">
        <p><?php esc_html_e("Import Subscriptions", "wpdiscuz"); ?></p>        
    </div>

    <div class="wpdtool-accordion-content">

        <div class="wpdtool wpdtool-import-stcr-subscriptions">
            <p class="wpdtool-desc"><?php _e("Here you can import subscriptions from <strong><i>Subscribe To Comments Reloaded</i></strong> plugin to wpDiscuz.", "wpdiscuz"); ?></p>
            <form action="" method="post" class="wc-tools-settings-form wc-form">
                <?php wp_nonce_field("wc_tools_form", "wpd-stcr-subscriptions"); ?>
                <div class="wpdtool-block">
                    <button type="submit" class="button button-secondary import-stcr" <?php echo $stcrDisabled; ?>>
                        <?php esc_html_e('Import subscriptions', "wpdiscuz"); ?>&nbsp;
                        <i class="fas wc-hidden"></i>
                    </button>
                    <span class="stcr-import-progress">&nbsp;</span>
                    <input type="hidden" name="stcr-subscriptions-count" value="<?php echo esc_attr($stcrSubscriptionsCount); ?>" class="stcr-subscriptions-count" />
                    <input type="hidden" name="stcr-step" value="0" class="stcr-step"/>
                </div>
            </form>
        </div>

        <div class="wpdtool wpdtool-import-lstc-subscriptions">
            <p class="wpdtool-desc"><?php _e("Here you can import subscriptions from <strong><i>Lightweight Subscribe To Comments</i></strong> plugin to wpDiscuz.", "wpdiscuz"); ?></p>
            <form action="" method="post" class="wc-tools-settings-form wc-form">
                <?php wp_nonce_field("wc_tools_form", "wpd-lstc-subscriptions"); ?>
                <div class="wpdtool-block">
                    <button type="submit" class="button button-secondary import-lstc" <?php echo $lstcDisabled; ?>>
                        <?php esc_html_e('Import subscriptions', "wpdiscuz"); ?>&nbsp;
                        <i class="fas wc-hidden"></i>
                    </button>
                    <span class="lstc-import-progress">&nbsp;</span>
                    <input type="hidden" name="lstc-subscriptions-count" value="<?php echo esc_attr($lstcSubscriptionsCount); ?>" class="lstc-subscriptions-count" />
                    <input type="hidden" name="lstc-step" value="0" class="lstc-step"/>
                </div>
            </form>
        </div>

    </div>
</div>