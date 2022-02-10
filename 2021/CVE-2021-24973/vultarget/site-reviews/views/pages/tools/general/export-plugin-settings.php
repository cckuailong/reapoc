<?php glsr()->hasPermission('settings') || die; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="tools-export-plugin-settings">
            <span class="title dashicons-before dashicons-admin-tools"><?= _x('Export Plugin Settings', 'admin-text', 'site-reviews'); ?></span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="tools-export-plugin-settings" class="inside">
        <p><?= sprintf(
            _x('Export the Site Reviews settings for this site to a %s file. This allows you to easily import the plugin settings into another site.', 'admin-text', 'site-reviews'),
            '<code>*.json</code>'
        ); ?></p>
        <p><?= sprintf(
            _x('To export your reviews and categories, please use the WordPress %s tool.', 'admin-text', 'site-reviews'),
            sprintf('<a href="%s">%s</a>', admin_url('export.php'), _x('Export', 'admin-text', 'site-reviews'))
        ); ?></p>
        <form method="post">
            <?php wp_nonce_field('export-settings'); ?>
            <input type="hidden" name="{{ id }}[_action]" value="export-settings">
            <button type="submit" class="components-button is-secondary" id="export-settings">
                <?= _x('Export Settings', 'admin-text', 'site-reviews'); ?>
            </button>
        </form>
    </div>
</div>
