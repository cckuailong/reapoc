<?php defined('ABSPATH') || die; ?>

<div class="glsr-addon">
    <a href="{{ link }}" class="glsr-addon-screenshot" data-slug="{{ slug }}">
        <span class="screen-reader-text">{{ title }}</span>
    </a>
    <div class="glsr-addon-description">
        <h3 class="glsr-addon-name">{{ title }}</h3>
        <p>{{ description }}</p>
    </div>
    <div class="glsr-addon-footer">
    <?php if (!is_wp_error(validate_plugin($plugin))) : ?>
        <?php if (is_plugin_active($plugin)) : ?>
        <span class="glsr-addon-link button button-secondary" disabled>
            <?= _x('Installed', 'admin-text', 'site-reviews'); ?>
        </span>
        <?php else: ?>
        <a href="<?= wp_nonce_url(self_admin_url('plugins.php?action=activate&plugin='.$plugin), 'activate-plugin_'.$plugin); ?>" class="glsr-addon-link button button-secondary">
            <?= _x('Activate', 'admin-text', 'site-reviews'); ?>
        </a>
        <?php endif; ?>
    <?php else: ?>
        <a href="{{ link }}" class="glsr-addon-link glsr-external button button-secondary">
            {{ link_text }}
        </a>
    <?php endif; ?>
    </div>
</div>
