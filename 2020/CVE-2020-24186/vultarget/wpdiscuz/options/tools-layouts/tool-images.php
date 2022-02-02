<?php
if (!defined("ABSPATH")) {
    exit();
}

$wmuIsActive = apply_filters("wpdiscuz_mu_isactive", false);

$cirComments = [];
$cirImagesCount = 0;
$cirDisabled = "disabled";
$dcoComments = [];
$dcoImagesCount = 0;
$dcoDisabled = "disabled";

if ($wmuIsActive) {

    /* ===== Comment Images Reloaded ===== */
    $cirComments = get_comments(
            [
                "count" => true,
                "meta_query" => [
                    [
                        "key" => "comment_image_reloaded",
                        "value" => "",
                        "compare" => "!="
                    ]
                ]
            ]
    );
    $cirImagesCount = intval($cirComments);
    $cirDisabled = $cirImagesCount ? "" : "disabled";

    /* ===== DCO Comment Attachment ===== */
    $dcoComments = get_comments(
            [
                "count" => true,
                "meta_query" => [
                    [
                        "key" => "attachment_id",
                        "value" => "",
                        "compare" => "!="
                    ]
                ]
            ]
    );
    $dcoImagesCount = intval($dcoComments);
    $dcoDisabled = $dcoImagesCount ? "" : "disabled='disabled'";
}
?>
<div class="wpdtool-accordion-item">

    <div class="fas wpdtool-accordion-title" data-wpdtool-selector="wpdtool-<?php echo $tool["selector"]; ?>">
        <p><?php esc_html_e("Import Comment Images", "wpdiscuz"); ?></p>
    </div>

    <div class="wpdtool-accordion-content">
        <?php if (!$wmuIsActive) { ?>
            <div class="wpdtool wpdtool-import-cir-images">
                <p class="wpdtool-desc"><?php _e("These tools are available only in <a href='https://gvectors.com/product/wpdiscuz-media-uploader/'><strong><i>wpDiscuz Media Uploader</i></strong></a> addon! Please install and activate it to use importer!", "wpdiscuz"); ?></p>
            </div>
        <?php } ?>

        <div class="wpdtool wpdtool-import-cir-images">
            <p class="wpdtool-desc"><?php _e("Here you can import comments' images from <strong><i>Comment Images Reloaded</i></strong> plugin to wpDiscuz.", "wpdiscuz"); ?></p>
            <form action="" method="post" class="wc-tools-settings-form wc-form">
                <?php wp_nonce_field("wc_tools_form", "wpd-cir-images"); ?>
                <div class="wpdtool-block">
                    <button type="submit" class="button button-secondary import-cir" <?php echo $cirDisabled; ?>>
                        <?php esc_html_e("Import images", "wpdiscuz"); ?>&nbsp;
                        <i class="fas wc-hidden"></i>
                    </button>
                    <span class="cir-import-progress">&nbsp;</span>
                    <input type="hidden" name="cir-images-count" value="<?php echo esc_attr($cirImagesCount); ?>" class="cir-images-count" />
                    <input type="hidden" name="cir-step" value="0" class="cir-step"/>
                </div>
            </form>
        </div>

        <div class="wpdtool wpdtool-import-dco-images">
            <p class="wpdtool-desc"><?php _e("Here you can import comments' images from <strong><i>DCO Comment Attachment</i></strong> plugin to wpDiscuz.", "wpdiscuz"); ?></p>
            <form action="" method="post" class="wc-tools-settings-form wc-form">
                <?php wp_nonce_field("wc_tools_form", "wpd-dco-images"); ?>
                <div class="wpdtool-block">
                    <button type="submit" class="button button-secondary import-dco" <?php echo $dcoDisabled; ?>>
                        <?php esc_html_e("Import images", "wpdiscuz"); ?>&nbsp;
                        <i class="fas wc-hidden"></i>
                    </button>
                    <span class="dco-import-progress">&nbsp;</span>
                    <input type="hidden" name="dco-images-count" value="<?php echo esc_attr($dcoImagesCount); ?>" class="dco-images-count" />
                    <input type="hidden" name="dco-step" value="0" class="dco-step"/>
                </div>
            </form>
        </div>

    </div>
</div>