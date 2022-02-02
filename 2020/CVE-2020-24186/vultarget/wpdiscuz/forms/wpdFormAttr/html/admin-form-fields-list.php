<?php
require_once WPDISCUZ_DIR_PATH . "/forms/wpdFormAttr/html/default-fields.php";

$wpdiscuzCustomFields = apply_filters("wpdiscuz_form_custom_fields", []);
?>
<div class="wpdiscz-default-fields">
    <h3 class="wpdiscuz-tb-title"><?php esc_html_e("Comment Form Fields", "wpdiscuz"); ?></h3>
    <?php
    foreach ($wpdiscuzDefaultFields["html"] as $class => $title) {
        ?>
        <button id="<?php echo esc_attr($class); ?>" class="wpd-field-button button wpdDefaultField"><?php echo esc_html($title); ?></button>
        <?php
    }
    ?>
</div>
<div class="wpdiscz-custom-fields">
    <?php if ($wpdiscuzCustomFields) { ?>
        <h3 class="wpdiscuz-tb-title"><?php esc_html_e("Custom Fields", "wpdiscuz"); ?></h3>
        <?php
        foreach ($wpdiscuzCustomFields as $k => $wpdiscuzCustomField) {
            ?>
            <a href="<?php echo esc_url_raw(admin_url("admin-ajax.php?action=getCustomFieldHtml&fieldType=" . $wpdiscuzCustomField["type"] . "&width=700&height=400")); ?>" class="button thickbox" title="<?php echo esc_attr($wpdiscuzCustomField["title"]); ?>"><?php echo esc_html($wpdiscuzCustomField["title"]); ?></a>
            <?php
        }
    }
    ?>
</div>