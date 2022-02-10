<?php defined('ABSPATH') || die; ?>

<span class="glsr-multibox-entry">
    <input type="hidden" name="{{ data.name }}" value="{{ data.id }}">
    <button type="button" class="glsr-remove-button">
        <span class="glsr-remove-icon" aria-hidden="true"></span>
        <span class="screen-reader-text"><?= _x('Remove assignment', 'admin-text', 'site-reviews'); ?></span>
    </button>
    <a href="{{ data.url }}">{{ data.title }}</a>
</span>
