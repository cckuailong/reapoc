<div class="notice notice-info is-dismissible glsr-notice" data-notice="migrate">
    <form method="post">
        <input type="hidden" name="<?= glsr()->id; ?>[_action]" value="migrate-plugin">
        <input type="hidden" name="<?= glsr()->id; ?>[alt]" value="0" data-alt>
        <?php wp_nonce_field('migrate-plugin'); ?>
        <p><?= sprintf(_x('Site Reviews will automatically migrate your reviews and settings to the latest version; if you don\'t want to wait, you may also run the migration by clicking the button below. If this notice continues to appear after 5 minutes, please read the %s section on the Help page.', 'admin-text', 'site-reviews'), $action); ?></p>
        <p>
            <button type="submit" class="glsr-button components-button is-small is-secondary" data-ajax-click data-remove-notice="migrate">
                <span data-alt-text="<?= esc_attr_x('Run All Migrations', 'admin-text', 'site-reviews'); ?>" data-loading="<?= esc_attr_x('Migrating, please wait...', 'admin-text', 'site-reviews'); ?>"><?= _x('Run Migration', 'admin-text', 'site-reviews'); ?></span>
            </button>
        </p>
    </form>
</div>
