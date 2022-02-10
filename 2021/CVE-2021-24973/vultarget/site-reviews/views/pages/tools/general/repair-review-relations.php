<?php glsr()->hasPermission('settings') || die; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="tools-repair-review-relations">
            <span class="title dashicons-before dashicons-admin-tools"><?= _x('Repair Review Relations', 'admin-text', 'site-reviews'); ?></span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="tools-repair-review-relations" class="inside">
        <?php if (!empty($myisam_tables)) { ?>
            <div class="components-notice is-info" style="margin-bottom:1em;">
                <p class="components-notice__content"><?= sprintf(
                    _x('Once you have repaired the review relationships, it is recommended that you run the %s tool to prevent the problem from happening again.', 'admin-text', 'site-reviews'),
                        sprintf('<a data-expand="#tools-optimise-db-tables" href="%s">%s</a>',
                            glsr_admin_url('tools', 'general'),
                            _x('Optimise Your Database Tables', 'admin-text', 'site-reviews')
                        )
                    ); ?>
                </p>
            </div>
        <?php } ?>
        <p><?= _x('Site Reviews stores review details in a custom database table, these entries are linked to the review post type in the WordPress posts table using the review\'s Post ID.', 'admin-text', 'site-reviews'); ?></p>
        <p><?= _x('This tool will repair the review relationships in your database by removing any review details in the custom database table that do not point to a valid review.', 'admin-text', 'site-reviews'); ?></p>
        <form method="post">
            <?php wp_nonce_field('repair-review-relations'); ?>
            <input type="hidden" name="{{ id }}[_action]" value="repair-review-relations">
            <button type="submit" class="glsr-button components-button is-secondary" id="repair-review-relations" data-ajax-click data-ajax-scroll>
                <span data-loading="<?= esc_attr_x('Repairing relations, please wait...', 'admin-text', 'site-reviews'); ?>"><?= _x('Repair Relations', 'admin-text', 'site-reviews'); ?></span>
            </button>
        </form>
    </div>
</div>
