<?php defined('ABSPATH') || die; ?>

<span class="{{ class }}">
    <span class="glsr-toggle">
        <label for="{{ id }}">{{ text }}</label>
        <span class="glsr-toggle-switch">
            {{ input }} &#8203; <!-- zero-space character used for alignment -->
            <span class="glsr-toggle-track"></span>
        </span>
    </span>
</span>
