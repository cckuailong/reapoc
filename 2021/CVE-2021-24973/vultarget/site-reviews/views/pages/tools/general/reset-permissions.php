<?php glsr()->hasPermission('settings') || die; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="tools-reset-permissions">
            <span class="title dashicons-before dashicons-admin-tools"><?= _x('Reset Permissions', 'admin-text', 'site-reviews'); ?></span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="tools-reset-permissions" class="inside">
        <div class="components-notice is-info">
            <p class="components-notice__content"><?= _x('Hold down the ALT/Option key to perform a "Hard Reset"; this removes all Site Reviews capabilites from your Editor, Author, and Contributor roles before re-adding them.', 'admin-text', 'site-reviews'); ?></p>
        </div>
        <p><?= _x('Site Reviews provides custom post_type capabilities that mirror the capabilities of your posts by default. For example, if a user role has permission to edit others posts, then that role will also have permission to edit other users reviews.', 'admin-text', 'site-reviews'); ?></p>
        <p><?= _x('If you have changed the capabilities of your user roles (Administrator, Editor, Author, and Contributor) and you suspect that Site Reviews is not working correctly due to your changes, you may use this tool to reset the Site Reviews capabilities for your user roles.', 'admin-text', 'site-reviews'); ?></p>
        <form method="post">
            <?php wp_nonce_field('reset-permissions'); ?>
            <input type="hidden" name="{{ id }}[_action]" value="reset-permissions">
            <input type="hidden" name="{{ id }}[alt]" value="0" data-alt>
            <button type="submit" class="glsr-button components-button is-secondary" id="reset-permissions" data-ajax-click data-ajax-scroll>
                <span data-alt-text="<?= esc_attr_x('Hard Reset Permissions', 'admin-text', 'site-reviews'); ?>" data-loading="<?= esc_attr_x('Resetting, please wait...', 'admin-text', 'site-reviews'); ?>"><?= _x('Reset Permissions', 'admin-text', 'site-reviews'); ?></span>
            </button>
        </form>
    </div>
</div>
