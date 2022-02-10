<?php defined('ABSPATH') || die; ?>

<div class="components-notice is-info">
    <p class="components-notice__content">
        <?= sprintf(_x('To receive support for this add-on, please %s to your Nifty Plugins account.', 'admin-text', 'site-reviews'),
            sprintf('<a href="https://niftyplugins.com/account/" target="_blank">%s</a>', _x('login', 'admin-text', 'site-reviews'))
        ); ?>
    </p>
</div>
