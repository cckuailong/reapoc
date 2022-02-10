<?php glsr()->hasPermission('settings') || die; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="tools-migrate-plugin">
            <span class="title dashicons-before dashicons-admin-tools"><?= _x('Migrate Plugin', 'admin-text', 'site-reviews'); ?></span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="tools-migrate-plugin" class="inside">
        <div class="components-notice is-info">
            <p class="components-notice__content"><?= _x('Hold down the ALT/Option key to force-run all previous migrations.', 'admin-text', 'site-reviews'); ?></p>
        </div>
        <p><?= _x('Run this tool if your reviews stopped working correctly after upgrading the plugin to the latest version (i.e. read-only reviews, zero-star ratings, missing role capabilities, etc.).', 'admin-text', 'site-reviews'); ?></p>
        <form method="post">
            <?php wp_nonce_field('migrate-plugin'); ?>
            <input type="hidden" name="{{ id }}[_action]" value="migrate-plugin">
            <input type="hidden" name="{{ id }}[alt]" value="0" data-alt>
            <button type="submit" class="glsr-button components-button is-secondary" id="migrate-plugin" data-ajax-click data-ajax-scroll data-remove-notice="migrate">
                <span data-alt-text="<?= esc_attr_x('Run All Migrations', 'admin-text', 'site-reviews'); ?>" data-loading="<?= esc_attr_x('Migrating, please wait...', 'admin-text', 'site-reviews'); ?>"><?= _x('Run Migration', 'admin-text', 'site-reviews'); ?></span>
            </button>
        </form>
    </div>
</div>
