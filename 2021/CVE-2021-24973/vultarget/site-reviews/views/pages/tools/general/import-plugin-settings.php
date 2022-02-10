<?php glsr()->hasPermission('settings') || die; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="tools-import-plugin-settings">
            <span class="title dashicons-before dashicons-admin-tools"><?= _x('Import Plugin Settings', 'admin-text', 'site-reviews'); ?></span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="tools-import-plugin-settings" class="inside">
        <p><?= sprintf(
            _x('Import the Site Reviews settings from a %s file. This file can be obtained by exporting the settings on another site using the export tool below.', 'admin-text', 'site-reviews'),
            '<code>*.json</code>'
        ); ?></p>
        <p><?= sprintf(
            _x('To import your reviews and categories from another website, please use the WordPress %s tool.', 'admin-text', 'site-reviews'),
            sprintf('<a href="%s">%s</a>', admin_url('import.php'), _x('Import', 'admin-text', 'site-reviews'))
        ); ?></p>
        <form method="post" enctype="multipart/form-data" onsubmit="submit.disabled = true;">
            <?php wp_nonce_field('import-settings'); ?>
            <input type="hidden" name="{{ id }}[_action]" value="import-settings">
            <p>
                <input type="file" name="import-file" accept="application/json">
            </p>
            <button type="submit" class="glsr-button components-button is-secondary" id="import-settings" data-expand="#tools-import-plugin-settings">
                <span data-loading="<?= esc_attr_x('Importing settings, please wait...', 'admin-text', 'site-reviews'); ?>"><?= _x('Import Settings', 'admin-text', 'site-reviews'); ?></span>
            </button>
        </form>
    </div>
</div>
