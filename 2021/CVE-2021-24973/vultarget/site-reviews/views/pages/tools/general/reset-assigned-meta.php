<?php glsr()->hasPermission('settings') || die; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="tools-reset-assigned-meta">
            <span class="title dashicons-before dashicons-admin-tools"><?= _x('Reset Assigned Meta Values', 'admin-text', 'site-reviews'); ?></span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="tools-reset-assigned-meta" class="inside">
        <p><?= _x('Site Reviews stores the individual review count, average rating, and ranking for each assigned post, category, and user. If you suspect that these meta values are incorrect (perhaps you cloned a page that had reviews assigned to it), you may use this tool to recalculate them.', 'admin-text', 'site-reviews'); ?></p>
        <form method="post">
            <?php wp_nonce_field('reset-assigned-meta'); ?>
            <input type="hidden" name="{{ id }}[_action]" value="reset-assigned-meta">
            <button type="submit" class="glsr-button components-button is-secondary" id="reset-assigned-meta" data-ajax-click data-ajax-scroll>
                <span data-loading="<?= esc_attr_x('Resetting values, please wait...', 'admin-text', 'site-reviews'); ?>"><?= _x('Reset Meta Values', 'admin-text', 'site-reviews'); ?></span>
            </button>
        </form>
    </div>
</div>
